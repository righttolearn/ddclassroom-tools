<?php

namespace App\Models;

use App\Models\Relations\BelongsToCountry;
use App\Models\Relations\BelongsToSubGrade;
use App\Models\Relations\HasManyAttendanceLog;
use App\Models\Relations\HasManyEnrollment;
use App\Models\Relations\HasManyScore;
use App\Models\Relations\HasOneAttendanceDetail;
use App\Models\Relations\HasOneResponsible;
use App\Models\Relations\HasOneScore;
use App\Models\Relations\HasOneStudentAttendance;
use App\Models\Relations\HasOneStudentResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Student extends Model
{
    use BelongsToCountry,
        BelongsToSubGrade,
        HasManyAttendanceLog,
        HasManyEnrollment,
        HasManyScore,
        HasOneAttendanceDetail,
        HasOneResponsible,
        HasOneScore,
        HasOneStudentAttendance,
        HasOneStudentResult;

    public const SUPPORT_TYPE_CASH = 'cash';
    public const SUPPORT_TYPE_CREDIT_CARD = 'credit_card';

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
        });
    }

    protected $guarded = [];

    /**
     * Get all student results for this student
     */
    public function studentResults()
    {
        return $this->hasMany(StudentResult::class);
    }

    public function monthlyAttendanceLogs()
    {
        return $this->hasMany(MonthlyAttendanceLog::class);
    }
}
