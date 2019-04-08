<?php

namespace Tests\Integration\Console;

use EthicalJobs\Elasticsearch\Commands\IndexDocuments;
use EthicalJobs\Elasticsearch\Exceptions\IndexingException;
use EthicalJobs\Elasticsearch\Indexing\Indexer;
use EthicalJobs\Elasticsearch\Utilities;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\Fixtures\Models;
use Tests\TestCase;

class IndexDocumentsCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it_indexes_all_indexables_by_default()
    {
        $indexables = Utilities::getIndexables();

        $indexer = Mockery::mock(Indexer::class)
            ->shouldReceive('queue')
            ->times(count($indexables))
            ->withAnyArgs()
            ->andReturn(null)
            ->getMock();

        $this->app->instance(Indexer::class, $indexer);

        Artisan::call('ej:es:index');
    }

    /**
     * @test
     */
    public function it_can_specify_indexables_to_index()
    {
        factory(Models\Family::class, 20)->create();

        $indexer = Mockery::mock(Indexer::class)
            ->shouldReceive('queue')
            ->once()
            ->withAnyArgs()
            ->andReturn(null)
            ->getMock();

        $this->app->instance(Indexer::class, $indexer);

        Artisan::call('ej:es:index', [
            '--indexables' => Models\Family::class,
        ]);
    }

    /**
     * @test
     */
    public function it_passes_correct_queries_to_indexer()
    {
        factory(Models\Family::class, 20)->create();

        $indexer = Mockery::mock(Indexer::class)
            ->shouldReceive('queue')
            ->once()
            ->withArgs(function ($query) {
                return $query->get()->toArray() === (new Models\Family)->getIndexingQuery()->get()->toArray();
            })
            ->andReturn(null);

        $this->app->instance(Indexer::class, $indexer->getMock());

        Artisan::call('ej:es:index', [
            '--indexables' => Models\Family::class,
        ]);
    }

    /**
     * @test
     */
    public function it_has_correct_default_chunk_size()
    {
        $indexer = Mockery::mock(Indexer::class)
            ->shouldReceive('queue')
            ->withArgs(function ($query, $chunkSize) {
                return $chunkSize === 250;
            })
            ->andReturn(null);

        $this->app->instance(Indexer::class, $indexer->getMock());

        Artisan::call('ej:es:index');
    }

    /**
     * @test
     */
    public function it_can_specify_chunk_size()
    {
        $indexer = Mockery::mock(Indexer::class)
            ->shouldReceive('queue')
            ->withArgs(function ($query, $chunkSize) {
                return $chunkSize === 1983;
            })
            ->andReturn(null);

        $this->app->instance(Indexer::class, $indexer->getMock());

        Artisan::call('ej:es:index', [
            '--chunk-size' => 1983,
        ]);
    }

    /**
     * @test
     */
    public function only_one_command_runs_at_a_time()
    {
        $this->expectException(IndexingException::class);

        Cache::forever(IndexDocuments::$cacheLock, true);

        $indexer = Mockery::mock(Indexer::class)
            ->shouldReceive('queue')
            ->never();

        $this->app->instance(Indexer::class, $indexer->getMock());

        Artisan::call('ej:es:index');

        Cache::forget(IndexDocuments::$cacheLock);
    }
}
