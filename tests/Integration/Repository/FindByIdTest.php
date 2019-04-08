<?php

namespace Tests\Integration\Repositories;

use EthicalJobs\Elasticsearch\Testing\ResetElasticsearchIndex;
use Tests\Fixtures\Models;
use Tests\Fixtures\Repositories\PersonRepository;
use Tests\Helpers\Indexer;
use Tests\TestCase;

class FindByIdTest extends TestCase
{
    use ResetElasticsearchIndex;

    /**
     * @test
     */
    public function it_can_find_by_sql_id()
    {
        $donald = factory(Models\Person::class)->create([
            'first_name' => 'Donald',
            'last_name' => 'Trump',
        ]);

        $barak = factory(Models\Person::class)->create([
            'first_name' => 'Barak',
            'last_name' => 'Obama',
        ]);

        Indexer::all(Models\Person::class);

        $shouldBeDonald = resolve(PersonRepository::class)
            ->findById($donald->id);

        $this->assertEquals(
            $shouldBeDonald->first_name,
            $donald->first_name
        );

        $shouldBeBarak = resolve(PersonRepository::class)
            ->findById($barak->id);

        $this->assertEquals(
            $shouldBeBarak->first_name,
            $barak->first_name
        );
    }
}
