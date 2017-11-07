<?php

namespace FormsAPI\Base;

use \Exception;
use \PDO;
use FormsAPI\ChildFormRelationship as ChildChildFormRelationship;
use FormsAPI\ChildFormRelationshipQuery as ChildChildFormRelationshipQuery;
use FormsAPI\Map\ChildFormRelationshipTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'child_form_relationship' table.
 *
 *
 *
 * @method     ChildChildFormRelationshipQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildChildFormRelationshipQuery orderByparentName($order = Criteria::ASC) Order by the parent_id column
 * @method     ChildChildFormRelationshipQuery orderByChildId($order = Criteria::ASC) Order by the child_id column
 * @method     ChildChildFormRelationshipQuery orderByTagId($order = Criteria::ASC) Order by the tag_id column
 * @method     ChildChildFormRelationshipQuery orderByReactionId($order = Criteria::ASC) Order by the reaction_id column
 *
 * @method     ChildChildFormRelationshipQuery groupById() Group by the id column
 * @method     ChildChildFormRelationshipQuery groupByparentName() Group by the parent_id column
 * @method     ChildChildFormRelationshipQuery groupByChildId() Group by the child_id column
 * @method     ChildChildFormRelationshipQuery groupByTagId() Group by the tag_id column
 * @method     ChildChildFormRelationshipQuery groupByReactionId() Group by the reaction_id column
 *
 * @method     ChildChildFormRelationshipQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildChildFormRelationshipQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildChildFormRelationshipQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildChildFormRelationshipQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildChildFormRelationshipQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildChildFormRelationshipQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildChildFormRelationship findOne(ConnectionInterface $con = null) Return the first ChildChildFormRelationship matching the query
 * @method     ChildChildFormRelationship findOneOrCreate(ConnectionInterface $con = null) Return the first ChildChildFormRelationship matching the query, or a new ChildChildFormRelationship object populated from the query conditions when no match is found
 *
 * @method     ChildChildFormRelationship findOneById(int $id) Return the first ChildChildFormRelationship filtered by the id column
 * @method     ChildChildFormRelationship findOneByparentName(int $parent_id) Return the first ChildChildFormRelationship filtered by the parent_id column
 * @method     ChildChildFormRelationship findOneByChildId(int $child_id) Return the first ChildChildFormRelationship filtered by the child_id column
 * @method     ChildChildFormRelationship findOneByTagId(int $tag_id) Return the first ChildChildFormRelationship filtered by the tag_id column
 * @method     ChildChildFormRelationship findOneByReactionId(int $reaction_id) Return the first ChildChildFormRelationship filtered by the reaction_id column *

 * @method     ChildChildFormRelationship requirePk($key, ConnectionInterface $con = null) Return the ChildChildFormRelationship by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildChildFormRelationship requireOne(ConnectionInterface $con = null) Return the first ChildChildFormRelationship matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildChildFormRelationship requireOneById(int $id) Return the first ChildChildFormRelationship filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildChildFormRelationship requireOneByparentName(int $parent_id) Return the first ChildChildFormRelationship filtered by the parent_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildChildFormRelationship requireOneByChildId(int $child_id) Return the first ChildChildFormRelationship filtered by the child_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildChildFormRelationship requireOneByTagId(int $tag_id) Return the first ChildChildFormRelationship filtered by the tag_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildChildFormRelationship requireOneByReactionId(int $reaction_id) Return the first ChildChildFormRelationship filtered by the reaction_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildChildFormRelationship[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildChildFormRelationship objects based on current ModelCriteria
 * @method     ChildChildFormRelationship[]|ObjectCollection findById(int $id) Return ChildChildFormRelationship objects filtered by the id column
 * @method     ChildChildFormRelationship[]|ObjectCollection findByparentName(int $parent_id) Return ChildChildFormRelationship objects filtered by the parent_id column
 * @method     ChildChildFormRelationship[]|ObjectCollection findByChildId(int $child_id) Return ChildChildFormRelationship objects filtered by the child_id column
 * @method     ChildChildFormRelationship[]|ObjectCollection findByTagId(int $tag_id) Return ChildChildFormRelationship objects filtered by the tag_id column
 * @method     ChildChildFormRelationship[]|ObjectCollection findByReactionId(int $reaction_id) Return ChildChildFormRelationship objects filtered by the reaction_id column
 * @method     ChildChildFormRelationship[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class ChildFormRelationshipQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \FormsAPI\Base\ChildFormRelationshipQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\FormsAPI\\ChildFormRelationship', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildChildFormRelationshipQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildChildFormRelationshipQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildChildFormRelationshipQuery) {
            return $criteria;
        }
        $query = new ChildChildFormRelationshipQuery();
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
     * @return ChildChildFormRelationship|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ChildFormRelationshipTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = ChildFormRelationshipTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @return ChildChildFormRelationship A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, parent_id, child_id, tag_id, reaction_id FROM child_form_relationship WHERE id = :p0';
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
            /** @var ChildChildFormRelationship $obj */
            $obj = new ChildChildFormRelationship();
            $obj->hydrate($row);
            ChildFormRelationshipTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildChildFormRelationship|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildChildFormRelationshipQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ChildFormRelationshipTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildChildFormRelationshipQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ChildFormRelationshipTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildChildFormRelationshipQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ChildFormRelationshipTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ChildFormRelationshipTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ChildFormRelationshipTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the parent_id column
     *
     * Example usage:
     * <code>
     * $query->filterByparentName(1234); // WHERE parent_id = 1234
     * $query->filterByparentName(array(12, 34)); // WHERE parent_id IN (12, 34)
     * $query->filterByparentName(array('min' => 12)); // WHERE parent_id > 12
     * </code>
     *
     * @param     mixed $parentName The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildChildFormRelationshipQuery The current query, for fluid interface
     */
    public function filterByparentName($parentName = null, $comparison = null)
    {
        if (is_array($parentName)) {
            $useMinMax = false;
            if (isset($parentName['min'])) {
                $this->addUsingAlias(ChildFormRelationshipTableMap::COL_PARENT_ID, $parentName['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($parentName['max'])) {
                $this->addUsingAlias(ChildFormRelationshipTableMap::COL_PARENT_ID, $parentName['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ChildFormRelationshipTableMap::COL_PARENT_ID, $parentName, $comparison);
    }

    /**
     * Filter the query on the child_id column
     *
     * Example usage:
     * <code>
     * $query->filterByChildId(1234); // WHERE child_id = 1234
     * $query->filterByChildId(array(12, 34)); // WHERE child_id IN (12, 34)
     * $query->filterByChildId(array('min' => 12)); // WHERE child_id > 12
     * </code>
     *
     * @param     mixed $childId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildChildFormRelationshipQuery The current query, for fluid interface
     */
    public function filterByChildId($childId = null, $comparison = null)
    {
        if (is_array($childId)) {
            $useMinMax = false;
            if (isset($childId['min'])) {
                $this->addUsingAlias(ChildFormRelationshipTableMap::COL_CHILD_ID, $childId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($childId['max'])) {
                $this->addUsingAlias(ChildFormRelationshipTableMap::COL_CHILD_ID, $childId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ChildFormRelationshipTableMap::COL_CHILD_ID, $childId, $comparison);
    }

    /**
     * Filter the query on the tag_id column
     *
     * Example usage:
     * <code>
     * $query->filterByTagId(1234); // WHERE tag_id = 1234
     * $query->filterByTagId(array(12, 34)); // WHERE tag_id IN (12, 34)
     * $query->filterByTagId(array('min' => 12)); // WHERE tag_id > 12
     * </code>
     *
     * @param     mixed $tagId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildChildFormRelationshipQuery The current query, for fluid interface
     */
    public function filterByTagId($tagId = null, $comparison = null)
    {
        if (is_array($tagId)) {
            $useMinMax = false;
            if (isset($tagId['min'])) {
                $this->addUsingAlias(ChildFormRelationshipTableMap::COL_TAG_ID, $tagId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($tagId['max'])) {
                $this->addUsingAlias(ChildFormRelationshipTableMap::COL_TAG_ID, $tagId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ChildFormRelationshipTableMap::COL_TAG_ID, $tagId, $comparison);
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
     * @return $this|ChildChildFormRelationshipQuery The current query, for fluid interface
     */
    public function filterByReactionId($reactionId = null, $comparison = null)
    {
        if (is_array($reactionId)) {
            $useMinMax = false;
            if (isset($reactionId['min'])) {
                $this->addUsingAlias(ChildFormRelationshipTableMap::COL_REACTION_ID, $reactionId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($reactionId['max'])) {
                $this->addUsingAlias(ChildFormRelationshipTableMap::COL_REACTION_ID, $reactionId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ChildFormRelationshipTableMap::COL_REACTION_ID, $reactionId, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildChildFormRelationship $childFormRelationship Object to remove from the list of results
     *
     * @return $this|ChildChildFormRelationshipQuery The current query, for fluid interface
     */
    public function prune($childFormRelationship = null)
    {
        if ($childFormRelationship) {
            $this->addUsingAlias(ChildFormRelationshipTableMap::COL_ID, $childFormRelationship->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the child_form_relationship table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ChildFormRelationshipTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            ChildFormRelationshipTableMap::clearInstancePool();
            ChildFormRelationshipTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(ChildFormRelationshipTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ChildFormRelationshipTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            ChildFormRelationshipTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            ChildFormRelationshipTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // ChildFormRelationshipQuery
