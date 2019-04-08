<?php

namespace EthicalJobs\Elasticsearch\Contracts;

use Elasticsearch\Client;

/**
 * Defines access to the elasticsearch client
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */
interface HasElasticsearch
{
    /**
     * Sets the elasticsearch client
     *
     * @param Client $client
     * @return void
     */
    public function setElasticsearchClient(Client $client): void;

    /**
     * Returns the elasticsearch client
     *
     * @return Client
     */
    public function getElasticsearchClient(): Client;
}