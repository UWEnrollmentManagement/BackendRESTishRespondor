<?php

namespace FormsAPI\Base;

use \Exception;
use \PDO;
use FormsAPI\Reaction as ChildReaction;
use FormsAPI\ReactionQuery as ChildReactionQuery;
use FormsAPI\Map\ReactionTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'reaction' table.
 *
 *
 *
 * @method     ChildReactionQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildReactionQuery orderBySubject($order = Criteria::ASC) Order by the subject column
 * @method     ChildReactionQuery orderByRecipient($order = Criteria::ASC) Order by the recipient column
 * @method     ChildReactionQuery orderBySender($order = Criteria::ASC) Order by the sender column
 * @method     ChildReactionQuery orderByReplyto($order = Criteria::ASC) Order by the replyTo column
 * @method     ChildReactionQuery orderByCc($order = Criteria::ASC) Order by the cc column
 * @method     ChildReactionQuery orderByBcc($order = Criteria::ASC) Order by the bcc column
 * @method     ChildReactionQuery orderByTemplate($order = Criteria::ASC) Order by the template column
 * @method     ChildReactionQuery orderByContent($order = Criteria::ASC) Order by the content column
 *
 * @method     ChildReactionQuery groupById() Group by the id column
 * @method     ChildReactionQuery groupBySubject() Group by the subject column
 * @method     ChildReactionQuery groupByRecipient() Group by the recipient column
 * @method     ChildReactionQuery groupBySender() Group by the sender column
 * @method     ChildReactionQuery groupByReplyto() Group by the replyTo column
 * @method     ChildReactionQuery groupByCc() Group by the cc column
 * @method     ChildReactionQuery groupByBcc() Group by the bcc column
 * @method     ChildReactionQuery groupByTemplate() Group by the template column
 * @method     ChildReactionQuery groupByContent() Group by the content column
 *
 * @method     ChildReactionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildReactionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildReactionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildReactionQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildReactionQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildReactionQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildReaction findOne(ConnectionInterface $con = null) Return the first ChildReaction matching the query
 * @method     ChildReaction findOneOrCreate(ConnectionInterface $con = null) Return the first ChildReaction matching the query, or a new ChildReaction object populated from the query conditions when no match is found
 *
 * @method     ChildReaction findOneById(int $id) Return the first ChildReaction filtered by the id column
 * @method     ChildReaction findOneBySubject(string $subject) Return the first ChildReaction filtered by the subject column
 * @method     ChildReaction findOneByRecipient(string $recipient) Return the first ChildReaction filtered by the recipient column
 * @method     ChildReaction findOneBySender(string $sender) Return the first ChildReaction filtered by the sender column
 * @method     ChildReaction findOneByReplyto(string $replyTo) Return the first ChildReaction filtered by the replyTo column
 * @method     ChildReaction findOneByCc(string $cc) Return the first ChildReaction filtered by the cc column
 * @method     ChildReaction findOneByBcc(string $bcc) Return the first ChildReaction filtered by the bcc column
 * @method     ChildReaction findOneByTemplate(string $template) Return the first ChildReaction filtered by the template column
 * @method     ChildReaction findOneByContent(string $content) Return the first ChildReaction filtered by the content column *

 * @method     ChildReaction requirePk($key, ConnectionInterface $con = null) Return the ChildReaction by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildReaction requireOne(ConnectionInterface $con = null) Return the first ChildReaction matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildReaction requireOneById(int $id) Return the first ChildReaction filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildReaction requireOneBySubject(string $subject) Return the first ChildReaction filtered by the subject column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildReaction requireOneByRecipient(string $recipient) Return the first ChildReaction filtered by the recipient column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildReaction requireOneBySender(string $sender) Return the first ChildReaction filtered by the sender column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildReaction requireOneByReplyto(string $replyTo) Return the first ChildReaction filtered by the replyTo column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildReaction requireOneByCc(string $cc) Return the first ChildReaction filtered by the cc column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildReaction requireOneByBcc(string $bcc) Return the first ChildReaction filtered by the bcc column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildReaction requireOneByTemplate(string $template) Return the first ChildReaction filtered by the template column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildReaction requireOneByContent(string $content) Return the first ChildReaction filtered by the content column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildReaction[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildReaction objects based on current ModelCriteria
 * @method     ChildReaction[]|ObjectCollection findById(int $id) Return ChildReaction objects filtered by the id column
 * @method     ChildReaction[]|ObjectCollection findBySubject(string $subject) Return ChildReaction objects filtered by the subject column
 * @method     ChildReaction[]|ObjectCollection findByRecipient(string $recipient) Return ChildReaction objects filtered by the recipient column
 * @method     ChildReaction[]|ObjectCollection findBySender(string $sender) Return ChildReaction objects filtered by the sender column
 * @method     ChildReaction[]|ObjectCollection findByReplyto(string $replyTo) Return ChildReaction objects filtered by the replyTo column
 * @method     ChildReaction[]|ObjectCollection findByCc(string $cc) Return ChildReaction objects filtered by the cc column
 * @method     ChildReaction[]|ObjectCollection findByBcc(string $bcc) Return ChildReaction objects filtered by the bcc column
 * @method     ChildReaction[]|ObjectCollection findByTemplate(string $template) Return ChildReaction objects filtered by the template column
 * @method     ChildReaction[]|ObjectCollection findByContent(string $content) Return ChildReaction objects filtered by the content column
 * @method     ChildReaction[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class ReactionQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \FormsAPI\Base\ReactionQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\FormsAPI\\Reaction', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildReactionQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildReactionQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildReactionQuery) {
            return $criteria;
        }
        $query = new ChildReactionQuery();
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
     * @return ChildReaction|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ReactionTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = ReactionTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @return ChildReaction A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, subject, recipient, sender, replyTo, cc, bcc, template, content FROM reaction WHERE id = :p0';
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
            /** @var ChildReaction $obj */
            $obj = new ChildReaction();
            $obj->hydrate($row);
            ReactionTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildReaction|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildReactionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ReactionTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildReactionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ReactionTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildReactionQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ReactionTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ReactionTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ReactionTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the subject column
     *
     * Example usage:
     * <code>
     * $query->filterBySubject('fooValue');   // WHERE subject = 'fooValue'
     * $query->filterBySubject('%fooValue%', Criteria::LIKE); // WHERE subject LIKE '%fooValue%'
     * </code>
     *
     * @param     string $subject The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildReactionQuery The current query, for fluid interface
     */
    public function filterBySubject($subject = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($subject)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ReactionTableMap::COL_SUBJECT, $subject, $comparison);
    }

    /**
     * Filter the query on the recipient column
     *
     * Example usage:
     * <code>
     * $query->filterByRecipient('fooValue');   // WHERE recipient = 'fooValue'
     * $query->filterByRecipient('%fooValue%', Criteria::LIKE); // WHERE recipient LIKE '%fooValue%'
     * </code>
     *
     * @param     string $recipient The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildReactionQuery The current query, for fluid interface
     */
    public function filterByRecipient($recipient = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($recipient)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ReactionTableMap::COL_RECIPIENT, $recipient, $comparison);
    }

    /**
     * Filter the query on the sender column
     *
     * Example usage:
     * <code>
     * $query->filterBySender('fooValue');   // WHERE sender = 'fooValue'
     * $query->filterBySender('%fooValue%', Criteria::LIKE); // WHERE sender LIKE '%fooValue%'
     * </code>
     *
     * @param     string $sender The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildReactionQuery The current query, for fluid interface
     */
    public function filterBySender($sender = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($sender)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ReactionTableMap::COL_SENDER, $sender, $comparison);
    }

    /**
     * Filter the query on the replyTo column
     *
     * Example usage:
     * <code>
     * $query->filterByReplyto('fooValue');   // WHERE replyTo = 'fooValue'
     * $query->filterByReplyto('%fooValue%', Criteria::LIKE); // WHERE replyTo LIKE '%fooValue%'
     * </code>
     *
     * @param     string $replyto The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildReactionQuery The current query, for fluid interface
     */
    public function filterByReplyto($replyto = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($replyto)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ReactionTableMap::COL_REPLYTO, $replyto, $comparison);
    }

    /**
     * Filter the query on the cc column
     *
     * Example usage:
     * <code>
     * $query->filterByCc('fooValue');   // WHERE cc = 'fooValue'
     * $query->filterByCc('%fooValue%', Criteria::LIKE); // WHERE cc LIKE '%fooValue%'
     * </code>
     *
     * @param     string $cc The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildReactionQuery The current query, for fluid interface
     */
    public function filterByCc($cc = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($cc)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ReactionTableMap::COL_CC, $cc, $comparison);
    }

    /**
     * Filter the query on the bcc column
     *
     * Example usage:
     * <code>
     * $query->filterByBcc('fooValue');   // WHERE bcc = 'fooValue'
     * $query->filterByBcc('%fooValue%', Criteria::LIKE); // WHERE bcc LIKE '%fooValue%'
     * </code>
     *
     * @param     string $bcc The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildReactionQuery The current query, for fluid interface
     */
    public function filterByBcc($bcc = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($bcc)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ReactionTableMap::COL_BCC, $bcc, $comparison);
    }

    /**
     * Filter the query on the template column
     *
     * Example usage:
     * <code>
     * $query->filterByTemplate('fooValue');   // WHERE template = 'fooValue'
     * $query->filterByTemplate('%fooValue%', Criteria::LIKE); // WHERE template LIKE '%fooValue%'
     * </code>
     *
     * @param     string $template The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildReactionQuery The current query, for fluid interface
     */
    public function filterByTemplate($template = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($template)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ReactionTableMap::COL_TEMPLATE, $template, $comparison);
    }

    /**
     * Filter the query on the content column
     *
     * Example usage:
     * <code>
     * $query->filterByContent('fooValue');   // WHERE content = 'fooValue'
     * $query->filterByContent('%fooValue%', Criteria::LIKE); // WHERE content LIKE '%fooValue%'
     * </code>
     *
     * @param     string $content The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildReactionQuery The current query, for fluid interface
     */
    public function filterByContent($content = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($content)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ReactionTableMap::COL_CONTENT, $content, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildReaction $reaction Object to remove from the list of results
     *
     * @return $this|ChildReactionQuery The current query, for fluid interface
     */
    public function prune($reaction = null)
    {
        if ($reaction) {
            $this->addUsingAlias(ReactionTableMap::COL_ID, $reaction->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the reaction table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ReactionTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            ReactionTableMap::clearInstancePool();
            ReactionTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(ReactionTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ReactionTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            ReactionTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            ReactionTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // ReactionQuery
