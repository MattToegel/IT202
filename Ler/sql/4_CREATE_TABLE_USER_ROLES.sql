CREATE TABLE IF NOT EXISTS `UserRoles` (
`id` int auto_increment not null,
`role_id` int not null,
`user_id` int not null,
`created` timestamp not null default current_timestamp,
`modified` timestamp not null default current_timestamp on update current_timestamp,
`is_active` boolean default 1,
PRIMARY KEY (`id`),
FOREIGN KEY (`role_id`) REFERENCES Roles(`id`) ON DELETE CASCADE,
FOREIGN KEY (`user_id`) REFERENCES StoryUsers(`id`) ON DELETE CASCADE
) CHARACTER SET utf8 COLLATE utf8_general_ci