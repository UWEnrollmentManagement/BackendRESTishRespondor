<?php
/**
 * Created by PhpStorm.
 * User: elizaw7
 * Date: 10/4/2017
 * Time: 3:30 PM
 */

namespace FormsAPI\Mediator;

use Propel\Runtime\Map\TableMap;
use Propel\Runtime\ActiveQuery\Criteria;

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

    protected $extraAttributeProviders;

    protected $errors = [];

    protected static $condMap = [
        MediatorInterface::COND_GT => Criteria::GREATER_THAN,
        MediatorInterface::COND_LT => Criteria::LESS_THAN,
        MediatorInterface::COND_EQUAL => Criteria::EQUAL,
        MediatorInterface::COND_GTE => Criteria::GREATER_EQUAL,
        MediatorInterface::COND_LTE => Criteria::LESS_EQUAL,
        MediatorInterface::COND_NOT_EQUAL => Criteria::NOT_EQUAL,
        MediatorInterface::COND_LIKE => Criteria::LIKE,
        MediatorInterface::COND_NULL => Criteria::ISNULL,
        MediatorInterface::COND_NOT_NULL => Criteria::ISNOTNULL,
    ];

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

    public function __construct($baseHref, array $extraAttributeProviders = []) {
        $this->href = $baseHref;
        $this->extraAttributeProviders = $extraAttributeProviders;
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

        $tableMapClass = $resource::TABLE_MAP;
        $columns = $tableMapClass::getTableMap()->getColumns();

        foreach ($columns as $key => $column) {
            if ($column->isForeignKey()) {
                $foreignKeyName = $column->getName();
                $foreignResourceType = array_search(trim($column->getRelatedTable()->getClassName(), '\\'), static::$classMap);
                $foreignReferenceName = substr($foreignKeyName, 0, -3);

                if (array_key_exists($foreignKeyName, $attributes) && $attributes[$foreignKeyName] !== null) {
                    $attributes[$foreignReferenceName] = "{$this->href}/$foreignResourceType/{$attributes[$foreignKeyName]}";
                } else {
                    $attributes[$foreignReferenceName] = null;
                }
            } elseif ($column->getType() === 'TIMESTAMP') {
                $attributes[$column->getName()] = strtotime($attributes[$column->getName()]);
            }
        }

        $attributes["href"] = "{$this->href}/$resourceType/{$attributes['id']}/";

        if (array_key_exists($resourceType, $this->extraAttributeProviders)) {
            $attributes = $this->extraAttributeProviders[$resourceType]($attributes);
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

    public function filter($collection, $attribute, $operator, $value = null)
    {
        $attribute = $collection->getTableMap()->getColumn($attribute)->getPhpName();
        $propelOperator = static::$condMap[$operator];
        return $collection->filterBy($attribute, $value, $propelOperator);
    }

    public function delete($resource)
    {
        $resource->delete();

        return $resource->isDeleted();
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