INSERT INTO RM_Items (id, name, description, stock, cost, image) VALUES
(-1, "Rope", "Saves from one Pit Fall", 9999999, 5, ""),
(-2, "Wolf Deterent 25%", "Has a chance to prevent a wolf from appearing during a game session", 9999999, 15, ""),
(-3, "First Aid Kit 1", "Gain some extra points for the next rescued friend", 9999999, 1, ""),
(-4, "First Aid Kit 2", "Gain moderate extra points for the next rescued friend", 9999999, 2, ""),
(-5, "First Aid Kit 3", "Gain large amount of extra points for the next rescued friend", 9999999, 5, ""),
(-6, "Wolf Deterent 50%", "Has a chance to prevent a wolf from appearing during a game session",9999999, 25,"")
ON DUPLICATE KEY UPDATE modified = CURRENT_TIMESTAMP()