create table tfp_responses(
    id int auto_increment,
    user_id int,
    question_id int,
    answer_id int,
    survey_id int,
    created TIMESTAMP default CURRENT_TIMESTAMP,
    modified TIMESTAMP default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    primary key (id),
    foreign key(user_id) references Users(id),
    foreign key(question_id) references tfp_questions(id),
    foreign key(answer_id) references tfp_answers(id),
    foreign key(survey_id) references tfp_surveys(id)
)