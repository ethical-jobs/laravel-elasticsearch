<?php

namespace Tests\Integration\Repositories;

use Mockery;
use Elasticsearch\Client;
use Tests\Fixtures\RepositoryFactory;
use Tests\Fixtures\Models;
use EthicalJobs\Elasticsearch\Testing\SearchResultsFactory;

class LimitTest extends \Tests\TestCase
{
    /**
     * @test
     * @group elasticsearch
     */
    public function it_can_add_a_limit()
    {
        $people = factory(Models\Person::class, 10)->create();

        $client = Mockery::mock(Client::class)
            ->shouldReceive('search')
            ->once()
            ->withArgs(function($query) {
                $this->assertEquals(17, array_get($query, 
                    'body.size'
                ));     
                return true;
            })
            ->andReturn(SearchResultsFactory::getSearchResults($people))
            ->getMock();       

        $repository = RepositoryFactory::make($client, new Models\Person);

        $result = $repository
            ->limit(17)
            ->find();

        $this->assertEquals(10, $result->count());        
    }                  
}
