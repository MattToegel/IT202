CREATE TABLE F20_Eggs(
	id int auto_increment,
	name varchar(30) not null unique,
	state int default 0, -- incubating, hatching, hatched, expired
	base_rate int default 1,
	mod_min int default 1,
	mod_max int default 10,
	next_stage_time timestamp,
	user_id int,
	primary key (id),
    FOREIGN KEY (user_id) REFERENCES Users (id)
)
