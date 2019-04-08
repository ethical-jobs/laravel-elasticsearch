<?php

use Illuminate\Database\Eloquent\Factory;
use Tests\Fixtures\Models;

/** @var Factory $factory */
$factory->define(Models\Family::class, function (Faker\Generator $faker) {
    return [
        'surname' => $faker->name,
    ];
});
