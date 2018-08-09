<?php

namespace Tests\Integration\Console;

use Mockery;
use Illuminate\Support\Facades\Artisan;
use EthicalJobs\Elasticsearch\IndexManager;

class DeleteIndexCommandTest extends \Tests\TestCase
{
    /**
     * @test
     */
    public function it_deletes_an_index()
    {
        $index = Mockery::mock(IndexManager::class)
            ->shouldReceive('delete')
            ->once()
            ->withNoArgs()     
            ->andReturn(true);

        $this->app->instance(IndexManager::class, $index->getMock());

        Artisan::call('ej:es:index-delete');
    }
}
