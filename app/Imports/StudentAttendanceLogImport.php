<?php

namespace App\Imports;

ini_set('max_execution_time', 120);

use App\Models\AttendanceLog;
use App\Models\AttendanceMissingEmail;
use App\Models\Student;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class StudentAttendanceLogImport implements ToModel, WithBatchInserts, WithChunkReading, WithHeadingRow
{
    public function model(array $row)
    {

        if (isset($row['email']) && isset($row['username']) && isset($row['status']) && isset($row['date']) && isset($row['course_name'])) {
            $username = $row['username'];
            $email = $row['username'];
            if ($username == 'NULL' || $username == null) {
                return null;
            }
            // Use cache to pre-load students and subjects
            $student = Cache::remember("student_username_{$username}", 3600, function () use ($username) {
                return Student::whereUsername($username)->first();
            });

            if (! $student) {
                AttendanceMissingEmail::updateOrCreate(['email' => $email], ['created_at' => now()]);

                return null;
            }

            if (request()->location == 'ddc') {
                $from = 0;
                $to = 3;
            } elseif (request()->location == 'dlc') {
                $from = 4;
                $to = 4;
            } elseif (request()->location == 'arsa') {
                $from = 5;
                $to = 4;
            }

            $subject = isset($row['course_name']) ? $row['course_name'] : null;
            $subjectId = Cache::remember('subject_name_'.substr($subject, $from, $to), 3600, function () use ($subject, $from, $to) {
                return Subject::where('en_name', 'like', substr($subject, $from, $to).'%')->value('id');
            });

            if ($subjectId) {
                if (request()->sub_grade_id) {
                    if (request()->sub_grade_id != $student->sub_grade_id) {
                        return null;
                    }
                }
                $createdAt = now();
                if (isset($row['date'])) {
                    $dateValue = $row['date'];
                    if (is_numeric($dateValue)) {
                        $dateTime = Carbon::instance(Date::excelToDateTimeObject($dateValue));
                        $createdAt = $dateTime->format('Y-m-d H:i:s');
                    } else {
                        try {
                            $dateTime = Carbon::parse($dateValue);
                            $createdAt = $dateTime->format('Y-m-d H:i:s');
                        } catch (\Exception $e) {
                        }
                    }
                }

                return new AttendanceLog([
                    'year' => request()->year,
                    'student_id' => $student->id,
                    'month_id' => request()->month_id,
                    'subject_id' => $subjectId,
                    'sub_grade_id' => $student->sub_grade_id,
                    'status' => $row['status'],
                    'first_term' => request()->term == 1,
                    'user_id' => auth()->id(),
                    'date' => $createdAt,
                ]);
            } else {
                info("Subject id is not found for $email, $subject");
            }
        }

        return null;
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
