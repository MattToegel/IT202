CREATE TABLE CA_Temperaments(
    `id`         int auto_increment not null,
    `name` VARCHAR(30),
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    PRIMARY KEY (`id`),
    UNIQUE KEY(`name`)
)