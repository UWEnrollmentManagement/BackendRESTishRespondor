<?php

namespace FormsAPI\Respondor;

require_once __DIR__ . '/../setup.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use FormsAPI\Form;
use FormsAPI\Mediator\PropelMediator;

class Respondor
{
    public $mediator;

    public function __construct()
    {
        $this->mediator = new PropelMediator("/");
    }

    public function __invoke(Request $request, Response $response)
    {

        // Retrieve the response body from the response
        // That's a bunch of json that looks like { name: "Mah form", slug: "my-form", successMessage: "you did it!"}
        // Turn that into an array
         // Then I think that there is a way to feed that array into the new form in one line

        $parsedBody = $request->getParsedBody();

        $objectData = null;
        // href and some other keys are generated in the respondor to return
        $success = false;
        $error = null;
        $status = 500;


        // We assume that every request coming in is to create a form
        // That means we're assuming that the method is POST and the path is /forms/

        // in request v

        // if method === POST and path === form
        if ($request.method === "POST") {
            $resource = $this->mediator->create(trim($request.path, "/"));

            // If $parsedBody is not already an array, then we will need
            // to make it one first
            $this->mediator->setAttributes($resource, $parsedBody);
            $resource = $this->mediator->save($resource);

            if ($resource !== false) {
                $status = 200;
                $success = true;
                $error = null;

                $objectData = $this->mediator->getAttributes($resource);
            } else {
                $status = 400;
                $success = false;
                $error = implode("; ", $this->mediator->error());
            }
        }

        if ($request.method === "GET") {

            //check if the path has an id? If not, it's asking for a list so do something else
//            $resource = $this->mediator->create(trim($request.path, "/"));
            // parse anything after /elements/ or /forms/ to check if there is an id
            if($request.id) {
                $resource = $this->mediator->retrieve($request.id);
            }

            if ($resource) {
                $status = 200;
                $success = true;
                $error = null;

                $objectData = $this->mediator->getAttributes($resource);
            } else {
                $status = 400;
                $success = false;
                $error = implode("; ", $this->mediator->error());
            }
        }

        if ($request.method === "DELETE") {

            //check if the path has an id? If not, it's asking for a list so do something else
//            $resource = $this->mediator->create(trim($request.path, "/"));
            // parse anything after /elements/ or /forms/ to check if there is an id
            if($request.id) {
                $resource = $this->mediator->retrieve($request.id);
            }

            if ($resource) {
                $status = 200;
                $success = true;
                $error = null;

                $objectData = $this->mediator->delete($resource);
            } else {
                $status = 400;
                $success = false;
                $error = implode("; ", $this->mediator->error());
            }
        }

        if ($request.method === "UPDATE") {

            // get it and save it again
            if($request.id) {
                $resource = $this->mediator->retrieve($request.id);
                $setResource = $resource->mediator->setAttributes($resource, $parsedBody);
                $resource = $this->mediator->save($setResource);
            }

            if ($resource) {
                $status = 200;
                $success = true;
                $error = null;

                $objectData = $this->mediator->getAttributes($resource);
            } else {
                $status = 400;
                $success = false;
                $error = implode("; ", $this->mediator->error());
            }
        }


        // let time match 2017-10-03T21:16:08.379Z
        $responseContents = [
            "success" => $success,
            "status" => $status,
            "data" => $objectData,
            "time" => date("Y-m-dH:i:s"),
            "error" => $error
        ];

        $responseContents = json_encode($responseContents);
        $response->getBody()->write($responseContents);
        return $response->withStatus($status, 'OK');
    }
}