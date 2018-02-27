<?php

namespace jarnix\LaravelAutoEnvironment;

use Illuminate\Routing\Router;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use \Illuminate\Foundation\Validation\ValidatesRequests;

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);
    }

    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $router = $app['router'];
        $this->addWebRoutes($router);
    }

    /**
     * @param Router $router
     */
    protected function addWebRoutes(Router $router)
    {
        $router->get('autoenv/get', [
            'as' => 'autoenv.get',
            'uses' => function () {
                return config('app.url') . ' in ' . config('app.env');
            }
        ]);
    }

    /**
     * @param Router $router
     */
    protected function addApiRoutes($router)
    {
        $router->group(['middleware' => \Barryvdh\Cors\HandleCors::class], function () use ($router) {
            $router->get('api/ping', [
                'as' => 'api.ping',
                'uses' => function () {
                    return 'pong';
                }
            ]);
            $router->post('api/ping', [
                'uses' => function () {
                    return 'PONG';
                }
            ]);
            $router->put('api/ping', [
                'uses' => function () {
                    return 'PONG';
                }
            ]);
            $router->post('api/error', [
                'uses' => function () {
                    abort(500);
                }
            ]);
            $router->post('api/validation', [
                'uses' => function (\Illuminate\Http\Request $request) {
                    $this->validate($request, [
                        'name' => 'required',
                    ]);
                    return 'ok';
                }
            ]);
        });
    }

}