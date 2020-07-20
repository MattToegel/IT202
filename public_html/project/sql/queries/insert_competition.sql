INSERT INTO Competitions (title, user_id, duration, expires,
first_place, second_place, third_place, entry_fee, increment_on_entry, percent_of_entry, points, min_participants)
    VALUES (:title, :user_id, :duration, :expires, :first_place, :second_place, :third_place,
    :entry_fee, :increment_on_entry, :percent_of_entry, :points, :min_participants))