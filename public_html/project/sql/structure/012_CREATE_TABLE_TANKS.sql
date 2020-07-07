CREATE TABLE IF NOT EXISTS  `Tanks`
(
    `id`         int auto_increment not null,
    `name`      varchar(100)       not null,
    `speed` int default 50,
    `range` int default 100,
    `turnSpeed` int default 25,
    `fireRate` int default 10,
    `health` int default 3,
    `tankColor` varchar(7) default '#05652D',
    `barrelColor` varchar(7) default '#034820',
    `barrelTipColor` varchar(7) default '#023417',
    `treadColor`    varchar(7) default '#000000',
    `hitColor` varchar(7) default '#A2082B',
    `gunType` int default 1,
    `user_id` int,
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES Users(`id`)
)