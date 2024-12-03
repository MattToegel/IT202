CREATE TABLE SC_UserGuides (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` int NOT NULL,
    `guide_id` int NOT NULL,
    `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`guide_id`) REFERENCES SC_Guides (`id`),
    FOREIGN KEY (`user_id`) REFERENCES Users (`id`),
    unique key (`guide_id`, `user_id`)
)