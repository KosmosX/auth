<?php
	/**
	 * Created by PhpStorm.
	 * User: fabrizio
	 * Date: 06/08/18
	 * Time: 11.33
	 */

	namespace Kosmosx\Auth;

	use Kosmosx\Core\Service;
	use Tymon\JWTAuth\JWTAuth;
	use Illuminate\Contracts\Auth\Guard;

	class AuthService extends Service
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
					return $this->response()->errorException('Error Exception');
			} catch (\Tymon\JWTAuth\Exceptions\TokenBlacklistedException $e) {
				return $this->response()->errorException('The token has been blacklisted');
			} catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
				return $this->response()->errorException('Token expired');
			} catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
				return $this->response()->errorException('Token invalid');
			} catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
				return $this->response()->errorException('Token absent');
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
				return $this->fail(404, array(), "User not found");

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
		public function invalidate($force = false)
		{
			$this->tryAuthenticatedUser();
			$this->jwt->parseToken()->invalidate($force);

			return $this->success(202, array(), 'The token has been invalidated');
		}

		/**
		 * Refresh token and invalidate old token
		 * Return status object with data and message
		 *
		 * @param bool $force
		 *
		 * @return \Core\Services\ServiceStatus|object
		 */
		public function refresh($force = false, $resetClaims = false)
		{
			$token = $this->jwt->parseToken()->refresh($force, $resetClaims);

			return $this->success(201, compact('token'), 'The token has been refreshed');
		}
	}