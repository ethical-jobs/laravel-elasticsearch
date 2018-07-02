<?php

namespace Tests\Integration\Repositories;

use Mockery;
use Elasticsearch\Client;
use Tests\Fixtures\RepositoryFactory;
use Tests\Fixtures\Models;
use EthicalJobs\Elasticsearch\Testing\SearchResultsFactory;

class SearchTest extends \Tests\TestCase
{
    /**
     * @test
     * @group elasticsearch
     */
    public function it_can_add_a_search_term_filter()
    {
        $people = factory(Models\Person::class, 10)->create();

        $client = Mockery::mock(Client::class)
            ->shouldReceive('search')
            ->once()
            ->withArgs(function($query) {
                $this->assertEquals(array_get($query, 'body.query'), [
                    'simple_query_string' => [
                        'default_operator' => 'and',
                        'query' => 'How much wood could a Woodchuck chuck?',
                        'fields' => [ '_all' ],
                    ],                 
                ]);        
                return true;
            })
            ->andReturn(SearchResultsFactory::getSearchResults($people))
            ->getMock();       

        $repository = RepositoryFactory::make($client, new Models\Person);   

        $result = $repository
            ->search('How much wood could a Woodchuck chuck?')
            ->find();

        $this->assertEquals(10, $result->count());        
    }                  
}
