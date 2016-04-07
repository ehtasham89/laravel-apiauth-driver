<?php namespace LvApiAuth;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Guard;

class LvApiAuthServiceProvider extends ServiceProvider {

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
		$this->package('ace-ticket/lv-api-auth');
		\Auth::extend('lvapiauth', function() {
		    return new Guard(new Providers\LvApiAuthUserProvider, \App::make('session.store'));
		});
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}