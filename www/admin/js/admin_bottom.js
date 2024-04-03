var page_num = '';
function goPage(total_page,add_query,paging_type_param,paging_type){
	//alert(page_num);
	if(parseInt(page_num) <=  total_page && page_num != '' ){
		if(paging_type == 'inner'){
			window.frames['act'].location.href='?'+paging_type_param+'nset='+(Math.floor(page_num/10)+1)+'&page='+page_num+add_query;
		}else{
			document.location.href='?nset='+(Math.floor(page_num/10)+1)+'&page='+page_num+add_query;
		}

	}else{
		alert('페이지 정보가 입력되지 않았거나 검색가능 페이지를 초과했습니다.');
	}
}

function readonlyRadio(e){ 
    var srcEl = getSrc(e);
    var ra = srcEl.form[srcEl.name]
    for(var i=0;i<ra.length;i++){
        if(ra[i].checked) ra[i].onpropertychange = function(e){getSrc(e).click()}
        else ra[i].onclick = function(){return false};
    }
}

function getSrc(e)
{
    return e? e.target || e.srcElement : event.srcElement;
}

$(document).ready(function() {
	$('#work_memo').keydown(function(evt){
		//alert(evt.keyCode);
		if (evt.keyCode==13)
			work_tmp();
	});

	LargeImageView();
	HelpCloudView();
	setProductModal();

	$('#foot_page_bar').html($('#page_area').html());
	//$('#page_area').css('display','none');
	//alert($("#btnCollapseAll"));
	$("#btnCollapseAll").click(function(){
		//alert($("#leftmenu_tree").dynatree);
		$("#leftmenu_tree").dynatree("getRoot").visit(function(node){
			node.expand(false);
		});
		return false;
	});
	$("#btnExpandAll").click(function(){
		$("#leftmenu_tree").dynatree("getRoot").visit(function(node){
			node.expand(true);
		});
		return false;
	});

});


function HelpCloudView(){
		var offsetX = -100;
		var offsetY = -80;
		
		$('.helpcloud').hover(function(e){
			//mouse on
			var help_html = $(this).attr('help_html');
			var help_width = "200";
			var help_height = "50";

			if($(this).attr('help_height')){
				help_height =  $(this).attr('help_height');
				offsetY = help_height*-1-37;
				//alert(offsetY);
			}else{
				offsetY = -80;
			}

			if($(this).attr('help_width')){
				help_width =  $(this).attr('help_width');
				offsetX = help_width/2*-1;
				//alert(offsetX);
			}else{
				offsetX = -100;
			}
			var html ;
			html = "<table border=0 id='helpcloud' class='helpcloud' style='position: absolute;width:"+help_width+"px;height:"+help_height+"px' cellpadding=0 cellspacing=0>";
			html += "<col width=3><col width=*><col width=13><col width=*><col width=3>";
			html += "<tr>";
			html += "	<th class='box_01'></th>";
			html += "	<td class='box_02' colspan=3></td>";
			html += "	<th class='box_03'></th>";
			html += "</tr>";
			html += "<tr>";
			html += "	<th class='box_04'></th>";
			html += "	<td class='box_05 ' colspan=3 style='height:"+help_height+"px;line-height:130%;padding:5px;'>	";
			html += "	"+ help_html +"";
			html += "	</td>";
			html += "	<th class='box_06'></th>";
			html += "</tr>";
			html += "<tr>";
			html += "	<th class='box_07'></th>";
			html += "	<td class='box_08'></td>";
			html += "	<td style='background-color:#ffffff'></td>";
			html += "	<td class='box_08'></td>";
			html += "	<th class='box_09'></th>";
			html += "</tr>";
			html += "<tr>";
			html += "	<th ></th>";
			html += "	<td ></td>";
			html += "	<td class='box_10'></td>";
			html += "	<td ></td>";
			html += "	<th ></th>";
			html += "</tr>";
			html += "</table>";

			//alert(html);
			$(html).css('top', e.pageY + offsetY).css('left', e.pageX + offsetX).appendTo('body');

			//alert($('#helpcloud').attr('offsetHeight'));

		}, function(){
			//mouse off
			$('#helpcloud').remove();
		});
		
		$('.helpcloud').mousemove(function(e){
			//alert(offsetX);
			$('#helpcloud').css('top', e.pageY + offsetY).css('left', e.pageX + offsetX);
			//alert($('#helpcloud').attr('height'));
			
		})
		
}


function setProductModal()
{
	try{
		$('a[rel*=facebox]').facebox({
			loadingImage : '/data/basic/templet/photoskin/images/loading.gif',
			closeImage   : '/admin/images/fancy_closebox.png'
		});
	}catch(e){}	
}

function winResize()
{
	if(window.dialogArguments){
		var Dwidth = parseInt(document.documentElement.clientWidth);
		var Dheight = parseInt(document.body.scrollHeight);
		//alert(1);
		//alert(Dwidth+":::"+Dheight);
		
		window.dialogWidth = Dwidth+"px";
		window.dialogHeight = (Dheight)+"px";
		/*
		document.body.appendChild(divEl);
		window.resizeBy(Dwidth-divEl.offsetWidth, Dheight-divEl.offsetHeight);
		document.body.removeChild(divEl);
		*/
	}else{
		
		var Dwidth = parseInt(document.body.scrollWidth);
		var Dheight = parseInt(document.body.scrollHeight);
		var divEl = document.createElement("div");
		divEl.style.position = "absolute";
		divEl.style.left = "0px";
		divEl.style.top = "0px";
		divEl.style.width = "100%";
		divEl.style.height = "100%";
//alert(Dheight+":::"+divEl.offsetHeight);
		document.body.appendChild(divEl);
		window.resizeBy(Dwidth-divEl.offsetWidth, Dheight-divEl.offsetHeight);
		document.body.removeChild(divEl);
		
		if( parseInt(Dheight) > parseInt($(window).height()) ) {
			window.document.body.scroll = 'auto';
			window.resizeBy(18,0);
		}	
	}
   
}


	//alert(width);
function HelpView(){
	var width = parseInt($(window).width());
	//alert("width :"+ width);
	var available_help_width = width - 1300;
	//alert("available_help_width :"+available_help_width);
	$('#RightMenuArea').attr('width',available_help_width);
	$('#basic_info').css('display','none');
	$('#help_info').css('display','block');
	

	$('#help_movie').html(viewMenual2("몰스토리동영상메뉴얼_페이지상세디자인(090322)_config.xml", 600, 350)); // 8:5
	
	

}

function WideView(){
	
	if($.cookie('ALL_HIDE_MENU') == "Y"){
		TopMenuHidden(false);
		MenuHidden(false);
		RightMenuHidden(false);
		$('#wide_view').attr('src',$('#wide_view').attr('general_src'));
		$.cookie('ALL_HIDE_MENU', 'N', {expires:1,domain:document.domain, path:'/', secure:0});
	}else{
		TopMenuHidden(true);
		MenuHidden(true);
		RightMenuHidden(true);
		$('#wide_view').attr('src',$('#wide_view').attr('wide_src'));
		$.cookie('ALL_HIDE_MENU', 'Y', {expires:1,domain:document.domain, path:'/', secure:0});
	}
}

function MenuHidden(view){
	//alert(document.getElementById('admin_left_menu').style.display);
	//if(document.getElementById('admin_left_menu').style.display == 'block'){
	//alert($('#admin_left_menu').css('display'));
	
	if(!view){
		view = ($('#admin_left_menu').css('display') == "none");
	}
	if(view){
		var layout_s = '';
		layout_s +="	<td width='12%'></td>";
		layout_s +="	<td width='8px'></td>";
		layout_s +="	<td width='100%'></td>";

		$('.layout_setting').html(layout_s);
		//$('#admin_left_menu').css('display','block');		
		$('#admin_left_menu').show();
		$('#admin_left_menu').attr('width','180');
		$('#left_hidden_btn').attr('src',$('#left_hidden_btn').attr('left_btn'));
		//window.frames['iframe_act'].location.href='/admin/menu_forcookie.php?menu_type=left&menu=Y';
		$.cookie('HIDE_MENU', 'Y', {expires:1,domain:document.domain, path:'/', secure:0});
		//document.getElementById('iframe_act').src='/admin/menu_forcookie.php?menu=Y';//kbk
	}else{
		
		var layout_s = '';
		layout_s +="	<td width='8px'></td>";
		layout_s +="	<td width='100%'></td>";

		$('.layout_setting').html(layout_s);
		//$('#admin_left_menu').css('display','none');
		$('#admin_left_menu').hide();
		$('#left_hidden_btn').attr('src',$('#left_hidden_btn').attr('right_btn'));	

		//window.frames['iframe_act'].location.href='/admin/menu_forcookie.php?menu_type=left&menu=N';
		$.cookie('HIDE_MENU', 'N', {expires:1,domain:document.domain, path:'/', secure:0});
		//document.getElementById('iframe_act').src='/admin/menu_forcookie.php?menu=N';//kbk
	}
}

function RightMenuHidden(view){
	if(!view){
		view = ($('#RightMenuArea').css('display') == "none");
	}
	if(view){
		$('#RightMenuArea').show();
		$('#RightMenuArea').attr('width','217');
		$('#right_hidden_btn').attr('src',$('#right_hidden_btn').attr('right_btn'));
		//window.frames['iframe_act'].location.href='/admin/menu_forcookie.php?menu_type=right&menu=Y';
		$.cookie('RIGHT_MENU_HIDDEN', 'Y', {expires:1,domain:document.domain, path:'/', secure:0});
		//document.getElementById('iframe_act').src='/admin/menu_forcookie.php?menu=Y';//kbk
	}else{
		//$('#RightMenuArea').css('display','none');
		$('#RightMenuArea').hide();
		$('#right_hidden_btn').attr('src',$('#right_hidden_btn').attr('left_btn'));	

		//window.frames['iframe_act'].location.href='/admin/menu_forcookie.php?menu_type=right&menu=N';
		$.cookie('RIGHT_MENU_HIDDEN', 'N', {expires:1,domain:document.domain, path:'/', secure:0});
		//document.getElementById('iframe_act').src='/admin/menu_forcookie.php?menu=N';//kbk
	}
}

function TopMenuHidden(view){
	//alert(document.getElementById('admin_left_menu').style.display);
	//if(document.getElementById('admin_left_menu').style.display == 'block'){
	//alert($('#admin_left_menu').css('display'));

	
	if(view==null){
		
		//view = ($('.top_menu_img').css('display') == "none");
		view = (!$('.top_menu_img').is(':visible'));
		//alert($('.top_menu_img').is(':visible'));
	}
	

	/*
	if(view){
		$('.top_menu_img').show();
		$('#content_new').css('top','126px');
		$('#top_hidden_btn').attr('src',$('#top_hidden_btn').attr('up_btn'));

		//window.frames['iframe_act'].location.href='/admin/menu_forcookie.php?menu_type=top&menu=Y';
		$.cookie('TOP_MENU_HIDDEN', 'Y', {expires:1,domain:document.domain, path:'/', secure:0});
		//document.getElementById('iframe_act').src='/admin/menu_forcookie.php?menu=Y';//kbk
	}else{
		$('.top_menu_img').hide();
		$('#content_new').css('top','73px');
		$('#top_hidden_btn').attr('src',$('#top_hidden_btn').attr('down_btn'));	
		//window.frames['iframe_act'].location.href='/admin/menu_forcookie.php?menu_type=top&menu=N';
		$.cookie('TOP_MENU_HIDDEN', 'N', {expires:1,domain:document.domain, path:'/', secure:0});
		//document.getElementById('iframe_act').src='/admin/menu_forcookie.php?menu=N';//kbk
	}
	*/
	
	top_menu_scroll(view);
}

function CrmSearch(frm){
	PoPWindow3('/admin/blank.php', 900, 700, 'pop_crm_search', 'no');
	frm.target = 'pop_crm_search';
	frm.submit();
}

function LargeImageView(){
		var offsetX = 20;
		var offsetY = 10;
		
		$('a.screenshot').hover(function(e){
			//mouse on
			var href = $(this).attr('rel');
			$('<img id=\"largeImage\" src=\"' + href + '\">').css('top', e.pageY + offsetY).css('left', e.pageX + offsetX).appendTo('body');
		}, function(){
			//mouse off
			$('#largeImage').remove();
		});
		
		$('a.screenshot').mousemove(function(e){
			$('#largeImage').css('top', e.pageY + offsetY).css('left', e.pageX + offsetX);
		})
		
}

function work_tmp(){
	
	if($('#work_memo').val() == ''){
		alert('할일을 입력해주세요');
		return;
	}
	//$('#work_memo').attr('readonly','true');
//wt_ix='".$mdb->dt[wt_ix]."' id='event_".$mdb->dt[wt_ix]."' ondblclick=\"work_tmp_delete('event_".$mdb->dt[wt_ix]."');\"
	$('#external-events div:first').after('<div class="external-event" style="display:none;">'+$('#work_memo').val()+'</div>');
	/*
	$('#tableClassname').find('tbody')     
		.append($('<tr>')         
			.append($('<td>')             
				.append($('<img>')                 
					.attr('src', 'img.png')                 
					.text('Image cell')             
				)         
			)     
		);
	*/
	
	$.ajax({ 
		type: 'GET', 
		data: {'act': 'work_tmp_insert','work_tmp_title': $('#work_memo').val()},
		url: '/admin/work/work.act.php',  
		dataType: 'html', 
		async: true, 
		beforeSend: function(){ 
			$('#work_memo').val('');
			
		},  
		success: function(wt_ix){ 
			//alert(wt_ix);
			
			$('#external-events div.external-event').each(function() {
					//alert($(this).text());
					// create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
					// it doesn't need to have a start or end
					var eventObject = {
						title: $.trim($(this).text()), // use the element's text as the event title
						wt_ix: $.trim($(this).attr('wt_ix'))
					};
					if($(this).text() != ""){
						if($(this).attr('wt_ix')){
							$(this).attr('wt_ix',$(this).attr('wt_ix'));
							$(this).attr('id','event_'+$(this).attr('wt_ix'));
						}else{
							$(this).attr('wt_ix',wt_ix);
							$(this).attr('id','event_'+wt_ix);
							$(this).slideDown(500);
						}
						//alert(1);
						//alert($(this).text());
						
						// store the Event Object in the DOM element so we can get to it later
						$(this).data('eventObject', eventObject);
						
						try{
						// make the event draggable using jQuery UI
						$(this).draggable({
							zIndex: 999,
							revert: true,      // will cause the event to go back to its
							revertDuration: 0  //  original position after the drag
						});
						}catch(e){}
					

						$(this).bind('dblclick',function(){
							//alert('event_'+wt_ix);
							//alert(wt_ix);
							work_tmp_delete('event_'+wt_ix);	
						});

						$(this).bind('click',function(){
							//alert('event_'+wt_ix);
							//alert(wt_ix);
							//alert($(this).html());	
						});
					}
					
					
				
			});
			//$('#work_memo').attr('readonly','false');
		} 
	}); 

}


function work_tmp_delete(obj_id){
	//alert(obj_id);
	//alert($('#work_memo').val());
	//alert($('#'+obj_id).attr('wl_ix'));
	//$('#external-events div:last').after('<div class="external-event">'+$('#work_memo').val()+'</div>');
	
	$.ajax({ 
		type: 'GET', 
		data: {'act': 'work_tmp_delete','wt_ix': $('#'+obj_id).attr('wt_ix')},
		url: '../work/work.act.php',  
		dataType: 'html', 
		async: true, 
		beforeSend: function(){ 
			
		},  
		success: function(calevents){ 
			//alert(calevents);
			//alert($('#'+obj_id).parent().html());
			//alert($('#'+obj_id).clone().wrapAll("<div/>").parent().html());

			
			$('#work_memo').val('');
			$('#'+obj_id).slideUp(500);
			$('#memo_list_'+obj_id).slideUp(500);

			/*
			$('#external-events div.external-event').each(function() {
	
					// create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
					// it doesn't need to have a start or end
					var eventObject = {
						title: $.trim($(this).text()), // use the element's text as the event title
						wt_ix: $.trim($(this).attr('wt_ix'))
					};
					
					// store the Event Object in the DOM element so we can get to it later
					$(this).data('eventObject', eventObject);
					
					// make the event draggable using jQuery UI
					$(this).draggable({
						zIndex: 999,
						revert: true,      // will cause the event to go back to its
						revertDuration: 0  //  original position after the drag
					});
					
				
			});
			*/
		} 
	}); 

}


function ViewAlertBox(message, event_function){
	//alert($('#show_alert_box'));
	try{
		$('#show_alert_box').html(message);
		$('#show_alert_box').animate({top:'0px'},500, null, function(){
			$('#show_alert_box').delay(1500).animate({top:'-42px'},500, null, function(){
				setTimeout($.unblockUI, 100); 
				if(event_function == "parent_reload"){
					parent.document.location.reload();
				}else if(event_function == "reload"){
					document.location.reload();
				}else if(event_function == "top_reload"){
					top.document.location.reload();
				}
			});
		});
		//setTimeout($.unblockUI, 2000); 
	}catch(e){
		alert(message);
	}
}


function WideView_new(){
	
	if($.cookie('ALL_HIDE_MENU') == "Y"){
		$('.content').css({'left':'10px'});
		$('.close_buttom3').hide();
		$('.close_buttom4').show();
		$('.con_height').hide();
		$('.content').css({'right':'8px'});
		$('.close_buttom6').show();
		$('.close_buttom5').hide();
		$('.con_height2').hide();
		$('.contents').css({'top':'75px'});
		$('.header_top2').css({'height':'25px'});
		$('.top_menu_img').hide();
		$('.close_buttom').hide();
		$('.close_buttom2').show();
		$('#wide_view').attr('src',$('#wide_view').attr('general_src'));
		$.cookie('ALL_HIDE_MENU', 'N', {expires:1,domain:document.domain, path:'/', secure:0});
	}else{
		$('.top_menu_img').show();
		$('.contents').css({'top':'123px'});
		$('.close_buttom').show();
		$('.close_buttom2').hide();
		$('.header_top2').css({'height':'74px'});
		$('.content').css({'left':'229px'});
		$('.close_buttom4').hide();
		$('.close_buttom3').show();
		$('.con_height').show();
		$('.content').css({'right':'220px'});
		$('.close_buttom5').show();
		$('.close_buttom6').hide();
		$('.con_height2').show();
		$('#wide_view').attr('src',$('#wide_view').attr('wide_src'));
		$.cookie('ALL_HIDE_MENU', 'Y', {expires:1,domain:document.domain, path:'/', secure:0});
	}
}


function toggleChangeFunction(){ 
		if($('#is_change_function').attr('checked') == true || $('#is_change_function').attr('checked') == 'checked'){		
			$.cookie('is_change_function', '1', {expires:1,domain:document.domain, path:'/', secure:0});
		}else{		
			$.cookie('is_change_function', '0', {expires:1,domain:document.domain, path:'/', secure:0});
		}
		 
		if($.cookie('is_change_function') == 1){
			$('#is_change_function').attr('checked',true);		
		}else{
			$('#is_change_function').attr('checked',false);
		}
		document.location.reload();
}

function moreDisplay(obj, target_obj, default_obj){
	if($(obj).attr('src') == $(obj).attr('open_src')){
		$(obj).attr('src',$(obj).attr('close_src'));
	}else{
		$(obj).attr('src',$(obj).attr('open_src'));
	}
	$(target_obj).toggle();
	if(default_obj){
		$(default_obj).toggle();
	}
}

	$(function(){
		// Initialize the tree inside the <div>element.
		// The tree structure is read from the contained <ul> tag.
		/*
		$("#leftmenu_tree").dynatree({
			title: "Programming Sample",
			onActivate: function(node) {
				$("#echoActive").text(node.data.title);
//				alert(node.getKeyPath());
				if( node.data.url )
					window.open(node.data.url, node.data.target);
			},
			onDeactivate: function(node) {
				$("#echoSelected").text("-");
			},
			onFocus: function(node) {
				$("#echoFocused").text(node.data.title);
			},
			onBlur: function(node) {
				$("#echoFocused").text("-");
			},
			onLazyRead: function(node){
				var fakeJsonResult = [
					{ title: 'Lazy node 1', isLazy: true },
					{ title: 'Simple node 2', select: true }
				];
//				alert ("Let's pretend we're using this AJAX response to load the branch:\n " + jsonResult);
				function fakeAjaxResponse() {
					return function() {
						node.addChild(fakeJsonResult);
						// Remove the 'loading...' status:
						node.setLazyNodeStatus(DTNodeStatus_Ok);
					};
				}
				window.setTimeout(fakeAjaxResponse(), 1500);
			}
		});
		*/
		/*
		$("#btnAddCode").click(function(){
			// Sample: add an hierarchic branch using code.
			// This is how we would add tree nodes programatically
			var rootNode = $("#leftmenu_tree").dynatree("getRoot");
			var childNode = rootNode.addChild({
				title: "Programatically addded nodes",
				tooltip: "This folder and all child nodes were added programmatically.",
				isFolder: true
			});
			childNode.addChild({
				title: "Document using a custom icon",
				icon: "customdoc1.gif"
			});
		});

		$("#btnAddObject").click(function(){
			// Sample: add an hierarchic branch using an array
			var obj = [
				{ title: 'Lazy node 1', isLazy: true },
				{ title: 'Lazy node 2', isLazy: true },
				{ title: 'Folder node 3', isFolder: true,
					children: [
						{ title: 'node 3.1' },
						{ title: 'node 3.2',
							children: [
								{ title: 'node 3.2.1' },
								{ title: 'node 3.2.2',
									children: [
										{ title: 'node 3.2.2.1' }
									]
								}
							]
						}
					]
				}
			];
			$("#leftmenu_tree").dynatree("getRoot").addChild(obj);
		});

		$("#btnActiveNode").click(function(){
			$("#leftmenu_tree").dynatree("getTree").activateKey("id4.3.2");
//			$("#leftmenu_tree").dynatree("getTree").getNodeByKey("id4.3.2").activate();
		});
		$("#btnSetTitle").click(function(){
			var node = $("#leftmenu_tree").dynatree("getActiveNode");
			if( !node ) return;
			node.setTitle(node.data.title + ", " + new Date());
			// this is a shortcut for
			// node.fromDict({title: node.data.title + new Date()});
		});
		$("#btnFromDict").click(function(){
			var node = $("#leftmenu_tree").dynatree("getActiveNode");
			if( !node ) return;
//			alert(JSON.stringify(node.toDict(true)));
			// Set node data and - optionally - replace children
			node.fromDict({
				title: node.data.title + new Date(),
				children: [{title: "t1"}, {title: "t2"}]
			});
		});
*/
/*
		$("#btnShowActive").click(function(){
			var node = $("#leftmenu_tree").dynatree("getActiveNode");
			if( node ){
				alert("Currently active: " + node.data.title);
			}else{
				alert("No active node.");
			}
		});

		$("#btnDisable").toggle(function(){
				$("#leftmenu_tree").dynatree("disable");
				$(this).text("Enable");
				return false;
			}, function(){
				$("#leftmenu_tree").dynatree("enable");
				$(this).text("Disable");
				return false;
			});
		$("#btnToggleExpand").click(function(){
			$("#leftmenu_tree").dynatree("getRoot").visit(function(node){
				node.toggleExpand();
			});
			return false;
		});
		*/
		
/*
		$("#btnSortActive").click(function(){
			var node = $("#leftmenu_tree").dynatree("getActiveNode");
			// Custom compare function (optional) that sorts case insensitive
			var cmp = function(a, b) {
				a = a.data.title.toLowerCase();
				b = b.data.title.toLowerCase();
				return a > b ? 1 : a < b ? -1 : 0;
			};
			node.sortChildren(cmp, false);
		});
		$("#btnSortAll").click(function(){
			var node = $("#leftmenu_tree").dynatree("getRoot");
			node.sortChildren(null, true);
		});
*/
	});



//2014-10-29 Hong 페이지 머무는 시간변수생성
AdminTimesecond = 0;
$(document).ready(function() {
	setInterval(function(){AdminTimesecond++;},1000);//초
})

//161128 add 
$(function() {
	if ($('.side-btn').is(':visible')) {
		$('#content_new').scroll(function() {
			$('.side-btn').css({top:$('#content_new').scrollTop()+215})
			console.glom
		})
		$('.side-btn a').click(function() {
			$('#content_new').scrollTop(0)
		})
		$('.side-btn a + a').click(function() {
			$('#content_new').scrollTop($('#content_new')[0].scrollHeight)
		})
	}
})