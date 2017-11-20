<?php

namespace FormsAPI\Base;

use \DateTime;
use \Exception;
use \PDO;
use FormsAPI\Form as ChildForm;
use FormsAPI\FormQuery as ChildFormQuery;
use FormsAPI\Response as ChildResponse;
use FormsAPI\ResponseQuery as ChildResponseQuery;
use FormsAPI\Status as ChildStatus;
use FormsAPI\StatusQuery as ChildStatusQuery;
use FormsAPI\Submission as ChildSubmission;
use FormsAPI\SubmissionQuery as ChildSubmissionQuery;
use FormsAPI\SubmissionTag as ChildSubmissionTag;
use FormsAPI\SubmissionTagQuery as ChildSubmissionTagQuery;
use FormsAPI\Visitor as ChildVisitor;
use FormsAPI\VisitorQuery as ChildVisitorQuery;
use FormsAPI\Map\ResponseTableMap;
use FormsAPI\Map\SubmissionTableMap;
use FormsAPI\Map\SubmissionTagTableMap;
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
use Propel\Runtime\Util\PropelDateTime;
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
 * Base class that represents a row from the 'submission' table.
 *
 *
 *
 * @package    propel.generator.FormsAPI.Base
 */
abstract class Submission implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\FormsAPI\\Map\\SubmissionTableMap';


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
     * The value for the submitted field.
     *
     * @var        DateTime
     */
    protected $submitted;

    /**
     * The value for the visitor_id field.
     *
     * @var        int
     */
    protected $visitor_id;

    /**
     * The value for the form_id field.
     *
     * @var        int
     */
    protected $form_id;

    /**
     * The value for the status_id field.
     *
     * @var        int
     */
    protected $status_id;

    /**
     * The value for the assignee_id field.
     *
     * @var        int
     */
    protected $assignee_id;

    /**
     * The value for the parent_id field.
     *
     * @var        int
     */
    protected $parent_id;

    /**
     * @var        ChildVisitor
     */
    protected $aVisitor;

    /**
     * @var        ChildForm
     */
    protected $aForm;

    /**
     * @var        ChildStatus
     */
    protected $aStatus;

    /**
     * @var        ChildVisitor
     */
    protected $aAssignee;

    /**
     * @var        ChildSubmission
     */
    protected $aSubmissionRelatedByParentId;

    /**
     * @var        ObjectCollection|ChildResponse[] Collection to store aggregation of ChildResponse objects.
     */
    protected $collResponses;
    protected $collResponsesPartial;

    /**
     * @var        ObjectCollection|ChildSubmission[] Collection to store aggregation of ChildSubmission objects.
     */
    protected $collAsParents;
    protected $collAsParentsPartial;

    /**
     * @var        ObjectCollection|ChildSubmissionTag[] Collection to store aggregation of ChildSubmissionTag objects.
     */
    protected $collSubmissionTags;
    protected $collSubmissionTagsPartial;

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
     * @var ObjectCollection|ChildResponse[]
     */
    protected $responsesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildSubmission[]
     */
    protected $asParentsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildSubmissionTag[]
     */
    protected $submissionTagsScheduledForDeletion = null;

    /**
     * Initializes internal state of FormsAPI\Base\Submission object.
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
     * Compares this with another <code>Submission</code> instance.  If
     * <code>obj</code> is an instance of <code>Submission</code>, delegates to
     * <code>equals(Submission)</code>.  Otherwise, returns <code>false</code>.
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
     * @return $this|Submission The current object, for fluid interface
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
     * Get the [optionally formatted] temporal [submitted] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getSubmitted($format = NULL)
    {
        if ($format === null) {
            return $this->submitted;
        } else {
            return $this->submitted instanceof \DateTimeInterface ? $this->submitted->format($format) : null;
        }
    }

    /**
     * Get the [visitor_id] column value.
     *
     * @return int
     */
    public function getVisitorId()
    {
        return $this->visitor_id;
    }

    /**
     * Get the [form_id] column value.
     *
     * @return int
     */
    public function getFormId()
    {
        return $this->form_id;
    }

    /**
     * Get the [status_id] column value.
     *
     * @return int
     */
    public function getStatusId()
    {
        return $this->status_id;
    }

    /**
     * Get the [assignee_id] column value.
     *
     * @return int
     */
    public function getAssigneeId()
    {
        return $this->assignee_id;
    }

    /**
     * Get the [parent_id] column value.
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return $this|\FormsAPI\Submission The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[SubmissionTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Sets the value of [submitted] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTimeInterface value.
     *               Empty strings are treated as NULL.
     * @return $this|\FormsAPI\Submission The current object (for fluent API support)
     */
    public function setSubmitted($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->submitted !== null || $dt !== null) {
            if ($this->submitted === null || $dt === null || $dt->format("Y-m-d H:i:s.u") !== $this->submitted->format("Y-m-d H:i:s.u")) {
                $this->submitted = $dt === null ? null : clone $dt;
                $this->modifiedColumns[SubmissionTableMap::COL_SUBMITTED] = true;
            }
        } // if either are not null

        return $this;
    } // setSubmitted()

    /**
     * Set the value of [visitor_id] column.
     *
     * @param int $v new value
     * @return $this|\FormsAPI\Submission The current object (for fluent API support)
     */
    public function setVisitorId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->visitor_id !== $v) {
            $this->visitor_id = $v;
            $this->modifiedColumns[SubmissionTableMap::COL_VISITOR_ID] = true;
        }

        if ($this->aVisitor !== null && $this->aVisitor->getId() !== $v) {
            $this->aVisitor = null;
        }

        return $this;
    } // setVisitorId()

    /**
     * Set the value of [form_id] column.
     *
     * @param int $v new value
     * @return $this|\FormsAPI\Submission The current object (for fluent API support)
     */
    public function setFormId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->form_id !== $v) {
            $this->form_id = $v;
            $this->modifiedColumns[SubmissionTableMap::COL_FORM_ID] = true;
        }

        if ($this->aForm !== null && $this->aForm->getId() !== $v) {
            $this->aForm = null;
        }

        return $this;
    } // setFormId()

    /**
     * Set the value of [status_id] column.
     *
     * @param int $v new value
     * @return $this|\FormsAPI\Submission The current object (for fluent API support)
     */
    public function setStatusId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->status_id !== $v) {
            $this->status_id = $v;
            $this->modifiedColumns[SubmissionTableMap::COL_STATUS_ID] = true;
        }

        if ($this->aStatus !== null && $this->aStatus->getId() !== $v) {
            $this->aStatus = null;
        }

        return $this;
    } // setStatusId()

    /**
     * Set the value of [assignee_id] column.
     *
     * @param int $v new value
     * @return $this|\FormsAPI\Submission The current object (for fluent API support)
     */
    public function setAssigneeId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->assignee_id !== $v) {
            $this->assignee_id = $v;
            $this->modifiedColumns[SubmissionTableMap::COL_ASSIGNEE_ID] = true;
        }

        if ($this->aAssignee !== null && $this->aAssignee->getId() !== $v) {
            $this->aAssignee = null;
        }

        return $this;
    } // setAssigneeId()

    /**
     * Set the value of [parent_id] column.
     *
     * @param int $v new value
     * @return $this|\FormsAPI\Submission The current object (for fluent API support)
     */
    public function setParentId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->parent_id !== $v) {
            $this->parent_id = $v;
            $this->modifiedColumns[SubmissionTableMap::COL_PARENT_ID] = true;
        }

        if ($this->aSubmissionRelatedByParentId !== null && $this->aSubmissionRelatedByParentId->getId() !== $v) {
            $this->aSubmissionRelatedByParentId = null;
        }

        return $this;
    } // setParentId()

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

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : SubmissionTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : SubmissionTableMap::translateFieldName('Submitted', TableMap::TYPE_PHPNAME, $indexType)];
            $this->submitted = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : SubmissionTableMap::translateFieldName('VisitorId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->visitor_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : SubmissionTableMap::translateFieldName('FormId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->form_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : SubmissionTableMap::translateFieldName('StatusId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->status_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : SubmissionTableMap::translateFieldName('AssigneeId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->assignee_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : SubmissionTableMap::translateFieldName('ParentId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->parent_id = (null !== $col) ? (int) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 7; // 7 = SubmissionTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\FormsAPI\\Submission'), 0, $e);
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
        if ($this->aVisitor !== null && $this->visitor_id !== $this->aVisitor->getId()) {
            $this->aVisitor = null;
        }
        if ($this->aForm !== null && $this->form_id !== $this->aForm->getId()) {
            $this->aForm = null;
        }
        if ($this->aStatus !== null && $this->status_id !== $this->aStatus->getId()) {
            $this->aStatus = null;
        }
        if ($this->aAssignee !== null && $this->assignee_id !== $this->aAssignee->getId()) {
            $this->aAssignee = null;
        }
        if ($this->aSubmissionRelatedByParentId !== null && $this->parent_id !== $this->aSubmissionRelatedByParentId->getId()) {
            $this->aSubmissionRelatedByParentId = null;
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
            $con = Propel::getServiceContainer()->getReadConnection(SubmissionTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildSubmissionQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aVisitor = null;
            $this->aForm = null;
            $this->aStatus = null;
            $this->aAssignee = null;
            $this->aSubmissionRelatedByParentId = null;
            $this->collResponses = null;

            $this->collAsParents = null;

            $this->collSubmissionTags = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Submission::setDeleted()
     * @see Submission::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(SubmissionTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildSubmissionQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(SubmissionTableMap::DATABASE_NAME);
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
                SubmissionTableMap::addInstanceToPool($this);
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

            if ($this->aVisitor !== null) {
                if ($this->aVisitor->isModified() || $this->aVisitor->isNew()) {
                    $affectedRows += $this->aVisitor->save($con);
                }
                $this->setVisitor($this->aVisitor);
            }

            if ($this->aForm !== null) {
                if ($this->aForm->isModified() || $this->aForm->isNew()) {
                    $affectedRows += $this->aForm->save($con);
                }
                $this->setForm($this->aForm);
            }

            if ($this->aStatus !== null) {
                if ($this->aStatus->isModified() || $this->aStatus->isNew()) {
                    $affectedRows += $this->aStatus->save($con);
                }
                $this->setStatus($this->aStatus);
            }

            if ($this->aAssignee !== null) {
                if ($this->aAssignee->isModified() || $this->aAssignee->isNew()) {
                    $affectedRows += $this->aAssignee->save($con);
                }
                $this->setAssignee($this->aAssignee);
            }

            if ($this->aSubmissionRelatedByParentId !== null) {
                if ($this->aSubmissionRelatedByParentId->isModified() || $this->aSubmissionRelatedByParentId->isNew()) {
                    $affectedRows += $this->aSubmissionRelatedByParentId->save($con);
                }
                $this->setSubmissionRelatedByParentId($this->aSubmissionRelatedByParentId);
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

            if ($this->responsesScheduledForDeletion !== null) {
                if (!$this->responsesScheduledForDeletion->isEmpty()) {
                    \FormsAPI\ResponseQuery::create()
                        ->filterByPrimaryKeys($this->responsesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->responsesScheduledForDeletion = null;
                }
            }

            if ($this->collResponses !== null) {
                foreach ($this->collResponses as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->asParentsScheduledForDeletion !== null) {
                if (!$this->asParentsScheduledForDeletion->isEmpty()) {
                    foreach ($this->asParentsScheduledForDeletion as $asParent) {
                        // need to save related object because we set the relation to null
                        $asParent->save($con);
                    }
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

            if ($this->submissionTagsScheduledForDeletion !== null) {
                if (!$this->submissionTagsScheduledForDeletion->isEmpty()) {
                    \FormsAPI\SubmissionTagQuery::create()
                        ->filterByPrimaryKeys($this->submissionTagsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->submissionTagsScheduledForDeletion = null;
                }
            }

            if ($this->collSubmissionTags !== null) {
                foreach ($this->collSubmissionTags as $referrerFK) {
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

        $this->modifiedColumns[SubmissionTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . SubmissionTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(SubmissionTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(SubmissionTableMap::COL_SUBMITTED)) {
            $modifiedColumns[':p' . $index++]  = 'submitted';
        }
        if ($this->isColumnModified(SubmissionTableMap::COL_VISITOR_ID)) {
            $modifiedColumns[':p' . $index++]  = 'visitor_id';
        }
        if ($this->isColumnModified(SubmissionTableMap::COL_FORM_ID)) {
            $modifiedColumns[':p' . $index++]  = 'form_id';
        }
        if ($this->isColumnModified(SubmissionTableMap::COL_STATUS_ID)) {
            $modifiedColumns[':p' . $index++]  = 'status_id';
        }
        if ($this->isColumnModified(SubmissionTableMap::COL_ASSIGNEE_ID)) {
            $modifiedColumns[':p' . $index++]  = 'assignee_id';
        }
        if ($this->isColumnModified(SubmissionTableMap::COL_PARENT_ID)) {
            $modifiedColumns[':p' . $index++]  = 'parent_id';
        }

        $sql = sprintf(
            'INSERT INTO submission (%s) VALUES (%s)',
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
                    case 'submitted':
                        $stmt->bindValue($identifier, $this->submitted ? $this->submitted->format("Y-m-d H:i:s.u") : null, PDO::PARAM_STR);
                        break;
                    case 'visitor_id':
                        $stmt->bindValue($identifier, $this->visitor_id, PDO::PARAM_INT);
                        break;
                    case 'form_id':
                        $stmt->bindValue($identifier, $this->form_id, PDO::PARAM_INT);
                        break;
                    case 'status_id':
                        $stmt->bindValue($identifier, $this->status_id, PDO::PARAM_INT);
                        break;
                    case 'assignee_id':
                        $stmt->bindValue($identifier, $this->assignee_id, PDO::PARAM_INT);
                        break;
                    case 'parent_id':
                        $stmt->bindValue($identifier, $this->parent_id, PDO::PARAM_INT);
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
        $pos = SubmissionTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getSubmitted();
                break;
            case 2:
                return $this->getVisitorId();
                break;
            case 3:
                return $this->getFormId();
                break;
            case 4:
                return $this->getStatusId();
                break;
            case 5:
                return $this->getAssigneeId();
                break;
            case 6:
                return $this->getParentId();
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

        if (isset($alreadyDumpedObjects['Submission'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Submission'][$this->hashCode()] = true;
        $keys = SubmissionTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getSubmitted(),
            $keys[2] => $this->getVisitorId(),
            $keys[3] => $this->getFormId(),
            $keys[4] => $this->getStatusId(),
            $keys[5] => $this->getAssigneeId(),
            $keys[6] => $this->getParentId(),
        );
        if ($result[$keys[1]] instanceof \DateTimeInterface) {
            $result[$keys[1]] = $result[$keys[1]]->format('c');
        }

        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aVisitor) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'visitor';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'visitor';
                        break;
                    default:
                        $key = 'Visitor';
                }

                $result[$key] = $this->aVisitor->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aForm) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'form';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'form';
                        break;
                    default:
                        $key = 'Form';
                }

                $result[$key] = $this->aForm->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aStatus) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'status';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'status';
                        break;
                    default:
                        $key = 'Status';
                }

                $result[$key] = $this->aStatus->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aAssignee) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'visitor';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'visitor';
                        break;
                    default:
                        $key = 'Assignee';
                }

                $result[$key] = $this->aAssignee->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aSubmissionRelatedByParentId) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'submission';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'submission';
                        break;
                    default:
                        $key = 'Submission';
                }

                $result[$key] = $this->aSubmissionRelatedByParentId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collResponses) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'responses';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'responses';
                        break;
                    default:
                        $key = 'Responses';
                }

                $result[$key] = $this->collResponses->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collAsParents) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'submissions';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'submissions';
                        break;
                    default:
                        $key = 'AsParents';
                }

                $result[$key] = $this->collAsParents->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collSubmissionTags) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'submissionTags';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'submission_tags';
                        break;
                    default:
                        $key = 'SubmissionTags';
                }

                $result[$key] = $this->collSubmissionTags->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
     * @return $this|\FormsAPI\Submission
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = SubmissionTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\FormsAPI\Submission
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setSubmitted($value);
                break;
            case 2:
                $this->setVisitorId($value);
                break;
            case 3:
                $this->setFormId($value);
                break;
            case 4:
                $this->setStatusId($value);
                break;
            case 5:
                $this->setAssigneeId($value);
                break;
            case 6:
                $this->setParentId($value);
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
        $keys = SubmissionTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setSubmitted($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setVisitorId($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setFormId($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setStatusId($arr[$keys[4]]);
        }
        if (array_key_exists($keys[5], $arr)) {
            $this->setAssigneeId($arr[$keys[5]]);
        }
        if (array_key_exists($keys[6], $arr)) {
            $this->setParentId($arr[$keys[6]]);
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
     * @return $this|\FormsAPI\Submission The current object, for fluid interface
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
        $criteria = new Criteria(SubmissionTableMap::DATABASE_NAME);

        if ($this->isColumnModified(SubmissionTableMap::COL_ID)) {
            $criteria->add(SubmissionTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(SubmissionTableMap::COL_SUBMITTED)) {
            $criteria->add(SubmissionTableMap::COL_SUBMITTED, $this->submitted);
        }
        if ($this->isColumnModified(SubmissionTableMap::COL_VISITOR_ID)) {
            $criteria->add(SubmissionTableMap::COL_VISITOR_ID, $this->visitor_id);
        }
        if ($this->isColumnModified(SubmissionTableMap::COL_FORM_ID)) {
            $criteria->add(SubmissionTableMap::COL_FORM_ID, $this->form_id);
        }
        if ($this->isColumnModified(SubmissionTableMap::COL_STATUS_ID)) {
            $criteria->add(SubmissionTableMap::COL_STATUS_ID, $this->status_id);
        }
        if ($this->isColumnModified(SubmissionTableMap::COL_ASSIGNEE_ID)) {
            $criteria->add(SubmissionTableMap::COL_ASSIGNEE_ID, $this->assignee_id);
        }
        if ($this->isColumnModified(SubmissionTableMap::COL_PARENT_ID)) {
            $criteria->add(SubmissionTableMap::COL_PARENT_ID, $this->parent_id);
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
        $criteria = ChildSubmissionQuery::create();
        $criteria->add(SubmissionTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \FormsAPI\Submission (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setSubmitted($this->getSubmitted());
        $copyObj->setVisitorId($this->getVisitorId());
        $copyObj->setFormId($this->getFormId());
        $copyObj->setStatusId($this->getStatusId());
        $copyObj->setAssigneeId($this->getAssigneeId());
        $copyObj->setParentId($this->getParentId());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getResponses() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addResponse($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getAsParents() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAsParent($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getSubmissionTags() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addSubmissionTag($relObj->copy($deepCopy));
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
     * @return \FormsAPI\Submission Clone of current object.
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
     * Declares an association between this object and a ChildVisitor object.
     *
     * @param  ChildVisitor $v
     * @return $this|\FormsAPI\Submission The current object (for fluent API support)
     * @throws PropelException
     */
    public function setVisitor(ChildVisitor $v = null)
    {
        if ($v === null) {
            $this->setVisitorId(NULL);
        } else {
            $this->setVisitorId($v->getId());
        }

        $this->aVisitor = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildVisitor object, it will not be re-added.
        if ($v !== null) {
            $v->addSubmissionRelatedByVisitorId($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildVisitor object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildVisitor The associated ChildVisitor object.
     * @throws PropelException
     */
    public function getVisitor(ConnectionInterface $con = null)
    {
        if ($this->aVisitor === null && ($this->visitor_id != 0)) {
            $this->aVisitor = ChildVisitorQuery::create()->findPk($this->visitor_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aVisitor->addSubmissionsRelatedByVisitorId($this);
             */
        }

        return $this->aVisitor;
    }

    /**
     * Declares an association between this object and a ChildForm object.
     *
     * @param  ChildForm $v
     * @return $this|\FormsAPI\Submission The current object (for fluent API support)
     * @throws PropelException
     */
    public function setForm(ChildForm $v = null)
    {
        if ($v === null) {
            $this->setFormId(NULL);
        } else {
            $this->setFormId($v->getId());
        }

        $this->aForm = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildForm object, it will not be re-added.
        if ($v !== null) {
            $v->addSubmission($this);
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
    public function getForm(ConnectionInterface $con = null)
    {
        if ($this->aForm === null && ($this->form_id != 0)) {
            $this->aForm = ChildFormQuery::create()->findPk($this->form_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aForm->addSubmissions($this);
             */
        }

        return $this->aForm;
    }

    /**
     * Declares an association between this object and a ChildStatus object.
     *
     * @param  ChildStatus $v
     * @return $this|\FormsAPI\Submission The current object (for fluent API support)
     * @throws PropelException
     */
    public function setStatus(ChildStatus $v = null)
    {
        if ($v === null) {
            $this->setStatusId(NULL);
        } else {
            $this->setStatusId($v->getId());
        }

        $this->aStatus = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildStatus object, it will not be re-added.
        if ($v !== null) {
            $v->addSubmission($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildStatus object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildStatus The associated ChildStatus object.
     * @throws PropelException
     */
    public function getStatus(ConnectionInterface $con = null)
    {
        if ($this->aStatus === null && ($this->status_id != 0)) {
            $this->aStatus = ChildStatusQuery::create()->findPk($this->status_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aStatus->addSubmissions($this);
             */
        }

        return $this->aStatus;
    }

    /**
     * Declares an association between this object and a ChildVisitor object.
     *
     * @param  ChildVisitor $v
     * @return $this|\FormsAPI\Submission The current object (for fluent API support)
     * @throws PropelException
     */
    public function setAssignee(ChildVisitor $v = null)
    {
        if ($v === null) {
            $this->setAssigneeId(NULL);
        } else {
            $this->setAssigneeId($v->getId());
        }

        $this->aAssignee = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildVisitor object, it will not be re-added.
        if ($v !== null) {
            $v->addAsAssignee($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildVisitor object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildVisitor The associated ChildVisitor object.
     * @throws PropelException
     */
    public function getAssignee(ConnectionInterface $con = null)
    {
        if ($this->aAssignee === null && ($this->assignee_id != 0)) {
            $this->aAssignee = ChildVisitorQuery::create()->findPk($this->assignee_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aAssignee->addAsAssignees($this);
             */
        }

        return $this->aAssignee;
    }

    /**
     * Declares an association between this object and a ChildSubmission object.
     *
     * @param  ChildSubmission $v
     * @return $this|\FormsAPI\Submission The current object (for fluent API support)
     * @throws PropelException
     */
    public function setSubmissionRelatedByParentId(ChildSubmission $v = null)
    {
        if ($v === null) {
            $this->setParentId(NULL);
        } else {
            $this->setParentId($v->getId());
        }

        $this->aSubmissionRelatedByParentId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildSubmission object, it will not be re-added.
        if ($v !== null) {
            $v->addAsParent($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildSubmission object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildSubmission The associated ChildSubmission object.
     * @throws PropelException
     */
    public function getSubmissionRelatedByParentId(ConnectionInterface $con = null)
    {
        if ($this->aSubmissionRelatedByParentId === null && ($this->parent_id != 0)) {
            $this->aSubmissionRelatedByParentId = ChildSubmissionQuery::create()->findPk($this->parent_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aSubmissionRelatedByParentId->addAsParents($this);
             */
        }

        return $this->aSubmissionRelatedByParentId;
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
        if ('Response' == $relationName) {
            $this->initResponses();
            return;
        }
        if ('AsParent' == $relationName) {
            $this->initAsParents();
            return;
        }
        if ('SubmissionTag' == $relationName) {
            $this->initSubmissionTags();
            return;
        }
    }

    /**
     * Clears out the collResponses collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addResponses()
     */
    public function clearResponses()
    {
        $this->collResponses = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collResponses collection loaded partially.
     */
    public function resetPartialResponses($v = true)
    {
        $this->collResponsesPartial = $v;
    }

    /**
     * Initializes the collResponses collection.
     *
     * By default this just sets the collResponses collection to an empty array (like clearcollResponses());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initResponses($overrideExisting = true)
    {
        if (null !== $this->collResponses && !$overrideExisting) {
            return;
        }

        $collectionClassName = ResponseTableMap::getTableMap()->getCollectionClassName();

        $this->collResponses = new $collectionClassName;
        $this->collResponses->setModel('\FormsAPI\Response');
    }

    /**
     * Gets an array of ChildResponse objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildSubmission is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildResponse[] List of ChildResponse objects
     * @throws PropelException
     */
    public function getResponses(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collResponsesPartial && !$this->isNew();
        if (null === $this->collResponses || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collResponses) {
                // return empty collection
                $this->initResponses();
            } else {
                $collResponses = ChildResponseQuery::create(null, $criteria)
                    ->filterBySubmission($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collResponsesPartial && count($collResponses)) {
                        $this->initResponses(false);

                        foreach ($collResponses as $obj) {
                            if (false == $this->collResponses->contains($obj)) {
                                $this->collResponses->append($obj);
                            }
                        }

                        $this->collResponsesPartial = true;
                    }

                    return $collResponses;
                }

                if ($partial && $this->collResponses) {
                    foreach ($this->collResponses as $obj) {
                        if ($obj->isNew()) {
                            $collResponses[] = $obj;
                        }
                    }
                }

                $this->collResponses = $collResponses;
                $this->collResponsesPartial = false;
            }
        }

        return $this->collResponses;
    }

    /**
     * Sets a collection of ChildResponse objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $responses A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildSubmission The current object (for fluent API support)
     */
    public function setResponses(Collection $responses, ConnectionInterface $con = null)
    {
        /** @var ChildResponse[] $responsesToDelete */
        $responsesToDelete = $this->getResponses(new Criteria(), $con)->diff($responses);


        $this->responsesScheduledForDeletion = $responsesToDelete;

        foreach ($responsesToDelete as $responseRemoved) {
            $responseRemoved->setSubmission(null);
        }

        $this->collResponses = null;
        foreach ($responses as $response) {
            $this->addResponse($response);
        }

        $this->collResponses = $responses;
        $this->collResponsesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Response objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Response objects.
     * @throws PropelException
     */
    public function countResponses(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collResponsesPartial && !$this->isNew();
        if (null === $this->collResponses || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collResponses) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getResponses());
            }

            $query = ChildResponseQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterBySubmission($this)
                ->count($con);
        }

        return count($this->collResponses);
    }

    /**
     * Method called to associate a ChildResponse object to this object
     * through the ChildResponse foreign key attribute.
     *
     * @param  ChildResponse $l ChildResponse
     * @return $this|\FormsAPI\Submission The current object (for fluent API support)
     */
    public function addResponse(ChildResponse $l)
    {
        if ($this->collResponses === null) {
            $this->initResponses();
            $this->collResponsesPartial = true;
        }

        if (!$this->collResponses->contains($l)) {
            $this->doAddResponse($l);

            if ($this->responsesScheduledForDeletion and $this->responsesScheduledForDeletion->contains($l)) {
                $this->responsesScheduledForDeletion->remove($this->responsesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildResponse $response The ChildResponse object to add.
     */
    protected function doAddResponse(ChildResponse $response)
    {
        $this->collResponses[]= $response;
        $response->setSubmission($this);
    }

    /**
     * @param  ChildResponse $response The ChildResponse object to remove.
     * @return $this|ChildSubmission The current object (for fluent API support)
     */
    public function removeResponse(ChildResponse $response)
    {
        if ($this->getResponses()->contains($response)) {
            $pos = $this->collResponses->search($response);
            $this->collResponses->remove($pos);
            if (null === $this->responsesScheduledForDeletion) {
                $this->responsesScheduledForDeletion = clone $this->collResponses;
                $this->responsesScheduledForDeletion->clear();
            }
            $this->responsesScheduledForDeletion[]= clone $response;
            $response->setSubmission(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Submission is new, it will return
     * an empty collection; or if this Submission has previously
     * been saved, it will retrieve related Responses from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Submission.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildResponse[] List of ChildResponse objects
     */
    public function getResponsesJoinElement(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildResponseQuery::create(null, $criteria);
        $query->joinWith('Element', $joinBehavior);

        return $this->getResponses($query, $con);
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

        $collectionClassName = SubmissionTableMap::getTableMap()->getCollectionClassName();

        $this->collAsParents = new $collectionClassName;
        $this->collAsParents->setModel('\FormsAPI\Submission');
    }

    /**
     * Gets an array of ChildSubmission objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildSubmission is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildSubmission[] List of ChildSubmission objects
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
                $collAsParents = ChildSubmissionQuery::create(null, $criteria)
                    ->filterBySubmissionRelatedByParentId($this)
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
     * Sets a collection of ChildSubmission objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $asParents A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildSubmission The current object (for fluent API support)
     */
    public function setAsParents(Collection $asParents, ConnectionInterface $con = null)
    {
        /** @var ChildSubmission[] $asParentsToDelete */
        $asParentsToDelete = $this->getAsParents(new Criteria(), $con)->diff($asParents);


        $this->asParentsScheduledForDeletion = $asParentsToDelete;

        foreach ($asParentsToDelete as $asParentRemoved) {
            $asParentRemoved->setSubmissionRelatedByParentId(null);
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
     * Returns the number of related Submission objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Submission objects.
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

            $query = ChildSubmissionQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterBySubmissionRelatedByParentId($this)
                ->count($con);
        }

        return count($this->collAsParents);
    }

    /**
     * Method called to associate a ChildSubmission object to this object
     * through the ChildSubmission foreign key attribute.
     *
     * @param  ChildSubmission $l ChildSubmission
     * @return $this|\FormsAPI\Submission The current object (for fluent API support)
     */
    public function addAsParent(ChildSubmission $l)
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
     * @param ChildSubmission $asParent The ChildSubmission object to add.
     */
    protected function doAddAsParent(ChildSubmission $asParent)
    {
        $this->collAsParents[]= $asParent;
        $asParent->setSubmissionRelatedByParentId($this);
    }

    /**
     * @param  ChildSubmission $asParent The ChildSubmission object to remove.
     * @return $this|ChildSubmission The current object (for fluent API support)
     */
    public function removeAsParent(ChildSubmission $asParent)
    {
        if ($this->getAsParents()->contains($asParent)) {
            $pos = $this->collAsParents->search($asParent);
            $this->collAsParents->remove($pos);
            if (null === $this->asParentsScheduledForDeletion) {
                $this->asParentsScheduledForDeletion = clone $this->collAsParents;
                $this->asParentsScheduledForDeletion->clear();
            }
            $this->asParentsScheduledForDeletion[]= $asParent;
            $asParent->setSubmissionRelatedByParentId(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Submission is new, it will return
     * an empty collection; or if this Submission has previously
     * been saved, it will retrieve related AsParents from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Submission.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildSubmission[] List of ChildSubmission objects
     */
    public function getAsParentsJoinVisitor(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildSubmissionQuery::create(null, $criteria);
        $query->joinWith('Visitor', $joinBehavior);

        return $this->getAsParents($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Submission is new, it will return
     * an empty collection; or if this Submission has previously
     * been saved, it will retrieve related AsParents from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Submission.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildSubmission[] List of ChildSubmission objects
     */
    public function getAsParentsJoinForm(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildSubmissionQuery::create(null, $criteria);
        $query->joinWith('Form', $joinBehavior);

        return $this->getAsParents($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Submission is new, it will return
     * an empty collection; or if this Submission has previously
     * been saved, it will retrieve related AsParents from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Submission.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildSubmission[] List of ChildSubmission objects
     */
    public function getAsParentsJoinStatus(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildSubmissionQuery::create(null, $criteria);
        $query->joinWith('Status', $joinBehavior);

        return $this->getAsParents($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Submission is new, it will return
     * an empty collection; or if this Submission has previously
     * been saved, it will retrieve related AsParents from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Submission.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildSubmission[] List of ChildSubmission objects
     */
    public function getAsParentsJoinAssignee(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildSubmissionQuery::create(null, $criteria);
        $query->joinWith('Assignee', $joinBehavior);

        return $this->getAsParents($query, $con);
    }

    /**
     * Clears out the collSubmissionTags collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addSubmissionTags()
     */
    public function clearSubmissionTags()
    {
        $this->collSubmissionTags = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collSubmissionTags collection loaded partially.
     */
    public function resetPartialSubmissionTags($v = true)
    {
        $this->collSubmissionTagsPartial = $v;
    }

    /**
     * Initializes the collSubmissionTags collection.
     *
     * By default this just sets the collSubmissionTags collection to an empty array (like clearcollSubmissionTags());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initSubmissionTags($overrideExisting = true)
    {
        if (null !== $this->collSubmissionTags && !$overrideExisting) {
            return;
        }

        $collectionClassName = SubmissionTagTableMap::getTableMap()->getCollectionClassName();

        $this->collSubmissionTags = new $collectionClassName;
        $this->collSubmissionTags->setModel('\FormsAPI\SubmissionTag');
    }

    /**
     * Gets an array of ChildSubmissionTag objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildSubmission is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildSubmissionTag[] List of ChildSubmissionTag objects
     * @throws PropelException
     */
    public function getSubmissionTags(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collSubmissionTagsPartial && !$this->isNew();
        if (null === $this->collSubmissionTags || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collSubmissionTags) {
                // return empty collection
                $this->initSubmissionTags();
            } else {
                $collSubmissionTags = ChildSubmissionTagQuery::create(null, $criteria)
                    ->filterBySubmission($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collSubmissionTagsPartial && count($collSubmissionTags)) {
                        $this->initSubmissionTags(false);

                        foreach ($collSubmissionTags as $obj) {
                            if (false == $this->collSubmissionTags->contains($obj)) {
                                $this->collSubmissionTags->append($obj);
                            }
                        }

                        $this->collSubmissionTagsPartial = true;
                    }

                    return $collSubmissionTags;
                }

                if ($partial && $this->collSubmissionTags) {
                    foreach ($this->collSubmissionTags as $obj) {
                        if ($obj->isNew()) {
                            $collSubmissionTags[] = $obj;
                        }
                    }
                }

                $this->collSubmissionTags = $collSubmissionTags;
                $this->collSubmissionTagsPartial = false;
            }
        }

        return $this->collSubmissionTags;
    }

    /**
     * Sets a collection of ChildSubmissionTag objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $submissionTags A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildSubmission The current object (for fluent API support)
     */
    public function setSubmissionTags(Collection $submissionTags, ConnectionInterface $con = null)
    {
        /** @var ChildSubmissionTag[] $submissionTagsToDelete */
        $submissionTagsToDelete = $this->getSubmissionTags(new Criteria(), $con)->diff($submissionTags);


        $this->submissionTagsScheduledForDeletion = $submissionTagsToDelete;

        foreach ($submissionTagsToDelete as $submissionTagRemoved) {
            $submissionTagRemoved->setSubmission(null);
        }

        $this->collSubmissionTags = null;
        foreach ($submissionTags as $submissionTag) {
            $this->addSubmissionTag($submissionTag);
        }

        $this->collSubmissionTags = $submissionTags;
        $this->collSubmissionTagsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related SubmissionTag objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related SubmissionTag objects.
     * @throws PropelException
     */
    public function countSubmissionTags(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collSubmissionTagsPartial && !$this->isNew();
        if (null === $this->collSubmissionTags || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collSubmissionTags) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getSubmissionTags());
            }

            $query = ChildSubmissionTagQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterBySubmission($this)
                ->count($con);
        }

        return count($this->collSubmissionTags);
    }

    /**
     * Method called to associate a ChildSubmissionTag object to this object
     * through the ChildSubmissionTag foreign key attribute.
     *
     * @param  ChildSubmissionTag $l ChildSubmissionTag
     * @return $this|\FormsAPI\Submission The current object (for fluent API support)
     */
    public function addSubmissionTag(ChildSubmissionTag $l)
    {
        if ($this->collSubmissionTags === null) {
            $this->initSubmissionTags();
            $this->collSubmissionTagsPartial = true;
        }

        if (!$this->collSubmissionTags->contains($l)) {
            $this->doAddSubmissionTag($l);

            if ($this->submissionTagsScheduledForDeletion and $this->submissionTagsScheduledForDeletion->contains($l)) {
                $this->submissionTagsScheduledForDeletion->remove($this->submissionTagsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildSubmissionTag $submissionTag The ChildSubmissionTag object to add.
     */
    protected function doAddSubmissionTag(ChildSubmissionTag $submissionTag)
    {
        $this->collSubmissionTags[]= $submissionTag;
        $submissionTag->setSubmission($this);
    }

    /**
     * @param  ChildSubmissionTag $submissionTag The ChildSubmissionTag object to remove.
     * @return $this|ChildSubmission The current object (for fluent API support)
     */
    public function removeSubmissionTag(ChildSubmissionTag $submissionTag)
    {
        if ($this->getSubmissionTags()->contains($submissionTag)) {
            $pos = $this->collSubmissionTags->search($submissionTag);
            $this->collSubmissionTags->remove($pos);
            if (null === $this->submissionTagsScheduledForDeletion) {
                $this->submissionTagsScheduledForDeletion = clone $this->collSubmissionTags;
                $this->submissionTagsScheduledForDeletion->clear();
            }
            $this->submissionTagsScheduledForDeletion[]= clone $submissionTag;
            $submissionTag->setSubmission(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Submission is new, it will return
     * an empty collection; or if this Submission has previously
     * been saved, it will retrieve related SubmissionTags from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Submission.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildSubmissionTag[] List of ChildSubmissionTag objects
     */
    public function getSubmissionTagsJoinTag(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildSubmissionTagQuery::create(null, $criteria);
        $query->joinWith('Tag', $joinBehavior);

        return $this->getSubmissionTags($query, $con);
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        if (null !== $this->aVisitor) {
            $this->aVisitor->removeSubmissionRelatedByVisitorId($this);
        }
        if (null !== $this->aForm) {
            $this->aForm->removeSubmission($this);
        }
        if (null !== $this->aStatus) {
            $this->aStatus->removeSubmission($this);
        }
        if (null !== $this->aAssignee) {
            $this->aAssignee->removeAsAssignee($this);
        }
        if (null !== $this->aSubmissionRelatedByParentId) {
            $this->aSubmissionRelatedByParentId->removeAsParent($this);
        }
        $this->id = null;
        $this->submitted = null;
        $this->visitor_id = null;
        $this->form_id = null;
        $this->status_id = null;
        $this->assignee_id = null;
        $this->parent_id = null;
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
            if ($this->collResponses) {
                foreach ($this->collResponses as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collAsParents) {
                foreach ($this->collAsParents as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collSubmissionTags) {
                foreach ($this->collSubmissionTags as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collResponses = null;
        $this->collAsParents = null;
        $this->collSubmissionTags = null;
        $this->aVisitor = null;
        $this->aForm = null;
        $this->aStatus = null;
        $this->aAssignee = null;
        $this->aSubmissionRelatedByParentId = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(SubmissionTableMap::DEFAULT_STRING_FORMAT);
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
        $metadata->addPropertyConstraint('form_id', new NotNull());
        $metadata->addPropertyConstraint('visitor_id', new NotNull());
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
            if (method_exists($this->aVisitor, 'validate')) {
                if (!$this->aVisitor->validate($validator)) {
                    $failureMap->addAll($this->aVisitor->getValidationFailures());
                }
            }
            // If validate() method exists, the validate-behavior is configured for related object
            if (method_exists($this->aForm, 'validate')) {
                if (!$this->aForm->validate($validator)) {
                    $failureMap->addAll($this->aForm->getValidationFailures());
                }
            }
            // If validate() method exists, the validate-behavior is configured for related object
            if (method_exists($this->aStatus, 'validate')) {
                if (!$this->aStatus->validate($validator)) {
                    $failureMap->addAll($this->aStatus->getValidationFailures());
                }
            }
            // If validate() method exists, the validate-behavior is configured for related object
            if (method_exists($this->aAssignee, 'validate')) {
                if (!$this->aAssignee->validate($validator)) {
                    $failureMap->addAll($this->aAssignee->getValidationFailures());
                }
            }
            // If validate() method exists, the validate-behavior is configured for related object
            if (method_exists($this->aSubmissionRelatedByParentId, 'validate')) {
                if (!$this->aSubmissionRelatedByParentId->validate($validator)) {
                    $failureMap->addAll($this->aSubmissionRelatedByParentId->getValidationFailures());
                }
            }

            $retval = $validator->validate($this);
            if (count($retval) > 0) {
                $failureMap->addAll($retval);
            }

            if (null !== $this->collResponses) {
                foreach ($this->collResponses as $referrerFK) {
                    if (method_exists($referrerFK, 'validate')) {
                        if (!$referrerFK->validate($validator)) {
                            $failureMap->addAll($referrerFK->getValidationFailures());
                        }
                    }
                }
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
            if (null !== $this->collSubmissionTags) {
                foreach ($this->collSubmissionTags as $referrerFK) {
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
