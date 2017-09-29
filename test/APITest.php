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
            [
                'method' => 'POST',
                'path' => '/elements/',
                'data' => [
                    'label' => 'mah mah mah form',
                    'bob' => 'bob',
                ]
            ],
        ];

        foreach ($requests as $request) {
            // Issue the request
            $response = $this->doJSONRequest($request['method'], $request['path'], $request['data']);

            // Assert that the status code received is 400
            $this->assertEquals(400, $response->getStatusCode());

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
    public function testCreateForm()
    {
        $requestData = [
            'active' => 'mah mah mah form',
            'type' => 'section-label',
            'label' => 'Mah Section Label',
            'initialValue' => 'some initial value',
            'helpText' => 'some help text',
            'placeholderText' => 'some placeholder text',
            'required' => true,
            'parentId' => null,
        ];

        $request = [
            'method' => 'POST',
            'path' => '/forms/',
            'data' => $requestData,
        ];

        $allParameters = [
            'id', 'href', 'active', 'type', 'label', 'initialValue', 'helpText', 'placeholderText',
            'required', 'parentId', 'parent',
        ];

        // Issue the request
        $response = $this->doJSONRequest($request['method'], $request['path'], $request['data']);

        // Assert that the return code is 200
        $this->assertEquals(200, $response->getStatusCode());

        // Retrieve the response data, assert that it is valid
        $responseData = $this->responseToArray($response);
        $this->assertHasRequiredResponseElements($responseData);

        // Assert that data is an array and has the necessary parameters
        $this->assertInternalType('array', $responseData['data']);
        $this->assertArrayHasKeys($allParameters, $responseData['data']);

        // Assert that the return object has the values we provided
        foreach ($requestData as $key => $value) {
            $this->assertEqual($value, $responseData['data']['key']);
        }

        // Assert that the id is an int
        $this->assertInternalType('int', $responseData['data']['id']);

    }


}
