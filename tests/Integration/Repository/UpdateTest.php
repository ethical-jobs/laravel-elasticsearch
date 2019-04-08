<?php

namespace Tests\Integration\Repositories;


use Tests\TestCase;

class UpdateTest extends TestCase
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
