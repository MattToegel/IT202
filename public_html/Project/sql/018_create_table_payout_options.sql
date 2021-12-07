CREATE TABLE IF NOT EXISTS BGD_Payout_Options(
    id int AUTO_INCREMENT PRIMARY KEY,
    first_place int default 70,
    second_place int default 20,
    third_place int default 10,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    check (first_place + second_place + third_place = 100)
)