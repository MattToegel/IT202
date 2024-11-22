CREATE TABLE SC_GuideTopics (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `topic_id` int NOT NULL,
    `guide_id` int NOT NULL,
    `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`guide_id`) REFERENCES SC_Guides (`id`),
    FOREIGN KEY (`topic_id`) REFERENCES SC_Topics (`id`),
    unique key (`guide_id`, `topic_id`)
)