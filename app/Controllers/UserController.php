<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
class UserController
{
    public function getList(Request $request, Response $response)
    {

        $response->withStatus(200)
            ->getBody()
            ->write(json_encode(['data' => ['user1', 'user2']]));

        return $response;
    }

    public function get(Request $request, Response $response, array $args)
    {

        $response->withStatus(200)
            ->getBody()
            ->write(json_encode(['data' => $args]));

        return $response;
    }

    public function create(Request $request, Response $response)
    {

        $response->withStatus(200)
            ->getBody()
            ->write(json_encode(['data' => ['user1', 'user2']]));

        return $response;
    }


    public function delete(Request $request, Response $response, array $args)
    {

        $response->withStatus(200)
            ->getBody()
            ->write(json_encode(['data' => ['user1', 'user2']]));

        return $response;
    }
}