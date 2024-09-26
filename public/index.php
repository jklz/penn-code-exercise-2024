<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../vendor/autoload.php';

/**
 * use PHP-DI bridge to create slim app for controller DI
 * @see https://php-di.org/doc/frameworks/slim.html
 */
$app = \DI\Bridge\Slim\Bridge::create();

$app->get('/', function (Request $request, Response $response){
   $response->getBody()
    ->write('root of app');
   return $response;
});

$app->group('/users', function (RouteCollectorProxy $usersRouteGroup) {
    // get list of all users
    $usersRouteGroup->get('', [App\Controllers\UserController::class, "getList"]);
    // create new user
    $usersRouteGroup->post('', [App\Controllers\UserController::class, "create"]);

    // working with individual users
    $usersRouteGroup->group('/{userId:[0-9]+}', function (RouteCollectorProxy $singleUserRouteGroup) {
        // get user by userId
        $singleUserRouteGroup->get('', [App\Controllers\UserController::class, "get"]);
        // delete user by userId
        $singleUserRouteGroup->delete('', [App\Controllers\UserController::class, "delete"]);

    });
});

$app->run();
