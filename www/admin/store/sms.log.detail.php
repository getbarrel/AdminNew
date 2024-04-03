<? 
include("../class/layout.class");
include("$DOCUMENT_ROOT/class/sms.class");
//include("$DOCUMENT_ROOT/shop_bbs/shop_board.lib.php");

$db = new MySQL;
$mdb = new MySQL;
$sms_design = new SMS;


	//검색 1주일단위 디폴트
	if ($startDate == ""){
		$before7day = mktime(0, 0, 0, date("m")  , date("d")-7, date("Y"));

		$startDate = date("Y-m-d", $before7day);
		$endDate = date("Y-m-d");

	}

	if($mode!="search"){
		$orderdate=1;
	}
	if($nowMonth){
		$nowMonth = $nowMonth;
	}else{
        $nowMonth = array_pop($sms_design->getSMSSendLogTable());
	}
	if(empty($nowMonth)){
		echo "<script>alert('등록된 발송로그가 존재하지 않습니다.'); history.back();</script>";
		exit;
	}
	$table = "msg_result_".$nowMonth;
	echo "<hr>";
	print_r($table);


	$mstring .="<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='margin-top:10px;'>
			<tr>
				<td align='left' colspan=6 >".GetTitleNavigation("SMS 발송로그", "마케팅지원 > SMS 발송목록 ")."</td>
			</tr> 
			<tr>
				<td>
				<form name='searchmember' method='GET'>
				<input type='hidden' name='mode' value='search' />
				<input type=hidden name='license' value='".$admininfo[mall_domain_key]."'><!-- license로 변경 kbk 13/09/23 -->
				<table border='0' cellpadding='0' cellspacing='0' width='100%'>
					<tr>
					<td style='width:100%;' valign=top colspan=3>
						<table width=100%  border=0 cellpadding='0' cellspacing='0'>
							<tr>
								<td align='left' colspan=2 width='100%' valign=top style='padding-top:5px;'>
										<TABLE cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0>
										<TR>
											<TD bgColor=#ffffff style='padding:0 0 0 0;'>
											<table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
												<col width='20%'>
												<col width='30%'>
												<col width='18%'>
												<col width='32%'>
												 <tr height='27'>
													<th class='search_box_title' bgcolor='#efefef' width='100' align='center'>
														발송날짜
													</th>
													<td class='search_box_item' colspan='3'>
														".getSendTable($nowMonth)."
													</td>
												 </tr>
												 <tr height='27'>
													<th class='search_box_title' bgcolor='#efefef' width='100' align='center'>SMS/LMS/KAKAO 구분</th>
													<td class='search_box_item' colspan='3'>
														<input type='checkbox' name='sms_type[]' id='sms' value='1' ".CompareReturnValue("1",$sms_type,"checked")."><label for='sms'>SMS</label>
														<input type='checkbox' name='sms_type[]' id='lms' value='3' ".CompareReturnValue("3",$sms_type,"checked")."><label for='lms'>LMS</label>
														<input type='checkbox' name='sms_type[]' id='kakao' value='6' ".CompareReturnValue("6",$sms_type,"checked")."><label for='kakao'>KAKAO</label>
													</td>
												 </tr>
												 <tr height='27'>
													<th class='search_box_title' bgcolor='#efefef' width='100' align='center'>전달여부</th>
													<td class='search_box_item' colspan='3'>
														<input type='checkbox' name='success_type[]' id='send_o' value='100' ".CompareReturnValue("100",$success_type,"checked")."><label for='send_o'>전달성공</label>
														<input type='checkbox' name='success_type[]' id='send_x' value='200' ".CompareReturnValue("200",$success_type,"checked")."><label for='send_x'>전달실패</label>
													</td>
												 </tr>												 
												<tr height=27>
													<td class='search_box_title'>  검색어
													<span style='padding-left:2px' class='helpcloud' help_width='220' help_height='30' help_html='검색하시고자 하는 값을 ,(콤마) 구분으로 넣어서 검색 하실 수 있습니다'><img src='/admin/images/icon_q.gif' align=absmiddle/></span>
													
													<input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'> (다중검색 체크)
													</td>
													<td class='search_box_item' colspan='3'>
														<table cellpadding=0 cellspacing=0 border='0'>
														<tr>
															<td valign='top'>
																<div style='padding-top:5px;'>
																<select name='search_type' id='search_type'  style=\"font-size:12px;\">
																	<!--<option value='opt_name' ".CompareReturnValue("opt_name",$search_type).">성명</option>-->
																	<!--option value='mallstory_id' ".CompareReturnValue("mallstory_id",$search_type).">ID</option-->
																	<option value='dstaddr' ".CompareReturnValue("dstaddr",$search_type).">전화번호</option>
																	<option value='text' ".CompareReturnValue("text",$search_type).">발송내용</option>
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
											</TD>
										</TR>
										</TABLE>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr >
					<td colspan=3 align=center style='padding:10px 0 20px 0'>
						<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
					</td>
				</tr>
				</table>
				</form>
				</td>
			</tr>";
	$mstring .="</table>";

	if($type=="excel"){

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="supply_vendor_excel.xls"');
		header('Cache-Control: max-age=0');



		exit;
	}

	$mstring .="
			<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>";
			if($_SESSION["admininfo"]["charger_id"]=="forbiz"){
				$mstring .="
				<tr>
					<td align='right'>
						<img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 align='absmiddle' style='cursor:pointer' onclick=\"location.href='?".$QUERY_STRING."&type=excel'\" >
					</td>
				</tr>";
			}
			$mstring .="
			<tr>
				<td>";
	if($_GET["license"]){
		$mstring .=  $sms_design->getSMSProductLogDetailUTF8($_GET, $regdate,$code,$table);
	}else{
		$mstring .=  $sms_design->getSMSProductLogDetailUTF8($admininfo, $regdate,$code,$table);
	}

	$mstring .="
				</td>
			</tr>
			</table>
			";
	
	$Contents = $mstring;
	
	$Script = "
<script type='text/javascript' >
function ChangeOrderDate(frm){
	if(frm.orderdate.checked){
		$('#startDate').addClass('point_color');
		$('#endDate').addClass('point_color');
		$('#endDate').attr('disabled',false);
		$('#startDate').attr('disabled',false);
	}else{
		$('#startDate').removeClass('point_color');
		$('#endDate').removeClass('point_color');
		$('#endDate').attr('disabled',true);
		$('#startDate').attr('disabled',true);
	}
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
</script>";

if($mmode == "personalization"){
	$P = new ManagePopLayOut();
	$P->addScript = "".$Script;//<script language='javascript' src='../include/DateSelect.js'></script>\n
	//$P->strLeftMenu = buyer_accounts_menu();
	$P->Navigation = "메일링/SMS > 문자충전/관리 > SMS 발송 상세리스트";
	$P->title = "SMS 발송 상세리스트";
    $P->NaviTitle = "SMS 발송 상세리스트";
	$P->strContents = $Contents;
	$P->layout_display = false;
	$P->view_type = "personalization";
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$display_position = "store";
	if($display_position == "store"){
		$P->strLeftMenu = store_menu();
	}else{
		$P->strLeftMenu = campaign_menu();
	}
	$P->Navigation = "메일링/SMS > 문자충전/관리 > SMS 발송 상세리스트";
	$P->title = "SMS 발송 상세리스트";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

function getSendTable($nowMonth){
	global $sms_design;
    $getSendTable = $sms_design->getSMSSendLogTable();

    $select = "<select name='nowMonth'>";
    $select .= "<option value=''> 선택해주세요 </option>";
    if(is_array($getSendTable)){
    	foreach($getSendTable as $key=>$val){
            $select .= "<option value='".$val."' ".CompareReturnValue($val,$nowMonth,"selected")."> ".$val."</option>";
		}
	}
	$select .="</select>";
    return $select;
}
?>
