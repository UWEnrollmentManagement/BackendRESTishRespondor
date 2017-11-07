<?php

namespace FormsAPI\Base;

use \Exception;
use \PDO;
use FormsAPI\FormReactions as ChildFormReactions;
use FormsAPI\FormReactionsQuery as ChildFormReactionsQuery;
use FormsAPI\Map\FormReactionsTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'form_reactions' table.
 *
 *
 *
 * @method     ChildFormReactionsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildFormReactionsQuery orderByReactionId($order = Criteria::ASC) Order by the reaction_id column
 * @method     ChildFormReactionsQuery orderByFormId($order = Criteria::ASC) Order by the form_id column
 *
 * @method     ChildFormReactionsQuery groupById() Group by the id column
 * @method     ChildFormReactionsQuery groupByReactionId() Group by the reaction_id column
 * @method     ChildFormReactionsQuery groupByFormId() Group by the form_id column
 *
 * @method     ChildFormReactionsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildFormReactionsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildFormReactionsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildFormReactionsQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildFormReactionsQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildFormReactionsQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildFormReactions findOne(ConnectionInterface $con = null) Return the first ChildFormReactions matching the query
 * @method     ChildFormReactions findOneOrCreate(ConnectionInterface $con = null) Return the first ChildFormReactions matching the query, or a new ChildFormReactions object populated from the query conditions when no match is found
 *
 * @method     ChildFormReactions findOneById(int $id) Return the first ChildFormReactions filtered by the id column
 * @method     ChildFormReactions findOneByReactionId(int $reaction_id) Return the first ChildFormReactions filtered by the reaction_id column
 * @method     ChildFormReactions findOneByFormId(int $form_id) Return the first ChildFormReactions filtered by the form_id column *

 * @method     ChildFormReactions requirePk($key, ConnectionInterface $con = null) Return the ChildFormReactions by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildFormReactions requireOne(ConnectionInterface $con = null) Return the first ChildFormReactions matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildFormReactions requireOneById(int $id) Return the first ChildFormReactions filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildFormReactions requireOneByReactionId(int $reaction_id) Return the first ChildFormReactions filtered by the reaction_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildFormReactions requireOneByFormId(int $form_id) Return the first ChildFormReactions filtered by the form_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildFormReactions[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildFormReactions objects based on current ModelCriteria
 * @method     ChildFormReactions[]|ObjectCollection findById(int $id) Return ChildFormReactions objects filtered by the id column
 * @method     ChildFormReactions[]|ObjectCollection findByReactionId(int $reaction_id) Return ChildFormReactions objects filtered by the reaction_id column
 * @method     ChildFormReactions[]|ObjectCollection findByFormId(int $form_id) Return ChildFormReactions objects filtered by the form_id column
 * @method     ChildFormReactions[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class FormReactionsQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \FormsAPI\Base\FormReactionsQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\FormsAPI\\FormReactions', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildFormReactionsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildFormReactionsQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildFormReactionsQuery) {
            return $criteria;
        }
        $query = new ChildFormReactionsQuery();
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
     * @return ChildFormReactions|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(FormReactionsTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = FormReactionsTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @return ChildFormReactions A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, reaction_id, form_id FROM form_reactions WHERE id = :p0';
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
            /** @var ChildFormReactions $obj */
            $obj = new ChildFormReactions();
            $obj->hydrate($row);
            FormReactionsTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildFormReactions|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildFormReactionsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(FormReactionsTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildFormReactionsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(FormReactionsTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildFormReactionsQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(FormReactionsTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(FormReactionsTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FormReactionsTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the reaction_id column
     *
     * Example usage:
     * <code>
     * $query->filterByReactionId(1234); // WHERE reaction_id = 1234
     * $query->filterByReactionId(array(12, 34)); // WHERE reaction_id IN (12, 34)
     * $query->filterByReactionId(array('min' => 12)); // WHERE reaction_id > 12
     * </code>
     *
     * @param     mixed $reactionId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildFormReactionsQuery The current query, for fluid interface
     */
    public function filterByReactionId($reactionId = null, $comparison = null)
    {
        if (is_array($reactionId)) {
            $useMinMax = false;
            if (isset($reactionId['min'])) {
                $this->addUsingAlias(FormReactionsTableMap::COL_REACTION_ID, $reactionId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($reactionId['max'])) {
                $this->addUsingAlias(FormReactionsTableMap::COL_REACTION_ID, $reactionId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FormReactionsTableMap::COL_REACTION_ID, $reactionId, $comparison);
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
     * @return $this|ChildFormReactionsQuery The current query, for fluid interface
     */
    public function filterByFormId($formId = null, $comparison = null)
    {
        if (is_array($formId)) {
            $useMinMax = false;
            if (isset($formId['min'])) {
                $this->addUsingAlias(FormReactionsTableMap::COL_FORM_ID, $formId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($formId['max'])) {
                $this->addUsingAlias(FormReactionsTableMap::COL_FORM_ID, $formId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FormReactionsTableMap::COL_FORM_ID, $formId, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildFormReactions $formReactions Object to remove from the list of results
     *
     * @return $this|ChildFormReactionsQuery The current query, for fluid interface
     */
    public function prune($formReactions = null)
    {
        if ($formReactions) {
            $this->addUsingAlias(FormReactionsTableMap::COL_ID, $formReactions->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the form_reactions table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(FormReactionsTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            FormReactionsTableMap::clearInstancePool();
            FormReactionsTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(FormReactionsTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(FormReactionsTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            FormReactionsTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            FormReactionsTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // FormReactionsQuery
