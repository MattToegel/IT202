CREATE TABLE tfp_orders
(
    id         int auto_increment,
    product_id int,
    quantity   int,
    user_id    int,
    price      decimal(12, 2) default 0.00,
    created    datetime       default current_timestamp,
    modified   datetime       default current_timestamp on update current_timestamp,
    order_id int default 0,
    primary key (id),
    foreign key (product_id) references tfp_products (id),
    foreign key (user_id) references Users (id)
)