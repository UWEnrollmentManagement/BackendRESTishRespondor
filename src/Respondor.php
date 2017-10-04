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
        $responseBody = $response;

        // That's a bunch of json that looks like { name: "Mah form", slug: "my-form", successMessage: "you did it!"}

        // Turn that into an array

        // Then I think that there is a way to feed that array into the new form in one line
        $form = new Form();

        // toArray() capitalizes the first letter of each element
        // so lowercase the first letter of each element
        $objectData = $form->toArray();
        foreach($objectData as $key => $value) {
            $objectData[lcfirst($key)] = $value;
            unset($objectData[$key]);
        }


        $form->setName($objectData[0])
            ->setSlug($objectData[1])
            ->setSuccessMessage("success");
//            ->setSuccessMessage("<h1>You did it!</h1><p>Thanks</p>");

        if(method_exists($form, 'validate')) {
            $form -> validate()
                -> save();
        } else {
           $form -> save();
        }

        $status = 200;

        // href and some other keys are generated in the respondor to return
        $objectData['href'] = "sdf";
        $objectData['elements'] = "sdf";
        $objectData['rootElement'] = "sdfds";

        $responseContents = [
            "success" => true,
            "status" => $status,
            "data" => $objectData,
            // 2017-10-03T21:16:08.379Z
            "time" => date("Y-m-dH:i:s"),
            "error" => null
        ];

        $responseContents = json_encode($responseContents);
        $response->getBody()->write($responseContents);
        return $response->withStatus($status, 'OK');
    }
}