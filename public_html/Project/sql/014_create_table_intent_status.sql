CREATE TABLE
    CA_Intent_Status(
        `id` int auto_increment not null PRIMARY key,
        `label` VARCHAR(15) UNIQUE,
        `created` timestamp default current_timestamp,
        `modified` timestamp default current_timestamp on update current_timestamp
    )