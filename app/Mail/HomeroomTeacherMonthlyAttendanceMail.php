<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class HomeroomTeacherMonthlyAttendanceMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public User $teacher, public Collection $logs) {}

    public function build()
    {
        return $this->subject('Students with More Than 30% Absence')
            ->view('emails.monthly-attendance.homeroom-teacher-summary')
            ->with([
                'teacher' => $this->teacher,
                'logs' => $this->logs,
            ]);
    }
}
