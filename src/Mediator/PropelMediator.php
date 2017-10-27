<?php
/**
 * Created by PhpStorm.
 * User: elizaw7
 * Date: 10/4/2017
 * Time: 3:30 PM
 */

namespace FormsAPI\Mediator;

use FormsAPI\Form;
use FormsAPI\Element;
use FormsAPI\FormQuery;


class PropelMediator implements MediatorInterface
{
    protected $href;

    protected $errors = [];

    protected static $classMap = [
        'forms' => Form::class,
        'elements' => Element::class,
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
            unset($attributes[$key]);
            $attributes[ucfirst($key)] = $value;
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

        $hrefRoot = array_search(get_class($resource), static::$classMap);

        //forms
        if(array_key_exists("rootElementId", $attributes)) {
            $hrefRoot .= $attributes["rootElementId"]."/";
            // form also needs to construct a reference to form elements
        } else if(array_key_exists("parentId", $attributes)) {
            //elements
            $hrefRoot .= $attributes["parentId"]."/";
        }

        // we'll make a test for these
        $attributes["href"] = $hrefRoot;
        $attributes['elements'] = "/";
        $attributes['rootElement'] = "/";

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

    public function error()
    {
        return $this->errors;
    }


}