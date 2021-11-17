CREATE TABLE IF NOT EXISTS BGD_ActiveEffects(
    id int AUTO_INCREMENT PRIMARY KEY,
    item_id int,
    user_id int,
    uses int default 2147483647 COMMENT 'Uses will deduct and at 0 will remove the item, use max int for infinite uses',
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id),
    FOREIGN KEY (item_id) REFERENCES BGD_Items(id),
    UNIQUE KEY (item_id, user_id),
)