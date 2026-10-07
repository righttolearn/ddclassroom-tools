<div style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <div dir="ltr">
        <p>Dear {{ $student->name }},</p>

        <p>
            You did not receive a top-up this month because your absence rate was more than <b>30%</b>
            according to the attendance records.
        </p>

        <p>
            Please make sure to attend your online classes regularly <b>(using your personal internet)</b>
            and reduce your absences so that you can be eligible to receive a top-up in the following months.
        </p>

        <p>
            Please note that our attendance system is automated and accurate, and your attendance is
            recorded based on your actual participation in classes. Therefore, you do not need to contact
            anyone to confirm that you were present in class.
        </p>

        <p>
            You should be careful. If you do not attend your classes this month, this may cause you not to
            receive the top-up next month, and the process will continue. It may eventually lead to you
            dropping out. Therefore, your attendance in the current month, at your own cost, is extremely
            important for us to see your commitment.
        </p>

        <p>Best regards,<br>DDC Top-Up Team</p>
    </div>

    <hr style="margin: 28px 0; border: 0; border-top: 1px solid #d1d5db;">

    <div dir="rtl" style="text-align: right; font-family: Tahoma, Arial, sans-serif;">
        <p>{{ $student->fa_name ?: $student->name }} عزیز،</p>

        <p>
            شما در این ماه تاپ‌آپ <b>(بسته اینترنتی)</b> دریافت نکرده‌اید، زیرا طبق سوابق حضور و غیاب，
            میزان غیرحاضری شما بیشتر از <b>۳۰٪</b>  بوده است.
        </p>

        <p>
            لطفاً اطمینان حاصل کنید که در کلاس‌های آنلاین خود به‌طور منظم شرکت می‌کنید
           <b> (با استفاده از اینترنت شخصی خودتان) </b> و میزان غیرحاضری خود را کاهش می‌دهید تا در ماه‌های بعد
            واجد شرایط دریافت تاپ‌آپ باشید.
        </p>

        <p>
            لطفاً توجه داشته باشید که سیستم حضور و غیاب ما خودکار و دقیق است و حضور شما بر اساس مشارکت
            واقعی شما در کلاس‌ها ثبت می‌شود. بنابراین، نیازی نیست برای تأیید حضور خود با کسی تماس بگیرید.
        </p>

        <p>
            همچنین، لطفاً توجه داشته باشید که <b>حضور منظم شما در ماه جاری، با استفاده از اینترنت شخصی و با
            هزینه خودتان، بسیار مهم است.</b> اگر در این ماه در کلاس‌های خود شرکت نکنید، ممکن است در ماه آینده
            نیز تاپ‌آپ دریافت نکنید و این روند ادامه پیدا کند. در نهایت، تداوم غیرحاضری می‌تواند منجر به
            <b>حذف شما از برنامه/ترک تحصیل از مکتب شود.</b>
        </p>

        <p>
            بنابراین، لطفاً حضور و مشارکت منظم خود را جدی بگیرید و تعهد خود را نسبت به ادامه تحصیل نشان دهید.
        </p>

        <p>با احترام،<br>تیم تاپ‌آپ دی دی سی</p>
    </div>
</div>
