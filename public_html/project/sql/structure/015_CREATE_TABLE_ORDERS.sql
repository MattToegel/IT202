CREATE TABLE IF NOT EXISTS  `TFP-Orders`
(
    `id`         int auto_increment not null,
    `order_id` int default 0,
    `item_id` int,
    `user_id` int,
    `quantity` int,
    `price` int,
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`item_id`) REFERENCES `TFP-Items`(`id`),
    FOREIGN KEY (`user_id`) REFERENCES Users(`id`)
)