<?php

namespace App\Mail;

use App\Models\MonthlyAttendanceLog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use RuntimeException;

class MonthlyAttendanceSupportMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public MonthlyAttendanceLog $log) {}

    public function build()
    {
        $template = $this->resolveTemplate();

        return $this->subject('Your Top-Up Status for This Month')
            ->view("emails.monthly-attendance.{$template}")
            ->with([
                'log' => $this->log,
                'student' => $this->log->student,
                'month' => $this->log->month,
            ]);
    }

    private function resolveTemplate(): string
    {
        $gradeName = $this->log->subGrade?->full_name ?? ($this->log->subGrade?->name ?? '');

        if (preg_match('/(?:grade\s*)?(1[01]|[789])\b/i', $gradeName, $matches)) {
            $grade = (int) $matches[1];

            return $grade <= 9 ? 'grade-7-9' : 'grade-10-11';
        }

        throw new RuntimeException('Unable to determine Grade 7, 8, 9, 10, or 11.');
    }
}
