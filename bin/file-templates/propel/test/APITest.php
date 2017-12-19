<?php

namespace {{ namespace }}\Test;

require_once __DIR__  . '/../src/setup.php';

use {{ namespace }}\App\App;

use Propel\Runtime\Propel;

use UWDOEM\Backend\Test\BaseTest;
use UWDOEM\Backend\Test\APIFaker;


class APITest extends BaseTest
{
    /**
     * @var array $fieldsMap
     *
     * As you adjust your schema.xml, you'll need to adjust this $fieldsMap array
     */
    protected $fieldsMap = [
        'statuses' => [
            'required' => [
                'name' => 'catchPhrase',
            ],
            'optional' => [],
            'readonly' => ['href'],
        ],
        'things' => [
            'required' => [
                'name' => 'catchPhrase',
            ],
            'optional' => [
                'active' => 'boolean',
                'status_id' => ['reference', 'statuses'],
            ],
            'readonly' => ['href', 'status'],
        ],
    ];

    public function __construct($appClass = null, $faker = null, $name = null, array $data = [], $dataName = '')
    {
        if ($appClass === null) {
            $appClass = App::class;
        }

        if ($faker === null) {
            $instance = $this;


            $faker = new APIFaker(
                $this->fieldsMap,
                [
                    'reference' => function($resourceType) use ($instance) {
                        $response = $instance->doCreateRequiredOnly($resourceType);
                        $responseData = $instance->responseToArray($response);

                        return $responseData['data']['id'];
                    },
                ]
            );
        }

        parent::__construct($appClass, $faker, $name, $data, $dataName);
    }

    protected function setUp()
    {
        parent::setUp();

        $con = Propel::getConnection();

        // Run the "initial" Propel sql statement
        $sql = file_get_contents(__DIR__ . '/../schema/generated-sql/default.sql');
        $stmt = $con->exec($sql);
    }

    /**
     * Various endpoints shall produce a 400 response when called with incorrect
     * parameters.
     */
    public function test400()
    {
        $resourceType = array_keys($this->fieldsMap)[0];
        $requests = [
            [
                'method' => 'POST',
                'path' => "/$resourceType/",
                'data' => [
                    'some_field_that_doesnt_exist' => false,
                ]
            ],
        ];

        foreach ($requests as $request) {
            // Issue the request
            $response = $this->doRequest($request['method'], $request['path'], $request['data']);

            // Assert that the status code received is 400
            $this->assertEquals(400, $response->getStatusCode(), "Failed on path {$request['path']} With method: {$request['method']}");

            // Retrieve the response data, assert that it is valid
            $responseData = $this->responseToArray($response);
            $this->assertHasRequiredResponseElements($responseData);


            // Assert that the response values are conformant to a 400 error
            $this->assertEquals(400, $responseData['status']);
            $this->assertFalse($responseData['success']);
            $this->assertNull($responseData['data']);
            $this->assertArrayHasKey('message', $responseData['error']);
        }
    }

    /**
     * Trying to GET, PATCH, or DELETE a form that does not exist should return a 404 error
     */
    public function test404Form()
    {
        foreach (['GET', 'DELETE', 'PATCH'] as $method) {

            // Hopefully, no form exists with this id.
            $formId = 9999999387;

            // {$method} form {$id}, which doesn't exist (I hope!)
            $request = [
                'method' => $method,
                'path' => "/forms/$formId/"
            ];
            $response = $this->doRequest($request['method'], $request['path']);

            // Assert that the return code is 404 and that the response is a valid error response
            $responseData = $this->responseToArray($response, "Trying to $method form $formId.");
            $this->assertEquals(404, $response->getStatusCode(), "Trying to $method form $formId.");
            $this->assertHasRequiredResponseElements($responseData, "Trying to $method form $formId.");
            $this->assertFalse($responseData['success'], "Trying to $method form $formId.");
            $this->assertNull($responseData['data'], "Trying to $method form $formId.");
            $this->assertInternalType('array', $responseData['error'], "Trying to $method form $formId.");
            $this->assertArrayHasKey('message', $responseData['error'], "Trying to $method form $formId.");
        }
    }

    /**
     * Trying to POST resource type that does not exist or trying to GET, PATCH, or
     * DELETE a specific resource with a type that does not exist should return a
     * 404 error.
     */
    public function test404Resource()
    {
        // This server does not have resources of type {$resourceType}, so we should get
        // 404 responses for all requests to this resource type.
        $resourceType = 'foo';
        
        // Try to create a {$resourceType}
        $request = [
            'method' => 'POST',
            'path' => "/$resourceType/"
        ];
        $response = $this->doRequest($request['method'], $request['path']);

        // Assert that the return code is 404 and that the response is a valid error response
        $responseData = $this->responseToArray($response);
        $this->assertEquals(404, $response->getStatusCode(), "Trying to create a $resourceType.");
        $this->assertHasRequiredResponseElements($responseData, "Trying to create a $resourceType.");
        $this->assertFalse($responseData['success'], "Trying to create a $resourceType.");
        $this->assertNull($responseData['data'], "Trying to create a $resourceType.");
        $this->assertInternalType('array', $responseData['error']);
        $this->assertArrayHasKey('message', $responseData['error'], "Trying to create a $resourceType.");

        foreach (['GET', 'DELETE', 'PATCH'] as $method) {

            // No {$resourceType} exists with this id because this server does not have
            // resources of type {$resourceType}.
            $resourceId = 1;

            // {$method} {$resourceType} {$id}
            $request = [
                'method' => $method,
                'path' => "/forms/$resourceId/"
            ];
            $response = $this->doRequest($request['method'], $request['path']);

            // Assert that the return code is 404 and that the response is a valid error response
            $responseData = $this->responseToArray($response);
            $this->assertEquals(404, $response->getStatusCode(), "Trying to $method $resourceType $resourceId.");
            $this->assertHasRequiredResponseElements($responseData, "Trying to $method $resourceType $resourceId.");
            $this->assertFalse($responseData['success'], "Trying to $method $resourceType $resourceId.");
            $this->assertNull($responseData['data'], "Trying to $method $resourceType $resourceId.");
            $this->assertInternalType('array', $responseData['error']);
            $this->assertArrayHasKey('message', $responseData['error'], "Trying to $method $resourceType $resourceId.");
        }


    }

    /**
     * A client shall be able to create all resource types described in $fieldsMaps
     */
    public function testCreateAllElse()
    {
        foreach ($this->fieldsMap as $resourceType => $parameters) {
            $allParameters = array_merge(
                $parameters['readonly'],
                array_keys($parameters['required']),
                array_keys($parameters['optional'])
            );

            $response = $this->doCreate($resourceType);

            // Assert that the return code is 200
            $this->assertEquals(200, $response->getStatusCode(), "$resourceType");

            // Retrieve the response data, assert that it is valid
            $responseData = $this->responseToArray($response);

            $this->assertHasRequiredResponseElements($responseData);

            // Assert that data is an array and has the necessary parameters
            $this->assertInternalType('array', $responseData['data']);
            $this->assertArrayHasKeys($allParameters, $responseData['data'], "For resource type $resourceType.");

            // Assert that the id is an int
            $this->assertInternalType('int', $responseData['data']['id']);
        }
    }

    /**
     * A client should be able to delete all resource types described in $fieldsmaps
     */
    public function testDeleteAllElse()
    {
        foreach ($this->fieldsMap as $resourceType => $parameters) {
            $response = $this->doCreate($resourceType);

            // Create the resource
            $this->assertEquals(200, $response->getStatusCode(), "$resourceType");
            $responseData = $this->responseToArray($response);

            $request = [
                'method' => 'DELETE',
                'path' => "/$resourceType/{$responseData['data']['id']}/"
            ];

            // Issue the request
            $response = $this->doRequest($request['method'], $request['path']);
            $this->assertEquals(200, $response->getStatusCode());

            // attempt to grab it to make sure it is dead
            $request['method'] = 'GET';
            $response = $this->doRequest($request['method'], $request['path']);

            // Assert that the return code is 404
            $this->assertEquals(404, $response->getStatusCode(), "For resource type $resourceType.");
        }
    }

    /**
     * A client shall be able to create all other resource types, in addition to
     * forms.
     */
    public function testGetAllElse()
    {
        foreach ($this->fieldsMap as $resourceType => $parameters) {
            // Create a resource of the given type
            $createResponse = $this->doCreate($resourceType);

            // Assert that the return code is 200
            $this->assertEquals(200, $createResponse->getStatusCode(), "$resourceType");

            // Retrieve the response data
            $createResponseData = $this->responseToArray($createResponse);

            // Assert that the id is an int
            $this->assertInternalType('int', $createResponseData['data']['id']);

            $request = [
                'method' => 'GET',
                'path' => "/$resourceType/{$createResponseData['data']['id']}/"
            ];

            // Issue the request
            $retrieveResponse = $this->doRequest($request['method'], $request['path']);
            $this->assertEquals(200, $retrieveResponse->getStatusCode());
            $retrieveResponseData = $this->responseToArray($retrieveResponse);

            // Assert that each attribute has the same value in the create response as
            // the retrieve response
            foreach ($createResponseData['data'] as $key => $value) {
                $this->assertEquals($value, $retrieveResponseData['data'][$key]);
            }
        }
    }

    /**
     * Client can get a list of all resources described in $fieldsmap
     **/
    public function testListAllElse()
    {
        foreach ($this->fieldsMap as $resourceType => $parameters) {
            $allParameters = array_merge(
                $parameters['readonly'],
                array_keys($parameters['required']),
                array_keys($parameters['optional'])
            );

            // Make some resources so we can return more than one
            $createResponseData = [];
            for($i = 0; $i < 5; $i++) {
                $response = $this->doCreate($resourceType);
                $responseData = $this->responseToArray($response)['data'];
                $createResponseData[$responseData['id']] = $responseData;
            }

            $request = [
                'method' => 'GET',
                'path' => "/$resourceType/"
            ];

            // Issue the request
            $response = $this->doRequest($request['method'], $request['path']);

            // Assert that the return code is 200
            $this->assertEquals(200, $response->getStatusCode());

            // Retrieve the response data, assert that it is valid
            $responseData = $this->responseToArray($response);
            $this->assertHasRequiredResponseElements($responseData);

            // Assert that data is an array
            $this->assertInternalType('array', $responseData['data']);

            $retrieveResponseData = [];
            foreach ($responseData['data'] as $resourceData) {
                $retrieveResponseData[$resourceData['id']] = $resourceData;
            }

            foreach ($createResponseData as $resourceId => $createResource) {
                $this->assertArrayHasKey($resourceId, $retrieveResponseData);

                $retrieveResource = $retrieveResponseData[$resourceId];

                $this->assertArrayHasKeys($allParameters, $retrieveResource);

                // Assert that the return object has the values we provided
                foreach ($createResource as $key => $value) {
                    $this->assertEquals(
                        $value,
                        $retrieveResource[$key],
                        "Comparing resource $resourceType $resourceId on key $key"
                    );
                }
            }

        }
    }

    /**
     * A client shall be able to update all resource types described in $fieldsMap
     * forms.
     */
    public function testModifyAllElse()
    {
        foreach ($this->fieldsMap as $resourceType => $parameters) {
            // Create a resource of the given type
            $createResponse = $this->doCreate($resourceType);

            // Assert that the return code is 200
            $this->assertEquals(200, $createResponse->getStatusCode(), "$resourceType");

            // Retrieve the response data
            $createResponseData = $this->responseToArray($createResponse);

            // Assert that the id is an int
            $this->assertInternalType('int', $createResponseData['data']['id']);

            $request = [
                'method' => 'PATCH',
                'path' => "/$resourceType/{$createResponseData['data']['id']}/",
                'data' => $this->faker->fake($resourceType),
            ];

            // Issue the request
            $response = $this->doRequest($request['method'], $request['path'], $request['data']);
            $this->assertEquals(200, $response->getStatusCode());
            $responseData = $this->responseToArray($response);

            // Assert that each attribute has the same value in the create response as
            // the retrieve response
            foreach ($request['data'] as $key => $value) {
                $this->assertEquals($value, $responseData['data'][$key], "For resource type $resourceType and key $key.");
            }
        }
    }

    /**
     * Client can get a list of the forms, with pagination
     **/
    public function testPagination()
    {
        $numForms = 10;
        $formsPerPage = 3;
        $resourceType = array_keys($this->fieldsMap)[0];

        // Make some forms so we can return more than one
        $createResponseData = [];
        for($i = 0; $i < $numForms; $i++) {
            $response = $this->doCreate($resourceType);
            $responseData = $this->responseToArray($response)['data'];
            $createResponseData[$responseData['id']] = $responseData;
        }

        $request = [
            'method' => 'GET',
            'path' => "/$resourceType/",
            'query' => ['limit' => $formsPerPage],
        ];

        // Issue the request
        $response = $this->doRequest($request['method'], $request['path'] . '?' . http_build_query($request['query']));

        // Assert that the return code is 200
        $this->assertEquals(200, $response->getStatusCode());

        // Retrieve the response data, assert that it is valid
        $responseData = $this->responseToArray($response);
        $this->assertHasRequiredResponseElements($responseData);

        // Assert that data is an array
        $this->assertInternalType('array', $responseData['data']);

        // Assert that the appropriate number of items have been returned
        $this->assertEquals($formsPerPage, sizeof($responseData['data']));

        // Assert that 'current' includes our current page
        $this->assertContains($request['path'], $responseData['current']);

        // Previous should be null, we are on the first page
        $this->assertNull($responseData['previous']);

        // Next should contain an href to the next page of results
        $this->assertNotNull($responseData['next']);
        $this->assertContains("/$resourceType/", $responseData['next']);
        $this->assertContains("limit=$formsPerPage", $responseData['next']);
        $this->assertContains("offset=$formsPerPage", $responseData['next']);

        // Begin accumulating all of the form data returned by these responses
        $retrieveResponseData = [];
        foreach ($responseData['data'] as $responseDatum) {
            $retrieveResponseData[$responseDatum['id']] = $responseDatum;
        }

        $pageFollows = 0;
        while($responseData['next'] !== null) {
            $request = [
                'method' => 'GET',
                'path' => $responseData['next']
            ];

            // Issue the request
            $response = $this->doRequest($request['method'], $request['path']);

            // Assert that the return code is 200
            $this->assertEquals(200, $response->getStatusCode());

            // Retrieve the response data, accumulate the results
            $responseData = $this->responseToArray($response);
            foreach ($responseData['data'] as $responseDatum) {
                $retrieveResponseData[$responseDatum['id']] = $responseDatum;
            }

            $pageFollows++;
            $this->assertLessThan(200, $pageFollows, 'Form pagination never gave a null next.');
        }

        // Assert that we retrieved all of the forms
        $this->assertEquals($numForms, sizeof($retrieveResponseData));
        $this->assertEquals($createResponseData, $retrieveResponseData);

        // Test that "previous" is generated correctly
        $request = [
            'method' => 'GET',
            'path' => "/$resourceType/?limit=$formsPerPage&offset=$formsPerPage"
        ];
        $response = $this->doRequest($request['method'], $request['path']);
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = $this->responseToArray($response);

        $this->assertContains("/$resourceType/", $responseData['previous']);
        $this->assertContains("limit=$formsPerPage", $responseData['previous']);
        $this->assertContains("offset=0", $responseData['previous']);

    }


    /**
     * Client can filter their list results using query variables
     *
     * This test depends on a resource type with a varchar attribute. As you change
     * your schema, you might need to change this test, specifically the $resourceType
     * and $attribute.
     **/
    public function testQueryFilter()
    {
        $resourceType = 'things';
        $attribute = 'name';

        $responseContents = [
            0, 1, 2, 3, 4, 5, "bob", "bobby", "Bob", "Sally"
        ];

        // Make some forms so we can return more than one
        $createResponseData = [];
        foreach($responseContents as $responseContent) {
            $response = $this->doCreate($resourceType, [$attribute => $responseContent]);
            $responseData = $this->responseToArray($response);
            $createResponseData[$responseData['data']['id']] = $responseData;
        }

        $request = [
            'method' => 'GET',
            'path' => "/$resourceType/",
            'data' => null,
            'query' => [
                'filter_attribute' => [$attribute],
                'filter_operator' => ['='],
                'filter_value' => ['2'],
            ]
        ];

        // Issue the request
        $response = $this->doRequest($request['method'], $request['path'] . '?' . http_build_query($request['query']));

        // Assert that the return code is 200
        $this->assertEquals(200, $response->getStatusCode());

        // Retrieve the response data, assert that it is valid
        $responseData = $this->responseToArray($response);
        $this->assertHasRequiredResponseElements($responseData);

        // Assert that data is an array
        $this->assertInternalType('array', $responseData['data']);

        // Assert that 1 element has been returned
        $this->assertEquals(1, sizeof($responseData['data']));


        $request = [
            'method' => 'GET',
            'path' => "/$resourceType/",
            'data' => null,
            'query' => [
                'filter_attribute' => [$attribute],
                'filter_operator' => ['LIKE'],
                'filter_value' => ['%bob%'],
            ]
        ];

        // Issue the request
        $response = $this->doRequest($request['method'], $request['path'] . '?' . http_build_query($request['query']));

        // Assert that the return code is 200
        $this->assertEquals(200, $response->getStatusCode());

        // Retrieve the response data, assert that it is valid
        $responseData = $this->responseToArray($response);
        $this->assertHasRequiredResponseElements($responseData);

        // Assert that data is an array
        $this->assertInternalType('array', $responseData['data']);

        // Assert that 1 element has been returned
        $this->assertEquals(3, sizeof($responseData['data']));

        // Issue a request with a bad operator
        $request = [
            'method' => 'GET',
            'path' => "/$resourceType/",
            'data' => null,
            'query' => [
                'filter_attribute' => [$attribute],
                'filter_operator' => ['BADOPERATOR'],
                'filter_value' => ['%bob%'],
            ]
        ];

        // Issue the request
        $response = $this->doRequest($request['method'], $request['path'] . '?' . http_build_query($request['query']));

        // Assert that the return code is 200
        $this->assertEquals(400, $response->getStatusCode());
    }
}
