<?php
/**
 * Routes configuration.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * It's loaded within the context of `Application::routes()` method which
 * receives a `RouteBuilder` instance `$routes` as method argument.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

/*
 * This file is loaded in the context of the `Application` class.
  * So you can use  `$this` to reference the application class instance
  * if required.
 */
return function (RouteBuilder $routes): void {
    /*
     * The default class to use for all routes
     *
     * The following route classes are supplied with CakePHP and are appropriate
     * to set as the default:
     *
     * - Route
     * - InflectedRoute
     * - DashedRoute
     *
     * If no call is made to `Router::defaultRouteClass()`, the class used is
     * `Route` (`Cake\Routing\Route\Route`)
     *
     * Note that `Route` does not do any inflections on URLs which will result in
     * inconsistently cased URLs when used with `{plugin}`, `{controller}` and
     * `{action}` markers.
     */
    $routes->setRouteClass(DashedRoute::class);

    $routes->scope('/', function (RouteBuilder $builder): void {
        /*
         * Here, we are connecting '/' (base path) to a controller called 'Pages',
         * its action called 'display', and we pass a param to select the view file
         * to use (in this case, templates/Pages/home.php)...
         */
        $builder->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);

        /*
         * ...and connect the rest of 'Pages' controller's URLs.
         */
        $builder->connect('/pages/*', 'Pages::display');

        /*
         * Connect catchall routes for all controllers.
         *
         * The `fallbacks` method is a shortcut for
         *
         * ```
         * $builder->connect('/{controller}', ['action' => 'index']);
         * $builder->connect('/{controller}/{action}/*', []);
         * ```
         *
         * You can remove these routes once you've connected the
         * routes you want in your application.
         */
        $builder->scope('/articles', function (RouteBuilder $builder) {
            $builder->connect('/tagged/*', ['controller' => 'Articles', 'action' => 'tags']);
        });

        $builder->scope('/users', function (RouteBuilder $builder) {
            $builder->connect('/users/index', ['controller' => 'Users', 'action' => 'index']);
        });

        // $builder->connect('/users', ['controller' => 'Users', 'action' => 'index']);
        // $builder->connect('/users/index', ['controller' => 'Users', 'action' => 'index']);


        $builder->connect('/products', ['controller' => 'Products', 'action' => 'index']);
        $builder->connect('/products/add', ['controller' => 'Products', 'action' => 'add']);
        $builder->connect('/products/add_production_date/:id', ['controller' => 'Products', 'action' => 'addProductionDate']);
        $builder->connect('/products/get/*', ['controller' => 'Products', 'action' => 'get']);
        $builder->connect('/products/regist_production_date', ['controller' => 'Products', 'action' => 'registProductionDate']);

        $builder->fallbacks(DashedRoute::class);
    });

    /*
     * If you need a different set of middleware or none at all,
     * open new scope and define routes there.
     *
     * ```
     * $routes->scope('/api', function (RouteBuilder $builder): void {
     *     // No $builder->applyMiddleware() here.
     *
     *     // Parse specified extensions from URLs
     *     // $builder->setExtensions(['json', 'xml']);
     *
     *     // Connect API actions here.
     * });
     * ```
     */
    // $routes->scope('api', function (RouteBuilder $routes) {
    //     $routes->setExtensions(['json']);
    //     $routes->resources('Products');
    //     $routes->resources('Articles');
    //     $routes->fallbacks(DashedRoute::class);
    // });

    $routes->prefix('api', function (RouteBuilder $routes) {
        $routes->setExtensions(['json']);
        $routes->resources('Products');
        $routes->resources('Products', [
            'map' => [
                'get' => [ 'action' => 'get', 'method' => 'GET', 'path' => 'get/:id'],
                'post' => ['action' => 'add', 'method' => 'POST', 'path' => 'add/']
            ]
        ]);
        $routes->resources('Articles');
        $routes->fallbacks(DashedRoute::class);
    });
};
