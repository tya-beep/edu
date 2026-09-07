<x-mail::message>
# Offer Letter from Al Amin HR

Dear **{{ $applicant->full_name }}**,

We are pleased to inform you that your application has been successful. After careful review of your qualifications and interview performance, we are delighted to offer you a position as **Teacher** at **Al Amin HR**.

## Offer Details

| Detail | Information |
|--------|-------------|
| 📅 Offer Date | {{ date('d F Y', strtotime($offerDate)) }} |
| 👔 Position | Teacher |
| 🏫 Institution | Al Amin HR |
| 📍 Location | To be assigned |
| 📝 Status | Pending Your Response |

## Next Steps

Please review the attached offer letter for complete details including:
- Terms and conditions of employment
- Salary and benefits package
- Working hours and expectations
- Probation period details

## Your Response Required

Please indicate your decision by clicking one of the buttons below:

<x-mail::button :url="route('offer.accept', $application_id)" color="success">
✅ Accept Offer
</x-mail::button>

<x-mail::button :url="route('offer.reject', $application_id)" color="error">
❌ Decline Offer
</x-mail::button>

> **Note:** This offer is valid for **7 days** from the date of issue. If we do not hear from you by then, the offer will automatically expire.

If you have any questions, please don't hesitate to contact our HR department.

Thank you for considering this opportunity. We look forward to welcoming you to the Al Amin HR family!

Best regards,

**HR Department**
Al Amin HR System

<x-mail::subcopy>
If you're having trouble clicking the buttons, copy and paste the URLs below into your web browser:

Accept Offer: {{ route('offer.accept', $application_id) }}
Decline Offer: {{ route('offer.reject', $application_id) }}
</x-mail::subcopy>
</x-mail::message>