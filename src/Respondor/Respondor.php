<?php

namespace UWDOEM\REST\Backend\Respondor;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use UWDOEM\REST\Backend\Mediator\MediatorInterface;

/**
 * Class Respondor
 * @package UWDOEM\REST\Backend\Respondor
 */
class Respondor implements RespondorInterface
{
    /** @var MediatorInterface $mediator  */
    public $mediator;

    /** @var callable[] $extraAttributeProviders */
    protected $extraAttributeProviders;

    /**
     * Respondor constructor.
     * @param MediatorInterface $mediator
     */
    public function __construct(MediatorInterface $mediator)
    {
        $this->mediator = $mediator;
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @return static
     */
    public function __invoke(Request $request, Response $response)
    {

        $parsedBody = $request->getParsedBody();
        $routeInfo = $request->getAttribute('routeInfo')[2];

        // default in case we never set this
        $objectData = null;
        $success = false;
        $error = null;
        $status = 500;
        $reasonPhrase = "Internal Server Error";

        $current = $request->getUri()->getPath();
        if (((string)$request->getUri()->getQuery()) !== '') {
            $current .= $request->getUri()->getQuery();
        }
        $next = null;
        $previous = null;

        $resourceType = $routeInfo['resourceType'];

        $resourceId = null;
        if (array_key_exists('id', $routeInfo) === true) {
            $resourceId = $routeInfo['id'];
        }

        $validResourceType = $this->mediator->resourceTypeExists($resourceType);

        $resource = null;
        if ($validResourceType === true && $resourceId !== null) {
            $resource = $this->mediator->retrieve($resourceType, $resourceId);
        }

        if ($validResourceType === false) {                                           // Invalid resource type
            $status = 404;
            $success = false;
            $error = ['message' => "No such resource type '$resourceType'."];
        } elseif ($resourceId !== null && $resource === null) {                      // Invalid resource id
            $status = 404;
            $success = false;
            $error = ['message' => "No such resource type '$resourceType'" . implode("; ", $this->mediator->error())];
        } elseif ($request->getMethod() === "POST") {                                // CREATE
            $resource = $this->mediator->create($resourceType);
            if ($resourceType === "submissions") {
                $parsedBody["submitted"] = null;
            }

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
        } elseif ($request->getMethod() === "GET" && $resource !== null) {                  // RETRIEVE
            $status = 200;
            $success = true;
            $error = null;

            $objectData = $this->mediator->getAttributes($resource);
        } elseif ($request->getMethod() === "GET" && $resource === null) {                 // LIST

            $collection = $this->mediator->retrieveList($resourceType);
            if ($collection !== null) {
                $status = 200;
                $success = true;
                $error = null;

                $params = $request->getQueryParams();
                $limit = $request->getQueryParam('limit', 100);
                $offset = $request->getQueryParam('offset', 0);

                $filterOperators = $request->getQueryParam('filter_operator', []);
                $filterAttributes = $request->getQueryParam('filter_attribute', []);
                $filterValues = $request->getQueryParam('filter_value', []);

                $params['limit'] = $limit;
                $params['offset'] = $offset;

                if (sizeof($filterOperators) !== sizeof($filterAttributes)
                    || sizeof($filterOperators) !== sizeof($filterValues)) {
                    $status = 400;
                    $success = false;
                    $error = "There must be an equal number of filter operators, attributes, and values. For ".
                        "operators which do not require a value, include a blank value.";
                } elseif (array_diff($filterOperators, MediatorInterface::ALL_CONDS) !== []) {
                    $status = 400;
                    $success = false;
                    $error = "Filter operators must be among [" . implode(', ', MediatorInterface::ALL_CONDS) . '], ' .
                        'but you provided operators [' . implode($filterOperators) . '].';
                } else {
                    foreach ($filterOperators as $key => $operator) {
                        $this->mediator->filter(
                            $collection,
                            $filterAttributes[$key],
                            $filterOperators[$key],
                            $filterValues[$key]
                        );
                    }
                    $this->mediator->limit($collection, $limit)->offset($offset);

                    $objectData = [];
                    foreach ($this->mediator->collectionToIterable($collection) as $resource) {
                        $objectData[] = $this->mediator->getAttributes($resource);
                    }

                    if (sizeof($objectData) === $limit) {
                        $nextParams = $params;
                        $nextParams['offset'] = $offset + $limit;
                        $next = $request->getUri()->getPath() . '?' . http_build_query($nextParams);
                    }

                    if ($offset > 0) {
                        $previousParams = $params;
                        $previousParams['offset'] = max(0, $offset - $limit);
                        $previous = $request->getUri()->getPath() . '?' . http_build_query($previousParams);
                    }
                }
            } else {
                $status = 404;
                $success = false;
                $error = implode("; ", $this->mediator->error());
            }
        } elseif ($request->getMethod() === "DELETE" && $resource !== null) {               // DELETE
            $deletion = $this->mediator->delete($resource);

            if ($deletion === true) {
                $status = 200;
                $success = true;
                $error = null;
            } else {
                $status = 500;
                $success = true;
                $error = ['message' => implode("; ", $this->mediator->error())];
            }
        } elseif ($request->getMethod() === "PATCH" && $resource !== null) {                // UPDATE
            $resource = $this->mediator->setAttributes($resource, $parsedBody);
            $resource = $this->mediator->save($resource);

            $status = 200;
            $success = true;
            $error = null;

            $objectData = $this->mediator->getAttributes($resource);
        }

        $responseContents = [
            "success" => $success,
            "status" => $status,
            "previous" => $previous,
            "current" => $current,
            "next" => $next,
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
