CREATE TABLE IF NOT EXISTS RM_Scores(
    id int AUTO_INCREMENT PRIMARY KEY,
    score int DEFAULT 0,
    level int DEFAULT 0,
    rescued int DEFAULT 0,
    user_id int,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id),
    check (score > 0),
    check (level > 0),
    check (rescued >= 0)
)