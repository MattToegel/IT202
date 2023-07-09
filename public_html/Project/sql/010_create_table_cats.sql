CREATE TABLE CA_Cats(
    `id`         int auto_increment not null,
    `name` VARCHAR(30),
    `description` TEXT,
    `breed_id` INT,
    `breed_extra` VARCHAR(60),
    `sex` VARCHAR(1),
    `fixed` TINYINT(1),
    `born` DATE,
    `weight` FLOAT,
    `status` VARCHAR(20),
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`breed_id`) REFERENCES CA_Breeds(`id`)
)