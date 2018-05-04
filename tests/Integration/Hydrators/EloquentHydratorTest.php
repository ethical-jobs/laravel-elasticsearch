<?php

namespace Tests\Integration\Hydrators;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Tests\Fixtures\Models;
use EthicalJobs\Storage\Hydrators\Elasticsearch\EloquentHydrator;
use EthicalJobs\Elasticsearch\Testing\SearchResultsFactory;

class EloquentHydratorTest extends \Tests\TestCase
{
    /**
     * @test
     * @group Integration
     */
    public function it_can_hydrate_a_single_entity()
    {
        $vehicle = factory(Models\Vehicle::class, 1)->create();

        $response = SearchResultsFactory::getSearchResults($vehicle);

        $toHydrate = $response['hits']['hits'][0];

        $hydrated = (new EloquentHydrator)
            ->setIndexable(new Models\Vehicle)
            ->hydrateEntity($toHydrate);

        $this->assertInstanceOf(Model::class, $hydrated);

        foreach ($toHydrate['_source'] as $property => $value) {
            if (! $hydrated->$property instanceof Carbon) {
                $this->assertEquals($hydrated->$property, $value);
            }
        }
    }    

    /**
     * @test
     * @group Integration
     */
    public function it_returns_a_collection_of_array_objects()
    {
        $vehicles = factory(Models\Vehicle::class, 5)->create();

        $response = SearchResultsFactory::getSearchResults($vehicles);

        $collection = (new EloquentHydrator)
            ->setIndexable(new Models\Vehicle)
            ->hydrateCollection($response, new Models\Vehicle);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $collection);

        $collection->each(function ($entity) {
            $this->assertInstanceOf(Model::class, $entity);
        });
    }

    /**
     * @test
     * @group Integration
     */
    public function it_sets_a_score_property_on_models()
    {
        $vehicles = factory(Models\Vehicle::class, 5)->create();

        $response = SearchResultsFactory::getSearchResults($vehicles);

        $collection = (new EloquentHydrator)
            ->setIndexable(new Models\Vehicle)
            ->hydrateCollection($response, new Models\Vehicle);

        $collection->each(function ($entity) {
            $this->assertTrue(is_numeric($entity->documentScore));
            $this->assertEquals(1, $entity->documentScore);
        });
    }

    /**
     * @test
     * @group Integration
     */
    public function it_sets_a_isDocument_property_on_models()
    {
        $vehicles = factory(Models\Vehicle::class, 5)->create();

        $response = SearchResultsFactory::getSearchResults($vehicles);

        $collection = (new EloquentHydrator)
            ->setIndexable(new Models\Vehicle)
            ->hydrateCollection($response, new Models\Vehicle);

        $collection->each(function ($entity) {
            $this->assertTrue($entity->isDocument);
        });
    }

    /**
     * @test
     * @group Integration
     */
    public function it_returns_empty_collection_when_there_are_no_results()
    {
        $response = [];

        $collection = (new EloquentHydrator)
            ->setIndexable(new Models\Vehicle)
            ->hydrateCollection($response, new Models\Vehicle);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $collection);

        $this->assertEquals(0, $collection->count());
    }

    /**
     * @test
     * @group Integration
     * @group skipped
     */
    public function it_builds_document_relations()
    {
        $expectedRelations = ['members', 'vehicles'];

        $documentRelations = (new Models\Family)->getDocumentRelations();

        $families = factory(Models\Family::class, 2)
            ->create()
            ->each(function ($family) {
                factory(Models\Person::class, rand(2, 6))->create(['family_id' => $family->id]);
                factory(Models\Vehicle::class, rand(1, 2))->create(['family_id' => $family->id]);
            });

        $families->load($documentRelations);

        $response = SearchResultsFactory::getSearchResults($families);

        $collection = (new EloquentHydrator)
            ->setIndexable(new Models\Family)
            ->hydrateCollection($response, new Models\Family);

        // Check that document relations are built
        foreach ($collection as $family) {
            foreach ($expectedRelations as $relation) {
                $this->assertTrue($family->$relation->id ? true : false);
            }
        }
    }
}
