<?
include("../class/layout.class");
include("./inventory.lib.php");

$db = new Database;
$db1 = new Database;
$db2 = new Database;
$db3 = new Database;
$odb = new Database;
$tdb = new Database;

//print_r($_SESSION["admininfo"]);

$sql = "select wm.*, pi.company_id as move_company_id, pi.place_name as move_place_name, ps.section_name as move_section_name
		from inventory_warehouse_move wm 
		left join  inventory_place_info pi on wm.move_pi_ix = pi.pi_ix
		left join  inventory_place_section ps on wm.move_ps_ix = ps.ps_ix	
		where wm_ix='$wm_ix' ";

//"SELECT * FROM inventory_warehouse_move wm left join WHERE wm_ix='$wm_ix'"

$db->query($sql);
$db->fetch();

if($db->total){
	$act = "update";
	$move_status = $db->dt[status];
	$move_company_id = $db->dt[move_company_id];
	$now_company_id = $db->dt[now_company_id];
	$charger_name = $db->dt[charger_name];
	$wm_apply_date = $db->dt[wm_apply_date];
	$apply_charger_ix = $db->dt[apply_charger_ix];
	//echo $_SESSION["admininfo"]["charger_ix"].":::".$db->dt[charger_ix];
	if($_SESSION["admininfo"]["charger_ix"] == $db->dt[charger_ix]){
		if($move_status == ""){
			$info_type = "select";
		}else{
			$info_type = "text";
		}
	}else{
		$info_type = "text";
	}
	
	//echo $move_company_id;
}else{
	$act = "insert";
	$info_type = "select";
//	$move_status = "MA";
	$charger_name = $_SESSION["admininfo"]["charger"];
	$wm_apply_date = date("Y-m-d");
	$apply_charger_ix = $_SESSION["admininfo"]["charger_ix"];
	//$now_company_id = $_SESSION["admininfo"]["company_id"];
}

//echo $_SESSION["admininfo"]["charger_name"];

$Contents ="
<form  name='input_frm' method='post' onsubmit=\"return CheckWarehouseMove(this)\" action='./warehouse_move.act.php'   target='act'>
<input type=hidden name=act value='".$act."'>
<input type=hidden name=mmode value='".$mmode."'>
<input type='hidden' name='wm_ix' id='wm_ix' value='".$wm_ix."'>
<input type='hidden' id='code' value=''>
<table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%' valign=top>
<tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("창고이동 요청대장", "입출고관리 > 창고이동 요청대장")."</td>
</tr>

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
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>창고이동 요청내역저장</b>
			</td>
		</tr>
		<tr>
			<td colspan='2' style='padding:0px 0px;'>
				<table cellpadding=0 cellspacing=0 border=0 width=100% align=center class='input_table_box'>
						<col width='15%'>
						<col width='35%'>
						<col width='15%'>
						<col width='35%'>
						<tr height='30'>
							
							<td class='input_box_title' ><b>요청일</b> <img src='".$required3_path."'></td>
							<td class='input_box_item' >
								<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100%>
									<col width=100>
									<col width=*>
									<tr>
										<TD nowrap>";
										if($info_type == "text"){
											$Contents .= "".$wm_apply_date."
											<input type='hidden' name='wm_apply_date' class='textbox' value='".$wm_apply_date."'  validation='true' title='요청일'  >";
										}else{
											$Contents .= "
											<input type='text' class='textbox point_color' name='wm_apply_date' class='textbox' value='".$wm_apply_date."' style='".($info_type == "text" ? "border:0px;":"")."height:20px;width:100px;text-align:center;' id='end_datepicker' validation='true' title='요청일' ".($info_type == "text" ? "readonly ":"").">";
										}
										$Contents .= "
										</TD>
									</tr>
								</table>
							</td>
							<td class='input_box_title' ><b>담당자</b> <img src='".$required3_path."'></td>
							<td class='input_box_item' >
							".CompayChargerSearch($_SESSION["admininfo"]["company_id"] ,$apply_charger_ix,"",$info_type)."
							</td>
						</tr>
						
						<tr height='30'>
							<td class='input_box_title' ><b>작성자</b> <img src='".$required3_path."'></td>
							<td class='input_box_item' >  ".$charger_name." </td>
							<td class='input_box_title'>".$sub_title." 창고이동유형 <img src='".$required3_path."'> </td>
							<td class='input_box_item' >";
		$Contents .= "".selectType('3',"5",$db->dt[h_type],'h_type',$info_type,true,"onchange=\"ChangeType(this.value);\" ").""; // type_div : 5 인건 창고이동

		$Contents .= "
							</td>
							
						</tr>
						<tr>
							<td class='input_box_title'>현재창고 <img src='".$required3_path."'></td>
							<td class='input_box_item' nowrap>
								".SelectEstablishment($now_company_id,"now_company_id",$info_type,"true","  onChange=\"loadPlace(this,'now_pi_ix')\" ")."";
								if($act == "update"){
									$Contents .= "
									".SelectInventoryInfo($now_company_id, $db->dt[now_pi_ix],'now_pi_ix',$info_type,'true', "validation=true title='현재창고' page_type='warehouse_move' onChange=\"loadPlaceSection(this,'now_ps_ix')\"  ")."
									".SelectSectionInfo($db->dt[now_pi_ix],$db->dt[now_ps_ix],'now_ps_ix',$info_type,"true"," title='보관장소' ")."";
								}else{
								$Contents .= "
									".SelectInventoryInfo($now_company_id, $db->dt[now_pi_ix],'now_pi_ix',$info_type,'true', "validation=true title='현재창고' page_type='warehouse_move' ")."";
								}
		$Contents .= "
							</td>
							<td class='input_box_title'>이동창고 <img src='".$required3_path."'></td>
							<td class='input_box_item' nowrap>								
								".SelectEstablishment($move_company_id,"move_company_id",$info_type,"true","onChange=\"MoveloadPlace(this,'move_pi_ix')\" ")."
								".SelectInventoryInfo($move_company_id, $db->dt[move_pi_ix],'move_pi_ix',$info_type,'true', "validation=true title='이동창고' page_type='warehouse_move'  onChange=\"MoveloadPlaceSection(this,'move_ps_ix')\"  ")."
								".SelectSectionInfo($db->dt[move_pi_ix],$db->dt[move_ps_ix],'move_ps_ix',$info_type,"true"," title='보관장소' ")." 
							</td>
						</tr>
						<tr height='30' id='move_status_tr' style='display:none;'>
							<td class='input_box_title'>창고이동 처리상태 <img src='".$required3_path."'></td>
							<td class='input_box_item' ><input type=hidden name=bstatus value='".$db->dt[status]."'>
							";
							if($db->dt[status] == "MC"){
								$Contents .= "이동완료";
							}else{
								$Contents .= 
								getInventoryStatus($move_status, "status"," onchange='changeMoveStatus(this);' style='min-width:140px;'");
							}
							$Contents .= "
							</td>		
							<td class='input_box_title' ><!--b>결제라인</b--></td>
							<td class='input_box_item' >
								<!--".makeSelectBoxAuthorizationLine($db->dt[al_ix], "al_ix",$reg_al_ix)."-->
							</td>
						</tr>
						<tr height='30'>
							<td class='input_box_title' ><b>비고</b></td>
							<td class='input_box_item'  colspan=3>";
										if($info_type == "text"){
											$Contents .= "".$db->dt[etc]."<input type=hidden class='textbox' name='etc' value='".$db->dt[etc]." ' id=order_etc style='width:90%'>";
										}else{
											$Contents .= "
											<input type=text class='textbox' name='etc' value='".$db->dt[etc]." ' id=order_etc style='width:90%'>";
										}
										$Contents .= "
							
							</td>
						</tr>
				</table>";

$Contents .= "

			</td>
		</tr>

		
	</table>
	</td>
</tr>
<tr>
	<td  height='25' style='padding:10px 0px;'>
		<span class='red'>* 요청일 > 담당자 >  창고이동유형 >  현재창고 > 이동창고 순으로 입력을 해주셔야 합니다.</span>
	</td>
	<td align=right>

	</td>
</tr>
<tr>
	<td  height='25' style='padding:10px 0px;'>
		<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>창고이동 요청품목</b>
	</td>
	<td align=right>";
	if($move_status == "" || $move_status == "MA"){
		$Contents .= "<a href=\"javascript:PopGoodsSelect();\"><img src='../images/".$admininfo["language"]."/btc_goods_add.gif' border='0' align='absmiddle'  style='cursor:pointer;'></a>";
	}
	$Contents .= "
	</td>
</tr>
<tr>
	<td colspan='2' height=300 style='vertical-align:top;'>
	";

$Contents .= "
	<table cellpadding=3 cellspacing=0 border=0 bgcolor=#ffffff width=100% onselect='return false;' align=center class='list_table_box' id='warehouse_move_apply_list'>
				<!--col width=4% >
				<col width=6% >
				<col width='*' >
				<col width=6% >
				<col width=6% >
				<col width=6% >
				<col width=6% >
				<col width=8% >
				<col width=6% >
				<col width=6% >
				<col width=6% >
				<col width=6% >
				<col width=6% >
				<col width=7% >
				<col width=7% -->
				<tr align=center height=30 style='font-weight:bold;' >";
	if($move_status == "" || $move_status == "MA"){
		$Contents .= "
					<td class=s_td rowspan=2><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.input_frm)'></td>";
	}
	
		$Contents .= "
					<td class=m_td rowspan=2>대표코드</td>
					<td class=m_td rowspan=2>품목코드</td>
					<td class=m_td rowspan=2>품목정보</td>
					<td class=m_td rowspan=2>단위</td>
					<td class=m_td rowspan=2>규격</td>
					<!--td class=m_td rowspan=2 nowrap>입고일</td-->
					<td class=m_td rowspan=2 nowrap>유통기한</td>
					<td class=m_td colspan=3>현재 사업장/창고</td>
					<td class=m_td rowspan=2>재고</td>
					<td class=m_td rowspan=2 nowrap>요청수량</td>
					<td class='m_td inner_warehouse_move' rowspan=2 nowrap>출고수량</td>
					<td class='m_td inner_warehouse_move' rowspan=2 nowrap>입고수량</td>
					<td class=m_td width='70px;' rowspan=2  nowrap>단가</td>
					<td class=e_td  width='70px;' rowspan=2 nowrap>합계</td>
				</tr>
				<tr align=center height=30>
					<td class=m_td>사업장</td>
					<td class=m_td>창고</td>
					<td class=m_td>보관장소</td>	
				</tr>";
if(false){
$Contents .= "
				<tr bgcolor=#ffffff height=30 >
					<td colspan='9' class=m_td><b><font color='#333333'>총합계</font></b></td>
					<td class=m_td><b id='apply_cnt_sum'><!--apply_cnt_sum--></b></td>
					<td class='m_td inner_warehouse_move'><b id='delivery_cnt_sum'><!--delivery_cnt_sum--></b></td>
					<td class='m_td inner_warehouse_move'><b id='entering_cnt_sum'><!--entering_cnt_sum--></b></td>
					<td align=center class=m_td colspan='2'><b> <font class='blk' style='font-size:12px;font-family:arial;'>".number_format($order_totalprice)." </font></b><font class='blk'> 원</font></td>
				</tr>";

}
/*
$sql = "select g.cid,g.gname, g.gcode, g.admin, g.input_price, g.basic_sellprice , g.ci_ix, g.pi_ix, pi.place_name,  ifnull(sum(ips.stock),0) as stock, gi.* 
		from inventory_goods g 
		right join inventory_goods_item gi  on g.gid =gi.gid
		left join  inventory_place_info pi on g.pi_ix = pi.pi_ix
		left join  inventory_product_stockinfo ips on gi.gid = ips.gid
		$where    
		 $stock_where 
		 group by gi.gid , gi.gi_ix, ips.pi_ix
		 $orderbyString 
		 LIMIT $start, $max
		 ";
*/

	$sql = "select data.*, 
		(select com_name as company_name from common_company_detail ccd where ccd.company_id = data.company_id   limit 1) as company_name , 
		(select place_name as place_name from inventory_place_info pi where pi.pi_ix = data.move_pi_ix   limit 1) as move_place_name , 
		(select section_name as section_name from inventory_place_section ps  where ps.ps_ix = data.move_ps_ix   limit 1) as move_section_name  
		from 
			(select g.cid,g.gname, g.gcode, g.admin, gu.buying_price, gu.sellprice ,  gu.avg_price,  g.ci_ix, g.pi_ix, pi.place_name, ps.section_name, wm.wm_ix, wm.wm_apply_date, wm.move_pi_ix , wm.move_ps_ix , wmd.apply_cnt , wmd.unit, wmd.standard, pi.company_id
		from inventory_warehouse_move wm 
		left join inventory_warehouse_move_detail wmd on wm.wm_ix = wmd.wm_ix
		left join inventory_goods g on wmd.gid = g.gid 
		right join inventory_goods_unit gu  on g.gid =gu.gid
		left join  inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit		
		left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix
		left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix		
		$where    
		 $stock_where 
		 group by g.gid , gu.unit, ips.pi_ix
		 $orderbyString 
		 LIMIT $start, $max
		 ) data
		 ";

	$sql = "select 
				data.*, 
				(select 
						com_name as company_name 
					from 
						common_company_detail ccd
					where 
						ccd.company_id = data.company_id   limit 1) as company_name
				from 
					(select 
						g.cid,g.gname, 
						g.gid,
						g.gcode,
						g.admin,
						gu.buying_price, 
						gu.sellprice , 
						gu.avg_price,
						g.ci_ix,
						wmd.pi_ix, 
						wmd.ps_ix, 
						pi.place_name, 
						ps.section_name, wm.wm_ix, wmd.wmd_ix, wm.wm_apply_date, wm.move_pi_ix , wm.move_ps_ix , wmd.apply_cnt , wmd.delivery_cnt, wmd.entering_cnt, wmd.unit, wmd.standard, pi.company_id, ips.vdate, ips.expiry_date, 
						sum(ips.stock) as stock
					from inventory_warehouse_move wm 
						left join inventory_warehouse_move_detail wmd on wm.wm_ix = wmd.wm_ix
						left join inventory_goods g on wmd.gid = g.gid 
						right join inventory_goods_unit gu  on g.gid =gu.gid and wmd.unit = gu.unit 
						right join  inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit		
						right join  inventory_place_info pi on ips.company_id = pi.company_id and ips.pi_ix = pi.pi_ix 
						left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
					where wm.wm_ix = '".$wm_ix."'
						group by g.gid , gu.unit, ips.pi_ix ) data
				 ";
		
/*
	$sql = "select data.*
		from 
			(select wmd.* 
			from inventory_warehouse_move wm 
			left join inventory_warehouse_move_detail wmd on wm.wm_ix = wmd.wm_ix
			left join inventory_goods g on wmd.gid = g.gid 
			right join inventory_goods_unit gu  on g.gid =gu.gid and wmd.unit = gu.unit 
			left join  inventory_product_stockinfo ips on wmd.gid = ips.gid and wmd.unit = ips.unit		
			
			where wm.wm_ix = '".$wm_ix."'
		 ) data
		 ";
*/
//echo nl2br($sql);

$db->query($sql);
$order_goods_total = $db->total;
//echo $order_goods_total;

if($move_status == "" || $move_status == "MA"){
	$goods_select_str = " ondblclick=\"javascript:PopGoodsSelect();\"  class='helpcloud' help_height='35' help_html='더블클릭시 품목을 선택할 수 있는 창이 노출되게 됩니다.' style='cursor:pointer;'  ";
}else{
	$goods_select_str = "";
}


if($db->total){

	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);

			$ci_ix = $db->dt[ci_ix];
			$gid = $db->dt[gid];
			$gname = $db->dt[gname];
			$gname_str .= $db->dt[gname];
			$order_cnt    = $db->dt[order_cnt];
			$buying_price = $db->dt[buying_price];
			$avg_price = $db->dt[avg_price];

			$place_name = $db->dt[place_name];
			$section_name = $db->dt[section_name];
			$company_name = $db->dt[company_name];
			$apply_cnt = $db->dt[apply_cnt];
			$delivery_cnt = $db->dt[delivery_cnt];
			$entering_cnt  = $db->dt[entering_cnt];
			$unit = $db->dt[unit];
			$stock = $db->dt[stock];
			$wmd_ix = $db->dt[wmd_ix];

			$apply_cnt_sum += $apply_cnt;
			$delivery_cnt_sum += $delivery_cnt;
			$entering_cnt_sum += $entering_cnt;
			
			
			
			$order_totalprice = $order_totalprice + $totalprice;
			//$coper = $coprice / $sellprice * 100;

			//$db->query("SELECT listprice, sellprice,  admin,company, state FROM ".TBL_SHOP_PRODUCT." WHERE id='$pid'");
			//$db->fetch();


			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $gid, "c"))) {
				$img_str = InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $gid, "c");
			}else{
				$img_str = "../image/no_img.gif";
			}
//echo $move_status;
			if($db->dt[delivery_cnt] == ""){
				$delivery_cnt = $db->dt[apply_cnt];
			}else{
				$delivery_cnt = $db->dt[delivery_cnt];
			}

			if($db->dt[entering_cnt] == ""){
				$entering_cnt = $db->dt[delivery_cnt];
			}else{
				$entering_cnt = $db->dt[entering_cnt];
			}

			if($move_status == "MC"){
				$totalprice = $delivery_cnt*$avg_price;
			}else{
				$totalprice = $apply_cnt*$avg_price;
			}

$Contents .="
				<tr height=30 depth=1  >";
	if($move_status == "" || $move_status == "MA"){
		$Contents .= "
					<td align=center>
						<input type=checkbox class='nonborder select_gid' id='select_gid'  name=warehouse_moveinfo[".$i."][select_gid] value='".$db->dt[gid]."'>
					</td>";
	}
		$Contents .= "
					<td align=center id='gcode_text'>".$db->dt[gcode]."</td>
					<td align=center id='gid_text'>".$db->dt[gid]." <input type=hidden class='nonborder' id='wmd_ix'  name=warehouse_moveinfo[".$i."][wmd_ix] value='".$db->dt[wmd_ix]."'> </td>
					<td style='padding:3px;' nowrap>
						<b id='gname'>$gname </b><input type=hidden class='textbox numeric gid' name='warehouse_moveinfo[".$i."][gid]' id='gid' value='$gid'>
					</td>
					<td align=center><span  id='unit_text'>".getUnit($db->dt[unit], "basic_unit","","text")."</span><input type=hidden class='textbox numeric' name='warehouse_moveinfo[".$i."][unit]' id='unit' value='$unit'> </td>
					<td align=center><span  id='standard_text'>".$db->dt[standard]."</span><input type=hidden class='textbox numeric' name='warehouse_moveinfo[".$i."][standard]' id='standard' value='$standard'> </td>					
					
					<!--td align=center><span  id='vdate_text'>".$db->dt[vdate]."</span><input type=hidden class='textbox numeric' name='warehouse_moveinfo[".$i."][vdate]' id='vdate' value='".$db->dt[vdate]."'> </td-->
					<td align=center><span  id='expiry_date_text'>".$db->dt[expiry_date]."</span><input type=hidden class='textbox numeric' name='warehouse_moveinfo[".$i."][expiry_date]' id='expiry_date' value='".$db->dt[expiry_date]."'> </td>
					<td align=center ><span id='company_name'>".$company_name."</span><!--input type=hidden  name='warehouse_moveinfo[".$i."][company_id]' id='company_id' value='$company_id'--></td>
					<td align=center ><span id='place_name'>".$place_name."</span><input type=hidden  name='warehouse_moveinfo[".$i."][pi_ix]' size=2 id='pi_ix' value='".$db->dt[pi_ix]."'></td>
					<td align=center ><span id='section_name'>".$section_name."</span><input type=hidden  name='warehouse_moveinfo[".$i."][ps_ix]' size=2 id='ps_ix' value='".$db->dt[ps_ix]."'></td>
					<td style='text-align:center;'>
						<span class='stock'  id='stock'>".$stock."</span>
					</td>
					<td style='text-align:center;' ".($move_status == "MA" ? "class='point'":"").">
						<input type=text class='textbox numeric apply_cnt' name='warehouse_moveinfo[".$i."][apply_cnt]'  id='apply_cnt' value='".$db->dt[apply_cnt]."' size=8 title='요청수량'  ".(($move_status == "" || $move_status == "MA") ? " validation=true style='width:80%;text-align:right;padding:0 5px 0 0'":"readonly style='border:0px;width:80%;text-align:right;padding:0 5px 0 0'  ")." > 
					</td>
					<td class='inner_warehouse_move ".($move_status == "MO" ? "point":"")."' style='text-align:center;'>
						<input type=text class='textbox numeric delivery_cnt' name='warehouse_moveinfo[".$i."][delivery_cnt]'  id='delivery_cnt' value='".$delivery_cnt."' apply_cnt='".$db->dt[apply_cnt]."'  size=8 title='출고수량'  ".(($move_status == "MO" || $move_status == "MA") ? " validation=true style='width:80%;text-align:right;padding:0 5px 0 0'":"style='border:0px;width:80%;text-align:right;padding:0 5px 0 0' readonly ")."> 
					</td>
					<td class='inner_warehouse_move ".($move_status == "MI" ? "point":"")."' style='text-align:center;'>
						<input type=text class='textbox numeric entering_cnt' name='warehouse_moveinfo[".$i."][entering_cnt]'  id='entering_cnt' value='".$entering_cnt."' delivery_cnt='".$db->dt[delivery_cnt]."' size=8 title='입고수량'  ".($move_status == "MI" ? " validation=true style='width:80%;text-align:right;padding:0 5px 0 0'":"style='border:0px;width:80%;text-align:right;padding:0 5px 0 0' readonly ")."  > 
					</td>
					<td align=center id='buying_price'>".$avg_price."</td>
					<td align=center id='total_price'>".number_format($totalprice)."</td>
				</tr>";
	}

	$Contents = str_replace("<!--apply_cnt_sum-->",$apply_cnt_sum,$Contents);
	$Contents = str_replace("<!--delivery_cnt_sum-->",$delivery_cnt_sum,$Contents);
	$Contents = str_replace("<!--entering_cnt_sum-->",$entering_cnt_sum,$Contents);

	
}else{
//$Contents .="<tr height=50><td colspan=7 align=center>창고이동 요청품목 내역이  존재 하지 않습니다.</td></tr>";
$Contents .="
				<tr height=30 depth=1 ".$goods_select_str."   >
					<td align=center><input type=checkbox class='nonborder select_gid' id='select_gid'  name=warehouse_moveinfo[0][select_gid] value='".$db->dt[gid]."'></td>
					<td align=center id='gcode_text'>".$db->dt[gcode]."</td>
					<td align=center id='gid_text'>".$db->dt[gid]."</td>
					<td style='padding:3px;' nowrap>
						<b id='gname'>$gname </b><input type=hidden class='textbox numeric gid' name='warehouse_moveinfo[0][gid]' id='gid' value='$gid'>
					</td>
					<td align=center><span  id='unit_text'>".$db->dt[unit_text]."</span><input type=hidden class='textbox numeric' name='warehouse_moveinfo[0][unit]' id='unit' value='$unit'> </td>
					<td align=center><span  id='standard_text'>".$db->dt[standard]."</span><input type=hidden class='textbox numeric' name='warehouse_moveinfo[0][standard]' id='standard' value='$standard'> </td>
					<!--td align=center><span  id='vdate_text'>".$db->dt[vdate]."</span><input type=hidden class='textbox numeric' name='warehouse_moveinfo[0][vdate]' id='vdate' value='$vdate'> </td-->
					<td align=center><span  id='expiry_date_text'>".$db->dt[expiry_date]."</span><input type=hidden class='textbox numeric' name='warehouse_moveinfo[0][expiry_date]' id='expiry_date' value='$expiry_date'> </td>
					<td align=center ><span id='company_name'>".$company_name."</span><!--input type=hidden  name='warehouse_moveinfo[0][company_id]' id='company_id' value='$company_id'--></td>
					<td align=center ><span id='place_name'>".$place_name."</span><input type=hidden  name='warehouse_moveinfo[0][pi_ix]' size=2 id='pi_ix' value='$pi_ix'></td>
					<td align=center ><span id='section_name'>".$section_name."</span><input type=hidden  name='warehouse_moveinfo[0][ps_ix]' size=2 id='ps_ix' value='$ps_ix'></td>
					<td style='text-align:center;'>
						<span class='stock' id='stock'>".$stock."</span>
					</td>
					<td style='text-align:center;' class='point'>
						<input type=text class='textbox numeric apply_cnt' name='warehouse_moveinfo[0][apply_cnt]'  id='apply_cnt' value='$apply_cnt' size=8 title='요청수량' ".(($move_status == "" || $move_status == "MA") ? " validation=true  style='width:80%;text-align:right;padding:0 5px 0 0'":"style='border:0px;width:80%;text-align:right;padding:0 5px 0 0'  ")." > 
					</td>
					<td class='inner_warehouse_move' style='text-align:center;'>
						<input type=text class='textbox numeric delivery_cnt' name='warehouse_moveinfo[0][delivery_cnt]'  id='delivery_cnt' value='$delivery_cnt' size=8 title='출고수량'  ".($move_status == "MO" ? " validation=true style='width:80%;text-align:right;padding:0 5px 0 0'":"style='border:0px;width:80%;text-align:right;padding:0 5px 0 0' readonly ")."  > 
					</td>
					<td class='inner_warehouse_move' style='text-align:center;'>
						<input type=text class='textbox numeric entering_cnt' name='warehouse_moveinfo[0][entering_cnt]'  id='entering_cnt' value='$entering_cnt' size=8 title='입고수량'  ".($move_status == "MI" ? " validation=true style='width:80%;text-align:right;padding:0 5px 0 0'":"style='border:0px;width:80%;text-align:right;padding:0 5px 0 0' readonly ")."  > 
					</td>
					<td align=center id='buying_price'>".$buying_price."</td>
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
								<td>";
								if($move_status == "" || $move_status == "MA"){
									$Contents .="<img src='../images/".$admininfo["language"]."/btc_select_goods_delete.gif' border='0' align='absmiddle' onclick='checkDelete()' style='cursor:pointer;'>";
								}
								$Contents .="
								</td>
							</tr>
						</table>
					</td>
					<td colspan=4>
						<table cellpadding=10 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center>
							<tr>
								<td align='right' style='font-size:12px;font-weight:bold;'>
								<!--품목금액  : <span class='blk' style='font-size:12px;font-family:arial;'>".number_format($order_totalprice)."</span> 원 +
								배송비 : <span class='blk' style='font-size:12px;font-family:arial;'>".number_format($total_delivery_price)."</span> 원 =
								총 주문금액 : <span class='blk' style='font-size:12px;font-family:arial;'>".number_format($order_totalprice + $total_delivery_price)."</span> 원 --></td>
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
		
	if($move_status == "MC"){
		
		$Contents .= " <input type=image src='../images/".$admininfo["language"]."/b_save.gif' onclick='javascript:alert(\"이미 처리완료 되엇습니다.\");return false;' border=0 align=absmiddle style='cursor:pointer;'>";
	}else{
		$Contents .= " <input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer;'>";
	}
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
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td >창고이동 요청대장은 이동창고가 다를 경우 별도로 작성을 하셔야 합니다..</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td >이동하고자 하는 정보와 품목의 수량정보를 입력하신후 저장버튼을 눌러 요청대장의 작성을 완료 하실 수 있습니다. </td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td ><u>내부창고</u> 이동의 경우 자동으로 이동출고 와 이동입고에 대한 기록이 남으며 재고정보도 즉시 이동되게 됩니다.</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td ><u>외부창고</u> 이동의 경우 보관장소는 자동으로 현재창고의 경우 출고 보관장소 가 이동창고의 경우는 입고 보관장소가 자동 선택되게 됩니다. </td></tr>
</table>
";



$Contents .= HelpBox("창고 이동요청", $help_text,"100");

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


});

function select_date(FromDate,ToDate,dType) {
	var frm = document.searchmember;

	$(\"#priod\").val(FromDate);
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
   var tbody = $('#warehouse_move_apply_list tbody');  	
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
				
				thisRow.find('#gcode_text').html('');
				thisRow.find('#gid_text').html('');   
				thisRow.find('#gid').val('');
				thisRow.find('#gname').html('');
				thisRow.find('#unit').val('');
				thisRow.find('#unit_text').html('');
				thisRow.find('#standard_text').html('');
				thisRow.find('#buying_price').html('');
				thisRow.find('#company_name').html('');
				thisRow.find('#place_name').html('');
				thisRow.find('#section_name').html('');

				thisRow.find('#vdate_text').html('');
				thisRow.find('#expiry_date_text').html('');

				thisRow.find('#gid').attr('name','warehouse_moveinfo[0][gid]');
				thisRow.find('#unit').attr('name','warehouse_moveinfo[0][unit]');
				thisRow.find('#order_cnt').attr('name','warehouse_moveinfo[0][order_cnt]');
				thisRow.find('#buying_price').attr('name','warehouse_moveinfo[0][buying_price]');
				thisRow.find('#apply_cnt').attr('name','warehouse_moveinfo[0][apply_cnt]');
				thisRow.find('#standard').attr('name','warehouse_moveinfo[0][standard]');
				thisRow.find('#pi_ix').attr('name','warehouse_moveinfo[0][pi_ix]');
				thisRow.find('#ps_ix').attr('name','warehouse_moveinfo[0][ps_ix]');

				thisRow.find('#vdate').attr('name','warehouse_moveinfo[0][vdate]');
				thisRow.find('#expiry_date').attr('name','warehouse_moveinfo[0][expiry_date]');
				
				thisRow.find('#stock').html('');
				thisRow.find('#apply_cnt').val('');
				thisRow.find('#total_price').html('');
			}
		}
	});		
}


function GoodsSelect(gid, gname, unit,unit_text, standard, buying_price, company_name, place_name, section_name, pi_ix, ps_ix, vdate, expiry_date,stock,offline_wholesale_price,sellprice,surtax_div,surtax_text,lately_price,change_amount,gcode){
   var tbody = $('#warehouse_move_apply_list tbody');  	
   var total_rows = tbody.find('tr[depth^=1]').length;  
   var rows = tbody.find('tr[depth^=1]').length;  

	if($.browser.msie){
		var thisRow = tbody.find('tr[depth^=1]:last');  
	}else{
		var thisRow = tbody.find('tr[depth^=1]:last');  
	}   


	if(thisRow.find('#gid_text').html() == ''){ 

		thisRow.find('#gcode_text').html(gcode);   
		thisRow.find('#gid_text').html(gid);   
		thisRow.find('#gid').val(gid);
		thisRow.find('#gname').html(gname);
		thisRow.find('#unit').val(unit);
		thisRow.find('#unit_text').html(unit_text);
		thisRow.find('#pi_ix').val(pi_ix);
		thisRow.find('#ps_ix').val(ps_ix);
		
		thisRow.find('#standard_text').html(standard);
		thisRow.find('#buying_price').html(buying_price);
		thisRow.find('#company_name').html(company_name);
		thisRow.find('#place_name').html(place_name);
		thisRow.find('#section_name').html(section_name);

		thisRow.find('#expiry_date').val(expiry_date);
		thisRow.find('#expiry_date_text').html(expiry_date);

		thisRow.find('#vdate').val(vdate);
		thisRow.find('#vdate_text').html(vdate);
		thisRow.find('#stock').html(stock);

		

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
				thisRow.find('#buying_price').html(data.buying_price);
				thisRow.find('#company_name').html(data.company_name);
				thisRow.find('#place_name').html(data.place_name);
				thisRow.find('#section_name').html(data.section_name);
				
			} 
		});
		*/
		
		
	}else{

		//20131022 Hong 같은 품목 등록 안되도록 처리
		if($('input.gid[value='+gid+']').length==0){

			var newRow = tbody.find('tr[depth^=1]:last').clone(true).appendTo(tbody);
			
			newRow.find('#gcode_text').html(gcode);
			newRow.find('#gid').attr('name','warehouse_moveinfo['+(total_rows)+'][gid]');
			newRow.find('#unit').attr('name','warehouse_moveinfo['+(total_rows)+'][unit]');
			newRow.find('#order_cnt').attr('name','warehouse_moveinfo['+(total_rows)+'][order_cnt]');
			newRow.find('#buying_price').attr('name','warehouse_moveinfo['+(total_rows)+'][buying_price]');
			newRow.find('#apply_cnt').attr('name','warehouse_moveinfo['+(total_rows)+'][apply_cnt]');
			newRow.find('#delivery_cnt').attr('name','warehouse_moveinfo['+(total_rows)+'][delivery_cnt]');
			newRow.find('#entering_cnt').attr('name','warehouse_moveinfo['+(total_rows)+'][entering_cnt]');

			newRow.find('#standard').attr('name','warehouse_moveinfo['+(total_rows)+'][standard]');
			newRow.find('#pi_ix').attr('name','warehouse_moveinfo['+(total_rows)+'][pi_ix]');
			newRow.find('#ps_ix').attr('name','warehouse_moveinfo['+(total_rows)+'][ps_ix]');
			newRow.find('#vdate').attr('name','warehouse_moveinfo['+(total_rows)+'][vdate]');

			newRow.find('#standard').attr('name','warehouse_moveinfo['+(total_rows)+'][standard]');
			newRow.find('#expiry_date').attr('name','warehouse_moveinfo['+(total_rows)+'][expiry_date]');

			newRow.find('#gid_text').html(gid);   
			newRow.find('#gid').val(gid);
			newRow.find('#gname').html(gname);
			newRow.find('#unit').val(unit);
			newRow.find('#unit_text').html(unit_text);
			newRow.find('#pi_ix').val(pi_ix);
			newRow.find('#ps_ix').val(ps_ix);
			newRow.find('#standard_text').html(standard);
			newRow.find('#buying_price').html(buying_price);
			newRow.find('#apply_cnt').val('');
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
	
	/*
	var apply_cnt_sum = 0;
	$('.apply_cnt').each(function(){
		apply_cnt_sum += parseInt($(this).val());
	});
	$('#apply_cnt_sum').html(apply_cnt_sum);

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


$(document).ready(function() {
	if($('#h_type').val() == 'IW'){
		$('#move_company_id').attr('disabled',true);
		$('.inner_warehouse_move').css('display','none');
		$('#move_status_tr').css('display','none');
	}else{
		$('#move_status_tr').css('display','');
		$('.inner_warehouse_move').css('display','');
	}

	$('#now_company_id').change(function(){
		if($('select#h_type option:selected').val() == 'IW'){
			$('#now_pi_ix').change();
			$('#move_company_id').val($('#now_company_id').val()).change();
			$('#move_pi_ix').change();
			
			//loadPlace($('#move_company_id option:selected'),'move_pi_ix');
		}else if($('select#h_type option:selected').val() == ''){
			$('#now_company_id').val('');			
			alert('창고이동 유형을 먼저 선택해주세요');
			$('#now_pi_ix').change();
		}
	});

	$('.apply_cnt').keyup(function(){
		check_total_price($(this));
	});

});

function check_total_price(obj){

	var obj_tr = obj.closest('tr');
	var total_price = parseInt(obj.val()) * parseInt(obj_tr.find('#buying_price').text());
	obj_tr.find('#total_price').html(FormatNumber(total_price));

}

function changeMoveStatus(obj){
	if(obj.value == 'MO'){
		$('.apply_cnt').each(function(){
			$(this).attr('readonly','true');
			$(this).css('border','0px');
		});

		$('.delivery_cnt').each(function(){
			$(this).attr('validation','true');
			$(this).attr('readonly',false);
		});
	}else if(obj.value == 'MA'){
		$('.apply_cnt').each(function(){
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




function ChangeType(val){
	if(val == 'IW'){
		//var table_width = $('#warehouse_move_apply_list').prop('offsetWidth');
		$('#move_company_id').attr('disabled',true);
		$('.inner_warehouse_move').css('display','none');
		$('#move_status_tr').css('display','none');
	}else{
		$('#move_company_id').attr('disabled',false);
		$('.inner_warehouse_move').show();
		$('#move_status_tr').show();
	}
}


function PopGoodsSelect(){
	//alert($('select[name^=now_company_id]').val());

	if($('select#h_type option:selected').val() == ''){
		alert('창고이동 유형을 선택후 상품등록을 진행하실 수 있습니다. ');	
	}else{
		if($('select[name^=now_company_id]').val() == ''){
			alert('현재 창고 사업장을 선택후 상품등록을 진행하실 수 있습니다. ');
		}else if($('select[name^=now_pi_ix]').val() == ''){
			alert('현재 창고를 선택후 상품등록을 진행하실 수 있습니다. ');
		}else if($('select[name^=move_company_id]').val() == ''){
			alert('이동 창고 사업장을 선택후 상품등록을 진행하실 수 있습니다. ');
		}else if($('select[name^=move_pi_ix]').val() == ''){
			alert('이동 창고를 선택후 상품등록을 진행하실 수 있습니다. ');
		}else if($('select[name^=move_ps_ix]').val() == ''){
			alert('이동 보관장소를 선택후 상품등록을 진행하실 수 있습니다. ');
		}else{
			if($('input[name=act]').val()=='update'){
				ShowModalWindow('goods_select.php?page_type=warehouse_move&company_id='+$('select[name^=now_company_id]').val()+'&pi_ix='+$('select[name^=now_pi_ix]').val()+'&ps_ix='+$('select[name^=now_ps_ix]').val(),1000,800,'goods_select');
			}else{
				ShowModalWindow('goods_select.php?page_type=warehouse_move&company_id='+$('select[name^=now_company_id]').val()+'&pi_ix='+$('select[name^=now_pi_ix]').val(),1000,800,'goods_select');
			}
			/*
			if($('select#h_type option:selected').val() == 'IW'){
				ShowModalWindow('goods_select.php?page_type=warehouse_move&company_id='+$('select[name^=now_company_id]').val()+'&pi_ix='+$('select[name^=now_pi_ix]').val()+'&ps_ix='+$('select[name^=now_ps_ix]').val(),1000,800,'goods_select');
			}else{
				ShowModalWindow('goods_select.php?page_type=warehouse_move&company_id='+$('select[name^=now_company_id]').val()+'&pi_ix='+$('select[name^=now_pi_ix]').val()+'&ps_ix='+$('select[name^=now_ps_ix]').val(),1000,800,'goods_select');
			}
			*/
		}
	}
}

function CheckWarehouseMove(obj){
	var now_pi_ix = $('#now_pi_ix').val();
	var now_ps_ix = $('#now_ps_ix').val();
	var check_bool = true;

	if($('input[name=act]').val()=='update'){
		$('.gid').each(function(){
				var gid = $(this).val();
		
				$.ajax({
			    url : './warehouse_move.act.php?act=check_stock}',
			    type : 'POST',
			    data : {
						act : 'check_stock',
						now_pi_ix:now_pi_ix,
						now_ps_ix:now_ps_ix,
						gid:gid
						},
			   	 dataType: 'html',
			      error: function(XMLHttpRequest, textStatus, errorThrown) {
						alert('error');
					},

			    success: function(transport){
					if(transport == 'Y'){
				
					}else{
						alert('현재 창고에 재고가 부족합니다...');
						return false;
					}
				}
		        });
		});
	}

	if(CheckFormValue(obj)){
		$('input.apply_cnt').each(function(){
			if($(this).val() == 0){
				$.unblockUI;
				alert('요청수량이 0인 상품이 있습니다. 창고이동이 불가능하니 수량체크 혹은 삭제후 다시 작성해주세요.');
				check_bool = false;
				return false;
			}
		});
		$('.delivery_cnt').each(function(){
			if($(this).val() > $(this).attr('apply_cnt')){
				$.unblockUI;
				alert($(this).val()+'::::'+$(this).attr('apply_cnt')+'출고수량이 요청수량보다 많습니다. 입력값을 확인해주세요');
				check_bool = false;
				return false;
			}
		});
		$('.entering_cnt').each(function(){
			if($(this).val() > $(this).attr('delivery_cnt')){
				$.unblockUI;
				alert('입고수량이 출고수량보다 많습니다. 입력값을 확인해주세요');
				check_bool = false;
				return false;
			}
		});

		$('#move_company_id').attr('disabled',false);
		$('#now_ps_ix').attr('disabled',false);
		$('#move_ps_ix').attr('disabled',false);

		return check_bool;
	}else{
		if($('select#h_type option:selected').val() == 'IW'){
			$('#move_company_id').attr('disabled',true);
		}else{
			$('#now_ps_ix').attr('disabled',true);
			$('#move_ps_ix').attr('disabled',true);
		}
		return false;
	}

}

</Script>
";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->strLeftMenu = inventory_menu();
	$P->addScript = $Script;
	$P->Navigation = "재고관리 > 입출고관리 > 창고이동 요청대장 ";
	$P->NaviTitle = "창고이동 요청대장 ";
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
	$P->Navigation = "재고관리 > 입출고관리 > 창고이동 요청대장";
	$P->title = "창고이동 요청대장";
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


CREATE TABLE IF NOT EXISTS `inventory_warehouse_move` (
  `wm_ix` int(10) unsigned AUTO_INCREMENT COMMENT '이동요청키값',
  `apply_charger_ix` varchar(32) DEFAULT NULL COMMENT '요청 담당자',
  `apply_charger_name` varchar(50) DEFAULT NULL COMMENT '요청 담당자 이름',
  `wm_apply_date` varchar(10) DEFAULT NULL COMMENT '이동 요청일자',
  `wm_delivery_date` varchar(10) DEFAULT NULL COMMENT '이동 출고일자',
  `wm_entering_date` varchar(10) NOT NULL COMMENT '이동 입고일자',
  `move_pi_ix` int(6) unsigned DEFAULT NULL COMMENT '창고키',
  `move_ps_ix` int(6) unsigned DEFAULT NULL COMMENT '보관장소키',
  `status` varchar(2) DEFAULT NULL COMMENT '상태',
  `charger_ix` varchar(32) DEFAULT NULL COMMENT '작성자',
  `charger_name` varchar(50) DEFAULT NULL COMMENT '작성자 이름',
  `etc` varchar(255) DEFAULT NULL COMMENT '기타필드',
  `al_ix` int(10) NOT NULL COMMENT '결제라인',
  `regdate` datetime NOT NULL COMMENT '등록일자',
  PRIMARY KEY (`wm_ix`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='창고이동내역';


CREATE TABLE IF NOT EXISTS `inventory_warehouse_move_detail` (
  `wmd_ix` int(10) NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `wm_ix` int(10) unsigned  COMMENT '이동요청키값',
  `pi_ix` int(6) unsigned DEFAULT NULL COMMENT '창고키',
  `ps_ix` int(6) unsigned DEFAULT NULL COMMENT '보관장소키',
  `gid` int(10) unsigned zerofill DEFAULT NULL COMMENT '품목아이디',
  `gname` varchar(255) DEFAULT NULL COMMENT '이동 품목명',
  `unit` varchar(100) DEFAULT NULL COMMENT '단위',
  `standard` varchar(100) DEFAULT NULL COMMENT '규격',
  `apply_cnt` int(8) DEFAULT NULL COMMENT '이동요청수량',
  `delivery_cnt` int(8) DEFAULT NULL COMMENT '이동 출고수량',
  `entering_cnt` int(8) DEFAULT NULL COMMENT '이동 입고수량',
  `regdate` datetime NOT NULL COMMENT '등록일자',
  PRIMARY KEY (`wmd_ix`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='창고이동 상세정보'  ;



create table inventory_order (
loid varchar(20) not null,
order_charger varchar(255) null default null,
limit_priod varchar(10) null default null,
ci_ix varchar(255) null default null,
incom_company_charger varchar(255) null default null,
total_price int(10) null default 0,
total_add_price int(10) null default 0,
status varchar(2) default null ,
etc varchar(255) default null ,
regdate datetime not null,
primary key(loid));

*/

?>