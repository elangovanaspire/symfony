<?php

namespace Acme\LibraryBundle\Model\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Acme\LibraryBundle\Model\Author;
use Acme\LibraryBundle\Model\AuthorQuery;
use Acme\LibraryBundle\Model\Book;
use Acme\LibraryBundle\Model\BookClubList;
use Acme\LibraryBundle\Model\BookClubListQuery;
use Acme\LibraryBundle\Model\BookListRel;
use Acme\LibraryBundle\Model\BookListRelQuery;
use Acme\LibraryBundle\Model\BookPeer;
use Acme\LibraryBundle\Model\BookQuery;

abstract class BaseBook extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Acme\\LibraryBundle\\Model\\BookPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        BookPeer
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
     * The value for the title field.
     * @var        string
     */
    protected $title;

    /**
     * The value for the isbn field.
     * @var        string
     */
    protected $isbn;

    /**
     * The value for the author_id field.
     * @var        int
     */
    protected $author_id;

    /**
     * @var        Author
     */
    protected $aAuthor;

    /**
     * @var        PropelObjectCollection|BookListRel[] Collection to store aggregation of BookListRel objects.
     */
    protected $collBookListRels;
    protected $collBookListRelsPartial;

    /**
     * @var        PropelObjectCollection|BookClubList[] Collection to store aggregation of BookClubList objects.
     */
    protected $collBookClubLists;

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
    protected $bookClubListsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $bookListRelsScheduledForDeletion = null;

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
     * Get the [title] column value.
     *
     * @return string
     */
    public function getTitle()
    {

        return $this->title;
    }

    /**
     * Get the [isbn] column value.
     *
     * @return string
     */
    public function getIsbn()
    {

        return $this->isbn;
    }

    /**
     * Get the [author_id] column value.
     *
     * @return int
     */
    public function getAuthorId()
    {

        return $this->author_id;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return Book The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = BookPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [title] column.
     *
     * @param  string $v new value
     * @return Book The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[] = BookPeer::TITLE;
        }


        return $this;
    } // setTitle()

    /**
     * Set the value of [isbn] column.
     *
     * @param  string $v new value
     * @return Book The current object (for fluent API support)
     */
    public function setIsbn($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->isbn !== $v) {
            $this->isbn = $v;
            $this->modifiedColumns[] = BookPeer::ISBN;
        }


        return $this;
    } // setIsbn()

    /**
     * Set the value of [author_id] column.
     *
     * @param  int $v new value
     * @return Book The current object (for fluent API support)
     */
    public function setAuthorId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->author_id !== $v) {
            $this->author_id = $v;
            $this->modifiedColumns[] = BookPeer::AUTHOR_ID;
        }

        if ($this->aAuthor !== null && $this->aAuthor->getId() !== $v) {
            $this->aAuthor = null;
        }


        return $this;
    } // setAuthorId()

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
            $this->title = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->isbn = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->author_id = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 4; // 4 = BookPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Book object", $e);
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

        if ($this->aAuthor !== null && $this->author_id !== $this->aAuthor->getId()) {
            $this->aAuthor = null;
        }
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
            $con = Propel::getConnection(BookPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = BookPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aAuthor = null;
            $this->collBookListRels = null;

            $this->collBookClubLists = null;
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
            $con = Propel::getConnection(BookPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = BookQuery::create()
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
            $con = Propel::getConnection(BookPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                BookPeer::addInstanceToPool($this);
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

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aAuthor !== null) {
                if ($this->aAuthor->isModified() || $this->aAuthor->isNew()) {
                    $affectedRows += $this->aAuthor->save($con);
                }
                $this->setAuthor($this->aAuthor);
            }

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

            if ($this->bookClubListsScheduledForDeletion !== null) {
                if (!$this->bookClubListsScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->bookClubListsScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($pk, $remotePk);
                    }
                    BookListRelQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->bookClubListsScheduledForDeletion = null;
                }

                foreach ($this->getBookClubLists() as $bookClubList) {
                    if ($bookClubList->isModified()) {
                        $bookClubList->save($con);
                    }
                }
            } elseif ($this->collBookClubLists) {
                foreach ($this->collBookClubLists as $bookClubList) {
                    if ($bookClubList->isModified()) {
                        $bookClubList->save($con);
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

        $this->modifiedColumns[] = BookPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . BookPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(BookPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(BookPeer::TITLE)) {
            $modifiedColumns[':p' . $index++]  = '`title`';
        }
        if ($this->isColumnModified(BookPeer::ISBN)) {
            $modifiedColumns[':p' . $index++]  = '`isbn`';
        }
        if ($this->isColumnModified(BookPeer::AUTHOR_ID)) {
            $modifiedColumns[':p' . $index++]  = '`author_id`';
        }

        $sql = sprintf(
            'INSERT INTO `book` (%s) VALUES (%s)',
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
                    case '`title`':
                        $stmt->bindValue($identifier, $this->title, PDO::PARAM_STR);
                        break;
                    case '`isbn`':
                        $stmt->bindValue($identifier, $this->isbn, PDO::PARAM_STR);
                        break;
                    case '`author_id`':
                        $stmt->bindValue($identifier, $this->author_id, PDO::PARAM_INT);
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


            // We call the validate method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aAuthor !== null) {
                if (!$this->aAuthor->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aAuthor->getValidationFailures());
                }
            }


            if (($retval = BookPeer::doValidate($this, $columns)) !== true) {
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
        $pos = BookPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getTitle();
                break;
            case 2:
                return $this->getIsbn();
                break;
            case 3:
                return $this->getAuthorId();
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
        if (isset($alreadyDumpedObjects['Book'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Book'][$this->getPrimaryKey()] = true;
        $keys = BookPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getTitle(),
            $keys[2] => $this->getIsbn(),
            $keys[3] => $this->getAuthorId(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aAuthor) {
                $result['Author'] = $this->aAuthor->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
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
        $pos = BookPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setTitle($value);
                break;
            case 2:
                $this->setIsbn($value);
                break;
            case 3:
                $this->setAuthorId($value);
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
        $keys = BookPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setTitle($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setIsbn($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setAuthorId($arr[$keys[3]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(BookPeer::DATABASE_NAME);

        if ($this->isColumnModified(BookPeer::ID)) $criteria->add(BookPeer::ID, $this->id);
        if ($this->isColumnModified(BookPeer::TITLE)) $criteria->add(BookPeer::TITLE, $this->title);
        if ($this->isColumnModified(BookPeer::ISBN)) $criteria->add(BookPeer::ISBN, $this->isbn);
        if ($this->isColumnModified(BookPeer::AUTHOR_ID)) $criteria->add(BookPeer::AUTHOR_ID, $this->author_id);

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
        $criteria = new Criteria(BookPeer::DATABASE_NAME);
        $criteria->add(BookPeer::ID, $this->id);

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
     * @param object $copyObj An object of Book (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setTitle($this->getTitle());
        $copyObj->setIsbn($this->getIsbn());
        $copyObj->setAuthorId($this->getAuthorId());

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
     * @return Book Clone of current object.
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
     * @return BookPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new BookPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Author object.
     *
     * @param                  Author $v
     * @return Book The current object (for fluent API support)
     * @throws PropelException
     */
    public function setAuthor(Author $v = null)
    {
        if ($v === null) {
            $this->setAuthorId(NULL);
        } else {
            $this->setAuthorId($v->getId());
        }

        $this->aAuthor = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Author object, it will not be re-added.
        if ($v !== null) {
            $v->addBook($this);
        }


        return $this;
    }


    /**
     * Get the associated Author object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Author The associated Author object.
     * @throws PropelException
     */
    public function getAuthor(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aAuthor === null && ($this->author_id !== null) && $doQuery) {
            $this->aAuthor = AuthorQuery::create()->findPk($this->author_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aAuthor->addBooks($this);
             */
        }

        return $this->aAuthor;
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
     * @return Book The current object (for fluent API support)
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
     * If this Book is new, it will return
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
                    ->filterByBook($this)
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
     * @return Book The current object (for fluent API support)
     */
    public function setBookListRels(PropelCollection $bookListRels, PropelPDO $con = null)
    {
        $bookListRelsToDelete = $this->getBookListRels(new Criteria(), $con)->diff($bookListRels);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->bookListRelsScheduledForDeletion = clone $bookListRelsToDelete;

        foreach ($bookListRelsToDelete as $bookListRelRemoved) {
            $bookListRelRemoved->setBook(null);
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
                ->filterByBook($this)
                ->count($con);
        }

        return count($this->collBookListRels);
    }

    /**
     * Method called to associate a BookListRel object to this object
     * through the BookListRel foreign key attribute.
     *
     * @param    BookListRel $l BookListRel
     * @return Book The current object (for fluent API support)
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
        $bookListRel->setBook($this);
    }

    /**
     * @param	BookListRel $bookListRel The bookListRel object to remove.
     * @return Book The current object (for fluent API support)
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
            $bookListRel->setBook(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Book is new, it will return
     * an empty collection; or if this Book has previously
     * been saved, it will retrieve related BookListRels from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Book.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|BookListRel[] List of BookListRel objects
     */
    public function getBookListRelsJoinBookClubList($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = BookListRelQuery::create(null, $criteria);
        $query->joinWith('BookClubList', $join_behavior);

        return $this->getBookListRels($query, $con);
    }

    /**
     * Clears out the collBookClubLists collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Book The current object (for fluent API support)
     * @see        addBookClubLists()
     */
    public function clearBookClubLists()
    {
        $this->collBookClubLists = null; // important to set this to null since that means it is uninitialized
        $this->collBookClubListsPartial = null;

        return $this;
    }

    /**
     * Initializes the collBookClubLists collection.
     *
     * By default this just sets the collBookClubLists collection to an empty collection (like clearBookClubLists());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initBookClubLists()
    {
        $this->collBookClubLists = new PropelObjectCollection();
        $this->collBookClubLists->setModel('BookClubList');
    }

    /**
     * Gets a collection of BookClubList objects related by a many-to-many relationship
     * to the current object by way of the book_x_list cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Book is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|BookClubList[] List of BookClubList objects
     */
    public function getBookClubLists($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collBookClubLists || null !== $criteria) {
            if ($this->isNew() && null === $this->collBookClubLists) {
                // return empty collection
                $this->initBookClubLists();
            } else {
                $collBookClubLists = BookClubListQuery::create(null, $criteria)
                    ->filterByBook($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collBookClubLists;
                }
                $this->collBookClubLists = $collBookClubLists;
            }
        }

        return $this->collBookClubLists;
    }

    /**
     * Sets a collection of BookClubList objects related by a many-to-many relationship
     * to the current object by way of the book_x_list cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $bookClubLists A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Book The current object (for fluent API support)
     */
    public function setBookClubLists(PropelCollection $bookClubLists, PropelPDO $con = null)
    {
        $this->clearBookClubLists();
        $currentBookClubLists = $this->getBookClubLists(null, $con);

        $this->bookClubListsScheduledForDeletion = $currentBookClubLists->diff($bookClubLists);

        foreach ($bookClubLists as $bookClubList) {
            if (!$currentBookClubLists->contains($bookClubList)) {
                $this->doAddBookClubList($bookClubList);
            }
        }

        $this->collBookClubLists = $bookClubLists;

        return $this;
    }

    /**
     * Gets the number of BookClubList objects related by a many-to-many relationship
     * to the current object by way of the book_x_list cross-reference table.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param boolean $distinct Set to true to force count distinct
     * @param PropelPDO $con Optional connection object
     *
     * @return int the number of related BookClubList objects
     */
    public function countBookClubLists($criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collBookClubLists || null !== $criteria) {
            if ($this->isNew() && null === $this->collBookClubLists) {
                return 0;
            } else {
                $query = BookClubListQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByBook($this)
                    ->count($con);
            }
        } else {
            return count($this->collBookClubLists);
        }
    }

    /**
     * Associate a BookClubList object to this object
     * through the book_x_list cross reference table.
     *
     * @param  BookClubList $bookClubList The BookListRel object to relate
     * @return Book The current object (for fluent API support)
     */
    public function addBookClubList(BookClubList $bookClubList)
    {
        if ($this->collBookClubLists === null) {
            $this->initBookClubLists();
        }

        if (!$this->collBookClubLists->contains($bookClubList)) { // only add it if the **same** object is not already associated
            $this->doAddBookClubList($bookClubList);
            $this->collBookClubLists[] = $bookClubList;

            if ($this->bookClubListsScheduledForDeletion and $this->bookClubListsScheduledForDeletion->contains($bookClubList)) {
                $this->bookClubListsScheduledForDeletion->remove($this->bookClubListsScheduledForDeletion->search($bookClubList));
            }
        }

        return $this;
    }

    /**
     * @param	BookClubList $bookClubList The bookClubList object to add.
     */
    protected function doAddBookClubList(BookClubList $bookClubList)
    {
        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$bookClubList->getBooks()->contains($this)) { $bookListRel = new BookListRel();
            $bookListRel->setBookClubList($bookClubList);
            $this->addBookListRel($bookListRel);

            $foreignCollection = $bookClubList->getBooks();
            $foreignCollection[] = $this;
        }
    }

    /**
     * Remove a BookClubList object to this object
     * through the book_x_list cross reference table.
     *
     * @param BookClubList $bookClubList The BookListRel object to relate
     * @return Book The current object (for fluent API support)
     */
    public function removeBookClubList(BookClubList $bookClubList)
    {
        if ($this->getBookClubLists()->contains($bookClubList)) {
            $this->collBookClubLists->remove($this->collBookClubLists->search($bookClubList));
            if (null === $this->bookClubListsScheduledForDeletion) {
                $this->bookClubListsScheduledForDeletion = clone $this->collBookClubLists;
                $this->bookClubListsScheduledForDeletion->clear();
            }
            $this->bookClubListsScheduledForDeletion[]= $bookClubList;
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->title = null;
        $this->isbn = null;
        $this->author_id = null;
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
            if ($this->collBookClubLists) {
                foreach ($this->collBookClubLists as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aAuthor instanceof Persistent) {
              $this->aAuthor->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collBookListRels instanceof PropelCollection) {
            $this->collBookListRels->clearIterator();
        }
        $this->collBookListRels = null;
        if ($this->collBookClubLists instanceof PropelCollection) {
            $this->collBookClubLists->clearIterator();
        }
        $this->collBookClubLists = null;
        $this->aAuthor = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string The value of the 'title' column
     */
    public function __toString()
    {
        return (string) $this->getTitle();
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
