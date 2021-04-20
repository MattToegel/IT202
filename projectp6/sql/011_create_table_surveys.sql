CREATE TABLE tfp_surveys(
     id          int auto_increment,
    title       varchar(30) not null unique,
    description TEXT,
    visibility  int, -- Draft 0, Private 1, Public 2
    created TIMESTAMP default CURRENT_TIMESTAMP,
    modified TIMESTAMP default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    user_id int,
    primary key (id),
    FOREIGN KEY (user_id) REFERENCES Users (id)
)