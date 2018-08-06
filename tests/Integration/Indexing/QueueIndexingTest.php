<?php

namespace Tests\Integration\Indexing\Indexer;

use Mockery;
use Elasticsearch\Client;
use Illuminate\Support\Facades\Queue;
use EthicalJobs\Elasticsearch\Indexing\Indexer;
use EthicalJobs\Elasticsearch\Indexing\IndexDocuments;
use Tests\Fixtures\Models\Person;

class QueueIndexingTest extends \Tests\TestCase
{
    /**
     * @test
     * @group Integration
     */
    public function it_can_chunk_a_query_and_queue_documents()
    {
        Queue::fake();

        factory(Person::class, 100)->create();    

        $indexer = resolve(Indexer::class);
         
        $query = Person::query();

 		$indexer->queue($query, 5); // chunks of 5

         Queue::assertPushed(IndexDocuments::class, 20); 
         
        Queue::assertPushed(IndexDocuments::class, function ($job) {
            return $job->documents->count() === 5;
        });         
    } 	  
}