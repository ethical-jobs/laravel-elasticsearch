<?php

namespace Tests\Fixtures;

use Mockery;
use EthicalJobs\Elasticsearch\Indexable;
use EthicalJobs\Elasticsearch\Repository;
use ONGR\ElasticsearchDSL\Search;
use Elasticsearch\Client;

/**
 * Repository static factory
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class RepositoryFactory
{
	/**
	 * Make a repository instance
	 * 
	 * @return EthicalJobs\Elasticsearch\Repository
	 */
	public static function make(Client $client = null, Indexable $indexable = null): Repository
	{
		if (is_null($indexable)) {
			$indexable = new Models\Person;
		}

		if (is_null($client)) {
			$client = Mockery::mock(Client::class)
				->shouldIgnoreMissing();
		}
       
		return new Repository($indexable, new Search, $client);
	}
}