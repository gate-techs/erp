<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$subs = Illuminate\Support\Facades\DB::table('push_subscriptions')->get();
echo "Total Push Subscriptions in DB: " . $subs->count() . PHP_EOL;
foreach ($subs as $s) {
    echo "ID: {$s->id} | User: {$s->user_id} | Endpoint: " . substr($s->endpoint, 0, 50) . "... | Hash: {$s->endpoint_hash}" . PHP_EOL;
}
