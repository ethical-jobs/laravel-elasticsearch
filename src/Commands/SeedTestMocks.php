<?php

namespace EthicalJobs\Elasticsearch\Commands;

use Illuminate\Console\Command;
use EthicalJobs\Elasticsearch\Indexing\IndexQuery;
use EthicalJobs\Elasticsearch\Indexing\Indexer;
use EthicalJobs\Elasticsearch\Indexable;
use EthicalJobs\Elasticsearch\Index;

/**
 * Indexes integration test mocks 
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class SeedTestMocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ej:es:seed-mocks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates and indexes integration test mocks';

    /**
     * Elastic search index instance
     *
     * @param \EthicalJobs\Elasticsearch\Index 
     */
    private $index;    

    /**
     * Elastic search index service
     *
     * @param \App\Services\Elasticsearch\Indexing\Indexer
     */
    private $indexer;

    /**
     * Constructor
     *
     * @param \App\Services\Elasticsearch\Indexing\Indexer $indexer
     * @param \EthicalJobs\Elasticsearch\Index $index
     * @return void
     */
    public function __construct(Index $index, Indexer $indexer)
    {
        parent::__construct();

        $this->indexer = $indexer;

        $this->index = $index;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->app['config']
            ->set('elasticsearch.logging.slack.enabled', false);

        $this->app['config']
            ->set('elasticsearch.logging.console.enabled', false);
                
        $this->info('Booting elasticsearch...');

        sleep(15);

        $this->info('Deleting primary index...');

        $this->call('ej:es:index-delete');

        sleep(3);

        $this->info('Seeding test mock...');

        $this->call('db:seed', [
            '--class' => 'TestFixturesSeeder',
        ]);

        foreach ($this->getIndexables() as $indexable) {
            $this->index($indexable);
        }
    }

    /**
     * Indexes an indexable resource
     *
     * @param  string $indexable
     * @return void
     */
    protected function index(string $indexable): void
    {
        $indexQuery = new IndexQuery(new $indexable, 250);

        $this->indexer->indexQuery($indexQuery);
    }       

    /**
     * Returns indexable entities
     *
     * @return array
     */
    protected function getIndexables(): array
    {   
        return $this->index->getSettings()->getIndexables();       
    }    
}
