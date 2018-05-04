<?php

namespace Tests\Integration\Repositories\Elasticsearch;

use Mockery;
use Elasticsearch\Client;
use EthicalJobs\Storage\Testing\RepositoryFactory;
use Tests\Fixtures\Models;
use EthicalJobs\Elasticsearch\Testing\SearchResultsFactory;

class OrderByTest extends \Tests\TestCase
{
    /**
     * @test
     * @group Unit
     */
    public function it_can_add_a_orderBy_filter()
    {
        $people = factory(Models\Person::class, 10)->create();

        $client = Mockery::mock(Client::class)
            ->shouldReceive('search')
            ->once()
            ->withArgs(function($query) {
                $this->assertEquals(["order" => "DESC"], array_get($query, 
                    'body.sort.0.age'
                ));
                $this->assertEquals(["order" => "DESC"], array_get($query, 
                    'body.sort.1._score'
                ));                
                return true;
            })
            ->andReturn(SearchResultsFactory::getSearchResults($people))
            ->getMock();       

        $repository = RepositoryFactory::makeElasticsearch($client, new Models\Person);   

        $result = $repository
            ->orderBy('age', 'DESC')
            ->find();

        $this->assertEquals(10, $result->count());        
    }                  
}
