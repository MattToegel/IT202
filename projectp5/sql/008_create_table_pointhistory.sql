CREATE TABLE tfp_pointhistory
(
    id            int auto_increment,
    user_id       int,
    points_change int,
    reason        varchar(60), -- change size as needed since we will filter by this
    created       datetime default current_timestamp,
    primary key (id),
    foreign key (user_id) references Users (id)
)