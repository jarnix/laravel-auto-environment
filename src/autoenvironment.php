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
| You put your different configuration files in the app's "config"
| folder (eg: local.env, testing.env, production.env)
|
| 1) You can use a .env file as usual, this code won't do anything
| 2) You can also use the autodetect feature of this code:
|    ___ENV_HOST_LOCAL_REGEXP: the regexp for the hostname in local mode (cli)
|    ___ENV_URL_LOCAL_REGEXP: the regexp for the http host in local mode (http)
|    ___ENV_URL_TESTING_REGEXP: the regexp for the http host in testing mode (http)
|    ___ENV_URL_PRODUCTION_REGEXP: the regexp for the http host in prod mode (http)
|    This code will load the env file that you want in the "config" folder.
| 3) You can force the environment you want in a file called "env" at the
|    root of the app.
|    Example: if the file's content is "local"
|    => the file : /config/local.env will be loaded
*/

const ___ENV_FOLDER = '/../config/';
const ___ENV_HOST_LOCAL_REGEXP = '/mycomputer/';
const ___ENV_URL_LOCAL_REGEXP = '/www\.localhost\.dev/';
const ___ENV_URL_TESTING_REGEXP = '/test\.localhost.dev/';
const ___ENV_URL_PRODUCTION_REGEXP = '/www\.omgthisisagreatapplicationyesss.com/';

$env = $app->detectEnvironment(function () {

    $foundEnv = null;

    // if we put a .env file, use it (default behavior)
    $usualEnvironmentPath = __DIR__ . '/../.env';
    if (!file_exists($usualEnvironmentPath)) {
        if (php_sapi_name()=='cli') {
            $envFromCli = getopt('env');
            if (!empty($envFromCli)) {
                $foundEnv = $envFromCli;
            } else {
                // guess from the hostname
                if (preg_match(___ENV_HOST_LOCAL_REGEXP, gethostname())) {
                    $foundEnv = 'local';
                }
            }
        } else {
            // see if we match a hostname with the given regular expressions
            if (preg_match(__ENV_URL_LOCAL_REGEXP, $_SERVER['HTTP_HOST']) !== false) {
                $foundEnv = 'local';
            } elseif (preg_match(__ENV_URL_TESTING_REGEXP, $_SERVER['HTTP_HOST']) !== false) {
                $foundEnv = 'testing';
            } elseif (preg_match(__ENV_URL_PRODUCTION_REGEXP, $_SERVER['HTTP_HOST']) !== false) {
                $foundEnv = 'production';
            }
        }
        // force the environment with a "env" file containing what environment to load
        $forcedEnvironmentPath = __DIR__ . '/../env';
        if (file_exists($forcedEnvironmentPath)) {
            $forcedEnv = preg_replace('/(#.*)/', '', trim(file_get_contents($forcedEnvironmentPath)));
            if (!empty($forcedEnv)) {
                $foundEnv = $forcedEnv;
            }
        }

        // loading the chosen environment file in the right folder
        $wantedEnvPath = [ __DIR__ . ___ENV_FOLDER, '.env.' . $foundEnv ];
        if (file_exists(implode($wantedEnvPath))) {
            putenv('APP_ENV=' . $foundEnv);
            $dotenv = new Dotenv($wantedEnvPath[0], $wantedEnvPath[1]);
            $dotenv->load();
        } else {
            if (php_sapi_name() != 'cli') {
                ini_set('display_errors', true);
            }
            throw new \Exception(
                'The environment file '
                . realpath($wantedEnvPath[0]) . '/'
                . $wantedEnvPath[1] . ' was not found'
            );
        }
    }
});
