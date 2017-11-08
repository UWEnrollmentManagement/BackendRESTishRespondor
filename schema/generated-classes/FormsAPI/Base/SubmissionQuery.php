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
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'submission' table.
 *
 *
 *
 * @method     ChildSubmissionQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildSubmissionQuery orderByVisitorId($order = Criteria::ASC) Order by the visitor_id column
 * @method     ChildSubmissionQuery orderByFormId($order = Criteria::ASC) Order by the form_id column
 * @method     ChildSubmissionQuery orderByStatusId($order = Criteria::ASC) Order by the status_id column
 * @method     ChildSubmissionQuery orderByAssigneeId($order = Criteria::ASC) Order by the assignee_id column
 * @method     ChildSubmissionQuery orderByParentId($order = Criteria::ASC) Order by the parent_id column
 * @method     ChildSubmissionQuery orderBySubmitted($order = Criteria::ASC) Order by the submitted column
 *
 * @method     ChildSubmissionQuery groupById() Group by the id column
 * @method     ChildSubmissionQuery groupByVisitorId() Group by the visitor_id column
 * @method     ChildSubmissionQuery groupByFormId() Group by the form_id column
 * @method     ChildSubmissionQuery groupByStatusId() Group by the status_id column
 * @method     ChildSubmissionQuery groupByAssigneeId() Group by the assignee_id column
 * @method     ChildSubmissionQuery groupByParentId() Group by the parent_id column
 * @method     ChildSubmissionQuery groupBySubmitted() Group by the submitted column
 *
 * @method     ChildSubmissionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildSubmissionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildSubmissionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildSubmissionQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildSubmissionQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildSubmissionQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildSubmission findOne(ConnectionInterface $con = null) Return the first ChildSubmission matching the query
 * @method     ChildSubmission findOneOrCreate(ConnectionInterface $con = null) Return the first ChildSubmission matching the query, or a new ChildSubmission object populated from the query conditions when no match is found
 *
 * @method     ChildSubmission findOneById(int $id) Return the first ChildSubmission filtered by the id column
 * @method     ChildSubmission findOneByVisitorId(int $visitor_id) Return the first ChildSubmission filtered by the visitor_id column
 * @method     ChildSubmission findOneByFormId(int $form_id) Return the first ChildSubmission filtered by the form_id column
 * @method     ChildSubmission findOneByStatusId(int $status_id) Return the first ChildSubmission filtered by the status_id column
 * @method     ChildSubmission findOneByAssigneeId(int $assignee_id) Return the first ChildSubmission filtered by the assignee_id column
 * @method     ChildSubmission findOneByParentId(int $parent_id) Return the first ChildSubmission filtered by the parent_id column
 * @method     ChildSubmission findOneBySubmitted(string $submitted) Return the first ChildSubmission filtered by the submitted column *

 * @method     ChildSubmission requirePk($key, ConnectionInterface $con = null) Return the ChildSubmission by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSubmission requireOne(ConnectionInterface $con = null) Return the first ChildSubmission matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSubmission requireOneById(int $id) Return the first ChildSubmission filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSubmission requireOneByVisitorId(int $visitor_id) Return the first ChildSubmission filtered by the visitor_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSubmission requireOneByFormId(int $form_id) Return the first ChildSubmission filtered by the form_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSubmission requireOneByStatusId(int $status_id) Return the first ChildSubmission filtered by the status_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSubmission requireOneByAssigneeId(int $assignee_id) Return the first ChildSubmission filtered by the assignee_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSubmission requireOneByParentId(int $parent_id) Return the first ChildSubmission filtered by the parent_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSubmission requireOneBySubmitted(string $submitted) Return the first ChildSubmission filtered by the submitted column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSubmission[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildSubmission objects based on current ModelCriteria
 * @method     ChildSubmission[]|ObjectCollection findById(int $id) Return ChildSubmission objects filtered by the id column
 * @method     ChildSubmission[]|ObjectCollection findByVisitorId(int $visitor_id) Return ChildSubmission objects filtered by the visitor_id column
 * @method     ChildSubmission[]|ObjectCollection findByFormId(int $form_id) Return ChildSubmission objects filtered by the form_id column
 * @method     ChildSubmission[]|ObjectCollection findByStatusId(int $status_id) Return ChildSubmission objects filtered by the status_id column
 * @method     ChildSubmission[]|ObjectCollection findByAssigneeId(int $assignee_id) Return ChildSubmission objects filtered by the assignee_id column
 * @method     ChildSubmission[]|ObjectCollection findByParentId(int $parent_id) Return ChildSubmission objects filtered by the parent_id column
 * @method     ChildSubmission[]|ObjectCollection findBySubmitted(string $submitted) Return ChildSubmission objects filtered by the submitted column
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
        $sql = 'SELECT id, visitor_id, form_id, status_id, assignee_id, parent_id, submitted FROM submission WHERE id = :p0';
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
     * Filter the query on the visitor_id column
     *
     * Example usage:
     * <code>
     * $query->filterByVisitorId(1234); // WHERE visitor_id = 1234
     * $query->filterByVisitorId(array(12, 34)); // WHERE visitor_id IN (12, 34)
     * $query->filterByVisitorId(array('min' => 12)); // WHERE visitor_id > 12
     * </code>
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
