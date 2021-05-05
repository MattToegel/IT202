
CREATE TABLE tfp_questions
(
    id        int auto_increment,
    question  TEXT,
    survey_id int,
    primary key (id),
    FOREIGN KEY (survey_id) REFERENCES tfp_surveys (id)
)