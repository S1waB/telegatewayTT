<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Inter', sans-serif; line-height: 1.6; color: #334155; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; padding: 40px; border: 1px solid #E2E8F0; border-radius: 12px; background-color: #ffffff; }
        .header { text-align: center; margin-bottom: 40px; }
        .logo { max-width: 180px; }
        .content { font-size: 16px; margin-bottom: 40px; white-space: pre-line; }
        .footer { text-align: center; color: #94A3B8; font-size: 12px; border-top: 1px solid #F1F5F9; padding-top: 30px; }
        .accent { color: #1A6FBF; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 class="accent">TeleGateway Announcement</h2>
        </div>
        <div class="content">
            {{ $messageText }}
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} TeleGateway. All rights reserved.<br>
            This is an official communication from your platform administrator.
        </div>
    </div>
</body>
</html>
