<?php

use App\Category;
use App\Product;
use App\Production;
use App\Supplier;
use App\Unit;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        
		$category   = factory(Category::class, 5)->create();
		$production = factory(Production::class, 5)->create();
		$unit       = factory(Unit::class, 5)->create();
		$supplier   = factory(Supplier::class, 5)->create();
		$product    = factory(Product::class, 5)->create();

    }
}
