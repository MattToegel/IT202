UPDATE `TFP-Tanks` set speed = :speed, `range` = :range, turnSpeed = :turnSpeed, fireRate = :fireRate, health = :health, damage=:damage

where id = :id AND user_id = :uid