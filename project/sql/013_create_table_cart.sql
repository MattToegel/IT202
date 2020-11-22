CREATE TABLE F20_Cart(
	id int auto_increment,
	product_id int,
	user_id int,
	quantity int default 1,
	price int,
    modified    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP on update current_timestamp,
    created     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	primary key (id),
    foreign key (user_id) references Users(id) on delete cascade ,
    foreign key (product_id) references F20_Products(id) on delete cascade ,
    unique key (user_id, product_id)
)
