<?php
	$id = @$_GET['id'];
	$start = @$_GET['start']||1;
	if(isset($id)){
		$str = "http://api.douban.com/people/$id/contacts?alt=json&start-index=$start";
		echo file_get_contents($str);
	}else{
		echo 'param id not given';
	}
?>