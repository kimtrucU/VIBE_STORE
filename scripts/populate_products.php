<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

$products = DB::table('products')->get();

foreach ($products as $p) {
    // Duplicate the product twice to have 3 identical products per category
    for ($i = 0; $i < 2; $i++) {
        DB::table('products')->insert([
            'category_id' => $p->category_id,
            'name' => $p->name . ' - Variant ' . ($i + 1),
            'slug' => Str::slug($p->name . ' - Variant ' . ($i + 1)) . '-' . rand(1000, 9999),
            'description' => $p->description,
            'price' => $p->price,
            'original_price' => $p->original_price,
            'images' => $p->images,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

echo "Duplicated products to populate the shop.\n";
