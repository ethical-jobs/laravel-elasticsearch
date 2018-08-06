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
     * @group Integration
     */
    public function it_indexes_the_document_and_returns_the_response()
    {
 		$indexName = 'test-index';

 		$person = factory(Person::class)->create();

 		$params = [
            'index' 	=> $indexName,
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

 		$indexer = new Indexer($client, $indexName);

 		$response = $indexer->deleteDocument($person);

 		$this->assertEquals(['hits' => 1], $response);
	} 	
	
    /**
     * @test
     * @group Integration
     */
    public function it_can_index_documents_synchronously()
    {
 		$indexName = 'test-index';

 		$person = factory(Person::class)->create();

 		$params = [
            'index' 	=> $indexName,
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

		$indexer = new Indexer($client, $indexName);
		 
		$indexer->synchronous();

 		$response = $indexer->deleteDocument($person);

 		$this->assertEquals(['hits' => 1], $response);
    }	
}
