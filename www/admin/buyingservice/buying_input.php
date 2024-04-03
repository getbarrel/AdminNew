<?
include("../class/layout.class");
include("buying.lib.php");
//include_once($_SERVER["DOCUMENT_ROOT"]."/include/sns.config.php");

$db = new Database;

if($option_type == ""){
	$option_type = "basic";
}
if($_GET["bai_ix"] != ""){
	$db->query("SELECT *  FROM buyingservice_apply_info where bai_ix = '".$bai_ix."' ");
	$db->fetch();
	$buyingservice_apply_info = $db->dt;
//print_r($buyingservice_apply_info);
	$act = "update";
}else{
	$act = "insert";
}

$vdate = date("Y-m-d", time());
$today = date("Y-m-d", time());
$tommorw = date("Y-m-d", time()+84600);
$vyesterday = date("Y-m-d", time()-84600);
$voneweekago = date("Y-m-d", time()-84600*7);
$vtwoweekago = date("Y-m-d", time()-84600*14);
$vfourweekago = date("Y-m-d", time()-84600*28);
$vyesterday = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
$voneweekago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
$v15ago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
$vfourweekago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
$vonemonthago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));


$mstring ="<form name='buying_input_frm' method='POST' action='buying_input.act.php' onsubmit='return CheckFormValue(this)'  >
<input type=hidden name='act' value='".$act."'><input type=hidden name='bai_ix' value='".$bai_ix."'><input type=hidden name='mmode' value='".$mmode."'>

		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left'> ".GetTitleNavigation("사입서작성/수정", "사입관리 > 사입대행 주문현황 > 사입서작성/수정")."</td>
		</tr>
		<tr>
			<td align='left' colspan=8 style='padding-bottom:14px;'>
			<table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box'>
			  <col width=15% >
			  <col width=35%>
			  <col width=15%>
			  <col width=35%>
			  <tr bgcolor=#ffffff >
				<td class='input_box_title'> 사입자 정보 </td>
				<td class='input_box_item'>
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type=hidden name='mem_ix' id='mem_ix' value='".$buyingservice_apply_info[mem_ix]."' style='width:100px;'></td>
							<td><input type=text class='textbox' id='buying_mem_name' name='buying_mem_name' value='".$buyingservice_apply_info[buying_mem_name]."' style='width:100px;' onclick=\"PoPWindow('./member_search.php?code=".$db->dt[code]."',600,380,'member_search')\" validation=true title='사입자 정보' readonly></td>
							<td style='padding-left:5px;'><img src='../v3/images/".$admininfo["language"]."/btn_member_search.gif' align=absmiddle onclick=\"PoPWindow('./member_search.php?code=".$db->dt[code]."',600,380,'sendsms')\"  style='cursor:pointer;'></td>
						</tr>
					</table>
				</td>
				<td class='input_box_title'> 사입 신청일 </td>
				<td class='input_box_item'>
					<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100%>
						<col width=70>
						<col width=*>
						<tr>
							<TD nowrap>
							<input type=text class='textbox' name='apply_date' id='apply_date'  value='".($buyingservice_apply_info[apply_date] == "" ? date("Y-m-d"):$buyingservice_apply_info[apply_date])."' style='width:95%;' validation=true title='사입 신청일'>
							</TD>

							<TD style='padding:0px 10px'>
								<a href=\"javascript:select_date('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
								<a href=\"javascript:select_date('$tommorw','$today',1);\">내일</a>
								<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
								<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
							</TD>
						</tr>
					</table>
					</td>
			  </tr>
			  
				<tr>
					<td class='input_box_title'> 사입금액 </td>
					<td class='input_box_item'>
					<input type=text name='buying_total_price' class='textbox' id='buying_total_price' value='".$buyingservice_apply_info[buying_total_price]."' style='width:100px;'>
					</td>
					<td class='input_box_title'> 대납금액 </td>
					<td class='input_box_item'>
					<input type=text name='pre_payment_price' class='textbox' id='pre_payment_price' value='".$buyingservice_apply_info[pre_payment_price]."' style='width:100px;'>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'> 예치금 사용금액 </td>
					<td class='input_box_item'>
					<input type=text name='buying_total_price' class='textbox' id='buying_total_price' value='".$buyingservice_apply_info[buying_total_price]."' style='width:100px;'>
					</td>
					<td class='input_box_title'> 사입매장갯수 </td>
					<td class='input_box_item'>
					<input type=text name='pre_payment_price' class='textbox' id='pre_payment_price' value='".$buyingservice_apply_info[pre_payment_price]."' style='width:100px;'>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'> 입금자명/계좌번호</td>
					<td class='input_box_item'>
					<input type=text name='buying_total_price' class='textbox' id='buying_total_price' value='".$buyingservice_apply_info[buying_total_price]."' style='width:100px;'>
					</td>
					<td class='input_box_title'>  <b>처리상태</b>  </td>
					<td class='input_box_item' >";
						$mstring .= "<select name='buying_status' validation=true title='사입상태'>";
						$mstring .= "<option value=''>사입 처리상태</option>";
					foreach($_buyingservice_status as $key => $value){
						$mstring .= "<option value='".$key."' ".ReturnStringAfterCompare($key, $buyingservice_apply_info["buying_status"], " selected").">". $value."</option>";
					}
						$mstring .= "</select>";
			$mstring .= "
					</td>
					
				</tr>
			  </table>
			</td>			
		</tr>";

$mstring .="
	<tr>
		<td height=560 valign=top>
		";




$mstring .= "<div ".($option_type == "basic" ? "":"style='display:none;'")." id='buying_service_zone'>";

$mstring .="<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:5px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'> <b class=blk> 사입상세 정보  </b></div>")."</td></tr></table>";


$sql = "select * from buyingservice_apply_info_detail  where bai_ix = '".$bai_ix."'  order by regdate asc ";
//echo $sql;
$db->query($sql);

$buying_infos = $db->fetchall();

//print_r($buying_infos);
$mstring .= "		<table width='100%' cellpadding=2 cellspacing=1 bgcolor=silver id='buying_service_table' class='buying_service_table' idx=".$i." style='margin-bottom:10px' >
								<col width='8%'/>
								<col width='8%'/>
								<col width='8%'/>
								<col width='8%'/>
								<col width='7%'/>
								<col width='7%'/>
								<col width='4%'/>
								<col width='4%'/>
								<col width='4%'/>
								<col width='4%'/>
								<col width='8%'/>
								<col width='8%'/>
								<col width='8%'/>
								<col width='8%'/>
								<!--col width='8%'/-->
								<col width='12%'/>
								<tr height=25 bgcolor='#ffffff' align=center>
									<td bgcolor=\"#efefef\" class=small rowspan=2  nowrap>도매처</td>
									<td bgcolor=\"#efefef\" class=small rowspan=2  nowrap>상품구분</td>
									<td bgcolor=\"#efefef\" class=small rowspan=2  nowrap>장기명</td>
									<td bgcolor=\"#efefef\" class=small rowspan=2  nowrap>상품명</td>
									<td bgcolor=\"#efefef\" class=small rowspan=2  nowrap>색상</td>
									<td bgcolor=\"#efefef\" class=small rowspan=2 >사이즈</td>
									<td bgcolor=\"#efefef\" class=small colspan=5 >상품수량</td>
									<td bgcolor=\"#efefef\" class=small rowspan=2 >도매가</td>
									<td bgcolor=\"#efefef\" class=small rowspan=2 >합계</td>
									<td bgcolor=\"#efefef\" class=small rowspan=2 >사입금액</td>
									<td bgcolor=\"#efefef\" class=small rowspan=2 >대납요청</td>
									<!--td bgcolor=\"#efefef\" class=small rowspan=2 >요청사항</td-->
									<td bgcolor=\"#efefef\" class=small rowspan=2 >관리</td>
								</tr>
								<tr height=25 bgcolor='#ffffff' align=center>
									<td bgcolor=\"#efefef\" class=small>총수량</td>
									<td bgcolor=\"#efefef\" class=small>완료</td>
									<td bgcolor=\"#efefef\" class=small>품절</td>
									<td bgcolor=\"#efefef\" class=small>취소</td>
									<td bgcolor=\"#efefef\" class=small>대기</td>
								</tr>";

if($db->total){


	for($i=0;$i < count($buying_infos);$i++){
		$mstring .= "
								<tr bgcolor='#ffffff' align=center id='buying_service_tr'>
									<td >
									<input type=hidden name=buying_infos[".$i."][baid_ix]' id='baid_ix' value='".$buying_infos[$i][baid_ix]."'>
									".getBuyingServiceSupplierInfo($i,$buying_infos[$i][ws_ix])."
									</td>
									<td >
									".getBuyingServiceDivisionInfo($i,$buying_infos[$i][division])."
									</td>
									<td><span id=''><input type='text' class='textbox' name=buying_infos[".$i."][paper_name]' id='paper_name' style='width:80%;vertical-align:middle' value='".$buying_infos[$i][paper_name]."'></span></td>
									<td><input type='text' class='textbox' name=buying_infos[".$i."][goodss_name]' id='goodss_name' style='width:80%;vertical-align:middle' value='".$buying_infos[$i][goodss_name]."'></td>
									<td align=center ><input type='text' class='textbox' name=buying_infos[".$i."][color]' id='color'  value='".$buying_infos[$i][color]."' style='width:80%;vertical-align:middle'></td>
									<td><input type='text' class='textbox' name=buying_infos[".$i."][size]' id='size' style='width:80%;vertical-align:middle' value='".$buying_infos[$i][size]."'></td>
									<td><input type='text' class='textbox number' name=buying_infos[".$i."][amount]' id='amount' style='width:70%;vertical-align:middle' value='".$buying_infos[$i][amount]."'></td>
									<td><input type='text' class='textbox number' name=buying_infos[".$i."][buying_complete_cnt]' id='buying_complete_cnt' style='width:70%;vertical-align:middle' value='".$buying_infos[$i][buying_complete_cnt]."'></td>
									<td><input type='text' class='textbox number' name=buying_infos[".$i."][soldout_cancel_cnt]' id='soldout_cancel_cnt' style='width:70%;vertical-align:middle' value='".$buying_infos[$i][soldout_cancel_cnt]."'></td>
									<td><input type='text' class='textbox number' name=buying_infos[".$i."][user_cancel_cnt]' id='user_cancel_cnt' style='width:70%;vertical-align:middle' value='".$buying_infos[$i][user_cancel_cnt]."'></td>
									<td><input type='text' class='textbox number' name=buying_infos[".$i."][incom_ready_cnt]' id='incom_ready_cnt' style='width:70%;vertical-align:middle' value='".$buying_infos[$i][incom_ready_cnt]."'></td>
									<td><input type='text' class='textbox number' name=buying_infos[".$i."][buying_price]' id='buying_price' title='도매가' validation='true'  style='width:80%;vertical-align:middle' value='".$buying_infos[$i][buying_price]."'></td>
									<td><input type='text' class='textbox number' name=buying_infos[".$i."][total_price]' id='total' style='width:80%;vertical-align:middle' value='".$buying_infos[$i][total_price]."'></td>
									<td><input type='text' class='textbox number' name=buying_infos[".$i."][buying_total_price]' id='buying_total_price' style='width:80%;vertical-align:middle' value='".$buying_infos[$i][buying_total_price]."'></td>
									<td><input type='text' class='textbox number' name=buying_infos[".$i."][pre_payment_price]' id='pre_payment_price' style='width:80%;vertical-align:middle' value='".$buying_infos[$i][pre_payment_price]."'></td>
									<!--td><input type='text' class='textbox number' name=buying_infos[".$i."][exchange_yn]' id='exchange_yn' style='width:80%;vertical-align:middle' value='".$buying_infos[$i][exchange_yn]."'></td-->
									<td nowrap>
										<img src='../images/".$admininfo["language"]."/btn_add2.gif' border=0 align=absmiddle style='cursor:pointer;' onclick=\"CopyBuyingServiceInfoRow('buying_service_tr');\" />";

		$mstring .= "			<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle class='btn_deletes' style='".($i > 0 ?  "display:inline;":"display:none;")."cursor:pointer;' onclick=\"DeleteRow($(this));\" />";

		$mstring .= "
									</td>
								</tr>
								<tr bgcolor='#ffffff'  class='buying_service_tr2'>
									<td   bgcolor=\"#efefef\" class=small align=center>요청사항</td>
									<td colspan='15'><input type='text' class='textbox' name=buying_infos[".$i."][comment]' id='comment' style='width:99%;vertical-align:middle' value='".$buying_infos[$i][comment]."'></td>
								</tr>
								";


		}
}else{
	$mstring .= "
								<tr bgcolor='#ffffff' align=center id='buying_service_tr'>
									<td >
									".getBuyingServiceSupplierInfo(0,$ws_ix)."
									</td>
									<td >
									".getBuyingServiceDivisionInfo(0,$ws_ix)."
									</td>
									<td><span id=''><input type='text' class='textbox' name=buying_infos[0][paper_name]' id='paper_name' style='width:80%;vertical-align:middle' value=''></span><!-- 옵션 삭제 --></td>
									<td><input type='text' class='textbox' name=buying_infos[0][goodss_name]' id='goodss_name' style='width:80%;vertical-align:middle' value=''></td>
									<td align=center>
									<input type='text' class='textbox' name=buying_infos[0][color]' id='color'  value='' style='width:80%;vertical-align:middle'>
									</td>
									<td><input type='text' class='textbox' name=buying_infos[0][size]' id='size' style='width:80%;vertical-align:middle' value=''></td>
									<td><input type='text' class='textbox number' name=buying_infos[0][amount]' id='amount' style='width:70%;vertical-align:middle' value=''></td>
									<td><input type='text' class='textbox number' name=buying_infos[0][buying_complete_cnt]' id='buying_complete_cnt' style='width:70%;vertical-align:middle' value=''></td>
									<td><input type='text' class='textbox number' name=buying_infos[0][soldout_cancel_cnt]' id='soldout_cancel_cnt' style='width:70%;vertical-align:middle' value=''></td>
									<td><input type='text' class='textbox number' name=buying_infos[0][user_cancel_cnt]' id='user_cancel_cnt' style='width:70%;vertical-align:middle' value=''></td>
									<td><input type='text' class='textbox number' name=buying_infos[0][incom_ready_cnt]' id='incom_ready_cnt' style='width:70%;vertical-align:middle' value=''></td>
									<td><input type='text' class='textbox number' name=buying_infos[0][buying_price]' id='buying_price' style='width:80%;vertical-align:middle' value=''></td>
									<td><input type='text' class='textbox number' name=buying_infos[0][total_price]' id='total_price' style='width:80%;vertical-align:middle' value=''></td>
									<td><input type='text' class='textbox number' name=buying_infos[0][buying_total_price]' id='buying_total_price' style='width:80%;vertical-align:middle' value=''></td>
									<td><input type='text' class='textbox number' name=buying_infos[0][pre_payment_price]' id='pre_payment_price' style='width:80%;vertical-align:middle' value=''></td>
									<!--td><input type='text' class='textbox number' name=buying_infos[0][exchange_yn]' id='exchange_yn'  style='width:80%;vertical-align:middle'  value=''></td-->
									<td>
										<img src='../images/".$admininfo["language"]."/btn_add2.gif' border=0 align=absmiddle style='cursor:pointer;' onclick=\"CopyBuyingServiceInfoRow('buying_service_tr');\" />
										<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle class='btn_deletes' style='display:none;cursor:pointer;' onclick=\"DeleteRow($(this));\" />
									</td>
								</tr>
								<tr bgcolor='#ffffff'   class='buying_service_tr2'>
									<td   bgcolor=\"#efefef\" class=small align=center>요청사항</td>
									<td colspan='13'><input type='text' class='textbox' name=buying_infos[0][comment]' id='comment' style='width:99%;vertical-align:middle' value=''></td>
								</tr>


							";
}
$mstring .="</table>
		</div>";


$mstring .= "
				<div align=center style='padding-top:20px;'><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer' ></div>

		</td>
	</tr>
	<tr>
		<td>

		</td>
	</tr>
	</table>
	</form>";

$mstring = $mstring;

$Script = "<link type='text/css' href='../js/themes/base/ui.all.css' rel='stylesheet' />
<script type='text/javascript' src='../js/ui/ui.core.js'></script>
<script type='text/javascript' src='../js/ui/ui.datepicker.js'></script>
<script Language='JavaScript' src='buying_input.js'></script>
<script Language='JavaScript' >
function buying_info_setting(){
	var frm = document.buying_input_frm;

	frm.buying_mem_name.value = '신훈식';
	frm.supplier_name.value = '아이소다';
	//frm.buying_mem_name.value = '신훈식';
}


$(function() {
	$(\"#apply_date\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		/*
		if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
			$('#end_datepicker').val(dateText);
		}else{
			$('#end_datepicker').datepicker('setDate','+0d');
		}
		*/
	}

	});

});

function select_date(FromDate,ToDate,dType) {

	var frm = document.buying_input_frm;
	//alert(FromDate);
	$(\"#apply_date\").val(FromDate);
}

</script>";
	$P = new ManagePopLayOut();
	$P->strLeftMenu = inventory_menu();
	$P->addScript = $Script;
	$P->Navigation = "사입관리 > 사입대행 주문현황 > 사입서작성/수정 ";
	$P->NaviTitle = "사입서작성/수정 ";
	$P->strContents = $mstring;
	$P->jquery_use = false;

	$P->PrintLayOut();


?>