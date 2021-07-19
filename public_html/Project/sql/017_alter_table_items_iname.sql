ALTER TABLE Items
-- Nice detail about MySQL 8 https://dev.mysql.com/doc/refman/8.0/en/data-type-defaults.html
ADD COLUMN iname varchar(30) default (LOWER(REPLACE(name, ' ', '_'))) COMMENT 'Internal name for code usage' 