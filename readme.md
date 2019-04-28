## KosmosX Auth
![](https://img.shields.io/badge/version-0.0.1--beta-green.svg)

### Installation
    
    composer require kosmosx/auth
    
    php artisan kosmosx:publish:auth //if will be use JWT
#### Laravel
    
**Add provider in array 'providers' (config/app.php)**
    
    Kosmosx\Auth\Providers\ManagerServiceProvider::class
    
**Add to (config/auth.php)**

    'service_providers' => [
        'jwt' =>  env('AUTH_PROVIDERS', Tymon\JWTAuth\Providers\LaravelServiceProvider::class)
    ],
    
    'guards' => [
        ...
        
        'api' => [
            'provider' => 'jwt',
            'driver' => 'jwt',
        ],
    ],
    		
    'providers' => [
        ...
        
        'jwt' => [
                'driver' => 'eloquent',
                'model' => env('AUTH_MODEL', App\Models\User::class),
            ],
    ]
    
**Add to .env file**
    
    AUTH_PROVIDERS=Tymon\JWTAuth\Providers\LaravelServiceProvider
    
#### Lumen

**File bootstrap/app.php**

    //uncomment this line:
    $app->withFacades();
    $app->withEloquent();

    //Register providers
	$app->register(Kosmosx\Auth\ManagerServiceProvider::class);
	
**Add to .env file (Or change config/auth.php key of array 'providers')**

    AUTH_PROVIDERS=Tymon\JWTAuth\Providers\LumenServiceProvider
    
  