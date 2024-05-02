SELECT *
FROM `TFP-Competitions`
where
    date(expires) <= curdate()
    and calced_winner = 0