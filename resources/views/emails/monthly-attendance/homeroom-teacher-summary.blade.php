<div style="font-family: Arial, sans-serif; color: #111827; line-height: 1.5;">
    <p>Dear {{ $teacher->en_name ?: $teacher->name }},</p>

    <p>
        The following students in your homeroom had an absence rate of more than 30%.
        An individual top-up status email has been prepared for each selected student.
    </p>

    <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
        <thead>
            <tr style="background-color: #f3f4f6;">
                <th style="border: 1px solid #d1d5db; padding: 8px; text-align: left;">Student</th>
                <th style="border: 1px solid #d1d5db; padding: 8px; text-align: left;">Grade / Class</th>
                <th style="border: 1px solid #d1d5db; padding: 8px; text-align: left;">Month</th>
                <th style="border: 1px solid #d1d5db; padding: 8px; text-align: left;">Year</th>
                <th style="border: 1px solid #d1d5db; padding: 8px; text-align: left;">Absence</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
                <tr>
                    <td style="border: 1px solid #d1d5db; padding: 8px;">{{ $log->student->name }}</td>
                    <td style="border: 1px solid #d1d5db; padding: 8px;">
                        {{ $log->subGrade->full_name ?? $log->subGrade->name }}
                    </td>
                    <td style="border: 1px solid #d1d5db; padding: 8px;">{{ $log->month->name }}</td>
                    <td style="border: 1px solid #d1d5db; padding: 8px;">{{ $log->year }}</td>
                    <td style="border: 1px solid #d1d5db; padding: 8px;">
                        {{ number_format((float) $log->absence_percentage, 2) }}%
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Best regards,<br>DDC Top-Up Team</p>
</div>
