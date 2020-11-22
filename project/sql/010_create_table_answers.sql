CREATE TABLE IF NOT EXISTS F20_Answers
(
    id          int auto_increment,
    answer      varchar(120) not null,

    modified    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP on update current_timestamp,
    created     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    question_id int,
    primary key (id),
    FOREIGN KEY (question_id) REFERENCES F20_Questions (id)
)