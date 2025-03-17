<?php
namespace App\Controllers;

use App\Models\Citizenship;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CitizenshipController
{
    public function list(Request $request, Response $response): Response
    {
        $users = Citizenship::all();
        $response->getBody()->write(json_encode($users));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function get(Request $request, Response $response, array $args): Response
    {
        $user = Citizenship::find($args['id']);
        $response->getBody()->write(json_encode($user));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        // if (!$data || !isset($data['name']) || empty(trim($data['name']))) {
        //     $response->getBody()->write(json_encode(['error' => 'Name is required']));
        //     return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        // }

        $userId = Citizenship::create($data);
        $response->getBody()->write(json_encode(['id' => $userId]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();
        Citizenship::update($args['id'], $data);
        return $response->withStatus(204);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        Citizenship::delete($args['id']);
        return $response->withStatus(204);
    }
}