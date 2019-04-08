<?php

namespace Tests\Fixtures\Repositories;

use EthicalJobs\Elasticsearch\Repository;
use Tests\Fixtures\Models\Family;

/**
 * Elasticsearch family repository
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */
class FamilyRepository extends Repository
{
    /**
     * Object constructor.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct(new Family);
    }
}