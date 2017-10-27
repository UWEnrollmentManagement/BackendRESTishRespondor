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
}
