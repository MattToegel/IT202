CREATE TABLE Responses(
    id int auto_increment,
    question_id int,
    answer_id int,
    user_id int,
    primary key(id),
    foreign key(question_id) REFERENCES Questions(id),
    foreign key(answer_id) REFERENCES Answers(id),
    foreign key(user_id) REFERENCES Users(id)
)