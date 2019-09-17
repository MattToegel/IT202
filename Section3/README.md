#Section 3 - Getting Started with PDO

1. Create a new file for running our sample connection
	1. __nano initDB.php__
	2. Insert part1 of the test
```
<?php
//TODO add error handling

//load the config from the same directory
require('config.php');
echo "Loaded host: " . $host;

?>
```

2. Navigate to the file in your browser assuming the default setup
	1. web.njit.edu/~yourucid/IT202/initDB.php
	2. You should see your host echoed if everything was done correctly
3. Proceed with part2, testing the connection and config details

```
//this is the same file but with extra code
<?php
//TODO add error handling

//load the config from the same directory
require('config.php');
echo "Loaded host: " . $host;

//new lines below
try{
	$conn_string = "mysql:host=$host;dbname=$databasename;charset=utf8mb4";
	$db = new PDO($conn_string, $username, $password);
	echo "Connected";
}
catch(Exception $e){
	echo $e->getMessage();
	echo "Something went wrong";
}
```
4. Navigate to the same file as step #2.
	1. You should see "Connected", if not add the following lines under "//TODO add error handling"
```

```
5. Rerun the file and see what errors are shown.
	1. Check syntax/typos
	2. Check tags
	3. Check __$username__ and __$password__ are the same that log you into web.njit.edu/mysql/phpMyAdmin
	4. Ensure items are under public_html/IT202 (or public_html/YourRepoFolderName)Each project will be split into 4 parts:App server (client side and php backend that connects to RabbitMQ)RabbitMQ server (handles the queue connections, connects to DB Server (via queues), and connects to API Server via queues)DB Server (php for queue and sql, mysql database)API Server (php for making requests to API and dealing with queues)Each project needs to cover the following:User loginPublic and Authenticated pages/parts of the appCalls to an APIDatabase to store user profiles and API cacheUser passwords should not be stored in plain textApp should only communicate with MQ, there should be no direct DB or direct API connections/consumption from the app server.Provide 3+ target features for your project beyond the above(optional) provide "reach" features you'd like to try to doThese features can only help you, if you wind up not being able to do these they won't impact your gradeProposals should include the names of all team members, a team name, a brief summary of the desired project, the API(s), and how you plan to utilize the API.Please bring in one paper copy per team so notes can be jotted down. Provide more than the minimum features in case some features aren't agreed upon for being enough weight.I'll create a separate submission for digital versions of the proposals.Example (elaborate where necessary):Type: Game ProjectGroup Name: The ExamplesMembers: John, Bill, Sarah, SteveAPI: Weather.com [no one is allowed to use any weather API so I'm using it for the sample]Objective: By utilizing the weather details from weather.com we'll be working on a game that adjusts difficulty based on the current weather for the day. Players/users will be able to visit previous levels that were generated from data from previous days similar to a random seed value.Goals:1. Random level generation based on Weather mapping2. Users can save level state if they didn't complete the level3. Users will have the ability to choose a new location for weather for future level generationReach goals:1. Multiplayer (describe)2. "Endless" play based on historical weather data until today's data that progressively gets more difficult in addition to the standard weather mapping
6. Resolve errors and run again.
