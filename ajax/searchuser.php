<?php
	
	$q = $_GET["id"];
	$ret = array();		
	
	
	$a = @file_get_contents("http://api.douban.com/people/$q?alt=json"); 
	if(!$a){
		$a = file_get_contents("http://api.douban.com/people?q=$q&alt=json");	
		$a = json_decode($a);
		
		$userarray = $a -> entry;
	}else{
		$a = json_decode($a);

		
		$userarray = array($a);	
	}
	
	
	
	foreach( $userarray as $u){
		$user = array(
			'uid' => $u->{'db:uid'}->{'$t'},
			'nickname'=> $u->{'title'}->{'$t'},
			'icon' => $u->{'link'}[2]->{'@href'}
		);	
		array_push($ret,$user);		
	} 
		 
	
	echo json_encode($ret);	
?>