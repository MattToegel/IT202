ALTER TABLE Responses
    ADD COLUMN `questionnaire_id` int,
    ADD FOREIGN KEY `questionnaire_id` REFERENCES Questionnaires(`id`) ON DELETE CASCADE