SELECT *
FROM (
        SELECT *, (
                attempts_per_day > (
                    SELECT COUNT(1)
                    FROM `TFP-Responses`
                    where
                        user_id = 1
                        and questionnaire_id = q.id
                        and date(created) = CURDATE()
                )
                and q.use_max = 0
            )
            or (
                q.use_max = 1
                and q.max_attempts > (
                    select COUNT(1)
                    from `TFP-Responses`
                    where
                        user_id = 1
                        and questionnaire_id = q.id
                )
            ) as available
        from `TFP-Questionnaires` as q
    ) as T
WHERE
    available > 0