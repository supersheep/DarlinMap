<?php
	require_once('common.inc.php');
	require_once('lib/OAuth.php');
	require_once('db.php');
	
	function say($word){
		$data = <<<XML
		<?xml version='1.0' encoding='UTF-8'?>
		<entry xmlns:ns0="http://www.w3.org/2005/Atom" xmlns:db="http://www.douban.com/xmlns/">
		<content>$word</content>
		</entry>
XML;
	
		return sigpost('http://api.douban.com/miniblog/saying',$data);
	}

	
	function sendmail($to,$title,$content,$captcha_id=null,$captcha_string=null){
		$data = <<<XML
		<?xml version="1.0" encoding="UTF-8"?>
		<entry xmlns="http://www.w3.org/2005/Atom" xmlns:db="http://www.douban.com/xmlns/" xmlns:gd="http://schemas.google.com/g/2005" xmlns:opensearch="http://a9.com/-/spec/opensearchrss/1.0/">
			<db:entity name="receiver">
				<uri>http://api.douban.com/people/$to</uri>
			</db:entity>
			<content>$content</content>
			<title>$title</title>		
XML;
		if(isset($captcha_id)&&isset($captcha_string)){
			$data .= <<<XML
		<db:attribute name="captcha_token">$captcha_id</db:attribute>
        <db:attribute name="captcha_string">$captcha_string</db:attribute>
XML;
		}
		$data.='</entry>';	
		
		return sigpost('http://api.douban.com/doumails',$data);
	}
	
	function invite($to,$title,$content,$captcha_id=null,$captcha_string=null){
		global $user,$invitedao;		
		$uid = $user->id;
		$code = md5(time(),true);
		$content = "$content\n"."http://".$_SERVER['HTTP_HOST']."/darlinmap/invite.php?code=".$code;
		
		//echo $content;
		$re = sendmail($to,$title,$content,$captcha_id,$captcha_string);		
		if($re == 'ok'){
			$invitedao->create(array(
				'uid'=>$uid,			
				'code'=>$code,
				'status'=>0,
			));			
			return $code;
		} else{
			return $re;
		}
	}
	
	
	
	
?>