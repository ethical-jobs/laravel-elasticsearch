<?php

namespace Tests\Integration\Repositories;

use Mockery;
use Elasticsearch\Client;
use ArrayObject;
use Illuminate\Database\Eloquent\Model;
use Tests\Fixtures\RepositoryFactory;
use Tests\Fixtures\Models;
use EthicalJobs\Elasticsearch\Hydrators;
use EthicalJobs\Elasticsearch\Testing\SearchResultsFactory;

class HydratorTest extends \Tests\TestCase
{
    /**
     * @test
     * @group Unit
     */
    public function it_can_hydrate_results_as_models()
    {
        $people = factory(Models\Person::class, 10)->create();

        $client = Mockery::mock(Client::class)
            ->shouldReceive('search')
            ->once()
            ->withAnyArgs()
            ->andReturn(SearchResultsFactory::getSearchResults($people))
            ->getMock();       

        $repository = RepositoryFactory::make($client, new Models\Person);

        $results = $repository
            ->setHydrator(new Hydrators\EloquentHydrator)
            ->find();

        $results->each(function($result) {
            $this->assertInstanceOf(Model::class, $result);
        });
    }      

    /**
     * @test
     * @group Unit
     */
    public function it_can_hydrate_results_as_objects_by_default()
    {
        $people = factory(Models\Person::class, 10)->create();

        $client = Mockery::mock(Client::class)
            ->shouldReceive('search')
            ->once()
            ->withAnyArgs()
            ->andReturn(SearchResultsFactory::getSearchResults($people))
            ->getMock();       

        $repository = RepositoryFactory::make($client, new Models\Person);

        $results = $repository
            ->find();

        $results->each(function($result) {
            $this->assertInstanceOf(ArrayObject::class, $result);
        });
    }                    

    /**
     * @test
     * @group Unit
     */
    public function it_can_hydrate_results_as_objects()
    {
        $people = factory(Models\Person::class, 10)->create();

        $client = Mockery::mock(Client::class)
            ->shouldReceive('search')
            ->once()
            ->withAnyArgs()
            ->andReturn(SearchResultsFactory::getSearchResults($people))
            ->getMock();       

        $repository = RepositoryFactory::make($client, new Models\Person);

        $results = $repository
            ->setHydrator(new Hydrators\ObjectHydrator)
            ->find();

        $results->each(function($result) {
            $this->assertInstanceOf(ArrayObject::class, $result);
        });
    }                  
}
