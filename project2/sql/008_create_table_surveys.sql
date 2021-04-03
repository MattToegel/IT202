CREATE TABLE IF NOT EXISTS F20_Surveys
(
    id               int auto_increment,
    name             varchar(30) not null,
    description      text,
    attempts_per_day int                  default 1,
    use_max          tinyint              default 0,
    max_attempts     int                  default 1,
    modified         TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP on update current_timestamp,
    created          TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    user_id          int,
    primary key (id),
    FOREIGN KEY (user_id) REFERENCES Users (id)
)