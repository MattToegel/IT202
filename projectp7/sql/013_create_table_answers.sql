
CREATE TABLE tfp_answers
(
    id        int auto_increment,
    answer  TEXT,
    question_id int,
    primary key (id),
    FOREIGN KEY (question_id) REFERENCES tfp_questions (id)
)