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


class PropelMediator implements MediatorInterface
{
    protected $href;

    protected $classMap;

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


    public function __construct($baseHref, array $classMap, array $extraAttributeProviders = []) {
        $this->href = $baseHref;
        $this->classMap = $classMap;
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
        $selectedClass = $this->classMap[$resourceType];

        $resource = new $selectedClass();
        return $resource;
    }

    public function setAttributes($resource, $attributes) {
        $resource->fromArray($attributes, TableMap::TYPE_FIELDNAME);
        return $resource;
    }

    public function getAttributes($resource) {

        $attributes = $resource->toArray(TableMap::TYPE_FIELDNAME);

        $resourceType = array_search(get_class($resource), $this->classMap);

        $tableMapClass = $resource::TABLE_MAP;
        $columns = $tableMapClass::getTableMap()->getColumns();

        foreach ($columns as $key => $column) {
            if ($column->isForeignKey()) {
                $foreignKeyName = $column->getName();
                $foreignResourceType = array_search(trim($column->getRelatedTable()->getClassName(), '\\'), $this->classMap);
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
        $queryClass = $this->classMap[$resourceType];
        $queryClass .= "Query";
        $query = $queryClass::create()->findOneById($key);
        return ($query != null ? $query : false);
    }

    public function retrieveList($resourceType)
    {
        $queryClass = $this->classMap[$resourceType];
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
        return array_key_exists($resourceType, $this->classMap);
    }

    public function error()
    {
        return $this->errors;
    }


}