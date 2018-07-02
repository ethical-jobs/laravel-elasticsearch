<?php

namespace Tests\Integration\Repositories;

use Mockery;
use Elasticsearch\Client;
use Tests\Fixtures\RepositoryFactory;
use Tests\Fixtures\Models;
use EthicalJobs\Elasticsearch\Testing\SearchResultsFactory;

class FindByIdTest extends \Tests\TestCase
{
    /**
     * @test
     * @group elasticsearch
     */
    public function it_can_find_by_id()
    {
        $people = factory(Models\Person::class, 1)->create();

        $searchResults = SearchResultsFactory::getSearchResults($people);

        $client = Mockery::mock(Client::class)
            ->shouldReceive('search')
            ->once()
            ->withArgs(function($query) use ($people) {
                $this->assertEquals($people->first()->id, array_get($query, 
                    'body.query.bool.filter.0.term.id'
                ));
                return true;
            })
            ->andReturn($searchResults)
            ->getMock();            

        $repository = RepositoryFactory::make($client, new Models\Person);

        $result = $repository->findById($people->first()->id);

        $this->assertEquals($result->id, $people->first()->id);
    }  

    /**
     * @test
     * @group elasticsearch
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

        $repository = RepositoryFactory::make($client, new Models\Person);

        $repository->findByField('first_name', 'Andrew');
    }         
}
