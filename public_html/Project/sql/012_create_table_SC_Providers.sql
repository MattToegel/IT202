CREATE TABLE SC_Providers (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` varchar(120) NOT NULL UNIQUE,
    `domain` varchar(120) NOT NULL unique,
    `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)