SELECT
    count(IF(date(r.created) = curdate(),true, null)) as responses_today,
    q.use_max,
    count(r.created) as responses_total,
    q.max_attempts,
    q.attempts_per_day
FROM
    (SELECT id, use_max, max_attempts, attempts_per_day FROM `TFP-Questionnaires` WHERE id = :qid ) as q
        left join
    (SELECT questionnaire_id, created FROM `TFP-Responses` where user_id = :uid group by questionnaire_id) as r

    on q.id = r.questionnaire_id