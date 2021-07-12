CREATE TABLE IF NOT EXISTS Pending_Rocks(
    -- historical data for rock options, chosen rock will be copied to Rocks table
    id int AUTO_INCREMENT PRIMARY KEY ,
    time_to_mine int DEFAULT 30 COMMENT 'Number of days to mine',
    potential_reward int DEFAULT 0 COMMENT 'Value given on successful mine',
    percent_chance FLOAT DEFAULT 0 COMMENT 'Chance to get the reward',
    owned_by int comment 'User id the choice belongs to, same as Batches.user_id',
    chosen_date TIMESTAMP DEFAULT null COMMENT 'When it was picked, only 1 out of the choices for a single batch can be not null',
    batches_id int COMMENT 'The batch or options this rock belongs to',
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,
    modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
    FOREIGN KEY (owned_by) REFERENCES Users(id),
    FOREIGN KEY (batches_id) REFERENCES Batches(id)
)