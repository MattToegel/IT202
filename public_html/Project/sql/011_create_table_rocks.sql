CREATE TABLE IF NOT EXISTS Rocks(
    -- chosen/accepted rocks in active "play"
    -- chose to copy data here so there could potentially be a difference or change
    -- based on item/worker factors. In most use-cases you'd have the common data referenced.
    id int AUTO_INCREMENT PRIMARY KEY,
    -- data from pending rocks
    time_to_mine int DEFAULT 30 COMMENT 'Number of days to mine',
    potential_reward int DEFAULT 0 COMMENT 'Value given on successful mine',
    percent_chance FLOAT DEFAULT 0 COMMENT 'Chance to get the reward',
    owned_by int comment 'User id the choice belongs to, same as Batches.user_id',
    batches_id int COMMENT 'The batch or options this rock belongs to',
    -- data for active rock
    opens_date TIMESTAMP DEFAULT null COMMENT 'The date the potential reward is evaluated',
    is_mining boolean DEFAULT 0 COMMENT 'helper/cache for open_date is not null',
    opened_date TIMESTAMP DEFAULT null COMMENT  'When the rock was opened/reward paid',
    given_reward int DEFAULT 0,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,
    modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
    FOREIGN KEY (owned_by) REFERENCES Users(id),
    FOREIGN KEY (batches_id) REFERENCES Batches(id)
)