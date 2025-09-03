<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks so truncate won't fail
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Product::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Product::insert([
            ['name' => 'Gaming Mouse', 'description' => 'Ergonomic gaming mouse', 'price' => 49.90, 'stock' => 50, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mechanical Keyboard', 'description' => 'RGB mechanical keyboard', 'price' => 119.00, 'stock' => 30, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'USB-C Hub', 'description' => '5-port high-speed hub', 'price' => 39.50, 'stock' => 100, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Wireless Headset', 'description' => 'Noise-cancelling over-ear headset', 'price' => 199.00, 'stock' => 25, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gaming Chair', 'description' => 'Adjustable ergonomic gaming chair', 'price' => 299.00, 'stock' => 15, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '4K Monitor', 'description' => '27-inch UHD gaming monitor', 'price' => 499.00, 'stock' => 10, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'External SSD 1TB', 'description' => 'High-speed portable SSD', 'price' => 149.00, 'stock' => 40, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Smartwatch', 'description' => 'Fitness tracker smartwatch', 'price' => 89.90, 'stock' => 60, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bluetooth Speaker', 'description' => 'Portable waterproof speaker', 'price' => 59.00, 'stock' => 75, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Webcam HD', 'description' => '1080p streaming webcam', 'price' => 79.00, 'stock' => 50, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Power Bank 20000mAh', 'description' => 'Fast charging portable power bank', 'price' => 45.00, 'stock' => 120, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'VR Headset', 'description' => 'Immersive virtual reality headset', 'price' => 399.00, 'stock' => 8, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Drone Camera', 'description' => 'Compact drone with 4K camera', 'price' => 599.00, 'stock' => 5, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Wireless Earbuds', 'description' => 'Noise cancelling true wireless earbuds', 'price' => 129.00, 'stock' => 55, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Smartphone Stand', 'description' => 'Adjustable phone holder', 'price' => 19.90, 'stock' => 150, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Laptop Cooling Pad', 'description' => 'RGB cooling pad with fans', 'price' => 35.00, 'stock' => 80, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'HDMI Cable 2m', 'description' => 'High-speed HDMI 2.1 cable', 'price' => 12.90, 'stock' => 200, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mechanical Pencil', 'description' => 'Premium drafting pencil', 'price' => 9.90, 'stock' => 300, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Graphic Tablet', 'description' => 'Drawing tablet with pen', 'price' => 179.00, 'stock' => 20, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Smart Light Bulb', 'description' => 'WiFi-enabled RGB bulb', 'price' => 25.00, 'stock' => 100, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Portable Projector', 'description' => 'Mini projector for home use', 'price' => 249.00, 'stock' => 12, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'USB Flash Drive 64GB', 'description' => 'High-speed USB 3.0 flash drive', 'price' => 15.90, 'stock' => 250, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gaming Controller', 'description' => 'Wireless controller for PC/console', 'price' => 69.00, 'stock' => 35, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Desk Lamp LED', 'description' => 'Adjustable LED desk lamp', 'price' => 29.90, 'stock' => 90, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Wireless Router', 'description' => 'Dual-band WiFi 6 router', 'price' => 159.00, 'stock' => 18, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Laptop Backpack', 'description' => 'Waterproof 15-inch laptop backpack', 'price' => 49.00, 'stock' => 70, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tripod Stand', 'description' => 'Lightweight camera tripod', 'price' => 39.90, 'stock' => 45, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Action Camera', 'description' => '4K waterproof action cam', 'price' => 199.00, 'stock' => 15, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Wireless Charger', 'description' => 'Fast charging Qi wireless pad', 'price' => 29.00, 'stock' => 85, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Smart Thermometer', 'description' => 'Bluetooth digital thermometer', 'price' => 22.00, 'stock' => 110, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

    }
}
