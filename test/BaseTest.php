<?php

namespace FormsAPI\Test;

require_once __DIR__ . '/../src/setup.php';

use Propel\Runtime\Propel;

use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;

use FormsAPI\App\App;



abstract class BaseTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Slim\App $app */
    protected $app;

    /** @var FormsAPIFaker */
    protected $faker;

    protected $allParameters = [
        'forms' =>
            [
                'id', 'href', 'elements', 'root_element', 'name',
                'slug', 'root_element_id', 'success_message', 'retired'
            ],
        'elements' =>
            [
                'id', 'href', 'retired', 'type', 'label', 'initial_value',
                'help_text', 'placeholder_text', 'required', 'parent_id', 'parent'
            ],
        'visitors' =>
            [
                'id', 'href', 'uw_student_number', 'uw_net_id', 'first_name',
                'middle_name', 'last_name',
            ],
        'choices' =>
            [
                'id', 'href', 'value',
            ],
        'conditions' =>
            [
                'id', 'href', 'operator', 'value',
            ],
        'dependencies' =>
            [
                'id', 'href', 'element', 'slave', 'condition',
            ],
        'requirements' =>
            [
                'id', 'href', 'element', 'condition', 'failure_message',
            ],
        'submissions' =>
            [
                'id', 'href', 'visitor', 'form', 'status', 'assignee', 'parent', 'submitted',
            ],
        'statuses' =>
            [
                'id', 'href', 'name', 'default_message',
            ],
        'tags' =>
            [
                'id', 'href', 'name', 'default_message',
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
                'id', 'href', 'subject', 'recipient', 'sender', 'reply_to', 'cc', 'bcc',
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
                'id', 'href', 'parent_id', 'child_id', 'tag_id', 'parent', 'child', 'tag',
            ],
        'elementchoices' =>
            [
                'id', 'href', 'element_id', 'choice_id', 'element', 'choice',
            ],
        'submissiontags' =>
            [
                'id', 'href', 'submission_id', 'submission', 'tag_id', 'tag',
            ],
        'formstatuses' =>
            [
                'id', 'href', 'form_id', 'form', 'status_id', 'status', 'message',
            ],
        'formtags' =>
            [
                'id', 'href', 'form_id', 'form', 'tag_id', 'tag', 'message',
            ],
        'formreactions' =>
            [
                'id', 'href', 'form_id', 'form', 'reaction_id', 'reaction',
            ],
        'dashboardelements' =>
            [
                'id', 'href', 'dashboard_id', 'dashboard', 'element_id', 'element',
            ],
        'dashboardforms' =>
            [
                'id', 'href', 'dashboard_id', 'dashboard', 'form_id', 'form',
            ],

    ];

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $instance = $this;
        $this->faker = new FormsAPIFaker(
            [
                'reference' => function($resourceType) use ($instance) {
                    $response = $instance->doCreateRequiredOnly($resourceType);
                    $responseData = $instance->responseToArray($response);

                    return $responseData['data']['id'];
                },
            ]
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $con = Propel::getConnection();

        // Run the "initial" Propel sql statement
        $sql = file_get_contents(__DIR__ . '/../schema/generated-sql/default.sql');
        $stmt = $con->exec($sql);
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

    protected function doCreateRequiredOnly($resourceType, $requestData = [])
    {
        $requestData = $this->faker->fakeRequiredOnly($resourceType, $requestData);

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

    protected function assertArrayHasKeys($keys, $array, $additionalMessage=null) {

        foreach ($keys as $key) {
            $this->assertArrayHasKey(
                $key,
                $array,
                "Array must contain key `$key`, but only contains keys: " . implode(', ', array_keys($array)) . " $additionalMessage"
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
