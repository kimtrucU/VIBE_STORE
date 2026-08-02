<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

$catId = DB::table('categories')->where('name', 'Sweaters')->value('id');

for ($i = 0; $i < 3; $i++) {
    $productName = 'Sweaters Item' . ($i > 0 ? ' - Variant ' . $i : '');
    $pId = DB::table('products')->insertGetId([
        'category_id' => $catId,
        'name' => $productName,
        'slug' => Str::slug($productName) . '-' . rand(1000, 9999),
        'description' => 'A nice sweater.',
        'price' => 450000,
        'sizes' => json_encode(['S', 'M', 'L', 'XL']),
        'rating' => 4.8,
        'reviews_count' => rand(10, 50),
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // map images
    $folder = 'long-sleeve';
    $fullFolderPath = __DIR__ . "/public/images/products/" . $folder;
    $files = array_diff(scandir($fullFolderPath), array('.', '..'));
    $files = array_values($files);
    
    $newImages = [];
    foreach ($files as $file) {
        $newImages[] = "images/products/" . $folder . "/" . $file;
    }
    DB::table('products')->where('id', $pId)->update(['images' => json_encode($newImages)]);
}
echo "Added Sweaters.\n";
