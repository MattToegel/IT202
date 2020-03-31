CREATE TABLE
 IF NOT EXISTS `StoryUsers`
 (
			  `id`            INT auto_increment NOT NULL
			, `email`         VARCHAR(100) NOT NULL UNIQUE
			, `username`	  VARCHAR(30) NOT NULL UNIQUE
			, `password`      VARCHAR(60) NOT NULL
			, `created`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
			, `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
			, PRIMARY KEY (`id`)
 )
 CHARACTER SET utf8 COLLATE utf8_general_ci