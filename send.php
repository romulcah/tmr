<?php

	require "twitteroauth/autoload.php";
	
	include("config.php");
	include("db.php");
	
	$database = new Database();
	
	use Abraham\TwitterOAuth\TwitterOAuth;
	
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
	
	
	$database->query('SELECT id,tweet,tweet_id from tweets WHERE sent=0');
	$rows = $database->resultset();
	
	$database->beginTransaction();
	$database->query('UPDATE tweets SET sent = 1 where id = :id');
	
	foreach($rows as $tweet){

		$result = $connection->post('statuses/update', array('in_reply_to_status_id'=> $tweet['tweet_id'] ,'status' =>$tweet['tweet']));
		
		print_r($result);
	
		if($result->id > 0){
			$database->bind(':id', $tweet['id']);
			$database->execute();
		}
	}
	
	$database->endTransaction();
	
