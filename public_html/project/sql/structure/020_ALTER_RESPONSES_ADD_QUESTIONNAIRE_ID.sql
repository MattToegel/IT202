ALTER TABLE `TFP-Responses`
    ADD COLUMN `questionnaire_id` int,
    ADD FOREIGN KEY (`questionnaire_id`) REFERENCES `TFP-Questionnaires`(`id`) ON DELETE CASCADE