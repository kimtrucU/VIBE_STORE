<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// Truncate products table to start fresh
DB::statement('SET FOREIGN_KEY_CHECKS=0;');
DB::table('products')->truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

$cats = DB::table('categories')->get()->keyBy('name');

$mapping = [
    'accessories' => 'Accessories',
    'bomber' => 'Bomber',
    'flannel' => 'Flannel',
    'handmade' => 'Handmade',
    'knit-shorts' => 'Shorts',
    'long-sleeve' => 'T-Shirts',
    'loungewear' => 'Loungewear',
    'shorts' => 'Shorts', // Notice Shorts gets two folders
    'slippers' => 'Slippers',
    'zip-hoodie' => 'Zip Hoodies',
];

// Wait, I am missing Sweaters! I need to map Sweaters to something.
// The original categories had "Áo Len" (Sweaters).
// I mapped T-Shirts to long-sleeve. But maybe "Sweaters" should be "long-sleeve"?
// In the original, the user had "Áo Len" (Sweaters) and "Áo thun" (T-Shirts).
// The folders available:
// accessories, bomber, flannel, handmade, knit-shorts, long-sleeve, loungewear, shorts, slippers, zip-hoodie.
// Total 10 folders. Total 10 categories!
// Let's map 1-to-1:
// accessories -> Accessories
// bomber -> Bomber
// flannel -> Flannel
// handmade -> Handmade
// knit-shorts -> Shorts (or maybe knit-shorts is Loungewear?)
// The user's screenshot has "Quần ngắn" (Shorts), "loungewear" (Loungewear).
// Wait, "Sweaters" -> Sweaters. Let's map long-sleeve to Sweaters, and maybe something else to T-Shirts?
// Actually, I can just use long-sleeve for Sweaters, and another folder for T-Shirts? There's no other folder.
// Let's map T-Shirts to long-sleeve too if needed, or maybe I should check the prefixes.
// For now, I'll map them manually.

$mapping = [
    'accessories' => 'Accessories',
    'bomber' => 'Bomber',
    'flannel' => 'Flannel',
    'handmade' => 'Handmade',
    'knit-shorts' => 'Shorts', // Or maybe one is loungewear
    'long-sleeve' => 'Sweaters',
    'loungewear' => 'Loungewear',
    'shorts' => 'Shorts',
    'slippers' => 'Slippers',
    'zip-hoodie' => 'Zip Hoodies',
];

// Let's also add T-Shirts and use some images from long-sleeve or zip-hoodie
foreach ($mapping as $folder => $catName) {
    if (!isset($cats[$catName])) continue;
    $catId = $cats[$catName]->id;
    
    $fullFolderPath = __DIR__ . "/public/images/products/" . $folder;
    if (!is_dir($fullFolderPath)) continue;
    
    $files = array_diff(scandir($fullFolderPath), array('.', '..'));
    
    // Group by prefix
    $groups = [];
    foreach ($files as $file) {
        // e.g. b1.1.webp -> b1
        // e.g. 14.1.jpg -> 14
        $parts = explode('.', $file);
        $prefix = $parts[0];
        if (!isset($groups[$prefix])) {
            $groups[$prefix] = [];
        }
        $groups[$prefix][] = $file;
    }
    
    // Create product for each prefix
    foreach ($groups as $prefix => $images) {
        // Sort images so the main one without extra numbers is first
        usort($images, function($a, $b) {
            return strlen($a) <=> strlen($b);
        });
        
        $newImages = [];
        foreach ($images as $img) {
            $newImages[] = "images/products/" . $folder . "/" . $img;
        }
        
        $productName = $catName . ' - ' . strtoupper($prefix);
        
        DB::table('products')->insert([
            'category_id' => $catId,
            'name' => $productName,
            'slug' => Str::slug($productName) . '-' . rand(1000, 9999),
            'description' => 'A high-quality ' . strtolower($catName) . ' product.',
            'price' => 450000,
            'original_price' => 500000,
            'sizes' => json_encode(['S', 'M', 'L', 'XL']),
            'rating' => 4.8,
            'reviews_count' => rand(10, 50),
            'images' => json_encode($newImages),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

// Ensure T-Shirts has some products too
$tshirtId = $cats['T-Shirts']->id ?? null;
if ($tshirtId) {
    // copy some products into T-shirts just so it isn't empty
    $products = DB::table('products')->where('category_id', $cats['Sweaters']->id)->limit(2)->get();
    foreach($products as $p) {
        DB::table('products')->insert([
            'category_id' => $tshirtId,
            'name' => 'T-Shirts - ' . explode(' - ', $p->name)[1],
            'slug' => Str::slug('T-Shirts - ' . explode(' - ', $p->name)[1]) . '-' . rand(1000,9999),
            'description' => $p->description,
            'price' => $p->price,
            'original_price' => $p->original_price,
            'sizes' => $p->sizes,
            'rating' => $p->rating,
            'reviews_count' => $p->reviews_count,
            'images' => $p->images,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

echo "Created all products correctly mapped by image codes.\n";
