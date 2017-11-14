<?php

namespace FormsAPI\Base;

use \Exception;
use \PDO;
use FormsAPI\Submission as ChildSubmission;
use FormsAPI\SubmissionQuery as ChildSubmissionQuery;
use FormsAPI\Map\SubmissionTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'submission' table.
 *
 *
 *
 * @method     ChildSubmissionQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildSubmissionQuery orderBySubmitted($order = Criteria::ASC) Order by the submitted column
 * @method     ChildSubmissionQuery orderByVisitorId($order = Criteria::ASC) Order by the visitor_id column
 * @method     ChildSubmissionQuery orderByFormId($order = Criteria::ASC) Order by the form_id column
 * @method     ChildSubmissionQuery orderByStatusId($order = Criteria::ASC) Order by the status_id column
 * @method     ChildSubmissionQuery orderByAssigneeId($order = Criteria::ASC) Order by the assignee_id column
 * @method     ChildSubmissionQuery orderByParentId($order = Criteria::ASC) Order by the parent_id column
 *
 * @method     ChildSubmissionQuery groupById() Group by the id column
 * @method     ChildSubmissionQuery groupBySubmitted() Group by the submitted column
 * @method     ChildSubmissionQuery groupByVisitorId() Group by the visitor_id column
 * @method     ChildSubmissionQuery groupByFormId() Group by the form_id column
 * @method     ChildSubmissionQuery groupByStatusId() Group by the status_id column
 * @method     ChildSubmissionQuery groupByAssigneeId() Group by the assignee_id column
 * @method     ChildSubmissionQuery groupByParentId() Group by the parent_id column
 *
 * @method     ChildSubmissionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildSubmissionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildSubmissionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildSubmissionQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildSubmissionQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildSubmissionQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildSubmissionQuery leftJoinVisitor($relationAlias = null) Adds a LEFT JOIN clause to the query using the Visitor relation
 * @method     ChildSubmissionQuery rightJoinVisitor($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Visitor relation
 * @method     ChildSubmissionQuery innerJoinVisitor($relationAlias = null) Adds a INNER JOIN clause to the query using the Visitor relation
 *
 * @method     ChildSubmissionQuery joinWithVisitor($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Visitor relation
 *
 * @method     ChildSubmissionQuery leftJoinWithVisitor() Adds a LEFT JOIN clause and with to the query using the Visitor relation
 * @method     ChildSubmissionQuery rightJoinWithVisitor() Adds a RIGHT JOIN clause and with to the query using the Visitor relation
 * @method     ChildSubmissionQuery innerJoinWithVisitor() Adds a INNER JOIN clause and with to the query using the Visitor relation
 *
 * @method     ChildSubmissionQuery leftJoinForm($relationAlias = null) Adds a LEFT JOIN clause to the query using the Form relation
 * @method     ChildSubmissionQuery rightJoinForm($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Form relation
 * @method     ChildSubmissionQuery innerJoinForm($relationAlias = null) Adds a INNER JOIN clause to the query using the Form relation
 *
 * @method     ChildSubmissionQuery joinWithForm($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Form relation
 *
 * @method     ChildSubmissionQuery leftJoinWithForm() Adds a LEFT JOIN clause and with to the query using the Form relation
 * @method     ChildSubmissionQuery rightJoinWithForm() Adds a RIGHT JOIN clause and with to the query using the Form relation
 * @method     ChildSubmissionQuery innerJoinWithForm() Adds a INNER JOIN clause and with to the query using the Form relation
 *
 * @method     ChildSubmissionQuery leftJoinStatus($relationAlias = null) Adds a LEFT JOIN clause to the query using the Status relation
 * @method     ChildSubmissionQuery rightJoinStatus($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Status relation
 * @method     ChildSubmissionQuery innerJoinStatus($relationAlias = null) Adds a INNER JOIN clause to the query using the Status relation
 *
 * @method     ChildSubmissionQuery joinWithStatus($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Status relation
 *
 * @method     ChildSubmissionQuery leftJoinWithStatus() Adds a LEFT JOIN clause and with to the query using the Status relation
 * @method     ChildSubmissionQuery rightJoinWithStatus() Adds a RIGHT JOIN clause and with to the query using the Status relation
 * @method     ChildSubmissionQuery innerJoinWithStatus() Adds a INNER JOIN clause and with to the query using the Status relation
 *
 * @method     ChildSubmissionQuery leftJoinAssignee($relationAlias = null) Adds a LEFT JOIN clause to the query using the Assignee relation
 * @method     ChildSubmissionQuery rightJoinAssignee($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Assignee relation
 * @method     ChildSubmissionQuery innerJoinAssignee($relationAlias = null) Adds a INNER JOIN clause to the query using the Assignee relation
 *
 * @method     ChildSubmissionQuery joinWithAssignee($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Assignee relation
 *
 * @method     ChildSubmissionQuery leftJoinWithAssignee() Adds a LEFT JOIN clause and with to the query using the Assignee relation
 * @method     ChildSubmissionQuery rightJoinWithAssignee() Adds a RIGHT JOIN clause and with to the query using the Assignee relation
 * @method     ChildSubmissionQuery innerJoinWithAssignee() Adds a INNER JOIN clause and with to the query using the Assignee relation
 *
 * @method     ChildSubmissionQuery leftJoinSubmissionRelatedByParentId($relationAlias = null) Adds a LEFT JOIN clause to the query using the SubmissionRelatedByParentId relation
 * @method     ChildSubmissionQuery rightJoinSubmissionRelatedByParentId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SubmissionRelatedByParentId relation
 * @method     ChildSubmissionQuery innerJoinSubmissionRelatedByParentId($relationAlias = null) Adds a INNER JOIN clause to the query using the SubmissionRelatedByParentId relation
 *
 * @method     ChildSubmissionQuery joinWithSubmissionRelatedByParentId($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the SubmissionRelatedByParentId relation
 *
 * @method     ChildSubmissionQuery leftJoinWithSubmissionRelatedByParentId() Adds a LEFT JOIN clause and with to the query using the SubmissionRelatedByParentId relation
 * @method     ChildSubmissionQuery rightJoinWithSubmissionRelatedByParentId() Adds a RIGHT JOIN clause and with to the query using the SubmissionRelatedByParentId relation
 * @method     ChildSubmissionQuery innerJoinWithSubmissionRelatedByParentId() Adds a INNER JOIN clause and with to the query using the SubmissionRelatedByParentId relation
 *
 * @method     ChildSubmissionQuery leftJoinResponse($relationAlias = null) Adds a LEFT JOIN clause to the query using the Response relation
 * @method     ChildSubmissionQuery rightJoinResponse($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Response relation
 * @method     ChildSubmissionQuery innerJoinResponse($relationAlias = null) Adds a INNER JOIN clause to the query using the Response relation
 *
 * @method     ChildSubmissionQuery joinWithResponse($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Response relation
 *
 * @method     ChildSubmissionQuery leftJoinWithResponse() Adds a LEFT JOIN clause and with to the query using the Response relation
 * @method     ChildSubmissionQuery rightJoinWithResponse() Adds a RIGHT JOIN clause and with to the query using the Response relation
 * @method     ChildSubmissionQuery innerJoinWithResponse() Adds a INNER JOIN clause and with to the query using the Response relation
 *
 * @method     ChildSubmissionQuery leftJoinAsParent($relationAlias = null) Adds a LEFT JOIN clause to the query using the AsParent relation
 * @method     ChildSubmissionQuery rightJoinAsParent($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AsParent relation
 * @method     ChildSubmissionQuery innerJoinAsParent($relationAlias = null) Adds a INNER JOIN clause to the query using the AsParent relation
 *
 * @method     ChildSubmissionQuery joinWithAsParent($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the AsParent relation
 *
 * @method     ChildSubmissionQuery leftJoinWithAsParent() Adds a LEFT JOIN clause and with to the query using the AsParent relation
 * @method     ChildSubmissionQuery rightJoinWithAsParent() Adds a RIGHT JOIN clause and with to the query using the AsParent relation
 * @method     ChildSubmissionQuery innerJoinWithAsParent() Adds a INNER JOIN clause and with to the query using the AsParent relation
 *
 * @method     ChildSubmissionQuery leftJoinSubmissionTag($relationAlias = null) Adds a LEFT JOIN clause to the query using the SubmissionTag relation
 * @method     ChildSubmissionQuery rightJoinSubmissionTag($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SubmissionTag relation
 * @method     ChildSubmissionQuery innerJoinSubmissionTag($relationAlias = null) Adds a INNER JOIN clause to the query using the SubmissionTag relation
 *
 * @method     ChildSubmissionQuery joinWithSubmissionTag($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the SubmissionTag relation
 *
 * @method     ChildSubmissionQuery leftJoinWithSubmissionTag() Adds a LEFT JOIN clause and with to the query using the SubmissionTag relation
 * @method     ChildSubmissionQuery rightJoinWithSubmissionTag() Adds a RIGHT JOIN clause and with to the query using the SubmissionTag relation
 * @method     ChildSubmissionQuery innerJoinWithSubmissionTag() Adds a INNER JOIN clause and with to the query using the SubmissionTag relation
 *
 * @method     \FormsAPI\VisitorQuery|\FormsAPI\FormQuery|\FormsAPI\StatusQuery|\FormsAPI\SubmissionQuery|\FormsAPI\ResponseQuery|\FormsAPI\SubmissionTagQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildSubmission findOne(ConnectionInterface $con = null) Return the first ChildSubmission matching the query
 * @method     ChildSubmission findOneOrCreate(ConnectionInterface $con = null) Return the first ChildSubmission matching the query, or a new ChildSubmission object populated from the query conditions when no match is found
 *
 * @method     ChildSubmission findOneById(int $id) Return the first ChildSubmission filtered by the id column
 * @method     ChildSubmission findOneBySubmitted(string $submitted) Return the first ChildSubmission filtered by the submitted column
 * @method     ChildSubmission findOneByVisitorId(int $visitor_id) Return the first ChildSubmission filtered by the visitor_id column
 * @method     ChildSubmission findOneByFormId(int $form_id) Return the first ChildSubmission filtered by the form_id column
 * @method     ChildSubmission findOneByStatusId(int $status_id) Return the first ChildSubmission filtered by the status_id column
 * @method     ChildSubmission findOneByAssigneeId(int $assignee_id) Return the first ChildSubmission filtered by the assignee_id column
 * @method     ChildSubmission findOneByParentId(int $parent_id) Return the first ChildSubmission filtered by the parent_id column *

 * @method     ChildSubmission requirePk($key, ConnectionInterface $con = null) Return the ChildSubmission by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSubmission requireOne(ConnectionInterface $con = null) Return the first ChildSubmission matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSubmission requireOneById(int $id) Return the first ChildSubmission filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSubmission requireOneBySubmitted(string $submitted) Return the first ChildSubmission filtered by the submitted column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSubmission requireOneByVisitorId(int $visitor_id) Return the first ChildSubmission filtered by the visitor_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSubmission requireOneByFormId(int $form_id) Return the first ChildSubmission filtered by the form_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSubmission requireOneByStatusId(int $status_id) Return the first ChildSubmission filtered by the status_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSubmission requireOneByAssigneeId(int $assignee_id) Return the first ChildSubmission filtered by the assignee_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSubmission requireOneByParentId(int $parent_id) Return the first ChildSubmission filtered by the parent_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSubmission[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildSubmission objects based on current ModelCriteria
 * @method     ChildSubmission[]|ObjectCollection findById(int $id) Return ChildSubmission objects filtered by the id column
 * @method     ChildSubmission[]|ObjectCollection findBySubmitted(string $submitted) Return ChildSubmission objects filtered by the submitted column
 * @method     ChildSubmission[]|ObjectCollection findByVisitorId(int $visitor_id) Return ChildSubmission objects filtered by the visitor_id column
 * @method     ChildSubmission[]|ObjectCollection findByFormId(int $form_id) Return ChildSubmission objects filtered by the form_id column
 * @method     ChildSubmission[]|ObjectCollection findByStatusId(int $status_id) Return ChildSubmission objects filtered by the status_id column
 * @method     ChildSubmission[]|ObjectCollection findByAssigneeId(int $assignee_id) Return ChildSubmission objects filtered by the assignee_id column
 * @method     ChildSubmission[]|ObjectCollection findByParentId(int $parent_id) Return ChildSubmission objects filtered by the parent_id column
 * @method     ChildSubmission[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class SubmissionQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \FormsAPI\Base\SubmissionQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\FormsAPI\\Submission', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildSubmissionQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildSubmissionQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildSubmissionQuery) {
            return $criteria;
        }
        $query = new ChildSubmissionQuery();
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
     * @return ChildSubmission|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SubmissionTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = SubmissionTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @return ChildSubmission A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, submitted, visitor_id, form_id, status_id, assignee_id, parent_id FROM submission WHERE id = :p0';
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
            /** @var ChildSubmission $obj */
            $obj = new ChildSubmission();
            $obj->hydrate($row);
            SubmissionTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildSubmission|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildSubmissionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SubmissionTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildSubmissionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SubmissionTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildSubmissionQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(SubmissionTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(SubmissionTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SubmissionTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the submitted column
     *
     * Example usage:
     * <code>
     * $query->filterBySubmitted('2011-03-14'); // WHERE submitted = '2011-03-14'
     * $query->filterBySubmitted('now'); // WHERE submitted = '2011-03-14'
     * $query->filterBySubmitted(array('max' => 'yesterday')); // WHERE submitted > '2011-03-13'
     * </code>
     *
     * @param     mixed $submitted The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSubmissionQuery The current query, for fluid interface
     */
    public function filterBySubmitted($submitted = null, $comparison = null)
    {
        if (is_array($submitted)) {
            $useMinMax = false;
            if (isset($submitted['min'])) {
                $this->addUsingAlias(SubmissionTableMap::COL_SUBMITTED, $submitted['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($submitted['max'])) {
                $this->addUsingAlias(SubmissionTableMap::COL_SUBMITTED, $submitted['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SubmissionTableMap::COL_SUBMITTED, $submitted, $comparison);
    }

    /**
     * Filter the query on the visitor_id column
     *
     * Example usage:
     * <code>
     * $query->filterByVisitorId(1234); // WHERE visitor_id = 1234
     * $query->filterByVisitorId(array(12, 34)); // WHERE visitor_id IN (12, 34)
     * $query->filterByVisitorId(array('min' => 12)); // WHERE visitor_id > 12
     * </code>
     *
     * @see       filterByVisitor()
     *
     * @param     mixed $visitorId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSubmissionQuery The current query, for fluid interface
     */
    public function filterByVisitorId($visitorId = null, $comparison = null)
    {
        if (is_array($visitorId)) {
            $useMinMax = false;
            if (isset($visitorId['min'])) {
                $this->addUsingAlias(SubmissionTableMap::COL_VISITOR_ID, $visitorId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($visitorId['max'])) {
                $this->addUsingAlias(SubmissionTableMap::COL_VISITOR_ID, $visitorId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SubmissionTableMap::COL_VISITOR_ID, $visitorId, $comparison);
    }

    /**
     * Filter the query on the form_id column
     *
     * Example usage:
     * <code>
     * $query->filterByFormId(1234); // WHERE form_id = 1234
     * $query->filterByFormId(array(12, 34)); // WHERE form_id IN (12, 34)
     * $query->filterByFormId(array('min' => 12)); // WHERE form_id > 12
     * </code>
     *
     * @see       filterByForm()
     *
     * @param     mixed $formId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSubmissionQuery The current query, for fluid interface
     */
    public function filterByFormId($formId = null, $comparison = null)
    {
        if (is_array($formId)) {
            $useMinMax = false;
            if (isset($formId['min'])) {
                $this->addUsingAlias(SubmissionTableMap::COL_FORM_ID, $formId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($formId['max'])) {
                $this->addUsingAlias(SubmissionTableMap::COL_FORM_ID, $formId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SubmissionTableMap::COL_FORM_ID, $formId, $comparison);
    }

    /**
     * Filter the query on the status_id column
     *
     * Example usage:
     * <code>
     * $query->filterByStatusId(1234); // WHERE status_id = 1234
     * $query->filterByStatusId(array(12, 34)); // WHERE status_id IN (12, 34)
     * $query->filterByStatusId(array('min' => 12)); // WHERE status_id > 12
     * </code>
     *
     * @see       filterByStatus()
     *
     * @param     mixed $statusId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSubmissionQuery The current query, for fluid interface
     */
    public function filterByStatusId($statusId = null, $comparison = null)
    {
        if (is_array($statusId)) {
            $useMinMax = false;
            if (isset($statusId['min'])) {
                $this->addUsingAlias(SubmissionTableMap::COL_STATUS_ID, $statusId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($statusId['max'])) {
                $this->addUsingAlias(SubmissionTableMap::COL_STATUS_ID, $statusId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SubmissionTableMap::COL_STATUS_ID, $statusId, $comparison);
    }

    /**
     * Filter the query on the assignee_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAssigneeId(1234); // WHERE assignee_id = 1234
     * $query->filterByAssigneeId(array(12, 34)); // WHERE assignee_id IN (12, 34)
     * $query->filterByAssigneeId(array('min' => 12)); // WHERE assignee_id > 12
     * </code>
     *
     * @see       filterByAssignee()
     *
     * @param     mixed $assigneeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSubmissionQuery The current query, for fluid interface
     */
    public function filterByAssigneeId($assigneeId = null, $comparison = null)
    {
        if (is_array($assigneeId)) {
            $useMinMax = false;
            if (isset($assigneeId['min'])) {
                $this->addUsingAlias(SubmissionTableMap::COL_ASSIGNEE_ID, $assigneeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($assigneeId['max'])) {
                $this->addUsingAlias(SubmissionTableMap::COL_ASSIGNEE_ID, $assigneeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SubmissionTableMap::COL_ASSIGNEE_ID, $assigneeId, $comparison);
    }

    /**
     * Filter the query on the parent_id column
     *
     * Example usage:
     * <code>
     * $query->filterByParentId(1234); // WHERE parent_id = 1234
     * $query->filterByParentId(array(12, 34)); // WHERE parent_id IN (12, 34)
     * $query->filterByParentId(array('min' => 12)); // WHERE parent_id > 12
     * </code>
     *
     * @see       filterBySubmissionRelatedByParentId()
     *
     * @param     mixed $parentId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSubmissionQuery The current query, for fluid interface
     */
    public function filterByParentId($parentId = null, $comparison = null)
    {
        if (is_array($parentId)) {
            $useMinMax = false;
            if (isset($parentId['min'])) {
                $this->addUsingAlias(SubmissionTableMap::COL_PARENT_ID, $parentId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($parentId['max'])) {
                $this->addUsingAlias(SubmissionTableMap::COL_PARENT_ID, $parentId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SubmissionTableMap::COL_PARENT_ID, $parentId, $comparison);
    }

    /**
     * Filter the query by a related \FormsAPI\Visitor object
     *
     * @param \FormsAPI\Visitor|ObjectCollection $visitor The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildSubmissionQuery The current query, for fluid interface
     */
    public function filterByVisitor($visitor, $comparison = null)
    {
        if ($visitor instanceof \FormsAPI\Visitor) {
            return $this
                ->addUsingAlias(SubmissionTableMap::COL_VISITOR_ID, $visitor->getId(), $comparison);
        } elseif ($visitor instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SubmissionTableMap::COL_VISITOR_ID, $visitor->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByVisitor() only accepts arguments of type \FormsAPI\Visitor or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Visitor relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSubmissionQuery The current query, for fluid interface
     */
    public function joinVisitor($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Visitor');

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
            $this->addJoinObject($join, 'Visitor');
        }

        return $this;
    }

    /**
     * Use the Visitor relation Visitor object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \FormsAPI\VisitorQuery A secondary query class using the current class as primary query
     */
    public function useVisitorQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinVisitor($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Visitor', '\FormsAPI\VisitorQuery');
    }

    /**
     * Filter the query by a related \FormsAPI\Form object
     *
     * @param \FormsAPI\Form|ObjectCollection $form The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildSubmissionQuery The current query, for fluid interface
     */
    public function filterByForm($form, $comparison = null)
    {
        if ($form instanceof \FormsAPI\Form) {
            return $this
                ->addUsingAlias(SubmissionTableMap::COL_FORM_ID, $form->getId(), $comparison);
        } elseif ($form instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SubmissionTableMap::COL_FORM_ID, $form->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByForm() only accepts arguments of type \FormsAPI\Form or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Form relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSubmissionQuery The current query, for fluid interface
     */
    public function joinForm($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Form');

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
            $this->addJoinObject($join, 'Form');
        }

        return $this;
    }

    /**
     * Use the Form relation Form object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \FormsAPI\FormQuery A secondary query class using the current class as primary query
     */
    public function useFormQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinForm($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Form', '\FormsAPI\FormQuery');
    }

    /**
     * Filter the query by a related \FormsAPI\Status object
     *
     * @param \FormsAPI\Status|ObjectCollection $status The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildSubmissionQuery The current query, for fluid interface
     */
    public function filterByStatus($status, $comparison = null)
    {
        if ($status instanceof \FormsAPI\Status) {
            return $this
                ->addUsingAlias(SubmissionTableMap::COL_STATUS_ID, $status->getId(), $comparison);
        } elseif ($status instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SubmissionTableMap::COL_STATUS_ID, $status->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByStatus() only accepts arguments of type \FormsAPI\Status or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Status relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSubmissionQuery The current query, for fluid interface
     */
    public function joinStatus($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Status');

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
            $this->addJoinObject($join, 'Status');
        }

        return $this;
    }

    /**
     * Use the Status relation Status object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \FormsAPI\StatusQuery A secondary query class using the current class as primary query
     */
    public function useStatusQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinStatus($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Status', '\FormsAPI\StatusQuery');
    }

    /**
     * Filter the query by a related \FormsAPI\Visitor object
     *
     * @param \FormsAPI\Visitor|ObjectCollection $visitor The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildSubmissionQuery The current query, for fluid interface
     */
    public function filterByAssignee($visitor, $comparison = null)
    {
        if ($visitor instanceof \FormsAPI\Visitor) {
            return $this
                ->addUsingAlias(SubmissionTableMap::COL_ASSIGNEE_ID, $visitor->getId(), $comparison);
        } elseif ($visitor instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SubmissionTableMap::COL_ASSIGNEE_ID, $visitor->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByAssignee() only accepts arguments of type \FormsAPI\Visitor or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Assignee relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSubmissionQuery The current query, for fluid interface
     */
    public function joinAssignee($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Assignee');

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
            $this->addJoinObject($join, 'Assignee');
        }

        return $this;
    }

    /**
     * Use the Assignee relation Visitor object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \FormsAPI\VisitorQuery A secondary query class using the current class as primary query
     */
    public function useAssigneeQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinAssignee($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Assignee', '\FormsAPI\VisitorQuery');
    }

    /**
     * Filter the query by a related \FormsAPI\Submission object
     *
     * @param \FormsAPI\Submission|ObjectCollection $submission The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildSubmissionQuery The current query, for fluid interface
     */
    public function filterBySubmissionRelatedByParentId($submission, $comparison = null)
    {
        if ($submission instanceof \FormsAPI\Submission) {
            return $this
                ->addUsingAlias(SubmissionTableMap::COL_PARENT_ID, $submission->getId(), $comparison);
        } elseif ($submission instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SubmissionTableMap::COL_PARENT_ID, $submission->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterBySubmissionRelatedByParentId() only accepts arguments of type \FormsAPI\Submission or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SubmissionRelatedByParentId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSubmissionQuery The current query, for fluid interface
     */
    public function joinSubmissionRelatedByParentId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SubmissionRelatedByParentId');

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
            $this->addJoinObject($join, 'SubmissionRelatedByParentId');
        }

        return $this;
    }

    /**
     * Use the SubmissionRelatedByParentId relation Submission object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \FormsAPI\SubmissionQuery A secondary query class using the current class as primary query
     */
    public function useSubmissionRelatedByParentIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinSubmissionRelatedByParentId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SubmissionRelatedByParentId', '\FormsAPI\SubmissionQuery');
    }

    /**
     * Filter the query by a related \FormsAPI\Response object
     *
     * @param \FormsAPI\Response|ObjectCollection $response the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSubmissionQuery The current query, for fluid interface
     */
    public function filterByResponse($response, $comparison = null)
    {
        if ($response instanceof \FormsAPI\Response) {
            return $this
                ->addUsingAlias(SubmissionTableMap::COL_ID, $response->getSubmissionId(), $comparison);
        } elseif ($response instanceof ObjectCollection) {
            return $this
                ->useResponseQuery()
                ->filterByPrimaryKeys($response->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByResponse() only accepts arguments of type \FormsAPI\Response or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Response relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSubmissionQuery The current query, for fluid interface
     */
    public function joinResponse($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Response');

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
            $this->addJoinObject($join, 'Response');
        }

        return $this;
    }

    /**
     * Use the Response relation Response object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \FormsAPI\ResponseQuery A secondary query class using the current class as primary query
     */
    public function useResponseQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinResponse($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Response', '\FormsAPI\ResponseQuery');
    }

    /**
     * Filter the query by a related \FormsAPI\Submission object
     *
     * @param \FormsAPI\Submission|ObjectCollection $submission the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSubmissionQuery The current query, for fluid interface
     */
    public function filterByAsParent($submission, $comparison = null)
    {
        if ($submission instanceof \FormsAPI\Submission) {
            return $this
                ->addUsingAlias(SubmissionTableMap::COL_ID, $submission->getParentId(), $comparison);
        } elseif ($submission instanceof ObjectCollection) {
            return $this
                ->useAsParentQuery()
                ->filterByPrimaryKeys($submission->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAsParent() only accepts arguments of type \FormsAPI\Submission or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AsParent relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSubmissionQuery The current query, for fluid interface
     */
    public function joinAsParent($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
     * Use the AsParent relation Submission object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \FormsAPI\SubmissionQuery A secondary query class using the current class as primary query
     */
    public function useAsParentQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinAsParent($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AsParent', '\FormsAPI\SubmissionQuery');
    }

    /**
     * Filter the query by a related \FormsAPI\SubmissionTag object
     *
     * @param \FormsAPI\SubmissionTag|ObjectCollection $submissionTag the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSubmissionQuery The current query, for fluid interface
     */
    public function filterBySubmissionTag($submissionTag, $comparison = null)
    {
        if ($submissionTag instanceof \FormsAPI\SubmissionTag) {
            return $this
                ->addUsingAlias(SubmissionTableMap::COL_ID, $submissionTag->getSubmissionId(), $comparison);
        } elseif ($submissionTag instanceof ObjectCollection) {
            return $this
                ->useSubmissionTagQuery()
                ->filterByPrimaryKeys($submissionTag->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySubmissionTag() only accepts arguments of type \FormsAPI\SubmissionTag or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SubmissionTag relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSubmissionQuery The current query, for fluid interface
     */
    public function joinSubmissionTag($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SubmissionTag');

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
            $this->addJoinObject($join, 'SubmissionTag');
        }

        return $this;
    }

    /**
     * Use the SubmissionTag relation SubmissionTag object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \FormsAPI\SubmissionTagQuery A secondary query class using the current class as primary query
     */
    public function useSubmissionTagQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSubmissionTag($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SubmissionTag', '\FormsAPI\SubmissionTagQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildSubmission $submission Object to remove from the list of results
     *
     * @return $this|ChildSubmissionQuery The current query, for fluid interface
     */
    public function prune($submission = null)
    {
        if ($submission) {
            $this->addUsingAlias(SubmissionTableMap::COL_ID, $submission->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the submission table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SubmissionTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            SubmissionTableMap::clearInstancePool();
            SubmissionTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(SubmissionTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(SubmissionTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            SubmissionTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            SubmissionTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // SubmissionQuery
