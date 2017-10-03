
-----------------------------------------------------------------------
-- element
-----------------------------------------------------------------------

DROP TABLE IF EXISTS [element];

CREATE TABLE [element]
(
    [id] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    [type] VARCHAR(31) NOT NULL,
    [label] VARCHAR(8191) NOT NULL,
    [active] INTEGER DEFAULT 1 NOT NULL,
    [administrative] INTEGER DEFAULT 0 NOT NULL,
    [short_name] VARCHAR(31) NOT NULL,
    [initial_value] VARCHAR(127),
    [help_text] VARCHAR(4095),
    [placeholder_text] VARCHAR(127),
    [choices] VARCHAR(4095),
    [dependent_upon] VARCHAR(127),
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
