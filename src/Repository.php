<?php

namespace EthicalJobs\Elasticsearch;

use EthicalJobs\Elasticsearch\Contracts\HasElasticsearch;
use EthicalJobs\Elasticsearch\Contracts\Indexable;
use EthicalJobs\Elasticsearch\Hydrators\ObjectHydrator;
use EthicalJobs\Storage\Contracts;
use EthicalJobs\Storage\CriteriaCollection;
use EthicalJobs\Storage\HasCriteria;
use EthicalJobs\Storage\HydratesResults;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\FullText;
use ONGR\ElasticsearchDSL\Query\TermLevel;
use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchDSL\Sort\FieldSort;

/**
 * Elasticsearch repository
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */
class Repository implements HasElasticsearch, Contracts\Repository, Contracts\HasCriteria, Contracts\HydratesResults
{
    use ElasticsearchClient, HasCriteria, HydratesResults;

    /**
     * Indexable model
     *
     * @var Indexable
     */
    protected $indexable;

    /**
     * Elasticsearch query DSL
     *
     * @var Search
     */
    protected $search;

    /**
     * Object constructor.
     *
     * @param Indexable $indexable
     * @return void
     */
    public function __construct(Indexable $indexable)
    {
        $this->indexable = $indexable;

        $this->search = new Search;

        $this->criteria = new CriteriaCollection;

        $this->setHydrator(new ObjectHydrator);

        $this->limit(10000); // ES max
    }

    /**
     * {@inheritdoc}
     */
    public function limit(int $limit): Contracts\Repository
    {
        $this->search->setSize($limit);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStorageEngine()
    {
        return $this->getElasticSearchClient();
    }

    /**
     * {@inheritdoc}
     */
    public function setStorageEngine($storage)
    {
        $this->setElasticsearchClient($storage);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function findById($id)
    {
        $query = new TermLevel\TermQuery('id', $id);

        $this->search->addQuery($query, BoolQuery::FILTER);

        return $this->limit(1)->find()->first();
    }

    /**
     * {@inheritdoc}
     */
    public function findByField(string $field, $value)
    {
        $query = new TermLevel\TermQuery($field, $value);

        $this->search->addQuery($query);

        return $this->limit(1)->find()->first();
    }

    /**
     * {@inheritdoc}
     */
    public function where(string $field, $operator, $value = null): Contracts\Repository
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
    public function whereIn(string $field, array $values): Contracts\Repository
    {
        $query = new TermLevel\TermsQuery($field, $values);

        $this->search->addQuery($query, BoolQuery::FILTER);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function whereHasIn(string $field, array $values): Contracts\Repository
    {
        $fields = explode('.', $field);

        $query = new TermLevel\TermsQuery($field, $values);

        $this->search->addQuery($query, BoolQuery::FILTER);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function orderBy(string $field, string $direction = 'asc'): Contracts\Repository
    {
        $this->search->addSort(new FieldSort($field, $direction));

        $this->search->addSort(new FieldSort('_score', $direction));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function search(string $term = ''): Contracts\Repository
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
    public function find(): iterable
    {
        // dd($this->search->toArray());

        $this->applyCriteria();

        $response = $this->getElasticsearchClient()->search([
            'index' => Utilities::config('index'),
            'type' => $this->indexable->getDocumentType(),
            'body' => $this->search->toArray(),
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
        throw new \Exception('Use EthicalJobs\Elasticsearch\Indexing\Indexer service.');
    }

    /**
     * {@inheritdoc}
     */
    public function updateCollection(iterable $entities)
    {
        throw new \Exception('Use EthicalJobs\Elasticsearch\Indexing\Indexer service.');
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        throw new \Exception('Use EthicalJobs\Elasticsearch\Indexing\Indexer service.');
    }
}