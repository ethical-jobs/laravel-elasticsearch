<?php

namespace EthicalJobs\Elasticsearch\Hydrators;

use Carbon\Carbon;
use ReflectionMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use EthicalJobs\Storage\Contracts\Hydrator;
use EthicalJobs\Elasticsearch\Indexable;

/**
 * Hydrates eloquent models from elasticsearch results
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class EloquentHydrator implements Hydrator
{
    /**
     * Indexable document type
     *
     * @param App\Services\Elasticsearch\Indexable
     */
    protected $indexable;

    /**
     * {@inheritdoc}
     */
    public function hydrateCollection(iterable $collection): iterable
    {
        $indexables = [];

        $hits = $collection['hits']['hits'] ?? null;

        if (! $hits || count($hits) < 1) {
            return $this->toCollection([]);
        }

        foreach ($hits as $hit) {
            $indexables[] = $this->hydrateEntityRecursive($hit);
        }

        return $this->toCollection($indexables);
    }

    /**
     * {@inheritdoc}
     */
    public function hydrateEntity($entity)
    {
        $instance = $this->indexable->newInstance([], true);

        $instance->isDocument = true;

        $instance->documentScore = (int) $entity['_score'];        

        $instance->setRawAttributes((array) $entity['_source'], true);

        $instance = $this->parseDateFields($instance);

        return $instance;
    }

    /**
     * Set indexable instance
     *
     * @param EthicalJobs\Elasticsearch\Indexable $indexable
     * @return EthicalJobs\Storage\Contracts\Hydrator
     */
    public function setIndexable(Indexable $indexable): Hydrator
    {    
        $this->indexable = $indexable;

        return $this;
    }

    /**
     * Returns the indexable instance
     * 
     * @return EthicalJobs\Elasticsearch\Indexable
     */
    public function getIndexable(): Indexable
    {    
        return $this->indexable;
    }    

    /**
     * Hydrates an eloquent model from an array of attributes
     *
     * @param array $entity
     * @return Illuminate\Database\Eloquent\Model
     */
    protected function hydrateEntityRecursive(array $hit): Model
    {
        $instance = $this->hydrateEntity($hit);

        $instance = $this->hydrateRelations($instance);

        return $instance;
    }

    /**
     * Parses date attributes
     *
     * @param \Illuminate\Database\Eloquent\Model $entity
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function parseDateFields(Model $entity): Model
    {
        foreach ($entity->getDates() as $dateAttribute) {
            if ($date = $entity->getOriginal($dateAttribute)) {
                $entity->setAttribute($dateAttribute, Carbon::parse($date));
            }
        }

        return $entity;
    }

    /**
     * Hydrates the relations of an indexable
     *
     * @param \Illuminate\Database\Eloquent\Model $entity
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function hydrateRelations(Model $entity): Model
    {

        $attributes = $entity->getAttributes();

        foreach ($attributes as $attribute => $value) {

            if ($value) {

                if (! in_array($attribute, $entity->getDocumentRelations())) {
                    continue;
                }

                $reflection_method = new ReflectionMethod($entity, $attribute);

                if ($reflection_method->class != Model::class) {

                    $relation = $entity->$attribute();

                    if ($relation instanceof Relation) {

                        // Check if the document array is supposed to be single or collection
                        if (is_null($value) === true || count($value) == count($value, COUNT_RECURSIVE)) {
                            $value = [$value];
                        }

                        $models = [];

                        foreach ($value as $relationValue) {
                            if ($relationValue) {
                                $models[] = $this->hydrateEntity($relation->getModel(), $relationValue);
                            }
                        }

                        $models = $this->toCollection($models);

                        // Unset attribute before match relation
                        unset($entity[$attribute]);
                        $relation->match([$entity], $models, $attribute);
                    }
                }
            }
        }

        return $entity;
    }

    /**
     * Converts array to collection
     *
     * @param array $entities
     * @return Illuminate\Database\Eloquent\Collection
     */
    protected function toCollection(array $entities)
    {
        return Collection::make($entities);
    }
}
