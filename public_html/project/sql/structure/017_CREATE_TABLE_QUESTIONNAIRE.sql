CREATE TABLE IF NOT EXISTS  `Questionnaire`
(
    `id`         int auto_increment not null,
    `name` varchar(120),
    `description` TEXT,
    `user_id` int,
    `attempts_per_day` int default 1,
    `max_attempts` int default 1,
    `use_max` tinyint default 0,
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES Users(`id`)
)