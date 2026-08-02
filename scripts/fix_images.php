<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$products = Illuminate\Support\Facades\DB::table('products')->get(['id', 'name', 'images']);

foreach ($products as $p) {
    // The images column currently has ["images/products/zip-hoodie/1.jpg",...]
    $imagesArr = json_decode($p->images, true);
    if (!$imagesArr || count($imagesArr) == 0) continue;
    
    // Extract folder from the first image path
    $pathParts = explode('/', $imagesArr[0]);
    if (count($pathParts) >= 4) {
        $folder = $pathParts[2]; // images/products/{folder}/1.jpg
        $fullFolderPath = __DIR__ . "/public/images/products/" . $folder;
        
        if (is_dir($fullFolderPath)) {
            $files = array_diff(scandir($fullFolderPath), array('.', '..'));
            $files = array_values($files);
            
            if (count($files) > 0) {
                $newImages = [];
                // take all images
                foreach ($files as $file) {
                    $newImages[] = "images/products/" . $folder . "/" . $file;
                }
                Illuminate\Support\Facades\DB::table('products')
                    ->where('id', $p->id)
                    ->update(['images' => json_encode($newImages)]);
                echo "Updated " . $p->name . " with real images from " . $folder . "\n";
            } else {
                echo "Folder empty: " . $folder . "\n";
            }
        } else {
            echo "Folder not found: " . $folder . "\n";
        }
    }
}
echo "Done fixing images.\n";
