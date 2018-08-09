<?php

namespace Tests\Fixtures\Repositories;

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
     * Object constructor.
     *
     * @param Search $search
     * @return void
     */
    public function __construct(Search $search)
    {
        parent::__construct(new Family, $search);
    }
}