
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
	loadCalendar();
	//
	//alert($.cookie('view_work_job') == 1);
	
	if($.cookie('view_work_job') == 1){
		$('#calendar_with_work').attr('checked',true);		
	}else{
		$('#calendar_with_work').attr('checked',false);
	}
});
/* hidden Layer */
var layerSt = 0;
function openLayer(obj) {
	
	if(layerSt == 0) {
		document.getElementById(obj).style.display = 'block';
		document.getElementById('layerBg').style.display = 'block';
		document.getElementById('layerBg').style.height = document.documentElement.scrollHeight+'px';
		layerSt = 1;
	} else {
		document.getElementById(obj).style.display = 'none';
		document.getElementById('layerBg').style.display = 'none';
		layerSt = 0;
	}
}


function loadCalendar(){
	var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();
	var _clicked;
	var _defaultView = $.cookie('defaultView');
	//alert(_defaultView);
	if(_defaultView == null){		
		_defaultView = "agendaWeek";
	}

	
	var calendar = $('#calendar').fullCalendar({
		header: {
			left: 'month,agendaWeek,agendaDay,basicWeek',
			center: 'prev title next today',
			right: 'work_view_check'
		},
		selectable: true,
		selectHelper: true,
		droppable:true,
		weekends: true,
		defaultView:_defaultView,
		editable: true,
		select: function(start, end, allDay) {
			
			//alert($.fullCalendar.formatDate(end,'yyyyMMdd HH:mm'));
			
			ShowModalWindow('work_add.php?mmode=pop&is_schedule=1&sdate='+$.fullCalendar.formatDate(start,'yyyyMMdd')+'&dday='+$.fullCalendar.formatDate(end,'yyyyMMdd')+'&stime='+$.fullCalendar.formatDate(start,'HH:mm')+'&dtime='+$.fullCalendar.formatDate(end,'HH:mm'),900,890,'member_info');

			//PopSWindow('work_add.php?mmode=pop&sdate='+$.fullCalendar.formatDate(start,'yyyyMMdd')+'&dday='+$.fullCalendar.formatDate(end,'yyyyMMdd')+'&stime='+$.fullCalendar.formatDate(start,'HH:mm')+'&dtime='+$.fullCalendar.formatDate(end,'HH:mm'),680,750,'work_view');
			
			var view = $('#calendar').fullCalendar('getView');
			//alert(1);
			$.ajax({ 
                type: 'GET', 
                data: {'list_view_type': 'calendar','list_type': list_type,'is_schedule': 1,'parent_group_ix': parent_group_ix,'group_ix': group_ix,'department': department,'charger_ix': charger_ix,'dp_ix': dp_ix,'sdate': $.fullCalendar.formatDate(view.start,'yyyyMMdd'), 'edate': $.fullCalendar.formatDate(view.end,'yyyyMMdd')},
                url: './work.json.php',  
                dataType: 'json', 
                async: false, 
                beforeSend: function(){ 
					//alert(1);
                     //   $('#loading-dialog').dialog({minHeight: 100, width: 250}).dialog('open'); 
                     //   $('#loading-dialog p').text('Loading '+cal+' Calendar Events'); 
                },  
                success: function(calevents){ 

						$('#calendar').fullCalendar('removeEvents');
                        $.each(calevents, function(i, calevent){ 
								//alert(i);
                                $('#calendar').fullCalendar('renderEvent', calevent, true);  
                        });  
                } 
			}); 
			//alert(1);
			/*
			//var title = callPrompt();
			var title = prompt('Event Title:');
			//openLayer('sign_in');
			if (title) {
				calendar.fullCalendar('renderEvent',
					{
						title: title,
						start: start,
						end: end,
						allDay: allDay
					},
					true // make the event 'stick'
				);
			}
			*/
			calendar.fullCalendar('unselect');
		},
		
		//revents: \"./work.json.php?list_view_type=calendar&department=".$_GET["department"]."&charger_ix=".$_GET["charger_ix"]."\",
		eventDragStart:function(event, jsEvent, ui, view){
			
		},
		eventDrop: function(event, daydelta,minuteDelta,allDay,revertFunc) {
			/*
			if (allDay) {
				alert(event.id + ' ::::'+ event.title + ' was moved ' + daydelta + ' days\\n' + '(should probably update your database)');
			}else{
				alert(event.id + ' ::::'+ event.title + ' was moved ' + minuteDelta + ' minute\\n' + '(should probably update your database)');
			}
			*/

			var s_date = new Date($.fullCalendar.formatDate(event.start,'yyyy'),$.fullCalendar.formatDate(event.start,'M')-1,$.fullCalendar.formatDate(event.start,'d'),$.fullCalendar.formatDate(event.start,'HH'),$.fullCalendar.formatDate(event.start,'m'));
			var s_d = s_date.getDate();
			var s_m = s_date.getMonth();
			var s_y = s_date.getFullYear();
			var s_h = $.fullCalendar.formatDate(event.start,'H');
			var s_i = $.fullCalendar.formatDate(event.start,'m')
			//ralert(s_h);
			var sdate = new Date(s_y,s_m,s_d,s_h, s_i);
			
			//alert(allDay+''+event.start+'~'+event.end);
			if(event.end == null){
				var e_date = new Date($.fullCalendar.formatDate(event.start,'yyyy'),$.fullCalendar.formatDate(event.start,'M')-1,$.fullCalendar.formatDate(event.start,'d'),$.fullCalendar.formatDate(event.start,'HH'),$.fullCalendar.formatDate(event.start,'m'));
				var e_d = e_date.getDate();
				var e_m = e_date.getMonth();
				var e_y = e_date.getFullYear();
				var e_h = $.fullCalendar.formatDate(event.start,'H');
				var e_i = $.fullCalendar.formatDate(event.start,'m')
				var edate = new Date(e_y,e_m,e_d,e_h, e_i);
			//	alert(e_date+';;;'+$.fullCalendar.formatDate(edate,'yyyyMMdd hh:mm'));
			}else{
				var e_date = new Date($.fullCalendar.formatDate(event.end,'yyyy'),$.fullCalendar.formatDate(event.end,'M')-1,$.fullCalendar.formatDate(event.end,'d'),$.fullCalendar.formatDate(event.end,'HH'),$.fullCalendar.formatDate(event.end,'m'));
				var e_d = e_date.getDate();
				var e_m = e_date.getMonth();
				var e_y = e_date.getFullYear();
				var e_h = $.fullCalendar.formatDate(event.end,'H');
				var e_i = $.fullCalendar.formatDate(event.end,'m')
				var edate = new Date(e_y,e_m,e_d,e_h, e_i);
			//	alert(e_date+';;;'+$.fullCalendar.formatDate(edate,'yyyyMMdd hh:mm'));
			}
			
			//alert($.fullCalendar.formatDate(sdate,'yyyyMMdd HH:mm') +';;;'+ $.fullCalendar.formatDate(edate,'yyyyMMdd HH:mm'));
			
			$.ajax({ 
			type: 'GET', 
			data: 
				{'act': 'date_update','wl_ix': event.id,'charger_ix': charger_ix,
				 'sdate': $.fullCalendar.formatDate(sdate,'yyyyMMdd'), 'dday': $.fullCalendar.formatDate(edate,'yyyyMMdd'),
				 'stime': $.fullCalendar.formatDate(sdate,'HH:mm'), 'dtime': $.fullCalendar.formatDate(edate,'HH:mm')
				},  
			url: './work.act.php',  
			dataType: 'html', 
			async: false, 
			beforeSend: function(){ 
				 //   $('#loading-dialog').dialog({minHeight: 100, width: 250}).dialog('open'); 
				 //   $('#loading-dialog p').text('Loading '+cal+' Calendar Events'); 
			},  
			success: function(calevents){ 
					//alert(calevents);
					/*
					$('#calendar').fullCalendar('removeEvents');
					$.each(calevents, function(i, calevent){ 
							//alert(i);
							$('#calendar').fullCalendar('renderEvent', calevent, true);  
					});
					*/
			} 
		}); 
		},
		eventRender: function(event, element) {
			//alert('eventRender'+event.title+' '+element);
			
		},
		eventAfterRender: function(event, element) {
			//alert('eventAfterRender'+event.title);
			
		},
		eventClick: function(event, element) {
			//PopSWindow('work_add.php?mmode=pop&wl_ix='+event.id+'&sdate='+$.fullCalendar.formatDate(event.start,'yyyyMMdd'),680,750,'work_view');
			
			//alert('eventClick');
			//ShowModalWindow('work_add.php?mmode=pop&wl_ix='+event.id+'&sdate='+$.fullCalendar.formatDate(event.start,'yyyyMMdd')+'&end='+$.fullCalendar.formatDate(event.end,'yyyyMMdd'),680,750,'work_view');
			PopSWindow('work_view.php?list_view_type=calendar&mmode=pop&wl_ix='+event.id+'&sdate='+$.fullCalendar.formatDate(event.start,'yyyyMMdd')+'&end='+$.fullCalendar.formatDate(event.end,'yyyyMMdd'),900,750,'work_view');
			/*
			if(_clicked != "event"){
				//WAIT FOR ANOTHER "event" CLICK
				
				
				setTimeout(function(){_clicked = "";}, 2000);
				_clicked = "event";
			}else{
				//RESET THE FLAG
				_clicked = "";
				
				
				//PRETEND IT'S A DOUBLE-CLICK
				//log("Faux event dblclick (8073311)");

			}
			*/
			//alert(_clicked);

			
		},
		
		 eventResize: function(event,dayDelta,minuteDelta,revertFunc) {
			
			var s_date = new Date($.fullCalendar.formatDate(event.start,'yyyy'),$.fullCalendar.formatDate(event.start,'MM')-1,$.fullCalendar.formatDate(event.start,'d'),$.fullCalendar.formatDate(event.start,'H'),$.fullCalendar.formatDate(event.start,'m'));
			var s_d = s_date.getDate();
			var s_m = s_date.getMonth();
			var s_y = s_date.getFullYear();
			var s_h = $.fullCalendar.formatDate(event.start,'H');
			var s_i = $.fullCalendar.formatDate(event.start,'m')
			
			var sdate = new Date(s_y,s_m,s_d,s_h, s_i);
			//alert($.fullCalendar.formatDate(sdate,'yyyyMMdd hh:mm'));
			//alert(dayDelta+'~'+minuteDelta);
			//alert(event.start+'~'+event.end);
			var e_date = new Date($.fullCalendar.formatDate(event.end,'yyyy'),$.fullCalendar.formatDate(event.end,'MM')-1,$.fullCalendar.formatDate(event.end,'d'),$.fullCalendar.formatDate(event.end,'H'),$.fullCalendar.formatDate(event.end,'m'));
			//alert($.fullCalendar.formatDate(s_date,'yyyyMMdd hh:mm')+'~'+$.fullCalendar.formatDate(e_date,'yyyyMMdd hh:mm'));

			var e_d = e_date.getDate();
			var e_m = e_date.getMonth();
			var e_y = e_date.getFullYear();
			var e_h = $.fullCalendar.formatDate(event.end,'H');
			var e_i = $.fullCalendar.formatDate(event.end,'m');
			var edate = new Date(e_y,e_m,e_d,e_h, e_i);
			//alert($.fullCalendar.formatDate(sdate,'yyyyMMdd hh:mm')+'~'+$.fullCalendar.formatDate(edate,'yyyyMMdd hh:mm'));

			$.ajax({ 
                type: 'GET', 
                data: 
					{'act': 'date_update','wl_ix': event.id,'charger_ix': charger_ix,
					 'sdate': $.fullCalendar.formatDate(sdate,'yyyyMMdd'), 'dday': $.fullCalendar.formatDate(edate,'yyyyMMdd'),
					 'stime': $.fullCalendar.formatDate(sdate,'HH:mm'), 'dtime': $.fullCalendar.formatDate(edate,'HH:mm')
					},  
                url: './work.act.php',  
                dataType: 'html', 
                async: false, 
                beforeSend: function(){ 
                     //   $('#loading-dialog').dialog({minHeight: 100, width: 250}).dialog('open'); 
                     //   $('#loading-dialog p').text('Loading '+cal+' Calendar Events'); 
                },  
                success: function(calevents){ 
						//alert(calevents);
						/*
						$('#calendar').fullCalendar('removeEvents');
                        $.each(calevents, function(i, calevent){ 
								//alert(i);
                                $('#calendar').fullCalendar('renderEvent', calevent, true);  
                        });
						*/
                } 
			}); 
			/*
			if (!confirm('is this okay?')) {
				revertFunc();
			}else{
				alert(
					'The end date of ' + event.title + 'has been moved ' +
					dayDelta + ' days and ' +
					minuteDelta + ' minutes.'
				);
			}*/

		},

		viewDisplay: function(view) {
			//alert($('#calendar').fullCalendar('defaultView'));
			//alert(view.name);
			
			$.cookie('defaultView', view.name, {expires:1,domain:document.domain, path:'/', secure:0});
			$.blockUI.defaults.css = {}; 
			$.blockUI({ message: $('#loading'), css: { width: '100px' , height: '100px' ,padding:  '10px'} });  
			//ralert('The new title of the view is ' + view.title+' :::: '+ $.fullCalendar.formatDate(view.start,'yyyyMMdd')+' :::: '+  $.fullCalendar.formatDate(view.end,'yyyyMMdd'));
			sdate = $.fullCalendar.formatDate(view.start,'yyyyMMdd');
			edate = $.fullCalendar.formatDate(view.end,'yyyyMMdd');
			
			

			$.ajax({ 
                type: 'GET', 
                data: {'list_view_type': 'calendar','list_type': list_type,'is_schedule': 1,'parent_group_ix': parent_group_ix,'group_ix': group_ix,'department': department,'charger_ix': charger_ix,'dp_ix': dp_ix,'sdate': $.fullCalendar.formatDate(view.start,'yyyyMMdd'), 'edate': $.fullCalendar.formatDate(view.end,'yyyyMMdd')},  
				
                url: './work.json.php',  
                dataType: 'json', 
                async: true, 
                beforeSend: function(){ 
					//$('#loading').show();
					//alert(1);
					
		 
                     //   $('#loading-dialog').dialog({minHeight: 100, width: 250}).dialog('open'); 
                     //   $('#loading-dialog p').text('Loading '+cal+' Calendar Events'); 
                },  
				error: function(xhr, status, error){ 
					alert(error);
                },  
                success: function(calevents){ 
					
						//alert(calevents);
						//alert(\"'\"+calevents+\"'\");
						try{
							if(calevents != null){
								
								$('#calendar').fullCalendar('removeEvents');
								$('#calendar').fullCalendar('addEventSource', calevents);  
										
								//$.each(calevents, function(i, calevent){ 
										//alert(i);
										//$('#calendar').fullCalendar('renderEvent', calevent, true);  
								//		$('#calendar').fullCalendar('addEventSource', calevent);  
										
								//});  
								
							}
						}catch(e){
							alert(e.message);
						}
						//$('#loading').hide();
						$.unblockUI(); 
						return ;
                } 
			}); 
			

		
		},
		dayClick: function(date, allDay, jsEvent, view) {
			/*
			if (allDay) {
				alert('Clicked on the entire day: ' + date);
			}else{
				alert('Clicked on the slot: ' + date);
			}

			alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);

			alert('Current view: ' + view.name);
			*/
			// change the day's background color just for fun
			//$(this).css('background-color', 'red');

		},
		loading: function(bool) {
			//if (bool) $('#loading').show();
			//else $('#loading').hide();
		},
		windowResize: function(view) {
			//alert('The calendar has adjusted to a window resize');
		},
		drop: function(date, allDay) { // this function is called when something is dropped
				
				// retrieve the dropped element's stored Event Object
				var originalEventObject = $(this).data('eventObject');
				
				// we need to copy it, so that multiple events don't have a reference to the same object
				var copiedEventObject = $.extend({}, originalEventObject);
				//print_r(copiedEventObject);
				// assign it the date that was reported
				

				var e_d = date.getDate();
				var e_m = date.getMonth();
				var e_y = date.getFullYear();
				var e_h = date.getHours();
				var e_i = date.getMinutes();
				var edate = new Date(e_y,e_m,e_d,e_h+1, e_i);

				
				copiedEventObject.start = date;
				copiedEventObject.end = edate;
				copiedEventObject.allDay = allDay;
				
				//alert(copiedEventObject.title);
				//alert($.fullCalendar.formatDate(date,'yyyyMMdd')+'::::'+$.fullCalendar.formatDate(date,'hh:mm'));
				//alert($.fullCalendar.formatDate(edate,'yyyyMMdd')+'::::'+$.fullCalendar.formatDate(edate,'hh:mm'));

				// render the event on the calendar
				// the last `true` argument determines if the event 'sticks' (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
				$('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
				
				// is the 'remove after drop' checkbox checked?
				//if ($('#drop-remove').is(':checked')) {
					$.ajax({ 
						type: 'GET', 
						data: {'act': 'work_tmp_delete', wt_ix:copiedEventObject.wt_ix},
						url: './work.act.php',  
						dataType: 'html', 
						async: false, 
						beforeSend: function(){ 
							//alert('faile');
						},  
						success: function(calevents){ 
							//alert(calevents);
							
						} 
					}); 
					$(this).remove();
					
				//}

				
				$.ajax({ 
					type: 'GET', 
					data: 
						{'act': 'insert','mmode':'json','charger_ix': charger_ix,'is_schedule':1,work_title:copiedEventObject.title,
						 'sdate': $.fullCalendar.formatDate(date,'yyyyMMdd'), 'dday': $.fullCalendar.formatDate(edate,'yyyyMMdd'),
						 'shour': $.fullCalendar.formatDate(date,'HH'),'sminute': $.fullCalendar.formatDate(date,'mm'), 
						 'dhour': $.fullCalendar.formatDate(edate,'HH'), 'dminute': $.fullCalendar.formatDate(edate,'mm')
						},  
					url: './work.act.php',  
					dataType: 'json', 
					async: false, 
					beforeSend: function(){ 
						 //   $('#loading-dialog').dialog({minHeight: 100, width: 250}).dialog('open'); 
						 //   $('#loading-dialog p').text('Loading '+cal+' Calendar Events'); 
					},  
					success: function(calevents){ 
							copiedEventObject.id = calevents[0].wl_ix;
							//alert(calevents[0].wl_ix);
							
					} 
				}); 
				
				
			}
	});
	
	//alert($('.fc-header-title').parent('td').parent().html());
	//alert($('.fc-button-prev').html(\"<img src='/admin/images/topmenu/estimation_on.gif'>\"));
	//.after('aaa');


}	
//});