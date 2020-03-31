CREATE TABLE IF NOT EXISTS `Roles` (
`id` int auto_increment not null,
`name` varchar(100) not null unique,
`created` timestamp not null default current_timestamp,
`modified` timestamp not null default current_timestamp on update current_timestamp,
`is_active` boolean default 1,
PRIMARY KEY (`id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci
 
