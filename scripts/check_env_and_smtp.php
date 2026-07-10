<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Events\MessageSending;

echo 'CONFIG_MAIL_DEFAULT=' . Config::get('mail.default') . PHP_EOL;
echo 'CONFIG_MAILER_HOST=' . Config::get('mail.mailers.smtp.host') . PHP_EOL;
echo 'CONFIG_MAILER_PORT=' . Config::get('mail.mailers.smtp.port') . PHP_EOL;
echo 'CONFIG_MAILER_USER=' . Config::get('mail.mailers.smtp.username') . PHP_EOL;
echo 'CONFIG_MAILER_ENCRYPTION=' . Config::get('mail.mailers.smtp.encryption') . PHP_EOL;
echo 'CONFIG_QUEUE_DEFAULT=' . Config::get('queue.default') . PHP_EOL;

try {
    Mail::raw('SMTP verification', function ($message) {
        $message->to('you@example.com')->subject('SMTP Verification');
    });
    echo 'SEND_OK' . PHP_EOL;
} catch (Throwable $e) {
    echo 'SEND_ERROR=' . $e->getMessage() . PHP_EOL;
}
