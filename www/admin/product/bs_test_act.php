<?
$buying_service_currencyinfo = array(
"bs_air_wt" => "0",
"bs_basic_air_shipping" => "0",
"exchange_rate" => "1220",
"bs_duty_rate" => "0",
"bs_fee_rate" => "0",
"clearance_fee" => "0"
);


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
$result = BuyingServicePriceCalcurate("20",$buying_service_currencyinfo);
$result2 = PriceRoundUpOrDown("round","0",$result[buyingservice_coprice],$result[bs_fee]);

print_r($result2);
?>