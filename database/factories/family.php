<?php

use Tests\Fixtures\Models;
use Illuminate\Support\Collection;

$factory->define(Models\Family::class, function (Faker\Generator $faker) {
    return [
        'surname' => $faker->name,
    ];
});
