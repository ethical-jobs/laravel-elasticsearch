<?php

namespace EthicalJobs\Elasticsearch\Testing;

use Mockery;
use Illuminate\Support\Collection;
use M6Web\Component\ElasticsearchMock\Client as MockClient;
use EthicalJobs\Elasticsearch\Contracts\IndexableObserver;
use EthicalJobs\Elasticsearch\Indexing\Indexer;

/**
 * Mocks the elasticsearch results
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class SearchResultsFactory
{
    /**
     * Mocks results of an Elasicsearch "search" api call
     *
     * @param Illuminate\Support\Collection $entities
     * @return MockClient
     */
    public static function getSearchResults(Collection $entities)
    {
        $elasticClient = new MockClient;

        $documents = $entities->map(function ($indexable) {
            return $indexable->getDocumentTree();
        })->toArray();

        $elasticClient->addSearchResult(
            'test-index',
            $entities->first()->getDocumentType(),
            $documents
        );

        return $elasticClient->search();
    } 

    /**
     * Mocks empty elasticsearch results
     *
     * @return MockClient
     */
    public static function getEmptySearchResults()
    {
        $elasticClient = new MockClient;

        $elasticClient->addSearchResult('test-index', 'mock_document_type', []);

        return $elasticClient->search();
    }     
}