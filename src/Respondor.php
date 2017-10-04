<?php

namespace FormsAPI;

require_once __DIR__ . '/setup.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


class Respondor
{
    public function __invoke(Request $request, Response $response)
    {

        // Retrieve the response body from the response
        // That's a bunch of json that looks like { name: "Mah form", slug: "my-form", successMessage: "you did it!"}
        // Turn that into an array
         // Then I think that there is a way to feed that array into the new form in one line

        $parsedBody = $request->getParsedBody();

        $form = new Form();
        $form->setName($parsedBody["name"])
            ->setSlug($parsedBody["slug"])
            ->setSuccessMessage($parsedBody["successMessage"]);

        // if the validate method exists, validate before saving
        if(method_exists($form, 'validate')) {
            $form -> validate()
                -> save();
        } else {
           $form -> save();
        }

        $status = 200;

        // toArray() capitalizes the first letter of each element
        // so lowercase the first letter of each element
        $objectData = $form->toArray();
        foreach($objectData as $key => $value) {
            $objectData[lcfirst($key)] = $value;
            unset($objectData[$key]);
        }
        // href and some other keys are generated in the respondor to return
        $objectData['href'] = "sdf";
        $objectData['elements'] = "sdf";
        $objectData['rootElement'] = "sdfds";

        // let time match 2017-10-03T21:16:08.379Z
        $responseContents = [
            "success" => true,
            "status" => $status,
            "data" => $objectData,
            "time" => date("Y-m-dH:i:s"),
            "error" => null
        ];

        $responseContents = json_encode($responseContents);
        $response->getBody()->write($responseContents);
        return $response->withStatus($status, 'OK');
    }
}