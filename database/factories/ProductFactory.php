<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
	$unit         = App\Unit::pluck('id')->toArray();
	$category     = App\Category::pluck('id')->toArray();
	$production   = App\Production::pluck('id')->toArray();
	$buyingPrice  = $faker->numerify('######');
	$sellingPrice = ceil($buyingPrice * 30 / 100 / 100) * 100;
    return [
		'name'          => $faker->name,
		'stock'         => $faker->numerify('##'),
		'buying_price'  => $buyingPrice,
		'selling_price' => $sellingPrice,
		'unit_id'       => $faker->randomElement($unit),
		'category_id'   => $faker->randomElement($category),
		'production_id' => $faker->randomElement($production),
    ];
});
