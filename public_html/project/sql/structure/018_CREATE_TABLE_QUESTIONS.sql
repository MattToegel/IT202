CREATE TABLE IF NOT EXISTS  `Questions`
(
    `id`         int auto_increment not null,
    `question` varchar(240),
    `user_id` int,
    `questionnaire_id` int,
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES Users(`id`),
    FOREIGN KEY (`questionnaire_id`) REFERENCES Questionnaire(`id`)
)