<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

DB::statement('SET FOREIGN_KEY_CHECKS=0;');
DB::table('categories')->truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

$userCategories = [
    'Sweaters', // Áo Len
    'Bomber', // Bomber
    'Accessories', // Phụ kiện
    'Flannel', // flannel
    'Handmade', // handmade
    'Slippers', // slippers
    'Zip Hoodies', // Áo khoác nỉ
    'T-Shirts', // Áo thun
    'Loungewear', // loungewear
    'Shorts', // Quần ngắn
];

foreach ($userCategories as $cat) {
    DB::table('categories')->insert([
        'name' => $cat,
        'slug' => Str::slug($cat),
        'description' => $cat . ' collection',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

$cats = DB::table('categories')->get()->keyBy('name');

// 10 folders mapping to 10 categories
$mapping = [
    'accessories' => 'Accessories',
    'bomber' => 'Bomber',
    'flannel' => 'Flannel',
    'handmade' => 'Handmade',
    'knit-shorts' => 'Shorts',
    'long-sleeve' => 'T-Shirts',
    'loungewear' => 'Loungewear',
    'shorts' => 'Shorts',
    'slippers' => 'Slippers',
    'zip-hoodie' => 'Zip Hoodies',
];

$products = DB::table('products')->orderBy('id')->get();

$i = 0;
foreach ($mapping as $folder => $catName) {
    if (!isset($products[$i])) break;
    $p = $products[$i];
    $catId = $cats[$catName]->id;
    
    // They want the product names to be like the categories (like in their screenshot: "Phụ kiện") but in English,
    // so we can just name the product the same as the category or folder.
    $productName = $catName . ' Item'; 
    if ($catName === 'Accessories') $productName = 'Accessory Item';
    if ($catName === 'Shorts') $productName = $folder === 'knit-shorts' ? 'Knit Shorts' : 'Casual Shorts';

    DB::table('products')->where('id', $p->id)->update([
        'category_id' => $catId,
        'name' => $productName,
        'slug' => Str::slug($productName),
        'price' => 450000,
        'sizes' => json_encode(['S', 'M', 'L', 'XL']),
        'rating' => 4.8,
        'reviews_count' => rand(10, 50),
    ]);
    
    $i++;
}

echo "Restored the 10 exact categories and mapped products.\n";
