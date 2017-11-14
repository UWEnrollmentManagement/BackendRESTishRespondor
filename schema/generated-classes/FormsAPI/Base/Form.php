<?php

namespace FormsAPI\Base;

use \Exception;
use \PDO;
use FormsAPI\ChildFormRelationship as ChildChildFormRelationship;
use FormsAPI\ChildFormRelationshipQuery as ChildChildFormRelationshipQuery;
use FormsAPI\DashboardForm as ChildDashboardForm;
use FormsAPI\DashboardFormQuery as ChildDashboardFormQuery;
use FormsAPI\Element as ChildElement;
use FormsAPI\ElementQuery as ChildElementQuery;
use FormsAPI\Form as ChildForm;
use FormsAPI\FormQuery as ChildFormQuery;
use FormsAPI\FormReaction as ChildFormReaction;
use FormsAPI\FormReactionQuery as ChildFormReactionQuery;
use FormsAPI\FormStatus as ChildFormStatus;
use FormsAPI\FormStatusQuery as ChildFormStatusQuery;
use FormsAPI\FormTag as ChildFormTag;
use FormsAPI\FormTagQuery as ChildFormTagQuery;
use FormsAPI\Requirement as ChildRequirement;
use FormsAPI\RequirementQuery as ChildRequirementQuery;
use FormsAPI\Submission as ChildSubmission;
use FormsAPI\SubmissionQuery as ChildSubmissionQuery;
use FormsAPI\Map\ChildFormRelationshipTableMap;
use FormsAPI\Map\DashboardFormTableMap;
use FormsAPI\Map\FormReactionTableMap;
use FormsAPI\Map\FormStatusTableMap;
use FormsAPI\Map\FormTableMap;
use FormsAPI\Map\FormTagTableMap;
use FormsAPI\Map\RequirementTableMap;
use FormsAPI\Map\SubmissionTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
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
 * Base class that represents a row from the 'form' table.
 *
 *
 *
 * @package    propel.generator.FormsAPI.Base
 */
abstract class Form implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\FormsAPI\\Map\\FormTableMap';


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
     * The value for the name field.
     *
     * @var        string
     */
    protected $name;

    /**
     * The value for the slug field.
     *
     * @var        string
     */
    protected $slug;

    /**
     * The value for the success_message field.
     *
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $success_message;

    /**
     * The value for the retired field.
     *
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $retired;

    /**
     * The value for the root_element_id field.
     *
     * @var        int
     */
    protected $root_element_id;

    /**
     * @var        ChildElement
     */
    protected $aElement;

    /**
     * @var        ObjectCollection|ChildChildFormRelationship[] Collection to store aggregation of ChildChildFormRelationship objects.
     */
    protected $collAsParents;
    protected $collAsParentsPartial;

    /**
     * @var        ObjectCollection|ChildChildFormRelationship[] Collection to store aggregation of ChildChildFormRelationship objects.
     */
    protected $collAschildren;
    protected $collAschildrenPartial;

    /**
     * @var        ObjectCollection|ChildRequirement[] Collection to store aggregation of ChildRequirement objects.
     */
    protected $collRequirements;
    protected $collRequirementsPartial;

    /**
     * @var        ObjectCollection|ChildSubmission[] Collection to store aggregation of ChildSubmission objects.
     */
    protected $collSubmissions;
    protected $collSubmissionsPartial;

    /**
     * @var        ObjectCollection|ChildFormStatus[] Collection to store aggregation of ChildFormStatus objects.
     */
    protected $collFormStatuses;
    protected $collFormStatusesPartial;

    /**
     * @var        ObjectCollection|ChildFormTag[] Collection to store aggregation of ChildFormTag objects.
     */
    protected $collFormTags;
    protected $collFormTagsPartial;

    /**
     * @var        ObjectCollection|ChildFormReaction[] Collection to store aggregation of ChildFormReaction objects.
     */
    protected $collFormReactions;
    protected $collFormReactionsPartial;

    /**
     * @var        ObjectCollection|ChildDashboardForm[] Collection to store aggregation of ChildDashboardForm objects.
     */
    protected $collDashboardForms;
    protected $collDashboardFormsPartial;

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
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildChildFormRelationship[]
     */
    protected $asParentsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildChildFormRelationship[]
     */
    protected $aschildrenScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildRequirement[]
     */
    protected $requirementsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildSubmission[]
     */
    protected $submissionsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildFormStatus[]
     */
    protected $formStatusesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildFormTag[]
     */
    protected $formTagsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildFormReaction[]
     */
    protected $formReactionsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildDashboardForm[]
     */
    protected $dashboardFormsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->success_message = '';
        $this->retired = false;
    }

    /**
     * Initializes internal state of FormsAPI\Base\Form object.
     * @see applyDefaults()
     */
    public function __construct()
    {
        $this->applyDefaultValues();
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
     * Compares this with another <code>Form</code> instance.  If
     * <code>obj</code> is an instance of <code>Form</code>, delegates to
     * <code>equals(Form)</code>.  Otherwise, returns <code>false</code>.
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
     * @return $this|Form The current object, for fluid interface
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
     * Get the [name] column value.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the [slug] column value.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Get the [success_message] column value.
     *
     * @return string
     */
    public function getSuccessMessage()
    {
        return $this->success_message;
    }

    /**
     * Get the [retired] column value.
     *
     * @return boolean
     */
    public function getRetired()
    {
        return $this->retired;
    }

    /**
     * Get the [retired] column value.
     *
     * @return boolean
     */
    public function isRetired()
    {
        return $this->getRetired();
    }

    /**
     * Get the [root_element_id] column value.
     *
     * @return int
     */
    public function getRootElementId()
    {
        return $this->root_element_id;
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return $this|\FormsAPI\Form The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[FormTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [name] column.
     *
     * @param string $v new value
     * @return $this|\FormsAPI\Form The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[FormTableMap::COL_NAME] = true;
        }

        return $this;
    } // setName()

    /**
     * Set the value of [slug] column.
     *
     * @param string $v new value
     * @return $this|\FormsAPI\Form The current object (for fluent API support)
     */
    public function setSlug($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->slug !== $v) {
            $this->slug = $v;
            $this->modifiedColumns[FormTableMap::COL_SLUG] = true;
        }

        return $this;
    } // setSlug()

    /**
     * Set the value of [success_message] column.
     *
     * @param string $v new value
     * @return $this|\FormsAPI\Form The current object (for fluent API support)
     */
    public function setSuccessMessage($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->success_message !== $v) {
            $this->success_message = $v;
            $this->modifiedColumns[FormTableMap::COL_SUCCESS_MESSAGE] = true;
        }

        return $this;
    } // setSuccessMessage()

    /**
     * Sets the value of the [retired] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param  boolean|integer|string $v The new value
     * @return $this|\FormsAPI\Form The current object (for fluent API support)
     */
    public function setRetired($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->retired !== $v) {
            $this->retired = $v;
            $this->modifiedColumns[FormTableMap::COL_RETIRED] = true;
        }

        return $this;
    } // setRetired()

    /**
     * Set the value of [root_element_id] column.
     *
     * @param int $v new value
     * @return $this|\FormsAPI\Form The current object (for fluent API support)
     */
    public function setRootElementId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->root_element_id !== $v) {
            $this->root_element_id = $v;
            $this->modifiedColumns[FormTableMap::COL_ROOT_ELEMENT_ID] = true;
        }

        if ($this->aElement !== null && $this->aElement->getId() !== $v) {
            $this->aElement = null;
        }

        return $this;
    } // setRootElementId()

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
            if ($this->success_message !== '') {
                return false;
            }

            if ($this->retired !== false) {
                return false;
            }

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

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : FormTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : FormTableMap::translateFieldName('Name', TableMap::TYPE_PHPNAME, $indexType)];
            $this->name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : FormTableMap::translateFieldName('Slug', TableMap::TYPE_PHPNAME, $indexType)];
            $this->slug = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : FormTableMap::translateFieldName('SuccessMessage', TableMap::TYPE_PHPNAME, $indexType)];
            $this->success_message = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : FormTableMap::translateFieldName('Retired', TableMap::TYPE_PHPNAME, $indexType)];
            $this->retired = (null !== $col) ? (boolean) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : FormTableMap::translateFieldName('RootElementId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->root_element_id = (null !== $col) ? (int) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 6; // 6 = FormTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\FormsAPI\\Form'), 0, $e);
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
        if ($this->aElement !== null && $this->root_element_id !== $this->aElement->getId()) {
            $this->aElement = null;
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
            $con = Propel::getServiceContainer()->getReadConnection(FormTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildFormQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aElement = null;
            $this->collAsParents = null;

            $this->collAschildren = null;

            $this->collRequirements = null;

            $this->collSubmissions = null;

            $this->collFormStatuses = null;

            $this->collFormTags = null;

            $this->collFormReactions = null;

            $this->collDashboardForms = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Form::setDeleted()
     * @see Form::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(FormTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildFormQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(FormTableMap::DATABASE_NAME);
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
                FormTableMap::addInstanceToPool($this);
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

            if ($this->aElement !== null) {
                if ($this->aElement->isModified() || $this->aElement->isNew()) {
                    $affectedRows += $this->aElement->save($con);
                }
                $this->setElement($this->aElement);
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

            if ($this->asParentsScheduledForDeletion !== null) {
                if (!$this->asParentsScheduledForDeletion->isEmpty()) {
                    \FormsAPI\ChildFormRelationshipQuery::create()
                        ->filterByPrimaryKeys($this->asParentsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->asParentsScheduledForDeletion = null;
                }
            }

            if ($this->collAsParents !== null) {
                foreach ($this->collAsParents as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->aschildrenScheduledForDeletion !== null) {
                if (!$this->aschildrenScheduledForDeletion->isEmpty()) {
                    \FormsAPI\ChildFormRelationshipQuery::create()
                        ->filterByPrimaryKeys($this->aschildrenScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->aschildrenScheduledForDeletion = null;
                }
            }

            if ($this->collAschildren !== null) {
                foreach ($this->collAschildren as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->requirementsScheduledForDeletion !== null) {
                if (!$this->requirementsScheduledForDeletion->isEmpty()) {
                    \FormsAPI\RequirementQuery::create()
                        ->filterByPrimaryKeys($this->requirementsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->requirementsScheduledForDeletion = null;
                }
            }

            if ($this->collRequirements !== null) {
                foreach ($this->collRequirements as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->submissionsScheduledForDeletion !== null) {
                if (!$this->submissionsScheduledForDeletion->isEmpty()) {
                    \FormsAPI\SubmissionQuery::create()
                        ->filterByPrimaryKeys($this->submissionsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->submissionsScheduledForDeletion = null;
                }
            }

            if ($this->collSubmissions !== null) {
                foreach ($this->collSubmissions as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->formStatusesScheduledForDeletion !== null) {
                if (!$this->formStatusesScheduledForDeletion->isEmpty()) {
                    \FormsAPI\FormStatusQuery::create()
                        ->filterByPrimaryKeys($this->formStatusesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->formStatusesScheduledForDeletion = null;
                }
            }

            if ($this->collFormStatuses !== null) {
                foreach ($this->collFormStatuses as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->formTagsScheduledForDeletion !== null) {
                if (!$this->formTagsScheduledForDeletion->isEmpty()) {
                    \FormsAPI\FormTagQuery::create()
                        ->filterByPrimaryKeys($this->formTagsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->formTagsScheduledForDeletion = null;
                }
            }

            if ($this->collFormTags !== null) {
                foreach ($this->collFormTags as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->formReactionsScheduledForDeletion !== null) {
                if (!$this->formReactionsScheduledForDeletion->isEmpty()) {
                    \FormsAPI\FormReactionQuery::create()
                        ->filterByPrimaryKeys($this->formReactionsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->formReactionsScheduledForDeletion = null;
                }
            }

            if ($this->collFormReactions !== null) {
                foreach ($this->collFormReactions as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->dashboardFormsScheduledForDeletion !== null) {
                if (!$this->dashboardFormsScheduledForDeletion->isEmpty()) {
                    \FormsAPI\DashboardFormQuery::create()
                        ->filterByPrimaryKeys($this->dashboardFormsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->dashboardFormsScheduledForDeletion = null;
                }
            }

            if ($this->collDashboardForms !== null) {
                foreach ($this->collDashboardForms as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
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

        $this->modifiedColumns[FormTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . FormTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(FormTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(FormTableMap::COL_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'name';
        }
        if ($this->isColumnModified(FormTableMap::COL_SLUG)) {
            $modifiedColumns[':p' . $index++]  = 'slug';
        }
        if ($this->isColumnModified(FormTableMap::COL_SUCCESS_MESSAGE)) {
            $modifiedColumns[':p' . $index++]  = 'success_message';
        }
        if ($this->isColumnModified(FormTableMap::COL_RETIRED)) {
            $modifiedColumns[':p' . $index++]  = 'retired';
        }
        if ($this->isColumnModified(FormTableMap::COL_ROOT_ELEMENT_ID)) {
            $modifiedColumns[':p' . $index++]  = 'root_element_id';
        }

        $sql = sprintf(
            'INSERT INTO form (%s) VALUES (%s)',
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
                    case 'name':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case 'slug':
                        $stmt->bindValue($identifier, $this->slug, PDO::PARAM_STR);
                        break;
                    case 'success_message':
                        $stmt->bindValue($identifier, $this->success_message, PDO::PARAM_STR);
                        break;
                    case 'retired':
                        $stmt->bindValue($identifier, $this->retired, PDO::PARAM_BOOL);
                        break;
                    case 'root_element_id':
                        $stmt->bindValue($identifier, $this->root_element_id, PDO::PARAM_INT);
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
        $pos = FormTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getName();
                break;
            case 2:
                return $this->getSlug();
                break;
            case 3:
                return $this->getSuccessMessage();
                break;
            case 4:
                return $this->getRetired();
                break;
            case 5:
                return $this->getRootElementId();
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

        if (isset($alreadyDumpedObjects['Form'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Form'][$this->hashCode()] = true;
        $keys = FormTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getName(),
            $keys[2] => $this->getSlug(),
            $keys[3] => $this->getSuccessMessage(),
            $keys[4] => $this->getRetired(),
            $keys[5] => $this->getRootElementId(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aElement) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'element';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'element';
                        break;
                    default:
                        $key = 'Element';
                }

                $result[$key] = $this->aElement->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collAsParents) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'childFormRelationships';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'child_form_relationships';
                        break;
                    default:
                        $key = 'AsParents';
                }

                $result[$key] = $this->collAsParents->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collAschildren) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'childFormRelationships';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'child_form_relationships';
                        break;
                    default:
                        $key = 'Aschildren';
                }

                $result[$key] = $this->collAschildren->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collRequirements) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'requirements';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'requirements';
                        break;
                    default:
                        $key = 'Requirements';
                }

                $result[$key] = $this->collRequirements->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collSubmissions) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'submissions';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'submissions';
                        break;
                    default:
                        $key = 'Submissions';
                }

                $result[$key] = $this->collSubmissions->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collFormStatuses) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'formStatuses';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'form_statuses';
                        break;
                    default:
                        $key = 'FormStatuses';
                }

                $result[$key] = $this->collFormStatuses->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collFormTags) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'formTags';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'form_tags';
                        break;
                    default:
                        $key = 'FormTags';
                }

                $result[$key] = $this->collFormTags->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collFormReactions) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'formReactions';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'form_reactions';
                        break;
                    default:
                        $key = 'FormReactions';
                }

                $result[$key] = $this->collFormReactions->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collDashboardForms) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'dashboardForms';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'dashboard_forms';
                        break;
                    default:
                        $key = 'DashboardForms';
                }

                $result[$key] = $this->collDashboardForms->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
     * @return $this|\FormsAPI\Form
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = FormTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\FormsAPI\Form
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setName($value);
                break;
            case 2:
                $this->setSlug($value);
                break;
            case 3:
                $this->setSuccessMessage($value);
                break;
            case 4:
                $this->setRetired($value);
                break;
            case 5:
                $this->setRootElementId($value);
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
        $keys = FormTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setName($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setSlug($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setSuccessMessage($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setRetired($arr[$keys[4]]);
        }
        if (array_key_exists($keys[5], $arr)) {
            $this->setRootElementId($arr[$keys[5]]);
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
     * @return $this|\FormsAPI\Form The current object, for fluid interface
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
        $criteria = new Criteria(FormTableMap::DATABASE_NAME);

        if ($this->isColumnModified(FormTableMap::COL_ID)) {
            $criteria->add(FormTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(FormTableMap::COL_NAME)) {
            $criteria->add(FormTableMap::COL_NAME, $this->name);
        }
        if ($this->isColumnModified(FormTableMap::COL_SLUG)) {
            $criteria->add(FormTableMap::COL_SLUG, $this->slug);
        }
        if ($this->isColumnModified(FormTableMap::COL_SUCCESS_MESSAGE)) {
            $criteria->add(FormTableMap::COL_SUCCESS_MESSAGE, $this->success_message);
        }
        if ($this->isColumnModified(FormTableMap::COL_RETIRED)) {
            $criteria->add(FormTableMap::COL_RETIRED, $this->retired);
        }
        if ($this->isColumnModified(FormTableMap::COL_ROOT_ELEMENT_ID)) {
            $criteria->add(FormTableMap::COL_ROOT_ELEMENT_ID, $this->root_element_id);
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
        $criteria = ChildFormQuery::create();
        $criteria->add(FormTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \FormsAPI\Form (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setName($this->getName());
        $copyObj->setSlug($this->getSlug());
        $copyObj->setSuccessMessage($this->getSuccessMessage());
        $copyObj->setRetired($this->getRetired());
        $copyObj->setRootElementId($this->getRootElementId());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getAsParents() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAsParent($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getAschildren() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAsChild($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getRequirements() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addRequirement($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getSubmissions() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addSubmission($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getFormStatuses() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addFormStatus($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getFormTags() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addFormTag($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getFormReactions() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addFormReaction($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getDashboardForms() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDashboardForm($relObj->copy($deepCopy));
                }
            }

        } // if ($deepCopy)

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
     * @return \FormsAPI\Form Clone of current object.
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
     * Declares an association between this object and a ChildElement object.
     *
     * @param  ChildElement $v
     * @return $this|\FormsAPI\Form The current object (for fluent API support)
     * @throws PropelException
     */
    public function setElement(ChildElement $v = null)
    {
        if ($v === null) {
            $this->setRootElementId(NULL);
        } else {
            $this->setRootElementId($v->getId());
        }

        $this->aElement = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildElement object, it will not be re-added.
        if ($v !== null) {
            $v->addRootElement($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildElement object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildElement The associated ChildElement object.
     * @throws PropelException
     */
    public function getElement(ConnectionInterface $con = null)
    {
        if ($this->aElement === null && ($this->root_element_id != 0)) {
            $this->aElement = ChildElementQuery::create()->findPk($this->root_element_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aElement->addRootElements($this);
             */
        }

        return $this->aElement;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('AsParent' == $relationName) {
            $this->initAsParents();
            return;
        }
        if ('AsChild' == $relationName) {
            $this->initAschildren();
            return;
        }
        if ('Requirement' == $relationName) {
            $this->initRequirements();
            return;
        }
        if ('Submission' == $relationName) {
            $this->initSubmissions();
            return;
        }
        if ('FormStatus' == $relationName) {
            $this->initFormStatuses();
            return;
        }
        if ('FormTag' == $relationName) {
            $this->initFormTags();
            return;
        }
        if ('FormReaction' == $relationName) {
            $this->initFormReactions();
            return;
        }
        if ('DashboardForm' == $relationName) {
            $this->initDashboardForms();
            return;
        }
    }

    /**
     * Clears out the collAsParents collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addAsParents()
     */
    public function clearAsParents()
    {
        $this->collAsParents = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collAsParents collection loaded partially.
     */
    public function resetPartialAsParents($v = true)
    {
        $this->collAsParentsPartial = $v;
    }

    /**
     * Initializes the collAsParents collection.
     *
     * By default this just sets the collAsParents collection to an empty array (like clearcollAsParents());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initAsParents($overrideExisting = true)
    {
        if (null !== $this->collAsParents && !$overrideExisting) {
            return;
        }

        $collectionClassName = ChildFormRelationshipTableMap::getTableMap()->getCollectionClassName();

        $this->collAsParents = new $collectionClassName;
        $this->collAsParents->setModel('\FormsAPI\ChildFormRelationship');
    }

    /**
     * Gets an array of ChildChildFormRelationship objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildForm is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildChildFormRelationship[] List of ChildChildFormRelationship objects
     * @throws PropelException
     */
    public function getAsParents(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collAsParentsPartial && !$this->isNew();
        if (null === $this->collAsParents || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collAsParents) {
                // return empty collection
                $this->initAsParents();
            } else {
                $collAsParents = ChildChildFormRelationshipQuery::create(null, $criteria)
                    ->filterByParent($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collAsParentsPartial && count($collAsParents)) {
                        $this->initAsParents(false);

                        foreach ($collAsParents as $obj) {
                            if (false == $this->collAsParents->contains($obj)) {
                                $this->collAsParents->append($obj);
                            }
                        }

                        $this->collAsParentsPartial = true;
                    }

                    return $collAsParents;
                }

                if ($partial && $this->collAsParents) {
                    foreach ($this->collAsParents as $obj) {
                        if ($obj->isNew()) {
                            $collAsParents[] = $obj;
                        }
                    }
                }

                $this->collAsParents = $collAsParents;
                $this->collAsParentsPartial = false;
            }
        }

        return $this->collAsParents;
    }

    /**
     * Sets a collection of ChildChildFormRelationship objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $asParents A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildForm The current object (for fluent API support)
     */
    public function setAsParents(Collection $asParents, ConnectionInterface $con = null)
    {
        /** @var ChildChildFormRelationship[] $asParentsToDelete */
        $asParentsToDelete = $this->getAsParents(new Criteria(), $con)->diff($asParents);


        $this->asParentsScheduledForDeletion = $asParentsToDelete;

        foreach ($asParentsToDelete as $asParentRemoved) {
            $asParentRemoved->setParent(null);
        }

        $this->collAsParents = null;
        foreach ($asParents as $asParent) {
            $this->addAsParent($asParent);
        }

        $this->collAsParents = $asParents;
        $this->collAsParentsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ChildFormRelationship objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ChildFormRelationship objects.
     * @throws PropelException
     */
    public function countAsParents(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collAsParentsPartial && !$this->isNew();
        if (null === $this->collAsParents || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAsParents) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getAsParents());
            }

            $query = ChildChildFormRelationshipQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByParent($this)
                ->count($con);
        }

        return count($this->collAsParents);
    }

    /**
     * Method called to associate a ChildChildFormRelationship object to this object
     * through the ChildChildFormRelationship foreign key attribute.
     *
     * @param  ChildChildFormRelationship $l ChildChildFormRelationship
     * @return $this|\FormsAPI\Form The current object (for fluent API support)
     */
    public function addAsParent(ChildChildFormRelationship $l)
    {
        if ($this->collAsParents === null) {
            $this->initAsParents();
            $this->collAsParentsPartial = true;
        }

        if (!$this->collAsParents->contains($l)) {
            $this->doAddAsParent($l);

            if ($this->asParentsScheduledForDeletion and $this->asParentsScheduledForDeletion->contains($l)) {
                $this->asParentsScheduledForDeletion->remove($this->asParentsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildChildFormRelationship $asParent The ChildChildFormRelationship object to add.
     */
    protected function doAddAsParent(ChildChildFormRelationship $asParent)
    {
        $this->collAsParents[]= $asParent;
        $asParent->setParent($this);
    }

    /**
     * @param  ChildChildFormRelationship $asParent The ChildChildFormRelationship object to remove.
     * @return $this|ChildForm The current object (for fluent API support)
     */
    public function removeAsParent(ChildChildFormRelationship $asParent)
    {
        if ($this->getAsParents()->contains($asParent)) {
            $pos = $this->collAsParents->search($asParent);
            $this->collAsParents->remove($pos);
            if (null === $this->asParentsScheduledForDeletion) {
                $this->asParentsScheduledForDeletion = clone $this->collAsParents;
                $this->asParentsScheduledForDeletion->clear();
            }
            $this->asParentsScheduledForDeletion[]= clone $asParent;
            $asParent->setParent(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Form is new, it will return
     * an empty collection; or if this Form has previously
     * been saved, it will retrieve related AsParents from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Form.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildChildFormRelationship[] List of ChildChildFormRelationship objects
     */
    public function getAsParentsJoinTag(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildChildFormRelationshipQuery::create(null, $criteria);
        $query->joinWith('Tag', $joinBehavior);

        return $this->getAsParents($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Form is new, it will return
     * an empty collection; or if this Form has previously
     * been saved, it will retrieve related AsParents from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Form.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildChildFormRelationship[] List of ChildChildFormRelationship objects
     */
    public function getAsParentsJoinReaction(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildChildFormRelationshipQuery::create(null, $criteria);
        $query->joinWith('Reaction', $joinBehavior);

        return $this->getAsParents($query, $con);
    }

    /**
     * Clears out the collAschildren collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addAschildren()
     */
    public function clearAschildren()
    {
        $this->collAschildren = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collAschildren collection loaded partially.
     */
    public function resetPartialAschildren($v = true)
    {
        $this->collAschildrenPartial = $v;
    }

    /**
     * Initializes the collAschildren collection.
     *
     * By default this just sets the collAschildren collection to an empty array (like clearcollAschildren());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initAschildren($overrideExisting = true)
    {
        if (null !== $this->collAschildren && !$overrideExisting) {
            return;
        }

        $collectionClassName = ChildFormRelationshipTableMap::getTableMap()->getCollectionClassName();

        $this->collAschildren = new $collectionClassName;
        $this->collAschildren->setModel('\FormsAPI\ChildFormRelationship');
    }

    /**
     * Gets an array of ChildChildFormRelationship objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildForm is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildChildFormRelationship[] List of ChildChildFormRelationship objects
     * @throws PropelException
     */
    public function getAschildren(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collAschildrenPartial && !$this->isNew();
        if (null === $this->collAschildren || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collAschildren) {
                // return empty collection
                $this->initAschildren();
            } else {
                $collAschildren = ChildChildFormRelationshipQuery::create(null, $criteria)
                    ->filterByChild($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collAschildrenPartial && count($collAschildren)) {
                        $this->initAschildren(false);

                        foreach ($collAschildren as $obj) {
                            if (false == $this->collAschildren->contains($obj)) {
                                $this->collAschildren->append($obj);
                            }
                        }

                        $this->collAschildrenPartial = true;
                    }

                    return $collAschildren;
                }

                if ($partial && $this->collAschildren) {
                    foreach ($this->collAschildren as $obj) {
                        if ($obj->isNew()) {
                            $collAschildren[] = $obj;
                        }
                    }
                }

                $this->collAschildren = $collAschildren;
                $this->collAschildrenPartial = false;
            }
        }

        return $this->collAschildren;
    }

    /**
     * Sets a collection of ChildChildFormRelationship objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $aschildren A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildForm The current object (for fluent API support)
     */
    public function setAschildren(Collection $aschildren, ConnectionInterface $con = null)
    {
        /** @var ChildChildFormRelationship[] $aschildrenToDelete */
        $aschildrenToDelete = $this->getAschildren(new Criteria(), $con)->diff($aschildren);


        $this->aschildrenScheduledForDeletion = $aschildrenToDelete;

        foreach ($aschildrenToDelete as $asChildRemoved) {
            $asChildRemoved->setChild(null);
        }

        $this->collAschildren = null;
        foreach ($aschildren as $asChild) {
            $this->addAsChild($asChild);
        }

        $this->collAschildren = $aschildren;
        $this->collAschildrenPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ChildFormRelationship objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ChildFormRelationship objects.
     * @throws PropelException
     */
    public function countAschildren(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collAschildrenPartial && !$this->isNew();
        if (null === $this->collAschildren || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAschildren) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getAschildren());
            }

            $query = ChildChildFormRelationshipQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByChild($this)
                ->count($con);
        }

        return count($this->collAschildren);
    }

    /**
     * Method called to associate a ChildChildFormRelationship object to this object
     * through the ChildChildFormRelationship foreign key attribute.
     *
     * @param  ChildChildFormRelationship $l ChildChildFormRelationship
     * @return $this|\FormsAPI\Form The current object (for fluent API support)
     */
    public function addAsChild(ChildChildFormRelationship $l)
    {
        if ($this->collAschildren === null) {
            $this->initAschildren();
            $this->collAschildrenPartial = true;
        }

        if (!$this->collAschildren->contains($l)) {
            $this->doAddAsChild($l);

            if ($this->aschildrenScheduledForDeletion and $this->aschildrenScheduledForDeletion->contains($l)) {
                $this->aschildrenScheduledForDeletion->remove($this->aschildrenScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildChildFormRelationship $asChild The ChildChildFormRelationship object to add.
     */
    protected function doAddAsChild(ChildChildFormRelationship $asChild)
    {
        $this->collAschildren[]= $asChild;
        $asChild->setChild($this);
    }

    /**
     * @param  ChildChildFormRelationship $asChild The ChildChildFormRelationship object to remove.
     * @return $this|ChildForm The current object (for fluent API support)
     */
    public function removeAsChild(ChildChildFormRelationship $asChild)
    {
        if ($this->getAschildren()->contains($asChild)) {
            $pos = $this->collAschildren->search($asChild);
            $this->collAschildren->remove($pos);
            if (null === $this->aschildrenScheduledForDeletion) {
                $this->aschildrenScheduledForDeletion = clone $this->collAschildren;
                $this->aschildrenScheduledForDeletion->clear();
            }
            $this->aschildrenScheduledForDeletion[]= clone $asChild;
            $asChild->setChild(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Form is new, it will return
     * an empty collection; or if this Form has previously
     * been saved, it will retrieve related Aschildren from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Form.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildChildFormRelationship[] List of ChildChildFormRelationship objects
     */
    public function getAschildrenJoinTag(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildChildFormRelationshipQuery::create(null, $criteria);
        $query->joinWith('Tag', $joinBehavior);

        return $this->getAschildren($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Form is new, it will return
     * an empty collection; or if this Form has previously
     * been saved, it will retrieve related Aschildren from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Form.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildChildFormRelationship[] List of ChildChildFormRelationship objects
     */
    public function getAschildrenJoinReaction(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildChildFormRelationshipQuery::create(null, $criteria);
        $query->joinWith('Reaction', $joinBehavior);

        return $this->getAschildren($query, $con);
    }

    /**
     * Clears out the collRequirements collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addRequirements()
     */
    public function clearRequirements()
    {
        $this->collRequirements = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collRequirements collection loaded partially.
     */
    public function resetPartialRequirements($v = true)
    {
        $this->collRequirementsPartial = $v;
    }

    /**
     * Initializes the collRequirements collection.
     *
     * By default this just sets the collRequirements collection to an empty array (like clearcollRequirements());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initRequirements($overrideExisting = true)
    {
        if (null !== $this->collRequirements && !$overrideExisting) {
            return;
        }

        $collectionClassName = RequirementTableMap::getTableMap()->getCollectionClassName();

        $this->collRequirements = new $collectionClassName;
        $this->collRequirements->setModel('\FormsAPI\Requirement');
    }

    /**
     * Gets an array of ChildRequirement objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildForm is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildRequirement[] List of ChildRequirement objects
     * @throws PropelException
     */
    public function getRequirements(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collRequirementsPartial && !$this->isNew();
        if (null === $this->collRequirements || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collRequirements) {
                // return empty collection
                $this->initRequirements();
            } else {
                $collRequirements = ChildRequirementQuery::create(null, $criteria)
                    ->filterByForm($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collRequirementsPartial && count($collRequirements)) {
                        $this->initRequirements(false);

                        foreach ($collRequirements as $obj) {
                            if (false == $this->collRequirements->contains($obj)) {
                                $this->collRequirements->append($obj);
                            }
                        }

                        $this->collRequirementsPartial = true;
                    }

                    return $collRequirements;
                }

                if ($partial && $this->collRequirements) {
                    foreach ($this->collRequirements as $obj) {
                        if ($obj->isNew()) {
                            $collRequirements[] = $obj;
                        }
                    }
                }

                $this->collRequirements = $collRequirements;
                $this->collRequirementsPartial = false;
            }
        }

        return $this->collRequirements;
    }

    /**
     * Sets a collection of ChildRequirement objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $requirements A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildForm The current object (for fluent API support)
     */
    public function setRequirements(Collection $requirements, ConnectionInterface $con = null)
    {
        /** @var ChildRequirement[] $requirementsToDelete */
        $requirementsToDelete = $this->getRequirements(new Criteria(), $con)->diff($requirements);


        $this->requirementsScheduledForDeletion = $requirementsToDelete;

        foreach ($requirementsToDelete as $requirementRemoved) {
            $requirementRemoved->setForm(null);
        }

        $this->collRequirements = null;
        foreach ($requirements as $requirement) {
            $this->addRequirement($requirement);
        }

        $this->collRequirements = $requirements;
        $this->collRequirementsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Requirement objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Requirement objects.
     * @throws PropelException
     */
    public function countRequirements(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collRequirementsPartial && !$this->isNew();
        if (null === $this->collRequirements || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collRequirements) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getRequirements());
            }

            $query = ChildRequirementQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByForm($this)
                ->count($con);
        }

        return count($this->collRequirements);
    }

    /**
     * Method called to associate a ChildRequirement object to this object
     * through the ChildRequirement foreign key attribute.
     *
     * @param  ChildRequirement $l ChildRequirement
     * @return $this|\FormsAPI\Form The current object (for fluent API support)
     */
    public function addRequirement(ChildRequirement $l)
    {
        if ($this->collRequirements === null) {
            $this->initRequirements();
            $this->collRequirementsPartial = true;
        }

        if (!$this->collRequirements->contains($l)) {
            $this->doAddRequirement($l);

            if ($this->requirementsScheduledForDeletion and $this->requirementsScheduledForDeletion->contains($l)) {
                $this->requirementsScheduledForDeletion->remove($this->requirementsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildRequirement $requirement The ChildRequirement object to add.
     */
    protected function doAddRequirement(ChildRequirement $requirement)
    {
        $this->collRequirements[]= $requirement;
        $requirement->setForm($this);
    }

    /**
     * @param  ChildRequirement $requirement The ChildRequirement object to remove.
     * @return $this|ChildForm The current object (for fluent API support)
     */
    public function removeRequirement(ChildRequirement $requirement)
    {
        if ($this->getRequirements()->contains($requirement)) {
            $pos = $this->collRequirements->search($requirement);
            $this->collRequirements->remove($pos);
            if (null === $this->requirementsScheduledForDeletion) {
                $this->requirementsScheduledForDeletion = clone $this->collRequirements;
                $this->requirementsScheduledForDeletion->clear();
            }
            $this->requirementsScheduledForDeletion[]= clone $requirement;
            $requirement->setForm(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Form is new, it will return
     * an empty collection; or if this Form has previously
     * been saved, it will retrieve related Requirements from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Form.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildRequirement[] List of ChildRequirement objects
     */
    public function getRequirementsJoinElement(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildRequirementQuery::create(null, $criteria);
        $query->joinWith('Element', $joinBehavior);

        return $this->getRequirements($query, $con);
    }

    /**
     * Clears out the collSubmissions collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addSubmissions()
     */
    public function clearSubmissions()
    {
        $this->collSubmissions = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collSubmissions collection loaded partially.
     */
    public function resetPartialSubmissions($v = true)
    {
        $this->collSubmissionsPartial = $v;
    }

    /**
     * Initializes the collSubmissions collection.
     *
     * By default this just sets the collSubmissions collection to an empty array (like clearcollSubmissions());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initSubmissions($overrideExisting = true)
    {
        if (null !== $this->collSubmissions && !$overrideExisting) {
            return;
        }

        $collectionClassName = SubmissionTableMap::getTableMap()->getCollectionClassName();

        $this->collSubmissions = new $collectionClassName;
        $this->collSubmissions->setModel('\FormsAPI\Submission');
    }

    /**
     * Gets an array of ChildSubmission objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildForm is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildSubmission[] List of ChildSubmission objects
     * @throws PropelException
     */
    public function getSubmissions(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collSubmissionsPartial && !$this->isNew();
        if (null === $this->collSubmissions || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collSubmissions) {
                // return empty collection
                $this->initSubmissions();
            } else {
                $collSubmissions = ChildSubmissionQuery::create(null, $criteria)
                    ->filterByForm($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collSubmissionsPartial && count($collSubmissions)) {
                        $this->initSubmissions(false);

                        foreach ($collSubmissions as $obj) {
                            if (false == $this->collSubmissions->contains($obj)) {
                                $this->collSubmissions->append($obj);
                            }
                        }

                        $this->collSubmissionsPartial = true;
                    }

                    return $collSubmissions;
                }

                if ($partial && $this->collSubmissions) {
                    foreach ($this->collSubmissions as $obj) {
                        if ($obj->isNew()) {
                            $collSubmissions[] = $obj;
                        }
                    }
                }

                $this->collSubmissions = $collSubmissions;
                $this->collSubmissionsPartial = false;
            }
        }

        return $this->collSubmissions;
    }

    /**
     * Sets a collection of ChildSubmission objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $submissions A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildForm The current object (for fluent API support)
     */
    public function setSubmissions(Collection $submissions, ConnectionInterface $con = null)
    {
        /** @var ChildSubmission[] $submissionsToDelete */
        $submissionsToDelete = $this->getSubmissions(new Criteria(), $con)->diff($submissions);


        $this->submissionsScheduledForDeletion = $submissionsToDelete;

        foreach ($submissionsToDelete as $submissionRemoved) {
            $submissionRemoved->setForm(null);
        }

        $this->collSubmissions = null;
        foreach ($submissions as $submission) {
            $this->addSubmission($submission);
        }

        $this->collSubmissions = $submissions;
        $this->collSubmissionsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Submission objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Submission objects.
     * @throws PropelException
     */
    public function countSubmissions(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collSubmissionsPartial && !$this->isNew();
        if (null === $this->collSubmissions || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collSubmissions) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getSubmissions());
            }

            $query = ChildSubmissionQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByForm($this)
                ->count($con);
        }

        return count($this->collSubmissions);
    }

    /**
     * Method called to associate a ChildSubmission object to this object
     * through the ChildSubmission foreign key attribute.
     *
     * @param  ChildSubmission $l ChildSubmission
     * @return $this|\FormsAPI\Form The current object (for fluent API support)
     */
    public function addSubmission(ChildSubmission $l)
    {
        if ($this->collSubmissions === null) {
            $this->initSubmissions();
            $this->collSubmissionsPartial = true;
        }

        if (!$this->collSubmissions->contains($l)) {
            $this->doAddSubmission($l);

            if ($this->submissionsScheduledForDeletion and $this->submissionsScheduledForDeletion->contains($l)) {
                $this->submissionsScheduledForDeletion->remove($this->submissionsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildSubmission $submission The ChildSubmission object to add.
     */
    protected function doAddSubmission(ChildSubmission $submission)
    {
        $this->collSubmissions[]= $submission;
        $submission->setForm($this);
    }

    /**
     * @param  ChildSubmission $submission The ChildSubmission object to remove.
     * @return $this|ChildForm The current object (for fluent API support)
     */
    public function removeSubmission(ChildSubmission $submission)
    {
        if ($this->getSubmissions()->contains($submission)) {
            $pos = $this->collSubmissions->search($submission);
            $this->collSubmissions->remove($pos);
            if (null === $this->submissionsScheduledForDeletion) {
                $this->submissionsScheduledForDeletion = clone $this->collSubmissions;
                $this->submissionsScheduledForDeletion->clear();
            }
            $this->submissionsScheduledForDeletion[]= clone $submission;
            $submission->setForm(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Form is new, it will return
     * an empty collection; or if this Form has previously
     * been saved, it will retrieve related Submissions from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Form.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildSubmission[] List of ChildSubmission objects
     */
    public function getSubmissionsJoinVisitor(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildSubmissionQuery::create(null, $criteria);
        $query->joinWith('Visitor', $joinBehavior);

        return $this->getSubmissions($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Form is new, it will return
     * an empty collection; or if this Form has previously
     * been saved, it will retrieve related Submissions from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Form.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildSubmission[] List of ChildSubmission objects
     */
    public function getSubmissionsJoinStatus(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildSubmissionQuery::create(null, $criteria);
        $query->joinWith('Status', $joinBehavior);

        return $this->getSubmissions($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Form is new, it will return
     * an empty collection; or if this Form has previously
     * been saved, it will retrieve related Submissions from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Form.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildSubmission[] List of ChildSubmission objects
     */
    public function getSubmissionsJoinAssignee(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildSubmissionQuery::create(null, $criteria);
        $query->joinWith('Assignee', $joinBehavior);

        return $this->getSubmissions($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Form is new, it will return
     * an empty collection; or if this Form has previously
     * been saved, it will retrieve related Submissions from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Form.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildSubmission[] List of ChildSubmission objects
     */
    public function getSubmissionsJoinSubmissionRelatedByParentId(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildSubmissionQuery::create(null, $criteria);
        $query->joinWith('SubmissionRelatedByParentId', $joinBehavior);

        return $this->getSubmissions($query, $con);
    }

    /**
     * Clears out the collFormStatuses collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addFormStatuses()
     */
    public function clearFormStatuses()
    {
        $this->collFormStatuses = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collFormStatuses collection loaded partially.
     */
    public function resetPartialFormStatuses($v = true)
    {
        $this->collFormStatusesPartial = $v;
    }

    /**
     * Initializes the collFormStatuses collection.
     *
     * By default this just sets the collFormStatuses collection to an empty array (like clearcollFormStatuses());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initFormStatuses($overrideExisting = true)
    {
        if (null !== $this->collFormStatuses && !$overrideExisting) {
            return;
        }

        $collectionClassName = FormStatusTableMap::getTableMap()->getCollectionClassName();

        $this->collFormStatuses = new $collectionClassName;
        $this->collFormStatuses->setModel('\FormsAPI\FormStatus');
    }

    /**
     * Gets an array of ChildFormStatus objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildForm is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildFormStatus[] List of ChildFormStatus objects
     * @throws PropelException
     */
    public function getFormStatuses(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collFormStatusesPartial && !$this->isNew();
        if (null === $this->collFormStatuses || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collFormStatuses) {
                // return empty collection
                $this->initFormStatuses();
            } else {
                $collFormStatuses = ChildFormStatusQuery::create(null, $criteria)
                    ->filterByForm($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collFormStatusesPartial && count($collFormStatuses)) {
                        $this->initFormStatuses(false);

                        foreach ($collFormStatuses as $obj) {
                            if (false == $this->collFormStatuses->contains($obj)) {
                                $this->collFormStatuses->append($obj);
                            }
                        }

                        $this->collFormStatusesPartial = true;
                    }

                    return $collFormStatuses;
                }

                if ($partial && $this->collFormStatuses) {
                    foreach ($this->collFormStatuses as $obj) {
                        if ($obj->isNew()) {
                            $collFormStatuses[] = $obj;
                        }
                    }
                }

                $this->collFormStatuses = $collFormStatuses;
                $this->collFormStatusesPartial = false;
            }
        }

        return $this->collFormStatuses;
    }

    /**
     * Sets a collection of ChildFormStatus objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $formStatuses A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildForm The current object (for fluent API support)
     */
    public function setFormStatuses(Collection $formStatuses, ConnectionInterface $con = null)
    {
        /** @var ChildFormStatus[] $formStatusesToDelete */
        $formStatusesToDelete = $this->getFormStatuses(new Criteria(), $con)->diff($formStatuses);


        $this->formStatusesScheduledForDeletion = $formStatusesToDelete;

        foreach ($formStatusesToDelete as $formStatusRemoved) {
            $formStatusRemoved->setForm(null);
        }

        $this->collFormStatuses = null;
        foreach ($formStatuses as $formStatus) {
            $this->addFormStatus($formStatus);
        }

        $this->collFormStatuses = $formStatuses;
        $this->collFormStatusesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related FormStatus objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related FormStatus objects.
     * @throws PropelException
     */
    public function countFormStatuses(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collFormStatusesPartial && !$this->isNew();
        if (null === $this->collFormStatuses || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collFormStatuses) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getFormStatuses());
            }

            $query = ChildFormStatusQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByForm($this)
                ->count($con);
        }

        return count($this->collFormStatuses);
    }

    /**
     * Method called to associate a ChildFormStatus object to this object
     * through the ChildFormStatus foreign key attribute.
     *
     * @param  ChildFormStatus $l ChildFormStatus
     * @return $this|\FormsAPI\Form The current object (for fluent API support)
     */
    public function addFormStatus(ChildFormStatus $l)
    {
        if ($this->collFormStatuses === null) {
            $this->initFormStatuses();
            $this->collFormStatusesPartial = true;
        }

        if (!$this->collFormStatuses->contains($l)) {
            $this->doAddFormStatus($l);

            if ($this->formStatusesScheduledForDeletion and $this->formStatusesScheduledForDeletion->contains($l)) {
                $this->formStatusesScheduledForDeletion->remove($this->formStatusesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildFormStatus $formStatus The ChildFormStatus object to add.
     */
    protected function doAddFormStatus(ChildFormStatus $formStatus)
    {
        $this->collFormStatuses[]= $formStatus;
        $formStatus->setForm($this);
    }

    /**
     * @param  ChildFormStatus $formStatus The ChildFormStatus object to remove.
     * @return $this|ChildForm The current object (for fluent API support)
     */
    public function removeFormStatus(ChildFormStatus $formStatus)
    {
        if ($this->getFormStatuses()->contains($formStatus)) {
            $pos = $this->collFormStatuses->search($formStatus);
            $this->collFormStatuses->remove($pos);
            if (null === $this->formStatusesScheduledForDeletion) {
                $this->formStatusesScheduledForDeletion = clone $this->collFormStatuses;
                $this->formStatusesScheduledForDeletion->clear();
            }
            $this->formStatusesScheduledForDeletion[]= clone $formStatus;
            $formStatus->setForm(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Form is new, it will return
     * an empty collection; or if this Form has previously
     * been saved, it will retrieve related FormStatuses from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Form.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildFormStatus[] List of ChildFormStatus objects
     */
    public function getFormStatusesJoinStatus(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildFormStatusQuery::create(null, $criteria);
        $query->joinWith('Status', $joinBehavior);

        return $this->getFormStatuses($query, $con);
    }

    /**
     * Clears out the collFormTags collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addFormTags()
     */
    public function clearFormTags()
    {
        $this->collFormTags = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collFormTags collection loaded partially.
     */
    public function resetPartialFormTags($v = true)
    {
        $this->collFormTagsPartial = $v;
    }

    /**
     * Initializes the collFormTags collection.
     *
     * By default this just sets the collFormTags collection to an empty array (like clearcollFormTags());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initFormTags($overrideExisting = true)
    {
        if (null !== $this->collFormTags && !$overrideExisting) {
            return;
        }

        $collectionClassName = FormTagTableMap::getTableMap()->getCollectionClassName();

        $this->collFormTags = new $collectionClassName;
        $this->collFormTags->setModel('\FormsAPI\FormTag');
    }

    /**
     * Gets an array of ChildFormTag objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildForm is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildFormTag[] List of ChildFormTag objects
     * @throws PropelException
     */
    public function getFormTags(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collFormTagsPartial && !$this->isNew();
        if (null === $this->collFormTags || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collFormTags) {
                // return empty collection
                $this->initFormTags();
            } else {
                $collFormTags = ChildFormTagQuery::create(null, $criteria)
                    ->filterByForm($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collFormTagsPartial && count($collFormTags)) {
                        $this->initFormTags(false);

                        foreach ($collFormTags as $obj) {
                            if (false == $this->collFormTags->contains($obj)) {
                                $this->collFormTags->append($obj);
                            }
                        }

                        $this->collFormTagsPartial = true;
                    }

                    return $collFormTags;
                }

                if ($partial && $this->collFormTags) {
                    foreach ($this->collFormTags as $obj) {
                        if ($obj->isNew()) {
                            $collFormTags[] = $obj;
                        }
                    }
                }

                $this->collFormTags = $collFormTags;
                $this->collFormTagsPartial = false;
            }
        }

        return $this->collFormTags;
    }

    /**
     * Sets a collection of ChildFormTag objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $formTags A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildForm The current object (for fluent API support)
     */
    public function setFormTags(Collection $formTags, ConnectionInterface $con = null)
    {
        /** @var ChildFormTag[] $formTagsToDelete */
        $formTagsToDelete = $this->getFormTags(new Criteria(), $con)->diff($formTags);


        $this->formTagsScheduledForDeletion = $formTagsToDelete;

        foreach ($formTagsToDelete as $formTagRemoved) {
            $formTagRemoved->setForm(null);
        }

        $this->collFormTags = null;
        foreach ($formTags as $formTag) {
            $this->addFormTag($formTag);
        }

        $this->collFormTags = $formTags;
        $this->collFormTagsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related FormTag objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related FormTag objects.
     * @throws PropelException
     */
    public function countFormTags(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collFormTagsPartial && !$this->isNew();
        if (null === $this->collFormTags || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collFormTags) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getFormTags());
            }

            $query = ChildFormTagQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByForm($this)
                ->count($con);
        }

        return count($this->collFormTags);
    }

    /**
     * Method called to associate a ChildFormTag object to this object
     * through the ChildFormTag foreign key attribute.
     *
     * @param  ChildFormTag $l ChildFormTag
     * @return $this|\FormsAPI\Form The current object (for fluent API support)
     */
    public function addFormTag(ChildFormTag $l)
    {
        if ($this->collFormTags === null) {
            $this->initFormTags();
            $this->collFormTagsPartial = true;
        }

        if (!$this->collFormTags->contains($l)) {
            $this->doAddFormTag($l);

            if ($this->formTagsScheduledForDeletion and $this->formTagsScheduledForDeletion->contains($l)) {
                $this->formTagsScheduledForDeletion->remove($this->formTagsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildFormTag $formTag The ChildFormTag object to add.
     */
    protected function doAddFormTag(ChildFormTag $formTag)
    {
        $this->collFormTags[]= $formTag;
        $formTag->setForm($this);
    }

    /**
     * @param  ChildFormTag $formTag The ChildFormTag object to remove.
     * @return $this|ChildForm The current object (for fluent API support)
     */
    public function removeFormTag(ChildFormTag $formTag)
    {
        if ($this->getFormTags()->contains($formTag)) {
            $pos = $this->collFormTags->search($formTag);
            $this->collFormTags->remove($pos);
            if (null === $this->formTagsScheduledForDeletion) {
                $this->formTagsScheduledForDeletion = clone $this->collFormTags;
                $this->formTagsScheduledForDeletion->clear();
            }
            $this->formTagsScheduledForDeletion[]= clone $formTag;
            $formTag->setForm(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Form is new, it will return
     * an empty collection; or if this Form has previously
     * been saved, it will retrieve related FormTags from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Form.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildFormTag[] List of ChildFormTag objects
     */
    public function getFormTagsJoinTag(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildFormTagQuery::create(null, $criteria);
        $query->joinWith('Tag', $joinBehavior);

        return $this->getFormTags($query, $con);
    }

    /**
     * Clears out the collFormReactions collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addFormReactions()
     */
    public function clearFormReactions()
    {
        $this->collFormReactions = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collFormReactions collection loaded partially.
     */
    public function resetPartialFormReactions($v = true)
    {
        $this->collFormReactionsPartial = $v;
    }

    /**
     * Initializes the collFormReactions collection.
     *
     * By default this just sets the collFormReactions collection to an empty array (like clearcollFormReactions());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initFormReactions($overrideExisting = true)
    {
        if (null !== $this->collFormReactions && !$overrideExisting) {
            return;
        }

        $collectionClassName = FormReactionTableMap::getTableMap()->getCollectionClassName();

        $this->collFormReactions = new $collectionClassName;
        $this->collFormReactions->setModel('\FormsAPI\FormReaction');
    }

    /**
     * Gets an array of ChildFormReaction objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildForm is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildFormReaction[] List of ChildFormReaction objects
     * @throws PropelException
     */
    public function getFormReactions(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collFormReactionsPartial && !$this->isNew();
        if (null === $this->collFormReactions || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collFormReactions) {
                // return empty collection
                $this->initFormReactions();
            } else {
                $collFormReactions = ChildFormReactionQuery::create(null, $criteria)
                    ->filterByForm($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collFormReactionsPartial && count($collFormReactions)) {
                        $this->initFormReactions(false);

                        foreach ($collFormReactions as $obj) {
                            if (false == $this->collFormReactions->contains($obj)) {
                                $this->collFormReactions->append($obj);
                            }
                        }

                        $this->collFormReactionsPartial = true;
                    }

                    return $collFormReactions;
                }

                if ($partial && $this->collFormReactions) {
                    foreach ($this->collFormReactions as $obj) {
                        if ($obj->isNew()) {
                            $collFormReactions[] = $obj;
                        }
                    }
                }

                $this->collFormReactions = $collFormReactions;
                $this->collFormReactionsPartial = false;
            }
        }

        return $this->collFormReactions;
    }

    /**
     * Sets a collection of ChildFormReaction objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $formReactions A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildForm The current object (for fluent API support)
     */
    public function setFormReactions(Collection $formReactions, ConnectionInterface $con = null)
    {
        /** @var ChildFormReaction[] $formReactionsToDelete */
        $formReactionsToDelete = $this->getFormReactions(new Criteria(), $con)->diff($formReactions);


        $this->formReactionsScheduledForDeletion = $formReactionsToDelete;

        foreach ($formReactionsToDelete as $formReactionRemoved) {
            $formReactionRemoved->setForm(null);
        }

        $this->collFormReactions = null;
        foreach ($formReactions as $formReaction) {
            $this->addFormReaction($formReaction);
        }

        $this->collFormReactions = $formReactions;
        $this->collFormReactionsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related FormReaction objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related FormReaction objects.
     * @throws PropelException
     */
    public function countFormReactions(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collFormReactionsPartial && !$this->isNew();
        if (null === $this->collFormReactions || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collFormReactions) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getFormReactions());
            }

            $query = ChildFormReactionQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByForm($this)
                ->count($con);
        }

        return count($this->collFormReactions);
    }

    /**
     * Method called to associate a ChildFormReaction object to this object
     * through the ChildFormReaction foreign key attribute.
     *
     * @param  ChildFormReaction $l ChildFormReaction
     * @return $this|\FormsAPI\Form The current object (for fluent API support)
     */
    public function addFormReaction(ChildFormReaction $l)
    {
        if ($this->collFormReactions === null) {
            $this->initFormReactions();
            $this->collFormReactionsPartial = true;
        }

        if (!$this->collFormReactions->contains($l)) {
            $this->doAddFormReaction($l);

            if ($this->formReactionsScheduledForDeletion and $this->formReactionsScheduledForDeletion->contains($l)) {
                $this->formReactionsScheduledForDeletion->remove($this->formReactionsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildFormReaction $formReaction The ChildFormReaction object to add.
     */
    protected function doAddFormReaction(ChildFormReaction $formReaction)
    {
        $this->collFormReactions[]= $formReaction;
        $formReaction->setForm($this);
    }

    /**
     * @param  ChildFormReaction $formReaction The ChildFormReaction object to remove.
     * @return $this|ChildForm The current object (for fluent API support)
     */
    public function removeFormReaction(ChildFormReaction $formReaction)
    {
        if ($this->getFormReactions()->contains($formReaction)) {
            $pos = $this->collFormReactions->search($formReaction);
            $this->collFormReactions->remove($pos);
            if (null === $this->formReactionsScheduledForDeletion) {
                $this->formReactionsScheduledForDeletion = clone $this->collFormReactions;
                $this->formReactionsScheduledForDeletion->clear();
            }
            $this->formReactionsScheduledForDeletion[]= clone $formReaction;
            $formReaction->setForm(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Form is new, it will return
     * an empty collection; or if this Form has previously
     * been saved, it will retrieve related FormReactions from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Form.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildFormReaction[] List of ChildFormReaction objects
     */
    public function getFormReactionsJoinReaction(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildFormReactionQuery::create(null, $criteria);
        $query->joinWith('Reaction', $joinBehavior);

        return $this->getFormReactions($query, $con);
    }

    /**
     * Clears out the collDashboardForms collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDashboardForms()
     */
    public function clearDashboardForms()
    {
        $this->collDashboardForms = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDashboardForms collection loaded partially.
     */
    public function resetPartialDashboardForms($v = true)
    {
        $this->collDashboardFormsPartial = $v;
    }

    /**
     * Initializes the collDashboardForms collection.
     *
     * By default this just sets the collDashboardForms collection to an empty array (like clearcollDashboardForms());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDashboardForms($overrideExisting = true)
    {
        if (null !== $this->collDashboardForms && !$overrideExisting) {
            return;
        }

        $collectionClassName = DashboardFormTableMap::getTableMap()->getCollectionClassName();

        $this->collDashboardForms = new $collectionClassName;
        $this->collDashboardForms->setModel('\FormsAPI\DashboardForm');
    }

    /**
     * Gets an array of ChildDashboardForm objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildForm is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildDashboardForm[] List of ChildDashboardForm objects
     * @throws PropelException
     */
    public function getDashboardForms(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDashboardFormsPartial && !$this->isNew();
        if (null === $this->collDashboardForms || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDashboardForms) {
                // return empty collection
                $this->initDashboardForms();
            } else {
                $collDashboardForms = ChildDashboardFormQuery::create(null, $criteria)
                    ->filterByForm($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDashboardFormsPartial && count($collDashboardForms)) {
                        $this->initDashboardForms(false);

                        foreach ($collDashboardForms as $obj) {
                            if (false == $this->collDashboardForms->contains($obj)) {
                                $this->collDashboardForms->append($obj);
                            }
                        }

                        $this->collDashboardFormsPartial = true;
                    }

                    return $collDashboardForms;
                }

                if ($partial && $this->collDashboardForms) {
                    foreach ($this->collDashboardForms as $obj) {
                        if ($obj->isNew()) {
                            $collDashboardForms[] = $obj;
                        }
                    }
                }

                $this->collDashboardForms = $collDashboardForms;
                $this->collDashboardFormsPartial = false;
            }
        }

        return $this->collDashboardForms;
    }

    /**
     * Sets a collection of ChildDashboardForm objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $dashboardForms A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildForm The current object (for fluent API support)
     */
    public function setDashboardForms(Collection $dashboardForms, ConnectionInterface $con = null)
    {
        /** @var ChildDashboardForm[] $dashboardFormsToDelete */
        $dashboardFormsToDelete = $this->getDashboardForms(new Criteria(), $con)->diff($dashboardForms);


        $this->dashboardFormsScheduledForDeletion = $dashboardFormsToDelete;

        foreach ($dashboardFormsToDelete as $dashboardFormRemoved) {
            $dashboardFormRemoved->setForm(null);
        }

        $this->collDashboardForms = null;
        foreach ($dashboardForms as $dashboardForm) {
            $this->addDashboardForm($dashboardForm);
        }

        $this->collDashboardForms = $dashboardForms;
        $this->collDashboardFormsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DashboardForm objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DashboardForm objects.
     * @throws PropelException
     */
    public function countDashboardForms(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDashboardFormsPartial && !$this->isNew();
        if (null === $this->collDashboardForms || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDashboardForms) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDashboardForms());
            }

            $query = ChildDashboardFormQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByForm($this)
                ->count($con);
        }

        return count($this->collDashboardForms);
    }

    /**
     * Method called to associate a ChildDashboardForm object to this object
     * through the ChildDashboardForm foreign key attribute.
     *
     * @param  ChildDashboardForm $l ChildDashboardForm
     * @return $this|\FormsAPI\Form The current object (for fluent API support)
     */
    public function addDashboardForm(ChildDashboardForm $l)
    {
        if ($this->collDashboardForms === null) {
            $this->initDashboardForms();
            $this->collDashboardFormsPartial = true;
        }

        if (!$this->collDashboardForms->contains($l)) {
            $this->doAddDashboardForm($l);

            if ($this->dashboardFormsScheduledForDeletion and $this->dashboardFormsScheduledForDeletion->contains($l)) {
                $this->dashboardFormsScheduledForDeletion->remove($this->dashboardFormsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildDashboardForm $dashboardForm The ChildDashboardForm object to add.
     */
    protected function doAddDashboardForm(ChildDashboardForm $dashboardForm)
    {
        $this->collDashboardForms[]= $dashboardForm;
        $dashboardForm->setForm($this);
    }

    /**
     * @param  ChildDashboardForm $dashboardForm The ChildDashboardForm object to remove.
     * @return $this|ChildForm The current object (for fluent API support)
     */
    public function removeDashboardForm(ChildDashboardForm $dashboardForm)
    {
        if ($this->getDashboardForms()->contains($dashboardForm)) {
            $pos = $this->collDashboardForms->search($dashboardForm);
            $this->collDashboardForms->remove($pos);
            if (null === $this->dashboardFormsScheduledForDeletion) {
                $this->dashboardFormsScheduledForDeletion = clone $this->collDashboardForms;
                $this->dashboardFormsScheduledForDeletion->clear();
            }
            $this->dashboardFormsScheduledForDeletion[]= clone $dashboardForm;
            $dashboardForm->setForm(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Form is new, it will return
     * an empty collection; or if this Form has previously
     * been saved, it will retrieve related DashboardForms from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Form.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildDashboardForm[] List of ChildDashboardForm objects
     */
    public function getDashboardFormsJoinDashboard(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildDashboardFormQuery::create(null, $criteria);
        $query->joinWith('Dashboard', $joinBehavior);

        return $this->getDashboardForms($query, $con);
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        if (null !== $this->aElement) {
            $this->aElement->removeRootElement($this);
        }
        $this->id = null;
        $this->name = null;
        $this->slug = null;
        $this->success_message = null;
        $this->retired = null;
        $this->root_element_id = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
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
            if ($this->collAsParents) {
                foreach ($this->collAsParents as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collAschildren) {
                foreach ($this->collAschildren as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collRequirements) {
                foreach ($this->collRequirements as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collSubmissions) {
                foreach ($this->collSubmissions as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collFormStatuses) {
                foreach ($this->collFormStatuses as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collFormTags) {
                foreach ($this->collFormTags as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collFormReactions) {
                foreach ($this->collFormReactions as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collDashboardForms) {
                foreach ($this->collDashboardForms as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collAsParents = null;
        $this->collAschildren = null;
        $this->collRequirements = null;
        $this->collSubmissions = null;
        $this->collFormStatuses = null;
        $this->collFormTags = null;
        $this->collFormReactions = null;
        $this->collDashboardForms = null;
        $this->aElement = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(FormTableMap::DEFAULT_STRING_FORMAT);
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
        $metadata->addPropertyConstraint('name', new NotNull());
        $metadata->addPropertyConstraint('slug', new NotNull());
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
            if (method_exists($this->aElement, 'validate')) {
                if (!$this->aElement->validate($validator)) {
                    $failureMap->addAll($this->aElement->getValidationFailures());
                }
            }

            $retval = $validator->validate($this);
            if (count($retval) > 0) {
                $failureMap->addAll($retval);
            }

            if (null !== $this->collAsParents) {
                foreach ($this->collAsParents as $referrerFK) {
                    if (method_exists($referrerFK, 'validate')) {
                        if (!$referrerFK->validate($validator)) {
                            $failureMap->addAll($referrerFK->getValidationFailures());
                        }
                    }
                }
            }
            if (null !== $this->collAschildren) {
                foreach ($this->collAschildren as $referrerFK) {
                    if (method_exists($referrerFK, 'validate')) {
                        if (!$referrerFK->validate($validator)) {
                            $failureMap->addAll($referrerFK->getValidationFailures());
                        }
                    }
                }
            }
            if (null !== $this->collRequirements) {
                foreach ($this->collRequirements as $referrerFK) {
                    if (method_exists($referrerFK, 'validate')) {
                        if (!$referrerFK->validate($validator)) {
                            $failureMap->addAll($referrerFK->getValidationFailures());
                        }
                    }
                }
            }
            if (null !== $this->collSubmissions) {
                foreach ($this->collSubmissions as $referrerFK) {
                    if (method_exists($referrerFK, 'validate')) {
                        if (!$referrerFK->validate($validator)) {
                            $failureMap->addAll($referrerFK->getValidationFailures());
                        }
                    }
                }
            }
            if (null !== $this->collFormStatuses) {
                foreach ($this->collFormStatuses as $referrerFK) {
                    if (method_exists($referrerFK, 'validate')) {
                        if (!$referrerFK->validate($validator)) {
                            $failureMap->addAll($referrerFK->getValidationFailures());
                        }
                    }
                }
            }
            if (null !== $this->collFormTags) {
                foreach ($this->collFormTags as $referrerFK) {
                    if (method_exists($referrerFK, 'validate')) {
                        if (!$referrerFK->validate($validator)) {
                            $failureMap->addAll($referrerFK->getValidationFailures());
                        }
                    }
                }
            }
            if (null !== $this->collFormReactions) {
                foreach ($this->collFormReactions as $referrerFK) {
                    if (method_exists($referrerFK, 'validate')) {
                        if (!$referrerFK->validate($validator)) {
                            $failureMap->addAll($referrerFK->getValidationFailures());
                        }
                    }
                }
            }
            if (null !== $this->collDashboardForms) {
                foreach ($this->collDashboardForms as $referrerFK) {
                    if (method_exists($referrerFK, 'validate')) {
                        if (!$referrerFK->validate($validator)) {
                            $failureMap->addAll($referrerFK->getValidationFailures());
                        }
                    }
                }
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
