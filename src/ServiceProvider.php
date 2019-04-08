<?php

namespace EthicalJobs\Elasticsearch;

use EthicalJobs\Elasticsearch\Commands;
use EthicalJobs\Elasticsearch\Indexing\IndexableObserver;

/**
 * Elasticsearch service provider
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */
class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Config file path
     *
     * @var string
     */
    protected $configPath = __DIR__ . '/../config/elasticsearch.php';

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            $this->configPath => config_path('elasticsearch.php'),
        ], 'config');

        $this->bootObservers();

        $this->bootCommands();
    }

    /**
     * Configure indexable observers
     *
     * @return void
     */
    protected function bootObservers(): void
    {
        foreach (Utilities::getIndexables() as $indexable) {
            $indexable::observe(IndexableObserver::class);
        }
    }

    /**
     * Register console commands
     *
     * @return void
     */
    protected function bootCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\CreateIndex::class,
                Commands\DeleteIndex::class,
                Commands\FlushIndex::class,
                Commands\IndexDocuments::class,
            ]);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom($this->configPath, 'elasticsearch');
    }
}