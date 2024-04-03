<?

include("../class/layout.class");

$db = new Database;

$Contents ="
<table cellpadding=0 cellspacing=0 width='100%'>
	<tr>
		<td align='left'> ".GetTitleNavigation("제휴사엑셀주문등록", "주문관리 > 수동주문 > 제휴사엑셀주문등록")."</td>
	</tr>
	<tr>
		<td>
			<form name='../order/excel_input_form' method='post' action='orders_input_excel.act.php' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)' target=''>
			<input type='hidden' name='act' value='new_excel_input'>
			<table width='100%' border=0 cellpadding=0 cellspacing=1 class='input_table_box'>
				<col width=18%>
				<col width=*>
				<tr height=30 align=center>
					<td class='input_box_title' ><b>엑셀파일 입력</b></td>
					<td class='input_box_item'>
						".orderExcelTemplateSelect("I"," validation='true' ")." <input type=file class='textbox' name='excel_file' style='height:22px;width:200px;' validation='true' title='엑셀파일 입력'>
					</td>
				</tr>
			</table>
			<table width='100%' border=0 cellpadding=0 cellspacing=1>
				<tr height=20>
					<td style='padding:6px;line-height:140%;' colspan=2>
						<div>
						<ol>
							<li>
								<img src='../image/emo_3_15.gif' border=0 align=absmiddle>
								엑셀정보에는 <b>' (따옴표)</b>는 사용하실수 없습니다. <!--".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."-->
							</li>
						</ol>
						</div>
					</td>
				</tr>
				<tr height=30>
					<td colspan=2 style='padding:10px 0px;' align=center>
						<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0>
					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
	<tr>
		<td align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' align=absmiddle><b class=blk> 업로드 엑셀정보 </b>&nbsp;</div>")."</td>
	</tr>
	<tr>
		<td align=left style='padding-bottom:10px;'><div style='width:1200px;height:300px;overflow:auto;' id='excel_data_list'>".MakeUploadExcelData()."</div></td>
	</tr>
	<tr>
		<td align=center style='padding-bottom:10px;'><img src='../image/b_save.gif' alt='주문등록하기' onclick='UploadExcelOrdersReg();' style='cursor:pointer;'/></td>
	</tr>
</table>";



$script = "<Script Language='JavaScript'>


function InventoryGoodsJoin(no,data){
	$.ajax({
		type: 'GET', 
		data: {'act': 'single_orders_update', 'o_no':no, 'gid':data['gid'], 'gu_ix':data['gu_ix'], 'unit_text':data['unit_text'], 'gname':data['gname'], 'standard':data['standard']},
		url: './orders_input_excel.act.php',
		dataType: 'html',
		async: true,
		beforeSend: function(){
		},
		success: function(data){
			$('#inventory_text_'+no).html(data);
		},
		error:function(x, o, e){
			alert(x.status + ' : '+ o +' : '+e);
		}
	});
}

var UploadExcelOrdersReg_i = 0;
var o_no = new Array();

function UploadExcelOrdersReg(){
	$('.upload_excel_infos').each(function(i){
		o_no[i] = $(this).val();
	});

	UploadExcelOrdersRegAjax(o_no.length,UploadExcelOrdersReg_i);
}

function UploadExcelOrdersRegAjax(total_no,now_no){

	$.ajax({
		type: 'GET', 
		data: {'act': 'single_orders_reg', 'o_no':o_no[now_no]},
		url: './orders_input_excel.act.php',
		dataType: 'html',
		async: true,
		beforeSend: function(){
			$('#status_message_'+o_no[now_no]).html('주문등록 진행중...<img src=\'/admin/images/indicator.gif\' border=0 width=20 height=20 align=absmiddle> ')
		},
		success: function(data){
			UploadExcelOrdersReg_i++;			
			try{
				if(total_no > now_no){
					$('#status_message_'+o_no[now_no]).html(data);
					UploadExcelOrdersRegAjax(total_no,UploadExcelOrdersReg_i);
				}else{
					alert('등록이 완료 되었습니다.');
					/*
					if(confirm('등록완료되었습니다. 등록이 실패한 주문정보를 엑셀로 다운받으시겠습니까?')){
						location.href='./orders_input_excel.act.php?act=bad_orders_info_excel';
					}
					*/
				}
			}catch(e){
				alert(e.message);
			}
		},
		error:function(x, o, e){
			//alert(x.status + ' : '+ o +' : '+e);
			alert('네트워크문제가 발생되어 중단되었습니다. 페이지 새로고침 후 저장버튼을 다시 클릭해주세요.');
		}
	});

}

</script>";

function MakeUploadExcelData(){

	include("../logstory/class/sharedmemory.class");
	$shmop = new Shared("upload_orders_excel_data_".$_SESSION["admininfo"]["company_id"]);
	//	$shmop->clear();
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admininfo"]["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$upload_excel_data = $shmop->getObjectForKey("upload_orders_excel_data_".$_SESSION["admininfo"]["company_id"]);

	if($upload_excel_data[session_id()]){

		$mstring = "<table cellpadding=3 cellspacing=0 class='list_table_box' >";
		$i = 0;

		foreach($upload_excel_data[session_id()] as $key => $value){
			
			$mstring .= "<tr align=center height=25>";
			$mstring .= "<td ".($i == 0 ? "class=m_td style='padding:0px 50px;'  nowrap":" class='point' nowrap")." style='text-align:left;'> ".($i == 0 ? "처리현황":"<span id='status_message_".$value["o_no"]."'>".$value["status_message"]."</span>")."</td>";
			
			$mstring .= "<td ".($i == 0 ? "class=m_td nowrap":" nowrap").">".$value["order_from"]."</td>";
			$mstring .= "<td ".($i == 0 ? "class=m_td nowrap":" nowrap")." align='left' id='inventory_text_".$value["o_no"]."'>".$value["inventory_text"]."</td>";

			if(array_key_exists('tmp_pname', $value)){
				$mstring .= "<td ".($i == 0 ? "class=m_td nowrap":" nowrap")." align='left'>".$value["tmp_pname"]."</td>";
			}

			foreach($value as $_key => $_value){
				if(!in_array($_key,array("status_message","o_no","inventory_text","gid","gu_ix","order_from","tmp_pname","status"))){
					$mstring .= "<td ".($i == 0 ? "class='m_td' nowrap":" nowrap").">
										".@htmlspecialchars($_value)." ";
					if($_key == "co_oid" && $i != 0){
						$mstring .= "<input type=hidden class='upload_excel_infos' id='o_no' name='upload_excel_infos[".$value["o_no"]."][o_no]' value='".$value["o_no"]."' />";
					}
					$mstring .= "</td>";
				}
			}

			$mstring .= "</tr>";

			$i++;
		}
		$mstring .= "</table>";
	}

	return $mstring;

}

$P = new LayOut();
$P->addScript = $script;
if($view_type == 'inventory'){
$P->strLeftMenu = inventory_menu();
$P->Navigation = "WMS관리 > 주문관리 > 제휴사엑셀주문등록";
}else{
$P->strLeftMenu = order_menu();
$P->Navigation = "주문관리 > 수동주문 > 제휴사엑셀주문등록";
}
$P->title = "제휴사엑셀주문등록";
$P->strContents = $Contents;
$P->PrintLayOut();



?>