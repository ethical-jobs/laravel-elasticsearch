<?php

namespace Tests\Integration\Indexing\Indexer;

use Mockery;
use Elasticsearch\Client;
use EthicalJobs\Elasticsearch\Indexing\Indexer;
use Tests\Fixtures\Models\Person;

class DeleteDocumentTest extends \Tests\TestCase
{
    /**
     * @test
     */
    public function it_indexes_the_document_and_returns_the_response()
    {
 		$person = factory(Person::class)->create();

 		$params = [
            'index' 	=> 'test-index',
            'id'    	=> $person->getDocumentKey(),
			'type'  	=> $person->getDocumentType(),
			'refresh'	=> false,
 		];

 		$client = Mockery::mock(Client::class)
 			->shouldReceive('delete')
 			->once()
 			->with($params)
 			->andReturn(['hits' => 1])
 			->getMock();

		$indexer = resolve(Indexer::class);
		 
		$indexer->setElasticsearchClient($client);

 		$response = $indexer->deleteDocument($person);

 		$this->assertEquals(['hits' => 1], $response);
	} 	
	
    /**
     * @test
     */
    public function it_can_delete_documents_synchronously()
    {
 		$person = factory(Person::class)->create();

 		$params = [
            'index' 	=> 'test-index',
            'id'    	=> $person->getDocumentKey(),
			'type'  	=> $person->getDocumentType(),
			'refresh'	=> 'wait_for',
 		];

 		$client = Mockery::mock(Client::class)
 			->shouldReceive('delete')
 			->once()
 			->with($params)
 			->andReturn(['hits' => 1])
 			->getMock();

		$indexer = resolve(Indexer::class);
		 
		$indexer->setElasticsearchClient($client);
		 
		$indexer->synchronous();

 		$response = $indexer->deleteDocument($person);

 		$this->assertEquals(['hits' => 1], $response);
    }	
}
