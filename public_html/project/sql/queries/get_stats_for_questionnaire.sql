SELECT r.question_id, question,r.question_id, answer, grouping(r.answer_id) `group`, count(r.user_id) total
from
    `TFP-Questions` q
    join `TFP-Answers` a on q.id = a.question_id
    join `TFP-Responses` r on r.answer_id = a.id
where r.questionnaire_id = :qid and q.questionnaire_id = :qid group by r.question_id, r.answer_id with ROLLUP