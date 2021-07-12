ALTER TABLE Accounts
ADD COLUMN quarry_vouchers int default 0
comment 'Purchased in shop, provides rock selection'