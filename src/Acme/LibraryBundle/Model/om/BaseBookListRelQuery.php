<?php

namespace Acme\LibraryBundle\Model\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Acme\LibraryBundle\Model\Book;
use Acme\LibraryBundle\Model\BookClubList;
use Acme\LibraryBundle\Model\BookListRel;
use Acme\LibraryBundle\Model\BookListRelPeer;
use Acme\LibraryBundle\Model\BookListRelQuery;

/**
 * @method BookListRelQuery orderByBookId($order = Criteria::ASC) Order by the book_id column
 * @method BookListRelQuery orderByBookClubListId($order = Criteria::ASC) Order by the book_club_list_id column
 *
 * @method BookListRelQuery groupByBookId() Group by the book_id column
 * @method BookListRelQuery groupByBookClubListId() Group by the book_club_list_id column
 *
 * @method BookListRelQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method BookListRelQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method BookListRelQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method BookListRelQuery leftJoinBook($relationAlias = null) Adds a LEFT JOIN clause to the query using the Book relation
 * @method BookListRelQuery rightJoinBook($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Book relation
 * @method BookListRelQuery innerJoinBook($relationAlias = null) Adds a INNER JOIN clause to the query using the Book relation
 *
 * @method BookListRelQuery leftJoinBookClubList($relationAlias = null) Adds a LEFT JOIN clause to the query using the BookClubList relation
 * @method BookListRelQuery rightJoinBookClubList($relationAlias = null) Adds a RIGHT JOIN clause to the query using the BookClubList relation
 * @method BookListRelQuery innerJoinBookClubList($relationAlias = null) Adds a INNER JOIN clause to the query using the BookClubList relation
 *
 * @method BookListRel findOne(PropelPDO $con = null) Return the first BookListRel matching the query
 * @method BookListRel findOneOrCreate(PropelPDO $con = null) Return the first BookListRel matching the query, or a new BookListRel object populated from the query conditions when no match is found
 *
 * @method BookListRel findOneByBookId(int $book_id) Return the first BookListRel filtered by the book_id column
 * @method BookListRel findOneByBookClubListId(int $book_club_list_id) Return the first BookListRel filtered by the book_club_list_id column
 *
 * @method array findByBookId(int $book_id) Return BookListRel objects filtered by the book_id column
 * @method array findByBookClubListId(int $book_club_list_id) Return BookListRel objects filtered by the book_club_list_id column
 */
abstract class BaseBookListRelQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseBookListRelQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = null, $modelName = null, $modelAlias = null)
    {
        if (null === $dbName) {
            $dbName = 'default';
        }
        if (null === $modelName) {
            $modelName = 'Acme\\LibraryBundle\\Model\\BookListRel';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new BookListRelQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   BookListRelQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return BookListRelQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof BookListRelQuery) {
            return $criteria;
        }
        $query = new BookListRelQuery(null, null, $modelAlias);

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
     * $obj = $c->findPk(array(12, 34), $con);
     * </code>
     *
     * @param array $key Primary key to use for the query
                         A Primary key composition: [$book_id, $book_club_list_id]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   BookListRel|BookListRel[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = BookListRelPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(BookListRelPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 BookListRel A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `book_id`, `book_club_list_id` FROM `book_x_list` WHERE `book_id` = :p0 AND `book_club_list_id` = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new BookListRel();
            $obj->hydrate($row);
            BookListRelPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return BookListRel|BookListRel[]|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|BookListRel[]|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($stmt);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return BookListRelQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(BookListRelPeer::BOOK_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(BookListRelPeer::BOOK_CLUB_LIST_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return BookListRelQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(BookListRelPeer::BOOK_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(BookListRelPeer::BOOK_CLUB_LIST_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the book_id column
     *
     * Example usage:
     * <code>
     * $query->filterByBookId(1234); // WHERE book_id = 1234
     * $query->filterByBookId(array(12, 34)); // WHERE book_id IN (12, 34)
     * $query->filterByBookId(array('min' => 12)); // WHERE book_id >= 12
     * $query->filterByBookId(array('max' => 12)); // WHERE book_id <= 12
     * </code>
     *
     * @see       filterByBook()
     *
     * @param     mixed $bookId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return BookListRelQuery The current query, for fluid interface
     */
    public function filterByBookId($bookId = null, $comparison = null)
    {
        if (is_array($bookId)) {
            $useMinMax = false;
            if (isset($bookId['min'])) {
                $this->addUsingAlias(BookListRelPeer::BOOK_ID, $bookId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($bookId['max'])) {
                $this->addUsingAlias(BookListRelPeer::BOOK_ID, $bookId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BookListRelPeer::BOOK_ID, $bookId, $comparison);
    }

    /**
     * Filter the query on the book_club_list_id column
     *
     * Example usage:
     * <code>
     * $query->filterByBookClubListId(1234); // WHERE book_club_list_id = 1234
     * $query->filterByBookClubListId(array(12, 34)); // WHERE book_club_list_id IN (12, 34)
     * $query->filterByBookClubListId(array('min' => 12)); // WHERE book_club_list_id >= 12
     * $query->filterByBookClubListId(array('max' => 12)); // WHERE book_club_list_id <= 12
     * </code>
     *
     * @see       filterByBookClubList()
     *
     * @param     mixed $bookClubListId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return BookListRelQuery The current query, for fluid interface
     */
    public function filterByBookClubListId($bookClubListId = null, $comparison = null)
    {
        if (is_array($bookClubListId)) {
            $useMinMax = false;
            if (isset($bookClubListId['min'])) {
                $this->addUsingAlias(BookListRelPeer::BOOK_CLUB_LIST_ID, $bookClubListId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($bookClubListId['max'])) {
                $this->addUsingAlias(BookListRelPeer::BOOK_CLUB_LIST_ID, $bookClubListId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BookListRelPeer::BOOK_CLUB_LIST_ID, $bookClubListId, $comparison);
    }

    /**
     * Filter the query by a related Book object
     *
     * @param   Book|PropelObjectCollection $book The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 BookListRelQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByBook($book, $comparison = null)
    {
        if ($book instanceof Book) {
            return $this
                ->addUsingAlias(BookListRelPeer::BOOK_ID, $book->getId(), $comparison);
        } elseif ($book instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(BookListRelPeer::BOOK_ID, $book->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByBook() only accepts arguments of type Book or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Book relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return BookListRelQuery The current query, for fluid interface
     */
    public function joinBook($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Book');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Book');
        }

        return $this;
    }

    /**
     * Use the Book relation Book object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Acme\LibraryBundle\Model\BookQuery A secondary query class using the current class as primary query
     */
    public function useBookQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinBook($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Book', '\Acme\LibraryBundle\Model\BookQuery');
    }

    /**
     * Filter the query by a related BookClubList object
     *
     * @param   BookClubList|PropelObjectCollection $bookClubList The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 BookListRelQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByBookClubList($bookClubList, $comparison = null)
    {
        if ($bookClubList instanceof BookClubList) {
            return $this
                ->addUsingAlias(BookListRelPeer::BOOK_CLUB_LIST_ID, $bookClubList->getId(), $comparison);
        } elseif ($bookClubList instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(BookListRelPeer::BOOK_CLUB_LIST_ID, $bookClubList->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByBookClubList() only accepts arguments of type BookClubList or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the BookClubList relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return BookListRelQuery The current query, for fluid interface
     */
    public function joinBookClubList($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('BookClubList');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'BookClubList');
        }

        return $this;
    }

    /**
     * Use the BookClubList relation BookClubList object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Acme\LibraryBundle\Model\BookClubListQuery A secondary query class using the current class as primary query
     */
    public function useBookClubListQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinBookClubList($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'BookClubList', '\Acme\LibraryBundle\Model\BookClubListQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   BookListRel $bookListRel Object to remove from the list of results
     *
     * @return BookListRelQuery The current query, for fluid interface
     */
    public function prune($bookListRel = null)
    {
        if ($bookListRel) {
            $this->addCond('pruneCond0', $this->getAliasedColName(BookListRelPeer::BOOK_ID), $bookListRel->getBookId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(BookListRelPeer::BOOK_CLUB_LIST_ID), $bookListRel->getBookClubListId(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

}
