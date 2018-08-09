<?php

namespace EthicalJobs\Elasticsearch\Commands;

use EthicalJobs\Elasticsearch\IndexManager;

/**
 * Creates the default elasticsearch index
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class CreateIndex extends \Illuminate\Console\Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ej:es:index-create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates the default elasticsearch index';

    /**
     * Elastic search index manager service
     *
     * @param IndexManager
     */
    private $indexManager;

    /**
     * Constructor
     *
     * @param IndexManager $indexManager
     * @return void
     */
    public function __construct(IndexManager $indexManager)
    {
        parent::__construct();

        $this->indexManager = $indexManager;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $response = $this->indexManager->create();

        if ($response) {
            $this->info('Index successfully created.');
        } else {
            $this->error('Index creation failed.');
        }        

        return $response;
    }
}
