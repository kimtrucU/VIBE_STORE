<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EnglishDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('products')->truncate();
        DB::table('categories')->truncate();
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // =====================
        // 1. USERS
        // =====================
        DB::table('users')->insertGetId([
            'name'       => 'Administrator',
            'email'      => 'admin@vibe.com',
            'password'   => bcrypt('123456'),
            'role_id'    => 1,
            'phone'      => '0901234567',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insertGetId([
            'name'       => 'Trần Minh Vỹ',
            'email'      => 'customer@vibe.com',
            'password'   => bcrypt('123456'),
            'role_id'    => 2,
            'phone'      => '0912345678',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // =====================
        // 2. CATEGORIES
        // =====================
        $categories = [
            ['name' => 'Áo Thun',   'slug' => 'ao-thun',   'description' => 'Áo thun cao cấp cotton 250gsm'],
            ['name' => 'Áo Hoodie', 'slug' => 'ao-hoodie', 'description' => 'Hoodie nỉ dày premium 380gsm'],
            ['name' => 'Áo Khoác', 'slug' => 'ao-khoac',  'description' => 'Áo khoác bomber, coach, windbreaker'],
            ['name' => 'Quần',      'slug' => 'quan',       'description' => 'Quần short, quần knit, quần jogger'],
            ['name' => 'Phụ Kiện',  'slug' => 'phu-kien',  'description' => 'Phụ kiện thời trang streetwear'],
            ['name' => 'Loungewear','slug' => 'loungewear', 'description' => 'Bộ đồ mặc nhà thoải mái'],
        ];

        $catIds = [];
        foreach ($categories as $cat) {
            $catIds[$cat['slug']] = DB::table('categories')->insertGetId([
                'name'        => $cat['name'],
                'slug'        => $cat['slug'],
                'description' => $cat['description'],
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // =====================
        // 3. PRODUCTS
        // =====================
        $products = [

            // --- AO THUN (long-sleeve folder) ---
            [
                'category_slug' => 'ao-thun',
                'name'          => 'Áo Thun Dài Tay Whenever Signature',
                'price'         => 450000,
                'original_price'=> 540000,
                'description'   => 'Áo thun dài tay cotton 250gsm với logo Whenever dập nổi chính hãng. Form oversize chuẩn, cổ tròn, màu trung tính dễ phối đồ.',
                'images'        => ['images/products/long-sleeve/1.webp','images/products/long-sleeve/1.1.webp'],
                'sizes'         => ['S','M','L','XL','XXL'],
                'details'       => ['Chất liệu' => 'Cotton 250gsm', 'Form dáng' => 'Oversize', 'Xuất xứ' => 'Việt Nam'],
                'is_new_arrival'=> true,
                'is_best_seller'=> true,
            ],
            [
                'category_slug' => 'ao-thun',
                'name'          => 'Áo Thun Basic Heavyweight Cotton',
                'price'         => 350000,
                'original_price'=> 420000,
                'description'   => 'Áo thun basic cotton nặng 220gsm, thiết kế tối giản dễ mặc hàng ngày. Bo tay và cổ áo chắc chắn, không nhăn sau giặt.',
                'images'        => ['images/products/long-sleeve/2.webp','images/products/long-sleeve/2.1.webp'],
                'sizes'         => ['S','M','L','XL'],
                'details'       => ['Chất liệu' => 'Cotton 220gsm', 'Form dáng' => 'Regular Fit', 'Xuất xứ' => 'Việt Nam'],
                'is_new_arrival'=> false,
                'is_best_seller'=> true,
            ],
            [
                'category_slug' => 'ao-thun',
                'name'          => 'Áo Thun Vintage Wash Washed Tee',
                'price'         => 420000,
                'original_price'=> 0,
                'description'   => 'Áo thun được wash enzyme tạo màu vintage độc đáo, mỗi chiếc có màu sắc riêng biệt. Cotton ring-spun mềm mịn cao cấp.',
                'images'        => ['images/products/long-sleeve/3.webp','images/products/long-sleeve/3.1.jpg'],
                'sizes'         => ['S','M','L','XL'],
                'details'       => ['Chất liệu' => 'Cotton Ring-Spun', 'Xử lý' => 'Enzyme Wash', 'Xuất xứ' => 'Việt Nam'],
                'is_new_arrival'=> true,
                'is_best_seller'=> false,
            ],
            [
                'category_slug' => 'ao-thun',
                'name'          => 'Áo Thun Heavyweight 280gsm Premium',
                'price'         => 480000,
                'original_price'=> 580000,
                'description'   => 'Áo thun cotton 280gsm premium, nặng và dày hơn so với áo thun thông thường. Phù hợp mặc độc hoặc layer trong mùa đông.',
                'images'        => ['images/products/long-sleeve/4.webp','images/products/long-sleeve/4.1.jpg'],
                'sizes'         => ['S','M','L','XL','XXL'],
                'details'       => ['Chất liệu' => 'Cotton 280gsm', 'Form dáng' => 'Boxy Fit', 'Xuất xứ' => 'Việt Nam'],
                'is_new_arrival'=> false,
                'is_best_seller'=> true,
            ],
            [
                'category_slug' => 'ao-thun',
                'name'          => 'Áo Thun Minimal Logo Embossed',
                'price'         => 390000,
                'original_price'=> 0,
                'description'   => 'Áo thun tối giản với logo dập nổi nhỏ góc ngực. Cotton 230gsm, form regular fit cổ điển không bao giờ lỗi mốt.',
                'images'        => ['images/products/long-sleeve/5.webp','images/products/long-sleeve/5.1.jpg'],
                'sizes'         => ['S','M','L','XL'],
                'details'       => ['Chất liệu' => 'Cotton 230gsm', 'In ấn' => 'Logo dập nổi', 'Xuất xứ' => 'Việt Nam'],
                'is_new_arrival'=> false,
                'is_best_seller'=> false,
            ],

            // --- AO HOODIE (zip-hoodie folder) ---
            [
                'category_slug' => 'ao-hoodie',
                'name'          => 'Áo Zip Hoodie Whenever Essentials',
                'price'         => 650000,
                'original_price'=> 780000,
                'description'   => 'Áo zip hoodie nỉ dày 380gsm collab Whenever chính hãng. Khóa kéo YKK cao cấp, túi kangaroo oversized, bo tay và bo gấu co giãn tốt.',
                'images'        => ['images/products/zip-hoodie/21.webp','images/products/zip-hoodie/21.1.webp'],
                'sizes'         => ['S','M','L','XL','XXL'],
                'details'       => ['Chất liệu' => 'Nỉ bông 380gsm', 'Khóa kéo' => 'YKK Nhật', 'Xuất xứ' => 'Việt Nam'],
                'is_new_arrival'=> true,
                'is_best_seller'=> true,
            ],
            [
                'category_slug' => 'ao-hoodie',
                'name'          => 'Áo Hoodie Phormfit Oversized',
                'price'         => 750000,
                'original_price'=> 900000,
                'description'   => 'Áo hoodie phorm fit rộng oversized, chất nỉ bông dày 400gsm cực ấm. Mũ 2 lớp chống gió, dây rút kim loại nguyên khối.',
                'images'        => ['images/products/zip-hoodie/22.webp','images/products/zip-hoodie/22.1.webp'],
                'sizes'         => ['S','M','L','XL'],
                'details'       => ['Chất liệu' => 'Nỉ bông 400gsm', 'Mũ' => '2 lớp chống gió', 'Xuất xứ' => 'Việt Nam'],
                'is_new_arrival'=> false,
                'is_best_seller'=> true,
            ],
            [
                'category_slug' => 'ao-hoodie',
                'name'          => 'Áo Zip Hoodie Vintage Garment Wash',
                'price'         => 820000,
                'original_price'=> 0,
                'description'   => 'Áo zip hoodie được garment wash vintage, màu sắc độc đáo cũ kỹ cổ điển. Chất nỉ pháp 360gsm mềm mịn bền màu.',
                'images'        => ['images/products/zip-hoodie/23.webp','images/products/zip-hoodie/23.1.webp'],
                'sizes'         => ['S','M','L','XL'],
                'details'       => ['Chất liệu' => 'Nỉ Pháp 360gsm', 'Xử lý' => 'Garment Wash', 'Xuất xứ' => 'Việt Nam'],
                'is_new_arrival'=> true,
                'is_best_seller'=> false,
            ],
            [
                'category_slug' => 'ao-hoodie',
                'name'          => 'Áo Hoodie Cotton Nỉ Dày Signature',
                'price'         => 680000,
                'original_price'=> 820000,
                'description'   => 'Áo hoodie chất cotton nỉ đặc biệt 350gsm, mặt trong brushed fleece cực mềm ấm. Logo thêu tay tỉ mỉ trên ngực trái.',
                'images'        => ['images/products/zip-hoodie/24.webp','images/products/zip-hoodie/24.1.webp'],
                'sizes'         => ['S','M','L','XL','XXL'],
                'details'       => ['Chất liệu' => 'Cotton Fleece 350gsm', 'Logo' => 'Thêu tay', 'Xuất xứ' => 'Việt Nam'],
                'is_new_arrival'=> false,
                'is_best_seller'=> true,
            ],

            // --- AO KHOAC (bomber folder) ---
            [
                'category_slug' => 'ao-khoac',
                'name'          => 'Áo Khoác Bomber Whenever Collab',
                'price'         => 1200000,
                'original_price'=> 1440000,
                'description'   => 'Áo khoác bomber chính hãng Whenever collab, vải nylon dù cao cấp chống thấm nước. Lớp lót quilted ấm, cổ rib dày, tay áo tag Whenever thật.',
                'images'        => ['images/products/bomber/c1.webp','images/products/bomber/c1.1.webp'],
                'sizes'         => ['S','M','L','XL'],
                'details'       => ['Chất liệu ngoài' => 'Nylon Dù', 'Lớp lót' => 'Quilted ấm', 'Xuất xứ' => 'Việt Nam'],
                'is_new_arrival'=> true,
                'is_best_seller'=> true,
            ],
            [
                'category_slug' => 'ao-khoac',
                'name'          => 'Áo Khoác Bomber Nylon Utility',
                'price'         => 1350000,
                'original_price'=> 0,
                'description'   => 'Áo khoác bomber nylon utility với nhiều túi chức năng. Kiểu dáng military chic, khóa kéo 2 chiều, vải chống gió hiệu quả.',
                'images'        => ['images/products/bomber/c2.webp','images/products/bomber/c2.1.webp'],
                'sizes'         => ['S','M','L','XL'],
                'details'       => ['Chất liệu' => 'Nylon Ripstop', 'Kiểu dáng' => 'Military Utility', 'Xuất xứ' => 'Việt Nam'],
                'is_new_arrival'=> true,
                'is_best_seller'=> false,
            ],
            [
                'category_slug' => 'ao-khoac',
                'name'          => 'Áo Khoác Oversized Bomber Denim',
                'price'         => 1500000,
                'original_price'=> 1800000,
                'description'   => 'Áo khoác bomber oversized chất liệu denim cao cấp 12oz. Đường may chắc chắn, form rộng thoải mái, có thể mặc đơn hoặc layer.',
                'images'        => ['images/products/bomber/c3.webp','images/products/bomber/c3.1.webp'],
                'sizes'         => ['S','M','L','XL','XXL'],
                'details'       => ['Chất liệu' => 'Denim 12oz', 'Form dáng' => 'Oversized', 'Xuất xứ' => 'Việt Nam'],
                'is_new_arrival'=> false,
                'is_best_seller'=> true,
            ],
            [
                'category_slug' => 'ao-khoac',
                'name'          => 'Áo Khoác Coach Jacket Phối Màu',
                'price'         => 950000,
                'original_price'=> 1140000,
                'description'   => 'Áo khoác coach jacket kiểu dáng sporty phối màu độc đáo. Chất liệu nylon nhẹ chống gió nhẹ, phù hợp cho tiết trời se lạnh.',
                'images'        => ['images/products/bomber/c4.webp','images/products/bomber/c4.1.webp'],
                'sizes'         => ['S','M','L','XL'],
                'details'       => ['Chất liệu' => 'Nylon nhẹ', 'Kiểu dáng' => 'Coach Jacket', 'Xuất xứ' => 'Việt Nam'],
                'is_new_arrival'=> false,
                'is_best_seller'=> false,
            ],

            // --- QUAN (knit-shorts folder) ---
            [
                'category_slug' => 'quan',
                'name'          => 'Quần Short Knit Streetwear Premium',
                'price'         => 450000,
                'original_price'=> 540000,
                'description'   => 'Quần short knit chất liệu len pha dệt kim cao cấp, co giãn 4 chiều thoải mái. Cạp thun rút dây, túi bên tiện lợi, hem straight chuẩn.',
                'images'        => ['images/products/knit-shorts/a1.webp','images/products/knit-shorts/a1.1.webp'],
                'sizes'         => ['S','M','L','XL'],
                'details'       => ['Chất liệu' => 'Len pha dệt kim', 'Co giãn' => '4 chiều', 'Xuất xứ' => 'Việt Nam'],
                'is_new_arrival'=> true,
                'is_best_seller'=> true,
            ],
            [
                'category_slug' => 'quan',
                'name'          => 'Quần Short Daily Comfort Basic',
                'price'         => 350000,
                'original_price'=> 0,
                'description'   => 'Quần short basic mặc hàng ngày, chất liệu cotton French Terry thoáng mát. Cạp thun co giãn, phù hợp tập gym hoặc mặc ở nhà.',
                'images'        => ['images/products/knit-shorts/a2.webp','images/products/knit-shorts/a2.1.jpg'],
                'sizes'         => ['S','M','L','XL','XXL'],
                'details'       => ['Chất liệu' => 'French Terry', 'Cạp' => 'Thun co giãn', 'Xuất xứ' => 'Việt Nam'],
                'is_new_arrival'=> false,
                'is_best_seller'=> true,
            ],
            [
                'category_slug' => 'quan',
                'name'          => 'Quần Short Cargo Utility 6 Túi',
                'price'         => 520000,
                'original_price'=> 620000,
                'description'   => 'Quần short cargo với 6 túi thực dụng, chất liệu ripstop bền chắc. Style military urban, phù hợp phối cùng áo thun hoặc tank top.',
                'images'        => ['images/products/knit-shorts/a3.webp','images/products/knit-shorts/a3.2.webp'],
                'sizes'         => ['S','M','L','XL'],
                'details'       => ['Chất liệu' => 'Ripstop Cotton', 'Túi' => '6 túi cargo', 'Xuất xứ' => 'Việt Nam'],
                'is_new_arrival'=> true,
                'is_best_seller'=> false,
            ],
            [
                'category_slug' => 'quan',
                'name'          => 'Quần Lửng Knit Cotton Wide-Leg',
                'price'         => 480000,
                'original_price'=> 580000,
                'description'   => 'Quần lửng wide-leg kiểu dáng thời thượng, chất knit cotton mềm mại. Form ống rộng thoải mái, phù hợp nhiều vóc dáng.',
                'images'        => ['images/products/knit-shorts/a4.webp','images/products/knit-shorts/a4.1.webp'],
                'sizes'         => ['S','M','L','XL'],
                'details'       => ['Chất liệu' => 'Knit Cotton', 'Form dáng' => 'Wide-Leg', 'Xuất xứ' => 'Việt Nam'],
                'is_new_arrival'=> false,
                'is_best_seller'=> false,
            ],

            // --- PHU KIEN (accessories folder) ---
            [
                'category_slug' => 'phu-kien',
                'name'          => 'Túi Canvas Whenever Auth Tote',
                'price'         => 320000,
                'original_price'=> 380000,
                'description'   => 'Túi tote canvas dày chống nước nhẹ, in logo Whenever Auth chính hãng. Quai đeo chắc chắn, dung tích lớn đựng được laptop 13 inch.',
                'images'        => ['images/products/accessories/b1.webp','images/products/accessories/b1.1.webp'],
                'sizes'         => ['Free Size'],
                'details'       => ['Chất liệu' => 'Canvas 12oz', 'Dung tích' => 'Fit laptop 13"', 'Xuất xứ' => 'Việt Nam'],
                'is_new_arrival'=> true,
                'is_best_seller'=> true,
            ],
            [
                'category_slug' => 'phu-kien',
                'name'          => 'Mũ Cap Logo Whenever Thêu Nổi',
                'price'         => 250000,
                'original_price'=> 300000,
                'description'   => 'Mũ cap 5 panel với logo Whenever thêu nổi 3D chính hãng. Chất liệu cotton twill cao cấp, khóa sau snapback điều chỉnh vừa đầu.',
                'images'        => ['images/products/accessories/b2.webp','images/products/accessories/b2.1.webp'],
                'sizes'         => ['Free Size'],
                'details'       => ['Chất liệu' => 'Cotton Twill', 'Logo' => 'Thêu 3D', 'Khóa' => 'Snapback'],
                'is_new_arrival'=> false,
                'is_best_seller'=> true,
            ],
            [
                'category_slug' => 'phu-kien',
                'name'          => 'Ví Da Thật Minimalist Bifold',
                'price'         => 450000,
                'original_price'=> 0,
                'description'   => 'Ví da bò thật minimalist bifold, chia ngăn tiện lợi. Độ bền cao, dập logo VIBE cẩn thận, theo thời gian lên nước đẹp tự nhiên.',
                'images'        => ['images/products/accessories/b3.webp','images/products/accessories/b3.1.webp'],
                'sizes'         => ['Free Size'],
                'details'       => ['Chất liệu' => 'Da bò thật', 'Kiểu dáng' => 'Bifold', 'Ngăn thẻ' => '4 ngăn'],
                'is_new_arrival'=> true,
                'is_best_seller'=> false,
            ],
            [
                'category_slug' => 'phu-kien',
                'name'          => 'Vớ Cổ Ngắn Whenever Pack 3 Đôi',
                'price'         => 150000,
                'original_price'=> 180000,
                'description'   => 'Set 3 đôi vớ cổ ngắn cotton cao cấp, logo Whenever thêu nhỏ phía sau. Vải cotton mềm thoáng khí, đế tăng cường không trơn trượt.',
                'images'        => ['images/products/accessories/b4.webp','images/products/accessories/b4.1.webp'],
                'sizes'         => ['Free Size'],
                'details'       => ['Chất liệu' => 'Cotton 80%', 'Số lượng' => '3 đôi/set', 'Size' => 'Unisex'],
                'is_new_arrival'=> false,
                'is_best_seller'=> true,
            ],

            // --- LOUNGEWEAR ---
            [
                'category_slug' => 'loungewear',
                'name'          => 'Bộ Loungewear Nỉ Bông Premium Set',
                'price'         => 850000,
                'original_price'=> 1020000,
                'description'   => 'Bộ đồ mặc nhà loungewear gồm áo hoodie + quần jogger chất nỉ bông 350gsm. Co giãn tốt, ấm áp thoải mái, phù hợp mọi hoạt động trong nhà.',
                'images'        => ['images/products/loungewear/6.webp','images/products/loungewear/6.1.jpg'],
                'sizes'         => ['S','M','L','XL'],
                'details'       => ['Bộ gồm' => 'Hoodie + Jogger', 'Chất liệu' => 'Nỉ bông 350gsm', 'Xuất xứ' => 'Việt Nam'],
                'is_new_arrival'=> true,
                'is_best_seller'=> false,
            ],
            [
                'category_slug' => 'loungewear',
                'name'          => 'Áo Crewneck Loungewear Ribbed',
                'price'         => 480000,
                'original_price'=> 580000,
                'description'   => 'Áo crewneck chất ribbed cotton mềm mịn thoải mái, phù hợp ở nhà hoặc ra ngoài cà phê. Cổ gân ôm sát, thân áo vừa vặn không quá rộng.',
                'images'        => ['images/products/loungewear/7.webp','images/products/loungewear/7.1.webp'],
                'sizes'         => ['S','M','L','XL'],
                'details'       => ['Chất liệu' => 'Ribbed Cotton', 'Kiểu cổ' => 'Crewneck', 'Xuất xứ' => 'Việt Nam'],
                'is_new_arrival'=> false,
                'is_best_seller'=> true,
            ],
        ];

        foreach ($products as $p) {
            $op = $p['original_price'] > 0 ? $p['original_price'] : null;
            DB::table('products')->insert([
                'category_id'   => $catIds[$p['category_slug']],
                'name'          => $p['name'],
                'slug'          => Str::slug($p['name']),
                'price'         => $p['price'],
                'original_price'=> $op,
                'description'   => $p['description'],
                'images'        => json_encode($p['images']),
                'sizes'         => json_encode($p['sizes']),
                'details'       => json_encode($p['details']),
                'is_active'     => true,
                'is_new_arrival'=> $p['is_new_arrival'],
                'is_best_seller'=> $p['is_best_seller'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}
