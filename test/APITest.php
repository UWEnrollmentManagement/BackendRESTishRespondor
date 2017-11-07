<?php

namespace FormsAPI\Test;

require_once __DIR__  . '/../vendor/autoload.php';

use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;


class APITest extends BaseTest
{

    /**
     * Various endpoints shall produce a 400 response when called with incorrect
     * parameters.
     */
    public function test400()
    {
        $requests = [
            [
                'method' => 'POST',
                'path' => '/forms/',
                'data' => [
                    'name' => 'mah mah mah form',
                    'bob' => 'bob',
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
     * A client shall be able to create a form, providing all required parameters.
     */
    public function testCreateForm($requestData = [])
    {
        $requestData = $this->faker->fake("forms", $requestData);
        $response = $this->doCreate('forms', $requestData);

        // Assert that the return code is 200
        $this->assertEquals(200, $response->getStatusCode());

        // Retrieve the response data, assert that it is valid
        $responseData = $this->responseToArray($response);
        $this->assertHasRequiredResponseElements($responseData);

        // Assert that data is an array and has the necessary parameters
        $this->assertInternalType('array', $responseData['data']);
        $this->assertArrayHasKeys($this->allParameters['forms'], $responseData['data']);

        // Assert that the return object has the values we provided
        foreach ($requestData as $key => $value) {
            $this->assertEquals($value, $responseData['data'][$key]);
        }

        // Assert that the id is an int
        $this->assertInternalType('int', $responseData['data']['id']);

        return $responseData['data'];
    }

    /**
     * Client can get a list of the forms if they have provided the necessary parameters
     **/
    public function testListForms()
    {
        // Make some forms so we can return more than one
        $createResponseData = [];
        for($i = 0; $i < 5; $i++) {
            $responseData = $this->testCreateForm();
            $createResponseData[$responseData['id']] = $responseData;
        }

        $request = [
            'method' => 'GET',
            'path' => '/forms/'
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

        foreach ($responseData['data'] as $formData) {
            $this->assertArrayHasKeys($this->allParameters['forms'], $formData);

            // Assert that the id is an int
            $this->assertInternalType('int', $formData['id']);

            // Note this form's id
            $formId = $formData['id'];

            // Assert that the return object has the values we provided
            foreach ($createResponseData[$formId] as $key => $value) {
                $this->assertEquals(
                    $value,
                    $formData[$key],
                    "Comparing form {$formData['id']} on key $key"
                );
            }
        }

    }

    /**
     * Client can get a form if they have provided the necessary parameters
     **/
    public function testGetForm()
    {

        // Create a form to use
        $createResponseData = $this->testCreateForm();
        $request = [
            'method' => 'GET',
            'path' => "/forms/{$createResponseData['id']}/"
        ];
        // Issue the request
        $response = $this->doRequest($request['method'], $request['path']);

        // Assert that the return code is 200
        $this->assertEquals(200, $response->getStatusCode());

        // Retrieve the response data, assert that it is valid
        $responseData = $this->responseToArray($response);
        $this->assertHasRequiredResponseElements($responseData);

        // Assert that data is an array and has the necessary parameters
        $this->assertInternalType('array', $responseData['data']);
        $this->assertArrayHasKeys($this->allParameters['forms'], $responseData['data']);

         //Assert that the return object has the values we provided
        foreach ($createResponseData as $key => $value) {
            $this->assertEquals($value, $responseData['data'][$key]);
        }

         //Assert that the id is an int
        $this->assertInternalType('int', $responseData['data']['id']);

    }

    /**
     * Client can delete a form if they have provided the necessary parameters
     **/
    public function testDeleteForm()
    {

        // Create a form to use
        $requestData = $this->testGetForm();

        $request = [
            'method' => 'DELETE',
            'path' => "/forms/{$requestData['id']}/"
        ];

        // Issue the request
        $response = $this->doRequest($request['method'], $request['path']);

        // Assert that the return code is 200
//        $this->assertEquals(200, $response->getStatusCode());

        // attempt to grab it to make sure it is dead
        $request['method'] = 'GET';
        $pulseCheck = $this->doRequest($request['method'], $request['path']);

        // Assert that the return code is 404
        $this->assertEquals(404, $response->getStatusCode());

    }

    /**
     * Client can modify/update a form if they have provided the necessary parameters
     **/
    public function testModifyForm() {
        // Create a form to use
        $requestData = $this->testCreateForm();

        $request = [
            'method' => 'PATCH',
            'path' => "/forms/{$requestData['id']}/",
            'data' => $requestData,
        ];

        // Issue the request
        $response = $this->doRequest($request['method'], $request['path'], $request['data']);

        // Assert that the return code is 200
        $this->assertEquals(200, $response->getStatusCode());

        // Retrieve the response data, assert that it is valid
        $responseData = $this->responseToArray($response);
        $this->assertHasRequiredResponseElements($responseData);

        // Assert that data is an array and has the necessary parameters
        $this->assertInternalType('array', $responseData['data']);
        $this->assertArrayHasKeys($this->allParameters['forms'], $responseData['data']);

        // Assert that the return object has the values we provided
        foreach ($requestData as $key => $value) {
            $this->assertEquals($value, $responseData['data'][$key]);
        }

        // Assert that the id is an int
        $this->assertInternalType('int', $responseData['data']['id']);

    }

    /**
     * A client shall be able to create all other resource types, in addition to
     * forms.
     */
    public function testCreateAllElse($requestData = [])
    {
        foreach ($this->allParameters as $resourceType => $allParameters) {
//            die(var_dump($resourceType));
            // forms
            $response = $this->doCreate($resourceType, $requestData);

            // Assert that the return code is 200
            if($response->getStatusCode() != "200") {
                var_dump($response);
                var_dump($resourceType);
            }
            $this->assertEquals(200, $response->getStatusCode());

            // Retrieve the response data, assert that it is valid
            $responseData = $this->responseToArray($response);
            $this->assertHasRequiredResponseElements($responseData);

            // Assert that data is an array and has the necessary parameters
            $this->assertInternalType('array', $responseData['data']);
            $this->assertArrayHasKeys($allParameters, $responseData['data']);

            // Assert that the return object has the values we provided
            foreach ($requestData as $key => $value) {
                $this->assertEquals($value, $responseData['data']['key']);
            }

            // Assert that the id is an int
            $this->assertInternalType('int', $responseData['data']['id']);
        }
    }
}
