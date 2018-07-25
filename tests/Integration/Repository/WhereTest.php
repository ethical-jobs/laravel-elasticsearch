<?php

namespace Tests\Integration\Repositories;

use Mockery;
use Elasticsearch\Client;
use EthicalJobs\Elasticsearch\Testing\SearchResultsFactory;
use Tests\Helpers\Indexer;
use Tests\Fixtures\Models;
use Tests\Fixtures\Repositories\PersonRepository;

class WhereTest extends \Tests\TestCase
{
    /**
     * @test
     * @group elasticsearch
     */
    public function it_can_filter_by_comparison_operators()
    {
        factory(Models\Person::class, 2)->create([
            'age' => 67,
        ]);

        factory(Models\Person::class, 2)->create([
            'age' => 65,
        ]);

        factory(Models\Person::class, 2)->create([
            'age' => 60,
        ]);

        Indexer::all(Models\Person::class);     
        
        dump(resolve(PersonRepository::class)->find()->count());  

        resolve(PersonRepository::class)
            ->where('age', '>', 65)
            ->find()
            ->each(function ($person) {
                $this->assertTrue($person->age > 65);
            });        

       resolve(PersonRepository::class)
            ->where('age', '>=', 65)
            ->find()
            ->each(function ($person) {
                $this->assertTrue($person->age >= 65);
            });    
            
       resolve(PersonRepository::class)
            ->where('age', '<', 65)
            ->find()
            ->each(function ($person) {
                $this->assertTrue($person->age < 65);
            });   
            
       resolve(PersonRepository::class)
            ->where('age', '<=', 65)
            ->find()
            ->each(function ($person) {
                $this->assertTrue($person->age <= 65);
            });    
            
       resolve(PersonRepository::class)
            ->where('age', '=', 65)
            ->find()
            ->each(function ($person) {
                $this->assertTrue($person->age == 65);
            });  
            
       resolve(PersonRepository::class)
            ->where('age', '!=', 65)
            ->find()
            ->each(function ($person) {
                $this->assertTrue($person->age != 65);
            });              
    }  

    /**
     * @test
     * @group elasticsearch
     */
    public function it_can_find_by_wildcard_operator()
    {
        factory(Models\Person::class)->create([
            'first_name' => 'Trump',
            'age' => 33
        ]);

        factory(Models\Person::class)->create([
            'first_name' => 'Ivanka Trump',
            'age' => 33
        ]);
        
        factory(Models\Person::class)->create([
            'first_name' => 'Barak Obama',
            'age' => 33
        ]);        

        Indexer::all(Models\Person::class); 

        // dump(resolve(PersonRepository::class)->find()->count());  

        $people = resolve(PersonRepository::class)
            ->where('first_name', 'like', 'Tr%mp')
            ->find();  

        dd($people);
    }                
}
