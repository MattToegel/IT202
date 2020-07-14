CREATE TABLE IF NOT EXISTS  `Responses`
(
    `id`         int auto_increment not null,
    `question_id` int,
    `user_id` int,
    `answer_id` int,
    `user_input` TEXT default '',
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES Users(`id`),
    FOREIGN KEY (`question_id`) REFERENCES Questions(`id`),
    FOREIGN KEY (`answer_id`) REFERENCES Answers(`id`)
)