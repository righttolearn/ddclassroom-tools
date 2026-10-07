<?php

namespace App\Exports;

use App\Models\MonthlyAttendanceLog;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExportMonthlyTopUpReport extends DefaultValueBinder implements FromQuery, ShouldAutoSize, WithColumnFormatting, WithCustomStartCell, WithCustomValueBinder, WithEvents, WithHeadings, WithMapping, WithTitle
{
    public function __construct(private readonly array $filters) {}

    public function query(): Builder
    {
        $query = MonthlyAttendanceLog::query()
            ->join('students', 'students.id', '=', 'monthly_attendance_logs.student_id')
            ->join('sub_grades', 'sub_grades.id', '=', 'monthly_attendance_logs.sub_grade_id')
            ->select([
                'monthly_attendance_logs.id',
                'monthly_attendance_logs.total_hours',
                'monthly_attendance_logs.total_absences',
                'monthly_attendance_logs.is_eligible_for_support',
                'students.name as student_name',
                'students.father_name',
                'students.phone',
                'sub_grades.full_name as sub_grade_name',
            ]);

        if ($this->filters['year']) {
            $query->where('monthly_attendance_logs.year', $this->filters['year']);
        }

        if ($this->filters['month_id']) {
            $query->where('monthly_attendance_logs.month_id', $this->filters['month_id']);
        }

        if ($this->filters['sub_grade_id']) {
            $query->where('monthly_attendance_logs.sub_grade_id', $this->filters['sub_grade_id']);
        }

        if ($this->filters['support_type']) {
            $query->where('monthly_attendance_logs.support_type', $this->filters['support_type']);
        }

        if ($this->filters['absence_percentage'] !== null) {
            $query->where('monthly_attendance_logs.absence_percentage', '>', $this->filters['absence_percentage']);
        }

        return $query->orderBy('sub_grades.full_name')
            ->orderBy('students.name')
            ->orderBy('monthly_attendance_logs.id');
    }

    public function headings(): array
    {
        return [
            'Name',
            "Father's Name",
            'Phone Number',
            'Grade / Class',
            'Eligibility',
            'Attendance',
        ];
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function map($log): array
    {
        $totalPresents = max(0, (int) $log->total_hours - (int) $log->total_absences);

        return [
            $log->student_name,
            $log->father_name,
            $log->phone,
            $log->sub_grade_name,
            $log->is_eligible_for_support ? 'Yes' : 'No',
            $totalPresents.' / '.(int) $log->total_hours,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function bindValue(Cell $cell, $value): bool
    {
        if ($cell->getColumn() === 'C' && $cell->getRow() > 2) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $period = $this->filters['month_name'].' '.($this->filters['year'] ?: 'All Years');

                $sheet->mergeCells('A1:F1');
                $sheet->setCellValue('A1', "Monthly Attendance Report — {$period}");
                $sheet->getStyle('A1:F1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal('center');

                $sheet->freezePane('A3');
                $sheet->setAutoFilter("A2:F{$lastRow}");
                $sheet->getStyle('A2:F2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1F4E78'],
                    ],
                ]);
                $sheet->getStyle("A1:F{$lastRow}")->getAlignment()->setVertical('center');

                if ($lastRow >= 2) {
                    $sheet->getStyle("E3:F{$lastRow}")->getAlignment()->setHorizontal('center');
                }
            },
        ];
    }

    public function title(): string
    {
        return 'Monthly Top-Up';
    }
}
