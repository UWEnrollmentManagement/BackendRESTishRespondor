<?php

namespace FormsAPI\Map;

use FormsAPI\Element;
use FormsAPI\ElementQuery;
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
 * This class defines the structure of the 'element' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class ElementTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'FormsAPI.Map.ElementTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'element';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\FormsAPI\\Element';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'FormsAPI.Element';

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
    const COL_ID = 'element.id';

    /**
     * the column name for the retired field
     */
    const COL_RETIRED = 'element.retired';

    /**
     * the column name for the type field
     */
    const COL_TYPE = 'element.type';

    /**
     * the column name for the label field
     */
    const COL_LABEL = 'element.label';

    /**
     * the column name for the initial_value field
     */
    const COL_INITIAL_VALUE = 'element.initial_value';

    /**
     * the column name for the help_text field
     */
    const COL_HELP_TEXT = 'element.help_text';

    /**
     * the column name for the placeholder_text field
     */
    const COL_PLACEHOLDER_TEXT = 'element.placeholder_text';

    /**
     * the column name for the required field
     */
    const COL_REQUIRED = 'element.required';

    /**
     * the column name for the parent_id field
     */
    const COL_PARENT_ID = 'element.parent_id';

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
        self::TYPE_PHPNAME       => array('Id', 'Retired', 'Type', 'Label', 'InitialValue', 'HelpText', 'PlaceholderText', 'Required', 'ParentId', ),
        self::TYPE_CAMELNAME     => array('id', 'retired', 'type', 'label', 'initialValue', 'helpText', 'placeholderText', 'required', 'parentId', ),
        self::TYPE_COLNAME       => array(ElementTableMap::COL_ID, ElementTableMap::COL_RETIRED, ElementTableMap::COL_TYPE, ElementTableMap::COL_LABEL, ElementTableMap::COL_INITIAL_VALUE, ElementTableMap::COL_HELP_TEXT, ElementTableMap::COL_PLACEHOLDER_TEXT, ElementTableMap::COL_REQUIRED, ElementTableMap::COL_PARENT_ID, ),
        self::TYPE_FIELDNAME     => array('id', 'retired', 'type', 'label', 'initial_value', 'help_text', 'placeholder_text', 'required', 'parent_id', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Retired' => 1, 'Type' => 2, 'Label' => 3, 'InitialValue' => 4, 'HelpText' => 5, 'PlaceholderText' => 6, 'Required' => 7, 'ParentId' => 8, ),
        self::TYPE_CAMELNAME     => array('id' => 0, 'retired' => 1, 'type' => 2, 'label' => 3, 'initialValue' => 4, 'helpText' => 5, 'placeholderText' => 6, 'required' => 7, 'parentId' => 8, ),
        self::TYPE_COLNAME       => array(ElementTableMap::COL_ID => 0, ElementTableMap::COL_RETIRED => 1, ElementTableMap::COL_TYPE => 2, ElementTableMap::COL_LABEL => 3, ElementTableMap::COL_INITIAL_VALUE => 4, ElementTableMap::COL_HELP_TEXT => 5, ElementTableMap::COL_PLACEHOLDER_TEXT => 6, ElementTableMap::COL_REQUIRED => 7, ElementTableMap::COL_PARENT_ID => 8, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'retired' => 1, 'type' => 2, 'label' => 3, 'initial_value' => 4, 'help_text' => 5, 'placeholder_text' => 6, 'required' => 7, 'parent_id' => 8, ),
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
        $this->setName('element');
        $this->setPhpName('Element');
        $this->setIdentifierQuoting(false);
        $this->setClassName('\\FormsAPI\\Element');
        $this->setPackage('FormsAPI');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('retired', 'Retired', 'BOOLEAN', true, null, false);
        $this->addColumn('type', 'Type', 'VARCHAR', true, 31, null);
        $this->addColumn('label', 'Label', 'VARCHAR', true, 8191, null);
        $this->addColumn('initial_value', 'InitialValue', 'VARCHAR', false, 127, null);
        $this->addColumn('help_text', 'HelpText', 'VARCHAR', false, 4095, null);
        $this->addColumn('placeholder_text', 'PlaceholderText', 'VARCHAR', false, 127, null);
        $this->addColumn('required', 'Required', 'BOOLEAN', true, null, true);
        $this->addForeignKey('parent_id', 'ParentId', 'INTEGER', 'element', 'id', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('ElementRelatedByParentId', '\\FormsAPI\\Element', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':parent_id',
    1 => ':id',
  ),
), 'SET NULL', null, null, false);
        $this->addRelation('Parent', '\\FormsAPI\\Element', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':parent_id',
    1 => ':id',
  ),
), 'SET NULL', null, 'Parents', false);
        $this->addRelation('Response', '\\FormsAPI\\Response', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':element_id',
    1 => ':id',
  ),
), 'CASCADE', null, 'Responses', false);
        $this->addRelation('RootElement', '\\FormsAPI\\Form', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':root_element_id',
    1 => ':id',
  ),
), 'SET NULL', null, 'RootElements', false);
        $this->addRelation('AsMaster', '\\FormsAPI\\Dependency', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':element_id',
    1 => ':id',
  ),
), 'CASCADE', null, 'AsMasters', false);
        $this->addRelation('AsSlave', '\\FormsAPI\\Dependency', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':slave_id',
    1 => ':id',
  ),
), 'CASCADE', null, 'AsSlaves', false);
        $this->addRelation('Requirement', '\\FormsAPI\\Requirement', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':element_id',
    1 => ':id',
  ),
), 'CASCADE', null, 'Requirements', false);
        $this->addRelation('ElementChoice', '\\FormsAPI\\ElementChoice', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':element_id',
    1 => ':id',
  ),
), 'CASCADE', null, 'ElementChoices', false);
        $this->addRelation('DashboardElement', '\\FormsAPI\\DashboardElement', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':element_id',
    1 => ':id',
  ),
), 'CASCADE', null, 'DashboardElements', false);
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
            'validate' => array('rule1' => array ('column' => 'type','validator' => 'Choice','options' => array ('choices' => array (0 => 'section-label',1 => 'information',2 => 'affirmation',3 => 'date',4 => 'text-field',5 => 'big-text-field',6 => 'choice-field',7 => 'secure-upload',8 => 'secure-upload-multiple',9 => 'choices-from-file',),),), ),
        );
    } // getBehaviors()
    /**
     * Method to invalidate the instance pool of all tables related to element     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in related instance pools,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        ElementTableMap::clearInstancePool();
        ResponseTableMap::clearInstancePool();
        FormTableMap::clearInstancePool();
        DependencyTableMap::clearInstancePool();
        RequirementTableMap::clearInstancePool();
        ElementChoiceTableMap::clearInstancePool();
        DashboardElementTableMap::clearInstancePool();
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
        return $withPrefix ? ElementTableMap::CLASS_DEFAULT : ElementTableMap::OM_CLASS;
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
     * @return array           (Element object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = ElementTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = ElementTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + ElementTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = ElementTableMap::OM_CLASS;
            /** @var Element $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            ElementTableMap::addInstanceToPool($obj, $key);
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
            $key = ElementTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = ElementTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var Element $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                ElementTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(ElementTableMap::COL_ID);
            $criteria->addSelectColumn(ElementTableMap::COL_RETIRED);
            $criteria->addSelectColumn(ElementTableMap::COL_TYPE);
            $criteria->addSelectColumn(ElementTableMap::COL_LABEL);
            $criteria->addSelectColumn(ElementTableMap::COL_INITIAL_VALUE);
            $criteria->addSelectColumn(ElementTableMap::COL_HELP_TEXT);
            $criteria->addSelectColumn(ElementTableMap::COL_PLACEHOLDER_TEXT);
            $criteria->addSelectColumn(ElementTableMap::COL_REQUIRED);
            $criteria->addSelectColumn(ElementTableMap::COL_PARENT_ID);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.retired');
            $criteria->addSelectColumn($alias . '.type');
            $criteria->addSelectColumn($alias . '.label');
            $criteria->addSelectColumn($alias . '.initial_value');
            $criteria->addSelectColumn($alias . '.help_text');
            $criteria->addSelectColumn($alias . '.placeholder_text');
            $criteria->addSelectColumn($alias . '.required');
            $criteria->addSelectColumn($alias . '.parent_id');
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
        return Propel::getServiceContainer()->getDatabaseMap(ElementTableMap::DATABASE_NAME)->getTable(ElementTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(ElementTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(ElementTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new ElementTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a Element or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Element object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(ElementTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \FormsAPI\Element) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(ElementTableMap::DATABASE_NAME);
            $criteria->add(ElementTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = ElementQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            ElementTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                ElementTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the element table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return ElementQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Element or Criteria object.
     *
     * @param mixed               $criteria Criteria or Element object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ElementTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Element object
        }

        if ($criteria->containsKey(ElementTableMap::COL_ID) && $criteria->keyContainsValue(ElementTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.ElementTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = ElementQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // ElementTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
ElementTableMap::buildTableMap();
