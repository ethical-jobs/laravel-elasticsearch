<?php

namespace Tests\Fixtures\Repositories;

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
     * Object constructor.
     *
     * @param Search $search
     * @return void
     */
    public function __construct(Search $search)
    {
        parent::__construct(new Person, $search);
    }
}