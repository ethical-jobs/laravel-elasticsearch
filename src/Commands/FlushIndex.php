<?php

namespace EthicalJobs\Elasticsearch\Commands;

/**
 * Deletes, creates and then indexes documents
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class FlushIndex extends \Illuminate\Console\Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ej:es:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes, creates and then indexes documents';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('ej:es:index-delete');

        $this->call('ej:es:index-create');

        $this->call('ej:es:index');
    }
}
