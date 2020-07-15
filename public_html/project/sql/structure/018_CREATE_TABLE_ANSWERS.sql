CREATE TABLE IF NOT EXISTS  `Answers`
(
    `id`         int auto_increment not null,
    `answer` varchar(240),
    `is_open_ended` tinyint default 0,
    `user_id` int,
    `question_id` int,
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES Users(`id`),
    FOREIGN KEY (`question_id`) REFERENCES Questions(`id`)
)