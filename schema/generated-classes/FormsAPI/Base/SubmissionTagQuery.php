<?php

namespace FormsAPI\Base;

use \Exception;
use \PDO;
use FormsAPI\SubmissionTag as ChildSubmissionTag;
use FormsAPI\SubmissionTagQuery as ChildSubmissionTagQuery;
use FormsAPI\Map\SubmissionTagTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'submission_tag' table.
 *
 *
 *
 * @method     ChildSubmissionTagQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildSubmissionTagQuery orderBySubmissionId($order = Criteria::ASC) Order by the submission_id column
 * @method     ChildSubmissionTagQuery orderByTagId($order = Criteria::ASC) Order by the tag_id column
 *
 * @method     ChildSubmissionTagQuery groupById() Group by the id column
 * @method     ChildSubmissionTagQuery groupBySubmissionId() Group by the submission_id column
 * @method     ChildSubmissionTagQuery groupByTagId() Group by the tag_id column
 *
 * @method     ChildSubmissionTagQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildSubmissionTagQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildSubmissionTagQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildSubmissionTagQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildSubmissionTagQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildSubmissionTagQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildSubmissionTag findOne(ConnectionInterface $con = null) Return the first ChildSubmissionTag matching the query
 * @method     ChildSubmissionTag findOneOrCreate(ConnectionInterface $con = null) Return the first ChildSubmissionTag matching the query, or a new ChildSubmissionTag object populated from the query conditions when no match is found
 *
 * @method     ChildSubmissionTag findOneById(int $id) Return the first ChildSubmissionTag filtered by the id column
 * @method     ChildSubmissionTag findOneBySubmissionId(int $submission_id) Return the first ChildSubmissionTag filtered by the submission_id column
 * @method     ChildSubmissionTag findOneByTagId(int $tag_id) Return the first ChildSubmissionTag filtered by the tag_id column *

 * @method     ChildSubmissionTag requirePk($key, ConnectionInterface $con = null) Return the ChildSubmissionTag by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSubmissionTag requireOne(ConnectionInterface $con = null) Return the first ChildSubmissionTag matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSubmissionTag requireOneById(int $id) Return the first ChildSubmissionTag filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSubmissionTag requireOneBySubmissionId(int $submission_id) Return the first ChildSubmissionTag filtered by the submission_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSubmissionTag requireOneByTagId(int $tag_id) Return the first ChildSubmissionTag filtered by the tag_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSubmissionTag[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildSubmissionTag objects based on current ModelCriteria
 * @method     ChildSubmissionTag[]|ObjectCollection findById(int $id) Return ChildSubmissionTag objects filtered by the id column
 * @method     ChildSubmissionTag[]|ObjectCollection findBySubmissionId(int $submission_id) Return ChildSubmissionTag objects filtered by the submission_id column
 * @method     ChildSubmissionTag[]|ObjectCollection findByTagId(int $tag_id) Return ChildSubmissionTag objects filtered by the tag_id column
 * @method     ChildSubmissionTag[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class SubmissionTagQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \FormsAPI\Base\SubmissionTagQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\FormsAPI\\SubmissionTag', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildSubmissionTagQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildSubmissionTagQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildSubmissionTagQuery) {
            return $criteria;
        }
        $query = new ChildSubmissionTagQuery();
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
     * @return ChildSubmissionTag|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SubmissionTagTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = SubmissionTagTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @return ChildSubmissionTag A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, submission_id, tag_id FROM submission_tag WHERE id = :p0';
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
            /** @var ChildSubmissionTag $obj */
            $obj = new ChildSubmissionTag();
            $obj->hydrate($row);
            SubmissionTagTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildSubmissionTag|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildSubmissionTagQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SubmissionTagTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildSubmissionTagQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SubmissionTagTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildSubmissionTagQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(SubmissionTagTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(SubmissionTagTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SubmissionTagTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the submission_id column
     *
     * Example usage:
     * <code>
     * $query->filterBySubmissionId(1234); // WHERE submission_id = 1234
     * $query->filterBySubmissionId(array(12, 34)); // WHERE submission_id IN (12, 34)
     * $query->filterBySubmissionId(array('min' => 12)); // WHERE submission_id > 12
     * </code>
     *
     * @param     mixed $submissionId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSubmissionTagQuery The current query, for fluid interface
     */
    public function filterBySubmissionId($submissionId = null, $comparison = null)
    {
        if (is_array($submissionId)) {
            $useMinMax = false;
            if (isset($submissionId['min'])) {
                $this->addUsingAlias(SubmissionTagTableMap::COL_SUBMISSION_ID, $submissionId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($submissionId['max'])) {
                $this->addUsingAlias(SubmissionTagTableMap::COL_SUBMISSION_ID, $submissionId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SubmissionTagTableMap::COL_SUBMISSION_ID, $submissionId, $comparison);
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
     * @return $this|ChildSubmissionTagQuery The current query, for fluid interface
     */
    public function filterByTagId($tagId = null, $comparison = null)
    {
        if (is_array($tagId)) {
            $useMinMax = false;
            if (isset($tagId['min'])) {
                $this->addUsingAlias(SubmissionTagTableMap::COL_TAG_ID, $tagId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($tagId['max'])) {
                $this->addUsingAlias(SubmissionTagTableMap::COL_TAG_ID, $tagId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SubmissionTagTableMap::COL_TAG_ID, $tagId, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildSubmissionTag $submissionTag Object to remove from the list of results
     *
     * @return $this|ChildSubmissionTagQuery The current query, for fluid interface
     */
    public function prune($submissionTag = null)
    {
        if ($submissionTag) {
            $this->addUsingAlias(SubmissionTagTableMap::COL_ID, $submissionTag->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the submission_tag table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SubmissionTagTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            SubmissionTagTableMap::clearInstancePool();
            SubmissionTagTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(SubmissionTagTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(SubmissionTagTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            SubmissionTagTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            SubmissionTagTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // SubmissionTagQuery
