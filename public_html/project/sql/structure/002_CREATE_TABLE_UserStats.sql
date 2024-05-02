CREATE TABLE IF NOT EXISTS  `TFP-UserStats`
(
    `id`         int auto_increment not null,
    `user_id`     int not null unique,
    `experience` BIGINT DEFAULT 0,
    `level`  int default 0,
    `points`   BIGINT DEFAULT 0,
    `wins`  int default 0,
    `losses`  int default 0,
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    PRIMARY KEY (`id`)
)