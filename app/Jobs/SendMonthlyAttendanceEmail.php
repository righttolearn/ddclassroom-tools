<?php

namespace App\Jobs;

use App\Mail\MonthlyAttendanceSupportMail;
use App\Models\MonthlyAttendanceLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class SendMonthlyAttendanceEmail implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /*
     * Retry failed email up to three times.
     */
    public int $tries = 3;

    /*
     * Prevent duplicate queued jobs
     * for the same monthly attendance record.
     */
    public int $uniqueFor = 3600;

    public function __construct(public int $monthlyAttendanceLogId) {}

    public function uniqueId(): string
    {
        return (string) $this->monthlyAttendanceLogId;
    }

    public function handle(): void
    {
        $monthlyAttendanceLog = MonthlyAttendanceLog::query()
            ->with(['student', 'subGrade', 'month'])
            ->findOrFail($this->monthlyAttendanceLogId);

        if ((float) $monthlyAttendanceLog->absence_percentage <= 30) {
            return;
        }

        if ($monthlyAttendanceLog->is_sent) {
            return;
        }

        if (! $monthlyAttendanceLog->student?->email) {
            throw new RuntimeException('Student does not have an email address.');
        }

        Mail::to($monthlyAttendanceLog->student->email)
            ->send(new MonthlyAttendanceSupportMail($monthlyAttendanceLog));

        $monthlyAttendanceLog->update([
            'is_sent' => true,
            'sent_at' => now(),
        ]);
    }
}
