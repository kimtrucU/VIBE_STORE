<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Mở rộng bảng orders
        Schema::table('orders', function (Blueprint $table) {
            // Thay đổi enum status để thêm trạng thái mới
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','confirmed','processing','shipped','delivered','completed','cancelled','returned') DEFAULT 'pending'");

            // Thêm coupon
            $table->string('coupon_code', 50)->nullable()->after('notes');
            $table->decimal('discount', 12, 2)->default(0)->after('coupon_code');

            // Timestamps trạng thái
            $table->timestamp('confirmed_at')->nullable()->after('discount');
            $table->timestamp('processed_at')->nullable()->after('confirmed_at');
            $table->timestamp('shipped_at')->nullable()->after('processed_at');
            $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            $table->timestamp('completed_at')->nullable()->after('delivered_at');
            $table->timestamp('cancelled_at')->nullable()->after('completed_at');
            $table->timestamp('returned_at')->nullable()->after('cancelled_at');
            $table->text('cancel_reason')->nullable()->after('returned_at');
        });

        // Bảng brands
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->string('logo', 500)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Thêm brand_id vào products nếu chưa có
        if (!Schema::hasColumn('products', 'brand_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('brand_id')->nullable()->after('category_id')->constrained('brands')->nullOnDelete();
            });
        }

        // Bảng reviews
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->string('title', 200)->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });

        // Bảng coupons
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 200);
            $table->enum('type', ['percent', 'fixed'])->default('percent');
            $table->decimal('value', 12, 2);
            $table->decimal('min_order', 12, 2)->default(0);
            $table->decimal('max_discount', 12, 2)->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->date('starts_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Bảng activity_logs
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 100);
            $table->string('model_type', 100)->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        // Bảng settings
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('group', 50)->default('general');
            $table->timestamps();
        });

        // Thêm settings mặc định
        DB::table('settings')->insertOrIgnore([
            ['key' => 'site_name', 'value' => 'Vibe Fashion', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'site_email', 'value' => 'contact@vibe.com', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'site_phone', 'value' => '+84 901 234 567', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'shipping_fee', 'value' => '30000', 'group' => 'shipping', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'free_shipping_threshold', 'value' => '500000', 'group' => 'shipping', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'currency', 'value' => 'VND', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('reviews');

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Brand::class);
        });

        Schema::dropIfExists('brands');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'coupon_code', 'discount', 'confirmed_at', 'processed_at',
                'shipped_at', 'delivered_at', 'completed_at', 'cancelled_at',
                'returned_at', 'cancel_reason'
            ]);
        });
    }
};
