<?php

namespace Acme\LibraryBundle\Model\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \DateTime;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelDateTime;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Acme\LibraryBundle\Model\Book;
use Acme\LibraryBundle\Model\BookClubList;
use Acme\LibraryBundle\Model\BookClubListPeer;
use Acme\LibraryBundle\Model\BookClubListQuery;
use Acme\LibraryBundle\Model\BookListRel;
use Acme\LibraryBundle\Model\BookListRelQuery;
use Acme\LibraryBundle\Model\BookQuery;

abstract class BaseBookClubList extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Acme\\LibraryBundle\\Model\\BookClubListPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        BookClubListPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the group_leader field.
     * @var        string
     */
    protected $group_leader;

    /**
     * The value for the theme field.
     * @var        string
     */
    protected $theme;

    /**
     * The value for the created_at field.
     * @var        string
     */
    protected $created_at;

    /**
     * @var        PropelObjectCollection|BookListRel[] Collection to store aggregation of BookListRel objects.
     */
    protected $collBookListRels;
    protected $collBookListRelsPartial;

    /**
     * @var        PropelObjectCollection|Book[] Collection to store aggregation of Book objects.
     */
    protected $collBooks;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * Flag to prevent endless clearAllReferences($deep=true) loop, if this object is referenced
     * @var        boolean
     */
    protected $alreadyInClearAllReferencesDeep = false;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $booksScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $bookListRelsScheduledForDeletion = null;

    /**
     * Get the [id] column value.
     * Unique ID for a school reading list.
     * @return int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [group_leader] column value.
     * The name of the teacher in charge of summer reading.
     * @return string
     */
    public function getGroupLeader()
    {

        return $this->group_leader;
    }

    /**
     * Get the [theme] column value.
     * The theme, if applicable, for the reading list.
     * @return string
     */
    public function getTheme()
    {

        return $this->theme;
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null, and 0 if column value is 0000-00-00 00:00:00
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = null)
    {
        if ($this->created_at === null) {
            return null;
        }

        if ($this->created_at === '0000-00-00 00:00:00') {
            // while technically this is not a default value of null,
            // this seems to be closest in meaning.
            return null;
        }

        try {
            $dt = new DateTime($this->created_at);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->created_at, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Set the value of [id] column.
     * Unique ID for a school reading list.
     * @param  int $v new value
     * @return BookClubList The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = BookClubListPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [group_leader] column.
     * The name of the teacher in charge of summer reading.
     * @param  string $v new value
     * @return BookClubList The current object (for fluent API support)
     */
    public function setGroupLeader($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->group_leader !== $v) {
            $this->group_leader = $v;
            $this->modifiedColumns[] = BookClubListPeer::GROUP_LEADER;
        }


        return $this;
    } // setGroupLeader()

    /**
     * Set the value of [theme] column.
     * The theme, if applicable, for the reading list.
     * @param  string $v new value
     * @return BookClubList The current object (for fluent API support)
     */
    public function setTheme($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->theme !== $v) {
            $this->theme = $v;
            $this->modifiedColumns[] = BookClubListPeer::THEME;
        }


        return $this;
    } // setTheme()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return BookClubList The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            $currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->created_at = $newDateAsString;
                $this->modifiedColumns[] = BookClubListPeer::CREATED_AT;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

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
        // otherwise, everything was equal, so return true
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
     * @param array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
     * @param int $startcol 0-based offset column which indicates which resultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        try {

            $this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->group_leader = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->theme = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->created_at = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 4; // 4 = BookClubListPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating BookClubList object", $e);
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

    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param boolean $deep (optional) Whether to also de-associated any related objects.
     * @param PropelPDO $con (optional) The PropelPDO connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getConnection(BookClubListPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = BookClubListPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collBookListRels = null;

            $this->collBooks = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param PropelPDO $con
     * @return void
     * @throws PropelException
     * @throws Exception
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(BookClubListPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = BookClubListQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @throws Exception
     * @see        doSave()
     */
    public function save(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(BookClubListPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
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
                BookClubListPeer::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see        save()
     */
    protected function doSave(PropelPDO $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->booksScheduledForDeletion !== null) {
                if (!$this->booksScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->booksScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($remotePk, $pk);
                    }
                    BookListRelQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->booksScheduledForDeletion = null;
                }

                foreach ($this->getBooks() as $book) {
                    if ($book->isModified()) {
                        $book->save($con);
                    }
                }
            } elseif ($this->collBooks) {
                foreach ($this->collBooks as $book) {
                    if ($book->isModified()) {
                        $book->save($con);
                    }
                }
            }

            if ($this->bookListRelsScheduledForDeletion !== null) {
                if (!$this->bookListRelsScheduledForDeletion->isEmpty()) {
                    BookListRelQuery::create()
                        ->filterByPrimaryKeys($this->bookListRelsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->bookListRelsScheduledForDeletion = null;
                }
            }

            if ($this->collBookListRels !== null) {
                foreach ($this->collBookListRels as $referrerFK) {
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
     * @param PropelPDO $con
     *
     * @throws PropelException
     * @see        doSave()
     */
    protected function doInsert(PropelPDO $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[] = BookClubListPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . BookClubListPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(BookClubListPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(BookClubListPeer::GROUP_LEADER)) {
            $modifiedColumns[':p' . $index++]  = '`group_leader`';
        }
        if ($this->isColumnModified(BookClubListPeer::THEME)) {
            $modifiedColumns[':p' . $index++]  = '`theme`';
        }
        if ($this->isColumnModified(BookClubListPeer::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`created_at`';
        }

        $sql = sprintf(
            'INSERT INTO `book_club_list` (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '`id`':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case '`group_leader`':
                        $stmt->bindValue($identifier, $this->group_leader, PDO::PARAM_STR);
                        break;
                    case '`theme`':
                        $stmt->bindValue($identifier, $this->theme, PDO::PARAM_STR);
                        break;
                    case '`created_at`':
                        $stmt->bindValue($identifier, $this->created_at, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param PropelPDO $con
     *
     * @see        doSave()
     */
    protected function doUpdate(PropelPDO $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();
        BasePeer::doUpdate($selectCriteria, $valuesCriteria, $con);
    }

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param mixed $columns Column name or an array of column names.
     * @return boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();

            return true;
        }

        $this->validationFailures = $res;

        return false;
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggregated array of ValidationFailed objects will be returned.
     *
     * @param array $columns Array of column names to validate.
     * @return mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objects otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            if (($retval = BookClubListPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collBookListRels !== null) {
                    foreach ($this->collBookListRels as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }


            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param string $name name
     * @param string $type The type of fieldname the $name is of:
     *               one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *               BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *               Defaults to BasePeer::TYPE_PHPNAME
     * @return mixed Value of field.
     */
    public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = BookClubListPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getGroupLeader();
                break;
            case 2:
                return $this->getTheme();
                break;
            case 3:
                return $this->getCreatedAt();
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
     * @param     string  $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     *                    BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                    Defaults to BasePeer::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to true.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['BookClubList'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['BookClubList'][$this->getPrimaryKey()] = true;
        $keys = BookClubListPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getGroupLeader(),
            $keys[2] => $this->getTheme(),
            $keys[3] => $this->getCreatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collBookListRels) {
                $result['BookListRels'] = $this->collBookListRels->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param string $name peer name
     * @param mixed $value field value
     * @param string $type The type of fieldname the $name is of:
     *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                     Defaults to BasePeer::TYPE_PHPNAME
     * @return void
     */
    public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = BookClubListPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

        $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @param mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setGroupLeader($value);
                break;
            case 2:
                $this->setTheme($value);
                break;
            case 3:
                $this->setCreatedAt($value);
                break;
        } // switch()
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
     * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     * The default key type is the column's BasePeer::TYPE_PHPNAME
     *
     * @param array  $arr     An array to populate the object from.
     * @param string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = BookClubListPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setGroupLeader($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setTheme($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setCreatedAt($arr[$keys[3]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(BookClubListPeer::DATABASE_NAME);

        if ($this->isColumnModified(BookClubListPeer::ID)) $criteria->add(BookClubListPeer::ID, $this->id);
        if ($this->isColumnModified(BookClubListPeer::GROUP_LEADER)) $criteria->add(BookClubListPeer::GROUP_LEADER, $this->group_leader);
        if ($this->isColumnModified(BookClubListPeer::THEME)) $criteria->add(BookClubListPeer::THEME, $this->theme);
        if ($this->isColumnModified(BookClubListPeer::CREATED_AT)) $criteria->add(BookClubListPeer::CREATED_AT, $this->created_at);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(BookClubListPeer::DATABASE_NAME);
        $criteria->add(BookClubListPeer::ID, $this->id);

        return $criteria;
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
     * @param  int $key Primary key.
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
     * @param object $copyObj An object of BookClubList (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setGroupLeader($this->getGroupLeader());
        $copyObj->setTheme($this->getTheme());
        $copyObj->setCreatedAt($this->getCreatedAt());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getBookListRels() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addBookListRel($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
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
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return BookClubList Clone of current object.
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
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return BookClubListPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new BookClubListPeer();
        }

        return self::$peer;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('BookListRel' == $relationName) {
            $this->initBookListRels();
        }
    }

    /**
     * Clears out the collBookListRels collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return BookClubList The current object (for fluent API support)
     * @see        addBookListRels()
     */
    public function clearBookListRels()
    {
        $this->collBookListRels = null; // important to set this to null since that means it is uninitialized
        $this->collBookListRelsPartial = null;

        return $this;
    }

    /**
     * reset is the collBookListRels collection loaded partially
     *
     * @return void
     */
    public function resetPartialBookListRels($v = true)
    {
        $this->collBookListRelsPartial = $v;
    }

    /**
     * Initializes the collBookListRels collection.
     *
     * By default this just sets the collBookListRels collection to an empty array (like clearcollBookListRels());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initBookListRels($overrideExisting = true)
    {
        if (null !== $this->collBookListRels && !$overrideExisting) {
            return;
        }
        $this->collBookListRels = new PropelObjectCollection();
        $this->collBookListRels->setModel('BookListRel');
    }

    /**
     * Gets an array of BookListRel objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this BookClubList is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|BookListRel[] List of BookListRel objects
     * @throws PropelException
     */
    public function getBookListRels($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collBookListRelsPartial && !$this->isNew();
        if (null === $this->collBookListRels || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collBookListRels) {
                // return empty collection
                $this->initBookListRels();
            } else {
                $collBookListRels = BookListRelQuery::create(null, $criteria)
                    ->filterByBookClubList($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collBookListRelsPartial && count($collBookListRels)) {
                      $this->initBookListRels(false);

                      foreach ($collBookListRels as $obj) {
                        if (false == $this->collBookListRels->contains($obj)) {
                          $this->collBookListRels->append($obj);
                        }
                      }

                      $this->collBookListRelsPartial = true;
                    }

                    $collBookListRels->getInternalIterator()->rewind();

                    return $collBookListRels;
                }

                if ($partial && $this->collBookListRels) {
                    foreach ($this->collBookListRels as $obj) {
                        if ($obj->isNew()) {
                            $collBookListRels[] = $obj;
                        }
                    }
                }

                $this->collBookListRels = $collBookListRels;
                $this->collBookListRelsPartial = false;
            }
        }

        return $this->collBookListRels;
    }

    /**
     * Sets a collection of BookListRel objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $bookListRels A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return BookClubList The current object (for fluent API support)
     */
    public function setBookListRels(PropelCollection $bookListRels, PropelPDO $con = null)
    {
        $bookListRelsToDelete = $this->getBookListRels(new Criteria(), $con)->diff($bookListRels);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->bookListRelsScheduledForDeletion = clone $bookListRelsToDelete;

        foreach ($bookListRelsToDelete as $bookListRelRemoved) {
            $bookListRelRemoved->setBookClubList(null);
        }

        $this->collBookListRels = null;
        foreach ($bookListRels as $bookListRel) {
            $this->addBookListRel($bookListRel);
        }

        $this->collBookListRels = $bookListRels;
        $this->collBookListRelsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related BookListRel objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related BookListRel objects.
     * @throws PropelException
     */
    public function countBookListRels(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collBookListRelsPartial && !$this->isNew();
        if (null === $this->collBookListRels || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collBookListRels) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getBookListRels());
            }
            $query = BookListRelQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByBookClubList($this)
                ->count($con);
        }

        return count($this->collBookListRels);
    }

    /**
     * Method called to associate a BookListRel object to this object
     * through the BookListRel foreign key attribute.
     *
     * @param    BookListRel $l BookListRel
     * @return BookClubList The current object (for fluent API support)
     */
    public function addBookListRel(BookListRel $l)
    {
        if ($this->collBookListRels === null) {
            $this->initBookListRels();
            $this->collBookListRelsPartial = true;
        }

        if (!in_array($l, $this->collBookListRels->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddBookListRel($l);

            if ($this->bookListRelsScheduledForDeletion and $this->bookListRelsScheduledForDeletion->contains($l)) {
                $this->bookListRelsScheduledForDeletion->remove($this->bookListRelsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	BookListRel $bookListRel The bookListRel object to add.
     */
    protected function doAddBookListRel($bookListRel)
    {
        $this->collBookListRels[]= $bookListRel;
        $bookListRel->setBookClubList($this);
    }

    /**
     * @param	BookListRel $bookListRel The bookListRel object to remove.
     * @return BookClubList The current object (for fluent API support)
     */
    public function removeBookListRel($bookListRel)
    {
        if ($this->getBookListRels()->contains($bookListRel)) {
            $this->collBookListRels->remove($this->collBookListRels->search($bookListRel));
            if (null === $this->bookListRelsScheduledForDeletion) {
                $this->bookListRelsScheduledForDeletion = clone $this->collBookListRels;
                $this->bookListRelsScheduledForDeletion->clear();
            }
            $this->bookListRelsScheduledForDeletion[]= clone $bookListRel;
            $bookListRel->setBookClubList(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this BookClubList is new, it will return
     * an empty collection; or if this BookClubList has previously
     * been saved, it will retrieve related BookListRels from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in BookClubList.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|BookListRel[] List of BookListRel objects
     */
    public function getBookListRelsJoinBook($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = BookListRelQuery::create(null, $criteria);
        $query->joinWith('Book', $join_behavior);

        return $this->getBookListRels($query, $con);
    }

    /**
     * Clears out the collBooks collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return BookClubList The current object (for fluent API support)
     * @see        addBooks()
     */
    public function clearBooks()
    {
        $this->collBooks = null; // important to set this to null since that means it is uninitialized
        $this->collBooksPartial = null;

        return $this;
    }

    /**
     * Initializes the collBooks collection.
     *
     * By default this just sets the collBooks collection to an empty collection (like clearBooks());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initBooks()
    {
        $this->collBooks = new PropelObjectCollection();
        $this->collBooks->setModel('Book');
    }

    /**
     * Gets a collection of Book objects related by a many-to-many relationship
     * to the current object by way of the book_x_list cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this BookClubList is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|Book[] List of Book objects
     */
    public function getBooks($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collBooks || null !== $criteria) {
            if ($this->isNew() && null === $this->collBooks) {
                // return empty collection
                $this->initBooks();
            } else {
                $collBooks = BookQuery::create(null, $criteria)
                    ->filterByBookClubList($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collBooks;
                }
                $this->collBooks = $collBooks;
            }
        }

        return $this->collBooks;
    }

    /**
     * Sets a collection of Book objects related by a many-to-many relationship
     * to the current object by way of the book_x_list cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $books A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return BookClubList The current object (for fluent API support)
     */
    public function setBooks(PropelCollection $books, PropelPDO $con = null)
    {
        $this->clearBooks();
        $currentBooks = $this->getBooks(null, $con);

        $this->booksScheduledForDeletion = $currentBooks->diff($books);

        foreach ($books as $book) {
            if (!$currentBooks->contains($book)) {
                $this->doAddBook($book);
            }
        }

        $this->collBooks = $books;

        return $this;
    }

    /**
     * Gets the number of Book objects related by a many-to-many relationship
     * to the current object by way of the book_x_list cross-reference table.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param boolean $distinct Set to true to force count distinct
     * @param PropelPDO $con Optional connection object
     *
     * @return int the number of related Book objects
     */
    public function countBooks($criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collBooks || null !== $criteria) {
            if ($this->isNew() && null === $this->collBooks) {
                return 0;
            } else {
                $query = BookQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByBookClubList($this)
                    ->count($con);
            }
        } else {
            return count($this->collBooks);
        }
    }

    /**
     * Associate a Book object to this object
     * through the book_x_list cross reference table.
     *
     * @param  Book $book The BookListRel object to relate
     * @return BookClubList The current object (for fluent API support)
     */
    public function addBook(Book $book)
    {
        if ($this->collBooks === null) {
            $this->initBooks();
        }

        if (!$this->collBooks->contains($book)) { // only add it if the **same** object is not already associated
            $this->doAddBook($book);
            $this->collBooks[] = $book;

            if ($this->booksScheduledForDeletion and $this->booksScheduledForDeletion->contains($book)) {
                $this->booksScheduledForDeletion->remove($this->booksScheduledForDeletion->search($book));
            }
        }

        return $this;
    }

    /**
     * @param	Book $book The book object to add.
     */
    protected function doAddBook(Book $book)
    {
        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$book->getBookClubLists()->contains($this)) { $bookListRel = new BookListRel();
            $bookListRel->setBook($book);
            $this->addBookListRel($bookListRel);

            $foreignCollection = $book->getBookClubLists();
            $foreignCollection[] = $this;
        }
    }

    /**
     * Remove a Book object to this object
     * through the book_x_list cross reference table.
     *
     * @param Book $book The BookListRel object to relate
     * @return BookClubList The current object (for fluent API support)
     */
    public function removeBook(Book $book)
    {
        if ($this->getBooks()->contains($book)) {
            $this->collBooks->remove($this->collBooks->search($book));
            if (null === $this->booksScheduledForDeletion) {
                $this->booksScheduledForDeletion = clone $this->collBooks;
                $this->booksScheduledForDeletion->clear();
            }
            $this->booksScheduledForDeletion[]= $book;
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->group_leader = null;
        $this->theme = null;
        $this->created_at = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep && !$this->alreadyInClearAllReferencesDeep) {
            $this->alreadyInClearAllReferencesDeep = true;
            if ($this->collBookListRels) {
                foreach ($this->collBookListRels as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collBooks) {
                foreach ($this->collBooks as $o) {
                    $o->clearAllReferences($deep);
                }
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collBookListRels instanceof PropelCollection) {
            $this->collBookListRels->clearIterator();
        }
        $this->collBookListRels = null;
        if ($this->collBooks instanceof PropelCollection) {
            $this->collBooks->clearIterator();
        }
        $this->collBooks = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(BookClubListPeer::DEFAULT_STRING_FORMAT);
    }

    /**
     * return true is the object is in saving state
     *
     * @return boolean
     */
    public function isAlreadyInSave()
    {
        return $this->alreadyInSave;
    }

}
