SELECT
        qs.id as question_id,
        qs.id as question_id,
        qs.question as question,
       q.id as questionnaire_id,
       q.name as questionnaire_name,
       q.description as questionnaire_description,
       a.id as answer_id,
       a.answer as answer,
       a.is_open_ended as open_ended
FROM `TFP-Questionnaires` as q
    JOIN `TFP-Questions` as qs on q.id = qs.questionnaire_id
    JOIN `TFP-Answers` as a on a.question_id = qs.id where q.id = :questionnaire_id
