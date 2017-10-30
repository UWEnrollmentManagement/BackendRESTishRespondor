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
     * Returns a resource given a primary key, or false if the resource can't be found.
     *
     * @param string $resourceType
     * @param mixed $key
     * @return mixed|false
     */
    public function retrieve($resourceType, $key);

    /**
     * Returns a resource given a resource, or false if the resource can't be found.
     *
     * @param string $resourceType
     * @return mixed|false
     */
    public function retrieveList($resourceType);

    /**
     * If you give me a "collection" then I will give you an iterable of resources
     *
     * @param $collection
     * @return mixed
     */
    public function collectionToIterable($collection);

    /**
     * Limits the number of resources that will be returned when a $collection is
     * iterated.
     *
     * @param mixed $collection
     * @param int limit
     * @return mixed
     */
    public function limit($collection, $limit);

    /**
     * Sets the first element that will be returned when a $collection is
     * iterated.
     *
     * @param $collection
     * @param int $offset
     * @return mixed
     */
    public function offset($collection, $offset);

    /**
     * Deletes a given resource. Return true on success or false on failure to delete.
     *
     * @param $resource
     * @return boolean
     */
    public function delete($resource);

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
     * A predicate which reports whether or not this mediator supports
     * the given resource type.
     *
     * @param string $resourceType
     * @return boolean
     */
    public function resourceTypeExists($resourceType);

    /**
     * Returns errors
     *
     * @return string[]
     */
    public function error();

}