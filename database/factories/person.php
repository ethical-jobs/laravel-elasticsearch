<?php

use Tests\Fixtures\Models;
use Illuminate\Support\Collection;

$factory->define(Models\Person::class, function (Faker\Generator $faker) {
    return [
        'first_name' 	=> $faker->firstName,
        'last_name' 	=> $faker->lastName,
        'email'   		=> $faker->email,
        'sex'           => (new Collection(['male','female']))->random(),
        'deleted_at'    => null,
    ];
});
