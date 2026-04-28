<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: Arial, sans-serif; background: #F8FAFC; margin: 0; padding: 20px; }
    .card { background: #fff; max-width: 560px; margin: 0 auto; border-radius: 12px; padding: 40px; border-top: 4px solid #E63946; }
    .logo { font-size: 22px; font-weight: bold; color: #1A6FBF; margin-bottom: 24px; }
    h1 { color: #1F2937; font-size: 20px; margin: 0 0 16px; }
    p { color: #4B5563; line-height: 1.6; font-size: 15px; }
    .creds { background: #FEE2E2; border-radius: 8px; padding: 16px 20px; margin: 20px 0; border: 1px solid #FECACA; }
    .creds table { width: 100%; border-collapse: collapse; font-size: 14px; }
    .creds td { padding: 6px 0; }
    .creds td:first-child { color: #7F1D1D; width: 130px; }
    .creds td:last-child { font-weight: bold; color: #991B1B; word-break: break-all; font-family: monospace; font-size: 16px; }
    .btn { display: inline-block; background: #1A6FBF; color: #fff; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-top: 16px; }
    .footer { color: #9CA3AF; font-size: 12px; margin-top: 32px; border-top: 1px solid #E5E7EB; padding-top: 16px; }
  </style>
</head>
<body>
<div class="card">
  <div class="logo">TeleGateway</div>
  <h1>Password Reset Notification</h1>
  <p>Hello {{ $user->name }},</p>
  <p>An administrator has reset your password for the TeleGateway Platform. You can now log in using the temporary password provided below:</p>
  <div class="creds">
    <table>
      <tr><td>Temporary Password</td><td>{{ $newPassword }}</td></tr>
    </table>
  </div>
  <p><strong>Security Note:</strong> For your protection, please change this password immediately after logging in.</p>
  <a href="{{ config('app.url') }}/login" class="btn">Login to Platform</a>
  <div class="footer">
    If you did not request this change or believe this was done in error, please contact support immediately.<br>
    &copy; {{ date('Y') }} TeleGateway.
  </div>
</div>
</body>
</html>
