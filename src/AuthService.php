<?php
	/**
	 * Created by PhpStorm.
	 * User: fabrizio
	 * Date: 06/08/18
	 * Time: 11.33
	 */

	namespace Kosmosx\Auth;

	use Symfony\Component\HttpKernel\Exception\HttpException;
	use Tymon\JWTAuth\JWTAuth;
	use Illuminate\Contracts\Auth\Guard;

	class AuthService
	{
		/**
		 * @var JWTAuth
		 */
		public $jwt;

		/**
		 * AuthService constructor.
		 *
		 * @param User $user
		 */
		public function __construct()
		{
			$this->jwt = app(JWTAuth::class);
		}

		/**
		 * Return User object if token is valid
		 * else return error response
		 *
		 * @return mixed
		 */
		public function getUser()
		{
			$this->tryAuthenticatedUser();
			return $this->user();
		}

		/**
		 * Verification of the jwt token with specific exception response
		 *
		 * @return mixed
		 */
		public function tryAuthenticatedUser()
		{
			try {
				if (!$user = $this->jwt->parseToken()->authenticate())
					throw new HttpException(400, 'Error Exception');
			} catch (\Tymon\JWTAuth\Exceptions\TokenBlacklistedException $e) {
				throw new HttpException(400, 'The token has been blacklisted');
			} catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
				throw new HttpException(400, 'Token expired');
			} catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
				throw new HttpException(400, 'Token invalid');
			} catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
				throw new HttpException(400, 'Token absent');
			}
		}

		/**
		 * Get User object without check token
		 * If user don't exist response not found error
		 *
		 * @return mixed
		 */
		public function user()
		{
			$user = $this->guard()->user();
			if (!$user)
				return null;

			return $user;
		}

		/**
		 * Method to use Auth\Guard function
		 *
		 * @return \Laravel\Lumen\Application|mixed
		 */
		public function guard()
		{
			return app(Guard::class);
		}

		/**
		 * Check token and invalidate it
		 * Return status object with message
		 *
		 * @param bool $force
		 *
		 * @return \Core\Services\ServiceStatus|object
		 */
		public function invalidate($force = false): void
		{
			$this->tryAuthenticatedUser();
			$this->jwt->parseToken()->invalidate($force);
		}

		/**
		 * Refresh token and invalidate old token
		 * Return status object with data and message
		 *
		 * @param bool $force
		 *
		 * @return \Core\Services\ServiceStatus|object
		 */
		public function refresh($force = false, $resetClaims = false): array
		{
			$token = $this->jwt->parseToken()->refresh($force, $resetClaims);

			return compact('token');
		}
	}