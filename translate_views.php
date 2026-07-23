<?php
$dir = new RecursiveDirectoryIterator('C:\laragon\www\vibe\resources\views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/.*\.blade\.php$/', RegexIterator::GET_MATCH);

$replacements = [
    'Hồ sơ cá nhân' => 'Profile',
    'Đơn hàng của tôi' => 'My Orders',
    'Đổi mật khẩu' => 'Change Password',
    'Đăng xuất' => 'Logout',
    'Đăng nhập' => 'Login',
    'Đăng ký' => 'Register',
    'Quản trị' => 'Admin Panel',
    'Về chúng tôi' => 'About',
    'Liên hệ' => 'Contact',
    'Trang chủ' => 'Home',
    'Sản phẩm' => 'Shop',
    'Giỏ hàng' => 'Cart',
    'Thanh toán' => 'Checkout',
    'Thêm vào giỏ' => 'Add to Cart',
    'Tổng cộng' => 'Total',
    'Tạm tính' => 'Subtotal',
    'Đơn hàng' => 'Orders',
    'Khách hàng' => 'Customers',
    'Doanh thu' => 'Revenue',
    'Đang xử lý' => 'Processing',
    'Chờ xử lý' => 'Pending',
    'Đã giao' => 'Delivered',
    'Đã hủy' => 'Cancelled',
    'Lỗi' => 'Error',
    'Thành công' => 'Success',
    'Quên mật khẩu' => 'Forgot Password',
    'Thêm mới' => 'Add New',
    'Xóa' => 'Delete',
    'Cập nhật' => 'Update',
    'Chỉnh sửa' => 'Edit',
    'Tài khoản' => 'Account',
    'Mật khẩu' => 'Password',
    'Email' => 'Email',
    'Nhập' => 'Enter',
    'Tìm kiếm' => 'Search',
    'Tất cả' => 'All'
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
echo "All Blade files translated.\n";
