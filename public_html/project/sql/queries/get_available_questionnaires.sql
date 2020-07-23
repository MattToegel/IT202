SELECT * from Questionnaires as q where
(attempts_per_day > (SELECT COUNT(1)
FROM Responses where user_id = :uid and questionnaire_id = q.id and date(created) = CURDATE())
and
q.use_max = 0)
or
(q.use_max = 1 and q.max_attempts > (select COUNT(1) from Responses where user_id = :uid and questionnaire_id = q.id))