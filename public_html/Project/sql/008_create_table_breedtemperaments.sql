CREATE TABLE CA_BreedTemperaments(
    `id`         int auto_increment not null,
    `breed_id` int,
    `temperament_id` int,
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`breed_id`) REFERENCES CA_Breeds(`id`),
    FOREIGN KEY (`temperament_id`) REFERENCES CA_Temperaments(`id`),
    UNIQUE KEY (`breed_id`, `temperament_id`)
)