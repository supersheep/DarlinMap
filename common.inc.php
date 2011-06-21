<?php
	
	/* db */
	
	$db_username = 'root';
	$db_password = '';
	$db_host = 'localhost';
	$db_dbname = 'darlinmap';
	
	
	/* douban oauth */
	$key = '0a9ed993615b782e1fdc8cba2412688a';
	$secret = '382949d23d5d6c72';
	$baseurl = 'http://www.douban.com/service/auth/';


	$request_token_url = $baseurl.'request_token';
	$authorize_url = $baseurl.'authorize';
	$access_token_url = $baseurl.'access_token';
	
	/* login signature */
	$sigkey = 'whereareyou';
	
	
	
	
	function getLatLngByLoc($loc){
		
		$geo = json_decode(file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=$loc&sensor=true"));
		$lat = $geo->results[0]->geometry->location->lat;
		$lng = $geo->results[0]->geometry->location->lng;
		return array('lat'=>$lat,'lng'=>$lng);
	}
	
	function getLocationById($id){
	
		$loc = array();
	
		$doubaninfo = file_get_contents("http://api.douban.com/people/$id?alt=json");
		$doubaninfo = json_decode($doubaninfo);
		
		$loc['location'] = $doubaninfo -> {'db:location'} -> {'$t'};
		$loc['latlng'] = getLatLngByLoc($loc['location']);
		return $loc;
	}
	
?>