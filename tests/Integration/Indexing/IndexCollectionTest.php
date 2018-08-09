<?php

namespace Tests\Integration\Indexing\Indexer;

use Mockery;
use Elasticsearch\Client;
use EthicalJobs\Elasticsearch\Indexing\Indexer;
use Tests\Fixtures\Models\Person;

class IndexCollectionTest extends \Tests\TestCase
{
    /**
     * @test
     */
    public function it_indexes_the_documents_and_returns_the_response()
    {
        $people = factory(Person::class, 25)->create();

        $params = [
            'refresh' => false,
            'body' => [],            
        ];
         
        foreach ($people as $person) {
            $params['body'][] = [
                'index' => [
                    '_index' => 'test-index',
                    '_id' => $person->getDocumentKey(),
                    '_type' => $person->getDocumentType(),
                ],
            ];
            $params['body'][] = $person->getDocumentTree();
        }    

 		$client = Mockery::mock(Client::class)
 			->shouldReceive('bulk')
 			->once()
 			->with($params)
 			->andReturn(['hits' => 1])
 			->getMock();

        $indexer = resolve(Indexer::class);
		 
        $indexer->setElasticsearchClient($client);

 		$response = $indexer->indexCollection($people);

 		$this->assertEquals(['hits' => 1], $response);
    } 	   
    
    /**
     * @test
     */
    public function it_can_index_a_collection_synchronously()
    {
        $people = factory(Person::class, 25)->create();

 		$client = Mockery::mock(Client::class)
 			->shouldReceive('bulk')
 			->once()
 			->withArgs(function ($params) {
                return $params['refresh'] === 'wait_for';
             })
 			->andReturn(['hits' => 1])
 			->getMock();

        $indexer = resolve(Indexer::class);
		 
        $indexer->setElasticsearchClient($client);
         
        $indexer->synchronous();

 		$response = $indexer->indexCollection($people);
    } 	    
}
