<?php
/**
 * Created by PhpStorm.
 * User: elizaw7
 * Date: 10/4/2017
 * Time: 3:30 PM
 */

namespace FormsAPI\Mediator;

use Propel\Runtime\Map\TableMap;
use FormsAPI\ChildFormRelationship;
use FormsAPI\ChoiceValue as Choice;
use FormsAPI\DashboardElement;
use FormsAPI\DashboardForm;
use FormsAPI\Dashboard;
use FormsAPI\Dependency;
use FormsAPI\ElementChoice;
use FormsAPI\Form;
use FormsAPI\Element;
use FormsAPI\FormQuery;
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


class PropelMediator implements MediatorInterface
{
    protected $href;

    protected $errors = [];

    protected static $classMap = [
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

    public function __construct($baseHref) {
        $this->href = $baseHref;
    }

    public function save($resource)
    {
        if(method_exists($resource, 'validate') && $resource->validate() === false) {
            foreach ($resource->getValidationFailures() as $failure) {
                $this->errors[] = "Property ".$failure->getPropertyPath().": ".$failure->getMessage()."\n";
            }

            return false;
        }

        try {
            $resource->save();
        } catch (\Exception $e) {

            $this->errors[] = 'Our database encountered an error fulfilling your request.';
            $this->errors[] = $e->getMessage();

            return false;
        }

        return $resource;

    }

    public function create($resourceType) {
        //might receive strings forms, elements
        $selectedClass = static::$classMap[$resourceType];

        $resource = new $selectedClass();
        return $resource;
    }

    public function setAttributes($resource, $attributes) {
        $resource->fromArray($attributes, TableMap::TYPE_FIELDNAME);
        return $resource;
    }

    public function getAttributes($resource) {

        $attributes = $resource->toArray(TableMap::TYPE_FIELDNAME);

        $resourceType = array_search(get_class($resource), static::$classMap);

        $attributes["href"] = "{$this->href}/$resourceType/{$attributes['id']}/";

        if ($resourceType === 'forms') {
            $attributes['elements'] = "{$this->href}/$resourceType/{$attributes['id']}/elements/";
            $attributes["root_element"] = "{$this->href}/elements/{$attributes['root_element_id']}/";

        } elseif ($resourceType === 'elements') {
            $attributes['parent'] = "{$this->href}/$resourceType/{$attributes['parent_id']}/";
        } elseif ($resourceType === 'dependencies') {
            $attributes['element'] = "{$this->href}/elements/{$attributes['element_id']}";
            $attributes['slave'] = "{$this->href}/elements/{$attributes['slave_id']}";
            $attributes['condition'] = "{$this->href}/conditions/{$attributes['condition_id']}";
        } elseif ($resourceType === "requirements") {
            $attributes['element'] = "{$this->href}/elements/{$attributes['element_id']}";
            $attributes['condition'] = "{$this->href}/conditions/{$attributes['condition_id']}";
        } elseif ($resourceType === "submissions") {
            $attributes['visitor'] = "{$this->href}/visitors/{$attributes['visitor_id']}";
            $attributes['form'] = "{$this->href}/forms/{$attributes['form_id']}";
            $attributes['status'] = "{$this->href}/statuses/{$attributes['status_id']}";
            $attributes['assignee'] = "{$this->href}/visitors/{$attributes['assignee_id']}";
            $attributes['submitted'] = "{$this->href}/visitors/{$attributes['assignee_id']}";
            if (array_key_exists('parent', $attributes)) {
                $attributes['parent'] = "{$this->href}/submissions/{$attributes['parent_id']}";
            } else {
                $attributes['parent'] = null;
            }
        } elseif ($resourceType === "recipients") {
            $attributes["note"] = "{$this->href}/notes/{$attributes['note_id']}";

        } elseif ($resourceType === 'responses') {
            $attributes['submission'] = "{$this->href}/submissions/{$attributes['submission_id']}";
            $attributes['element'] = "{$this->href}/elements/{$attributes['element_id']}";
        } elseif ($resourceType === 'stakeholders') {
            $attributes['form'] = "{$this->href}/forms/{$attributes['form_id']}";
        } elseif ($resourceType === 'childformrelationships') {
            $attributes['child'] = "{$this->href}/forms/{$attributes['child_id']}";
            $attributes['parent'] = "{$this->href}/forms/{$attributes['parent_id']}";
            $attributes['tag'] = "{$this->href}/tags/{$attributes['tag_id']}";
            $attributes['reaction'] = "{$this->href}/reactions/{$attributes['reaction_id']}";
        } elseif ($resourceType === 'elementchoices') {
            $attributes['element'] = "{$this->href}/elements/{$attributes['element_id']}";
            $attributes['choice'] = "{$this->href}/choices/{$attributes['choice_id']}";
        } elseif ($resourceType === 'submissiontags') {
            $attributes['submission'] = "{$this->href}/submissions/{$attributes['submission_id']}";
            $attributes['tag'] = "{$this->href}/tags/{$attributes['tag_id']}";
        } elseif ($resourceType === 'formstatuses') {
            $attributes['form'] = "{$this->href}/forms/{$attributes['form_id']}";
            $attributes['status'] = "{$this->href}/statuses/{$attributes['status_id']}";
        } elseif ($resourceType === 'formtags') {
            $attributes['form'] = "{$this->href}/forms/{$attributes['form_id']}";
            $attributes['tag'] = "{$this->href}/tags/{$attributes['tag_id']}";
        } elseif ($resourceType === 'formreactions') {
            $attributes['form'] = "{$this->href}/forms/{$attributes['form_id']}";
            $attributes['reaction'] = "{$this->href}/reactions/{$attributes['reaction_id']}";
        } elseif ($resourceType === 'dashboardelements') {
            $attributes['dashboard'] = "{$this->href}/dashboards/{$attributes['dashboard_id']}";
            $attributes['element'] = "{$this->href}/element/{$attributes['element_id']}";
        } elseif ($resourceType === 'dashboardforms') {
            $attributes['dashboard'] = "{$this->href}/dashboards/{$attributes['dashboard_id']}";
            $attributes['form'] = "{$this->href}/forms/{$attributes['form_id']}";
        }


        return $attributes;

    }

    public function retrieve($resourceType, $key)
    {
        $queryClass = static::$classMap[$resourceType];
        $queryClass .= "Query";
        $query = $queryClass::create()->findOneById($key);
        return ($query != null ? $query : false);
    }

    public function retrieveList($resourceType)
    {
        $queryClass = static::$classMap[$resourceType];
        $queryClass .= "Query";
        $query = $queryClass::create();
        return $query;
    }

    public function limit($collection, $limit)
    {
        $limit = max(1, $limit);
        $limit = min(100, $limit);

        return $collection->limit($limit);
    }

    public function collectionToIterable($collection)
    {
        return $collection->find();
    }

    public function offset($collection, $offset)
    {
        return $collection->offset($offset);
    }

    public function delete($resource)
    {
        $resource->delete();

        return $resource->wasDeleted();
    }

    public function resourceTypeExists($resourceType)
    {
        return array_key_exists($resourceType, static::$classMap);
    }

    public function error()
    {
        return $this->errors;
    }


}