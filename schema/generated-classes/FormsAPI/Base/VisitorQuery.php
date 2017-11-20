<?php

namespace FormsAPI\Base;

use \Exception;
use \PDO;
use FormsAPI\Visitor as ChildVisitor;
use FormsAPI\VisitorQuery as ChildVisitorQuery;
use FormsAPI\Map\VisitorTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'visitor' table.
 *
 *
 *
 * @method     ChildVisitorQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildVisitorQuery orderByUWStudentNumber($order = Criteria::ASC) Order by the uw_student_number column
 * @method     ChildVisitorQuery orderByUWNetID($order = Criteria::ASC) Order by the uw_net_id column
 * @method     ChildVisitorQuery orderByFirstName($order = Criteria::ASC) Order by the first_name column
 * @method     ChildVisitorQuery orderByMiddleName($order = Criteria::ASC) Order by the middle_name column
 * @method     ChildVisitorQuery orderByLastName($order = Criteria::ASC) Order by the last_name column
 *
 * @method     ChildVisitorQuery groupById() Group by the id column
 * @method     ChildVisitorQuery groupByUWStudentNumber() Group by the uw_student_number column
 * @method     ChildVisitorQuery groupByUWNetID() Group by the uw_net_id column
 * @method     ChildVisitorQuery groupByFirstName() Group by the first_name column
 * @method     ChildVisitorQuery groupByMiddleName() Group by the middle_name column
 * @method     ChildVisitorQuery groupByLastName() Group by the last_name column
 *
 * @method     ChildVisitorQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildVisitorQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildVisitorQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildVisitorQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildVisitorQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildVisitorQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildVisitorQuery leftJoinSubmissionRelatedByVisitorId($relationAlias = null) Adds a LEFT JOIN clause to the query using the SubmissionRelatedByVisitorId relation
 * @method     ChildVisitorQuery rightJoinSubmissionRelatedByVisitorId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SubmissionRelatedByVisitorId relation
 * @method     ChildVisitorQuery innerJoinSubmissionRelatedByVisitorId($relationAlias = null) Adds a INNER JOIN clause to the query using the SubmissionRelatedByVisitorId relation
 *
 * @method     ChildVisitorQuery joinWithSubmissionRelatedByVisitorId($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the SubmissionRelatedByVisitorId relation
 *
 * @method     ChildVisitorQuery leftJoinWithSubmissionRelatedByVisitorId() Adds a LEFT JOIN clause and with to the query using the SubmissionRelatedByVisitorId relation
 * @method     ChildVisitorQuery rightJoinWithSubmissionRelatedByVisitorId() Adds a RIGHT JOIN clause and with to the query using the SubmissionRelatedByVisitorId relation
 * @method     ChildVisitorQuery innerJoinWithSubmissionRelatedByVisitorId() Adds a INNER JOIN clause and with to the query using the SubmissionRelatedByVisitorId relation
 *
 * @method     ChildVisitorQuery leftJoinAsAssignee($relationAlias = null) Adds a LEFT JOIN clause to the query using the AsAssignee relation
 * @method     ChildVisitorQuery rightJoinAsAssignee($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AsAssignee relation
 * @method     ChildVisitorQuery innerJoinAsAssignee($relationAlias = null) Adds a INNER JOIN clause to the query using the AsAssignee relation
 *
 * @method     ChildVisitorQuery joinWithAsAssignee($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the AsAssignee relation
 *
 * @method     ChildVisitorQuery leftJoinWithAsAssignee() Adds a LEFT JOIN clause and with to the query using the AsAssignee relation
 * @method     ChildVisitorQuery rightJoinWithAsAssignee() Adds a RIGHT JOIN clause and with to the query using the AsAssignee relation
 * @method     ChildVisitorQuery innerJoinWithAsAssignee() Adds a INNER JOIN clause and with to the query using the AsAssignee relation
 *
 * @method     \FormsAPI\SubmissionQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildVisitor findOne(ConnectionInterface $con = null) Return the first ChildVisitor matching the query
 * @method     ChildVisitor findOneOrCreate(ConnectionInterface $con = null) Return the first ChildVisitor matching the query, or a new ChildVisitor object populated from the query conditions when no match is found
 *
 * @method     ChildVisitor findOneById(int $id) Return the first ChildVisitor filtered by the id column
 * @method     ChildVisitor findOneByUWStudentNumber(string $uw_student_number) Return the first ChildVisitor filtered by the uw_student_number column
 * @method     ChildVisitor findOneByUWNetID(string $uw_net_id) Return the first ChildVisitor filtered by the uw_net_id column
 * @method     ChildVisitor findOneByFirstName(string $first_name) Return the first ChildVisitor filtered by the first_name column
 * @method     ChildVisitor findOneByMiddleName(string $middle_name) Return the first ChildVisitor filtered by the middle_name column
 * @method     ChildVisitor findOneByLastName(string $last_name) Return the first ChildVisitor filtered by the last_name column *

 * @method     ChildVisitor requirePk($key, ConnectionInterface $con = null) Return the ChildVisitor by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildVisitor requireOne(ConnectionInterface $con = null) Return the first ChildVisitor matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildVisitor requireOneById(int $id) Return the first ChildVisitor filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildVisitor requireOneByUWStudentNumber(string $uw_student_number) Return the first ChildVisitor filtered by the uw_student_number column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildVisitor requireOneByUWNetID(string $uw_net_id) Return the first ChildVisitor filtered by the uw_net_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildVisitor requireOneByFirstName(string $first_name) Return the first ChildVisitor filtered by the first_name column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildVisitor requireOneByMiddleName(string $middle_name) Return the first ChildVisitor filtered by the middle_name column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildVisitor requireOneByLastName(string $last_name) Return the first ChildVisitor filtered by the last_name column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildVisitor[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildVisitor objects based on current ModelCriteria
 * @method     ChildVisitor[]|ObjectCollection findById(int $id) Return ChildVisitor objects filtered by the id column
 * @method     ChildVisitor[]|ObjectCollection findByUWStudentNumber(string $uw_student_number) Return ChildVisitor objects filtered by the uw_student_number column
 * @method     ChildVisitor[]|ObjectCollection findByUWNetID(string $uw_net_id) Return ChildVisitor objects filtered by the uw_net_id column
 * @method     ChildVisitor[]|ObjectCollection findByFirstName(string $first_name) Return ChildVisitor objects filtered by the first_name column
 * @method     ChildVisitor[]|ObjectCollection findByMiddleName(string $middle_name) Return ChildVisitor objects filtered by the middle_name column
 * @method     ChildVisitor[]|ObjectCollection findByLastName(string $last_name) Return ChildVisitor objects filtered by the last_name column
 * @method     ChildVisitor[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class VisitorQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \FormsAPI\Base\VisitorQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\FormsAPI\\Visitor', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildVisitorQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildVisitorQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildVisitorQuery) {
            return $criteria;
        }
        $query = new ChildVisitorQuery();
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
     * @return ChildVisitor|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(VisitorTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = VisitorTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @return ChildVisitor A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, uw_student_number, uw_net_id, first_name, middle_name, last_name FROM visitor WHERE id = :p0';
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
            /** @var ChildVisitor $obj */
            $obj = new ChildVisitor();
            $obj->hydrate($row);
            VisitorTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildVisitor|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildVisitorQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(VisitorTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildVisitorQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(VisitorTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildVisitorQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(VisitorTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(VisitorTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(VisitorTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the uw_student_number column
     *
     * Example usage:
     * <code>
     * $query->filterByUWStudentNumber('fooValue');   // WHERE uw_student_number = 'fooValue'
     * $query->filterByUWStudentNumber('%fooValue%', Criteria::LIKE); // WHERE uw_student_number LIKE '%fooValue%'
     * </code>
     *
     * @param     string $uWStudentNumber The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildVisitorQuery The current query, for fluid interface
     */
    public function filterByUWStudentNumber($uWStudentNumber = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($uWStudentNumber)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(VisitorTableMap::COL_UW_STUDENT_NUMBER, $uWStudentNumber, $comparison);
    }

    /**
     * Filter the query on the uw_net_id column
     *
     * Example usage:
     * <code>
     * $query->filterByUWNetID('fooValue');   // WHERE uw_net_id = 'fooValue'
     * $query->filterByUWNetID('%fooValue%', Criteria::LIKE); // WHERE uw_net_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $uWNetID The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildVisitorQuery The current query, for fluid interface
     */
    public function filterByUWNetID($uWNetID = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($uWNetID)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(VisitorTableMap::COL_UW_NET_ID, $uWNetID, $comparison);
    }

    /**
     * Filter the query on the first_name column
     *
     * Example usage:
     * <code>
     * $query->filterByFirstName('fooValue');   // WHERE first_name = 'fooValue'
     * $query->filterByFirstName('%fooValue%', Criteria::LIKE); // WHERE first_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $firstName The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildVisitorQuery The current query, for fluid interface
     */
    public function filterByFirstName($firstName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($firstName)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(VisitorTableMap::COL_FIRST_NAME, $firstName, $comparison);
    }

    /**
     * Filter the query on the middle_name column
     *
     * Example usage:
     * <code>
     * $query->filterByMiddleName('fooValue');   // WHERE middle_name = 'fooValue'
     * $query->filterByMiddleName('%fooValue%', Criteria::LIKE); // WHERE middle_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $middleName The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildVisitorQuery The current query, for fluid interface
     */
    public function filterByMiddleName($middleName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($middleName)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(VisitorTableMap::COL_MIDDLE_NAME, $middleName, $comparison);
    }

    /**
     * Filter the query on the last_name column
     *
     * Example usage:
     * <code>
     * $query->filterByLastName('fooValue');   // WHERE last_name = 'fooValue'
     * $query->filterByLastName('%fooValue%', Criteria::LIKE); // WHERE last_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $lastName The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildVisitorQuery The current query, for fluid interface
     */
    public function filterByLastName($lastName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($lastName)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(VisitorTableMap::COL_LAST_NAME, $lastName, $comparison);
    }

    /**
     * Filter the query by a related \FormsAPI\Submission object
     *
     * @param \FormsAPI\Submission|ObjectCollection $submission the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildVisitorQuery The current query, for fluid interface
     */
    public function filterBySubmissionRelatedByVisitorId($submission, $comparison = null)
    {
        if ($submission instanceof \FormsAPI\Submission) {
            return $this
                ->addUsingAlias(VisitorTableMap::COL_ID, $submission->getVisitorId(), $comparison);
        } elseif ($submission instanceof ObjectCollection) {
            return $this
                ->useSubmissionRelatedByVisitorIdQuery()
                ->filterByPrimaryKeys($submission->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySubmissionRelatedByVisitorId() only accepts arguments of type \FormsAPI\Submission or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SubmissionRelatedByVisitorId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildVisitorQuery The current query, for fluid interface
     */
    public function joinSubmissionRelatedByVisitorId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SubmissionRelatedByVisitorId');

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
            $this->addJoinObject($join, 'SubmissionRelatedByVisitorId');
        }

        return $this;
    }

    /**
     * Use the SubmissionRelatedByVisitorId relation Submission object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \FormsAPI\SubmissionQuery A secondary query class using the current class as primary query
     */
    public function useSubmissionRelatedByVisitorIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSubmissionRelatedByVisitorId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SubmissionRelatedByVisitorId', '\FormsAPI\SubmissionQuery');
    }

    /**
     * Filter the query by a related \FormsAPI\Submission object
     *
     * @param \FormsAPI\Submission|ObjectCollection $submission the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildVisitorQuery The current query, for fluid interface
     */
    public function filterByAsAssignee($submission, $comparison = null)
    {
        if ($submission instanceof \FormsAPI\Submission) {
            return $this
                ->addUsingAlias(VisitorTableMap::COL_ID, $submission->getAssigneeId(), $comparison);
        } elseif ($submission instanceof ObjectCollection) {
            return $this
                ->useAsAssigneeQuery()
                ->filterByPrimaryKeys($submission->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAsAssignee() only accepts arguments of type \FormsAPI\Submission or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AsAssignee relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildVisitorQuery The current query, for fluid interface
     */
    public function joinAsAssignee($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AsAssignee');

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
            $this->addJoinObject($join, 'AsAssignee');
        }

        return $this;
    }

    /**
     * Use the AsAssignee relation Submission object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \FormsAPI\SubmissionQuery A secondary query class using the current class as primary query
     */
    public function useAsAssigneeQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinAsAssignee($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AsAssignee', '\FormsAPI\SubmissionQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildVisitor $visitor Object to remove from the list of results
     *
     * @return $this|ChildVisitorQuery The current query, for fluid interface
     */
    public function prune($visitor = null)
    {
        if ($visitor) {
            $this->addUsingAlias(VisitorTableMap::COL_ID, $visitor->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the visitor table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(VisitorTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            VisitorTableMap::clearInstancePool();
            VisitorTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(VisitorTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(VisitorTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            VisitorTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            VisitorTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // VisitorQuery
