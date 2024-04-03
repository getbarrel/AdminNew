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
	if(confirm('선택하신 예치금정보를 수정하시겠습니까?')){
	//'적립금 정보를 정말로 삭제하시겠습니까?'
		//document.frames['iframe_act'].location.href='membed.act.php?act=reserve_delete&id='+id+'&uid='+uid;
		window.frames['iframe_act'].location.href='membed.act.php?act=reserve_delete&id='+id+'&uid='+uid;
	}
}

function UpdateReserve(deposit_ix,state,use_state,uid,etc,deposit,use_type){
	var frm = document.forms['reserve_list'];
	if(confirm('예치금 정보를 수정하시겠습니까?')){

		window.frames['iframe_act'].location.href='deposit.act.php?act=update_state&deposit_ix='+deposit_ix+'&state='+state+'&use_state='+use_state+'&uid='+uid+'&etc='+etc+'&deposit='+deposit+'&use_type='+use_type;

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
	if(confirm('선택하신 예치금 정보를 변경하시겠습니까?')){
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
	frm.act.value = 'deposit_select_update';
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

if($info_type == "list" or $info_type == ""){

	if($mmode == "personalization"){ 
		$total_where = " where uid ='".$mem_ix."' ";
	}
	$sql = "select
				sum(if(state='1',deposit,0)) as state_wait,
				sum(if(state='3',deposit,0)) as state_complate,

				sum(if(state='4',deposit,0)) as state_use,
				sum(if(state='5',deposit,0)) as widthdrawl_request,
				sum(if(state='7',deposit,0)) as widthdrawl_check,
				sum(if(state='8',deposit,0)) as widthdrawl_use
			from
				shop_deposit
			$total_where
			";
	$mdb->query($sql);
	$mdb->fetch();

	$state_wait = $mdb->dt[state_wait];		//입금대기
	$state_complate = $mdb->dt[state_complate];	//입금완료
	
	$widthdrawl_request = $mdb->dt[widthdrawl_request];	//출금요청
	$widthdrawl_check = $mdb->dt[widthdrawl_check];	//출금확정
	$widthdrawl_use = $mdb->dt[widthdrawl_use];	//송금완료	

	$state_use = $mdb->dt[state_use];		//사용완료
	
	$request_total = $widthdrawl_request + $widthdrawl_check;
	$total_deposit = $state_complate - $state_use - $widthdrawl_use;
	$total_use_deposit = $state_use + $widthdrawl_use;

	if($mmode == "personalization"){ 
	$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
		<tr height=35><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>예치금 정보</b></td></tr>
	</table>

	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
		<tr height='30' bgcolor='#ffffff'>
			<td width='20%' align='center' class=s_td><font color='#000000'><b>입금대기 금액</b></font></td>
			<td width='20%' align='center' class='m_td'><font color='#000000'><b>총 입금금액(+)</b></font></td>
			<td width='20%' align='center' class='m_td'><font color='#000000'><b>총 출금금액(-)</b></font></td>
			<td width='20%' align='center' class='m_td'><font color='#000000'><b>예치금 보유금액(선수금)</b></font></td>
			<td width='20%' align='center' class='e_td'><font color='#000000'><b>출금대기 금액</b></font></td>
		</tr>
		<tr height='30'>
			<td class='list_box_td' >".number_format($state_wait)."</td>
			<td class='list_box_td'>".number_format($state_complate)."</td>
			<td class='list_box_td'>".number_format($total_use_deposit)."</td>
			<td class='list_box_td'>".number_format($total_deposit)."</td>
			<td class='list_box_td'>".number_format($request_total)."</td>
		</tr>
	</table>
	<br><br>";
	}
}

$Contents01 .= "
	<form name='searchmember' style='display:inline;'>
	<input type='hidden' name='info_type' value='$info_type'>
	<input type='hidden' name='mmode' value='$mmode'>
	<input type='hidden' name='mem_ix' value='$mem_ix'>
	<table border='0' cellpadding='0' cellspacing='0' width='100%'>";
if($mmode != "personalization"){ 
$Contents01 .= "
	<tr height=35><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>예치금 검색하기</b></td></tr>";
}
$Contents01 .= "
	<tr>
		<td align='left' colspan=2  width='100%' valign=top style='padding-top:0px;'>
			<table border='0' cellpadding='0' cellspacing='0' width='100%'>
			<tr>
				<td class='box_05' valign=top style='padding:0px;'>
					<table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
					<col width=20%>
					<col width=30%>
					<col width=18%>
					<col width=32%>";
if($mmode != "personalization"){ 
$Contents01 .= "
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
					</tr>";
}
$Contents01 .= "
					<tr height=27>
						<td class='search_box_title' bgcolor='#efefef' align=center>
							<label for='search_check'><b>처리일자</b></label>";
				if($info_type == "list" || $info_type == ""){
					$Contents01 .= "
							<select name='search_state_type'>
								<option value='state_4'>입금일</option>
								<option value='state_8'>출금일</option>
							</select>";
				}else{
					$Contents01 .= "
							<select name='search_state_type'>
								<option value='state_1'>입금대기일</option>
							</select>";
				}

				$Contents01 .= "
							<input type='checkbox' name='search_check' id='search_check' value='1' onclick='ChangeRegistDate(document.searchmember);'".CompareReturnValue("1",$search_check,"checked").">
						</td>
						<td class='search_box_item' colspan='3'>
							".search_date('sdate','edate',$sDate,$eDate)."
						</td>
					</tr>
					<tr height=27>
						<td class='search_box_title' bgcolor='#efefef' align=center><b>입금 처리상태</b></td>
						<td class='search_box_item' colspan='3'>";
					
					if($info_type == "list"){
			$Contents01 .= "
						<input type=checkbox name='state[]' value='1' id='state_1'  ".CompareReturnValue('1',$state,"checked")." ><label for='state_1'>&nbsp;입금대기</label>&nbsp;
						<input type=checkbox name='state[]' value='2' id='state_2' ".CompareReturnValue('2',$state,"checked")."><label for='state_2'>&nbsp;입금취소</label>&nbsp;
						<input type=checkbox name='state[]' value='3' id='state_3' ".CompareReturnValue('3',$state,"checked")."><label for='state_3'>&nbsp;입금완료(+)</label>&nbsp;
						";
					}else if($info_type == "deposit_wating_list"){
			$Contents01 .= "
						<input type=checkbox name='state' value='1' id='state_1' checked ><label for='state_1'>&nbsp;입금대기</label>&nbsp;
						";
					}

	$Contents01 .= "
						</td>
					</tr>";
	
	$Contents01 .= "
					<tr height=27>
						<td class='search_box_title' bgcolor='#efefef' align=center><b>사용 처리상태</b></td>
						<td class='search_box_item' colspan='3'>";
					
					if($info_type == "list"){
			$Contents01 .= "
						<input type=checkbox name='state[]' value='10' id='state_10'  ".CompareReturnValue('10',$state,"checked")." ><label for='state_10'>&nbsp;사용대기</label>&nbsp;
						<input type=checkbox name='state[]' value='11' id='state_11' ".CompareReturnValue('11',$state,"checked")."><label for='state_11'>&nbsp;사용대기취소</label>&nbsp;
						<input type=checkbox name='state[]' value='4' id='state_4' ".CompareReturnValue('4',$state,"checked")."><label for='state_4'>&nbsp;사용완료(-)</label>&nbsp;
						<input type=checkbox name='state[]' value='5' id='state_5'  ".CompareReturnValue('5',$state,"checked")." ><label for='state_5'>&nbsp;출금요청</label>&nbsp;
						<input type=checkbox name='state[]' value='6' id='state_6' ".CompareReturnValue('6',$state,"checked")."><label for='state_6'>&nbsp;출금취소</label>&nbsp;
						<input type=checkbox name='state[]' value='7' id='state_7' ".CompareReturnValue('7',$state,"checked")."><label for='state_7'>&nbsp;출금확정</label>&nbsp;
						<input type=checkbox name='state[]' value='8' id='state_8' ".CompareReturnValue('8',$state,"checked")."><label for='state_8'>&nbsp;송금완료</label>&nbsp;
						";
					}else if($info_type == "deposit_refund_list"){
			$Contents01 .= "
						<input type=checkbox name='state' value='5' id='state_2' checked><label for='state_2'>&nbsp;출금요청</label>&nbsp;
						";
					
					}else if($info_type == "deposit_withdrawal_list"){
			$Contents01 .= "
						<input type=checkbox name='state' value='7' id='state_3' checked><label for='state_3'>&nbsp;출금확정</label>&nbsp;
						";
					}else if($info_type == "4"){
			$Contents01 .= "
						<input type=checkbox name='state' value='4' id='state_4' checked><label for='state_4'>&nbsp;사용완료(-)</label>&nbsp;
						";
					}

	$Contents01 .= "
						</td>
					</tr>";

	$Contents01 .= "
					<tr>
						<td class='search_box_title'>검색어
						<span style='padding-left:2px' class='helpcloud' help_width='220' help_height='30' help_html='검색하시고자 하는 값을 ,(콤마) 구분으로 넣어서 검색 하실 수 있습니다'></span>
						
						<input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'> <label for='mult_search_use'>(다중검색 체크)</label> <img src='/admin/images/icon_q.gif' align=absmiddle/>
						</td>
						<td class='search_box_item' colspan='3'>
							<table cellpadding=0 cellspacing=0 border='0'>
							<tr>
								<td valign='top'>
									<div style='padding-top:5px;'>
									<select name='search_type' id='search_type'  style=\"font-size:12px;\">";
if($mmode != "personalization"){ 
$Contents01 .= "
									<option value='cmd.name' ".CompareReturnValue("cmd.name",$search_type).">회원명</option>
									<option value='cu.id' ".CompareReturnValue("cu.id",$search_type).">아이디</option>
									<option value='d.oid' ".CompareReturnValue("d.oid",$search_type).">주문번호</option>";
}
$Contents01 .= "
									<option value='d.charger_name' ".CompareReturnValue("d.charger_name",$search_type).">담당자명</option>
									
									</select>
									</div>
								</td>
								<td style='padding:5px;'>
									<div id='search_text_input_div'>
										<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='width: 150px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
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
}else{
	$start = ($page - 1) * $max;
}

if($mmode == "personalization"){
	$where .= " and d.uid = '".$mem_ix."' ";
}

if($info_type == "list" or $info_type == ""){
	
}else if($info_type == "1"){
	$where .= " and d.state = '0' ";
}else if($info_type == "2"){
	$where .= " and d.state = '9' ";
}else if($info_type == "3"){
	$where .= " and d.state = '1' ";
}else if($info_type == "4"){
	$where .= " and d.state = '2' ";
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
			d.*
		from 
			shop_deposit as d 
			left join common_user as cu on (d.uid = cu.code)
			left join common_member_detail as cmd on (cu.code = cmd.code)
		where
			1
			$where";
$db->query($sql);
$db->fetch();
$total = $db->total;

if($db->dbms_type == "oracle"){
	$sql = "select
			*
		from 
			shop_deposit as d
			left join common_user as cu on (d.uid = cu.code)
			left join common_member_detail as cmd on (cu.code = cmd.code)
		where
			1
			$where
			order by d.edit_date DESC, deposit_ix DESC LIMIT $start, $max";
			
$db->query($sql);

}else{

$sql = "select
			d.*,
			cu.id as member_id,
			AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
		from 
			shop_deposit as d 
			left join common_user as cu on (d.uid = cu.code)
			left join common_member_detail as cmd on (cu.code = cmd.code)
		where
			1
			$where
			order by d.edit_date DESC, deposit_ix DESC LIMIT $start, $max";

$db->query($sql);
}

$data_array = $db->fetchall();

if($mode == "excel"){	//엑셀다운로드
	$info_type = 'deposit_info';
	$goods_infos = $data_array;
	include("excel_out_columsinfo.php");
	$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='deposit_info_".$info_type."' ";
	$db->query($sql);
	$db->fetch();
	$stock_report_excel = $db->dt[conf_val];

	$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='check_deposit_info_".$info_type."' ";
//	echo nl2br($sql)."<br><br>";
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
	if(is_array($check_colums)){
		foreach($check_colums as $key => $value){
			$inventory_excel->getActiveSheet(0)->setCellValue($col . "1", $columsinfo[$value][title]);
			$col++;
		}
	}

	$before_pid = "";

	for($i = 0; $i < count($goods_infos); $i++)
	{
		$j="A";
		foreach($check_colums as $key => $value){
			if($key == "regdate"){	//처리일자
				switch($goods_infos[$i][state]){
					case '1':
						$value_str = $goods_infos[$i][waiting_date];
						break;
					case '2':
						$value_str = $goods_infos[$i][cancel_date];
						break;
					case '3':
						$value_str = $goods_infos[$i][complete_date];
						break;
					case '4':
						$value_str = $goods_infos[$i][use_date];
						break;
					case '5':
						$value_str = $goods_infos[$i][w_request_date];
						break;
					case '6':
						$value_str = $goods_infos[$i][w_cancel_date];
						break;
					case '7':
						$value_str = $goods_infos[$i][w_fixed_date];
						break;
					case '8':
						$value_str = $goods_infos[$i][w_complate_date];
						break;
				}
			}else if($key == "use_type"){		//입출금 구분
				switch($goods_infos[$i][use_type]){
					case 'P':
						$value_str = '입금';
						break;
					case 'W':
						$value_str = '출금';
						break;
				}
			}else if($key == "state"){	//처리상태
				switch($goods_infos[$i][state]){
					case '1':
						$value_str = '입금대기';
						break;
					case '2':
						$value_str = '입금취소';
						break;
					case '3':
						$value_str = '입금완료';
						break;
					case '4':
						$value_str = '사용완료';
						break;
					case '5':
						$value_str = '출금요청';
						break;
					case '6':
						$value_str = '출금취소';
						break;
					case '7':
						$value_str = '출금확정';
						break;
					case '8':
						$value_str = '송금완료';
						break;
					case '10':
						$value_str = '사용대기';
						break;
					case '11':
						$value_str = '사용대기취소';
						break;
				}
			}else if($key == "use_state"){
				switch($goods_infos[$i][use_state]){
					case '1':
						$value_str = '지연취소';
						break;
					case '2':
						$value_str = '고객입금';
						break;
					case '3':
						$value_str = '주문취소';
						break;
					case '4':
						$value_str = '주문교환';
						break;
					case '5':
						$value_str = '주문반품입금';
						break;
					case '6':
						$value_str = '마케팅';
						break;
					case '7':
						$value_str = '상품구매';
						break;
					case '8':
						$value_str = '고객요청';
						break;
					case '9':
						$value_str = '기타';
						break;
				}
			}else if($key == "deposit"){
				$value_str = $goods_infos[$i][deposit];
			}else if($key == "use_deposit"){
				$value_str = $goods_infos[$i][use_deposit];
			}else if($key == "user_name"){
				$value_str = $goods_infos[$i][name];
			}else if($key == "user_id"){
				$value_str = $goods_infos[$i][member_id];
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
	if(is_array($check_colums)){
		foreach($check_colums as $key => $value){
			$inventory_excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
			$col++;
		}
	}

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="deposit_Info_'.$info_type.'.xls"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'Excel5');
	$objWriter->save('php://output');

	exit;
}

if($info_type == 'deposit_refund_list'){
	$title_name = "출금금액";
}else{
	$title_name = "입/출금 금액";
}

$Contents01 .= "
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
	<tr height=30 >
		<td>
			<b>전체 : ".$total." 개</b> 
		</td>
		<td colspan=5 align=right>";
	if($mmode != "personalization"){ 
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents01 .= "<span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'>
			<a href='excel_config.php?".$QUERY_STRING."&info_type=deposit_info&excel_type=deposit_info_excel' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a></span>";
		}else{
			$Contents01 .= "
			<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
		}

		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents01 .= " <a href='deposit_list.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}else{
			$Contents01 .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}
	}
$Contents01 .= "
		</td>
	</tr>
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>
	<form name=reserve_list method=post action='deposit.act.php' onsubmit='return CheckDelete(this)' target=''>
	<input type='hidden' name='act' value='deposit_select_update'>
	<input type='hidden' name='info_type' value='".$info_type."'>
	<col width=3%>
	<col width=12%>

	<col width=6%>
	<col width=6%>
	<col width=9%>";
if($mmode != "personalization"){ 
$Contents01 .= "
	<col width=9%>";
}
$Contents01 .= "
	<col width=12%>
	<col width=*>
	<col width=8%>
	<col width=9%>";
if($info_type == 'deposit_refund_list'){	//출고요청관리 페이지일 경우 출금가능여부 추가 2014-07-23
$Contents01 .= "<col width=8%>";
}
$Contents01 .= "
	<col width=11%>
	<tr bgcolor=#efefef style='font-weight:bold' align=center height=28>
		<td class='s_td'><input type=checkbox class=nonborder id='all_fix' onclick='fixAll(document.reserve_list)'></td>
		<td class='m_td'>처리일</td>
		<td class='m_td'>처리상태</td>
		<td class='m_td'>타입</td>
		<td class='m_td'>".$title_name." </td>
		<td class='m_td'>현 보유예치금</td>";
if($mmode != "personalization"){ 
$Contents01 .= "
		<td class='m_td'>회원명/ID</td>";
}
$Contents01 .= "
		<td class='m_td'>입/출금 상세내역</td>
		<td class='m_td'>처리담당자</td>
		<td class='m_td'>입/출금 구분</td>";

if($info_type == 'deposit_refund_list'){	//출고요청관리 페이지일 경우 출금가능여부 추가 2014-07-23

$Contents01 .= "<td class='m_td'>출금가능여부</td>";
}
$Contents01 .= "
		<td class='e_td'>관리</td>
	</tr>";

if(count($data_array) > 0){
	for($i=0;$i  < count($data_array); $i++){
		switch($data_array[$i][use_type]){
			case 'P':
				$use_type = '입금';
				break;
			case 'W':
				$use_type = '출금';
				break;
		}

		switch($data_array[$i][state]){
			case '1':
				$mstate = '입금대기';
				$regdate = $data_array[$i][waiting_date];
				break;
			case '2':
				$mstate = '입금취소';
				$regdate = $data_array[$i][cancel_date];
				break;
			case '3':
				$mstate = '입금완료';
				$regdate = $data_array[$i][complete_date];
				break;
			case '4':
				$mstate = '사용완료';
				$regdate = $data_array[$i][use_date];
				break;
			case '5':
				$mstate = '출금요청';
				$regdate = $data_array[$i][w_request_date];
				break;
			case '6':
				$mstate = '출금취소';
				$regdate = $data_array[$i][w_cancel_date];
				break;
			case '7':
				$mstate = '출금확정';
				$regdate = $data_array[$i][w_fixed_date];
				break;
			case '8':
				$mstate = '송금완료';
				$regdate = $data_array[$i][w_complate_date];
				break;
			case '10':
				$mstate = '사용대기';
				$regdate = $data_array[$i][w_use_date];
				break;
			case '11':
				$mstate = '사용대기취소';
				$regdate = $data_array[$i][c_use_date];
				break;
		}

		switch($data_array[$i][use_state]){
			case '1':
				$use_state = '지연취소';
				break;
			case '2':
				$use_state = '고객입금';
				break;
			case '3':
				$use_state = '주문취소';
				break;
			case '4':
				$use_state = '주문교환';
				break;
			case '5':
				$use_state = '주문반품입금';
				break;
			case '6':
				$use_state = '마케팅';
				break;
			case '7':
				$use_state = '상품구매';
				break;
			case '8':
				$use_state = '고객요청';
				break;
			case '9':
				$use_state = '기타';
				break;
		}

		if($data_array[$i][state] =='4' || $data_array[$i][state] =='7' || $data_array[$i][state] =='8'){
			$font_color = '#FF0000';
		}else if($data_array[$i][state] =='3'){
			$font_color = '#0054FF';
		}else{
			$font_color = '#000000';
		}
$Contents01 .= "
			<tr height=28 align=center>
				<td class='list_box_td list_bg_gray' ><input type=checkbox class=nonborder id='reserve_id' name=rid[] value='".$data_array[$i][deposit_ix]."'></td>
				<td class='list_box_td' bgcolor='#ffffff'>".$regdate."</td>
				<td class='list_box_td' bgcolor='#ffffff'>".$use_type."</td>
				<td class='list_box_td' >".$use_state."</td>";

$Contents01 .= "
				<td class='list_box_td list_bg_gray'>
				<font color='".$font_color."'>";
				if($data_array[$i][state]	== '2' || $data_array[$i][state]	== '11' || $data_array[$i][state]	== '6'){
	$Contents01 .= "<s>".number_format($data_array[$i][deposit])."</s>";
				}else if($data_array[$i][state]	== '4'){
	$Contents01 .= "-".number_format($data_array[$i][deposit])."";
				}else{
	$Contents01 .= number_format($data_array[$i][deposit]);
				}
	$Contents01 .= "<font>
				</td>";

$Contents01 .= "
				<td class='list_box_td list_bg_gray'><font color='".$font_colod."'>";
			if($data_array[$i][state]	== RESERVE_STATUS_ORDER_CANCEL){
				$Contents01 .= "<s>".number_format($data_array[$i][use_deposit])."</s>";
			}else{
				$Contents01 .= number_format($data_array[$i][use_deposit]);
			}
$Contents01 .= "<font></td>";

if($mmode != "personalization"){ 
$Contents01 .= "
				<td class='list_box_td point' bgcolor='#efefef'>
				<a href=\"javascript:PoPWindow('deposit.pop.php?code=".$data_array[$i][uid]."',750,550,'reserve_pop')\">".$data_array[$i][name]." <br> ".$data_array[$i][member_id]."</a></td>";
}
$Contents01 .= "
				<td class='list_box_td' bgcolor='#ffffff' style='padding:5px;' align=left><a href=\"javascript:PopSWindow('../order/orders.read.php?oid=".$data_array[$i][oid]."&pid=".$data_array[$i][pid]."&mmode=pop',960,600,'order_read')\">".$data_array[$i][oid]."</a><br>".$data_array[$i][etc]."</td>
				";

$Contents01 .= "
				<td class='list_box_td list_bg_gray' >".$data_array[$i][charger_name]."</td>
				<td class='list_box_td' bgcolor='#ffffff'>".$mstate."</td>";

if($info_type == 'deposit_refund_list'){	//출고요청관리 페이지일 경우 출금가능여부 추가 2014-07-23
	if($data_array[$i][state]	== '6' || $data_array[$i][state]	== '8'){
		$deposit_div = "불가";
	}else{
		$deposit_div = "가능";
	}
$Contents01 .= "<td class='list_box_td list_bg_gray' >".$data_array[$i][charger_name]."</td>";
}
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
$Contents01 .= "
					<td class='list_box_td' style='padding:3px;'>";

if($info_type == 'list'){

	if($data_array[$i][state] == '1'){
		$Contents01 .= "<a href=\"javascript:UpdateReserve('".$data_array[$i][deposit_ix]."', '2', '9','".$data_array[$i][uid]."','".$data_array[$i][etc]."','".$data_array[$i][deposit]."','".$data_array[$i][use_type]."')\"><img src='../images/icon/deposit_0722_cc.gif' border=0 align=absmiddle  style='cursor:pointer;'></a> ";
		$Contents01 .= "<a href=\"javascript:UpdateReserve('".$data_array[$i][deposit_ix]."', '3', '9','".$data_array[$i][uid]."','".$data_array[$i][etc]."','".$data_array[$i][deposit]."','".$data_array[$i][use_type]."')\"><img src='../images/icon/deposit_0722_ic.gif' border=0 align=absmiddle  style='cursor:pointer;'></a>";
	}else if($data_array[$i][state] == '5'){
		$Contents01 .= "<a href=\"javascript:UpdateReserve('".$data_array[$i][deposit_ix]."', '6', '9','".$data_array[$i][uid]."','".$data_array[$i][etc]."','".$data_array[$i][deposit]."','".$data_array[$i][use_type]."')\"><img src='../images/icon/deposit_0722_wc.gif' border=0 align=absmiddle  style='cursor:pointer;'></a> ";
		$Contents01 .= "<a href=\"javascript:UpdateReserve('".$data_array[$i][deposit_ix]."', '7', '9','".$data_array[$i][uid]."','".$data_array[$i][etc]."','".$data_array[$i][deposit]."','".$data_array[$i][use_type]."')\"><img src='../images/icon/deposit_0722_fixed.gif' border=0 align=absmiddle  style='cursor:pointer;'></a> ";
		//$Contents01 .= "<a href=\"javascript:UpdateReserve('".$data_array[$i][deposit_ix]."', '8', '9','".$data_array[$i][uid]."')\">송금완료</a> <br>";
	}else if($data_array[$i][state] == '10' && ($data_array[$i][deposit_status] != 'C' && $data_array[$i][deposit_status] != 'U')){

		$Contents01 .= "<a href=\"javascript:UpdateReserve('".$data_array[$i][deposit_ix]."', '11', '7','".$data_array[$i][uid]."','".$data_array[$i][etc]."','".$data_array[$i][deposit]."','".$data_array[$i][use_type]."')\"><img src='../images/icon/deposit_0722_w.gif' border=0 align=absmiddle  style='cursor:pointer;'></a> ";
		$Contents01 .= "<a href=\"javascript:UpdateReserve('".$data_array[$i][deposit_ix]."', '4', '7','".$data_array[$i][uid]."','".$data_array[$i][etc]."','".$data_array[$i][deposit]."','".$data_array[$i][use_type]."')\"><img src='../images/icon/deposit_0722_use.gif' border=0 align=absmiddle  style='cursor:pointer;'></a> ";
	
		//$Contents01 .= "<a href=\"javascript:UpdateReserve('".$data_array[$i][deposit_ix]."', '8', '9','".$data_array[$i][uid]."')\">송금완료</a> <br>";
	}else{
		$Contents01 .= "처리완료";
	}

	//$Contents01 .= "<a href=\"javascript:PoPWindow('deposit.pop.php?code=".$data_array[$i][uid]."',800,550,'reserve_pop')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";

}else if($info_type == 'deposit_wating_list'){
	if($data_array[$i][state] == '1'){
		$Contents01 .= "<a href=\"javascript:UpdateReserve('".$data_array[$i][deposit_ix]."', '2', '9','".$data_array[$i][uid]."','".$data_array[$i][etc]."','".$data_array[$i][deposit]."','".$data_array[$i][use_type]."')\"><img src='../images/icon/deposit_0722_cc.gif' border=0 align=absmiddle  style='cursor:pointer;'></a> ";
		$Contents01 .= "<a href=\"javascript:UpdateReserve('".$data_array[$i][deposit_ix]."', '3', '9','".$data_array[$i][uid]."','".$data_array[$i][etc]."','".$data_array[$i][deposit]."','".$data_array[$i][use_type]."')\"><img src='../images/icon/deposit_0722_ic.gif' border=0 align=absmiddle  style='cursor:pointer;'></a> ";
	}
}else if($info_type == 'deposit_refund_list'){
	if($data_array[$i][state] == '5'){
		$Contents01 .= "<a href=\"javascript:UpdateReserve('".$data_array[$i][deposit_ix]."', '6', '9','".$data_array[$i][uid]."','".$data_array[$i][etc]."','".$data_array[$i][deposit]."','".$data_array[$i][use_type]."')\"><img src='../images/icon/deposit_0722_wc.gif' border=0 align=absmiddle  style='cursor:pointer;'></a> ";
		$Contents01 .= "<a href=\"javascript:UpdateReserve('".$data_array[$i][deposit_ix]."', '7', '9','".$data_array[$i][uid]."','".$data_array[$i][etc]."','".$data_array[$i][deposit]."','".$data_array[$i][use_type]."')\"><img src='../images/icon/deposit_0722_fixed.gif' border=0 align=absmiddle  style='cursor:pointer;'></a> ";
		//$Contents01 .= "<a href=\"javascript:UpdateReserve('".$data_array[$i][deposit_ix]."', '8', '9','".$data_array[$i][uid]."')\">송금완료</a> <br>";
	}
}else if($info_type == 'deposit_withdrawal_list'){
	if($data_array[$i][state] == '7'){
		$Contents01 .= "<a href=\"javascript:UpdateReserve('".$data_array[$i][deposit_ix]."', '8', '9','".$data_array[$i][uid]."','".$data_array[$i][etc]."','".$data_array[$i][deposit]."','".$data_array[$i][use_type]."')\">송금완료</a> ";
	}
}


$Contents01 .= "</td>";
				}else{

$Contents01 .= "<td class='list_box_td' ><a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a></td>";
				}
$Contents01 .= "
			</tr>";
	}

$Contents01 .= "</form>";

}else{

$Contents01 .= "
			<tr height=60><td class='list_box_td' ".($mmode == "personalization" ? "colspan=10":"colspan=12")." align=center>예치금 내용이 없습니다.</td></tr>";
			
}

$Contents01 .= "
		</table>";

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


if($mmode == "personalization"){
	
	$P = new ManagePopLayOut();
	$P->addScript = "".$Script;//<script language='javascript' src='../include/DateSelect.js'></script>\n
	$P->strLeftMenu = buyer_accounts_menu();
	$P->Navigation = "구매자정산관리 > 예치금 충전관리";
	$P->title = "예치금 충전관리";
    $P->NaviTitle = "예치금 충전관리"; 
	$P->strContents = $Contents;
	$P->layout_display = false;
	$P->view_type = "personalization";
	echo $P->PrintLayOut();
}else{ 
	$P = new LayOut();
	$P->addScript = "".$Script;//<script language='javascript' src='../include/DateSelect.js'></script>\n
	$P->OnloadFunction = "";
	$P->strLeftMenu = buyer_accounts_menu();
	$P->Navigation = "구매자정산관리 > 예치금 충전관리";
	$P->title = "예치금 충전관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}
?>
