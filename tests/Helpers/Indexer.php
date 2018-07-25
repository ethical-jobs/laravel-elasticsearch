<?php

namespace Tests\Helpers;

use EthicalJobs\Elasticsearch\Indexing\Indexer as DocumentIndexer;
use EthicalJobs\Elasticsearch\Indexing\IndexQuery;
use EthicalJobs\Elasticsearch\Indexable;

/**
 * Document indexer helper
 * 
 * @auth Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class Indexer
{
    /**
     * Index a single document
     *
     * @param Indexable $indexable
     * @return void
     */
    public static function single(Indexable $indexable) : void
    {
        $indexer = resolve(DocumentIndexer::class);

        $indexer->synchronous();

        $indexer->indexDocument($indexable);
    }

    /**
     * Index collection of documents
     *
     * @param string $indexable
     * @return void
     */
    public static function all(string $indexable) : void
    {
        $indexer = resolve(DocumentIndexer::class);

        $indexer->synchronous();

        $indexer->indexQuery(new IndexQuery(new $indexable, 50000));
    }    
}