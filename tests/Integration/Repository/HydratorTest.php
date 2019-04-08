<?php

namespace Tests\Integration\Repositories;

use ArrayObject;
use EthicalJobs\Elasticsearch\Hydrators;
use Illuminate\Database\Eloquent\Model;
use Tests\Fixtures\Models;
use Tests\Fixtures\Repositories\PersonRepository;
use Tests\Helpers\Indexer;
use Tests\TestCase;

class HydratorTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_hydrate_results_as_models()
    {
        factory(Models\Person::class, 10)->create();

        Indexer::all(Models\Person::class);

        $repository = resolve(PersonRepository::class);

        $results = $repository
            ->setHydrator(new Hydrators\EloquentHydrator)
            ->find();

        $results->each(function ($result) {
            $this->assertInstanceOf(Model::class, $result);
        });
    }

    /**
     * @test
     * @group elasticsearch
     */
    public function it_can_hydrate_results_as_objects_by_default()
    {
        factory(Models\Person::class, 10)->create();

        Indexer::all(Models\Person::class);

        $repository = resolve(PersonRepository::class);

        $results = $repository
            ->find();

        $results->each(function ($result) {
            $this->assertInstanceOf(ArrayObject::class, $result);
        });
    }

    /**
     * @test
     */
    public function it_can_hydrate_results_as_objects()
    {
        factory(Models\Person::class, 10)->create();

        Indexer::all(Models\Person::class);

        $repository = resolve(PersonRepository::class);

        $results = $repository
            ->setHydrator(new Hydrators\ObjectHydrator)
            ->find();

        $results->each(function ($result) {
            $this->assertInstanceOf(ArrayObject::class, $result);
        });
    }
}
