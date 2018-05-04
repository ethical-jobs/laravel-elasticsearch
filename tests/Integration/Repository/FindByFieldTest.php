<?php

namespace Tests\Integration\Repositories\Elasticsearch;

use Mockery;
use Elasticsearch\Client;
use EthicalJobs\Storage\Testing\RepositoryFactory;
use Tests\Fixtures\Models;
use EthicalJobs\Elasticsearch\Testing\SearchResultsFactory;

class FindByFieldTest extends \Tests\TestCase
{
    /**
     * @test
     * @group Unit
     */
    public function it_can_find_by_a_field()
    {
        $people = factory(Models\Person::class, 1)->create();

        $searchResults = SearchResultsFactory::getSearchResults($people);

        $client = Mockery::mock(Client::class)
            ->shouldReceive('search')
            ->once()
            ->withArgs(function($query) {
                $this->assertEquals('Andrew', array_get($query, 
                    'body.query.bool.filter.0.term.first_name'
                ));
                return true;
            })
            ->andReturn($searchResults)
            ->getMock();            

        $repository = RepositoryFactory::makeElasticsearch($client, new Models\Person);

        $result = $repository->findByField('first_name', 'Andrew');

        $this->assertEquals($result->first_name, $people->first()->first_name);
    }    

    /**
     * @test
     * @group Unit
     */
    public function it_throws_http_404_exception_when_no_model_found()
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

        $repository->findByField('first_name', 'Andrew');
    }         
}
