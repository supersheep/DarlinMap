//require jQuery

var dm = {};

dm.douban={

	ajaxurl : 'ajax/douban.php',
	
	say : function(opt){
		var url = dm.douban.ajaxurl + '?action=say';
		$.ajax({
			type:'post',
			url:url,
			data:{content:opt.content},
			success:opt.callback||function(e){console.log(e)}
		});
	},
	
	invite : function(opt){
		var url = dm.douban.ajaxurl + '?action=invite';
		
		var data = {to:opt.to,title:opt.title,content:opt.content};
		if(opt.sigid && opt.sigstr){
			$.extend(data,{sigid:opt.sigid,sigstr:opt.sigstr});
		}
		
		$.ajax({
			type:'post',
			url:url,
			data:data,
			success:opt.callback||function(e){console.log(e)}
		});
	},
	/* 
	sendmail : function(opt){
		var url = dm.douban.ajaxurl + '?action=sendmail';
		
		var data = {to:opt.to,title:opt.title,content:opt.content};
		if(opt.sigid && opt.sigstr){
			$.extend(data,{sigid:opt.sigid,sigstr:opt.sigstr});
		}
		
		$.ajax({
			type:'post',
			url:url,
			data:data,
			success:opt.callback||function(e){console.log(e)}
		});
	}, */
	
	
	getcontacts : function(id){
		$.get('ajax/contacts.php?id='+id,function(e){
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
};


dm.map = {
	initialize : function() {
		var latlng = new google.maps.LatLng(30,150);
		var myOptions = {
			zoom: localStorage.zoom || 2,
			center: localStorage.latlng || latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		dm.map.map = new google.maps.Map(document.getElementById("map"),myOptions);
	}		
};



dm.util={

	//弹出窗口
	pop : function(){		
		var wrap,inner,close,i=0,newarg=[];
		
		$('.popup').remove();
		
		wrap = $('<div class="popup"></div>');
		inner = $('<div class="inner clear-fix"></div>');
		close = $('<a href="#" class="close">×</a>');
		
		wrap.append(inner);
		inner.append(close);
		for(;i<arguments.length-1;i++){
			inner.append(arguments[i]);			
			newarg[i+1] = arguments[i];
		}
		if(typeof arguments[i] == 'function'){
			newarg[0]=wrap;
			arguments[i].apply(dm.util.pop,newarg);//最后一个参数用来绑定事件
		}else{
			inner.append(arguments[i]);
		}
		close.click(function(){
			wrap.remove();	
			dm.util.pop.show = false;
		});
		$('body').append(wrap);
		return false;	
	},
	
	
	invite:function(el,id){	
		var word;
		if(confirm('将要向Ta发送豆邮邀请，确定吗？')){
			word = prompt('说些什么推荐这个应用吧','like this');
			if(word){
				dm.douban.invite({title:'DarlinMap邀请',content:word,to:id,callback:function(e){
					console.log(e);
					el.html('已邀请');
					el.parent().addClass('selected');	
				}})
			}
		}
	},	
	
	//通过json生成头像列表到wrap中
	makeiconlist : function(wrap,json){
		var i=0,l=json.length,ul;
		ul = $('<ul class="clear-fix"></ul>');
		for(; i < l ; i++ ){
			li = $('<li></li>').addClass('people');		
			theicon = json[i].icon;
			theuid = json[i].uid;
			thenickname = json[i].nickname;
			img = $('<img />').attr('src',theicon).attr('title',thenickname).attr('alt',thenickname);
			//p = $('<p></p>').html(thenickname);
			invitebtn = $('<p class="invitebtn">邀请Ta</p>').attr('title',thenickname).attr('ref',theuid);
			li.append(img).append(invitebtn);				
			ul.append(li);
		}
		/* 
		wrap.delegate('input:[type=checkbox]','click',function(){
			$(this).parent().toggleClass('selected');
		}) */
		wrap.find('.loading').replaceWith(ul);
	
	},
	
	
	
	
	//获取头部朋友列表
	getfriends : function(){
		$.get('ajax/friends.php?'+(+ new Date),function(e){
			var o = $.parseJSON(e),
				mapicon,marker,mapicon,
				ul,li,img,
				i,l;
			if(o){
				ul = $('.darlinlist ul');
				for( i = 0 , l= o.length ; i < l ; i++ ){					
					li = $('<li></li>');
					img = $('<img />');
					img.attr('alt',o[i].nickname).attr('title',o[i].nickname).attr('src',o[i].icon);
					li.append(img);
					ul.append(li);
					mapicon = new google.maps.MarkerImage(o[i].icon,new google.maps.Size(20,20),new google.maps.Point(0,0),new google.maps.Point(0,10),new google.maps.Size(20,20));
					latlng = new google.maps.LatLng(o[i].latlng.split(',')[0],o[i].latlng.split(',')[1]);
					marker = new google.maps.Marker({
						position: latlng,
						map: dm.map.map,
						icon: mapicon
					});				
				}					
			}
		});
	}
}