<?php

namespace EthicalJobs\Elasticsearch\Commands;

use EthicalJobs\Elasticsearch\Exceptions\IndexingException;
use EthicalJobs\Elasticsearch\Indexing\Indexer;
use EthicalJobs\Elasticsearch\Utilities;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

/**
 * Indexes indexable entities in Elasticsearch
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */
class IndexDocuments extends Command
{
    /**
     * Cache lock key
     *
     * @var string
     */
    public static $cacheLock = 'ej:es:indexing';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ej:es:index
                            {--chunk-size=250 : How many documents to index at once}
                            {--indexables=* : An array of indexables to index (none == all)}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Indexes indexables into Elasticsearch';
    /**
     * Elastic search index service
     *
     * @param Indexer
     */
    private $indexer;

    /**
     * Object constructor.
     *
     * @param Indexer $indexer
     * @return void
     */
    public function __construct(Indexer $indexer)
    {
        parent::__construct();

        $this->indexer = $indexer;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws IndexingException
     */
    public function handle()
    {
        foreach ($this->getIndexables() as $indexable) {
            $this->index($indexable);
        }

        return 0;
    }

    /**
     * Returns indexable entities
     *
     * @return array
     */
    protected function getIndexables(): array
    {
        if ($option = $this->option('indexables')) {
            return is_array($option) ? $option : [$option];
        }

        return Utilities::getIndexables();
    }

    /**
     * Indexes an indexable resource
     *
     * @param string $indexable
     * @return void
     * @throws IndexingException
     */
    protected function index(string $indexable): void
    {
        if (Cache::get(static::$cacheLock)) {
            throw new IndexingException('Indexing operation already running.');
        }

        Cache::forever(static::$cacheLock, true);

        try {
            $this->queueIndexable($indexable);
        } catch (Exception $exception) {
            Cache::forget(static::$cacheLock);
            throw $exception;
        }

        Cache::forget(static::$cacheLock);
    }

    /**
     * Initiates indexing of an indexable type
     *
     * @param string $indexable
     * @return void
     */
    protected function queueIndexable(string $indexable): void
    {
        $query = (new $indexable)->getIndexingQuery();

        $this->indexer->queue($query, $this->option('chunk-size'));
    }
}
