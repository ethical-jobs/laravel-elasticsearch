<?php

namespace Tests;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Orchestra\Database\ConsoleServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use EthicalJobs\Elasticsearch\ServiceProvider;
use EthicalJobs\Elasticsearch\Testing\IndexableObservers;
use Tests\Fixtures\Models;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
	use RefreshDatabase;

    /**
     * Setup the test environment.
     *
     * @return void
     * @throws Exception
     */
	protected function setUp(): void
	{
	    parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');        

		$this->withFactories(__DIR__.'/../database/factories');
		
		IndexableObservers::withoutObservers();
	}	

	/**
	 * Define environment setup.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function getEnvironmentSetUp($app)
	{
		$app['config']->set('elasticsearch.index', 'test-index');

	    $app['config']->set('elasticsearch.indexables', [
	        Models\Person::class,
	        Models\Family::class,
	        Models\Vehicle::class,
	    ]);
	}	

	/**
	 * Inject package service provider
	 * 
	 * @param  Application $app
	 * @return array
	 */
	protected function getPackageProviders($app)
	{
	    return [
	    	ServiceProvider::class,
	    	ConsoleServiceProvider::class,
	   	];
	}

	/**
	 * Inject package facade aliases
	 * 
	 * @param  Application $app
	 * @return array
	 */
	protected function getPackageAliases($app)
	{
	    return [];
	}	
}