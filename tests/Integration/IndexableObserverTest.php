<?php

namespace Tests\Integration;

use EthicalJobs\Elasticsearch\Indexing\Indexer;
use EthicalJobs\Elasticsearch\Testing\IndexableObservers;
use EthicalJobs\Elasticsearch\Utilities;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\Fixtures\Models;
use Tests\TestCase;

class IndexableObserverTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        IndexableObservers::withObservers();
    }

    /**
     * @test
     * @group Integration
     */
    public function it_indexes_created_indexables()
    {
        $indexer = Mockery::mock(Indexer::class)
            ->shouldReceive('indexDocument')
            ->once()
            ->withArgs(function ($person) {
                $this->assertEquals('Andrew', $person->first_name);
                $this->assertEquals('McLagan', $person->last_name);

                return true;
            })
            ->getMock();

        App::instance(Indexer::class, $indexer);

        factory(Models\Person::class)->create([
            'first_name' => 'Andrew',
            'last_name' => 'McLagan',
        ]);
    }

    /**
     * @test
     * @group Integration
     */
    public function it_indexes_updated_indexables()
    {
        $indexer = Mockery::mock(Indexer::class)
            ->shouldReceive('indexDocument')
            ->once()
            ->withAnyArgs()
            ->andReturn([])
            ->shouldReceive('indexDocument')
            ->once()
            ->withArgs(function ($person) {
                $this->assertEquals('Werdna', $person->first_name);
                $this->assertEquals('NagaLcM', $person->last_name);

                return true;
            })
            ->andReturn([])
            ->getMock();

        App::instance(Indexer::class, $indexer);

        factory(Models\Person::class)
            ->create([
                'first_name' => 'Andrew',
                'last_name' => 'McLagan',
            ])
            ->update([
                'first_name' => 'Werdna',
                'last_name' => 'NagaLcM',
            ]);
    }

    /**
     * @test
     * @group Integration
     */
    public function it_indexes_soft_deleted_indexables()
    {
        $indexer = Mockery::mock(Indexer::class)
            ->shouldReceive('indexDocument')
            ->once()
            ->withAnyArgs()
            ->andReturn([])
            ->shouldReceive('indexDocument')
            ->once()
            ->withArgs(function ($person) {
                $this->assertFalse(is_null($person->deleted_at));

                return true;
            })
            ->andReturn([])
            ->getMock();

        App::instance(Indexer::class, $indexer);

        $person = factory(Models\Person::class)->create([
            'first_name' => 'Andrew',
            'last_name' => 'McLagan',
        ]);

        $person->delete();
    }

    /**
     * @test
     * @group Integration
     */
    public function it_deletes_non_soft_deleted_indexables()
    {
        $indexer = Mockery::mock(Indexer::class)
            ->shouldReceive('indexDocument')
            ->once()
            ->withAnyArgs()
            ->andReturn([])
            ->shouldReceive('deleteDocument')
            ->once()
            ->withArgs(function ($family) {
                $this->assertEquals('McLagan', $family->surname);

                return true;
            })
            ->andReturn([])
            ->getMock();

        App::instance(Indexer::class, $indexer);

        $family = factory(Models\Family::class)->create([
            'surname' => 'McLagan',
        ]);

        $family->delete();
    }

    /**
     * @test
     * @group Integration
     */
    public function it_deletes_soft_deleteables_when_force_deleting()
    {
        $indexer = Mockery::mock(Indexer::class)
            ->shouldReceive('indexDocument')
            ->once()
            ->withAnyArgs()
            ->andReturn([])
            ->shouldReceive('deleteDocument')
            ->once()
            ->withArgs(function ($family) {
                $this->assertEquals('Andrew', $family->first_name);

                return true;
            })
            ->andReturn([])
            ->getMock();

        App::instance(Indexer::class, $indexer);

        $person = factory(Models\Person::class)->create([
            'first_name' => 'Andrew',
        ]);

        $this->assertTrue(Utilities::isSoftDeletable($person));

        $person->forceDelete();
    }

    /**
     * @test
     * @group Integration
     */
    public function it_indexes_restored_indexables()
    {
        $indexer = Mockery::mock(Indexer::class)
            ->shouldReceive('indexDocument')
            ->times(3)
            ->withArgs(function ($person) {
                $this->assertEquals('Andrew', $person->first_name);
                $this->assertEquals('McLagan', $person->last_name);

                return true;
            })
            ->andReturn([])
            ->getMock();

        App::instance(Indexer::class, $indexer);

        $person = factory(Models\Person::class)->create([
            'first_name' => 'Andrew',
            'last_name' => 'McLagan',
        ]);

        $person->delete();

        $person->restore();
    }

    /**
     * @test
     * @group Integration
     */
    public function it_swallows_exceptions_and_logs_them()
    {
        Log::shouldReceive('critical')
            ->times(4)
            ->withAnyArgs()
            ->andReturn(null);

        $indexer = Mockery::mock(Indexer::class)
            ->shouldReceive('indexDocument')
            ->times(4)
            ->withAnyArgs()
            ->andThrow(\Exception::class)
            ->getMock();

        App::instance(Indexer::class, $indexer);

        $person = factory(Models\Person::class)->create();

        $person->update([
            'first_name' => 'Werdna',
            'last_name' => 'NagaLcM',
        ]);

        $person->delete();

        $person->restore();
    }
}
