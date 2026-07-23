<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$product = App\Models\Product::where('name', 'like', '%Bomber - C1%')->first();
if ($product) {
    echo "Bomber C1 images: " . json_encode($product->images) . "\n";
} else {
    echo "Bomber C1 not found\n";
}
