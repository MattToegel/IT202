CREATE TABLE Cart{
    id int auto_increment,
    product_id int,
    quantity int,
    user_id int,
    created datetime default current_timestamp ,
    modified datetime default current_timestamp onupdate current_timestamp,
    primary key(id),
    foreign key(product_id) references Products.id,
    foreign key(user_id) references Users.id
}