<?php

namespace EthicalJobs\Elasticsearch\Testing;

use EthicalJobs\Elasticsearch\Index;

/**
 * Resets the elasticsearch index on each testcase
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

trait ResetElasticsearchIndex
{
    /**
     * Run before each testcase
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->resetTestIndex();
    }     

    /**
     * Resets the elasticsearch testing index
     *
     * @return void
     */
    public function resetTestIndex() : void
    {
        $index = resolve(Index::class);

        if ($index->exists()) {
            $index->delete();
        }   
        
        $index->create();
    }       
}