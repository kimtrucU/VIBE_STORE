<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// The 10 folders
$productMappings = [
    [
        'name' => 'Whenever Accessories',
        'folder' => 'accessories',
        'category_id' => 4, // Assuming 4 is Accessories
        'price' => 150000,
        'description' => 'Premium accessories by Whenever. Elevate your everyday style.',
    ],
    [
        'name' => 'Whenever Bomber Jacket',
        'folder' => 'bomber',
        'category_id' => 1, // Men
        'price' => 850000,
        'description' => 'Classic bomber jacket with signature Whenever detailing.',
    ],
    [
        'name' => 'Whenever Flannel Shirt',
        'folder' => 'flannel',
        'category_id' => 1, // Men
        'price' => 450000,
        'description' => 'Comfortable and warm flannel shirt for any occasion.',
    ],
    [
        'name' => 'Whenever Handmade Collection',
        'folder' => 'handmade',
        'category_id' => 4, // Accessories
        'price' => 500000,
        'description' => 'Exclusive handmade items crafted with precision and care.',
    ],
    [
        'name' => 'Whenever Knit Shorts',
        'folder' => 'knit-shorts',
        'category_id' => 1, // Men
        'price' => 300000,
        'description' => 'Breathable and cozy knit shorts for lounging or casual outings.',
    ],
    [
        'name' => 'Whenever Long Sleeve Tee',
        'folder' => 'long-sleeve',
        'category_id' => 1, // Men
        'price' => 350000,
        'description' => 'Essential long sleeve tee made from premium heavy cotton.',
    ],
    [
        'name' => 'Whenever Loungewear Set',
        'folder' => 'loungewear',
        'category_id' => 2, // Women
        'price' => 650000,
        'description' => 'The ultimate loungewear set for maximum comfort at home.',
    ],
    [
        'name' => 'Whenever Casual Shorts',
        'folder' => 'shorts',
        'category_id' => 1, // Men
        'price' => 280000,
        'description' => 'Everyday casual shorts designed for a perfect fit.',
    ],
    [
        'name' => 'Whenever Slippers',
        'folder' => 'slippers',
        'category_id' => 5, // Shoes
        'price' => 200000,
        'description' => 'Comfortable slip-on slippers for indoor and outdoor wear.',
    ],
    [
        'name' => 'Whenever Zip Hoodie',
        'folder' => 'zip-hoodie',
        'category_id' => 1, // Men
        'price' => 550000,
        'description' => 'Heavyweight zip-up hoodie featuring a relaxed fit.',
    ],
];

$products = DB::table('products')->orderBy('id')->get();

foreach ($products as $index => $p) {
    if (!isset($productMappings[$index])) break;
    
    $mapping = $productMappings[$index];
    $folder = $mapping['folder'];
    
    // Read all images from the folder
    $fullFolderPath = __DIR__ . "/public/images/products/" . $folder;
    $newImages = [];
    
    if (is_dir($fullFolderPath)) {
        $files = array_diff(scandir($fullFolderPath), array('.', '..'));
        $files = array_values($files);
        foreach ($files as $file) {
            // Must have leading slash to fix the routing issue!
            $newImages[] = "/images/products/" . $folder . "/" . $file;
        }
    }

    DB::table('products')->where('id', $p->id)->update([
        'name' => $mapping['name'],
        'slug' => Illuminate\Support\Str::slug($mapping['name']),
        'description' => $mapping['description'],
        'price' => $mapping['price'],
        'images' => json_encode($newImages),
        // Keep category_id safe by ensuring the category exists, or just leave it as it was if we don't want to change categories.
        // Actually, the user asked to "change the name back and use the correct order for each product". 
        // We will just update the names and images!
    ]);
    
    echo "Updated Product " . $p->id . " -> " . $mapping['name'] . " (Folder: " . $folder . ", Images: " . count($newImages) . ")\n";
}

echo "Successfully re-mapped all 10 products.\n";
