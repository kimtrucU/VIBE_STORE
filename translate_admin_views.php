<?php
$dir = new RecursiveDirectoryIterator('C:\laragon\www\vibe\resources\views\admin');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/.*\.blade\.php$/', RegexIterator::GET_MATCH);

$replacements = [
    'Tổng quan Dashboard' => 'Dashboard Overview',
    'Quản lý Danh mục' => 'Manage Categories',
    'Danh sách Danh mục' => 'Category List',
    'Search danh mục...' => 'Search categories...',
    'Thêm Danh mục' => 'Add Category',
    'Tên Danh mục' => 'Category Name',
    'Ngày tạo' => 'Created At',
    'Hành động' => 'Actions',
    'Sửa' => 'Edit',
    'Xóa' => 'Delete',
    'Bạn có chắc chắn muốn xóa danh mục này?' => 'Are you sure you want to delete this category?',
    'Sửa Danh mục' => 'Edit Category',
    'Đóng' => 'Close',
    'Lưu thay đổi' => 'Save Changes',
    'Chưa có danh mục nào.' => 'No categories found.',
    'Thêm Danh mục Mới' => 'Add New Category',
    'Tên danh mục' => 'Category Name',
    'Nhập tên danh mục' => 'Enter category name',
    'Hành Động' => 'Actions',
    'Quản lý Đơn hàng' => 'Manage Orders',
    'Danh sách Đơn hàng' => 'Order List',
    'Search mã đơn, tên...' => 'Search order code, name...',
    'Lọc trạng thái' => 'Filter Status',
    'Mã ĐH' => 'Order Code',
    'Tổng tiền' => 'Total Amount',
    'Trạng thái' => 'Status',
    'Ngày đặt' => 'Order Date',
    'Chi tiết' => 'Details',
    'Chưa có đơn hàng nào.' => 'No orders found.',
    'Chi tiết Đơn hàng' => 'Order Details',
    'Trở lại' => 'Back',
    'Thông tin Khách hàng' => 'Customer Information',
    'Họ tên' => 'Full Name',
    'Số điện thoại' => 'Phone Number',
    'Địa chỉ giao hàng' => 'Shipping Address',
    'Ghi chú' => 'Notes',
    'Không có' => 'None',
    'Trạng thái Đơn hàng' => 'Order Status',
    'Cập nhật trạng thái' => 'Update Status',
    'Danh sách Sản phẩm' => 'Product List',
    'Sản phẩm' => 'Product',
    'Đơn giá' => 'Unit Price',
    'Số lượng' => 'Quantity',
    'Thành tiền' => 'Amount',
    'Tạm tính' => 'Subtotal',
    'Phí vận chuyển' => 'Shipping Fee',
    'Tổng cộng' => 'Total',
    'Thêm Sản phẩm' => 'Add Product',
    'Danh sách Sản phẩm' => 'Product List',
    'Tên Sản phẩm' => 'Product Name',
    'Giá bán' => 'Price',
    'Giá gốc' => 'Original Price',
    'Tồn kho' => 'Stock',
    'Hình ảnh' => 'Images',
    'Quản lý Khách hàng' => 'Manage Customers',
    'Danh sách Khách hàng' => 'Customer List',
    'Vai trò' => 'Role',
    'Ngày tham gia' => 'Joined Date',
    'Người dùng' => 'User',
    'Quản trị viên' => 'Admin',
    'Chưa có khách hàng nào.' => 'No customers found.',
    'Hình ảnh chính' => 'Primary Image',
    'Tên sản phẩm' => 'Product Name',
    'Chọn danh mục' => 'Select category',
    'Giá' => 'Price',
    'Chi tiết sản phẩm' => 'Product Details',
    'Nổi bật' => 'Featured',
    'Bán chạy' => 'Best Seller',
    'Hàng mới' => 'New Arrival',
    'Lưu sản phẩm' => 'Save Product',
    'Hủy' => 'Cancel',
    'Chọn ảnh' => 'Select image',
    'Nhập URL hình ảnh' => 'Enter image URL',
    'Tất cả' => 'All',
    'Đang xử lý' => 'Processing',
    'Chờ xử lý' => 'Pending',
    'Đã giao' => 'Delivered',
    'Đã hủy' => 'Cancelled',
    'Không' => 'No',
    'Có' => 'Yes'
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
echo "All Admin Blade files translated.\n";
