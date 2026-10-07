<?php

namespace App\Models;

use App\Models\Relations\BelongsToMonth;
use App\Models\Relations\BelongsToStudent;
use App\Models\Relations\BelongsToSubGrade;
use App\Models\Relations\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyAttendanceLog extends Model
{
    use HasFactory, BelongsToMonth, BelongsToUser, BelongsToStudent, BelongsToSubGrade;

    protected $fillable = [
        'student_id',
        'sub_grade_id',
        'year',
        'month_id',
        'total_hours',
        'total_absences',
        'absence_percentage',
        'support_type',
        'is_eligible_for_support',
        'is_sent',
        'sent_at',
        'user_id',
    ];

    protected $casts = [
        'student_id' => 'integer',
        'sub_grade_id' => 'integer',
        'year' => 'integer',
        'month_id' => 'integer',

        'total_hours' => 'integer',
        'total_absences' => 'integer',

        'absence_percentage' => 'decimal:2',

        'is_eligible_for_support' => 'boolean',
        'is_sent' => 'boolean',

        'sent_at' => 'datetime',
    ];

    /*
     * total_presents is not stored in the database.
     * It is calculated automatically.
     */
    protected $appends = [
        'total_presents',
    ];

    public function getTotalPresentsAttribute(): int
    {
        return max(0,(int) $this->total_hours - (int) $this->total_absences);
    }

    public function getSupportTypeLabelAttribute(): ?string
    {
        return match ($this->support_type) {
            'cash' => 'Cash',
            'credit_card' => 'Credit Card',
            default => null,
        };
    }
}
