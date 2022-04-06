CREATE TABLE IF NOT EXISTS RM_Gem_History(
    -- this will be like the bank project transactions table (pairs of transactions)
    id int AUTO_INCREMENT PRIMARY KEY ,
    src int,
    dest int,
    diff int,
    reason varchar(15) not null COMMENT 'The type of transaction that occurred',
    details varchar(240) default null COMMENT  'Any extra details to attach to the transaction',
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    FOREIGN KEY (src) REFERENCES RM_Accounts(id),
    FOREIGN KEY(dest) REFERENCES RM_Accounts(id),
    constraint ZeroTransferNotAllowed CHECK(diff != 0)
)