<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Offer Letter</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            line-height: 1.6;
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }
        .text-center { text-align: center; }
        .mb-4 { margin-bottom: 20px; }
        .mb-5 { margin-bottom: 40px; }
        .mt-4 { margin-top: 20px; }
        .mt-5 { margin-top: 40px; }
        h2 { font-size: 20px; margin-bottom: 5px; }
        h3 { font-size: 18px; margin-top: 20px; margin-bottom: 10px; }
        h5 { font-size: 16px; margin-top: 15px; margin-bottom: 5px; }
        hr { border: 1px solid #333; margin: 20px 0; }
        ul { padding-left: 20px; }
        .signature { margin-top: 50px; }
        .footer { 
            position: fixed; 
            bottom: 0; 
            width: 100%; 
            text-align: center; 
            font-size: 10px; 
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="text-center mb-5">
        <h2>MINISTRY OF EDUCATION MALAYSIA</h2>
        <h4>Human Resource Division</h4>
        <p>Level 4, Block E8, Complex E, Federal Government Administrative Centre<br>
        62604 Putrajaya, Malaysia</p>
        <hr>
        <h3>OFFER LETTER OF APPOINTMENT</h3>
        <p><strong>Reference No:</strong> {{ $referenceNo }}</p>
    </div>

    <div class="mb-4">
        <p><strong>Date:</strong> {{ $date }}</p>
    </div>

    <div class="mb-4">
        <p>
            <strong>{{ $applicantName }}</strong><br>
            {{ $address }}<br>
            IC No: {{ $icNumber }}
        </p>
    </div>

    <div class="mb-4">
        <p><strong>REF: OFFER OF APPOINTMENT AS A TEACHER</strong></p>
    </div>

    <div class="mb-4">
        <p>Dear <strong>{{ $applicantName }}</strong>,</p>
        
        <p>We are pleased to offer you the position of <strong>Teacher</strong> at the Ministry of Education Malaysia.</p>
        
        <h5 class="mt-4">1. Salary</h5>
        <p>Your expected monthly salary is <strong>RM {{ number_format($expectedSalary, 2) }}</strong>.</p>
        
        <h5>2. Probation Period</h5>
        <p>You will undergo a probation period of <strong>12 months</strong>.</p>
        
        <h5>3. Acceptance</h5>
        <p>Please indicate your acceptance of this offer within 14 days.</p>
        
        <p>Yours sincerely,</p>
        <div class="signature">
            <p><strong>_________________________</strong><br>
            <strong>Director of Human Resources</strong><br>
            Ministry of Education Malaysia</p>
        </div>
    </div>

    <div class="footer">
        This is a system-generated document. No signature is required.
    </div>
</body>
</html>