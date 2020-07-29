SELECT
    question_id,
    (select question from Questions where id = question_id limit 1) as question,
    answer_id,
    (select answer from Answers where id = answer_id limit 1) as answer,
    count(user_id) as total from
                                 (SELECT r.question_id, r.answer_id, a.answer, r.user_id from Answers a
                                     left join Responses r on a.id = r.answer_id where r.questionnaire_id = :qid order by r.question_id) as T
group by answer_id order by question_id