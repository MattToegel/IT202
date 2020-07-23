INSERT INTO `Items` (`id`, `name`, `description`, `stat`, `modifier`, `quantity`, `cost`) VALUES
(1, 'Speed', 'Let\'s move it, move it.', 'speed', 1, 99999999, 1),
(2, 'Range', 'Over there!', 'range', 1, 99999999, 1),
(3, 'Turn Speed', 'Spin to win!', 'turnSpeed', 1, 99999999, 1),
(4, 'Armor', 'Turtle!', 'health', 1, 99999999, 1),
(5, 'Damage', 'Power up!', 'damage', 1, 99999999, 1),
(6, 'Fire Rate', 'Shavin\' seconds.', 'fireRate', 1, 99999999, 1)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    description = VALUES(description),
    stat = VALUES(stat),
    modifier = VALUES(modifier),
    quantity = VALUES(quantity),
    cost = VALUES(cost);
