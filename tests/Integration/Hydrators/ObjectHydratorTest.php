<?php

namespace Tests\Integration\Hydrators\Elasticsearch;

use ArrayObject;
use Tests\Fixtures\Models;
use Illuminate\Support\Collection;
use EthicalJobs\Elasticsearch\Hydrators\ObjectHydrator;
use EthicalJobs\Elasticsearch\Testing\SearchResultsFactory;
use Tests\TestCase;

class ObjectHydratorTest extends TestCase
{
    /**
     * @test
     * @group Integration
     */
    public function it_can_hydrate_a_single_entity()
    {
        $vehicle = factory(Models\Vehicle::class, 1)->create();

        $response = SearchResultsFactory::getSearchResults($vehicle);

        $toHydrate = $response['hits']['hits'][0]['_source'];

        $hydrated = (new ObjectHydrator)
            ->setIndexable(new Models\Vehicle)
            ->hydrateEntity($toHydrate);

        $this->assertInstanceOf(ArrayObject::class, $hydrated);

        foreach ($toHydrate as $property => $value) {
            $this->assertEquals($hydrated->$property, $value);
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

        $collection = (new ObjectHydrator)
            ->setIndexable(new Models\Vehicle)
            ->hydrateCollection($response);

        $this->assertInstanceOf(Collection::class, $collection);

        $collection->each(function ($entity) {
            $this->assertInstanceOf(ArrayObject::class, $entity);
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

        $collection = (new ObjectHydrator)
            ->setIndexable(new Models\Vehicle)
            ->hydrateCollection($response);

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

        $collection = (new ObjectHydrator)
            ->setIndexable(new Models\Vehicle)
            ->hydrateCollection($response);

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

        $collection = (new ObjectHydrator)
            ->setIndexable(new Models\Vehicle)
            ->hydrateCollection($response);

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertEquals(0, $collection->count());
    }

    /**
     * @test
     * @group Integration
     */
    public function it_builds_document_relations()
    {
        $documentRelations = (new Models\Family)->getDocumentRelations();

        $families = factory(Models\Family::class, 3)
            ->create()
            ->each(function ($family) {
                factory(Models\Person::class, rand(2, 6))->create(['family_id' => $family->id]);
                factory(Models\Vehicle::class)->create(['family_id' => $family->id]);
            });

        $families->load($documentRelations);

        $response = SearchResultsFactory::getSearchResults($families);

        $hydrated = (new ObjectHydrator)
            ->setIndexable(new Models\Family)
            ->hydrateCollection($response);

        // Check that document relations are built
        foreach ($hydrated as $family) {
            // Vehicle
            $this->assertInstanceOf(ArrayObject::class, $family->vehicle);
            $this->assertTrue(isset($family->vehicle->id));
            // Family members
            $this->assertInstanceOf(Collection::class, $family->members);
            $family->members->each(function ($person) {
                $this->assertInstanceOf(ArrayObject::class, $person);
                $this->assertTrue(isset($person->id));
            });
        }
    }
}
