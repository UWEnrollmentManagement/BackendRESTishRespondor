<?php

namespace FormsAPI\Test;

require_once __DIR__ . '/../vendor/autoload.php';

use FormsAPI\App\App;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;



abstract class BaseTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Slim\App $app */
    protected $app;

    /** @var FormsAPIFaker */
    protected $faker;

    protected $allParameters = [
        'forms' =>
            [
                'id', 'href', 'elements', 'rootElement', 'name',
                'slug', 'rootElementId', 'successMessage', 'retired'
            ],
        'elements' =>
            [
                'id', 'href', 'retired', 'type', 'label', 'initialValue',
                'helpText', 'placeholderText', 'required', 'parentId', 'parent'
            ],
        'visitors' =>
            [
                'id', 'href', 'UWStudentNumber', 'UWNetID', 'firstName',
                'middleName', 'lastName',
            ],
        'choices' =>
            [
                'id', 'href', 'value',
            ],
        'dependencies' =>
            [
                'id', 'href', 'element', 'slave', 'condition',
            ],
        'requirements' =>
            [
                'id', 'href', 'element', 'condition', 'failureMessage',
            ],
        'submissions' =>
            [
                'id', 'href', 'visitor', 'form', 'status', 'assignee', 'parent', 'submitted',
            ],
        'statuses' =>
            [
                'id', 'href', 'name', 'defaultMessage',
            ],
        'tags' =>
            [
                'id', 'href', 'name', 'defaultMessage',
            ],
        'notes' =>
            [
                'id', 'href', 'content', 'subject',
            ],
        'recipients' =>
            [
                'id', 'href', 'address', 'note',
            ],
        'stakeholders' =>
            [
                'id', 'href', 'label', 'address', 'form',
            ],
        'reactions' =>
            [
                'id', 'href', 'subject', 'recipient', 'sender', 'replyTo', 'cc', 'bcc',
                'template', 'content',
            ],
        'settings' =>
            [
                'id', 'href', 'key', 'value',
            ],
        'dashboards' =>
            [
                'id', 'href', 'name',
            ],
        'childformrelationships' =>
            [
                'id', 'href',
            ],
        'elementchoices' =>
            [
                'id', 'href',
            ],
        'submissiontags' =>
            [
                'id', 'href',
            ],
        'formtags' =>
            [
                'id', 'href',
            ],
        'formreactions' =>
            [
                'id', 'href',
            ],
        'dashboardelements' =>
            [
                'id', 'href',
            ],
        'dashboardforms' =>
            [
                'id', 'href',
            ],

    ];

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->faker = new FormsAPIFaker();
    }

    protected function doRequest($method, $path, $data = null)
    {
        $app = App::get();

        $vars = [
            'REQUEST_METHOD' => $method,
            'REQUEST_URI' => Uri::createFromString($path),
            'CONTENT_TYPE' => 'application/json;charset=utf8',
        ];

        $env = Environment::mock($vars);
        $request = Request::createFromEnvironment($env);
        
        if($data) {
            $request->getBody()->write(json_encode($data));   
        }

        $app->getContainer()['request'] = $request;

        return $app->run(true);
    }

    protected function doCreate($resourceType, $requestData = [])
    {
        $requestData = $this->faker->fake($resourceType, $requestData);

        // Build the request
        $request = [
            'method' => 'POST',
            'path' => "/$resourceType/",
            'data' => $requestData,
        ];

        // Issue the request
        return $this->doRequest($request['method'], $request['path'], $request['data']);
    }

    protected function responseToArray(Response $response)
    {
        $body = (string)$response->getBody();

        $responseData = json_decode($body, true);
        $this->assertNotNull($responseData, "Response should be valid json. Instead was: " . (string)$body);

        return $responseData;
    }

    protected function assertArrayHasKeys($keys, $array, $message=null) {

        foreach ($keys as $key) {
            $this->assertArrayHasKey(
                $key,
                $array,
                $message ? $message : "Array must contain key `$key`, but only contains keys: " . implode(', ', array_keys($array))
            );
        }
    }

    protected function assertHasRequiredResponseElements($responseData, $message=null)
    {
        $requiredResponseFields = [
            "success",
            "status",
            "time",
            "data",
            "error",
        ];

        $this->assertArrayHasKeys($requiredResponseFields, $responseData, $message);
    }
}
