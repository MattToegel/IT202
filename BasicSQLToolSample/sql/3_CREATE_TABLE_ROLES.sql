CREATE TABLE IF NOT EXISTS `Roles` (
`id` int auto_increment not null,
`role_name` varchar(100) not null unique,
`date_created` timestamp not null default current_timestamp,
`date_modified` timestamp not null default current_timestamp on update current_timestamp,
`is_active` boolean default 1,
PRIMARY KEY (`id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci
 
