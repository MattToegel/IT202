CREATE TABLE CA_CatImages(
    `id`         int auto_increment not null,
    `cat_id` INT,
    `image_id` INT,
    `is_active` TINYINT(1) DEFAULT 1,
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`cat_id`) REFERENCES CA_Cats(`id`),
    FOREIGN KEY(`image_id`) REFERENCES CA_Images(`id`),
    UNIQUE KEY(`cat_id`, `image_id`)
)