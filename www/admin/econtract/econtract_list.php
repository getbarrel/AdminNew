<?
include("../class/layout.class");

$db = new MySQL;
$mdb = new MySQL;
/*
$sql = "select ei.* from econtract_info ei where com_ceo = ''  ";

$mdb->query($sql);
$modify_econtract = $mdb->fetchall();

for($i=0;$i < count($modify_econtract);$i++){
	$db->query("Select * from common_company_detail where company_id ='".$modify_econtract[$i][company_id]."'");
	$db->fetch();
	$company_id = $db->dt[company_id];
	$com_name = $db->dt[com_name];
	$com_ceo = $db->dt[com_ceo];
	$com_zip = $db->dt[com_zip];
	$com_addr1 = $db->dt[com_addr1];
	$com_addr2 = $db->dt[com_addr2];
	$com_reg_no = $db->dt[com_number];

	$db->query("Select * from common_company_detail where company_id ='".$modify_econtract[$i][contractor_id]."'");
	$db->fetch();
	$contractor_id = $db->dt[company_id];
	$contractor_name = $db->dt[com_name];
	$contractor_ceo = $db->dt[com_ceo];
	$contractor_zip = $db->dt[com_zip];
	$contractor_addr1 = $db->dt[com_addr1];
	$contractor_addr2 = $db->dt[com_addr2];
	$contractor_reg_no = $db->dt[com_number];

$sql = "update econtract_info set
			com_ceo='$com_ceo',
			com_zip='$com_zip',
			com_addr1='$com_addr1',
			com_addr2='$com_addr2',
			com_reg_no='$com_reg_no',
			
			contractor_ceo='$contractor_ceo',
			contractor_zip='$contractor_zip',
			contractor_addr1='$contractor_addr1',
			contractor_addr2='$contractor_addr2',
			contractor_reg_no='$contractor_reg_no' 
			where ei_ix='".$modify_econtract[$i][ei_ix]."' ";
			$mdb->query($sql);
	//echo nl2br($sql);
}
*/
$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
if ($use_sdate == "1"){
	if(!$contract_sdate || !$contract_edate){ 
		$contract_sdate = date("Ymd", $before10day);
		$contract_edate = date("Ymd");	
	}
}

if ($use_edate == "1"){
	if(!$st_end_sdate || !$st_end_edate){ 
		$st_end_sdate = date("Ymd", $before10day);
		$st_end_edate = date("Ymd");	
	}
}

if($page_title == ""){
	$page_title = "전자계약 목록";
}
$Script = "
<link rel='stylesheet' type='text/css' href='/admin/v3/css/jquery-ui.css' />
<script type='text/javascript' src='/js/ui/jquery-ui-1.8.9.custom.js'></script>
<script language='javascript' src='/admin/js/jquery.form.min.js'></script>
<script language='javascript'>

 function econtractAllCheck(frm){ 
	if($('form[name='+frm.name+']').find('input#all').attr('checked') == 'checked'){

		$('form[name='+frm.name+']').find('input#ei_ix').each(function(){
			if(!$(this).attr('disabled')){
				$(this).attr('checked','checked');
			}
		});
	}else{ 
		$('form[name='+frm.name+']').find('input#ei_ix').each(function(){
			if(!$(this).attr('disabled')){
				$(this).attr('checked',false);
			}
		});
	}
}

function econtractDelete(ei_ix){
	if(confirm('해당 전자계약서를 삭제하시겠습니까?')){
		window.frames['act'].location.href= 'contract.act.php?act=deleteContract&ei_ix='+ei_ix;
	}
}


function setSelectDate(sdate,edate,date_type) {
	var frm = document.search_contract;
	if(date_type == 1){
		$(\"#contract_sdate\").val(sdate);
		$(\"#contract_edate\").val(edate);
	}
}


function searchUseSdate(frm){
	if($('#use_sdate').attr('checked') == 'checked'){
		$('#contract_sdate').attr('disabled',false);
		$('#contract_edate').attr('disabled',false);	 
	}else{
		$('#contract_sdate').val('').attr('disabled',true);
		$('#contract_edate').val('').attr('disabled',true);
	}
}

 
 $(document).ready(function (){

		if($('#use_sdate').attr('checked') == 'checked'){
			$('#contract_sdate').attr('disabled',false);
			$('#contract_edate').attr('disabled',false);
		}else{
			$('#contract_sdate').removeClass('point_color').val('').attr('disabled',true);
			$('#contract_edate').removeClass('point_color').val('').attr('disabled',true);
		}
 
		ChangeUpdateForm($('#update_kind_cancel'), $('#update_bulk_cancel'));
});

function ChangeUpdateForm(thisObj, selectedObject){
	$('.update_form').hide();
	$(selectedObject).show();
	
	//alert(thisObj.val());
	if(thisObj.val() == 'bulk_cancel'){ 
		$('input[class=ei_ix]').attr('disabled',false);
		//alert($('input[class=ei_ix][com_signature=1]').length);
		$('input[class=ei_ix][contractor_signature=1]').attr('disabled','disabled');
		$('input[class=ei_ix][com_signature=1]').attr('disabled','disabled');
	}else if(thisObj.val() == 'bulk_return'){ 
		$('input[class=ei_ix]').attr('disabled',false);
		$('input[class=ei_ix][com_signature=0][contractor_signature=0]').attr('disabled','disabled');
		$('input[class=ei_ix][com_signature=1][contractor_signature=1]').attr('disabled','disabled');
	}
	
}


function cancelContract(){
	$('#change_status').val('CRS');

	$('#econtract_list').ajaxSubmit({
		type:'POST',
		url:'contract.act.php',
		dataType:  'json', 
		success: function(data) { 
			alert(data.message);
			$('#change_status').val('');
			document.location.reload();
			//$('#search_excel_file').val('');
		},
		error:function(xhr){ 
			//alert('error');
		}
	}); 	 
}

function returnContract(){
	$('#change_status').val('CRT');

	$('#econtract_list').ajaxSubmit({
		type:'POST',
		url:'contract.act.php',
		dataType:  'json', 
		success: function(data) { 
			alert(data.message);
			$('#change_status').val('');
			document.location.reload();
			//$('#search_excel_file').val('');
		},
		error:function(xhr){ 
			//alert('error');
		}
	}); 
	 
}



</script>";


$mstring ="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation($page_title, "전자계약 승인관리 > 전자계약 관리")."</td>
		</tr>
		<tr>
			<td>";
		$mstring .= "
		<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
			<tr>
				<th class='box_01'></th>
				<td class='box_02'></td>
				<th class='box_03'></th>
			</tr>
			<tr>
				<th class='box_04'></th>
				<td class='box_05'  valign=top style='padding:5px'>
				<form name=search_contract method='get' ><!--SubmitX(this);'-->
				<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
					<col width='15%'>
					<col width='35%'>
					<col width='15%'>
					<col width='35%'> 
					<tr height=30>
					  <td class='search_box_title'>검색조건 </td>
					  <td class='search_box_item'   >
						  <select name=search_type>
								<option value='' ".CompareReturnValue("",$search_type,"selected")." style='vertical-align:middle;'>검색조건</option>
								<option value='contract_title' ".CompareReturnValue("contract_title",$search_type,"selected")." style='vertical-align:middle;'>계약서 명</option>
						  </select>
						  <input type=text name='search_text' class='textbox' value='".$search_text."' style='width:30% ; vertical-align:top;' >
					  </td>
					   <td class='search_box_title'>진행여부 </td>
					  <td class='search_box_item' >
					  <input type=radio name='is_ing' value='' id='is_ing_a'  ".CompareReturnValue("",$is_ing,"checked")."><label for='is_ing_a'>전체</label>
					  <input type=radio name='is_ing' value='1' id='is_ing_1'  ".CompareReturnValue("1",$is_ing,"checked")."><label for='is_ing_1'>진행중</label>
					  <input type=radio name='is_ing' value='0' id='is_ing_0' ".CompareReturnValue("0",$is_ing,"checked")."><label for='is_ing_0'>진행완료</label>
					  </td>
					</tr>
					<tr >
					  <td class='search_box_title' >  <b>계약서 종류</b></td>
					  <td class='search_box_item'>
							<input type='radio' name='contract_type' id='contract_type_'  align='middle' value='' ".($contract_type == '' ? "checked":"")."><label for='contract_type_' class='green'>전체</label> 
							<input type='radio' name='contract_type' id='contract_type_1'  align='middle' value='1' ".($contract_type == '1' ? "checked":"")."><label for='contract_type_1' class='green'>일반계약서</label> 
							<input type='radio' name='contract_type' id='contract_type_2'  align='middle' value='2' ".($contract_type == '2' ? "checked":"")."><label for='contract_type_2' class='green'>첨부서류</label> 
					  </td>
					  <td class='search_box_title'>담당자</td>
						<td class='search_box_item'>
						".CompayChargerSearch($_SESSION["admininfo"]["company_id"] ,$charger_ix,"","selectbox")."
						</td>
					</tr>
					<tr height=30>
					  <td class='search_box_title'>계약서 처리상태 </td>
					  <td class='search_box_item' >
					  <!--input type=checkbox name='econtract_status' value='' id='econtract_status_a'  ".CompareReturnValue("",$econtract_status,"checked")."><label for='econtract_status_a'>전체</label-->
					  <input type=checkbox name='econtract_status' value='CA' id='econtract_status_y'  ".CompareReturnValue("CA",$econtract_status,"checked")."><label for='econtract_status_y'>계약서 요청</label>
					  <input type=checkbox name='econtract_status' value='CRS' id='econtract_status_n' ".CompareReturnValue("CRS",$econtract_status,"checked")."><label for='econtract_status_n'>계약서 취소</label>
					  <input type=checkbox name='econtract_status' value='CRT' id='econtract_status_n' ".CompareReturnValue("CRT",$econtract_status,"checked")."><label for='econtract_status_n'>계약서 반려</label>
					  <input type=checkbox name='econtract_status' value='CC' id='econtract_status_n' ".CompareReturnValue("CC",$econtract_status,"checked")."><label for='econtract_status_n'>계약서 완료</label>
					  </td >
					  <td class='search_box_title'>설러명</td>
					  <td class='search_box_item' >
					  ".companyAuthList($contractor_id , "validation=true title='계약자(을)' ","contractor_id", "contractor_id","contractor_name", "contractor_info")."
					  </td>
					</tr>
					
					<tr height=30>
					  <td class='search_box_title'>계약서(갑) 서명상태 </td>
					  <td class='search_box_item' >
					  <input type=radio name='com_signature' value='' id='com_signature'  ".CompareReturnValue("",$com_signature,"checked")."><label for='com_signature'>전체</label>
					  <input type=radio name='com_signature' value='Y' id='com_signature_y' ".CompareReturnValue("Y",$com_signature,"checked")."><label for='com_signature_y'>서명함</label>
					  <input type=radio name='com_signature' value='N' id='com_signature_n' ".CompareReturnValue("N",$com_signature,"checked")."><label for='com_signature_n'>서명안함</label>
					  </td >
					  <td class='search_box_title'>계약서(을) 서명상태</td>
					  <td class='search_box_item' >
					  <input type=radio name='contractor_signature' value='' id='contractor_signature'  ".CompareReturnValue("",$contractor_signature,"checked")."><label for='contractor_signature'>전체</label>
					  <input type=radio name='contractor_signature' value='Y' id='contractor_signature_y' ".CompareReturnValue("Y",$contractor_signature,"checked")."><label for='contractor_signature_y'>서명함</label>
					  <input type=radio name='contractor_signature' value='N' id='contractor_signature_n' ".CompareReturnValue("N",$contractor_signature,"checked")."><label for='contractor_signature_n'>서명안함</label>
					  </td>
					</tr>
					"; 
 $mstring .= "
					<tr height=27>
						<td class='search_box_title' align=center><label for='use_sdate'><b>계약서 작성일</b></label><input type='checkbox' name='use_sdate' id='use_sdate' value='1' onclick='searchUseSdate(document.search_contract);' ".($use_sdate == "1" ? "checked":"")."></td>
						<td class='search_box_item' colspan=3  >
						".search_date('contract_sdate','contract_edate',$contract_sdate,$contract_edate,'N','D')."									
						</td>
					</tr> 
					</table>
				</td>
				<th class='box_06'></th>
			</tr>
			<tr>
				<th class='box_07'></th>
				<td class='box_08'></td>
				<th class='box_09'></th>
			</tr>
			<tr >
				<td style='padding:10px 0px;' colspan=3 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
			</tr>
		</table>
		</form> 
			</td>
		</tr> 
		<tr>
			<td align='right'>";
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
					$mstring .= "<a href='?mode=excel&".str_replace("mode=".$mode, "mode=excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
				}else{
					$mstring .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
				}
				 $mstring .= "
			</td>
		</tr>  
		</table>";

if($status == ""){
			$select = "
				<select name='update_type' >
					<option value='2' selected>선택한 상품 전체에</option>
					<option value='1' >검색한 상품 전체에</option>
				</select>";


			$select .= "<!--input type='radio' name='update_kind' id='update_kind_sign' value='bulk_sign' checked onclick=\"ChangeUpdateForm($(this), $('#update_bulk_sign'));\"><label for='update_kind_sign'>본사서명</label-->
			<input type='radio' name='update_kind' id='update_kind_cancel' value='bulk_cancel'  onclick=\"ChangeUpdateForm($(this), $('#update_bulk_cancel'));\" checked><label for='update_kind_cancel'>계약서 취소</label>
			<input type='radio' name='update_kind' id='update_kind_return' value='bulk_return'  onclick=\"ChangeUpdateForm($(this), $('#update_bulk_return'));\"><label for='update_kind_return'>계약서 반려</label>";


			if($update_kind_type == ''){
			$help_text .= "
			<div id='update_bulk_sign' class='update_form' ".($update_kind == "bulk_sign" ? "style='display:block;padding-top:10px;'":"style='display:none'")." >
				<div style='padding:4px 0 4px 0'>
					<img src='../images/dot_org.gif'> <b>일괄 인증처리</b> 
					<span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
				</div> 
				<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
				<col width='20%'>
				<col width='*'>
				<tr height=30>
					<td class='input_box_title'> 인증처리상태</td>
					<td class='input_box_item' style='line-height:150%;padding:10px;'> 			 
						서명하기
					</td>
				</tr>
				</table> ";
			if(checkMenuAuth(md5("/admin/product/goods_input.php"),"U") && checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
			$help_text .= "
				<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
					<tr height=50><td colspan=4 align=center><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' onclick=''></td></tr>
				</table>";
			}else{
				$help_text .= "<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
					<tr height=50><td align=center><a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle ></a></td></tr>
				</table>";
			}	
			$help_text .= "
			</div>
			<div id='update_bulk_cancel' class='update_form' ".($update_kind == "bulk_cancel"  || $update_kind == "" ? "style='display:block;padding-top:10px;'":"style='display:none;padding-top:10px;'")." >
				<div style='padding:4px 0 4px 0'>
					<img src='../images/dot_org.gif'> <b>일괄 계약취소 </b> 
					<span class=small style='color:gray'>  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
				</div> 
				<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
				<col width='20%'>
				<col width='*'>
				<tr height=30>
					<td class='input_box_title'> 취소사유</td>
					<td class='input_box_item' style='line-height:150%;padding:10px;'>
						<input type='text' class='textbox' name='cancel_message' id='cancel_message'  value='' style='width:400px;'>
					</td>
				</tr>
				</table> ";
			if(checkMenuAuth(md5("/admin/product/goods_input.php"),"U") && checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
			$help_text .= "
				<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
					<tr height=50><td colspan=4 align=center><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' onclick='cancelContract();'></td></tr>
				</table>";
			}else{
				$help_text .= "<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
					<tr height=50><td align=center><a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle ></a></td></tr>
				</table>";
			}	
			$help_text .= "
			</div>
			<div id='update_bulk_return' class='update_form' ".($update_kind == "bulk_return" ? "style='display:block;padding-top:10px;'":"style='display:none;padding-top:10px;'")." >
				<div style='padding:4px 0 4px 0'>
					<img src='../images/dot_org.gif'> <b>일괄 계약취소 </b> 
					<span class=small style='color:gray'>  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
				</div> 
				<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
				<col width='20%'>
				<col width='*'>
				<tr height=30>
					<td class='input_box_title'> 반려사유</td>
					<td class='input_box_item' style='line-height:150%;padding:10px;'>
						<input type='text' class='textbox' name='return_message' value='' style='width:400px;'>
					</td>
				</tr>
				</table>";
			if(checkMenuAuth(md5("/admin/product/goods_input.php"),"U") && checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
			$help_text .= "
				<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
					<tr height=50><td colspan=4 align=center><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' onclick='returnContract();'></td></tr>
				</table>";
			}else{
				$help_text .= "<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
					<tr height=50><td align=center><a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle ></a></td></tr>
				</table>";
			}	
			$help_text .= "
			</div>";

			}
			 
			$help_text = HelpBox("<table cellpadding='0' cellspacing='0' border='0'><tr><td ><b>".$select."</b></td></tr></table>", $help_text,400);

			$mstring .= "<div id=''>
							<form  name='econtract_list' id='econtract_list'>
							<input type=hidden name='act' value='changeContractStatus'>
							<input type=hidden name='change_status' id='change_status' value=''>
							
							";
			$mstring .= getContractList();
if($_SESSION["admininfo"]["admin_level"] == 9){	
			$Contents = $mstring.$help_text;
}else{
			$Contents = $mstring;
}
			$Contents .= "</div></form>";
}else{
	$mstring .= "<div id=''>
						<form  name='econtract_list' id='econtract_list'>
						<input type=hidden name='act' value='changeContractStatus'>
						<input type=hidden name='change_status' id='change_status' value=''>
						
						";
		$mstring .= getContractList();

		$Contents = $mstring.$help_text;
		$Contents .= "</div></form>";
}
$P = new LayOut();
$P->addScript = "".$Script;
$P->OnloadFunction = "";
$P->Navigation = "전자계약 관리약 > 전자계약 관리";
$P->title = $page_title;//"전자계약 목록";
$P->strLeftMenu = econtract_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

function getContractList(){
	global $db, $mdb, $page, $search_type, $status;
	global $auth_delete_msg, $auth_excel_msg, $admininfo;
	global $_CONTRACT_STATUS;
	global $check_date, $sub_status, $mode;


	$where = " where ei_ix <> '0'   ";
	

	if($status != ""){
		$where .= " and ei.status =  '".$status."' ";
	}else{
		$where .= " and status <> 'CRM' ";
	}

	if($_GET["contractor_id"] != ""){
		$where .= " and ei.contractor_id =  '".$_GET["contractor_id"]."' ";
	}
	
	if($_GET["contract_type"] != ""){
		$where .= " and ei.contract_type =  '".$_GET["contract_type"]."' ";
	}

	if($_GET["charger_ix"] != ""){
		$where .= " and ei.charger_ix =  '".$_GET["charger_ix"]."' ";
	}

	if($_GET["disp"] != ""){
		$where .= " and ei.disp =  '".$_GET["disp"]."' ";
	}

	if($_GET["search_type"] != "" && $_GET["search_text"] != ""){
		$where .= " and ".$_GET["search_type"]." LIKE  '%".$_GET["search_text"]."%' ";
	}
	
	if($_GET["contractor_signature"] !=""){
		if($_GET["contractor_signature"] =='Y'){
			$where .= " and contractor_signature !='' ";
		}else{
			$where .= " and contractor_signature = '' ";
		}
	}
	if($_GET["com_signature"] !=""){
		if($_GET["com_signature"] =='Y'){
			$where .= " and com_signature !='' ";
		}else{
			$where .= " and com_signature = '' ";
		}
	}
	if($_GET["contract_sdate"] != "" && $_GET["contract_edate"] != ""){
		//$unix_timestamp_start_sdate = mktime(0,0,0,substr($_GET["contract_sdate"],4,2),substr($_GET["contract_sdate"],6,2),substr($_GET["contract_sdate"],0,4));
		//$unix_timestamp_start_edate = mktime(0,0,0,substr($_GET["contract_edate"],4,2),substr($_GET["contract_edate"],6,2),substr($_GET["contract_edate"],0,4));

		$where .= " and  contract_date between '".$_GET["contract_sdate"]."' and '".$_GET["contract_edate"]."' ";
	}

	if($check_date){
		$where .= " and  date_sub(contract_edate, INTERVAL ". $check_date." DAY) < '".date("Y-m-d")."' ";
	}

	if($sub_status == "CE"){
		$where .= " and  contract_edate < '".date("Y-m-d")."' ";
	}
	
	if($_SESSION["admininfo"]["admin_level"] < 9){	
		$where .= " and contractor_id = '".$_SESSION["admininfo"]["company_id"]."' ";
	}
	 

	$sql = "select ei.* from econtract_info ei $where";

	//echo nl2br($sql);
	$mdb->query($sql);
	$total = $mdb->total;

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}
	
	if($_SERVER["QUERY_STRING"] == "nset=$nset&page=$page"){
		$query_string = str_replace("nset=$nset&page=$page","",$_SERVER["QUERY_STRING"]) ;
	}else{
		$query_string = str_replace("nset=$nset&page=$page&","","&".$_SERVER["QUERY_STRING"]) ;
	}
	$str_page_bar = page_bar($total, $page, $max, $query_string,"");


	$mString = "<table cellpadding=0 cellspacing=0 border=0 width=100%  class='list_table_box'>";
	$mString .= "
	<col width='3%'> 
	<col width='7%'> 
	<col width='8%'>
	<col width='*'>
	<col width='6%'>
	<col width='10%'>
	<col width='8%'>
	<col width='8%'>
	<col width='9%'>
	
	<col width='8%'>
	<col width='6%'>
	<col width='12%'>
	<tr align=center bgcolor=#efefef height='30'>
		<td class='s_td' rowspan=2><input type='checkbox' name='all' id='all'  style='border:0' onclick='econtractAllCheck(document.econtract_list)'></td>
		<td class=m_td rowspan=2>계약 요청일</td> 
		<td class=m_td rowspan=2>분류</td>
		<td class=m_td rowspan=2>계약서명</td>
		<td class=m_td rowspan=2>담당자</td>";
$mString .= "
		<td class=m_td rowspan=2>계약일자</td>";

$mString .= "
		<td class=m_td colspan=2>계약자(갑)</td>
		<td class=m_td colspan=2>계약자(을)</td>
		<td class=m_td rowspan=2>처리상태</td>
		<td class=e_td rowspan=2>관리</td>
	</tr>
	<tr height='30'>
		<td class=m_td>계약자명</td>
		<td class=m_td>서명</td> 
		<td class=m_td>계약자명</td>
		<td class=m_td>서명</td>
	</tr>";


	if ($total == 0){
		$mString .= "<tr bgcolor=#ffffff height=70><td colspan=12 align=center>전자계약서가 존재 하지 않습니다.</td></tr>";
		$mString .= "</table>";
		$mString .= "<table cellpadding=0 cellspacing=0 border=0 width=100%  >";
		//$mString .= "<tr bgcolor=#ffffff ><td colspan=5 align=right style='padding:10px 0px;'><a href='search_text.php'><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0 ></a></td></tr>";

	}else{

		$sql = "select ei.*, eg.group_name , AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name  , ccd.com_name as com_name, contract_ccd.com_name as contractor_name
					from econtract_info ei 
					left join econtract_group eg on ei.contract_group = eg.group_ix  
					left join common_company_detail ccd on ei.company_id = ccd.company_id
					left join common_company_detail contract_ccd on ei.contractor_id = contract_ccd.company_id
					left join common_member_detail cmd on ei.charger_ix = cmd.code
					$where 
					order by regdate desc ";
		if($mode != "excel"){
			$sql .= "
			LIMIT $start, $max";
		}
		$sql .= "
			";
					
		
		$db->query($sql);
if($mode == "excel"){

	
	include '../include/phpexcel/Classes/PHPExcel.php';
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

	date_default_timezone_set('Asia/Seoul');

	$inventory_excel = new PHPExcel();

	// 속성 정의
	$inventory_excel->getProperties()->setCreator("포비즈 코리아")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle("accounts plan price List")
								 ->setSubject("accounts plan price List")
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory("accounts plan price List");
	$col = 'A';
	
	
	$inventory_excel->getActiveSheet(0)->mergeCells('A1:A2');
	$inventory_excel->getActiveSheet(0)->mergeCells('B1:B2');
	$inventory_excel->getActiveSheet(0)->mergeCells('C1:C2');
	$inventory_excel->getActiveSheet(0)->mergeCells('D1:D2');
	$inventory_excel->getActiveSheet(0)->mergeCells('E1:E2');
	$inventory_excel->getActiveSheet(0)->mergeCells('F1:H1');
	$inventory_excel->getActiveSheet(0)->mergeCells('G1:H1');
	$inventory_excel->getActiveSheet(0)->mergeCells('I1:J1');
	$inventory_excel->getActiveSheet(0)->mergeCells('K1:K2');
	$inventory_excel->getActiveSheet(0)->mergeCells('L1:L2');
	$inventory_excel->getActiveSheet(0)->mergeCells('M1:M2');
	
	$inventory_excel->getActiveSheet(0)->setCellValue('G' . 1, "계약자(갑)");
	$inventory_excel->getActiveSheet(0)->setCellValue('I' . 1, "계약자(을)");

	$inventory_excel->getActiveSheet(0)->setCellValue('A' . 2, "순");
	$inventory_excel->getActiveSheet(0)->setCellValue('B' . 2, "계약요청일");
	$inventory_excel->getActiveSheet(0)->setCellValue('C' . 2, "분류");
	$inventory_excel->getActiveSheet(0)->setCellValue('D' . 2, "계약서명");
	$inventory_excel->getActiveSheet(0)->setCellValue('E' . 2, "담당자");
	$inventory_excel->getActiveSheet(0)->setCellValue('F' . 2, "계약일자");
	$inventory_excel->getActiveSheet(0)->setCellValue('G' . 2, "계약자명");
	$inventory_excel->getActiveSheet(0)->setCellValue('H' . 2, "서명");
	$inventory_excel->getActiveSheet(0)->setCellValue('I' . 2, "계약자명");
	$inventory_excel->getActiveSheet(0)->setCellValue('J' . 2, "서명");
	$inventory_excel->getActiveSheet(0)->setCellValue('K' . 2, "처리상태");
	$inventory_excel->getActiveSheet(0)->setCellValue('L' . 2, "전화번호");
	$inventory_excel->getActiveSheet(0)->setCellValue('M' . 2, "담당자");

/*	$inventory_excel->getActiveSheet(0)->setCellValue('F' . 2, "수량");
	$inventory_excel->getActiveSheet(0)->setCellValue('G' . 2, "단가");
	$inventory_excel->getActiveSheet(0)->setCellValue('H' . 2, "금액");
	$inventory_excel->getActiveSheet(0)->setCellValue('I' . 2, "수량");
	$inventory_excel->getActiveSheet(0)->setCellValue('J' . 2, "단가");
	$inventory_excel->getActiveSheet(0)->setCellValue('K' . 2, "금액");
	$inventory_excel->getActiveSheet(0)->setCellValue('L' . 2, "수량");
	$inventory_excel->getActiveSheet(0)->setCellValue('M' . 2, "단가");
	$inventory_excel->getActiveSheet(0)->setCellValue('N' . 2, "금액");
	$inventory_excel->getActiveSheet(0)->setCellValue('O' . 2, "수량");
	
*/
	$before_pid = "";

	for ($i = 0; $i < $db->total; $i++)
	{
		
		$db->fetch($i);
			
		$sql = "SELECT 
					* 
				FROM 
					".TBL_COMMON_COMPANY_DETAIL." as  ccd 
					inner join ".TBL_COMMON_SELLER_DETAIL." as sd on (ccd.company_id = sd.company_id)	
				where 
					ccd.company_id = '".$db->dt[contractor_id]."'";
		$mdb->query($sql);
		$mdb->fetch();
		
		
		if($db->dt[com_signature] !=''){
			$com_signature = "서명완료";
			$signature_date = $db->dt[signature_date];
		}else{
			$com_signature = "서명안함";
		}
		if($db->dt[contractor_signature] != ''){
			$contractor_signature = "서명완료";
			$contractor_signature_date = $db->dt[contractor_signature_date];
		}else{
			$contractor_signature = "서명안함";
		}

		$inventory_excel->getActiveSheet()->setCellValue('A' . ($i + 3), $i+1);
		$inventory_excel->getActiveSheet()->setCellValue('B' . ($i + 3), $db->dt[contract_date]);
		$inventory_excel->getActiveSheet()->setCellValue('C' . ($i + 3), $db->dt[group_name]);		
		$inventory_excel->getActiveSheet()->setCellValue('D' . ($i + 3), $db->dt[contract_title]);
		$inventory_excel->getActiveSheet()->setCellValue('E' . ($i + 3), $db->dt[name]);
		$inventory_excel->getActiveSheet()->setCellValue('F' . ($i + 3), $db->dt[contract_sdate]."~".$db->dt[contract_edate]);
		$inventory_excel->getActiveSheet()->setCellValue('G' . ($i + 3), $db->dt[com_name]);
		$inventory_excel->getActiveSheet()->setCellValue('H' . ($i + 3), $com_signature."  ".$com_signature_date);
		$inventory_excel->getActiveSheet()->setCellValue('I' . ($i + 3), $db->dt[contractor_name]);
		$inventory_excel->getActiveSheet()->setCellValue('J' . ($i + 3), $contractor_signature."  ".$contractor_signature_date);
		$inventory_excel->getActiveSheet()->setCellValue('K' . ($i + 3), $_CONTRACT_STATUS[$db->dt[status]]);
		$inventory_excel->getActiveSheet()->setCellValue('L' . ($i + 3), $mdb->dt[tax_person_name]);
		$inventory_excel->getActiveSheet()->setCellValue('M' . ($i + 3), $mdb->dt[tax_person_phone]);
	 
	}

	// 첫번째 시트 선택
	$inventory_excel->setActiveSheetIndex(0);

	// 너비조정
	$col = 'A';
	
	
	$inventory_excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$inventory_excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$inventory_excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$inventory_excel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
	

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.iconv("utf-8","cp949","전자계약서 목록.csv").'"');
	header('Cache-Control: max-age=0');

	//$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'Excel5');
	$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'CSV');
	$objWriter->setUseBOM(true);
	$objWriter->save('php://output');

	exit;
}
		
		$contract_infos = $db->fetchall();

		for($i=0;$i < count($contract_infos);$i++){

			$no = $total - ($page - 1) * $max - $i;

			$mString = $mString."<tr height=40 bgcolor=#ffffff align=center>
			<td class='list_box_td point'><input type='checkbox' name='ei_ix[]' style='border:0px' class='ei_ix' id='ei_ix' value='".$contract_infos[$i][ei_ix]."' ".($contract_infos[$i][com_signature] ? "com_signature=1":"com_signature=0")." ".($contract_infos[$i][contractor_signature] ? "contractor_signature=1":"contractor_signature=0")."><!--  ".(($contract_infos[$i][use_yn] == 0 && $useable_yn == 1) ? "":"disabled")."--></td>
			<td class='list_box_td list_bg_gray'>".$contract_infos[$i][contract_date]."</td>";
	$mString .= "
			<td class='list_box_td '>".$contract_infos[$i][group_name]."</td>
			<td class='list_box_td '>".$contract_infos[$i][contract_title]."</td>
			<td class='list_box_td '>".$contract_infos[$i][name]." </td>
			<td class='list_box_td point' nowrap>".$contract_infos[$i][contract_sdate]." ~ <br>".$contract_infos[$i][contract_edate]."</td>
			<td class='list_box_td '>".$contract_infos[$i][com_name]." </td>			
			<td class='list_box_td list_bg_gray' style='padding:5px;'>";
			if($_SESSION["admininfo"]["company_id"] == $contract_infos[$i][company_id]){	
$mString .= "
			".($contract_infos[$i][com_signature] ? "<a href=\"javascript:PoPWindow3('econtract_signed_view.php?mmode=pop&sign_type=H&ei_ix=".$contract_infos[$i][ei_ix]."',1100,800,'main_goods')\"><img src='../v3/images/".$admininfo["language"]."/btn_signed_view.gif' align=absmiddle style='padding-bottom:3px;'></a><br>".$contract_infos[$i][signature_date]:"<a href=\"javascript:PoPWindow3('econtract_object_check.php?mmode=pop&ei_ix=".$contract_infos[$i][ei_ix]."',1100,800,'main_goods')\"><img src='../v3/images/".$admininfo["language"]."/btn_sign.gif' align=absmiddle></a>")."";
			}else{
$mString .= "
			".($contract_infos[$i][com_signature] ? "서명완료":"서명대기")."";
			}
$mString .= "			
			</td>
			<td class='list_box_td list_bg_gray'>".$contract_infos[$i][contractor_name]."</td>
			<td class='list_box_td ' style='line-height:140%;padding:5px;'>";
			if($_SESSION["admininfo"]["company_id"] == $contract_infos[$i][contractor_id]){	
$mString .= "	
			".($contract_infos[$i][contractor_signature] ? "<a href=\"javascript:PoPWindow3('econtract_signed_view.php?mmode=pop&sign_type=C&ei_ix=".$contract_infos[$i][ei_ix]."',1100,800,'main_goods')\"><img src='../v3/images/".$admininfo["language"]."/btn_signed_view.gif' align=absmiddle style='padding-bottom:3px;'></a><br>".$contract_infos[$i][contractor_signature_date]:"<a href=\"javascript:PoPWindow3('econtract_object_check.php?mmode=pop&ei_ix=".$contract_infos[$i][ei_ix]."',1100,800,'main_goods')\"><img src='../v3/images/".$admininfo["language"]."/btn_sign.gif' align=absmiddle></a>")."";
			}else{
$mString .= "	".($contract_infos[$i][contractor_signature] ? "<a href=\"javascript:PoPWindow3('econtract_signed_view.php?mmode=pop&sign_type=C&ei_ix=".$contract_infos[$i][ei_ix]."',1100,800,'main_goods')\"><img src='../v3/images/".$admininfo["language"]."/btn_signed_view.gif' align=absmiddle style='padding-bottom:3px;'></a><br>".$contract_infos[$i][contractor_signature_date]:"서명대기");
			}
			$mString .= "	
			</td>
			<td class='list_box_td list_bg_gray'>".$_CONTRACT_STATUS[$contract_infos[$i][status]]."</td>
			
			<td class='list_box_td list_bg_gray' nowrap>";

			// remaker		Dominic
			// date			2014-07-02
			// contents		move econtract object check page & old move page remove
			$mString .= "<a href=\"javascript:PoPWindow3('econtract_object_check.php?mmode=pop&ei_ix=".$contract_infos[$i][ei_ix]."',1100,800,'main_goods')\"><img src='../images/".$admininfo["language"]."/view_contract.gif' align=absmiddle></a> ";
			// $mString .= "<a href=\"javascript:PoPWindow3('econtract_view.php?mmode=pop&ei_ix=".$contract_infos[$i][ei_ix]."',1100,800,'main_goods')\">상세보기</a> ";
			
			// $mString .= "<a href='econtract_view.php?mmode=pop&ei_ix=".$contract_infos[$i][ei_ix]."'>상세보기<!--img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle alt='수정' title='수정'--></a> ";
			if($_SESSION["admininfo"]["admin_level"] == 9){	
				//if($contract_infos[$i][com_signature] == "" && $contract_infos[$i][contractor_signature] == ""){
					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
						$mString .= "<a href='econtract.php?ei_ix=".$contract_infos[$i][ei_ix]."'><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle alt='수정' title='수정'></a>";
					}
				
					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
						$mString .= "<a href=\"JavaScript:econtractDelete('".$contract_infos[$i][ei_ix]."')\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='margin:0px 5px;' alt='삭제' title='삭제'></a>";
					}else{
						$mString .= "<a href=\"".$auth_delete_msg."\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='margin:0px 5px;'></a>";
					}
				//}
			}
			$mString .= "
			</td>
			</tr>
			";
		}

		$mString .= "</table>";
		$mString .= "<table cellpadding=0 cellspacing=0 border=0 width=100%  >";
		$mString .= "<tr bgcolor=#ffffff style='height:50px;'>
					<td colspan=3 align=left>".$str_page_bar."</td>
					<td colspan=2 align=right>";

		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
		//$mString .= "<a href='econtract.php'><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0 ></a>";
		}

		$mString .= "
					</td>
				</tr>";
	}


	$mString .= "</table>";

	return $mString;
}


?>