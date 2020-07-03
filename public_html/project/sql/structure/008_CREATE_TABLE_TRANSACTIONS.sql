CREATE TABLE IF NOT EXISTS  `Transactions`
(
    `id`         int auto_increment not null,
    `user_id_src` int,
    `user_id_dest` int,
    `amount` int,
    `type` varchar(20),
    `memo` varchar(256) default '',
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id_src`) REFERENCES Users(`id`),
    FOREIGN KEY (`user_id_dest`) REFERENCES Users(`id`)
)