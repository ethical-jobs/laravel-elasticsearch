<?php

namespace Tests\Integration\Repositories;

use Mockery;
use Tests\Fixtures\Models;
use Tests\Fixtures\RepositoryFactory;
use Tests\Fixtures\Criteria;
use EthicalJobs\Storage\CriteriaCollection;
use EthicalJobs\Storage\Repositories\ElasticsearchRepository;
use EthicalJobs\Elasticsearch\Testing\SearchResultsFactory;

class CriteriaTest extends \Tests\TestCase
{
    /**
     * @test
     * @group Integration
     */
    public function its_criteria_are_an_empty_collection_by_default()
    {
        $repository = RepositoryFactory::make();  
        
        $criteria = $repository->getCriteriaCollection();

        $this->assertInstanceOf(CriteriaCollection::class, $criteria);

        $this->assertTrue($criteria->isEmpty());
    }    

    /**
     * @test
     * @group Integration
     */
    public function it_can_set_and_get_it_criteria_collection()
    {
        $repository = RepositoryFactory::make();  

        $collection = new CriteriaCollection([
            Criteria\Males::class,
            Criteria\OverFifity::class,
        ]);
        
        $repository->setCriteriaCollection($collection);

        $this->assertEquals($repository->getCriteriaCollection(), $collection);
    }      

    /**
     * @test
     * @group Integration
     */
    public function it_can_add_criteria_items()
    {
        $repository = RepositoryFactory::make();  
        
        $expected = $repository->addCriteria(Criteria\OverFifity::class)
            ->getCriteriaCollection()
            ->get(Criteria\OverFifity::class);

        $this->assertEquals(new Criteria\OverFifity, $expected);
    }  

    /**
     * @test
     * @group Integration
     */
    public function it_can_apply_criteria()
    {
        $overFifties = factory(Models\Person::class, 5)
            ->create(['age' => 55]);        

        $searchResults = SearchResultsFactory::getSearchResults($overFifties);

        $client = Mockery::mock(Client::class)
            ->shouldReceive('search')
            ->once()
            ->withAnyArgs()
            ->andReturn($searchResults)
            ->getMock();

        $repository = RepositoryFactory::make();  
        
        $people = $repository
            ->setStorageEngine($client)
            ->addCriteria(Criteria\OverFifity::class)
            ->find();

        $people->each(function($person) {
            $this->assertTrue($person->age > 50);
        });
    }      
}
