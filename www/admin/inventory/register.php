<?
include("../class/layout.class");
include("./inventory.lib.php");

$db = new Database;
$db1 = new Database;
$db2 = new Database;
$db3 = new Database;
$odb = new Database;
$tdb = new Database;

//print_r($_POST);

$db->query("SELECT * FROM inventory_history WHERE h_ix='$h_ix' ");
$db->fetch();

if($db->total){
	$act = "update";
	$move_status = $db->dt[status];
	$h_type = $db->dt[h_type];
	$vdate = $db->dt[vdate];
}else{
	$act = "insert";
	$vdate = date("Ymd");
	$charger_ix = $_SESSION["admininfo"]["charger_ix"];
}

$Contents ="
<form  name='input_frm' method='post' onsubmit='return registerSubmit(this)' action='./register.act.php' target='act'><!--  target='act'-->
<input type=hidden name=act value='".$act."'>
<input type=hidden name=mmode value='".$mmode."'>
<input type='hidden' name='h_ix' id='h_ix' value='".$h_ix."'>
<input type='hidden' name='h_div' id='h_div' value='".$h_div."'>
<!--input type='hidden' name='h_type' id='h_type' value='".$h_type."'-->
<input type='hidden' id='code' value=''>
<table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%' valign=top>
<tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("$title_str 작성", "".$sub_title."(사입)요청관리 > $title_str 작성")."</td>
</tr>";
if($page_type == 'adjustment'){
$Contents .=	"
<tr>
	<td align='left' colspan=4 style='padding-bottom:15px;'>
		<div class='tab'>
		<table class='s_org_tab'>
			<tr>
				<td class='tab'>
					<table id='tab_01'  ".(($adjustment_type == "stocked" || $adjustment_type == "") ? "class='on' ":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='?page_type=".$page_type."&adjustment_type=stocked'\">입고조정</td>
							<th class='box_03'></th>
						</tr>
					</table>
					<!--
					<table id='tab_01' ".(($adjustment_type == "delivery") ? "class='on' ":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='?page_type=".$page_type."&adjustment_type=delivery'\">출고조정</td>
							<th class='box_03'></th>
						</tr>
					</table>
					<table id='tab_00' ".(($adjustment_type == "basic" ) ? "class='on' ":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='?page_type=".$page_type."&adjustment_type=basic'\">기초조정</td>
							<th class='box_03'></th>
						</tr>
					</table>
					-->
				</td>
				<td align='right' style='text-align:right;vertical-align:bottom;padding:0 0 6px 4px;'>
				</td>
			</tr>
		</table>
	</div>
	</td>
</tr>";
}
$Contents .=	"
<tr >
	<td colspan=2 width='100%' valign=top style='padding-top:3px;'>";

$est_delivery_zip = explode("-", $db1->dt[est_delivery_zip]);
$est_tel = explode("-", $db1->dt[est_tel]);
$est_mobile = explode("-", $db1->dt[est_mobile]);

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

$Contents .= "

	<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center height='120px'>
		<col width='50%'>
		<col width='50%'>

		<tr>
			<td colspan='2' height='25' style='padding:5px 0px;'>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>$title_str 작성</b>
			</td>
		</tr>
		<tr>
			<td colspan='2' style='padding:0px 0px;'>
				<table cellpadding=0 cellspacing=0 border=0 width=100% align=center class='input_table_box'>
						<col width='15%'>
						<col width='40%'>
						<col width='15%'>
						<col width='30%'>
						<tr height='30'>

							<td class='input_box_title' ><b>".$sub_title."일</b> <img src='".$required3_path."'></td>
							<td class='input_box_item' >
								<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100%>
									<col width=100>
									<col width=*>
									<tr>
										<TD nowrap>
											<input type='text' class='textbox point_color' name='vdate' class='textbox' value='".$vdate."' style='height:20px;width:100px;text-align:center;' id='end_datepicker' validation='true' title='".$sub_title."일'>
										</TD>
									</tr>
								</table>
							</td>
							<td class='input_box_title' ><b>담당자</b> <img src='".$required3_path."'></td>
							<td class='input_box_item' >
							<!--input type=text class='textbox' name='charger_name' value='".$db->dt[charger_name]."' id=charger_name style='width:200px' validation='true' title='담당자'-->
							".CompayChargerSearch($_SESSION["admininfo"]["company_id"] ,$charger_ix)."
							</td>
						</tr>";

$Contents .= "
						<tr height='30'>
							<td class='input_box_title'>".$sub_title."창고 <img src='".$required3_path."'></td>
							<td class='input_box_item' >
								".SelectEstablishment($regist_company_id,"regist_company_id","select","true","onChange=\"loadPlace(this,'regist_pi_ix')\" ")."";
								if($type_div=="2"){ // 예외입고(입고등록)& 예외출고(출고등록)
									if($h_div=="1"){ // 입고
										$Contents .= "
										".SelectInventoryInfo($regist_company_id,$db->dt[regist_pi_ix],'regist_pi_ix','select','true', "validation=true title='이동창고' ")."
										 <span class='blue small'>* 기본보관장소로 재고가 입고됩니다. </span>";
										//onChange=\"loadPlaceSection(this,'regist_ps_ix','','S')\"
									}else{//출고
										$Contents .= "
										".SelectInventoryInfo($regist_company_id,$db->dt[regist_pi_ix],'regist_pi_ix','select','true', "validation=true title='이동창고' onChange=\"loadPlaceSection(this,'regist_ps_ix','','D')\"  ")."";
									}
								}else{
									if($h_div=="2"){
										$Contents .= "
										".SelectInventoryInfo($regist_company_id,$db->dt[regist_pi_ix],'regist_pi_ix','select','true', "validation=true title='이동창고' onChange=\"loadPlaceSection(this,'regist_ps_ix');\"  ")."";
									}else{
										$Contents .= "
										".SelectInventoryInfo($regist_company_id,$db->dt[regist_pi_ix],'regist_pi_ix','select','true', "validation=true title='이동창고'")."";
									}
								}
								if($h_div=="2"){
									$Contents .= "
									".SelectSectionInfo($db->dt[regist_pi_ix],$db->dt[regist_ps_ix],'regist_ps_ix',"select","true"," title='보관장소' ")."";
								}
							$Contents .= "
							</td>
							<td class='input_box_title' ><b>작성자</b> <img src='".$required3_path."'> </td>
							<td class='input_box_item' ><span>".$admininfo[charger]."</span> ".$db->dt[charger_name]." </td>

						</tr>";


if($page_type == 'stocked' || $page_type == 'delivery'){
	$Contents .= "
						<tr height='30'>
							<td class='input_box_title'> 매입처 <img src='".$required3_path."'></td>
							<td class='input_box_item'>".SelectSupplyCompany($ci_ix,"ci_ix","select", "true", $h_div)."</td>
							<td class='input_box_title'>".$sub_title." 유형 <img src='".$required3_path."'></td>
							<td class='input_box_item' >";
	if($page_type == 'stocked'){
			$Contents .= "".selectDeliveryType('1',$type_div,'','h_type')."";
	}else if($page_type == 'delivery'){
			$Contents .= "".selectDeliveryType('2',$type_div,'','h_type')."";
	}
	$Contents .= "

							</td>
						</tr>
						<tr height='30'>
							<td class='input_box_title' ><b>비고</b></td>
							<td class='input_box_item' colspan=3 ><input type=text class='textbox' name='etc' value='".$db->dt[etc]." ' id=order_etc style='width:90%'></td>
						</tr>";
}else if($page_type == 'adjustment'){
	$Contents .= "
						<tr height='30'>
							<td class='input_box_title'>".$sub_title." 유형</td>
							<td class='input_box_item' >";
	if($adjustment_type == 'stocked' || $adjustment_type == ''){
			$Contents .= "".selectDeliveryType('1',$type_div,$h_type,'h_type')."";
	}else if($adjustment_type == 'delivery'){
			$Contents .= "".selectDeliveryType('2',$type_div,$h_type,'h_type')."";
	}else  if($adjustment_type == 'basic' ){
			$Contents .= "".selectDeliveryType('1',$type_div,$type_code,'h_type','text')."";
	}
	$Contents .= "

							</td>
							<td class='input_box_title' ><b>비고</b></td>
							<td class='input_box_item' ><input type=text class='textbox' name='etc' value='".$db->dt[etc]." ' id=order_etc style='width:90%'></td>

						</tr>";
}else{
	$Contents .= "
						<tr height='30'>
							<td class='input_box_title' ><b>비고</b></td>
							<td class='input_box_item' colspan=3><input type=text class='textbox' name='etc' value='".$db->dt[etc]." ' id=order_etc style='width:90%'></td>

						</tr>";
}
$Contents .= "
				</table>";

$Contents .= "

			</td>
		</tr>


	</table>

	</td>
</tr>
<tr>
	<td  height='25' style='padding:10px 0px;'>
		<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>$title_str 품목</b>
	</td>
	<td align=right>
		<a href=\"javascript:PopGoodsSelect();\"><img src='../images/".$admininfo["language"]."/btc_goods_add.gif' border='0' align='absmiddle'  style='cursor:pointer;'></a>
		<input type=text class='textbox number' value='바코드 입력&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' id='barcode' onclick=\"Submit_bool=false;$(this).val('')\">
	</td>
</tr>
<tr>
	<td colspan='2' height=300 style='vertical-align:top;'>
	";

$Contents .= "
	<table cellpadding=3 cellspacing=0 border=0 bgcolor=#ffffff width=100% onselect='return false;' align=center class='list_table_box' id='regist_item_list'>
				<col width=4% >
				<col width=6% >
				<col width='15%' >
				<col width=6% >
				<col width=6% >
				<col width=6% >
				<col width=6% >
				<!--col width=6% >
				<col width=8% >
				<col width=6% -->
				<col width=6% >
				<col width=7% >
				<col width=6% >
				<col width=7% >
				<col width=7% >
				<tr align=center height=30 style='font-weight:bold;' >
					<td class=s_td ><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.input_frm)'></td>
					<td class=m_td >품목코드</td>
					<td class=m_td >품목정보</td>
					<td class=m_td >규격</td>
					<td class=m_td  nowrap>품목계정</td>
					<td class=m_td >단위</td>
					<td class=m_td >부가세 적용</td>
					<!--td class=m_td colspan=3>사업장/보관장소</td-->
					<td class=m_td >재고</td>
					<td class=m_td >유통기한</td>
					<td class=m_td  nowrap>".$sub_amount_title."</td>
					<td class=m_td   nowrap>".$sub_price_title."</td>
					<td class=e_td   nowrap>합계</td>
				</tr>
				<!--tr align=center height=30>
					<td class=m_td>사업장</td>
					<td class=m_td>창고</td>
					<td class=m_td>보관장소</td>
				</tr-->";
if(false){
$Contents .= "
				<!--tr bgcolor=#ffffff height=30 >
					<td colspan='7' class=m_td><b><font color='#333333'>총합계</font></b></td>
					<td class=m_td><b id='stock_sum'>0</b></td>
					<td class=m_td><b id='expiry_date_sum'></b></td>
					<td class=m_td><b id='amount_sum'><!--amount_sum--></b></td>
					<td class=m_td><b>0</b></td>
					<td align=center class=m_td colspan='1'><b> <font class='blk' style='font-size:12px;font-family:arial;'>".number_format($order_totalprice)." </font></b><font class='blk'> </font></td>
				</tr-->";
}


	$sql = "select data.*,
		(select com_name as company_name from common_company_detail ccd where ccd.company_id = data.company_id   limit 1) as company_name
		from
			(select g.cid,g.gname, g.gid, g.gcode, g.admin, g.item_account,  gu.sellprice ,  pi.place_name, ps.section_name, h.h_ix, hd.hd_ix, h.vdate, h.pi_ix , h.ps_ix , hd.amount , hd.unit, hd.standard, hd.price, pi.company_id,  ips.expiry_date, ips.stock
		from inventory_history h
		left join inventory_history_detail hd on h.h_ix = hd.h_ix
		left join inventory_goods g on hd.gid = g.gid
		right join inventory_goods_unit gu  on g.gid =gu.gid and hd.unit = gu.unit
		left join  inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit
		left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix
		left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
		where h.h_ix = '".$h_ix."'
		 ) data
		 ";
/*
	$sql = "select data.*
		from
			(select hd.*
			from inventory_history wm
			left join inventory_history_detail wmd on wm.h_ix = hd.h_ix
			left join inventory_goods g on hd.gid = g.gid
			right join inventory_goods_unit gu  on g.gid =gu.gid and hd.unit = gu.unit
			left join  inventory_product_stockinfo ips on hd.gid = ips.gid and hd.unit = ips.unit

			where wm.h_ix = '".$h_ix."'
		 ) data
		 ";
*/

$db->query($sql);
$order_goods_total = $db->total;
//echo $order_goods_total;
if($db->total){

	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);

			$ci_ix = $db->dt[ci_ix];
			$gid = $db->dt[gid];
			$gname = $db->dt[gname];
			$gname_str .= $db->dt[gname];
			$order_cnt    = $db->dt[order_cnt];
			$price = $db->dt[price];

			$place_name = $db->dt[place_name];
			$section_name = $db->dt[section_name];
			$company_name = $db->dt[company_name];
			$amount = $db->dt[amount];
			$surtax_div = $db->dt[surtax_div];
			$delivery_cnt = $db->dt[delivery_cnt];
			$entering_cnt  = $db->dt[entering_cnt];
			$unit = $db->dt[unit];
			$stock = $db->dt[stock];
			$hd_ix = $db->dt[hd_ix];



			$totalprice = $order_cnt*$price;
			$order_totalprice = $order_totalprice + $totalprice;
			//$coper = $coprice / $sellprice * 100;

			//$db->query("SELECT listprice, sellprice,  admin,company, state FROM ".TBL_SHOP_PRODUCT." WHERE id='$pid'");
			//$db->fetch();


			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $gid, "c"))) {
				$img_str = InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $gid, "c");
			}else{
				$img_str = "../image/no_img.gif";
			}

$Contents .="
				<tr height=30 depth=1 ondblclick=\"javascript:PopGoodsSelect();\"  class='helpcloud' help_height='35' help_html='더블클릭시 품목을 선택할 수 있는 창이 노출되게 됩니다.' style='cursor:pointer;'>
					<td align=center>
						<input type=hidden class='nonborder' id='hd_ix'  name=item_infos[".$i."][hd_ix] value='".$db->dt[hd_ix]."'>
						<input type=checkbox class='nonborder select_gid' id='select_gid'  name=item_infos[".$i."][select_gid] value='".$db->dt[gid]."'>
					</td>
					<td align=center id='gid_text'>".$db->dt[gid]."</td>
					<td style='padding:3px;' nowrap>
						<b id='gname'>$gname </b><input type=hidden class='textbox numeric' name='item_infos[".$i."][gid]' id='gid' value='$gid'>
					</td>
					<td align=center><span  id='standard_text'>".$db->dt[standard]."</span><input type=hidden class='textbox numeric' name='item_infos[".$i."][standard]' id='standard' value='$standard'> </td>
					<td align=center><span  id='item_account_text'>".$db->dt[item_account]."</span><input type=hidden class='textbox numeric' name='item_infos[".$i."][item_account]' id='item_account' value='".$db->dt[item_account]."'> </td>
					<td align=center><span  id='unit_text'>".getUnit($db->dt[unit], "basic_unit","","text")."</span><input type=hidden class='textbox numeric' name='item_infos[".$i."][unit]' id='unit' value='$unit'> </td>
					<td align=center><span  id='surtax_div_text'>".getSurTaxDiv($surtax_div, "surtax_div","","text")."</span><input type=hidden class='textbox numeric' name='item_infos[".$i."][surtax_div]' id='surtax_div' value='$surtax_div'> </td>

					<!--td align=center ><span id='company_name'>".$company_name."</span></td>
					<td align=center ><span id='place_name'>".$place_name."</span><input type=hidden  name='item_infos[".$i."][pi_ix]' size=2 id='pi_ix' value='".$db->dt[pi_ix]."'></td>
					<td align=center ><span id='section_name'>".$section_name."</span><input type=hidden  name='item_infos[".$i."][ps_ix]' size=2 id='ps_ix' value='".$db->dt[ps_ix]."'></td-->
					<td style='text-align:center;'>
						<span id='stock'>".$stock."</span>
					</td>
					<td style='text-align:center;' class='point'>
						<input type=text class='textbox numeric expiry_date' name='item_infos[".$i."][expiry_date]'  id='expiry_date' value='".$db->dt[expiry_date]."' size=10 title='유통기한'  >
					</td>
					<td style='text-align:center;' class='point'>
						<input type=text class='textbox numeric amount' name='item_infos[".$i."][amount]'  id='amount' value='".$db->dt[amount]."' size=8 title='수량'  ".(($move_status == "" || $move_status == "MA") ? " validation=true style='width:80%;text-align:right;padding:0 5px 0 0'":"style='border:0px;width:80%;text-align:right;padding:0 5px 0 0' readonly ")." >
					</td>
					<!--td style='text-align:center;' class='point'>
						<input type=text class='textbox numeric delivery_cnt' name='item_infos[".$i."][delivery_cnt]'  id='delivery_cnt' value='".$db->dt[delivery_cnt]."' size=8 title='출고수량'  ".(($move_status == "MO" || $move_status == "MA") ? " validation=true style='width:80%;text-align:right;padding:0 5px 0 0'":"style='border:0px;width:80%;text-align:right;padding:0 5px 0 0' readonly ").">
					</td>
					<td style='text-align:center;'>
						<input type=text class='textbox numeric entering_cnt' name='item_infos[".$i."][entering_cnt]'  id='entering_cnt' value='".$db->dt[entering_cnt]."' size=8 title='".$sub_title."수량'  ".($move_status == "MI" ? " validation=true style='width:80%;text-align:right;padding:0 5px 0 0'":"style='border:0px;width:80%;text-align:right;padding:0 5px 0 0' readonly ")."  >
					</td-->
					<td align=center class='point'><input type=text class='textbox numeric price' name='item_infos[".$i."][price]'  id='price' validation=true  value='".$db->dt[price]."' size=8 title='매입가' ></td>
					<td align=center id='total_price'>".number_format($totalprice)."</td>
				</tr>";
	}
}else{
//$Contents .="<tr height=50><td colspan=7 align=center>창고이동 요청품목 내역이  존재 하지 않습니다.</td></tr>";
$Contents .="
				<tr height=30 depth=1 ondblclick=\"javascript:PopGoodsSelect();\" style='cursor:pointer;' ><!--class='helpcloud' help_height='35' help_html='더블클릭시 품목을 선택할 수 있는 창이 노출되게 됩니다.' -->
					<td align=center><input type=checkbox class='nonborder select_gid' id='select_gid'  name=item_infos[0][select_gid] value='".$db->dt[gid]."'></td>
					<td align=center id='gid_text'>".$db->dt[customer_name]."</td>
					<td style='padding:3px;' nowrap>
						<b id='gname'>$gname </b><input type=hidden class='textbox numeric' name='item_infos[0][gid]' id='gid' value='$gid'>
					</td>

					<td align=center><span  id='standard_text'>".$db->dt[standard]."</span><input type=hidden class='textbox numeric' name='item_infos[0][standard]' id='standard' value='$standard'> </td>
					<td align=center><span  id='item_account_text'>".$db->dt[item_account]."</span><input type=hidden class='textbox numeric' name='item_infos[0][item_account]' id='item_account' value='$item_account'> </td>
					<td align=center><span  id='unit_text'>".$db->dt[unit_text]."</span><input type=hidden class='textbox numeric' name='item_infos[0][unit]' id='unit' value='$unit'> </td>
					<td align=center><span  id='surtax_div_text'>".getSurTaxDiv($surtax_div, "surtax_div","","text")."</span><input type=hidden class='textbox numeric' name='item_infos[0][surtax_div]' id='surtax_div' value='$surtax_div'> </td>

					<!--td align=center ><span id='company_name'>".$company_name."</span></td>
					<td align=center ><span id='place_name'>".$place_name."</span><input type=hidden  name='item_infos[0][pi_ix]' size=2 id='pi_ix' value='$pi_ix'></td>
					<td align=center ><span id='section_name'>".$section_name."</span><input type=hidden  name='item_infos[0][ps_ix]' size=2 id='ps_ix' value='$ps_ix'></td-->
					<td style='text-align:center;'>
						<span id='stock'>".$stock."</span>
					</td>
					<td style='text-align:center;' class='point'>
						<input type=text class='textbox numeric expiry_date' name='item_infos[0][expiry_date]'  id='expiry_date' value='".$db->dt[expiry_date]."' size=10 title='유통기한'  >
					</td>
					<td style='text-align:center;' class='point'>
						<input type=text class='textbox numeric amount' name='item_infos[0][amount]'  id='amount' value='$amount' size=8 title='수량' ".(($move_status == "" || $move_status == "MA") ? " validation=true  style='width:80%;text-align:right;padding:0 5px 0 0'":"style='border:0px;width:80%;text-align:right;padding:0 5px 0 0' readonly ")." onkeyup=\"if($(this).val()=='0'){alert('수량 0을 입력하실수 없습니다.');$(this).val('1')}$(this).parent().parent().find('#total_price').html( $(this).val()*$(this).parent().parent().find('#price').val())\">
					</td>
					<!--td style='text-align:center;' class='point'>
						<input type=text class='textbox numeric delivery_cnt' name='item_infos[0][delivery_cnt]'  id='delivery_cnt' value='$delivery_cnt' size=8 title='출고수량'  ".($move_status == "MO" ? " validation=true style='width:80%;text-align:right;padding:0 5px 0 0'":"style='border:0px;width:80%;text-align:right;padding:0 5px 0 0' readonly ")." >
					</td>
					<td style='text-align:center;'>
						<input type=text class='textbox numeric entering_cnt' name='item_infos[0][entering_cnt]'  id='entering_cnt' value='$entering_cnt' size=8 title='".$sub_title."수량'  ".($move_status == "MI" ? " validation=true style='width:80%;text-align:right;padding:0 5px 0 0'":"style='border:0px;width:80%;text-align:right;padding:0 5px 0 0' readonly ")."   >
					</td-->
					<td align=center class='point'><input type=text class='textbox numeric price' name='item_infos[0][price]' validation=true  id='price' value='$price' size=8 title='매입가' onkeyup=\"$(this).parent().parent().find('#total_price').html( $(this).val()*$(this).parent().parent().find('#amount').val())\" ></td>
					<td align=center id='total_price'>".number_format($totalprice)."</td>
				</tr>";

}


$Contents .="

			</table>
			<table cellpadding=0 cellspacing=0 border=0 width=100%>
				<tr>
					<td colspan=4 align=left>
						<table cellpadding=0>
							<tr height=40>
								<td><!--b><a href='cart.php'>이전</a></b--><img src='../images/".$admininfo["language"]."/btc_select_goods_delete.gif' border='0' align='absmiddle' onclick='checkDelete()' style='cursor:pointer;'></td>
							</tr>
						</table>
					</td>
					<td colspan=4>
						<table cellpadding=10 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center>
							<tr>
								<td align='right' style='font-size:12px;font-weight:bold;'>
								<!--품목금액  : <span class='blk' style='font-size:12px;font-family:arial;'>".number_format($order_totalprice)."</span> 원 +
								배송비 : <span class='blk' style='font-size:12px;font-family:arial;'>".number_format($total_delivery_price)."</span> 원 =
								총 주문금액 : <span class='blk' style='font-size:12px;font-family:arial;'>".number_format($order_totalprice + $total_delivery_price)."</span> 원--></td>
							</tr>
						</table><!--f:buttonSection-->
					</td>
				</tr>
			</table>
";


$Contents .= "

	";


$Contents .= "
	</td>
</tr>
<tr height=20>
	<td colspan='2' style='padding:3px;' align=center>";
	//if($order_goods_total == 0){
	//	$Contents .= " <img  src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer;' onclick=\"alert('창고이동 요청하시고자 하는 품목이 한개 이상 선택되어야 합니다.\/n 창고이동 요청예정품목에서 창고이동 요청하시고자 하는 품목을 선택해주세요 ');\">";
	//}else{
		$Contents .= " <input type='checkbox' name='is_continue' id='is_continue' value='1'><label for='is_continue'>작성후 계속 작성</label>
		<img type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer;' onclick=\"Submit_bool=true;$(this).closest('form').submit();\" >";
	//}
	$Contents .= "
	</td>
</tr>
</table>
</form>
		";

$help_text = "
<table cellpadding=2 cellspacing=0 class='small' style='line-height:120%' >
	<col width=8>
	<col width=*>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td >$title_str 작성은 ".$sub_title."창고가 다를 경우 별도로 작성을 하셔야 합니다..</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td >".$sub_title."고자 하는 정보와 품목의 수량정보를 입력하신후 저장버튼을 눌러 요청대장의 작성을 완료 하실 수 있습니다. </td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td ></td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td > </td></tr>
</table>
";



$Contents .= HelpBox("$title_str", $help_text,"100");

$Script = "
<!--link type='text/css' href='../js/themes/base/ui.all.css' rel='stylesheet' /-->
<script Language='JavaScript' type='text/javascript' src='placesection.js'></script>

<script language='JavaScript' >
function sum_order_totalprice(){
	//b_delivery_price = Number($('#b_delivery_price').val());
	a_delivery_price = Number($('#a_delivery_price').val());
	//b_tax = Number($('#b_tax').val());
	a_tax = Number($('#a_tax').val());
	//b_commission = Number($('#b_commission').val());
	a_commission = Number($('#a_commission').val());

	order_totalprice = Number($('#order_totalprice').text());

	etc_price = a_delivery_price+a_tax+a_commission;

	order_pttotalprice = order_totalprice+etc_price;

	$('#etc_price').text(etc_price)
	$('#order_pttotalprice').val(order_pttotalprice)
}


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

	$(\"#expiry_date\").datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력'
	});
});

function select_date(FromDate,ToDate,dType) {
	var frm = document.searchmember;

	$(\"#priod\").val(FromDate);
}

function num_apply(cart_key, gid) {
	var quantity = parseInt($('#quantity_'+cart_key).val()) ;
	var sellprice = parseInt($('#sellprice_'+cart_key).val()) ;
	//alert('#sellprice_'+cart_key);
	//document.write('countadd.php?cart_key='+cart_key+'&gid='+gid+'&act=mod&count='+quantity+'&sellprice='+sellprice+'&mode=$mode&est_ix=$est_ix');
	window.frames['act'].location.href='countadd.php?cart_key='+cart_key+'&gid='+gid+'&act=mod&count='+quantity+'&sellprice='+sellprice+'&mode=$mode&est_ix=$est_ix';
}

function idsearch() {
	var zip = window.open('./searchuser.php','','width=440,height=400,scrollbars=yes,status=no');
}

function input_text(){
	if($('#msg2').attr('rel') == 'first'){
		$('#msg2').val('');
		$('#msg2').attr('rel','');
	}
}


function clearAll(frm){
		$('.select_gid').each(function(){
			$(this).attr('checked',false);
		});
		/*
		for(i=0;i < frm.gid.length;i++){
				frm.gid[i].checked = false;
		}
		*/
}

function checkAll(frm){
       	$('.select_gid').each(function(){
			$(this).attr('checked','checked');
		});
}
function fixAll(frm){
	//alert(frm.all_fix.checked);
	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;

	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
}

function checkDelete(){
   var tbody = $('#regist_item_list tbody');
   var total_rows = tbody.find('tr[depth^=1]').length;
   var thisRow = '';

	if($.browser.msie){
		var thisRow = tbody.find('tr[depth^=1]:last');
	}else{
		var thisRow = tbody.find('tr[depth^=1]:last');
	}

	$('.select_gid').each(function(){
		if($(this).attr('checked') == 'checked'){
			var total_rows = tbody.find('tr[depth^=1]').length;
			if(total_rows > 1){
				$(this).parent().parent().remove();
			}else{
				thisRow = $(this).parent().parent();
				thisRow.find('#gid_text').html('');
				thisRow.find('#gid').val('');
				thisRow.find('#gname').html('');
				thisRow.find('#unit').val('');
				thisRow.find('#unit_text').html('');
				thisRow.find('#standard_text').html('');
				thisRow.find('#price').val('');
				thisRow.find('#company_name').html('');
				thisRow.find('#place_name').html('');
				thisRow.find('#section_name').html('');

				thisRow.find('#vdate_text').html('');
				thisRow.find('#expiry_date_text').html('');

				thisRow.find('#gid').attr('name','item_infos[0][gid]');
				thisRow.find('#unit').attr('name','item_infos[0][unit]');
				thisRow.find('#order_cnt').attr('name','item_infos[0][order_cnt]');
				thisRow.find('#price').attr('name','item_infos[0][price]');
				thisRow.find('#amount').attr('name','item_infos[0][amount]');
				thisRow.find('#standard').attr('name','item_infos[0][standard]');
				thisRow.find('#pi_ix').attr('name','item_infos[0][pi_ix]');
				thisRow.find('#ps_ix').attr('name','item_infos[0][ps_ix]');

				thisRow.find('#vdate').attr('name','item_infos[0][vdate]');
				thisRow.find('#expiry_date').attr('name','item_infos[0][expiry_date]');

			}
		}
	});
}


Submit_bool=true;
function registerSubmit(frm) {
	if(!Submit_bool){
		return false;
	}
	if(!CheckFormValue(frm)){
		return false;
	}
}

$(document).ready(function(){

	$('#barcode').keypress(function(e){
		if(e.keyCode==13){
			BarcodeGoodsSelect($(this));
		}
	})

});

function BarcodeGoodsSelect(obj){
	$.ajax({
		type: 'GET',
		data: {'act': 'get_goods_barcode', 'company_id':$('#company_id').val(), 'barcode':obj.val()},
		url: './purchase.act.php',
		dataType: 'json',
		error: function(x, o, e){
			 alert(x.status + ' : '+ o +' : '+e);
		},
		success: function(data){
			if(data.gid!=null){
				barcodeBool = true;
				GoodsSelect(data.gid,data.gname,data.unit,data.unit_text,data.standard,data.buying_price,'','','','','','','',data.stock,'','');
				barcodeBool = false;
			}else{
				alert('검색된 품목이 없습니다.');
			}
		}
	});

	obj.val('');
}

var barcodeBool = false;
function GoodsSelect(gid, gname, unit,unit_text, standard, price, company_name, place_name, section_name, pi_ix, ps_ix, vdate, expiry_date,stock, wholesale_price, sellprice){
   var tbody = $('#regist_item_list tbody');
   var total_rows = tbody.find('tr[depth^=1]').length;
   var rows = tbody.find('tr[depth^=1]').length;
	
	
	if($('input[id=gid][value=\"'+gid+'\"]').length > 0 ){
		if(barcodeBool){
			var thisAmountObj = $('input[id=gid][value=\"'+gid+'\"]').closest('tr').find('#amount');

			var amount = parseInt(thisAmountObj.val());
			thisAmountObj.val( amount + 1 );
		}
	}else{
		if($.browser.msie){
			var thisRow = tbody.find('tr[depth^=1]:last');
		}else{
			var thisRow = tbody.find('tr[depth^=1]:last');
		}

		if(thisRow.find('#gid_text').html() == ''){

			thisRow.find('#gid_text').html(gid);
			thisRow.find('#gid').val(gid);
			thisRow.find('#gname').html(gname);
			thisRow.find('#unit').val(unit);
			thisRow.find('#unit_text').html(unit_text);
			thisRow.find('#pi_ix').val(pi_ix);
			thisRow.find('#ps_ix').val(ps_ix);

			thisRow.find('#standard_text').html(standard);
			thisRow.find('#price').val(price);
			thisRow.find('#company_name').html(company_name);
			thisRow.find('#place_name').html(place_name);
			thisRow.find('#section_name').html(section_name);

			thisRow.find('#expiry_date').val(expiry_date);
			thisRow.find('#expiry_date_text').html(expiry_date);

			thisRow.find('#vdate').val(vdate);
			thisRow.find('#vdate_text').html(vdate);
			thisRow.find('#stock').html(stock);
			thisRow.find('#wholesale_price').html(wholesale_price);
			thisRow.find('#sellprice').html(sellprice);

			thisRow.find('#amount').val('1');

			/*
			$.ajax({
				type: 'GET',
				data: {'act': 'get_goodsinfo', 'gid':gid,'unit':unit},
				url: './warehouse_move.act.php',
				dataType: 'html',
				async: true,
				beforeSend: function(){

				},
				success: function(data){

					thisRow.find('#gid_text').html(data.gid);
					thisRow.find('#gid').val(data.gid);
					thisRow.find('#gname').html(data.gname);
					thisRow.find('#unit').val(data.unit);
					thisRow.find('#unit_text').html(data.unit_text);
					thisRow.find('#standard_text').html(data.standard);
					thisRow.find('#price').val(data.price);
					thisRow.find('#company_name').html(data.company_name);
					thisRow.find('#place_name').html(data.place_name);
					thisRow.find('#section_name').html(data.section_name);

				}
			});
			*/


		}else{
			if($.browser.msie){
			  var newRow = tbody.find('tr[depth^=1]:first').clone(true).appendTo(tbody);
			}else{
			  var newRow = tbody.find('tr[depth^=1]:first').clone(true).appendTo(tbody);
			}

			newRow.find('#gid').attr('name','item_infos['+(total_rows)+'][gid]');
			newRow.find('#unit').attr('name','item_infos['+(total_rows)+'][unit]');
			newRow.find('#order_cnt').attr('name','item_infos['+(total_rows)+'][order_cnt]');
			newRow.find('#price').attr('name','item_infos['+(total_rows)+'][price]');
			newRow.find('#amount').attr('name','item_infos['+(total_rows)+'][amount]');
			newRow.find('#delivery_cnt').attr('name','item_infos['+(total_rows)+'][delivery_cnt]');
			newRow.find('#entering_cnt').attr('name','item_infos['+(total_rows)+'][entering_cnt]');

			newRow.find('#standard').attr('name','item_infos['+(total_rows)+'][standard]');
			newRow.find('#pi_ix').attr('name','item_infos['+(total_rows)+'][pi_ix]');
			newRow.find('#ps_ix').attr('name','item_infos['+(total_rows)+'][ps_ix]');
			newRow.find('#vdate').attr('name','item_infos['+(total_rows)+'][vdate]');

			newRow.find('#standard').attr('name','item_infos['+(total_rows)+'][standard]');
			newRow.find('#expiry_date').attr('name','item_infos['+(total_rows)+'][expiry_date]');
			newRow.find('#expiry_date').attr('id','expiry_date_'+total_rows);
			//alert(total_rows);
			newRow.find('#expiry_date_'+total_rows).datepicker('destroy').datepicker({
				//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
				dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
				//showMonthAfterYear:true,
				dateFormat: 'yy-mm-dd',
				buttonImageOnly: true,
				buttonText: '달력'

		   });


			newRow.find('#gid_text').html(gid);
			newRow.find('#gid').val(gid);
			newRow.find('#gname').html(gname);
			newRow.find('#unit').val(unit);
			newRow.find('#unit_text').html(unit_text);
			newRow.find('#pi_ix').val(pi_ix);
			newRow.find('#ps_ix').val(ps_ix);
			newRow.find('#standard_text').html(standard);
			newRow.find('#price').val(price);
			newRow.find('#amount').val('1');
			newRow.find('#delivery_cnt').val('');
			newRow.find('#entering_cnt').val('');

			newRow.find('#company_name').html(company_name);
			newRow.find('#place_name').html(place_name);
			newRow.find('#section_name').html(section_name);

			newRow.find('#expiry_date').val(expiry_date);
			newRow.find('#expiry_date_text').html(expiry_date);

			newRow.find('#vdate').val(vdate);
			newRow.find('#vdate_text').html(vdate);
			newRow.find('#stock').html(stock);

		}
	}

		var stock_sum = 0;
	$('.stock').each(function(){
		stock_sum += parseInt($(this).html());
	});
	$('#stock_sum').html(stock_sum);

	/*
	var amount_sum = 0;
	$('.amount').each(function(){
		amount_sum += parseInt($(this).val());
	});
	$('#amount_sum').html(amount_sum);

	var delivery_cnt_sum = 0;
	$('.delivery_cnt').each(function(){
		delivery_cnt_sum += parseInt($(this).val());
	});
	$('#delivery_cnt_sum').html(delivery_cnt_sum);

	var entering_cnt_sum = 0;
	$('.entering_cnt').each(function(){
		entering_cnt_sum += parseInt($(this).val());
	});
	$('#entering_cnt_sum').html(entering_cnt_sum);
	*/
}

function changeMoveStatus(obj){
	if(obj.value == 'MO'){
		$('.amount').each(function(){
			$(this).attr('readonly','true');
			$(this).css('border','0px');
		});

		$('.delivery_cnt').each(function(){
			$(this).attr('validation','true');
			$(this).attr('readonly',false);
		});
	}else if(obj.value == 'MA'){
		$('.amount').each(function(){
			$(this).attr('readonly','false');
			$(this).css('border','1px solid silver');
		});

		$('.delivery_cnt').each(function(){
			$(this).attr('validation','false');
			$(this).attr('readonly','true');
		});
	}else if(obj.value == 'MI'){
		$('.delivery_cnt').each(function(){
			$(this).attr('readonly','true');
			$(this).css('border','0px');
		});
	}
}



function PopGoodsSelect(){
	var page_type = '".$page_type."';
	//alert($('select[name^=regist_company_id]').val());
	if($('select#h_type option:selected').val() == ''){
		alert('창고이동 유형을 선택후 상품등록을 진행하실 수 있습니다. ');
	}else{
		if(page_type == 'stocked'){
			ShowModalWindow('goods_select.php?page_type=".$page_type."&type_div=".$type_div."&stock_company_id='+$('select[name^=regist_company_id]').val(),1000,800,'goods_select');
		}else{
			if($('select[name^=regist_company_id]').val() == ''){
				alert(' 창고 사업장을 선택후 상품등록을 진행하실 수 있습니다. ');
			}else if($('select[name^=regist_pi_ix]').val() == ''){
				alert(' 창고를 선택후 상품등록을 진행하실 수 있습니다. ');
			}else if($('select[name^=regist_ps_ix]').val() == ''){
				alert(' 보관장소를 선택후 상품등록을 진행하실 수 있습니다. ');
			}else{
				if($('select[name^=regist_ps_ix]').length > 0){
					var str_url = '&ps_ix='+$('select[name^=regist_ps_ix]').val();
				}else{
					var str_url = '';
				}
				ShowModalWindow('goods_select.php?page_type=".$page_type."&type_div=".$type_div."&company_id='+$('select[name^=regist_company_id]').val()+'&pi_ix='+$('select[name^=regist_pi_ix]').val()+str_url,1000,800,'goods_select');
			}
		}

	}
}

</Script>
";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->strLeftMenu = inventory_menu();
	$P->addScript = $Script;
	$P->Navigation = "재고관리 > ".$sub_title."(사입)요청관리 > $title_str 작성 ";
	$P->NaviTitle = "$title_str 작성 ";
	$P->strContents = $Contents;
	$P->jquery_use = false;

	$P->PrintLayOut();
}else{
	$P = new LayOut;
	$P->addScript = $Script;
	$P->OnloadFunction = "";//MenuHidden(false);
	$P->prototype_use = false;
	$P->jquery_use = true;
	$P->strLeftMenu = inventory_menu();
	$P->strContents = $Contents;
	$P->Navigation = "재고관리 > ".$sub_title."(사입)요청관리 > $title_str 작성";
	$P->title = "$title_str 작성";
	$P->PrintLayOut();
}
function getCompanyCartAdmin($company_id,$delivery_company, $cart_key){
	global $user;
	$where = " cart_key = '$cart_key'";
	if($delivery_company == "MI"){
		$delivery_company_where = " and (c.delivery_company ='MI' or c.delivery_company = '') ";
	}else{
		$delivery_company_where = " and c.delivery_company = '$delivery_company' ";
	}
	$mdb = new Database;
	$admin_delievery_policy = getTopDeliveryPolicy($mdb);

	$sql = "select c.*,
			p.delivery_package,
			if(p.delivery_policy =1,
				(select if(delivery_policy = 1,'".$admin_delievery_policy[delivery_price]."',delivery_price) from ".TBL_COMMON_SELLER_DELIVERY." where company_id = '$company_id' )
			,delivery_price) as delivery_price,
			(select if(delivery_policy = 1,'".$admin_delievery_policy[delivery_basic_policy]."',delivery_basic_policy) from ".TBL_COMMON_SELLER_DELIVERY." where company_id = '".$company_id."') as delivery_basic_policy
			from shop_cart c,shop_product p
			where $where and c.id = p.id and company_id = '".$company_id."'
			and c.delivery_company='$delivery_company'
			order by c.regdate desc ";//정렬이 delivery_price 인 것을 regdate 로 바꿈 kbk 11.10.10

	$mdb->query($sql);
	return $mdb->fetchall();
}
function giftRelation($total_price){
	global $db;

	$sql = "select * from shop_product where $total_price >= startprice and $total_price < endprice and product_type = '6' limit 4";
	$db->query($sql);

	$gift_product = $db->fetchall();

	return $gift_product;
}

function getScName($sc_code){
	global $db;

	$db->query("select sc_nm from shop_comm_sc where sc_code = '$sc_code' ");
	$db->fetch();

	return $db->dt[sc_nm];
}


/*


CREATE TABLE IF NOT EXISTS `inventory_history` (
  `h_ix` int(10) NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `h_div` enum('1','2') DEFAULT '1' COMMENT '입출고구분(1:입고, 2:출고)',
  `vdate` varchar(8) DEFAULT NULL COMMENT '입출고날짜',
  `customer_name` varchar(50)  NOT NULL COMMENT '거래처명',
  `com_name` VARCHAR( 100) NOT NULL COMMENT '사업장명',
  `place_name` varchar(100) DEFAULT NULL COMMENT '창고명',
  `section_name` varchar(100) unsigned NOT NULL COMMENT '보관장소명',
  `company_id` VARCHAR( 32) NOT NULL COMMENT '사업장코드' ,
  `ci_ix` int(10) NOT NULL COMMENT '거래처코드',
  `pi_ix` int(6) DEFAULT NULL COMMENT '창고코드',
  `ps_ix` int(8) unsigned NOT NULL COMMENT '보관장소코드',
  `oid` varchar(17) DEFAULT NULL COMMENT '주문번호',
  `msg` varchar(255) DEFAULT NULL COMMENT '메세지',
  `h_type` int(10) DEFAULT NULL COMMENT '입출고타입',
  `charger_name` varchar(32) NOT NULL COMMENT '담당자이름',
  `charger_ix` varchar(32) NOT NULL COMMENT '담당자 코드',
  `regdate` datetime NOT NULL COMMENT '등록일자',
  PRIMARY KEY (`h_ix`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='입/출고 마스터'



CREATE TABLE IF NOT EXISTS `inventory_history_detail` (
  `hd_ix` int(10) NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `h_ix` int(10) unsigned DEFAULT NULL COMMENT '히스토리키',
  `gid` int(10) unsigned zerofill DEFAULT NULL COMMENT '품목코드',
  `unit` int(5) DEFAULT NULL COMMENT '단위',
  `gname` varchar(255) NOT NULL COMMENT '품목명',
  `standard` varchar(255) DEFAULT NULL COMMENT '규격',
  `amount` int(10) DEFAULT NULL COMMENT '수량',
  `price` int(10) NOT NULL COMMENT '입출고가격',
  `regdate` datetime NOT NULL COMMENT '등록일자',
  PRIMARY KEY (`hd_ix`),
  KEY `pid` (`gid`,`unit`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='입/출고 상세정보'
*/


?>