<?php
$content = file_get_contents('vibe_fashion.sql');
$replacements = [
    'Áo khoác' => 'Jackets',
    'Áo thun' => 'T-Shirts',
    'Áo len' => 'Sweaters',
    'Quần jean' => 'Jeans',
    'Quần ngắn' => 'Shorts',
    'Phụ kiện' => 'Accessories',
    'Quần' => 'Pants',
    'Áo' => 'Shirts',
    'Giày' => 'Shoes',
    'Túi xách' => 'Bags',
    'Màu' => 'Color',
    'Nam' => 'Men',
    'Nữ' => 'Women',
    'Cao cấp' => 'Premium',
    'Thiết kế' => 'Design',
    'Chất liệu' => 'Material',
    'Phong cách' => 'Style',
    'Trẻ trung' => 'Youthful',
    'Năng động' => 'Dynamic',
    'Mô tả' => 'Description',
    'Thoáng mát' => 'Breathable',
    'ao-khoac' => 'jackets',
    'ao-thun' => 't-shirts',
    'quan-jean' => 'jeans'
];
$content = str_replace(array_keys($replacements), array_values($replacements), $content);
file_put_contents('vibe_fashion.sql', $content);
echo "SQL translated.\n";
