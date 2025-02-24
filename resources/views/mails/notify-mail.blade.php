<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notification Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            text-align: center;
            padding: 20px 0;
            background-color: #007bff;
            color: #ffffff;
            font-size: 24px;
            font-weight: bold;
            border-radius: 8px 8px 0 0;
        }

        .email-body {
            padding: 20px;
            font-size: 16px;
            color: #333333;
        }

        .email-footer {
            text-align: center;
            padding: 20px;
            font-size: 14px;
            color: #777777;
            border-top: 1px solid #dddddd;
        }

        .btn {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 20px;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        @media screen and (max-width: 600px) {
            .email-container {
                width: 100%;
                padding: 10px;
            }
        }
    </style>
</head>

<body>

    <div class="email-container">
        <div class="email-header">
            Notification Alert
        </div>
        <div class="email-body">
            <p>Dear User,</p>
            <p>You have a new notification. Please review the details below:</p>
            <p><strong>Subject:</strong> Your Important Update</p>
            <p><strong>Message:</strong> This is a sample notification message for you.</p>
            <p>Click the button below to view more details:</p>
            <p><a href="#" class="btn">View Notification</a></p>
            <p>Thank you,<br> The Team</p>
        </div>
        <div class="email-footer">
            © 2025 Your Company. All rights reserved.
        </div>
    </div>

</body>

</html>
