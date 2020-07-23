SELECT * FROM Competitions where date(expires) >= curdate()
AND id not in (SELECT competition_id from UserCompetitions where user_id = :user_id and competition_id = Competitions.id)
order by expires desc, entry_fee asc limit 100