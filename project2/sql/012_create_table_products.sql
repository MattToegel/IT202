CREATE TABLE F20_Products(
	id int auto_increment,
	name varchar(30) not null unique,
	description text,
	quantity int default 0,
	price int default 99999,
    modified    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP on update current_timestamp,
    created     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	primary key (id)
)
