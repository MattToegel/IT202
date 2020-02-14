CREATE TABLE IF NOT EXISTS `UserRoles` (
`id` int auto_increment not null,
`role_id` int not null,
`user_id` int not null,
`date_created` timestamp not null default current_timestamp,
`date_modified` timestamp not null default current_timestamp on update current_timestamp,
`is_active` boolean default 1,
PRIMARY KEY (`id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci
 
