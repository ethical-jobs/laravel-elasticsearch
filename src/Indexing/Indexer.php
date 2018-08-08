<?php

namespace EthicalJobs\Elasticsearch\Indexing;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use EthicalJobs\Elasticsearch\Contracts\HasElasticsearch;
use EthicalJobs\Elasticsearch\ElasticsearchClient;
use EthicalJobs\Elasticsearch\Indexable;

/**
 * Indexes documents in elasticsearch
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class Indexer implements HasElasticsearch
{
    use ElasticsearchClient;

    /**
     * Wait for documents to be indexed and available
     *
     * @param bool
     */
    private $synchronous = false;     

    /**
     * Enables "blocking" synchronous document indexing
     *
     * @return void
     */
    public function synchronous() : void
    {
        $this->synchronous = true;
    }

    /**
     * Indexes a indexable instance
     *
     * @param Indexable $indexable
     * @return array
     */
    public function indexDocument(Indexable $indexable): array
    {
        return $this->getElasticsearchClient()->index([
            'index'     => $this->indexName,
            'id'        => $indexable->getDocumentKey(),
            'type'      => $indexable->getDocumentType(),
            'refresh'   => $this->synchronous ? 'wait_for' : false,
            'body'      => $indexable->getDocumentTree(),
        ]);
    }

    /**
     * Deletes a indexable instance
     *
     * @param \EthicalJobs\Elasticsearch\Indexable $indexable
     * @return array
     */
    public function deleteDocument(Indexable $indexable): array
    {
        return $this->getElasticsearchClient()->delete([
            'index'     => $this->indexName,
            'id'        => $indexable->getDocumentKey(),
            'type'      => $indexable->getDocumentType(),
            'refresh'   => $this->synchronous ? 'wait_for' : false,
        ]);
    }    

    /**
     * Indexes a collection of documents
     *
     * @param Collection $collection
     * @return array
     */
    public function indexCollection(Collection $collection) : array
    {
        $params = [
            'refresh' => $this->synchronous ? 'wait_for' : false,
            'body' => [],
        ];

        foreach ($collection as $indexable) {

            $params['body'][] = [
                'index' => [
                    '_index' => $this->indexName,
                    '_id' => $indexable->getDocumentKey(),
                    '_type' => $indexable->getDocumentType(),
                ],
            ];

            $params['body'][] = $indexable->getDocumentTree();
        }

        return $this->getElasticsearchClient()->bulk($params);
    }

    /**
     * Queue the indexing of documents
     *
     * @param Builder $query
     * @param integer $chunks
     * @return void
     */
    public function queue(Builder $query, int $chunks = 100) : void
    {
        $query->chunk($chunks, function ($documents) {
            IndexDocuments::dispatch($documents);
        });
    } 
}
