<?php

namespace Tests\Integration\Repositories;

use EthicalJobs\Elasticsearch\Testing\ResetElasticsearchIndex;
use EthicalJobs\Storage\CriteriaCollection;
use Tests\Fixtures\Criteria;
use Tests\Fixtures\Models;
use Tests\Fixtures\Repositories\PersonRepository;
use Tests\Helpers\Indexer;
use Tests\TestCase;

class CriteriaTest extends TestCase
{
    use ResetElasticsearchIndex;

    /**
     * @test
     */
    public function its_criteria_are_an_empty_collection_by_default()
    {
        $repository = resolve(PersonRepository::class);

        $criteria = $repository->getCriteriaCollection();

        $this->assertInstanceOf(CriteriaCollection::class, $criteria);

        $this->assertTrue($criteria->isEmpty());
    }

    /**
     * @test
     */
    public function it_can_set_and_get_it_criteria_collection()
    {
        $repository = resolve(PersonRepository::class);

        $collection = new CriteriaCollection([
            Criteria\Males::class,
            Criteria\OverFifity::class,
        ]);

        $repository->setCriteriaCollection($collection);

        $this->assertEquals($repository->getCriteriaCollection(), $collection);
    }

    /**
     * @test
     */
    public function it_can_add_criteria_items()
    {
        $repository = resolve(PersonRepository::class);

        $expected = $repository->addCriteria(Criteria\OverFifity::class)
            ->getCriteriaCollection()
            ->get(Criteria\OverFifity::class);

        $this->assertEquals(new Criteria\OverFifity, $expected);
    }

    /**
     * @test
     */
    public function it_can_apply_criteria()
    {
        factory(Models\Person::class, 2)->create([
            'age' => 55,
        ]);

        factory(Models\Person::class, 2)->create([
            'age' => 30,
        ]);

        Indexer::all(Models\Person::class);

        $people = resolve(PersonRepository::class)
            ->addCriteria(Criteria\OverFifity::class)
            ->find();

        $this->assertEquals(2, $people->count());

        $people->each(function ($person) {
            $this->assertTrue($person->age > 50);
        });
    }
}
