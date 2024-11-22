CREATE TABLE SC_GuideProviders (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `guide_id` int NOT NULL,
    `provider_id` int NOT NULL,
    `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`guide_id`) REFERENCES SC_Guides (`id`),
    FOREIGN KEY (`provider_id`) REFERENCES SC_Providers (`id`),
    unique key (`guide_id`, `provider_id`)
)