<?php

	namespace Kosmosx\Auth;

	use Illuminate\Support\ServiceProvider;
	use Gate;

	class AuthServiceProvider extends ServiceProvider
	{
		public function boot()
		{
			$this->policies();
			$this->define();
		}

		/**
		 * Register Gate policy
		 */
		private function policies()
		{
			$policies = config('permission.policies') ?: array();
			foreach ($policies as $key => $policy)
				Gate::policy($key, $policy);
		}

		/**
		 * Register Gate define
		 */
		private function define()
		{
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
			try {
				$this->app->configure('auth');
				$this->app->configure('jwt');
				$this->app->configure('permission');

				$this->app->routeMiddleware(array(
					'api.jwt' => \Kosmosx\Auth\Middleware\JwtMiddleware::class,
					'api.auth' => \Kosmosx\Auth\Middleware\AuthenticateMiddleware::class
				));
			} catch (\Exception $e) {

			}

			class_alias(\Tymon\JWTAuth\Facades\JWTAuth::class, 'JWTAuth');
			class_alias(\Tymon\JWTAuth\Facades\JWTFactory::class, 'JWTFactory');
			class_alias(\Kosmosx\Auth\AuthFacade::class, 'AuthService');

			if ($provider = config('auth.service_providers.jwt'))
				$this->app->register($provider);

			$this->app->bind('service.auth', 'Kosmosx\Auth\AuthService');

			$this->commands(\Kosmosx\Auth\Console\Commands\PublishConfig::class);
		}
	}
