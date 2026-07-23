<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // 1. Translate Categories
    $categoryTranslations = [
        'Áo khoác' => 'Jackets',
        'Áo thun' => 'T-Shirts',
        'Áo len' => 'Sweaters',
        'Quần jean' => 'Jeans',
        'Quần ngắn' => 'Shorts',
        'Phụ kiện' => 'Accessories',
        'Quần' => 'Pants',
        'Áo' => 'Shirts',
        'Giày' => 'Shoes',
        'Túi xách' => 'Bags'
    ];

    foreach ($categoryTranslations as $vi => $en) {
        DB::table('categories')->where('name', $vi)->update(['name' => $en]);
        // Note: we might want to regenerate slugs or leave them, leaving slugs as-is is safer for existing URLs, 
        // but the prompt said "Đổi toàn bộ tên danh mục", let's update slugs too.
        $slug = \Illuminate\Support\Str::slug($en);
        DB::table('categories')->where('name', $en)->update(['slug' => $slug]);
    }
    echo "Categories translated.\n";

    // 2. Translate Products
    $products = DB::table('products')->get();
    foreach ($products as $p) {
        $name = str_replace(array_keys($categoryTranslations), array_values($categoryTranslations), $p->name);
        $name = str_replace(['Màu', 'Nam', 'Nữ', 'Cao cấp'], ['Color', 'Men', 'Women', 'Premium'], $name);
        
        $desc = $p->description;
        if ($desc) {
            $desc = str_replace(array_keys($categoryTranslations), array_values($categoryTranslations), $desc);
            $desc = str_replace(['Thiết kế', 'Chất liệu', 'Phong cách', 'Trẻ trung', 'Năng động', 'Mô tả', 'Thoáng mát'], 
                                ['Design', 'Material', 'Style', 'Youthful', 'Dynamic', 'Description', 'Breathable'], $desc);
        }

        $slug = \Illuminate\Support\Str::slug($name);

        DB::table('products')->where('id', $p->id)->update([
            'name' => $name,
            'slug' => $slug,
            'description' => $desc
        ]);
    }
    echo "Products translated.\n";

    // 3. Translate Orders Statuses if any
    // Orders use enums: 'pending', 'processing', 'shipped', 'delivered', 'cancelled'. These are already in English!
    
    // 4. Update image directories in the DB
    // e.g. "images/products/ao-khoac/..." -> "images/products/jackets/..."
    foreach ($products as $p) {
        if ($p->images) {
            $images = json_decode($p->images, true);
            if (is_array($images)) {
                $newImages = [];
                foreach ($images as $img) {
                    // Simple replacement for directory paths
                    $img = str_replace(['ao-khoac', 'ao-thun', 'quan-jean'], ['jackets', 't-shirts', 'jeans'], $img);
                    $newImages[] = $img;
                }
                DB::table('products')->where('id', $p->id)->update(['images' => json_encode($newImages)]);
            }
        }
    }
    echo "Image paths translated.\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
