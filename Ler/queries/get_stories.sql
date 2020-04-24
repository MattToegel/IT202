SELECT * from Stories s JOIN StoryUsers ss on s.author = ss.id where s.title
like CONCAT('%', :title, '%') AND ss.username like CONCAT('%', :username, '%')