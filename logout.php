<?php
	require('db.php');
	session_start();
	$sessiondao->delete(array('sid'=>session_id()));
	setcookie('sid','',time()-1);
	setcookie('sig','',time()-1);
	header('Location:index.php');
?>