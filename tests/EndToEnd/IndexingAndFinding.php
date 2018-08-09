<?php

namespace Tests\Integration\Indexing\Indexer;

use EthicalJobs\Elasticsearch\Hydrators;
use EthicalJobs\Elasticsearch\Indexing\Indexer;
use Tests\Fixtures\Repositories\PersonRepository;
use Tests\Fixtures\Models\Person;

class IndexingAndFinding extends \Tests\TestCase
{   
    /**
     * @test
     */
    public function it_can_index_documents_then_return_them()
    {
        factory(Person::class, 4000)->create(); 

        $query = Person::query();

        $indexer = resolve(Indexer::class);
         
        $indexer->synchronous();

        $indexer->queue($query, 500);

        sleep(2);

        $repository = resolve(PersonRepository::class);

        $people = $repository
            ->setHydrator(new Hydrators\EloquentHydrator)
            ->limit(10000)
            ->find();

        $this->assertEquals(4000, $people->count());

        foreach ($people as $person) {
            $this->assertInstanceOf(Person::class, $person);
        }
    } 	    
}
