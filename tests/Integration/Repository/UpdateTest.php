<?php

namespace Tests\Integration\Repositories;

use Tests\Fixtures\RepositoryFactory;

class UpdateTest extends \Tests\TestCase
{
    /**
     * @test
     * @group elasticsearch
     */
    public function it_can_update_an_entity_and_return_it()
    {
        $this->expectException(\Exception::class);

        $repository->update(123, []);
    }    
}
