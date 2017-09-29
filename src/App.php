<?php
namespace FormsAPI;

require_once __DIR__ . '/../vendor/autoload.php';


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

        $respondor = new Respondor();

        $app->get('/forms/', $respondor);
        $app->post('/forms/', $respondor);
        $app->get('/forms/{id}/', $respondor);
        $app->patch('/forms/{id}/', $respondor);
        $app->delete('/forms/{id}/', $respondor);
        $app->get('/forms/{id}/elements/', $respondor);

        $app->get('/elements/', $respondor);
        $app->post('/elements/', $respondor);
        $app->get('/elements/{id}/', $respondor);
        $app->patch('/elements/{id}/', $respondor);
        $app->delete('/elements/{id}/', $respondor);

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
