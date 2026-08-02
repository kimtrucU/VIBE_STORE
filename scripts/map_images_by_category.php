<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$mapping = [
    'Accessories' => 'accessories',
    'Bomber' => 'bomber',
    'Flannel' => 'flannel',
    'Handmade' => 'handmade',
    'Shorts' => 'shorts',
    'T-Shirts' => 'long-sleeve', // Wait, earlier I mapped 'long-sleeve' to 'T-Shirts'
    'Loungewear' => 'loungewear',
    'Slippers' => 'slippers',
    'Zip Hoodies' => 'zip-hoodie',
    'Sweaters' => 'long-sleeve', // Or maybe I should use knit-shorts for something else? Let's just use what makes sense
];

// Let's re-read the categories and map to folders correctly
$products = Illuminate\Support\Facades\DB::table('products')
    ->join('categories', 'products.category_id', '=', 'categories.id')
    ->select('products.id', 'products.name', 'categories.name as category_name')
    ->get();

foreach ($products as $p) {
    $folder = '';
    
    if ($p->category_name == 'Accessories') $folder = 'accessories';
    if ($p->category_name == 'Bomber') $folder = 'bomber';
    if ($p->category_name == 'Flannel') $folder = 'flannel';
    if ($p->category_name == 'Handmade') $folder = 'handmade';
    if ($p->category_name == 'Shorts') {
        // give one shorts and the other knit-shorts
        $folder = str_contains($p->name, 'Knit') ? 'knit-shorts' : 'shorts';
    }
    if ($p->category_name == 'T-Shirts') $folder = 'long-sleeve';
    if ($p->category_name == 'Loungewear') $folder = 'loungewear';
    if ($p->category_name == 'Slippers') $folder = 'slippers';
    if ($p->category_name == 'Zip Hoodies') $folder = 'zip-hoodie';
    if ($p->category_name == 'Sweaters') $folder = 'long-sleeve'; // no sweaters folder, maybe just use long-sleeve

    if ($folder) {
        $fullFolderPath = __DIR__ . "/public/images/products/" . $folder;
        if (is_dir($fullFolderPath)) {
            $files = array_diff(scandir($fullFolderPath), array('.', '..'));
            $files = array_values($files);
            
            if (count($files) > 0) {
                $newImages = [];
                foreach ($files as $file) {
                    $newImages[] = "images/products/" . $folder . "/" . $file;
                }
                Illuminate\Support\Facades\DB::table('products')
                    ->where('id', $p->id)
                    ->update(['images' => json_encode($newImages)]);
                echo "Mapped " . $p->name . " -> " . $folder . "\n";
            }
        }
    }
}
echo "Done.\n";
