SELECT s.*, su.username, count(f.user_id) as total FROM Stories s JOIN Favorites f on s.id = f.story_id JOIN StoryUsers su on f.user_id = su.id
WHERE visibility = 2 and is_active = 1 and starting_arc > -1
group by f.story_id, su.username order by total DESC LIMIT 10