<?
include("../class/layout.class");

$db = new Database;
$mdb = new Database;


if ($sdate == "" || $edate == "" ){		//기본설정 시간
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));

//	$sDate = date("Y/m/d");
	$sdate = date("Y-m-d", $before10day);
	$edate = date("Y-m-d");

}


$Script ="
<script language='JavaScript' >
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
</script>
";


$sql = "select sum(deposit) all_deposit from ".TBL_COMMON_USER." where deposit > 0 ";
$db->query($sql);
$db->fetch();
$all_deposit = $db->dt[all_deposit];

//처리상태 history_type (1:입금대기 2:입금취소 3:입금완료 4:사용완료 5:출금요청 6:출금취소 7:출금확정 8:송금완료)
$sql = "select 
				SUM(case when use_type = 'P' then deposit else 0 end) as deposit_complete,
				SUM(case when use_type = 'W' then deposit else 0 end) as deposit_return_complete
			from 
				shop_deposit_info ";
$db->query($sql);
$db->fetch();
$deposit_complete = $db->dt[deposit_complete];
$deposit_return_complete = $db->dt[deposit_return_complete];


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

$where = " where de_ix !='' ";
//처리상태 
if(is_array($use_type) && count($use_type)>0){		//노출여부 
	$where.=" AND use_type IN ('".implode("','",$use_type)."')";
}else{
	if($use_type != ""){
		$where .= " and use_type = '".$use_type."'";
	}else{
		$use_type =array();
	}
}

$search_text = trim($search_text);
if($db->dbms_type == "oracle"){
	if($search_type != "" && $search_text != ""){
		if($search_type == "cmd.name"){
			$mem_where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
		}else{
			$mem_where .= " and $search_type LIKE  '%$search_text%' ";
		}
	}
}else{
	if($search_type != "" && $search_text != ""){
		if($search_type == "cmd.name"){
			$mem_where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
		}else if($search_type == "cu.id"){

			if(strpos($search_text,",") !== false){
				$search_array = explode(",",$search_text);
				$mem_where .= "and ( ";
				for($i=0;$i<count($search_array);$i++){

					if($i == count($search_array) - 1){
						$mem_where .= $search_type." = '".trim($search_array[$i])."'";
					}else{
						$mem_where .= $search_type." = '".trim($search_array[$i])."' or ";
					}
				}
				$mem_where .= ")";
			}else if(strpos($search_text,"\n") !== false){//\n

				$search_array = explode("\n",$search_text);

				$mem_where .= "and ( ";
				for($i=0;$i<count($search_array);$i++){

					if($i == count($search_array) - 1){
						$mem_where .= $search_type." = '".trim($search_array[$i])."'";
					}else{
						$mem_where .= $search_type." = '".trim($search_array[$i])."' or ";
					}
				}
				$mem_where .= ")";
			}else{
				$mem_where .= "and ".$search_type." = '".trim($search_text)."'";
			}
		}else{
			$where .= " and $search_type LIKE  '%$search_text%' ";
		}

		
		$sql = "select cu.code from ".TBL_COMMON_USER." cu left join ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code where 1 $mem_where ";
		$db->query($sql);
		$code_array = $db->fetchall(0);
		if(is_array($code_array)){
			foreach($code_array as $val){
				$code_where[] = $val[code];
			}
			$code_where = implode("','",$code_where);
			$where .= " and uid in ('".$code_where."')";
		}
		
	}
}


if($search_check == "1" && $sdate != "" && $edate != ""){
	$where .= " and ".$search_history_type." between  '".$sdate." 00:00:00' and '".$edate." 23:59:59' ";
}

$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<tr height=25><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>예치금 관리</b></td></tr>
</table>

<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
	<col width='33%' />
	<col width='33%' />
	<col width='*' />
	<tr height='30' bgcolor='#ffffff'>
		<td  align='center' class=s_td><font color='#000000'><b>입금금액</b></font></td>
		<td  align='center' class='m_td'><font color='#000000'><b>출금금액</b></font></td>
		<td  align='center' class='m_td'><font color='#000000'><b>현재 총 보유</b></font></td>
	</tr>
	<tr height='30'>
		<td class='list_box_td'>".number_format($deposit_complete)."</td>
		<td class='list_box_td'>".number_format($deposit_return_complete)."</td>
		<td class='list_box_td'>".number_format($all_deposit)."</td>
	</tr>
</table>
<br><br>";

$Contents01 .= "
	<form name='searchmember' style='display:inline;'>
	<input type='hidden' name='info_type' value='$info_type'>
	<input type='hidden' name='mode' value='search'>
	<input type='hidden' name='mem_ix' value='$mem_ix'>
	<table border='0' cellpadding='0' cellspacing='0' width='100%'>
	<tr height=35><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>예치금 검색하기</b></td></tr>
	<tr>
		<td align='left' colspan=2  width='100%' valign=top style='padding-top:0px;'>
			<table border='0' cellpadding='0' cellspacing='0' width='100%'>
			<tr>
				<td class='box_05' valign=top style='padding:0px;'>
					<table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
					<col width=20%>
					<col width=30%>
					<col width=18%>
					<col width=32%>
					<!--tr height=27>
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
					</tr-->
					<tr height=27>
						<td class='search_box_title' bgcolor='#efefef' align=center>
							<label for='search_check'><b>기간검색</b></label>
						</td>
						<td class='search_box_item' colspan='3'>
							".search_date('sdate','edate',$sdate,$edate)."
						</td>
					</tr>
					<tr height=27>
						<td class='search_box_title' bgcolor='#efefef' align=center><b>처리상태</b></td>
						<td class='search_box_item' colspan='3'>
						<input type=checkbox name='use_type[]' value='P' id='use_type_1'  ".CompareReturnValue('P',$use_type,"checked")." ><label for='use_type_1'>&nbsp;입금</label>&nbsp;
						<input type=checkbox name='use_type[]' value='W' id='use_type_2' ".CompareReturnValue('W',$use_type,"checked")."><label for='use_type_2'>&nbsp;출금</label>&nbsp;
						</td>
					</tr>
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
									<select name='search_type' id='search_type'  style=\"font-size:12px;\">
									<option value='cmd.name' ".CompareReturnValue("cmd.name",$search_type).">회원명</option>
									<option value='cu.id' ".CompareReturnValue("cu.id",$search_type).">아이디</option>
									<option value='oid' ".CompareReturnValue("oid",$search_type).">주문번호</option>
									
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


//쿼리 영역
$sql = "select * from shop_deposit_info $where ";
$db->query($sql);
$total = $db->total;

if($mode == "excel"){	//엑셀다운로드
	include '../include/phpexcel/Classes/PHPExcel.php';
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

	date_default_timezone_set('Asia/Seoul');

	$memberXL = new PHPExcel();

		// 속성 정의
	$memberXL->getProperties()->setCreator("포비즈 코리아")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle("Point List")
								 ->setSubject("Point List")
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory("Point List");

	// 데이터 등록
	$memberXL->getActiveSheet(0)->setCellValue('A' . 1, "처리일");
	$memberXL->getActiveSheet(0)->setCellValue('B' . 1, "처리상태");
	$memberXL->getActiveSheet(0)->setCellValue('C' . 1, "입/출금 금액");
	$memberXL->getActiveSheet(0)->setCellValue('D' . 1, "현 보유예치금");
	$memberXL->getActiveSheet(0)->setCellValue('E' . 1, "메모");
	$memberXL->getActiveSheet(0)->setCellValue('F' . 1, "회원명");
	$memberXL->getActiveSheet(0)->setCellValue('G' . 1, "아이디");
	
	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);

		switch($db->dt[use_type]){
			case 'P':
				$use_type = '입금';
				break;
			case 'W':
				$use_type = '출금';
				break;
		}

		$memberXL->getActiveSheet()->setCellValue('A' . ($i + 2), $db->dt[regdate]);
		$memberXL->getActiveSheet()->setCellValue('B' . ($i + 2), $use_type);
		$memberXL->getActiveSheet()->setCellValue('C' . ($i + 2), $db->dt[deposit]);
		$memberXL->getActiveSheet()->setCellValue('D' . ($i + 2), $db->dt[total_deposit]);
		$memberXL->getActiveSheet()->setCellValue('E' . ($i + 2), $db->dt[etc]);
		$memberXL->getActiveSheet()->setCellValue('F' . ($i + 2), get_member_name($db->dt[uid]));
		$memberXL->getActiveSheet()->setCellValue('G' . ($i + 2), get_member_id($db->dt[uid]));
	}

	$memberXL->getActiveSheet()->setTitle('예치금 내역');

	// 첫번째 시트 선택
	$memberXL->setActiveSheetIndex(0);
	$memberXL->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$memberXL->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$memberXL->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$memberXL->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$memberXL->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$memberXL->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$memberXL->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="deposit_list.xls"');
	header('Cache-Control: max-age=0');


	$objWriter = PHPExcel_IOFactory::createWriter($memberXL, 'Excel5');
	$objWriter->save('php://output');

	exit;
}

$sql = "select * from shop_deposit_info $where order by de_ix desc limit $start,$max";
$db->query($sql);
$deposit_history = $db->fetchall();

$Contents01 .= "
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
	<tr height=30 >
		<td>
			<b>전체 : ".$total." 개</b> 
		</td>
		<td colspan=5 align=right>";

		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents01 .= " <a href='?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}else{
			$Contents01 .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}
	
$Contents01 .= "
		</td>
	</tr>
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>
	<form name=reserve_list method=post action='deposit.act.php' onsubmit='return CheckDelete(this)' target=''>
	<input type='hidden' name='act' value='deposit_select_update'>
	<input type='hidden' name='info_type' value='".$info_type."'>
	<!--col width=3%-->
	<col width=14%>
	
	<col width=14%>
	<col width=14%>
	<col width=14%>
	<col width=14%>
	<col width=14%>
	<col width=*>
	<tr bgcolor=#efefef style='font-weight:bold' align=center height=28>
		<!--td class='s_td'><input type=checkbox class=nonborder id='all_fix' onclick='fixAll(document.reserve_list)'></td-->
		<td class='m_td'>처리일</td>
		<td class='m_td'>처리상태</td>
		<td class='m_td'>입/출금 금액</td>		
		<td class='m_td'>현 보유예치금</td>
		<td class='m_td'>메모</td>
		<td class='m_td'>회원명/ID</td>
		<td class='e_td'>관리</td>
	</tr>";
	if(count($deposit_history) > 0){
		foreach($deposit_history  as $val){

			switch($val[use_type]){
				
				case 'P':
					$use_type = '입금';
					break;
				case 'W':
					$use_type = '출금';
					break;
			}

			$Contents01 .= "
			<tr height=28 align=center>
				<!--td class='list_box_td list_bg_gray' ><input type=checkbox class=nonborder id='reserve_id' name=rid[] value='".$data_array[$i][history_ix]."'></td-->
				<td class='list_box_td' bgcolor='#ffffff'>".$val[regdate]."</td>
				<td class='list_box_td' bgcolor='#ffffff'>".$use_type."</td>				
				<td class='list_box_td list_bg_gray'>".number_format($val[deposit])."</td>
				<td class='list_box_td' >".number_format($val[total_deposit])."</td>
				<td class='list_box_td list_bg_gray'> ".$val[etc]."</td>
				<td class='list_box_td list_bg_gray'> ".get_member_name($val[uid])."/".get_member_id($val[uid])."</td>
";

				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
$Contents01 .= "
					<td class='list_box_td' style='padding:3px;'>
						<img src='../images/".$admininfo[language]."/btn_detail_view.gif' onclick=\"PoPWindow('deposit_pop.php?code=".$val[uid]."',750,550,'reserve_pop')\" align='absmiddle'style='cursor:pointer;' >
						";
						if($val[history_type] == '1'){
							$Contents01 .= "
							<img src='../images/icon/deposit_0722_cc.gif' border=0 align=absmiddle  style='cursor:pointer;' onclick=\"deposit_cancel('".$val[history_ix]."')\";>
							<img src='../images/icon/deposit_0722_ic.gif' border=0 align=absmiddle  style='cursor:pointer;' onclick=\"deposit_in_complete('".$val[history_ix]."')\";>";
						}else if($val[history_type] == '5'){
							$Contents01 .= "
							<img src='../images/icon/deposit_0722_wc.gif' border=0 align=absmiddle  style='cursor:pointer;'>
							<img src='../images/icon/deposit_0722_fixed.gif' border=0 align=absmiddle  style='cursor:pointer;'>
							";

						}
						$Contents01 .= "					
						
						<!--img src='../images/icon/deposit_0722_w.gif' border=0 align=absmiddle  style='cursor:pointer;'>
						<img src='../images/icon/deposit_0722_use.gif' border=0 align=absmiddle  style='cursor:pointer;'-->
					</td>";
				}else{
						
$Contents01 .= "<td class='list_box_td' ><a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a></td>";
				}
$Contents01 .= "
			</tr>";

		}
	}else{
		$Contents01 .= "
			<tr height=60><td class='list_box_td' colspan=7 align=center>예치금 내용이 없습니다.</td></tr>";
	}
$Contents01 .= "</form>";




			

$Contents01 .= "
		</table>";

$Contents01 .= "
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
<col width='10%'>
<col width='*'>
<tr height=40>";
/*
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
	$Contents01 .= "
	<td align=left><a href=\"JavaScript:SelectDelete(document.forms['reserve_list']);\"><img  src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0 align=absmiddle ></a></td>";
}else{
	$Contents01 .= "
	<td align=left><a href=\"".$auth_delete_msg."\"><img  src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0 align=absmiddle ></a></td>";
}*/
$Contents01 .= "
	
</tr>
</table>";


$Contents = $Contents01;

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("예치금 입/출금내역", $help_text);
$Script .= "
	<script language='javascript' src='./deposit_charge_info.js'></script>
";

$P = new LayOut();
$P->addScript = "".$Script;//<script language='javascript' src='../include/DateSelect.js'></script>\n
$P->OnloadFunction = "";
$P->strLeftMenu = buyer_accounts_menu();
$P->Navigation = "구매자정산관리 > 예치금 입/출금내역";
$P->title = "예치금 입/출금내역";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>