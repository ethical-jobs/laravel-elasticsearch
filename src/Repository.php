<?php

namespace EthicalJobs\Elasticsearch;

use Elasticsearch\Client;
use Illuminate\Database\Eloquent\Model;
use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchDSL\Sort\FieldSort;
use ONGR\ElasticsearchDSL\Query\TermLevel;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\FullText;
use ONGR\ElasticsearchDSL\Query\Joining;
use EthicalJobs\Storage\Contracts;
use EthicalJobs\Storage\HasCriteria;
use EthicalJobs\Storage\CriteriaCollection;
use EthicalJobs\Storage\HydratesResults;
use EthicalJobs\Elasticsearch\Hydrators\ObjectHydrator;
use EthicalJobs\Elasticsearch\Utilities;

/**
 * Elasticsearch repository
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class Repository implements Contracts\Repository, Contracts\HasCriteria, Contracts\HydratesResults
{
    use HasCriteria, HydratesResults;

    /**
     * Elasticsearch client
     * 
     * @var Elasticsearch\Client
     */
    protected $client;

    /**
     * Name of the working Elasticsearch index
     * 
     * @var string
     */    
    protected $indexName;
    
    /**
     * Indexable model 
     * 
     * @var EthicalJobs\Elasticsearch\Indexable
     */    
    protected $indexable;
    
    /**
     * Elasticsearch query DSL
     * 
     * @var ONGR\ElasticsearchDSL\Search
     */    
    protected $search;

    /**
     * Object constructor
     *
     * @param \EthicalJobs\Elasticsearch\Indexable $indexable
     * @param \ONGR\ElasticsearchDSL\Search $search
     * @param \Elasticsearch\Client $client
     * @param string $indexName
     * @return void
     */
    public function __construct(
        Indexable $indexable, 
        Search $search, 
        Client $client, 
        string $indexName = 'test-index'
    )
    {
        $this->indexable = $indexable;

        $this->search = $search;

        $this->indexName = $indexName;

        $this->setStorageEngine($client);

        $this->criteria = new CriteriaCollection;

        $this->setHydrator(new ObjectHydrator);
    }

    /**
     * {@inheritdoc}
     */
    public function getStorageEngine()
    {    
        return $this->client;
    }

    /**
     * {@inheritdoc}
     */
    public function setStorageEngine($storage)
    {    
        $this->client = $storage;

        return $this;
    }        

    /**
     * {@inheritdoc}
     */
    public function findById($id)
    {
        $query = new TermLevel\TermQuery('id', $id);

        $this->search->addQuery($query, BoolQuery::FILTER);        

        return $this->find()->first();
    }  

    /**
     * {@inheritdoc}
     */
    public function findByField(string $field, $value)
    {
        $query = new TermLevel\TermQuery($field, $value);

        $this->search->addQuery($query, BoolQuery::FILTER);        

        return $this->find()->first();
    }     

    /**
     * {@inheritdoc}
     */
    public function where(string $field, $operator, $value = null) : Contracts\Repository
    {
        $operator = Utilities::translateOperator($operator);

        switch ($operator) {
            case 'lte':
            case 'gte':
            case 'lt':
            case 'gt':
                $query = new TermLevel\RangeQuery($field, [$operator => $value]);
                $bool = BoolQuery::FILTER;
                break;
            case 'like':
                $query = new FullText\QueryStringQuery(str_replace('%', '*', $value));
                $query->addParameter('default_field', $field);
                $bool = null;
                break;    
            case '!=':
                $query = new TermLevel\TermQuery($field, $value);
                $bool = BoolQuery::MUST_NOT;
                break; 
            case '=':
            default:
                $query = new TermLevel\TermQuery($field, $value ? $value : $operator);
                $bool = BoolQuery::FILTER;
                break;                                             
        }

        if ($bool) {
            $this->search->addQuery($query, $bool); 
        } else {
            $this->search->addQuery($query); 
        }

        return $this;
    }  

    /**
     * {@inheritdoc}
     */
    public function whereIn(string $field, array $values) : Contracts\Repository
    {
        $query = new TermLevel\TermsQuery($field, $values);

        $this->search->addQuery($query, BoolQuery::FILTER);        

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function whereHasIn(string $field, array $values) : Contracts\Repository
    {
        $fields = explode('.', $field);

        $boolQuery = new BoolQuery;

        $termsQuery = new TermLevel\TermsQuery($field, $values);

        $boolQuery->add($termsQuery);

        $nestedQuery = new Joining\NestedQuery($fields[0], $boolQuery);

        $this->search->addQuery($nestedQuery, BoolQuery::FILTER);  
        
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function orderBy(string $field, $direction = 'asc') : Contracts\Repository
    {
        $this->search->addSort(new FieldSort($field, $direction));

        $this->search->addSort(new FieldSort('_score', $direction));  

        return $this;
    }               

    /**
     * {@inheritdoc}
     */
    public function limit(int $limit) : Contracts\Repository
    {
        $this->search->setSize($limit);

        return $this;
    }    
    
    /**
     * {@inheritdoc}
     */
    public function search(string $term = '') : Contracts\Repository
    {
        $query = new FullText\SimpleQueryStringQuery($term, [
            'fields' => ['_all'],
            'default_operator' => 'and',
        ]);

        $this->search->addQuery($query);

        return $this;
    }        

    /**
     * {@inheritdoc}
     */
    public function find() : iterable
    {
        // dd($this->search->toArray());

        $response = $this->client->search([
            'index' => $this->indexName,
            'type'  => $this->indexable->getDocumentType(),
            'body'  => $this->search->toArray(),
        ]);
        
        return $this->getHydrator()
            ->setIndexable($this->indexable)
            ->hydrateCollection($response);
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $attributes)
    {
        throw new \Exception('Use EthicalJobs\Elasticsearch\Indexing service.');
    }        

    /**
     * {@inheritdoc}
     */
    public function updateCollection(iterable $entities)
    {
        throw new \Exception('Use EthicalJobs\Elasticsearch\Indexing service.');
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        throw new \Exception('Use EthicalJobs\Elasticsearch\Indexing service.');
    }       
}