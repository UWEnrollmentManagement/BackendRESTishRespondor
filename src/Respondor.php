<?php

namespace FormsAPI;

require_once __DIR__ . '/setup.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


class Respondor
{
    public function __invoke(Request $request, Response $response)
    {

        $form = new Form();

        $form->setName("My Form")
            ->setSlug("my-form")
            ->setSuccessMessage("<h1>You did it!</h1><p>Thanks</p>")
            ->save();

        $status = 200;

        $responseContents = [
            "success" => true,
            "status" => $status,
            "data" => $form->toArray(),
        ];

        $response->getBody()->write(json_encode($responseContents));

        return $response->withStatus($status, 'OK');
    }
}