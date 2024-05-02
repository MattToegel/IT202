SELECT amount, type, memo, created from `TFP-Transactions` where user_id_src = :uid order by created desc limit 50
