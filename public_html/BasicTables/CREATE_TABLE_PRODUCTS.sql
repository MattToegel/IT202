CREATE TABLE Products{
    id int auto_increment,
    name varchar(60) NOT NULL unique,
    quantity int default  0,
    price decimal(10,2) default  0.00,
    description TEXT,
    modified datetime default current_timestamp  onupdate current_timestamp ,
    created datetime default  current_timestamp,
    user_id int,
    primary key (id),
    foreign key(user_id) references Users.id --manager role for example
}