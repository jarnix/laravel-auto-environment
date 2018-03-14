For Laravel 5+
---
Auto-detects the application environment with the hostname or http host
---
Allows for multiple environments and versioned "env" files.
---

Detects the application environment from a env file or the hostname or http host. A .env file will always overwrite any other file (default Laravel's behavior)

Use this code by adding this in bootstrap/app.php 

```
\Jarnix\LaravelAutoEnvironment\Loader::init($app, [
    'envsFolder' => '/config',
    'hostLocalRegexp' => '/vagrantphp7',
    'urlLocalRegexp' => '/l\.webedev\.com/',
    'urlTestingRegexp' => '/test\.webedev.com/',
    'urlProductionRegexp' => '/www\.webedev.com/'
]);
```

Put your different configuration files in the app's "/config" folder, or another folder (specified as the 'envsFolder' parameter) :
- .env.local
- .env.testing
- .env.production

Parameters
-- 
You can customize these parameters:

- envsFolder: where you put the .env files (local.env, production.env...)
- hostnameLocalRegexp: the regexp for the hostname in local mode (cli)
- urlLocalRegexp: the regexp for the http host in local mode (http)
- urlTestingRegexp: the regexp for the http host in testing mode (http)
- urlProductionRegexp: the regexp for the http host in prod mode (http)

You can force the environment everywhere
--
You can also force the environment you want by creating a file called "env" at the root of the app, containing the environment you want to load. 

This code will then load the "env" file that you want in the "envsFolder" folder. For example: if the file's content is "local" => the file : /config/.env.local will be loaded

You can still force the environment in artisan
--
If you haven't created a env file, you can still obviously force the environment by using:
```
php artisan --env=testing
```

Or use a .env file
--
And you can use a .env file as usual, this will then bypass this code.
