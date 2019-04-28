<?php
	/**
	 * Created by PhpStorm.
	 * User: fabrizio
	 * Date: 09/08/18
	 * Time: 17.42
	 */
	namespace Kosmosx\Auth;

	use Illuminate\Support\Facades\Facade;

	class AuthFacade extends Facade
	{
		protected static function getFacadeAccessor()
		{
			return 'service.auth';
		}
	}