<?php

namespace EthicalJobs\Elasticsearch;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

trait ElasticsearchClient
{
    /**
     * Elasticsearch client
     *
     * @var Client
     */
    protected $client;

    /**
     * Sets the elasticsearch client 
     *
     * @param Client $client
     * @return void
     */  
    public function setElasticsearchClient(Client $client) : void
    {
        $this->client = $client;
    }

    /**
     * Returns the elasticsearch client
     *
     * @return Client
     */    
    public function getElasticsearchClient() : Client
    {
        if (empty($this->client)) {
            $connectionKey = config('elasticsearch.default');

            $connection = config("elasticsearch.connections.$connectionKey", []);

            $this->client = $this->buildClient($connection);
        }

        return $this->client;
    }     

    /**
     * Builds a new client instance
     *
     * @param array $params
     * @return Client
     */
    protected function buildClient(array $params) : Client
    {
        $builder = ClientBuilder::create();
        
        $builder->setHosts($params['hosts']);

        if ($params['logging']) {
            $logPath = storage_path('logs/elasticsearch-'.php_sapi_name().'.log');
            $logger = ClientBuilder::defaultLogger($logPath);
            $builder->setLogger($logger);
        }

        return $builder->build();        
    }
}