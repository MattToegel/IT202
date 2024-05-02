SELECT u.id, email, username, password, experience, level, points, wins, losses FROM Users u LEFT JOIN `TFP-UserStats` us on us.user_id = u.id where email = :email LIMIT 1
