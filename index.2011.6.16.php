<?php
	require_once("db.php");	
	$logged = islogged();
	if(isset($user)){
		$currentuser = array(
			'nickname' => $user->nickname,
			'refid'	=>	$user->refid,
			'location' => $user->location,
			'signature'=> $user->signature,
			'latlng' => $user->latlng,
			'icon' => $user->icon,
		);	
	}else{
		$currentuser = '';
	}
?>


<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>DarlinMap</title>
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
	<script type="text/javascript" src="s/jquery.js"></script>
	<script type="text/javascript" src="s/darlinmap.js"></script>
	<script type="text/javascript">
		var currentuser = <?php echo json_encode($currentuser); ?>;
		
		//dm.douban.sendmail('52114966',prompt('title'),'ceshiyoujian');
		//dm.douban.say('test');
		
		var GMap = {};
		GMap.initialize = function() {
			var latlng = new google.maps.LatLng(30,150);
			var myOptions = {
				zoom: 2,
				center: latlng,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			GMap.map = new google.maps.Map(document.getElementById("map"),myOptions);
		}
		
		$(document).ready(function(){
			GMap.initialize();
			$.get('ajax/friends.php?'+(+ new Date),function(e){
				var o = $.parseJSON(e);
				if(o){
					var ul = $('.darlinlist ul');
					for(var i = 0 , l=o.length ; i<l ; i++ ){					
						var li = $('<li></li>');
						var img = $('<img />');
						img.attr('alt',o[i].nickname).attr('title',o[i].nickname).attr('src',o[i].icon);
						li.append(img);
						ul.append(li);
						var mapicon = new google.maps.MarkerImage(o[i].icon,new google.maps.Size(20,20),new google.maps.Point(0,0),new google.maps.Point(0,10),new google.maps.Size(20,20));
						var latlng = new google.maps.LatLng(o[i].latlng.split(',')[0],o[i].latlng.split(',')[1]);
						var beachMarker = new google.maps.Marker({
							position: latlng,
							map: GMap.map,
							icon: mapicon
						});				
					}					
				}
			});
			
			
			$('.pop').click(function(){
				var rel = $(this).attr('rel');
				var total;
				var pannel = $('#' + rel );
				if(pannel.hasClass('hide')){
					pannel.removeClass('hide');
				}else{
					return false;
				}
				
				if(rel == 'invite'){
					pannel.find('.btn').html('邀请他们').removeClass('disable');
					
					pannel.find('#sendinvite').click(function(){
						if(!$(this).hasClass('disable')){
							$(this).addClass('disable').html('正在发送');
							var selections=[];
							pannel.find('.selected').each(function(){
								selections.push($(this).attr('ref'));
							});
							console.log(selections);
						}else{
							return false;
						}
					});
					
					$.get('ajax/contacts.php?id='+currentuser.refid,function(e){
						var o = $.parseJSON(e),
							total = total || o['openSearch:totalResults']['$t'],
							entry = o.entry,
							ul,li,img,p,checkbox,
							i=0,l = o.entry.length,
							thepeople,
							theicon;
						
						ul = $('<ul class="clear-fix"></ul>');
						for(; i < l ; i++ ){
							li = $('<li></li>').addClass('people');
							thepeople = entry[i];							
							theicon = thepeople['link'][2]['@href'];
							theuid = thepeople['db:uid']['$t'];
							thenickname = thepeople['title']['$t'];
							li.attr('ref',theuid);
							img = $('<img />').attr('src',theicon);
							p = $('<p></p>').html(thenickname);
							checkbox = $('<input type="checkbox" />');
							li.append(img).append(p).append(checkbox);							
							ul.append(li);
						}
						$('.iconlist>img').replaceWith(ul);
					});			
				}
				
				return false;
			});
			
			
			$('.popup').delegate('input:[type=checkbox]','click',function(){
				$(this).parent().toggleClass('selected');
			});
			
			
			
			$('.popup .close').click(function(){
				$(this).parent().parent().addClass('hide');
			});
			
		});
	</script>
	<style type="text/css">
		body,a,p,ul,ol,li,input,span,div,h1,h2,h3{margin:0;padding:0;font-size:12px;}
		ol,ul{list-style:none;}
		a{text-decoration:none;}
		body{background:#ccc;}
		
		.hd{margin-bottom:10px;}
		.hd .topnav{float:right;}
		.hd .topnav li{float:left;}
		.hd .topnav li float:left;}
		
		.clear-fix{}
		.clear-fix:after{content:'';clear:both;display:block;}
		
		.hide{display:none;}
		
		.row{width:950px;background:#fff;margin:0 auto;}
		
		.hd{}
		.hd .topnav li a{display:block;padding:0 10px;height:35px;line-height:35px;}
		
		
		.bd{}
		.bd .map{height:500px;}
		.bd .darlinlist{}
		.bd .darlinlist li{float:left;}
		.bd .darlinlist li img{width:50px;height:50px;padding:5px;margin:5px;border:1px solid #ccc;}
		
		
		/* Popup */
		.popup{background-color:rgba(255,255,255,0.7);position: fixed;top: 150px;left:450px;z-index:10;border-radius:10px;}
		.popup .inner{background:#fff;position:relative;margin:10px;width:400px;padding:15px;border-radius:10px;}
		.popup .inner a.close{background:#c31;display:block;position:absolute;right:5px;top:5px;border-radius:10px;height:20px;width:20px;text-align:center;line-height:20px;color:#fff;font-weight:bold;}
		.popup .inner a:hover{background:#e53;}
		.popup .inner .field{padding:0 0 20px 0;}
		.popup .inner .left{float:left;width:250px;}		
		.popup .inner .right{float:left;width:125px;}
		
		.popup .iconlist .loading{display:block;margin:20px auto;}
		.popup .iconlist ul{margin:0 auto;width:360px;}
		.popup .iconlist li{float:left;position:relative;margin:0 5px 10px 5px;overflow:hidden;width:48px;text-align:center;padding:6px;border:1px solid #ccc;background-color:#eee;}
		.popup .iconlist li img{width:48px;height:48px;}
		.popup .iconlist li p{line-height:20px;height:16px;}
		.popup .iconlist li input{position:absolute;top:5px;left:5px;}
		.popup .iconlist .selected{background-color:#83BF73;color:#fff;}
		
		
		.btn{display:block;border-radius:5px;cursor:pointer;width:50px;margin:0 auto;padding:5px 14px;;background-color:#eee;border:1px solid #ccc;background-image: -moz-linear-gradient(-90deg,#F8F8F8 0,#DDD 100%);background-image: -webkit-gradient(linear,left top,left bottom,color-stop(0,#F8F8F8),color-stop(1,#DDD));}
		.btn:active{background-image:none;}
		.disable{background-image:none;color:#666;}
		
	</style>
</head>
<body>	
	<div class="hd">
		<div class="row clear-fix">
			<div class="topnav">				
				<div class="icon"></div>
				<ul>
				<?php
					if($logged):
				?>
					<li><a href="#"><?php echo $user->nickname; ?></a></li>
					<li><a href="#">设置</a></li>					
					<li><a href="#" rel="invite" class="pop">邀请朋友</a></li>					
					<li><a href="logout.php">登出</a></li>
				<?php
					else:
				?>
					<li><a rel="login" href="#" class="pop">登录</a></li>
				<?php 
					endif;
				?>
				</ul>
			</div>
		</div>
	</div>
	<div class="bd">
		<div class="row darlinlist clear-fix">
			<ul>				
			</ul>
		</div>	
		<div class="row map" id="map"></div>
		
	</div>
	
	
	<div id="login" class="popup hide">
		<div class="inner clear-fix">			
			<a href="#" class="close">×</a>
			<div class="left">
				<p class="field">用名：<input type="text" name="" id="" /></p>
				<p class="field">密码：<input type="password" name="" id="" /></p>
				<p><input type="submit" value="提交" /></p>
			</div>
			<div class="right">
				<a href="auth.php"><img src="http://img3.douban.com/pics/doubanicon-16-full.png" alt="" /></a>
			</div>			
		</div>
	</div>
	
	<div id="invite" class="popup hide">
		<div class="inner clear-fix">			
			<a href="#" class="close">×</a>
			<div class="iconlist">
				<img class="loading" src="s/loading.gif" alt="" />
			</div>	
			<div><span id="sendinvite" class="btn">邀请他们</span></div>
		</div>
	</div>
	
	
</body>
</html>