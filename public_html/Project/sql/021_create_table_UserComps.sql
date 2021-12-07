CREATE TABLE BGD_UserComps(
    id int AUTO_INCREMENT PRIMARY KEY,
    user_id int,
    competition_id int,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    unique key(user_id, competition_id),
    FOREIGN KEY (user_id) REFERENCES Users(id),
    FOREIGN KEY (competition_id) REFERENCES BGD_Competitions(id)
)