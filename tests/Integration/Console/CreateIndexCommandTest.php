<?php

namespace Tests\Integration\Console;

use EthicalJobs\Elasticsearch\IndexManager;
use Illuminate\Support\Facades\Artisan;
use Mockery;
use Tests\TestCase;

class CreateIndexCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_an_index()
    {
        $index = Mockery::mock(IndexManager::class)
            ->shouldReceive('create')
            ->once()
            ->withNoArgs()
            ->andReturn(true);

        $this->app->instance(IndexManager::class, $index->getMock());

        Artisan::call('ej:es:index-create');
    }
}
