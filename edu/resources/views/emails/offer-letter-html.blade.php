<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offer Letter from Al Amin HR</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #059669, #047857);
            padding: 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .offer-details {
            background: #f0fdf4;
            border-left: 4px solid #059669;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .offer-details table {
            width: 100%;
        }
        .offer-details td {
            padding: 8px;
        }
        .button-group {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 0 10px;
            transition: transform 0.2s;
        }
        .btn-accept {
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
        }
        .btn-reject {
            background: #ef4444;
            color: white;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .note {
            background: #fef3c7;
            padding: 10px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>📧 Offer Letter</h1>
                <p>Al Amin HR System</p>
            </div>
            
            <div class="content">
                <h2>Dear {{ $applicant->full_name }},</h2>
                
                <p>We are pleased to inform you that your application has been successful. After careful review, we are delighted to offer you a position as <strong>Teacher</strong> at <strong>Al Amin HR</strong>.</p>
                
                <div class="offer-details">
                    <table>
                        <tr>
                            <td><strong>📅 Offer Date:</strong></td>
                            <td>{{ date('d F Y', strtotime($offerDate)) }}</td>
                        </tr>
                        <tr>
                            <td><strong>👔 Position:</strong></td>
                            <td>Teacher</td>
                        </tr>
                        <tr>
                            <td><strong>🏫 Institution:</strong></td>
                            <td>Al Amin HR</td>
                        </tr>
                        <tr>
                            <td><strong>📝 Status:</strong></td>
                            <td>Pending Your Response</td>
                        </tr>
                    </table>
                </div>
                
                <div class="button-group">
                    <a href="{{ route('offer.accept', $application_id) }}" class="btn btn-accept">✅ Accept Offer</a>
                    <a href="{{ route('offer.reject', $application_id) }}" class="btn btn-reject">❌ Decline Offer</a>
                </div>
                
                <div class="note">
                    <strong>⏰ Note:</strong> This offer is valid for 7 days from the date of issue.
                </div>
            </div>
            
            <div class="footer">
                <p>&copy; {{ date('Y') }} Al Amin HR. All rights reserved.</p>
                <p>This is an automated message, please do not reply.</p>
            </div>
        </div>
    </div>
</body>
</html>