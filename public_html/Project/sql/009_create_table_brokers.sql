CREATE TABLE `IT202-S24-Brokers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL, -- keeping varchar so I can change individual broker instead of all references to name
  `rarity` int NOT NULL,
  `life` int DEFAULT NULL,
  `power` int DEFAULT NULL, -- attack power
  `defense` int DEFAULT NULL,
  `stonks` int DEFAULT NULL, -- total battle power of broker
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `rarity_range` CHECK (((`rarity` >= 1) and (`rarity` <= 10))),
  CONSTRAINT `life_min` CHECK ((`life` >= 0)),
  CONSTRAINT `power_min` CHECK ((`power` >= 0)),
  CONSTRAINT `defense_min` CHECK ((`defense` >= 0)),
  CONSTRAINT `stonks_min` CHECK ((`stonks` >= 0))
)