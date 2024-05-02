SELECT * FROM (
      SELECT competition_id,
             e.user_id, count(1) as wins,
             RANK() OVER (PARTITION BY competition_id ORDER BY count(uc.user_id) DESC) AS rnk
From
    `TFP-UserCompetitions` uc
    join `TFP-Competitions` c on c.id = uc.competition_id
    join `TFP-Experience` e on uc.user_id = e.user_id
      where competition_id IN(?)
        and e.type = 'win' and e.created BETWEEN c.created and c.expires
      group by competition_id, e.user_id
      order by competition_id asc, wins desc
              ) as T
where rnk <= 10