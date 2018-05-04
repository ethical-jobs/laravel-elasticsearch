<?php

namespace EthicalJobs\Elasticsearch\Testing;

use Mockery;
use EthicalJobs\Elasticsearch\IndexableObserver;
use EthicalJobs\Elasticsearch\Indexing\Indexer;

/**
 * Mocks the elasticsearch client
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

trait IndexableObservers
{
    /**
     * Disables ES indexable observer for testing purposes
     *
     * @return void
     */
    public static function withoutObservers(): void
    {
        app()->bind(IndexableObserver::class, function(){
            return Mockery::mock(IndexableObserver::class)->shouldIgnoreMissing();
        });  
    }     

    /**
     * Enable ES indexable observer for testing purposes
     *
     * @return void
     */
    public static function withObservers(): void
    {
        app()->bind(IndexableObserver::class, function(){
            return new IndexableObserver(resolve(Indexer::class));
        });  
    }       
}