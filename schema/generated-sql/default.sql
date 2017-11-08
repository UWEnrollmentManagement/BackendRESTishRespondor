
-----------------------------------------------------------------------
-- element
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [element];

CREATE TABLE [element]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [retired] INTEGER DEFAULT 0 NOT NULL,
    [type] VARCHAR(31) NOT NULL,
    [label] VARCHAR(8191) NOT NULL,
    [initial_value] VARCHAR(127),
    [help_text] VARCHAR(4095),
    [placeholder_text] VARCHAR(127),
    [required] INTEGER DEFAULT 1 NOT NULL,
    [parent_id] INTEGER,
    UNIQUE ([id]),
    FOREIGN KEY ([parent_id]) REFERENCES [element] ([id])
        ON DELETE SET NULL
);

-----------------------------------------------------------------------
-- form
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [form];

CREATE TABLE [form]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [name] VARCHAR(127) NOT NULL,
    [slug] VARCHAR(127) NOT NULL,
    [success_message] VARCHAR(1683) DEFAULT '',
    [retired] INTEGER DEFAULT 0 NOT NULL,
    [root_element_id] INTEGER,
    UNIQUE ([id]),
    FOREIGN KEY ([root_element_id]) REFERENCES [element] ([id])
        ON DELETE SET NULL
);

-----------------------------------------------------------------------
-- visitor
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [visitor];

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

-----------------------------------------------------------------------
-- child_form_relationship
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [child_form_relationship];

CREATE TABLE [child_form_relationship]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [parent_id] INTEGER NOT NULL,
    [child_id] INTEGER NOT NULL,
    [tag_id] INTEGER,
    [reaction_id] INTEGER,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- choice_value
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [choice_value];

CREATE TABLE [choice_value]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [value] VARCHAR(127) NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- condition
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [condition];

CREATE TABLE [condition]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [operator] VARCHAR(127) NOT NULL,
    [value] VARCHAR(127) NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- dependency
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [dependency];

CREATE TABLE [dependency]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [element_id] INTEGER NOT NULL,
    [slave_id] INTEGER NOT NULL,
    [condition_id] INTEGER NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- requirement
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [requirement];

CREATE TABLE [requirement]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [element_id] INTEGER NOT NULL,
    [condition_id] INTEGER NOT NULL,
    [failure_message] VARCHAR(255),
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- submission
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [submission];

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

-----------------------------------------------------------------------
-- status
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [status];

CREATE TABLE [status]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [name] VARCHAR(63) NOT NULL,
    [default_message] VARCHAR(1683),
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- tag
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [tag];

CREATE TABLE [tag]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [name] VARCHAR(63) NOT NULL,
    [default_message] VARCHAR(1683),
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- note
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [note];

CREATE TABLE [note]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [content] VARCHAR(1683) NOT NULL,
    [subject] VARCHAR(127) NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- recipient
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [recipient];

CREATE TABLE [recipient]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [address] INTEGER NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- stakeholder
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [stakeholder];

CREATE TABLE [stakeholder]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [label] VARCHAR(127) NOT NULL,
    [address] VARCHAR(127) NOT NULL,
    [formId] INTEGER NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- reaction
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [reaction];

CREATE TABLE [reaction]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [subject] VARCHAR(127) NOT NULL,
    [recipient] VARCHAR(63) NOT NULL,
    [sender] VARCHAR(63) NOT NULL,
    [replyTo] VARCHAR(63),
    [cc] VARCHAR(63),
    [bcc] VARCHAR(63),
    [template] VARCHAR(127) NOT NULL,
    [content] VARCHAR(255) NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- setting
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [setting];

CREATE TABLE [setting]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [key] VARCHAR(255) NOT NULL,
    [value] VARCHAR(255) NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- dashboard
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [dashboard];

CREATE TABLE [dashboard]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [name] VARCHAR(255) NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- element_choice
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [element_choice];

CREATE TABLE [element_choice]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [element_id] INTEGER NOT NULL,
    [choice_id] INTEGER NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- submission_tag
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [submission_tag];

CREATE TABLE [submission_tag]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [submission_id] INTEGER NOT NULL,
    [tag_id] INTEGER NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- form_tag
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [form_tag];

CREATE TABLE [form_tag]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [form_id] INTEGER NOT NULL,
    [tag_id] INTEGER NOT NULL,
    [message] VARCHAR(63),
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- form_reaction
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [form_reaction];

CREATE TABLE [form_reaction]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [reaction_id] INTEGER NOT NULL,
    [form_id] INTEGER NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- dashboard_element
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [dashboard_element];

CREATE TABLE [dashboard_element]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [dashboard_id] INTEGER NOT NULL,
    [element_id] INTEGER NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- dashboard_form
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [dashboard_form];

CREATE TABLE [dashboard_form]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [dashboard_id] INTEGER NOT NULL,
    [form_id] INTEGER NOT NULL,
    UNIQUE ([id])
);
