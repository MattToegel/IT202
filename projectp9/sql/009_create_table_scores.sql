CREATE TABLE IF NOT EXISTS tfp_scores(
    id int auto_increment,
    user_id int,
    score int, -- win will be 1 and loss will be 0,
    created TIMESTAMP default current_timestamp,
    primary key (id),
    FOREIGN KEY (`user_id`) REFERENCES Users(`id`)
)