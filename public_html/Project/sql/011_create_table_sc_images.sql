CREATE TABLE SC_Images (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `url` varchar(500) NOT NULL unique,
    `width` int default 512,
    `height` int default 512,
    `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)