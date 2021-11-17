INSERT INTO BGD_Items (id, name, description, stock, cost, image) VALUES
(-1, "2x Multiplier", "Doubles your score for one game play", 9999999, 5, ""),
(-2, "3x Multiplier", "Triples your score for one game play", 9999999, 15, ""),
(-3, "Bouncy Shots I", "Allows projectiles to bounce off side walls for one game play", 9999999, 1, ""),
(-4, "Bouncy Shots II", "Allows projectiles to bounce off side and bottom walls for one game play", 9999999, 2, ""),
(-5,"Bouncy Shots III", "Allows projectiles to bounce off all four walls for one game play", 9999999, 5, ""),
(-6, "Piercing Shots I", "Allows projectiles to hit 1 more target for one game play",9999999, 1,""),
(-7, "Piercing Shots II", "Allows projectiles to hit 2 more target for one game play",9999999, 1,""),
(-8, "Piercing Shots III", "Allows projectiles to hit 3 more target for one game play",9999999, 1,""),
(-9, "High Calibur I", "Increases the projectile size slightly for one game player", 9999999,1,""),
(-11,"High Calibur II", "Increases the projectile size moderately for one game player",9999999, 1,""),
(-12, "High Calibur III", "Increases the projectile size modestly for one game player",9999999, 1,""),
(-13, "Bouncy Shots IV", "Always allows projectiles to bounce of the side walls",9999999, 50,""),
(-14, "Bouncy Shots V", "Always allows projectiles to bounce of all walls",9999999, 100,"")
ON DUPLICATE KEY UPDATE modified = CURRENT_TIMESTAMP()

