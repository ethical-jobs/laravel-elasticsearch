<?php

namespace EthicalJobs\Elasticsearch\Commands;

use Illuminate\Support\Facades\Cache;
use EthicalJobs\Elasticsearch\Exceptions\IndexingException;
use EthicalJobs\Elasticsearch\Indexing\Indexer;
use EthicalJobs\Elasticsearch\Utilities;

/**
 * Indexes indexable entities in Elasticsearch
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class IndexDocuments extends \Illuminate\Console\Command
{
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
     * Cache lock key
     *
     * @var string
     */
    public static $cacheLock = 'ej:es:indexing';

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
     */
    public function handle()
    {
        foreach ($this->getIndexables() as $indexable) {
            $this->index($indexable);
        }
    }

    /**
     * Indexes an indexable resource
     *
     * @param  string $indexable
     * @return void
     */
    protected function index(string $indexable) : void
    {
        if (Cache::get(static::$cacheLock)) {
            throw new IndexingException('Indexing operation already running.');
        }

        Cache::forever(static::$cacheLock, true); 

        try {
            $this->queueIndexable($indexable);
        } catch (\Exception $exception) {
            Cache::forget(static::$cacheLock);
            throw $exception;
        }

        Cache::forget(static::$cacheLock);
    }       

    /**
     * Returns indexable entities
     *
     * @return array
     */
    protected function getIndexables() : array
    {
        if ($option = $this->option('indexables')) {
            return is_array($option) ? $option : [$option];
        }
        
        return Utilities::getIndexables();       
    }    

    /**
     * Initiiates indexing of an indexable type
     *
     * @param  string $indexable
     * @return void
     */
    protected function queueIndexable(string $indexable) : void
    {
        $query = (new $indexable)->getIndexingQuery();

        $this->indexer->queue($query, $this->option('chunk-size'));             
    }        
}
