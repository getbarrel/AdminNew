<?
include("../class/layout.class");


$db = new Database;
$mdb = new Database;

if($info_type == ""){
	$info_type = "list";
}

if ($FromYY == ""){		//기본설정 시간
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));

//	$sDate = date("Y/m/d");
	$sDate = date("Y-m-d", $before10day);
	$eDate = date("Y-m-d");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	$sDate = $FromYY."-".$FromMM."-".$FromDD;
	$eDate = $ToYY."-".$ToMM."-".$ToDD;
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;
	$birDate = $birYY.$birMM.$birDD;
}

$Script ="
<script language='JavaScript' >
function ReserveReset(){
	var frm = document.forms['reserve_list'];

	frm.reset();
	frm.act.value = 'reserve_insert';
}

function DeleteReserve(id, uid){
	if(confirm(language_data['common']['G'][language])){
	//'적립금 정보를 정말로 삭제하시겠습니까?'
		//document.frames['iframe_act'].location.href='membed.act.php?act=reserve_delete&id='+id+'&uid='+uid;
		window.frames['iframe_act'].location.href='membed.act.php?act=reserve_delete&id='+id+'&uid='+uid;
	}
}

function UpdateReserve(deposit_ix,state,use_state,uid,etc,deposit){
	var frm = document.forms['reserve_list'];
	if(confirm('예치금 정보를 수정하시겠습니까?')){
		window.frames['iframe_act'].location.href='deposit.act.php?act=update_state&deposit_ix='+deposit_ix+'&state='+state+'&use_state='+use_state+'&uid='+uid+'&etc='+etc+'&deposit='+deposit;
	}
}

function CheckReserve(frm){
	if(frm.etc.value.length < 1){
		alert(language_data['reserve.php']['A'][language]);
		//'적립내용을 입력해주세요'
		//frm.etc.focus();
		return false;
	}

	if(frm.reserve.value.length < 1){
		alert(language_data['reserve.php']['B'][language]);
		//'마일리지를 입력해주세요'
		//frm.reserve.focus();
		return false;
	}

	return true;
}

function clearAll(frm){
	for(i=0;i < frm.reserve_id.length;i++){
			frm.reserve_id[i].checked = false;
	}
}

function checkAll(frm){
	for(i=0;i < frm.reserve_id.length;i++){
			frm.reserve_id[i].checked = true;
	}
}

function fixAll(frm){
	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;

	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
}

function CheckDelete(frm){
	if(confirm(language_data['reserve.php']['G'][language])){
	//'선택하신 적립금을 정말로 삭제하시겠습니까? 삭제하신 적립금은 복원되지 않습니다'
		for(i=0;i < frm.reserve_id.length;i++){
			if(frm.reserve_id[i].checked){
				return true
			}
		}
		alert(language_data['reserve.php']['C'][language]);
		//'삭제하실 목록을 한개이상 선택하셔야 합니다.'
	}
	return false;
}

function SelectDelete(frm){
	frm.act.value = 'deposit_select_delete';
	if(CheckDelete(frm)){
		frm.submit();
	}
}

function onLoad(FromDate, ToDate) {
	var frm = document.searchmember;

	LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate);
	LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate);
}

$(document).ready(function (){

//다중검색어 시작 2014-04-10 이학봉

	$('input[name=mult_search_use]').click(function (){
		var value = $(this).attr('checked');

		if(value == 'checked'){
			$('#search_text_input_div').css('display','none');
			$('#search_text_area_div').css('display','');
			
			$('#search_text_area').attr('disabled',false);
			$('#search_texts').attr('disabled',true);
		}else{
			$('#search_text_input_div').css('display','');
			$('#search_text_area_div').css('display','none');

			$('#search_text_area').attr('disabled',true);
			$('#search_texts').attr('disabled',false);
		}
	});

	var mult_search_use = $('input[name=mult_search_use]:checked').val();
		
	if(mult_search_use == '1'){
		$('#search_text_input_div').css('display','none');
		$('#search_text_area_div').css('display','');

		$('#search_text_area').attr('disabled',false);
		$('#search_texts').attr('disabled',true);
	}else{
		$('#search_text_input_div').css('display','');
		$('#search_text_area_div').css('display','none');

		$('#search_text_area').attr('disabled',true);
		$('#search_texts').attr('disabled',false);
	}

//다중검색어 끝 2014-04-10 이학봉

});
</Script>";

$sql = "select 
			count(*) as total_member,
			sum(deposit) as total_deposit,
			(sum(deposit)/count(*)) as ping_deposit
		from 
			common_user
		where
			deposit > 0";
$mdb->query($sql);
$mdb->fetch();

$total_member = $mdb->dt[total_member];		//총 예치금 보유회원수
$total_deposit = $mdb->dt[total_deposit];	//총 예치금 보유금액
$ping_deposit = $mdb->dt[ping_deposit];		//1인당 예치금 평균 보유금액

$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<tr height=25><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>예치금 관리</b></td></tr>
</table>

<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
	<tr height='30' bgcolor='#ffffff'>
		<td width='33%' align='center' class=s_td><font color='#000000'><b>예치금 보유인원</b></font></td>
		<td width='33%' align='center' class='m_td'><font color='#000000'><b>예치금 전체 보유금액</b></font></td>
		<td width='34%' align='center' class='e_td'><font color='#000000'><b>1인 평균 예치금 보유금액</b></font></td>
	</tr>
	<tr height='30'>
		<td class='list_box_td' >".number_format($total_member)."</td>
		<td class='list_box_td'>".number_format($total_deposit)."</td>
		<td class='list_box_td'>".number_format(round($ping_deposit))."</td>
	</tr>
</table>
<br><br>";

$Contents01 .= "
	<form name='searchmember' style='display:inline;'>
	<input type='hidden' name='info_type' value='$info_type'>
	<table border='0' cellpadding='0' cellspacing='0' width='100%'>
	<tr height=25><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>예치금 검색하기</b></td></tr>
	<tr>
		<td align='left' colspan=2  width='100%' valign=top style='padding-top:0px;'>
			<table border='0' cellpadding='0' cellspacing='0' width='100%'>
			<tr>
				<td class='box_05' valign=top style='padding:0px;'>
					<table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
					<col width=18%>
					<col width=32%>
					<col width=18%>
					<col width=32%>
					<tr height=27>
						<td class='search_box_title' >회원구분 </td>
						<td class='search_box_item' >
							<input type=checkbox name='nationality[]' value='I' id='nationality_I'  ".CompareReturnValue("I",$nationality,"checked")."><label for='nationality_I'>국내회원</label>&nbsp;&nbsp;
							<input type=checkbox name='nationality[]' value='O' id='nationality_O'  ".CompareReturnValue("O",$nationality,"checked")."><label for='nationality_O'>해외회원</label>
						</td>
						<td class='search_box_title' >회원타입 </td>
						<td class='search_box_item' >
							<input type=checkbox name='mem_type[]' value='M' id='mem_type_m'  ".CompareReturnValue("M",$mem_type,"checked")."><label for='mem_type_m'>일반회원</label>&nbsp;&nbsp;
							<input type=checkbox name='mem_type[]' value='C' id='mem_type_c' ".CompareReturnValue("C",$mem_type,"checked")."><label for='mem_type_c'>사업자회원</label>&nbsp;&nbsp;
							<input type=checkbox name='mem_type[]' value='S' id='mem_type_S' ".CompareReturnValue("F",$mem_type,"checked")."><label for='mem_type_S'>셀러회원</label>
						</td>
					</tr>
					<tr height=27>
						<td class='search_box_title' bgcolor='#efefef' align=center>
							<label for='search_check'><b>처리일자</b></label>";
				$Contents01 .= "
							<select name='search_state_type'>
								<option value='join_date'>가입일</option>
							</select>";

				$Contents01 .= "
							<input type='checkbox' name='search_check' id='search_check' value='1' onclick='ChangeRegistDate(document.searchmember);'".CompareReturnValue("1",$search_check,"checked").">
						</td>
						<td class='search_box_item' colspan='3'>
							".search_date('sdate','edate',$sDate,$eDate)."
						</td>
					</tr>
					<tr>
						<td class='search_box_title'>검색어
						<span style='padding-left:2px' class='helpcloud' help_width='220' help_height='30' help_html='검색하시고자 하는 값을 ,(콤마) 구분으로 넣어서 검색 하실 수 있습니다'><img src='/admin/images/icon_q.gif' align=absmiddle/></span>
						
						<input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'> <label for='mult_search_use'>(다중검색 체크)</label>
						</td>
						<td class='search_box_item' colspan='3'>
							<table cellpadding=0 cellspacing=0 border='0'>
							<tr>
								<td valign='top'>
									<div style='padding-top:5px;'>
									<select name='search_type' id='search_type'  style=\"font-size:12px;\">
									<option value='cmd.name' ".CompareReturnValue("cmd.name",$search_type).">회원명</option>
									<option value='cu.id' ".CompareReturnValue("cu.id",$search_type).">아이디</option>
									<option value='d.charger_name' ".CompareReturnValue("d.charger_name",$search_type).">담당자명</option>
									</select>
									</div>
								</td>
								<td style='padding:5px;'>
									<div id='search_text_input_div'>
										<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='WIDTH: 210px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
									</div>
									<div id='search_text_area_div' style='display:none;'>
										<textarea name='search_text' name='search_text_area' id='search_text_area' class='tline textbox' style='padding:0px;height:90px;width:150px;' >".$search_text."</textarea>
									</div>
								</td>
								<td>
									<div>
										<span class='small blu' > * 다중 검색은 다중 아이디로 검색 지원이 가능합니다. 구분값은 ',' 혹은 'Enter'로 사용 가능합니다. </span>
									</div>
								</td>
							</tr>
							</table>
						</td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=3 align=center style='padding:10px 0;'>
			<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
		</td>
	</tr>
	</table>
	</form>";

$max = 20; //페이지당 갯수

if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}

//회원구분 검색관련
if(is_array($nationality) && count($nationality)>0){		//회원구분 
	$where.=" AND cmd.nationality IN ('".implode("','",$nationality)."')";
}else{
	if($nationality != ""){
		$where .= " and cmd.nationality = '".$nationality."'";
	}else{
		$nationality=array();
	}
}

//회원타입 
if(is_array($mem_type) && count($mem_type)>0){		//노출여부 
	$where.=" AND cu.mem_type IN ('".implode("','",$mem_type)."')";
}else{
	if($mem_type != ""){
		$where .= " and cu.mem_type = '".$mem_type."'";
	}else{
		$mem_type =array();
	}
}

//처리상태 
if(is_array($state) && count($state)>0){		//노출여부 
	$where.=" AND d.state IN ('".implode("','",$state)."')";
}else{
	if($state != ""){
		$where .= " and d.state = '".$state."'";
	}else{
		$state =array();
	}
}

$search_text = trim($search_text);
if($db->dbms_type == "oracle"){
	if($search_type != "" && $search_text != ""){
		if($search_type == "name" || $search_type == "mail" || $search_type == "pcs" || $search_type == "tel" ){
			$where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
			$count_where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
		}else{
			$where .= " and $search_type LIKE  '%$search_text%' ";
			$count_where .= " and $search_type LIKE  '%$search_text%' ";
		}
	}
}else{
	if($search_type != "" && $search_text != ""){
		if($search_type == "cmd.name"){
			$where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
			$count_where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
		}else if($search_type == "id"){

			if(strpos($search_text,",") !== false){
				$search_array = explode(",",$search_text);
				$where .= "and ( ";
				$count_where .= "and ( ";
				for($i=0;$i<count($search_array);$i++){

					if($i == count($search_array) - 1){
						$where .= $search_type." = '".trim($search_array[$i])."'";
						$count_where .= $search_type." = '".trim($search_array[$i])."'";
					}else{
						$where .= $search_type." = '".trim($search_array[$i])."' or ";
						$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
					}
				}
				$where .= ")";
				$count_where .= ")";
			}else if(strpos($search_text,"\n") !== false){//\n

				$search_array = explode("\n",$search_text);

				$where .= "and ( ";
				$count_where .= "and ( ";
				for($i=0;$i<count($search_array);$i++){

					if($i == count($search_array) - 1){
						$where .= $search_type." = '".trim($search_array[$i])."'";
						$count_where .= $search_type." = '".trim($search_array[$i])."'";
					}else{
						$where .= $search_type." = '".trim($search_array[$i])."' or ";
						$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
					}
				}
				$where .= ")";
				$count_where .= ")";
			}else{
				$where .= "and ".$search_type." = '".trim($search_text)."'";
				$count_where .= "and ".$search_type." = '".trim($search_text)."'";
			}
		}else{
			$where .= " and $search_type LIKE  '%$search_text%' ";
			$count_where .= " and $search_type LIKE  '%$search_text%' ";
		}
	}
}

$startDate = $FromYY.$FromMM.$FromDD;
$endDate = $ToYY.$ToMM.$ToDD;

if($startDate != "" && $endDate != ""){
	if($db->dbms_type == "oracle"){
		$where .= " and  to_char(ri.regdate , 'YYYYMMDD') between  $startDate and $endDate ";
	}else{
		$where .= " and  ri.regdate between  '$startDate' and '$endDate' ";
	}
}

$sql = "select 
			cu.id
		from 
			common_user as cu 
		where cu.deposit > 0";

/*
16 02 17 위의 쿼리로 변경

$sql = "select 
			*
		from 
			common_user as cu 
			inner join common_member_detail as cmd on (cu.code = cmd.code)
		where cu.deposit > 0";

*/


$db->query($sql);
$db->fetch();
$total = $db->total;

if($db->dbms_type == "oracle"){
$sql = "select 
			(select sum(deposit) from shop_deposit where state = '3' and uid = cu.code) as total_deposit,
			(select sum(deposit) from shop_deposit where state = '4' and uid = cu.code) as total_use_deposit,
			(select sum(deposit) from shop_deposit where state = '8' and uid = cu.code) as total_withdraw_deposit,
			(select ifnull(count(*),(count(*) + 1)) as member_ranking from common_user as cu1 where cu1.code = cu.code and cu1.deposit >= cu.deposit) as ranking,
			count(*) as total_member,
			sum(cu.deposit) as total_depositv,
			g.gp_name,
			AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
			cu.id,
			cu.code as uid
		from 
			common_user as cu 
			inner join common_member_detail as cmd on (cu.code = cmd.code)
			left join shop_groupinfo as g on (cmd.gp_ix = g.gp_ix)
		where
			1
			$where
			and cu.deposit > 0
			group by cu.code
			order by cu.deposit ASC, cmd.date DESC LIMIT $start, $max";
			
$db->query($sql);

}else{

$sql = "select 
			(select sum(deposit) from shop_deposit where state = '3' and uid = cu.code) as total_deposit,
			(select sum(deposit) from shop_deposit where state = '4' and uid = cu.code) as total_use_deposit,
			(select sum(deposit) from shop_deposit where state = '8' and uid = cu.code) as total_withdraw_deposit,
			(select ifnull(count(*),(count(*) + 1)) as member_ranking from common_user as cu1 where cu1.code = cu.code and cu1.deposit >= cu.deposit) as ranking,
			count(*) as total_member,
			sum(cu.deposit) as total_depositv,
			g.gp_name,
			AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
			cu.id,
			cu.code as uid
		from 
			common_user as cu 
			inner join common_member_detail as cmd on (cu.code = cmd.code)
			left join shop_groupinfo as g on (cmd.gp_ix = g.gp_ix)
		where
			1
			$where
			and cu.deposit > 0
			group by cu.code
			LIMIT $start, $max";

$db->query($sql);

}

$data_array = $db->fetchall();

if($mode == "excel"){	//엑셀다운로드

	$exe_type = 'deposit_use';
	$goods_infos = $data_array;
	include("excel_out_columsinfo.php");
	$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='deposit_info_".$exe_type."' ";
	$db->query($sql);
	$db->fetch();
	$stock_report_excel = $db->dt[conf_val];

	$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='check_deposit_info_".$exe_type."' ";
	$db->query($sql);
	$db->fetch();
	$stock_report_excel_checked = $db->dt[conf_val];

	$check_colums = unserialize(stripslashes($stock_report_excel_checked));

	$columsinfo = $colums;

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
	foreach($check_colums as $key => $value){
		$inventory_excel->getActiveSheet(0)->setCellValue($col . "1", $columsinfo[$value][title]);
		$col++;
	}

	$before_pid = "";

	for($i = 0; $i < count($goods_infos); $i++){
		$j="A";
		foreach($check_colums as $key => $value){
			if($key == "deposit"){				//처리일자
				$value_str = $goods_infos[$i][total_deposit] - $goods_infos[$i][total_use_deposit] - $goods_infos[$i][total_withdraw_deposit];
			}else{
				$value_str = $goods_infos[$i][$value];//$db1->dt[$value];
			}

			$inventory_excel->getActiveSheet()->setCellValue($j . ($z + 2), $value_str);
			$j++;

			unset($history_text);
		}
		$z++;
	}
	// 첫번째 시트 선택
	$inventory_excel->setActiveSheetIndex(0);

	// 너비조정
	$col = 'A';
	foreach($check_colums as $key => $value){

		$inventory_excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
		$col++;
	}

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="deposit_Info_'.$info_type.'.xls"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'Excel5');
	$objWriter->save('php://output');

	exit;
}


$Contents01 .= "
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
	<tr height=30 >
		<td>
			<b>전체 : ".$total." 개</b>
		</td>
		<td colspan=5 align=right>";

		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents01 .= "<span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'>
			<a href='excel_config.php?".$QUERY_STRING."&info_type=deposit_use&excel_type=deposit_info_excel' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a></span>";
		}else{
			$Contents01 .= "
			<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
		}

		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents01 .= " <a href='deposit_possess_list.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}else{
			$Contents01 .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}

$Contents01 .= "
		</td>
	</tr>
	</table>

	<form name=reserve_list method=post action='deposit.act.php' onsubmit='return CheckDelete(this)' target='iframe_act'>
	<input type='hidden' name='act' value='reserve_select_delete'>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>
	<col width=3%>
	<col width=10%>
	<col width=10%>
	<col width=8%>
	<col width=7%>
	<col width=7%>
	<col width=7%>
	<col width=7%>
	<col width=5%>
	<col width=6%>
	<tr bgcolor=#efefef style='font-weight:bold' align=center height=28>
		<td class='s_td'><input type=checkbox class=nonborder id='all_fix' onclick='fixAll(document.reserve_list)'></td>
		<td class='m_td'>회원명</td>
		<td class='m_td'>아이디</td>
		<td class='m_td'>회원그룹</td>
		<td class='m_td'>입금금액</td>
		<td class='m_td'>사용완료 금액</td>
		<td class='m_td'>출금금액</td>
		<td class='m_td'>보유예치금</td>
		<td class='m_td'>보유순위</td>
		<td class='e_td'>관리</td>
	</tr>";

if(count($data_array) > 0){
	for($i=0;$i < count($data_array); $i++){
		$no = $i + 1;
$Contents01 .= "
			<tr height=28 align=center>
				<td class='list_box_td list_bg_gray' ><input type=checkbox class=nonborder id='reserve_id' name=rid[] value='".$data_array[$i][id]."'></td>
				<td class='list_box_td' bgcolor='#ffffff'>".$data_array[$i][name]."</td>
				<td class='list_box_td' bgcolor='#ffffff'>".$data_array[$i][id]."</td>
				<td class='list_box_td' bgcolor='#ffffff'>".$data_array[$i][gp_name]."</td>
				<td class='list_box_td' ><font color='#0054FF'>".number_format($data_array[$i][total_deposit])."<font></td>
				<td class='list_box_td list_bg_gray'><font color='#FF0000'>".number_format($data_array[$i][total_use_deposit])."<font></td>
				<td class='list_box_td list_bg_gray'><font color='#000000'>".number_format($data_array[$i][total_deposit])."<font></td>
				<td class='list_box_td list_bg_gray'><font color='#000000'>".number_format($data_array[$i][total_deposit] - $data_array[$i][total_use_deposit] - $data_array[$i][total_withdraw_deposit])."<font></td>";

$Contents01 .= "
				<td class='list_box_td point' bgcolor='#efefef'>
				<a href=\"javascript:PoPWindow('deposit.pop.php?code=".$data_array[$i][uid]."',750,550,'reserve_pop')\">".$data_array[$i][ranking]."</a></td>";

				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
$Contents01 .= "
				<td class='list_box_td' style='padding:3px;'>";
$Contents01 .= "	<a href=\"javascript:PoPWindow('deposit.pop.php?code=".$data_array[$i][uid]."',800,550,'reserve_pop')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
$Contents01 .= "</td>";
				}else{
$Contents01 .= "<td class='list_box_td' ><a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a></td>";
				}
$Contents01 .= "
			</tr>";
	}


}else{

$Contents01 .= "
			<tr height=60><td class='list_box_td' colspan=11 align=center>예치금 내용이 없습니다.</td></tr>";
}

$Contents01 .= "
		</table></form>";

$Contents01 .= "
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
<col width='10%'>
<col width='*'>
<tr height=40>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
	$Contents01 .= "
	<td align=left><a href=\"JavaScript:SelectDelete(document.forms['reserve_list']);\"><img  src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0 align=absmiddle ></a></td>";
}else{
	$Contents01 .= "
	<td align=left><a href=\"".$auth_delete_msg."\"><img  src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0 align=absmiddle ></a></td>";
}
$Contents01 .= "
	<td align=right>".page_bar($total, $page, $max,"&code=$code&state=$state&ust_status_cancel=$ust_status_cancel&mem_type=$mem_type&nationality=$nationality&gp_ix=$gp_ix&info_type=$info_type&search_type=$search_type&search_text=$search_text&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD","")."</td>
</tr>
</table>";


$ContentsDesc02 = "
<table width='660' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td align=left style='padding:10px;'>
		<img src='../image/emo_3_15.gif' align=absmiddle> 거래은행 및 그룹 정보는 결제시 이용됩니다. 정확하게 입력해주세요
	</td>
</tr>
</table>
";

$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";


$Contents = $Contents01;

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("예치금 충전관리", $help_text);

$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "";
$P->strLeftMenu = buyer_accounts_menu();
$P->Navigation = "구매자정산관리 > 예치금 충전관리";
$P->title = "예치금 충전관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();
?>
