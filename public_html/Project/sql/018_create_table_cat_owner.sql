CREATE TABLE
    CA_Cat_Owner(
        `id` int auto_increment not null PRIMARY key,
        `cat_id` int,
        `owner_id` int,
        `intent_id` int,
        `created` timestamp default current_timestamp,
        `modified` timestamp default current_timestamp on update current_timestamp,
        FOREIGN KEY (`cat_id`) REFERENCES CA_Cats(`id`),
        FOREIGN KEY(`owner_id`) REFERENCES Users(`id`),
        FOREIGN KEY(`intent_id`) REFERENCES CA_Intents(`id`),
        unique key (`owner_id`, `cat_id`)
    )