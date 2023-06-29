CREATE TABLE IF NOT EXISTS  `UserRoles`
(
    `id`         int auto_increment not null,
    `user_id`    int,
    `role_id`  int,
    `is_active`  TINYINT(1) default 1,
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES Users(`id`),
    FOREIGN KEY (`role_id`) REFERENCES Roles(`id`),
    UNIQUE KEY (`user_id`, `role_id`)
)