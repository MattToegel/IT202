ALTER TABLE BGD_Items ADD COLUMN uses int 
default 2147483647
COMMENT 'Number of uses the item has, max int for infinite';