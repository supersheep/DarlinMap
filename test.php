<?php
	$a = explode(' ',microtime());
	echo substr(md5($a[1]+$a[0]),0,8);

?>