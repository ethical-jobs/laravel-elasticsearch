<?php

namespace EthicalJobs\Elasticsearch;

use EthicalJobs\Elasticsearch\Contracts\HasElasticsearch;
use EthicalJobs\Elasticsearch\Utilities;

/**
 * Elasticsearch index manager
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class IndexManager implements HasElasticsearch
{
    use ElasticsearchClient;

    /**
     * Returns the index name
     *
     * @return string
     */
    public static function getIndexName() : string
    {
        return Utilities::config('index');
    }

    /**
     * Returns the mappings for the index
     *
     * @return array
     */
    public function getIndexMappings() : array
    {
        $mappings = [];

        foreach (Utilities::getIndexables() as $class) {

            $indexable = new $class;

            $typeName = $indexable->getDocumentType();

            foreach ($indexable->getDocumentMappings() as $attribute => $map) {

                if ($indexable->isIndexableRelation($attribute)) {
                    $map['properties'] = $indexable
                        ->getDocumentRelation($attribute)
                        ->getDocumentMappings();
                }

                $mappings[$typeName]['properties'][$attribute] = $map;
            }
        }

        return array_merge(Utilities::config('mappings'), $mappings);
    }

    /**
     * Create the index
     *
     * @return int
     */
    public function create() : int
    {
        if ($this->exists()) {
            throw new \Exception('Index already exists.');
        }

        return $this->getElasticsearchClient()->indices()->create([
            'index' => static::getIndexName(),
            'body'  => [
                'settings'  => Utilities::config('settings', []),
                'mappings'  => $this->getIndexMappings(),
            ],
        ]);
    }

    /**
     * Delete the index
     *
     * @return int
     */
    public function delete() : int
    {
        return $this->getElasticsearchClient()->indices()->delete([
            'index' => static::getIndexName(),
        ]);
    }

    /**
     * Determine if the index exists
     *
     * @return bool
     */
    public function exists() : bool
    {
        return $this->getElasticsearchClient()->indices()->exists([
            'index' => static::getIndexName(),
        ]);
    }
}