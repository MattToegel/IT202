CREATE TABLE IF NOT EXISTS `Favorites` (
`id` int auto_increment not null,
`user_id` int not null,
`story_id` int not null,
`created` timestamp not null default current_timestamp,
`modified` timestamp not null default current_timestamp on update current_timestamp,

PRIMARY KEY (`id`),
FOREIGN KEY (`user_id`) REFERENCES StoryUsers(`id`) ON DELETE CASCADE,
FOREIGN KEY (`story_id`) REFERENCES  Stories(`id`) ON DELETE  CASCADE,
UNIQUE KEY(`user_id`,`story_id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci