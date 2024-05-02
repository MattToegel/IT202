CREATE TABLE IF NOT EXISTS  `TFP-Experience`
(
    `id`         int auto_increment not null,
    `user_id` int,
    `amount` int,
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES Users(`id`)
)