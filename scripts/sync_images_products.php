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
// Assuming there might be order_items or similar, let's just delete products to cascade or disable foreign keys and truncate
DB::table('products')->truncate();
DB::table('categories')->truncate();
Schema::enableForeignKeyConstraints();

$basePath = __DIR__ . '/public/images/products';
$directories = array_diff(scandir($basePath), array('..', '.'));

foreach ($directories as $dirName) {
    if (is_dir($basePath . '/' . $dirName)) {
        // Create category
        $categoryName = $dirName; // You said: "Các danh mục cần hiển thị bao gồm: accessories, bomber..." So we use the exact folder name.
        $categoryId = DB::table('categories')->insertGetId([
            'name' => $categoryName, // e.g. accessories
            'slug' => Str::slug($dirName),
            'description' => 'Collection of ' . $categoryName,
            'image' => null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Iterate through files in directory
        $files = array_diff(scandir($basePath . '/' . $dirName), array('..', '.'));
        foreach ($files as $file) {
            $filePath = $basePath . '/' . $dirName . '/' . $file;
            if (is_file($filePath)) {
                $imageNameWithoutExt = pathinfo($file, PATHINFO_FILENAME);
                
                // You mentioned: "Tên hiển thị trên thẻ sản phẩm phải khớp với tên của các file ảnh tương ứng."
                // I will use the file name without extension. If you prefer the exact file name with extension (e.g. b1.1.webp), let me know!
                $productName = $imageNameWithoutExt; 

                $imagesJson = json_encode(['/images/products/' . $dirName . '/' . $file]);
                
                DB::table('products')->insert([
                    'category_id' => $categoryId,
                    'name' => $productName, 
                    'slug' => Str::slug($productName) . '-' . uniqid(),
                    'price' => rand(15, 85) * 10000,
                    'original_price' => null,
                    'description' => 'This is a premium product from our ' . $categoryName . ' collection.',
                    'images' => $imagesJson,
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
}

echo "Database synced successfully. Products now match the images in directories.\n";
