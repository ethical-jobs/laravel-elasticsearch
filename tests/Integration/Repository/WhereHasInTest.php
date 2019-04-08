<?php

namespace Tests\Integration\Repositories;

use Tests\Fixtures\Models;
use Tests\Fixtures\Repositories\FamilyRepository;
use Tests\Helpers\Indexer;
use Tests\TestCase;

class WhereHasInTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_query_a_relation_with_whereIn_term_filter()
    {
        $obamas = factory(Models\Family::class)->create(['surname' => 'Obama']);
        $trumps = factory(Models\Family::class)->create(['surname' => 'Trump']);
        $clintons = factory(Models\Family::class)->create(['surname' => 'Clinton']);

        factory(Models\Person::class, 2)->create([
            'sex' => 'male',
            'family_id' => $obamas->id,
        ]);

        factory(Models\Person::class)->create([
            'sex' => 'female',
            'family_id' => $obamas->id,
        ]);

        factory(Models\Person::class)->create([
            'sex' => 'trans',
            'family_id' => $trumps->id,
        ]);

        Indexer::all(Models\Family::class);

        $families = resolve(FamilyRepository::class)
            ->whereHasIn('members.sex', ['male', 'trans'])
            ->find();

        $this->assertEquals(2, $families->count());
        $this->assertEquals($families->pluck('surname')->toArray(), ['Trump', 'Obama']);

        foreach ($families as $family) {

            $shouldHaveAtLeastOne = false;

            foreach ($family->members->pluck('sex') as $gender) {
                if (in_array($gender, ['male', 'trans'])) {
                    $shouldHaveAtLeastOne = true;
                }
            }

            $this->assertTrue($shouldHaveAtLeastOne);
        }
    }
}
