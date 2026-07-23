<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$products = Illuminate\Support\Facades\DB::table('products')->get(['id', 'images']);

foreach ($products as $p) {
    $imagesArr = json_decode($p->images, true);
    if (!$imagesArr) continue;
    
    $updated = false;
    foreach ($imagesArr as &$img) {
        if (!str_starts_with($img, '/')) {
            $img = '/' . $img;
            $updated = true;
        }
    }
    
    if ($updated) {
        Illuminate\Support\Facades\DB::table('products')
            ->where('id', $p->id)
            ->update(['images' => json_encode($imagesArr)]);
        echo "Prefixed images for product ID {$p->id}\n";
    }
}
echo "Done prefixing images with /.\n";
