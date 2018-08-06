<?php

namespace Tests\Integration\Repositories;

use EthicalJobs\Elasticsearch\Testing\ResetElasticsearchIndex;
use Tests\Fixtures\Repositories\PersonRepository;
use Tests\Helpers\Indexer;
use Tests\Fixtures\Models;

class LimitTest extends \Tests\TestCase
{
    use ResetElasticsearchIndex;

    /**
     * @test
     */
    public function it_can_limit_results()
    {
        factory(Models\Person::class, 30)->create();      

        Indexer::all(Models\Person::class);     

        $people = resolve(PersonRepository::class)
            ->limit(20)
            ->find();

        $this->assertEquals(20, $people->count());           
    } 
    
    /**
     * @test
     */
    public function it_limit_is_10_thousand_by_default()
    {
        // We dont want to populate 10,000 models as thats extreme 
        // so lets just do a large-ish amount
        //
        // @todo Write a trait called HasSearchParams that has methods:
        // - getParams
        // - setParams
        // - resetParams (resets search to defaults e.g limit and clears (see construct))

        factory(Models\Person::class, 3000)->create();      

        Indexer::all(Models\Person::class);     

        $people = resolve(PersonRepository::class)
            ->find();

        $this->assertEquals(3000, $people->count());           
    }      
}
