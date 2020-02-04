DB Tool

1. mkdir BasicSQLToolSample (inside your git directory)
2. cd BasicSQLToolSample
3. mkdir sql
4. copy your config.php here
5. copy my init_db.php file here (shouldn't need to edit the code)
6. In the sql folder create all your DB structure items (create table, alter table, constraints, functions, etc)
	1. Prefix filenames so they get ksorted in the order you want them to execute
7. Invoke the init_db.php script from commandline if PDO is enabled for cli otherwise navigate to the directory on your site where it's hosted.
8. You should see the relavant output for each script ran (can also confirm via phpMyAdmin)
9. Commit your changes to github
10. (in the future) add new files here, don't edit existing working ones. This will automatically run everthing as if you're doing it step by step.
