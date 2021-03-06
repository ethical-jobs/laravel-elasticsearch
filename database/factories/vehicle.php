<?php

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Collection;
use Tests\Fixtures\Models;

/** @var Factory $factory */
$factory->define(Models\Vehicle::class, function (Faker\Generator $faker) {

    $cars = new Collection([
        'tesla' => ['roadster', 'model-6', 'model-3'],
        'ford' => ['fiesta', 'falcon', 'discovery'],
        'toyota' => ['camry', 'prius', 'lexus'],
    ]);

    $make = $cars->keys()->random();

    $model = (new Collection($cars->get($make)))->random();

    return [
        'year' => rand(1995, 2018),
        'make' => $make,
        'model' => $model,
    ];
});
