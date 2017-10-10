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
    public function testCreateForm($requestData = null)
    {
        if ($requestData == null) {
            $requestData = $this->faker->fake('forms');
        }

        $request = [
            'method' => 'POST',
            'path' => '/forms/',
            'data' => $requestData,
        ];

        $allParameters = [
            'id', 'href', 'elements', 'rootElement', 'name',
            'slug', 'rootElementId', 'successMessage', 'retired'
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
        $this->assertArrayHasKeys($allParameters, $responseData['data']);

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
    public function testGetForms()
    {
        // Create a form to use so that there is a form in the db
        $newForm = $this->testCreateForm();

        // does php or slim automatically url decode the params?
        $requestData = [
            // max number of items to return
            'limit' => 25,
            'start' => 1,
            'filter-field' => ['id1', 'id2'],
            'filter-condition' => ['greater than'],
//            'filter-condition' => ['greater%20than'],
            'filter-criterion' => 'ascending'
        ];

        $request = [
            'method' => 'GET',
            'path' => '/forms/',
            'data' => $requestData
        ];

        $allParameters = [
            'id', 'href', 'elements', 'rootElement', 'name',
            'slug', 'rootElementId', 'successMessage', 'retired'
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
        $this->assertArrayHasKeys($allParameters, $responseData['data']);

        // Assert that the return object has the values we provided
        foreach ($requestData as $key => $value) {
            $this->assertEquals($value, $responseData['data']['key']);
        }

        // Assert that the id is an int
        $this->assertInternalType('int', $responseData['data']['id']);

    }

    /**
     * Client can get a form if they have provided the necessary parameters
     **/
    public function testGetForm()
    {

        // Create a form to use
        $requestData = $this->testCreateForm();

        $request = [
            'method' => 'GET',
            'path' => '/forms/$requestData.data.id',
            'data' => $requestData
        ];

        $allParameters = [
            'id', 'href', 'elements', 'rootElement', 'name',
            'slug', 'rootElementId', 'successMessage', 'retired'
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
        $this->assertArrayHasKeys($allParameters, $responseData['data']);

         //Assert that the return object has the values we provided
        foreach ($requestData as $key => $value) {
            $this->assertEquals($value, $responseData['data']['key']);
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
        $requestData = $this->testCreateForm();

        $request = [
            'method' => 'DELETE',
            'path' => '/forms/$requestData.data.id',
            'data' => $requestData
        ];

        $allParameters = [];

        // Issue the request
        $response = $this->doRequest($request['method'], $request['path'], $request['data']);

        // Assert that the return code is 200
        $this->assertEquals(200, $response->getStatusCode());

        // Retrieve the response data, assert that it is valid
        $responseData = $this->responseToArray($response);
        $this->assertHasRequiredResponseElements($responseData);

        // Assert that data is an array and has the necessary parameters
        $this->assertInternalType('array', $responseData['data']);
        $this->assertArrayHasKeys($allParameters, $responseData['data']);

        //Assert that the return object has the values we provided
        foreach ($requestData as $key => $value) {
            $this->assertEquals($value, $responseData['data']['key']);
        }

        //Assert that the id is an int
        $this->assertInternalType('int', $responseData['data']['id']);

    }

    /**
     * Client can modify/update a form if they have provided the necessary parameters
     **/
    public function testModifyForm() {
        // Create a form to use
        $requestData = $this->testCreateForm();

        $request = [
            'method' => 'PATCH',
            'path' => '/forms/$requestData.data.id',
            'data' => $requestData,
        ];

        $allParameters = [
            'id', 'href', 'elements', 'rootElement', 'name',
            'slug', 'rootElementId', 'successMessage', 'retired'
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
        $this->assertArrayHasKeys($allParameters, $responseData['data']);

        // Assert that the return object has the values we provided
        foreach ($requestData as $key => $value) {
            $this->assertEquals($value, $responseData['data']['key']);
        }

        // Assert that the id is an int
        $this->assertInternalType('int', $responseData['data']['id']);

    }

    /*
     * Elements Tests
     */

    /**
     * A client shall be able to create an element, providing all required parameters.
     */
    public function testCreateElement($requestData = null)
    {
        if ($requestData == null) {
            $requestData = $this->faker->fake('elements');
        }

        $request = [
            'method' => 'POST',
            'path' => '/elements/',
            'data' => $requestData,
        ];

        $allParameters = [
            'id', 'href', 'retired', 'type', 'label', 'initialValue',
            'helpText', 'placeholderText', 'required', 'parentId', 'parent'
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
        $this->assertArrayHasKeys($allParameters, $responseData['data']);

        // Assert that the return object has the values we provided
        foreach ($requestData as $key => $value) {
            $this->assertEquals($value, $responseData['data']['key']);
        }

        // Assert that the id is an int
        $this->assertInternalType('int', $responseData['data']['id']);

    }

    /**
     * A client shall be able to create an element, providing all required parameters.
     */
    public function testGetElements()
    {
        // create an instance to test with
        $newForm = $this-> testCreateElement();

        // does php or slim automatically url decode the params?
        $requestData = [
            // max number of items to return
            'limit' => 25,
            'start' => 1,
            'filter-field' => ['id1', 'id2'],
            'filter-condition' => ['greater than'],
//            'filter-condition' => ['greater%20than'],
            'filter-criterion' => 'ascending'
        ];

        $request = [
            'method' => 'GET',
            'path' => '/elements/$newForm.data.id',
            'data' => $requestData,
        ];

        $allParameters = [
            'id', 'href', 'retired', 'type', 'label', 'initialValue',
            'helpText', 'placeholderText', 'required', 'parentId', 'parent'
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
        $this->assertArrayHasKeys($allParameters, $responseData['data']);

        // Assert that the return object has the values we provided
        foreach ($requestData as $key => $value) {
            $this->assertEquals($value, $responseData['data']['key']);
        }

        // Assert that the id is an int
        $this->assertInternalType('int', $responseData['data']['id']);

    }

    /**
     * Client can get a form if they have provided the necessary parameters
     **/

    public function testGetElement()
    {
        // create an instance to test with
        $requestData = $this-> testCreateElement();

        $request = [
            'method' => 'GET',
            'path' => '/elements/$requestData.data.id',
            'data' => $requestData,
        ];

        $allParameters = [
            'id', 'href', 'retired', 'type', 'label', 'initialValue',
            'helpText', 'placeholderText', 'required', 'parentId', 'parent'
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
        $this->assertArrayHasKeys($allParameters, $responseData['data']);

        // Assert that the return object has the values we provided
        foreach ($requestData as $key => $value) {
            $this->assertEqual($value, $responseData['data']['key']);
        }

        // Assert that the id is an int
        $this->assertInternalType('int', $responseData['data']['id']);

    }

    /**
     * Client can delete a form if they have provided the necessary parameters
     **/
    public function testDeleteElement()
    {

        // create an instance to test with
        $requestData = $this-> testCreateElement();

        $request = [
            'method' => 'DELETE',
            'path' => '/elements/$requestData.element.id',
            'data' => $requestData
        ];

        $allParameters = [];

        // Issue the request
        $response = $this->doRequest($request['method'], $request['path'], $request['data']);

        // Assert that the return code is 200
        $this->assertEquals(200, $response->getStatusCode());

        // Retrieve the response data, assert that it is valid
        $responseData = $this->responseToArray($response);
        $this->assertHasRequiredResponseElements($responseData);

        // Assert that data is an array and has the necessary parameters
        $this->assertInternalType('array', $responseData['data']);
        $this->assertArrayHasKeys($allParameters, $responseData['data']);

        //Assert that the return object has the values we provided
        foreach ($requestData as $key => $value) {
            $this->assertEquals($value, $responseData['data']['key']);
        }

        //Assert that the id is an int
        $this->assertInternalType('int', $responseData['data']['id']);

    }


    /**
     * Client can modify/update a form if they have provided the necessary parameters
     **/
    public function testModifyElement() {

        // create an instance to test with
        $requestData = $this-> testCreateElement();

        $request = [
            'method' => 'PATCH',
            'path' => '/forms/$requestData.data.id',
            'data' => $requestData,
        ];

        $allParameters = [
            'id', 'href', 'retired', 'type', 'label', 'initialValue',
            'helpText', 'placeholderText', 'required', 'parentId', 'parent'
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
        $this->assertArrayHasKeys($allParameters, $responseData['data']);

        // Assert that the return object has the values we provided
        foreach ($requestData as $key => $value) {
            $this->assertEquals($value, $responseData['data']['key']);
        }

        // Assert that the id is an int
        $this->assertInternalType('int', $responseData['data']['id']);

    }


}
