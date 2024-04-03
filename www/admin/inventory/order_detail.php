<?
include("../class/layout.class");
include("inventory.lib.php");
//include_once($_SERVER["DOCUMENT_ROOT"]."/include/sns.config.php");

$db = new Database;
if ($FromYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", $before10day);
	$eDate = date("Y/m/d");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;
}

if(!$ptype || $ptype=="") $ptype=1;

if($ptype==1) {
	$tab_on_txt1="class='on'";
	$tab_on_txt2="";
	$arr_product_type=$shop_product_type;
} else {
	$tab_on_txt1="";
	$tab_on_txt2="class='on'";
	$arr_product_type=$sns_product_type;
}
$product_type_txt=implode(",",$arr_product_type);

$Script = "	<script language='javascript'>

$(function() {
	$(\"#start_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){

		if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
			$('#end_datepicker').val(dateText);
		}else{
			$('#end_datepicker').datepicker('setDate','+3d');
		}
	}

	});


	$(\"#end_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력'

	});


});



function CheckOrderSubmit(frm){

	var check_incom_cnt = true;

	$('.iod_ix').each(function(){
		if($(this).is(':checked')){
			incom_cnt = parseInt($('#incom_cnt_'+$(this).val()).val());
			order_cnt = parseInt($('#order_cnt_'+$(this).val()).val());

			if(incom_cnt > order_cnt){
				alert('발주수량보다 입고수량이 클수 없습니다. 확인해주세요');
				$('#incom_cnt_'+$(this).val()).focus();
				check_incom_cnt = false;
			}
			//$(this).attr('checked','');
		}
	});

	if(!check_incom_cnt){
		return false;
	}
	if(!CheckFormValue(frm)){
		return false;
	}
}
function clearAll(frm){
		for(i=0;i < frm.iod_ix.length;i++){
				frm.iod_ix[i].checked = false;
		}
}
function checkAll(frm){
       	for(i=0;i < frm.iod_ix.length;i++){
				frm.iod_ix[i].checked = true;
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

function authorization_line(act,ala_ix,ioid,all_approve){
	if(confirm('해당 발주를 승인처리 하시겠습니까?')){
		window.frames['act'].location.href='./order_pop.act.php?act='+act+'&ala_ix='+ala_ix+'&ioid='+ioid+'&all_approve='+all_approve;
	}
}
		</script>";
/*
<tr>
	    <td align='left' colspan=4 > ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 사업자 정보</b></div>")."</td>
	  </tr>
*/

$vdate = date("Ymd", time());
$today = date("Y/m/d", time());
$vyesterday = date("Y/m/d", time()-84600);
$voneweekago = date("Y/m/d", time()-84600*7);
$vtwoweekago = date("Y/m/d", time()-84600*14);
$vfourweekago = date("Y/m/d", time()-84600*28);
$vyesterday = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
$voneweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
$v15ago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
$vfourweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
$vonemonthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));
$mstring ="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left'> ".GetTitleNavigation("발주(사입)내역", "재고관리 > 발주(사입)작성 > 발주(사입)내역 ")."</td>
		</tr>";
if(true){


$sql = "select * from inventory_order io where ioid = '".$ioid."' ";

//echo $sql."<br>";
$db->query($sql);
$db->fetch();
$inventory_order_infos = $db->dt;

$mstring .="
		<tr>
			<td height=595 valign=top>
				<form  name='frm' method='post' onsubmit='return CheckOrderSubmit(this)' action='./order_pop.act.php' target='act'><!--target='act'-->
				<input type=hidden name=act value='order_change'>
				<input type=hidden name='ioid' value='$ioid'>
				<input type=hidden name='total_cart_price' value='$order_totalprice'>
				<input type=hidden name='delivery_total_price' value='$total_delivery_price'>
				<input type='hidden' id='code' value=''>
					<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center >
						<col width='50%'>
						<col width='50%'>
						<tr>
							<td height='25' style='padding:0px 0px;vertical-align:bottom'>
								<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>발주내역</b>
							</td>
							<td style='text-align:right;'>
								<table cellpadding=0 cellspacing=0 border=0 width=330 align=right class='input_table_box'>
										<col width='33%'>
										<col width='33%'>
										<col width='33%'>";

$sql = "select aa.ala_ix,aa.approve_yn,aa.approve_date,adi.disp_name,adi.charger_name,adi.charger_ix from common_authline_approve aa ,common_authline_detail_info adi where aa.aldt_ix = adi.aldt_ix and aa.ioid = '".$inventory_order_infos[ioid]."' order by adi.order_approve asc ";
//echo $sql."<br>";
$db->query($sql);
$authorization_line_infos = $db->fetchall();
//print_r($authorization_line_infos);
$mstring .="						<tr height='25'>";
				for($i=0; $i < count($authorization_line_infos);$i++){
					$mstring .="		<td class='list_box_td list_bg_gray' style='text-align:center; padding:5px;'><b>".$authorization_line_infos[$i]["disp_name"]."(".$authorization_line_infos[$i]["charger_name"].")</b></td>";
				}

$mstring .="						</tr>
										<tr height='50'>";

				$approve_yn_check = false;

				for($i=0; $i < count($authorization_line_infos);$i++){

					if($i == (count($authorization_line_infos) -1)) $all_approve='true';
					else $all_approve='false';

					if($authorization_line_infos[$i][approve_yn] == 'N'){
						if(!$approve_yn_check){
							if($authorization_line_infos[$i]["charger_ix"] == $admininfo[charger_ix] )$approve_msg = "<a href=\"javascript:authorization_line('approve','".$authorization_line_infos[$i][ala_ix]."','".$inventory_order_infos[ioid]."','".$all_approve."');\"><img src='../images/".$admininfo[language]."/btn_check_confirm.gif'></a>";
							else $approve_msg = "-";
						}else{
							if($authorization_line_infos[$i]["charger_ix"] == $admininfo[charger_ix] )$approve_msg = "전담당자승인대기중";
							else $approve_msg = "-";
						}

						$approve_yn_check = true;

					}else{
						$approve_msg = "<img src='".$required3_path."'> <br /> ".$authorization_line_infos[$i][approve_date]."";
					}

					$mstring .="		<td class='list_box_td' style='text-align:center; padding:5px;'>".$approve_msg."</td>";
				}

$mstring .="
										</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan='2' style='padding:5px 0px;'>
								<table cellpadding=0 cellspacing=0 border=0 width=100% align=center class='input_table_box'>
										<col width='20%'>
										<col width='30%'>
										<col width='20%'>
										<col width='30%'>

										<!--tr height='25'>
											<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>사전 현지 운송료</b></td>
											<td class='list_box_td' style='text-align:left; padding:10px;'><span>".number_format($inventory_order_infos[b_delivery_price])."원</span></td>
											<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>사후 현지 운송료</b></td>
											<td class='list_box_td' style='text-align:left; padding:10px;'><span>".number_format($inventory_order_infos[a_delivery_price])."원</span></td>
										</tr>
										<tr height='25'>
											<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>사전 현지 세금</b></td>
											<td class='list_box_td' style='text-align:left; padding:10px;'><span>".number_format($inventory_order_infos[b_tax])."원</span></td>
											<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>사후 현지 세금</b></td>
											<td class='list_box_td' style='text-align:left; padding:10px;'><span>".number_format($inventory_order_infos[a_tax])."원</span></td>
										</tr>
										<tr height='25'>
											<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>사전 수수료</b></td>
											<td class='list_box_td' style='text-align:left; padding:10px;'><span>".number_format($inventory_order_infos[b_commission])."원</span></td>
											<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>사후 수수료</b></td>
											<td class='list_box_td' style='text-align:left; padding:10px;'><span>".number_format($inventory_order_infos[a_commission])."원</span></td>
										</tr-->

										<tr height='25'>
											<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>발주상품 총 금액</b></td>
											<td class='list_box_td' style='text-align:left; padding:10px;'><span>".number_format($inventory_order_infos[total_price])."원</span></td>
											<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>기타현지비용/수수료</b></td>
											<td class='list_box_td' style='text-align:left; padding:10px;'><span>".number_format($inventory_order_infos[a_delivery_price] + $inventory_order_infos[a_tax] + $inventory_order_infos[a_commission])."원</span></td>
											<!--td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>배송료</b></td>
											<td class='list_box_td' style='text-align:left; padding:10px;'><span>".number_format($inventory_order_infos[total_delivery_price])."원</span></td-->
										</tr>

										<tr height='25'>
											<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>최종 결제 금액</b></td>
											<td class='list_box_td' style='text-align:left; padding:10px;'><span>".number_format($inventory_order_infos[pttotal_price])."원</span></td>
											<!--td class='list_box_td' style='text-align:left; padding:10px;'><span>".number_format($inventory_order_infos[total_price]+$inventory_order_infos[total_delivery_price])."원 (무료배송!, 배송비".number_format($inventory_order_infos[total_delivery_price])."원 할인)</span></td-->
											<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>납기일</b></td>
											<td class='list_box_td' style='text-align:left; padding:3px 10px;'>
												<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100%>
													<col width=70>
													<col width=*>
													<tr>
														<TD nowrap>
														<input type='text' name='limit_priod_s' class='textbox' value='".$inventory_order_infos[limit_priod_s]."' style='height:20px;width:70px;text-align:center;' id='start_datepicker'>  ~
														<input type='text' name='limit_priod_e' class='textbox' value='".$inventory_order_infos[limit_priod_e]."' style='height:20px;width:70px;text-align:center;' id='end_datepicker'>
														</TD>
														<!--TD style='padding:0px 10px'>
															<a href=\"javascript:select_date('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
															<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
															<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
														</TD-->
													</tr>
												</table>
											</td>
											<!--td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>발주상태</b></td>
											<td class='list_box_td point' style='text-align:left; padding:10px;'>".$inventory_order_status[$inventory_order_infos[status]]."</td-->
										</tr>
										<tr height='25'>
											<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>작성자</b></td>
											<td class='list_box_td' style='text-align:left; padding:10px;'><span>".$inventory_order_infos[order_charger]."</span></td>
											<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>결제라인</b></td>
											<td class='list_box_td' style='text-align:left; padding:3px 10px;'>
												".makeSelectBoxAuthorizationLine($inventory_order_infos[al_ix], "al_ix",$reg_al_ix)."
											</td>
										</tr>
										<tr height='25'>
											<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;' nowrap><b>발주업체명(입고처)</b></td>
											<td class='list_box_td' style='text-align:left; padding:10px;'>".SelectSupplyCompany($inventory_order_infos[ci_ix],"input_company","text")."</td>
											<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>업체담당자</b></td>
											<td class='list_box_td' style='text-align:left; padding:10px;'><input type=text class='textbox' name='incom_company_charger' id=incom_company_charger value='".$inventory_order_infos[incom_company_charger]."' style='width:200px'></td>
										</tr>
										<tr height='25'>
											<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>비고</b></td>
											<td class='list_box_td' style='text-align:left; padding:10px;' colspan=3><input type=text class='textbox' name='etc' id=order_etc value='".$inventory_order_infos[etc]."' style='width:700px'></td>
										</tr>
								</table>";



				$mstring .= "

							</td>
						</tr>
						<tr>
							<td colspan='2' height='25' style='padding:3px 0px;'>
								<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>발주상품</b>
							</td>
						</tr>
						<tr>
							<td align='left' colspan=2 style='padding-bottom:15px;'>
								<div class='tab'>
									<table class='s_org_tab' style='width:100%' border=1>
									<tr>
										<td class='tab'>
											<table id='tab_01' ".($order_type == "" ? "class='on'":"")."  >
											<tr>
												<th class='box_01'></th>
												<td class='box_02' onclick=\"document.location.href='?ioid=".$_GET[ioid]."&order_type='\">발주/입고</td>
												<th class='box_03'></th>
											</tr>
											</table>
											<table id='tab_02' ".($order_type == "part" ? "class='on'":"").">
											<tr>
												<th class='box_01'></th>
												<td class='box_02' onclick=\"document.location.href='?ioid=".$_GET[ioid]."&order_type=part'\">부분입고</td>
												<th class='box_03'></th>
											</tr>
											</table>
											<table id='tab_03' ".($order_type == "direct" ? "class='on'":"").">
											<tr>
												<th class='box_01'></th>
												<td class='box_02' onclick=\"document.location.href='?ioid=".$_GET[ioid]."&order_type=direct'\">직접발주</td>
												<th class='box_03'></th>
											</tr>
											</table>
											";
				$mstring .= "
										</td>
										<td  align='right' >
											<a href='order_detail_excel.php?".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>
										</td>
										<td class='btn' style='vertical-align:bottom;padding-bottom:5px;' align=right>

										</td>
									</tr>
									</table>
									</div>
							</td>
						</tr>
						<tr>
							<td colspan='2' style='clear:both;'>
							".PrintOrderDetail($ioid)."
							</td>
						</tr>
					</table>
				</form>
		</td>
	</tr>";
}
$mstring .="

	</table>";

$mstring = $mstring;


	$P = new ManagePopLayOut();
	$P->strLeftMenu = inventory_menu();
	$P->addScript = $Script;
	$P->Navigation = "발주(사입)관리 > 발주(사입) 상세정보 ";
	$P->NaviTitle = "발주(사입) 상세정보 ";
	$P->strContents = $mstring;
	$P->jquery_use = false;

	$P->PrintLayOut();

function PrintOrderDetail($ioid){
	global $admininfo, $admin_config, $DOCUMENT_ROOT,$nset,$page,$search_type,$search_text,$_GET,$product_type_txt;
	global $inventory_order_status, $inventory_order_infos, $order_type, $complete_status;

	$mdb = new Database;

	$where = " where io.ioid = iod.ioid  and iod.gid = g.gid ";

	if($ioid != ""){
		$where .= " and iod.ioid = '".$ioid."' ";
	}

	if($search_type != "" && $search_text != ""){
		$where .= " and io.".$search_type." LIKE '%$search_text%' ";
	}

	$startDate = $_GET["FromYY"].$_GET["FromMM"].$_GET["FromDD"];
	$endDate = $_GET["ToYY"].$_GET["ToMM"].$_GET["ToDD"];

	if($startDate != "" && $endDate != ""){
		$where .= " and  io.regdate between  $startDate and $endDate ";
	}

	$startDate = $_GET["vFromYY"].$_GET["FromMM"].$_GET["FromDD"];
	$endDate = $_GET["vToYY"].$_GET["ToMM"].$_GET["ToDD"];
	if($startDate != "" && $endDate != ""){
		$where .= " and  io.regdate between  $startDate and $endDate ";
	}

	if($admininfo[admin_level] == 9){
		$sql = "select COUNT(*) as total from inventory_order io, inventory_order_detail iod , inventory_goods g $where ";
	}else{
		$sql = "select COUNT(*) as total from inventory_order io, inventory_order_detail iod , inventory_goods g  $where and company_id = '".$admininfo["company_id"]."' ";
	}
	//echo $sql."<br>";
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[total];
	$max = 10;
	//echo $total;
	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	if($order_type != "direct" && $order_type != "part"){
		$checked_str = " checked";
	}

	$mString ="";
	$mString .= "<table cellpadding=0 cellspacing=0 width=100% bgcolor=silver border='0' style='padding:0px;margin:0px;' class='list_table_box'>";
	$mString .= "<tr bgcolor=#efefef >
		<td class=s_td width='4%'><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.frm)' ".$checked_str."></td>
		<td class=m_td width='5%' height=27 align='center'>번호</td>
		<td class=m_td width='9%' align='center'>접수일자</td>
		<td class=m_td width='*' align='center'>상품코드/상품명/옵션명</td>
		<td class=m_td width='8%' align='center'>발주수량</td>
		<td class='m_td' width='10%' align='center'>입고수량</td>
		<td class='m_td' width='10%' align='center'>입고단가</td>
		<td class='m_td' width='8%' align='center'>공급가</td>
		<td class='m_td' width='8%' align='center'>부가세</td>
		<td class=e_td width='11%' align='center'>상태</td>
		</tr>
		";
	if ($total == 0){
		if($admininfo[admin_level] == 9){
			$mString .= "<tr bgcolor=#ffffff height=50><td class='list_box_td' colspan=10 align=center>발주 내역이 존재 하지 않습니다.</td></tr>";
		}else{
			$mString .= "<tr bgcolor=#ffffff height=50><td class='list_box_td' colspan=10 align=center>발주 내역이 존재 하지 않습니다.</td></tr>";
		}
	}else{
		if($admininfo[admin_level] == 9){
			/*
			$sql = "select  io.* ,  iod.iod_ix,iod.pname,iod.option_name,iod.coprice, iod.sellprice, iod.order_cnt, iod.order_coprice,  iod.incom_cnt, iod.detail_status, sp.id , sp.pcode
						from inventory_order io, inventory_order_detail iod , ".TBL_SHOP_PRODUCT." sp
						$where
						order by  io.regdate desc
						limit $start , $max";
			*/
			$sql = "select  io.* ,  iod.iod_ix,iod.gname,iod.item_name,iod.coprice, iod.order_cnt, iod.order_coprice,  iod.incom_cnt, iod.detail_status ,g.gid ,g.cid
						from inventory_order io, inventory_order_detail iod , inventory_goods g
						$where
						order by  io.regdate desc
						limit $start, $max";
		}else{
			/*
			$sql = "select io.* ,  iod.iod_ix,iod.pname,iod.option_name,iod.coprice, iod.sellprice, iod.order_cnt, iod.order_coprice, iod.incom_cnt, iod.detail_status, sp.id , sp.pcode
						from inventory_order io, inventory_order_detail iod , ".TBL_SHOP_PRODUCT." sp
						$where and io.company_id = '".$admininfo["company_id"]."'
						order by  io.regdate desc
						limit $start , $max ";
			*/
			$sql = "select io.* ,  iod.iod_ix,iod.gname,iod.item_name,iod.coprice, iod.order_cnt, iod.order_coprice,  iod.incom_cnt, iod.detail_status ,g.gid ,g.cid
						from inventory_order io, inventory_order_detail iod , inventory_goods g
						$where and io.company_id = '".$admininfo["company_id"]."'
						order by  io.regdate desc
						limit $start, $max ";
		}
		//echo nl2br($sql)."<br>";
		$mdb->query($sql);



		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);

			$no = $total - ($page - 1) * $max - $i;


			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $mdb->dt[gid], "c"))) {
				$img_str = InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $mdb->dt[gid], "c");
			}else{
				$img_str = "../image/no_img.gif";
			}

			if($mdb->dt[detail_status] == "DC") $dc_cnt++;
			if($mdb->dt[detail_status] == "WP") $wp_cnt++;


			$mString .= "<tr bgcolor=#ffffff align=center height='50'>
			<td class='list_box_td list_bg_gray'>";
			if(in_array($mdb->dt[detail_status],$complete_status)){
				$mString .= "<input type=checkbox class=iod_ix id='iod_ix' name=iod_ix[] value='".$mdb->dt[iod_ix]."' disabled ".$checked_str.">";
			}else{
				$mString .= "<input type=checkbox class=iod_ix id='iod_ix' name=iod_ix[] value='".$mdb->dt[iod_ix]."' ".$checked_str.">";
			}
			$mString .="
			</td>
			<td class='list_box_td' bgcolor='#efefef'>".$no."</td>
			<td class='list_box_td' bgcolor='#ffffff' align=left style='line-height:130%'>
				<table cellpadding='0' cellspacing='0' border='0' width='100%'>
					<tr>
						<td>".substr($mdb->dt[regdate],0,10)."</td>
					</tr>
				</table>
			</td>

			<td class='list_box_td point' bgcolor='#efefef' style='text-align:left;line-height:130%'>
				<table>
					<tr>
						<td bgcolor='#ffffff' align=center style='padding:5px 5px' >
							<a href='./inventory_goods_input.php?gid=".$mdb->dt[gid]."' ><img src='".$img_str."' width=50 height=50 style='border:1px solid #efefef'></a>
						</td>
						<td bgcolor='#ffffff' align=left style='font-weight:normal;line-height:140%;'>
						".getIventoryCategoryPathByAdmin($mdb->dt[cid], 4)."<br>
						<a href='./inventory_goods_input.php?gid=".$mdb->dt[gid]."&mode=$mode&nset=$nset&page=$page&cid2=$cid2&depth=$depth&company_id=$company_id&brand2=$brand2&max=$max&state2=$state2&disp=$disp&search_type=$search_type&search_text=".trim($search_text)."&onew=$onew&best=$best&sale=$sale&event=$event&wnew=$wnew&mnew=$mnew' target='_blank'><b>  ".$mdb->dt[gname]."</b>
						".($mdb->dt[item_name] != "" ? "<br><b>단품명 : ".$mdb->dt[item_name]."</b>":"")."
						</a>
						</td>
					</tr>
				</table>
			</td>
			<td bgcolor='#ffffff'>".$mdb->dt[order_cnt]."<input type=hidden id='order_cnt_".$mdb->dt[iod_ix]."' value='".$mdb->dt[order_cnt]."'> </td>
			<td bgcolor='#ffffff'>
				<input type=text class='textbox number' name='order_infos[".$mdb->dt[iod_ix]."][incom_cnt]' id='incom_cnt_".$mdb->dt[iod_ix]."' value='".$mdb->dt[order_cnt]."' size=7  ".(in_array($mdb->dt[detail_status],$complete_status) ? "readonly style='border:0px;'":"")." ".($_GET["order_type"] == ""  ? "readonly ":"").">
			</td>
			<td bgcolor='#ffffff'>".number_format($mdb->dt[order_coprice])."</td>
			<td class='list_box_td' bgcolor='#ffffff'> ".number_format($mdb->dt[order_coprice]*$mdb->dt[incom_cnt])." </td>
			<td class='list_box_td' bgcolor='#efefef'>".number_format($mdb->dt[order_coprice]*$mdb->dt[incom_cnt]*0.1)."</td>
			<td class='list_box_td' bgcolor='#ffffff' align=center>
			 ".$inventory_order_status[$mdb->dt[detail_status]]."
			</td>
			</tr>
			";
		}
	}

	if($_SERVER[QUERY_STRING] == "nset=$nset&page=$page"){
		$query_string = str_replace("nset=$nset&page=$page","",$_SERVER[QUERY_STRING]) ;
	}else{
		$query_string = str_replace("nset=$nset&page=$page&","","&".$_SERVER[QUERY_STRING]) ;
	}
	$mString .= "
					</table>";
	if($inventory_order_infos[status] != 'AR'){
		$mString .= "
					<table cellpadding=0 cellspacing=0 align=center>
					<tr height=30 bgcolor=#ffffff>
						<td>";
						if($inventory_order_infos[status] == "OR" ||  $inventory_order_infos[status] == "OC"){
							 if($order_type == "direct"){
								$mString .= "<b style='font-size:18px;font-weight:bold;color:#000000;'>선택된 상품을 직접 발주 합니다.</b> <input type=hidden name='change_status' value='DC'>";
							 }else{
								$mString .= "
									<select name=change_status style='font-size:20px;height:32px;font-weight:bold;'>
										<option value='' >발주상태 선택</option>
										<option value='OR' ".CompareReturnValue("OR",$inventory_order_infos[status],"selected").">".$inventory_order_status["OR"]."</option><!-- 발주대기 -->
										<option value='WR' ".CompareReturnValue("WR",$inventory_order_infos[status],"selected").">".$inventory_order_status["WR"]."</option><!-- 발주완료(입고대기) -->";
										if(($dc_cnt + $wp_cnt) == 0){
								$mString .= "<option value='OC' ".CompareReturnValue("OC",$inventory_order_infos[status],"selected").">".$inventory_order_status["OC"]."</option><!-- 발주취소 -->";
										}
								$mString .= "
									</select>";
							 }
						}else{
								if($order_type == "part"){
									$mString .= " <b style='font-size:18px;font-weight:bold;color:#000000;'>선택된 상품을 부분입고 합니다.</b> <input type=hidden name='change_status' value='WP'>
									<!--select name=change_status style='font-size:20px;height:32px;font-weight:bold;'>
										<option value='' >발주상태 선택</option>
										<option value='WP' ".CompareReturnValue("WP",$inventory_order_infos[status],"selected").">".$inventory_order_status["WP"]."</option>
									</select-->";
								}else if($order_type == "direct"){
									$mString .= "<b style='font-size:18px;font-weight:bold;color:#000000;'>선택된 상품을 직접 발주 합니다.</b> <input type=hidden name='change_status' value='DC'>
									<!--select name=change_status style='font-size:20px;height:32px;font-weight:bold;'>
										<option value='' >발주상태 선택</option>
										<option value='DC' ".CompareReturnValue("DC",$inventory_order_infos[status],"selected").">".$inventory_order_status["DC"]."</option>
									</select-->";
								}else{

									$mString .= "
									<select name=change_status style='font-size:20px;height:32px;font-weight:bold;'>
										<option value='' >발주상태 선택</option>
										<!--option value='OR' ".CompareReturnValue("OR",$inventory_order_infos[status],"selected").">".$inventory_order_status["OR"]."</option-->
										<option value='WR' ".CompareReturnValue("WR",$inventory_order_infos[status],"selected").">".$inventory_order_status["WR"]."</option>
										<option value='WC' ".CompareReturnValue("WC",$inventory_order_infos[status],"selected").">".$inventory_order_status["WC"]."</option>
										<!--option value='CC' ".CompareReturnValue("CC",$inventory_order_infos[status],"selected").">".$inventory_order_status["CC"]."</option-->
										<!--option value='DC' ".CompareReturnValue("DC",$inventory_order_infos[status],"selected").">".$inventory_order_status["DC"]."</option-->";
										if(($dc_cnt + $wp_cnt) == 0){
								$mString .= "<option value='OC' ".CompareReturnValue("OC",$inventory_order_infos[status],"selected").">".$inventory_order_status["OC"]."</option><!-- 발주취소 -->";
										}
								$mString .= "
									</select>";
								}
						}
						$mString .= "
						</td>
						<td  align=center style='padding:10px 10px;'>
							<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer;'>
						</td>
					</tr>
					</table>";
	}

	/*
	$mString .= "
					<table cellpadding=0 cellspacing=0 width=100%>
					<tr height=30 bgcolor=#ffffff>
						<td colspan=6 align=center style='padding:10px 0 0 0'>".page_bar($total, $page, $max,  $query_string, "")."</td>
					</tr>
					<!--tr height=30 bgcolor=#ffffff>
						<td colspan=6 align=right><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer;'></td>
					</tr-->
					</table>";
	*/


	return $mString;
}

?>