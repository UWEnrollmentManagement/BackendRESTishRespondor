<?php
/**
 * Created by PhpStorm.
 * User: elizaw7
 * Date: 10/4/2017
 * Time: 3:30 PM
 */

namespace FormsAPI\Mediator;

use FormsAPI\ChildFormRelationship;
use FormsAPI\Choices;
use FormsAPI\DashboardElements;
use FormsAPI\DashboardForms;
use FormsAPI\Dashboards;
use FormsAPI\Dependencies;
use FormsAPI\ElementChoices;
use FormsAPI\Form;
use FormsAPI\Element;
use FormsAPI\FormQuery;
use FormsAPI\FormReactions;
use FormsAPI\FormTags;
use FormsAPI\Notes;
use FormsAPI\Reactions;
use FormsAPI\Recipients;
use FormsAPI\Requirements;
use FormsAPI\Settings;
use FormsAPI\Stakeholders;
use FormsAPI\Statuses;
use FormsAPI\Submissions;
use FormsAPI\SubmissionTags;
use FormsAPI\Tags;
use FormsAPI\Visitor;


class PropelMediator implements MediatorInterface
{
    protected $href;

    protected $errors = [];

    protected static $classMap = [
        'forms' => Form::class,
        'elements' => Element::class,
        'visitors' => Visitor::class,
        'choices' => Choices::class,
        'dependencies' => Dependencies::class,
        'requirements' => Requirements::class,
        'submissions' => Submissions::class,
        'statuses' => Statuses::class,
        'tags' => Tags::class,
        'notes' => Notes::class,
        'recipients' => Recipients::class,
        'stakeholders' => Stakeholders::class,
        'reactions' => Reactions::class,
        'settings' => Settings::class,
        'dashboards' => Dashboards::class,
        'childformrelationships' => ChildFormRelationship::class,
        'elementchoices' => ElementChoices::class,
        'submissiontags' => SubmissionTags::class,
        'formtags' => FormTags::class,
        'formreactions' => FormReactions::class,
        'dashboardelements' => DashboardElements::class,
        'dashboardforms' => DashboardForms::class,
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
        // proper capitalization
        foreach($attributes as $key => $value) {
//            unset($attributes[$key]);
//            $attributes[ucfirst($key)] = $value;
        }
        if(array_key_exists("UwNetID", $attributes)) {
            print_r($attributes);
            echo "SDFDS";
        }
        $resource->fromArray($attributes);
        return $resource;
    }

    public function getAttributes($resource) {

        $attributes = $resource->toArray();

        foreach($attributes as $key => $value) {
            unset($attributes[$key]);
            $attributes[lcfirst($key)] = $value;
        }
        // attach href
        // if form, build reference based off rootElementId

        // if element, build reference to parentId
        // ex. /forms/{form_id}/

        $resourceType = array_search(get_class($resource), static::$classMap);

        $attributes["href"] = "{$this->href}/$resourceType/{$attributes['id']}/";

        if ($resourceType === 'forms') {
            $attributes['elements'] = "{$this->href}/$resourceType/{$attributes['id']}/elements/";
            $attributes["rootElement"] = "{$this->href}/elements/{$attributes['rootElementId']}/";

        } elseif ($resourceType === 'elements') {
            $attributes['parent'] = "{$this->href}/$resourceType/{$attributes['parentId']}/";
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