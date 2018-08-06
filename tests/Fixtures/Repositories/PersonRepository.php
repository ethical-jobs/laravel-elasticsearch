<?php

namespace Tests\Fixtures\Repositories;

use Elasticsearch\Client;
use ONGR\ElasticsearchDSL\Search;
use Tests\Fixtures\Models\Person;

/**
 * Elasticsearch person repository
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class PersonRepository extends \EthicalJobs\Elasticsearch\Repository
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
        parent::__construct(new Person, $search, $client, config('elasticsearch.index'));
    }
}