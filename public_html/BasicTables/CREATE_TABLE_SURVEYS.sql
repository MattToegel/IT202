CREATE TABLE Survey(
    id int auto_increment,
    title varchar(30) not null unique,
    description TEXT,
    visibility int, -- DRAFT 0, Private 1, Publish 2
    primary key (id)
)