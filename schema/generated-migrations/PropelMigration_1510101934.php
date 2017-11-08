<?php

use Propel\Generator\Manager\MigrationManager;

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1510101934.
 * Generated on 2017-11-08 01:45:34 
 */
class PropelMigration_1510101934
{
    public $comment = '';

    public function preUp(MigrationManager $manager)
    {
        // add the pre-migration code here
    }

    public function postUp(MigrationManager $manager)
    {
        // add the post-migration code here
    }

    public function preDown(MigrationManager $manager)
    {
        // add the pre-migration code here
    }

    public function postDown(MigrationManager $manager)
    {
        // add the post-migration code here
    }

    /**
     * Get the SQL statements for the Up migration
     *
     * @return array list of the SQL strings to execute for the Up migration
     *               the keys being the datasources
     */
    public function getUpSQL()
    {
        return array (
  'default' => '
PRAGMA foreign_keys = OFF;

ALTER TABLE [submission] ADD [submitted] TIMESTAMP;

PRAGMA foreign_keys = ON;
',
);
    }

    /**
     * Get the SQL statements for the Down migration
     *
     * @return array list of the SQL strings to execute for the Down migration
     *               the keys being the datasources
     */
    public function getDownSQL()
    {
        return array (
  'default' => '
PRAGMA foreign_keys = OFF;

CREATE TEMPORARY TABLE [submission__temp__5a0253ae2ff2d] AS SELECT [id],[visitor_id],[form_id],[status_id],[assignee_id],[parent_id],[submitted] FROM [submission];
DROP TABLE [submission];

CREATE TABLE [submission]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [visitor_id] INTEGER NOT NULL,
    [form_id] INTEGER NOT NULL,
    [status_id] INTEGER,
    [assignee_id] INTEGER,
    [parent_id] INTEGER,
    UNIQUE ([id])
);

INSERT INTO [submission] (id, visitor_id, form_id, status_id, assignee_id, parent_id) SELECT id, visitor_id, form_id, status_id, assignee_id, parent_id FROM [submission__temp__5a0253ae2ff2d];
DROP TABLE [submission__temp__5a0253ae2ff2d];

PRAGMA foreign_keys = ON;
',
);
    }

}