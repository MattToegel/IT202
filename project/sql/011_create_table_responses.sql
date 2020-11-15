CREATE TABLE IF NOT EXISTS F20_Responses
(
    id          int auto_increment,
    survey_id   int,
    question_id int,
    answer_id   int,
    modified    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP on update current_timestamp,
    user_id     int,
    primary key (id),
    FOREIGN KEY (user_id) REFERENCES Users (id),
    FOREIGN KEY (question_id) REFERENCES F20_Questions (id),
    FOREIGN KEY (answer_id) REFERENCES F20_Answers (id),
    FOREIGN KEY (survey_id) REFERENCES F20_Surveys (id),
    UNIQUE KEY (user_id, question_id, answer_id, survey_id)
)