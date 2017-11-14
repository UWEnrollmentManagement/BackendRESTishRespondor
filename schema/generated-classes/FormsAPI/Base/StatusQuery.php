<?php

namespace FormsAPI\Base;

use \Exception;
use \PDO;
use FormsAPI\Status as ChildStatus;
use FormsAPI\StatusQuery as ChildStatusQuery;
use FormsAPI\Map\StatusTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'status' table.
 *
 *
 *
 * @method     ChildStatusQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildStatusQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildStatusQuery orderByDefaultMessage($order = Criteria::ASC) Order by the default_message column
 *
 * @method     ChildStatusQuery groupById() Group by the id column
 * @method     ChildStatusQuery groupByName() Group by the name column
 * @method     ChildStatusQuery groupByDefaultMessage() Group by the default_message column
 *
 * @method     ChildStatusQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildStatusQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildStatusQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildStatusQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildStatusQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildStatusQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildStatusQuery leftJoinSubmission($relationAlias = null) Adds a LEFT JOIN clause to the query using the Submission relation
 * @method     ChildStatusQuery rightJoinSubmission($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Submission relation
 * @method     ChildStatusQuery innerJoinSubmission($relationAlias = null) Adds a INNER JOIN clause to the query using the Submission relation
 *
 * @method     ChildStatusQuery joinWithSubmission($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Submission relation
 *
 * @method     ChildStatusQuery leftJoinWithSubmission() Adds a LEFT JOIN clause and with to the query using the Submission relation
 * @method     ChildStatusQuery rightJoinWithSubmission() Adds a RIGHT JOIN clause and with to the query using the Submission relation
 * @method     ChildStatusQuery innerJoinWithSubmission() Adds a INNER JOIN clause and with to the query using the Submission relation
 *
 * @method     ChildStatusQuery leftJoinFormStatus($relationAlias = null) Adds a LEFT JOIN clause to the query using the FormStatus relation
 * @method     ChildStatusQuery rightJoinFormStatus($relationAlias = null) Adds a RIGHT JOIN clause to the query using the FormStatus relation
 * @method     ChildStatusQuery innerJoinFormStatus($relationAlias = null) Adds a INNER JOIN clause to the query using the FormStatus relation
 *
 * @method     ChildStatusQuery joinWithFormStatus($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the FormStatus relation
 *
 * @method     ChildStatusQuery leftJoinWithFormStatus() Adds a LEFT JOIN clause and with to the query using the FormStatus relation
 * @method     ChildStatusQuery rightJoinWithFormStatus() Adds a RIGHT JOIN clause and with to the query using the FormStatus relation
 * @method     ChildStatusQuery innerJoinWithFormStatus() Adds a INNER JOIN clause and with to the query using the FormStatus relation
 *
 * @method     \FormsAPI\SubmissionQuery|\FormsAPI\FormStatusQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildStatus findOne(ConnectionInterface $con = null) Return the first ChildStatus matching the query
 * @method     ChildStatus findOneOrCreate(ConnectionInterface $con = null) Return the first ChildStatus matching the query, or a new ChildStatus object populated from the query conditions when no match is found
 *
 * @method     ChildStatus findOneById(int $id) Return the first ChildStatus filtered by the id column
 * @method     ChildStatus findOneByName(string $name) Return the first ChildStatus filtered by the name column
 * @method     ChildStatus findOneByDefaultMessage(string $default_message) Return the first ChildStatus filtered by the default_message column *

 * @method     ChildStatus requirePk($key, ConnectionInterface $con = null) Return the ChildStatus by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildStatus requireOne(ConnectionInterface $con = null) Return the first ChildStatus matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildStatus requireOneById(int $id) Return the first ChildStatus filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildStatus requireOneByName(string $name) Return the first ChildStatus filtered by the name column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildStatus requireOneByDefaultMessage(string $default_message) Return the first ChildStatus filtered by the default_message column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildStatus[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildStatus objects based on current ModelCriteria
 * @method     ChildStatus[]|ObjectCollection findById(int $id) Return ChildStatus objects filtered by the id column
 * @method     ChildStatus[]|ObjectCollection findByName(string $name) Return ChildStatus objects filtered by the name column
 * @method     ChildStatus[]|ObjectCollection findByDefaultMessage(string $default_message) Return ChildStatus objects filtered by the default_message column
 * @method     ChildStatus[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class StatusQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \FormsAPI\Base\StatusQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\FormsAPI\\Status', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildStatusQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildStatusQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildStatusQuery) {
            return $criteria;
        }
        $query = new ChildStatusQuery();
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
     * @return ChildStatus|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(StatusTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = StatusTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @return ChildStatus A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, name, default_message FROM status WHERE id = :p0';
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
            /** @var ChildStatus $obj */
            $obj = new ChildStatus();
            $obj->hydrate($row);
            StatusTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildStatus|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildStatusQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(StatusTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildStatusQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(StatusTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildStatusQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(StatusTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(StatusTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StatusTableMap::COL_ID, $id, $comparison);
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
     * @return $this|ChildStatusQuery The current query, for fluid interface
     */
    public function filterByName($name = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($name)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StatusTableMap::COL_NAME, $name, $comparison);
    }

    /**
     * Filter the query on the default_message column
     *
     * Example usage:
     * <code>
     * $query->filterByDefaultMessage('fooValue');   // WHERE default_message = 'fooValue'
     * $query->filterByDefaultMessage('%fooValue%', Criteria::LIKE); // WHERE default_message LIKE '%fooValue%'
     * </code>
     *
     * @param     string $defaultMessage The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildStatusQuery The current query, for fluid interface
     */
    public function filterByDefaultMessage($defaultMessage = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($defaultMessage)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StatusTableMap::COL_DEFAULT_MESSAGE, $defaultMessage, $comparison);
    }

    /**
     * Filter the query by a related \FormsAPI\Submission object
     *
     * @param \FormsAPI\Submission|ObjectCollection $submission the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildStatusQuery The current query, for fluid interface
     */
    public function filterBySubmission($submission, $comparison = null)
    {
        if ($submission instanceof \FormsAPI\Submission) {
            return $this
                ->addUsingAlias(StatusTableMap::COL_ID, $submission->getStatusId(), $comparison);
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
     * @return $this|ChildStatusQuery The current query, for fluid interface
     */
    public function joinSubmission($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useSubmissionQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
     * @return ChildStatusQuery The current query, for fluid interface
     */
    public function filterByFormStatus($formStatus, $comparison = null)
    {
        if ($formStatus instanceof \FormsAPI\FormStatus) {
            return $this
                ->addUsingAlias(StatusTableMap::COL_ID, $formStatus->getStatusId(), $comparison);
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
     * @return $this|ChildStatusQuery The current query, for fluid interface
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
     * Exclude object from result
     *
     * @param   ChildStatus $status Object to remove from the list of results
     *
     * @return $this|ChildStatusQuery The current query, for fluid interface
     */
    public function prune($status = null)
    {
        if ($status) {
            $this->addUsingAlias(StatusTableMap::COL_ID, $status->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the status table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(StatusTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            StatusTableMap::clearInstancePool();
            StatusTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(StatusTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(StatusTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            StatusTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            StatusTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // StatusQuery
