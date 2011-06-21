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
		
		
		
		$(document).ready(function(){
			dm.map.initialize();
			dm.util.getfriends();		
			
			
			
			$('#senddoumail').click(function(){			
				return dm.util.pop($('<div>p</div>'),function(e){
					console.log(e.find('.close'));
				});
				//var a = prompt('your word');
				//dm.douban.sendmail({content:a,title:'test',to:'52114966'});
			});
			
			$('.pop').click(function(){
				var rel = $(this).attr('rel');
				switch(rel){
					case 'login':dm.util.pop(
					$('<div class="left"><p class="field">用名：<input type="text" name="" id="" /></p><p class="field">密码：<input type="password" name="" id="" /></p><p><input type="submit" value="提交" /></p></div>'),
					$('<div class="right"><a href="auth.php"><img src="http://img3.douban.com/pics/doubanicon-16-full.png" alt="" /></a></div>'));break;
					
					case 'invite':dm.util.pop(
					$('<div class="clear-fix filter"><input class="input" type="text" placeholder="输入好友id或用户名"/><input class="btn" id="btn-addfriend" type="button" value="搜索" /></div>'),
					$('<div class="iconlist"></div>'),function(wrap,filter,iconlist){
						var input = filter.find('.input'),						
							btn = filter.find('.btn'),						
							handler = function(e){
								if((!e.keyCode || (e.keyCode && e.keyCode=='13')) && input.val()){
									//input.attr('disabled',true);									
									iconlist.find('ul').remove();
									iconlist.append($('<img class="loading" src="s/loading.gif" alt="load" />'));
									$.get('ajax/searchuser.php?id='+input.val(),function(json){
										json = $.parseJSON(json);
										dm.util.makeiconlist(iconlist,json);
										iconlist.delegate('.invitebtn','click',function(){	
											if(!$(this).hasClass('selected')){
												dm.util.invite($(this),$(this).attr('ref'));
											}
										})
									});
									input.val('').focus();
								}
							};
							
						input.focus().keyup(handler);
						btn.click(handler);
					});break;
				}
				
				return false;
			});	
			
			
		});
	</script>
	<link rel="stylesheet" href="s/style.css" />
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
					<li><a href="#" rel="invite" class="pop">邀请好友</a></li>			
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
	
</body>
</html>