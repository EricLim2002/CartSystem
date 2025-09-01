<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder {
    public function run() {
        // Disable foreign key checks so truncate won't fail
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Product::truncate(); 
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Product::insert([
            ['name'=>'Gaming Mouse', 'description'=>'Ergonomic gaming mouse', 'price'=>49.90, 'stock'=>50, 'image'=>null, 'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Mechanical Keyboard', 'description'=>'RGB mechanical keyboard', 'price'=>119.00, 'stock'=>30, 'image'=>null, 'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'USB-C Hub', 'description'=>'5-port high-speed hub', 'price'=>39.50, 'stock'=>100, 'image'=>null, 'created_at'=>now(),'updated_at'=>now()],
            // ... add more
        ]);
    }
}
