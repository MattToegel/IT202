CREATE TABLE IF NOT EXISTS Inventory(
    id int AUTO_INCREMENT PRIMARY KEY ,
    item_id int COMMENT 'Reference to shop item',
    quantity int DEFAULT 0,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,
    modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    user_id int,
    FOREIGN KEY (user_id) REFERENCES Users(id),
    FOREIGN KEY (item_id) REFERENCES Items(id),
    unique key(item_id, user_id)
)