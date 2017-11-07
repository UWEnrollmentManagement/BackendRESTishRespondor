<?php

namespace FormsAPI\Base;

use \Exception;
use \PDO;
use FormsAPI\ElementChoices as ChildElementChoices;
use FormsAPI\ElementChoicesQuery as ChildElementChoicesQuery;
use FormsAPI\Map\ElementChoicesTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'element_choices' table.
 *
 *
 *
 * @method     ChildElementChoicesQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildElementChoicesQuery orderByElementId($order = Criteria::ASC) Order by the element_id column
 * @method     ChildElementChoicesQuery orderByChoiceId($order = Criteria::ASC) Order by the choice_id column
 *
 * @method     ChildElementChoicesQuery groupById() Group by the id column
 * @method     ChildElementChoicesQuery groupByElementId() Group by the element_id column
 * @method     ChildElementChoicesQuery groupByChoiceId() Group by the choice_id column
 *
 * @method     ChildElementChoicesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildElementChoicesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildElementChoicesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildElementChoicesQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildElementChoicesQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildElementChoicesQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildElementChoices findOne(ConnectionInterface $con = null) Return the first ChildElementChoices matching the query
 * @method     ChildElementChoices findOneOrCreate(ConnectionInterface $con = null) Return the first ChildElementChoices matching the query, or a new ChildElementChoices object populated from the query conditions when no match is found
 *
 * @method     ChildElementChoices findOneById(int $id) Return the first ChildElementChoices filtered by the id column
 * @method     ChildElementChoices findOneByElementId(int $element_id) Return the first ChildElementChoices filtered by the element_id column
 * @method     ChildElementChoices findOneByChoiceId(int $choice_id) Return the first ChildElementChoices filtered by the choice_id column *

 * @method     ChildElementChoices requirePk($key, ConnectionInterface $con = null) Return the ChildElementChoices by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildElementChoices requireOne(ConnectionInterface $con = null) Return the first ChildElementChoices matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildElementChoices requireOneById(int $id) Return the first ChildElementChoices filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildElementChoices requireOneByElementId(int $element_id) Return the first ChildElementChoices filtered by the element_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildElementChoices requireOneByChoiceId(int $choice_id) Return the first ChildElementChoices filtered by the choice_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildElementChoices[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildElementChoices objects based on current ModelCriteria
 * @method     ChildElementChoices[]|ObjectCollection findById(int $id) Return ChildElementChoices objects filtered by the id column
 * @method     ChildElementChoices[]|ObjectCollection findByElementId(int $element_id) Return ChildElementChoices objects filtered by the element_id column
 * @method     ChildElementChoices[]|ObjectCollection findByChoiceId(int $choice_id) Return ChildElementChoices objects filtered by the choice_id column
 * @method     ChildElementChoices[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class ElementChoicesQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \FormsAPI\Base\ElementChoicesQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\FormsAPI\\ElementChoices', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildElementChoicesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildElementChoicesQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildElementChoicesQuery) {
            return $criteria;
        }
        $query = new ChildElementChoicesQuery();
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
     * @return ChildElementChoices|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ElementChoicesTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = ElementChoicesTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @return ChildElementChoices A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, element_id, choice_id FROM element_choices WHERE id = :p0';
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
            /** @var ChildElementChoices $obj */
            $obj = new ChildElementChoices();
            $obj->hydrate($row);
            ElementChoicesTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildElementChoices|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildElementChoicesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ElementChoicesTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildElementChoicesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ElementChoicesTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildElementChoicesQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ElementChoicesTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ElementChoicesTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ElementChoicesTableMap::COL_ID, $id, $comparison);
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
     * @return $this|ChildElementChoicesQuery The current query, for fluid interface
     */
    public function filterByElementId($elementId = null, $comparison = null)
    {
        if (is_array($elementId)) {
            $useMinMax = false;
            if (isset($elementId['min'])) {
                $this->addUsingAlias(ElementChoicesTableMap::COL_ELEMENT_ID, $elementId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($elementId['max'])) {
                $this->addUsingAlias(ElementChoicesTableMap::COL_ELEMENT_ID, $elementId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ElementChoicesTableMap::COL_ELEMENT_ID, $elementId, $comparison);
    }

    /**
     * Filter the query on the choice_id column
     *
     * Example usage:
     * <code>
     * $query->filterByChoiceId(1234); // WHERE choice_id = 1234
     * $query->filterByChoiceId(array(12, 34)); // WHERE choice_id IN (12, 34)
     * $query->filterByChoiceId(array('min' => 12)); // WHERE choice_id > 12
     * </code>
     *
     * @param     mixed $choiceId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildElementChoicesQuery The current query, for fluid interface
     */
    public function filterByChoiceId($choiceId = null, $comparison = null)
    {
        if (is_array($choiceId)) {
            $useMinMax = false;
            if (isset($choiceId['min'])) {
                $this->addUsingAlias(ElementChoicesTableMap::COL_CHOICE_ID, $choiceId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($choiceId['max'])) {
                $this->addUsingAlias(ElementChoicesTableMap::COL_CHOICE_ID, $choiceId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ElementChoicesTableMap::COL_CHOICE_ID, $choiceId, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildElementChoices $elementChoices Object to remove from the list of results
     *
     * @return $this|ChildElementChoicesQuery The current query, for fluid interface
     */
    public function prune($elementChoices = null)
    {
        if ($elementChoices) {
            $this->addUsingAlias(ElementChoicesTableMap::COL_ID, $elementChoices->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the element_choices table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ElementChoicesTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            ElementChoicesTableMap::clearInstancePool();
            ElementChoicesTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(ElementChoicesTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ElementChoicesTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            ElementChoicesTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            ElementChoicesTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // ElementChoicesQuery
