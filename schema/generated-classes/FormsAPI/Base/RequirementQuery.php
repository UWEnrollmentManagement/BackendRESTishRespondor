<?php

namespace FormsAPI\Base;

use \Exception;
use \PDO;
use FormsAPI\Requirement as ChildRequirement;
use FormsAPI\RequirementQuery as ChildRequirementQuery;
use FormsAPI\Map\RequirementTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'requirement' table.
 *
 *
 *
 * @method     ChildRequirementQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildRequirementQuery orderByElementId($order = Criteria::ASC) Order by the element_id column
 * @method     ChildRequirementQuery orderByConditionId($order = Criteria::ASC) Order by the condition_id column
 * @method     ChildRequirementQuery orderByFailureMessage($order = Criteria::ASC) Order by the failure_message column
 *
 * @method     ChildRequirementQuery groupById() Group by the id column
 * @method     ChildRequirementQuery groupByElementId() Group by the element_id column
 * @method     ChildRequirementQuery groupByConditionId() Group by the condition_id column
 * @method     ChildRequirementQuery groupByFailureMessage() Group by the failure_message column
 *
 * @method     ChildRequirementQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildRequirementQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildRequirementQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildRequirementQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildRequirementQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildRequirementQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildRequirement findOne(ConnectionInterface $con = null) Return the first ChildRequirement matching the query
 * @method     ChildRequirement findOneOrCreate(ConnectionInterface $con = null) Return the first ChildRequirement matching the query, or a new ChildRequirement object populated from the query conditions when no match is found
 *
 * @method     ChildRequirement findOneById(int $id) Return the first ChildRequirement filtered by the id column
 * @method     ChildRequirement findOneByElementId(int $element_id) Return the first ChildRequirement filtered by the element_id column
 * @method     ChildRequirement findOneByConditionId(int $condition_id) Return the first ChildRequirement filtered by the condition_id column
 * @method     ChildRequirement findOneByFailureMessage(string $failure_message) Return the first ChildRequirement filtered by the failure_message column *

 * @method     ChildRequirement requirePk($key, ConnectionInterface $con = null) Return the ChildRequirement by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildRequirement requireOne(ConnectionInterface $con = null) Return the first ChildRequirement matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildRequirement requireOneById(int $id) Return the first ChildRequirement filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildRequirement requireOneByElementId(int $element_id) Return the first ChildRequirement filtered by the element_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildRequirement requireOneByConditionId(int $condition_id) Return the first ChildRequirement filtered by the condition_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildRequirement requireOneByFailureMessage(string $failure_message) Return the first ChildRequirement filtered by the failure_message column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildRequirement[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildRequirement objects based on current ModelCriteria
 * @method     ChildRequirement[]|ObjectCollection findById(int $id) Return ChildRequirement objects filtered by the id column
 * @method     ChildRequirement[]|ObjectCollection findByElementId(int $element_id) Return ChildRequirement objects filtered by the element_id column
 * @method     ChildRequirement[]|ObjectCollection findByConditionId(int $condition_id) Return ChildRequirement objects filtered by the condition_id column
 * @method     ChildRequirement[]|ObjectCollection findByFailureMessage(string $failure_message) Return ChildRequirement objects filtered by the failure_message column
 * @method     ChildRequirement[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class RequirementQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \FormsAPI\Base\RequirementQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\FormsAPI\\Requirement', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildRequirementQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildRequirementQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildRequirementQuery) {
            return $criteria;
        }
        $query = new ChildRequirementQuery();
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
     * @return ChildRequirement|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(RequirementTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = RequirementTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @return ChildRequirement A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, element_id, condition_id, failure_message FROM requirement WHERE id = :p0';
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
            /** @var ChildRequirement $obj */
            $obj = new ChildRequirement();
            $obj->hydrate($row);
            RequirementTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildRequirement|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildRequirementQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(RequirementTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildRequirementQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(RequirementTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildRequirementQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(RequirementTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(RequirementTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RequirementTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the element_id column
     *
     * Example usage:
     * <code>
     * $query->filterByElementId(1234); // WHERE element_id = 1234
     * $query->filterByElementId(array(12, 34)); // WHERE element_id IN (12, 34)
     * $query->filterByElementId(array('min' => 12)); // WHERE element_id > 12
     * </code>
     *
     * @param     mixed $elementId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildRequirementQuery The current query, for fluid interface
     */
    public function filterByElementId($elementId = null, $comparison = null)
    {
        if (is_array($elementId)) {
            $useMinMax = false;
            if (isset($elementId['min'])) {
                $this->addUsingAlias(RequirementTableMap::COL_ELEMENT_ID, $elementId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($elementId['max'])) {
                $this->addUsingAlias(RequirementTableMap::COL_ELEMENT_ID, $elementId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RequirementTableMap::COL_ELEMENT_ID, $elementId, $comparison);
    }

    /**
     * Filter the query on the condition_id column
     *
     * Example usage:
     * <code>
     * $query->filterByConditionId(1234); // WHERE condition_id = 1234
     * $query->filterByConditionId(array(12, 34)); // WHERE condition_id IN (12, 34)
     * $query->filterByConditionId(array('min' => 12)); // WHERE condition_id > 12
     * </code>
     *
     * @param     mixed $conditionId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildRequirementQuery The current query, for fluid interface
     */
    public function filterByConditionId($conditionId = null, $comparison = null)
    {
        if (is_array($conditionId)) {
            $useMinMax = false;
            if (isset($conditionId['min'])) {
                $this->addUsingAlias(RequirementTableMap::COL_CONDITION_ID, $conditionId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($conditionId['max'])) {
                $this->addUsingAlias(RequirementTableMap::COL_CONDITION_ID, $conditionId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RequirementTableMap::COL_CONDITION_ID, $conditionId, $comparison);
    }

    /**
     * Filter the query on the failure_message column
     *
     * Example usage:
     * <code>
     * $query->filterByFailureMessage('fooValue');   // WHERE failure_message = 'fooValue'
     * $query->filterByFailureMessage('%fooValue%', Criteria::LIKE); // WHERE failure_message LIKE '%fooValue%'
     * </code>
     *
     * @param     string $failureMessage The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildRequirementQuery The current query, for fluid interface
     */
    public function filterByFailureMessage($failureMessage = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($failureMessage)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RequirementTableMap::COL_FAILURE_MESSAGE, $failureMessage, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildRequirement $requirement Object to remove from the list of results
     *
     * @return $this|ChildRequirementQuery The current query, for fluid interface
     */
    public function prune($requirement = null)
    {
        if ($requirement) {
            $this->addUsingAlias(RequirementTableMap::COL_ID, $requirement->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the requirement table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(RequirementTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            RequirementTableMap::clearInstancePool();
            RequirementTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(RequirementTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(RequirementTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            RequirementTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            RequirementTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // RequirementQuery
