ALTER TABLE tfp_pointhistory
ADD src_id int default null,
ADD FOREIGN KEY (src_id) REFERENCES Users(id)