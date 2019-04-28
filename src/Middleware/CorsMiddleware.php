<?php

	namespace Kosmosx\Auth\Middleware;

	use Closure;
	use FactoryResponse;

	class CorsMiddleware
	{
		/**
		 * Handle an incoming request.
		 *
		 * @param  \Illuminate\Http\Request $request
		 * @param  \Closure                 $next
		 *
		 * @return mixed
		 */
		public function handle($request, Closure $next)
		{
			//Return request
			if (false === $this->isCorsRequest($request))
				return $next($request);

			//Get CORS headers with helper function
			$cors_headers = get_config_env('api.cors', 'standard');

			//Return preflight response
			if (true === $this->isPreflightRequest($request))
				return FactoryResponse::success("OK", 200, $cors_headers);

			//Add CORS headers to response
			$response = $next($request);
			foreach ($cors_headers as $key => $value)
				$response->headers->set($key, $value);

			return $response;
		}

		/**
		 * Check if reqeust is a preflight request
		 *
		 * @param $request
		 *
		 * @return bool
		 *             true: is preflight request
		 */
		public function isPreflightRequest($request)
		{
			return $request->method() === 'OPTIONS' && $request->headers->has('Access-Control-Request-Method');
		}

		/**
		 * Check if request is a CORS request
		 * @param $request
		 *
		 * @return bool
		 *             true: is CORS request
		 */
		public function isCorsRequest($request): bool
		{
			return $request->headers->has('Origin') && !($request->headers->get('Origin') === $request->getSchemeAndHttpHost());
		}
	}