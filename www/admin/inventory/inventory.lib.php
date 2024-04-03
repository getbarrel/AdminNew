<?
include_once ($_SERVER["DOCUMENT_ROOT"]."/admin/basic/company.lib.php");

$tb = $_SESSION['admin_config']["mall_inventory_category_div"]=="P"	?	TBL_SHOP_CATEGORY_INFO:"inventory_category_info";
/*
$inventory_order_status["AR"] = "승인대기";
$inventory_order_status["OR"] = "발주대기";
$inventory_order_status["WR"] = "발주완료(입고대기)";
$inventory_order_status["WC"] = "입고완료";
$inventory_order_status["CC"] = "입고취소";
$inventory_order_status["OC"] = "발주취소";
$inventory_order_status["WP"] = "부분입고";
$inventory_order_status["DC"] = "직발완료";
*/

$inventory_order_status["AC"] = "청구확정";
$inventory_order_status["ACC"] = "청구확정취소";
$inventory_order_status["OR"] = "발주예정";
$inventory_order_status["ORC"] = "발주예정취소";
$inventory_order_status["OC"] = "발주확정";
$inventory_order_status["OCC"] = "발주확정취소";
$inventory_order_status["WP"] = "부분입고";
$inventory_order_status["WC"] = "입고완료";
$inventory_order_status["GA"] = "지급확정";


$INVENTORY_GOODS_STATUS["0"]="일시품절";
$INVENTORY_GOODS_STATUS["1"]="판매중";
$INVENTORY_GOODS_STATUS["2"]="단종(품절)";



$inventory_places["1"] = "물류창고";
/*
$inventory_places["2"] = "위치";
$inventory_places["3"] = "공장";
$inventory_places["4"] = "생산공정";
$inventory_places["5"] = "작업장";
$inventory_places["9"] = "기타";
*/

$ITEM_ACCOUNT["1"] = "완제품(상품)";
$ITEM_ACCOUNT["2"] = "부재료";
$ITEM_ACCOUNT["3"] = "반제품";
//$ITEM_ACCOUNT["4"] = "완제품(상품)";
$ITEM_ACCOUNT["5"] = "용역";
$ITEM_ACCOUNT["6"] = "저장품";
//$ITEM_ACCOUNT["7"] = "7";
//$ITEM_ACCOUNT["8"] = "8";
$ITEM_ACCOUNT["9"] = "가상품목";


$ITEM_UNIT["1"] = "EA";
$ITEM_UNIT["2"] = "Kg";
$ITEM_UNIT["3"] = "m2";
$ITEM_UNIT["4"] = "Roll";
$ITEM_UNIT["5"] = "BOX";
$ITEM_UNIT["6"] = "Pack";
$ITEM_UNIT["7"] = "생산단위";
$ITEM_UNIT["8"] = "식";


$SURTAX_DIV["1"] = "부과세포함";
//$SURTAX_DIV["2"] = "부과세별도"; 2014-07-01 우선 개발이 안되어 있엇 주석처리 Hong
//$SURTAX_DIV["3"] = "영세율적용"; 2014-07-01 우선 개발이 안되어 있엇 주석처리 Hong
$SURTAX_DIV["4"] = "면세율적용";
//$SURTAX_DIV["5"] = "부가세없음";


$DELIVERY_STATUS["WDA"] = "출고요청";
$DELIVERY_STATUS["WDW"] = "출고작업";
$DELIVERY_STATUS["WDR"] = "출고대기";
$DELIVERY_STATUS["WDC"] = "출고완료";

$INVENTORY_STATUS["MA"] = "이동요청";
$INVENTORY_STATUS["AC"] = "요청취소";
$INVENTORY_STATUS["MO"] = "이동출고(작업중)";
$INVENTORY_STATUS["MI"] = "이동중";
$INVENTORY_STATUS["MC"] = "이동완료";
$INVENTORY_STATUS["MR"] = "이동취소"; // move retraction 


$TYPE_DIV["1"]["1"] = "재고조정";
$TYPE_DIV["1"]["2"] = "예외입고";
$TYPE_DIV["1"]["3"] = "반품입고";
$TYPE_DIV["1"]["4"] = "발주입고";
$TYPE_DIV["1"]["5"] = "창고이동";

$TYPE_DIV["2"]["11"] = "재고조정";
$TYPE_DIV["2"]["12"] = "예외출고";
$TYPE_DIV["2"]["13"] = "반품출고";
$TYPE_DIV["2"]["14"] = "판매출고";
$TYPE_DIV["2"]["15"] = "창고이동";

$SECTION_TYPE["G"] = "일반장소";
$SECTION_TYPE["S"] = "입고 보관장소";
$SECTION_TYPE["D"] = "출고 보관장소";
$SECTION_TYPE["P"] = "반품 보관장소 (양호)";
$SECTION_TYPE["B"] = "반품 보관장소 (불량)";


$DELIVERY_METHOD["TE"] = "택배";
$DELIVERY_METHOD["QU"] = "퀵서비스(오토바이)";
$DELIVERY_METHOD["TR"] = "용달(개인 트럭)";
$DELIVERY_METHOD["SE"] = "직접방문";


$complete_status = array('WC','CC','OC','DC','WP');


$inventory_image_info[] = array("width"=>"300","height"=>"300");
$inventory_image_info[] = array("width"=>"50","height"=>"50");


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

	$mstring = "<select id='$select_name' name='$select_name'  onchange=\"document.location.href='?gid=".$_GET["gid"]."&$select_name='+this.value+'&ci_ix='+$('#ci_ix').val();\"  validation='".$validation."' title='창고' style='border:1px solid silver;width:150px;'>";
	$mstring .= "<option value=''>창고</option>";

		if($mdb->total){
			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);
				$mstring .= "<option value='".$mdb->dt[pi_ix]."' ".($mdb->dt[pi_ix] == $pi_ix ? "selected":"").">".$mdb->dt[place_name]."</option>";
			}
		}else{
			$mstring .= "<option value=''>등록된창고 가 없습니다.</option>";
		}

		$mstring .= "</select>";
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

	$mstring = "<select id='$select_name' name='$select_name'   validation='".$validation."' title='창고' style='border:1px solid silver;width:150px;'>";
	$mstring .= "<option value=''>창고 </option>";

		if($mdb->total){
			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);
				$mstring .= "<option value='".$mdb->dt[pi_ix]."' ".($mdb->dt[pi_ix] == $pi_ix ? "selected":"").">".$mdb->dt[place_name]."</option>";
			}
		}else{
			$mstring .= "<option value=''>등록된창고 가 없습니다.</option>";
		}
		/*
		$mstring .= "</select> ".($pi_ix == "" ? "":"<input type='checkbox' name='place_change' value='Y' style='border:0px' onclick=\"inventoryChange(this,'".$pi_ix."')\"> 보관장소 변경<span id='insert_place_info'><input type='hidden' name='$select_name' value='".$pi_ix."'></span>")." ";
		*/
	return $mstring;
}

function SelectBoxSellCustomer($select_name, $ci_ix, $validation='true'){
	$mdb = new Database;
	$sql = "select
				ccd.company_id as ci_ix,
				ccd.com_name as customer_name
				from
					common_company_detail as ccd
				where
					seller_type like '%1%'
					and seller_type like '%3%'
					
		";
	$mdb->query($sql);

	$mstring = "<select name='$select_name' id='$select_name' validation='$validation' title='출고처'>";
	$mstring .= "<option value=''>매출처 </option>";
	if($mdb->total){
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			$mstring .= "<option value='".$mdb->dt[ci_ix]."' ".($ci_ix == $mdb->dt[ci_ix] ? " selected":"").">".$mdb->dt[customer_name]."</option>";
		}
	}else{
		$mstring .= "<option value=''>등록된 매출처가 없습니다</option>";
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

function makeSelectBoxAuthorizationLine($al_ix, $select_name,$reg_al_ix="", $validation = "true"){
	global $id;


	$mdb = new Database;
	if(is_array($reg_al_ix)){
		$sql = "SELECT * FROM common_authline_info ali where disp = '1' and ali.al_ix NOT IN (".implode(',',$reg_al_ix).") ";
		//echo $sql;

	}else{
		$sql = "SELECT * FROM common_authline_info ali where disp = '1'  ";
		//echo $sql;
	}
	$mdb->query($sql);

	$mstring = "<select id='$select_name' name='$select_name'   validation='".$validation."' title='결제라인' style='width:150px;'>";
	$mstring .= "<option value=''>결제라인 선택</option>";

		if($mdb->total){
			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);
				$mstring .= "<option value='".$mdb->dt[al_ix]."' ".($mdb->dt[al_ix] == $al_ix ? "selected":"").">".$mdb->dt[authline_name]."</option>";
			}
		}else{
			$mstring .= "<option value=''>결제라인이 없습니다.</option>";
		}
	return $mstring;
}

function PrintStockByOption1($goods_info){
	global $admininfo;
	$mdb = new Database;
	$idb = new Database;
	/*
	$sql = "select a.id, ips.vdate , option_div,option_code,option_price, option_coprice, option_stock, option_sell_ing_cnt, option_safestock,option_etc1 , ips.opnd_ix, ips.pi_ix, pi.place_name
				from  ".TBL_SHOP_PRODUCT_OPTIONS." b,
				".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." a
				left join inventory_product_stockinfo ips on a.pid = ips.pid and a.id = ips.opnd_ix
				left join inventory_place_info pi on ips.pi_ix = pi.pi_ix
				where b.option_kind = 'b' and b.pid = '".$goods_info[id]."' and a.opn_ix = b.opn_ix
				order by a.id asc";
	*/

	$mString = "<table cellpadding=4 cellspacing=0 border='0' width=100% height=65 style='table-layout : fixed' bgcolor=silver border=0>
						<col width='9%' >
						<col width='9%' >
						<col width='7%' >
						<col width='6%' >
						<col width='7%' >
						<col width='6%' >
						<col width='7%' >
						<col width='6%' >
						<col width='9%' >
						<col width='9%' >
						<col width='9%' >
						<col width='9%' >
						<col width='9%' >
						";

	$sql = "select gi.*, ips.pi_ix,vdate,place_name,stock
			from inventory_goods_unit gu left join inventory_product_stockinfo ips on (gi.gid = ips.gid and gu.gu_ix = ips.gu_ix)
			left join inventory_place_info pi on (ips.pi_ix = pi.pi_ix)
			where gi.gid = '".$goods_info[gid]."'
			order by ips.pi_ix, vdate ";
	//echo nl2br($sql)."<br><br>";
	//exit;
	$mdb->query($sql);
	//echo $mdb->total."<br>";
	unset($befor_place_name);

	for($j=0;$j < $mdb->total;$j++){

		$mdb->fetch($j);

		$sql = "select count(pi_ix) as ct_pi_ix
				from inventory_product_stockinfo where gid = '".$goods_info[gid]."' and pi_ix = '".$mdb->dt[pi_ix]."'";
		//echo nl2br($sql)."<br><br>";
		$idb->query($sql);
		$idb->fetch();
		$ct_pi_ix = $idb->dt[ct_pi_ix];
		//echo $goods_info[gid].":::".$ct_pi_ix."<br><Br>";

		$mString .= "<tr height=30>
			<td width='10%' align=center bgcolor='#efefef' ".(($mdb->total-1) != $j  ? "style='border-bottom:1px solid silver;'":"").">".$mdb->dt[item_name]."</td>
			<td width='10%' align=center bgcolor='#ffffff' ".(($mdb->total-1) != $j  ? "style='border-bottom:1px solid silver;'":"").">".$mdb->dt[item_code]."</td>";

		if(!$mdb->dt[place_name]) $mdb->dt[place_name] = '등록된재고없음';

		//if($befor_place_name != $mdb->dt[place_name]){
		
		//}

		$befor_place_name = $mdb->dt[place_name];

		if($mdb->dt[place_name] != '등록된재고없음'){
			$mString .= "
				<td width='12%' align=center bgcolor='#efefef' ".(($mdb->total-1) != $j  ? "style='border-bottom:1px solid silver;'":"").">".$mdb->dt[vdate]."</td>";
		}

		$mString .= "
			<td width='12%' align=center bgcolor='#efefef' ".(($mdb->total-1) != $j  ? "style='border-bottom:1px solid silver;'":"").">".number_format($mdb->dt[input_price])."</td>
			<td align=center bgcolor='#ffffff' ".(($mdb->total-1) != $j  ? "style='border-bottom:1px solid silver;'":"").">".number_format($mdb->dt[input_price])."</td>
			<td  align=center bgcolor='#efefef' ".(($mdb->total-1) != $j  ? "style='border-bottom:1px solid silver;'":"").">".number_format($mdb->dt[stock])."</td>
			<td  align=center bgcolor='#ffffff' ".(($mdb->total-1) != $j  ? "style='border-bottom:1px solid silver;'":"").">".$mdb->dt[item_sell_ing_cnt]."</td>
			<td  align=center bgcolor='#efefef' ".(($mdb->total-1) != $j  ? "style='border-bottom:1px solid silver;'":"").">".$mdb->dt[item_safestock]."</td>
			<td  align=center bgcolor='#ffffff' ".(($mdb->total-1) != $j  ? "style='border-bottom:1px solid silver;'":"").">".$mdb->dt[item_safestock]."</td>
			<td  align=center bgcolor='#efefef' ".(($mdb->total-1) != $j  ? "style='border-bottom:1px solid silver;'":"").">".$mdb->dt[item_safestock]."</td>
			<td  align=center bgcolor='#ffffff' ".(($mdb->total-1) != $j  ? "style='border-bottom:1px solid silver;'":"").">".$mdb->dt[item_safestock]."</td>
			<td  align=center bgcolor='#efefef' ".(($mdb->total-1) != $j  ? "style='border-bottom:1px solid silver;'":"").">".$mdb->dt[item_safestock]."</td>
			<td  align=center bgcolor='#ffffff' ".(($mdb->total-1) != $j  ? "style='border-bottom:1px solid silver;'":"").">".$mdb->dt[item_safestock]."</td>
			<td  align=center bgcolor='#efefef' ".(($mdb->total-1) != $j  ? "style='border-bottom:1px solid silver;'":"").">".$mdb->dt[item_safestock]."</td>
			</tr>";
	}


	$mString = $mString."</table>";

	return $mString;
}



function PrintStockByOption($goods_info){
	global $admininfo;
	$mdb = new Database;
	$idb = new Database;
	/*
	$sql = "select a.id, ips.vdate , option_div,option_code,option_price, option_coprice, option_stock, option_sell_ing_cnt, option_safestock,option_etc1 , ips.opnd_ix, ips.pi_ix, pi.place_name
				from  ".TBL_SHOP_PRODUCT_OPTIONS." b,
				".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." a
				left join inventory_product_stockinfo ips on a.pid = ips.pid and a.id = ips.opnd_ix
				left join inventory_place_info pi on ips.pi_ix = pi.pi_ix
				where b.option_kind = 'b' and b.pid = '".$goods_info[id]."' and a.opn_ix = b.opn_ix
				order by a.id asc";
	*/

	$mString = "<table cellpadding=4 cellspacing=0 width=100% height=65 style='table-layout : fixed' bgcolor=silver border=0>
						<col width='12%' >
						<col width='12%' >
						<col width='12%' >
						<col width='12%' >
						<col width='12%' >
						<col width='12%' >
						<col width='12%' >
						<col width='12%' >";

	$sql = "select gi.*, ips.pi_ix,vdate,place_name,stock
			from inventory_goods_unit gu left join inventory_product_stockinfo ips on (gi.gid = ips.gid and gu.gu_ix = ips.gu_ix)
			left join inventory_place_info pi on (ips.pi_ix = pi.pi_ix)
			where gi.gid = '".$goods_info[gid]."'
			order by ips.pi_ix, vdate ";
	//echo nl2br($sql)."<br><br>";
	//exit;
	$mdb->query($sql);
	//echo $mdb->total."<br>";
	unset($befor_place_name);

	for($j=0;$j < $mdb->total;$j++){

		$mdb->fetch($j);

		$sql = "select count(pi_ix) as ct_pi_ix
				from inventory_product_stockinfo where gid = '".$goods_info[gid]."' and pi_ix = '".$mdb->dt[pi_ix]."'";
		//echo nl2br($sql)."<br><br>";
		$idb->query($sql);
		$idb->fetch();
		$ct_pi_ix = $idb->dt[ct_pi_ix];
		//echo $goods_info[gid].":::".$ct_pi_ix."<br><Br>";

		$mString .= "<tr height=30>
			<td width='12%' align=center bgcolor='#efefef' ".(($mdb->total-1) != $j  ? "style='border-bottom:1px solid silver;'":"").">".$mdb->dt[item_name]."</td>
			<td width='12%' align=center bgcolor='#ffffff' ".(($mdb->total-1) != $j  ? "style='border-bottom:1px solid silver;'":"").">".$mdb->dt[item_code]."</td>";

		if(!$mdb->dt[place_name]) $mdb->dt[place_name] = '등록된재고없음';

		//if($befor_place_name != $mdb->dt[place_name]){
			$mString .= "
				<td width='12%' align=center bgcolor='#efefef' ".(($mdb->total-1) != $j  ? "style='border-bottom:0px solid silver;'":"")." title='".$ct_pi_ix."' ".($mdb->dt[place_name] == '등록된재고없음' ? "colspan='2'":"")." >".$mdb->dt[place_name]."</td>";//rowspan='".$ct_pi_ix."'
		//}

		$befor_place_name = $mdb->dt[place_name];

		if($mdb->dt[place_name] != '등록된재고없음'){
			$mString .= "
				<td width='12%' align=center bgcolor='#efefef' ".(($mdb->total-1) != $j  ? "style='border-bottom:1px solid silver;'":"").">".$mdb->dt[vdate]."</td>";
		}

		$mString .= "
			<td width='12%' align=center bgcolor='#ffffff' ".(($mdb->total-1) != $j  ? "style='border-bottom:1px solid silver;'":"").">".number_format($mdb->dt[input_price])."</td>
			<td width='12%' align=center bgcolor='#efefef' ".(($mdb->total-1) != $j  ? "style='border-bottom:1px solid silver;'":"").">".number_format($mdb->dt[stock])."</td>
			<td width='12%' align=center bgcolor='#ffffff' ".(($mdb->total-1) != $j  ? "style='border-bottom:1px solid silver;'":"").">".$mdb->dt[item_sell_ing_cnt]."</td>
			<td width='12%' align=center bgcolor='#efefef' ".(($mdb->total-1) != $j  ? "style='border-bottom:1px solid silver;'":"").">".$mdb->dt[item_safestock]."</td>
			</tr>";
	}


	$mString = $mString."</table>";

	return $mString;
}


function PrintStockByOptionToOrder($goods_info){
	global $admininfo;
	//print_r($sdb);
	$mdb = new Database;
	if($mdb->dbms_type == "oracle"){
		$sql = "select gi.gid, item_name,item_code,item_stock, item_safestock, ips.gu_ix, ips.pi_ix,pi.place_name,  sum(ips.stock) as stock
				from  inventory_goods_unit gu
				left join inventory_product_stockinfo ips on gi.gid = ips.gid and gu.gu_ix = ips.gu_ix
				left join inventory_place_info pi on ips.pi_ix = pi.pi_ix
				where gi.gid = '".$goods_info[gid]."'
				group by gi.gid, item_name,item_code,input_price, output_price, item_stock, item_safestock, ips.gu_ix, ips.pi_ix,pi.place_name
				order by gi.gid asc";
	}else{
		$sql = "select gi.gid, item_name,item_code,item_stock, item_safestock, ips.gu_ix, ips.pi_ix,pi.place_name,  sum(ips.stock) as stock
				from  inventory_goods_unit gu
				left join inventory_product_stockinfo ips on gi.gid = ips.gid and gu.gu_ix = ips.gu_ix
				left join inventory_place_info pi on ips.pi_ix = pi.pi_ix
				where gi.gid = '".$goods_info[gid]."'
				group by gi.gid , ips.pi_ix
				order by gi.gid asc";
	}
	//echo nl2br($sql);
	$mdb->query($sql);

	$mString = "<table cellpadding=4 cellspacing=0 width=100% height=65 style='table-layout : fixed' bgcolor=silver border=0>";

	$goods_option_info = $mdb->fetchall();

	for($i=0;$i < count($goods_option_info);$i++){
		//$mdb->fetch($i);

		if($goods_info[gu_ix] != ""){
			$opnd_ix_str = " and gu_ix = '".$goods_info[gu_ix]."' ";
		}

		/*
		if($_GET["pi_ix"] != ""){
			$pi_ix_str = " and pi_ix = '".$_GET["pi_ix"]."' ";
		}
		*/
		/*
		$sql = "select gu_ix,sum(stock) from inventory_product_stockinfo where gid = '".$goods_info[gid]."'  $opnd_ix_str group by gu_ix ";
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
		*/
		$mString .= "<tr height=27 bgcolor=#ffffff>
		<td width='10%' bgcolor='#ffffff' align=center >".$goods_option_info[$i][item_name]." </td>
		<td width='10%' align=center bgcolor='#efefef' >".$goods_option_info[$i][item_code]."</td>
		<td width='10%' bgcolor='#ffffff' align=center >".$goods_option_info[$i][place_name]." </td>
		<td width='10%' align=center bgcolor='#efefef' >".$goods_info[input_price]."</td>
		<td width='10%' align=center bgcolor='#ffffff' >".$goods_info[output_price]."</td>
		<td width='10%' align=center bgcolor='#efefef' >".$goods_option_info[$i][stock]."</td>
		<td width='10%' align=center bgcolor='#ffffff' >".$goods_option_info[$i][item_safestock]."</td>";
		if($goods_info[gid] != $bgid){
		$mString .= "
		<td width='10%' bgcolor='#efefef'  style='text-align:center;' rowspan='".count($goods_option_info)."' nowrap>
			<a href=\"javascript:PoPWindow3('../inventory/order_pop.php?gid=".$goods_info[gid]."',750,700,'input_pop')\"><img src='../images/".$admininfo["language"]."/bts_order.gif'></a>
		</td>";
		}
		$mString .= "
		</tr>
		";
		$bgid = $goods_info[gid];
	}


	$mString = $mString."</table>";

	return $mString;
}

function SelectSupplyCompany($company_id,$select_name,$return_type="select",$validation="true",$h_div="2",$type=''){

	$mdb = new Database;
	$db = new Database;
	
	if($h_div == '1'){		// 1:입고, 2:출고
		$select_title = "매입처";
		$where = " and ( ccd.seller_type like '%2%' or ccd.seller_type like '%4%') order by ccr.relation_code asc";
	}else if($h_div == '2'){
		$select_title = "매출처";
		$where = " and (ccd.seller_type like '%1%' or ccd.seller_type like '%3%')  order by ccr.relation_code asc";
	}
	$sql = "select relation_code from common_company_relation where company_id = '".$_SESSION[admininfo][company_id]."'";
	
	$mdb->query($sql);
	$mdb->fetch();
	$relaiotn_code = $mdb->dt[relation_code];

	$sql = "select
			ccd.company_id as company_id,
			ccd.com_name as com_name
			from
				common_company_detail as ccd
				inner join common_company_relation as ccr on (ccd.company_id = ccr.company_id)
			where
				ccr.relation_code like '".$relaiotn_code."%'
				$where
	";
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->total;

	
	if($return_type == "select"){
		if($total < constant("MAX_SUPPLY_VIEW_CNT")){
			//$sql = "SELECT * FROM inventory_customer_info where customer_type = 'E'  ";
			//$sql = "SELECT * FROM common_company_detail where seller_type = ''  ";
			
			if($h_div == '1'){		// 1:입고, 2:출고
				$select_title = "매입처";
				$where = " and ( ccd.seller_type like '%2%' or ccd.seller_type like '%4%') order by ccr.relation_code asc";
			}else if($h_div == '2'){
				$select_title = "매출처";
				$where = " and (ccd.seller_type like '%1%' or ccd.seller_type like '%3%')  order by ccr.relation_code asc";
			}
			$sql = "select relation_code from common_company_relation where company_id = '".$_SESSION[admininfo][company_id]."'";
			
			$mdb->query($sql);
			$mdb->fetch();
			$relaiotn_code = $mdb->dt[relation_code];

			$sql = "select
					ccd.company_id as company_id,
					ccd.com_name as com_name
					from
						common_company_detail as ccd
						inner join common_company_relation as ccr on (ccd.company_id = ccr.company_id)
					where
						ccr.relation_code like '".$relaiotn_code."%'
						$where";
			$mdb->query($sql);

			$mstring = "<div class='ui-widget' style='margin:5px 0px;'><select name='$select_name'  id='$select_name'  validation='".$validation."' title='".$select_title."' style='width:170px;'>";
			$mstring .= "<option value=''>".$select_title." 선택</option>";

				if($mdb->total){
					for($i=0;$i < $mdb->total;$i++){
						$mdb->fetch($i);
						$mstring .= "<option value='".$mdb->dt[company_id]."' ".($company_id == $mdb->dt[company_id] ? "selected":"").">".$mdb->dt[com_name]."</option>";
					}
				}else{
					$mstring .= "<option value=''>등록된 ".$select_title."가 없습니다</option>";
				}
				$mstring .= "</select></div>";
				$mstring .= "
				<script>
				$(function() {
					$('#".$select_name."').combobox();
				});
				</script>";

			return $mstring;
		
		}else{

				if($company_id){
					$db->query("SELECT * FROM common_company_detail where company_id='".$company_id."' ");
					$db->fetch();
				}

				$mstring =	"<table cellpadding=0 cellspacing=0>
								<tr>
									<td><input type=hidden class='textbox' name='$select_name' id='$select_name'  value='".$company_id."' validation='".$validation."' title='".$select_title."'></td>
									<td>
										<input type=text class='textbox point_color' name='ci_name' id='ci_name' value='".$db->dt[com_name]."' style='width:140px;' readonly>
									</td>
									<td style='padding-left:5px;'><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../inventory/company_search.php?h_div=$h_div&company_id=".$_SESSION[admininfo][company_id]."&type=".$type."',600,450,'company_search')\"  style='cursor:pointer;'> 
									</td>
									<td style='padding-left:3px;'> 
										<img src='../images/btn_x.gif' style='cursor:pointer' onclick=\"$(this).parent().parent().find('#".$select_name."').val('');$(this).parent().parent().find('#ci_name').val('');\">
									</td>
								</tr>
							</table>";
				return $mstring;
		}




	}else{
		//$sql = "SELECT * FROM common_company_detail where seller_type = '' and company_id = '".$company_id."' ";
		$sql = "select
					company_id as company_id,
					com_name as com_name
				from
					common_company_detail
				where
					company_id = '".$supply_company."' ";
		//echo $sql;
		$mdb->query($sql);
		$mdb->fetch();

		return $mdb->dt[com_name]."<input type=hidden name='".$select_name."' id='".$select_name."' value='".$mdb->dt[company_id]."'>";
	}

	
}


function SelectEstablishment($company_id,$select_name,$return_type="select",$validation="true",$event_handler=""){
	global $id;

	$db = new Database;
	$mdb = new Database;
	
	if($_SESSION["admininfo"]["mallstory_version"] == "service"){

		return $_SESSION["admininfo"]["company_name"]." <input type=hidden name='".$select_name."' id='".$select_name."' value='".$_SESSION["admininfo"]["company_id"]."'>";

	}else{
		
		//$sql = "select relation_code from common_company_relation where company_id = '".$company_id."'";
		$sql = "select relation_code from common_company_relation where company_id = '".$_SESSION["admininfo"]["company_id"]."'";
		$mdb->query($sql);
		$mdb->fetch();
		$relaiotn_code = $mdb->dt[relation_code];

		if($return_type == "select"){
			if($company_id != ""){
			$sql = "SELECT 
						distinct(ccd.company_id),
						ccd.*,
						cr.relation_code
						FROM 
						common_company_detail ccd
						inner join common_company_relation as cr on (ccd.company_id = cr.company_id)
						inner join inventory_place_info as ipi on (ccd.company_id = ipi.company_id)
						where ccd.com_type in ('BR','HO','BP','BO','G','A','S' ) and ccd.is_wharehouse =1
						and cr.relation_code like '".$relaiotn_code."%'
						order by cr.relation_code ASC
						";
			}else{
			$sql = "SELECT 
						distinct(ccd.company_id),
						ccd.*,
						cr.relation_code
						FROM 
						common_company_detail ccd
						inner join common_company_relation as cr on (ccd.company_id = cr.company_id)
						inner join inventory_place_info as ipi on (ccd.company_id = ipi.company_id)
						where ccd.com_type in ('BR','HO','BP','BO','G','A','S' ) and ccd.is_wharehouse =1
						order by cr.relation_code ASC";
			}

			$db->query($sql);
			$mstring = "<select name='$select_name' id='$select_name'  validation='".$validation."' title='사업장' ".$event_handler." style='min-width:140px;width:140px;'>";
			$mstring .= "<option value=''>사업장 </option>";
				if($db->total){
					for($i=0;$i < $db->total;$i++){
						$db->fetch($i);
						$mstring .= "<option value='".$db->dt[company_id]."' ".($db->dt[company_id] == $company_id ? "selected style='background-color:#fff7da' ":"").">".$db->dt[com_name]."</option>";
					}
				}else{
					$mstring .= "<option value=''>등록 사업장 정보가 없습니다</option>";
				}
				$mstring .= "</select> ";

		}else{
			//$sql = "SELECT pi_ix, place_name FROM inventory_place_info where pi_ix = '".$pi_ix."' ";
			$sql = "SELECT ccd.*,
						cr.relation_code
						FROM 
						common_company_detail ccd
						inner join common_company_relation as cr on (ccd.company_id = cr.company_id)
						where ccd.com_type in ('BR','HO','BP','BO','G','A','S' ) and is_wharehouse =1 and ccd.company_id = '".$company_id."'
						order by ccd.com_name ASC
						";
			//echo $sql;
			$db->query($sql);
			$db->fetch();

			return $db->dt[com_name]." <input type=hidden name='".$select_name."' id='".$select_name."' value='".$db->dt[company_id]."'>";
		}
	}
	return $mstring;
}

function SelectInventoryInfo($company_id, $pi_ix,$select_name,$return_type="select",$validation="true",$event_handler=""){
	global $id;

	$db = new Database;
	
	if($return_type == "select"){
		if($_SESSION["admininfo"]["mallstory_version"] == "service"){
			$db->query("SELECT * FROM inventory_place_info where disp = 'Y' and company_id = '".$_SESSION["admininfo"]["company_id"]."' ");
		}else{
			$db->query("SELECT * FROM inventory_place_info where disp = 'Y' and company_id = '".$company_id."' ");
		}
		
		if($_SESSION["admininfo"]["mallstory_version"] == "service"){
			if(substr_count($event_handler,"multiple") > 0){
				$mstring = "<select name='$select_name' id='$select_name' validation='".$validation."' title='창고' ".$event_handler." style='min-width:140px;'>";
			}else{
				$mstring = " / <select name='$select_name' id='$select_name' validation='".$validation."' title='창고' ".$event_handler." style='min-width:140px;'>";
			}
		}else{
			$mstring = "<select name='$select_name' id='$select_name' validation='".$validation."' title='창고' ".$event_handler." style='min-width:140px;'>";
		}

		$mstring .= "<option value=''>창고 </option>";
			if($db->total){
				for($i=0;$i < $db->total;$i++){
					$db->fetch($i);
					$mstring .= "<option value='".$db->dt[pi_ix]."' ".($db->dt[pi_ix] == $pi_ix ? "selected":"").">".$db->dt[place_name]."</option>";
				}
			}else{
				//$mstring .= "<option value=''>창고선택</option>";
			}
			$mstring .= "</select>";
	}else{
		$sql = "SELECT pi_ix, place_name FROM inventory_place_info where pi_ix = '".$pi_ix."' ";
		//echo $sql;
		$db->query($sql);
		$db->fetch();

		return "  > ".$db->dt[place_name]." <input type=hidden name='".$select_name."' id='".$select_name."' value='".$db->dt[pi_ix]."'>";
	}

	return $mstring;
}



function SelectSectionInfo($pi_ix, $ps_ix,$select_name,$return_type="select",$validation="true",$event_handler=""){
	global $id;

	$db = new Database; 

	if($return_type == "select"){

		$db->query("SELECT * FROM inventory_place_section where disp = '1' and pi_ix = '".$pi_ix."'  ");

		$mstring = "<select name='$select_name' id='$select_name' validation='".$validation."' title='보관장소'  ".$event_handler." style='min-width:90px;'>";
		$mstring .= "<option value=''>보관장소 </option>";
			if($db->total){
				for($i=0;$i < $db->total;$i++){
					$db->fetch($i);
					$mstring .= "<option value='".$db->dt[ps_ix]."' ".($db->dt[ps_ix] == $ps_ix ? "selected":"").">".$db->dt[section_name]."</option>";
				}
			}else{
				if($pi_ix){
					$mstring .= "<option value=''>등록된창고 가 없습니다</option>";
				}else{
					//$mstring .= "<option value=''>보관장소 선택</option>";
				}
			}
			$mstring .= "</select>";
	}else{
		$sql = "SELECT ps_ix, section_name FROM inventory_place_section where ps_ix = '".$ps_ix."' ";
		//echo $sql;
		$db->query($sql);
		$db->fetch();
		if($db->dt[section_name]){
			return " > ".$db->dt[section_name]."<input type=hidden name='".$select_name."' id='".$select_name."' value='".$db->dt[ps_ix]."'>";
		}
	}

	return $mstring;
}


function selectType($type,$type_div,$type_code,$select_name='type_code',$return_type="select",$validation="true",$property=""){
	//global $id;

	$db = new Database;

	if($type=='I' || $type=='1'){
		$type_title="입고유형";
	}elseif($type=='O' || $type=='2'){
		$type_title="출고유형";
	}elseif($type=='3'){
		$type_title="창고이동 유형";
		$type='2';
	}

	if($return_type == "select"){

		$db->query("SELECT * FROM inventory_type where disp = '1' and type ='".$type."' and type_div LIKE '%".$type_div."%' ");

		$mstring = "<select name='$select_name' id='$select_name' validation='".$validation."'   ".$property."  title='".$type_title."'> ";
		$mstring .= "<option value=''>".$type_title."을 선택</option>";
			if($db->total){
				for($i=0;$i < $db->total;$i++){
					$db->fetch($i);
					$mstring .= "<option value='".$db->dt[type_code]."' ".($db->dt[type_code] == $type_code ? "selected":"").">".$db->dt[type_name]."</option>";
				}
			}else{
				$mstring .= "<option value=''>등록된 ".$type_title."이 없습니다</option>";
			}
			$mstring .= "</select>";

	}elseif($return_type == "checkbox"){

		$db->query("SELECT * FROM inventory_type where disp = '1' and type ='".$type."' and type_div LIKE '%".$type_div."%' ");
		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);
			$mstring .= "<input type='checkbox' name='".$select_name."[]' id='".$select_name."_".$db->dt[type_code]."' value='".$db->dt[type_code]."' ".(count($_GET[$select_name]) && in_array($db->dt[type_code],$_GET[$select_name]) ? "checked":"")." /><label for='".$select_name."_".$db->dt[type_code]."' >".$db->dt[type_name]."</label> ";
		}
	}else{
		$sql = "SELECT * FROM inventory_type where disp = '1'  and type_code = '".$type_code."' and type ='".$type."' and type_div LIKE '%".$type_div."%' ";
		//echo $sql;
		$db->query($sql);
		$db->fetch();

		return $db->dt[type_name]."<input type=hidden class='textbox point_color' name='".$select_name."' id='".$select_name."' value='".$db->dt[type_code]."'>";
		//return $db->dt[type_name];
	}

	return $mstring;
}

function selectDeliveryType($type,$type_div,$type_code,$select_name='type_code',$return_type="select",$validation="true"){
	//global $id;

	$db = new Database;

	if($type=='1'){
		$type_title="입고유형";
	}elseif($type=='2'){
		$type_title="출고유형";
	}

	if($return_type == "select"){
		if($type=='1'){
			$db->query("SELECT * FROM inventory_type where disp = '1' and type ='".$type."' and type_div LIKE '%".$type_div."%' and type_code not in ('FC') ");
		}else if($type=='2'){
			$db->query("SELECT * FROM inventory_type where disp = '1' and type ='".$type."' and type_div LIKE '%".$type_div."%' ");
		}else{
			$db->query("SELECT * FROM inventory_type where disp = '1' and type_div LIKE '%".$type_div."%' ");
		}

		$mstring = "<select name='$select_name' validation='".$validation."'   title='".$type_title."'><!--".( $type=='O' ? "onchange=\"SelectDeliveryType(this.value);\"" : "" )." -->";
		$mstring .= "<option value=''>".$type_title."을 선택</option>";
			if($db->total){
				for($i=0;$i < $db->total;$i++){
					$db->fetch($i);
					$mstring .= "<option value='".$db->dt[type_code]."' ".($db->dt[type_code] == $type_code ? "selected":"").">".$db->dt[type_name]."</option>";
				}
			}else{
				$mstring .= "<option value=''>등록된 ".$type_title."이 없습니다</option>";
			}
			$mstring .= "</select>";
	}else{
		if($type==''){
			$sql = "SELECT * FROM inventory_type where disp = '1'  and type_code = '".$type_code."' ";
		}else{
			$sql = "SELECT * FROM inventory_type where disp = '1'  and type_code = '".$type_code."' and type ='".$type."' ";//and type_div LIKE '%".$type_div."%' 
		}
		//echo $sql."<br><br>";
		$db->query($sql);
		$db->fetch();

		//return $db->dt[place_name]."<input type=hidden name='".$select_name."' id='".$select_name."' value='".$db->dt[pi_ix]."'>";
		return $db->dt[type_name];
	}

	return $mstring;
}

function GetInventoryGoods($gid,$Column){
	//global $id;
	$db = new Database;

	$db->query("SELECT * FROM inventory_goods where gid='".$gid."'");
	$db->fetch();

	return $db->dt[$Column];
}

function GetInventoryGoodsItem($gu_ix,$Column){
	//global $id;
	$db = new Database;

	$db->query("SELECT * FROM inventory_goods_item where gu_ix='".$gu_ix."'");
	$db->fetch();

	return $db->dt[$Column];
}

function UpdateGoodsItemStockInfo($stocked_info, $mdb=""){
	if(!$mdb){
		$mdb = new Database;
	}
	//print_r($stocked_info);
	//exit;
	//stocked_cnt : 입고수량
	//vdate : 입고일
	//manufacture_date : 제조년월일
		//print_r($stocked_info);
		//exit;
		if($stocked_info[stocked_cnt] > 0 && $stocked_info[stocked_cnt] != "" || true){
				if($stocked_info[pi_ix] && $stocked_info[ps_ix] && $stocked_info[company_id]){
						if($stocked_info[pi_ix]){
							$sql = "select place_name, ccd.com_name
										from inventory_place_info pi 
										left join common_company_detail ccd on pi.company_id = ccd.company_id
										where pi.pi_ix = '".$stocked_info[pi_ix]."' ";
							$mdb->query($sql);
							$mdb->fetch();
							$company_id = $mdb->dt[company_id];
							$com_name = $mdb->dt[com_name];
							$place_name = $mdb->dt[place_name];
						}

						if($stocked_info[ps_ix]){
							$sql = "select * from inventory_place_section where ps_ix = '".$stocked_info[ps_ix]."' ";
							$mdb->query($sql);
							$mdb->fetch();
							$section_name = $mdb->dt[section_name];
						}

						if($stocked_info[company_id]){
							$sql = "select * from common_company_detail where company_id = '".$stocked_info[company_id]."' ";
							$mdb->query($sql);
							$mdb->fetch();
							$customer_name = $mdb->dt[com_name];
						}
						if($stocked_info[customer_name]){
							$customer_name = $stocked_info[customer_name];
						}
						
						$sql = "insert into inventory_history
									(h_ix,h_div,vdate,customer_name,com_name,place_name,section_name,company_id,ci_ix,pi_ix,ps_ix,ioid,oid,msg,h_type,charger_name,charger_ix,regdate) 
									values
									('','".$stocked_info[h_div]."','".$stocked_info[vdate]."','".$customer_name."','".$com_name."','".$place_name."','".$section_name."','".$stocked_info[company_id]."','".$stocked_info[ci_ix]."','".$stocked_info[pi_ix]."','".$stocked_info[ps_ix]."','".$stocked_info[ioid]."','".$stocked_info[oid]."','".$stocked_info[msg]."','".$stocked_info[h_type]."','".$stocked_info[charger_name]."','".$stocked_info[charger_ix]."',NOW()) ";
						
						$mdb->sequences = "INVENTORY_HISTORY_SEQ";
						$mdb->query($sql);
						//}

						if($mdb->dbms_type == "oracle"){
							$h_ix = $mdb->last_insert_id;
						}else{
							$mdb->query("SELECT h_ix FROM inventory_history WHERE h_ix=LAST_INSERT_ID()");
							$mdb->fetch();
							$h_ix = $mdb->dt[h_ix];
						}
				}
				//print_r($stocked_info);
				//exit;
				$stocked_detail_info = $stocked_info[detail];

				//for($i=0;$i<count($stocked_detail_info);$i++){
				foreach($stocked_detail_info as $detail_info){//20131022 Hong foreach로 변경
					// 마스터 정보에 창고 정보
					if(!$stocked_info[pi_ix] && !$stocked_info[ps_ix] && !$stocked_info[company_id]){
							if($detail_info[pi_ix]){
								$sql = "select place_name, ccd.com_name
											from inventory_place_info pi 
											left join common_company_detail ccd on pi.company_id = ccd.company_id
											where pi.pi_ix = '".$detail_info[pi_ix]."' ";
								$mdb->query($sql);
								$mdb->fetch();
								$company_id = $mdb->dt[company_id];
								$com_name = $mdb->dt[com_name];
								$place_name = $mdb->dt[place_name];
							}

							if($detail_info[ps_ix]){
								$sql = "select * from inventory_place_section where ps_ix = '".$detail_info[ps_ix]."' ";
								$mdb->query($sql);
								$mdb->fetch();
								$section_name = $mdb->dt[section_name];
							}

							if($detail_info[company_id]){
								$sql = "select * from common_company_detail where company_id = '".$detail_info[company_id]."' ";
								$mdb->query($sql);
								$mdb->fetch();
								$customer_name = $mdb->dt[com_name];
							}
							$sql = "insert into inventory_history
										(h_ix,h_div,vdate,customer_name,com_name,place_name,section_name,company_id,ci_ix,pi_ix,ps_ix,ioid,oid,msg,h_type,charger_name,charger_ix,regdate) 
										values
										('','".$stocked_info[h_div]."','".$stocked_info[vdate]."','".$customer_name."','".$com_name."','".$place_name."','".$section_name."','".$detail_info[company_id]."','".$stocked_info[ci_ix]."','".$detail_info[pi_ix]."','".$detail_info[ps_ix]."','".$stocked_info[ioid]."','".$stocked_info[oid]."','".$stocked_info[msg]."','".$stocked_info[h_type]."','".$stocked_info[charger_name]."','".$stocked_info[charger_ix]."',NOW()) ";
							//echo $sql;
							//exit;
							$mdb->sequences = "INVENTORY_HISTORY_SEQ";
							$mdb->query($sql);
							//}

							if($mdb->dbms_type == "oracle"){
								$h_ix = $mdb->last_insert_id;
							}else{
								$mdb->query("SELECT h_ix FROM inventory_history WHERE h_ix=LAST_INSERT_ID()");
								$mdb->fetch();
								$h_ix = $mdb->dt[h_ix];
							}
					}

					$sql = "SELECT g.gname FROM inventory_goods g WHERE gid = '".$detail_info[gid]."' ";
					$mdb->query($sql);
					$mdb->fetch();
					$gname = $mdb->dt[gname];

					/* 입출고일 경우 평균단가, 판매단가,수량,최종판매가 inventory_history_detail 추가 2013-08-08 이학봉*/
					if($stocked_info[h_type] != "IW" && $stocked_info[h_type] != "OW"){ // 창고이동이 아닐경우...
						if($stocked_info[h_div] == "2"){

							$remain_stock = 0;

							// 출고일경우 현재 평균단가를 가져온다
							$sql = "select avg_price from inventory_goods_unit where gid = '".$detail_info[gid]."' and unit = '".$detail_info[unit]."' ";
							$mdb->query($sql);
							$mdb->fetch();
							$avg_price = $mdb->dt[avg_price];
						}else{
							
							$remain_stock = $detail_info[amount];

							// 입고일때 재고자산가지 이동평균법 처리 : (누적입고금액+현재입금금액/누적수량+현입고수량) = 현재고자산단가
							$sql = "select 
										total_stock,
										avg_price, 
										total_stock*avg_price as asset_price 
									from inventory_goods_unit 
									where gid = '".$detail_info[gid]."' and unit = '".$detail_info[unit]."' ";
							$mdb->query($sql);
							
							if($mdb->total){
								$mdb->fetch();
								$assets_info = $mdb->dt;

								if($stocked_info[oid]){

										// 반품, 환불 입고처리의 경우 출고리스트의 평균단가로 다시 입고단가를 계산한다.

										$sql = "select
													avg_price
												from
													inventory_history as h
													inner join inventory_history_detail as hd on (h.h_ix = hd.h_ix and h.oid = '".$stocked_info[oid]."')
												where
													hd.gid = '".$detail_info[gid]."'
													and hd.unit = '".$detail_info[unit]."'";
										$mdb->query($sql);
										$mdb->fetch();
										$output_avg_price = $mdb->dt[avg_price];

										if(abs($assets_info[total_stock]+$detail_info[amount]) > 0){
											$avg_price = ($assets_info[asset_price]+($detail_info[amount]*$output_avg_price))/(abs($assets_info[total_stock]+$detail_info[amount]));		//
										}else{
											$avg_price = '0';	// 없을경우 평균단가는 0
										}
										
								}else{
									if(abs($assets_info[total_stock]+$detail_info[amount]) > 0){
										$avg_price = ($assets_info[asset_price]+($detail_info[amount]*$detail_info[price]))/(abs($assets_info[total_stock]+$detail_info[amount]));
									}
								}

								if($avg_price=="") $avg_price=0;

							}else{
								$avg_price = '0';	// 없을경우 평균단가는 0
							}
							
						}
					}
					/* 입출고일 경우 평균단가, 판매단가,수량,최종판매가 inventory_history_detail 추가 2013-08-08 이학봉*/
					
					//20131128 Hong inventory_history_detail 에 ptprice 입력하기 위한 주문쪽데이터와 비교하기 위한 구분값 
					if($stocked_info["act_from"] == "inventory"){
						$ptprice = $detail_info[price] *  $detail_info[amount];
					}else{
						//$ptprice = $detail_info[ptprice] - $detail_info[use_coupon];
						$ptprice = $detail_info[pt_dcprice];
					}

					$sql = "insert into inventory_history_detail
								(hd_ix,h_ix,gid,unit,gname,standard,amount,price,expiry_date,regdate,avg_price,ptprice,remain_stock)
								values
								('','".$h_ix."','".$detail_info[gid]."','".$detail_info[unit]."','".$gname."','".$detail_info[standard]."','".$detail_info[amount]."','".$detail_info[price]."','".$detail_info[expiry_date]."',NOW(),'".$avg_price."','".$ptprice."','".$remain_stock."') ";

					$mdb->sequences = "INVENTORY_HISTORY_DT_SEQ";
					$mdb->query($sql);

					//2014-04-11 출고일때 입고된 history 의 remain_stock 차감하기
					if($stocked_info[h_type] != "IW" && $stocked_info[h_type] != "OW"){
						if($stocked_info[h_div] == "2"){
							$sql = "select hd.hd_ix,hd.remain_stock from inventory_history h , inventory_history_detail hd where h.h_ix=hd.h_ix and h.h_div='1' and hd.gid='".$detail_info[gid]."' and hd.unit='".$detail_info[unit]."' and  hd.remain_stock > 0  order by hd.regdate asc ";
							$mdb->query($sql);
							if($mdb->total){
								$input_history = $mdb->fetchall("object");
								for($tmp_remain_stock=$detail_info[amount],$i=0;$tmp_remain_stock > 0;$i++){
									if($input_history[$i]["hd_ix"]){
										if(($tmp_remain_stock - $input_history[$i]["remain_stock"]) > 0){
											$tmp_remain_stock = $tmp_remain_stock - $input_history[$i]["remain_stock"];
											$update_stock = 0;
										}else{
											$update_stock = $input_history[$i]["remain_stock"]-$tmp_remain_stock;
											$tmp_remain_stock = 0;
										}

										$sql = "update inventory_history_detail set remain_stock = '".$update_stock."'  where hd_ix = '".$input_history[$i]["hd_ix"]."' ";
										$mdb->query($sql);
									}else{
										$tmp_remain_stock = 0;
									}
								}
							}
						}
					}


					$_stocked_detail_info = $stocked_info;
					
					if($detail_info[company_id]){
						$_stocked_detail_info[company_id] = $detail_info[company_id];
					}
					if($detail_info[pi_ix]){
						$_stocked_detail_info[pi_ix] = $detail_info[pi_ix];
					}
					if($detail_info[ps_ix]){
						$_stocked_detail_info[ps_ix] = $detail_info[ps_ix];
					}
					$_stocked_detail_info[gid] = $detail_info[gid];
					$_stocked_detail_info[unit] = $detail_info[unit];
					$_stocked_detail_info[standard] = $detail_info[standard];
					$_stocked_detail_info[amount] = $detail_info[amount];
					$_stocked_detail_info[price] = $detail_info[price];
					$_stocked_detail_info[expiry_date] = $detail_info[expiry_date];
					$_stocked_detail_info[gname] = $gname;
					//array_merge(, $detail_info);

					//$_stocked_detail_info = array_merge($stocked_info, $detail_info);
					//print_r($_stocked_detail_info);
					//exit;
					//$_stocked_detail_info[ps_ix] = "";

					if($_stocked_detail_info[amount] > 0){ // 재고가 있을경우 ...

						UpdateProductStockInfo($_stocked_detail_info, $mdb);
					}
				}
		
				/*
				$sql = "select * from inventory_product_stockinfo where vdate = '".date("Y-m-d")."' and pi_ix = '".$pi_ix."' and ps_ix = '".$ps_ix."' and gid = '".$gid."' and unit = '".$options[$i][unit]."' ";
				$mdb->query($sql);

				if($mdb->total){
						$mdb->fetch();
						$sql = "update inventory_product_stockinfo set
						stock = stock + ".$stocked_info[stocked_cnt]."
						where psi_ix = '".$mdb->dt[psi_ix]."'  ";
						//echo $sql;
						//exit;
						$mdb->query($sql);
				}else{

						$sql = "insert into inventory_product_stockinfo
									(psi_ix,vdate, ci_ix,pi_ix,ps_ix, gid,unit,stock_pcode,stock,exit_order,regdate)
									values
									('','".date("Y-m-d")."','".$ci_ix."','".$pi_ix."','".$ps_ix."','".$gid."','".$options[$i][unit]."','".$options[$i][item_code]."','".$stocked_info[stocked_cnt]."','1',NOW()) ";
						//echo $sql;
						//exit;
						$mdb->sequences = "INVENTORY_GOODS_INFO_SEQ";
						$mdb->query($sql);
				}
				*/

				//$item_info[unit] = $options[$i][unit];
				
			}
}

function UpdateGoodsItemNoStockInfo($stocked_info, $mdb=""){
	if(!$mdb){
		$mdb = new Database;
	}

	if($stocked_info[stocked_cnt] > 0 && $stocked_info[stocked_cnt] != "" || true){
		if($stocked_info[pi_ix] && $stocked_info[ps_ix] && $stocked_info[company_id]){
				if($stocked_info[pi_ix]){
					$sql = "select place_name, ccd.com_name
								from inventory_place_info pi 
								left join common_company_detail ccd on pi.company_id = ccd.company_id
								where pi.pi_ix = '".$stocked_info[pi_ix]."' ";
					$mdb->query($sql);
					$mdb->fetch();
					$company_id = $mdb->dt[company_id];
					$com_name = $mdb->dt[com_name];
					$place_name = $mdb->dt[place_name];
				}

				if($stocked_info[ps_ix]){
					$sql = "select * from inventory_place_section where ps_ix = '".$stocked_info[ps_ix]."' ";
					$mdb->query($sql);
					$mdb->fetch();
					$section_name = $mdb->dt[section_name];
				}

				if($stocked_info[company_id]){
					$sql = "select * from common_company_detail where company_id = '".$stocked_info[company_id]."' ";
					$mdb->query($sql);
					$mdb->fetch();
					$customer_name = $mdb->dt[com_name];
				}
				if($stocked_info[customer_name]){
					$customer_name = $stocked_info[customer_name];
				}
				
				$sql = "insert into inventory_history
							(h_ix,h_div,vdate,customer_name,com_name,place_name,section_name,company_id,ci_ix,pi_ix,ps_ix,ioid,oid,msg,h_type,charger_name,charger_ix,regdate) 
							values
							('','".$stocked_info[h_div]."','".$stocked_info[vdate]."','".$customer_name."','".$com_name."','".$place_name."','".$section_name."','".$stocked_info[company_id]."','".$stocked_info[ci_ix]."','".$stocked_info[pi_ix]."','".$stocked_info[ps_ix]."','".$stocked_info[ioid]."','".$stocked_info[oid]."','".$stocked_info[msg]."','".$stocked_info[h_type]."','".$stocked_info[charger_name]."','".$stocked_info[charger_ix]."',NOW()) ";
				
				$mdb->sequences = "INVENTORY_HISTORY_SEQ";
				$mdb->query($sql);

				if($mdb->dbms_type == "oracle"){
					$h_ix = $mdb->last_insert_id;
				}else{
					$mdb->query("SELECT h_ix FROM inventory_history WHERE h_ix=LAST_INSERT_ID()");
					$mdb->fetch();
					$h_ix = $mdb->dt[h_ix];
				}
		}
		
		$stocked_detail_info = $stocked_info[detail];

		foreach($stocked_detail_info as $detail_info){//20131022 Hong foreach로 변경
			// 마스터 정보에 창고 정보
			if(!$stocked_info[pi_ix] && !$stocked_info[ps_ix] && !$stocked_info[company_id]){
				if($detail_info[pi_ix]){
					$sql = "select place_name, ccd.com_name
								from inventory_place_info pi 
								left join common_company_detail ccd on pi.company_id = ccd.company_id
								where pi.pi_ix = '".$detail_info[pi_ix]."' ";
					$mdb->query($sql);
					$mdb->fetch();
					$company_id = $mdb->dt[company_id];
					$com_name = $mdb->dt[com_name];
					$place_name = $mdb->dt[place_name];
				}

				if($detail_info[ps_ix]){
					$sql = "select * from inventory_place_section where ps_ix = '".$detail_info[ps_ix]."' ";
					$mdb->query($sql);
					$mdb->fetch();
					$section_name = $mdb->dt[section_name];
				}

				if($detail_info[company_id]){
					$sql = "select * from common_company_detail where company_id = '".$detail_info[company_id]."' ";
					$mdb->query($sql);
					$mdb->fetch();
					$customer_name = $mdb->dt[com_name];
				}
				$sql = "insert into inventory_history
							(h_ix,h_div,vdate,customer_name,com_name,place_name,section_name,company_id,ci_ix,pi_ix,ps_ix,ioid,oid,msg,h_type,charger_name,charger_ix,regdate) 
							values
							('','".$stocked_info[h_div]."','".$stocked_info[vdate]."','".$customer_name."','".$com_name."','".$place_name."','".$section_name."','".$detail_info[company_id]."','".$stocked_info[ci_ix]."','".$detail_info[pi_ix]."','".$detail_info[ps_ix]."','".$stocked_info[ioid]."','".$stocked_info[oid]."','".$stocked_info[msg]."','".$stocked_info[h_type]."','".$stocked_info[charger_name]."','".$stocked_info[charger_ix]."',NOW()) ";
				$mdb->sequences = "INVENTORY_HISTORY_SEQ";
				$mdb->query($sql);

				if($mdb->dbms_type == "oracle"){
					$h_ix = $mdb->last_insert_id;
				}else{
					$mdb->query("SELECT h_ix FROM inventory_history WHERE h_ix=LAST_INSERT_ID()");
					$mdb->fetch();
					$h_ix = $mdb->dt[h_ix];
				}
			}

			$sql = "SELECT g.gname FROM inventory_goods g WHERE gid = '".$detail_info[gid]."' ";
			$mdb->query($sql);
			$mdb->fetch();
			$gname = $mdb->dt[gname];

			/* 입출고일 경우 평균단가, 판매단가,수량,최종판매가 inventory_history_detail 추가 2013-08-08 이학봉*/
			if($stocked_info[h_type] != "IW" && $stocked_info[h_type] != "OW"){ // 창고이동이 아닐경우...
				if($stocked_info[h_div] == "2"){

					$remain_stock = 0;

					// 출고일경우 현재 평균단가를 가져온다
					$sql = "select avg_price from inventory_goods_unit where gid = '".$detail_info[gid]."' and unit = '".$detail_info[unit]."' ";
					$mdb->query($sql);
					$mdb->fetch();
					$avg_price = $mdb->dt[avg_price];
				}else{
					
					$remain_stock = $detail_info[amount];

					// 입고일때 재고자산가지 이동평균법 처리 : (누적입고금액+현재입금금액/누적수량+현입고수량) = 현재고자산단가
					$sql = "select 
								total_stock,
								avg_price, 
								total_stock*avg_price as asset_price 
							from inventory_goods_unit 
							where gid = '".$detail_info[gid]."' and unit = '".$detail_info[unit]."' ";
					$mdb->query($sql);
					
					if($mdb->total){
						$mdb->fetch();
						$assets_info = $mdb->dt;

						if($stocked_info[oid]){

							// 반품, 환불 입고처리의 경우 출고리스트의 평균단가로 다시 입고단가를 계산한다.

							$sql = "select
										avg_price
									from
										inventory_history as h
										inner join inventory_history_detail as hd on (h.h_ix = hd.h_ix and h.oid = '".$stocked_info[oid]."')
									where
										hd.gid = '".$detail_info[gid]."'
										and hd.unit = '".$detail_info[unit]."'";
							$mdb->query($sql);
							$mdb->fetch();
							$output_avg_price = $mdb->dt[avg_price];

							if(abs($assets_info[total_stock]+$detail_info[amount]) > 0){
								$avg_price = ($assets_info[asset_price]+($detail_info[amount]*$output_avg_price))/(abs($assets_info[total_stock]+$detail_info[amount]));		//
							}else{
								$avg_price = '0';	// 없을경우 평균단가는 0
							}
								
						}else{
							if(abs($assets_info[total_stock]+$detail_info[amount]) > 0){
								$avg_price = ($assets_info[asset_price]+($detail_info[amount]*$detail_info[price]))/(abs($assets_info[total_stock]+$detail_info[amount]));
							}
						}

						if($avg_price=="") $avg_price=0;

					}else{
						$avg_price = '0';	// 없을경우 평균단가는 0
					}
					
				}
			}
			/* 입출고일 경우 평균단가, 판매단가,수량,최종판매가 inventory_history_detail 추가 2013-08-08 이학봉*/
			
			//20131128 Hong inventory_history_detail 에 ptprice 입력하기 위한 주문쪽데이터와 비교하기 위한 구분값 
			if($stocked_info["act_from"] == "inventory"){
				$ptprice = $detail_info[price] *  $detail_info[amount];
			}else{
				//$ptprice = $detail_info[ptprice] - $detail_info[use_coupon];
				$ptprice = $detail_info[pt_dcprice];
			}

			$sql = "insert into inventory_history_detail
						(hd_ix,h_ix,gid,unit,gname,standard,amount,price,expiry_date,regdate,avg_price,ptprice,remain_stock)
						values
						('','".$h_ix."','".$detail_info[gid]."','".$detail_info[unit]."','".$gname."','".$detail_info[standard]."','".$detail_info[amount]."','".$detail_info[price]."','".$detail_info[expiry_date]."',NOW(),'".$avg_price."','".$ptprice."','".$remain_stock."') ";

			$mdb->sequences = "INVENTORY_HISTORY_DT_SEQ";
			$mdb->query($sql);

			//2014-04-11 출고일때 입고된 history 의 remain_stock 차감하기
			if($stocked_info[h_type] != "IW" && $stocked_info[h_type] != "OW"){
				if($stocked_info[h_div] == "2"){
					$sql = "select hd.hd_ix,hd.remain_stock from inventory_history h , inventory_history_detail hd where h.h_ix=hd.h_ix and h.h_div='1' and hd.gid='".$detail_info[gid]."' and hd.unit='".$detail_info[unit]."' and  hd.remain_stock > 0  order by hd.regdate asc ";
					$mdb->query($sql);
					if($mdb->total){
						$input_history = $mdb->fetchall("object");
						for($tmp_remain_stock=$detail_info[amount],$i=0;$tmp_remain_stock > 0;$i++){
							if($input_history[$i]["hd_ix"]){
								if(($tmp_remain_stock - $input_history[$i]["remain_stock"]) > 0){
									$tmp_remain_stock = $tmp_remain_stock - $input_history[$i]["remain_stock"];
									$update_stock = 0;
								}else{
									$update_stock = $input_history[$i]["remain_stock"]-$tmp_remain_stock;
									$tmp_remain_stock = 0;
								}

								$sql = "update inventory_history_detail set remain_stock = '".$update_stock."'  where hd_ix = '".$input_history[$i]["hd_ix"]."' ";
								$mdb->query($sql);
							}else{
								$tmp_remain_stock = 0;
							}
						}
					}
				}
			}


			$_stocked_detail_info = $stocked_info;
			
			if($detail_info[company_id]){
				$_stocked_detail_info[company_id] = $detail_info[company_id];
			}
			if($detail_info[pi_ix]){
				$_stocked_detail_info[pi_ix] = $detail_info[pi_ix];
			}
			if($detail_info[ps_ix]){
				$_stocked_detail_info[ps_ix] = $detail_info[ps_ix];
			}
			$_stocked_detail_info[gid] = $detail_info[gid];
			$_stocked_detail_info[unit] = $detail_info[unit];
			$_stocked_detail_info[standard] = $detail_info[standard];
			$_stocked_detail_info[amount] = $detail_info[amount];
			$_stocked_detail_info[price] = $detail_info[price];
			$_stocked_detail_info[expiry_date] = $detail_info[expiry_date];
			$_stocked_detail_info[gname] = $gname;

			if($_stocked_detail_info[amount] > 0){ // 재고가 있을경우 ...

				//UpdateProductStockInfo($_stocked_detail_info, $mdb);
			}
		}
	}
}

function UpdateProductStockInfo($item_info, $mdb=''){
	if(!$mdb){
		$mdb = new Database;
	} 
	//$mdb->debug = true;
	//print_r($item_info);
	if($item_info[h_div] == "2"){
		// 출고일때는 입고일 기준 셀렉트는 제외
		$sql = "select * from inventory_product_stockinfo 
				where 1  "; //stock > 0

		if($item_info[expiry_date]){
			$sql .= "and expiry_date = '".$item_info[expiry_date]."' ";
		}
		if($item_info[pi_ix]){
			$sql .= "and pi_ix = '".$item_info[pi_ix]."' ";
		}
		if($item_info[ps_ix]){
			$sql .= "and ps_ix = '".$item_info[ps_ix]."' ";
		}
		$sql .= "and gid = '".$item_info[gid]."' 
				and unit = '".$item_info[unit]."' 
				order by expiry_date asc, vdate asc ";
				// 현재행의 재고가 전체 출고 수량보다 적을때는 차례로 다음 재고정보에서 차감하는 로직 추가필요
		
		$first_stock=0;
	}else{
		
		$sql = "select * from inventory_product_stockinfo 
				where gid = '".$item_info[gid]."' ";
		$mdb->query($sql);
		if(!$mdb->total)
				$first_stock=$item_info[amount];
		else
				$first_stock=0;

		$sql = "select * from inventory_product_stockinfo 
				where expiry_date = '".$item_info[expiry_date]."' 
				and company_id = '".$item_info[company_id]."' 
				and pi_ix = '".$item_info[pi_ix]."' 
				and ps_ix = '".$item_info[ps_ix]."' 
				and gid = '".$item_info[gid]."' 
				and unit = '".$item_info[unit]."' 
				order by expiry_date asc, vdate asc limit 1";
				//vdate = '".$item_info[vdate]."' 				and 
	}

	$mdb->query($sql);

	if($mdb->total){
		//echo "amount1:".$item_info[amount]."<br>";
			$now_stock_infos = $mdb->fetchall();
			//print_r($item_info);
			if($item_info[h_div] == "2"){// 출고
				$amount = $item_info[amount]*(-1);
				for($j=0; $j < count($now_stock_infos);$j++){

						$sql = "update inventory_product_stockinfo set stock = stock + ".$amount." , total_out_stock = total_out_stock + '".$item_info[amount]."'
									where psi_ix = '".$now_stock_infos[$j][psi_ix]."'  ";
						$mdb->query($sql);
				}
			}else{ // 입고
				$amount = $item_info[amount];
				
				for($j=0; $j < count($now_stock_infos);$j++){
					$sql = "update inventory_product_stockinfo set stock = stock + ".$amount." , total_in_stock = total_in_stock + '".$item_info[amount]."'
								where psi_ix = '".$now_stock_infos[$j][psi_ix]."'  ";
					//echo $sql;
					//exit;
					$mdb->query($sql);
				}
			}
	}else{
			if($item_info[h_div] == "2"){
				$amount = $item_info[amount]*(-1);
				$total_out_stock = $item_info[amount];
				$total_in_stock = 0;
			}else{
				$amount = $item_info[amount];
				$total_out_stock = 0;
				$total_in_stock = $item_info[amount];
			}
			$sql = "insert into inventory_product_stockinfo
						(psi_ix,vdate, expiry_date, company_id,pi_ix,ps_ix, gid,unit,stock_pcode,stock,exit_order,first_stock,total_in_stock,total_out_stock,regdate)
						values
						('','".$item_info[vdate]."','".$item_info[expiry_date]."','".$item_info[company_id]."','".$item_info[pi_ix]."','".$item_info[ps_ix]."','".$item_info[gid]."','".$item_info[unit]."','','".$amount."','1','".$first_stock."','".$total_in_stock."','".$total_out_stock."',NOW()) ";
			//echo $sql;
			//exit;
			$mdb->sequences = "INVENTORY_GOODS_INFO_SEQ";
			$mdb->query($sql);
			
/*
			$sql = "select total_stock, avg_price, total_stock*avg_price as asset_price  from inventory_goods_unit where gid = '".$item_info[gid]."' and unit = '".$item_info[unit]."' ";
			$mdb->query($sql);
			

			if($mdb->total){
				$mdb->fetch();
				$assets_info = $mdb->dt;
				echo "(".$assets_info[asset_price]."+".$amount."*".$item_info[price].")/(".$assets_info[total_stock]."+".$amount.")<br>";

				$now_assets_price = ($assets_info[asset_price]+($amount*$item_info[price]))/($assets_info[total_stock]+$amount);

				$sql = "update inventory_goods_unit set total_stock = total_stock + ".$amount.", avg_price = ".$now_assets_price."
							where gid = '".$item_info[gid]."' and unit = '".$item_info[unit]."'  ";
				//echo $sql;
				//exit;
				$mdb->query($sql);
			}
*/
	}


	if($item_info[h_type] != "IW" && $item_info[h_type] != "OW"){ // 창고이동이 아닐경우...
		if($item_info[h_div] == "2"){
			// 출고일경우 재고자산 가치 이동평균법 계산 : 단위 자산가치는 변동이 없으므로 수량변경만 해준다.
			$sql = "update inventory_goods_unit set total_stock = total_stock + ".$amount."
						where gid = '".$item_info[gid]."' and unit = '".$item_info[unit]."'  ";
			//echo $sql;
			//exit;
			$mdb->query($sql);
		}else{

			// 입고일때 재고자산가지 이동평균법 처리 : (누적입고금액+현재입금금액/누적수량+현입고수량) = 현재고자산단가

			$sql = "select total_stock, avg_price, total_stock*avg_price as asset_price  from inventory_goods_unit where gid = '".$item_info[gid]."' and unit = '".$item_info[unit]."' ";
			$mdb->query($sql);
			

			if($mdb->total){
				$mdb->fetch();
				$assets_info = $mdb->dt;

				if($item_info[oid]){

						// 반품, 환불 입고처리의 경우 출고리스트의 평균단가로 다시 입고단가를 계산한다.

						$sql = "select
									avg_price
								from
									inventory_history as h
									inner join inventory_history_detail as hd on (h.h_ix = hd.h_ix and h.oid = '".$item_info[oid]."')
								where
									hd.gid = '".$item_info[gid]."'
									and hd.unit = '".$item_info[unit]."'";
						$mdb->query($sql);
						$mdb->fetch();
						$output_avg_price = $mdb->dt[avg_price];
					if(abs($assets_info[total_stock]+$amount) > 0){
						$now_assets_price = ($assets_info[asset_price]+($amount*$output_avg_price))/(abs($assets_info[total_stock]+$amount));		//
					}else{
						$now_assets_price = '0';
					}
					
				}else{
					if(abs($assets_info[total_stock]+$amount) > 0){
						$now_assets_price = ($assets_info[asset_price]+($amount*$item_info[price]))/(abs($assets_info[total_stock]+$amount));
					}else{
						$now_assets_price = '0';
					}
				}

				//$now_assets_price = ($assets_info[asset_price]+($amount*$item_info[price]))/($assets_info[total_stock]+$amount);
				//echo "now_assets_price:".$now_assets_price;

				if($now_assets_price=="") $now_assets_price=0;//$now_assets_price가 빈 값일 경우 쿼리 에러 발생하므로 0 처리 kbk 13/08/04
				$sql = "update inventory_goods_unit set total_stock = total_stock + ".$amount.", avg_price = ".$now_assets_price."
							where gid = '".$item_info[gid]."' and unit = '".$item_info[unit]."'  ";
				//echo $sql;
				//exit;
				$mdb->query($sql);
			}
		}
	
	//exit;

		$sql = "select gu.gu_ix, gu.safestock, sum(ps.stock) as stock
					from inventory_goods_unit gu  left join inventory_product_stockinfo ps on ps.unit = gu.unit
					left join  inventory_place_info pi on ps.pi_ix = pi.pi_ix
					where ps.gid = '".$item_info[gid]."' and  ps.unit = '".$item_info[unit]."' and pi.online_place_yn = 'Y'
					group by gu.gu_ix ,  gu.safestock ";

		$mdb->query($sql);
		$mdb->fetch();

		$item_stock_sum = $mdb->dt[stock];
		$item_safestock = $mdb->dt[safestock];

		if($item_stock_sum == ""){
			$item_stock_sum = 0;
		}

		if($item_safestock == ""){
			$item_safestock = 0;
		}

		if($item_stock_sum > 0){
			$stock_update = " , option_stock_yn = 'Y' ";
		}else if($item_stock_sum <= 0 ){
			$stock_update = " , option_stock_yn = 'N' ";
		}else if($item_stock_sum < $item_safestock){
			$stock_update = " , option_stock_yn = 'R' ";
		}
	/*
		$sql = "update inventory_goods_unit set
					item_stock = ".$item_stock_sum."
					where gu_ix = '".$item_info[gu_ix]."' ";
		$mdb->query($sql);
	*/

		if($item_info[gid] && $item_info[unit]){

			$sql = "select gu_ix from inventory_goods_unit
					where gid = '".$item_info[gid]."' and unit = '".$item_info[unit]."' ";

			$mdb->query($sql);

			if($mdb->total){
				$mdb->fetch();
				$gu_ix = $mdb->dt[gu_ix];
			}else{
				echo "품목정보와 단위정보가 정확하지 않습니다. ";
				return;
				//exit;
			}

			//옵션테이블에 각옵션별 재고 업데이트
			$sql = "select 
                        od.id as opnd_ix ,pid, p.product_type 
                    from 
                      ".TBL_SHOP_PRODUCT." p 
                    inner join 
                        ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." od on (p.id=od.pid) 
                    where p.stock_use_yn='Y' and option_code = '".$gu_ix."' AND auto_sync_wms='Y' and product_type != '77' 
                    union 
                    select 
                        '' as opnd_ix , p.id, p.product_type
                    from 
                       ".TBL_SHOP_PRODUCT." p
                    where 
                        p.stock_use_yn='Y' and pcode = '".$gu_ix."' AND auto_sync_wms='Y' and product_type = '77'
                    ";
			//syslog(1,$sql);
			$mdb->query($sql);

			if($mdb->total){

				$option_dt_info = $mdb->fetchall("object");

				for($j=0;$j<count($option_dt_info);$j++){

				    if($option_dt_info[$j]['product_type'] != '77') {


                        $sql = "update " . TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " set
								option_stock = " . $item_stock_sum . "
								where id = '" . $option_dt_info[$j][opnd_ix] . "'   ";

                        $mdb->query($sql);

                        $sql = "update shop_product_options_detail_global set
								option_stock = " . $item_stock_sum . "
								where id = '" . $option_dt_info[$j][opnd_ix] . "'   ";

                        $mdb->query($sql);

                        //, sum(option_sell_ing_cnt) as option_sell_ing_cnt
                        $sql = "SELECT o.pid, sum(option_stock) as option_stock 
								FROM " . TBL_SHOP_PRODUCT_OPTIONS . " o , " . TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " od
								WHERE od.pid='" . $option_dt_info[$j][pid] . "' and o.opn_ix = od.opn_ix
								and option_kind in ('a','b','x','x2','s2') and od.option_soldout != '1' group by o.pid ";
                        //option_kind = 'b' => option_kind in ('b','x','x2','s2')
                        $mdb->query($sql);
                        if ($mdb->total) {
                            $mdb->fetch();
                            $goods_stock = $mdb->dt[option_stock];
                        } else {
                            //세트 상품일때 품목별 재고 상품 업데이트 방법 처리
                            $sql = "select sum(stock) as option_stock 
                                    from inventory_product_stockinfo where gid in (
                                    SELECT od.option_gid 
                                    FROM " . TBL_SHOP_PRODUCT_OPTIONS . " o , " . TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " od
                                    WHERE od.pid='" . $option_dt_info[$j][pid] . "' and o.opn_ix = od.opn_ix
                                    and option_soldout != '1'
                                    and option_kind in ('c')
                                    group by od.option_gid
								)
								";
                            $mdb->query($sql);
                            $mdb->fetch();
                            $goods_stock = $mdb->dt[option_stock];
                        }
                    }else{
                        $goods_stock = $item_stock_sum;
                    }



					//$goods_sell_ing_cnt = $mdb->dt[option_sell_ing_cnt];

					/*
					if($goods_stock > 0){
						$option_stock_yn = "Y";
					}else{
						$option_stock_yn = "N";
					}
					*/

					//동일 상품으로 여러번 업데이트는 될 수 있으나 현재 여러번 업데이트 될 경우 문제점을 찾지 못하여 따로 중복에 대한
                    //업데이트 방지 처리는 하지 않았으며 추후 필요에의해 동일 상품 id 를 기준으로 업데이트 시 예외처리 한다. JK191213
					$mdb->query("update ".TBL_SHOP_PRODUCT." set stock = '".$goods_stock."' $stock_update  where id ='".$option_dt_info[$j][pid]."'");

                    $mdb->query("update shop_product_global set stock = '".$goods_stock."' $stock_update  where id ='".$option_dt_info[$j][pid]."'");
				}
			}

			//상품에 물려있는 품목코드 수량 업데이트
            //아래 코드는 현재 pcode 를 gu_ix 로 사용하지 않고 있기 때문에 불필요 코드로 판단되며 실제 상품의 옵션재고 업데이트는 1554 line 의 query 로 처리한다.JK191213
//			$sql = "update  ".TBL_SHOP_PRODUCT." set stock = '".$item_stock_sum."' where pcode = '".$gu_ix."' and stock_use_yn='Y' ";
//			$mdb->query($sql);
//
//			$sql = "update shop_product_global set stock = '".$item_stock_sum."' where pcode = '".$gu_ix."' and stock_use_yn='Y' ";
//			$mdb->query($sql);
		}
		
//		if($gu_ix){
//			$sql="select od_ix, pcnt from shop_order_detail  where gu_ix = '".$gu_ix."' and status in ('IR','IC','DR','DD') order by regdate asc";
//			$mdb->query($sql);
//			if($mdb->total){
//				$od_info = $mdb->fetchall("object");
//
//				$real_lack_stock = $item_stock_sum;
//
//				for($j=0;$j<count($od_info);$j++){
//					$real_lack_stock -= $od_info[$j][pcnt];
//					$sql="update shop_order_detail set real_lack_stock='".$real_lack_stock."' where od_ix='".$od_info[$j][od_ix]."' ";
//					$mdb->query($sql);
//				}
//			}
//		}
	} //창고이동이 아닐경우

	///exit;

}

function UpdateSellingCnt($order_info){ // 배송전 취소완료일떄쓰는 함수!!

	$mdb = new Database;

	if($order_info[stock_use_yn] == "Y"){//WMS 재고관리 처리 따로 분리 20130821 Hong
		if($order_info[gu_ix]){

			$mdb->query("select sum(pcnt) as sell_ing_cnt from shop_order_detail  where gu_ix = '".$order_info[gu_ix]."' and status in ('IR','IC','DR','DD')");
			$mdb->fetch();
			$sell_ing_cnt = $mdb->dt[sell_ing_cnt];

			$sql = "select od.id as opnd_ix ,pid from ".TBL_SHOP_PRODUCT." p inner join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." od on (p.id=od.pid) where p.stock_use_yn='Y' and option_code = '".$order_info[gu_ix]."' ";
			$mdb->query($sql);
			if($mdb->total){
				$option_dt_info = $mdb->fetchall();
				for($j=0;$j<count($option_dt_info);$j++){
					$mdb->query("update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set option_sell_ing_cnt = '".$sell_ing_cnt."' where id = '".$option_dt_info[$j][opnd_ix]."' ");
				}

				$mdb->query("select sum(pcnt) as sell_ing_cnt from shop_order_detail  where pid = '".$order_info[pid]."' and status in ('IR','IC','DR','DD')");
				$mdb->fetch();
				$p_sell_ing_cnt = $mdb->dt[sell_ing_cnt];

				$mdb->query("UPDATE ".TBL_SHOP_PRODUCT."  Set sell_ing_cnt = '".$p_sell_ing_cnt."'  WHERE id = '".$order_info[pid]."'");
			}

			$mdb->query("update ".TBL_SHOP_PRODUCT." set sell_ing_cnt = '".$sell_ing_cnt."' where pcode ='".$order_info[gu_ix]."' and stock_use_yn='Y' ");

			$mdb->query("update inventory_goods_unit set sell_ing_cnt = '".$sell_ing_cnt."' where gu_ix = '".$order_info[gu_ix]."' ");
		}
	}elseif($order_info[stock_use_yn] == "Q"){//빠른 재고관리 사용 추가 $order_info[stock_use_yn] == "Q" kbk 13/06/20
		if($order_info[option_id] != 0){
			// 옵션 코드값이 있는 경우에 옵션이 가격/재고 관리 옵션인지 'b' 판단해서  가격/재고 관리 옵션일경우 옵션의 재고수량도 증가시켜준다
			$mdb->query("select po.id, pos.pid, pos.opn_ix, option_stock,option_safestock,po.option_div,pos.option_name from  ".TBL_SHOP_PRODUCT_OPTIONS." pos, ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po  WHERE id = '".$order_info[option_id]."' and pos.opn_ix = po.opn_ix and pos.option_kind in ('a','b','x','x2','s2') ");// pos.pid, 추가 kbk 13/06/20
			if($mdb->total){
				$mdb->fetch();
				$pod_id = $mdb->dt[id];
				
				$mdb->query("select sum(pcnt) as sell_ing_cnt from shop_order_detail  where option_id = '".$order_info[option_id]."' and status in ('IR','IC','DR','DD')");
				$mdb->fetch();
				$sell_ing_cnt = $mdb->dt[sell_ing_cnt];

				$mdb->query("UPDATE ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL."  Set option_sell_ing_cnt = '".$sell_ing_cnt."'  WHERE id = '".$pod_id."'");

				//주문취소시 판매진행중 재고만 감소하기 때문에 실재 재고는 변경이 없다. 따라서 재고여부도 변경되지 않는다.
				//$mdb->query("UPDATE ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL."  Set option_sell_ing_cnt = option_sell_ing_cnt - ".$order_info[pcnt]."  WHERE id = '".$mdb->dt[id]."'");
			}
		}

		$mdb->query("select sum(pcnt) as sell_ing_cnt from shop_order_detail  where pid = '".$order_info[pid]."' and status in ('IR','IC','DR','DD')");
		$mdb->fetch();
		$sell_ing_cnt = $mdb->dt[sell_ing_cnt];

		$mdb->query("UPDATE ".TBL_SHOP_PRODUCT."  Set sell_ing_cnt = '".$sell_ing_cnt."'  WHERE id = '".$order_info[pid]."'");

	}
}

function UpdateProductCnt_complete($order_info) {

	//빠른 재고 관리 전용 주문진행 중 수량 차감 kbk 13/06/20 배송후! 
	//WMS (판매진행재고), 빠른재고관리 (재고,판매진행재고) 업데이트 20130821 Hong 
	$mdb = new Database;
	
	if($order_info[stock_use_yn] == "Y"){//WMS 재고관리 처리 따로 분리 20130821 Hong
		if($order_info[gu_ix]){

			$mdb->query("select sum(pcnt) as sell_ing_cnt from shop_order_detail  where gu_ix = '".$order_info[gu_ix]."' and status in ('IR','IC','DR','DD')");
			$mdb->fetch();
			$sell_ing_cnt = $mdb->dt[sell_ing_cnt];
			
			//WMS 재고관리 UpdateProductStockInfo () 함수안에 stock update 있음
			$sql = "select od.id as opnd_ix ,pid from ".TBL_SHOP_PRODUCT." p inner join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." od on (p.id=od.pid) where p.stock_use_yn='Y' and option_code = '".$order_info[gu_ix]."' ";
			$mdb->query($sql);
			if($mdb->total){
				$option_dt_info = $mdb->fetchall();
				for($j=0;$j<count($option_dt_info);$j++){
					$mdb->query("update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set option_sell_ing_cnt = '".$sell_ing_cnt."' where id = '".$option_dt_info[$j][opnd_ix]."' ");
				}

				$mdb->query("select sum(pcnt) as sell_ing_cnt from shop_order_detail  where pid = '".$order_info[pid]."' and status in ('IR','IC','DR','DD')");
				$mdb->fetch();
				$p_sell_ing_cnt = $mdb->dt[sell_ing_cnt];

				$mdb->query("UPDATE ".TBL_SHOP_PRODUCT."  Set sell_ing_cnt = '".$p_sell_ing_cnt."'  WHERE id = '".$order_info[pid]."'");
			}
			
			$mdb->query("update ".TBL_SHOP_PRODUCT." set sell_ing_cnt = '".$sell_ing_cnt."' where pcode ='".$order_info[gu_ix]."' and stock_use_yn='Y' ");

			$mdb->query("update inventory_goods_unit set sell_ing_cnt = '".$sell_ing_cnt."' where gu_ix = '".$order_info[gu_ix]."' ");
		}
	}elseif($order_info[stock_use_yn] == "Q"){

		if($order_info["option_id"] != 0){
			// 옵션 코드값이 있는 경우에 옵션이 가격/재고 관리 옵션인지 'b' 판단해서  가격/재고 관리 옵션일경우 옵션의 재고수량도 증가시켜준다
			$mdb->query("select po.id, pos.pid, pos.opn_ix, option_stock,option_safestock,po.option_div,pos.option_name from  ".TBL_SHOP_PRODUCT_OPTIONS." pos, ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po  WHERE id = '".$order_info[option_id]."' and pos.opn_ix = po.opn_ix and pos.option_kind in ('a','b','x','x2','s2') ");// pos.pid, 추가 kbk 13/06/20
			if($mdb->total){

				$mdb->query("select sum(pcnt) as sell_ing_cnt from shop_order_detail  where option_id = '".$order_info[option_id]."' and status in ('IR','IC','DR','DD')");
				$mdb->fetch();
				$sell_ing_cnt = $mdb->dt[sell_ing_cnt];
				
				$mdb->query("update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set option_sell_ing_cnt = '".$sell_ing_cnt."' , option_stock = option_stock - ".$order_info["pcnt"]."  where pid = '".$order_info["pid"]."' and id ='".$order_info["option_id"]."' ");
			}

			$mdb->query("SELECT option_stock , option_safestock FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE pid = '".$order_info["pid"]."' and id ='".$order_info["option_id"]."' ");
			if($mdb->total){
				$mdb->fetch();

				if($mdb->dt[option_stock] <= 0){
					$mdb->query("update ".TBL_SHOP_PRODUCT." set option_stock_yn = 'N' where id ='".$order_info["pid"]."'"); // 품절
				}else if($mdb->dt[option_stock] < $mdb->dt[option_safestock]){
					$mdb->query("update ".TBL_SHOP_PRODUCT." set option_stock_yn = 'R' where id ='".$order_info["pid"]."'");  // 재고 부족
				}
			}
		}

		if($order_info["di_date"] == ""||$order_info["di_date"] == "0000-00-00 00:00:00"){

			$mdb->query("select sum(pcnt) as sell_ing_cnt from shop_order_detail  where pid = '".$order_info[pid]."' and status in ('IR','IC','DR','DD')");
			$mdb->fetch();
			$sell_ing_cnt = $mdb->dt[sell_ing_cnt];
			
			$mdb->query("UPDATE ".TBL_SHOP_PRODUCT."  Set sell_ing_cnt = '".$sell_ing_cnt."' , stock = stock - ".$order_info["pcnt"]."  WHERE id = '".$order_info["pid"]."'");
		}

	}
}

function UpdateProductCnt_cancel($order_info) {
	//빠른 재고 관리 전용 주문진행 중 수량 증가 kbk 13/06/20 배송후!
	//WMS 는 반품 회수완료 , 교환회수완료 라서 UpdateProductStockInfo 안에서 처리함  20130821 Hong 

	$mdb = new Database;
	if($order_info[stock_use_yn] == "Q"){
		if($order_info["option_id"] != 0){
			// 옵션 코드값이 있는 경우에 옵션이 가격/재고 관리 옵션인지 'b' 판단해서  가격/재고 관리 옵션일경우 옵션의 재고수량도 증가시켜준다
			$mdb->query("select po.id,option_stock,option_safestock from  ".TBL_SHOP_PRODUCT_OPTIONS." pos, ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po  WHERE id = '".$order_info["option_id"]."' and pos.opn_ix = po.opn_ix and pos.option_kind in ('a','b','x','x2','s2') ");
			if($mdb->total){
				$mdb->fetch();
				$mdb->query("UPDATE ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL."  Set option_stock = option_stock + ".$order_info["pcnt"]." WHERE id = '".$mdb->dt[id]."'");

				if(($mdb->dt[option_stock] + $order_info["pcnt"]) <= 0){
					$stock_update = " , option_stock_yn = 'N' "; // 품절
				}else if(($mdb->dt[option_stock]  + $order_info["pcnt"] ) < $mdb->dt[option_safestock]){
					$stock_update = " , option_stock_yn = 'R' ";  // 여유
				}
			}
		}
		$mdb->query("UPDATE ".TBL_SHOP_PRODUCT."  set stock = stock + ".$order_info["pcnt"]." $stock_update WHERE id = '".$order_info["pid"]."'");
	}
}


function getItemAccount($selected="", $property="", $return_type = "selectbox"){
	global $ITEM_ACCOUNT;
	if($return_type == "selectbox"){
		$mstring = "<select name='item_account' ".$property." style='min-width:140px;' ".$selected.">
							<option value=''>상품분류</option>";


		foreach($ITEM_ACCOUNT as $key => $value){
			$mstring .= "<option value='".$key."' ".($selected == $key ? "selected":"").">".$value."</option>";
		}
			$mstring .= "</select>";
	}else{
			$mstring = $ITEM_ACCOUNT[$selected];
	}
	
	return $mstring;
}



function getUnit($selected="", $obj_name="", $property="", $return_type = "selectbox"){
	global $ITEM_UNIT;
	if($return_type == "selectbox"){
		$mstring = "<select name='$obj_name'   ".$property.">
							<option value=''>단위</option>";


		foreach($ITEM_UNIT as $key => $value){
			$mstring .= "<option value='".$key."' ".($selected == $key ? "selected":"").">".$value."</option>";
		}
			$mstring .= "</select>";
	}else{
			$mstring = $ITEM_UNIT[$selected];
	}
	
	return $mstring;
}

function getSurTaxDiv($selected="", $obj_name="", $property="", $return_type = "selectbox"){
	global $SURTAX_DIV;
	if($return_type == "selectbox"){
		$mstring = "<select name='$obj_name'   ".$property.">
							<option value=''>부가세 선택</option>";


		foreach($SURTAX_DIV as $key => $value){
			$mstring .= "<option value='".$key."' ".($selected == $key ? "selected":"").">".$value."</option>";
		}
			$mstring .= "</select>";
	}else{
			$mstring = $SURTAX_DIV[$selected];
	}
	
	return $mstring;
}


function getTypeDiv($type, $selected="", $obj_name="", $property="", $return_type = "selectbox"){
	global $TYPE_DIV;
	if($return_type == "selectbox"){
		$mstring = "<select name='$obj_name'   ".$property.">
							<option value=''>".($type == "I" ? "입고유형":"출고유형")."</option>";


		foreach($TYPE_DIV[$type] as $key => $value){
			$mstring .= "<option value='".$key."' ".($selected == $key ? "selected":"").">".$value."</option>";
		}
			$mstring .= "</select>";
	}else if($return_type == "checkbox"){
		if(is_array($TYPE_DIV[$type])){
			foreach($TYPE_DIV[$type] as $key => $value){
				$mstring .= "<input type='checkbox' class='type_div' name='type_div[]' ".(substr_count(" ".$selected,$key) ? "checked":"")." id='type_div_".$key."' value='".$key."'><label for='type_div_".$key."'>".$value."</label>";
			}
		}
	}else{
		//echo $selected;
		$type_divs = explode("|",$selected);
		if(is_array($type_divs)){
			
			//print_r($type_divs);
			for($i=0;$i < count($type_divs);$i++){
				//echo $type_divs[$i];
				if($i==0){
					$mstring = $TYPE_DIV[$type][$type_divs[$i]];
				}else{
					$mstring .= ",".$TYPE_DIV[$type][$type_divs[$i]];
				}
			}
		}else{
			$mstring = $TYPE_DIV[$type][$selected];
		}
	}
	
	return $mstring;
}


function getInventoryStatus($selected="", $obj_name="", $property="", $return_type = "selectbox"){
	global $INVENTORY_STATUS,$status;//$selected가 ALL 인 경우에 사용하기 위해 $status 추가 kbk 13/08/08
	
	if($selected == ""){
		$allowable_status = array("MA");
		$selected = "MA";
	}else if($selected == "MA"){
		$allowable_status = array("MA","MO","MR");
	}else if($selected == "MO"){
		$allowable_status = array("MO","MI","MR");//,"MC"
	}else if($selected == "MI"){
		$allowable_status = array("MI","MC");
	}else if($selected == "MC"){
		$allowable_status = array("MC");
	}else if($selected == "MR"){
		$allowable_status = array("MR");
	}else if($selected == "ALL"){
		$allowable_status = array("MA","AC","MO","MI","MC","MR");
	}
	/*
	$INVENTORY_STATUS["MA"] = "이동요청";
	$INVENTORY_STATUS["MO"] = "이동출고";
	$INVENTORY_STATUS["MI"] = "이동중";
	$INVENTORY_STATUS["MC"] = "이동입고";
	*/

	if($return_type == "selectbox"){
		$mstring = "<select name='$obj_name'   ".$property.">
							<option value=''>창고이동 처리상태</option>";


		foreach($INVENTORY_STATUS as $key => $value){
			if(in_array($key,$allowable_status)){
				if($selected == "ALL") $selected=$status;
				$mstring .= "<option value='".$key."' ".($selected == $key ? "selected":"").">".$value."</option>";
			}
		}
			$mstring .= "</select>";
	}elseif($return_type == "checkbox"){
		foreach($INVENTORY_STATUS as $key => $value){
			if(in_array($key,$allowable_status)){
				$mstring .= "<input type='checkbox' name='".$obj_name."[]' id='".$obj_name."_".$key."' value='".$key."' ".(count($_GET[$obj_name]) && in_array($key,$_GET[$obj_name]) ? "checked":"")." /><label for='".$obj_name."_".$key."' >".$value."</label> ";
			}
		}
	}else{
			$mstring = $INVENTORY_STATUS[$selected];
	}
	
	return $mstring;
}


function getDeliveryStatus($selected="", $obj_name="", $property="", $return_type = "selectbox"){
	global $DELIVERY_STATUS;
	/*
	if($selected == ""){
		$allowable_status = array("MA");
		$selected = "MA";
	}else if($selected == "MA"){
		$allowable_status = array("MA","MO","MR");
	}else if($selected == "MO"){
		$allowable_status = array("MO","MI","MR");//,"MC"
	}else if($selected == "MI"){
		$allowable_status = array("MI","MC");
	}else if($selected == "MC"){
		$allowable_status = array("MC");
	}else if($selected == "ALL"){
		$allowable_status = array("MA","MO","MI","MC","MR");
	}
	*/
	$allowable_status = array("WDA","WDW","WDR","WDC");

	if($return_type == "selectbox"){
		$mstring = "<select name='$obj_name'   ".$property.">
							<option value=''>출고상태</option>";


		foreach($DELIVERY_STATUS as $key => $value){
			if(in_array($key,$allowable_status)){
				$mstring .= "<option value='".$key."' ".($selected == $key ? "selected":"").">".$value."</option>";
			}
		}
			$mstring .= "</select>";
	}else{
			$mstring = $DELIVERY_STATUS[$selected];
	}
	
	return $mstring;
}


/*
function getDeliveryMethod($selected="", $obj_name="", $property="", $return_type = "selectbox"){
	global $DELIVERY_METHOD;
	
	//if($selected == ""){
	//	$allowable_status = array("MA");
	//	$selected = "MA";
	//}else if($selected == "MA"){
	//	$allowable_status = array("MA","MO","MR");
	//}else if($selected == "MO"){
	//	$allowable_status = array("MO","MI","MR");//,"MC"
	//}else if($selected == "MI"){
	//	$allowable_status = array("MI","MC");
	//}else if($selected == "MC"){
	//	$allowable_status = array("MC");
	//}else if($selected == "ALL"){
	//	$allowable_status = array("MA","MO","MI","MC","MR");
	//}

	
	//$DELIVERY_METHOD["TE"] = "택배";
	//$DELIVERY_METHOD["QU"] = "퀵서비스(오토바이)";
	//$DELIVERY_METHOD["TR"] = "용달(개인 트럭)";
	//$DELIVERY_METHOD["SE"] = "직접방문";

	
	$allowable_status = array("D","Q","T","S");

	if($return_type == "selectbox"){
		$mstring = "<select name='$obj_name'   ".$property.">
							<option value=''>배송방법</option>";


		foreach($DELIVERY_METHOD as $key => $value){
			if(in_array($key,$allowable_status)){
				$mstring .= "<option value='".$key."' ".($selected == $key ? "selected":"").">".$value."</option>";
			}
		}
			$mstring .= "</select>";
	}else{
			$mstring = $DELIVERY_METHOD[$selected];
	}
	
	return $mstring;
}
*/



function getInventoryPlaces($selected="", $return_type = "selectbox"){
	global $inventory_places;
	if($return_type == "selectbox"){
		$mstring = "<select name='place_type'>
							<option value=''>창고구분</option>";


		foreach($inventory_places as $key => $value){
			$mstring .= "<option value='".$key."' ".($selected == $key ? "selected":"").">".$value."</option>";
		}
			$mstring .= "</select>";
	}else if($return_type == "radio"){
		
		$i=0;
		foreach($inventory_places as $key => $value){
			if($i == 0){
				$mstring .= "<input type=radio name='place_type' id='place_type_".$key."' value='".$key."' ".(($selected == $key || $selected == '') ? "checked":"")."><label for='place_type_".$key."'>".$value."</label> &nbsp;&nbsp;";
			}else{
				$mstring .= "<input type=radio name='place_type' id='place_type_".$key."' value='".$key."' ".($selected == $key ? "checked":"")."><label for='place_type_".$key."'>".$value."</label> &nbsp;&nbsp;";
			}
			$i++;
		}
	}else if($return_type == "checkbox"){
		foreach($inventory_places as $key => $value){
			if(is_array($selected)) {//체크박스일 때 여러개를 선택하여 사용하므로 배열로 받아진 값으로 검사 kbk 13/08/09
				$selected_bool=in_array($key,$selected);
			} else {
				if($selected == $key) $selected_bool=true;
			}
				$mstring .= "<input type=checkbox name='place_type[]' id='place_type_".$key."' value='".$key."' ".($selected_bool ? "checked":"")."><label for='place_type_".$key."'>".$value."</label> &nbsp;";			
		}

	}else{
			$mstring = $inventory_places[$selected];
	}
	
	return $mstring;
}

function get_supply_vendor($basic_info,$required3_path,$type = 'inventory'){

	
		global $admininfo, $HTTP_URL;
		global $admin_config;

		$customer_phone = explode("-",$basic_info[customer_phone]);
		$customer_fax = explode("-",$basic_info[customer_fax]);
		$customer_type =$basic_info[customer_type];

if($type == "inventory"){
$Contents01 = "
		<table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box'  style='border-collapse:separate; border-spacing:1px;'>
	  <col width=18% />
	  <col width=32% />
	  <col width=18% />
	  <col width=32% />
	   <tr bgcolor=#ffffff>
	    <td class='input_box_title'> <b>입고처 구분 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<div style='padding:5px;'>
			<!--select name='customer_div'>
				<option value='1' ".($basic_info[customer_div] == "1" || $basic_info[customer_div] == "" ? "selected":"").">온라인 쇼핑몰</option>
				<option value='2' ".($basic_info[customer_div] == "2"  ? "selected":"").">오프라인 매장</option>
				<option value='3' ".($basic_info[customer_div] == "3"  ? "selected":"").">오픈마켓</option>
				<option value='3' ".($basic_info[customer_div] != "9"  ? "selected":"").">소매점</option>
			</select-->";
			if($db->dt[is_basic] == "Y"){
			$Contents01 .= "<b>자사입고처</b> <input type='hidden' name='customer_div' id='customer_div_1' value='1' >";
			}else{
				$Contents01 .= "
				<input type='radio' name='customer_div' id='customer_div_1' value='1' ".($basic_info[customer_div] == "1" || $basic_info[customer_div] == "" ? "checked":"")." onclick=\"$('.sale_agency_info').hide();$('.company_list').hide();\"><label for='customer_div_1'>자사입고처</label>
				<!--input type='radio' name='customer_div' id='customer_div_2' value='2' ".($basic_info[customer_div] == "2"  ? "checked":"")." onclick=\"$('.sale_agency_info').hide();$('.company_list').hide();\"><label for='customer_div_2'>오프라인 매장</label>
				<input type='radio' name='customer_div' id='customer_div_3' value='3' ".($basic_info[customer_div] == "3"  ? "checked":"")." onclick=\"$('.sale_agency_info').hide();$('.company_list').hide();\"><label for='customer_div_3'>오픈마켓</label-->
				<input type='radio' name='customer_div' id='customer_div_9' value='9' ".($basic_info[customer_div] == "9"  ? "checked":"")." onclick=\"$('.sale_agency_info').show();$('.company_list').hide();\"><label for='customer_div_9'>타사입고처</label>
				<input type='radio' name='customer_div' id='customer_div_8' value='8' ".($basic_info[customer_div] == "8"  ? "checked":"")." onclick=\"$('.sale_agency_info').hide();$('.company_list').show();\"><label for='customer_div_8'>입점업체</label>
				<div class='company_list' style='display:".($basic_info[customer_div] == "8" ? '' : 'none' ).";margin-top:10px;'>".CompanyList2($basic_info[company_id],"","true")." </div>";
			}
			$Contents01 .= "
			</div>
		</td>
		<td class='input_box_title'> <b>입고처 구분 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' >
		<b>".($db->dt[is_basic] == "Y" ? "기본입고처":"사용자 추가입고처")."</b>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff>
	    <td class='input_box_title'> <b>입고처 위치  <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type='radio' name='customer_position' id='customer_position_D' value='D' ".($basic_info[customer_position] == "D" || $basic_info[customer_position] == ""  ? "checked":"")." ><label for='customer_position_D'>국내</label>
			<input type='radio' name='customer_position' id='customer_position_O' value='O' ".($basic_info[customer_position] == "O"  ? "checked":"")." ><label for='customer_position_O'>해외</label>
		</td>
		<td class='input_box_title'> <b>창고보관료 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' >
			<input type=text name='storage_fee' value='".$basic_info[storage_fee]."' class='textbox'  style='width:140px' validation='true' title='일일창고보관료'> / 일
		</td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>입고처명 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' colspan=3 >
		<input type=text name='customer_name' value='".$basic_info[customer_name]."' class='textbox'  style='width:240px' validation='true' title='입고처명'>
		예) 자사입고처의 경우 : 자사몰, 오픈마켓(옥션), 오픈마켓(11번가), 양재동 오프라인 매장

		</td>

	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>전화</b></td>
		<td class='input_box_item'>
			<input type=text name='customer_phone1' value='".$customer_phone[0]."' maxlength=3 size=3  class='textbox' validation='false' title='전화'> -
			<input type=text name='customer_phone2' value='".$customer_phone[1]."' maxlength=4 size=5 class='textbox' validation='false' title='전화'> -
			<input type=text name='customer_phone3' value='".$customer_phone[2]."' maxlength=4 size=5 class='textbox' validation='false' title='전화'>
		</td>
	    <td class='input_box_title'> <b>팩스</b></td>
		<td class='input_box_item'>
			<input type=text name='customer_fax1' value='".$customer_fax[0]."' maxlength=3 size=3 class='textbox' > -
			<input type=text name='customer_fax2' value='".$customer_fax[1]."' maxlength=4 size=5 class='textbox' > -
			<input type=text name='customer_fax3' value='".$customer_fax[2]."' maxlength=4 size=5 class='textbox' >
		</td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>입고처 설명</b>   </td>
	    <td class='input_box_item' style='padding:5px;' colspan=3><textarea name='customer_msg'  style='width:90%;height:40px;padding:3px;' validation=false title='입고처 설명'>".$basic_info[customer_msg]."</textarea></td>
	  </tr>
	  </table>

";
}else if($type == "basic"){
	$Contents01 = "
		<table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box'  style='border-collapse:separate; border-spacing:1px;'>
	  <col width=18% />
	  <col width=32% />
	  <col width=18% />
	  <col width=32% />
	   <tr bgcolor=#ffffff>
	    <td class='input_box_title'> <b>입고처 구분 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<div style='padding:5px;'>
			<!--select name='customer_div'>
				<option value='1' ".($basic_info[customer_div] == "1" || $basic_info[customer_div] == "" ? "selected":"").">온라인 쇼핑몰</option>
				<option value='2' ".($basic_info[customer_div] == "2"  ? "selected":"").">오프라인 매장</option>
				<option value='3' ".($basic_info[customer_div] == "3"  ? "selected":"").">오픈마켓</option>
				<option value='3' ".($basic_info[customer_div] != "9"  ? "selected":"").">소매점</option>
			</select-->";
			if($db->dt[is_basic] == "Y"){
			$Contents01 .= "<b>자사입고처</b> <input type='hidden' name='customer_div' id='customer_div_1' value='1' >";
			}else{
				$Contents01 .= "
				<input type='radio' name='customer_div_su' id='customer_div_1' value='1' ".($basic_info[customer_div] == "1" || $basic_info[customer_div] == "" ? "checked":"")." onclick=\"$('.sale_agency_info').hide();$('.company_list').hide();\"><label for='customer_div_1'>자사입고처</label>
				<!--input type='radio' name='customer_div_su' id='customer_div_2' value='2' ".($basic_info[customer_div] == "2"  ? "checked":"")." onclick=\"$('.sale_agency_info').hide();$('.company_list').hide();\"><label for='customer_div_2'>오프라인 매장</label>
				<input type='radio' name='customer_div_su' id='customer_div_3' value='3' ".($basic_info[customer_div] == "3"  ? "checked":"")." onclick=\"$('.sale_agency_info').hide();$('.company_list').hide();\"><label for='customer_div_3'>오픈마켓</label-->
				<input type='radio' name='customer_div_su' id='customer_div_9' value='9' ".($basic_info[customer_div] == "9"  ? "checked":"")." onclick=\"$('.sale_agency_info').show();$('.company_list').hide();\"><label for='customer_div_9'>타사입고처</label>
				<input type='radio' name='customer_div_su' id='customer_div_8' value='8' ".($basic_info[customer_div] == "8"  ? "checked":"")." onclick=\"$('.sale_agency_info').hide();$('.company_list').show();\"><label for='customer_div_8'>입점업체</label>
				<div class='company_list' style='display:".($basic_info[customer_div] == "8" ? '' : 'none' ).";margin-top:10px;'>".CompanyList2($basic_info[company_id],"","false")." </div>";
			}
			$Contents01 .= "
			</div>
		</td>
		<td class='input_box_title'> <b>입고처 구분 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' >
		<b>".($db->dt[is_basic] == "Y" ? "기본입고처":"사용자 추가입고처")."</b>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff>
	    <td class='input_box_title'> <b>입고처 위치  <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type='radio' name='customer_position_su' id='customer_position_D' value='D' ".($basic_info[customer_position] == "D" || $basic_info[customer_position] == ""  ? "checked":"")." ><label for='customer_position_D'>국내</label>
			<input type='radio' name='customer_position_su' id='customer_position_O' value='O' ".($basic_info[customer_position] == "O"  ? "checked":"")." ><label for='customer_position_O'>해외</label>
		</td>
		<td class='input_box_title'> <b>창고보관료 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' >
			<input type=text name='storage_fee_su' value='".$basic_info[storage_fee]."' class='textbox'  style='width:140px' validation='false' title='일일창고보관료'> / 일
		</td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>입고처명 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' colspan=3 >
		<input type=text name='customer_name_su' id='customer_name_su' value='".$basic_info[customer_name]."' class='textbox'  style='width:240px' validation='false' title='입고처명'>
		예) 자사입고처의 경우 : 자사몰, 오픈마켓(옥션), 오픈마켓(11번가), 양재동 오프라인 매장

		</td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>전화</b></td>
		<td class='input_box_item'>
			<input type=text name='customer_phone1_su' id='customer_phone1_su' value='".$customer_phone[0]."' maxlength=3 size=3  class='textbox' validation='false' title='전화'> -
			<input type=text name='customer_phone2_su' id='customer_phone2_su' value='".$customer_phone[1]."' maxlength=4 size=5 class='textbox' validation='false' title='전화'> -
			<input type=text name='customer_phone3_su' id='customer_phone3_su' value='".$customer_phone[2]."' maxlength=4 size=5 class='textbox' validation='false' title='전화'>
		</td>
	    <td class='input_box_title'> <b>팩스</b></td>
		<td class='input_box_item'>
			<input type=text name='customer_fax1_su' id='customer_fax1_su' value='".$customer_fax[0]."' maxlength=3 size=3 class='textbox' > -
			<input type=text name='customer_fax2_su' id='customer_fax2_su' value='".$customer_fax[1]."' maxlength=4 size=5 class='textbox' > -
			<input type=text name='customer_fax3_su' id='customer_fax3_su' value='".$customer_fax[2]."' maxlength=4 size=5 class='textbox' >
		</td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>입고처 설명</b>   </td>
	    <td class='input_box_item' style='padding:5px;' colspan=3><textarea name='customer_msg_su'  style='width:90%;height:40px;padding:3px;' validation=false title='입고처 설명'>".$basic_info[customer_msg]."</textarea></td>
	  </tr>
	  </table>

";

}
	return $Contents01;
	
}

function get_sales_vendor($basic_info,$required3_path){
		
		global $admininfo, $HTTP_URL;
		global $admin_config;
		
		$customer_phone = explode("-",$basic_info[customer_phone]);
		$customer_fax = explode("-",$basic_info[customer_fax]);
		$customer_type =$basic_info[customer_type];

	$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box'>
	  <col width=18% />
	  <col width=32% />
	  <col width=18% />
	  <col width=32% />
	   <tr>
	    <td class='input_box_title'> <b>출고처 구분 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<!--select name='customer_div'>
				<option value='1' ".($basic_info[customer_div] == "1" || $basic_info[customer_div] == "" ? "selected":"").">온라인 쇼핑몰</option>
				<option value='2' ".($basic_info[customer_div] == "2"  ? "selected":"").">오프라인 매장</option>
				<option value='3' ".($basic_info[customer_div] == "3"  ? "selected":"").">오픈마켓</option>
				<option value='3' ".($basic_info[customer_div] != "9"  ? "selected":"").">소매점</option>
			</select-->
				<input type='radio' name='customer_div' id='customer_div_1' value='1' ".($basic_info[customer_div] == "1" || $basic_info[customer_div] == "" ? "checked":"")." onclick=\"$('.sale_agency_info').hide();\"><label for='customer_div_1'>자사출고처</label>
				<!--input type='radio' name='customer_div' id='customer_div_2' value='2' ".($basic_info[customer_div] == "2"  ? "checked":"")." onclick=\"$('.sale_agency_info').hide();\"><label for='customer_div_2'>오프라인 매장</label>
				<input type='radio' name='customer_div' id='customer_div_3' value='3' ".($basic_info[customer_div] == "3"  ? "checked":"")." onclick=\"$('.sale_agency_info').hide();\"><label for='customer_div_3'>오픈마켓</label-->
				<input type='radio' name='customer_div' id='customer_div_9' value='9' ".($basic_info[customer_div] == "9"  ? "checked":"")." onclick=\"$('.sale_agency_info').show();\"><label for='customer_div_9'>타사출고처</label>
		</td>
		<td class='input_box_title'> <b>출고처 구분 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' >
		<b>".($db->dt[is_basic] == "Y" ? "기본판매처":"사용자 추가판매처")."</b>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff>
	    <td class='input_box_title'> <b>입고처 위치<img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type='radio' name='customer_position' id='customer_position_D' value='D' ".($basic_info[customer_position] == "D" || $basic_info[customer_position] == ""  ? "checked":"")." ><label for='customer_position_D'>국내</label>
			<input type='radio' name='customer_position' id='customer_position_O' value='O' ".($basic_info[customer_position] == "O"  ? "checked":"")." ><label for='customer_position_O'>해외</label>
		</td>
		<td class='input_box_title'></td>
		<td class='input_box_item' >
		</td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>출고처명 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' colspan=3 >
		<input type=text name='customer_name' value='".$basic_info[customer_name]."' class='textbox'  style='width:240px' validation='false' title='출고처명'>
		예) 자사출고처의 경우 : 자사몰, 오픈마켓(옥션), 오픈마켓(11번가), 양재동 오프라인 매장

		</td>

	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>전화</b></td>
		 <td class='input_box_item'>
			<input type=text name='customer_phone1' value='".$customer_phone[0]."' maxlength=3 size=3  class='textbox' validation='false' title='전화'> -
			<input type=text name='customer_phone2' value='".$customer_phone[1]."' maxlength=4 size=5 class='textbox' validation='false' title='전화'> -
			<input type=text name='customer_phone3' value='".$customer_phone[2]."' maxlength=4 size=5 class='textbox' validation='false' title='전화'>
		</td>
	    <td class='input_box_title'> <b>팩스</b></td>
		<td class='input_box_title'>
			<input type=text name='customer_fax1' value='".$customer_fax[0]."' maxlength=3 size=3 class='textbox' > -
			<input type=text name='customer_fax2' value='".$customer_fax[1]."' maxlength=4 size=5 class='textbox' > -
			<input type=text name='customer_fax3' value='".$customer_fax[2]."' maxlength=4 size=5 class='textbox' >
		</td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>출고처 설명</b>   </td>
	    <td class='input_box_item' style='padding:5px;' colspan=3><textarea name='customer_msg'  style='width:90%;height:40px;padding:3px;' validation=false title='출고처 설명'>".$basic_info[customer_msg]."</textarea></td>
	  </tr>
	  </table>
	";

	return $Contents01;
}


function get_place($place_info,$required3_path){
	global $admininfo, $HTTP_URL;
	global $admin_config;
	$mdb = new Database;
	
	$place_tel	 = explode("-",$place_info[place_tel]);
	$place_fax	= explode("-",$place_info[place_fax]);
	
	if($_SESSION["admininfo"]["mallstory_version"] != "service"){
		$sql = "select
					cmd.code,
					cmd.com_group,
					cmd.department,
					cmd.position,
					cmd.duty
				from
					".TBL_COMMON_MEMBER_DETAIL." as cmd
				where
					cmd.code = '".$place_info[com_person]."'
		";
		$mdb->query($sql);
		$mdb->fetch();
		$group_ix = $mdb->dt[com_group];
		$dp_ix = $mdb->dt[department];
		$ps_ix = $mdb->dt[position];
		$cu_ix = $mdb->dt[duty];
		$com_person = $mdb->dt[code];
	}

	$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box'  style='border-collapse:separate; border-spacing:1px;'>
	  <col width=18% />
	  <col width=32% />
	  <col width=18% />
	  <col width=32% />
	  <tr bgcolor=#ffffff>
	    <td class='input_box_title'> <b>사업장 선택 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' colspan='3'>";
			if($_SESSION["admininfo"]["mallstory_version"] == "service"){
				$Contents02 .= "<div class='company_list' >".SelectEstablishment($place_info["company_id"],"company_id","select","false","" )." </div>";
			}else{
				$Contents02 .= "<div class='company_list' >".Company_basic($place_info[company_id],"wharehouse","false", " onChange=\"loadperson(this,'com_person')\" " )." </div>";
			}
		$Contents02 .= "
		</td>
	  </tr>";
	
		/*
	  $Contents02 .= "
	  <tr bgcolor=#ffffff>
	    <td class='input_box_title'> <b>담당자 선택 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' colspan='3'>
			<table border=0 cellpadding=0 cellspacing=0>
				<tr>";
				if($_SESSION["admininfo"]["mallstory_version"] == "service"){
					$Contents02 .= "
					<td style='padding-right:5px;'>".get_person($com_group,$department,$position,$duty,$company_id,$com_person)."</td>";
				}else{
					
					//$Contents02 .= "
					//<td style='padding-right:5px;'>".getgroup1($group_ix," onChange=\"loadperson(this,'com_person')\" ")."</td>
					//<td style='padding-right:5px;'>".getdepartment($dp_ix," onChange=\"loadperson(this,'com_person')\" ")."</td>
					//<td style='padding-right:5px;'>".getposition($ps_ix," onChange=\"loadperson(this,'com_person')\" ")."</td>
					//<td style='padding-right:5px;'>".getduty($cu_ix," onChange=\"loadperson(this,'com_person')\" ")."</td>
					//<td style='padding-right:5px;'>".get_person($com_group,$department,$position,$duty,$company_id,$com_person)."</td>";
					
					$Contents02 .= "<td style='padding-right:5px;'>".MDSelect($com_person,'com_person')."</td>";
				}
					$Contents02 .= "
				</tr>
			</table>
		</td>
	  </tr>";
		*/

	  $Contents02 .= "
	  <tr bgcolor=#ffffff>
	    <td class='input_box_title'> <b>창고명 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' ><input type=text name='place_name' value='".$place_info[place_name]."' class='textbox'  style='width:95%' validation='false' title='창고명'></td>
		<td class='input_box_title'> <b>사용여부</b> <img src='".$required3_path."'></td>
		<td class='input_box_item'>
			<input type='radio' name='disp' id='disp_y' value='Y' ".($place_info[disp] == "Y"||$place_info[disp] == ""? "checked":"")." ><label for='disp_y'>사용</label>
			<input type='radio' name='disp' id='disp_n' value='N' ".($place_info[disp] == "N"  ? "checked":"")." ><label for='disp_n'>사용안함</label>
		</td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>창고 구분</b> <img src='".$required3_path."'></td>
		<td class='input_box_item'>
			".getInventoryPlaces($place_info[place_type],"radio")."
		</td>
		<td class='input_box_title'> <b>온라인 쇼핑몰 창고</b> <img src='".$required3_path."'></td>
		<td class='input_box_item'>
			<input type='radio' name='online_place_yn' id='online_place_y' value='Y' ".($place_info[online_place_yn] == "Y" ? "checked":"")." ><label for='online_place_y'>사용</label>
			<input type='radio' name='online_place_yn' id='online_place_n' value='N' ".($place_info[online_place_yn] == "N" ||$place_info[online_place_yn] == "" ? "checked":"")." ><label for='online_place_n'>사용안함</label>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff>
	    <td class='input_box_title'> <b>창고 위치 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item' colspan='3'>
			<input type='radio' name='place_position' id='place_position_D' value='D' ".($place_info[place_position] == "D" || $place_info[place_position] == ""  ? "checked":"")." ><label for='place_position_D'>국내</label>
			<input type='radio' name='place_position' id='place_position_O' value='O' ".($place_info[place_position] == "O"  ? "checked":"")." ><label for='place_position_O'>해외</label>
			<input type='radio' name='place_position' id='place_position_E' value='E' ".($place_info[place_position] == "E"  ? "checked":"")." ><label for='place_position_E'>기타</label>
		</td>
		<!--td class='input_box_title'> <b>지정창고등록</b></td>
		<td class='input_box_item'>
			<input type='radio' name='return_position' id='return_position_B' value='B' ".($place_info[return_position] == "B" || $place_info[return_position] == "" ? "checked":"")." ><label for='return_position_B'>일반창고</label>
			<input type='radio' name='return_position' id='return_position_U' value='U' ".($place_info[return_position] == "N" ? "checked":"")." ><label for='return_position_U'>입고창고
			</label>
			<input type='radio' name='return_position' id='return_position_S' value='S' ".($place_info[return_position] == "S"  ? "checked":"")." ><label for='return_position_S'>출고창고</label>
			<input type='radio' name='return_position' id='return_position_R' value='R' ".($place_info[return_position] == "R"  ? "checked":"")." ><label for='return_position_R'>반품창고</label>
			<input type='radio' name='return_position' id='return_position_D' value='D' ".($place_info[return_position] == "D"  ? "checked":"")." ><label for='return_position_D'>페기창고</label>
		</td-->
	</tr>";

	$place_zip = explode("-",$place_info[place_zip]);

	$Contents02 .= "
	<tr>
		<td class='input_box_title'> <b>창고주소  <img src='".$required3_path."'> </b></td>
		<td class='input_box_item' colspan=3 style='padding:5px 10px;'>
			<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
				<col width='80px'>
				<col width='100px'>
				<col width='*'>
				<tr>
					<td height=26>
						<input type='text' class='textbox' name='place_zip1' id='zipcode1' size='7' maxlength='7' value='".$place_info[place_zip]."' validation='true' title='배달주소 우편번호' readonly> 
					</td>
					<td style='padding:1px 0 0 5px;'>
						<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('1');\" style='cursor:pointer;'>
					</td>
					<td></td>
				</tr>
				<tr>
					<td height=26 colspan='3'>
						<input type=text name='place_addr1'  id='addr1' value='".$place_info[place_addr1]."' size=50 class='textbox' validation='true' title='배달주소' style='width:450px'>
					</td>
				</tr>
				<tr>
					<td height=26 colspan='3'>
						<input type=text name='place_addr2'  id='addr2'  value='".$place_info[place_addr2]."' size=70 class='textbox' validation='false' title='배달주소' style='width:450px'> (상세주소)
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff>
	    <td class='input_box_title'> <b>창고 전화번호 </b></td>
		<td class='input_box_item'>
			<input type=text name='place_tel_1' id='com_mobile_1' value='".$place_tel[0]."' maxlength=3 size=3  class='textbox' com_numeric=true validation='false' title='전화번호'> -
			<input type=text name='place_tel_2' id='com_mobile_2' value='".$place_tel[1]."' maxlength=4 size=5 class='textbox' com_numeric=true validation='false' title='전화번호'> -
			<input type=text name='place_tel_3' id='com_mobile_3' value='".$place_tel[2]."' maxlength=4 size=5 class='textbox' com_numeric=true validation='false' title='전화번호'>
			</td>
		<td class='input_box_title'> <b>창고 팩스 </b></td>
		<td class='input_box_item'>		
		<input type=text name='place_fax_1' id='place_fax_1' value='".$place_fax[0]."' maxlength=3 size=3  class='textbox' com_numeric=true validation='false' title='창고 팩스'> -
		<input type=text name='place_fax_2' id='place_fax_2' value='".$place_fax[1]."' maxlength=4 size=5 class='textbox' com_numeric=true validation='false' title='창고 팩스'> -
		<input type=text name='place_fax_3' id='place_fax_3' value='".$place_fax[2]."' maxlength=4 size=5 class='textbox' com_numeric=true validation='false' title='창고 팩스'>
		</td>
	</tr>
	<tr>
	    <td class='input_box_title'> <b>창고 설명</b>   </td>
	    <td class='input_box_item' style='padding:5px;' colspan=3><textarea name='place_msg'  style='padding:3px;width:97%;height:70px;' validation=false title='상점 설명'>".$place_info[place_msg]."</textarea></td>
	</tr>
	</table>";

	  return $Contents02;
	
}

function insert_multi_price($goods_setup_info,$gu_data,$gid,$gu_ix){

	global $admininfo, $HTTP_URL;
	global $admin_config;
	
	return false;	//다이소는 다중가격할인 테이블 사용안함 주석처리 2014-08-14 이학봉

	$db = new Database;
	$db2 = new Database;
	//$is_wholesale_array = array('R','W');		//품목다중가격 추가 2014-05-07 이학봉
	$is_wholesale_array = array('R','W');		//품목다중가격 추가 2014-05-07 이학봉
	//품목 다중가격 테이블에 없을시 매입가로 일괄 적용 시작 2014-05-07 이학봉
	//$sql = "select * from inventory_goods_multi_price where gid = '".$gid."' and unit = '".$gu_data[unit]."'";
	//$db2->query($sql);
	//if(!$db2->total){

		if($goods_setup_info[update_kind] == "update_sellprice_multi"){
			$type = 'multi';
		}else{
			$type = 'rate';
		}

		for($i=0;$i<count($is_wholesale_array);$i++){
				
			if($type == 'rate'){				//판매가 대비 할인
				if($is_wholesale_array[$i] == 'R'){
					$sellprice = $gu_data[sellprice];	
				}else{
					$sellprice = $gu_data[wholesale_price];
				}

				$product_sellprice = $goods_setup_info[round_type][rate]($sellprice*((100-$goods_setup_info[batch][rate][$is_wholesale_array[$i]][product_sellprice])/100).($goods_setup_info[round_type][rate] == 'round'?",".$goods_setup_info[round_cnt][rate]:''));		//할인가

				$type_a_sellprice =$goods_setup_info[round_type][rate]($sellprice*((100-$goods_setup_info[batch][rate][$is_wholesale_array[$i]][a])/100).($goods_setup_info[round_type][rate] == 'round'?",".$goods_setup_info[round_cnt][rate]:''));

				$type_b_sellprice =$goods_setup_info[round_type][rate]($sellprice*((100-$goods_setup_info[batch][rate][$is_wholesale_array[$i]][b])/100).($goods_setup_info[round_type][rate] == 'round'?",".$goods_setup_info[round_cnt][rate]:''));

				$type_c_sellprice =$goods_setup_info[round_type][rate]($sellprice*((100-$goods_setup_info[batch][rate][$is_wholesale_array[$i]][c])/100).($goods_setup_info[round_type][rate] == 'round'?",".$goods_setup_info[round_cnt][rate]:''));

				$type_d_sellprice =$goods_setup_info[round_type][rate]($sellprice*((100-$goods_setup_info[batch][rate][$is_wholesale_array[$i]][d])/100).($goods_setup_info[round_type][rate] == 'round'?",".$goods_setup_info[round_cnt][rate]:''));

				$type_e_sellprice =$goods_setup_info[round_type][rate]($sellprice*((100-$goods_setup_info[batch][rate][$is_wholesale_array[$i]][e])/100).($goods_setup_info[round_type][rate] == 'round'?",".$goods_setup_info[round_cnt][rate]:''));

			}else{				//매입가 대비 배당
				
				if($is_wholesale_array[$i] == 'R'){
					$basic_price = $gu_data[sellprice];	
				}else{
					$basic_price = $gu_data[wholesale_price];
				}

				$product_sellprice = $goods_setup_info[batch][multi][$is_wholesale_array[$i]][product_sellprice] > 0?$goods_setup_info[round_type][multi]($gu_data[buying_price] * $goods_setup_info[batch][multi][$is_wholesale_array[$i]][product_sellprice].($goods_setup_info[round_type][multi] == 'round'?",".$goods_setup_info[round_cnt][multi]:'')):$basic_price;

				$sellprice = $goods_setup_info[batch][multi][$is_wholesale_array[$i]][sellprice] > 0?$goods_setup_info[round_type][multi]($gu_data[buying_price] * $goods_setup_info[batch][multi][$is_wholesale_array[$i]][sellprice].($goods_setup_info[round_type][multi] == 'round'?",".$goods_setup_info[round_cnt][multi]:'')):$basic_price;

				$type_a_sellprice = $goods_setup_info[batch][multi][$is_wholesale_array[$i]][a] > 0?$goods_setup_info[round_type][multi]($gu_data[buying_price] * $goods_setup_info[batch][multi][$is_wholesale_array[$i]][a].($goods_setup_info[round_type][multi] == 'round'?",".$goods_setup_info[round_cnt][multi]:'')):$basic_price;

				$type_b_sellprice = $goods_setup_info[batch][multi][$is_wholesale_array[$i]][b] > 0?$goods_setup_info[round_type][multi]($gu_data[buying_price] * $goods_setup_info[batch][multi][$is_wholesale_array[$i]][b].($goods_setup_info[round_type][multi] == 'round'?",".$goods_setup_info[round_cnt][multi]:'')):$basic_price;

				$type_c_sellprice = $goods_setup_info[batch][multi][$is_wholesale_array[$i]][c] > 0?$goods_setup_info[round_type][multi]($gu_data[buying_price] * $goods_setup_info[batch][multi][$is_wholesale_array[$i]][c].($goods_setup_info[round_type][multi] == 'round'?",".$goods_setup_info[round_cnt][multi]:'')):$basic_price;

				$type_d_sellprice = $goods_setup_info[batch][multi][$is_wholesale_array[$i]][d] > 0?$goods_setup_info[round_type][multi]($gu_data[buying_price] * $goods_setup_info[batch][multi][$is_wholesale_array[$i]][d].($goods_setup_info[round_type][multi] == 'round'?",".$goods_setup_info[round_cnt][multi]:'')):$basic_price;

				$type_e_sellprice = $goods_setup_info[batch][multi][$is_wholesale_array[$i]][e] > 0?$goods_setup_info[round_type][multi]($gu_data[buying_price] * $goods_setup_info[batch][multi][$is_wholesale_array[$i]][e].($goods_setup_info[round_type][multi] == 'round'?",".$goods_setup_info[round_cnt][multi]:'')):$basic_price;
				

				$product_sellprice = ($gu_data[buying_price] > $product_sellprice?$gu_data[buying_price]:$product_sellprice);	//공급가대비율 사용시 공급가보다 작을시 공급가로 측정

				$sellprice = ($gu_data[buying_price] > $sellprice?$gu_data[buying_price]:$sellprice);

				$type_a_sellprice = ($gu_data[buying_price] > $type_a_sellprice?$basic_price:$type_a_sellprice);

				$type_b_sellprice = ($gu_data[buying_price] > $type_b_sellprice?$basic_price:$type_b_sellprice);

				$type_c_sellprice = ($gu_data[buying_price] > $type_c_sellprice?$basic_price:$type_c_sellprice);

				$type_d_sellprice = ($gu_data[buying_price] > $type_d_sellprice?$basic_price:$type_d_sellprice);

				$type_e_sellprice = ($gu_data[buying_price] > $type_e_sellprice?$basic_price:$type_e_sellprice);
			}
			
			$sql = "select pmp_ix from inventory_goods_multi_price where gid = '".$gid."' and gu_ix = '".$gu_ix."' and is_wholesale = '".$is_wholesale_array[$i]."'";
			$db->query($sql);

			if($db->total > 0){

			$sql = "update inventory_goods_multi_price set
						
						buying_price = '".$gu_data[buying_price]."',
						sellprice = '".$sellprice."',
						product_sellprice = '".$product_sellprice."',
						type_a_sellprice = '".$type_a_sellprice."',
						type_b_sellprice = '".$type_b_sellprice."',
						type_c_sellprice = '".$type_c_sellprice."',
						type_d_sellprice = '".$type_d_sellprice."',
						type_e_sellprice = '".$type_e_sellprice."',

						sellprice_rate = '0',
						product_sellprice_rate = '".$goods_setup_info[batch][rate][$is_wholesale_array[$i]][product_sellprice]."',
						type_a_sellprice_rate = '".$goods_setup_info[batch][rate][$is_wholesale_array[$i]][a]."',
						type_b_sellprice_rate = '".$goods_setup_info[batch][rate][$is_wholesale_array[$i]][b]."',
						type_c_sellprice_rate = '".$goods_setup_info[batch][rate][$is_wholesale_array[$i]][c]."',
						type_d_sellprice_rate = '".$goods_setup_info[batch][rate][$is_wholesale_array[$i]][d]."',
						type_e_sellprice_rate = '".$goods_setup_info[batch][rate][$is_wholesale_array[$i]][e]."',
						editdate = NOW()
					where
						gid = '".$gid."'
						and gu_ix = '".$gu_ix."'
						and unit = '".$gu_data[unit]."'
						and is_wholesale = '".$is_wholesale_array[$i]."'
						";
						
				$db->query($sql);

			}else{
			$sql = "insert into inventory_goods_multi_price set
						gid = '".$gid."',
						gu_ix = '".$gu_ix."',
						unit = '".$gu_data[unit]."',
						is_wholesale = '".$is_wholesale_array[$i]."',
						buying_price = '".$gu_data[buying_price]."',
						sellprice = '".$sellprice."',
						product_sellprice = '".$product_sellprice."',
						type_a_sellprice = '".$type_a_sellprice."',
						type_b_sellprice = '".$type_b_sellprice."',
						type_c_sellprice = '".$type_c_sellprice."',
						type_d_sellprice = '".$type_d_sellprice."',
						type_e_sellprice = '".$type_e_sellprice."',

						sellprice_rate = '0',
						product_sellprice_rate = '".$goods_setup_info[batch][rate][$is_wholesale_array[$i]][product_sellprice]."',
						type_a_sellprice_rate = '".$goods_setup_info[batch][rate][$is_wholesale_array[$i]][a]."',
						type_b_sellprice_rate = '".$goods_setup_info[batch][rate][$is_wholesale_array[$i]][b]."',
						type_c_sellprice_rate = '".$goods_setup_info[batch][rate][$is_wholesale_array[$i]][c]."',
						type_d_sellprice_rate = '".$goods_setup_info[batch][rate][$is_wholesale_array[$i]][d]."',
						type_e_sellprice_rate = '".$goods_setup_info[batch][rate][$is_wholesale_array[$i]][e]."',

						regdate = NOW()
						";
				$db->query($sql);
			}
		}

		
	//}
		//품목 다중가격 테이블에 없을시 매입가로 일괄 적용 시작 2014-05-07 이학봉

}

function update_product_coprice($goods_unit,$gu_ix){	//품목과 관련된 상품의 공급가 변경 

	//return false;

	global $admininfo, $HTTP_URL;
	global $admin_config;
	
	$db = new Database;
	$db2 = new Database;
	
	$sql = "select * from inventory_goods_unit where gu_ix = '".$gu_ix."'";
	$db->query($sql);
	$db->fetch();

	$b_buying_price = $db->dt[buying_price];	//공급가
	$b_wholesale_price = $db->dt[wholesale_price];	//기본도매가(판매가)
	$b_sellprice = $db->dt[sellprice];	//기본소매가(판매가)

	if($b_buying_price != $goods_unit[buying_price]){	//공급가가 변햇을경우

		$sql = "update shop_product set 
					coprice = '".$goods_unit[buying_price]."'
				where
					pcode = '".$gu_ix."'
					and stock_use_yn = 'Y'";
		$db->query($sql);

		/*
		$sql = "update shop_product_options_detail set
					option_coprice = '".$goods_unit[buying_price]."'
				where
					option_code = '".$gu_ix."'";
		$db->query($sql);
		*/

		//상품 옵션 금액 변경 시작 (WMS 사용하는 상품중 같은 품목코드인것에 대해update 처리함 2014-08-13 이학봉)
		//2014-08-13 상품검색 조건 추가 option_kind 추가 재고관리를 사용하는 옵션에서만 처리가능하게함 
		$m_table_name = "option_detail_table_".date("i");
		$db->query("create temporary table ".$m_table_name." (id int)");
		$db->query("insert into ".$m_table_name."(id) 
					select 
						od.id
					from
						shop_product as p 
						inner join shop_product_options as po on (p.id = po.pid)
						inner join shop_product_options_detail  as od on (p.id = od.pid)
					where
						p.product_type = '0'
						and po.option_kind in ('b','x','s2','x2','c','a')
						and od.option_code = '".$gu_ix."'
						and p.stock_use_yn = 'Y'");
	
		$sql = "update shop_product_options_detail set
					option_coprice = '".$goods_unit[buying_price]."'
				where
					id in (select id from ".$m_table_name.")";
		$db->query($sql);

		$db->query("drop table ".$m_table_name."");
		//상품 옵션 금액 변경 끝

		/*다이소는 다중가격할인 테이블 사용안함 주석처리 2014-08-14 이학봉
		$sql = "update inventory_goods_multi_price set
					buying_price = '".$goods_unit[buying_price]."'
				where
					gu_ix = '".$gu_ix."'";
		$db->query($sql);
		*/

	}
}

function update_product_listprice($goods_unit,$gu_ix){

	//return false;

	global $admininfo, $HTTP_URL;
	global $admin_config;
	
	$db = new Database;
	$db2 = new Database;
	
	$db->query("select * from inventory_goods_unit where gu_ix = '".$gu_ix."' ");
	$db->fetch();
	$b_wholesale_price = $db->dt[wholesale_price];	//기존 도매판매가
	$b_sellprice = $db->dt[sellprice];	//기존 도매판매가

	if($b_wholesale_price != $goods_unit[wholesale_price] || $b_sellprice != $goods_unit[sellprice]){	//기본도매가가 변햇을경우

		//품목다중가격 기본가 변경
		/* 다이소는 다중가격할인 테이블 사용안함 주석처리 2014-08-14 이학봉
		$is_wholesale = array('R','W');
		for($i=0;$i<count($is_wholesale);$i++){
			
			$db->query("select * from inventory_goods_multi_price where gu_ix = '".$gu_ix."' and is_wholesale = '".$is_wholesale[$i]."'");
			$db->fetch();
			
			if($is_wholesale[$i] == 'R'){
				$sellprice = $goods_unit[sellprice];
				$product_sellprice = round($sellprice * ((100 - $db->dt[product_sellprice_rate])/100));
			}else{
				$sellprice = $goods_unit[wholesale_price];
				$product_wholesale_sellprice = round($sellprice * ((100 - $db->dt[product_sellprice_rate])/100));
			}
			
			$sql = "update inventory_goods_multi_price set
						sellprice = '".$sellprice."',
						product_sellprice = '".round($sellprice * ((100 - $db->dt[product_sellprice_rate])/100))."',
						type_a_sellprice = '".round($sellprice * ((100 - $db->dt[type_a_sellprice_rate])/100))."',
						type_b_sellprice = '".round($sellprice * ((100 - $db->dt[type_b_sellprice_rate])/100))."',
						type_c_sellprice = '".round($sellprice * ((100 - $db->dt[type_c_sellprice_rate])/100))."',
						type_d_sellprice = '".round($sellprice * ((100 - $db->dt[type_d_sellprice_rate])/100))."',
						type_e_sellprice = '".round($sellprice * ((100 - $db->dt[type_e_sellprice_rate])/100))."'
					where
						gu_ix = '".$gu_ix."'
						and is_wholesale = '".$is_wholesale[$i]."'";
			$db->query($sql);
			
		}
		*/

		$inventory_set = " sellprice = '".$goods_unit[sellprice]."', wholesale_price = '".$goods_unit[wholesale_price]."' ";
		$product_set = " wholesale_price = '".$goods_unit[wholesale_price]."',
						 listprice = '".$goods_unit[sellprice]."',
						 wholesale_sellprice = '".$goods_unit[wholesale_price]."',
						 sellprice = '".$goods_unit[sellprice]."'";

		$option_set = " option_wholesale_listprice = '".$goods_unit[wholesale_price]."',
						option_listprice = '".$goods_unit[sellprice]."',
						option_price = '".$goods_unit[sellprice]."',
						option_wholesale_price = '".$goods_unit[wholesale_price]."'";	//옵션도매판매가

		/*
		기본가 변경시 기본가 기준 할인율 대비  할인가도 변경되어야함 . /shop_product, shop_product_options_detail 할인가
		*/
		
		//품목 기본가 변경
		$sql = "update inventory_goods_unit set 
					$inventory_set
				where
					gu_ix = '".$gu_ix."'";
		$db->query($sql);

		//상품 기본가 변경 (WMS 사용하는 상품중 같은 품목코드인것에 대해update 처리함 2014-08-13 이학봉)
		$sql = "update shop_product set 
					$product_set
				where
					pcode = '".$gu_ix."'
					and stock_use_yn = 'Y'";
		$db->query($sql);
		
		//상품 옵션 금액 변경 시작 (WMS 사용하는 상품중 같은 품목코드인것에 대해update 처리함 2014-08-13 이학봉)
		//2014-08-13 상품검색 조건 추가 option_kind 추가 재고관리를 사용하는 옵션에서만 처리가능하게함 
		$m_table_name = "option_id_table_".date("i");
		$db->query("create temporary table ".$m_table_name." (id int)");
		$db->query("insert into ".$m_table_name."(id) 
					select 
						od.id
					from
						shop_product as p 
						inner join shop_product_options as po on (p.id = po.pid)
						inner join shop_product_options_detail  as od on (p.id = od.pid)
					where
						p.product_type = '0'
						and po.option_kind in ('b','x','s2','x2','c','a')
						and od.option_code = '".$gu_ix."'
						and p.stock_use_yn = 'Y'");

		$sql = "update shop_product_options_detail set
					$option_set
				where
					id in (select id from ".$m_table_name.")";
		$db->query($sql);

		$db->query("drop table ".$m_table_name."");
		//상품 옵션 금액 변경 끝

	}

}

function floorBetter_admin($number, $precision = 0, $direction = NULL) {	//1 자리수 반내림 
	if (!isset($direction) || is_null($direction)) { 
		return round($number, $precision); 
	}else{
		$factor = pow(10, -1 * $precision); 

		return strtolower(substr($direction, 0, 1)) == 'd' ? floor($number / $factor) * $factor : ceil($number / $factor) * $factor;
	}
}

//주문쪽에서도 사용해야 하기 떄문에 함수화함!
function inventory_warehouse_move($gu_ix,$delivery_cnt,$ps_ix,$delivery_ps_ix,$charger_ix="",$charger="",$mmode="",$oid=""){
	
	$db = new Database;

	if(empty($charger_ix)){
		$charger_ix=$_SESSION["admininfo"]["charger_ix"];
	}

	if(empty($charger)){
		$charger=$_SESSION["admininfo"]["charger"];
	}
	

	//(-)수량 이동처리 X
	if($delivery_cnt > 0){
		$sql = "select pi.company_id, ps.pi_ix, ps.ps_ix from inventory_place_info pi , inventory_place_section ps where pi.pi_ix = ps.pi_ix and ps.ps_ix = '".$ps_ix."' ";
		$db->query($sql);
		$db->fetch();
		$pi_ix = $db->dt[pi_ix];
		$now_company_id = $db->dt[company_id];
		$now_pi_ix = $db->dt[pi_ix];
		$now_ps_ix = $db->dt[ps_ix];

		$sql = "select pi.company_id, ps.pi_ix, ps.ps_ix from inventory_place_info pi , inventory_place_section ps where pi.pi_ix = ps.pi_ix and ps.ps_ix = '".$delivery_ps_ix."' ";
		$db->query($sql);
		$db->fetch();
		$move_company_id = $db->dt[company_id];
		$move_pi_ix = $db->dt[pi_ix];
		$move_ps_ix = $db->dt[ps_ix];

		$sql = "select g.gid, gu.unit, g.standard,  '".$delivery_cnt."' as amount , '".$now_company_id."' as company_id,  '".$now_pi_ix."' as pi_ix,  '".$now_ps_ix."' as ps_ix  
					from inventory_goods g , inventory_goods_unit gu 
					where g.gid = gu.gid and gu.gu_ix = '".$gu_ix."'";
		// 출고가격을 어떻게 처리 할지? 
		// 한꺼번에 여러개를 묶음으로 처리하는데 출고가 ... 
		$db->query($sql);
		$warehouse_moveinfo = $db->fetchall();

		$sql = "insert into inventory_warehouse_move
					(wm_ix,apply_charger_ix,apply_charger_name,now_company_id, now_pi_ix, now_ps_ix, move_company_id, move_pi_ix, move_ps_ix, wm_apply_date,status,charger_ix,charger_name,etc,h_type,al_ix,editdate, regdate) values('','".$charger_ix."','".$charger."','$now_company_id','$now_pi_ix','$now_ps_ix','$move_company_id','$move_pi_ix','$move_ps_ix',NOW(),'MC','".$charger_ix."','".$charger."','$etc','IW','$al_ix',NOW(),NOW())";
		//echo nl2br($sql)."<br><br>";
		//exit;
		$db->sequences = "INVENTORY_WH_MOVE_SEQ";
		$db->query($sql);

		$db->query("SELECT wm_ix FROM inventory_warehouse_move WHERE wm_ix=LAST_INSERT_ID()");
		$db->fetch();
		$wm_ix = $db->dt[wm_ix];

		for($i=0 ; $i < count($warehouse_moveinfo);$i++){

			$sql = "SELECT g.gname FROM inventory_goods g WHERE gid = '".$warehouse_moveinfo[$i][gid]."' ";
			$db->query($sql);
			$db->fetch();
			$gname = $db->dt[gname];

			if($warehouse_moveinfo[$i][pi_ix]){
				$sql = "select pi.company_id
							from inventory_place_info pi 
							where pi.pi_ix = '".$warehouse_moveinfo[$i][pi_ix]."' ";

				$db->query($sql);
				$db->fetch();
				$company_id = $db->dt[company_id];
			}

			$sql = "insert into inventory_warehouse_move_detail
					(wmd_ix,wm_ix,company_id, pi_ix,ps_ix,gid,gname,unit,standard,expiry_date,apply_cnt, delivery_cnt, entering_cnt,regdate) 
					values
					('','$wm_ix','".$company_id."','".$warehouse_moveinfo[$i][pi_ix]."','".$warehouse_moveinfo[$i][ps_ix]."','".$warehouse_moveinfo[$i][gid]."','".$gname."','".$warehouse_moveinfo[$i][unit]."','".$warehouse_moveinfo[$i][standard]."','".$warehouse_moveinfo[$i][expiry_date]."','".$warehouse_moveinfo[$i][amount]."', '".$warehouse_moveinfo[$i][amount]."','".$warehouse_moveinfo[$i][amount]."',NOW()) ";
			//echo $sql;
			//exit;
			$db->query($sql);

			unset($warehouse_moveinfo[$i][pi_ix]);
			unset($warehouse_moveinfo[$i][ps_ix]);
		}


		/*
		$sql = "insert into inventory_history_detail
				(hd_ix,h_ix,gid,unit,gname,standard,amount,price,expiry_date,regdate) 
				values
				('','".$h_ix."','".$stocked_detail_info[$i][gid]."','".$stocked_detail_info[$i][unit]."','".$gname."','".$stocked_detail_info[$i][standard]."','".$stocked_detail_info[$i][amount]."','".$stocked_detail_info[$i][price]."','".$stocked_detail_info[$i][expiry_date]."',NOW()) ";
		*/

		$item_info[pi_ix] = $pi_ix; // 입출고 내역은 어디로 이동해 갔는지가 남기 때문에 move_pi_ix 기록만 남긴다.
		$item_info[ps_ix] = $ps_ix; // 이동출고장소
		$item_info[company_id] = $now_company_id; // 이동사업장
		$item_info[h_div] = "2";  // 입출고유형 2 :  출고
		$item_info[vdate] = date("Ymd");
		//$item_info[ci_ix] = $_POST["ci_ix"]; // 거래처
		$item_info[oid] = $oid;
		$item_info[msg] = $msg."내부창고이동(출고)";//$_POST["etc"];
		$item_info[h_type] = 'IW';//$_POST["h_type"]; // 51: 내부창고 이동
		$item_info[charger_name] = $charger;
		$item_info[charger_ix] = $charger_ix;
		$item_info[detail] = $warehouse_moveinfo;
		//print_r($item_info);
		//exit;
		UpdateGoodsItemStockInfo($item_info, $db);



		$sql = "select g.gid, gu.unit, g.standard,  '".$delivery_cnt."' as amount , '".$move_company_id."' as company_id,  '".$move_pi_ix."' as pi_ix,  '".$move_ps_ix."' as ps_ix  
					from inventory_goods g , inventory_goods_unit gu 
					where g.gid = gu.gid and gu.gu_ix = '".$gu_ix."'";
		// 출고가격을 어떻게 처리 할지? 
		// 한꺼번에 여러개를 묶음으로 처리하는데 출고가 ... 
		$db->query($sql);
		$warehouse_moveinfo = $db->fetchall();


		$item_info[pi_ix] = $move_pi_ix;
		$item_info[ps_ix] = $move_ps_ix;
		$item_info[company_id] = $move_company_id;
		$item_info[h_div] = "1";
		$item_info[vdate] = date("Ymd");
		//$item_info[ci_ix] = $_POST["ci_ix"];
		$item_info[oid] = $oid;
		$item_info[msg] = $msg."내부창고이동(입고)";//$_POST["etc"];
		$item_info[h_type] = 'IW';//$_POST["h_type"]; 내부창고이동
		$item_info[charger_name] = $charger;
		$item_info[charger_ix] = $charger_ix;
		$item_info[detail] = $warehouse_moveinfo;

		UpdateGoodsItemStockInfo($item_info, $db);

		$db->query("update inventory_warehouse_move set  wm_delivery_date = '".date("Ymd")."', wm_entering_date = '".date("Ymd")."' WHERE wm_ix= '".$wm_ix."' ");
	}

	if($mmode == "pop"){
		echo "<script type='text/javascript'>
		<!--
			alert('정상적으로 이동 처리 되었습니다.');
			top.opener.location.reload();
			top.self.close();
		//-->
		</script>";
	}elseif($mmode == "return"){
		return "Y";
	}else{
		echo "정상적으로 이동 처리 되었습니다.";
	}

	exit;
}

//출고 잘못했을때 사용하는 함수!
function inventory_cancel_output_stock($order_info){
	$db = new Database;

	$sql = "select g.gid, gu.unit, g.standard, 
				'".$order_info['pcnt']."' as amount ,
				'".$order_info['delivery_company_id']."' as company_id,
				'".$order_info['delivery_pi_ix']."' as pi_ix,
				'".$order_info['delivery_basic_ps_ix']."' as ps_ix  
				from inventory_goods g , inventory_goods_unit gu 
				where g.gid = gu.gid and gu.gu_ix = '".$order_info['gu_ix']."'";
	$db->query($sql);
	$warehouse_moveinfo = $db->fetchall();

	$item_info[pi_ix] = $order_info['delivery_pi_ix'];
	$item_info[ps_ix] = $order_info['delivery_basic_ps_ix'];
	$item_info[company_id] = $order_info['delivery_company_id'];
	$item_info[h_div] = "1";
	$item_info[vdate] = date("Ymd");
	$item_info[oid] = $order_info['oid'];
	$item_info[msg] = "배송중->출고대기 취소 입고";//$_POST["etc"];
	$item_info[h_type] = 'ETC';//$_POST["h_type"]; 기타
	$item_info[charger_name] = $_SESSION['admininfo']["charger"];
	$item_info[charger_ix] = $_SESSION['admininfo']["charger_ix"];
	$item_info[detail] = $warehouse_moveinfo;

	UpdateGoodsItemStockInfo($item_info, $db);

	/*
	2015-01-29 Hong 재고 차김 이슈생겨서 프로세스 변경

	$sql="select * from inventory_goods_unit where gu_ix='".$order_info[gu_ix]."' ";
	//echo $sql."<br/>";
	$db->query($sql);
	$db->fetch();
	$gu_info = $db->dt;
	//print_r($gu_info);

	//h_div = 2 출고 //주문분할로 같은 데이터가 있을수 있기 때문에 하나만!
	$sql="select hd.hd_ix from inventory_history h left join inventory_history_detail hd on (h.h_ix=hd.h_ix) where h.h_div='2' and h.oid='".$order_info[oid]."' and hd.gid='".$gu_info[gid]."' and hd.unit ='".$gu_info[unit]."' and hd.amount='".$order_info[pcnt]."' limit 0,1 ";
	//echo $sql."<br/>";
	$db->query($sql);
	$db->fetch();
	$hd_ix = $db->dt[hd_ix];

	$sql="update inventory_history_detail set is_delete='1' where hd_ix='".$hd_ix."' ";
	$db->query($sql);
	
	//주문분할로 같은 데이터가 있을수 있기 때문에 하나만!
	
	$sql="select psi_ix from inventory_product_stockinfo where company_id='".$order_info[delivery_company_id]."' and pi_ix='".$order_info[delivery_pi_ix]."' and ps_ix='".$order_info[delivery_basic_ps_ix]."' and gid='".$gu_info[gid]."' and unit ='".$gu_info[unit]."' ";
	//echo $sql."<br/>";
	$db->query($sql);
	$db->fetch();
	$psi_ix = $db->dt[psi_ix];
	
	$sql="update inventory_product_stockinfo set stock= stock + '".$order_info[pcnt]."' , total_out_stock= total_out_stock-'".$order_info[pcnt]."' where psi_ix='".$psi_ix."' ";
	//echo $sql."<br/>";
	$db->query($sql);
	*/

}

//[S] 쉐어드메모리 데이터 처리

function sharedControll($name, $act="select", $data="")
{
	include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class");

	$shmop = new Shared($name);
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();

	if($act == "select"){
		$warehouse_data = $shmop->getObjectForKey($name);
		$warehouse_data = unserialize(urldecode($warehouse_data));
	}else if($act == "insert"){
		$warehouse_data = urlencode(serialize($data));
		$shmop->SetFilePath();
		$shmop->setObjectForKey($warehouse_data, $name);
	}else{
		return false;
	}

	return $warehouse_data;
}

//[E] 쉐어드메모리 데이터 처리

?>