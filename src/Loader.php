<?php

use Dotenv\Dotenv;

/*
|--------------------------------------------------------------------------
| Auto-detects the application environment
|--------------------------------------------------------------------------
|
| Detects the application environment from a env file or the hostname or
| http host. A .env file will always overwrite any other file (default
| Laravel's behavior)
|
| Use this code by adding this in bootstrap/app.php (
|    \Jarnix\LaravelAutoEnvironment::init($app, [
|      'envsFolder' => '/config',
|      'hostLocalRegexp' => '/vagrantphp7',
|      'urlLocalRegexp' => '/l\.webedev\.com/',
|      'urlTestingRegexp' => '/test\.webedev.com/',
|      'urlProductionRegexp' => '/www\.webedev.com/'
|    ]);
|
| Put your different configuration files in the app's "config"
| folder, or another folder (specified as a parameter):
| (eg: local.env, testing.env, production.env)
|
| 1) You can use a .env file as usual, this code won't do anything
| 2) You can also use the autodetect feature of this code:
|    envsFolder: where you put the .env files (local.env, production.env...)
|    hostnameLocalRegexp: the regexp for the hostname in local mode (cli)
|    urlLocalRegexp: the regexp for the http host in local mode (http)
|    urlTestingRegexp: the regexp for the http host in testing mode (http)
|    urlProductionRegexp: the regexp for the http host in prod mode (http)
|    This code will load the env file that you want in the "config" folder.
| 3) You can force the environment you want in a file called "env" at the
|    root of the app.
|    Example: if the file's content is "local"
|    => the file : /config/local.env will be loaded
*/

namespace Jarnix\LaravelAutoEnvironment;

class Loader
{
    const PARAMS = [
        'envsFolder' => '/config',
        'hostnameLocalRegexp' => '/vagrant/',
        'urlLocalRegexp' => '/localhost/',
        'urlTestingRegexp' => '/test\.localhost.dev/',
        'urlProductionRegexp' => '/www\.mywonderfullaravelwebsite.com/'
    ];

    public static function init(&$app, $inParams)
    {
        $params = self::PARAMS;
        foreach ($inParams as $inParam => $inValue) {
            if (in_array($inParam, self::PARAMS)) {
                $params[$inParam] = $inValue;
            }
        }

        //$app->detectEnvironment(function () use $params {
        $foundEnv = null;
        
        // if we put a .env file, use it (default behavior)
        $usualEnvironmentPath = base_path('/.env');
        if (!file_exists($usualEnvironmentPath)) {
            if (php_sapi_name()=='cli') {
                $envFromCli = getopt('env');
                if (!empty($envFromCli)) {
                    $foundEnv = $envFromCli;
                } else {
                    // guess from the hostname
                    if (preg_match($params['hostnameLocalRegexp'], gethostname())) {
                        $foundEnv = 'local';
                    }
                }
            } else {
                // see if we match a hostname with the given regular expressions
                if (preg_match($params['urlLocalRegexp'], $_SERVER['HTTP_HOST']) !== false) {
                    $foundEnv = 'local';
                } elseif (preg_match($params['urlTestingRegexp'], $_SERVER['HTTP_HOST']) !== false) {
                    $foundEnv = 'testing';
                } elseif (preg_match($params['urlProductionRegexp'], $_SERVER['HTTP_HOST']) !== false) {
                    $foundEnv = 'production';
                }
            }
            // force the environment with a "env" file containing what environment to load
            $forcedEnvironmentPath = base_path('/env');
            if (file_exists($forcedEnvironmentPath)) {
                $forcedEnv = preg_replace('/(#.*)/', '', trim(file_get_contents($forcedEnvironmentPath)));
                if (!empty($forcedEnv)) {
                    $foundEnv = $forcedEnv;
                }
            }
        
            // loading the chosen environment file in the right folder
            $wantedEnvPath = [ base_path($params['envsFolder']) . '/', '.env.' . $foundEnv ];
            if (file_exists(implode($wantedEnvPath))) {
                putenv('APP_ENV=' . $foundEnv);
                $dotenv = new \Dotenv\Dotenv($wantedEnvPath[0], $wantedEnvPath[1]);
                $dotenv->load();
            } else {
                if (php_sapi_name() != 'cli') {
                    ini_set('display_errors', true);
                }
                throw new \Exception('The environment file ' . realpath($wantedEnvPath[0]) . '/' . $wantedEnvPath[1] . ' was not found');
            }
        }
        //});
    }
}
