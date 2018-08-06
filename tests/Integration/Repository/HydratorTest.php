<?php

namespace Tests\Integration\Repositories;

use ArrayObject;
use Illuminate\Database\Eloquent\Model;
use EthicalJobs\Elasticsearch\Hydrators;
use EthicalJobs\Elasticsearch\Testing\ResetElasticsearchIndex;
use Tests\Fixtures\Repositories\PersonRepository;
use Tests\Helpers\Indexer;
use Tests\Fixtures\Models;

class HydratorTest extends \Tests\TestCase
{
    /**
     * @test
     */
    public function it_can_hydrate_results_as_models()
    {
        $people = factory(Models\Person::class, 10)->create();      

        Indexer::all(Models\Person::class);        

        $repository = resolve(PersonRepository::class);

        $results = $repository
            ->setHydrator(new Hydrators\EloquentHydrator)
            ->find();

        $results->each(function($result) {
            $this->assertInstanceOf(Model::class, $result);
        });
    }      

    /**
     * @test
     * @group elasticsearch
     */
    public function it_can_hydrate_results_as_objects_by_default()
    {
        $people = factory(Models\Person::class, 10)->create();      

        Indexer::all(Models\Person::class);        

        $repository = resolve(PersonRepository::class);

        $results = $repository
            ->find();

        $results->each(function($result) {
            $this->assertInstanceOf(ArrayObject::class, $result);
        });
    }                    

    /**
     * @test
     */
    public function it_can_hydrate_results_as_objects()
    {
        $people = factory(Models\Person::class, 10)->create();      

        Indexer::all(Models\Person::class);        

        $repository = resolve(PersonRepository::class);

        $results = $repository
            ->setHydrator(new Hydrators\ObjectHydrator)
            ->find();

        $results->each(function($result) {
            $this->assertInstanceOf(ArrayObject::class, $result);
        });
    }                  
}
