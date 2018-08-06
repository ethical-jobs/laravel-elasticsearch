<?php

namespace Tests\Integration\Console;

use Mockery;
use Carbon\Carbon;
use Elasticsearch\Client;
use Illuminate\Support\Facades\Artisan;
use EthicalJobs\Elasticsearch\Indexing\Indexer;
use EthicalJobs\Elasticsearch\Indexing\IndexQuery;
use EthicalJobs\Elasticsearch\Index;
use Tests\Fixtures\Models;

class IndexDocumentsCommandTest extends \Tests\TestCase
{
    /**
     * @test
     * @group Integration
     */
    public function it_chunks_documents_correctly()
    {
        factory(Models\Family::class, 50)->create();
        factory(Models\Person::class, 50)->create();
        factory(Models\Vehicle::class, 50)->create();

        $client = Mockery::mock(Client::class);

        $this->app->instance(Client::class, $client);

        $indexer = Mockery::mock(Indexer::class)
            ->shouldReceive('queue')
            ->times(3)
            ->withArgs(function ($query, $chunkSize) {
                return $chunkSize === 25;
            })
            ->andReturn(null)
            ->getMock();

        $this->app->instance(Indexer::class, $indexer);

        Artisan::call('ej:es:index', [
            '--chunk-size' => 25,   
        ]);
    }  

    /**
     * @test
     * @group Integration
     */
    public function it_can_specify_indexables_to_index()
    {
        factory(Models\Family::class, 20)->create();

        $indexer = Mockery::mock(Indexer::class)
            ->shouldReceive('queue')
            ->once()
            ->withArgs(function ($query) {
                $this->assertInstanceOf(Models\Family::class, $query->getModel());
                return true;
            })
            ->andReturn(null)
            ->getMock();         

        $this->app->instance(Indexer::class, $indexer);

        Artisan::call('ej:es:index', [          
            '--indexables'   => Models\Family::class,
        ]);
    }    
    
    /**
     * @test
     * @group Integration
     */
    public function it_includes_trashed_items()
    {
        factory(Models\Person::class, 50)->create([
            'deleted_at' => null,
        ]);

        factory(Models\Person::class, 50)->create([
            'deleted_at' => Carbon::now(),
        ]);        

        $indexer = Mockery::mock(Indexer::class)
            ->shouldReceive('queue')
            ->once()
            ->withArgs(function ($query, $chunkSize) {
                return $query->count() === 100;
            })
            ->andReturn(null)
            ->getMock();

        $this->app->instance(Indexer::class, $indexer);

        Artisan::call('ej:es:index', [
            '--chunk-size' => 100,   
            '--indexables' => Models\Person::class,
        ]);
    }     
}
