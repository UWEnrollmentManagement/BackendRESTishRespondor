<?php

namespace DeansListAPI\App;
namespace {{ namespace }}\App;

require_once __DIR__ . '/../setup.php';

use UWDOEM\Backend\Mediator\PropelMediator;
use UWDOEM\Backend\Respondor\RESTishRespondor;

use {{ namespace }}\Thing;
use {{ namespace }}\Status;


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

        $classMap = [
            'things' => Thing::class,
            'statuses' => Status::class,
        ];

        $respondor = new RESTishRespondor(new PropelMediator(
                '\\',
                $classMap,
                [
                    'example' => function(array $attributes) {
                        $attributes['phrase'] = "Example {$attributes['id']} is the best example.";
                        return $attributes;
                    }
                ]
            )
        );

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
