<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$products = Illuminate\Support\Facades\DB::table('products')->get(['id', 'name', 'images']);
foreach ($products as $p) {
    echo $p->id . ' - ' . $p->name . " => " . $p->images . "\n";
}
