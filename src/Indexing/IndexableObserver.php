<?php

namespace EthicalJobs\Elasticsearch\Indexing;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use EthicalJobs\Elasticsearch\Utilities;

/**
 * Updates elasticsearch from eloquent model events.
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class IndexableObserver
{
    /**
     * Elastic search index service
     *
     * @param Indexer
     */
    private $indexer;

    /**
     * Constructor
     *
     * @param Indexer $indexer
     * @return void
     */
    public function __construct(Indexer $indexer)
    {
        $this->indexer = $indexer;
    }

    /**
     * Listens to the created event
     *
     * @param Model $indexable
     * @return void
     */
    public function created(Model $indexable)
    {
        $this->indexDocument($indexable); 
    }

    /**
     * Listens to the updated event
     *
     * @param Model $indexable
     * @return void
     */
    public function updated(Model $indexable)
    {
        $this->indexDocument($indexable); 
    }

    /**
     * Listen to the deleting event.
     *
     * @param Model $indexable
     * @return void
     */
    public function deleted(Model $indexable)
    {
        if (Utilities::isSoftDeletable($indexable) && !$indexable->isForceDeleting()) {
            $this->indexDocument($indexable); 
        } else {
            $this->deleteDocument($indexable);   
        }
    }

    /**
     * Listen to the restored event.
     *
     * @param Model $indexable
     * @return void
     */
    public function restored(Model $indexable)
    {
        $this->indexDocument($indexable); 
    }

    /**
     * Executes index action. Swallow and log.
     *
     * @param Model $indexable
     * @return void
     */
    protected function indexDocument(Model $indexable)
    {
        try {
            $this->indexer->indexDocument($indexable); 
        } catch (\Exception $exception) {
            Log::critical('ej:es:indexing:observer '.$exception->getMessage(), $indexable->toArray());
        }
    }    

    /**
     * Executes delete action. Swallow and log.
     *
     * @param Model $indexable
     * @return void
     */
    protected function deleteDocument(Model $indexable)
    {
        try {
            $this->indexer->deleteDocument($indexable); 
        } catch (\Exception $exception) {
            Log::critical('ej:es:indexing:observer '.$exception->getMessage(), $indexable->toArray());
        }
    }        
}