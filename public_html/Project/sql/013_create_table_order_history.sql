CREATE TABLE IF NOT EXISTS RM_OrderHistory(
    id int AUTO_INCREMENT PRIMARY KEY,
    order_id int,
    item_id int,
    quantity int,
    user_id int,
    cost int,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id),
    FOREIGN KEY (item_id) REFERENCES RM_Items(id),
    check(quantity > 0)
)