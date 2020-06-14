CREATE TABLE Things (
    id int auto_increment,
    name varchar(20) unique,
    quantity int default 0,
    created datetime default current_timestamp,
    modified datetime default current_timestamp onupdate current_timestamp,
    primary key (id)
)