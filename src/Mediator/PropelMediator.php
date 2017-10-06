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


class PropelMediator implements MediatorInterface
{
    protected $href;
    protected static $classMap = [
        'forms' => Form::class,
        'elements' => Element::class,
    ];

    public function __construct($baseHref) {
        $this->href = $baseHref;
    }

    public function save($resource)
    {
        if(method_exists($resource, 'validate')) {
            $resource -> validate()
                -> save();
        } else {
            $resource -> save();
        }
        return $resource;
    }

    public function create($resourceType) {
        //might receive strings forms, elements

        $selectedClass = static::$classMap[$resourceType];

        $resource = new $selectedClass();
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
        $hrefRoot = static::$classMap[$resource];
        $hrefRoot = "/$hrefRoot/";

        //forms
        if(array_key_exists("rootElementId", $attributes)) {
            $hrefRoot .= $attributes["rootElementId"]."/";
            // form also needs to construct a reference to form elements
        } else if(array_key_exists("parentId", $attributes)) {
            //elements
            $hrefRoot .= $attributes["parentId"]."/";
        }

        $attributes["href"] = $hrefRoot;
        return $attributes;

    }



}