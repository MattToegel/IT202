CREATE TABLE IF NOT EXISTS Points_History(
    -- this will be like the bank project transactions table (pairs of transactions)
    id int AUTO_INCREMENT PRIMARY KEY ,
    account_src int,
    account_dest int,
    point_change int,
    reason varchar(15) not null COMMENT 'The type of transaction that occurred',
    memo varchar(240) default null COMMENT  'Any extra details to attach to the transaction',
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)