CREATE TABLE IF NOT EXISTS `Arcs` (
`id` int auto_increment not null,
`title` varchar(200) not null,
`content` text not null,
`story_id` int not null,
`created` timestamp not null default current_timestamp,
`modified` timestamp not null default current_timestamp on update current_timestamp,
`visibility` int not null default 0,
`is_active` boolean default 1,

PRIMARY KEY (`id`),
FOREIGN KEY (`story_id`) REFERENCES Stories(`id`) ON DELETE CASCADE
) CHARACTER SET utf8 COLLATE utf8_general_ci