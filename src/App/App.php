<?php
namespace FormsAPI\App;

require_once __DIR__ . '/../setup.php';

use FormsAPI\Mediator\PropelMediator;
use FormsAPI\Respondor\Respondor;

use FormsAPI\ChildFormRelationship;
use FormsAPI\ChoiceValue as Choice;
use FormsAPI\DashboardElement;
use FormsAPI\DashboardForm;
use FormsAPI\Dashboard;
use FormsAPI\Dependency;
use FormsAPI\ElementChoice;
use FormsAPI\Form;
use FormsAPI\Element;
use FormsAPI\Condition;
use FormsAPI\FormReaction;
use FormsAPI\FormTag;
use FormsAPI\FormStatus;
use FormsAPI\Response;
use FormsAPI\Note;
use FormsAPI\Reaction;
use FormsAPI\Recipient;
use FormsAPI\Requirement;
use FormsAPI\Setting;
use FormsAPI\Stakeholder;
use FormsAPI\Status;
use FormsAPI\Submission;
use FormsAPI\SubmissionTag;
use FormsAPI\Tag;
use FormsAPI\Visitor;


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
            'forms' => Form::class,
            'elements' => Element::class,
            'visitors' => Visitor::class,
            'choices' => Choice::class,
            'conditions' => Condition::class,
            'dependencies' => Dependency::class,
            'requirements' => Requirement::class,
            'submissions' => Submission::class,
            'responses' => Response::class,
            'statuses' => Status::class,
            'tags' => Tag::class,
            'notes' => Note::class,
            'recipients' => Recipient::class,
            'stakeholders' => Stakeholder::class,
            'reactions' => Reaction::class,
            'settings' => Setting::class,
            'dashboards' => Dashboard::class,
            'childformrelationships' => ChildFormRelationship::class,
            'elementchoices' => ElementChoice::class,
            'submissiontags' => SubmissionTag::class,
            'formtags' => FormTag::class,
            'formstatuses' => FormStatus::class,
            'formreactions' => FormReaction::class,
            'dashboardelements' => DashboardElement::class,
            'dashboardforms' => DashboardForm::class,
        ];

        $respondor = new Respondor(new PropelMediator(
            '\\',
            $classMap,
            [
                'forms' => function(array $attributes) {
                    $attributes['elements'] = $attributes['href'] . '/elements/';
                    return $attributes;
                }
            ]
            )
        );

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
