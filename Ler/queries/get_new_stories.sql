SELECT s.*, su.username FROM Stories s JOIN StoryUsers su on s.author = su.id
WHERE visibility = 2 and is_active = 1 and starting_arc > -1
order by created DESC LIMIT 10