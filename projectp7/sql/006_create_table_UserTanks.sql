create table if not exists tfp_usertanks(
    id int not null AUTO_INCREMENT,
    user_id int,
    tank_id int,
    created TIMESTAMP default CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users (id),
    FOREIGN KEY (tank_id) REFERENCES tfp_tanks (id),
    primary key(id),
    unique key (user_id, tank_id)
)