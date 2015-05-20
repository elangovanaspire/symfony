<?php

namespace Acme\LibraryBundle\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'book' table.
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
class BookTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Acme.LibraryBundle.Model.map.BookTableMap';

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
        $this->setName('book');
        $this->setPhpName('Book');
        $this->setClassname('Acme\\LibraryBundle\\Model\\Book');
        $this->setPackage('src.Acme.LibraryBundle.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('title', 'Title', 'VARCHAR', false, 100, null);
        $this->getColumn('title', false)->setPrimaryString(true);
        $this->addColumn('isbn', 'Isbn', 'VARCHAR', false, 20, null);
        $this->addForeignKey('author_id', 'AuthorId', 'INTEGER', 'author', 'id', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Author', 'Acme\\LibraryBundle\\Model\\Author', RelationMap::MANY_TO_ONE, array('author_id' => 'id', ), null, null);
    } // buildRelations()

} // BookTableMap
