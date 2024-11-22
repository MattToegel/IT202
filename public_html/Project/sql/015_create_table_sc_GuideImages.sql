CREATE TABLE SC_GuideImages (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `guide_id` int NOT NULL,
    `image_id` int NOT NULL,
    `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`guide_id`) REFERENCES SC_Guides (`id`),
    FOREIGN KEY (`image_id`) REFERENCES SC_Images (`id`),
    unique key (`guide_id`, `image_id`)
)