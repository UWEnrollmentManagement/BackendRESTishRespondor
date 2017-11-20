<?php

namespace FormsAPI\Base;

use \Exception;
use \PDO;
use FormsAPI\DashboardElement as ChildDashboardElement;
use FormsAPI\DashboardElementQuery as ChildDashboardElementQuery;
use FormsAPI\Dependency as ChildDependency;
use FormsAPI\DependencyQuery as ChildDependencyQuery;
use FormsAPI\Element as ChildElement;
use FormsAPI\ElementChoice as ChildElementChoice;
use FormsAPI\ElementChoiceQuery as ChildElementChoiceQuery;
use FormsAPI\ElementQuery as ChildElementQuery;
use FormsAPI\Form as ChildForm;
use FormsAPI\FormQuery as ChildFormQuery;
use FormsAPI\Requirement as ChildRequirement;
use FormsAPI\RequirementQuery as ChildRequirementQuery;
use FormsAPI\Response as ChildResponse;
use FormsAPI\ResponseQuery as ChildResponseQuery;
use FormsAPI\Map\DashboardElementTableMap;
use FormsAPI\Map\DependencyTableMap;
use FormsAPI\Map\ElementChoiceTableMap;
use FormsAPI\Map\ElementTableMap;
use FormsAPI\Map\FormTableMap;
use FormsAPI\Map\RequirementTableMap;
use FormsAPI\Map\ResponseTableMap;
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
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Context\ExecutionContextFactory;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Base class that represents a row from the 'element' table.
 *
 *
 *
 * @package    propel.generator.FormsAPI.Base
 */
abstract class Element implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\FormsAPI\\Map\\ElementTableMap';


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
     * The value for the retired field.
     *
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $retired;

    /**
     * The value for the type field.
     *
     * @var        string
     */
    protected $type;

    /**
     * The value for the label field.
     *
     * @var        string
     */
    protected $label;

    /**
     * The value for the initial_value field.
     *
     * @var        string
     */
    protected $initial_value;

    /**
     * The value for the help_text field.
     *
     * @var        string
     */
    protected $help_text;

    /**
     * The value for the placeholder_text field.
     *
     * @var        string
     */
    protected $placeholder_text;

    /**
     * The value for the required field.
     *
     * Note: this column has a database default value of: true
     * @var        boolean
     */
    protected $required;

    /**
     * The value for the parent_id field.
     *
     * @var        int
     */
    protected $parent_id;

    /**
     * @var        ChildElement
     */
    protected $aElementRelatedByParentId;

    /**
     * @var        ObjectCollection|ChildElement[] Collection to store aggregation of ChildElement objects.
     */
    protected $collParents;
    protected $collParentsPartial;

    /**
     * @var        ObjectCollection|ChildResponse[] Collection to store aggregation of ChildResponse objects.
     */
    protected $collResponses;
    protected $collResponsesPartial;

    /**
     * @var        ObjectCollection|ChildForm[] Collection to store aggregation of ChildForm objects.
     */
    protected $collRootElements;
    protected $collRootElementsPartial;

    /**
     * @var        ObjectCollection|ChildDependency[] Collection to store aggregation of ChildDependency objects.
     */
    protected $collAsMasters;
    protected $collAsMastersPartial;

    /**
     * @var        ObjectCollection|ChildDependency[] Collection to store aggregation of ChildDependency objects.
     */
    protected $collAsSlaves;
    protected $collAsSlavesPartial;

    /**
     * @var        ObjectCollection|ChildRequirement[] Collection to store aggregation of ChildRequirement objects.
     */
    protected $collRequirements;
    protected $collRequirementsPartial;

    /**
     * @var        ObjectCollection|ChildElementChoice[] Collection to store aggregation of ChildElementChoice objects.
     */
    protected $collElementChoices;
    protected $collElementChoicesPartial;

    /**
     * @var        ObjectCollection|ChildDashboardElement[] Collection to store aggregation of ChildDashboardElement objects.
     */
    protected $collDashboardElements;
    protected $collDashboardElementsPartial;

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
     * @var ObjectCollection|ChildElement[]
     */
    protected $parentsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildResponse[]
     */
    protected $responsesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildForm[]
     */
    protected $rootElementsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildDependency[]
     */
    protected $asMastersScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildDependency[]
     */
    protected $asSlavesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildRequirement[]
     */
    protected $requirementsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildElementChoice[]
     */
    protected $elementChoicesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildDashboardElement[]
     */
    protected $dashboardElementsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->retired = false;
        $this->required = true;
    }

    /**
     * Initializes internal state of FormsAPI\Base\Element object.
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
     * Compares this with another <code>Element</code> instance.  If
     * <code>obj</code> is an instance of <code>Element</code>, delegates to
     * <code>equals(Element)</code>.  Otherwise, returns <code>false</code>.
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
     * @return $this|Element The current object, for fluid interface
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
     * Get the [type] column value.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the [label] column value.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Get the [initial_value] column value.
     *
     * @return string
     */
    public function getInitialValue()
    {
        return $this->initial_value;
    }

    /**
     * Get the [help_text] column value.
     *
     * @return string
     */
    public function getHelpText()
    {
        return $this->help_text;
    }

    /**
     * Get the [placeholder_text] column value.
     *
     * @return string
     */
    public function getPlaceholderText()
    {
        return $this->placeholder_text;
    }

    /**
     * Get the [required] column value.
     *
     * @return boolean
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Get the [required] column value.
     *
     * @return boolean
     */
    public function isRequired()
    {
        return $this->getRequired();
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
     * @return $this|\FormsAPI\Element The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[ElementTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Sets the value of the [retired] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param  boolean|integer|string $v The new value
     * @return $this|\FormsAPI\Element The current object (for fluent API support)
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
            $this->modifiedColumns[ElementTableMap::COL_RETIRED] = true;
        }

        return $this;
    } // setRetired()

    /**
     * Set the value of [type] column.
     *
     * @param string $v new value
     * @return $this|\FormsAPI\Element The current object (for fluent API support)
     */
    public function setType($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->type !== $v) {
            $this->type = $v;
            $this->modifiedColumns[ElementTableMap::COL_TYPE] = true;
        }

        return $this;
    } // setType()

    /**
     * Set the value of [label] column.
     *
     * @param string $v new value
     * @return $this|\FormsAPI\Element The current object (for fluent API support)
     */
    public function setLabel($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->label !== $v) {
            $this->label = $v;
            $this->modifiedColumns[ElementTableMap::COL_LABEL] = true;
        }

        return $this;
    } // setLabel()

    /**
     * Set the value of [initial_value] column.
     *
     * @param string $v new value
     * @return $this|\FormsAPI\Element The current object (for fluent API support)
     */
    public function setInitialValue($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->initial_value !== $v) {
            $this->initial_value = $v;
            $this->modifiedColumns[ElementTableMap::COL_INITIAL_VALUE] = true;
        }

        return $this;
    } // setInitialValue()

    /**
     * Set the value of [help_text] column.
     *
     * @param string $v new value
     * @return $this|\FormsAPI\Element The current object (for fluent API support)
     */
    public function setHelpText($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->help_text !== $v) {
            $this->help_text = $v;
            $this->modifiedColumns[ElementTableMap::COL_HELP_TEXT] = true;
        }

        return $this;
    } // setHelpText()

    /**
     * Set the value of [placeholder_text] column.
     *
     * @param string $v new value
     * @return $this|\FormsAPI\Element The current object (for fluent API support)
     */
    public function setPlaceholderText($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->placeholder_text !== $v) {
            $this->placeholder_text = $v;
            $this->modifiedColumns[ElementTableMap::COL_PLACEHOLDER_TEXT] = true;
        }

        return $this;
    } // setPlaceholderText()

    /**
     * Sets the value of the [required] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param  boolean|integer|string $v The new value
     * @return $this|\FormsAPI\Element The current object (for fluent API support)
     */
    public function setRequired($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->required !== $v) {
            $this->required = $v;
            $this->modifiedColumns[ElementTableMap::COL_REQUIRED] = true;
        }

        return $this;
    } // setRequired()

    /**
     * Set the value of [parent_id] column.
     *
     * @param int $v new value
     * @return $this|\FormsAPI\Element The current object (for fluent API support)
     */
    public function setParentId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->parent_id !== $v) {
            $this->parent_id = $v;
            $this->modifiedColumns[ElementTableMap::COL_PARENT_ID] = true;
        }

        if ($this->aElementRelatedByParentId !== null && $this->aElementRelatedByParentId->getId() !== $v) {
            $this->aElementRelatedByParentId = null;
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
            if ($this->retired !== false) {
                return false;
            }

            if ($this->required !== true) {
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

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : ElementTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : ElementTableMap::translateFieldName('Retired', TableMap::TYPE_PHPNAME, $indexType)];
            $this->retired = (null !== $col) ? (boolean) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : ElementTableMap::translateFieldName('Type', TableMap::TYPE_PHPNAME, $indexType)];
            $this->type = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : ElementTableMap::translateFieldName('Label', TableMap::TYPE_PHPNAME, $indexType)];
            $this->label = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : ElementTableMap::translateFieldName('InitialValue', TableMap::TYPE_PHPNAME, $indexType)];
            $this->initial_value = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : ElementTableMap::translateFieldName('HelpText', TableMap::TYPE_PHPNAME, $indexType)];
            $this->help_text = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : ElementTableMap::translateFieldName('PlaceholderText', TableMap::TYPE_PHPNAME, $indexType)];
            $this->placeholder_text = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : ElementTableMap::translateFieldName('Required', TableMap::TYPE_PHPNAME, $indexType)];
            $this->required = (null !== $col) ? (boolean) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : ElementTableMap::translateFieldName('ParentId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->parent_id = (null !== $col) ? (int) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 9; // 9 = ElementTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\FormsAPI\\Element'), 0, $e);
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
        if ($this->aElementRelatedByParentId !== null && $this->parent_id !== $this->aElementRelatedByParentId->getId()) {
            $this->aElementRelatedByParentId = null;
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
            $con = Propel::getServiceContainer()->getReadConnection(ElementTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildElementQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aElementRelatedByParentId = null;
            $this->collParents = null;

            $this->collResponses = null;

            $this->collRootElements = null;

            $this->collAsMasters = null;

            $this->collAsSlaves = null;

            $this->collRequirements = null;

            $this->collElementChoices = null;

            $this->collDashboardElements = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Element::setDeleted()
     * @see Element::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(ElementTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildElementQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(ElementTableMap::DATABASE_NAME);
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
                ElementTableMap::addInstanceToPool($this);
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

            if ($this->aElementRelatedByParentId !== null) {
                if ($this->aElementRelatedByParentId->isModified() || $this->aElementRelatedByParentId->isNew()) {
                    $affectedRows += $this->aElementRelatedByParentId->save($con);
                }
                $this->setElementRelatedByParentId($this->aElementRelatedByParentId);
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

            if ($this->parentsScheduledForDeletion !== null) {
                if (!$this->parentsScheduledForDeletion->isEmpty()) {
                    foreach ($this->parentsScheduledForDeletion as $parent) {
                        // need to save related object because we set the relation to null
                        $parent->save($con);
                    }
                    $this->parentsScheduledForDeletion = null;
                }
            }

            if ($this->collParents !== null) {
                foreach ($this->collParents as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
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

            if ($this->rootElementsScheduledForDeletion !== null) {
                if (!$this->rootElementsScheduledForDeletion->isEmpty()) {
                    foreach ($this->rootElementsScheduledForDeletion as $rootElement) {
                        // need to save related object because we set the relation to null
                        $rootElement->save($con);
                    }
                    $this->rootElementsScheduledForDeletion = null;
                }
            }

            if ($this->collRootElements !== null) {
                foreach ($this->collRootElements as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->asMastersScheduledForDeletion !== null) {
                if (!$this->asMastersScheduledForDeletion->isEmpty()) {
                    \FormsAPI\DependencyQuery::create()
                        ->filterByPrimaryKeys($this->asMastersScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->asMastersScheduledForDeletion = null;
                }
            }

            if ($this->collAsMasters !== null) {
                foreach ($this->collAsMasters as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->asSlavesScheduledForDeletion !== null) {
                if (!$this->asSlavesScheduledForDeletion->isEmpty()) {
                    \FormsAPI\DependencyQuery::create()
                        ->filterByPrimaryKeys($this->asSlavesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->asSlavesScheduledForDeletion = null;
                }
            }

            if ($this->collAsSlaves !== null) {
                foreach ($this->collAsSlaves as $referrerFK) {
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

            if ($this->elementChoicesScheduledForDeletion !== null) {
                if (!$this->elementChoicesScheduledForDeletion->isEmpty()) {
                    \FormsAPI\ElementChoiceQuery::create()
                        ->filterByPrimaryKeys($this->elementChoicesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->elementChoicesScheduledForDeletion = null;
                }
            }

            if ($this->collElementChoices !== null) {
                foreach ($this->collElementChoices as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->dashboardElementsScheduledForDeletion !== null) {
                if (!$this->dashboardElementsScheduledForDeletion->isEmpty()) {
                    \FormsAPI\DashboardElementQuery::create()
                        ->filterByPrimaryKeys($this->dashboardElementsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->dashboardElementsScheduledForDeletion = null;
                }
            }

            if ($this->collDashboardElements !== null) {
                foreach ($this->collDashboardElements as $referrerFK) {
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

        $this->modifiedColumns[ElementTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . ElementTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ElementTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(ElementTableMap::COL_RETIRED)) {
            $modifiedColumns[':p' . $index++]  = 'retired';
        }
        if ($this->isColumnModified(ElementTableMap::COL_TYPE)) {
            $modifiedColumns[':p' . $index++]  = 'type';
        }
        if ($this->isColumnModified(ElementTableMap::COL_LABEL)) {
            $modifiedColumns[':p' . $index++]  = 'label';
        }
        if ($this->isColumnModified(ElementTableMap::COL_INITIAL_VALUE)) {
            $modifiedColumns[':p' . $index++]  = 'initial_value';
        }
        if ($this->isColumnModified(ElementTableMap::COL_HELP_TEXT)) {
            $modifiedColumns[':p' . $index++]  = 'help_text';
        }
        if ($this->isColumnModified(ElementTableMap::COL_PLACEHOLDER_TEXT)) {
            $modifiedColumns[':p' . $index++]  = 'placeholder_text';
        }
        if ($this->isColumnModified(ElementTableMap::COL_REQUIRED)) {
            $modifiedColumns[':p' . $index++]  = 'required';
        }
        if ($this->isColumnModified(ElementTableMap::COL_PARENT_ID)) {
            $modifiedColumns[':p' . $index++]  = 'parent_id';
        }

        $sql = sprintf(
            'INSERT INTO element (%s) VALUES (%s)',
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
                    case 'retired':
                        $stmt->bindValue($identifier, $this->retired, PDO::PARAM_BOOL);
                        break;
                    case 'type':
                        $stmt->bindValue($identifier, $this->type, PDO::PARAM_STR);
                        break;
                    case 'label':
                        $stmt->bindValue($identifier, $this->label, PDO::PARAM_STR);
                        break;
                    case 'initial_value':
                        $stmt->bindValue($identifier, $this->initial_value, PDO::PARAM_STR);
                        break;
                    case 'help_text':
                        $stmt->bindValue($identifier, $this->help_text, PDO::PARAM_STR);
                        break;
                    case 'placeholder_text':
                        $stmt->bindValue($identifier, $this->placeholder_text, PDO::PARAM_STR);
                        break;
                    case 'required':
                        $stmt->bindValue($identifier, $this->required, PDO::PARAM_BOOL);
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
        $pos = ElementTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getRetired();
                break;
            case 2:
                return $this->getType();
                break;
            case 3:
                return $this->getLabel();
                break;
            case 4:
                return $this->getInitialValue();
                break;
            case 5:
                return $this->getHelpText();
                break;
            case 6:
                return $this->getPlaceholderText();
                break;
            case 7:
                return $this->getRequired();
                break;
            case 8:
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

        if (isset($alreadyDumpedObjects['Element'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Element'][$this->hashCode()] = true;
        $keys = ElementTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getRetired(),
            $keys[2] => $this->getType(),
            $keys[3] => $this->getLabel(),
            $keys[4] => $this->getInitialValue(),
            $keys[5] => $this->getHelpText(),
            $keys[6] => $this->getPlaceholderText(),
            $keys[7] => $this->getRequired(),
            $keys[8] => $this->getParentId(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aElementRelatedByParentId) {

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

                $result[$key] = $this->aElementRelatedByParentId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collParents) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'elements';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'elements';
                        break;
                    default:
                        $key = 'Parents';
                }

                $result[$key] = $this->collParents->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
            if (null !== $this->collRootElements) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'forms';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'forms';
                        break;
                    default:
                        $key = 'RootElements';
                }

                $result[$key] = $this->collRootElements->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collAsMasters) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'dependencies';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'dependencies';
                        break;
                    default:
                        $key = 'AsMasters';
                }

                $result[$key] = $this->collAsMasters->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collAsSlaves) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'dependencies';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'dependencies';
                        break;
                    default:
                        $key = 'AsSlaves';
                }

                $result[$key] = $this->collAsSlaves->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
            if (null !== $this->collElementChoices) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'elementChoices';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'element_choices';
                        break;
                    default:
                        $key = 'ElementChoices';
                }

                $result[$key] = $this->collElementChoices->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collDashboardElements) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'dashboardElements';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'dashboard_elements';
                        break;
                    default:
                        $key = 'DashboardElements';
                }

                $result[$key] = $this->collDashboardElements->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
     * @return $this|\FormsAPI\Element
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = ElementTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\FormsAPI\Element
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setRetired($value);
                break;
            case 2:
                $this->setType($value);
                break;
            case 3:
                $this->setLabel($value);
                break;
            case 4:
                $this->setInitialValue($value);
                break;
            case 5:
                $this->setHelpText($value);
                break;
            case 6:
                $this->setPlaceholderText($value);
                break;
            case 7:
                $this->setRequired($value);
                break;
            case 8:
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
        $keys = ElementTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setRetired($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setType($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setLabel($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setInitialValue($arr[$keys[4]]);
        }
        if (array_key_exists($keys[5], $arr)) {
            $this->setHelpText($arr[$keys[5]]);
        }
        if (array_key_exists($keys[6], $arr)) {
            $this->setPlaceholderText($arr[$keys[6]]);
        }
        if (array_key_exists($keys[7], $arr)) {
            $this->setRequired($arr[$keys[7]]);
        }
        if (array_key_exists($keys[8], $arr)) {
            $this->setParentId($arr[$keys[8]]);
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
     * @return $this|\FormsAPI\Element The current object, for fluid interface
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
        $criteria = new Criteria(ElementTableMap::DATABASE_NAME);

        if ($this->isColumnModified(ElementTableMap::COL_ID)) {
            $criteria->add(ElementTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(ElementTableMap::COL_RETIRED)) {
            $criteria->add(ElementTableMap::COL_RETIRED, $this->retired);
        }
        if ($this->isColumnModified(ElementTableMap::COL_TYPE)) {
            $criteria->add(ElementTableMap::COL_TYPE, $this->type);
        }
        if ($this->isColumnModified(ElementTableMap::COL_LABEL)) {
            $criteria->add(ElementTableMap::COL_LABEL, $this->label);
        }
        if ($this->isColumnModified(ElementTableMap::COL_INITIAL_VALUE)) {
            $criteria->add(ElementTableMap::COL_INITIAL_VALUE, $this->initial_value);
        }
        if ($this->isColumnModified(ElementTableMap::COL_HELP_TEXT)) {
            $criteria->add(ElementTableMap::COL_HELP_TEXT, $this->help_text);
        }
        if ($this->isColumnModified(ElementTableMap::COL_PLACEHOLDER_TEXT)) {
            $criteria->add(ElementTableMap::COL_PLACEHOLDER_TEXT, $this->placeholder_text);
        }
        if ($this->isColumnModified(ElementTableMap::COL_REQUIRED)) {
            $criteria->add(ElementTableMap::COL_REQUIRED, $this->required);
        }
        if ($this->isColumnModified(ElementTableMap::COL_PARENT_ID)) {
            $criteria->add(ElementTableMap::COL_PARENT_ID, $this->parent_id);
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
        $criteria = ChildElementQuery::create();
        $criteria->add(ElementTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \FormsAPI\Element (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setRetired($this->getRetired());
        $copyObj->setType($this->getType());
        $copyObj->setLabel($this->getLabel());
        $copyObj->setInitialValue($this->getInitialValue());
        $copyObj->setHelpText($this->getHelpText());
        $copyObj->setPlaceholderText($this->getPlaceholderText());
        $copyObj->setRequired($this->getRequired());
        $copyObj->setParentId($this->getParentId());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getParents() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addParent($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getResponses() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addResponse($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getRootElements() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addRootElement($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getAsMasters() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAsMaster($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getAsSlaves() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAsSlave($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getRequirements() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addRequirement($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getElementChoices() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addElementChoice($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getDashboardElements() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDashboardElement($relObj->copy($deepCopy));
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
     * @return \FormsAPI\Element Clone of current object.
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
     * @return $this|\FormsAPI\Element The current object (for fluent API support)
     * @throws PropelException
     */
    public function setElementRelatedByParentId(ChildElement $v = null)
    {
        if ($v === null) {
            $this->setParentId(NULL);
        } else {
            $this->setParentId($v->getId());
        }

        $this->aElementRelatedByParentId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildElement object, it will not be re-added.
        if ($v !== null) {
            $v->addParent($this);
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
    public function getElementRelatedByParentId(ConnectionInterface $con = null)
    {
        if ($this->aElementRelatedByParentId === null && ($this->parent_id != 0)) {
            $this->aElementRelatedByParentId = ChildElementQuery::create()->findPk($this->parent_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aElementRelatedByParentId->addParents($this);
             */
        }

        return $this->aElementRelatedByParentId;
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
        if ('Parent' == $relationName) {
            $this->initParents();
            return;
        }
        if ('Response' == $relationName) {
            $this->initResponses();
            return;
        }
        if ('RootElement' == $relationName) {
            $this->initRootElements();
            return;
        }
        if ('AsMaster' == $relationName) {
            $this->initAsMasters();
            return;
        }
        if ('AsSlave' == $relationName) {
            $this->initAsSlaves();
            return;
        }
        if ('Requirement' == $relationName) {
            $this->initRequirements();
            return;
        }
        if ('ElementChoice' == $relationName) {
            $this->initElementChoices();
            return;
        }
        if ('DashboardElement' == $relationName) {
            $this->initDashboardElements();
            return;
        }
    }

    /**
     * Clears out the collParents collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addParents()
     */
    public function clearParents()
    {
        $this->collParents = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collParents collection loaded partially.
     */
    public function resetPartialParents($v = true)
    {
        $this->collParentsPartial = $v;
    }

    /**
     * Initializes the collParents collection.
     *
     * By default this just sets the collParents collection to an empty array (like clearcollParents());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initParents($overrideExisting = true)
    {
        if (null !== $this->collParents && !$overrideExisting) {
            return;
        }

        $collectionClassName = ElementTableMap::getTableMap()->getCollectionClassName();

        $this->collParents = new $collectionClassName;
        $this->collParents->setModel('\FormsAPI\Element');
    }

    /**
     * Gets an array of ChildElement objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildElement is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildElement[] List of ChildElement objects
     * @throws PropelException
     */
    public function getParents(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collParentsPartial && !$this->isNew();
        if (null === $this->collParents || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collParents) {
                // return empty collection
                $this->initParents();
            } else {
                $collParents = ChildElementQuery::create(null, $criteria)
                    ->filterByElementRelatedByParentId($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collParentsPartial && count($collParents)) {
                        $this->initParents(false);

                        foreach ($collParents as $obj) {
                            if (false == $this->collParents->contains($obj)) {
                                $this->collParents->append($obj);
                            }
                        }

                        $this->collParentsPartial = true;
                    }

                    return $collParents;
                }

                if ($partial && $this->collParents) {
                    foreach ($this->collParents as $obj) {
                        if ($obj->isNew()) {
                            $collParents[] = $obj;
                        }
                    }
                }

                $this->collParents = $collParents;
                $this->collParentsPartial = false;
            }
        }

        return $this->collParents;
    }

    /**
     * Sets a collection of ChildElement objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $parents A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildElement The current object (for fluent API support)
     */
    public function setParents(Collection $parents, ConnectionInterface $con = null)
    {
        /** @var ChildElement[] $parentsToDelete */
        $parentsToDelete = $this->getParents(new Criteria(), $con)->diff($parents);


        $this->parentsScheduledForDeletion = $parentsToDelete;

        foreach ($parentsToDelete as $parentRemoved) {
            $parentRemoved->setElementRelatedByParentId(null);
        }

        $this->collParents = null;
        foreach ($parents as $parent) {
            $this->addParent($parent);
        }

        $this->collParents = $parents;
        $this->collParentsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Element objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Element objects.
     * @throws PropelException
     */
    public function countParents(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collParentsPartial && !$this->isNew();
        if (null === $this->collParents || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collParents) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getParents());
            }

            $query = ChildElementQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByElementRelatedByParentId($this)
                ->count($con);
        }

        return count($this->collParents);
    }

    /**
     * Method called to associate a ChildElement object to this object
     * through the ChildElement foreign key attribute.
     *
     * @param  ChildElement $l ChildElement
     * @return $this|\FormsAPI\Element The current object (for fluent API support)
     */
    public function addParent(ChildElement $l)
    {
        if ($this->collParents === null) {
            $this->initParents();
            $this->collParentsPartial = true;
        }

        if (!$this->collParents->contains($l)) {
            $this->doAddParent($l);

            if ($this->parentsScheduledForDeletion and $this->parentsScheduledForDeletion->contains($l)) {
                $this->parentsScheduledForDeletion->remove($this->parentsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildElement $parent The ChildElement object to add.
     */
    protected function doAddParent(ChildElement $parent)
    {
        $this->collParents[]= $parent;
        $parent->setElementRelatedByParentId($this);
    }

    /**
     * @param  ChildElement $parent The ChildElement object to remove.
     * @return $this|ChildElement The current object (for fluent API support)
     */
    public function removeParent(ChildElement $parent)
    {
        if ($this->getParents()->contains($parent)) {
            $pos = $this->collParents->search($parent);
            $this->collParents->remove($pos);
            if (null === $this->parentsScheduledForDeletion) {
                $this->parentsScheduledForDeletion = clone $this->collParents;
                $this->parentsScheduledForDeletion->clear();
            }
            $this->parentsScheduledForDeletion[]= $parent;
            $parent->setElementRelatedByParentId(null);
        }

        return $this;
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
     * If this ChildElement is new, it will return
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
                    ->filterByElement($this)
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
     * @return $this|ChildElement The current object (for fluent API support)
     */
    public function setResponses(Collection $responses, ConnectionInterface $con = null)
    {
        /** @var ChildResponse[] $responsesToDelete */
        $responsesToDelete = $this->getResponses(new Criteria(), $con)->diff($responses);


        $this->responsesScheduledForDeletion = $responsesToDelete;

        foreach ($responsesToDelete as $responseRemoved) {
            $responseRemoved->setElement(null);
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
                ->filterByElement($this)
                ->count($con);
        }

        return count($this->collResponses);
    }

    /**
     * Method called to associate a ChildResponse object to this object
     * through the ChildResponse foreign key attribute.
     *
     * @param  ChildResponse $l ChildResponse
     * @return $this|\FormsAPI\Element The current object (for fluent API support)
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
        $response->setElement($this);
    }

    /**
     * @param  ChildResponse $response The ChildResponse object to remove.
     * @return $this|ChildElement The current object (for fluent API support)
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
            $response->setElement(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Element is new, it will return
     * an empty collection; or if this Element has previously
     * been saved, it will retrieve related Responses from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Element.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildResponse[] List of ChildResponse objects
     */
    public function getResponsesJoinSubmission(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildResponseQuery::create(null, $criteria);
        $query->joinWith('Submission', $joinBehavior);

        return $this->getResponses($query, $con);
    }

    /**
     * Clears out the collRootElements collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addRootElements()
     */
    public function clearRootElements()
    {
        $this->collRootElements = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collRootElements collection loaded partially.
     */
    public function resetPartialRootElements($v = true)
    {
        $this->collRootElementsPartial = $v;
    }

    /**
     * Initializes the collRootElements collection.
     *
     * By default this just sets the collRootElements collection to an empty array (like clearcollRootElements());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initRootElements($overrideExisting = true)
    {
        if (null !== $this->collRootElements && !$overrideExisting) {
            return;
        }

        $collectionClassName = FormTableMap::getTableMap()->getCollectionClassName();

        $this->collRootElements = new $collectionClassName;
        $this->collRootElements->setModel('\FormsAPI\Form');
    }

    /**
     * Gets an array of ChildForm objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildElement is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildForm[] List of ChildForm objects
     * @throws PropelException
     */
    public function getRootElements(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collRootElementsPartial && !$this->isNew();
        if (null === $this->collRootElements || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collRootElements) {
                // return empty collection
                $this->initRootElements();
            } else {
                $collRootElements = ChildFormQuery::create(null, $criteria)
                    ->filterByElement($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collRootElementsPartial && count($collRootElements)) {
                        $this->initRootElements(false);

                        foreach ($collRootElements as $obj) {
                            if (false == $this->collRootElements->contains($obj)) {
                                $this->collRootElements->append($obj);
                            }
                        }

                        $this->collRootElementsPartial = true;
                    }

                    return $collRootElements;
                }

                if ($partial && $this->collRootElements) {
                    foreach ($this->collRootElements as $obj) {
                        if ($obj->isNew()) {
                            $collRootElements[] = $obj;
                        }
                    }
                }

                $this->collRootElements = $collRootElements;
                $this->collRootElementsPartial = false;
            }
        }

        return $this->collRootElements;
    }

    /**
     * Sets a collection of ChildForm objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $rootElements A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildElement The current object (for fluent API support)
     */
    public function setRootElements(Collection $rootElements, ConnectionInterface $con = null)
    {
        /** @var ChildForm[] $rootElementsToDelete */
        $rootElementsToDelete = $this->getRootElements(new Criteria(), $con)->diff($rootElements);


        $this->rootElementsScheduledForDeletion = $rootElementsToDelete;

        foreach ($rootElementsToDelete as $rootElementRemoved) {
            $rootElementRemoved->setElement(null);
        }

        $this->collRootElements = null;
        foreach ($rootElements as $rootElement) {
            $this->addRootElement($rootElement);
        }

        $this->collRootElements = $rootElements;
        $this->collRootElementsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Form objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Form objects.
     * @throws PropelException
     */
    public function countRootElements(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collRootElementsPartial && !$this->isNew();
        if (null === $this->collRootElements || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collRootElements) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getRootElements());
            }

            $query = ChildFormQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByElement($this)
                ->count($con);
        }

        return count($this->collRootElements);
    }

    /**
     * Method called to associate a ChildForm object to this object
     * through the ChildForm foreign key attribute.
     *
     * @param  ChildForm $l ChildForm
     * @return $this|\FormsAPI\Element The current object (for fluent API support)
     */
    public function addRootElement(ChildForm $l)
    {
        if ($this->collRootElements === null) {
            $this->initRootElements();
            $this->collRootElementsPartial = true;
        }

        if (!$this->collRootElements->contains($l)) {
            $this->doAddRootElement($l);

            if ($this->rootElementsScheduledForDeletion and $this->rootElementsScheduledForDeletion->contains($l)) {
                $this->rootElementsScheduledForDeletion->remove($this->rootElementsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildForm $rootElement The ChildForm object to add.
     */
    protected function doAddRootElement(ChildForm $rootElement)
    {
        $this->collRootElements[]= $rootElement;
        $rootElement->setElement($this);
    }

    /**
     * @param  ChildForm $rootElement The ChildForm object to remove.
     * @return $this|ChildElement The current object (for fluent API support)
     */
    public function removeRootElement(ChildForm $rootElement)
    {
        if ($this->getRootElements()->contains($rootElement)) {
            $pos = $this->collRootElements->search($rootElement);
            $this->collRootElements->remove($pos);
            if (null === $this->rootElementsScheduledForDeletion) {
                $this->rootElementsScheduledForDeletion = clone $this->collRootElements;
                $this->rootElementsScheduledForDeletion->clear();
            }
            $this->rootElementsScheduledForDeletion[]= $rootElement;
            $rootElement->setElement(null);
        }

        return $this;
    }

    /**
     * Clears out the collAsMasters collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addAsMasters()
     */
    public function clearAsMasters()
    {
        $this->collAsMasters = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collAsMasters collection loaded partially.
     */
    public function resetPartialAsMasters($v = true)
    {
        $this->collAsMastersPartial = $v;
    }

    /**
     * Initializes the collAsMasters collection.
     *
     * By default this just sets the collAsMasters collection to an empty array (like clearcollAsMasters());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initAsMasters($overrideExisting = true)
    {
        if (null !== $this->collAsMasters && !$overrideExisting) {
            return;
        }

        $collectionClassName = DependencyTableMap::getTableMap()->getCollectionClassName();

        $this->collAsMasters = new $collectionClassName;
        $this->collAsMasters->setModel('\FormsAPI\Dependency');
    }

    /**
     * Gets an array of ChildDependency objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildElement is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildDependency[] List of ChildDependency objects
     * @throws PropelException
     */
    public function getAsMasters(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collAsMastersPartial && !$this->isNew();
        if (null === $this->collAsMasters || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collAsMasters) {
                // return empty collection
                $this->initAsMasters();
            } else {
                $collAsMasters = ChildDependencyQuery::create(null, $criteria)
                    ->filterByElement($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collAsMastersPartial && count($collAsMasters)) {
                        $this->initAsMasters(false);

                        foreach ($collAsMasters as $obj) {
                            if (false == $this->collAsMasters->contains($obj)) {
                                $this->collAsMasters->append($obj);
                            }
                        }

                        $this->collAsMastersPartial = true;
                    }

                    return $collAsMasters;
                }

                if ($partial && $this->collAsMasters) {
                    foreach ($this->collAsMasters as $obj) {
                        if ($obj->isNew()) {
                            $collAsMasters[] = $obj;
                        }
                    }
                }

                $this->collAsMasters = $collAsMasters;
                $this->collAsMastersPartial = false;
            }
        }

        return $this->collAsMasters;
    }

    /**
     * Sets a collection of ChildDependency objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $asMasters A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildElement The current object (for fluent API support)
     */
    public function setAsMasters(Collection $asMasters, ConnectionInterface $con = null)
    {
        /** @var ChildDependency[] $asMastersToDelete */
        $asMastersToDelete = $this->getAsMasters(new Criteria(), $con)->diff($asMasters);


        $this->asMastersScheduledForDeletion = $asMastersToDelete;

        foreach ($asMastersToDelete as $asMasterRemoved) {
            $asMasterRemoved->setElement(null);
        }

        $this->collAsMasters = null;
        foreach ($asMasters as $asMaster) {
            $this->addAsMaster($asMaster);
        }

        $this->collAsMasters = $asMasters;
        $this->collAsMastersPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Dependency objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Dependency objects.
     * @throws PropelException
     */
    public function countAsMasters(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collAsMastersPartial && !$this->isNew();
        if (null === $this->collAsMasters || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAsMasters) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getAsMasters());
            }

            $query = ChildDependencyQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByElement($this)
                ->count($con);
        }

        return count($this->collAsMasters);
    }

    /**
     * Method called to associate a ChildDependency object to this object
     * through the ChildDependency foreign key attribute.
     *
     * @param  ChildDependency $l ChildDependency
     * @return $this|\FormsAPI\Element The current object (for fluent API support)
     */
    public function addAsMaster(ChildDependency $l)
    {
        if ($this->collAsMasters === null) {
            $this->initAsMasters();
            $this->collAsMastersPartial = true;
        }

        if (!$this->collAsMasters->contains($l)) {
            $this->doAddAsMaster($l);

            if ($this->asMastersScheduledForDeletion and $this->asMastersScheduledForDeletion->contains($l)) {
                $this->asMastersScheduledForDeletion->remove($this->asMastersScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildDependency $asMaster The ChildDependency object to add.
     */
    protected function doAddAsMaster(ChildDependency $asMaster)
    {
        $this->collAsMasters[]= $asMaster;
        $asMaster->setElement($this);
    }

    /**
     * @param  ChildDependency $asMaster The ChildDependency object to remove.
     * @return $this|ChildElement The current object (for fluent API support)
     */
    public function removeAsMaster(ChildDependency $asMaster)
    {
        if ($this->getAsMasters()->contains($asMaster)) {
            $pos = $this->collAsMasters->search($asMaster);
            $this->collAsMasters->remove($pos);
            if (null === $this->asMastersScheduledForDeletion) {
                $this->asMastersScheduledForDeletion = clone $this->collAsMasters;
                $this->asMastersScheduledForDeletion->clear();
            }
            $this->asMastersScheduledForDeletion[]= clone $asMaster;
            $asMaster->setElement(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Element is new, it will return
     * an empty collection; or if this Element has previously
     * been saved, it will retrieve related AsMasters from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Element.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildDependency[] List of ChildDependency objects
     */
    public function getAsMastersJoinCondition(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildDependencyQuery::create(null, $criteria);
        $query->joinWith('Condition', $joinBehavior);

        return $this->getAsMasters($query, $con);
    }

    /**
     * Clears out the collAsSlaves collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addAsSlaves()
     */
    public function clearAsSlaves()
    {
        $this->collAsSlaves = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collAsSlaves collection loaded partially.
     */
    public function resetPartialAsSlaves($v = true)
    {
        $this->collAsSlavesPartial = $v;
    }

    /**
     * Initializes the collAsSlaves collection.
     *
     * By default this just sets the collAsSlaves collection to an empty array (like clearcollAsSlaves());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initAsSlaves($overrideExisting = true)
    {
        if (null !== $this->collAsSlaves && !$overrideExisting) {
            return;
        }

        $collectionClassName = DependencyTableMap::getTableMap()->getCollectionClassName();

        $this->collAsSlaves = new $collectionClassName;
        $this->collAsSlaves->setModel('\FormsAPI\Dependency');
    }

    /**
     * Gets an array of ChildDependency objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildElement is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildDependency[] List of ChildDependency objects
     * @throws PropelException
     */
    public function getAsSlaves(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collAsSlavesPartial && !$this->isNew();
        if (null === $this->collAsSlaves || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collAsSlaves) {
                // return empty collection
                $this->initAsSlaves();
            } else {
                $collAsSlaves = ChildDependencyQuery::create(null, $criteria)
                    ->filterBySlave($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collAsSlavesPartial && count($collAsSlaves)) {
                        $this->initAsSlaves(false);

                        foreach ($collAsSlaves as $obj) {
                            if (false == $this->collAsSlaves->contains($obj)) {
                                $this->collAsSlaves->append($obj);
                            }
                        }

                        $this->collAsSlavesPartial = true;
                    }

                    return $collAsSlaves;
                }

                if ($partial && $this->collAsSlaves) {
                    foreach ($this->collAsSlaves as $obj) {
                        if ($obj->isNew()) {
                            $collAsSlaves[] = $obj;
                        }
                    }
                }

                $this->collAsSlaves = $collAsSlaves;
                $this->collAsSlavesPartial = false;
            }
        }

        return $this->collAsSlaves;
    }

    /**
     * Sets a collection of ChildDependency objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $asSlaves A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildElement The current object (for fluent API support)
     */
    public function setAsSlaves(Collection $asSlaves, ConnectionInterface $con = null)
    {
        /** @var ChildDependency[] $asSlavesToDelete */
        $asSlavesToDelete = $this->getAsSlaves(new Criteria(), $con)->diff($asSlaves);


        $this->asSlavesScheduledForDeletion = $asSlavesToDelete;

        foreach ($asSlavesToDelete as $asSlaveRemoved) {
            $asSlaveRemoved->setSlave(null);
        }

        $this->collAsSlaves = null;
        foreach ($asSlaves as $asSlave) {
            $this->addAsSlave($asSlave);
        }

        $this->collAsSlaves = $asSlaves;
        $this->collAsSlavesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Dependency objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Dependency objects.
     * @throws PropelException
     */
    public function countAsSlaves(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collAsSlavesPartial && !$this->isNew();
        if (null === $this->collAsSlaves || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAsSlaves) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getAsSlaves());
            }

            $query = ChildDependencyQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterBySlave($this)
                ->count($con);
        }

        return count($this->collAsSlaves);
    }

    /**
     * Method called to associate a ChildDependency object to this object
     * through the ChildDependency foreign key attribute.
     *
     * @param  ChildDependency $l ChildDependency
     * @return $this|\FormsAPI\Element The current object (for fluent API support)
     */
    public function addAsSlave(ChildDependency $l)
    {
        if ($this->collAsSlaves === null) {
            $this->initAsSlaves();
            $this->collAsSlavesPartial = true;
        }

        if (!$this->collAsSlaves->contains($l)) {
            $this->doAddAsSlave($l);

            if ($this->asSlavesScheduledForDeletion and $this->asSlavesScheduledForDeletion->contains($l)) {
                $this->asSlavesScheduledForDeletion->remove($this->asSlavesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildDependency $asSlave The ChildDependency object to add.
     */
    protected function doAddAsSlave(ChildDependency $asSlave)
    {
        $this->collAsSlaves[]= $asSlave;
        $asSlave->setSlave($this);
    }

    /**
     * @param  ChildDependency $asSlave The ChildDependency object to remove.
     * @return $this|ChildElement The current object (for fluent API support)
     */
    public function removeAsSlave(ChildDependency $asSlave)
    {
        if ($this->getAsSlaves()->contains($asSlave)) {
            $pos = $this->collAsSlaves->search($asSlave);
            $this->collAsSlaves->remove($pos);
            if (null === $this->asSlavesScheduledForDeletion) {
                $this->asSlavesScheduledForDeletion = clone $this->collAsSlaves;
                $this->asSlavesScheduledForDeletion->clear();
            }
            $this->asSlavesScheduledForDeletion[]= clone $asSlave;
            $asSlave->setSlave(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Element is new, it will return
     * an empty collection; or if this Element has previously
     * been saved, it will retrieve related AsSlaves from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Element.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildDependency[] List of ChildDependency objects
     */
    public function getAsSlavesJoinCondition(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildDependencyQuery::create(null, $criteria);
        $query->joinWith('Condition', $joinBehavior);

        return $this->getAsSlaves($query, $con);
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
     * If this ChildElement is new, it will return
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
                    ->filterByElement($this)
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
     * @return $this|ChildElement The current object (for fluent API support)
     */
    public function setRequirements(Collection $requirements, ConnectionInterface $con = null)
    {
        /** @var ChildRequirement[] $requirementsToDelete */
        $requirementsToDelete = $this->getRequirements(new Criteria(), $con)->diff($requirements);


        $this->requirementsScheduledForDeletion = $requirementsToDelete;

        foreach ($requirementsToDelete as $requirementRemoved) {
            $requirementRemoved->setElement(null);
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
                ->filterByElement($this)
                ->count($con);
        }

        return count($this->collRequirements);
    }

    /**
     * Method called to associate a ChildRequirement object to this object
     * through the ChildRequirement foreign key attribute.
     *
     * @param  ChildRequirement $l ChildRequirement
     * @return $this|\FormsAPI\Element The current object (for fluent API support)
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
        $requirement->setElement($this);
    }

    /**
     * @param  ChildRequirement $requirement The ChildRequirement object to remove.
     * @return $this|ChildElement The current object (for fluent API support)
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
            $requirement->setElement(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Element is new, it will return
     * an empty collection; or if this Element has previously
     * been saved, it will retrieve related Requirements from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Element.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildRequirement[] List of ChildRequirement objects
     */
    public function getRequirementsJoinCondition(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildRequirementQuery::create(null, $criteria);
        $query->joinWith('Condition', $joinBehavior);

        return $this->getRequirements($query, $con);
    }

    /**
     * Clears out the collElementChoices collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addElementChoices()
     */
    public function clearElementChoices()
    {
        $this->collElementChoices = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collElementChoices collection loaded partially.
     */
    public function resetPartialElementChoices($v = true)
    {
        $this->collElementChoicesPartial = $v;
    }

    /**
     * Initializes the collElementChoices collection.
     *
     * By default this just sets the collElementChoices collection to an empty array (like clearcollElementChoices());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initElementChoices($overrideExisting = true)
    {
        if (null !== $this->collElementChoices && !$overrideExisting) {
            return;
        }

        $collectionClassName = ElementChoiceTableMap::getTableMap()->getCollectionClassName();

        $this->collElementChoices = new $collectionClassName;
        $this->collElementChoices->setModel('\FormsAPI\ElementChoice');
    }

    /**
     * Gets an array of ChildElementChoice objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildElement is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildElementChoice[] List of ChildElementChoice objects
     * @throws PropelException
     */
    public function getElementChoices(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collElementChoicesPartial && !$this->isNew();
        if (null === $this->collElementChoices || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collElementChoices) {
                // return empty collection
                $this->initElementChoices();
            } else {
                $collElementChoices = ChildElementChoiceQuery::create(null, $criteria)
                    ->filterByElement($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collElementChoicesPartial && count($collElementChoices)) {
                        $this->initElementChoices(false);

                        foreach ($collElementChoices as $obj) {
                            if (false == $this->collElementChoices->contains($obj)) {
                                $this->collElementChoices->append($obj);
                            }
                        }

                        $this->collElementChoicesPartial = true;
                    }

                    return $collElementChoices;
                }

                if ($partial && $this->collElementChoices) {
                    foreach ($this->collElementChoices as $obj) {
                        if ($obj->isNew()) {
                            $collElementChoices[] = $obj;
                        }
                    }
                }

                $this->collElementChoices = $collElementChoices;
                $this->collElementChoicesPartial = false;
            }
        }

        return $this->collElementChoices;
    }

    /**
     * Sets a collection of ChildElementChoice objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $elementChoices A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildElement The current object (for fluent API support)
     */
    public function setElementChoices(Collection $elementChoices, ConnectionInterface $con = null)
    {
        /** @var ChildElementChoice[] $elementChoicesToDelete */
        $elementChoicesToDelete = $this->getElementChoices(new Criteria(), $con)->diff($elementChoices);


        $this->elementChoicesScheduledForDeletion = $elementChoicesToDelete;

        foreach ($elementChoicesToDelete as $elementChoiceRemoved) {
            $elementChoiceRemoved->setElement(null);
        }

        $this->collElementChoices = null;
        foreach ($elementChoices as $elementChoice) {
            $this->addElementChoice($elementChoice);
        }

        $this->collElementChoices = $elementChoices;
        $this->collElementChoicesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ElementChoice objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ElementChoice objects.
     * @throws PropelException
     */
    public function countElementChoices(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collElementChoicesPartial && !$this->isNew();
        if (null === $this->collElementChoices || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collElementChoices) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getElementChoices());
            }

            $query = ChildElementChoiceQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByElement($this)
                ->count($con);
        }

        return count($this->collElementChoices);
    }

    /**
     * Method called to associate a ChildElementChoice object to this object
     * through the ChildElementChoice foreign key attribute.
     *
     * @param  ChildElementChoice $l ChildElementChoice
     * @return $this|\FormsAPI\Element The current object (for fluent API support)
     */
    public function addElementChoice(ChildElementChoice $l)
    {
        if ($this->collElementChoices === null) {
            $this->initElementChoices();
            $this->collElementChoicesPartial = true;
        }

        if (!$this->collElementChoices->contains($l)) {
            $this->doAddElementChoice($l);

            if ($this->elementChoicesScheduledForDeletion and $this->elementChoicesScheduledForDeletion->contains($l)) {
                $this->elementChoicesScheduledForDeletion->remove($this->elementChoicesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildElementChoice $elementChoice The ChildElementChoice object to add.
     */
    protected function doAddElementChoice(ChildElementChoice $elementChoice)
    {
        $this->collElementChoices[]= $elementChoice;
        $elementChoice->setElement($this);
    }

    /**
     * @param  ChildElementChoice $elementChoice The ChildElementChoice object to remove.
     * @return $this|ChildElement The current object (for fluent API support)
     */
    public function removeElementChoice(ChildElementChoice $elementChoice)
    {
        if ($this->getElementChoices()->contains($elementChoice)) {
            $pos = $this->collElementChoices->search($elementChoice);
            $this->collElementChoices->remove($pos);
            if (null === $this->elementChoicesScheduledForDeletion) {
                $this->elementChoicesScheduledForDeletion = clone $this->collElementChoices;
                $this->elementChoicesScheduledForDeletion->clear();
            }
            $this->elementChoicesScheduledForDeletion[]= clone $elementChoice;
            $elementChoice->setElement(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Element is new, it will return
     * an empty collection; or if this Element has previously
     * been saved, it will retrieve related ElementChoices from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Element.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildElementChoice[] List of ChildElementChoice objects
     */
    public function getElementChoicesJoinChoiceValue(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildElementChoiceQuery::create(null, $criteria);
        $query->joinWith('ChoiceValue', $joinBehavior);

        return $this->getElementChoices($query, $con);
    }

    /**
     * Clears out the collDashboardElements collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDashboardElements()
     */
    public function clearDashboardElements()
    {
        $this->collDashboardElements = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDashboardElements collection loaded partially.
     */
    public function resetPartialDashboardElements($v = true)
    {
        $this->collDashboardElementsPartial = $v;
    }

    /**
     * Initializes the collDashboardElements collection.
     *
     * By default this just sets the collDashboardElements collection to an empty array (like clearcollDashboardElements());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDashboardElements($overrideExisting = true)
    {
        if (null !== $this->collDashboardElements && !$overrideExisting) {
            return;
        }

        $collectionClassName = DashboardElementTableMap::getTableMap()->getCollectionClassName();

        $this->collDashboardElements = new $collectionClassName;
        $this->collDashboardElements->setModel('\FormsAPI\DashboardElement');
    }

    /**
     * Gets an array of ChildDashboardElement objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildElement is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildDashboardElement[] List of ChildDashboardElement objects
     * @throws PropelException
     */
    public function getDashboardElements(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDashboardElementsPartial && !$this->isNew();
        if (null === $this->collDashboardElements || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDashboardElements) {
                // return empty collection
                $this->initDashboardElements();
            } else {
                $collDashboardElements = ChildDashboardElementQuery::create(null, $criteria)
                    ->filterByElement($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDashboardElementsPartial && count($collDashboardElements)) {
                        $this->initDashboardElements(false);

                        foreach ($collDashboardElements as $obj) {
                            if (false == $this->collDashboardElements->contains($obj)) {
                                $this->collDashboardElements->append($obj);
                            }
                        }

                        $this->collDashboardElementsPartial = true;
                    }

                    return $collDashboardElements;
                }

                if ($partial && $this->collDashboardElements) {
                    foreach ($this->collDashboardElements as $obj) {
                        if ($obj->isNew()) {
                            $collDashboardElements[] = $obj;
                        }
                    }
                }

                $this->collDashboardElements = $collDashboardElements;
                $this->collDashboardElementsPartial = false;
            }
        }

        return $this->collDashboardElements;
    }

    /**
     * Sets a collection of ChildDashboardElement objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $dashboardElements A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildElement The current object (for fluent API support)
     */
    public function setDashboardElements(Collection $dashboardElements, ConnectionInterface $con = null)
    {
        /** @var ChildDashboardElement[] $dashboardElementsToDelete */
        $dashboardElementsToDelete = $this->getDashboardElements(new Criteria(), $con)->diff($dashboardElements);


        $this->dashboardElementsScheduledForDeletion = $dashboardElementsToDelete;

        foreach ($dashboardElementsToDelete as $dashboardElementRemoved) {
            $dashboardElementRemoved->setElement(null);
        }

        $this->collDashboardElements = null;
        foreach ($dashboardElements as $dashboardElement) {
            $this->addDashboardElement($dashboardElement);
        }

        $this->collDashboardElements = $dashboardElements;
        $this->collDashboardElementsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DashboardElement objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DashboardElement objects.
     * @throws PropelException
     */
    public function countDashboardElements(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDashboardElementsPartial && !$this->isNew();
        if (null === $this->collDashboardElements || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDashboardElements) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDashboardElements());
            }

            $query = ChildDashboardElementQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByElement($this)
                ->count($con);
        }

        return count($this->collDashboardElements);
    }

    /**
     * Method called to associate a ChildDashboardElement object to this object
     * through the ChildDashboardElement foreign key attribute.
     *
     * @param  ChildDashboardElement $l ChildDashboardElement
     * @return $this|\FormsAPI\Element The current object (for fluent API support)
     */
    public function addDashboardElement(ChildDashboardElement $l)
    {
        if ($this->collDashboardElements === null) {
            $this->initDashboardElements();
            $this->collDashboardElementsPartial = true;
        }

        if (!$this->collDashboardElements->contains($l)) {
            $this->doAddDashboardElement($l);

            if ($this->dashboardElementsScheduledForDeletion and $this->dashboardElementsScheduledForDeletion->contains($l)) {
                $this->dashboardElementsScheduledForDeletion->remove($this->dashboardElementsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildDashboardElement $dashboardElement The ChildDashboardElement object to add.
     */
    protected function doAddDashboardElement(ChildDashboardElement $dashboardElement)
    {
        $this->collDashboardElements[]= $dashboardElement;
        $dashboardElement->setElement($this);
    }

    /**
     * @param  ChildDashboardElement $dashboardElement The ChildDashboardElement object to remove.
     * @return $this|ChildElement The current object (for fluent API support)
     */
    public function removeDashboardElement(ChildDashboardElement $dashboardElement)
    {
        if ($this->getDashboardElements()->contains($dashboardElement)) {
            $pos = $this->collDashboardElements->search($dashboardElement);
            $this->collDashboardElements->remove($pos);
            if (null === $this->dashboardElementsScheduledForDeletion) {
                $this->dashboardElementsScheduledForDeletion = clone $this->collDashboardElements;
                $this->dashboardElementsScheduledForDeletion->clear();
            }
            $this->dashboardElementsScheduledForDeletion[]= clone $dashboardElement;
            $dashboardElement->setElement(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Element is new, it will return
     * an empty collection; or if this Element has previously
     * been saved, it will retrieve related DashboardElements from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Element.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildDashboardElement[] List of ChildDashboardElement objects
     */
    public function getDashboardElementsJoinDashboard(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildDashboardElementQuery::create(null, $criteria);
        $query->joinWith('Dashboard', $joinBehavior);

        return $this->getDashboardElements($query, $con);
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        if (null !== $this->aElementRelatedByParentId) {
            $this->aElementRelatedByParentId->removeParent($this);
        }
        $this->id = null;
        $this->retired = null;
        $this->type = null;
        $this->label = null;
        $this->initial_value = null;
        $this->help_text = null;
        $this->placeholder_text = null;
        $this->required = null;
        $this->parent_id = null;
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
            if ($this->collParents) {
                foreach ($this->collParents as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collResponses) {
                foreach ($this->collResponses as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collRootElements) {
                foreach ($this->collRootElements as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collAsMasters) {
                foreach ($this->collAsMasters as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collAsSlaves) {
                foreach ($this->collAsSlaves as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collRequirements) {
                foreach ($this->collRequirements as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collElementChoices) {
                foreach ($this->collElementChoices as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collDashboardElements) {
                foreach ($this->collDashboardElements as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collParents = null;
        $this->collResponses = null;
        $this->collRootElements = null;
        $this->collAsMasters = null;
        $this->collAsSlaves = null;
        $this->collRequirements = null;
        $this->collElementChoices = null;
        $this->collDashboardElements = null;
        $this->aElementRelatedByParentId = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(ElementTableMap::DEFAULT_STRING_FORMAT);
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
        $metadata->addPropertyConstraint('type', new Choice(array ('choices' => array (0 => 'section-label',1 => 'information',2 => 'affirmation',3 => 'date',4 => 'text-field',5 => 'big-text-field',6 => 'choice-field',7 => 'secure-upload',8 => 'secure-upload-multiple',9 => 'choices-from-file',),)));
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
            if (method_exists($this->aElementRelatedByParentId, 'validate')) {
                if (!$this->aElementRelatedByParentId->validate($validator)) {
                    $failureMap->addAll($this->aElementRelatedByParentId->getValidationFailures());
                }
            }

            $retval = $validator->validate($this);
            if (count($retval) > 0) {
                $failureMap->addAll($retval);
            }

            if (null !== $this->collParents) {
                foreach ($this->collParents as $referrerFK) {
                    if (method_exists($referrerFK, 'validate')) {
                        if (!$referrerFK->validate($validator)) {
                            $failureMap->addAll($referrerFK->getValidationFailures());
                        }
                    }
                }
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
            if (null !== $this->collRootElements) {
                foreach ($this->collRootElements as $referrerFK) {
                    if (method_exists($referrerFK, 'validate')) {
                        if (!$referrerFK->validate($validator)) {
                            $failureMap->addAll($referrerFK->getValidationFailures());
                        }
                    }
                }
            }
            if (null !== $this->collAsMasters) {
                foreach ($this->collAsMasters as $referrerFK) {
                    if (method_exists($referrerFK, 'validate')) {
                        if (!$referrerFK->validate($validator)) {
                            $failureMap->addAll($referrerFK->getValidationFailures());
                        }
                    }
                }
            }
            if (null !== $this->collAsSlaves) {
                foreach ($this->collAsSlaves as $referrerFK) {
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
            if (null !== $this->collElementChoices) {
                foreach ($this->collElementChoices as $referrerFK) {
                    if (method_exists($referrerFK, 'validate')) {
                        if (!$referrerFK->validate($validator)) {
                            $failureMap->addAll($referrerFK->getValidationFailures());
                        }
                    }
                }
            }
            if (null !== $this->collDashboardElements) {
                foreach ($this->collDashboardElements as $referrerFK) {
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
