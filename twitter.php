<?php

	$dw = date( "w", time() );
	
	if($dw<0 && $dw>4){
		"NO RANKINGS YET";
		exit;
	}

	require "twitteroauth/autoload.php";
	
	include("config.php");
	include("db.php");
	
	$database = new Database();
	
	use Abraham\TwitterOAuth\TwitterOAuth;
	
	$tweets = array();
	
	function search(array $query)
	{
	  $toa = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
	  return $toa->get('search/tweets', $query);
	}
	 
	$query = array(
	  "q" => "@MatthewBerryTMR",
	);
	  
	$results = search($query);

	foreach ($results->statuses as $result) {

		$matches = array();
		$test = 0;
		
		
		
		$pattern= "/(\w+\s){2}or(\s\w+){2}/";
		
		$result->text = str_replace("CJ", "C.J.", $result->text);
		$result->text = str_replace("cj", "C.J.", $result->text);
		
		$subject= $result->text;

		$test = preg_match($pattern, $subject, $matches);
		
		$tweet_created=false;
			
		//$result->text = str_replace("Steve Smith", "Steve Smith Sr.", $result->text);

		//echo("... $matches[0] ...");
		
		if($test==1){
			$temp = explode(" or ",$matches[0]);
			
			$name1 = $temp[0];
			$name2 = $temp[1];
			
			echo $temp[0];
			echo $temp[1];
			
			
									
			if(strpos(strtolower($result->text), 'ppr' )>0){
				
			} else {
				
			
				$database->query('SELECT * FROM rankings WHERE name = :name');
				$database->bind(':name', $temp[0]);
				$row = $database->single();
				$player1 = $row;
				
				print_r($player1);
				
				$database->query('SELECT * FROM rankings WHERE name = :name');
				$database->bind(':name', $temp[1]);
				$row = $database->single();
				$player2 = $row;
				
				print_r($player2);
				
				echo $result->text."<br />";
			
				if(strlen($player1['name'])>5 && strlen($player2['name'])>5){
			
					if($player1['position']==$player2['position']){
						if($player1['rank']<$player2['rank']){
							$tweet_temp['tweet_id'] = $result->id;
							$tweet_temp['text'] = "@".$result->user->screen_name." Between ".$player1['name']." and ".$player2['name'].", start ".$player1['name']." per @MatthewBerryTMR rankings ".RANKINGS;
							$tweet_created=true;	
							
							$tweets[]=$tweet_temp;
						} elseif($player2['rank']<$player1['rank']) {
							$tweet_temp['tweet_id'] = $result->id;
							$tweet_temp['text'] = "@".$result->user->screen_name." Between ".$player1['name']." and ".$player2['name'].", start ".$player2['name']." per @MatthewBerryTMR rankings ".RANKINGS;	
							$tweets[]=$tweet_temp;
							$tweet_created=true;
						}		
						
						print_r($tweets);
					}
	
				}
		
			}
		}
		
		if(!$tweet_created){
		
			$pattern= "/(\w+\s){1}or(\s\w+){1}/";
					
			$subject= $result->text;
	
			$test = preg_match($pattern, $subject, $matches);
			
			if($test==1){
			
				$temp = explode(" or ",$matches[0]);
				
				$name1 = $temp[0];
				$name2 = $temp[1];
				
				echo $temp[0];
				echo $temp[1];
				
				if(strpos(strtolower($result->text), 'ppr' )>0){
				
				} else {
				
					$database->query('SELECT * FROM rankings WHERE name like :name');
					$database->bind(':name', "%".$temp[0]."%");
					$row = $database->single();
					$player1 = $row;
					
					print_r($player1);
					
					$database->query('SELECT * FROM rankings WHERE name like :name');
					$database->bind(':name', "%".$temp[1]."%");
					$row = $database->single();
					$player2 = $row;
					
					print_r($player2);
					
					echo $result->text."<br />";
				
					if(strlen($player1['name'])>5 && strlen($player2['name'])>5){
				
						if($player1['position']==$player2['position']){
							if($player1['rank']<$player2['rank']){
								$tweet_temp['tweet_id'] = $result->id;
								$tweet_temp['text'] = "@".$result->user->screen_name." Between ".$player1['name']." and ".$player2['name'].", start ".$player1['name']." per @MatthewBerryTMR rankings ".RANKINGS;	
								
								$tweets[]=$tweet_temp;
							} elseif($player2['rank']<$player1['rank']) {
								$tweet_temp['tweet_id'] = $result->id;
								$tweet_temp['text'] = "@".$result->user->screen_name." Between ".$player1['name']." and ".$player2['name'].", start ".$player2['name']." per @MatthewBerryTMR rankings ".RANKINGS;	
								$tweets[]=$tweet_temp;
							}		
							
							print_r($tweets);
						}
					}
				}
			}
		}	
	}
	
	$database->beginTransaction();
	$database->query('INSERT IGNORE INTO tweets (tweet, tweet_id) VALUES (:tweet, :tweet_id)');
	
	foreach($tweets as $tweet){
		
		$database->bind(':tweet', $tweet['text']);
		$database->bind(':tweet_id', $tweet['tweet_id']);
		$database->execute();
	}
	
	$database->endTransaction();
	
	
?>