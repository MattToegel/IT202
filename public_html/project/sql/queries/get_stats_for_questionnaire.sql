SELECT question,r.question_id, answer, grouping(r.answer_id) `group`, count(r.user_id)
from Questions q join Answers a on q.id = a.question_id join Responses r on r.answer_id = a.id
where r.questionnaire_id = :qid and q.questionnaire_id = :qid group by r.question_id, r.answer_id with ROLLUP