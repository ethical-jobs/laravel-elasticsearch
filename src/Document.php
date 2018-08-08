<?php

namespace EthicalJobs\Elasticsearch;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Builder;

/**
 * Indexable trait for eloquent models
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

trait Document
{
    /**
     * Is indexed in elastic search.
     *
     * @var bool
     */
    public $isDocument = false;

    /**
     * Hit score after querying Elasticsearch.
     *
     * @var null|int
     */
    public $documentScore = null;

    /**
     * {@inheritdoc}
     */
    public function getDocumentKey()
    {
        return $this->getKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentType() : string
    {
        return $this->getTable();
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentBody() : array
    {
        return $this->attributesToArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentMappings() : array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentRelations() : array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentRelation($relation)
    {
        if ($this->isIndexableRelation($relation)) {

            $relation = $this->$relation()->getModel();

            return new $relation;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexingQuery() : Builder
    {
        $query = $this
            ->with($this->getDocumentRelations())
            ->latest();

        if (Utilities::isSoftDeletable(__CLASS__)) {
            $query->withTrashed();
        }

        return $query;    
    }           

    /**
     * Is attribute an indexable relation
     *
     * @param string $attribute
     * @return bool
     */
    public function isIndexableRelation($attribute) : bool
    {
        if (! in_array($attribute, $this->getDocumentRelations())) {
            return false;
        }

        if (! method_exists($this, $attribute)) {
            return false;
        }

        if (! Utilities::isIndexable($this->$attribute()->getModel())) {
            return false;
        }

        return true;
    }

    /**
     * Builds a tree of the document and its relations
     *
     * @return array
     */
    public function getDocumentTree() : array
    {
        $body = $this->getDocumentBody();

        // Convert date params
        foreach ($this->getDates() as $dateField) {
            if (isset($this->$dateField)) {
                $body[$dateField] = $this->$dateField->toIso8601String();
            }
        }

        // Fill relations
        foreach ($this->getDocumentRelations() as $relationKey) {
            if ($this->$relationKey !== null) {
                if ($this->isIndexableRelation($relationKey)) {
                    if ($this->$relationKey instanceof Collection) {
                        $body[$relationKey] = $this->$relationKey->map(function ($indexable) {
                            return $indexable->getDocumentBody();
                        })->toArray();
                    } else if ($this->$relationKey instanceof Indexable) {
                        $body[$relationKey] = $this->$relationKey->getDocumentBody();
                    }
                } else if ($this->relationLoaded($relationKey)) {
                    if (method_exists($this->$relationKey, 'attributesToArray')) {
                        $body[$relationKey] = $this->$relationKey->attributesToArray();
                    } else if (method_exists($this->$relationKey, 'toArray')) {
                        $body[$relationKey] = $this->$relationKey->toArray();
                    }
                }
            }
        }

        return $body;
    } 
}
