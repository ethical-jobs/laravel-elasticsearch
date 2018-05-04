<?php

namespace Tests\Integration\Repositories\Elasticsearch;

use Mockery;
use Elasticsearch\Client;
use EthicalJobs\Storage\Testing\RepositoryFactory;
use Tests\Fixtures\Models;
use EthicalJobs\Storage\Repositories\ElasticsearchRepository;

class ElasticsearchRepositoryTest extends \Tests\TestCase
{
    /**
     * @test
     * @group Unit
     */
    public function it_can_set_and_get_its_storage_engine()
    {
        $client = Mockery::mock(Client::class);

        $repository = RepositoryFactory::makeElasticsearch();  

        $repository->setStorageEngine($client);

        $this->assertEquals($repository->getStorageEngine(), $client);            
    }    
}
