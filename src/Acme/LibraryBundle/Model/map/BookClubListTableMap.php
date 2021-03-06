<?php

namespace Acme\LibraryBundle\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'book_club_list' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.src.Acme.LibraryBundle.Model.map
 */
class BookClubListTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Acme.LibraryBundle.Model.map.BookClubListTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('book_club_list');
        $this->setPhpName('BookClubList');
        $this->setClassname('Acme\\LibraryBundle\\Model\\BookClubList');
        $this->setPackage('src.Acme.LibraryBundle.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('group_leader', 'GroupLeader', 'VARCHAR', true, 100, null);
        $this->addColumn('theme', 'Theme', 'VARCHAR', false, 50, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('BookListRel', 'Acme\\LibraryBundle\\Model\\BookListRel', RelationMap::ONE_TO_MANY, array('id' => 'book_club_list_id', ), 'CASCADE', null, 'BookListRels');
        $this->addRelation('Book', 'Acme\\LibraryBundle\\Model\\Book', RelationMap::MANY_TO_MANY, array(), 'CASCADE', null, 'Books');
    } // buildRelations()

} // BookClubListTableMap
