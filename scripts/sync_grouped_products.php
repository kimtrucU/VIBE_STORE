<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

Schema::disableForeignKeyConstraints();
DB::table('wishlists')->truncate();
DB::table('carts')->truncate();
DB::table('products')->truncate();
DB::table('categories')->truncate();
Schema::enableForeignKeyConstraints();

$basePath = __DIR__ . '/public/images/products';
$directories = array_diff(scandir($basePath), array('..', '.'));

foreach ($directories as $dirName) {
    if (is_dir($basePath . '/' . $dirName)) {
        $categoryName = $dirName;
        $categoryTitle = Str::title(str_replace('-', ' ', $dirName));
        
        $categoryId = DB::table('categories')->insertGetId([
            'name' => $categoryName,
            'slug' => Str::slug($categoryName),
            'description' => 'Collection of ' . $categoryTitle,
            'image' => null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $files = array_diff(scandir($basePath . '/' . $dirName), array('..', '.'));
        $productsMap = [];

        foreach ($files as $file) {
            $filePath = $basePath . '/' . $dirName . '/' . $file;
            if (is_file($filePath)) {
                // Get the base code (e.g. "14" from "14.1.webp", "b1" from "b1.webp")
                $parts = explode('.', $file);
                $baseCode = $parts[0];

                if (!isset($productsMap[$baseCode])) {
                    $productsMap[$baseCode] = [];
                }
                $productsMap[$baseCode][] = '/images/products/' . $dirName . '/' . $file;
            }
        }

        foreach ($productsMap as $baseCode => $images) {
            // Sort images: main image (e.g. 14.webp) first, then 14.1, 14.2
            usort($images, function($a, $b) use ($baseCode) {
                $aIsMain = preg_match('/\/'.preg_quote($baseCode, '/').'\.[a-zA-Z0-9]+$/', $a) ? 1 : 0;
                $bIsMain = preg_match('/\/'.preg_quote($baseCode, '/').'\.[a-zA-Z0-9]+$/', $b) ? 1 : 0;
                
                if ($aIsMain != $bIsMain) {
                    return $bIsMain - $aIsMain; // main image comes first
                }
                return strcmp($a, $b); // alphabetical for the rest (14.1 before 14.2)
            });

            // Name format: Category Name + Base Code (e.g. "Shorts 14", "Accessories B1")
            $productName = $categoryTitle . ' ' . strtoupper($baseCode);

            DB::table('products')->insert([
                'category_id' => $categoryId,
                'name' => $productName, 
                'slug' => Str::slug($productName) . '-' . uniqid(),
                'price' => rand(15, 85) * 10000,
                'original_price' => null,
                'description' => 'Premium ' . $productName . ' from our ' . $categoryTitle . ' collection.',
                'images' => json_encode($images),
                'sizes' => json_encode(['S', 'M', 'L', 'XL']),
                'details' => json_encode(['Material' => 'Premium', 'Fit' => 'Regular']),
                'stock' => 100,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

echo "Database synced successfully. Images are now grouped by product code.\n";
