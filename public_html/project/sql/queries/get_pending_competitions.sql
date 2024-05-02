SELECT id, title, points, first_place, second_place, third_place, participants, min_participants
FROM `TFP-Competitions`
where
    expires <= curdate()
    and calced_winner = 0