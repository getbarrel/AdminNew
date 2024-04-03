<?

include("../class/layout.class");
include_once("buyingService.lib.php");
//print_r($_POST);
//exit;
//session_start();

if($admininfo[company_id] == ""){
	echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert(language_data['common']['C'][language]);parent.document.location.href='/admin/admin.php'</script>";
	exit;
}
$db = new Database;
$mdb = new Database;
$db2 = new Database;

if($act == "currency_type_add"){
	$sql = "insert into shop_buyingservice_currencytype_info(currency_ix,cid,currency_type_name,basic_currency,price_currency,is_basic,disp,regdate)
				values('$currency_ix','$cid','$currency_type_name','$basic_currency','$price_currency','$is_basic','$disp',NOW())";

	$db->sequences = "SHOP_BS_CURRENCYTYPE_INFO_SEQ";
	$db->query ($sql);
	echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('화폐타입이 정상적으로 등록되었습니다 ');parent.document.location.reload();</script>";
		exit;

}

if ($act == "insert"){
		if($usable_round == ""){
			$usable_round = "N";
		}
		$sql = "insert into shop_buyingservice_info
						(bsi_ix,exchange_type,exchange_rate,bs_basic_air_shipping, bs_add_air_shipping, bs_duty,bs_supertax_rate,clearance_fee,bs_fee_rate,usable_round, round_precision, round_type, disp,regdate)
						values
						('$bsi_ix','$exchange_type','$exchange_rate','$bs_basic_air_shipping','$bs_add_air_shipping','$bs_duty','$bs_supertax_rate','$clearance_fee','$bs_fee_rate','$usable_round','$round_precision','$round_type','1',NOW()) ";
		//echo $sql;
		//exit;

		$db->sequences = "SHOP_BS_INFO_SEQ";
		$db->query ($sql);


		$sql = "select * from shop_buyingservice_info where exchange_type = '".$exchange_type."' order by regdate desc limit 0,1 ";
		$db2->query ($sql);

		if($db2->total){
			$db2->fetch();
		//print_r($db2->dt);
			$exchange_rate = $db2->dt[exchange_rate];
			$bs_basic_air_shipping = $db2->dt[bs_basic_air_shipping];
			$bs_add_air_shipping = $db2->dt[bs_add_air_shipping];

			$bs_duty_rate = $db2->dt[bs_duty];
			$clearance_fee = $db2->dt[clearance_fee];
			$bs_supertax_rate = $db2->dt[bs_supertax_rate];

		}


		$bs_info["exchange_rate"] = $exchange_rate;
		//$bs_info["air_wt"] = $bs_air_wt;
		//$bs_info["air_shipping"] = $bs_air_shipping;
		$bs_info["duty_rate"] = $bs_duty_rate;
		$bs_info["clearance_fee"] = $clearance_fee;
		$bs_info["bs_supertax_rate"] = $bs_supertax_rate;

		//print_r($bs_info);
		//exit;

			$sql = "select p.id,pbp.orgin_price, pbp.clearance_type, pbp.air_wt, pbp.air_shipping , pbp.bs_fee_rate, p.buyingservice_coprice
						from shop_product p
						left join shop_product_buyingservice_priceinfo pbp on  p.id = pbp.pid and bs_use_yn = '1'
						where  product_type = 1
						and price_policy = 'N'
						and currency_ix = '".$exchange_type."'
						  "; //and p.id = '044045' limit 1 and state = '1'


		$db->query ($sql);
		//echo $db->total;
		//exit;
		// 상품정보에서 취하는 정보는 air_wt(화물예상무게) , orgin_price(orgin 원가),  clearance_type(통관타입)
		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);
			$bs_info["pid"] = $db->dt[id];
			if($db->dt[orgin_price]){
				$bs_info["orgin_price"] = $db->dt[orgin_price];
			}else{
				$bs_info["orgin_price"] = $db->dt[buyingservice_coprice];
			}
			$bs_info["clearance_type"] = $db->dt[clearance_type];
			$bs_info["air_wt"] = $db->dt[air_wt];
			$bs_info["bs_fee_rate"] = $db->dt[bs_fee_rate];

			if($bs_info["air_wt"] <= 1){
				//$bs_air_shipping = $bs_basic_air_shipping;
				$bs_info["air_shipping"] = $bs_basic_air_shipping ;
			}else{
				$bs_info["air_shipping"] = $bs_basic_air_shipping + ($bs_add_air_shipping * ($bs_info["air_wt"] - 1));//$db->dt[air_shipping];
				//$bs_air_shipping = $bs_basic_air_shipping + ($bs_add_air_shipping * ($bs_air_wt - 1));
			}
			$bs_duty_basis = ($bs_info["orgin_price"]+$bs_info["air_shipping"])*$bs_info["exchange_rate"]; // 부가세대상금액
			//echo $bs_info["clearance_type"]."------";
			//exit;
			if($bs_info["clearance_type"] == "0"){// 목록통관
				$bs_info["bs_duty"] = round($bs_duty_basis*$bs_info["duty_rate"]/100,-1);  // 여기는 관세만 취급된 값이다.
				$bs_info["bs_supertax"] = round(($bs_duty_basis+$bs_info["bs_duty"])*$bs_info["bs_supertax_rate"]/100,-1);
				//$bs_info["clearance_fee"] 해당값은 이미 채워저 있다
				//$bs_info["clearance_fee"] = $bs_info["clearance_fee"];
				//echo $bs_info["bs_supertax"];
			}else{
				$bs_info["bs_duty"] = 0;  // 여기는 관세만 취급된 값이다.
				$bs_info["bs_supertax"] = 0;
			}
			$bs_info["buyingservice_coprice"] = round(($bs_info["orgin_price"]+$bs_info["air_shipping"])*$bs_info["exchange_rate"]+$bs_info["bs_duty"]+$bs_info["bs_supertax"]+$bs_info["clearance_fee"],0);
			$bs_info["coprice"] = $bs_info["buyingservice_coprice"];
			$bs_info["bs_fee"] = round($bs_info["buyingservice_coprice"]*$bs_info["bs_fee_rate"]/100,-1);

			//$bs_info["listprice"] = round($bs_info["buyingservice_coprice"]+$bs_info["bs_fee"],-1);
			//$bs_info["sellprice"] = round($bs_info["buyingservice_coprice"]+$bs_info["bs_fee"],-1);

			if($usable_round == "Y"){

				if($round_type == "round"){
					$bs_info["listprice"] = roundBetterUp($bs_info["buyingservice_coprice"]+$bs_info["bs_fee"],-1*$round_precision);
					$bs_info["sellprice"] = roundBetterUp($bs_info["buyingservice_coprice"]+$bs_info["bs_fee"],-1*$round_precision);
				}else if($round_type == "floor"){
					$bs_info["listprice"] = roundBetterDown($bs_info["buyingservice_coprice"]+$bs_info["bs_fee"],-1*$round_precision);
					$bs_info["sellprice"] = roundBetterDown($bs_info["buyingservice_coprice"]+$bs_info["bs_fee"],-1*$round_precision);
				}

			}else{
				$bs_info["listprice"] = round($bs_info["buyingservice_coprice"]+$bs_info["bs_fee"],-1);
				$bs_info["sellprice"] = round($bs_info["buyingservice_coprice"]+$bs_info["bs_fee"],-1);
			}


			BuyServiceProductPriceUpdate($bs_info);
			/**
             * 환율변경시 옵션 가격업데이트처리
             *
             * 12-11-13 bgh
             */
            /**/
            //define_syslog_variables();
            //openlog("phplog", LOG_PID ,
            $sub_db = new Database;

            $sub_sql = "select * from shop_product_options_detail where pid = '".$bs_info["pid"]."' AND option_coprice > 0";

            $sub_db->query($sub_sql);
            if($db->total){
                $result = $sub_db->fetchAll();

                foreach($result as $rt):

                    $bs_fee_array = BuyingServicePriceCalcurate($rt[option_coprice],$bs_info);
                    $round_result_array = PriceRoundUpOrDown($round_type,$round_precision,$bs_fee_array[buyingservice_coprice],$bs_fee_array[bs_fee]);

                    $data[pid] = $bs_info["pid"];
                    $data[id] = $rt[id];
                    $data[update_price] = $round_result_array[sellprice];

                    $return = BuyServiceOptionPriceUpdate($data);
                    if($return){
                        //syslog(LOG_INFO, '옵션 업데이트 성공 id = '.$data[id]);
                    }else{
                        //syslog(LOG_INFO, '옵션 업데이트 실패 id = '.$data[id]);
                    }

                endforeach;
            }
            //closelog();

            /**/
            set_time_limit(30);


			//$db2->query ("update shop_product_buyingservice_priceinfo set bs_use_yn = '1' where pid ='".$db->dt[id]."' limit 1");
		}



		echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('구매대행 환율/수수료 정보가 정상적으로 변경 되었습니다.');parent.document.location.reload();</script>";
		exit;
}

if ($act == "delete"){

		$sql = "delete from shop_buyingservice_info where bsi_ix = '$bsi_ix' ";
		//echo $sql;
		//exit;
		$db->query ($sql);

		echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('구매대행 환율/수수료 정보가 정상적으로 삭제 되었습니다.');parent.document.location.reload();</script>";
		exit;
}

if ($act == "json"){

		$sql = "select * from shop_buyingservice_info where exchange_type = '$currency_ix' order by regdate desc limit 1  ";
		//echo $sql;
		//exit;
		$db->query ($sql);
		$buyingservice_info = $db->fetchall2("object");
		//print_r($events);
		$datas = str_replace("\"true\"","true",json_encode($buyingservice_info[0]));
		//$datas = str_replace("\"true\"","true",$events);
		$datas = str_replace("\"false\"","false",$datas);
		echo $datas;


}

function BuyServiceProductPriceUpdate($bs_info){
	global $mdb;


	//$db->query("update shop_product_buyingservice_priceinfo set bs_use_yn = '0' where pid ='".$pid."'");
	$mdb->query("update shop_product_buyingservice_priceinfo set bs_use_yn = '0' where pid ='".$bs_info["pid"]."'");
	//echo $bs_info["bs_supertax"]."+".$bs_info["bs_duty"];

	$sql = "insert into shop_product_buyingservice_priceinfo (bsp_ix,pid,orgin_price,exchange_rate,air_wt,air_shipping,duty,clearance_fee,clearance_type,bs_fee_rate,bs_fee,bs_use_yn,regdate)
					values('','".$bs_info["pid"]."','".$bs_info["orgin_price"]."','".$bs_info["exchange_rate"]."','".$bs_info["air_wt"]."','".$bs_info["air_shipping"]."','".($bs_info["bs_supertax"]+$bs_info["bs_duty"])."','".$bs_info["clearance_fee"]."','".$bs_info["clearance_type"]."','".$bs_info["bs_fee_rate"]."','".$bs_info["bs_fee"]."','1',NOW()) ";

	//echo $sql;
	//exit;

	$mdb->sequences = "SHOP_GOODS_BS_PRICEINFO_SEQ";
	$mdb->query($sql);

	$update_sql = "update shop_product p set p.buyingservice_coprice = '".$bs_info["buyingservice_coprice"]."',p.coprice = '".$bs_info["coprice"]."', 	p.listprice = '".$bs_info["listprice"]."', p.sellprice = '".$bs_info["sellprice"]."'
								where p.product_type = '1'  and p.id ='".$bs_info["pid"]."'  ";
	//echo $update_sql."<br>" ;
	/*
	$update_sql = "update shop_product p , shop_product_buyingservice_priceinfo pbp  set
	p.coprice =
	case when clearance_type = '1' then
		(pbp.orgin_price+pbp.air_shipping)*exchange_rate
	else
		(pbp.orgin_price+pbp.air_shipping)*exchange_rate*(1+".$bs_duty_rate."/100)+".$clearance_fee."
	end ,
	p.listprice = round((p.coprice + round(p.coprice*pbp.bs_fee_rate/100) * /100,1)*100,
	p.sellprice = p.listprice
	where p.id = pbp.pid and bs_use_yn = '1' and p.product_type = '1'  and p.id ='".$bs_info["pid"]."'  ";
	echo $update_sql ;
	*/
	$mdb->query($update_sql);

	//exit;
	//$sql = "update shop_product set  order by regdate desc limit 1 ";

}
function BuyServiceOptionPriceUpdate($data){
    global $mdb;

    $sql = "update shop_product_options_detail set option_price = '".$data[update_price]."' where pid = '".$data[pid]."' AND id = '".$data[id]."'";
    if($mdb->query($sql)){
        return true;
    }else{
        return false;
    }


}
?>