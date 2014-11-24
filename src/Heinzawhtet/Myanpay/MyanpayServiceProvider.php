<?php namespace Heinzawhtet\Myanpay;

use Illuminate\Support\ServiceProvider;

class MyanpayServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('heinzawhtet/myanpay');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['myanpay'] = $this->app->share(function($app)
		{
			return new Myanpay;
		});

		$this->app->booting(function()
		{
		  $loader = \Illuminate\Foundation\AliasLoader::getInstance();
		  $loader->alias('Myanpay', 'Heinzawhtet\Myanpay\MyanpayFacade');
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('myanpay');
	}

}
