<?php

namespace Tests\Integration;

use Elasticsearch\Client;
use EthicalJobs\Elasticsearch\IndexManager;
use EthicalJobs\Elasticsearch\Utilities;
use Mockery;
use Tests\TestCase;

class IndexManagerTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_the_index_name()
    {
        $this->assertEquals(config('elasticsearch.index'), IndexManager::getIndexName());
    }

    /**
     * @test
     */
    public function it_returns_the_index_mappings()
    {
        $indexManager = resolve(IndexManager::class);

        $indexManagerMappings = $indexManager->getIndexMappings();

        foreach (Utilities::getIndexables() as $class) {

            $indexManagerable = new $class;

            $indexManagerableMapKeys = array_keys($indexManagerable->getDocumentMappings());

            $expected = $indexManagerMappings[$indexManagerable->getDocumentType()]['properties'];

            $this->assertTrue(array_has($expected, $indexManagerableMapKeys));
        }
    }

    /**
     * @test
     */
    public function it_can_create_the_index()
    {
        $indexManager = resolve(IndexManager::class);

        $client = Mockery::mock(Client::class)
            ->shouldReceive('indices')
            ->withNoArgs()
            ->andReturn(Mockery::self())
            ->shouldReceive('exists')
            ->with([
                'index' => IndexManager::getIndexName(),
            ])
            ->andReturn(false)
            ->shouldReceive('indices')
            ->withNoArgs()
            ->andReturn(Mockery::self())
            ->shouldReceive('create')
            ->with([
                'index' => 'test-index',
                'body' => [
                    'settings' => Utilities::config('settings'),
                    'mappings' => $indexManager->getIndexMappings(),
                ],
            ])
            ->andReturn(['acknowledged' => true])
            ->getMock();

        $indexManager->setElasticsearchClient($client);

        $result = $indexManager->create();

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function it_returns_false_when_failing_to_create_the_index()
    {
        $client = Mockery::mock(Client::class)
            ->shouldReceive('indices')
            ->withAnyArgs()
            ->andReturn(Mockery::self())
            ->shouldReceive('exists')
            ->withAnyArgs()
            ->andReturn(false)
            ->shouldReceive('indices')
            ->withAnyArgs()
            ->andReturn(Mockery::self())
            ->shouldReceive('create')
            ->withAnyArgs()
            ->andReturn(['acknowledged' => false])
            ->getMock();

        $indexManager = resolve(IndexManager::class);

        $indexManager->setElasticsearchClient($client);

        $result = $indexManager->create();

        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_creating_an_already_existing_index()
    {
        $this->expectException(\Exception::class);

        $client = Mockery::mock(Client::class)
            ->shouldReceive('indices')
            ->withNoArgs()
            ->andReturn(Mockery::self())
            ->shouldReceive('exists')
            ->with([
                'index' => IndexManager::getIndexName(),
            ])
            ->andReturn(true)
            ->getMock();

        $indexManager = resolve(IndexManager::class);

        $indexManager->setElasticsearchClient($client);

        $indexManager->create();
    }

    /**
     * @test
     */
    public function it_can_delete_the_index()
    {
        $client = Mockery::mock(Client::class)
            ->shouldReceive('indices')
            ->withNoArgs()
            ->andReturn(Mockery::self())
            ->shouldReceive('delete')
            ->with(['index' => IndexManager::getIndexName()])
            ->andReturn(['acknowledged' => true])
            ->getMock();

        $indexManager = resolve(IndexManager::class);

        $indexManager->setElasticsearchClient($client);

        $result = $indexManager->delete();

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function it_returns_false_when_failing_to_delete_the_index()
    {
        $client = Mockery::mock(Client::class)
            ->shouldReceive('indices')
            ->withNoArgs()
            ->andReturn(Mockery::self())
            ->shouldReceive('delete')
            ->with(['index' => IndexManager::getIndexName()])
            ->andReturn(['acknowledged' => false])
            ->getMock();

        $indexManager = resolve(IndexManager::class);

        $indexManager->setElasticsearchClient($client);

        $result = $indexManager->delete();

        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_deleting_a_non_existing_index()
    {
        $this->expectException(\Exception::class);

        $client = Mockery::mock(Client::class)
            ->shouldReceive('indices')
            ->withNoArgs()
            ->andReturn(Mockery::self())
            ->shouldReceive('exists')
            ->with([
                'index' => IndexManager::getIndexName(),
            ])
            ->andReturn(false)
            ->getMock();

        $indexManager = resolve(IndexManager::class);

        $indexManager->setElasticsearchClient($client);

        $indexManager->delete();
    }

    /**
     * @test
     */
    public function it_can_check_if_the_index_exits()
    {
        $client = Mockery::mock(Client::class)
            ->shouldReceive('indices')
            ->withNoArgs()
            ->andReturn(Mockery::self())
            ->shouldReceive('exists')
            ->with([
                'index' => IndexManager::getIndexName(),
            ])
            ->andReturn(true)
            ->getMock();

        $indexManager = resolve(IndexManager::class);

        $indexManager->setElasticsearchClient($client);

        $result = $indexManager->exists();

        $this->assertTrue($result);
    }
}
