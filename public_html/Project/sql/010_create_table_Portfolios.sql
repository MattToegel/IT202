CREATE TABLE `IT202-S24-Portfolios`(
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `broker_id` int DEFAULT NULL,
    `symbol` varchar(10) NOT NULL,
    `shares` int DEFAULT '1',
    `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_sym` (`broker_id`, `symbol`),
    FOREIGN KEY (`broker_id`) REFERENCES `IT202-S24-Brokers` (`id`),
    CONSTRAINT `shares_min` CHECK (`shares` > 0)
)