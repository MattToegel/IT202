SELECT user_id, count(1) as wins
FROM `TFP-Experience`
where
    type = 'win'
group by
    user_id
order by wins desc
limit 10