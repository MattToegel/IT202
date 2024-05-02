CREATE TABLE IF NOT EXISTS  `TPF-UserCompetitions`
(
    `id`         int auto_increment not null,
    `competition_id` int,
    `user_id` int,
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES Users(`id`),
    FOREIGN KEY (`competition_id`) REFERENCES `TFP-Competitions`(`id`),
    UNIQUE KEY `user_comp` (`user_id`, `competition_id`)
)