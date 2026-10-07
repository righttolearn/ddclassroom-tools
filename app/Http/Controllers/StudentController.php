<?php

namespace App\Http\Controllers;

use App\Enums\UserTypeEnum;
use App\Http\Requests\Student\CreateMultipleStudentsRequest;
use App\Http\Requests\Student\CreateStudentRequest;
use App\Http\Requests\Student\UpdateStudentInfoRequest;
use App\Imports\StudentImport;
use App\Imports\UpdateStudentInfoImport;
use App\Jobs\SendEmailJob;
use App\Models\Country;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Score;
use App\Models\Student;
use App\Models\StudentResult;
use App\Models\Subject;
use App\Models\SubGrade;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\DomCrawler\Crawler;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query()->with(['country:id,name', 'subGrade:id,full_name']);

        if ($request->search) {
            $name = $request->search;
            $query->where(function ($query) use ($name) {
                $query->whereAny(['name', 'last_name', 'username', 'father_name', 'fa_name', 'fa_last_name', 'fa_father_name', 'email', 'phone', 'id_number'], 'like', "%$name%");
            });
        }
        if ($request->grade_id) {
            $query->where('sub_grade_id', $request->grade_id);
        }

        $students = $query->orderByDesc('id')->paginate(350);
        $grades = SubGrade::whereIsActive(true)->get();
        $countries = Country::whereIsActive(true)->get();

        return inertia('Student/Index', ['students' => $students, 'countries' => $countries, 'grades' => $grades]);
    }

    public function create()
    {
        $grades = SubGrade::whereIsActive(true)->get();
        $countries = Country::whereIsActive(true)->get();

        return inertia('Student/Create', ['countries' => $countries, 'grades' => $grades]);
    }

    public function store(CreateStudentRequest $request)
    {
        $student = Student::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'father_name' => $request->father_name,
            'fa_name' => $request->fa_name,
            'fa_last_name' => $request->fa_last_name,
            'fa_father_name' => $request->fa_father_name,
            'id_number' => $request->id_number,
            'phone' => $request->phone,
            'photo' => $request->photo,
            'username' => $request->username,
            'email' => $request->email,
            'country_id' => $request->country_id,
            'sub_grade_id' => $request->grade_id,
            'password' => $request->password,
            'name_in_system' => $request->name_in_system,
            'school' => $request->school,
            'password' => $request->password,
            'is_active' => $request->is_active == 1 ? true : false,
        ]);

        Enrollment::create([
            'student_id' => $student->id,
            'sub_grade_id' => $student->sub_grade_id,
            'user_id' => auth()->id(),
            'year' => date('Y'),
        ]);

        // Upload student photo
        if ($request->hasFile('photo')) {
            $student->photo = 'student-photos/'.$this->upload($request);
            $student->save();
        }

        return redirect('students');
    }

    public function show(Student $student)
    {
        // Redirect to edit page since there's no dedicated show page
        return redirect()->route('students.edit', $student);
    }

    public function edit(Student $student)
    {
        $grades = SubGrade::whereIsActive(true)->get();
        $countries = Country::whereIsActive(true)->get();

        return inertia('Student/Edit', [
            'student' => $student,
            'countries' => $countries,
            'grades' => $grades,
            'canManageSupportType' => $this->canManageSupportType(),
        ]);
    }

    public function upload($request)
    {
        if ($request->hasFile('photo')) {
            $filename = time().'-'.$request->photo->getClientOriginalName();
            $request->photo->storeAs('student-photos', $filename, 'public');

            return $filename;
        }
    }

    public function update(CreateStudentRequest $request, Student $student)
    {
        if ($request->has('support_type') && $request->support_type !== $student->support_type) {
            abort_unless($this->canManageSupportType(), 403);
        }

        if ($student->sub_grade_id != $request->grade_id) {
            $currentClass = $student->subGrade->grade;
            $newClass = SubGrade::find($request->grade_id)->grade;

            if ($currentClass->id == $newClass->id) {
                Enrollment::where(['student_id' => $student->id, 'sub_grade_id' => $student->sub_grade_id])->update([
                    'sub_grade_id' => $request->grade_id,
                    'user_id' => auth()->id(),
                ]);
            } else {
                Enrollment::create([
                    'student_id' => $student->id,
                    'sub_grade_id' => $request->grade_id,
                    'user_id' => auth()->id(),
                    'year' => date('Y'),
                ]);
            }
            $student->sub_grade_id = $request->grade_id;
            $student->save();
        }

        $studentData = [
            'name' => $request->name,
            'last_name' => $request->last_name,
            'father_name' => $request->father_name,
            'fa_name' => $request->fa_name,
            'fa_last_name' => $request->fa_last_name,
            'fa_father_name' => $request->fa_father_name,
            'id_number' => $request->id_number,
            'phone' => $request->phone,
            'photo' => $request->photo,
            'father_phone' => $request->father_phone,
            'father_email' => $request->father_email,
            'province' => $request->province,
            'username' => $request->username,
            'email' => $request->email,
            'country_id' => $request->country_id,

            'password' => $request->password,
            'name_in_system' => $request->name_in_system,
            'school' => $request->school,
            'password' => $request->password,
            'is_active' => $request->is_active == 1 ? true : false,
        ];

        if ($this->canManageSupportType()) {
            $studentData['support_type'] = $request->support_type;
        }

        $student->update($studentData);

        // Upload student photo
        if ($request->hasFile('photo')) {
            $student->photo = 'student-photos/'.$this->upload($request);
            $student->save();
        }

        return redirect('students');
    }

    private function canManageSupportType(): bool
    {
        return in_array(
            (int) auth()->user()?->user_type_id,
            [UserTypeEnum::SuperAdmin->value, UserTypeEnum::User->value],
            true,
        );
    }

    public function createMultipleStudents()
    {
        $grades = SubGrade::whereIsActive(true)->get();
        $years = Year::all(['id', 'name']);

        return inertia('Student/CreateMultipleStudents', ['grades' => $grades, 'years' => $years]);
    }

    public function editStudentInfo()
    {
        $grades = SubGrade::whereIsActive(true)->get();
        $years = Year::all(['id', 'name']);
        $countries = Country::all(['id', 'name']);

        return inertia('Student/EditStudentInfo', ['grades' => $grades, 'years' => $years, 'countries' => $countries]);
    }

    public function storeMultipleStudents(CreateMultipleStudentsRequest $request)
    {
        Excel::import(new StudentImport($request->grade_id, $request->year, auth()->id()), $request->file);
    }

    public function updateStudentInfo(UpdateStudentInfoRequest $request)
    {
        Excel::import(new UpdateStudentInfoImport, $request->file);
    }

    public function emailHandbook($uuid)
    {
        // $students = Student::where('id', '>=', 503)->where('id', '<=', 510)->get();
        // $delay = 2; // Delay in seconds
        // foreach ($students as $index => $student) {
        //     $job = (new SendEmailJob($student))
        //         ->onConnection('database')
        //         ->delay(now()->addSeconds($index * $delay));
        //     dispatch($job);
        // }
        $student = Student::where('uuid', $uuid)->firstOrFail();

        dispatch(new SendEmailJob($student))->onConnection('database');

        return back();
    }

    public function studentUpladingSampleFile()
    {
        $filePath = storage_path('app/public/student-uploading-sample-file.xlsx');

        $fileName = 'student-uploading-sample-file.xlsx';

        return response()->download($filePath, $fileName);
    }

    public function sendResultCard(Request $request)
    {
        // Retrieve the student's data from the request
        $student = $request->input('student');

        // Render the Blade view that displays the student's result card
        $html = view('student.result-card', compact('student'))->render();

        // Use the Symfony DomCrawler to capture a screenshot of the result card
        $crawler = new Crawler($html);
        $screenshot = $crawler->filter('body')->screenshot('result-card.png');

        // Create an Excel file attachment
        $excel = Excel::create('student-result', function ($excel) use ($student) {
            $excel->sheet('Results', function ($sheet) use ($student) {
                $sheet->fromArray([
                    'Name' => $student['name'],
                    'Grade' => $student['grade'],
                    'Attendance' => $student['attendance'],
                ]);
            });
        })->store('xlsx');

        // Send the email with the screenshot and Excel attachment
        Mail::send('emails.result-card', compact('student', 'screenshot', 'excel'), function ($message) use ($student) {
            $message->to($student['email'])->subject('Student Result Card');
            $message->attach(storage_path('app/result-card.png'));
            $message->attach(storage_path('app/student-result.xlsx'));
        });

        return redirect()->back()->with('success', 'Result card sent successfully!');
    }

    public function filter(Request $request)
    {
        $grades = Grade::where('is_active', true)->get(['id', 'name']);
        $subGrades = SubGrade::whereIsActive(true)->get(['id', 'full_name', 'grade_id']);
        $years = Year::all(['id', 'name']);
        $countries = Country::where('is_active', true)->get(['id', 'name']);
        $subjects = Subject::all(['id', 'name']);

        if ($request->subject_id) {
            $query = Score::query()
                ->select([
                    'scores.id',
                    'scores.written',
                    'scores.verbal',
                    'scores.attendance',
                    'scores.activity',
                    'scores.homework',
                    'scores.evaluation',
                    'scores.total',
                    'scores.type',
                    'scores.is_passed',
                    'scores.year',
                    'scores.student_id',
                    'scores.sub_grade_id',
                    'scores.subject_id',
                    'scores.teacher_id',
                    'scores.user_id',
                    'scores.created_at',
                    'scores.updated_at'
                ])
                ->with([
                    'student:id,name,father_name,country_id,fa_name,fa_father_name,uuid,id_number,email',
                    'subject:id,name',
                    'subGrade:id,full_name,grade_id',
                    'subGrade.grade:id,name',
                    'teacher:id,name'
                ]);

            $query->where('subject_id', $request->subject_id);

            if ($request->student_id) {
                $query->whereHas('student', function ($q) use ($request) {
                    $q->where('id_number', 'like', "%{$request->student_id}%");
                });
            }

            if ($request->email) {
                $query->whereHas('student', function ($q) use ($request) {
                    $q->where('email', 'like', "%{$request->email}%");
                });
            }

            if ($request->country_id) {
                $query->whereHas('student', function ($q) use ($request) {
                    $q->where('country_id', $request->country_id);
                });
            }

            if ($request->grade_id) {
                $query->whereHas('subGrade', function ($q) use ($request) {
                    $q->where('grade_id', $request->grade_id);
                });
            }

            if ($request->sub_grade_id) {
                $query->where('sub_grade_id', $request->sub_grade_id);
            }

            if ($request->year) {
                $query->where('year', $request->year);
            }

            if ($request->exam_type === 'midterm') {
                $query->where('type', 1);
            } elseif ($request->exam_type === 'final') {
                $query->where('type', 2);
            } elseif ($request->exam_type === 'result') {
                $query->where('type', 3);
            } else {
                $query->where('type', 3);
            }

            if ($request->status === 'passed') {
                $query->where('is_passed', true);
            } elseif ($request->status === 'failed') {
                $query->where('is_passed', false);
            }

            $results = $query->orderByDesc('id')->paginate(350)->appends($request->query());

            $totalPassed = Score::query()
                ->where('subject_id', $request->subject_id)
                ->where('is_passed', true);

            $totalFailed = Score::query()
                ->where('subject_id', $request->subject_id)
                ->where('is_passed', false);

            if ($request->student_id) {
                $totalPassed->whereHas('student', function ($q) use ($request) {
                    $q->where('id_number', 'like', "%{$request->student_id}%");
                });
                $totalFailed->whereHas('student', function ($q) use ($request) {
                    $q->where('id_number', 'like', "%{$request->student_id}%");
                });
            }
            if ($request->email) {
                $totalPassed->whereHas('student', function ($q) use ($request) {
                    $q->where('email', 'like', "%{$request->email}%");
                });
                $totalFailed->whereHas('student', function ($q) use ($request) {
                    $q->where('email', 'like', "%{$request->email}%");
                });
            }
            if ($request->country_id) {
                $totalPassed->whereHas('student', function ($q) use ($request) {
                    $q->where('country_id', $request->country_id);
                });
                $totalFailed->whereHas('student', function ($q) use ($request) {
                    $q->where('country_id', $request->country_id);
                });
            }
            if ($request->grade_id) {
                $totalPassed->whereHas('subGrade', function ($q) use ($request) {
                    $q->where('grade_id', $request->grade_id);
                });
                $totalFailed->whereHas('subGrade', function ($q) use ($request) {
                    $q->where('grade_id', $request->grade_id);
                });
            }
            if ($request->sub_grade_id) {
                $totalPassed->where('sub_grade_id', $request->sub_grade_id);
                $totalFailed->where('sub_grade_id', $request->sub_grade_id);
            }
            if ($request->year) {
                $totalPassed->where('year', $request->year);
                $totalFailed->where('year', $request->year);
            }
            if ($request->exam_type === 'midterm') {
                $totalPassed->where('type', 1);
                $totalFailed->where('type', 1);
            } elseif ($request->exam_type === 'final') {
                $totalPassed->where('type', 2);
                $totalFailed->where('type', 2);
            } elseif ($request->exam_type === 'result') {
                $totalPassed->where('type', 3);
                $totalFailed->where('type', 3);
            } else {
                $totalPassed->where('type', 3);
                $totalFailed->where('type', 3);
            }

            $passedCount = $totalPassed->count();
            $failedCount = $totalFailed->count();

            return inertia('Student/Filter', [
                'results' => $results,
                'grades' => $grades,
                'subGrades' => $subGrades,
                'years' => $years,
                'countries' => $countries,
                'subjects' => $subjects,
                'hasSubject' => true,
                'passedCount' => $passedCount,
                'failedCount' => $failedCount,
            ]);
        }

        $query = StudentResult::query()->with([
            'student:id,name,father_name,country_id,fa_name,fa_father_name,uuid,id_number,email',
            'middleResult:id,name',
            'subGrade:id,full_name,grade_id',
            'subGrade.grade:id,name',
            'teacher:id,name'
        ]);

        if ($request->student_id) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('id_number', 'like', "%{$request->student_id}%");
            });
        }

        if ($request->email) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('email', 'like', "%{$request->email}%");
            });
        }

        if ($request->country_id) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('country_id', $request->country_id);
            });
        }

        if ($request->grade_id) {
            $query->whereHas('subGrade', function ($q) use ($request) {
                $q->where('grade_id', $request->grade_id);
            });
        }

        if ($request->sub_grade_id) {
            $query->where('sub_grade_id', $request->sub_grade_id);
        }

        if ($request->year) {
            $query->where('year', $request->year);
        }

        if ($request->exam_type) {
            if ($request->exam_type === 'midterm') {
                $query->whereNotNull('middle')->where('middle', '>', 0);
            } elseif ($request->exam_type === 'final') {
                $query->whereNotNull('final')->where('final', '>', 0);
            }
        }

        $results = $query->orderByDesc('id')->paginate(350)->appends($request->query());

        $totalKamyab = StudentResult::query();
        $totalNakam = StudentResult::query();
        $totalMashroot = StudentResult::query();

        if ($request->student_id) {
            $totalKamyab->whereHas('student', function ($q) use ($request) {
                $q->where('id_number', 'like', "%{$request->student_id}%");
            });
            $totalNakam->whereHas('student', function ($q) use ($request) {
                $q->where('id_number', 'like', "%{$request->student_id}%");
            });
            $totalMashroot->whereHas('student', function ($q) use ($request) {
                $q->where('id_number', 'like', "%{$request->student_id}%");
            });
        }
        if ($request->email) {
            $totalKamyab->whereHas('student', function ($q) use ($request) {
                $q->where('email', 'like', "%{$request->email}%");
            });
            $totalNakam->whereHas('student', function ($q) use ($request) {
                $q->where('email', 'like', "%{$request->email}%");
            });
            $totalMashroot->whereHas('student', function ($q) use ($request) {
                $q->where('email', 'like', "%{$request->email}%");
            });
        }
        if ($request->country_id) {
            $totalKamyab->whereHas('student', function ($q) use ($request) {
                $q->where('country_id', $request->country_id);
            });
            $totalNakam->whereHas('student', function ($q) use ($request) {
                $q->where('country_id', $request->country_id);
            });
            $totalMashroot->whereHas('student', function ($q) use ($request) {
                $q->where('country_id', $request->country_id);
            });
        }
        if ($request->grade_id) {
            $totalKamyab->whereHas('subGrade', function ($q) use ($request) {
                $q->where('grade_id', $request->grade_id);
            });
            $totalNakam->whereHas('subGrade', function ($q) use ($request) {
                $q->where('grade_id', $request->grade_id);
            });
            $totalMashroot->whereHas('subGrade', function ($q) use ($request) {
                $q->where('grade_id', $request->grade_id);
            });
        }
        if ($request->sub_grade_id) {
            $totalKamyab->where('sub_grade_id', $request->sub_grade_id);
            $totalNakam->where('sub_grade_id', $request->sub_grade_id);
            $totalMashroot->where('sub_grade_id', $request->sub_grade_id);
        }
        if ($request->year) {
            $totalKamyab->where('year', $request->year);
            $totalNakam->where('year', $request->year);
            $totalMashroot->where('year', $request->year);
        }
        if ($request->exam_type === 'midterm') {
            $totalKamyab->whereNotNull('middle')->where('middle', '>', 0);
            $totalNakam->whereNotNull('middle')->where('middle', '>', 0);
            $totalMashroot->whereNotNull('middle')->where('middle', '>', 0);
        } elseif ($request->exam_type === 'final') {
            $totalKamyab->whereNotNull('final')->where('final', '>', 0);
            $totalNakam->whereNotNull('final')->where('final', '>', 0);
            $totalMashroot->whereNotNull('final')->where('final', '>', 0);
        }

        // Filter by result status
        $totalKamyab->where('result_name', 'کامیاب');
        $totalNakam->where(function($q) {
            $q->where('result_name', 'تکرار صنف')
              ->orWhere('result_name', 'ناکام');
        });
        $totalMashroot->where(function($q) {
            $q->where('result_name', 'تکرار بیشتر')
              ->orWhere('result_name', 'مشروط');
        });

        $kamyabCount = $totalKamyab->count();
        $nakamCount = $totalNakam->count();
        $mashrootCount = $totalMashroot->count();

        return inertia('Student/Filter', [
            'results' => $results,
            'grades' => $grades,
            'subGrades' => $subGrades,
            'years' => $years,
            'countries' => $countries,
            'subjects' => $subjects,
            'hasSubject' => false,
            'kamyabCount' => $kamyabCount,
            'nakamCount' => $nakamCount,
            'mashrootCount' => $mashrootCount,
        ]);
    }

    public function destroy(Student $student)
    {
        $student->update(['is_active' => ! $student->is_active]);

        return back();
    }
}
