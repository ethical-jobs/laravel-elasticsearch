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
     * @return bool
     */
    public function create() : bool
    {
        if ($this->exists()) {
            throw new \Exception('Index already exists.');
        }

        $response = $this->getElasticsearchClient()->indices()->create([
            'index' => static::getIndexName(),
            'body'  => [
                'settings'  => Utilities::config('settings', []),
                'mappings'  => $this->getIndexMappings(),
            ],
        ]);

        return isset($response['acknowledged']) ? $response['acknowledged'] === true : false;
    }

    /**
     * Delete the index
     *
     * @return bool
     */
    public function delete() : bool
    {
        $response = $this->getElasticsearchClient()->indices()->delete([
            'index' => static::getIndexName(),
        ]);

        return isset($response['acknowledged']) ? $response['acknowledged'] === true : false;
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