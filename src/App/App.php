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

        $app->get('/choices/', $respondor);
        $app->post('/choices/', $respondor);
        $app->get('/choices/{id}/', $respondor);
        $app->patch('/choices/{id}/', $respondor);
        $app->delete('/choices/{id}/', $respondor);

        $app->get('/dependencies/', $respondor);
        $app->post('/dependencies/', $respondor);
        $app->get('/dependencies/{id}/', $respondor);
        $app->patch('/dependencies/{id}/', $respondor);
        $app->delete('/dependencies/{id}/', $respondor);

        $app->get('/requirements/', $respondor);
        $app->post('/requirements/', $respondor);
        $app->get('/requirements/{id}/', $respondor);
        $app->patch('/requirements/{id}/', $respondor);
        $app->delete('/requirements/{id}/', $respondor);

        $app->get('/submissions/', $respondor);
        $app->post('/submissions/', $respondor);
        $app->get('/submissions/{id}/', $respondor);
        $app->patch('/submissions/{id}/', $respondor);
        $app->delete('/submissions/{id}/', $respondor);

        $app->get('/statuses/', $respondor);
        $app->post('/statuses/', $respondor);
        $app->get('/statuses/{id}/', $respondor);
        $app->patch('/statuses/{id}/', $respondor);
        $app->delete('/statuses/{id}/', $respondor);

        $app->get('/tags/', $respondor);
        $app->post('/tags/', $respondor);
        $app->get('/tags/{id}/', $respondor);
        $app->patch('/tags/{id}/', $respondor);
        $app->delete('/tags/{id}/', $respondor);

        $app->get('/notes/', $respondor);
        $app->post('/notes/', $respondor);
        $app->get('/notes/{id}/', $respondor);
        $app->patch('/notes/{id}/', $respondor);
        $app->delete('/notes/{id}/', $respondor);

        $app->get('/recipients/', $respondor);
        $app->post('/recipients/', $respondor);
        $app->get('/recipients/{id}/', $respondor);
        $app->patch('/recipients/{id}/', $respondor);
        $app->delete('/recipients/{id}/', $respondor);

        $app->get('/stakeholders/', $respondor);
        $app->post('/stakeholders/', $respondor);
        $app->get('/stakeholders/{id}/', $respondor);
        $app->patch('/stakeholders/{id}/', $respondor);
        $app->delete('/stakeholders/{id}/', $respondor);

        $app->get('/reactions/', $respondor);
        $app->post('/reactions/', $respondor);
        $app->get('/reactions/{id}/', $respondor);
        $app->patch('/reactions/{id}/', $respondor);
        $app->delete('/reactions/{id}/', $respondor);

        $app->get('/settings/', $respondor);
        $app->post('/settings/', $respondor);
        $app->get('/settings/{id}/', $respondor);
        $app->patch('/settings/{id}/', $respondor);
        $app->delete('/settings/{id}/', $respondor);

        $app->get('/dashboards/', $respondor);
        $app->post('/dashboards/', $respondor);
        $app->get('/dashboards/{id}/', $respondor);
        $app->patch('/dashboards/{id}/', $respondor);
        $app->delete('/dashboards/{id}/', $respondor);

        $app->get('/childformrelationships/', $respondor);
        $app->post('/childformrelationships/', $respondor);
        $app->get('/childformrelationships/{id}/', $respondor);
        $app->patch('/childformrelationships/{id}/', $respondor);
        $app->delete('/childformrelationships/{id}/', $respondor);

        $app->get('/elementchoices/', $respondor);
        $app->post('/elementchoices/', $respondor);
        $app->get('/elementchoices/{id}/', $respondor);
        $app->patch('/elementchoices/{id}/', $respondor);
        $app->delete('/elementchoices/{id}/', $respondor);

        $app->get('/submissiontags/', $respondor);
        $app->post('/submissiontags/', $respondor);
        $app->get('/submissiontags/{id}/', $respondor);
        $app->patch('/submissiontags/{id}/', $respondor);
        $app->delete('/submissiontags/{id}/', $respondor);

        $app->get('/formtags/', $respondor);
        $app->post('/formtags/', $respondor);
        $app->get('/formtags/{id}/', $respondor);
        $app->patch('/formtags/{id}/', $respondor);
        $app->delete('/formtags/{id}/', $respondor);

        $app->get('/formreactions/', $respondor);
        $app->post('/formreactions/', $respondor);
        $app->get('/formreactions/{id}/', $respondor);
        $app->patch('/formreactions/{id}/', $respondor);
        $app->delete('/formreactions/{id}/', $respondor);

        $app->get('/dashboardelements/', $respondor);
        $app->post('/dashboardelements/', $respondor);
        $app->get('/dashboardelements/{id}/', $respondor);
        $app->patch('/dashboardelements/{id}/', $respondor);
        $app->delete('/dashboardelements/{id}/', $respondor);

        $app->get('/dashboardforms/', $respondor);
        $app->post('/dashboardforms/', $respondor);
        $app->get('/dashboardforms/{id}/', $respondor);
        $app->patch('/dashboardforms/{id}/', $respondor);
        $app->delete('/dashboardforms/{id}/', $respondor);

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
