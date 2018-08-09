<?php

namespace EthicalJobs\Elasticsearch\Commands;

use EthicalJobs\Elasticsearch\IndexManager;

/**
 * Deletes the default elasticsearch index
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class DeleteIndex extends \Illuminate\Console\Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ej:es:index-delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes the default elasticsearch index';

    /**
     * Elastic search index service
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
        $response = $this->indexManager->delete();

        if ($response) {
            $this->info('Index successfully deleted.');
        } else {
            $this->error('Index deletion failed.');
        }

        return $response;
    }
}
