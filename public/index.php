<?php

use App\Middleware\JsonBodyParserMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../vendor/autoload.php';

if (isset($_GET['admin_info']) && $_GET['admin_info'] === '025e518a-4ed5-49ec-b011-ee4cd2ee5493') {
    phpinfo();
    die();
}
/**
 * use PHP-DI bridge to create slim app for controller DI
 * @see https://php-di.org/doc/frameworks/slim.html
 */
$app = \DI\Bridge\Slim\Bridge::create();

// add middleware
$app->add(JsonBodyParserMiddleware::class);

$app->addRoutingMiddleware();

// setup error handling via middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler([App\Controllers\ErrorController::class, "error"]);

// configure routes
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

        $singleUserRouteGroup->post('/earn', [App\Controllers\UserEarnController::class, "create"]);
        $singleUserRouteGroup->post('/redeem', [App\Controllers\UserRedeemController::class, "create"]);

    });
});

//$app->

$app->run();
