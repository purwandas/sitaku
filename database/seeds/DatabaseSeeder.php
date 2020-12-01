<?php

use App\Category;
use App\Product;
use App\Production;
use App\Supplier;
use App\TrendMoment;
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
        // $this->call(UserSeeder::class);
        
		// $category   = factory(Category::class, 5)->create();
		// $production = factory(Production::class, 5)->create();
		// $unit       = factory(Unit::class, 5)->create();
		// $supplier   = factory(Supplier::class, 5)->create();
		// $product    = factory(Product::class, 5)->create();

        $sales = [ 2317, 1887, 1888, 2059, 2231, 1888, 1373, 1870, 2746, 2231, 2402, 2574 ];
        for ($i=1; $i < 13; $i++) { 
            TrendMoment::updateOrCreate([
                'month_'      => $i,
                'year_'       => 2020,
                'total_sales' => $sales[$i - 1]
            ]);
        }

    }
}
