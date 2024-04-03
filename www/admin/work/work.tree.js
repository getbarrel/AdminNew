/*
var treeData = [
		{title: 'item1 with key and tooltip', tooltip: 'Look, a tool tip!' },
		{title: 'item2: selected on init', select: true },
		{title: 'Folder', isFolder: true, key: 'id3',
			children: [
				{title: 'Sub-item 3.1',
					children: [
						{title: 'Sub-item 3.1.1', key: 'id3.1.1' },
						{title: 'Sub-item 3.1.2', key: 'id3.1.2' }
					]
				},
				{title: 'Sub-item 3.2',
					children: [
						{title: 'Sub-item 3.2.1', key: 'id3.2.1' },
						{title: 'Sub-item 3.2.2', key: 'id3.2.2' }
					]
				}
			]
		},
		{title: 'Document with some children (expanded on init)', key: 'id4', expand: true,
			children: [
				{title: 'Sub-item 4.1 (active on init)', activate: true,
					children: [
						{title: 'Sub-item 4.1.1', key: 'id4.1.1' },
						{title: 'Sub-item 4.1.2', key: 'id4.1.2' }
					]
				},
				{title: 'Sub-item 4.2 (selected on init)', select: true,
					children: [
						{title: 'Sub-item 4.2.1', key: 'id4.2.1' },
						{title: 'Sub-item 4.2.2', key: 'id4.2.2' }
					]
				},
				{title: 'Sub-item 4.3 (hideCheckbox)', hideCheckbox: true },
				{title: 'Sub-item 4.4 (unselectable)', unselectable: true }
			]
		}
	];
*/
var treeWorkGroupData = [];
var treeData = [];
var charger_ix = "";
var group_ix = "";

	$.ajax({ 
		type: 'GET', 
		data: 
			{'act': 'get_department'
			},  
		url: './work.tree.php?mode=work_user',  
		dataType: 'json', 
		async: false, 
		beforeSend: function(){ 
			 //   $('#loading-dialog').dialog({minHeight: 100, width: 250}).dialog('open'); 
			 //   $('#loading-dialog p').text('Loading '+cal+' Calendar Events'); 
		},  
		success: function(tree_datas){ 
				//alert(tree_datas);
				treeData = tree_datas;
				
		} 
	}); 

	$.ajax({ 
		type: 'GET', 
		data: 
			{'act': 'get_department'
			},  
		url: './work.tree.php?mode=work_group',  
		dataType: 'json', 
		async: false, 
		beforeSend: function(){ 
			 //   $('#loading-dialog').dialog({minHeight: 100, width: 250}).dialog('open'); 
			 //   $('#loading-dialog p').text('Loading '+cal+' Calendar Events'); 
		},  
		success: function(tree_datas){ 
				//alert(tree_datas);
				
				treeWorkGroupData = tree_datas;
				
		} 
	}); 

	$(function(){
		
		$('#tree_user').dynatree({
			checkbox: true,
			selectMode: 3,
			children: treeData,
			persist: true,
			onSelect: function(select, node) {
				//alert($.fullCalendar);
				
				// Get a list of all selected nodes, and convert to a key array:
				var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
					return node.data.key;
				});
				$('#echoSelection3').text(selKeys.join(', '));

				// Get a list of all selected TOP nodes
				var selRootNodes = node.tree.getSelectedNodes(true);
				// ... and convert to a key array:
				var selRootKeys = $.map(selRootNodes, function(node){
					return node.data.key;
				});
				$('#echoSelectionRootKeys3').text(selRootKeys.join(', '));
				$('#echoSelectionRoots3').text(selRootNodes.join(', '));
				//alert(selKeys.join(', '));
				select_charger_ix = selKeys.join(',');
				if(select_charger_ix == ''){
					select_charger_ix = charger_ix;
				}
				//alert(select_charger_ix);
				$.blockUI.defaults.css = {}; 
				$.blockUI({ message: $('#loading'), css: { width: '100px' , height: '100px' ,padding:  '10px'} }); 

				if($.fullCalendar){
					var ajax_url = './work.json.php';
					var ajax_dataType = 'json';
					var list_view_type = 'calendar';
					var mmode = '';
					list_type = '';
				}else{
					var ajax_url = './work_list.php';
					var ajax_dataType = 'html';
					var list_view_type = 'weekly';
					var mmode = 'inner_list';
					//list_type = '';
				}
				//if(dp_ix != ""){
					//document.write("work.json.php?list_view_type=calendar&list_type="+list_type+"&is_schedule=1&parent_group_ix="+parent_group_ix+"&group_ix="+group_ix+"&department="+department+"&charger_ix="+charger_ix+"&dp_ix="+dp_ix+"&sdate="+sdate+"&edate="+edate);
				//}
				//alert(list_view_type+"::"+list_type);
				$.ajax({ 
					type: 'GET', 
					data: {'list_view_type': list_view_type,'mmode': mmode,'list_type': list_type,'parent_group_ix': parent_group_ix,'group_ix': group_ix,'department': department,'charger_ix': select_charger_ix,'dp_ix': dp_ix,'sdate': sdate, 'edate': edate},  
					url: ajax_url,  
					dataType: ajax_dataType, 
					async: true, 
					beforeSend: function(){ 
						//alert(1);
						//$('#loading').show();
						 
						
						//alert(2);
						 //   $('#loading-dialog').dialog({minHeight: 100, width: 250}).dialog('open'); 
						 //   $('#loading-dialog p').text('Loading '+cal+' Calendar Events'); 
					},  
					success: function(calevents){ 
							//alert(calevents);
							//document.write(calevents);
							//alert(list_view_type);
							//alert(calevents);
							if(list_view_type == "calendar"){	
								//$('#loading').show();
								//$.blockUI.defaults.css = {}; 
								//$.blockUI({ message: $('#loading'), css: { width: '100px' , height: '100px' ,padding:  '10px'} });  

								//alert(calevents);
								if(calevents != null){
									$('#calendar').fullCalendar('removeEvents');
									$.each(calevents, function(i, calevent){ 
											//alert(calevent.sdate);
											$('#calendar').fullCalendar('renderEvent', calevent, true);  
									});
								}
								//$('#loading').hide();
						
								$.unblockUI(); 
							}else{
								//alert(calevents);
								//alert(Math.random());
								calevents = "<div style='display:none;'>"+Math.random()+"</div>"+calevents;
								$('#result_area').html(calevents);
								//alert($('#result_area').html());
								$.unblockUI(); 
							}

					},
					error: function(data){
						alert(data);
					}
				}); 

			},
			onDblClick: function(node, event) {
				node.toggleSelect();
			},
			onKeydown: function(node, event) {
				if( event.which == 32 ) {
					node.toggleSelect();
					return false;
				}
			},
			onPostInit: function(isReloading, isError) {
				//alert(1);
				// logMsg("onPostInit(%o, %o)", isReloading, isError);
				 // Re-fire onActivate, so the text is update
				// this.reactivate();
			},


			// The following options are only required, if we have more than one tree on one page:
//				initId: 'treeData',
			cookieId: 'dynatree-user',
			idPrefix: 'dynatree-user-'
		});
		
		$('#tree_work_group').dynatree({
			checkbox: true,
			selectMode: 3,
			persist: true,
			children: treeWorkGroupData,
			onSelect: function(select, node) {
				// Get a list of all selected nodes, and convert to a key array:
				var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
					return node.data.key;
				});
				$('#echoSelection3').text(selKeys.join(', '));

				// Get a list of all selected TOP nodes
				var selRootNodes = node.tree.getSelectedNodes(true);
				// ... and convert to a key array:
				var selRootKeys = $.map(selRootNodes, function(node){
					return node.data.key;
				});
				$('#echoSelectionRootKeys3').text(selRootKeys.join(', '));
				$('#echoSelectionRoots3').text(selRootNodes.join(', '));

				$.blockUI.defaults.css = {}; 
				$.blockUI({ message: $('#loading'), css: { width: '100px' , height: '100px' ,padding:  '10px'} }); 

				group_ix = selKeys.join(',');
				//alert(group_ix);
				if($.fullCalendar){
					var ajax_url = './work.json.php';
					var ajax_dataType = 'json';
					var list_view_type = 'calendar';
					var mmode = '';
				}else{
					var ajax_url = './work_list.php';
					var ajax_dataType = 'html';
					var list_view_type = 'list';
					var mmode = 'inner_list';
				}

				$.ajax({ 
					type: 'GET', 
					data: {'list_view_type': list_view_type,'mmode': mmode,'list_type': list_type,'parent_group_ix': parent_group_ix,'group_ix': group_ix,'department': department,'charger_ix': charger_ix,'dp_ix': dp_ix,'sdate': sdate, 'edate': edate},  
					url: ajax_url,  
					dataType: ajax_dataType, 
					async: true, 
					beforeSend: function(){ 
						//$('#loading').show();
						$.blockUI.defaults.css = {}; 
						$.blockUI({ message: $('#loading'), css: { width: '100px' , height: '100px' ,padding:  '10px'} });  
						
						 //   $('#loading-dialog').dialog({minHeight: 100, width: 250}).dialog('open'); 
						 //   $('#loading-dialog p').text('Loading '+cal+' Calendar Events'); 
					},  
					success: function(calevents){ 

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
								//alert(calevents);
								$('#result_area').html(calevents);
								//alert($('#result_area').html());
								$.unblockUI(); 
							}

							//$('#loading').hide();

							
					} 
				}); 
			},
			onDblClick: function(node, event) {
				node.toggleSelect();
			},
			onKeydown: function(node, event) {
				if( event.which == 32 ) {
					node.toggleSelect();
					return false;
				}
			},
			// The following options are only required, if we have more than one tree on one page:
//				initId: 'treeData',
			cookieId: 'dynatree-work_group',
			idPrefix: 'dynatree-work_group-'
		});
		

		$('#btnToggleSelect').click(function(){
			$('#tree2').dynatree('getRoot').visit(function(node){
				node.toggleSelect();
			});
			return false;
		});
		$('#btnDeselectAll').click(function(){
			$('#tree2').dynatree('getRoot').visit(function(node){
				node.select(false);
			});
			return false;
		});
		$('#btnSelectAll').click(function(){
			$('#tree2').dynatree('getRoot').visit(function(node){
				node.select(true);
			});
			return false;
		});
		
	});


