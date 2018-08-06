<?php

namespace Tests\Integration\Repositories;

use Tests\Fixtures\RepositoryFactory;

class DeleteTest extends \Tests\TestCase
{
    /**
     * @test
     * @group elasticsearch
     */
    public function it_can_delete_an_entity_and_return_it()
    {
        $this->expectException(\Exception::class);

        $repository->delete(123);
    }    
}
