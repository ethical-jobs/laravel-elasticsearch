<?php

namespace Tests\Integration\Console;

use Mockery;
use Illuminate\Support\Facades\Artisan;
use EthicalJobs\Elasticsearch\IndexManager;
use EthicalJobs\Elasticsearch\Indexing\Indexer;
use Tests\TestCase;

class FlushIndexCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it_deletes_creates_and_indexes_documents()
    {
        $index = Mockery::mock(IndexManager::class)
            ->shouldReceive('delete')
            ->once()
            ->withNoArgs()     
            ->andReturn(1);

        $index->shouldReceive('create')
            ->once()
            ->withNoArgs()     
            ->andReturn(1);        

        $this->app->instance(IndexManager::class, $index->getMock());

        $indexer = Mockery::mock(Indexer::class)
            ->shouldReceive('queue')
            ->atLeast()
            ->once()
            ->withAnyArgs();

        $this->app->instance(Indexer::class, $indexer->getMock());            

        Artisan::call('ej:es:flush');
    }
}
