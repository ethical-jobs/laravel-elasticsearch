<?php

namespace Tests\Integration\Repositories\Elasticsearch;

use Mockery;
use Elasticsearch\Client;
use EthicalJobs\Storage\Testing\RepositoryFactory;
use Tests\Fixtures\Models;
use EthicalJobs\Elasticsearch\Testing\SearchResultsFactory;

class FindTest extends \Tests\TestCase
{
    /**
     * @test
     * @group Unit
     */
    public function it_searches_the_correct_index()
    {
        $people = factory(Models\Person::class, 10)->create();

        $client = Mockery::mock(Client::class)
            ->shouldReceive('search')
            ->once()
            ->withArgs(function($query) {
                $this->assertEquals('test-index', $query['index']);
                return true;
            })
            ->andReturn(SearchResultsFactory::getSearchResults($people))
            ->getMock();       

        $repository = RepositoryFactory::makeElasticsearch($client, new Models\Person);

        $results = $repository->find();
    }      

    /**
     * @test
     * @group Unit
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

        $repository = RepositoryFactory::makeElasticsearch($client, new Models\Person);  

        $results = $repository->find();
    }    

    /**
     * @test
     * @group Unit
     */
    public function it_throws_excepion_on_empty_results()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);

        $searchResults = SearchResultsFactory::getEmptySearchResults();

        $client = Mockery::mock(Client::class)
            ->shouldReceive('search')
            ->once()
            ->withAnyArgs()
            ->andReturn($searchResults)
            ->getMock();       

        $repository = RepositoryFactory::makeElasticsearch($client, new Models\Person);    

        $results = $repository->find();
    }                        
}
