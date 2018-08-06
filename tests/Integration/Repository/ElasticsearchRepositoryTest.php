<?php

namespace Tests\Integration\Repositories;

use Mockery;
use Carbon\Carbon;
use EthicalJobs\Elasticsearch\Testing\ResetElasticsearchIndex;
use Tests\Fixtures\Repositories\PersonRepository;
use Tests\Helpers\Indexer;
use Tests\Fixtures\Models;

class ElasticsearchRepositoryTest extends \Tests\TestCase
{
    /**
     * @test
     */
    public function it_can_set_and_get_its_storage_engine()
    {
        $client = Mockery::mock(Client::class);

        $repository = resolve(PersonRepository::class);  

        $repository->setStorageEngine($client);

        $this->assertEquals($repository->getStorageEngine(), $client);            
    }    

    /**
     * @test
     */
    public function it_can_filter_by_multiple_methods()
    {
        factory(Models\Person::class, 3)->create([
            'first_name' => 'Barak',
            'last_name' => 'Obama',
            'sex' => 'male',
            'age' => 56,
        ]);   

        factory(Models\Person::class, 3)->create([
            'first_name' => 'Hillary',
            'last_name' => 'Clinton',
            'sex' => 'female',
            'age' => 70,
        ]);         

        factory(Models\Person::class, 2)->create([
            'first_name' => 'Bill',
            'last_name' => 'Clinton',
            'sex' => 'male',
            'age' => 71,
        ]);            

        factory(Models\Person::class, 5)->create([
            'first_name' => 'Donald',
            'last_name' => 'Trump',
            'sex' => 'trans',
            'age' => 72,
        ]);     

        factory(Models\Person::class, 3)->create([
            'first_name' => 'George',
            'last_name' => 'Bush',
            'sex' => 'female',
            'age' => 73,
        ]);              

        Indexer::all(Models\Person::class);     

        $shouldBeTheClintons = resolve(PersonRepository::class)
            ->whereIn('sex', ['male','female'])
            ->where('age', '>', 40)
            ->where('age', '<', 72)
            ->orderBy('created_at')
            ->search('clinton')
            ->find(); 

        $this->assertEquals(5, $shouldBeTheClintons->count());

        foreach ($shouldBeTheClintons as $clinton) {
            $this->assertTrue($clinton->age > 40 && $clinton->age < 72);
            $this->assertTrue(in_array($clinton->sex, ['male','female']));
            $this->assertTrue(str_contains($clinton->full_name, 'Clinton'));
        }        
    }       
}
