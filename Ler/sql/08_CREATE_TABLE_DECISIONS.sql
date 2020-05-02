CREATE TABLE IF NOT EXISTS `Decisions` (
`id` int auto_increment not null,
`content` text not null,
`parent_id` int not null,
`created` timestamp not null default current_timestamp,
`modified` timestamp not null default current_timestamp on update current_timestamp,
`visibility` int not null default 0,
`is_active` boolean default 1,
`next_arc_id` int not null default -1,

PRIMARY KEY (`id`),
FOREIGN KEY (`parent_id`) REFERENCES Arcs(`id`) ON DELETE CASCADE
) CHARACTER SET utf8 COLLATE utf8_general_ci