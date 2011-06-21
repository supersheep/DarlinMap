<?php
	
	require_once('../doubanapis.php');
	
	$action = @$_GET['action'];
	
	
	$content = @$_POST['content'];
	$title = @$_POST['title'];
	$to = @$_POST['to'];
	
	
	
	
	
	if(islogged()){	
		switch($action){		
			case 'say': echo say($content);
			case 'sendmail': echo sendmail($to,$title,$content);
			case 'invite' : echo invite($to,$title,$content);
		}
	
	}else{
		echo "{code:400,msg:not logged}";
	}
	

?>