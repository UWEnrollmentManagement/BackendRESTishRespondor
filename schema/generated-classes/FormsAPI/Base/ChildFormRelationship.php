<?php

namespace FormsAPI\Base;

use \Exception;
use \PDO;
use FormsAPI\ChildFormRelationshipQuery as ChildChildFormRelationshipQuery;
use FormsAPI\Form as ChildForm;
use FormsAPI\FormQuery as ChildFormQuery;
use FormsAPI\Reaction as ChildReaction;
use FormsAPI\ReactionQuery as ChildReactionQuery;
use FormsAPI\Tag as ChildTag;
use FormsAPI\TagQuery as ChildTagQuery;
use FormsAPI\Map\ChildFormRelationshipTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\LogicException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Context\ExecutionContextFactory;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Base class that represents a row from the 'child_form_relationship' table.
 *
 *
 *
 * @package    propel.generator.FormsAPI.Base
 */
abstract class ChildFormRelationship implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\FormsAPI\\Map\\ChildFormRelationshipTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var boolean
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var boolean
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = array();

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = array();

    /**
     * The value for the id field.
     *
     * @var        int
     */
    protected $id;

    /**
     * The value for the parent_id field.
     *
     * @var        int
     */
    protected $parent_id;

    /**
     * The value for the child_id field.
     *
     * @var        int
     */
    protected $child_id;

    /**
     * The value for the tag_id field.
     *
     * @var        int
     */
    protected $tag_id;

    /**
     * The value for the reaction_id field.
     *
     * @var        int
     */
    protected $reaction_id;

    /**
     * @var        ChildForm
     */
    protected $aParent;

    /**
     * @var        ChildForm
     */
    protected $aChild;

    /**
     * @var        ChildTag
     */
    protected $aTag;

    /**
     * @var        ChildReaction
     */
    protected $aReaction;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    // validate behavior

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * ConstraintViolationList object
     *
     * @see     http://api.symfony.com/2.0/Symfony/Component/Validator/ConstraintViolationList.html
     * @var     ConstraintViolationList
     */
    protected $validationFailures;

    /**
     * Initializes internal state of FormsAPI\Base\ChildFormRelationship object.
     */
    public function __construct()
    {
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return boolean True if the object has been modified.
     */
    public function isModified()
    {
        return !!$this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return boolean True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return boolean true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param boolean $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (boolean) $b;
    }

    /**
     * Whether this object has been deleted.
     * @return boolean The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param  boolean $b The deleted state of this object.
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (boolean) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            if (isset($this->modifiedColumns[$col])) {
                unset($this->modifiedColumns[$col]);
            }
        } else {
            $this->modifiedColumns = array();
        }
    }

    /**
     * Compares this with another <code>ChildFormRelationship</code> instance.  If
     * <code>obj</code> is an instance of <code>ChildFormRelationship</code>, delegates to
     * <code>equals(ChildFormRelationship)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        if (!$obj instanceof static) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey() || null === $obj->getPrimaryKey()) {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param  string  $name The virtual column name
     * @return boolean
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string $name The virtual column name
     * @return mixed
     *
     * @throws PropelException
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return $this|ChildFormRelationship The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param  string  $msg
     * @param  int     $priority One of the Propel::LOG_* logging levels
     * @return boolean
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @return string  The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        $cls = new \ReflectionClass($this);
        $propertyNames = [];
        $serializableProperties = array_diff($cls->getProperties(), $cls->getProperties(\ReflectionProperty::IS_STATIC));

        foreach($serializableProperties as $property) {
            $propertyNames[] = $property->getName();
        }

        return $propertyNames;
    }

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the [parent_id] column value.
     *
     * @return int
     */
    public function getparentName()
    {
        return $this->parent_id;
    }

    /**
     * Get the [child_id] column value.
     *
     * @return int
     */
    public function getChildId()
    {
        return $this->child_id;
    }

    /**
     * Get the [tag_id] column value.
     *
     * @return int
     */
    public function getTagId()
    {
        return $this->tag_id;
    }

    /**
     * Get the [reaction_id] column value.
     *
     * @return int
     */
    public function getReactionId()
    {
        return $this->reaction_id;
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return $this|\FormsAPI\ChildFormRelationship The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[ChildFormRelationshipTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [parent_id] column.
     *
     * @param int $v new value
     * @return $this|\FormsAPI\ChildFormRelationship The current object (for fluent API support)
     */
    public function setparentName($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->parent_id !== $v) {
            $this->parent_id = $v;
            $this->modifiedColumns[ChildFormRelationshipTableMap::COL_PARENT_ID] = true;
        }

        if ($this->aParent !== null && $this->aParent->getId() !== $v) {
            $this->aParent = null;
        }

        return $this;
    } // setparentName()

    /**
     * Set the value of [child_id] column.
     *
     * @param int $v new value
     * @return $this|\FormsAPI\ChildFormRelationship The current object (for fluent API support)
     */
    public function setChildId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->child_id !== $v) {
            $this->child_id = $v;
            $this->modifiedColumns[ChildFormRelationshipTableMap::COL_CHILD_ID] = true;
        }

        if ($this->aChild !== null && $this->aChild->getId() !== $v) {
            $this->aChild = null;
        }

        return $this;
    } // setChildId()

    /**
     * Set the value of [tag_id] column.
     *
     * @param int $v new value
     * @return $this|\FormsAPI\ChildFormRelationship The current object (for fluent API support)
     */
    public function setTagId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->tag_id !== $v) {
            $this->tag_id = $v;
            $this->modifiedColumns[ChildFormRelationshipTableMap::COL_TAG_ID] = true;
        }

        if ($this->aTag !== null && $this->aTag->getId() !== $v) {
            $this->aTag = null;
        }

        return $this;
    } // setTagId()

    /**
     * Set the value of [reaction_id] column.
     *
     * @param int $v new value
     * @return $this|\FormsAPI\ChildFormRelationship The current object (for fluent API support)
     */
    public function setReactionId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->reaction_id !== $v) {
            $this->reaction_id = $v;
            $this->modifiedColumns[ChildFormRelationshipTableMap::COL_REACTION_ID] = true;
        }

        if ($this->aReaction !== null && $this->aReaction->getId() !== $v) {
            $this->aReaction = null;
        }

        return $this;
    } // setReactionId()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array   $row       The row returned by DataFetcher->fetch().
     * @param int     $startcol  0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @param string  $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : ChildFormRelationshipTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : ChildFormRelationshipTableMap::translateFieldName('parentName', TableMap::TYPE_PHPNAME, $indexType)];
            $this->parent_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : ChildFormRelationshipTableMap::translateFieldName('ChildId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->child_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : ChildFormRelationshipTableMap::translateFieldName('TagId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->tag_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : ChildFormRelationshipTableMap::translateFieldName('ReactionId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->reaction_id = (null !== $col) ? (int) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 5; // 5 = ChildFormRelationshipTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\FormsAPI\\ChildFormRelationship'), 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
        if ($this->aParent !== null && $this->parent_id !== $this->aParent->getId()) {
            $this->aParent = null;
        }
        if ($this->aChild !== null && $this->child_id !== $this->aChild->getId()) {
            $this->aChild = null;
        }
        if ($this->aTag !== null && $this->tag_id !== $this->aTag->getId()) {
            $this->aTag = null;
        }
        if ($this->aReaction !== null && $this->reaction_id !== $this->aReaction->getId()) {
            $this->aReaction = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ChildFormRelationshipTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildChildFormRelationshipQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aParent = null;
            $this->aChild = null;
            $this->aTag = null;
            $this->aReaction = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see ChildFormRelationship::setDeleted()
     * @see ChildFormRelationship::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(ChildFormRelationshipTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildChildFormRelationshipQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $this->setDeleted(true);
            }
        });
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($this->alreadyInSave) {
            return 0;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(ChildFormRelationshipTableMap::DATABASE_NAME);
        }

        return $con->transaction(function () use ($con) {
            $ret = $this->preSave($con);
            $isInsert = $this->isNew();
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                ChildFormRelationshipTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }

            return $affectedRows;
        });
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aParent !== null) {
                if ($this->aParent->isModified() || $this->aParent->isNew()) {
                    $affectedRows += $this->aParent->save($con);
                }
                $this->setParent($this->aParent);
            }

            if ($this->aChild !== null) {
                if ($this->aChild->isModified() || $this->aChild->isNew()) {
                    $affectedRows += $this->aChild->save($con);
                }
                $this->setChild($this->aChild);
            }

            if ($this->aTag !== null) {
                if ($this->aTag->isModified() || $this->aTag->isNew()) {
                    $affectedRows += $this->aTag->save($con);
                }
                $this->setTag($this->aTag);
            }

            if ($this->aReaction !== null) {
                if ($this->aReaction->isModified() || $this->aReaction->isNew()) {
                    $affectedRows += $this->aReaction->save($con);
                }
                $this->setReaction($this->aReaction);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                    $affectedRows += 1;
                } else {
                    $affectedRows += $this->doUpdate($con);
                }
                $this->resetModified();
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @throws PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[ChildFormRelationshipTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . ChildFormRelationshipTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ChildFormRelationshipTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(ChildFormRelationshipTableMap::COL_PARENT_ID)) {
            $modifiedColumns[':p' . $index++]  = 'parent_id';
        }
        if ($this->isColumnModified(ChildFormRelationshipTableMap::COL_CHILD_ID)) {
            $modifiedColumns[':p' . $index++]  = 'child_id';
        }
        if ($this->isColumnModified(ChildFormRelationshipTableMap::COL_TAG_ID)) {
            $modifiedColumns[':p' . $index++]  = 'tag_id';
        }
        if ($this->isColumnModified(ChildFormRelationshipTableMap::COL_REACTION_ID)) {
            $modifiedColumns[':p' . $index++]  = 'reaction_id';
        }

        $sql = sprintf(
            'INSERT INTO child_form_relationship (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'id':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'parent_id':
                        $stmt->bindValue($identifier, $this->parent_id, PDO::PARAM_INT);
                        break;
                    case 'child_id':
                        $stmt->bindValue($identifier, $this->child_id, PDO::PARAM_INT);
                        break;
                    case 'tag_id':
                        $stmt->bindValue($identifier, $this->tag_id, PDO::PARAM_INT);
                        break;
                    case 'reaction_id':
                        $stmt->bindValue($identifier, $this->reaction_id, PDO::PARAM_INT);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @return Integer Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = ChildFormRelationshipTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getparentName();
                break;
            case 2:
                return $this->getChildId();
                break;
            case 3:
                return $this->getTagId();
                break;
            case 4:
                return $this->getReactionId();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {

        if (isset($alreadyDumpedObjects['ChildFormRelationship'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['ChildFormRelationship'][$this->hashCode()] = true;
        $keys = ChildFormRelationshipTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getparentName(),
            $keys[2] => $this->getChildId(),
            $keys[3] => $this->getTagId(),
            $keys[4] => $this->getReactionId(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aParent) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'form';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'form';
                        break;
                    default:
                        $key = 'Parent';
                }

                $result[$key] = $this->aParent->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aChild) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'form';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'form';
                        break;
                    default:
                        $key = 'Child';
                }

                $result[$key] = $this->aChild->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aTag) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'tag';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'tag';
                        break;
                    default:
                        $key = 'Tag';
                }

                $result[$key] = $this->aTag->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aReaction) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'reaction';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'reaction';
                        break;
                    default:
                        $key = 'Reaction';
                }

                $result[$key] = $this->aReaction->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param  string $name
     * @param  mixed  $value field value
     * @param  string $type The type of fieldname the $name is of:
     *                one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                Defaults to TableMap::TYPE_PHPNAME.
     * @return $this|\FormsAPI\ChildFormRelationship
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = ChildFormRelationshipTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\FormsAPI\ChildFormRelationship
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setparentName($value);
                break;
            case 2:
                $this->setChildId($value);
                break;
            case 3:
                $this->setTagId($value);
                break;
            case 4:
                $this->setReactionId($value);
                break;
        } // switch()

        return $this;
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = ChildFormRelationshipTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setparentName($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setChildId($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setTagId($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setReactionId($arr[$keys[4]]);
        }
    }

     /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     * @param string $keyType The type of keys the array uses.
     *
     * @return $this|\FormsAPI\ChildFormRelationship The current object, for fluid interface
     */
    public function importFrom($parser, $data, $keyType = TableMap::TYPE_PHPNAME)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), $keyType);

        return $this;
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ChildFormRelationshipTableMap::DATABASE_NAME);

        if ($this->isColumnModified(ChildFormRelationshipTableMap::COL_ID)) {
            $criteria->add(ChildFormRelationshipTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(ChildFormRelationshipTableMap::COL_PARENT_ID)) {
            $criteria->add(ChildFormRelationshipTableMap::COL_PARENT_ID, $this->parent_id);
        }
        if ($this->isColumnModified(ChildFormRelationshipTableMap::COL_CHILD_ID)) {
            $criteria->add(ChildFormRelationshipTableMap::COL_CHILD_ID, $this->child_id);
        }
        if ($this->isColumnModified(ChildFormRelationshipTableMap::COL_TAG_ID)) {
            $criteria->add(ChildFormRelationshipTableMap::COL_TAG_ID, $this->tag_id);
        }
        if ($this->isColumnModified(ChildFormRelationshipTableMap::COL_REACTION_ID)) {
            $criteria->add(ChildFormRelationshipTableMap::COL_REACTION_ID, $this->reaction_id);
        }

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @throws LogicException if no primary key is defined
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = ChildChildFormRelationshipQuery::create();
        $criteria->add(ChildFormRelationshipTableMap::COL_ID, $this->id);

        return $criteria;
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        $validPk = null !== $this->getId();

        $validPrimaryKeyFKs = 0;
        $primaryKeyFKs = [];

        if ($validPk) {
            return crc32(json_encode($this->getPrimaryKey(), JSON_UNESCAPED_UNICODE));
        } elseif ($validPrimaryKeyFKs) {
            return crc32(json_encode($primaryKeyFKs, JSON_UNESCAPED_UNICODE));
        }

        return spl_object_hash($this);
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {
        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \FormsAPI\ChildFormRelationship (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setparentName($this->getparentName());
        $copyObj->setChildId($this->getChildId());
        $copyObj->setTagId($this->getTagId());
        $copyObj->setReactionId($this->getReactionId());
        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param  boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return \FormsAPI\ChildFormRelationship Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Declares an association between this object and a ChildForm object.
     *
     * @param  ChildForm $v
     * @return $this|\FormsAPI\ChildFormRelationship The current object (for fluent API support)
     * @throws PropelException
     */
    public function setParent(ChildForm $v = null)
    {
        if ($v === null) {
            $this->setparentName(NULL);
        } else {
            $this->setparentName($v->getId());
        }

        $this->aParent = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildForm object, it will not be re-added.
        if ($v !== null) {
            $v->addAsParent($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildForm object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildForm The associated ChildForm object.
     * @throws PropelException
     */
    public function getParent(ConnectionInterface $con = null)
    {
        if ($this->aParent === null && ($this->parent_id != 0)) {
            $this->aParent = ChildFormQuery::create()->findPk($this->parent_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aParent->addAsParents($this);
             */
        }

        return $this->aParent;
    }

    /**
     * Declares an association between this object and a ChildForm object.
     *
     * @param  ChildForm $v
     * @return $this|\FormsAPI\ChildFormRelationship The current object (for fluent API support)
     * @throws PropelException
     */
    public function setChild(ChildForm $v = null)
    {
        if ($v === null) {
            $this->setChildId(NULL);
        } else {
            $this->setChildId($v->getId());
        }

        $this->aChild = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildForm object, it will not be re-added.
        if ($v !== null) {
            $v->addAsChild($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildForm object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildForm The associated ChildForm object.
     * @throws PropelException
     */
    public function getChild(ConnectionInterface $con = null)
    {
        if ($this->aChild === null && ($this->child_id != 0)) {
            $this->aChild = ChildFormQuery::create()->findPk($this->child_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aChild->addAschildren($this);
             */
        }

        return $this->aChild;
    }

    /**
     * Declares an association between this object and a ChildTag object.
     *
     * @param  ChildTag $v
     * @return $this|\FormsAPI\ChildFormRelationship The current object (for fluent API support)
     * @throws PropelException
     */
    public function setTag(ChildTag $v = null)
    {
        if ($v === null) {
            $this->setTagId(NULL);
        } else {
            $this->setTagId($v->getId());
        }

        $this->aTag = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildTag object, it will not be re-added.
        if ($v !== null) {
            $v->addChildFormRelationship($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildTag object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildTag The associated ChildTag object.
     * @throws PropelException
     */
    public function getTag(ConnectionInterface $con = null)
    {
        if ($this->aTag === null && ($this->tag_id != 0)) {
            $this->aTag = ChildTagQuery::create()->findPk($this->tag_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aTag->addChildFormRelationships($this);
             */
        }

        return $this->aTag;
    }

    /**
     * Declares an association between this object and a ChildReaction object.
     *
     * @param  ChildReaction $v
     * @return $this|\FormsAPI\ChildFormRelationship The current object (for fluent API support)
     * @throws PropelException
     */
    public function setReaction(ChildReaction $v = null)
    {
        if ($v === null) {
            $this->setReactionId(NULL);
        } else {
            $this->setReactionId($v->getId());
        }

        $this->aReaction = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildReaction object, it will not be re-added.
        if ($v !== null) {
            $v->addChildFormRelationship($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildReaction object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildReaction The associated ChildReaction object.
     * @throws PropelException
     */
    public function getReaction(ConnectionInterface $con = null)
    {
        if ($this->aReaction === null && ($this->reaction_id != 0)) {
            $this->aReaction = ChildReactionQuery::create()->findPk($this->reaction_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aReaction->addChildFormRelationships($this);
             */
        }

        return $this->aReaction;
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        if (null !== $this->aParent) {
            $this->aParent->removeAsParent($this);
        }
        if (null !== $this->aChild) {
            $this->aChild->removeAsChild($this);
        }
        if (null !== $this->aTag) {
            $this->aTag->removeChildFormRelationship($this);
        }
        if (null !== $this->aReaction) {
            $this->aReaction->removeChildFormRelationship($this);
        }
        $this->id = null;
        $this->parent_id = null;
        $this->child_id = null;
        $this->tag_id = null;
        $this->reaction_id = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references and back-references to other model objects or collections of model objects.
     *
     * This method is used to reset all php object references (not the actual reference in the database).
     * Necessary for object serialisation.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
        } // if ($deep)

        $this->aParent = null;
        $this->aChild = null;
        $this->aTag = null;
        $this->aReaction = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(ChildFormRelationshipTableMap::DEFAULT_STRING_FORMAT);
    }

    // validate behavior

    /**
     * Configure validators constraints. The Validator object uses this method
     * to perform object validation.
     *
     * @param ClassMetadata $metadata
     */
    static public function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('parent_id', new NotNull());
        $metadata->addPropertyConstraint('child_id', new NotNull());
    }

    /**
     * Validates the object and all objects related to this table.
     *
     * @see        getValidationFailures()
     * @param      ValidatorInterface|null $validator A Validator class instance
     * @return     boolean Whether all objects pass validation.
     */
    public function validate(ValidatorInterface $validator = null)
    {
        if (null === $validator) {
            $validator = new RecursiveValidator(
                new ExecutionContextFactory(new IdentityTranslator()),
                new LazyLoadingMetadataFactory(new StaticMethodLoader()),
                new ConstraintValidatorFactory()
            );
        }

        $failureMap = new ConstraintViolationList();

        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            // We call the validate method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            // If validate() method exists, the validate-behavior is configured for related object
            if (method_exists($this->aParent, 'validate')) {
                if (!$this->aParent->validate($validator)) {
                    $failureMap->addAll($this->aParent->getValidationFailures());
                }
            }
            // If validate() method exists, the validate-behavior is configured for related object
            if (method_exists($this->aChild, 'validate')) {
                if (!$this->aChild->validate($validator)) {
                    $failureMap->addAll($this->aChild->getValidationFailures());
                }
            }
            // If validate() method exists, the validate-behavior is configured for related object
            if (method_exists($this->aTag, 'validate')) {
                if (!$this->aTag->validate($validator)) {
                    $failureMap->addAll($this->aTag->getValidationFailures());
                }
            }
            // If validate() method exists, the validate-behavior is configured for related object
            if (method_exists($this->aReaction, 'validate')) {
                if (!$this->aReaction->validate($validator)) {
                    $failureMap->addAll($this->aReaction->getValidationFailures());
                }
            }

            $retval = $validator->validate($this);
            if (count($retval) > 0) {
                $failureMap->addAll($retval);
            }


            $this->alreadyInValidation = false;
        }

        $this->validationFailures = $failureMap;

        return (Boolean) (!(count($this->validationFailures) > 0));

    }

    /**
     * Gets any ConstraintViolation objects that resulted from last call to validate().
     *
     *
     * @return     object ConstraintViolationList
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preSave')) {
            return parent::preSave($con);
        }
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postSave')) {
            parent::postSave($con);
        }
    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preInsert')) {
            return parent::preInsert($con);
        }
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postInsert')) {
            parent::postInsert($con);
        }
    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preUpdate')) {
            return parent::preUpdate($con);
        }
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postUpdate')) {
            parent::postUpdate($con);
        }
    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preDelete')) {
            return parent::preDelete($con);
        }
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postDelete')) {
            parent::postDelete($con);
        }
    }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
