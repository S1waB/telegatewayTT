<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: Arial, sans-serif; background: #F0F6FF; margin: 0; padding: 20px; }
    .card { background: #fff; max-width: 560px; margin: 0 auto; border-radius: 12px; padding: 40px; border-top: 4px solid #1A6FBF; }
    .logo { font-size: 22px; font-weight: bold; color: #1A6FBF; margin-bottom: 24px; }
    h1 { color: #0D4A8A; font-size: 20px; margin: 0 0 16px; }
    p { color: #444; line-height: 1.6; font-size: 15px; }
    .creds { background: #E8F1FA; border-radius: 8px; padding: 16px 20px; margin: 20px 0; }
    .creds table { width: 100%; border-collapse: collapse; font-size: 14px; }
    .creds td { padding: 6px 0; }
    .creds td:first-child { color: #555; width: 130px; }
    .creds td:last-child { font-weight: bold; color: #0D4A8A; word-break: break-all; }
    .btn { display: inline-block; background: #1A6FBF; color: #fff; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-top: 16px; }
    .footer { color: #999; font-size: 12px; margin-top: 32px; border-top: 1px solid #eee; padding-top: 16px; }
    .warning { background: #FFF3CD; border-radius: 6px; padding: 10px 14px; font-size: 13px; color: #856404; margin-top: 12px; }
  </style>
</head>
<body>
<div class="card">
  <div class="logo">TeleGateway</div>
  <h1>Welcome, {{ $user->name }}!</h1>
  <p>Your account on the TeleGateway IoT Management Platform has been created. Below are your login credentials:</p>
  <div class="creds">
    <table>
      <tr><td>Platform URL</td><td>{{ config('app.url') }}</td></tr>
      <tr><td>Email address</td><td>{{ $user->email }}</td></tr>
      <tr><td>Password</td><td>{{ $plainPassword }}</td></tr>
      <tr><td>Role</td><td>{{ $user->roles->first()?->name ?? 'N/A' }}</td></tr>
    </table>
  </div>
  <div class="warning">
    For security, please change your password immediately after your first login.
  </div>
  @if($user->phone_number)
  <p style="margin-top:12px;font-size:13px;color:#555;">Phone registered: {{ $user->phone_number }}</p>
  @endif
  <a href="{{ config('app.url') }}/login" class="btn">Log in to TeleGateway</a>
  <div class="footer">
    This email was sent automatically. If you did not expect this account, please contact your system administrator.<br>
    &copy; {{ date('Y') }} TeleGateway — All rights reserved.
  </div>
</div>
</body>
</html>
