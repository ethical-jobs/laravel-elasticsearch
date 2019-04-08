<?php

namespace EthicalJobs\Elasticsearch\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * Indexable within elasticsearch interface
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */
interface Indexable
{
    /**
     * Return the document id
     *
     * @return String
     */
    public function getDocumentKey();

    /**
     * Return the document type
     *
     * @return String
     */
    public function getDocumentType();

    /**
     * Return the documents field data
     *
     * @return array
     */
    public function getDocumentBody();

    /**
     * Return the documents field map
     *
     * @return array
     */
    public function getDocumentMappings();

    /**
     * Return the documents relations
     *
     * @return array
     */
    public function getDocumentRelations();

    /**
     * Returns indexing query
     *
     * @return Builder
     */
    public function getIndexingQuery(): Builder;
}
