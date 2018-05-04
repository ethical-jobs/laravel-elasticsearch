<?php

namespace EthicalJobs\Elasticsearch\Testing;

use Mockery;
use Tests\Fixtures\Models;
use Illuminate\Database\Eloquent\Model;
use EthicalJobs\Storage\Repositories;
use EthicalJobs\Storage\Contracts;
use EthicalJobs\SDK\ApiClient;
use Elasticsearch\Client;
use ONGR\ElasticsearchDSL\Search;
use EthicalJobs\Elasticsearch\Indexable;

/**
 * Repository static factory - builds repository instances
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au
 */

class RepositoryFactory
{
    /**
     * Elasticsearch repository factory
     *
     * @param  ...
     * @return EthicalJobs\Storage\Contracts\Repository
     */
    public static function make(Client $client = null, Indexable $indexable = null, Search $search = null): Contracts\Repository
    {
        if (is_null($indexable)) {
            $indexable = new Models\Person;
        }

        if (is_null($search)) {
            $search = new Search;
        }

        if (is_null($client)) {
            $client = Mockery::mock(Client::class)->shouldIgnoreMissing();
        }            

        return new Repositories\ElasticsearchRepository($indexable, $search, $client, 'test-index');
    }
}