CREATE TABLE IF NOT EXISTS  `TFP-Items`
(
    `id`         int auto_increment not null,
    `name`      varchar(100)       not null,
    `description` TEXT,
    `stat` varchar(20) not null,
    `modifier` int default 1,
    `quantity` int default 0,
    `cost` int default 1,
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    PRIMARY KEY (`id`)
)