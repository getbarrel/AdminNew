<?
include("../class/layout.class");
$db = new Database; 

if($admininfo[company_id] == ""){
	echo "LOGIN";
	exit;
}

if($act=="product_list"){

	$where = "where p.id Is NOT NULL and p.id = r.pid and r.basic = 1 and p.product_type NOT IN ('".implode("','",$sns_product_type)."')  ";

	if($admininfo[admin_level] == 9){
		if($admininfo[mem_type] == "MD"){
			$where .= " and p.admin in (".getMySellerList($_SESSION["admininfo"]["charger_ix"]).") ";
		}
	}else{
		$where .= " and p.admin ='".$_SESSION["admininfo"]["company_id"]."' ";
	}

	if($search_text != ""){
		$where .= "and ( p.pname LIKE '%".trim($search_text)."%' OR p.pcode LIKE '%".trim($search_text)."%' OR p.id LIKE '%".trim($search_text)."%' ) ";
	}

	$sql="select p.* from ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r $where order by p.regdate desc limit ".$start.",".$max."";
	$db->query($sql);

	if($db->total > 0){
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);

			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[id], "s", $db->dt)) || $image_hosting_type=='ftp'){
				$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[id], "s", $db->dt);
			}else{
				$img_str = "../image/no_img.gif";
			}

			$Contents01 .= "
				<tr>
					<th align='center'>
						<div class='img_box'>
							<div class='goods_img'><img src='".$img_str."' alt='".$db->dt[pname]."' width='80' /></div>
							<!--img src='./images/delete_check.png' alt='' class='delete_check' /--><!-- <--삭제버튼 차후 개발-->
						</div>
					</th>
					<td align='left'>
						<dl class='goods_t_list'>
							<dt>".$db->dt[pname]."</dt>
							<dd><span>상품코드</span> ".($db->dt[pcode] ? $db->dt[pcode] : "-")."</dd>
							<dd><span>판매가</span> <b>".number_format($db->dt[sellprice])."</b>원</dd>
							<dd><span>도매가</span> <strike>".number_format($db->dt[wholesale_sellprice])."</strike>원</dd>
						</dl>
					</td>
					<td>";
						if($db->dt[disp]=='1'){
							$Contents01 .= "
							<img src='./images/goods_show.png' alt='노출함' width='50' class='' style='cursor:pointer;' onclick=\"dispUpdate($(this),'".$db->dt[id]."');\"/>";
						}else{
							$Contents01 .= "
							<img src='./images/goods_hide.png' alt='노출안함' width='50' class='' style='cursor:pointer;' onclick=\"dispUpdate($(this),'".$db->dt[id]."');\"/>";
						}
					$Contents01 .= "
					</td>
				</tr>";
		}
	}else{
		$Contents01 .= "{LAST}
		<tr>
			<th align='center' colspan='3'>
				<dl class='goods_t_list' style='text-align:center;'>
					<dt>마지막 상품입니다.</dt>
				</dl>
			</td>
		</tr>";
	}
	
	echo $Contents01;
	exit;
}


if($act=="order_list"){
	
	if($db->dbms_type == "oracle"){
		$where = "WHERE od.status !='SR' and o.oid=od.oid ";
	}else{
		$where = "WHERE od.status <> '' and od.status !='SR' and o.oid=od.oid ";
	}
	
	if($_SESSION["admininfo"]["admin_level"]!=9){
		$where .= " and od.company_id = '".$_SESSION["admininfo"]["company_id"]."' ";
	}

	//입금예정
	if($page_type=="" || $page_type==ORDER_STATUS_INCOM_READY){
		$where .=" and od.status in ('".ORDER_STATUS_INCOM_READY."')";
	}elseif($page_type==ORDER_STATUS_INCOM_COMPLETE){
		$where .=" and od.status in ('".ORDER_STATUS_INCOM_COMPLETE."')";
	}elseif($page_type==ORDER_STATUS_DELIVERY_READY){
		$where .=" and od.status in ('".ORDER_STATUS_DELIVERY_READY."')";
	}elseif($page_type==ORDER_STATUS_DELIVERY_ING){
		$where .=" and od.status in ('".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')";
	}elseif($page_type==ORDER_STATUS_CANCEL_APPLY){
		$where .=" and od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_EXCHANGE_APPLY."','".ORDER_STATUS_RETURN_APPLY."')";
	}

	if($status!=""){
		$where .=" and od.status = '".$status."' ";
	}

	if($startDate!="" && $endDate!=""){
		if($db->dbms_type == "oracle"){
			$where .= " and date_format(o.date_,'%Y%m%d') between $startDate and $endDate ";
		}else{
			$where .= " and date_format(o.date,'%Y%m%d') between $startDate and $endDate ";
		}
	}
	
	if($search_text!=""){
		$where .=" and ( o.oid = '".$search_text."' OR o.bname like '%".$search_text."%' OR o.rname like '%".$search_text."%') ";
	}

	if($page_type=="" || $page_type==ORDER_STATUS_INCOM_READY){//입금 예정일때
		/*
		if($db->dbms_type == "oracle"){
			$sql = "SELECT o.*, sum(od.ptprice-ifnull(od.member_sale_price,0)-ifnull(od.use_coupon,0)) as payment_price
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						$where
						group by o.oid
						ORDER BY date_ DESC LIMIT $start, $max";
		}else{

			$sql = "SELECT o.*, sum(od.ptprice-ifnull(od.member_sale_price,0)-ifnull(od.use_coupon,0)) as payment_price
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						$where
						group by o.oid
						ORDER BY date DESC LIMIT $start, $max";//쿼리 과부하로 인해 o.payment_price 뺌 -> 대표님 작업 kbk 13/05/31
		}
		*/
		
		if($db->dbms_type == "oracle"){
			$sql = "SELECT o.*, sum(od.ptprice-ifnull(od.use_coupon,0)) as payment_price
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						$where
						group by o.oid
						ORDER BY date_ DESC LIMIT $start, $max";
		}else{

			$sql = "SELECT o.*, sum(od.ptprice-ifnull(od.use_coupon,0)) as payment_price
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						$where
						group by o.oid
						ORDER BY date DESC LIMIT $start, $max";//쿼리 과부하로 인해 o.payment_price 뺌 -> 대표님 작업 kbk 13/05/31
		}

		$db->query($sql);

	}else{

		if($db->dbms_type == "oracle"){
			$sql = "SELECT o.oid
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						$where
						group by o.oid
						ORDER BY date_ DESC, oid LIMIT $start, $max";
		}else{

			$sql = "SELECT o.oid
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						$where
						group by o.oid
						ORDER BY date DESC, oid LIMIT $start, $max";//쿼리 과부하로 인해 o.payment_price 뺌 -> 대표님 작업 kbk 13/05/31
		}

		$db->query($sql);

		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$oid_list[] = $db->dt[oid];
		}
		
		if($db->total){
			$where .= " and o.oid in ('".implode("','",$oid_list)."') ";
		}else{
			$where .= " and o.oid in ('') ";
		}
		
		if($db->dbms_type == "oracle"){
			$sql = "SELECT *
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
					$where
					ORDER BY date_ DESC";
		}else{

			$sql = "SELECT *
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
					$where
					ORDER BY date DESC";
		}

		$db->query($sql);
	}

	$order_list = $db->fetchall();


	if($db->total > 0){
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			
			if($page_type=="" || $page_type==ORDER_STATUS_INCOM_READY){

				switch($order_list[$i][method]){
					case ORDER_METHOD_CARD :
							$method = "카드결제";
						break;
					case ORDER_METHOD_BANK :
							$method = "무통장 입금<br/>".$order_list[$i][bank];
						break;
					case ORDER_METHOD_VBANK :
							$method = "가상계좌<br/>".$order_list[$i][bank];
						break;
					case ORDER_METHOD_ASCROW :
							$method = "에스크로";
						break;
					case ORDER_METHOD_NOPAY :
							$method = "무료결제";
						break;
					case ORDER_METHOD_CASH :
							$method = "현금";
						break;
					default:
							$method = "-";
				}

				$Contents01 .= "
				<tr>
					<td>".$order_list[$i][oid]."<br />".$order_list[$i][bname]."(".$order_list[$i][rname].")</td>
					<td style='padding-left:10px;text-align:left;'>".$method."<br /><span style='color:#ff3e0c;'>".number_format($order_list[$i][payment_price])."원(".$order_list[$i][pcnt]."개)</span></td>
					<td><p>".getOrderStatus($order_list[$i][status])."</p><input type='checkbox' name='oid[]' class='select_checkbox' id='oid_".$order_list[$i][oid]."' value='".$order_list[$i][oid]."'/></td>
				</tr>";
			
			}elseif($page_type==ORDER_STATUS_INCOM_COMPLETE){

				$Contents01 .= "
				<tr>";
					
					if($b_oid != $order_list[$i][oid]){
						$od_cnt = 0;

						foreach($order_list as $order){
							if($order_list[$i][oid] == $order[oid]){
								$od_cnt++;
								echo $od_cnt;
							}
						}
						
						$Contents01 .= "
						<td rowspan='".$od_cnt."'>".$order_list[$i][oid]."<br />".$order_list[$i][bname]."(".$order_list[$i][rname].")</td>";
					}

					$Contents01 .= "
					<td class='goods_infomation'>
						<dl>
							<dt><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $order_list[$i][pid], 'm',$order_list[$i])."' /></dt>
							<dd>
								<ul>
									<li>[".$order_list[$i][company_name]."]</li>
									<li>".$order_list[$i][pname]."</li>
									<li><span style='color:#1e9be2;'>옵션 : ".($order_list[$i][option_text]!="" ? $order_list[$i][option_text] : "-")."</span></li>
								</ul>
							</dd>
						</dl>
						<p><span style='color:#ff3e0c;'>".number_format($order_list[$i][ptprice]-$order_list[$i][member_sale_price]-$order_list[$i][use_coupon])."원(".$order_list[$i][pcnt].")개</span></p>
					</td>
					<td><p>".getOrderStatus($order_list[$i][status])."</p><input type='checkbox' name='od_ix[]' class='select_checkbox' id='od_ix_".$order_list[$i][od_ix]."' value='".$order_list[$i][od_ix]."'/></td>
				</tr>";

			}elseif($page_type==ORDER_STATUS_DELIVERY_READY){

				$Contents01 .= "
				<tr>";
					
					if($b_oid != $order_list[$i][oid]){
						$od_cnt = 0;

						foreach($order_list as $order){
							if($order_list[$i][oid] == $order[oid]){
								$od_cnt++;
							}
						}
						
						$Contents01 .= "
						<td rowspan='".$od_cnt."'>".$order_list[$i][oid]."<br />".$order_list[$i][bname]."(".$order_list[$i][rname].")</td>";
					}

					$Contents01 .= "
					<td class='goods_infomation'>
						<dl>
							<dt><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $order_list[$i][pid], 'm',$order_list[$i])."' /></dt>
							<dd>
								<ul>
									<li>[".$order_list[$i][company_name]."]</li>
									<li>".$order_list[$i][pname]."</li>
									<li><span style='color:#1e9be2;'>옵션 : ".($order_list[$i][option_text]!="" ? $order_list[$i][option_text] : "-")."</span></li>
								</ul>
							</dd>
						</dl>
						<p><span style='color:#ff3e0c;'>".number_format($order_list[$i][ptprice]-$order_list[$i][member_sale_price]-$order_list[$i][use_coupon])."원(".$order_list[$i][pcnt].")개</span></p>
					</td>
					<td><p>".getOrderStatus($order_list[$i][status])."</p><input type='checkbox' name='od_ix[]' class='select_checkbox' id='od_ix_".$order_list[$i][od_ix]."' value='".$order_list[$i][od_ix]."'/></td>
				</tr>";
			
			}elseif($page_type==ORDER_STATUS_DELIVERY_ING){

				$Contents01 .= "
				<tr>";
					
					if($b_oid != $order_list[$i][oid]){
						$od_cnt = 0;

						foreach($order_list as $order){
							if($order_list[$i][oid] == $order[oid]){
								$od_cnt++;
							}
						}
						
						$Contents01 .= "
						<td rowspan='".$od_cnt."'>".$order_list[$i][oid]."<br />".$order_list[$i][bname]."(".$order_list[$i][rname].")</td>";
					}

					$Contents01 .= "
					<td style='padding-left:10px;text-align:left;'>[".$order_list[$i][company_name]."]<br />".$order_list[$i][pname]."<br /><span style='color:#1e9be2;'>옵션 : ".($order_list[$i][option_text]!="" ? $order_list[$i][option_text] : "-")."</span><br /><span style='color:#ff3e0c;'>".number_format($order_list[$i][ptprice]-$order_list[$i][member_sale_price]-$order_list[$i][use_coupon])."원(".$order_list[$i][pcnt].")개</span></td>
					<td>
						<table cellpadding='0' cellspacing='0' border='0' width='100%' class='add_td'>
							<tr>
								<td>
									".getOrderStatus($order_list[$i][status])."&nbsp;";
									if($order_list[$i][status]==ORDER_STATUS_DELIVERY_ING){
										$Contents01 .= "
										<form name=listform method='post' action='../order/orders.goods_list.act.php' onsubmit='return CheckStatusUpdate(this)' target='act'>
										<input type='hidden' name='act' value='status_update'>
										<input type='hidden' name='change_status' value='".ORDER_STATUS_DELIVERY_COMPLETE."'>
										<input type='hidden' name='od_ix' value='".$order_list[$i][od_ix]."'>
										<input type='image' src='./images/delivery_success.png' align='absmiddle' width='55' />
										</form>";
									}
								$Contents01 .= "
								</td>
							</tr>
							<tr>
								<td class='delivery_shop'>".deliveryCompanyList($order_list[$i][quick],"text")."</br /><a href=\"javascript:searchGoodsFlow('".$order_list[$i][quick]."', '".str_replace("-","",$order_list[$i][invoice_no])."')\">".$order_list[$i][invoice_no]."</a></td>
							</tr>
						</table>
					</td>
				</tr>";

			}elseif($page_type==ORDER_STATUS_CANCEL_APPLY){

				$Contents01 .= "
				<tr>";
					
					if($b_oid != $order_list[$i][oid]){
						$od_cnt = 0;

						foreach($order_list as $order){
							if($order_list[$i][oid] == $order[oid]){
								$od_cnt++;
							}
						}
						
						$Contents01 .= "
						<td rowspan='".$od_cnt."'>".$order_list[$i][oid]."<br />".$order_list[$i][bname]."(".$order_list[$i][rname].")</td>";
					}

					$Contents01 .= "
					<td style='padding-left:10px;text-align:left;'>[".$order_list[$i][company_name]."]<br />".$order_list[$i][pname]."<br /><span style='color:#1e9be2;'>옵션 : ".($order_list[$i][option_text]!="" ? $order_list[$i][option_text] : "-")."</span><br /><span style='color:#ff3e0c;'>".number_format($order_list[$i][ptprice]-$order_list[$i][member_sale_price]-$order_list[$i][use_coupon])."원(".$order_list[$i][pcnt].")개</span></td>
					<td>
						<table cellpadding='0' cellspacing='0' border='0' width='100%' class='add_td'>
							<tr>
								<td>
									".getOrderStatus($order_list[$i][status])."&nbsp;";
									if($order_list[$i][status]==ORDER_STATUS_CANCEL_APPLY){
										$Contents01 .= "
										<form name=listform method='post' action='../order/orders.goods_list.act.php' onsubmit='return CheckStatusUpdate(this)' target='act'>
										<input type='hidden' name='act' value='status_update'>
										<input type='hidden' name='change_status' value='".ORDER_STATUS_CANCEL_COMPLETE."'>
										<input type='hidden' name='od_ix' value='".$order_list[$i][od_ix]."'>
										<input type='image' src='./images/cancel_success.png' align='absmiddle' width='55' />
										</form>";
									}elseif($order_list[$i][status]==ORDER_STATUS_EXCHANGE_APPLY){
										$Contents01 .= "
										<form name=listform method='post' action='../order/orders.goods_list.act.php' onsubmit='return CheckStatusUpdate(this)' target='act'>
										<input type='hidden' name='act' value='status_update'>
										<input type='hidden' name='change_status' value='".ORDER_STATUS_EXCHANGE_ING."'>
										<input type='hidden' name='od_ix' value='".$order_list[$i][od_ix]."'>
										<input type='image' src='./images/swap_agree.png' align='absmiddle' width='55' />
										</form>";
									}elseif($order_list[$i][status]==ORDER_STATUS_RETURN_APPLY){
										$Contents01 .= "
										<form name=listform method='post' action='../order/orders.goods_list.act.php' onsubmit='return CheckStatusUpdate(this)' target='act'>
										<input type='hidden' name='act' value='status_update'>
										<input type='hidden' name='change_status' value='".ORDER_STATUS_RETURN_ING."'>
										<input type='hidden' name='od_ix' value='".$order_list[$i][od_ix]."'>
										<input type='image' src='./images/return_agree.png' align='absmiddle' width='55' />
										</form>";
									}
								$Contents01 .= "
								</td>
							</tr>
							<tr>
								<td class='delivery_shop'>";
									if($order_list[$i][quick]){
										$Contents01 .= "
										".deliveryCompanyList($order_list[$i][quick],"text")."</br /><a href=\"javascript:searchGoodsFlow('".$order_list[$i][quick]."', '".str_replace("-","",$order_list[$i][invoice_no])."')\">".$order_list[$i][invoice_no]."</a>";
									}
								$Contents01 .= "
								</td>
							</tr>
						</table>
					</td>
				</tr>";

			}

			$b_oid = $order_list[$i][oid];
		}
	}else{
		$Contents01 .= "{LAST}<tr><td colspan='3'>조회된 결과가 없습니다.</td></tr>";
	}
	
	echo $Contents01."
	<script type='text/javascript'>
	<!--
		$('input.select_checkbox:visible').imageTick({
		tick_image_path: './images/checkbox_on.png',
		no_tick_image_path: './images/checkbox.png',
		image_tick_class: 'checkbox_image_tick05'
	});
	//-->
	</script>";
	exit;
}

?>