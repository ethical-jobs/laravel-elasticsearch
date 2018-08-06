<?php

use Illuminate\Database\Seeder;
use Tests\Fixtures\Models;

class TestFixturesSeeder extends Seeder
{
    /**
     * Run the fixtures seeds.
     *
     * @return void
     */
    public function run()
    {
        $families = factory(Models\Family::class, 50)->create();

        foreach ($families as $family) {

            factory(Models\Person::class, rand(2, 5))->create([
                'family_id' => $family->id,
            ]);
            
            factory(Models\Vehicle::class, rand(0, 3))->create([
                'family_id' => $family->id,
            ]);
        }
    }
}
