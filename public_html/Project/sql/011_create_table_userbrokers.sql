CREATE TABLE IF NOT EXISTS  `IT202-S24-UserBrokers`
(
    `id`         int auto_increment not null,
    `user_id`    int,
    `broker_id`  int,
    `is_active`  TINYINT(1) default 1,
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES Users(`id`),
    FOREIGN KEY (`broker_id`) REFERENCES `IT202-S24-Brokers`(`id`),
    -- UNIQUE KEY (`user_id`, `broker_id`) -- this would be for many-to-many with unique pairs
    UNIQUE KEY(`broker_id`) -- this would be for many-to-one (this is for me)
)