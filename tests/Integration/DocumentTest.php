<?php

namespace Tests\Integration;

use EthicalJobs\Elasticsearch\Utilities;

class DocumentTest extends \Tests\TestCase
{
    /**
     * @test
     */
    public function documents_can_return_their_keys()
    {
        foreach (Utilities::getIndexables() as $class) {
            $indexable = new $class;
            $this->assertEquals($indexable->id, $indexable->getDocumentKey());
        }
    }

    /**
     * @test
     */
    public function documents_can_return_their_body()
    {
        foreach (Utilities::getIndexables() as $class) {
            $indexable = new $class;
            $this->assertTrue(is_array($indexable->getDocumentBody()));
        }
    }

    /**
     * @test
     */
    public function documents_can_return_their_type()
    {
        foreach (Utilities::getIndexables() as $class) {
            $indexable = new $class;
            $this->assertEquals($indexable->getTable(), $indexable->getDocumentType());
        }
    }

    /**
     * @test
     */
    public function documents_can_return_their_mappings()
    {
        foreach (Utilities::getIndexables() as $class) {
            foreach ((new $class)->getDocumentMappings() as $mapping) {
                $this->assertTrue(array_has($mapping, 'type'));
            }
        }
    }

    /**
     * @test
     */
    public function documents_can_return_their_relations()
    {
        foreach (Utilities::getIndexables() as $class) {
            $indexable = new $class;
            $this->assertTrue(is_array($indexable->getDocumentRelations()));
        }
    }
}
