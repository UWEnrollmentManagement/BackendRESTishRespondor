<?php

namespace FormsAPI\Map;

use FormsAPI\Visitor;
use FormsAPI\VisitorQuery;
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
 * This class defines the structure of the 'visitor' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class VisitorTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'FormsAPI.Map.VisitorTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'visitor';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\FormsAPI\\Visitor';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'FormsAPI.Visitor';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 6;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 6;

    /**
     * the column name for the id field
     */
    const COL_ID = 'visitor.id';

    /**
     * the column name for the uw_student_number field
     */
    const COL_UW_STUDENT_NUMBER = 'visitor.uw_student_number';

    /**
     * the column name for the uw_net_id field
     */
    const COL_UW_NET_ID = 'visitor.uw_net_id';

    /**
     * the column name for the first_name field
     */
    const COL_FIRST_NAME = 'visitor.first_name';

    /**
     * the column name for the middle_name field
     */
    const COL_MIDDLE_NAME = 'visitor.middle_name';

    /**
     * the column name for the last_name field
     */
    const COL_LAST_NAME = 'visitor.last_name';

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
        self::TYPE_PHPNAME       => array('Id', 'UWStudentNumber', 'UWNetID', 'FirstName', 'MiddleName', 'LastName', ),
        self::TYPE_CAMELNAME     => array('id', 'uWStudentNumber', 'uWNetID', 'firstName', 'middleName', 'lastName', ),
        self::TYPE_COLNAME       => array(VisitorTableMap::COL_ID, VisitorTableMap::COL_UW_STUDENT_NUMBER, VisitorTableMap::COL_UW_NET_ID, VisitorTableMap::COL_FIRST_NAME, VisitorTableMap::COL_MIDDLE_NAME, VisitorTableMap::COL_LAST_NAME, ),
        self::TYPE_FIELDNAME     => array('id', 'uw_student_number', 'uw_net_id', 'first_name', 'middle_name', 'last_name', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'UWStudentNumber' => 1, 'UWNetID' => 2, 'FirstName' => 3, 'MiddleName' => 4, 'LastName' => 5, ),
        self::TYPE_CAMELNAME     => array('id' => 0, 'uWStudentNumber' => 1, 'uWNetID' => 2, 'firstName' => 3, 'middleName' => 4, 'lastName' => 5, ),
        self::TYPE_COLNAME       => array(VisitorTableMap::COL_ID => 0, VisitorTableMap::COL_UW_STUDENT_NUMBER => 1, VisitorTableMap::COL_UW_NET_ID => 2, VisitorTableMap::COL_FIRST_NAME => 3, VisitorTableMap::COL_MIDDLE_NAME => 4, VisitorTableMap::COL_LAST_NAME => 5, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'uw_student_number' => 1, 'uw_net_id' => 2, 'first_name' => 3, 'middle_name' => 4, 'last_name' => 5, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, )
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
        $this->setName('visitor');
        $this->setPhpName('Visitor');
        $this->setIdentifierQuoting(false);
        $this->setClassName('\\FormsAPI\\Visitor');
        $this->setPackage('FormsAPI');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('uw_student_number', 'UWStudentNumber', 'VARCHAR', false, 7, null);
        $this->addColumn('uw_net_id', 'UWNetID', 'VARCHAR', true, 63, null);
        $this->addColumn('first_name', 'FirstName', 'VARCHAR', false, 63, null);
        $this->addColumn('middle_name', 'MiddleName', 'VARCHAR', false, 63, null);
        $this->addColumn('last_name', 'LastName', 'VARCHAR', false, 63, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
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
            'validate' => array('rule1' => array ('column' => 'uw_net_id','validator' => 'NotNull',), ),
        );
    } // getBehaviors()

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
        return $withPrefix ? VisitorTableMap::CLASS_DEFAULT : VisitorTableMap::OM_CLASS;
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
     * @return array           (Visitor object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = VisitorTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = VisitorTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + VisitorTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = VisitorTableMap::OM_CLASS;
            /** @var Visitor $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            VisitorTableMap::addInstanceToPool($obj, $key);
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
            $key = VisitorTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = VisitorTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var Visitor $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                VisitorTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(VisitorTableMap::COL_ID);
            $criteria->addSelectColumn(VisitorTableMap::COL_UW_STUDENT_NUMBER);
            $criteria->addSelectColumn(VisitorTableMap::COL_UW_NET_ID);
            $criteria->addSelectColumn(VisitorTableMap::COL_FIRST_NAME);
            $criteria->addSelectColumn(VisitorTableMap::COL_MIDDLE_NAME);
            $criteria->addSelectColumn(VisitorTableMap::COL_LAST_NAME);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.uw_student_number');
            $criteria->addSelectColumn($alias . '.uw_net_id');
            $criteria->addSelectColumn($alias . '.first_name');
            $criteria->addSelectColumn($alias . '.middle_name');
            $criteria->addSelectColumn($alias . '.last_name');
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
        return Propel::getServiceContainer()->getDatabaseMap(VisitorTableMap::DATABASE_NAME)->getTable(VisitorTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(VisitorTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(VisitorTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new VisitorTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a Visitor or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Visitor object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(VisitorTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \FormsAPI\Visitor) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(VisitorTableMap::DATABASE_NAME);
            $criteria->add(VisitorTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = VisitorQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            VisitorTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                VisitorTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the visitor table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return VisitorQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Visitor or Criteria object.
     *
     * @param mixed               $criteria Criteria or Visitor object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(VisitorTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Visitor object
        }

        if ($criteria->containsKey(VisitorTableMap::COL_ID) && $criteria->keyContainsValue(VisitorTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.VisitorTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = VisitorQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // VisitorTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
VisitorTableMap::buildTableMap();
