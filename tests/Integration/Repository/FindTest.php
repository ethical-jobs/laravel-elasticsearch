<?php

namespace Tests\Integration\Repositories;

use Mockery;
use Elasticsearch\Client;
use ONGR\ElasticsearchDSL\Search;
use EthicalJobs\Elasticsearch\Testing\SearchResultsFactory;
use Tests\Fixtures\Repositories\PersonRepository;
use Tests\Fixtures\Models;

class FindTest extends \Tests\TestCase
{
    /**
     * @test
     */
    public function it_searches_the_correct_index()
    {
        $people = factory(Models\Person::class, 10)->create();

        $client = Mockery::mock(Client::class)
            ->shouldReceive('search')
            ->once()
            ->withArgs(function($query) {
                $this->assertEquals('testing', $query['index']);
                return true;
            })
            ->andReturn(SearchResultsFactory::getSearchResults($people))
            ->getMock();       

        $repository = new PersonRepository(new Search, $client);

        $results = $repository->find();
    }      

    /**
     * @test
     */
    public function it_searches_the_correct_document_type()
    {
        $people = factory(Models\Person::class, 10)->create();

        $client = Mockery::mock(Client::class)
            ->shouldReceive('search')
            ->once()
            ->withArgs(function($query) {
                $this->assertEquals('people', $query['type']);
                return true;
            })
            ->andReturn(SearchResultsFactory::getSearchResults($people))
            ->getMock();       

        $repository = new PersonRepository(new Search, $client); 

        $results = $repository->find();
    }                       
}
