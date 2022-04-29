CREATE TABLE IF NOT EXISTS RM_Inventory(
    id int AUTO_INCREMENT PRIMARY KEY,
    item_id int,
    quantity int,
    user_id int,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id),
    FOREIGN KEY (item_id) REFERENCES RM_Items(id),
    UNIQUE KEY (item_id, user_id),
    check (quantity >= 0)
)