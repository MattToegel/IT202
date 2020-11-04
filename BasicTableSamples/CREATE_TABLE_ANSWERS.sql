CREATE TABLE Questions
(
    id          int auto_increment,
    answer      TEXT,
    question_id int,
    is_correct  tinyint default 0,
    primary key (id),
    FOREIGN KEY (question_id) REFERENCES Questions (id)
)