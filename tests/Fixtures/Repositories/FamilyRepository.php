<?php

namespace Tests\Fixtures\Repositories;

use Elasticsearch\Client;
use ONGR\ElasticsearchDSL\Search;
use Tests\Fixtures\Models\Family;

/**
 * Elasticsearch family repository
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class FamilyRepository extends \EthicalJobs\Elasticsearch\Repository
{

    /**
     * Object constructor
     *
     * @param \ONGR\ElasticsearchDSL\Search $search
     * @param \Elasticsearch\Client $client
     * @return void
     */
    public function __construct(Search $search, Client $client)
    {
        parent::__construct(new Family, $search, $client, config('elasticsearch.index'));
    }
}