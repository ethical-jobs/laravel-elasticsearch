<?php

namespace EthicalJobs\Elasticsearch\Hydrators;

use ArrayObject;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use EthicalJobs\Elasticsearch\Contracts\Indexable;
use EthicalJobs\Storage\Contracts\Hydrator;

/**
 * Hydrates ArrayObjects from elasticsearch results
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class ObjectHydrator implements Hydrator
{   
    /**
     * Indexable document type
     *
     * @param Indexable
     */
    protected $indexable;

    /**
     * {@inheritdoc}
     */
    public function hydrateCollection(iterable $collection) : iterable
    {
        if (empty($collection)) {
            return new Collection;
        }

        $items = $collection['hits']['hits'] ?? [];

        $results = [];

        foreach ($items as $hit) {
            $results[] = $this->hydrateRecursive($hit);
        }

        return new Collection($results);
    }

    /**
     * {@inheritdoc}
     */
    public function hydrateEntity($entity)
    {
        return new ArrayObject($entity, ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * Set indexable instance
     *
     * @param Indexable $indexable
     * @return Hydrator
     */
    public function setIndexable(Indexable $indexable): Hydrator
    {    
        $this->indexable = $indexable;

        return $this;
    }

    /**
     * Returns the indexable instance
     * 
     * @return Indexable
     */
    public function getIndexable(): Indexable
    {    
        return $this->indexable;
    }        

    /**
     * Hydrates a elastic hit
     *
     * @param array $item
     * @return ArrayObject
     */
    protected function hydrateRecursive(Array $item)
    {
        $relations = $this->indexable->getDocumentRelations();

        $hit = $item['_source'] ?? [];

        $hit['documentScore'] = $item['_score'] ?? 0;
        $hit['isDocument'] = true;

        $relationHits = [];

        foreach ($relations as $relation) {
            if (isset($hit[$relation]) && is_array($hit[$relation])) {
                if (Arr::isAssoc($hit[$relation])) { 
                    // Is a single document relation
                    $relationHits[$relation] =  $this->hydrateEntity($hit[$relation]);
                } else { 
                    // Is collection of related documents
                    $relationHits[$relation] = new Collection;
                    foreach ($hit[$relation] as $relationHit) {
                        $relatedHit = $this->hydrateEntity($relationHit);
                        $relationHits[$relation]->put($relatedHit['id'], $relatedHit);
                    }                    
                }
            }
        }

        $hit = array_merge($hit, $relationHits);

        return $this->hydrateEntity($hit);
    }
}
