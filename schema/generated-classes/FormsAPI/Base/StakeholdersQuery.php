<?php

namespace FormsAPI\Base;

use \Exception;
use \PDO;
use FormsAPI\Stakeholders as ChildStakeholders;
use FormsAPI\StakeholdersQuery as ChildStakeholdersQuery;
use FormsAPI\Map\StakeholdersTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'stakeholders' table.
 *
 *
 *
 * @method     ChildStakeholdersQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildStakeholdersQuery orderByLabel($order = Criteria::ASC) Order by the label column
 * @method     ChildStakeholdersQuery orderByAddress($order = Criteria::ASC) Order by the address column
 * @method     ChildStakeholdersQuery orderByFormid($order = Criteria::ASC) Order by the formId column
 *
 * @method     ChildStakeholdersQuery groupById() Group by the id column
 * @method     ChildStakeholdersQuery groupByLabel() Group by the label column
 * @method     ChildStakeholdersQuery groupByAddress() Group by the address column
 * @method     ChildStakeholdersQuery groupByFormid() Group by the formId column
 *
 * @method     ChildStakeholdersQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildStakeholdersQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildStakeholdersQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildStakeholdersQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildStakeholdersQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildStakeholdersQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildStakeholders findOne(ConnectionInterface $con = null) Return the first ChildStakeholders matching the query
 * @method     ChildStakeholders findOneOrCreate(ConnectionInterface $con = null) Return the first ChildStakeholders matching the query, or a new ChildStakeholders object populated from the query conditions when no match is found
 *
 * @method     ChildStakeholders findOneById(int $id) Return the first ChildStakeholders filtered by the id column
 * @method     ChildStakeholders findOneByLabel(string $label) Return the first ChildStakeholders filtered by the label column
 * @method     ChildStakeholders findOneByAddress(string $address) Return the first ChildStakeholders filtered by the address column
 * @method     ChildStakeholders findOneByFormid(int $formId) Return the first ChildStakeholders filtered by the formId column *

 * @method     ChildStakeholders requirePk($key, ConnectionInterface $con = null) Return the ChildStakeholders by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildStakeholders requireOne(ConnectionInterface $con = null) Return the first ChildStakeholders matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildStakeholders requireOneById(int $id) Return the first ChildStakeholders filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildStakeholders requireOneByLabel(string $label) Return the first ChildStakeholders filtered by the label column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildStakeholders requireOneByAddress(string $address) Return the first ChildStakeholders filtered by the address column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildStakeholders requireOneByFormid(int $formId) Return the first ChildStakeholders filtered by the formId column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildStakeholders[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildStakeholders objects based on current ModelCriteria
 * @method     ChildStakeholders[]|ObjectCollection findById(int $id) Return ChildStakeholders objects filtered by the id column
 * @method     ChildStakeholders[]|ObjectCollection findByLabel(string $label) Return ChildStakeholders objects filtered by the label column
 * @method     ChildStakeholders[]|ObjectCollection findByAddress(string $address) Return ChildStakeholders objects filtered by the address column
 * @method     ChildStakeholders[]|ObjectCollection findByFormid(int $formId) Return ChildStakeholders objects filtered by the formId column
 * @method     ChildStakeholders[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class StakeholdersQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \FormsAPI\Base\StakeholdersQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\FormsAPI\\Stakeholders', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildStakeholdersQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildStakeholdersQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildStakeholdersQuery) {
            return $criteria;
        }
        $query = new ChildStakeholdersQuery();
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
     * @return ChildStakeholders|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(StakeholdersTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = StakeholdersTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @return ChildStakeholders A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, label, address, formId FROM stakeholders WHERE id = :p0';
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
            /** @var ChildStakeholders $obj */
            $obj = new ChildStakeholders();
            $obj->hydrate($row);
            StakeholdersTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildStakeholders|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildStakeholdersQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(StakeholdersTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildStakeholdersQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(StakeholdersTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildStakeholdersQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(StakeholdersTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(StakeholdersTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StakeholdersTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the label column
     *
     * Example usage:
     * <code>
     * $query->filterByLabel('fooValue');   // WHERE label = 'fooValue'
     * $query->filterByLabel('%fooValue%', Criteria::LIKE); // WHERE label LIKE '%fooValue%'
     * </code>
     *
     * @param     string $label The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildStakeholdersQuery The current query, for fluid interface
     */
    public function filterByLabel($label = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($label)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StakeholdersTableMap::COL_LABEL, $label, $comparison);
    }

    /**
     * Filter the query on the address column
     *
     * Example usage:
     * <code>
     * $query->filterByAddress('fooValue');   // WHERE address = 'fooValue'
     * $query->filterByAddress('%fooValue%', Criteria::LIKE); // WHERE address LIKE '%fooValue%'
     * </code>
     *
     * @param     string $address The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildStakeholdersQuery The current query, for fluid interface
     */
    public function filterByAddress($address = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($address)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StakeholdersTableMap::COL_ADDRESS, $address, $comparison);
    }

    /**
     * Filter the query on the formId column
     *
     * Example usage:
     * <code>
     * $query->filterByFormid(1234); // WHERE formId = 1234
     * $query->filterByFormid(array(12, 34)); // WHERE formId IN (12, 34)
     * $query->filterByFormid(array('min' => 12)); // WHERE formId > 12
     * </code>
     *
     * @param     mixed $formid The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildStakeholdersQuery The current query, for fluid interface
     */
    public function filterByFormid($formid = null, $comparison = null)
    {
        if (is_array($formid)) {
            $useMinMax = false;
            if (isset($formid['min'])) {
                $this->addUsingAlias(StakeholdersTableMap::COL_FORMID, $formid['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($formid['max'])) {
                $this->addUsingAlias(StakeholdersTableMap::COL_FORMID, $formid['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StakeholdersTableMap::COL_FORMID, $formid, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildStakeholders $stakeholders Object to remove from the list of results
     *
     * @return $this|ChildStakeholdersQuery The current query, for fluid interface
     */
    public function prune($stakeholders = null)
    {
        if ($stakeholders) {
            $this->addUsingAlias(StakeholdersTableMap::COL_ID, $stakeholders->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the stakeholders table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(StakeholdersTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            StakeholdersTableMap::clearInstancePool();
            StakeholdersTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(StakeholdersTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(StakeholdersTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            StakeholdersTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            StakeholdersTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // StakeholdersQuery
