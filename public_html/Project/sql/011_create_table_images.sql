CREATE TABLE CA_Images(
    `id`         int auto_increment not null,
    `api_breed_id` VARCHAR(10) COMMENT 'Breed id from the API',
    `breed_id` INT,
    `api_id` VARCHAR(10) COMMENT 'Image id from the API',
    `url` varchar(100),
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`breed_id`) REFERENCES CA_Breeds(`id`),
    UNIQUE KEY(`api_id`, `api_breed_id`)
)