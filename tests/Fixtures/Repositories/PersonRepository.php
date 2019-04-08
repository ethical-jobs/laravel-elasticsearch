<?php

namespace Tests\Fixtures\Repositories;

use EthicalJobs\Elasticsearch\Repository;
use Tests\Fixtures\Models\Person;

/**
 * Elasticsearch person repository
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */
class PersonRepository extends Repository
{
    /**
     * Object constructor.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct(new Person);
    }
}