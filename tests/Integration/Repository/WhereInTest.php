<?php

namespace Tests\Integration\Repositories;

use EthicalJobs\Elasticsearch\Testing\ResetElasticsearchIndex;
use Tests\Fixtures\Models;
use Tests\Fixtures\Repositories\PersonRepository;
use Tests\Helpers\Indexer;
use Tests\TestCase;

class WhereInTest extends TestCase
{
    use ResetElasticsearchIndex;

    /**
     * @test
     */
    public function it_can_find_by_a_whereIn_terms_query()
    {
        factory(Models\Person::class)->create(['age' => 61]);
        factory(Models\Person::class)->create(['age' => 62]);
        factory(Models\Person::class)->create(['age' => 63]);
        factory(Models\Person::class)->create(['age' => 64]);
        factory(Models\Person::class)->create(['age' => 65]);

        Indexer::all(Models\Person::class);

        $people = resolve(PersonRepository::class)
            ->whereIn('age', [61, 63, 65])
            ->find();

        $this->assertEquals(3, $people->count());

        foreach ($people as $person) {
            $this->assertTrue(in_array($person->age, [61, 63, 65]));
        }
    }

    /**
     * @test
     */
    public function it_can_find_by_multiple_whereIn_filters()
    {
        factory(Models\Person::class)->create(['age' => 61, 'sex' => 'male']);
        factory(Models\Person::class)->create(['age' => 65, 'sex' => 'female']);
        factory(Models\Person::class)->create(['age' => 62, 'sex' => 'trans']);
        factory(Models\Person::class)->create(['age' => 63, 'sex' => 'female']);
        factory(Models\Person::class)->create(['age' => 65, 'sex' => 'male']);

        Indexer::all(Models\Person::class);

        $people = resolve(PersonRepository::class)
            ->whereIn('age', [61, 62])
            ->whereIn('sex', ['male', 'trans'])
            ->find();

        $this->assertEquals(2, $people->count());

        foreach ($people as $person) {
            $this->assertTrue(in_array($person->age, [61, 62]));
            $this->assertTrue(in_array($person->sex, ['male', 'trans']));
        }
    }
}
