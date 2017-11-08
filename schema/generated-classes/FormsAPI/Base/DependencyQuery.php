<?php

namespace FormsAPI\Base;

use \Exception;
use \PDO;
use FormsAPI\Dependency as ChildDependency;
use FormsAPI\DependencyQuery as ChildDependencyQuery;
use FormsAPI\Map\DependencyTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'dependency' table.
 *
 *
 *
 * @method     ChildDependencyQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildDependencyQuery orderByElementId($order = Criteria::ASC) Order by the element_id column
 * @method     ChildDependencyQuery orderBySlaveId($order = Criteria::ASC) Order by the slave_id column
 * @method     ChildDependencyQuery orderByConditionId($order = Criteria::ASC) Order by the condition_id column
 *
 * @method     ChildDependencyQuery groupById() Group by the id column
 * @method     ChildDependencyQuery groupByElementId() Group by the element_id column
 * @method     ChildDependencyQuery groupBySlaveId() Group by the slave_id column
 * @method     ChildDependencyQuery groupByConditionId() Group by the condition_id column
 *
 * @method     ChildDependencyQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildDependencyQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildDependencyQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildDependencyQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildDependencyQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildDependencyQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildDependency findOne(ConnectionInterface $con = null) Return the first ChildDependency matching the query
 * @method     ChildDependency findOneOrCreate(ConnectionInterface $con = null) Return the first ChildDependency matching the query, or a new ChildDependency object populated from the query conditions when no match is found
 *
 * @method     ChildDependency findOneById(int $id) Return the first ChildDependency filtered by the id column
 * @method     ChildDependency findOneByElementId(int $element_id) Return the first ChildDependency filtered by the element_id column
 * @method     ChildDependency findOneBySlaveId(int $slave_id) Return the first ChildDependency filtered by the slave_id column
 * @method     ChildDependency findOneByConditionId(int $condition_id) Return the first ChildDependency filtered by the condition_id column *

 * @method     ChildDependency requirePk($key, ConnectionInterface $con = null) Return the ChildDependency by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildDependency requireOne(ConnectionInterface $con = null) Return the first ChildDependency matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildDependency requireOneById(int $id) Return the first ChildDependency filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildDependency requireOneByElementId(int $element_id) Return the first ChildDependency filtered by the element_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildDependency requireOneBySlaveId(int $slave_id) Return the first ChildDependency filtered by the slave_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildDependency requireOneByConditionId(int $condition_id) Return the first ChildDependency filtered by the condition_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildDependency[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildDependency objects based on current ModelCriteria
 * @method     ChildDependency[]|ObjectCollection findById(int $id) Return ChildDependency objects filtered by the id column
 * @method     ChildDependency[]|ObjectCollection findByElementId(int $element_id) Return ChildDependency objects filtered by the element_id column
 * @method     ChildDependency[]|ObjectCollection findBySlaveId(int $slave_id) Return ChildDependency objects filtered by the slave_id column
 * @method     ChildDependency[]|ObjectCollection findByConditionId(int $condition_id) Return ChildDependency objects filtered by the condition_id column
 * @method     ChildDependency[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class DependencyQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \FormsAPI\Base\DependencyQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\FormsAPI\\Dependency', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildDependencyQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildDependencyQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildDependencyQuery) {
            return $criteria;
        }
        $query = new ChildDependencyQuery();
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
     * @return ChildDependency|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(DependencyTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = DependencyTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @return ChildDependency A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, element_id, slave_id, condition_id FROM dependency WHERE id = :p0';
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
            /** @var ChildDependency $obj */
            $obj = new ChildDependency();
            $obj->hydrate($row);
            DependencyTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildDependency|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildDependencyQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(DependencyTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildDependencyQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(DependencyTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildDependencyQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(DependencyTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(DependencyTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DependencyTableMap::COL_ID, $id, $comparison);
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
     * @return $this|ChildDependencyQuery The current query, for fluid interface
     */
    public function filterByElementId($elementId = null, $comparison = null)
    {
        if (is_array($elementId)) {
            $useMinMax = false;
            if (isset($elementId['min'])) {
                $this->addUsingAlias(DependencyTableMap::COL_ELEMENT_ID, $elementId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($elementId['max'])) {
                $this->addUsingAlias(DependencyTableMap::COL_ELEMENT_ID, $elementId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DependencyTableMap::COL_ELEMENT_ID, $elementId, $comparison);
    }

    /**
     * Filter the query on the slave_id column
     *
     * Example usage:
     * <code>
     * $query->filterBySlaveId(1234); // WHERE slave_id = 1234
     * $query->filterBySlaveId(array(12, 34)); // WHERE slave_id IN (12, 34)
     * $query->filterBySlaveId(array('min' => 12)); // WHERE slave_id > 12
     * </code>
     *
     * @param     mixed $slaveId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildDependencyQuery The current query, for fluid interface
     */
    public function filterBySlaveId($slaveId = null, $comparison = null)
    {
        if (is_array($slaveId)) {
            $useMinMax = false;
            if (isset($slaveId['min'])) {
                $this->addUsingAlias(DependencyTableMap::COL_SLAVE_ID, $slaveId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($slaveId['max'])) {
                $this->addUsingAlias(DependencyTableMap::COL_SLAVE_ID, $slaveId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DependencyTableMap::COL_SLAVE_ID, $slaveId, $comparison);
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
     * @return $this|ChildDependencyQuery The current query, for fluid interface
     */
    public function filterByConditionId($conditionId = null, $comparison = null)
    {
        if (is_array($conditionId)) {
            $useMinMax = false;
            if (isset($conditionId['min'])) {
                $this->addUsingAlias(DependencyTableMap::COL_CONDITION_ID, $conditionId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($conditionId['max'])) {
                $this->addUsingAlias(DependencyTableMap::COL_CONDITION_ID, $conditionId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DependencyTableMap::COL_CONDITION_ID, $conditionId, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildDependency $dependency Object to remove from the list of results
     *
     * @return $this|ChildDependencyQuery The current query, for fluid interface
     */
    public function prune($dependency = null)
    {
        if ($dependency) {
            $this->addUsingAlias(DependencyTableMap::COL_ID, $dependency->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the dependency table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(DependencyTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            DependencyTableMap::clearInstancePool();
            DependencyTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(DependencyTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(DependencyTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            DependencyTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            DependencyTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // DependencyQuery
