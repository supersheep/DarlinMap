<?php
	require('../db.php');
	if(islogged()){
		$a = $user;
		$b = array('location'=>'北京','nickname'=>'萝卜','signature'=>'就不告诉你','latlng'=>'0,0','icon'=>'http://img3.douban.com/icon/user_normal.jpg');
		$arr = array($a);
		echo(json_encode($arr));
	}
?>