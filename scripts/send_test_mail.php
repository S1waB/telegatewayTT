<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use App\Mail\NewUserWelcomeMail;
use App\Models\User;

$u = User::first();
if (! $u) {
    echo "NO_USER\n";
    exit(0);
}

try {
    Mail::to('you@example.com')->send(new NewUserWelcomeMail($u, 'testpwd'));
    echo "SENT\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
