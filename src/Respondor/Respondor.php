<?php

namespace FormsAPI\Respondor;

require_once __DIR__ . '/../setup.php';

use FormsAPI\Mediator\MediatorInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use FormsAPI\Form;
use FormsAPI\Mediator\PropelMediator;

class Respondor
{
    /** @var MediatorInterface PropelMediator  */
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

        $routeInfo = $request->getAttribute('routeInfo')[2];
        $resourceType = $routeInfo['resourceType'];
        $resourceId = null;
        if(array_key_exists('id', $routeInfo)) {
            $resourceId = $routeInfo['id'];
        }

        // We assume that every request coming in is to create a form
        // That means we're assuming that the method is POST and the path is /forms/

        /** CREATE **/
        if ($request->getMethod() === "POST") {

            $resource = $this->mediator->create($resourceType);
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

        /** RETRIEVE **/
        if ($request->getMethod() === "GET" && $resourceId) {

            $resource = $this->mediator->retrieve($resourceType, $resourceId);

            if ($resource) {
                $status = 200;
                $success = true;
                $error = null;

                $objectData = $this->mediator->getAttributes($resource);
            } else {
                $status = 404;
                $success = false;
                $error = implode("; ", $this->mediator->error());
            }
        }

        /** LIST **/
        if ($request->getMethod() === "GET" && !$resourceId) {

            // don't forget to consider pagination and x amount of results per page
            $collection = $this->mediator->retrieveList($resourceType);
            if ($collection) {
                $status = 200;
                $success = true;
                $error = null;

                $objectData = [];

                foreach($this->mediator->collectionToIterable($collection) as $resource) {
                    $objectData[] = $this->mediator->getAttributes($resource);
                }

            } else {
                $status = 404;
                $success = false;
                $error = implode("; ", $this->mediator->error());
            }

        }

        if ($request->getMethod() === "DELETE" && $resourceId) {
            // make a thing, get it to make sure it worked, delete it, try to get it again and assert 404

            $resource = $this->mediator->create($resourceType);
            $this->mediator->setAttributes($resource, $parsedBody);
            $resource = $this->mediator->save($resource);

            if ($resource) {
                $status = 200;
                $success = true;
                $error = null;

                $objectData = $this->mediator->getAttributes($resource);
                $deletion = $this->mediator->delete($resourceType, $objectData[$resourceId]);
                if ($deletion) {
                    $attempt = $this->mediator->retrieve($resourceType, $resourceId);
                    if(!$attempt) {
                        $status = 404;
                    }
                }
            } else {
                $status = 404;
                $success = false;
                $error = implode("; ", $this->mediator->error());
            }
        }

        /** UPDATE **/
        if ($request->getMethod() === "PATCH") {

            // get it and save it again
            $resource = $this->mediator->retrieve($resourceType, $resourceId);
            $setResource = $this->mediator->setAttributes($resource, $parsedBody);
            $resource = $this->mediator->save($setResource);

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