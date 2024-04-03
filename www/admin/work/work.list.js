

$(function() {
	$("#start_datepicker").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
    dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
    //showMonthAfterYear:true,
    dateFormat: 'yymmdd',
    buttonImageOnly: true,
    buttonText: '달력'

	});
	
	$("#end_datepicker").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
    dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
    //showMonthAfterYear:true,
    dateFormat: 'yymmdd',
    buttonImageOnly: true,
    buttonText: '달력'

	});

	$("#dday_sdate_datepicker").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
    dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
    //showMonthAfterYear:true,
    dateFormat: 'yymmdd',
    buttonImageOnly: true,
    buttonText: '달력'

	});
	
	$("#dday_edate_datepicker").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
    dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
    //showMonthAfterYear:true,
    dateFormat: 'yymmdd',
    buttonImageOnly: true,
    buttonText: '달력'
	});
});



$(document).ready(function() {
	  
	$('#external-events div.external-event').each(function() {
	
		// create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
		// it doesn't need to have a start or end
		//alert($(this).attr('wt_ix'));
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
			revertDuration: 500  //  original position after the drag
		});
		
	});
	$('#result_area').droppable({
		drop: function(event, ui) {
			//alert(ui.draggable.html()+';;;;;;'+ui.draggable.attr('wt_ix'));
			//$(this).append('끌어서놓기 됨');
			//alert($('#work_table tr:first').parent().html());

			
			$.ajax({ 
				type: 'GET', 
				data: 
					{'act': 'insert','mmode': 'json','charger_ix': charger_ix,work_title:ui.draggable.html()},  
				url: './work.act.php',  
				dataType: 'json', 
				async: false, 
				beforeSend: function(){ 
					 //   $('#loading-dialog').dialog({minHeight: 100, width: 250}).dialog('open'); 
					 //   $('#loading-dialog p').text('Loading '+cal+' Calendar Events'); 
				},  
				success: function(calevents){ 
						//alert(calevents);
						//alert(calevents[0].work_title);
						var html_string = "";

						html_string = "<tr  bgcolor='#ffffff' id='row_"+calevents[0].wl_ix+"' style='display:none;height:33px'>";
						html_string += "<td class='list_box_td list_bg_gray'  align=center ><input type=checkbox name=wl_ix[] id='wl_ix' value=''></td><td class='list_box_td'  align=center colspan=2>"+calevents[0].sdate.substring(0,4)+"-"+calevents[0].sdate.substring(4,6)+"-"+calevents[0].sdate.substring(6,8)+" "+calevents[0].stime+"<br>";
						html_string += calevents[0].dday.substring(0,4)+"-"+calevents[0].dday.substring(4,6)+"-"+calevents[0].dday.substring(6,8)+" "+calevents[0].dtime+"</td>";
						html_string += "<td class='list_box_td point' style='text-align:left;padding:3px 0 3px 5px;line-height:160%'>";
						html_string += "	<div class=small style='font-weight:normal;color:gray;'>"+calevents[0].group_name+"</div>";
						html_string += "	<a href='work_view.php?mmode=&wl_ix="+calevents[0].wl_ix+"'  title='"+calevents[0].work_detail+"'><b style='font-size:11px;'>"+calevents[0].work_title+"</b></a>";
						html_string += "</td>";
						html_string += "<td class='list_box_td'  align=center>-</td>";
						html_string += "<td class='list_box_td list_bg_gray'  align=center>"+calevents[0].charger+"</td>";
						html_string += "<td class='list_box_td'  align=center>";
						if(ss_charger_ix == calevents[0].charger_ix){
						html_string += "<a href=\"javascript:PopSWindow('work_add.php?mmode=pop&wl_ix="+calevents[0].wl_ix+"',900,750,'member_info')\"><img src='../image/btc_modify.gif' border=0></a>";
						}else{
						html_string += "-";
						}
						if(ss_charger_ix ==calevents[0].reg_charger_ix){
						html_string += " <a href=\"javascript:DeleteWorkList('"+calevents[0].wl_ix+"')\"><img src='../image/btc_del.gif' border=0></a>";
						}
						html_string += "</td>";
						html_string += "</tr>";
						$('#work_table tr:first').after(html_string);

						if ($('#drop-remove').is(':checked')) {
							$.ajax({ 
								type: 'GET', 
								data: {'act': 'work_tmp_delete', wt_ix:ui.draggable.attr('wt_ix')},
								url: './work.act.php',  
								dataType: 'html', 
								async: true, 
								beforeSend: function(){ 
									
								},  
								success: function(calevents){ 
									//alert(calevents);
									$('#event_'+ui.draggable.attr('wt_ix')).css('display','none');
								} 
							}); 
							//alert(1);
							
							//ui.draggable.remove();
							
						}
						
				} 
			}); 




			//alert($('#work_table tr:first').next().html());
			// $('#work_table tr:first').next().toggle('slide', {easing: 'easeOutQuint', direction: 'down'}, 1000); 
			//alert($('#work_table tr:first').next().fadesliderToggle);
			//$('#work_table tr:first').next().toggle('slide',{}, 1000);
			//$('#work_table tr:first').next().slideToggle('1000');
			$('#work_table tr:first').next().slideRow('down',500);
			/*
			$('#work_table tr:first').next()
			.find('td')
			.wrapInner('<div style=\"display: block;\" />')
			.parent()
			.find('td > div')
			.slideDown(700,function(){
				//$(this).parent().parent().remove(); 
			});
			*/
			/*
			$('#work_table tr:first').next().animate({
				opacity: 0,
				height: 'toggle'}, 1000, 'swing');
			*/
			
		}
	});

	if($.cookie('company_goal_view') == '1' || $.cookie('company_goal_view') == ''){
		$('#company_goal').show();
	}
/*
	if($.cookie('tree_work_group_view') == '1' || $.cookie('tree_work_group_view') == ''){
		$('#tree_work_group').show();
	}else{
		$('#tree_work_group').hide();
	}
	//alert($.cookie('tree_user_view'));
	if($.cookie('tree_user_view') == '1' || $.cookie('tree_user_view') == ''){
		$('#tree_user').show();
	}else{
		$('#tree_user').hide();
	}
*/
	//if(confirm('해당 업무 목록을 정말로 삭제하시겠습니까?')){
		$.ajax({ 
			type: 'GET', 
			data: {'act': 'google'},
			url: './work.sync.php',  
			dataType: 'html', 
			async: true, 
			beforeSend: function(){ 
				
			},  
			success: function(calevents){ 
				//alert(calevents);
				//$('#row_'+wl_ix).slideRow('up',500);
			} 
		}); 

	//}
	/*
	$('tree_user_box').mouseover(function(){
		//$('#tree_user').animate({width:"200px",height:"300px"},500);
		
		$('tree_user_box').css('position','absolute');
		$('tree_user_box').css('z-index','100');
		$('tree_user_box').css('width','200px');
		$('tree_user_box').css('height','300px');
		$('tree_user_box').css('background-color','#ffffff');
		$('tree_user_box').css('border','2px solid silver');
		
	});
*/

	
/*
	$('#result_area').mouseover(function(){
		
		//$('#tree_user').css('width','150px').delay(3000);
		//$('#tree_user').css('height','150px').delay(3000);
		
		$('#tree_user').animate({width:"150px",height:"150px"},500);
		$('#tree_user').css('background-color','#ffffff');
		$('#tree_user').css('border','');
		//$('#tree_user').css('position','relative').;
		//$('#tree_user').css('z-index','0');
	});
*/
	

});


function ViewContents(wl_ix, mode){
	if(mode == 'over'){
		var offset = $('#magnifier_'+wl_ix).offset();
		//$('#contents_box').fadeIn(3000);
		//$('#contents_box').css('display','block');
		if( $.browser.msie ){
			$('#contents_box').toggle();
			$('#contents_box').css('position','absolute');
			$('#contents_box').css('left',offset.left-105);
			$('#contents_box').css('top',offset.top-140);
			$('#contents_desc').html($('#work_title_'+wl_ix).html()+"<br><br>"+$('#work_title_'+wl_ix).attr('desc'));
		}else{
			$('#contents_box').toggle();
			$('#contents_box').css('position','absolute');
			$('#contents_box').css('left',offset.left+25);
			$('#contents_box').css('top',offset.top);
			$('#contents_desc').html($('#work_title_'+wl_ix).html()+"<br><br>"+$('#work_title_'+wl_ix).attr('desc'));
		}

	}else{
		//$('#contents_box').fadeOut(3000);
		$('#contents_box').css('display','none');
	}
	//alert(offset.left+":::"+offset.top);
	
}

function ToggleIssue(type){
	//alert($('#company_goal').css('display')+":::"+$.cookie('company_goal_view'));
	//alert($('#view_complete_job').attr('checked'));
	//alert(list_type);
	if(type == "close_issue"){
		if($('#view_close_issue').attr('checked') == true || $('#view_close_issue').attr('checked') == "checked"){		
			$.cookie('view_close_issue', '1', {expires:1,domain:document.domain, path:'/', secure:0});
		}else{		
			$.cookie('view_close_issue', '0', {expires:1,domain:document.domain, path:'/', secure:0});
		}
	}
	document.location.reload();
}

function ToggleJob(type){
	//alert($('#company_goal').css('display')+":::"+$.cookie('company_goal_view'));
	//alert($('#view_complete_job').attr('checked'));
	//alert(list_type);
	if(type == "complete"){
		if($('#view_complete_job').attr('checked') == true || $('#view_complete_job').attr('checked') == "checked"){		
			$.cookie('view_complete_job', '1', {expires:1,domain:document.domain, path:'/', secure:0});
		}else{		
			$.cookie('view_complete_job', '0', {expires:1,domain:document.domain, path:'/', secure:0});
		}
	}else if(type == "project"){
		if($('#view_project_job').attr('checked') == true || $('#view_project_job').attr('checked') == "checked"){		
			$.cookie('view_project_job', '1', {expires:1,domain:document.domain, path:'/', secure:0});
		}else{		
			$.cookie('view_project_job', '0', {expires:1,domain:document.domain, path:'/', secure:0});
		}
	}

	/*
	var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
		return node.data.key;
	});

	// Get a list of all selected TOP nodes
	var selRootNodes = node.tree.getSelectedNodes(true);
	// ... and convert to a key array:
	var selRootKeys = $.map(selRootNodes, function(node){
		return node.data.key;
	});

	group_ix = selKeys.join(',');
	alert(group_ix);
	*/
	//alert(Math.random());
	
	$.ajax({ 
		type: 'GET', 
		data: {'list_view_type': 'list','mmode': 'inner_list','list_type': list_type,'parent_group_ix': parent_group_ix,'group_ix': group_ix,'department': department,'charger_ix': charger_ix,'dp_ix': dp_ix,'sdate': sdate, 'edate': edate, 'rand':Math.random()},  
		url: './work_list.php',  
		dataType: 'html', 
		async: true, 
		cache:false,
		beforeSend: function(){ 
			//$('#loading').show();
			$.blockUI.defaults.css = {}; 
			$.blockUI({ message: $('#loading'), css: {backgroundColor:'transparent',  width: '100px' , height: '100px' ,padding:  '10px'} });  
			
			 //   $('#loading-dialog').dialog({minHeight: 100, width: 250}).dialog('open'); 
			 //   $('#loading-dialog p').text('Loading '+cal+' Calendar Events'); 
		},  
		success: function(calevents){ 
				//alert(document.cookie);
				//alert(calevents);
				if(list_view_type == "calendar"){	
					if(calevents != null){
						$('#calendar').fullCalendar('removeEvents');
						$.each(calevents, function(i, calevent){ 
								//alert(i);
								$('#calendar').fullCalendar('renderEvent', calevent, true);  
						});  
					}
					$.unblockUI(); 
				}else{
					//alert(calevents.length);
					$('#result_area').html("<div style='display:none;'>"+Math.random()+"</div>"+calevents);
					//alert($('#result_area').html());
					$.unblockUI(); 
				}

				//$('#loading').hide();

				
		} 
	}); 
}
