<?php

namespace FormsAPI;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


class Respondor
{
    public function __invoke(Request $request, Response $response)
    {
        $response->getBody()->write('You have attempted to brew coffee at this endpoint, yet I am a teapot.');

        return $response->withStatus(418, 'I\'m a teapot');
    }
}