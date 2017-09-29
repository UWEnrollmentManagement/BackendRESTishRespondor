<?php
namespace FormsAPI;

require_once __DIR__ . '/../vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$mah_handler = function(Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");

    return $response;
};

class App
{
    /** @var static $instance */
    protected static $instance;

    protected function __construct() { }

    /**
     * @return \Slim\App
     */
    protected function make()
    {
        $app = new \Slim\App;

        global $mah_handler;

        $app->get('/forms/', $mah_handler);
        $app->post('/forms/', $mah_handler);
        $app->get('/forms/{id}/', $mah_handler);
        $app->patch('/forms/{id}/', $mah_handler);
        $app->delete('/forms/{id}/', $mah_handler);
        $app->get('/forms/{id}/elements/', $mah_handler);

        $app->get('/elements/', $mah_handler);
        $app->post('/elements/', $mah_handler);
        $app->get('/elements/{id}/', $mah_handler);
        $app->patch('/elements/{id}/', $mah_handler);
        $app->delete('/elements/{id}/', $mah_handler);

        return $app;
    }

    public static function get()
    {
        if (static::$instance == null) {
            static::$instance = new static();
        }
        return static::$instance->make();
    }
}
