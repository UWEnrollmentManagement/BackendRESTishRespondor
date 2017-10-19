<?php
namespace FormsAPI\App;

require_once __DIR__ . '/../setup.php';
use FormsAPI\Respondor\Respondor;

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

        $app->get('/forms/{id}/elements/', $respondor);

        $app->get('/{resourceType}/', $respondor);
        $app->post('/{resourceType}/', $respondor);
        $app->get('/{resourceType}/{id}/', $respondor);
        $app->patch('/{resourceType}/{id}/', $respondor);
        $app->delete('/{resourceType}/{id}/', $respondor);

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
