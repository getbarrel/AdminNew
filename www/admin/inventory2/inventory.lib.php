<?
$inventory_order_status["OR"] = "발주대기";
$inventory_order_status["WR"] = "발주완료(입고대기)";
$inventory_order_status["WC"] = "입고완료";
$inventory_order_status["CC"] = "입고취소";
$inventory_order_status["OC"] = "발주취소";
$inventory_order_status["WP"] = "부분입고";
$inventory_order_status["DC"] = "직발완료";

$inventory_places["1"] = "창고";
$inventory_places["2"] = "선반";
$inventory_places["3"] = "공장";
$inventory_places["4"] = "외주공장";
$inventory_places["9"] = "기타";

$complete_status = array('WC','CC','OC','DC','WP');



function UpdateProductStockInfo($order_info){
	
	$mdb = new Database;

	$sql = "select pid, opn_ix, opnd_ix , sum(stock) as stock  from inventory_product_stockinfo 
				where pi_ix = '".$order_info[pi_ix]."' 
				and pid = '".$order_info[pid]."' 
				and opn_ix = '".$order_info[opn_ix]."' 
				and opnd_ix = '".$order_info[opnd_ix]."' 
				group by pid, opn_ix, opnd_ix ";
	//$mdb->debug = true;
	$mdb->query("begin");
	$mdb->query($sql);
	$now_stock_info = $mdb->fetchall();
	
	if(count($now_stock_info) > 0){
			
			for($j=0; $j < count($now_stock_info);$j++){
					$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set 
								option_stock = ".$now_stock_info[$j][stock]." 
								where pid = '".$now_stock_info[$j][pid]."' and opn_ix = '".$now_stock_info[$j][opn_ix]."' and id = '".$now_stock_info[$j][opnd_ix]."'   ";
					//echo $sql;
					//exit;
					$total_stock = $total_stock + $now_stock_info[$j][stock];
					$mdb->query($sql);
			}
			
			$sql = "select  sum(stock) as stock from inventory_product_stockinfo where pid = '".$order_info[pid]."'   ";
			$mdb->query($sql);
			$mdb->fetch();
			$stock_sum = $mdb->dt[stock];
			if($stock_sum == ""){
				$stock_sum = 0;
			}
			if($stock_sum > 0){
				$option_stock_yn = "Y";
			}else{
				$option_stock_yn = "N";
			}
			$sql = "update shop_product set stock = ".$stock_sum." , option_stock_yn = '".$option_stock_yn."' where id = '".$order_info[pid]."'";
			$mdb->query($sql);
			

	}
	$mdb->query("commit");

}

function makeSelectBoxPlace($pi_ix, $select_name,$reg_pi_ix="", $validation = "true"){
	global $id;

	
	$mdb = new Database;
	if(is_array($reg_pi_ix)){
		$sql = "SELECT * FROM inventory_place_info pi where disp = 'Y' and pi.pi_ix NOT IN (".implode(',',$reg_pi_ix).") ";
		//echo $sql;

	}else{
		$sql = "SELECT * FROM inventory_place_info pi where disp = 'Y'  ";
		//echo $sql;
	}
	$mdb->query($sql);

	$mstring = "<select id='$select_name' name='$select_name'  onchange=\"document.location.href='?id=".$_GET["id"]."&$select_name='+this.value+'&ci_ix='+$('#ci_ix').val();\"  validation='".$validation."' title='보관장소' style='border:1px solid silver;width:150px;'>";
	$mstring .= "<option value=''>보관장소를 선택</option>";

		if($mdb->total){
			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);
				$mstring .= "<option value='".$mdb->dt[pi_ix]."' ".($mdb->dt[pi_ix] == $pi_ix ? "selected":"").">".$mdb->dt[place_name]."</option>";
			}
		}else{
			$mstring .= "<option value=''>등록된보관장소 가 없습니다.</option>";
		}
		/*
		$mstring .= "</select> ".($pi_ix == "" ? "":"<input type='checkbox' name='place_change' value='Y' style='border:0px' onclick=\"inventoryChange(this,'".$pi_ix."')\"> 보관장소 변경<span id='insert_place_info'><input type='hidden' name='$select_name' value='".$pi_ix."'></span>")." ";
		*/
	return $mstring;
}

function makeSelectBoxTargetPlace($pi_ix, $select_name,$reg_pi_ix="", $validation = "true"){
	global $id;

	
	$mdb = new Database;
	if($pi_ix){
		$sql = "SELECT * FROM inventory_place_info pi where disp = 'Y' and pi.pi_ix != '".$pi_ix."' ";
		//echo $sql;

	}else{
		$sql = "SELECT * FROM inventory_place_info pi where disp = 'Y'  ";
		//echo $sql;
	}
	$mdb->query($sql);

	$mstring = "<select id='$select_name' name='$select_name'   validation='".$validation."' title='보관장소' style='border:1px solid silver;width:150px;'>";
	$mstring .= "<option value=''>보관장소를 선택</option>";

		if($mdb->total){
			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);
				$mstring .= "<option value='".$mdb->dt[pi_ix]."' ".($mdb->dt[pi_ix] == $pi_ix ? "selected":"").">".$mdb->dt[place_name]."</option>";
			}
		}else{
			$mstring .= "<option value=''>등록된보관장소 가 없습니다.</option>";
		}
		/*
		$mstring .= "</select> ".($pi_ix == "" ? "":"<input type='checkbox' name='place_change' value='Y' style='border:0px' onclick=\"inventoryChange(this,'".$pi_ix."')\"> 보관장소 변경<span id='insert_place_info'><input type='hidden' name='$select_name' value='".$pi_ix."'></span>")." ";
		*/
	return $mstring;
}

function SelectBoxSellCustomer($select_name, $ci_ix, $validation='true'){
	$mdb = new Database;
	$mdb->query("SELECT * FROM inventory_customer_info where customer_type = 'D' ");

	$mstring = "<select name='$select_name' id='$select_name' validation='$validation' title='출고처'>";
	$mstring .= "<option value=''>출고처를 선택해 주세요</option>";
	if($mdb->total){
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			$mstring .= "<option value='".$mdb->dt[ci_ix]."' ".($ci_ix == $mdb->dt[ci_ix] ? " selected":"").">".$mdb->dt[customer_name]."</option>";
		}
	}else{
		$mstring .= "<option value=''>등록된 출고처가 없습니다</option>";
	}
	$mstring .= "</select>";


	return $mstring;
}

/*
function makeSelectBox($sdb,$table,$where,$select_name,$msg){
	$sdb->query("SELECT * FROM inventory_customer_info ".$where." ");

	$mstring = "<select name='$select_name' validation='true' title='출고처'>";
	$mstring .= "<option value=''>출고처를 선택해 주세요</option>";
	if($sdb->total){
		for($i=0;$i < $sdb->total;$i++){
			$sdb->fetch($i);
			$mstring .= "<option value='".$sdb->dt[ci_ix]."'>".$sdb->dt[customer_name]."</option>";
		}
	}else{
		$mstring .= "<option value=''>".$msg."</option>";
	}
	$mstring .= "</select>";


	return $mstring;
}
function makeSelectBox2($select_name,$msg){
	global $id;
	$odb = new Database;

	$sql = "select id, option_div from shop_product_options_detail where pid = '$id'";
	$odb->query($sql);
	$option_infos = $odb->fetchall();
		$mstring = "<table cellpadding=0 cellspacing=0>
					<tr>
						<td>";
		if(count($option_infos)){
			$mstring .= "<select name='$select_name' validation='true' title='옵션' style='width:200px;'>";
			$mstring .= "<option value=''>옵션선택</option>";
			for($i=0;$i < count($option_infos);$i++){
				//$odb->fetch($i);
				//$sql = "select option_div from shop_product_options_detail where id = '".$option_infos[$i][id]."'";
				//$odb->query($sql);
				//$odb->fetch();
				$mstring .= "<option value='".$option_infos[$i][id]."'>".$option_infos[$i][option_div]."</option>";
			}
			$mstring .= "</select>";
		}else{
			$mstring .= "<select name='$select_name' >";
			$mstring .= "<option value=''>".$msg."</option>";
			$mstring .= "</select>";
		}
		$mstring .= "
						</td>
						<td style='padding:0px 2px'>
						<input type='text' name='output_totalsize' class='textbox' size=10 validation='true' title='옵션수량'><input type='hidden' name='option_name' value='".$option_infos[$i][option_div]."'
						</td>
					</tr>
				</table>";


	return $mstring;
}

function makeSelectBox3($sdb,$table,$where,$select_name,$msg){
	global $id;
	$sdb->query("SELECT * FROM ".$table." ".$where." ");

	$mstring = "<select name='$select_name' class=small onchange=\"inventorySelect('".$id."',this.value)\">";
	$mstring .= "<option value=''>창고를 선택해 주세요</option>";

		if($sdb->total){
			for($i=0;$i < $sdb->total;$i++){
				$sdb->fetch($i);

				$mstring .= "<option value='".$sdb->dt[inventory_code]."'>".$sdb->dt[inventory_name]."</option>";
			}
		}else{
			$mstring .= "<option value=''>".$msg."</option>";
		}
		$mstring .= "</select>";
	return $mstring;
}
*/
function PrintStockByOption($goods_info){
	global $admininfo;
	$mdb = new Database;

	$sql = "select a.id, ips.vdate , option_div,option_code,option_price, option_coprice, option_stock, option_sell_ing_cnt, option_safestock,option_etc1 , ips.opnd_ix, ips.pi_ix,pi.place_name,  sum(ips.stock) as stock
				from  ".TBL_SHOP_PRODUCT_OPTIONS." b, 
				".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." a 
				left join inventory_product_stockinfo ips on a.pid = ips.pid and a.id = ips.opnd_ix 
				left join inventory_place_info pi on ips.pi_ix = pi.pi_ix 
				where b.option_kind = 'b' and b.pid = '".$goods_info[id]."' and a.opn_ix = b.opn_ix 
				group by a.id , ips.pi_ix 
				order by a.id asc";
	//echo nl2br($sql)."<br><br>";
	//exit;
	$mdb->query($sql);

	$mString = "<table cellpadding=4 cellspacing=0 width=100% height=65 style='table-layout : fixed' bgcolor=silver border=0>";



	if ($mdb->total == 0){
			if($_GET["pi_ix"] != ""){
				$pi_ix_str = " and pi_ix = '".$_GET["pi_ix"]."' ";
			}

			$sql = "select sum(stock) as stock from inventory_product_stockinfo where pid = '".$goods_info[id]."'  group by pid ";
			//echo $sql;
			$mdb->query($sql);

			if($mdb->total){
				$mdb->fetch();
				$stock = $mdb->dt[stock];
				$sum = $sum + $mdb->dt[stock];
			}else{
				$stock = 0;
			}

			$sql = "select vdate,place_name, sum(stock) as stock 
					from inventory_product_stockinfo ips, inventory_place_info pi 
					where pid = '".$goods_info[id]."' and ips.pi_ix = pi.pi_ix 
					group by pid, vdate ";
			//echo $sql;
			//exit;
			$mdb->query($sql);
			if($mdb->total){
				for($j=0;$j < $mdb->total;$j++){
					$mdb->fetch($j);

			$mString .= "<tr height=30>";
			if($j == 0){
			$mString .= "<td width='24%' bgcolor='#ffffff'  colspan=2 align=center rowspan='".$mdb->total."'><b>재고합계</b></td>";
			}
			$mString .= "
				<td width='12%' align=center bgcolor='#efefef' >".$mdb->dt[place_name]."</td>
				<td width='12%' align=center bgcolor='#efefef' >".$mdb->dt[vdate]."</td>
				<td width='12%' align=center bgcolor='#ffffff' >".number_format($goods_info[coprice])."</td>
				<td width='12%' align=center bgcolor='#efefef'>".$mdb->dt[stock]."</td>			
				<td width='12%' align=center bgcolor='#ffffff'>".$goods_info[sell_ing_cnt]."</td>
				<td width='12%' align=center bgcolor='#efefef'>".$goods_info[safestock]."</td>
				</tr>";
				}
			}else{
			$mString .= "<tr height=30>";
			$mString .= "<td width='24%' bgcolor='#ffffff'  colspan=2 align=center><b>재고합계</b></td>
				<td width='12%' align=center bgcolor='#efefef' >".$goods_info[place_name]."</td>
				<td width='12%' align=center bgcolor='#efefef' >".$mdb->dt[vdate]."</td>
				<td width='12%' align=center bgcolor='#ffffff' >".number_format($goods_info[coprice])."</td>
				<td width='12%' align=center bgcolor='#efefef'>".$mdb->dt[stock]."</td>			
				<td width='12%' align=center bgcolor='#ffffff'>".$goods_info[sell_ing_cnt]."</td>
				<td width='12%' align=center bgcolor='#efefef'>".$goods_info[safestock]."</td>
				</tr>";
			}
	}else{
		$i=0;

		$goods_option_info = $mdb->fetchall();
		

		for($i=0;$i < count($goods_option_info) ;$i++){
			//$mdb->fetch($i);

			if($goods_info[opn_ix] != ""){
				$opn_ix_str = " and opn_ix = '".$goods_info[opn_ix]."' ";
			}

			if($goods_info[opnd_ix] != ""){
				$opnd_ix_str = " and opnd_ix = '".$goods_info[opnd_ix]."' ";
			}

			/*
			if($_GET["pi_ix"] != ""){
				$pi_ix_str = " and pi_ix = '".$_GET["pi_ix"]."' ";
			}
			*/
			/*
			$sql = "select pid, opnd_ix, pi_ix, sum(stock) as stock from inventory_product_stockinfo where pid = '".$goods_info[id]."'  $opn_ix_str  $opnd_ix_str group by pid ,  opnd_ix , pi_ix ";
			//$sql = "select stock from inventory_product_stockinfo where pid = '".$goods_info[id]."'  $opn_ix_str  $opnd_ix_str ";
			$mdb->query($sql);
			

			if($mdb->total){
				$mdb->fetch();
				$stock = $mdb->dt[stock];
				$sum = $sum + $mdb->dt[stock];
			}else{
				$stock = 0;
			}
			*/
			$stock_total = $stock_total + $goods_option_info[$i][stock];

			$mString = $mString."<tr height=27 bgcolor=#ffffff>
			<td width='12%' align=center bgcolor='#efefef' >".$goods_option_info[$i][option_div]." </td>
			<td width='12%' align=center bgcolor='#ffffff' title='opnd_ix : ".$goods_option_info[$i][opnd_ix]."' >".$goods_option_info[$i][option_code]."</td>
			<td width='12%' align=center bgcolor='#efefef' >".$goods_option_info[$i][place_name]."</td>
			<td width='12%' align=center bgcolor='#efefef' >".$goods_option_info[$i][vdate]."</td>
			<td width='12%' align=center bgcolor='#ffffff' >".number_format($goods_option_info[$i][option_coprice])."</td>
			<td width='12%' align=center bgcolor='#efefef' >".$goods_option_info[$i][stock]."</td>
			<td width='12%' align=center bgcolor='#ffffff' >".$goods_option_info[$i][option_sell_ing_cnt]."</td>
			<td width='12%' align=center bgcolor='#efefef' >".$goods_option_info[$i][option_safestock]."</td>
			</tr>
			";
		}

		$mString .= "<tr height=27 bgcolor=#ffffff>
			<td width='48%' colspan=4 bgcolor='#efefef' align=center style='border-top:1px solid silver;'><b>총계</b></td>
			<td width='12%' bgcolor='#ffffff' align=center style='border-top:1px solid silver;'>-</td>
			<td width='12%' bgcolor='#efefef' align=center style='border-top:1px solid silver;'>".$stock_total."</td>	
			<td width='12%' bgcolor='#ffffff' align=center style='border-top:1px solid silver;'>-</td>
			<td width='12%' bgcolor='#efefef' align=center style='border-top:1px solid silver;'>-</td>
			</tr>";
	}
	$mString = $mString."</table>";

	return $mString;
}


function PrintStockByOptionToOrder($goods_info){
	global $admininfo;
	//print_r($sdb);
	$mdb = new Database;

	$sql = "select a.id, option_div,option_code,option_price,  option_coprice, option_stock, option_safestock,option_etc1 , ips.opnd_ix, ips.pi_ix,pi.place_name,  sum(ips.stock) as stock
				from  ".TBL_SHOP_PRODUCT_OPTIONS." b, 
				".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." a 
				left join inventory_product_stockinfo ips on a.pid = ips.pid and a.id = ips.opnd_ix 
				left join inventory_place_info pi on ips.pi_ix = pi.pi_ix 
				where b.option_kind = 'b' and b.pid = '".$goods_info[id]."' and a.opn_ix = b.opn_ix 
				group by a.id , ips.pi_ix 
				order by a.id asc";
	//echo nl2br($sql);
	$mdb->query($sql);

	$mdb->query($sql);

	$mString = "<table cellpadding=4 cellspacing=0 width=100% height=65 style='table-layout : fixed' bgcolor=silver border=0>";



	//$mString = $mString."<tr align=center bgcolor=#efefef height=25><td>비회원가</td><td>회원가</td><td>딜러가</td><td>대리점가</td><td >재고</td><td >안전재고</td></tr>";
	if ($mdb->total == 0){
		//print_r($goods_info);
			if($_GET["pi_ix"] != ""){
				$pi_ix_str = " and pi_ix = '".$_GET["pi_ix"]."' ";
			}

			$sql = "select sum(stock) as stock from inventory_product_stockinfo where pid = '".$goods_info[id]."'  group by pid ";
			//echo $sql;
			$mdb->query($sql);
			

			if($mdb->total){
				$mdb->fetch();
				$stock = $mdb->dt[stock];
				$sum = $sum + $mdb->dt[stock];
			}else{
				$stock = 0;
			}

		$mString .= "<tr height=30>";
		$mString .= "<td width='20%' bgcolor='#efefef'  colspan=2 align=center><b>-</b></td>
			<td width='10%' bgcolor='#ffffff' align=center >".$goods_info[place_name]." </td>
			<td width='10%' bgcolor='#efefef' align=center>".$goods_info[coprice]."</td>
			<td width='10%' bgcolor='#ffffff' align=center>".$goods_info[sellprice]."</td>
			<td width='10%' bgcolor='#efefef' align=center>".$stock."</td>
			<td width='10%' bgcolor='#ffffff' align=center>".$goods_info[safestock]."</td>
			<td width='10%' bgcolor='#efefef' align=center>
			<!--a href='cart.php?act=add&id=".$goods_info[id]."&pcount=1' >발주하기</a-->
			<a href=\"javascript:PoPWindow3('../inventory/order_pop.php?id=".$goods_info[id]."',750,700,'input_pop')\"><img src='../images/".$admininfo["language"]."/bts_order.gif'></a>
			</td>
			</tr>";
	}else{
		$i=0;

		
		
		
		$goods_option_info = $mdb->fetchall();

		


		for($i=0;$i < count($goods_option_info);$i++){
			//$mdb->fetch($i);

			if($goods_info[opn_ix] != ""){
				$opn_ix_str = " and opn_ix = '".$goods_info[opn_ix]."' ";
			}

			if($goods_info[opnd_ix] != ""){
				$opnd_ix_str = " and opnd_ix = '".$goods_info[opnd_ix]."' ";
			}

			/*
			if($_GET["pi_ix"] != ""){
				$pi_ix_str = " and pi_ix = '".$_GET["pi_ix"]."' ";
			}
			*/
			$sql = "select sum(stock) from inventory_product_stockinfo where pid = '".$goods_info[id]."'  $opn_ix_str  $opnd_ix_str group by pid , opn_ix ";
			//echo $sql;
			$mdb->query($sql);
			

			if($mdb->total){
				$mdb->fetch();
				$stock = $mdb->dt[stock];
				$sum = $sum + $mdb->dt[stock];
			}else{
				$stock = 0;
			}
			$stock_total = $stock_total + $stock;
			
			$mString .= "<tr height=27 bgcolor=#ffffff>			
			<td width='10%' bgcolor='#ffffff' align=center >".$goods_option_info[$i][option_div]." </td>
			<td width='10%' align=center bgcolor='#efefef' >".$goods_option_info[$i][option_code]."</td>
			<td width='10%' bgcolor='#ffffff' align=center >".$goods_option_info[$i][place_name]." </td>
			<td width='10%' align=center bgcolor='#efefef' >".$goods_option_info[$i][option_coprice]."</td>
			<td width='10%' align=center bgcolor='#ffffff' >".$goods_option_info[$i][option_coprice]."</td>
			<td width='10%' align=center bgcolor='#efefef' >".$goods_option_info[$i][stock]."</td>
			<td width='10%' align=center bgcolor='#ffffff' >".$goods_option_info[$i][option_safestock]."</td>";
			if($goods_info[id] != $bpid){
			$mString .= "
			<td width='10%' bgcolor='#efefef'  style='text-align:center;' rowspan='".count($goods_option_info)."' nowrap>
				<a href=\"javascript:PoPWindow3('../inventory/order_pop.php?id=".$goods_info[id]."',750,700,'input_pop')\"><img src='../images/".$admininfo["language"]."/bts_order.gif'></a>
			</td>";
			}
			$mString .= "
			</tr>
			";
			$bpid = $goods_info[id];
		}

	}
	$mString = $mString."</table>";

	return $mString;
}

function SelectSupplyCompany($supply_company,$select_name,$return_type="select",$validation="true"){

	$mdb = new Database;
	

	if($return_type == "select"){
		$sql = "SELECT * FROM inventory_customer_info where customer_type = 'E'  ";
		//echo $sql;
		$mdb->query($sql);

		$mstring = "<select name='$select_name'  id='$select_name'  validation='".$validation."' title='입고처' style='width:170px;'>";
		$mstring .= "<option value=''>입고처를 선택해 주세요</option>";

			if($mdb->total){
				for($i=0;$i < $mdb->total;$i++){
					$mdb->fetch($i);
					$mstring .= "<option value='".$mdb->dt[ci_ix]."' ".($supply_company == $mdb->dt[ci_ix] ? "selected":"").">".$mdb->dt[customer_name]."</option>";
				}
			}else{
				$mstring .= "<option value=''>등록된 입고처가 없습니다</option>";
			}
			$mstring .= "</select>";

		return $mstring;
	}else{
		$sql = "SELECT ci_ix, customer_name FROM inventory_customer_info where customer_type = 'E' and ci_ix = '".$supply_company."' ";
		//echo $sql;
		$mdb->query($sql);
		$mdb->fetch();

		return $mdb->dt[customer_name]."<input type=hidden name='".$select_name."' id='".$select_name."' value='".$mdb->dt[ci_ix]."'>";
	}
}


function SelectInventoryInfo($pi_ix,$select_name,$return_type="select",$validation="true"){
	global $id;

	$db = new Database;

	if($return_type == "select"){
		
		$db->query("SELECT * FROM inventory_place_info ".$where." ");

		$mstring = "<select name='$select_name' validation='".$validation."' title='출고보관장소'>";
		$mstring .= "<option value=''>출고 보관장소를 선택해주세요</option>";
			if($db->total){
				for($i=0;$i < $db->total;$i++){
					$db->fetch($i);
					$mstring .= "<option value='".$db->dt[pi_ix]."' ".($db->dt[pi_ix] == $pi_ix ? "selected":"").">".$db->dt[place_name]."</option>";
				}
			}else{
				$mstring .= "<option value=''>등록된창고 가 없습니다</option>";
			}
			$mstring .= "</select>";
	}else{
		$sql = "SELECT pi_ix, place_name FROM inventory_place_info where pi_ix = '".$pi_ix."' ";
		//echo $sql;
		$db->query($sql);
		$db->fetch();

		return $db->dt[place_name]."<input type=hidden name='".$select_name."' id='".$select_name."' value='".$db->dt[pi_ix]."'>";
	}

	return $mstring;
}


function selectDeliveryType($dt_ix,$select_name,$return_type="select",$validation="true"){
	global $id;

	$db = new Database;

	if($return_type == "select"){
		
		$db->query("SELECT * FROM inventory_delivery_type where disp = '1' ");

		$mstring = "<select name='$select_name' validation='".$validation."' onchange=\"SelectDeliveryType(this.value);\"  title='출고 형태'>";
		$mstring .= "<option value=''>출고 보관장소를 선택해주세요</option>";
			if($db->total){
				for($i=0;$i < $db->total;$i++){
					$db->fetch($i);
					$mstring .= "<option value='".$db->dt[dt_ix]."' ".($db->dt[dt_ix] == $dt_ix ? "selected":"").">".$db->dt[delivery_type]."</option>";
				}
			}else{
				$mstring .= "<option value=''>등록된 출고 보관장소가 없습니다</option>";
			}
			$mstring .= "</select>";
	}else{
		$sql = "SELECT * FROM inventory_delivery_type where disp = '1'  and dt_ix = '".$dt_ix."' ";
		//echo $sql;
		$db->query($sql);
		$db->fetch();

		return $db->dt[place_name]."<input type=hidden name='".$select_name."' id='".$select_name."' value='".$db->dt[pi_ix]."'>";
	}

	return $mstring;
}

?>