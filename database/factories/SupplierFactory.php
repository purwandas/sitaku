<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Production;
use App\Supplier;
use Faker\Generator as Faker;

$factory->define(Supplier::class, function (Faker $faker) {
    $production = App\Production::pluck('id')->toArray();
    return [
		'name'          => $faker->name,
		'address'       => $faker->address,
		'phone'         => $faker->numerify('08#########'),
		'production_id' => $faker->randomElement($production),
    ];
});
