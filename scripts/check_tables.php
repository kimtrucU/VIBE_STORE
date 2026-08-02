<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    if (Schema::hasColumn('products', 'sale_price') && !Schema::hasColumn('products', 'price')) {
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('sale_price', 'price');
        });
        echo "Renamed sale_price to price\n";
    }

    if (Schema::hasColumn('products', 'base_price') && Schema::hasColumn('products', 'original_price')) {
        // Drop base_price if original_price already exists and we don't need base_price
        // Or copy data over. Let's just run an update query.
        DB::statement('UPDATE products SET original_price = base_price WHERE original_price IS NULL');
        echo "Copied base_price to original_price\n";
    }

    echo "Category count: " . App\Models\Category::count() . "\n";
    echo "Product count: " . App\Models\Product::count() . "\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
