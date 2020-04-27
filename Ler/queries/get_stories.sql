SELECT s.id as story_id, s.title, s.author, s.created, s.modified, s.visibility, s.starting_arc, ss.username from Stories s
JOIN StoryUsers ss on s.author = ss.id where
is_active = 1 AND visibility = 2 AND
s.title like CONCAT('%', :title, '%') AND ss.username like CONCAT('%', :username, '%')