CREATE TABLE IF NOT EXISTS Batches(
    id int AUTO_INCREMENT primary key,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,
    modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
    user_id int,
    made_choice boolean not null default 0, -- secretly tinyint(1)
    FOREIGN KEY (user_id) REFERENCES Users(id)
)