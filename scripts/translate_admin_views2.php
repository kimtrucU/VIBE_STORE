<?php
$dir = new RecursiveDirectoryIterator('C:\laragon\www\vibe\resources\views\admin');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/.*\.blade\.php$/', RegexIterator::GET_MATCH);

$replacements = [
    'Edit Danh mục' => 'Edit Category',
    'Add Category Mới' => 'Add New Category',
    'Quản lý User' => 'Manage Users',
    'Danh sách User' => 'User List',
    'Search tên, email...' => 'Search name, email...',
    'All quyền' => 'All Roles',
    'Admin Panel viên' => 'Admin',
    'Lọc' => 'Filter',
    'Tên' => 'Name',
    'Ngày đăng ký' => 'Registration Date',
    'Hoạt động' => 'Active',
    'Đã khóa' => 'Locked',
    'Phân quyền' => 'Role',
    'Admin Panel viên (Admin)' => 'Admin',
    'Status tài khoản' => 'Account Status',
    'Khóa tài khoản' => 'Lock Account',
    'No tìm thấy người dùng nào.' => 'No users found.',
    'Danh mục' => 'Categories',
    'Cài đặt' => 'Settings',
    'Hồ sơ' => 'Profile',
    'Quản lý Orders' => 'Manage Orders',
    'Danh sách Orders' => 'Order List',
    'Thêm Shop' => 'Add Product',
    'Thêm Shop Mới' => 'Add New Product',
    'Thông tin cơ bản' => 'Basic Info',
    'Mô tả ngắn' => 'Short Description',
    'Mô tả chi tiết' => 'Detailed Description',
    'Ảnh đại diện' => 'Featured Image',
    'Tùy chọn khác' => 'Other Options',
    'Tùy chọn' => 'Options',
    'Kích hoạt (Hiển thị)' => 'Active (Visible)',
    'Shop mới' => 'New Product',
    'Lưu Shop' => 'Save Product',
    'Quản lý Shop' => 'Manage Products',
    'Danh sách Shop' => 'Product List',
    'Shop' => 'Product'
];

foreach ($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    $newContent = str_replace(array_keys($replacements), array_values($replacements), $content);
    if ($content !== $newContent) {
        file_put_contents($path, $newContent);
        echo "Translated: " . basename($path) . "\n";
    }
}
echo "All Admin Blade files translated part 2.\n";
