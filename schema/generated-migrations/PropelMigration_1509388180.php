<?php

use Propel\Generator\Manager\MigrationManager;

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1509388180.
 * Generated on 2017-10-30 19:29:40 
 */
class PropelMigration_1509388180
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

CREATE TEMPORARY TABLE [visitor__temp__59f76f9456108] AS SELECT [id],[uw_student_number],[uw_net_id],[first_name],[middle_name],[last_name] FROM [visitor];
DROP TABLE [visitor];

CREATE TABLE [visitor]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [uw_student_number] VARCHAR(7),
    [uw_net_id] VARCHAR(63) NOT NULL,
    [first_name] VARCHAR(63),
    [middle_name] VARCHAR(63),
    [last_name] VARCHAR(63),
    UNIQUE ([id])
);

INSERT INTO [visitor] (uw_student_number, uw_net_id, first_name, middle_name, last_name, id) SELECT uw_student_number, uw_net_id, first_name, middle_name, last_name, id FROM [visitor__temp__59f76f9456108];
DROP TABLE [visitor__temp__59f76f9456108];

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

CREATE TEMPORARY TABLE [visitor__temp__59f76f9456446] AS SELECT [id],[uw_student_number],[uw_net_id],[first_name],[middle_name],[last_name] FROM [visitor];
DROP TABLE [visitor];

CREATE TABLE [visitor]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [uw_student_number] MEDIUMBLOB,
    [uw_net_id] MEDIUMBLOB NOT NULL,
    [first_name] MEDIUMBLOB,
    [middle_name] MEDIUMBLOB,
    [last_name] MEDIUMBLOB,
    UNIQUE ([id])
);

INSERT INTO [visitor] (uw_student_number, uw_net_id, first_name, middle_name, last_name, id) SELECT uw_student_number, uw_net_id, first_name, middle_name, last_name, id FROM [visitor__temp__59f76f9456446];
DROP TABLE [visitor__temp__59f76f9456446];

PRAGMA foreign_keys = ON;
',
);
    }

}