SELECT id, title, participants, expires, points, entry_fee, participants, min_participants FROM `TFP-Competitions` where calced_winner = 0 order by expires asc limit :n
