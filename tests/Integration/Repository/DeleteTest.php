<?php

namespace Tests\Integration\Repositories\Elasticsearch;

use EthicalJobs\Storage\Testing\RepositoryFactory;

class DeleteTest extends \Tests\TestCase
{
    /**
     * @test
     * @group Unit
     */
    public function it_can_delete_an_entity_and_return_it()
    {
        $this->expectException(\Exception::class);

        $repository->delete(123);
    }    
}
