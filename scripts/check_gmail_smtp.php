<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

echo 'MAIL_MAILER='.env('MAIL_MAILER')."\n";
echo 'MAIL_HOST='.env('MAIL_HOST')."\n";
echo 'MAIL_PORT='.env('MAIL_PORT')."\n";
echo 'MAIL_USERNAME='.env('MAIL_USERNAME')."\n";
echo 'MAIL_ENCRYPTION='.env('MAIL_ENCRYPTION')."\n";
echo 'CONFIG_MAIL_DEFAULT='.Config::get('mail.default')."\n";
echo 'CONFIG_QUEUE_DEFAULT='.Config::get('queue.default')."\n";
echo 'JOBS_COUNT=' . DB::table('jobs')->count() . "\n";
echo 'FAILED_JOBS_COUNT=' . DB::table('failed_jobs')->count() . "\n";

try {
    Mail::raw('Gmail SMTP test from TeleGateway', function ($message) {
        $message->to('you@example.com')->subject('TeleGateway SMTP Test');
    });
    echo "SEND_OK\n";
} catch (Throwable $e) {
    echo 'SEND_ERROR=' . $e->getMessage() . "\n";
}
