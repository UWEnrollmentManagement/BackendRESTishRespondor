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
        // default in case we never set this
        $reasonPhrase = "Internal Server Error";

        $routeInfo = $request->getAttribute('routeInfo')[2];
        $resourceType = $routeInfo['resourceType'];
        $resourceId = null;

        $resourceId = array_key_exists('id', $routeInfo) ? $routeInfo['id'] : null;

        $validResourceType = $this->mediator->resourceTypeExists($resourceType);

        $resource = $validResourceType && $resourceId ? $this->mediator->retrieve($resourceType, $resourceId) : null;

        if ($validResourceType === false) {                                           // Invalid resource type
            $status = 404;
            $success = false;
            $error = ['message' => "No such resource type '$resourceType'."];
        } elseif ($resourceId && !$resource) {                                       // Invalid resource id
            $status = 404;
            $success = false;
            $error = ['message' => "No such resource type '$resourceType'" . implode("; ", $this->mediator->error())];
        } elseif ($request->getMethod() === "POST") {                                // CREATE
            $resource = $this->mediator->create($resourceType);

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
                $error = ['message' => implode("; ", $this->mediator->error())];
            }
        } elseif ($request->getMethod() === "GET" && $resource) {                  // RETRIEVE
            $status = 200;
            $success = true;
            $error = null;

            $objectData = $this->mediator->getAttributes($resource);
        } elseif ($request->getMethod() === "GET" && !$resource) {                 // LIST

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

        } elseif ($request->getMethod() === "DELETE" && $resource) {               // DELETE
            $deletion = $this->mediator->delete($resource);

            if ($deletion) {
                $status = 200;
                $success = true;
                $error = null;
            } else {
                $status = 500;
                $success = true;
                $error = ['message' => implode("; ", $this->mediator->error())];
            }
        } elseif ($request->getMethod() === "PATCH" && $resource) {                // UPDATE
            $resource = $this->mediator->setAttributes($resource, $parsedBody);
            $resource = $this->mediator->save($resource);

            $status = 200;
            $success = true;
            $error = null;

            $objectData = $this->mediator->getAttributes($resource);

        }


        // let time match 2017-10-03T21:16:08.379Z
        $responseContents = [
            "success" => $success,
            "status" => $status,
            "data" => $objectData,
            "time" => date("Y-m-dH:i:s"),
            "error" => $error
        ];

        $statusMap = [
            200 => "OK",
            400 => "Bad Request",
            404 => "Not Found",
            500 => "Internal Server Error"
        ];

        $reasonPhrase = $statusMap[$status];
        $responseContents = json_encode($responseContents);
        $response->getBody()->write($responseContents);
        return $response->withStatus($status, $reasonPhrase);
    }
}