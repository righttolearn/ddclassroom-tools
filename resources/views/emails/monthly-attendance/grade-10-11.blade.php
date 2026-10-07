<div style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <div dir="ltr">
        <p>Dear {{ $student->name }},</p>

        <p>
            You did not receive a top-up this month because of low progress in your OVS courses and/or
            high absenteeism from your DDC classes.
        </p>

        <p>Your absence rate was more than 30% this month. Please make sure to:</p>

        <ul>
            <li>Stay active and make regular progress in your OVS courses.</li>
            <li>Attend your DDC classes regularly.</li>
            <li>Complete your required coursework and activities.</li>
            <li>Stay connected with your courses and keep up with your studies.</li>
        </ul>

        <p>
            Please work on improving your course progress and attendance so that you can be eligible to
            receive a top-up next month.
        </p>

        <p>Best regards,<br>DDC Top-Up Team</p>
    </div>

    <hr style="margin: 28px 0; border: 0; border-top: 1px solid #d1d5db;">

    <div dir="rtl" style="text-align: right; font-family: Tahoma, Arial, sans-serif;">
        <p>{{ $student->fa_name ?: $student->name }} عزیز،</p>

        <p>
            شما در این ماه به دلیل پیشرفت کم در کورس‌های OVS و/یا غیرحاضری زیاد در صنف‌های DDC
            تاپ‌آپ دریافت نکردید.
        </p>

        <p>میزان غیرحاضری شما در این ماه بیشتر از ۳۰٪ بوده است. لطفاً موارد زیر را رعایت کنید:</p>

        <ul>
            <li>در کورس‌های OVS فعال باشید و به‌طور منظم پیشرفت کنید.</li>
            <li>در صنف‌های DDC به‌طور منظم اشتراک کنید.</li>
            <li>وظایف و فعالیت‌های درسی خود را به‌موقع تکمیل کنید.</li>
            <li>با کورس‌های خود در ارتباط باشید و به درس‌های خود به‌طور منظم ادامه دهید.</li>
        </ul>

        <p>
            لطفاً برای بهبود پیشرفت درسی و حضور خود تلاش کنید تا در ماه آینده واجد شرایط دریافت تاپ‌آپ باشید.
        </p>

        <p>با احترام،<br>تیم تاپ‌آپ دی دی سی</p>
    </div>
</div>
