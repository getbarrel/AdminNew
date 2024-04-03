<?
/**
 * 구매대행 비용계산 
 * 12.4.16 배광호
 */
function BuyingServicePriceCalcurate($price,$buying_service_currencyinfo){

	if($buying_service_currencyinfo[bs_air_wt] <= 1){ // 예상무게가 기본 1파운드 미만일경우
		$air_shipping = $buying_service_currencyinfo[bs_basic_air_shipping]; // 기본 1파운드 항공운송료
	}else{// 예상무계가 1파운드를 초과할경우
		$air_shipping = $buying_service_currencyinfo[bs_basic_air_shipping] + ($buying_service_currencyinfo[bs_add_air_shipping] * ($buying_service_currencyinfo[bs_air_wt] - 1)); 
	}
	
	$price = str_replace(",","",$price);
	$bs_duty_basis = ($price+$air_shipping)*$buying_service_currencyinfo[exchange_rate]; // 관세 대상 기준금액						
	$bs_duty = round($bs_duty_basis*$buying_service_currencyinfo[bs_duty_rate]/100,-1); // 관세
	$bs_supertax = round(($bs_duty_basis+$bs_duty)*$buying_service_currencyinfo[bs_supertax_rate]/100,-1); // 부가세
	
	$bs_fee_rate = $buying_service_currencyinfo[bs_fee_rate];
	$buyingservice_coprice = round(($price+$air_shipping)*$buying_service_currencyinfo[exchange_rate]+$bs_duty+$bs_supertax+$buying_service_currencyinfo[clearance_fee],0);
	// 공급원가 = (orgin 원가 + 항공운송료)* 환율 + 관세 + 부가세 + 통관수수료 
	$bs_fee = round($buyingservice_coprice*$bs_fee_rate/100,-1);
    
    $bs_fee_array[air_shipping] = $air_shipping;
    $bs_fee_array[bs_duty] = $bs_duty;
    $bs_fee_array[bs_supertax] = $bs_supertax;
    $bs_fee_array[buyingservice_coprice] = $buyingservice_coprice;
    $bs_fee_array[bs_fee] = $bs_fee;
    
    return $bs_fee_array;
}
/**
 * 가격 반올림/버림 처리함수 
 * 12.4.16 배광호
 */
function PriceRoundUpOrDown($round_type,$round_precision,$buyingservice_coprice,$bs_fee){
    if($round_type && $round_precision){//$usable_round == "Y"
		//exit;
		if($round_type == "round"){
			$price_array[listprice] = roundBetterUp($buyingservice_coprice+$bs_fee,-1*$round_precision);
			$price_array[sellprice] = roundBetterUp($buyingservice_coprice+$bs_fee,-1*$round_precision);
		}else if($round_type == "floor"){
			$price_array[listprice] = roundBetterDown($buyingservice_coprice+$bs_fee,-1*$round_precision);
			$price_array[sellprice] = roundBetterDown($buyingservice_coprice+$bs_fee,-1*$round_precision);
		}

	}else{
		$price_array[listprice] = round($buyingservice_coprice+$bs_fee,-1);
		$price_array[sellprice] = round($buyingservice_coprice+$bs_fee,-1);
	}
    return $price_array;
}

function getBuyingServiceCurrencyInfo($selected="", $type="select", $property = " validation='true' "){
	global $admininfo;
	$mdb = new Database;

	//echo $seelcted;
	if($type == "array"){
		$sql = 	"SELECT * FROM shop_buyingservice_currencytype_info
				where disp = '1' order by regdate desc ";

		$mdb->query($sql);
		$currencys = $mdb->fetchall("object");
		for($i=0;$i < count($currencys);$i++){
			$__currencys[$currencys[$i]["currency_ix"]]["currency_type_name"] = $currencys[$i]["currency_type_name"];
			$__currencys[$currencys[$i]["currency_ix"]]["basic_currency"] = $currencys[$i]["basic_currency"];
		}

		return $__currencys;
	}else if($type == "text"){
		$sql = 	"SELECT * FROM shop_buyingservice_currencytype_info
					where disp = '1' and currency_ix = '".$selected."' order by regdate desc ";
//echo $sql;
		$mdb->query($sql);
		$mdb->fetch();
 
		return $mdb->dt[currency_type_name];
	}else{
		$sql = 	"SELECT * FROM shop_buyingservice_currencytype_info
					where disp = '1' order by regdate desc ";

		$mdb->query($sql);

		$mstring = "<select name='currency_ix' id='currency_ix' title='환율타입' ".$property.">";
		$mstring .= "<option value=''>환율타입</option>";
		if($mdb->total){


			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);
				if($mdb->dt[currency_ix] == $selected){
					$mstring .= "<option value='".$mdb->dt[currency_ix]."' selected>".$mdb->dt[currency_type_name]." (".$mdb->dt[basic_currency]."-".$mdb->dt[price_currency].")</option>";
				}else if(($mdb->dt[is_basic] == "Y" && $selected == "" && $type == "input")){
					$mstring .= "<option value='".$mdb->dt[currency_ix]."' selected>".$mdb->dt[currency_type_name]." (".$mdb->dt[basic_currency]."-".$mdb->dt[price_currency].")</option>";
				}else{
					$mstring .= "<option value='".$mdb->dt[currency_ix]."'>".$mdb->dt[currency_type_name]." (".$mdb->dt[basic_currency]."-".$mdb->dt[price_currency].")</option>";
				}
			}

		}
		$mstring .= "</select>";
	}

	return $mstring;
}
/**
 *  사용유무 반영되도록
 *  disp = 1인 사이트명만 가져오도록 수정 
 *  12. 4. 10 배광호
 */

function getBuyingServiceSiteInfo($selected="", $property=""){
	global $admininfo;
	$mdb = new Database;

	$sql = 	"SELECT cr.* FROM shop_buyingservice_site cr
			where depth = 1 and disp = '1'
			order by vieworder asc ";

	$mdb->query($sql);

	$mstring = "<select name='bs_site' id='bs_site' ".$property."	>";
	$mstring .= "<option value=''>구매대행 사이트</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[site_code] == $selected){
				$mstring .= "<option value='".$mdb->dt[site_code]."' selected>".$mdb->dt[site_name]." (".$mdb->dt[site_domain].")</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[site_code]."'>".$mdb->dt[site_name]." (".$mdb->dt[site_domain].")</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}


/*
function getFirstDIV($selected=""){
	global $admininfo;
	$mdb = new Database;

	$sql = 	"SELECT cr.* FROM shop_buyingservice_site cr
			where depth = 1 
			group by bs_ix order by vieworder asc ";

	$mdb->query($sql);

	$mstring = "<select name='parent_bs_ix' id='parent_bs_ix' disabled>";
	$mstring .= "<option value=''>구매대행 사이트</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[bs_ix] == $selected){
				$mstring .= "<option value='".$mdb->dt[bs_ix]."' selected>".$mdb->dt[site_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[bs_ix]."'>".$mdb->dt[site_name]."</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}
*/
function buyingservice_site_tab($selected = "site"){

	$mstring = " <div class='tab'>
		<table class='s_org_tab'>
			<tr>
				<td class='tab'>
					<!--table id='tab_01' ".($selected == "md" ? "class='on'":"")." >
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='md_manage.php'>MD 목록</a></td>
						<th class='box_03'></th>
					</tr>
					</table-->
					<table id='tab_02' ".($selected == "site" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='site.php'>구매대행사이트</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					
				</td>
			</tr>
			</table>
			</div>";

	return $mstring;
}



?>