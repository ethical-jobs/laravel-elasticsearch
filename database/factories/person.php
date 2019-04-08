<?php

use Illuminate\Database\Eloquent\Factory;
use Tests\Fixtures\Models;

/** @var Factory $factory */
$factory->define(Models\Person::class, function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'age' => rand(3, 115),
        'sex' => collect(['male', 'female'])->random(),
        'deleted_at' => null,
    ];
});
