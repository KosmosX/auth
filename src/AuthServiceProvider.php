<?php

	namespace Kosmosx\Auth;

	use Illuminate\Support\ServiceProvider;
	use Gate;

	class AuthServiceProvider extends ServiceProvider
	{
		public function boot()
		{
			$policies = config('permission.policies') ?: array();
			foreach ($policies as $key => $policy)
				Gate::policy($key, $policy);

			$defines = config('permission.defines') ?: array();
			foreach ($defines as $key => $define)
				Gate::define($key, $define);
		}

		/**
		 * Register any application services.
		 *
		 * @return void
		 */
		public function register()
		{
			$this->app->configure('auth');
			$this->app->configure('jwt');
			$this->app->configure('permission');

			register_alias(\Tymon\JWTAuth\Facades\JWTAuth::class, 'JWTAuth');
			register_alias(\Tymon\JWTAuth\Facades\JWTFactory::class, 'JWTFactory');
			register_alias(\Kosmosx\Auth\AuthFacade::class, 'AuthService');

			$this->app->register(\Tymon\JWTAuth\Providers\LumenServiceProvider::class);

			$this->app->bind('service.auth', 'Kosmosx\Auth\AuthService');

			$this->commands(\Kosmosx\Auth\Console\PublishConfig::class);
		}
	}
