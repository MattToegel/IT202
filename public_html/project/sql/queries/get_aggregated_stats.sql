SELECT XP, Points, Wins, Losses From
    (SELECT 1 as t, SUM(amount) as XP, SUM(type='win') as Wins, SUM(type='loss') as Losses from Experience where user_id = :uid) as x
        JOIN
    (SELECT 1 as t, SUM(amount) as Points from Transactions where user_id_src = :uid) as p
    ON x.t = p.t