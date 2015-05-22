<?php

namespace Acme\TaskBundle\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'tag' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.src.Acme.TaskBundle.Model.map
 */
class TagTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Acme.TaskBundle.Model.map.TagTableMap';

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
        $this->setName('tag');
        $this->setPhpName('Tag');
        $this->setClassname('Acme\\TaskBundle\\Model\\Tag');
        $this->setPackage('src.Acme.TaskBundle.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('tags', 'Tags', 'VARCHAR', false, 20, null);
        $this->addForeignKey('task_id', 'TaskId', 'INTEGER', 'task', 'id', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Task', 'Acme\\TaskBundle\\Model\\Task', RelationMap::MANY_TO_ONE, array('task_id' => 'id', ), null, null);
    } // buildRelations()

} // TagTableMap
