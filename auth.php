<?php


require_once("lib/OAuth.php");
require_once("common.inc.php");
require_once("db.php");

header('Content-Type: text/html; charset=UTF-8');

function getRequestToken($key,$secret,$endpoint){	
	$signal_method =  new OAuthSignatureMethod_HMAC_SHA1();
	$consumer = new OAuthConsumer($key, $secret, NULL);
	$params = array();
	$req_req = OAuthRequest::from_consumer_and_token($consumer, NULL, "GET", $endpoint, $params);

	$req_req->sign_request($signal_method, $consumer, NULL);
	$ret = file_get_contents($req_req);
	parse_str($ret,$r);
	return $r;
}

function authorize($key,$secret,$token,$token_secret,$endpoint){
	$siteurl = 'http://localhost/darlinmap/auth.php';
	$callback_url = $siteurl."?key=$key&secret=$secret&token=$token&token_secret=$token_secret&endpoint=".urlencode($endpoint);
	
	$auth_url = $endpoint . "?oauth_token=$token&oauth_callback=".urlencode($callback_url);
	
	Header("Location: $auth_url");
}

function getAccessToken($key,$secret,$token,$token_secret,$endpoint){	

	$consumer = new OAuthConsumer($key, $secret, NULL);
	$signal_method =  new OAuthSignatureMethod_HMAC_SHA1();
	$params = array();
	$access_token = new OAuthConsumer($token, $token_secret);
	$acc_req = OAuthRequest::from_consumer_and_token($consumer, $access_token, "GET", $endpoint, $params);
	$acc_req->sign_request($signal_method, $consumer, $access_token);
	$ret = file_get_contents($acc_req);
	parse_str($ret,$r);
	return $r;
}

$request_token = getRequestToken($key,$secret,$request_token_url);

if(!isset($_GET['endpoint'])){
	authorize($key,$secret,$request_token['oauth_token'],$request_token['oauth_token_secret'],$authorize_url);
}else{	
	$access_token = getAccessToken($key,$secret,@$_GET['token'],@$_GET['token_secret'],$access_token_url);
	$douban_user_id = $access_token['douban_user_id'];
	
	
	$current_user = json_decode(file_get_contents("http://api.douban.com/people/$douban_user_id?alt=json"));
	
	$theuser = array(		
		'username' 	=> $access_token['oauth_token'],
		'password' 	=> $access_token['oauth_token_secret']
	);
	
	$user = array(
		'username' 	=> $access_token['oauth_token'],
		'password' 	=> $access_token['oauth_token_secret'],
		'refid'		=> $current_user->{"db:uid"}->{"\$t"}, 
		'nickname'	=> $current_user->{"title"}->{"\$t"},
		'signature'	=> $current_user->{"db:signature"}->{"\$t"},
		'location'	=> $current_user->{"db:location"}->{"\$t"},
		'icon'		=> $current_user->{"link"}[2]->{"@href"}
	);
	
	if($user['location']!=''){		
		$geo = getLatLngByLoc($user['location']);
		$lat = $geo['lat'];
		$lng = $geo['lng'];
		$user['latlng'] = "$lat,$lng";
	}
	
	if(!$userdao->has($theuser)){
		$userdao->create($user);		
	}else{
		$userdao->update($user,$theuser);
	}
	
	
	session_start();
		
	$session = array(
		'sid' => session_id(),
		'uid' => $userdao->getOne(array('username'=>$user['username']))->id,
		'ua'  => $_SERVER['HTTP_USER_AGENT'],
		'ip'  => $_SERVER['REMOTE_ADDR']
	);
		
	if(!$sessiondao->has($session)){
		$sessiondao->create($session);
	}		
	
	setcookie('sid',session_id(),time()+3600*24*365);
	
	$sigarr=array(
		'username' => $user['username'],
		'password' => $user['password'],
		'ua' => $session['ua'],
		'ip' => $session['ip'],
		'key' => $sigkey
	);	
	
	
	setcookie('sig',md5(implode($sigarr,'')),time()+3600*24*365);
	
	header('Location:index.php');
}

?>