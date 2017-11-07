<?php

namespace FormsAPI\Base;

use \Exception;
use \PDO;
use FormsAPI\Reactions as ChildReactions;
use FormsAPI\ReactionsQuery as ChildReactionsQuery;
use FormsAPI\Map\ReactionsTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'reactions' table.
 *
 *
 *
 * @method     ChildReactionsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildReactionsQuery orderBySubject($order = Criteria::ASC) Order by the subject column
 * @method     ChildReactionsQuery orderByRecipient($order = Criteria::ASC) Order by the recipient column
 * @method     ChildReactionsQuery orderBySender($order = Criteria::ASC) Order by the sender column
 * @method     ChildReactionsQuery orderByReplyto($order = Criteria::ASC) Order by the replyTo column
 * @method     ChildReactionsQuery orderByCc($order = Criteria::ASC) Order by the cc column
 * @method     ChildReactionsQuery orderByBcc($order = Criteria::ASC) Order by the bcc column
 * @method     ChildReactionsQuery orderByTemplate($order = Criteria::ASC) Order by the template column
 * @method     ChildReactionsQuery orderByContent($order = Criteria::ASC) Order by the content column
 *
 * @method     ChildReactionsQuery groupById() Group by the id column
 * @method     ChildReactionsQuery groupBySubject() Group by the subject column
 * @method     ChildReactionsQuery groupByRecipient() Group by the recipient column
 * @method     ChildReactionsQuery groupBySender() Group by the sender column
 * @method     ChildReactionsQuery groupByReplyto() Group by the replyTo column
 * @method     ChildReactionsQuery groupByCc() Group by the cc column
 * @method     ChildReactionsQuery groupByBcc() Group by the bcc column
 * @method     ChildReactionsQuery groupByTemplate() Group by the template column
 * @method     ChildReactionsQuery groupByContent() Group by the content column
 *
 * @method     ChildReactionsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildReactionsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildReactionsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildReactionsQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildReactionsQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildReactionsQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildReactions findOne(ConnectionInterface $con = null) Return the first ChildReactions matching the query
 * @method     ChildReactions findOneOrCreate(ConnectionInterface $con = null) Return the first ChildReactions matching the query, or a new ChildReactions object populated from the query conditions when no match is found
 *
 * @method     ChildReactions findOneById(int $id) Return the first ChildReactions filtered by the id column
 * @method     ChildReactions findOneBySubject(string $subject) Return the first ChildReactions filtered by the subject column
 * @method     ChildReactions findOneByRecipient(string $recipient) Return the first ChildReactions filtered by the recipient column
 * @method     ChildReactions findOneBySender(string $sender) Return the first ChildReactions filtered by the sender column
 * @method     ChildReactions findOneByReplyto(string $replyTo) Return the first ChildReactions filtered by the replyTo column
 * @method     ChildReactions findOneByCc(string $cc) Return the first ChildReactions filtered by the cc column
 * @method     ChildReactions findOneByBcc(string $bcc) Return the first ChildReactions filtered by the bcc column
 * @method     ChildReactions findOneByTemplate(string $template) Return the first ChildReactions filtered by the template column
 * @method     ChildReactions findOneByContent(string $content) Return the first ChildReactions filtered by the content column *

 * @method     ChildReactions requirePk($key, ConnectionInterface $con = null) Return the ChildReactions by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildReactions requireOne(ConnectionInterface $con = null) Return the first ChildReactions matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildReactions requireOneById(int $id) Return the first ChildReactions filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildReactions requireOneBySubject(string $subject) Return the first ChildReactions filtered by the subject column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildReactions requireOneByRecipient(string $recipient) Return the first ChildReactions filtered by the recipient column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildReactions requireOneBySender(string $sender) Return the first ChildReactions filtered by the sender column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildReactions requireOneByReplyto(string $replyTo) Return the first ChildReactions filtered by the replyTo column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildReactions requireOneByCc(string $cc) Return the first ChildReactions filtered by the cc column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildReactions requireOneByBcc(string $bcc) Return the first ChildReactions filtered by the bcc column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildReactions requireOneByTemplate(string $template) Return the first ChildReactions filtered by the template column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildReactions requireOneByContent(string $content) Return the first ChildReactions filtered by the content column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildReactions[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildReactions objects based on current ModelCriteria
 * @method     ChildReactions[]|ObjectCollection findById(int $id) Return ChildReactions objects filtered by the id column
 * @method     ChildReactions[]|ObjectCollection findBySubject(string $subject) Return ChildReactions objects filtered by the subject column
 * @method     ChildReactions[]|ObjectCollection findByRecipient(string $recipient) Return ChildReactions objects filtered by the recipient column
 * @method     ChildReactions[]|ObjectCollection findBySender(string $sender) Return ChildReactions objects filtered by the sender column
 * @method     ChildReactions[]|ObjectCollection findByReplyto(string $replyTo) Return ChildReactions objects filtered by the replyTo column
 * @method     ChildReactions[]|ObjectCollection findByCc(string $cc) Return ChildReactions objects filtered by the cc column
 * @method     ChildReactions[]|ObjectCollection findByBcc(string $bcc) Return ChildReactions objects filtered by the bcc column
 * @method     ChildReactions[]|ObjectCollection findByTemplate(string $template) Return ChildReactions objects filtered by the template column
 * @method     ChildReactions[]|ObjectCollection findByContent(string $content) Return ChildReactions objects filtered by the content column
 * @method     ChildReactions[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class ReactionsQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \FormsAPI\Base\ReactionsQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\FormsAPI\\Reactions', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildReactionsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildReactionsQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildReactionsQuery) {
            return $criteria;
        }
        $query = new ChildReactionsQuery();
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
     * @return ChildReactions|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ReactionsTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = ReactionsTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @return ChildReactions A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, subject, recipient, sender, replyTo, cc, bcc, template, content FROM reactions WHERE id = :p0';
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
            /** @var ChildReactions $obj */
            $obj = new ChildReactions();
            $obj->hydrate($row);
            ReactionsTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildReactions|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildReactionsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ReactionsTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildReactionsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ReactionsTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildReactionsQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ReactionsTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ReactionsTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ReactionsTableMap::COL_ID, $id, $comparison);
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
     * @return $this|ChildReactionsQuery The current query, for fluid interface
     */
    public function filterBySubject($subject = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($subject)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ReactionsTableMap::COL_SUBJECT, $subject, $comparison);
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
     * @return $this|ChildReactionsQuery The current query, for fluid interface
     */
    public function filterByRecipient($recipient = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($recipient)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ReactionsTableMap::COL_RECIPIENT, $recipient, $comparison);
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
     * @return $this|ChildReactionsQuery The current query, for fluid interface
     */
    public function filterBySender($sender = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($sender)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ReactionsTableMap::COL_SENDER, $sender, $comparison);
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
     * @return $this|ChildReactionsQuery The current query, for fluid interface
     */
    public function filterByReplyto($replyto = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($replyto)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ReactionsTableMap::COL_REPLYTO, $replyto, $comparison);
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
     * @return $this|ChildReactionsQuery The current query, for fluid interface
     */
    public function filterByCc($cc = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($cc)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ReactionsTableMap::COL_CC, $cc, $comparison);
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
     * @return $this|ChildReactionsQuery The current query, for fluid interface
     */
    public function filterByBcc($bcc = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($bcc)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ReactionsTableMap::COL_BCC, $bcc, $comparison);
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
     * @return $this|ChildReactionsQuery The current query, for fluid interface
     */
    public function filterByTemplate($template = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($template)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ReactionsTableMap::COL_TEMPLATE, $template, $comparison);
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
     * @return $this|ChildReactionsQuery The current query, for fluid interface
     */
    public function filterByContent($content = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($content)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ReactionsTableMap::COL_CONTENT, $content, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildReactions $reactions Object to remove from the list of results
     *
     * @return $this|ChildReactionsQuery The current query, for fluid interface
     */
    public function prune($reactions = null)
    {
        if ($reactions) {
            $this->addUsingAlias(ReactionsTableMap::COL_ID, $reactions->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the reactions table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ReactionsTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            ReactionsTableMap::clearInstancePool();
            ReactionsTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(ReactionsTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ReactionsTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            ReactionsTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            ReactionsTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // ReactionsQuery
