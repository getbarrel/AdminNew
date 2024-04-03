<?
include("../class/layout.class");


$db = new Database;
$mdb = new Database;

if($info_type == ""){
	$info_type = "list";
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

function onLoad(FromDate, ToDate){

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

$Contents01 .= "
	<form name='searchmember' style='display:inline;'>
	<input type='hidden' name='info_type' value='$info_type'>
	<table border='0' cellpadding='0' cellspacing='0' width='100%'>
	<!--tr height=25><td  ><img src='../images/dot_org.gif' align=absmiddle> <b>판매신용점수 검색하기</b></td></tr-->
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
						<td class='search_box_title' bgcolor='#efefef' align=center><b>적립상태</b></td>
						<td class='search_box_item'  >
							<input type=checkbox name='state[]' value='1' id='state_1'  ".CompareReturnValue('1',$state,"checked")." ><label for='state_1'>&nbsp;적립(+)</label>&nbsp;
							<input type=checkbox name='state[]' value='2' id='state_2' ".CompareReturnValue('2',$state,"checked")."><label for='state_2'>&nbsp;차감(-)</label>&nbsp;
						</td> 
						<td class='search_box_title' bgcolor='#efefef' align=center><b>판매신용점수구분</b></td>
						<td class='search_box_item'  >
							<select name='use_state' id='use_state' style='display:'>
								<option value='' >선택</option>
								<option value='1' ".CompareReturnValue("1",$db->dt[use_state],"selected").">입금완료</option>
								<option value='2' ".CompareReturnValue("2",$db->dt[use_state],"selected").">배송완료</option>
								<option value='3' ".CompareReturnValue("3",$db->dt[use_state],"selected").">구매확정</option>
								<option value='4' ".CompareReturnValue("4",$db->dt[use_state],"selected").">입금후취소</option>
								<option value='5' ".CompareReturnValue("5",$db->dt[use_state],"selected").">교환확정</option>
								<option value='6' ".CompareReturnValue("6",$db->dt[use_state],"selected").">반품확정</option>
								<option value='7' ".CompareReturnValue("7",$db->dt[use_state],"selected").">배송지연</option>
								<option value='8' ".CompareReturnValue("8",$db->dt[use_state],"selected").">추가배송지연</option>
								<option value='9' ".CompareReturnValue("9",$db->dt[use_state],"selected").">기타</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class='search_box_title' ><span style='vertical-align:middle;'>검색어</span>
						<span style='padding-left:2px;vertical-align:middle;' class='helpcloud' help_width='220' help_height='30' help_html='검색하시고자 하는 값을 ,(콤마) 구분으로 넣어서 검색 하실 수 있습니다'><img src='/admin/images/icon_q.gif' align=absmiddle/></span>
						
						<input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크' style='vertical-align:middle;'> <label for='mult_search_use' style='vertical-align:middle;'>(다중검색 체크)</label>
						</td>
						<td class='search_box_item' colspan='3'>
							<table cellpadding=0 cellspacing=0 border='0'>
							<tr>
								<td valign='top'>
									<div style='padding-top:5px;'>
									<select name='search_type' id='search_type'  style=\"font-size:12px;\">
									<option value='ccd.com_ceo' ".CompareReturnValue("ccd.com_ceo",$search_type).">사업자명</option>
									<option value='cu.id' ".CompareReturnValue("cu.id",$search_type).">셀러ID</option>
									<option value='csp.charger_name' ".CompareReturnValue("csp.charger_name",$search_type).">담당자명</option>
									<option value='csp.oid' ".CompareReturnValue("csp.oid",$search_type).">주문번호</option>
									<option value='od.pid' ".CompareReturnValue("csp.pid",$search_type).">상품번호</option>
									<option value='od.pname' ".CompareReturnValue("csp.pname",$search_type).">상품명</option>
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
					<tr height=27>
						<td class='search_box_title' bgcolor='#efefef' align=center>
							<label for='search_check'><b>처리일자</b></label>
							<select name='search_state_type'>
								<option value='state_4'>적립일</option>
								<option value='state_8'>차감일</option>
							</select>
							<input type='checkbox' name='search_check' id='search_check' value='1' onclick='ChangeRegistDate(document.searchmember);'".CompareReturnValue("1",$search_check,"checked").">
						</td>
						<td class='search_box_item' colspan='3'>
							".search_date('sdate','edate',$sDate,$eDate)."
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

//처리상태 
if(is_array($state) && count($state)>0){		//노출여부 
	$where.=" AND csp.state IN ('".implode("','",$state)."')";
}else{
	if($state != ""){
		$where .= " and csp.state = '".$state."'";
	}else{
		$state =array();
	}
}

//판매신용점수구분
if($use_state != ""){
	$where .= " and csp.use_state = '".$use_state."' ";
}

$search_text = trim($search_text);
if($db->dbms_type == "oracle"){
	if($search_type != "" && $search_text != ""){
		if($search_type == "name" || $search_type == "mail" || $search_type == "pcs" || $search_type == "tel" ){
			$where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
			$count_where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
		}else{
			$where .= " and $search_type LIKE '%$search_text%' ";
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

if($search_check == '1'){
	if($sdate != "" && $edate != ""){
		if($db->dbms_type == "oracle"){
			$where .= " and  to_char(csp.regdate, 'YYYYMMDD') between $sdate and $edate ";
		}else{
			$where .= " and  date_format(csp.regdate,'%Y-%m-%d') between '$sdate' and '$edate' ";
		}
	}
}

$sql = "select
			csp.*
		from
			common_seller_penalty as csp 
			inner join common_company_detail as ccd on (ccd.company_id = csp.company_id)
			inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
			left join common_member_detail as cmd on (csd.charge_code = cmd.code)
			left join common_user as cu on (cmd.code = cu.code)
			left join shop_order_detail as od on (csp.od_ix = od.od_ix)
		where
			1
			$where";
$db->query($sql);
$db->fetch();
$total = $db->total;

if($db->dbms_type == "oracle"){
$sql = "select
			ccd.com_name,
			AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
			ccd.com_ceo,
			cu.id,
			cmd.code,
			csp.*,
			od.pname,
			od.option_text,
			od.pcnt
		from
			common_seller_penalty as csp 
			inner join common_company_detail as ccd on (ccd.company_id = csp.company_id)
			inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
			left join common_member_detail as cmd on (csd.charge_code = cmd.code)
			left join common_user as cu on (cmd.code = cu.code)
			left join shop_order_detail as od on (csp.od_ix = od.od_ix)
		where
			1
			$where
			order by csp.edit_date DESC, penalty_ix DESC LIMIT $start, $max";

}else{

$sql = "select
			ccd.com_name,
			AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
			ccd.com_ceo,
			cu.id,
			cmd.code,
			csp.*,
			od.pname,
			od.option_text,
			od.pcnt
		from
			common_seller_penalty as csp 
			inner join common_company_detail as ccd on (ccd.company_id = csp.company_id)
			inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
			left join common_member_detail as cmd on (csd.charge_code = cmd.code)
			left join common_user as cu on (cmd.code = cu.code)
			left join shop_order_detail as od on (csp.od_ix = od.od_ix)
		where
			1
			$where
			order by csp.edit_date DESC, penalty_ix DESC LIMIT $start, $max";

}

$db->query($sql);
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
	foreach($check_colums as $key => $value){
		$inventory_excel->getActiveSheet(0)->setCellValue($col . "1", $columsinfo[$value][title]);
		$col++;
	}

	$before_pid = "";

	for($i = 0; $i < count($goods_infos); $i++)
	{
		$j="A";
		foreach($check_colums as $key => $value){
			if($key == "regdate"){	//처리일자
				switch($goods_infos[$i][state]){
					case '1':
						$value_str = $goods_infos[$i][complete_date];
						break;
					case '2':
						$value_str = $goods_infos[$i][use_date];
						break;
				}
			}else if($key == "state"){	//처리상태
				switch($goods_infos[$i][state]){
					case '1':
						$value_str = '적립(+)';
						break;
					case '2':
						$value_str = '차감(-)';
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
	<tr   >
		<td style='padding-bottom:5px;'>
			<b>전체 : ".$total." 개</b>
		</td>
		<td colspan=5 align=right>";
			
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents02 .= "<span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'>
			<a href='excel_config.php?".$QUERY_STRING."&info_type=deposit_info&excel_type=deposit_info_excel' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a></span>";
		}else{
			$Contents02 .= "
			<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
		}

		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents02 .= " <a href='deposit_list.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}else{
			$Contents02 .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}

$Contents01 .= "
		</td>
	</tr>
	</table>";

$Contents01 .= "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>
	<form name=reserve_list method=post action='deposit.act.php' onsubmit='return CheckDelete(this)' target='iframe_act'>
	<input type='hidden' name='act' value='reserve_select_delete'>
	<col width=3%>
	<col width=11%>
	<col width=*>
	<col width=14%>

	<col width=12%>
	<col width=5%>
	<col width=5%>

	<col width=7%>
	<col width=10%>
	<col width=7%>
	<col width=5%>
	<tr bgcolor=#efefef style='font-weight:bold' align=center height=33>
		<td class='s_td'><input type=checkbox class=nonborder id='all_fix' onclick='fixAll(document.reserve_list)'></td>
		<td class='m_td'>주문번호</td>
		<td class='m_td'>상품명/옵션명/수량</td>
		<td class='m_td'>셀러ID/셀러명/사업자명</td>
		<td class='m_td'>적립내용</td>
		<td class='m_td'>점수 </td>
		<td class='m_td'>총점수</td>
		<td class='m_td small' ><b>판매신용점수<br>구분</b></td>
		<td class='m_td small'><b>처리일자<br>처리담당자</b></td>
		<td class='e_td'>관리</td>
	</tr>";

if(count($data_array) > 0){
	for($i=0;$i  < count($data_array); $i++){

		switch($data_array[$i][state]){
			case '1':
				$mstate = '적립(+)';
				$regdate = $data_array[$i][complete_date];
				break;
			case '2':
				$mstate = '차감(-)';
				$regdate = $data_array[$i][use_date];
				break;
		}

		switch($data_array[$i][use_state]){
			case '1':
				$use_state = '입금완료';
				break;
			case '2':
				$use_state = '배송완료';
				break;
			case '3':
				$use_state = '구매확정';
				break;
			case '4':
				$use_state = '입금후취소';
				break;
			case '5':
				$use_state = '교환승인';
				break;
			case '6':
				$use_state = '반품승인';
				break;
			case '7':
				$use_state = '입금완료후 발송지연';
				break;
			case '8':
				$use_state = '입금완료후 추가발송지연';
				break;
			case '9':
				$use_state = '기타';
				break;
		}

		if($data_array[$i][state] !='4'){	//적립상태,사용구분 선택후 수정가능 부분
			$add_display = '';
			$cancel_display = 'none';
			$font_color = '#0054FF';
		}else{
			$add_display = 'none';
			$cancel_display = '';
			$font_color = '#FF0000';
		}

$Contents01 .= "
			<tr height=78 align=center>
				<td class='list_box_td list_bg_gray' ><input type=checkbox class=nonborder id='reserve_id' name=rid[] value='".$data_array[$i][penalty_ix]."'></td>
				<td class='list_box_td' bgcolor='#ffffff'>
					<a href=\"javascript:PopSWindow('../order/orders.read.php?oid=".$data_array[$i][oid]."&pid=".$data_array[$i][pid]."&mmode=pop',960,600,'order_read')\">
					".$data_array[$i][oid]."</a>
				</td>
				<td class='list_box_td' bgcolor='#ffffff' style='padding:10px;line-height:150%;text-align:left;'>
					<b>".$data_array[$i][pname]."</b><br>
					".$data_array[$i][option_text]."
					수량 : ".$data_array[$i][pcnt]." 개
				</td>
				<td class='list_box_td' bgcolor='#ffffff' style='padding:2px;line-height:150%;'>
					".$data_array[$i][id]."<br>
					".$data_array[$i][com_name]."<br>
					".$data_array[$i][com_ceo]."
				</td>
				<td class='list_box_td' >".$data_array[$i][etc]."</td>
				<td class='list_box_td list_bg_gray'><font color='".$font_colod."'>";
			if($data_array[$i][state]	== '2'){
$Contents01 .= "<font color='red'> -".number_format($data_array[$i][penalty])."</font>";
			}else{
$Contents01 .= number_format($data_array[$i][penalty]);
			}
$Contents01 .= "<font>
				</td>
				<td class='list_box_td list_bg_gray'><font color='".$font_colod."'>".number_format($data_array[$i][use_penalty])."<font></td>
				<td class='list_box_td point' bgcolor='#efefef'>".$use_state."</td>
				<td class='list_box_td' bgcolor='#ffffff' style='padding:5px;' align=left>".$regdate."<br>".$data_array[$i][charger_name]."</td>
				<td class='list_box_td' style='padding:3px;'>
					<a href=\"javascript:PoPWindow('seller_penalty.pop.php?company_id=".$data_array[$i][company_id]."',750,550,'penalty_pop')\">
						<img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align='absmiddle'>
					</a>
				</td>
			</tr>";
	}

$Contents01 .= "</form>";

}else{

$Contents01 .= "
			<tr height=60><td class='list_box_td' colspan=11 align=center>상품 레벨점수 내역이 존재하지 않습니다.</td></tr>";
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

 
$ButtonString = "
		<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
		<tr bgcolor=#ffffff >
			<td colspan=4 align=center>
				<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' >
			</td>
		</tr>
		</table>";

$Contents = $Contents01;

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("상품 레벨점수 관리", $help_text);

$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "";
$P->strLeftMenu = seller_menu();
$P->Navigation = "셀러관리 > 상품 레벨점수 관리";
$P->title = "상품 레벨점수 관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();
?>
