<?php

namespace Tests\Integration\Repositories;

use Mockery;
use Elasticsearch\Client;
use EthicalJobs\Elasticsearch\Testing\SearchResultsFactory;
use Tests\Helpers\RepositoryFactory;
use Tests\Helpers\Indexer;
use Tests\Fixtures\Models;
use Tests\Fixtures\Repositories\PersonRepository;

class WhereTest extends \Tests\TestCase
{
    /**
     * @test
     * @group elasticsearch
     */
    public function it_can_find_by_greater_then_operator()
    {
        factory(Models\Person::class, 2)->create([
            'age' => 65,
        ]);

        factory(Models\Person::class, 2)->create([
            'age' => 35,
        ]);

        Indexer::all(Models\Person::class);      

        $repository = resolve(PersonRepository::class);

        $people = $repository
            ->where('age', '>', 65)
            ->find();
//!!!
        foreach ($people as $person) {
            $this->assertTrue($person->age > 65);
        }            
    }  

    /**
     * @test
     * @group elasticsearch
     */
    public function it_can_find_by_wildcard_operator()
    {
        $people = factory(Models\Person::class, 10)->create();

        $client = Mockery::mock(Client::class)
            ->shouldReceive('search')
            ->once()
            ->withArgs(function($query) {
                $this->assertEquals('Andre* McL*an', array_get($query, 
                    'body.query.bool.filter.0.wildcard.name.value'
                ));
                return true;
            })
            ->andReturn(SearchResultsFactory::getSearchResults($people))
            ->getMock();       

        $repository = RepositoryFactory::make($client, new Models\Person);     

        $result = $repository
            ->where('name', 'like', 'Andre% McL%an')
            ->find();

        $this->assertEquals(10, $result->count());        
    }    

    /**
     * @test
     * @group elasticsearch
     */
    public function it_can_find_by_not_equals_operator()
    {
        $people = factory(Models\Person::class, 10)->create();

        $client = Mockery::mock(Client::class)
            ->shouldReceive('search')
            ->once()
            ->withArgs(function($query) {
                $this->assertEquals(34, array_get($query, 
                    'body.query.bool.must_not.0.term.age'
                ));
                return true;
            })
            ->andReturn(SearchResultsFactory::getSearchResults($people))
            ->getMock();       

        $repository = RepositoryFactory::make($client, new Models\Person);         

        $result = $repository
            ->where('age', '!=', 34)
            ->find();

        $this->assertEquals(10, $result->count());        
    }       

    /**
     * @test
     * @group elasticsearch
     */
    public function it_can_find_by_equals_operator()
    {
        $people = factory(Models\Person::class, 10)->create();

        $client = Mockery::mock(Client::class)
            ->shouldReceive('search')
            ->once()
            ->withArgs(function($query) {
                $this->assertEquals(34, array_get($query, 
                    'body.query.bool.filter.0.term.age'
                ));
                return true;
            })
            ->andReturn(SearchResultsFactory::getSearchResults($people))
            ->getMock();       

        $repository = RepositoryFactory::make($client, new Models\Person);         

        $result = $repository
            ->where('age', '=', 34)
            ->find();

        $this->assertEquals(10, $result->count());        
    }  

    /**
     * @test
     * @group elasticsearch
     */
    public function it_can_find_by_equals_operator_by_default()
    {
        $people = factory(Models\Person::class, 10)->create();

        $client = Mockery::mock(Client::class)
            ->shouldReceive('search')
            ->once()
            ->withArgs(function($query) {
                $this->assertEquals(37, array_get($query, 
                    'body.query.bool.filter.0.term.age'
                ));
                return true;
            })
            ->andReturn(SearchResultsFactory::getSearchResults($people))
            ->getMock();       

        $repository = RepositoryFactory::make($client, new Models\Person);         

        $result = $repository
            ->where('age', 37)
            ->find();

        $this->assertEquals(10, $result->count());        
    }                 
}
