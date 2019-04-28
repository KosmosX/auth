<?php

	namespace Kosmosx\Auth;

	use Kosmosx\Core\Providers\BaseServiceProvider;
	use Gate;

	class AuthServiceProvider extends BaseServiceProvider
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
			$this->registerConfigs('auth', 'jwt', 'permission');

			$this->registerAlias(array(
				'JWTAuth' => \Tymon\JWTAuth\Facades\JWTAuth::class,
				'JWTFactory' => \Tymon\JWTAuth\Facades\JWTFactory::class,
				'AuthService' => \Kosmosx\Auth\AuthFacade::class,
			));

			$this->registerProviders(config('auth.service_providers')?:array());

			$this->app->bind('service.auth', 'Kosmosx\Auth\AuthService');

			$this->registerRouteMiddleware(array(
				'api.jwt' => \Kosmosx\Auth\Middleware\JwtMiddleware::class,
				'api.auth' => \Kosmosx\Auth\Middleware\AuthenticateMiddleware::class
			));
		}
	}
