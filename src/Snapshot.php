<?php

namespace EthicalJobs\Elasticsearch;

use Elasticsearch\Client;

/**
 * Snapshot service
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class Snapshot
{
    /**
     * Elasticsearch client
     * 
     * @var Elasticsearch\Client
     */
    protected $client;

    /**
     * Object constructor
     *
     * @param \Elasticsearch\Client $client
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}