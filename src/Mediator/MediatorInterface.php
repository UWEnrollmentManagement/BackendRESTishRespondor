<?php
/**
 * Created by PhpStorm.
 * User: elizaw7
 * Date: 10/4/2017
 * Time: 3:23 PM
 */

namespace FormsAPI\Mediator;


Interface MediatorInterface
{

    /**
     * MediatorInterface constructor.
     *
     * @param string $baseHref The base href for the API. Eg: https://example.com/api/v1/
     */
    public function __construct($baseHref);

    /**
     * Creates and returns an interface of the indicated $resourceType.
     *
     * Eg: `create("forms")` creates and returns a new form.
     *
     * @param string $resourceType
     * @return mixed
     */
    public function create($resourceType);

    /**
     * Persists the given resource.
     *
     * Returns the resource, or false if there is an error with the resource's attributes.
     *
     * @param mixed $resource
     * @return mixed
     */
    public function save($resource);

    /**
     * Sets the given resource's attributes from the given key => value array.
     *
     *
     * @param mixed $resource
     * @param array $attributes
     * @return mixed
     */
    public function setAttributes($resource, $attributes);

    /**
     * Returns a key => value array of the resource's attributes.
     *
     * @param array $resources
     * @return mixed
     */
    public function getAttributes($resource);

    /**
     * @param $error
     *
     * Returns errors
     *
     * @return mixed
     */
//    public function error($error);

}