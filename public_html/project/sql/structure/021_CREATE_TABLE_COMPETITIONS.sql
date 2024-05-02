CREATE TABLE IF NOT EXISTS  `TFP-Competitions`
(
    `id`         int auto_increment not null,
    `title` varchar(240),
    `user_id` int,
    `participants` int default 0,
    `duration` int default 7,
    `expires` timestamp,
    `first_place` float default 0,
    `second_place` float default 0,
    `third_place` float default 0,
    `entry_fee` int default 0,
    `increment_on_entry` tinyint default 0,
    `percent_of_entry` float default 0.5,
    `points` int default 0,
    `min_participants` int default 3,
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES Users(`id`)
)