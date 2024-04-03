<?
include("../class/layout.class");
include("./contract.lib.php");

$db = new MySQL;
$mdb = new MySQL;
$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
if ($use_sdate == "1"){
	if(!$econtract_sdate || !$econtract_edate){ 
		$econtract_sdate = date("Ymd", $before10day);
		$econtract_edate = date("Ymd");	
	}
}

if ($use_edate == "1"){
	if(!$st_end_sdate || !$st_end_edate){ 
		$st_end_sdate = date("Ymd", $before10day);
		$st_end_edate = date("Ymd");	
	}
}

$Script = "
<link rel='stylesheet' type='text/css' href='/admin/v3/css/jquery-ui.css' />
<script type='text/javascript' src='/js/ui/jquery-ui-1.8.9.custom.js'></script>
<script language='javascript'>
/*
$(function() {
	$(\"#econtract_sdate\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		if($('#event_edate').val() != '' && $('#event_edate').val() <= dateText){
			$('#event_edate').val(dateText);
		}
	}

	});

	$(\"#econtract_edate\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력'

	});


	$(\"#st_end_sdate\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		if($('#event_edate').val() != '' && $('#event_edate').val() <= dateText){
			$('#event_edate').val(dateText);
		}
	}

	});

	$(\"#st_end_edate\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	//$('#end_timepicker').timepicker();
});
*/

function contractDelete(et_ix){
	if(confirm('해당 검색어를 삭제하시겠습니까?')){
		window.frames['act'].location.href= 'contract.act.php?act=delete&et_ix='+et_ix;
	}
}


function setSelectDate(sdate,edate,date_type) {
	var frm = document.search_events;
	if(date_type == 1){
		$(\"#econtract_sdate\").val(sdate);
		$(\"#econtract_edate\").val(edate);
	}else{
		$(\"#st_end_sdate\").val(sdate);
		$(\"#st_end_edate\").val(edate);
	}
}


function searchUseSdate(frm){
	if($('#use_sdate').attr('checked') == 'checked'){
		$('#econtract_sdate').attr('disabled',false);
		$('#econtract_edate').attr('disabled',false);	 
	}else{
		$('#econtract_sdate').val('').attr('disabled',true);
		$('#econtract_edate').val('').attr('disabled',true);
	}
}

function searchUseEdate(frm){
	if($('#use_edate').attr('checked') == 'checked'){
		$('#st_end_sdate').attr('disabled',false);
		$('#st_end_edate').attr('disabled',false);	 
	}else{
		$('#st_end_sdate').val('').attr('disabled',true);
		$('#st_end_edate').val('').attr('disabled',true);
	}
}
 
 $(document).ready(function (){

		if($('#use_sdate').attr('checked') == 'checked'){
			$('#econtract_sdate').attr('disabled',false);
			$('#econtract_edate').attr('disabled',false);
		}else{
			$('#econtract_sdate').removeClass('point_color').val('').attr('disabled',true);
			$('#econtract_edate').removeClass('point_color').val('').attr('disabled',true);
		}

		if($('#use_edate').attr('checked') == 'checked'){
			$('#st_end_sdate').attr('disabled',false);
			$('#st_end_edate').attr('disabled',false);
		}else{
			$('#st_end_sdate').removeClass('point_color').val('').attr('disabled',true);
			$('#st_end_edate').removeClass('point_color').val('').attr('disabled',true);
		}

});


</script>";


$mstring ="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("자주쓰는 계약서", "전시관리 > 전자계약 관리")."</td>
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
				<form name=search_events method='get' ><!--SubmitX(this);'-->
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
								<option value='contract_title' ".CompareReturnValue("st_text",$search_type,"selected")." style='vertical-align:middle;'>계약서 명</option>
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
							<input type='radio' name='econtract_type' id='econtract_type_'  align='middle' value='' ".($econtract_type == '' ? "checked":"")."><label for='econtract_type_' class='green'>전체</label> 
							<input type='radio' name='econtract_type' id='econtract_type_1'  align='middle' value='1' ".($econtract_type == '1' ? "checked":"")."><label for='econtract_type_1' class='green'>일반계약서</label> 
							<input type='radio' name='econtract_type' id='econtract_type_2'  align='middle' value='2' ".($econtract_type == '2' ? "checked":"")."><label for='econtract_type_2' class='green'>첨부서류</label> 
					  </td>
					  <td class='search_box_title'>담당자</td>
						<td class='search_box_item'>
						".CompayChargerSearch($_SESSION["admininfo"]["company_id"] ,$charger_ix,"","selectbox")."
						</td>
					</tr>
					<tr height=30>
					  <td class='search_box_title'>사용여부 </td>
					  <td class='search_box_item' colspan=3>
					  <input type=radio name='is_use' value='' id='is_use_a'  ".CompareReturnValue("",$is_use,"checked")."><label for='is_use_a'>전체</label>
					  <input type=radio name='is_use' value='1' id='is_use_y'  ".CompareReturnValue("1",$is_use,"checked")."><label for='is_use_y'>사용</label>
					  <input type=radio name='is_use' value='0' id='is_use_n' ".CompareReturnValue("0",$is_use,"checked")."><label for='is_use_n'>미사용</label>
					  </td 
					</tr>
					"; 
 $mstring .= "
					<tr height=27>
						<td class='search_box_title' align=center><label for='use_sdate'><b>계약서 작성일</b></label><input type='checkbox' name='use_sdate' id='use_sdate' value='1' onclick='searchUseSdate(document.search_events);' ".($use_sdate == "1" ? "checked":"")."></td>
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
		</table>";
		$mstring .= "
			</td>
		</tr>
		<tr >
			<td style='padding:10px 0px;' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
		</tr>
		</form>
		<tr>
			<td>
			".getContractList()."
			</td>
		</tr>
		";
$mstring .="</table>";

$help_text = HelpBox("<table cellpadding='0' cellspacing='0' border='0'><tr><td valign=top><b>전자계약 관리</b></td></tr></table>", $help_text,220);

$Contents = $mstring.$help_text;


$P = new LayOut();
$P->addScript = "".$Script;
$P->OnloadFunction = "";
$P->Navigation = "전자계약 관리약 > 전자계약 관리";
$P->title = "자주쓰는 계약서";
$P->strLeftMenu = econtract_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

function getContractList(){
	global $db, $mdb, $page, $search_type;
	global $auth_delete_msg, $auth_excel_msg, $admininfo;


	$where = " where et_ix <> '0' ";
	

	if($_GET["contract_type"] != ""){
		$where .= " and contract_type =  '".$_GET["contract_type"]."' ";
	}

	if($_GET["is_use"] != ""){
		$where .= " and st.is_use =  '".$_GET["is_use"]."' ";
	}

	if($_GET["contract_title"] != ""){
		$where .= " and contract_title LIKE  '%".$_GET["contract_title"]."%' ";
	}
 
	if($_GET["econtract_sdate"] != "" && $_GET["econtract_edate"] != ""){
		$unix_timestamp_start_sdate = mktime(0,0,0,substr($_GET["econtract_sdate"],4,2),substr($_GET["econtract_sdate"],6,2),substr($_GET["econtract_sdate"],0,4));
		$unix_timestamp_start_edate = mktime(0,0,0,substr($_GET["econtract_edate"],4,2),substr($_GET["econtract_edate"],6,2),substr($_GET["econtract_edate"],0,4));

		$where .= " and  st_sdate between  ".$unix_timestamp_start_sdate." and ".$unix_timestamp_start_edate." ";
	}
 

	if($_GET["st_end_sdate"] != "" && $_GET["st_end_edate"] != ""){
		$unix_timestamp_end_sdate = mktime(0,0,0,substr($_GET["st_end_sdate"],4,2),substr($_GET["st_end_sdate"],6,2),substr($_GET["st_end_sdate"],0,4));
		$unix_timestamp_end_edate = mktime(0,0,0,substr($_GET["st_end_edate"],4,2),substr($_GET["st_end_edate"],6,2),substr($_GET["st_end_edate"],0,4));

		$where .= " and  st_edate between  ".$unix_timestamp_end_sdate." and ".$unix_timestamp_end_edate." ";
	}


	$sql = "select st.* from econtract_tmp st $where";

	//echo $sql;
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
	<col width='5%'>
	".($_SESSION["admin_config"][front_multiview] == "Y" ? "<col width=10%>":"")."
	<col width='10%'>
	<col width='*'>
	<col width='10%'>
	<col width='10%'>
	<col width='13%'>
	<col width='7%'>
	<col width='15%'>
	<col width='7%'>
	<col width='15%'>
	<tr align=center bgcolor=#efefef height='30'>
		<td class=s_td >번호</td>
		".($_SESSION["admin_config"][front_multiview] == "Y" ? "<td class='m_td'  > 프론트전시</td>":"")."
		<td class=m_td >계약서 분류</td>
		<td class=m_td >계약서명</td>
		<td class=m_td>담당자</td>
		<td class=m_td>첨부서류</td>
		<td class=m_td>등록일/수정일</td>
		<td class=m_td>계약건수</td>
		<td class=m_td>사용여부</td>
		<td class=e_td>관리</td>
		</tr>";


	if ($total == 0){
		$mString .= "<tr bgcolor=#ffffff height=70><td colspan=9 align=center>등록한 검색어가 존재 하지 않습니다.</td></tr>";
		$mString .= "</table>";
		$mString .= "<table cellpadding=0 cellspacing=0 border=0 width=100%  >";
		$mString .= "<tr bgcolor=#ffffff ><td colspan=5 align=right style='padding:10px 0px;'><a href='search_text.php'><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0 ></a></td></tr>";

	}else{
//echo $db->ase_encrypt_key;
//AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name
		$sql = "select et.*, eg.group_name , AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name  
					from econtract_tmp et 
					left join econtract_group eg on et.contract_group = eg.group_ix  
					left join common_member_detail cmd on et.charger_ix = cmd.code
					$where 
					order by regdate desc 
					limit $start, $max";

		$db->query($sql);
		$contract_infos = $db->fetchall();

		for($i=0;$i < count($contract_infos);$i++){

			$no = $total - ($page - 1) * $max - $i;

			$mString = $mString."<tr height=30 bgcolor=#ffffff align=center>
			<td class='list_box_td list_bg_gray'>".$no."</td>";
if($_SESSION["admin_config"]["front_multiview"] == "Y"){
	$mString .= "
		    <td class='list_box_td'  >".GetDisplayDivision($contract_infos[$i][mall_ix], "text")."</td>";
}
	$mString .= "
			<td class='list_box_td '>".$contract_infos[$i][group_name]."</td>
			<td class='list_box_td '>".$contract_infos[$i][contract_title]."</td>
			<td class='list_box_td '>".$contract_infos[$i][name]." ".$contract_infos[$i][name2]." </td>
			<td class='list_box_td list_bg_gray'>".($contract_infos[$i][contract_type] == "1" ? "셀러 기본계약서":"-")."</td>
			<td class='list_box_td ' style='line-height:140%;'>".$contract_infos[$i][regdate]."</td>
			<td class='list_box_td point'>".number_format($contract_infos[$i][searchcnt])."</td> 
			<td class='list_box_td list_bg_gray'>".($contract_infos[$i][is_use] == "1" ? "사용":"사용안함")."</td>
			
			<td class='list_box_td list_bg_gray' nowrap>";

			$mString .= "<a href='contract.php?et_ix=".$contract_infos[$i][et_ix]."'><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle alt='수정' title='수정'></a>";

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$mString .= "<a href=\"JavaScript:contractDelete('".$contract_infos[$i][et_ix]."')\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='margin:0px 5px;' alt='삭제' title='삭제'></a>";
			}else{
				$mString .= "<a href=\"".$auth_delete_msg."\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='margin:0px 5px;'></a>";
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
		$mString .= "<a href='contract.php'><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0 ></a>";
		}

		$mString .= "
					</td>
				</tr>";
	}


	$mString .= "</table>";

	return $mString;
}
 

?>