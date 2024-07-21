<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #007bff;
            color: #ffffff;
            text-align: center;
            padding: 10px;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }
        .content {
            padding: 20px;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Password Reset</h2>
    </div>
    <div class="content">
        <p>Hello,</p>
        <p>We received a request to reset your account password. Please use the following code to reset your password:</p>
        <p style="text-align: center; font-size: 24px; font-weight: bold;">{{ $code }}</p>
        <p>The code is valid for one hour from the time this email was sent.</p>
        <p>If you didn't request this, you can safely ignore this email.</p>
        <p>Thank you!</p>
    </div>
</div>
</body>
</html>
