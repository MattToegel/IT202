CREATE TABLE `IT202-S24-BattleEvents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `action` varchar(10) NOT NULL,
  `broker1_id` int NOT NULL,
  `broker2_id` int DEFAULT NULL,
  `broker1_life` int DEFAULT NULL,
  `broker2_life` int DEFAULT NULL,
  `broker1_dmg` int DEFAULT NULL,
  `broker2_dmg` int DEFAULT NULL,
  `round` int DEFAULT NULL,
  `battle_uuid` VARCHAR(20) NOT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
FOREIGN KEY (`broker1_id`) REFERENCES `IT202-S24-Brokers` (`id`),
FOREIGN KEY (`broker2_id`) REFERENCES `IT202-S24-Brokers` (`id`)
)