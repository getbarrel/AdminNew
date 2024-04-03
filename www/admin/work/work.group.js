 function CheckValue(frm){
 	if(frm.group_depth[1].checked){
 		if(frm.parent_group_ix.value == ''){
	 		alert('2차 그룹을 등록하기 위해서는 1차그룹을 반드시 선택하셔야 합니다.');
	 		return false;
 		}
 	}
 	
 	if(frm.group_name.value.length < 1){
 		alert('등록하시고자 하는 업무 그룹명을 입력해주세요');
 		frm.group_name.focus();
 		return false;
 	}
 	
 }
 function updateGroupInfo(group_ix,group_name,disp, vieworder, is_project, group_depth, parent_group_ix, contract_sdate, contract_edate){
 	var frm = document.group_form;
 	
 	frm.act.value = 'update';
 	frm.group_ix.value = group_ix;
 	frm.group_name.value = group_name;
	frm.contract_sdate.value = contract_sdate;
	frm.contract_edate.value = contract_edate;
 	frm.vieworder.value = vieworder;
	if(is_project == '1'){
		frm.is_project.checked = true;
	}else{
		frm.is_project.checked = false;
	}
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}

	if(group_depth == '2'){ 
		frm.group_depth[1].checked = true;
		frm.parent_group_ix.disabled = false;

		$("#parent_group_ix option").each(function () {
			//str += $(this).text() + " ";
			if($(this).val() == parent_group_ix){
				$(this).attr('selected',true);
			}
		});
	}else if(group_depth == '1'){ 
		frm.group_depth[0].checked = true;
		frm.parent_group_ix.disabled = true;
		frm.parent_group_ix[0].selected = true;
	}

 
}
 
 function deleteGroupInfo(act, group_ix){
 	if(confirm('해당그룹  정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.group_form; 	
 		frm.act.value = act;
 		frm.group_ix.value = group_ix;
 		frm.submit();
 	}	
}

function ToggleAllGroup(){
	//alert($('#company_goal').css('display')+":::"+$.cookie('company_goal_view'));
	//alert($('#view_complete_job').attr('checked'));
	if($('#view_all_group').attr('checked') == true || $('#view_all_group').attr('checked') == "checked"){		
		$.cookie('view_all_group', '1', {expires:1,domain:document.domain, path:'/', secure:0});
	}else{		
		$.cookie('view_all_group', '0', {expires:1,domain:document.domain, path:'/', secure:0});
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
	$.ajax({ 
		type: 'GET', 
		data: {'mmode': 'inner_list'},  
		url: './work_group.php',  
		dataType: 'html', 
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
					$('#result_area').html(calevents);
					//alert($('#result_area').html());
					$.unblockUI(); 
			
				
		} 
	}); 
}