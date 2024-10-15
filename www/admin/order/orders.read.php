<?
include("../class/layout.class");
include("../order/orders.lib.php");

$odb = new Database;
$db = new Database;

if($print_type=='combo_print'){
	$tmp_oid=$oid.",".$oid;
	$oid = "";
	$oid_array = split(",",$tmp_oid);
	sort($oid_array);
	$combo_bool=true;
}else{
	$oid_array = split(",",$oid);
}

for($o_i=0;$o_i < count($oid_array);$o_i++){
$oid = $oid_array[$o_i];

if($combo_bool && $o_i % 2 == 1){
	$print_type='buyer_print';
}elseif($combo_bool && $o_i % 2 == 0){
	$print_type='provider_print';
}


$sql = "SELECT o.*,AES_DECRYPT(UNHEX(refund_bank),'".$db->ase_encrypt_key."') as refund_bank1, AES_DECRYPT(UNHEX(refund_bank_name),'".$db->ase_encrypt_key."') as refund_bank_name1,
od.order_from, date_format(od.ic_date,'%Y-%m-%d') as ic_date , odd.rmail, odd.rname, odd.rtel, odd.rmobile, odd.zip, odd.addr1, odd.addr2,od.mall_ix
FROM ".TBL_SHOP_ORDER." o inner join ".TBL_SHOP_ORDER_DETAIL." as od on (o.oid = od.oid) left join shop_order_detail_deliveryinfo odd on (odd.oid=o.oid and odd.order_type='1')
WHERE o.oid = '".$oid."' limit 0,1";
$odb->query($sql);
$odb->fetch();

$origin_currency_unit = $admin_config["currency_unit"];
$admin_config["currency_unit"] = check_currency_unit($odb->dt['mall_ix']);
if($admin_config["currency_unit"] == 'USD'){
    $decimals_value = 2;
}else{
    $decimals_value = 0;
}
$Contents = "

<div class='order_print_area'>
	<table width='100%'>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("주문내역확인", "매출관리 > 주문내역확인 ")."</td>
		</tr>
	</table>

	<table cellpadding='0' cellspacing='0' border='0' class='print_area'  style='display:none;' width='100%'>";
		if($print_type=='picking_print'){
			$print_area_title="PICKING";
		}else{
			if($print_type=="provider_print"){
				$print_area_title="주문 명세서 (공급자용)";
			}else{
				$print_area_title="주문 명세서 (고객용)";
			}
		}
		$Contents .= "
		<tr>
			<td width='200'></td>
			<td width='*' height='50' align='center'>
				<b class='print_area_big_font' >".$print_area_title."</b>
			</td>
			<td width='200'><img src='/include/barcode/php-barcode-0.4/barcode.php?code=".$odb->dt[oid]."&scale=1&mode=png&encoding=128' /></td>
		</tr>
	</table>
	<table border='0' width='100%' cellspacing='1' cellpadding='0'>
		<tr height=30 bgcolor=''>
			<td style='padding:0;text-align:left;' > <b class='middle_title'>* 주문자/수취인정보</b></td>
		</tr>
		<tr>
			<td>
				<table border='0' width='100%' cellspacing='0' cellpadding='0' class='input_table_box' style='width:100%;'>
				<col width='15%' />
				<col width='35%' />
				<col width='15%' />
				<col width='35%' />
					<tr height=28 bgcolor='#ffffff' >
						<td class='input_box_title'>주문번호</td>
						<td class='input_box_item'>&nbsp;".$odb->dt[oid]." <b class='org'>".getOrderFromName($odb->dt[order_from])."</b> </td>
						<td class='input_box_title'>주문일자</td>
						<td class='input_box_item'>&nbsp;".$odb->dt[order_date]."</td>
					</tr>
					<tr height=28 bgcolor='#ffffff' >
						<td class='input_box_title' >주문자명/회원등급</td>
						<td class='input_box_item'>
							&nbsp;".$odb->dt[bname]." / ".($odb->dt[buserid] ? "(".$odb->dt[buserid].")" : "").$odb->dt[mem_group]."
						</td>
						<td class='input_box_title'>주문자메일</td>
						<td class='input_box_item'>
							&nbsp;".$odb->dt[bmail]."
						</td>
					</tr>
					<tr height=28 bgcolor='#ffffff' >
						<td class='input_box_title'>주문자전화</td>
						<td class='input_box_item'>
							&nbsp;".$odb->dt[btel]."
						</td>
						<td class='input_box_title'>주문자핸드폰</td>
						<td class='input_box_item'>
							&nbsp;".$odb->dt[bmobile]."
						</td>
					</tr>
					<tr height=28 bgcolor='#ffffff'>
						<td class='input_box_title'>수취인명</td>
						<td class='input_box_item'>
							&nbsp;".$odb->dt[rname]."
						</td>
						<td class='input_box_title'>수취인메일</td>
						<td class='input_box_item'>
							&nbsp;".$odb->dt[rmail]."
						</td>
					</tr>
					<tr height=28 bgcolor='#ffffff'>
						<td class='input_box_title'>수취인전화</td>
						<td class='input_box_item'>
							&nbsp;".$odb->dt[rtel]."
						</td>
						<td class='input_box_title'>수취인핸드폰</td>
						<td class='input_box_item'>
							&nbsp;".$odb->dt[rmobile]."
						</td>
					</tr>
					<tr height=28 bgcolor='#ffffff'>
						<td class='input_box_title'>배송주소</td>
						<td class='input_box_item' colspan='3'>
							&nbsp;[우: ".$odb->dt[zip]."] ".$odb->dt[addr1]." ".$odb->dt[addr2]."
						</td>
					</tr>
				</table>";
				
				$Contents .= "
				<table border='0' width='100%' cellspacing='1' cellpadding='0' class='not_print_area' style='margin-top:20px;'>
					<tr height=30 bgcolor=''> <!--박수철과장 추가 사항/2013.06.10-->
						<td style='padding:0;text-align:left;' > <b class='middle_title'>* 주문결제정보</b></td>
					</tr>
					<tr >
						<td >
							<table border='0' width='100%' cellspacing='0' cellpadding='0' class='input_table_box' style='width:100%;'>
							<col width='15%' />
							<col width='35%' />
							<col width='15%' />
							<col width='35%' />";

if($admininfo[admin_level] == 9){

			$method_info = getOrderMethodInfo($odb->dt);
			$method_=$method_info["method"];
			$method_str=$method_info["method_str"];
			$method_width=$method_info["method_width"];
			$method_height=$method_info["method_height"];
			$method_pay_info=$method_info["method_pay_info"];
			$total_real_pay_price=$method_info["total_real_pay_price"];

			$method = "<label class='helpcloud' help_width='".$method_width."' help_height='".$method_height."' help_html='".$method_str."'>".getMethodStatus($method_,"text")."</label>";
			

			$db->query("select
					(product_price+delivery_price-saveprice) as real_payment_price,
					expect_order_price
				from (
					select
						sum(case when payment_status='G' then expect_product_price + expect_delivery_price else '0' end) as expect_order_price,
						sum(case when payment_status='F' then -product_price else product_price end) as product_price,
						sum(case when payment_status='F' then -delivery_price else delivery_price end) as delivery_price,
						sum(case when payment_status='F' then -reserve else reserve end) as reserve,
						sum(case when payment_status='F' then -point else point end) as point,
						sum(case when payment_status='F' then -saveprice else saveprice end) as saveprice
					 from shop_order_price where oid='".$oid."'
				 ) p ");
			$order_price=$db->fetch();

			$Contents .= "
							<tr height=28>
								<td class='input_box_title'>결제방법</td>
								<td class='input_box_item' style='line-height:140%' >".$method." / ".getPaymentAgentType($odb->dt[payment_agent_type])."</span></td>
								<td class='input_box_title'>입금확인일자</td>
								<td class='input_box_item' >&nbsp;".$odb->dt[ic_date]."</td>
							</tr>
							<tr height=28>
								<td class='input_box_title'><b>주문 금액</b></td>
								<td class='input_box_item'>&nbsp; ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($order_price[expect_order_price],$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
								<td class='input_box_title'><b>총 결제금액</b></td>
								<td class='input_box_item'>&nbsp; ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($total_real_pay_price,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."(-포인트-적립금-예치금)</td>
							</tr>";


					$sql="select dc_type,sum(dc_price) as dc_price from shop_order_detail_discount where oid='".$oid."' group by dc_type";
					$db->query($sql);
					

					$product_discount="";
					$delivery_discount="";
					//dc_type 할인타입(MC:복수구매,MG:그룹,C:카테고리,GP:기획,SP:특별,CP:쿠폰,SCP:중복쿠폰,M:모바일,E:에누리,DCP:배송쿠폰,DE:배송비에누리)
					if($db->total){
						$dc_info=$db->fetchall();
						foreach($dc_info as $dc){

							if($dc[dc_type]=="DCP" || $dc[dc_type]=="DE"){
								$delivery_discount .=" > ".$_DISCOUNT_TYPE[$dc[dc_type]]." : ".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($dc[dc_price],$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."";
							}else{
								$product_discount .=" > ".$_DISCOUNT_TYPE[$dc[dc_type]]." : ".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($dc[dc_price],$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."";
							}
						}
					}

					if($product_discount!="")	$discount_product_str = "<b>상품  :</b> ".substr($product_discount,3);
					if($delivery_discount!="")	$discount_delivery_str = "<b>배송비  :</b> ".substr($delivery_discount,3);
					if($discount_product_str!="" && $discount_delivery_str!="")		$discount_delivery_str = "<br/>".$discount_delivery_str;

					if($discount_product_str!="" || $discount_delivery_str!=""){
						$Contents .= "
							<tr>
								<td class='input_box_title'><b>할인상세내역</b></td>
								<td class='input_box_item'style='line-height:140%;padding:3px;' colspan='3'>
									".$discount_product_str."
									".$discount_delivery_str."
								</td>
							</tr>";

					}

					$Contents .= "
						</table>
						<table border='0' width='100%' cellspacing='0' cellpadding='0' class='input_table_box' style='width:100%;margin-top:10px;'>
						<col width='*' />
						<col width='12%' />
						<col width='12%' />
						<col width='12%' />
						<col width='12%' />
						<col width='12%' />
						<col width='12%' />
						<col width='12%' />
						<col width='12%' />
							<tr>
								<td class='input_box_title' rowspan='2'><b>구분</b></td>
								<td class='input_box_title' colspan='3'><b>주문상세내역</b></td>
								<td class='input_box_title' colspan='4'><b>결제상세내역</b></td>
							</tr>
							<tr>
								<td class='input_box_title'><b>합계</b></td>
								<td class='input_box_title'><b>상품금액</b></td>
								<td class='input_box_title'><b>배송금액</b></td>
								<td class='input_box_title'><b>합계</b></td>
								<td class='input_box_title'><b>PG+무통장</b></td>
								<td class='input_box_title'><b>예치금</b></td>
								<td class='input_box_title'><b>적립금</b></td>
							</tr>";

						$sql="select 
								payment_status, 
								(expect_product_price + expect_delivery_price) as expect_total_price,
								expect_product_price,
								expect_delivery_price,
								(pg_payment_price + saveprice_payment_price + reserve_payment_price) as total_payment_price,
								pg_payment_price,
								saveprice_payment_price,
								reserve_payment_price
						from (
							select
								payment_status,
								ifnull(expect_product_price,0) as expect_product_price,
								ifnull(expect_delivery_price,0) as expect_delivery_price,
								SUM(case when pay_status='IC' and method not in ('".ORDER_METHOD_SAVEPRICE."','".ORDER_METHOD_RESERVE."') then payment_price else 0 end) as pg_payment_price,
								SUM(case when pay_status='IC' and method in ('".ORDER_METHOD_SAVEPRICE."') then payment_price else 0 end) as saveprice_payment_price,
								SUM(case when pay_status='IC' and method in ('".ORDER_METHOD_RESERVE."') then payment_price else 0 end) as reserve_payment_price
							from 
								shop_order_price p left join shop_order_payment p2 on (p.oid=p2.oid and p.payment_status=p2.pay_type)
							where 
								p.oid='".$oid."' 
							group by pay_type
							order by op_ix 
						) pay
						
						";

						$db->query($sql);

						if($db->total){
							for($i=0;$i<$db->total;$i++){
								$db->fetch($i);

								if($db->dt[payment_status]=='G'){
									$p_status=1;
									$p_title="주문결제";
								}elseif($db->dt[payment_status]=='A'){
									$p_status=1;
									$p_title="추가";
								}elseif($db->dt[payment_status]=='F'){
									$p_status=-1;
									$p_title="환불";
								}else{
									$p_status=0;
									$p_title="-";
								}

				$Contents .= "<tr>
								<td class='input_box_title'><b>".$p_title." </b></td>
								<td class='input_box_item' style='text-align:right;'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[expect_total_price]*$p_status,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."&nbsp;</td>
								<td class='input_box_item' style='text-align:right;'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[expect_product_price]*$p_status,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."&nbsp;</td>
								<td class='input_box_item' style='text-align:right;'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[expect_delivery_price]*$p_status,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."&nbsp;</td>
								<td class='input_box_item' style='text-align:right;'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[total_payment_price]*$p_status,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."&nbsp;</td>
								<td class='input_box_item' style='text-align:right;'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[pg_payment_price]*$p_status,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."&nbsp;</td>
								<td class='input_box_item' style='text-align:right;'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[saveprice_payment_price]*$p_status,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."&nbsp;</td>
								<td class='input_box_item' style='text-align:right;'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[reserve_payment_price]*$p_status,$decimals_value)." ".$currency_display[$admin_config["currency_unit"]]["back"]."&nbsp;</td>
							</tr>";
							}
						}else{
				$Contents .= "<tr>
								<td class='input_box_item' colspan='8' style='text-align:center;'>사용 내역이 없습니다.</td>
							</tr>";
						}
				$Contents .= "
							</table>
						</td>
					</tr>
				</table>";

}

				$Contents .= "
				<table border='0' width='100%' cellspacing='1' cellpadding='0' class='not_print_area' style='margin-top:20px;'>
					<tr height=30 bgcolor=''>
						<td style='padding:0;text-align:left;'> <b class='middle_title'>* 상태변경 내역</b></td>
					</tr>
				</table>
				<table border='0' width='100%' cellspacing='0' cellpadding='0' class='input_table_box not_print_area' style='width:100%;'>
					<tr bgcolor='#edefed'>
						<td class='input_box_title'> 주문상태</td>
						<td class='input_box_item'  colspan='3' style='padding:10px 0 10px 10px'>";

						if($admininfo[admin_level] == 9){
							$db->query("select distinct os.regdate, os.status, os.status_message, os.pid, c.com_name,os.invoice_no,os.quick,admin_message from ".TBL_SHOP_ORDER_STATUS." os left join ".TBL_COMMON_COMPANY_DETAIL." c on os.company_id = c.company_id where os.oid ='$oid'	 order by regdate asc");
						}else if($admininfo[admin_level] == 8){

							$db->query("select distinct os.regdate, os.status, os.status_message, os.pid, c.com_name,os.invoice_no,os.quick,admin_message from ".TBL_SHOP_ORDER_STATUS." os left join ".TBL_COMMON_COMPANY_DETAIL." c on os.company_id = c.company_id left join ".TBL_SHOP_PRODUCT." p on os.pid = p.id  where os.oid ='$oid' and p.admin ='".$admininfo[company_id]."'	order by regdate asc");
						}

						for($j = 0; $j < $db->total; $j++)
						{
							$db->fetch($j);
							$Contents .= "<span class=small>".$db->dt[regdate]." ".getOrderStatus($db->dt[status],$sattle_method)."  ".($db->dt[pid] ? "(상품코드:".$db->dt[pid]." - ".$db->dt[admin_message].")":"")." <span style='color:blue'>".($db->dt[invoice_no].":" ? codeName($db->dt[quick]).":":"")." ".($db->dt[invoice_no] ? $db->dt[invoice_no]:"")."</span> ".($db->dt[com_name] ? "- 수정업체:".$db->dt[com_name]."":"")." - <b>".$db->dt[status_message]."</b></span><br>";
						}

	$Contents .= "
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<br><br>

	<table border='0' width='100%' cellspacing='1' cellpadding='0'>
		<tr height=30 bgcolor=''>
			<td style='padding:0;text-align:left;'> <b  class='middle_title'>* 주문상품 정보</b></td>
		</tr>
	</table>
	<table border='0' width='100%'>
		<tr>
			<td>
				<table width='100%' border='0' cellpadding='0' class='input_table_box' >";
				if($print_type =="noprice_print" || $print_type =="picking_print"){
					$Contents .= "
					<tr height='35' bgcolor='#efefef' align=center>
						<td width='30px' class='s_td' ><b>번호</b></td>
						<td width='*' class='m_td small' ><b>상품명/옵션</b></td>
						<td width='40px' class='m_td small' ><b>수량</b></td>";
						if($print_type =="picking_print"){
						$Contents .= "
						<td width='17%' class='m_td small ' ><b>품목코드/품목명</b></td>
						<td width='15%' class='m_td small ' ><b>사업장/창고/보관장소</b></td>
						<td width='40px' class='m_td small ' ><b>재고</b></td>";
						}else{
						$Contents .= "
						<td width='10%' class='m_td small ' ><b>처리상태</b></td>";
						}
						$Contents .= "
						".($admininfo[admin_level]== 9 ? "<td width='7%' class='m_td small ".($print_type =="picking_print" ? "" : "not_print_area")."' ><b>출고처리</b></td>" : "")."
						<td width='13%' class='e_td small'><b>관리(바코드)</b></td>
					</tr>";
				}else{
					$Contents .= "
					<tr height='30' bgcolor='#efefef' align=center>
						<td width='35px' class='s_td' rowspan='2'><b>번호</b></td>
						<td width='*' class='m_td small' rowspan='2'><b>상품명/옵션</b></td>
						<td width='6%' class='m_td small ' rowspan='2'><b>판매단가<br/>(할인가)</b></td>
						<td width='40px' class='m_td small' rowspan='2'><b>수량</b></td>
						<td width='5%' class='m_td small not_print_area' rowspan='2'><b>상품가격</b></td>
						<td width='5%' class='m_td small not_print_area' rowspan='2'><b>할인액</b></td>
						<td width='21%' class='m_td small' colspan='3'><b>실결제금액</b></td>
						<td width='5%' class='m_td small not_print_area' rowspan='2'><b>적립금</b></td>
						<td width='7%' class='m_td small ' rowspan='2'><b>처리상태</b></td>
						".($admininfo[admin_level]== 9 ? "<td width='7%' class='m_td small not_print_area' rowspan='2'><b>출고처리</b></td>" : "")."
						<td width='8%' class='e_td small' rowspan='2'><b>관리(바코드)</b></td>
					</tr>
					<tr height='30'>
						<td width='7%' class='m_td small'><b>공급가</b></td>
						<td width='7%' class='m_td small'><b>세액</b></td>
						<td width='7%' class='m_td small'><b>판매가</b></td>
					</tr>";
				}

	if($print_type =="picking_print"){
		$sql = "SELECT od.*, ips.stock, ccd.com_name as inventory_com_name, pi.place_name, ps.section_name, gu.gid, gu.unit, g.gname, g.standard
			FROM ".TBL_SHOP_ORDER_DETAIL." od
			left join inventory_goods_unit gu on (gu.gu_ix=od.gu_ix)
			left join inventory_goods g on (g.gid=gu.gid)
			left join inventory_product_stockinfo ips on (ips.gid=g.gid and ips.unit=gu.unit and ips.company_id=od.delivery_company_id and ips.pi_ix=od.delivery_pi_ix and ips.ps_ix=od.delivery_ps_ix)
			left join common_company_detail ccd on (ccd.company_id = ips.company_id)
			left join inventory_place_info pi  on (pi.pi_ix = ips.pi_ix)
			left join inventory_place_section ps on (ps.ps_ix = ips.ps_ix)
			WHERE 
				od.oid = '".$oid."' 
			and 
				od.delivery_status in ('".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."','".ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM."','".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING."','".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."') ";
	}else{
		if($admininfo[admin_level] == 9){
			$sql = "SELECT od.*
				FROM ".TBL_SHOP_ORDER_DETAIL." od WHERE od.oid = '".$oid."' AND od.status not in ('SR') ";
		}else if($admininfo[admin_level] == 8){
			$sql = "SELECT od.*
			FROM ".TBL_SHOP_ORDER_DETAIL." od
			WHERE od.oid = '".$oid."' and od.company_id = '".$admininfo[company_id]."' AND od.status not in ('SR') ";
		}
	}

	$db->query($sql);


	$num = 1;

	$sum = 0;

	for($j = 0; $j < $db->total; $j++)
	{
		$db->fetch($j);

		if($db->dt[barcode]){
			if($print_type=="picking_print"){
				$barcode_str = "<img src='/include/barcode/php-barcode-0.4/barcode.php?code=".$db->dt[barcode]."&encoding=128&scale=1&mode=png' style='margin:10px 0px 10px 0px;' />";
			}else{
				$barcode_str = $db->dt[barcode];
			}
		}else{
			$barcode_str = "";
		}

                    $addQaDir = "";
                    if($admin_config['mall_domain'] == "0925admintest.barrelmade.co.kr"){
                        $addQaDir = "/QA";
                    }

					$Contents .= "
					<tr height='30' align='center'>
						<td class='' style='text-align:center;background:#fff;'>".$num."</td>
						<td class='' style='text-align:left;padding:5px;background:#fff;'>
							<table>
								<tr>
									<td align='center'>
										<img src='".PrintImage($admin_config[mall_data_root]."/images/addimgNew".$addQaDir, $db->dt[pid], "slist", $db->dt)."'  onerror=\"this.src='".$admin_config[mall_data_root]."/images/noimg_52.gif'\" style='margin:2px;border:1px solid silver'  width=50 style='margin:5px;'>
									</td>
									<td>";

					if(in_array($db->dt[product_type],$sns_product_type)){
							$Contents .= "<a href=\"/sns/shop/goods_view.php?id=".$db->dt[pid]."\" target=_blank>".$db->dt[pname]."</a>";
					} else {

						if($db->dt[product_type]=='99'||$db->dt[product_type]=='21'||$db->dt[product_type]=='31'){
							$Contents .= "<b class='".($db->dt[product_type]=='99' ? "red" : "blue")."' >".$db->dt[pname]."</b><br/><strong>".$db->dt[set_name]."<br /></strong>".$db->dt[sub_pname];
						}else{
							$Contents .= $db->dt[pname];
						}
					}
					
					if(strip_tags($db->dt[option_text])){
						$Contents .= "<br/> ▶ ".strip_tags($db->dt[option_text]).($db->dt[option_price] > 0 ? "<br/> + ".number_format($db->dt[option_price],$decimals_value)."":"");
					}

					$Contents .= "
									</td>
								</tr>
							</table>
						</td>";

						if($print_type =="noprice_print"||$print_type =="picking_print"){
							$Contents .= "
							<td class='' style='text-align:center;background:#fff;'>".$db->dt[pcnt]."</td>";
							if($print_type =="picking_print"){
								$Contents .= "
								<td class='' style='text-align:left;background:#fff;padding-left:5px;'>[".$db->dt[gid]."] <br/> ".$db->dt[gname]."".($db->dt[standard] ? "<br/>▶".$db->dt[standard] : "")."</td>
								<td class='' style='text-align:left;background:#fff;padding-left:5px;'>
									".$db->dt[inventory_com_name]."<br/>
									&nbsp; > ".$db->dt[place_name]."<br/>
									&nbsp; > ".$db->dt[section_name]."
								</td>
								<td class='' style='text-align:center;background:#fff;'>".number_format($db->dt[stock])."</td>";
							}
						}else{
							$Contents .= "
							<td class='' style='text-align:center;background:#fff;'>".number_format($db->dt[psprice]+$db->dt[option_price],$decimals_value)." </td>
							<td class='' style='text-align:center;background:#fff;'>".$db->dt[pcnt]."</td>
							<td class='not_print_area'  style='text-align:center;background:#fff;'>".number_format($db->dt[ptprice],$decimals_value)."</td>
							<td class='not_print_area'  style='text-align:center;background:#fff;'>".number_format($db->dt[ptprice]-$db->dt[pt_dcprice],$decimals_value)."</td>
							<td class='' style='text-align:center;background:#fff;'> ".number_format(round($db->dt[pt_dcprice])/1.1,$decimals_value)." </td>
							<td class='' style='text-align:center;background:#fff;'> ".number_format($db->dt[pt_dcprice]-round($db->dt[pt_dcprice])/1.1,$decimals_value)." </td>
							<td class='' style='text-align:center;background:#fff;'> ".number_format($db->dt[pt_dcprice],$decimals_value)."</td>
							<td class='not_print_area' style='text-align:center;background:#fff;'>".number_format($db->dt[reserve],$decimals_value)." P</td>";
						}

						if($print_type !="picking_print"){
							$Contents .= "			
							<td class=''  style='text-align:center;background:#fff;'>".getOrderStatus($db->dt[status])."</td>";
						}

						if($admininfo[admin_level]==9){
							$Contents .= "
							<td class=' ".($print_type =="picking_print" ? "" : "not_print_area")."' style='text-align:center;background:#fff;'>".getOrderStatus($db->dt[delivery_status])."</td>";
						}

						$Contents .= "
						<td class='' style='text-align:center;background:#fff;'>".$barcode_str."</td>
					</tr>";

		$num++;
	}
				$Contents .= "
				</table>
			</td>
		</tr>
	</table><br>";

	if (strlen($odb->dt[msg])){

		$Contents .= "
		<table border='0' width='100%' cellspacing='1' cellpadding='0'>
			<tr height=30 bgcolor=''>
				<td style='padding:0;text-align:left;'> <b  class='middle_title'>* 전달사항</b></td>
			</tr>
		</table>
		<table width='100%' border='0' cellpadding='0' cellspacing='1'>
			<tr>
				<td bgcolor='silver'>
					<table border='0' width='100%' cellspacing='1' cellpadding='2'>
						<tr>
							<td bgcolor='#ffffff'>
								<table border='0' width='100%'>
									<tr>
										<td style='padding:10px;'>
		".nl2br($odb->dt[msg])."
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table><br>";

	}
	
	if($print_type !="picking_print"){
		$Contents .= "
		<table width='100%' border='0' cellpadding='0' class='print_area' style='margin-top:30px;display:none;'>
			<tr>
				<td align=right style='line-height:250%'>
					<b class='middle_title' style='font-size:20px;'> 20&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 년 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 월 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 일<br/>
					상기품목을 인수합니다.<br/>
					인수자 : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (인)</b>
				</td>
			</tr>
		</table>";
	}

		if($o_i != count($oid_array)-1)		$Contents .= "<P CLASS=\"breakhere\">";

$Contents = $Contents." 
								</td>
							</tr>
						</table>	";


}//loop end
$Contents .= "</div><!--print_area 끝-->";
$Contents .= "
<table width='100%' border='0' cellpadding='0' cellspacing='1'>
	<tr>
		<td>
		</td>";
		//print_r($admininfo);
if ($odb->dt[stats] == "6"){
	$Contents .= "	<td align=right><!--img src='../images/".$admininfo["language"]."/btn_taxbill_view.gif' align=absmiddle onclick=\"PoPWindow('taxbill.php?uid=".$uid."&oid=".$odb->dt[oid]."',680,800,'sendsms')\" style='cursor:pointer;'-->  <button onclick=\"ReturnOK('".$odb->dt[oid]."')\">반품확인</button></td>";
}else if ($odb->dt[stats] == "7"){
	$Contents .= "";
}else{
	//$Contents .= "	<td align=right><button onclick=\"PoPWindow('product_return.php?oid=".$odb->dt[oid]."','400','150','return')\">반품신청</button></td>";
	$Contents .= "	<td align=right style='padding-right:10px;'>
				<!--button onclick=\"PrintDealingsSheet('".$odb->dt[oid]."')\">거래명세표</button>&nbsp;-->
				<!--button onclick=\"PrintMemberOrder('".$odb->dt[oid]."')\">출고증</button>&nbsp;-->
				<!--button onclick=\"PrintOrderDetail('".$odb->dt[oid]."')\">주문내역서</button>&nbsp;-->
				<!--img src='../images/".$admininfo["language"]."/btn_print.gif' align=absmiddle border=0 onClick='printArea()' style='cursor:pointer;'-->
				<!--img src='../images/".$admininfo["language"]."/btn_taxbill_view.gif' align=absmiddle onclick=\"PoPWindow('taxbill.php?uid=".$uid."&oid=".$odb->dt[oid]."',680,800,'sendsms')\" style='cursor:pointer;' align=absmiddle border=0-->";
				if($admininfo[admin_level] == 9 && $admininfo[language] == "korea"){
					if($admininfo[sattle_module] == "inicis"){
						$Contents .= " <a href='https://iniweb.inicis.com/' target='_blank'><img src='../images/".$admininfo["language"]."/btn_pg_inisis.gif' align=absmiddle border=0  ></a>";
					}else if($admininfo[sattle_module] == "allthegate"){
						$Contents .= " <a href='https://www.allthegate.com/login/r_login.jsp' target='_blank'><img src='../images//btn_pg_admin.gif' align=absmiddle border=0  ></a>";
					}else if($admininfo[sattle_module] == "lgdacom"){
						$Contents .= " <a href='http://pgweb.lgdacom.net' target='_blank'><img src='../images/".$admininfo["language"]."/btn_pg_lgdacom.gif' align=absmiddle border=0  ></a>";
					}else if($admininfo[sattle_module] == "kcp"){
						$Contents .= " <a href='https://admin.kcp.co.kr' target='_blank'><img src='../images/".$admininfo["language"]."/btn_pg_kcp.gif' align=absmiddle border=0  ></a>";
					}
				}
				/*
				if($odb->dt[receipt_y] == "Y"){
					if($receipt_cnt == 0){
						if($admininfo[sattle_module] == "inicis"){
							$Contents .="&nbsp;<img src='../image/btn_receipt.gif' style='cursor:pointer;vertical-align:middle;' onclick=\"PoPWindow('/shop/inicis/sample/INIreceipt_write.php?oid=".$odb->dt[oid]."','550','500','')\">
							</td>";
						}else if($admininfo[sattle_module] == "allthegate"){
							$Contents .="&nbsp;<img src='../image/btn_receipt.gif' style='cursor:pointer;vertical-align:middle;' onclick=\"PoPWindow('/cash/AGSCash.php?oid=".$odb->dt[oid]."','550','420','')\">
							</td>";
						}else if($admininfo[sattle_module] == "lgdacom"){
							$Contents .="&nbsp;<img src='../image/btn_receipt.gif' style='cursor:pointer;vertical-align:middle;' onclick=\"PoPWindow('/shop/lgdacom/cashreceipt_write.php?oid=".$odb->dt[oid]."','550','420','')\">
							</td>";
						}
					}else{
						$Contents .="&nbsp;<img src='../image/btn_receipt_view.gif' style='cursor:pointer;vertical-align:middle;' onclick=\"PoPWindow('/admin/order/receipt_view.php?oid=".$odb->dt[oid]."','650','370','')\">
							</td>";
					}
				}*/

			$Contents .="</td>";
}

unset($sumptprice);

$Contents .= "

				</tr>
			</table>
		 
";

/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >세금계산서를 발급받기 위해서는 세금계산서 관련 정보를 입력하셔야 합니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >반품신청을 원하시면 반품신청 버튼을 클릭하신후 반품 사유를 입력하시고 반품 확인 버튼을 누르시면 됩니다</td></tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$help_text = HelpBox("주문내역확인", $help_text);
$Contents .= $help_text;

$Contents .="
<!--object id='factory' style='display:none' viewastext classid='clsid:1663ed61-23eb-11d2-b92f-008048fdd814'
codebase='http://".$_SERVER["HTTP_HOST"]."/admin/order/scriptx/smsx.cab#Version=7.2.0.36'>
</object-->";
$Contents .="
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";

$admin_config["currency_unit"] = $origin_currency_unit;
$Script = "
<style type = \"text/css\">
	P.breakhere {page-break-before: always}
	.input_box_title {padding-left: 15px;background: #efefef;text-align: left;font-weight: bold;color: #000000;height:25px;}
	.input_box_item	{padding: 0px;background: #ffffff;padding-left: 10px;text-align: left;height:25px;}
</style>
<script language='javascript'>

var initBody ;

function beforePrint() {
	//document.body.innerHTML = document.getElementById('print_area').innerHTML;
	$('.not_print_area').hide();
	$('.print_area').show();
	$('*').css('font-size','10px');
	$('b.middle_title').css('font-size','12px');
	$('b.print_area_big_font').css('font-size','18px');
	tmp = '';
	$('.order_print_area').each(function(){
		//alert($(this).html());
		tmp += $(this).html();
	});
	
	document.body.innerHTML = tmp;
	initBody = document.body.innerHTML;
}

function afterPrint() {
	document.body.innerHTML = initBody;
}

window.onbeforeprint = beforePrint;
window.onafterprint = afterPrint;

function printArea() {
	window.focus();
	window.print();
	//beforePrint();
	// printPage();
}";

if($mmode == "print"){
	$Script .= "
	$(window).ready(function() {
		printArea();
	});";
}

$Script .= "
/*
//스크립트 X 주문을 일정량 넘어서면 치명적인 오류 발생으로 인해 사용 중단
function printPage() {
		factory.printing.header = ''; // Header에 들어갈 문장
		factory.printing.footer = ''; // Footer에 들어갈 문장
		factory.printing.portrait = true // true 면 세로인쇄, false 면 가로인쇄
		factory.printing.leftMargin = 15 // 왼쪽 여백 사이즈
		factory.printing.topMargin = 15 // 위 여백 사이즈
		factory.printing.rightMargin = 15 // 오른쪽 여백 사이즈
		factory.printing.bottomMargin = 15 // 아래 여백 사이즈
		factory.printing.preview();
		//factory.printing.Print(false) // 출력하기
	}
*/
</script>
";

if($mmode == "pop"||$mmode == "print"){

	$P = new ManagePopLayOut();
	$P->strLeftMenu = order_menu();
	$P->addScript = $Script."\n<script language='javascript' src='orders.js'></script>";
	$P->Navigation = "HOME > 주문관리 > 주문내역확인";
	$P->NaviTitle = "주문내역확인";
	$P->strContents = $Contents;

	echo $P->PrintLayOut();
}else{

	$P = new LayOut();
	$P->strLeftMenu = order_menu();
	$P->addScript = $Script."\n<script language='javascript' src='orders.js'></script>";
	$P->Navigation = "HOME > 주문관리 > 주문내역확인";
	$P->title = "주문내역확인";
	$P->strContents = $Contents;


	echo $P->PrintLayOut();
}

function SelectQuickLink($QuickCode, $deliverycode){
	$divname = array ("#",
	"http://www.ilogen.com/customer/reserve_03-1_ok.asp?f_slipno=",
	"http://www.doortodoor.co.kr/jsp/cmn/Tracking.jsp?QueryType=3&pTdNo=",
	"http://samsunghth.com/homepage/searchTraceGoods/SearchTraceDtdShtno.jhtml?dtdShtno=",
	"#",
	"#",
	"http://service.epost.go.kr/trace.RetrieveRegiPrclDeliv.postal?sid1=",
	"http://www.kgbls.co.kr/tracing.asp?number=",
	"http://www.yellowcap.co.kr/branch/chase/listbody.html?a_gb=branch&a_cd=5&a_item=0&f_slipno=",
	"#");


	return "<a href='".$divname[$QuickCode]."$deliverycode' target=_blank>$deliverycode</a>";

}

?>