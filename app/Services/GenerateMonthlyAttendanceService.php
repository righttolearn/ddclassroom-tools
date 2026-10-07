<?php

namespace App\Services;

use App\Models\AttendanceLog;
use App\Models\MonthlyAttendanceLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class GenerateMonthlyAttendanceService
{
    public function getResults(int $year, int $monthId, ?int $subGradeId = null): Collection
    {
        $query = AttendanceLog::query()
            ->join('students', 'students.id', '=', 'attendance_logs.student_id')
            ->join('sub_grades', 'sub_grades.id', '=', 'attendance_logs.sub_grade_id')
            ->select([
                'attendance_logs.student_id',
                'attendance_logs.sub_grade_id',
                'students.name as student_name',
                'students.father_name',
                'students.phone',
                'students.support_type',
                'sub_grades.full_name as sub_grade_name',
            ])
            ->selectRaw('COUNT(*) as total_hours')
            ->selectRaw(
                "SUM(CASE WHEN attendance_logs.status = 'A' THEN 1 ELSE 0 END) as total_absences",
            )
            ->where('attendance_logs.year', $year)
            ->where('attendance_logs.month_id', $monthId)
            ->whereIn('attendance_logs.status', ['P', 'L', 'A']);

        if ($subGradeId) {
            $query->where('attendance_logs.sub_grade_id', $subGradeId);
        }

        $attendanceLogs = $query
            ->groupBy(
                'attendance_logs.student_id',
                'attendance_logs.sub_grade_id',
                'students.name',
                'students.father_name',
                'students.phone',
                'students.support_type',
                'sub_grades.full_name',
            )
            ->orderBy('students.name')
            ->get();

        if ($attendanceLogs->isEmpty()) {
            return collect();
        }

        return $attendanceLogs->map(function ($attendance) use ($year, $monthId) {
            $totalHours = (int) $attendance->total_hours;
            $totalAbsences = (int) $attendance->total_absences;
            $absencePercentage = $totalHours > 0
                ? round(($totalAbsences / $totalHours) * 100, 2)
                : 0;

            return [
                'student_id' => (int) $attendance->student_id,
                'student_name' => $attendance->student_name,
                'father_name' => $attendance->father_name,
                'phone' => $attendance->phone,
                'sub_grade_id' => (int) $attendance->sub_grade_id,
                'sub_grade_name' => $attendance->sub_grade_name,
                'year' => $year,
                'month_id' => $monthId,
                'total_hours' => $totalHours,
                'total_presents' => $totalHours - $totalAbsences,
                'total_absences' => $totalAbsences,
                'absence_percentage' => $absencePercentage,
                'support_type' => $attendance->support_type,
                'is_eligible_for_support' => $absencePercentage <= 30,
            ];
        });
    }

    public function generate(int $year, int $monthId, int $userId, ?int $subGradeId = null): int
    {
        return Cache::lock("monthly-attendance-generation:{$year}:{$monthId}", 300)
            ->block(15, function () use ($year, $monthId, $userId, $subGradeId) {
                return $this->generateWhileLocked($year, $monthId, $userId, $subGradeId);
            });
    }

    private function generateWhileLocked(int $year, int $monthId, int $userId, ?int $subGradeId): int
    {
        $results = $this->getResults($year, $monthId, $subGradeId);

        if ($results->isEmpty()) {
            return 0;
        }

        $now = now();
        $results
            ->sortBy('student_id')
            ->chunk(100)
            ->each(function (Collection $resultsChunk) use ($userId, $now) {
                $summaries = $resultsChunk->map(function (array $result) use ($userId, $now) {
                    return [
                        'student_id' => $result['student_id'],
                        'sub_grade_id' => $result['sub_grade_id'],
                        'year' => $result['year'],
                        'month_id' => $result['month_id'],
                        'total_hours' => $result['total_hours'],
                        'total_absences' => $result['total_absences'],
                        'absence_percentage' => $result['absence_percentage'],
                        'support_type' => $result['support_type'],
                        'is_eligible_for_support' => $result['is_eligible_for_support'],
                        'user_id' => $userId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                })->all();

                MonthlyAttendanceLog::query()->upsert(
                    $summaries,
                    ['student_id', 'year', 'month_id'],
                    [
                        'sub_grade_id',
                        'total_hours',
                        'total_absences',
                        'absence_percentage',
                        'support_type',
                        'is_eligible_for_support',
                        'user_id',
                        'updated_at',
                    ],
                );
            });

        return $results->count();
    }
}
