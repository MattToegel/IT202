CREATE TABLE SC_Guides (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `path` text NOT NULL unique,
    `title` varchar(300) not null UNIQUE,
    `excerpt` text,
    `srcUrl` text,
    `webUrl` text,
    `originalUrl` text,
    `featuredContent` text,
    `publishedDateTime` TIMESTAMP,
    `type` VARCHAR(15) not null,
    `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)