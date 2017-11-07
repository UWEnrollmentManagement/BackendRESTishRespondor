
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
-- choices
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [choices];

CREATE TABLE [choices]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [value] VARCHAR(127) NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- dependencies
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [dependencies];

CREATE TABLE [dependencies]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [element_id] INTEGER NOT NULL,
    [slave_id] INTEGER NOT NULL,
    [condition_id] INTEGER NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- requirements
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [requirements];

CREATE TABLE [requirements]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [element_id] INTEGER NOT NULL,
    [condition_id] INTEGER NOT NULL,
    [failure_message] VARCHAR(255) NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- submissions
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [submissions];

CREATE TABLE [submissions]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [visitor_id] INTEGER NOT NULL,
    [form_id] INTEGER NOT NULL,
    [status_id] INTEGER NOT NULL,
    [assignee_id] INTEGER NOT NULL,
    [parent_id] INTEGER NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- statuses
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [statuses];

CREATE TABLE [statuses]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [name] VARCHAR(63) NOT NULL,
    [default_message] VARCHAR(1683) NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- tags
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [tags];

CREATE TABLE [tags]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [name] VARCHAR(63) NOT NULL,
    [default_message] VARCHAR(1683) NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- notes
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [notes];

CREATE TABLE [notes]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [content] VARCHAR(1683) NOT NULL,
    [subject] VARCHAR(127) NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- recipients
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [recipients];

CREATE TABLE [recipients]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [address] INTEGER NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- stakeholders
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [stakeholders];

CREATE TABLE [stakeholders]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [label] VARCHAR(127) NOT NULL,
    [address] VARCHAR(127) NOT NULL,
    [formId] INTEGER NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- reactions
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [reactions];

CREATE TABLE [reactions]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [subject] VARCHAR(127) NOT NULL,
    [recipient] VARCHAR(63) NOT NULL,
    [sender] VARCHAR(63) NOT NULL,
    [replyTo] VARCHAR(63) NOT NULL,
    [cc] VARCHAR(63) NOT NULL,
    [bcc] VARCHAR(63) NOT NULL,
    [template] VARCHAR(127) NOT NULL,
    [content] VARCHAR(255) NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- settings
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [settings];

CREATE TABLE [settings]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [key] VARCHAR(255) NOT NULL,
    [value] VARCHAR(255) NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- dashboards
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [dashboards];

CREATE TABLE [dashboards]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [name] VARCHAR(255) NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- element_choices
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [element_choices];

CREATE TABLE [element_choices]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [element_id] INTEGER NOT NULL,
    [choice_id] INTEGER NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- submission_tags
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [submission_tags];

CREATE TABLE [submission_tags]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [submission_id] INTEGER NOT NULL,
    [tag_id] INTEGER NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- form_tags
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [form_tags];

CREATE TABLE [form_tags]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [form_id] INTEGER NOT NULL,
    [tag_id] INTEGER NOT NULL,
    [message] VARCHAR(63) NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- form_reactions
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [form_reactions];

CREATE TABLE [form_reactions]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [reaction_id] INTEGER NOT NULL,
    [form_id] INTEGER NOT NULL,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- dashboard_elements
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [dashboard_elements];

CREATE TABLE [dashboard_elements]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    UNIQUE ([id])
);

-----------------------------------------------------------------------
-- dashboard_forms
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [dashboard_forms];

CREATE TABLE [dashboard_forms]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    UNIQUE ([id])
);
