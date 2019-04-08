<?php

namespace Tests\Unit;

use EthicalJobs\Elasticsearch\Utilities;
use Tests\Fixtures\Models;
use Tests\TestCase;

class UtilitiesTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_translate_operators()
    {
        $this->assertEquals('gte', Utilities::translateOperator('>='));
        $this->assertEquals('lte', Utilities::translateOperator('<='));
        $this->assertEquals('gt', Utilities::translateOperator('>'));
        $this->assertEquals('lt', Utilities::translateOperator('<'));
    }

    /**
     * @test
     */
    public function it_returns_original_operator_when_unable_to_translate()
    {
        $this->assertEquals('like', Utilities::translateOperator('like'));
    }

    /**
     * @test
     */
    public function it_can_determine_if_a_model_is_soft_deletable()
    {
        $this->assertTrue(
            Utilities::isSoftDeletable(Models\Person::class)
        );

        $this->assertFalse(
            Utilities::isSoftDeletable(Models\Family::class)
        );
    }

    /**
     * @test
     */
    public function it_can_determine_if_a_model_is_indexable()
    {
        $this->assertTrue(
            Utilities::isIndexable(new Models\Person)
        );
    }

    /**
     * @test
     */
    public function it_can_return_indexables()
    {
        $this->assertEquals(Utilities::getIndexables(), [
            Models\Person::class,
            Models\Family::class,
            Models\Vehicle::class,
        ]);
    }

    /**
     * @test
     */
    public function it_can_return_config_values()
    {
        $this->assertEquals(Utilities::config('index'), 'test-index');

        $this->assertEquals(Utilities::config('ninjas.are.cool', ['default', 'foo']), ['default', 'foo']);

        $this->assertEquals(Utilities::config(), config('elasticsearch'));
    }
}
