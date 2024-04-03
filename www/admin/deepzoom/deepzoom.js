function applyservice_check( type , serviceCd ) {
	
	var strDlg			= "";
	var dlgForm			= $("#applyservice_check");
	var el_dialog		= null;

	
	activateDialog(dlgForm.dialog({ 
		open: function(event, ui) {
			//alert(1);
			$(this).css("padding","0").parent().css("padding","0");
			$(this).css("border","0").parent().css("border","0");
		},
		width : 480,
		autoOpen : false,
		modal : true,
		resizable : false,
		zIndex: 2000
	}));

		dlgForm.dialog("open");
	//alert(1);
	// 청약 신청
	/*
	if (type == "s_add") {
		
		if(boolCheck){
			$("#check_title").html("");
			$("#check_msg").html("[ PM 11:00~익일 AM 5:00에는 상품청약이 불가합니다.<br>AM 5시 이후 상품청약을 진행해 주시기 바랍니다. ]");
			$("#btnCheck_ok").unbind().bind("click", function () {
				applyservice_check_close();
			});
		}
		else {
			$("#check_title").html("청약 서비스 신청");
			$("#check_msg").html("해당 서비스를 신청하시겠습니까?");
			$("#btnCheck_ok").unbind().bind("click", function () {
				serviceAdditionStep1(serviceCd);
			});
		}
		
		dlgForm.dialog("open");
	}
	// 서비스 해지
	else if ( type == "s_cancel") {
		
		el_dialog	= $("#ord_del_step1");
		
		dialogInit(el_dialog, {width:480});
		
		el_dialog.dialog("open");
		
		// 삭제 버튼
		$(".ord_del_confirm", el_dialog).unbind("click").bind("click", function() {
			el_dialog.dialog("close");
			serviceCancel(serviceCd);
		});
		// 취소 버튼
		$(".ord_del_cancel", el_dialog).unbind("click").bind("click", function() {
			el_dialog.dialog("close");
		});
		
	}
	// 청약 해지
	else if ( type == "o_cancel" ) {
		
		el_dialog	= $("#ord_del_step1");
		
		dialogInit(el_dialog, {width:480});
		
		el_dialog.dialog("open");
		
		$(".ord_del_confirm", el_dialog).unbind("click").bind("click", function() {
			el_dialog.dialog("close");
			apOrdCancel(serviceCd);
		});
		$(".ord_del_cancel", el_dialog).unbind("click").bind("click", function() {
			el_dialog.dialog("close");
		});
	}
	// 현재 사용 안함
	else if ( type == "s_reCreate" ) {
		
		$("#check_title").html("사용자 생성 재시도");
		$("#check_msg").html("사용자 생성 재시도를 진행하시겠습니까?");
		$("#btnCheck_ok").unbind().bind("click", function () {
			reCreateUser(serviceCd);
		});
		dlgForm.dialog("open");
	}
	*/
	
}

// 안내창 닫기
function applyservice_check_close() {
	$("#applyservice_check").dialog("close");
}


function makeHtml() {
	var arrHtml		= [];

	arrHtml.push("	<!-- // POPUP AREA START -->");
	arrHtml.push("	<div class=\"popWrap\" id=\"applyservice_check\" style=\"display:none;\">");
	arrHtml.push("		<div class=\"t_bg\"></div>");
	arrHtml.push("		<div class=\"close\"></div>");
	arrHtml.push("		<div class=\"con\">");
	arrHtml.push("			<h1 id=\"check_title\"></h1>");
	arrHtml.push("			<div class=\"timg2\" id=\"tmpDiv\"></div>");
	arrHtml.push("			<div class=\"g_box2 \">");
	arrHtml.push("				<span id=\"check_msg\" style=\"padding-left:10px;\">서비스 신청 하시겠습니까?</span>");
	arrHtml.push("			</div>");
	arrHtml.push("			<div class=\"bimg\"></div>");
	arrHtml.push("			<p class=\"btns \">");
	arrHtml.push("				<a id=\"btnCheck_ok\" href=\"javascript:;\"><img src=\"/images/popup/btn_ok.gif\" title=\"확인\" /></a>");
	arrHtml.push("				<a id=\"btnCheck_cancel\" href=\"javascript:DeepZoomRegClose()\"><img src=\"/images/popup/btn_cancel.gif\" title=\"확인\" /></a>");
	arrHtml.push("			</p>");
	arrHtml.push("		</div>");
	arrHtml.push("		<div class=\"b_bg\"></div>");
	arrHtml.push("	</div>");
	arrHtml.push("	<!-- // POPUP AREA START -->");
	arrHtml.push("	<div class=\"popWrap\" id=\"applyservice_Msg\" style=\"display:none;\">");
	arrHtml.push("		<div class=\"t_bg\"></div>");
	arrHtml.push("		<div class=\"close\"></div>");
	arrHtml.push("		<div class=\"con\">");
	arrHtml.push("			<h1>청약 서버와 통신중</h1>");
	arrHtml.push("			<div class=\"timg2\" id=\"tmpDiv\"></div>");
	arrHtml.push("			<div class=\"g_box2 \">");
	arrHtml.push("				<table class=\"p_box\">");
	arrHtml.push("				<colgroup>");
	arrHtml.push("					<col width=\"150\">");
	arrHtml.push("					<col width=\"450\">");
	arrHtml.push("				</colgroup>");
	arrHtml.push("				<tbody>");
	arrHtml.push("					<tr>");
	arrHtml.push("						<td  class=\"alignR mgT30 mgB30 fh_20\">청약내용 조회</td>");
	arrHtml.push("						<td id=\"step1_loadding\" class=\"alignC mgT30 mgB30 fh_20\"><img src=\"../images/common/ajax-loader.gif\"/></td>");
	arrHtml.push("					</tr>");
	arrHtml.push("					<tr height=\"10px\" class=\"alignC mgT30 mgB30 fh_20\"><td colspan=\"2\"></td></tr>");
	arrHtml.push("					<tr>");
	arrHtml.push("						<td class=\"alignR mgT30 mgB30 fh_20\" id=\"text_action\"></td>");
	arrHtml.push("						<td id=\"step2_loadding\" class=\"alignC mgT30 mgB30 fh_20\">준비중</td>");
	arrHtml.push("					</tr>");
	arrHtml.push("				</tbody>");
	arrHtml.push("				</table>");
	arrHtml.push("			</div>");
	arrHtml.push("			<div class=\"bimg\"></div>");
	arrHtml.push("			<p class=\"btns \">");
	arrHtml.push("				<a id=\"btnApplyserviceOk\" href=\"javascript:serviceAdditionMsgClose();\"><img src=\"/images/popup/btn_ok.gif\" title=\"확인\" /></a>");
	arrHtml.push("			</p>");
	arrHtml.push("		</div>");
	arrHtml.push("		<div class=\"b_bg\"></div>");
	arrHtml.push("	</div>");
	arrHtml.push("	<!-- // POPUP AREA START -->");
	arrHtml.push("	<div class=\"popWrap\" id=\"applyservice_telno\" style=\"display:none;\">");
	arrHtml.push("		<div class=\"t_bg\"></div>");
	arrHtml.push("		<div class=\"close\"></div>");
	arrHtml.push("		<div class=\"con\">");
	arrHtml.push("			<h1 id=\"check_title\"></h1>");
	arrHtml.push("			<div class=\"timg2\" id=\"tmpDiv\"></div>");
	arrHtml.push("			<div class=\"g_box2 \">");
	arrHtml.push("				<span style=\"padding-left:10px;\">원활한 상담을 위해 연락처를 입력해 주세요</span>");
	arrHtml.push("				<div style=\"padding:10px;\">");
	arrHtml.push("					<input type=\"text\" name=\"memTelNo1\" id=\"memTelNo1\" value=\"\" maxlength=\"4\" style=\"height:20px; width:60px; border:1px solid #d8d8d8;ime-mode:disabled;\" /> - ");
	arrHtml.push("					<input type=\"text\" name=\"memTelNo2\" id=\"memTelNo2\" value=\"\" maxlength=\"4\" style=\"height:20px; width:60px; border:1px solid #d8d8d8;ime-mode:disabled;\" /> - ");
	arrHtml.push("					<input type=\"text\" name=\"memTelNo3\" id=\"memTelNo3\" value=\"\" maxlength=\"4\" style=\"height:20px; width:60px; border:1px solid #d8d8d8;ime-mode:disabled;\" />");
	arrHtml.push("				</div>");
	arrHtml.push("				<span id=\"telno_Msg\" style=\"padding-left:10px;color:#ff9922;font-weight:bold\"></span>");
	arrHtml.push("			</div>");
	arrHtml.push("			<div class=\"bimg\"></div>");
	arrHtml.push("			<p class=\"btns \">");
	arrHtml.push("				<a href=\"javascript:applyservice_telno_check();\"><img src=\"/images/popup/btn_ok.gif\" title=\"확인\" /></a>");
	arrHtml.push("				<a href=\"javascript:applyservice_telno_close();\"><img src=\"/images/popup/btn_cancel.gif\" title=\"취소\" /></a>");
	arrHtml.push("			</p>");
	arrHtml.push("		</div>");
	arrHtml.push("		<div class=\"b_bg\"></div>");
	arrHtml.push("	</div>");
	
	arrHtml.push("	<!-- // POPUP AREA START -->");
	arrHtml.push("	<div class=\"popWrap\" id=\"ord_del_step1\" style=\"display:none;\">");
	arrHtml.push("	    <div class=\"t_bg\"></div>");
	arrHtml.push("		<div class=\"close\"><a href=\"#\"><img src=\"../images/popup/close.gif\" title=\"close\" /></a></div>");
	arrHtml.push("		<div class=\"con\">");
	arrHtml.push("		    <h1><img src=\"../images/popup/tit_payinfo.gif\" title=\"청구정보 삭제\" /></h1>");
	arrHtml.push("			<p class=\"timg\"></p>");
	arrHtml.push("			<div class=\"ok_pbox03\">");
	arrHtml.push("				<p class=\"f_b\">해당 청구 정보를 삭제하시겠습니까?</p>");
	arrHtml.push("				<p class=\"ok_tbox01\"><span class=\"fc_red\">주의사항</span><br>청구 정보를 삭제할 경우 사용중인 상품을 다시 복구할 수 없습니다.<br>중요한 자료는 미리 백업 받아 놓으시기 바랍니다.</p>");
	arrHtml.push("			</div>");
	arrHtml.push("			<p class=\"bimg\"></p>");
	arrHtml.push("			<div class=\"btns\">");
	arrHtml.push("				<a href=\"#\" class=\"ord_del_confirm\"><img src=\"../images/btn/cbt24.gif\" title=\"해지신청\" /></a>");
	arrHtml.push("				<a href=\"#\" class=\"ord_del_cancel\"><img src=\"../images/btn/cancel.gif\" title=\"취소\" /></a>");
	arrHtml.push("			</div>");
	arrHtml.push("		</div>");
	arrHtml.push("		<div class=\"b_bg\"></div>");
	arrHtml.push("	</div>");
	arrHtml.push("	<!-- // POPUP AREA END -->");
	arrHtml.push("	 ");
	arrHtml.push("	<!-- // POPUP AREA START -->");
	arrHtml.push("	<div class=\"popWrap\" id=\"ord_del_succ\" style=\"display:none;\">");
	arrHtml.push("		<div class=\"t_bg\"></div>");
	arrHtml.push("		<div class=\"close\"><a href=\"#\"><img src=\"../images/popup/close.gif\" title=\"close\" /></a></div>");
	arrHtml.push("		<div class=\"con\">");
	arrHtml.push("			<h1><img src=\"../images/popup/tit_payinfo_com.gif\" title=\"청구정보 삭제 완료\" /></h1>");
	arrHtml.push("			<p class=\"timg\"></p>");
	arrHtml.push("			<div class=\"ok_pbox03\">");
	arrHtml.push("				<p class=\"f_b\">정상적으로 청구 정보 삭제가 완료되었습니다.</p>");
	arrHtml.push("				<ul class=\"ok_list02\">");
	arrHtml.push("					<li>금일까지 사용한 내역에 대한 과금은 다음달 청구됩니다.</li>");
	arrHtml.push("					<li>사용내역에 대한 상세한 안내는 <span class=\"f_b\">내정보관리>상품이용내역</span>에서<br>확인 가능합니다. 이용해주셔서 감사합니다.</li>");
	arrHtml.push("				</ul>");
	arrHtml.push("			</div>");
	arrHtml.push("			<p class=\"bimg\"></p>");
	arrHtml.push("			<div class=\"btns\">");
	arrHtml.push("				<a href=\"#\" class=\"ord_del_confirm\"><img src=\"../images/popup/btn_ok.gif\" title=\"확인\" /></a>");
	arrHtml.push("			</div>");
	arrHtml.push("		</div>");
	arrHtml.push("		<div class=\"b_bg\"></div>");
	arrHtml.push("	</div>");
	arrHtml.push("	<!-- // POPUP AREA END -->");
	arrHtml.push("	 ");
	arrHtml.push("	<!-- // POPUP AREA START -->");
	arrHtml.push("	<div class=\"popWrap\" id=\"ord_del_fail\" style=\"display:none;\">");
	arrHtml.push("		<div class=\"t_bg\"></div>");
	arrHtml.push("		<div class=\"close\"><a href=\"#\"><img src=\"../images/popup/close.gif\" title=\"close\" /></a></div>");
	arrHtml.push("		<div class=\"con\">");
	arrHtml.push("			<h1><img src=\"../images/popup/tit_payinfo_fail.gif\" title=\"청구정보 삭제 실패\" /></h1>");
	arrHtml.push("			<p class=\"timg\"></p>");
	arrHtml.push("			<div class=\"ok_pbox03\">");
	arrHtml.push("				<table class=\"table03 mgL20\">");
	arrHtml.push("	        	<colgroup>");
	arrHtml.push("	        		<col width=\"200\" />");
	arrHtml.push("	        		<col width=\"200\" />");
	arrHtml.push("	        	</colgroup>");
	arrHtml.push("	        	<tr><th>청약 내역 조회</th><td>완료</td></tr>");
	arrHtml.push("	        	</table>");
	arrHtml.push("				<table class=\"table03 mgL20\">");
	arrHtml.push("	        	<colgroup>");
	arrHtml.push("	        	<col width=\"200\" />");
	arrHtml.push("	        	<col width=\"200\" />");
	arrHtml.push("	        	</colgroup>");
	arrHtml.push("	        	<tbody class=\"del_vm_info\">");
	arrHtml.push("	        	</tbody>");
	arrHtml.push("	        	</table>");
	arrHtml.push("				<p class=\"del_vm_text\">위와 같이 현재 사용중인 상품이 존재하고 있습니다.<br>해당 상품을 해지하신 후 다시 시도하여  주시기 바랍니다.</p>");
	arrHtml.push("			</div>");
	arrHtml.push("			<p class=\"bimg\"></p>");
	arrHtml.push("			<div class=\"btns\">");
	arrHtml.push("				<a href=\"#\" class=\"ord_del_confirm\"><img src=\"../images/popup/btn_ok.gif\" title=\"확인\" /></a>");
	arrHtml.push("			</div>");
	arrHtml.push("		</div>");
	arrHtml.push("		<div class=\"b_bg\"></div>");
	arrHtml.push("	</div>");
	arrHtml.push("	<!-- // POPUP AREA END -->");	

	return arrHtml.join("");
}

function initApplyservice() {
	//$("body").append(makeHtml());
}

$(document).ready(function(){
	//$("#dialog").dialog();

	initApplyservice();
	
	//$("#deepzoom_reg").attr("href", "javascript:applyservice_check('s_add', 'S1820')");
	//alert($('#popWrap').attr('background-color','transparent'));
});


function DeepZoomReg(){
	//$.blockUI.defaults.css = {}; 
	$.blockUI({ message: $('#deepzoomreg'), css: { width: '0px' , height: '0px' ,padding:  '0px', border: '0px solid #ffffff'} }); 
}

function DeepZoomRegClose(){
	$.unblockUI(); 
}


function DeepZoomUrlCopy(di_ix) {	 
	$.ajax({ 
			type: 'GET', 
			data: {'mode': 'copy', 'di_ix':di_ix},
			url: './sample.php',  
			dataType: 'html', 
			async: true, 
			beforeSend: function(){ 
				
			},  
			success: function(data){ 
				//alert($('#row_'+wl_ix));
				//alert(data);
				try{
					window.clipboardData.setData('Text', data);
					alert(data+'\n 팁줌 스크립트가 클립보드에 복사되었습니다.!');
				}catch(e){
					//alert($(opener.document));
					//$('#calendar',opener.document).fullCalendar('removeEvents');
					//$('#calendar',opener.document).fullCalendar('removeEvent',wl_ix);
					
				}
			} 
		}); 

	 
}

function DeepZoomGalleryCopy(dgi_ix) {	 
	$.ajax({ 
			type: 'GET', 
			data: {'mode': 'copy', 'dgi_ix':dgi_ix},
			url: './gallery_sample.php',  
			dataType: 'html', 
			async: true, 
			beforeSend: function(){ 
				
			},  
			success: function(data){ 
				//alert($('#row_'+wl_ix));
				//alert(data);
				try{
					window.clipboardData.setData('Text', data);
					alert(data+'\n 팁줌 스크립트가 클립보드에 복사되었습니다.!');
				}catch(e){
					//alert($(opener.document));
					//$('#calendar',opener.document).fullCalendar('removeEvents');
					//$('#calendar',opener.document).fullCalendar('removeEvent',wl_ix);
					
				}
			} 
		}); 

	 
}



function DeleteIamge(di_ix){
	if(confirm('해당 이미지를 정말로 삭제하시겠습니까?')){
		$.ajax({ 
			type: 'GET', 
			data: {'act': 'delete', 'di_ix':di_ix},
			url: './deepzoom.act.php',  
			dataType: 'html', 
			async: true, 
			beforeSend: function(){ 
				
			},  
			success: function(data){ 
				//alert($('#row_'+wl_ix));
				//alert(data);
				try{
					$('#image_'+di_ix).slideUp();
				}catch(e){
					//alert($(opener.document));
					//$('#calendar',opener.document).fullCalendar('removeEvents');
					//$('#calendar',opener.document).fullCalendar('removeEvent',wl_ix);
					
				}
			} 
		}); 
		
	}
}

var treeImageGroupData = [];


	$.ajax({ 
		type: 'GET', 
		data: 
			{'act': 'get_department'
			},  
		url: './image.tree.php?mode=image_group',  
		dataType: 'json', 
		async: false, 
		beforeSend: function(){ 
			 //   $('#loading-dialog').dialog({minHeight: 100, width: 250}).dialog('open'); 
			 //   $('#loading-dialog p').text('Loading '+cal+' Calendar Events'); 
		},  
		success: function(tree_datas){ 
				//alert(tree_datas);
				treeImageGroupData = tree_datas;
				
		} 
	}); 

	$(function(){
		//alert(treeImageGroupData);
		$('#tree_image_group').dynatree({
			minexpandlevel:1,
			checkbox: true,
			selectMode: 3,
			persist: true,
			children: treeImageGroupData,
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

				group_ix = selKeys.join(',');

				
				var ajax_url = './index.php';
				var ajax_dataType = 'html';
				var list_view_type = 'list';
				var mmode = 'inner_list';
				

				$.ajax({ 
					type: 'GET', 
					data: {'list_view_type': list_view_type,'mmode': mmode,'list_type': list_type,'parent_group_ix': parent_group_ix,'group_ix': group_ix},  
					url: ajax_url,  
					dataType: ajax_dataType, 
					async: true, 
					beforeSend: function(){ 
						//$('#loading').show();
						$.blockUI.defaults.css = {}; 
						$.blockUI({ message: $('#loading'), css: { width: '0' , height: '0' ,padding:  '0px'} });  
						
						 //   $('#loading-dialog').dialog({minHeight: 100, width: 250}).dialog('open'); 
						 //   $('#loading-dialog p').text('Loading '+cal+' Calendar Events'); 
					},  
					success: function(datas){ 

							//alert(calevents);
							
							//alert(datas);
							$('#result_area').html(datas);
							//alert($('#result_area').html());
							$.unblockUI(); 
							

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
			cookieId: 'dynatree-image_group',
			idPrefix: 'dynatree-image_group-'
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


$(document).ready(function() {


	$('#tree_image_group').mouseover(function(){
		//$('#tree_work_group').animate({width:"200px",height:"300px"},500);
		$('#tree_image_group').css('position','absolute');
		$('#tree_image_group').css('z-index','100');
		$('#tree_image_group').css('width','200px');
		$('#tree_image_group').css('height','200px');
		$('#tree_image_group').css('background-color','#ffffff');
		$('#tree_image_group').css('border','2px solid silver');

	});

	$('#result_area').mouseover(function(){
		

		$('#tree_image_group').css('width','155px').delay(3000);
		$('#tree_image_group').css('height','150px').delay(3000);
		$('#tree_image_group').css('border','');
		$('#tree_image_group').css('z-index','0');
/*
		$('#work_tmp_box').css('width','153px').delay(3000);
		$('#work_tmp_input').css('width','153px').delay(3000);
		$('#work_tmp_box').css('z-index','0');
		$('#work_tmp_box').css('position','');
		$('#external-events').css('border','0px solid silver');
*/		
	});

});