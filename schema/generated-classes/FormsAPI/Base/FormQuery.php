<?php

namespace FormsAPI\Base;

use \Exception;
use \PDO;
use FormsAPI\Form as ChildForm;
use FormsAPI\FormQuery as ChildFormQuery;
use FormsAPI\Map\FormTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'form' table.
 *
 *
 *
 * @method     ChildFormQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildFormQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildFormQuery orderBySlug($order = Criteria::ASC) Order by the slug column
 * @method     ChildFormQuery orderBySuccessMessage($order = Criteria::ASC) Order by the success_message column
 * @method     ChildFormQuery orderByRetired($order = Criteria::ASC) Order by the retired column
 * @method     ChildFormQuery orderByRootElementId($order = Criteria::ASC) Order by the root_element_id column
 *
 * @method     ChildFormQuery groupById() Group by the id column
 * @method     ChildFormQuery groupByName() Group by the name column
 * @method     ChildFormQuery groupBySlug() Group by the slug column
 * @method     ChildFormQuery groupBySuccessMessage() Group by the success_message column
 * @method     ChildFormQuery groupByRetired() Group by the retired column
 * @method     ChildFormQuery groupByRootElementId() Group by the root_element_id column
 *
 * @method     ChildFormQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildFormQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildFormQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildFormQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildFormQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildFormQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildFormQuery leftJoinElement($relationAlias = null) Adds a LEFT JOIN clause to the query using the Element relation
 * @method     ChildFormQuery rightJoinElement($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Element relation
 * @method     ChildFormQuery innerJoinElement($relationAlias = null) Adds a INNER JOIN clause to the query using the Element relation
 *
 * @method     ChildFormQuery joinWithElement($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Element relation
 *
 * @method     ChildFormQuery leftJoinWithElement() Adds a LEFT JOIN clause and with to the query using the Element relation
 * @method     ChildFormQuery rightJoinWithElement() Adds a RIGHT JOIN clause and with to the query using the Element relation
 * @method     ChildFormQuery innerJoinWithElement() Adds a INNER JOIN clause and with to the query using the Element relation
 *
 * @method     ChildFormQuery leftJoinAsParent($relationAlias = null) Adds a LEFT JOIN clause to the query using the AsParent relation
 * @method     ChildFormQuery rightJoinAsParent($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AsParent relation
 * @method     ChildFormQuery innerJoinAsParent($relationAlias = null) Adds a INNER JOIN clause to the query using the AsParent relation
 *
 * @method     ChildFormQuery joinWithAsParent($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the AsParent relation
 *
 * @method     ChildFormQuery leftJoinWithAsParent() Adds a LEFT JOIN clause and with to the query using the AsParent relation
 * @method     ChildFormQuery rightJoinWithAsParent() Adds a RIGHT JOIN clause and with to the query using the AsParent relation
 * @method     ChildFormQuery innerJoinWithAsParent() Adds a INNER JOIN clause and with to the query using the AsParent relation
 *
 * @method     ChildFormQuery leftJoinAsChild($relationAlias = null) Adds a LEFT JOIN clause to the query using the AsChild relation
 * @method     ChildFormQuery rightJoinAsChild($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AsChild relation
 * @method     ChildFormQuery innerJoinAsChild($relationAlias = null) Adds a INNER JOIN clause to the query using the AsChild relation
 *
 * @method     ChildFormQuery joinWithAsChild($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the AsChild relation
 *
 * @method     ChildFormQuery leftJoinWithAsChild() Adds a LEFT JOIN clause and with to the query using the AsChild relation
 * @method     ChildFormQuery rightJoinWithAsChild() Adds a RIGHT JOIN clause and with to the query using the AsChild relation
 * @method     ChildFormQuery innerJoinWithAsChild() Adds a INNER JOIN clause and with to the query using the AsChild relation
 *
 * @method     ChildFormQuery leftJoinRequirement($relationAlias = null) Adds a LEFT JOIN clause to the query using the Requirement relation
 * @method     ChildFormQuery rightJoinRequirement($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Requirement relation
 * @method     ChildFormQuery innerJoinRequirement($relationAlias = null) Adds a INNER JOIN clause to the query using the Requirement relation
 *
 * @method     ChildFormQuery joinWithRequirement($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Requirement relation
 *
 * @method     ChildFormQuery leftJoinWithRequirement() Adds a LEFT JOIN clause and with to the query using the Requirement relation
 * @method     ChildFormQuery rightJoinWithRequirement() Adds a RIGHT JOIN clause and with to the query using the Requirement relation
 * @method     ChildFormQuery innerJoinWithRequirement() Adds a INNER JOIN clause and with to the query using the Requirement relation
 *
 * @method     ChildFormQuery leftJoinSubmission($relationAlias = null) Adds a LEFT JOIN clause to the query using the Submission relation
 * @method     ChildFormQuery rightJoinSubmission($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Submission relation
 * @method     ChildFormQuery innerJoinSubmission($relationAlias = null) Adds a INNER JOIN clause to the query using the Submission relation
 *
 * @method     ChildFormQuery joinWithSubmission($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Submission relation
 *
 * @method     ChildFormQuery leftJoinWithSubmission() Adds a LEFT JOIN clause and with to the query using the Submission relation
 * @method     ChildFormQuery rightJoinWithSubmission() Adds a RIGHT JOIN clause and with to the query using the Submission relation
 * @method     ChildFormQuery innerJoinWithSubmission() Adds a INNER JOIN clause and with to the query using the Submission relation
 *
 * @method     ChildFormQuery leftJoinFormStatus($relationAlias = null) Adds a LEFT JOIN clause to the query using the FormStatus relation
 * @method     ChildFormQuery rightJoinFormStatus($relationAlias = null) Adds a RIGHT JOIN clause to the query using the FormStatus relation
 * @method     ChildFormQuery innerJoinFormStatus($relationAlias = null) Adds a INNER JOIN clause to the query using the FormStatus relation
 *
 * @method     ChildFormQuery joinWithFormStatus($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the FormStatus relation
 *
 * @method     ChildFormQuery leftJoinWithFormStatus() Adds a LEFT JOIN clause and with to the query using the FormStatus relation
 * @method     ChildFormQuery rightJoinWithFormStatus() Adds a RIGHT JOIN clause and with to the query using the FormStatus relation
 * @method     ChildFormQuery innerJoinWithFormStatus() Adds a INNER JOIN clause and with to the query using the FormStatus relation
 *
 * @method     ChildFormQuery leftJoinFormTag($relationAlias = null) Adds a LEFT JOIN clause to the query using the FormTag relation
 * @method     ChildFormQuery rightJoinFormTag($relationAlias = null) Adds a RIGHT JOIN clause to the query using the FormTag relation
 * @method     ChildFormQuery innerJoinFormTag($relationAlias = null) Adds a INNER JOIN clause to the query using the FormTag relation
 *
 * @method     ChildFormQuery joinWithFormTag($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the FormTag relation
 *
 * @method     ChildFormQuery leftJoinWithFormTag() Adds a LEFT JOIN clause and with to the query using the FormTag relation
 * @method     ChildFormQuery rightJoinWithFormTag() Adds a RIGHT JOIN clause and with to the query using the FormTag relation
 * @method     ChildFormQuery innerJoinWithFormTag() Adds a INNER JOIN clause and with to the query using the FormTag relation
 *
 * @method     ChildFormQuery leftJoinFormReaction($relationAlias = null) Adds a LEFT JOIN clause to the query using the FormReaction relation
 * @method     ChildFormQuery rightJoinFormReaction($relationAlias = null) Adds a RIGHT JOIN clause to the query using the FormReaction relation
 * @method     ChildFormQuery innerJoinFormReaction($relationAlias = null) Adds a INNER JOIN clause to the query using the FormReaction relation
 *
 * @method     ChildFormQuery joinWithFormReaction($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the FormReaction relation
 *
 * @method     ChildFormQuery leftJoinWithFormReaction() Adds a LEFT JOIN clause and with to the query using the FormReaction relation
 * @method     ChildFormQuery rightJoinWithFormReaction() Adds a RIGHT JOIN clause and with to the query using the FormReaction relation
 * @method     ChildFormQuery innerJoinWithFormReaction() Adds a INNER JOIN clause and with to the query using the FormReaction relation
 *
 * @method     ChildFormQuery leftJoinDashboardForm($relationAlias = null) Adds a LEFT JOIN clause to the query using the DashboardForm relation
 * @method     ChildFormQuery rightJoinDashboardForm($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DashboardForm relation
 * @method     ChildFormQuery innerJoinDashboardForm($relationAlias = null) Adds a INNER JOIN clause to the query using the DashboardForm relation
 *
 * @method     ChildFormQuery joinWithDashboardForm($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the DashboardForm relation
 *
 * @method     ChildFormQuery leftJoinWithDashboardForm() Adds a LEFT JOIN clause and with to the query using the DashboardForm relation
 * @method     ChildFormQuery rightJoinWithDashboardForm() Adds a RIGHT JOIN clause and with to the query using the DashboardForm relation
 * @method     ChildFormQuery innerJoinWithDashboardForm() Adds a INNER JOIN clause and with to the query using the DashboardForm relation
 *
 * @method     \FormsAPI\ElementQuery|\FormsAPI\ChildFormRelationshipQuery|\FormsAPI\RequirementQuery|\FormsAPI\SubmissionQuery|\FormsAPI\FormStatusQuery|\FormsAPI\FormTagQuery|\FormsAPI\FormReactionQuery|\FormsAPI\DashboardFormQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildForm findOne(ConnectionInterface $con = null) Return the first ChildForm matching the query
 * @method     ChildForm findOneOrCreate(ConnectionInterface $con = null) Return the first ChildForm matching the query, or a new ChildForm object populated from the query conditions when no match is found
 *
 * @method     ChildForm findOneById(int $id) Return the first ChildForm filtered by the id column
 * @method     ChildForm findOneByName(string $name) Return the first ChildForm filtered by the name column
 * @method     ChildForm findOneBySlug(string $slug) Return the first ChildForm filtered by the slug column
 * @method     ChildForm findOneBySuccessMessage(string $success_message) Return the first ChildForm filtered by the success_message column
 * @method     ChildForm findOneByRetired(boolean $retired) Return the first ChildForm filtered by the retired column
 * @method     ChildForm findOneByRootElementId(int $root_element_id) Return the first ChildForm filtered by the root_element_id column *

 * @method     ChildForm requirePk($key, ConnectionInterface $con = null) Return the ChildForm by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildForm requireOne(ConnectionInterface $con = null) Return the first ChildForm matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildForm requireOneById(int $id) Return the first ChildForm filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildForm requireOneByName(string $name) Return the first ChildForm filtered by the name column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildForm requireOneBySlug(string $slug) Return the first ChildForm filtered by the slug column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildForm requireOneBySuccessMessage(string $success_message) Return the first ChildForm filtered by the success_message column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildForm requireOneByRetired(boolean $retired) Return the first ChildForm filtered by the retired column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildForm requireOneByRootElementId(int $root_element_id) Return the first ChildForm filtered by the root_element_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildForm[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildForm objects based on current ModelCriteria
 * @method     ChildForm[]|ObjectCollection findById(int $id) Return ChildForm objects filtered by the id column
 * @method     ChildForm[]|ObjectCollection findByName(string $name) Return ChildForm objects filtered by the name column
 * @method     ChildForm[]|ObjectCollection findBySlug(string $slug) Return ChildForm objects filtered by the slug column
 * @method     ChildForm[]|ObjectCollection findBySuccessMessage(string $success_message) Return ChildForm objects filtered by the success_message column
 * @method     ChildForm[]|ObjectCollection findByRetired(boolean $retired) Return ChildForm objects filtered by the retired column
 * @method     ChildForm[]|ObjectCollection findByRootElementId(int $root_element_id) Return ChildForm objects filtered by the root_element_id column
 * @method     ChildForm[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class FormQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \FormsAPI\Base\FormQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\FormsAPI\\Form', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildFormQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildFormQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildFormQuery) {
            return $criteria;
        }
        $query = new ChildFormQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildForm|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(FormTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = FormTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
            // the object is already in the instance pool
            return $obj;
        }

        return $this->findPkSimple($key, $con);
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildForm A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, name, slug, success_message, retired, root_element_id FROM form WHERE id = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildForm $obj */
            $obj = new ChildForm();
            $obj->hydrate($row);
            FormTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildForm|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, ConnectionInterface $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return $this|ChildFormQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(FormTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildFormQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(FormTableMap::COL_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildFormQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(FormTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(FormTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FormTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the name column
     *
     * Example usage:
     * <code>
     * $query->filterByName('fooValue');   // WHERE name = 'fooValue'
     * $query->filterByName('%fooValue%', Criteria::LIKE); // WHERE name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $name The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildFormQuery The current query, for fluid interface
     */
    public function filterByName($name = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($name)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FormTableMap::COL_NAME, $name, $comparison);
    }

    /**
     * Filter the query on the slug column
     *
     * Example usage:
     * <code>
     * $query->filterBySlug('fooValue');   // WHERE slug = 'fooValue'
     * $query->filterBySlug('%fooValue%', Criteria::LIKE); // WHERE slug LIKE '%fooValue%'
     * </code>
     *
     * @param     string $slug The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildFormQuery The current query, for fluid interface
     */
    public function filterBySlug($slug = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($slug)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FormTableMap::COL_SLUG, $slug, $comparison);
    }

    /**
     * Filter the query on the success_message column
     *
     * Example usage:
     * <code>
     * $query->filterBySuccessMessage('fooValue');   // WHERE success_message = 'fooValue'
     * $query->filterBySuccessMessage('%fooValue%', Criteria::LIKE); // WHERE success_message LIKE '%fooValue%'
     * </code>
     *
     * @param     string $successMessage The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildFormQuery The current query, for fluid interface
     */
    public function filterBySuccessMessage($successMessage = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($successMessage)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FormTableMap::COL_SUCCESS_MESSAGE, $successMessage, $comparison);
    }

    /**
     * Filter the query on the retired column
     *
     * Example usage:
     * <code>
     * $query->filterByRetired(true); // WHERE retired = true
     * $query->filterByRetired('yes'); // WHERE retired = true
     * </code>
     *
     * @param     boolean|string $retired The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildFormQuery The current query, for fluid interface
     */
    public function filterByRetired($retired = null, $comparison = null)
    {
        if (is_string($retired)) {
            $retired = in_array(strtolower($retired), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(FormTableMap::COL_RETIRED, $retired, $comparison);
    }

    /**
     * Filter the query on the root_element_id column
     *
     * Example usage:
     * <code>
     * $query->filterByRootElementId(1234); // WHERE root_element_id = 1234
     * $query->filterByRootElementId(array(12, 34)); // WHERE root_element_id IN (12, 34)
     * $query->filterByRootElementId(array('min' => 12)); // WHERE root_element_id > 12
     * </code>
     *
     * @see       filterByElement()
     *
     * @param     mixed $rootElementId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildFormQuery The current query, for fluid interface
     */
    public function filterByRootElementId($rootElementId = null, $comparison = null)
    {
        if (is_array($rootElementId)) {
            $useMinMax = false;
            if (isset($rootElementId['min'])) {
                $this->addUsingAlias(FormTableMap::COL_ROOT_ELEMENT_ID, $rootElementId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($rootElementId['max'])) {
                $this->addUsingAlias(FormTableMap::COL_ROOT_ELEMENT_ID, $rootElementId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FormTableMap::COL_ROOT_ELEMENT_ID, $rootElementId, $comparison);
    }

    /**
     * Filter the query by a related \FormsAPI\Element object
     *
     * @param \FormsAPI\Element|ObjectCollection $element The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildFormQuery The current query, for fluid interface
     */
    public function filterByElement($element, $comparison = null)
    {
        if ($element instanceof \FormsAPI\Element) {
            return $this
                ->addUsingAlias(FormTableMap::COL_ROOT_ELEMENT_ID, $element->getId(), $comparison);
        } elseif ($element instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(FormTableMap::COL_ROOT_ELEMENT_ID, $element->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByElement() only accepts arguments of type \FormsAPI\Element or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Element relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildFormQuery The current query, for fluid interface
     */
    public function joinElement($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Element');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Element');
        }

        return $this;
    }

    /**
     * Use the Element relation Element object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \FormsAPI\ElementQuery A secondary query class using the current class as primary query
     */
    public function useElementQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinElement($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Element', '\FormsAPI\ElementQuery');
    }

    /**
     * Filter the query by a related \FormsAPI\ChildFormRelationship object
     *
     * @param \FormsAPI\ChildFormRelationship|ObjectCollection $childFormRelationship the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFormQuery The current query, for fluid interface
     */
    public function filterByAsParent($childFormRelationship, $comparison = null)
    {
        if ($childFormRelationship instanceof \FormsAPI\ChildFormRelationship) {
            return $this
                ->addUsingAlias(FormTableMap::COL_ID, $childFormRelationship->getparentName(), $comparison);
        } elseif ($childFormRelationship instanceof ObjectCollection) {
            return $this
                ->useAsParentQuery()
                ->filterByPrimaryKeys($childFormRelationship->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAsParent() only accepts arguments of type \FormsAPI\ChildFormRelationship or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AsParent relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildFormQuery The current query, for fluid interface
     */
    public function joinAsParent($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AsParent');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'AsParent');
        }

        return $this;
    }

    /**
     * Use the AsParent relation ChildFormRelationship object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \FormsAPI\ChildFormRelationshipQuery A secondary query class using the current class as primary query
     */
    public function useAsParentQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAsParent($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AsParent', '\FormsAPI\ChildFormRelationshipQuery');
    }

    /**
     * Filter the query by a related \FormsAPI\ChildFormRelationship object
     *
     * @param \FormsAPI\ChildFormRelationship|ObjectCollection $childFormRelationship the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFormQuery The current query, for fluid interface
     */
    public function filterByAsChild($childFormRelationship, $comparison = null)
    {
        if ($childFormRelationship instanceof \FormsAPI\ChildFormRelationship) {
            return $this
                ->addUsingAlias(FormTableMap::COL_ID, $childFormRelationship->getChildId(), $comparison);
        } elseif ($childFormRelationship instanceof ObjectCollection) {
            return $this
                ->useAsChildQuery()
                ->filterByPrimaryKeys($childFormRelationship->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAsChild() only accepts arguments of type \FormsAPI\ChildFormRelationship or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AsChild relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildFormQuery The current query, for fluid interface
     */
    public function joinAsChild($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AsChild');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'AsChild');
        }

        return $this;
    }

    /**
     * Use the AsChild relation ChildFormRelationship object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \FormsAPI\ChildFormRelationshipQuery A secondary query class using the current class as primary query
     */
    public function useAsChildQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAsChild($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AsChild', '\FormsAPI\ChildFormRelationshipQuery');
    }

    /**
     * Filter the query by a related \FormsAPI\Requirement object
     *
     * @param \FormsAPI\Requirement|ObjectCollection $requirement the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFormQuery The current query, for fluid interface
     */
    public function filterByRequirement($requirement, $comparison = null)
    {
        if ($requirement instanceof \FormsAPI\Requirement) {
            return $this
                ->addUsingAlias(FormTableMap::COL_ID, $requirement->getConditionId(), $comparison);
        } elseif ($requirement instanceof ObjectCollection) {
            return $this
                ->useRequirementQuery()
                ->filterByPrimaryKeys($requirement->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByRequirement() only accepts arguments of type \FormsAPI\Requirement or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Requirement relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildFormQuery The current query, for fluid interface
     */
    public function joinRequirement($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Requirement');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Requirement');
        }

        return $this;
    }

    /**
     * Use the Requirement relation Requirement object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \FormsAPI\RequirementQuery A secondary query class using the current class as primary query
     */
    public function useRequirementQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinRequirement($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Requirement', '\FormsAPI\RequirementQuery');
    }

    /**
     * Filter the query by a related \FormsAPI\Submission object
     *
     * @param \FormsAPI\Submission|ObjectCollection $submission the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFormQuery The current query, for fluid interface
     */
    public function filterBySubmission($submission, $comparison = null)
    {
        if ($submission instanceof \FormsAPI\Submission) {
            return $this
                ->addUsingAlias(FormTableMap::COL_ID, $submission->getFormId(), $comparison);
        } elseif ($submission instanceof ObjectCollection) {
            return $this
                ->useSubmissionQuery()
                ->filterByPrimaryKeys($submission->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySubmission() only accepts arguments of type \FormsAPI\Submission or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Submission relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildFormQuery The current query, for fluid interface
     */
    public function joinSubmission($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Submission');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Submission');
        }

        return $this;
    }

    /**
     * Use the Submission relation Submission object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \FormsAPI\SubmissionQuery A secondary query class using the current class as primary query
     */
    public function useSubmissionQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSubmission($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Submission', '\FormsAPI\SubmissionQuery');
    }

    /**
     * Filter the query by a related \FormsAPI\FormStatus object
     *
     * @param \FormsAPI\FormStatus|ObjectCollection $formStatus the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFormQuery The current query, for fluid interface
     */
    public function filterByFormStatus($formStatus, $comparison = null)
    {
        if ($formStatus instanceof \FormsAPI\FormStatus) {
            return $this
                ->addUsingAlias(FormTableMap::COL_ID, $formStatus->getFormId(), $comparison);
        } elseif ($formStatus instanceof ObjectCollection) {
            return $this
                ->useFormStatusQuery()
                ->filterByPrimaryKeys($formStatus->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByFormStatus() only accepts arguments of type \FormsAPI\FormStatus or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the FormStatus relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildFormQuery The current query, for fluid interface
     */
    public function joinFormStatus($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('FormStatus');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'FormStatus');
        }

        return $this;
    }

    /**
     * Use the FormStatus relation FormStatus object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \FormsAPI\FormStatusQuery A secondary query class using the current class as primary query
     */
    public function useFormStatusQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinFormStatus($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'FormStatus', '\FormsAPI\FormStatusQuery');
    }

    /**
     * Filter the query by a related \FormsAPI\FormTag object
     *
     * @param \FormsAPI\FormTag|ObjectCollection $formTag the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFormQuery The current query, for fluid interface
     */
    public function filterByFormTag($formTag, $comparison = null)
    {
        if ($formTag instanceof \FormsAPI\FormTag) {
            return $this
                ->addUsingAlias(FormTableMap::COL_ID, $formTag->getFormId(), $comparison);
        } elseif ($formTag instanceof ObjectCollection) {
            return $this
                ->useFormTagQuery()
                ->filterByPrimaryKeys($formTag->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByFormTag() only accepts arguments of type \FormsAPI\FormTag or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the FormTag relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildFormQuery The current query, for fluid interface
     */
    public function joinFormTag($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('FormTag');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'FormTag');
        }

        return $this;
    }

    /**
     * Use the FormTag relation FormTag object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \FormsAPI\FormTagQuery A secondary query class using the current class as primary query
     */
    public function useFormTagQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinFormTag($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'FormTag', '\FormsAPI\FormTagQuery');
    }

    /**
     * Filter the query by a related \FormsAPI\FormReaction object
     *
     * @param \FormsAPI\FormReaction|ObjectCollection $formReaction the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFormQuery The current query, for fluid interface
     */
    public function filterByFormReaction($formReaction, $comparison = null)
    {
        if ($formReaction instanceof \FormsAPI\FormReaction) {
            return $this
                ->addUsingAlias(FormTableMap::COL_ID, $formReaction->getFormId(), $comparison);
        } elseif ($formReaction instanceof ObjectCollection) {
            return $this
                ->useFormReactionQuery()
                ->filterByPrimaryKeys($formReaction->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByFormReaction() only accepts arguments of type \FormsAPI\FormReaction or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the FormReaction relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildFormQuery The current query, for fluid interface
     */
    public function joinFormReaction($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('FormReaction');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'FormReaction');
        }

        return $this;
    }

    /**
     * Use the FormReaction relation FormReaction object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \FormsAPI\FormReactionQuery A secondary query class using the current class as primary query
     */
    public function useFormReactionQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinFormReaction($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'FormReaction', '\FormsAPI\FormReactionQuery');
    }

    /**
     * Filter the query by a related \FormsAPI\DashboardForm object
     *
     * @param \FormsAPI\DashboardForm|ObjectCollection $dashboardForm the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFormQuery The current query, for fluid interface
     */
    public function filterByDashboardForm($dashboardForm, $comparison = null)
    {
        if ($dashboardForm instanceof \FormsAPI\DashboardForm) {
            return $this
                ->addUsingAlias(FormTableMap::COL_ID, $dashboardForm->getFormId(), $comparison);
        } elseif ($dashboardForm instanceof ObjectCollection) {
            return $this
                ->useDashboardFormQuery()
                ->filterByPrimaryKeys($dashboardForm->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByDashboardForm() only accepts arguments of type \FormsAPI\DashboardForm or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DashboardForm relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildFormQuery The current query, for fluid interface
     */
    public function joinDashboardForm($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DashboardForm');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'DashboardForm');
        }

        return $this;
    }

    /**
     * Use the DashboardForm relation DashboardForm object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \FormsAPI\DashboardFormQuery A secondary query class using the current class as primary query
     */
    public function useDashboardFormQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDashboardForm($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DashboardForm', '\FormsAPI\DashboardFormQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildForm $form Object to remove from the list of results
     *
     * @return $this|ChildFormQuery The current query, for fluid interface
     */
    public function prune($form = null)
    {
        if ($form) {
            $this->addUsingAlias(FormTableMap::COL_ID, $form->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the form table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(FormTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            FormTableMap::clearInstancePool();
            FormTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(FormTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(FormTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            FormTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            FormTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // FormQuery
