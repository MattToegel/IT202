CREATE TABLE IF NOT EXISTS `Users3` (
                            `id` int auto_increment not null,
                            `email` varchar(100) not null unique,
                            `password` varchar(60) not null,
                            `date_created` timestamp not null default current_timestamp,
			    `date_modified` timestamp not null default current_timestamp on update current_timestamp,
                            PRIMARY KEY (`id`)
                            ) CHARACTER SET utf8 COLLATE utf8_general_ci
