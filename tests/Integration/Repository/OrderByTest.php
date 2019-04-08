<?php

namespace Tests\Integration\Repositories;

use Carbon\Carbon;
use EthicalJobs\Elasticsearch\Testing\ResetElasticsearchIndex;
use Tests\Fixtures\Models;
use Tests\Fixtures\Repositories\PersonRepository;
use Tests\Helpers\Indexer;
use Tests\TestCase;

class OrderByTest extends TestCase
{
    use ResetElasticsearchIndex;

    /**
     * @test
     */
    public function it_can_order_by_a_date_field()
    {
        factory(Models\Person::class)->create([
            'first_name' => 'Donald',
            'created_at' => Carbon::now(),
        ]);

        factory(Models\Person::class)->create([
            'first_name' => 'Barak',
            'created_at' => Carbon::now()->subDays(1),
        ]);

        factory(Models\Person::class)->create([
            'first_name' => 'George',
            'created_at' => Carbon::now()->subDays(2),
        ]);

        factory(Models\Person::class)->create([
            'first_name' => 'Bill',
            'created_at' => Carbon::now()->subDays(3),
        ]);

        Indexer::all(Models\Person::class);

        $presidents = resolve(PersonRepository::class)
            ->orderBy('created_at', 'DESC')
            ->find();

        $this->assertEquals($presidents->pluck('first_name')->toArray(), [
            'Donald',
            'Barak',
            'George',
            'Bill',
        ]);

        $presidents = resolve(PersonRepository::class)
            ->orderBy('created_at', 'ASC')
            ->find();

        $this->assertEquals($presidents->pluck('first_name')->toArray(), [
            'Bill',
            'George',
            'Barak',
            'Donald',
        ]);
    }

    /**
     * @test
     */
    public function it_can_order_by_a_numeric_field()
    {
        factory(Models\Person::class)->create([
            'first_name' => 'Barak',
            'age' => 56,
        ]);

        factory(Models\Person::class)->create([
            'first_name' => 'Bill',
            'age' => 71,
        ]);

        factory(Models\Person::class)->create([
            'first_name' => 'Donald',
            'age' => 72,
        ]);

        factory(Models\Person::class)->create([
            'first_name' => 'George',
            'age' => 73,
        ]);

        Indexer::all(Models\Person::class);

        $presidents = resolve(PersonRepository::class)
            ->orderBy('age', 'ASC')
            ->find();

        $this->assertEquals($presidents->pluck('first_name')->toArray(), [
            'Barak',
            'Bill',
            'Donald',
            'George',
        ]);

        $presidents = resolve(PersonRepository::class)
            ->orderBy('age', 'DESC')
            ->find();

        $this->assertEquals($presidents->pluck('first_name')->toArray(), [
            'George',
            'Donald',
            'Bill',
            'Barak',
        ]);
    }

    /**
     * @test
     */
    public function it_orders_by_ascending_defaultly()
    {
        factory(Models\Person::class)->create([
            'first_name' => 'Barak',
            'age' => 56,
        ]);

        factory(Models\Person::class)->create([
            'first_name' => 'Bill',
            'age' => 71,
        ]);

        factory(Models\Person::class)->create([
            'first_name' => 'Donald',
            'age' => 72,
        ]);

        factory(Models\Person::class)->create([
            'first_name' => 'George',
            'age' => 73,
        ]);

        Indexer::all(Models\Person::class);

        $presidents = resolve(PersonRepository::class)
            ->orderBy('age')
            ->find();

        $this->assertEquals($presidents->pluck('first_name')->toArray(), [
            'Barak',
            'Bill',
            'Donald',
            'George',
        ]);
    }
}
