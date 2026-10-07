<?php

namespace App\Http\Controllers;

use App\Exports\ExportAttendanceLogDetailsReport;
use App\Exports\ExportAttendanceLogReport;
use App\Http\Requests\Attendance\CreateMultipleAttendanceLogRequest;
use App\Imports\StudentAttendanceLogImport;
use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\AttendanceLog;
use App\Models\Country;
use App\Models\Month;
use App\Models\Student;
use App\Models\SubGrade;
use App\Models\Subject;
use App\Models\Year;
use App\Traits\AttendanceLogConditionTrait;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceLogController extends Controller
{
    use AttendanceLogConditionTrait;

    public function index(Request $request)
    {
        $query = AttendanceLog::query()->with(['student:id,fa_name,fa_father_name,uuid,id_number', 'subGrade:id,full_name', 'month:id,name', 'subject:id,en_name']);

        if ($request->search) {
            $name = $request->search;
            $query->where(function ($query) use ($name) {
                $query->whereHas('student', function ($query) use ($name) {
                    $query->whereAny(['name', 'username', 'father_name', 'fa_name', 'fa_father_name', 'email', 'id_number'], 'like', "%$name%");
                });
            });
        }
        if ($request->grade_id) {
            $query->where('sub_grade_id', $request->grade_id);
        }

        if ($request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->year) {
            $query->where('year', $request->year);
        }

        if ($request->month_id) {
            $query->where('month_id', $request->month_id);
        }

        $scores = $query->paginate()->appends(request()->query());
        $grades = SubGrade::whereIsActive(true)->get();
        $years = Year::all(['id', 'name']);
        $months = Month::all(['id', 'name']);
        $subjects = Subject::all(['id', 'name']);

        return inertia('AttendanceLog/Index', ['scores' => $scores, 'months' => $months, 'years' => $years, 'grades' => $grades, 'subjects' => $subjects]);
    }

    public function studentAttendanceLogReports(Request $request)
    {
        $query = Student::query();
        $query
            ->withCount([
                'attendanceLogs' => function ($query) use ($request) {
                    return $this->filterAttendance($query, $request);
                },
            ])
            ->withCount([
                'present' => function ($query) use ($request) {
                    return $this->filterAttendance($query, $request);
                },
            ])
            ->withCount([
                'absent' => function ($query) use ($request) {
                    return $this->filterAttendance($query, $request);
                },
            ])
            ->withCount([
                'late' => function ($query) use ($request) {
                    return $this->filterAttendance($query, $request);
                },
            ])
            ->withCount([
                'excused' => function ($query) use ($request) {
                    return $this->filterAttendance($query, $request);
                },
            ]);

        if ($request->search) {
            $name = $request->search;
            $query->where(function ($query) use ($name) {
                $query->whereAny(['name', 'last_name', 'username', 'father_name', 'fa_name', 'fa_last_name', 'fa_father_name', 'email', 'phone', 'id_number'], 'like', "%$name%");
            });
        }
        if ($request->sub_grade_id) {
            $query->where('sub_grade_id', $request->sub_grade_id);
        }
        $students = $query->paginate(50)->appends(request()->query());

        $grades = SubGrade::whereIsActive(true)->get();
        $years = Year::all(['id', 'name']);
        $months = Month::all(['id', 'name']);
        $subjects = Subject::all(['id', 'name']);
        $countries = Country::all(['id', 'name']);

        return inertia('AttendanceLog/Report', ['students' => $students, 'countries' => $countries, 'months' => $months, 'years' => $years, 'grades' => $grades, 'subjects' => $subjects]);
    }

    public function createMultipleStudentAttendance()
    {
        $terms = [
            1 => 'First Term',
            2 => 'Second Term',
        ];

        $locations = [
            'ddc' => 'DDClassroom',
            'dlc' => 'DLC Pakistan',
            'arsa' => 'ARSA Turkey',
        ];

        $years = Year::all(['id', 'name']);
        $months = Month::all(['id', 'name']);
        $subGrades = SubGrade::all();

        return inertia('AttendanceLog/CreateMultipleStudentsAttendanceLog', [
            'months' => $months,
            'terms' => $terms,
            'years' => $years,
            'subGrades' => $subGrades,
            'locations' => $locations,
        ]);
    }

    public function storeMultipleStudentsAttendanceLog(CreateMultipleAttendanceLogRequest $request)
    {
        Excel::import(new StudentAttendanceLogImport, $request->file);

        return redirect()
            ->route('monthly-attendance-logs.create', [
                'year' => $request->year,
                'month_id' => $request->month_id,
                'sub_grade_id' => $request->sub_grade_id,
            ])
            ->with('success', 'Attendance logs imported successfully. Review the results before generating the report.');
    }

    public function generalAttendanceScore(Request $request)
    {
        $query = Student::query()->where('sub_grade_id', $request->sub_grade_id)->select('id', 'sub_grade_id');
        $query
            ->withCount([
                'attendanceLogs' => function ($query) use ($request) {
                    return $this->filterAttendance($query, $request);
                },
            ])
            ->withCount([
                'present' => function ($query) use ($request) {
                    return $this->filterAttendance($query, $request);
                },
            ])
            ->withCount([
                'absent' => function ($query) use ($request) {
                    return $this->filterAttendance($query, $request);
                },
            ])
            ->withCount([
                'late' => function ($query) use ($request) {
                    return $this->filterAttendance($query, $request);
                },
            ])
            ->withCount([
                'excused' => function ($query) use ($request) {
                    return $this->filterAttendance($query, $request);
                },
            ]);

        $students = $query->get();
        $studnetId = $students->pluck('id');
        $where = [
            'sub_grade_id' => $request->sub_grade_id,
            'type' => $request->type,
            'year' => $request->year,
        ];
        Attendance::whereIn('student_id', $studnetId)->where($where)->delete();
        AttendanceDetail::whereIn('student_id', $studnetId)->where($where)->delete();
        foreach ($students as $student) {
            $subjects = Subject::withCount([
                'attendanceLogs' => function ($query) use ($student) {
                    return $query->where('student_id', $student->id);
                },
            ])
                ->withCount([
                    'present' => function ($query) use ($student) {
                        return $query->where('student_id', $student->id);
                    },
                ])
                ->withCount([
                    'absent' => function ($query) use ($student) {
                        return $query->where('student_id', $student->id);
                    },
                ])
                ->withCount([
                    'late' => function ($query) use ($student) {
                        return $query->where('student_id', $student->id);
                    },
                ])
                ->withCount([
                    'excused' => function ($query) use ($student) {
                        return $query->where('student_id', $student->id);
                    },
                ])->get();
            Attendance::insert([
                'type' => $request->type,
                'year' => $request->year,
                'student_id' => $student->id,
                'sub_grade_id' => $student->sub_grade_id,
                'teacher_id' => auth()->id(),
                'user_id' => auth()->id(),
                'total_class_hours' => $student->attendance_logs_count,
                'present' => $student->present_count + $student->late_count,
                'absent' => $student->absent_count,
                'permission' => $student->excused_count,
                'patient' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($subjects as $subject) {
                AttendanceDetail::insert([
                    'type' => $request->type,
                    'year' => $request->year,
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'sub_grade_id' => $student->sub_grade_id,
                    'teacher_id' => auth()->id(),
                    'user_id' => auth()->id(),
                    'total_class_hours' => $subject->attendance_logs_count,
                    'present' => $subject->present_count + $subject->late_count,
                    'absent' => $subject->absent_count,
                    'permission' => $subject->excused_count,
                    'patient' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

        }

        // }

        return back();
    }

    public function getAttendanceLogReportAsExcel(Request $request)
    {
        if ($request->type == 'details') {
            return Excel::download(new ExportAttendanceLogDetailsReport($request->all()), 'جزییات-حاضری.xlsx');
        }

        return Excel::download(new ExportAttendanceLogReport($request->all()), 'گزارش-حاضری-صنف.xlsx');
    }

    public function clearAllAttendanceLog()
    {
        AttendanceLog::where('id', '>=', 1)->delete();
    }
}
