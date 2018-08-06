<?php

namespace Tests\Integration\Repositories;

use EthicalJobs\Elasticsearch\Testing\ResetElasticsearchIndex;
use Tests\Fixtures\Repositories\PersonRepository;
use Tests\Helpers\Indexer;
use Tests\Fixtures\Models;

class FindByFieldTest extends \Tests\TestCase
{
    use ResetElasticsearchIndex;

    /**
     * @test
     */
    public function it_can_find_by_a_field()
    {
        $donald = factory(Models\Person::class)->create([
            'first_name' => 'Donald',
            'last_name' => 'Trump',
        ]);     

        $barak = factory(Models\Person::class)->create([
            'first_name' => 'Barak',
            'last_name' => 'Obama',
        ]);        

        Indexer::all(Models\Person::class);     

        $shouldBeDonald = resolve(PersonRepository::class)
            ->findByField('first_name', $donald->first_name);

        $this->assertEquals(
            $shouldBeDonald->first_name,
            $donald->first_name
        );

        $shouldBeBarak = resolve(PersonRepository::class)
            ->findByField('last_name', $barak->last_name);

        $this->assertEquals(
            $shouldBeBarak->last_name,
            $barak->last_name
        );        
    }          
}
