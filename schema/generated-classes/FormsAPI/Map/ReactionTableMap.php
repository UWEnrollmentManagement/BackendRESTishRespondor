<?php

namespace FormsAPI\Map;

use FormsAPI\Reaction;
use FormsAPI\ReactionQuery;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;


/**
 * This class defines the structure of the 'reaction' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class ReactionTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'FormsAPI.Map.ReactionTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'reaction';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\FormsAPI\\Reaction';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'FormsAPI.Reaction';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 9;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 9;

    /**
     * the column name for the id field
     */
    const COL_ID = 'reaction.id';

    /**
     * the column name for the subject field
     */
    const COL_SUBJECT = 'reaction.subject';

    /**
     * the column name for the recipient field
     */
    const COL_RECIPIENT = 'reaction.recipient';

    /**
     * the column name for the sender field
     */
    const COL_SENDER = 'reaction.sender';

    /**
     * the column name for the reply_to field
     */
    const COL_REPLY_TO = 'reaction.reply_to';

    /**
     * the column name for the cc field
     */
    const COL_CC = 'reaction.cc';

    /**
     * the column name for the bcc field
     */
    const COL_BCC = 'reaction.bcc';

    /**
     * the column name for the template field
     */
    const COL_TEMPLATE = 'reaction.template';

    /**
     * the column name for the content field
     */
    const COL_CONTENT = 'reaction.content';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('Id', 'Subject', 'Recipient', 'Sender', 'ReplyTo', 'Cc', 'Bcc', 'Template', 'Content', ),
        self::TYPE_CAMELNAME     => array('id', 'subject', 'recipient', 'sender', 'replyTo', 'cc', 'bcc', 'template', 'content', ),
        self::TYPE_COLNAME       => array(ReactionTableMap::COL_ID, ReactionTableMap::COL_SUBJECT, ReactionTableMap::COL_RECIPIENT, ReactionTableMap::COL_SENDER, ReactionTableMap::COL_REPLY_TO, ReactionTableMap::COL_CC, ReactionTableMap::COL_BCC, ReactionTableMap::COL_TEMPLATE, ReactionTableMap::COL_CONTENT, ),
        self::TYPE_FIELDNAME     => array('id', 'subject', 'recipient', 'sender', 'reply_to', 'cc', 'bcc', 'template', 'content', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Subject' => 1, 'Recipient' => 2, 'Sender' => 3, 'ReplyTo' => 4, 'Cc' => 5, 'Bcc' => 6, 'Template' => 7, 'Content' => 8, ),
        self::TYPE_CAMELNAME     => array('id' => 0, 'subject' => 1, 'recipient' => 2, 'sender' => 3, 'replyTo' => 4, 'cc' => 5, 'bcc' => 6, 'template' => 7, 'content' => 8, ),
        self::TYPE_COLNAME       => array(ReactionTableMap::COL_ID => 0, ReactionTableMap::COL_SUBJECT => 1, ReactionTableMap::COL_RECIPIENT => 2, ReactionTableMap::COL_SENDER => 3, ReactionTableMap::COL_REPLY_TO => 4, ReactionTableMap::COL_CC => 5, ReactionTableMap::COL_BCC => 6, ReactionTableMap::COL_TEMPLATE => 7, ReactionTableMap::COL_CONTENT => 8, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'subject' => 1, 'recipient' => 2, 'sender' => 3, 'reply_to' => 4, 'cc' => 5, 'bcc' => 6, 'template' => 7, 'content' => 8, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, )
    );

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('reaction');
        $this->setPhpName('Reaction');
        $this->setIdentifierQuoting(false);
        $this->setClassName('\\FormsAPI\\Reaction');
        $this->setPackage('FormsAPI');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('subject', 'Subject', 'VARCHAR', true, 127, null);
        $this->addColumn('recipient', 'Recipient', 'VARCHAR', true, 63, null);
        $this->addColumn('sender', 'Sender', 'VARCHAR', true, 63, null);
        $this->addColumn('reply_to', 'ReplyTo', 'VARCHAR', false, 63, null);
        $this->addColumn('cc', 'Cc', 'VARCHAR', false, 63, null);
        $this->addColumn('bcc', 'Bcc', 'VARCHAR', false, 63, null);
        $this->addColumn('template', 'Template', 'VARCHAR', true, 127, null);
        $this->addColumn('content', 'Content', 'VARCHAR', true, 255, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('ChildFormRelationship', '\\FormsAPI\\ChildFormRelationship', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':reaction_id',
    1 => ':id',
  ),
), 'SET NULL', null, 'ChildFormRelationships', false);
        $this->addRelation('FormReaction', '\\FormsAPI\\FormReaction', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':reaction_id',
    1 => ':id',
  ),
), 'CASCADE', null, 'FormReactions', false);
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'validate' => array('rule1' => array ('column' => 'subject','validator' => 'NotNull',), 'rule2' => array ('column' => 'recipient','validator' => 'NotNull',), 'rule3' => array ('column' => 'sender','validator' => 'NotNull',), 'rule4' => array ('column' => 'template','validator' => 'NotNull',), 'rule5' => array ('column' => 'content','validator' => 'NotNull',), ),
        );
    } // getBehaviors()
    /**
     * Method to invalidate the instance pool of all tables related to reaction     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in related instance pools,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        ChildFormRelationshipTableMap::clearInstancePool();
        FormReactionTableMap::clearInstancePool();
    }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return string The primary key hash of the row
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return null === $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] || is_scalar($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)]) || is_callable([$row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)], '__toString']) ? (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] : $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        return (int) $row[
            $indexType == TableMap::TYPE_NUM
                ? 0 + $offset
                : self::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)
        ];
    }

    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param boolean $withPrefix Whether or not to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? ReactionTableMap::CLASS_DEFAULT : ReactionTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array  $row       row returned by DataFetcher->fetch().
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     * @return array           (Reaction object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = ReactionTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = ReactionTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + ReactionTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = ReactionTableMap::OM_CLASS;
            /** @var Reaction $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            ReactionTableMap::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = ReactionTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = ReactionTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var Reaction $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                ReactionTableMap::addInstanceToPool($obj, $key);
            } // if key exists
        }

        return $results;
    }
    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria object containing the columns to add.
     * @param string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(ReactionTableMap::COL_ID);
            $criteria->addSelectColumn(ReactionTableMap::COL_SUBJECT);
            $criteria->addSelectColumn(ReactionTableMap::COL_RECIPIENT);
            $criteria->addSelectColumn(ReactionTableMap::COL_SENDER);
            $criteria->addSelectColumn(ReactionTableMap::COL_REPLY_TO);
            $criteria->addSelectColumn(ReactionTableMap::COL_CC);
            $criteria->addSelectColumn(ReactionTableMap::COL_BCC);
            $criteria->addSelectColumn(ReactionTableMap::COL_TEMPLATE);
            $criteria->addSelectColumn(ReactionTableMap::COL_CONTENT);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.subject');
            $criteria->addSelectColumn($alias . '.recipient');
            $criteria->addSelectColumn($alias . '.sender');
            $criteria->addSelectColumn($alias . '.reply_to');
            $criteria->addSelectColumn($alias . '.cc');
            $criteria->addSelectColumn($alias . '.bcc');
            $criteria->addSelectColumn($alias . '.template');
            $criteria->addSelectColumn($alias . '.content');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(ReactionTableMap::DATABASE_NAME)->getTable(ReactionTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(ReactionTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(ReactionTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new ReactionTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a Reaction or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Reaction object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param  ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ReactionTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \FormsAPI\Reaction) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(ReactionTableMap::DATABASE_NAME);
            $criteria->add(ReactionTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = ReactionQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            ReactionTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                ReactionTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the reaction table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return ReactionQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Reaction or Criteria object.
     *
     * @param mixed               $criteria Criteria or Reaction object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ReactionTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Reaction object
        }

        if ($criteria->containsKey(ReactionTableMap::COL_ID) && $criteria->keyContainsValue(ReactionTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.ReactionTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = ReactionQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // ReactionTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
ReactionTableMap::buildTableMap();
