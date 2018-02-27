For Laravel 5+
---
Auto-detects the application environment with the hostname or http host
---
Allows for multiple environments and versioned "env" files.
---

 Detects the application environment from a env file or the hostname or http host. A .env file will always overwrite any other file (default Laravel's behavior)

 Use this code by adding this in bootstrap/app.php 

 ```
    \Jarnix\LaravelAutoEnvironment::init($app, [
      'envsFolder' => '/config',
      'hostLocalRegexp' => '/vagrantphp7',
      'urlLocalRegexp' => '/l\.webedev\.com/',
      'urlTestingRegexp' => '/test\.webedev.com/',
      'urlProductionRegexp' => '/www\.webedev.com/'
    ]);
```

 Put your different configuration files in the app's "config" folder, or another folder (specified as a parameter):

 (eg: local.env, testing.env, production.env)

 1) You can use a .env file as usual, this code won't do anything
 
 2) You can also use the autodetect feature of this code:
 ```
    envsFolder: where you put the .env files (local.env, production.env...)
    hostnameLocalRegexp: the regexp for the hostname in local mode (cli)
    urlLocalRegexp: the regexp for the http host in local mode (http)
    urlTestingRegexp: the regexp for the http host in testing mode (http)
    urlProductionRegexp: the regexp for the http host in prod mode (http)
```
    This code will load the env file that you want in the "config" folder.
 
 3) You can force the environment you want in a file called "env" at the
    root of the app.
    Example: if the file's content is "local"
    => the file : /config/local.env will be loaded