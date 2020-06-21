CREATE TABLE Transactions(
    id int auto_increment,
    act_src_id int not null,
    act_dest_id int null,
    amount decimal(12,2),
    `type` varchar(10), --deposit, withdraw, transfer, etc
    memo TEXT,
    expected_total decimal (12,2)
    created datetime default current_timestamp,
    primary key (id),
    foreign key (act_src_id) references Accounts(id),
    foreign key (act_dest_id) references Accounts(id)
)