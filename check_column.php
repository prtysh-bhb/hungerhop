<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$result = \DB::select("SHOW COLUMNS FROM orders WHERE Field = 'payment_status'");
echo "payment_status column type: " . $result[0]->Type . "\n";
