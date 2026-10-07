<?php

namespace App\Jobs;

use App\Mail\HomeroomTeacherMonthlyAttendanceMail;
use App\Models\MonthlyAttendanceLog;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendHomeroomTeacherMonthlyAttendanceEmail implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $uniqueFor = 3600;

    public function __construct(public int $teacherId, public array $monthlyAttendanceLogIds)
    {
        sort($this->monthlyAttendanceLogIds);
    }

    public function uniqueId(): string
    {
        return $this->teacherId.':'.implode(',', $this->monthlyAttendanceLogIds);
    }

    public function handle(): void
    {
        $teacher = User::query()->find($this->teacherId);

        if (! $teacher?->email) {
            return;
        }

        $logs = MonthlyAttendanceLog::query()
            ->whereIn('monthly_attendance_logs.id', $this->monthlyAttendanceLogIds)
            ->where('absence_percentage', '>', 30)
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('class_responsibles')
                    ->where('teacher_id', $this->teacherId)
                    ->whereColumn('class_responsibles.sub_grade_id', 'monthly_attendance_logs.sub_grade_id')
                    ->whereColumn('class_responsibles.year', 'monthly_attendance_logs.year');
            })
            ->with(['student', 'subGrade', 'month'])
            ->orderBy('year')
            ->orderBy('month_id')
            ->orderBy('sub_grade_id')
            ->orderByDesc('absence_percentage')
            ->get();

        if ($logs->isEmpty()) {
            return;
        }

        Mail::to($teacher->email)
            ->send(new HomeroomTeacherMonthlyAttendanceMail($teacher, $logs));
    }
}
