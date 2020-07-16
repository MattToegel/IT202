SELECT
       count(IF(date(r.created) = curdate(),true, null)) as responses_today,
       q.use_max,
       count(r.created) as responses_total,
       q.max_attempts,
       q.attempts_per_day
FROM Questionnaires as q
    join Responses r on q.id = r.questionnaire_id where q.id = :qid and r.user_id = :uid