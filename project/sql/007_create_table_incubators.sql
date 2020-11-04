CREATE TABLE F20_Incubators
(
    id        int auto_increment,
    name      varchar(30) not null,
    egg_id    int default null,
    base_rate int default 1,
    mod_min   int default 1,
    mod_max   int default 10,
    modified  timestamp, -- when egg changed
    created   timestamp, -- when user acquired
    user_id   int,       -- owner
    primary key (id),
    FOREIGN KEY (user_id) REFERENCES Users (id),
    FOREIGN KEY (egg_id) REFERENCES F20_Eggs (id)
)