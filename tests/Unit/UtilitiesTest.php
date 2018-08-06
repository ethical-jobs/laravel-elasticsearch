<?php

namespace Tests\Unit;

use Tests\Fixtures\Models;
use EthicalJobs\Elasticsearch\Utilities;

class UtilitiesTest extends \Tests\TestCase
{
    /**
     * @test
     * @group Unit
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
     * @group Unit
     */
    public function it_returns_original_operator_when_unable_to_translate()
    {
        $this->assertEquals('like', Utilities::translateOperator('like'));
    }  
    
    /**
     * @test
     * @group Unit
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
}
