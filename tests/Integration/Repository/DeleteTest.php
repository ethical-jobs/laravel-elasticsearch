<?php

namespace Tests\Integration\Repositories;

use Exception;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    /**
     * @test
     * @group elasticsearch
     */
    public function it_can_delete_an_entity_and_return_it()
    {
        $this->expectException(Exception::class);

        $repository->delete(123);
    }
}
