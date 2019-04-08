<?php

namespace Tests\Integration\Repositories;

use EthicalJobs\Elasticsearch\Testing\ResetElasticsearchIndex;
use Tests\Fixtures\Models;
use Tests\Fixtures\Repositories\PersonRepository;
use Tests\Helpers\Indexer;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use ResetElasticsearchIndex;

    /**
     * @test
     */
    public function it_can_filter_by_keyword()
    {
        factory(Models\Person::class)->create([
            'first_name' => 'Donald',
            'last_name' => 'Trump',
        ]);

        factory(Models\Person::class)->create([
            'first_name' => 'Donald',
            'last_name' => 'Ivanka',
        ]);

        factory(Models\Person::class)->create([
            'first_name' => 'Barak',
            'last_name' => 'Obama',
        ]);

        Indexer::all(Models\Person::class);

        $people = resolve(PersonRepository::class)
            ->search('Barak')
            ->find();

        $this->assertEquals(1, $people->count());

        foreach ($people as $person) {
            $this->assertTrue(str_contains($person->full_name, 'Barak'));
        }
    }
}
