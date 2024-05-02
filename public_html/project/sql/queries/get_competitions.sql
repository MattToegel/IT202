SELECT * FROM `TFP-Competitions` as c where date(expires) >= curdate()
AND id not in (SELECT competition_id from `TFP-UserCompetitions` where user_id = :user_id and competition_id = c.id)

order by expires desc, entry_fee asc limit 100