<?php

namespace Tests\Integration\Repositories;

use Mockery;
use Elasticsearch\Client;
use Tests\Fixtures\RepositoryFactory;
use Tests\Fixtures\Models;
use EthicalJobs\Storage\Repositories\ElasticsearchRepository;

class ElasticsearchRepositoryTest extends \Tests\TestCase
{
    /**
     * @test
     * @group elasticsearch
     */
    public function it_can_set_and_get_its_storage_engine()
    {
        $client = Mockery::mock(Client::class);

        $repository = RepositoryFactory::make();  

        $repository->setStorageEngine($client);

        $this->assertEquals($repository->getStorageEngine(), $client);            
    }    
}
