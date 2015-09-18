<?php

	include_once('simple_html_dom.php');
	include("config.php");
	include("db.php");

	$database = new Database();
	
	$qburl = "http://games.espn.go.com/ffl/tools/rankingsTable?slotCategoryId=0&seasonId=2015&scoringPeriodId=".WEEK."&html=true";
	
	$rburl = "http://games.espn.go.com/ffl/tools/rankingsTable?slotCategoryId=2&seasonId=2015&scoringPeriodId=".WEEK."&html=true";
	
	$wrurl = "http://games.espn.go.com/ffl/tools/rankingsTable?slotCategoryId=4&seasonId=2015&scoringPeriodId=".WEEK."&html=true";
	
	$teurl = "http://games.espn.go.com/ffl/tools/rankingsTable?slotCategoryId=6&seasonId=2015&scoringPeriodId=".WEEK."&html=true";
	
	$kurl = "http://games.espn.go.com/ffl/tools/rankingsTable?slotCategoryId=17&seasonId=2015&scoringPeriodId=".WEEK."&html=true";
	
	$dsturl = "http://games.espn.go.com/ffl/tools/rankingsTable?slotCategoryId=16&seasonId=2015&scoringPeriodId=".WEEK."&html=true";
	
	
	
	$database->beginTransaction();
	$database->query('TRUNCATE rankings');
	$database->execute();
	$database->endTransaction();
	
	$html = file_get_html($qburl);
	foreach($html->find('table#rankings tbody tr') as $rank) {
		$item['rank']     	= $rank->find('td', 3)->plaintext;
		$item['name']     	= $rank->find('a.flexpop', 0)->plaintext;
		$item['position']   = "qb";
		
		if($item['name']!=""){
			$rankings[] = $item;
		}
		
		echo $item['name']." ".$item['rank']."<br />";
	}
	
	$html = file_get_html($rburl);
	foreach($html->find('table#rankings tbody tr') as $rank) {
		$item['rank']     	= $rank->find('td', 3)->plaintext;
		$item['name']     	= $rank->find('a.flexpop', 0)->plaintext;
		$item['position']   = "rb";
		
		if($item['name']!=""){
			$rankings[] = $item;
		}
		
		echo $item['name']." ".$item['rank']."<br />";
	}
	
	$html = file_get_html($wrurl);
	foreach($html->find('table#rankings tbody tr') as $rank) {
		$item['rank']     	= $rank->find('td', 3)->plaintext;
		$item['name']     	= $rank->find('a.flexpop', 0)->plaintext;
		$item['position']   = "wr";
		
		if($item['name']!=""){
			$rankings[] = $item;
		}
		
		echo $item['name']." ".$item['rank']."<br />";
	}
	
	$html = file_get_html($teurl);
	foreach($html->find('table#rankings tbody tr') as $rank) {
		$item['rank']     	= $rank->find('td', 3)->plaintext;
		$item['name']     	= $rank->find('a.flexpop', 0)->plaintext;
		$item['position']   = "te";
		
		if($item['name']!=""){
			$rankings[] = $item;
		}
		
		echo $item['name']." ".$item['rank']."<br />";
	}
	
	$html = file_get_html($kurl);
	foreach($html->find('table#rankings tbody tr') as $rank) {
		$item['rank']     	= $rank->find('td', 3)->plaintext;
		$item['name']     	= $rank->find('a.flexpop', 0)->plaintext;
		$item['position']   = "k";
		
		if($item['name']!=""){
			$rankings[] = $item;
		}
		
		echo $item['name']." ".$item['rank']."<br />";
	}
	
	$html = file_get_html($dsturl);
	foreach($html->find('table#rankings tbody tr') as $rank) {
		$item['rank']     	= $rank->find('td', 3)->plaintext;
		$item['name']     	= $rank->find('a.flexpop', 0)->plaintext;
		$item['position']   = "dst";
		
		if($item['name']!=""){
			$rankings[] = $item;
		}
		
		echo $item['name']." ".$item['rank']."<br />";
	}

	$database->beginTransaction();
	$database->query('INSERT INTO rankings (name, position, rank) VALUES (:name, :position, :rank)');

	foreach($rankings as $player){
		if($player['rank']=="NR"){
			$player['rank'] = 1000;
		}
	
		$database->bind(':name', $player['name']);
		$database->bind(':position', $player['position']);
		$database->bind(':rank', $player['rank']);
		$database->execute();
		
	}
	$database->endTransaction();
	
	