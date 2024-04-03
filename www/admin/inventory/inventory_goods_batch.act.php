<?
include("../class/layout.class");
include("./inventory.lib.php");

if($search_searialize_value){
	$unserialize_search_value = unserialize(urldecode($search_searialize_value));
	extract($unserialize_search_value);
}

$db = new Database;

if($admininfo[admin_level] == 9){
	$where = "where g.gid Is NOT NULL ";

	if($admininfo[mem_type] == "MD"){
		$where .= " and g.admin in (".getMySellerList($admininfo[charger_ix]).") ";
	}

}else{
	$where = "where g.gid Is NOT NULL and g.admin ='".$admininfo[company_id]."' ";
}

if($search_text != ""){
	if($search_type == "gname_gid"){
		$where .= "and (g.gname LIKE '%".$search_text."%' or g.gid LIKE '%".$search_text."%') ";
	}else{
		$where .= "and ".$search_type." LIKE '%".$search_text."%' ";
	}
}


if($company_id != ""){
	$where .= "and pi.company_id = '".$company_id."' ";
}

if($pi_ix != ""){
	$where .= "and pi.pi_ix = '".$pi_ix."' ";
}

if($ps_ix != ""){
	$where .= "and ps.ps_ix = '".$ps_ix."' ";
}


if($item_account != ""){
	$where .= "and g.item_account = '".$item_account."' ";
}


switch ($depth){
	case 0:
		$cut_num = 3;
		break;
	case 1:
		$cut_num = 6;
		break;
	case 2:
		$cut_num = 9;
		break;
	case 3:
		$cut_num = 12;
		break;
	case 4:
		$cut_num = 15;
		break;
}

if ($cid2){
	$where .= " and g.cid LIKE '".substr($cid2,0,$cut_num)."%' ";
}


$sql = "select data.*
	from (
		select g.gid, gu.gu_ix
		from inventory_goods g left join inventory_place_info pi on (g.pi_ix = pi.pi_ix)  
		left join inventory_goods_unit gu on (g.gid=gu.gid)
		left join  inventory_place_section ps on g.ps_ix = ps.ps_ix
		$where
	) data
	 ";
$db->query($sql);
$inventory_goods = $db->fetchall("object");


if ($update_kind == "price_each"){// 개별가격수정

	if($update_type == 2){// 선택회원일때

		for($i=0;$i<count($gu_ix);$i++){
			if($admininfo[admin_level] == 9){
				$sql = "UPDATE inventory_goods_unit SET 
					offline_wholesale_price = '".$offline_wholesale_price[$gu_ix[$i]]."' , 
					wholesale_price = '".$wholesale_price[$gu_ix[$i]]."' ,
					wholesale_sellprice = '".$wholesale_sellprice[$gu_ix[$i]]."' ,
					sellprice = '".$sellprice[$gu_ix[$i]]."' ,
					discount_price = '".$discount_price[$gu_ix[$i]]."' 
				 Where gu_ix = '".$gu_ix[$i]."' ";
				$db->query ($sql);
			}
		}

		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택품목의 가격변경이 적상적으로 완료되었습니다.');</script>");
		echo("<script>if(top.document.getElementById('act').src != 'about:blank'){top.select_update_unloading();top.location.reload();}else{top.location.reload();}</script>");

	}
	exit;
}


if ($update_kind == "price_batch"){//일괄가격수정
	
	$update_str = "";

	if($batch_offline_wholesale_price > 0)					$update_str[]= "offline_wholesale_price = '".$batch_offline_wholesale_price."'";
	if($batch_wholesale_price > 0)								$update_str[]= "wholesale_price = '".$batch_wholesale_price."'";
	if($batch_wholesale_sellprice > 0)							$update_str[]= "wholesale_sellprice = '".$batch_wholesale_sellprice."'";
	if($batch_sellprice > 0)											$update_str[]= "sellprice = '".$batch_sellprice."'";
	if($batch_discount_price > 0)								$update_str[]= "discount_price = '".$batch_discount_price."'";

	if($update_type == 2){// 선택회원일때

		if($admininfo[admin_level] == 9){
			if(count($update_str)){
				$sql = "UPDATE inventory_goods_unit SET 
					".implode(",",$update_str)."
				 Where gu_ix in ('".implode("','",$gu_ix)."') ";
				$db->query ($sql);
			}
		}

		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택품목의 가격변경이 적상적으로 완료되었습니다.');</script>");
		echo("<script>if(top.document.getElementById('act').src != 'about:blank'){top.select_update_unloading();top.location.reload();}else{top.location.reload();}</script>");

	}else{// 검색회원일때
			
			for($i=0;$i<count($inventory_goods);$i++){
				$tmp_gu_ix[] = $inventory_goods[$i]["gu_ix"];
			}
			
			if(count($update_str)){
				$sql = "UPDATE inventory_goods_unit SET 
						".implode(",",$update_str)."
					where gu_ix in ('".implode("','",$tmp_gu_ix)."')";
				$db->query($sql);
			}

			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('검색품목의 가격변경이 적상적으로 완료되었습니다.');</script>");
			echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");

	}

}


if ($update_kind == "price_coprice"){// 공급가대비가격수정
	
	$update_str = "";

	if($wholesale_price_value > 0)							$update_str[]= "wholesale_price = offline_wholesale_price * '".($wholesale_price_value/100)."'";

	if($wholesale_sellprice_value > 0){
		if($wholesale_sellprice_type=="R")				$update_str[]= "wholesale_sellprice = wholesale_price * '".($wholesale_sellprice_value/100)."'"; //율%
		else																$update_str[]= "wholesale_sellprice = wholesale_price - '".$wholesale_sellprice_value."'"; //원
	}

	if($sellprice_value > 0)										$update_str[]= "sellprice = offline_wholesale_price * '".($sellprice_value/100)."'";

	if($discount_price_value > 0){
		if($discount_price_type=="R")						$update_str[]= "discount_price = sellprice * '".($discount_price_value/100)."'"; //율%
		else																$update_str[]= "discount_price = sellprice - '".$discount_price_value."'"; //원
	}

	if($update_type == 2){// 선택회원일때

		if($admininfo[admin_level] == 9){
			if(count($update_str)){
				$sql = "UPDATE inventory_goods_unit SET 
					".implode(",",$update_str)."
				 Where gu_ix in ('".implode("','",$gu_ix)."') ";
				$db->query ($sql);
			}
		}

		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택품목의 가격변경이 적상적으로 완료되었습니다.');</script>");
		echo("<script>if(top.document.getElementById('act').src != 'about:blank'){top.select_update_unloading();top.location.reload();}else{top.location.reload();}</script>");

	}else{// 검색회원일때
			
			for($i=0;$i<count($inventory_goods);$i++){
				$tmp_gu_ix[] = $inventory_goods[$i]["gu_ix"];
			}
			
			if(count($update_str)){
				$sql = "UPDATE inventory_goods_unit SET 
						".implode(",",$update_str)."
					where gu_ix in ('".implode("','",$tmp_gu_ix)."')";
				$db->query($sql);
			}

			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('검색품목의 가격변경이 적상적으로 완료되었습니다.');</script>");
			echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");

	}

}


//////////////////////////////////////////////품목다중가격 시작 ////////////////////////////////////////////////////////////

if($mode == 'batch_search'){

	if($admininfo[admin_level] == 9){
		$batch_where = "where g.gid is NOT NULL ";

		if($admininfo[mem_type] == "MD"){
			$batch_where .= " and g.admin in (".getMySellerList($admininfo[charger_ix]).") ";
		}

	}else{
		$batch_where = "where g.gid is NOT NULL and g.admin ='".$admininfo[company_id]."' ";
	}

	if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
		//다중검색 시작 2014-04-10 이학봉
		if($search_text != ""){
			if(strpos($search_text,",") !== false){
				$search_array = explode(",",$search_text);
				$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
				$batch_where .= "and ( ";
				for($i=0;$i<count($search_array);$i++){
					$search_array[$i] = trim($search_array[$i]);
					if($search_array[$i]){
						if($i == count($search_array) - 1){
							$batch_where .= "g.".$search_type." = '".trim($search_array[$i])."'";
						}else{
							$batch_where .= "g.".$search_type." = '".trim($search_array[$i])."' or ";
						}
					}
				}
				$batch_where .= ")";
			}else if(strpos($search_text,"\n") !== false){//\n
				$search_array = explode("\n",$search_text);
				$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
				$batch_where .= "and ( ";

				for($i=0;$i<count($search_array);$i++){
					$search_array[$i] = trim($search_array[$i]);
					if($search_array[$i]){
						if($i == count($search_array) - 1){
							$batch_where .= "g.".$search_type." = '".trim($search_array[$i])."'";
						}else{
							$batch_where .= "g.".$search_type." = '".trim($search_array[$i])."' or ";
						}
					}
				}
				$batch_where .= ")";
			}else{
				$batch_where .= " and g.".$search_type." = '".trim($search_text)."'";
			}
		}

	}else{	//검색어 단일검색
		if($search_text != ""){
			if($search_type == "gname_gid"){
				$batch_where .= "and (g.gname LIKE '%".$search_text."%' or g.gid LIKE '%".$search_text."%') ";
			}else{
				$batch_where .= "and ".$search_type." LIKE '%".$search_text."%' ";
			}
		}
	}

	if(is_array($cid) && count($cid) > 0){		//품목카테고리 다중검색
		$batch_where .= " and g.cid in (".implode(",",$cid).")";
	}

	if($is_use != ""){
		$batch_where .= " and g.is_use = '".$is_use."' ";
	}

}

if($update_kind == "update_sellprice" || $update_kind == "update_sellprice_rate" || $update_kind == "update_sellprice_multi"){
	if($update_type == '2'){	//선택한 품목
		$select_gid = $select_gid;
	}else{
		
		$sql = "select
				*
				from
					inventory_goods as g 
					left join inventory_goods_multi_price as gmp on (g.gid = gmp.gid)
				$batch_where
				";
		
		$db->query($sql);
		$data_array = $db->fetchall();

		for($i=0;$i<count($data_array);$i++){
			$select_gid[$data_array[$i][gid]][$data_array[$i][gu_ix]] = $data_array[$i][gu_ix];
		}

	}

}

//품목다중가격 일괄수정 시작 2014-05-06 이학봉
if($update_kind == "update_sellprice"){			//개별가격수정(공급가제외  수정가능)
	//일반상품에서 : inventory_goods_unit,  shop_product, shop_product_options_detail 에 할인가,기본도매가,기본소매가 수정

	if(is_array($select_gid) && count($select_gid) > 0){
		foreach($select_gid as $gid => $gu_info){
			foreach($gu_info as $key => $gu_ix){
				foreach($goods_price[$gid] as $is_wholesale => $gu_detail ){
						
						$sellprice = ($use_buyingprice_rate[$gid] == '1' && $gu_detail[$gu_ix][buying_price] > $gu_detail[$gu_ix][sellprice]?$gu_detail[$gu_ix][buying_price]:$gu_detail[$gu_ix][sellprice]);

						$product_sellprice = ($use_buyingprice_rate[$gid] == '1' && $gu_detail[$gu_ix][buying_price] > $gu_detail[$gu_ix][product_sellprice]?$gu_detail[$gu_ix][buying_price]:$gu_detail[$gu_ix][product_sellprice]);

						$type_a_sellprice = ($use_buyingprice_rate[$gid] == '1' && $gu_detail[$gu_ix][buying_price] > $gu_detail[$gu_ix][type_a_sellprice]?$gu_detail[$gu_ix][buying_price]:$gu_detail[$gu_ix][type_a_sellprice]);
					
						$type_b_sellprice = ($use_buyingprice_rate[$gid] == '1' && $gu_detail[$gu_ix][buying_price] > $gu_detail[$gu_ix][type_b_sellprice]?$gu_detail[$gu_ix][buying_price]:$gu_detail[$gu_ix][type_b_sellprice]);

						$type_c_sellprice = ($use_buyingprice_rate[$gid] == '1' && $gu_detail[$gu_ix][buying_price] > $gu_detail[$gu_ix][type_c_sellprice]?$gu_detail[$gu_ix][buying_price]:$gu_detail[$gu_ix][type_c_sellprice]);

						$type_d_sellprice = ($use_buyingprice_rate[$gid] == '1' && $gu_detail[$gu_ix][buying_price] > $gu_detail[$gu_ix][type_d_sellprice]?$gu_detail[$gu_ix][buying_price]:$gu_detail[$gu_ix][type_d_sellprice]);

						$type_e_sellprice = ($use_buyingprice_rate[$gid] == '1' && $gu_detail[$gu_ix][buying_price] > $gu_detail[$gu_ix][type_e_sellprice]?$gu_detail[$gu_ix][buying_price]:$gu_detail[$gu_ix][type_e_sellprice]);
						
						
						$sql = "update inventory_goods_multi_price set
									sellprice = '".$sellprice."',
									product_sellprice = '".$product_sellprice."',
									type_a_sellprice = '".$type_a_sellprice."',
									type_b_sellprice = '".$type_b_sellprice."',
									type_c_sellprice = '".$type_c_sellprice."',
									type_d_sellprice = '".$type_d_sellprice."',
									type_e_sellprice = '".$type_e_sellprice."',

									sellprice_rate = '0',
									product_sellprice_rate = '".round((1-$product_sellprice/$sellprice)*100)."',
									type_a_sellprice_rate = '".round((1-$type_a_sellprice/$sellprice)*100)."',
									type_b_sellprice_rate = '".round((1-$type_b_sellprice/$sellprice)*100)."',
									type_c_sellprice_rate = '".round((1-$type_c_sellprice/$sellprice)*100)."',
									type_d_sellprice_rate = '".round((1-$type_d_sellprice/$sellprice)*100)."',
									type_e_sellprice_rate = '".round((1-$type_e_sellprice/$sellprice)*100)."'

								where
									gid = '".$gid."'
									and gu_ix = '".$gu_ix."'
									and is_wholesale = '".$is_wholesale."'
									";
						$db->query($sql);

						/*
						어라운지만 적용	(기본가, 할인가 변경시 아래 정보도 변경)
						2. shop_product_options_detail	 할인가(소매할인가,도매할인가)
						3. shop_product					 할인가(소매할인가,도매할인가)
						*/
						$sql = "select
									gid,
									gu_ix,
									is_wholesale,
									sellprice,
									product_sellprice 
								from 
									inventory_goods_multi_price 
								where
									gu_ix = '".$gu_ix."'
									and is_wholesale = '".$is_wholesale."'";

						$db->query($sql);
						$data_array = $db->fetchall();

						for($i=0;$i<count($data_array);$i++){
							
							if($data_array[$i][is_wholesale] == 'R'){
								$inventory_goods_unit = " sellprice = '".$data_array[$i][sellprice]."' ";				//품목 기본소매가
								$shop_product = " listprice = '".$data_array[$i][sellprice]."', sellprice = '".$data_array[$i][product_sellprice]."' ";	//상품 소매판매가, 소매할인가
								$shop_product_options_detail = " option_listprice = '".$data_array[$i][sellprice]."', option_price = '".$data_array[$i][product_sellprice]."' ";	//옵션 소매판매가,소매할인가
							}else{
								$inventory_goods_unit = " wholesale_price = '".$data_array[$i][sellprice]."' ";		//품목 기본도매가
								$shop_product = " wholesale_price = '".$data_array[$i][sellprice]."', wholesale_sellprice = '".$data_array[$i][product_sellprice]."' ";	//상품 도매판매가, 도매할인가
								$shop_product_options_detail = " option_wholesale_listprice = '".$data_array[$i][sellprice]."', option_wholesale_price = '".$data_array[$i][product_sellprice]."' ";	//옵션 소매판매가,소매할인가
							}

							$sql = "update inventory_goods_unit set
										$inventory_goods_unit
									where
										gu_ix = '".$gu_ix."'";
										//echo nl2br($sql)."<br>";
							$db->query($sql);

							$sql = "update shop_product set
										$shop_product
									where
										pcode = '".$gu_ix."'
										and product_type = '0'
										";
										//echo nl2br($sql)."<br>";
							$db->query($sql);
							
							//상품 옵션 금액 변경 시작 
							$m_table_name = "option_id_table_".date("i");
							$db->query("create temporary table ".$m_table_name." (id int)");
							$db->query("insert into ".$m_table_name."(id) 
										select 
											od.id
										from
											shop_product as p 
											inner join shop_product_options_detail  as od on (p.id = od.pid)
										where
											p.product_type = '0'
											and od.option_code = '".$gu_ix."'");
							
							$sql = "update shop_product_options_detail set
										$shop_product_options_detail
									where
										id in (select id from ".$m_table_name.")";
							$db->query($sql);

							$db->query("drop table ".$m_table_name."");
							//상품 옵션 금액 변경 끝
							

						}
				}
			}
		}

		echo("<script>alert('검색품목의 가격변경이 적상적으로 완료되었습니다.');</script>");
		echo("<script>parent.location.reload()</script>");
	}

}


if($update_kind == "update_sellprice_rate"){			//판매가대비가격수정

	$round_type = $round_type;	//반올림타입 round, floor
	$round_cnt = $round_cnt;	//반올림 자리수 10,100,1000

	if(is_array($select_gid) && count($select_gid) > 0){
		foreach($select_gid as $gid => $gu_info){
			foreach($gu_info as $key => $gu_ix){
				foreach($goods_price[$gid] as $is_wholesale => $gu_detail ){
					if($batch[rate][$is_wholesale][check] == '1'){	//도매,소매 비율 수정 체크되엇을경우 .... 실행
					//판매가대비 가격수정(할인 비율) 기본가격 기준 (sellprice * ((100 - 할인비율)/100))
						if($gu_detail[$gu_ix][sellprice] > 0){

							$product_sellprice = $gu_detail[$gu_ix][sellprice]*((100-$batch[rate][$is_wholesale][product_sellprice])/100);
							$type_a_sellprice = $gu_detail[$gu_ix][sellprice]*((100-$batch[rate][$is_wholesale][a])/100);
							$type_b_sellprice = $gu_detail[$gu_ix][sellprice]*((100-$batch[rate][$is_wholesale][b])/100);
							$type_c_sellprice = $gu_detail[$gu_ix][sellprice]*((100-$batch[rate][$is_wholesale][c])/100);
							$type_d_sellprice = $gu_detail[$gu_ix][sellprice]*((100-$batch[rate][$is_wholesale][d])/100);
							$type_e_sellprice = $gu_detail[$gu_ix][sellprice]*((100-$batch[rate][$is_wholesale][e])/100);
							
							if($round_type[rate] == 'round'){
								$product_sellprice = round($product_sellprice,$round_cnt[rate]);//direction : 내림 round : 올림
								$type_a_sellprice = round($type_a_sellprice,$round_cnt[rate]);//direction : 내림 round : 올림
								$type_b_sellprice = round($type_b_sellprice,$round_cnt[rate]);//direction : 내림 round : 올림
								$type_c_sellprice = round($type_c_sellprice,$round_cnt[rate]);//direction : 내림 round : 올림
								$type_d_sellprice = round($type_d_sellprice,$round_cnt[rate]);//direction : 내림 round : 올림
								$type_e_sellprice = round($type_e_sellprice,$round_cnt[rate]);//direction : 내림 round : 올림
							}else{
								$product_sellprice = floorBetter_admin($product_sellprice,$round_cnt[rate],$round_type[rate]);//direction : 내림 round : 올림
								$type_a_sellprice = floorBetter_admin($type_a_sellprice,$round_cnt[rate],$round_type[rate]);//direction : 내림 round : 올림
								$type_b_sellprice = floorBetter_admin($type_b_sellprice,$round_cnt[rate],$round_type[rate]);//direction : 내림 round : 올림
								$type_c_sellprice = floorBetter_admin($type_c_sellprice,$round_cnt[rate],$round_type[rate]);//direction : 내림 round : 올림
								$type_d_sellprice = floorBetter_admin($type_d_sellprice,$round_cnt[rate],$round_type[rate]);//direction : 내림 round : 올림
								$type_e_sellprice = floorBetter_admin($type_e_sellprice,$round_cnt[rate],$round_type[rate]);//direction : 내림 round : 올림
							}


							$product_sellprice = ($use_buyingprice_rate[$gid] == '1' && $gu_detail[$gu_ix][buying_price] > $product_sellprice?$gu_detail[$gu_ix][buying_price]:$product_sellprice);	//판매가대비 가격수정 사용시 공급가보다 작을시 공급가로 측정

							$type_a_sellprice = ($use_buyingprice_rate[$gid] == '1' && $gu_detail[$gu_ix][buying_price] > $type_a_sellprice?$gu_detail[$gu_ix][buying_price]:$type_a_sellprice);

							$type_b_sellprice = ($use_buyingprice_rate[$gid] == '1' && $gu_detail[$gu_ix][buying_price] > $type_b_sellprice?$gu_detail[$gu_ix][buying_price]:$type_b_sellprice);

							$type_c_sellprice = ($use_buyingprice_rate[$gid] == '1' && $gu_detail[$gu_ix][buying_price] > $type_c_sellprice?$gu_detail[$gu_ix][buying_price]:$type_c_sellprice);

							$type_d_sellprice = ($use_buyingprice_rate[$gid] == '1' && $gu_detail[$gu_ix][buying_price] > $type_d_sellprice?$gu_detail[$gu_ix][buying_price]:$type_d_sellprice);

							$type_e_sellprice = ($use_buyingprice_rate[$gid] == '1' && $gu_detail[$gu_ix][buying_price] > $type_e_sellprice?$gu_detail[$gu_ix][buying_price]:$type_e_sellprice);


							$sql = "update inventory_goods_multi_price set

										product_sellprice = '".$product_sellprice."',
										type_a_sellprice = '".$type_a_sellprice."',
										type_b_sellprice = '".$type_b_sellprice."',
										type_c_sellprice = '".$type_c_sellprice."',
										type_d_sellprice = '".$type_d_sellprice."',
										type_e_sellprice = '".$type_e_sellprice."',

										sellprice_rate = '0',
										product_sellprice_rate = '".$batch[rate][$is_wholesale][product_sellprice]."',
										type_a_sellprice_rate = '".$batch[rate][$is_wholesale][a]."',
										type_b_sellprice_rate = '".$batch[rate][$is_wholesale][b]."',
										type_c_sellprice_rate = '".$batch[rate][$is_wholesale][c]."',
										type_d_sellprice_rate = '".$batch[rate][$is_wholesale][d]."',
										type_e_sellprice_rate = '".$batch[rate][$is_wholesale][e]."'
									where
										gid = '".$gid."'
										and gu_ix = '".$gu_ix."'
										and is_wholesale = '".$is_wholesale."'
										";
										//echo nl2br($sql)."<br>";
							$db->query($sql);

							/*
							어라운지만 적용	(기본가, 할인가 변경시 아래 정보도 변경)
							2. shop_product_options_detail	 할인가(소매할인가,도매할인가)
							3. shop_product					 할인가(소매할인가,도매할인가)
							*/
							$sql = "select
										gid,
										gu_ix,
										is_wholesale,
										sellprice,
										product_sellprice 
									from 
										inventory_goods_multi_price 
									where
										gu_ix = '".$gu_ix."'
										and is_wholesale = '".$is_wholesale."'";

							$db->query($sql);
							$data_array = $db->fetchall();

							for($i=0;$i<count($data_array);$i++){
								
								if($data_array[$i][is_wholesale] == 'R'){
									$inventory_goods_unit = " sellprice = '".$data_array[$i][sellprice]."' ";				//품목 기본소매가
									$shop_product = " sellprice = '".$data_array[$i][product_sellprice]."' ";	//상품 소매할인가
									$shop_product_options_detail = " option_price = '".$data_array[$i][product_sellprice]."' ";	//옵션 ,소매할인가
								}else{
									$inventory_goods_unit = " wholesale_price = '".$data_array[$i][sellprice]."' ";		//품목 기본도매가
									$shop_product = " wholesale_sellprice = '".$data_array[$i][product_sellprice]."' ";	//상품 도매판매가, 도매할인가
									$shop_product_options_detail = " option_wholesale_price = '".$data_array[$i][product_sellprice]."' ";	//옵션 소매판매가,소매할인가
								}
								
								//일반상품의 할인가만 수정가능
								$sql = "update shop_product set
											$shop_product
										where
											pcode = '".$gu_ix."'
											and product_type = '0'";
											//echo nl2br($sql)."<br>";
								$db->query($sql);
								
								//상품 옵션 금액 변경 시작 
								$m_table_name = "option_id_table_".date("i");
								$db->query("create temporary table ".$m_table_name." (id int)");
								$db->query("insert into ".$m_table_name."(id) 
											select 
												od.id
											from
												shop_product as p 
												inner join shop_product_options_detail  as od on (p.id = od.pid)
											where
												p.product_type = '0'
												and od.option_code = '".$gu_ix."'");
								
								$sql = "update shop_product_options_detail set
											$shop_product_options_detail
										where
											id in (select id from ".$m_table_name.")";
								$db->query($sql);

								$db->query("drop table ".$m_table_name."");
								//상품 옵션 금액 변경 끝
							}
						}
					}else{
						continue;
					}
				}
			}
		}
		
		echo("<script>alert('검색품목의 가격변경이 적상적으로 완료되었습니다.');</script>");
		echo("<script>parent.location.reload()</script>");
	}
}

if($update_kind == "update_sellprice_multi"){			//매입가대비가격수정

	$round_type = $round_type;	//반올림타입 round, floor
	$round_cnt = $round_cnt;	//반올림 자리수 10,100,1000

	if(is_array($select_gid) && count($select_gid) > 0){
		foreach($select_gid as $gid => $gu_info){
			foreach($gu_info as $key => $gu_ix){
				foreach($goods_price[$gid] as $is_wholesale => $gu_detail ){

					if($batch[multi][$is_wholesale][check] == '1'){	//도매,소매 배당 수정 체크되엇을경우 .... 실행
					//매입가대비 가격수정 매입가(공급가) * 배당 

						if($gu_detail[$gu_ix][buying_price] > 0){
							
							$product_sellprice = $batch[multi][$is_wholesale][product_sellprice] > 0?$gu_detail[$gu_ix][buying_price] * $batch[multi][$is_wholesale][product_sellprice]:$gu_detail[$gu_ix][product_sellprice];

							$sellprice = $batch[multi][$is_wholesale][sellprice] > 0?$gu_detail[$gu_ix][buying_price] * $batch[multi][$is_wholesale][sellprice]:$gu_detail[$gu_ix][sellprice];

							$type_a_sellprice = $batch[multi][$is_wholesale][a] > 0?$gu_detail[$gu_ix][buying_price] * $batch[multi][$is_wholesale][a]:$gu_detail[$gu_ix][type_a_sellprice];

							$type_b_sellprice = $batch[multi][$is_wholesale][b] > 0?$gu_detail[$gu_ix][buying_price] * $batch[multi][$is_wholesale][b]:$gu_detail[$gu_ix][type_b_sellprice];

							$type_c_sellprice = $batch[multi][$is_wholesale][c] > 0?$gu_detail[$gu_ix][buying_price] * $batch[multi][$is_wholesale][c]:$gu_detail[$gu_ix][type_c_sellprice];

							$type_d_sellprice = $batch[multi][$is_wholesale][d] > 0?$gu_detail[$gu_ix][buying_price] * $batch[multi][$is_wholesale][d]:$gu_detail[$gu_ix][type_d_sellprice];

							$type_e_sellprice = $batch[multi][$is_wholesale][e] > 0?$gu_detail[$gu_ix][buying_price] * $batch[multi][$is_wholesale][e]:$gu_detail[$gu_ix][type_e_sellprice];
							
							if($round_type[rate] == 'round'){
								$product_sellprice = round($product_sellprice,$round_cnt[multi]);//direction : 내림 round : 올림
								$sellprice = round($sellprice,$round_cnt[multi]);//direction : 내림 round : 올림
								$type_a_sellprice = round($type_a_sellprice,$round_cnt[multi]);//direction : 내림 round : 올림
								$type_b_sellprice = round($type_b_sellprice,$round_cnt[multi]);//direction : 내림 round : 올림
								$type_c_sellprice = round($type_c_sellprice,$round_cnt[multi]);//direction : 내림 round : 올림
								$type_d_sellprice = round($type_d_sellprice,$round_cnt[multi]);//direction : 내림 round : 올림
								$type_e_sellprice = round($type_e_sellprice,$round_cnt[multi]);//direction : 내림 round : 올림
							}else{
								$product_sellprice = floorBetter_admin($product_sellprice,$round_cnt[multi],$round_type[multi]);//direction : 내림 round : 올림
								$sellprice = floorBetter_admin($sellprice,$round_cnt[multi],$round_type[multi]);//direction : 내림 round : 올림
								$type_a_sellprice = floorBetter_admin($type_a_sellprice,$round_cnt[multi],$round_type[multi]);//direction : 내림 round : 올림
								$type_b_sellprice = floorBetter_admin($type_b_sellprice,$round_cnt[multi],$round_type[multi]);//direction : 내림 round : 올림
								$type_c_sellprice = floorBetter_admin($type_c_sellprice,$round_cnt[multi],$round_type[multi]);//direction : 내림 round : 올림
								$type_d_sellprice = floorBetter_admin($type_d_sellprice,$round_cnt[multi],$round_type[multi]);//direction : 내림 round : 올림
								$type_e_sellprice = floorBetter_admin($type_e_sellprice,$round_cnt[multi],$round_type[multi]);//direction : 내림 round : 올림
							}
							

							$product_sellprice = ($use_buyingprice_rate[$gid] == '1' && $gu_detail[$gu_ix][buying_price] > $product_sellprice?$gu_detail[$gu_ix][sellprice]:$product_sellprice);	//공급가대비율 사용시 공급가보다 작을시 공급가로 측정

							$sellprice = ($use_buyingprice_rate[$gid] == '1' && $gu_detail[$gu_ix][buying_price] > $sellprice?$gu_detail[$gu_ix][sellprice]:$sellprice);

							$type_a_sellprice = ($use_buyingprice_rate[$gid] == '1' && $gu_detail[$gu_ix][buying_price] > $type_a_sellprice?$gu_detail[$gu_ix][sellprice]:$type_a_sellprice);

							$type_b_sellprice = ($use_buyingprice_rate[$gid] == '1' && $gu_detail[$gu_ix][buying_price] > $type_b_sellprice?$gu_detail[$gu_ix][sellprice]:$type_b_sellprice);

							$type_c_sellprice = ($use_buyingprice_rate[$gid] == '1' && $gu_detail[$gu_ix][buying_price] > $type_c_sellprice?$gu_detail[$gu_ix][sellprice]:$type_c_sellprice);

							$type_d_sellprice = ($use_buyingprice_rate[$gid] == '1' && $gu_detail[$gu_ix][buying_price] > $type_d_sellprice?$gu_detail[$gu_ix][sellprice]:$type_d_sellprice);

							$type_e_sellprice = ($use_buyingprice_rate[$gid] == '1' && $gu_detail[$gu_ix][buying_price] > $type_e_sellprice?$gu_detail[$gu_ix][sellprice]:$type_e_sellprice);

							$sql = "update inventory_goods_multi_price set
										product_sellprice = '".$product_sellprice."',
										sellprice = '".$sellprice."',
										type_a_sellprice = '".$type_a_sellprice."',
										type_b_sellprice = '".$type_b_sellprice."',
										type_c_sellprice = '".$type_c_sellprice."',
										type_d_sellprice = '".$type_d_sellprice."',
										type_e_sellprice = '".$type_e_sellprice."',

										product_sellprice_rate = '".round((1-$product_sellprice/$sellprice)*100)."',
										sellprice_rate = '0',
										type_a_sellprice_rate = '".round((1-$type_a_sellprice/$sellprice)*100)."',
										type_b_sellprice_rate = '".round((1-$type_b_sellprice/$sellprice)*100)."',
										type_c_sellprice_rate = '".round((1-$type_c_sellprice/$sellprice)*100)."',
										type_d_sellprice_rate = '".round((1-$type_d_sellprice/$sellprice)*100)."',
										type_e_sellprice_rate = '".round((1-$type_e_sellprice/$sellprice)*100)."'
									where
										gid = '".$gid."'
										and gu_ix = '".$gu_ix."'
										and is_wholesale = '".$is_wholesale."'
										";
										
							$db->query($sql);
	
							/*
							어라운지만 적용	(기본가, 할인가 변경시 아래 정보도 변경)
							1. inventory_goods_unit 금액변경	기본가(기본도매가,기본판매가)
							2. shop_product_options_detail		기본가(소매 판매가,도매판매가) 할인가(소매할인가,도매할인가)
							3. shop_product						기본가(소매 판매가,도매판매가) 할인가(소매할인가,도매할인가)
							*/
							$sql = "select
										gid,
										gu_ix,
										is_wholesale,
										sellprice,
										product_sellprice 
									from 
										inventory_goods_multi_price 
									where
										gu_ix = '".$gu_ix."'
										and is_wholesale = '".$is_wholesale."'";

							$db->query($sql);
							$data_array = $db->fetchall();

							for($i=0;$i<count($data_array);$i++){
								
								if($data_array[$i][is_wholesale] == 'R'){
									$inventory_goods_unit = " sellprice = '".$data_array[$i][sellprice]."' ";				//품목 기본소매가
									$shop_product = " listprice = '".$data_array[$i][sellprice]."', sellprice = '".$data_array[$i][product_sellprice]."' ";	//상품 소매판매가, 소매할인가
									$shop_product_options_detail = " option_listprice = '".$data_array[$i][sellprice]."', option_price = '".$data_array[$i][product_sellprice]."' ";	//옵션 소매판매가,소매할인가
								}else{
									$inventory_goods_unit = " wholesale_price = '".$data_array[$i][sellprice]."' ";		//품목 기본도매가
									$shop_product = " wholesale_price = '".$data_array[$i][sellprice]."', wholesale_sellprice = '".$data_array[$i][product_sellprice]."' ";	//상품 도매판매가, 도매할인가
									$shop_product_options_detail = " option_wholesale_listprice = '".$data_array[$i][sellprice]."', option_wholesale_price = '".$data_array[$i][product_sellprice]."' ";	//옵션 소매판매가,소매할인가
								}
								
								//품목의 기본도매가,기본소매가 수정
								$sql = "update inventory_goods_unit set
											$inventory_goods_unit
										where
											gu_ix = '".$gu_ix."'";
											//echo nl2br($sql)."<br>";
								$db->query($sql);
								
								//일반상품의 할인가,소매,도매 판매가 수정
								$sql = "update shop_product set
											$shop_product
										where
											pcode = '".$gu_ix."'
											and product_type = '0'
											";
											//echo nl2br($sql)."<br>";
								$db->query($sql);

								//상품 옵션 금액 변경 시작 
								$m_table_name = "option_id_table_".date("i");
								$db->query("create temporary table ".$m_table_name." (id int)");
								$db->query("insert into ".$m_table_name."(id) 
											select 
												od.id
											from
												shop_product as p 
												inner join shop_product_options_detail  as od on (p.id = od.pid)
											where
												p.product_type = '0'
												and od.option_code = '".$gu_ix."'");
								
								$sql = "update shop_product_options_detail set
											$shop_product_options_detail
										where
											id in (select id from ".$m_table_name.")";
								$db->query($sql);

								$db->query("drop table ".$m_table_name."");
								//상품 옵션 금액 변경 끝

							}
						}
					}else{
						continue;
					}
				}
				
			}
		
		}
		
		echo("<script>alert('검색품목의 가격변경이 적상적으로 완료되었습니다.');</script>");
		echo("<script>parent.location.reload()</script>");
	}

}
//품목다중가격 일괄수정 끝 2014-05-06 이학봉


//////////////////////


//품목가격수정 시작 도매,소매가 수정 2014-05-07 이학봉

if($update_kind == "update_list_once" || $update_kind == "update_list_batch" || $update_kind == "update_list_buying_price" || $update_kind == "update_list_sellprice"){
	if($update_type == '2'){	//선택한 품목
		$select_gid = $select_gid;
	}else{
		
		$sql = "select
			data.*,
			gu.unit,
			gu.gu_ix,
			gu.buying_price,
			gu.sellprice,
			gu.wholesale_price,
		(select count(gu_ix) as gu_cnt from inventory_goods_unit where gid = data.gid) as gu_cnt
		from (
			select 
				g.cid,g.gid, g.gname, g.gcode, 
				g.admin, g.item_account, g.ci_ix, g.pi_ix, 
				g.is_use, g.standard, date_format(g.regdate,'%Y-%m-%d') as regdate
				
			from 
				inventory_goods g 
				left join inventory_place_info pi on (g.pi_ix = pi.pi_ix)  
			$batch_where
				
		) data
		left join inventory_goods_unit gu on (data.gid=gu.gid)";
		
		$db->query($sql);
		$data_array = $db->fetchall();

		for($i=0;$i<count($data_array);$i++){
			$select_gid[$data_array[$i][gid]][$data_array[$i][gu_ix]] = $data_array[$i][gu_ix];
		}

	}

}

if($update_kind == "update_list_once"){		//개별수정(공급가만 수정 가능)
	
	if(is_array($select_gid) && count($select_gid) > 0){
		
		foreach($select_gid as $gid => $gu_detail){
			foreach($gu_detail as $key => $gu_ix){

				//품목 공급가 업데이트
				$sql = "update inventory_goods_unit set
							buying_price = '".$goods_price[$gid][$gu_ix][buying_price]."'
						where
							gid = '".$gid."'
							and gu_ix = '".$gu_ix."'
							";
					
				$db->query($sql);
				/*상품의 기본가도 변경?*/
				
				//품목다중가격 공급가 업데이트
				$sql = "update inventory_goods_multi_price set
							buying_price = '".$goods_price[$gid][$gu_ix][buying_price]."'
						where
							gu_ix = '".$gu_ix."'";
				$db->query($sql);
				
				//상품 공급가 업데이트
				$sql = "update shop_product set 
							coprice = '".$goods_price[$gid][$gu_ix][buying_price]."'
						where
							pcode = '".$gu_ix."'";
				$db->query($sql);
				
				//옵션 공급가 업데이트
				$sql = "update shop_product_options_detail set
							option_coprice = '".$goods_price[$gid][$gu_ix][buying_price]."'
						where
							option_code = '".$gu_ix."'";
				$db->query($sql);

				//기본도매가,기본소매가 변경시 다중품목 금액 변경 시작 
				update_product_listprice($goods_price[$gid][$gu_ix],$gu_ix);	//단위기본가 변경시 해당 품목과 연결되 상품의 금액도 변경 2014-05-09 이학봉 항상 업데이트 위에 잇어야함
				//공급가, 기본가 변경시 다중품목 금액 변경 끝 
			}
		}

		echo("<script>alert('검색품목의 가격변경이 적상적으로 완료되었습니다.');</script>");
		echo("<script>parent.location.reload()</script>");	}
	
}

if($update_kind == "update_list_batch"){

	if(is_array($select_gid) && count($select_gid) > 0){
		
		foreach($select_gid as $gid => $gu_detail){
			foreach($gu_detail as $key => $gu_ix){
				
				$sql = "update inventory_goods_unit set
							wholesale_price = '".$batch[batch][wholesale_price]."',
							sellprice = '".$batch[batch][sellprice]."'
						where
							gid = '".$gid."'
							and gu_ix = '".$gu_ix."'
							";
				$db->query($sql);

				/*상품의 기본가도 변경?*/
			}
		}

		echo("<script>alert('검색품목의 가격변경이 적상적으로 완료되었습니다.');</script>");
		echo("<script>parent.location.reload()</script>");	}

}

if($update_kind == "update_list_buying_price"){	//공급가대비(*)

	if(is_array($select_gid) && count($select_gid) > 0){
		
		foreach($select_gid as $gid => $gu_detail){
			foreach($gu_detail as $key => $gu_ix){
				//공급가가 없거나 0일경우 기존 금액그대로 업데이트 한다. 2014-05-07 이학봉
				$buying_price = $goods_price[$gid][$gu_ix][buying_price];
				$wholesale_price = ($buying_price > 0?$goods_price[$gid][$gu_ix][buying_price] * $batch[buying][wholesale_price]:$goods_price[$gid][$gu_ix][wholesale_price]);
				$sellprice = ($buying_price > 0?$goods_price[$gid][$gu_ix][buying_price] * $batch[buying][sellprice]:$goods_price[$gid][$gu_ix][sellprice]);

				$sql = "update inventory_goods_unit set
							wholesale_price = '".$wholesale_price."',
							sellprice = '".$sellprice."'
						where
							gid = '".$gid."'
							and gu_ix = '".$gu_ix."'
							";
				$db->query($sql);
				/*상품의 기본가도 변경?*/
			}
		}

		echo("<script>alert('검색품목의 가격변경이 적상적으로 완료되었습니다.');</script>");
		echo("<script>parent.location.reload()</script>");	}

}

if($update_kind == "update_list_sellprice"){	//소매가 대비 할인율

	if(is_array($select_gid) && count($select_gid) > 0){
		
		foreach($select_gid as $gid => $gu_detail){
			foreach($gu_detail as $key => $gu_ix){
				
				$sellprice = $goods_price[$gid][$gu_ix][sellprice];
				$wholesale_price = ($sellprice > 0?round($sellprice * ((100 - $batch[sellprice][wholesale_price])/100)):$goods_price[$gid][$gu_ix][wholesale_price]);

				$sql = "update inventory_goods_unit set
							wholesale_price = '".$wholesale_price."'
						where
							gid = '".$gid."'
							and gu_ix = '".$gu_ix."'
							";
					
				$db->query($sql);
				/*상품의 기본가도 변경?*/
			}
		}

		echo("<script>alert('검색품목의 가격변경이 적상적으로 완료되었습니다.');</script>");
		echo("<script>parent.location.reload()</script>");
	}

}

//품목가격수정 끝 도매,소매가 수정 2014-05-07 이학봉

//기본보관장소 설정
if($update_kind == "basic_place"||$update_kind == "safestock_once"||$update_kind == "safestock_batch"){
	if($admininfo[admin_level] == 9){
		$where = "where g.gid is NOT NULL ";

		if($admininfo[mem_type] == "MD"){
			$where .= " and g.admin in (".getMySellerList($admininfo[charger_ix]).") ";
		}

	}else{
		$where = "where g.gid is NOT NULL and g.admin ='".$admininfo[company_id]."' ";
	}

	if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
		//다중검색 시작 2014-04-10 이학봉
		if($search_text != ""){
			if(strpos($search_text,",") !== false){
				$search_array = explode(",",$search_text);
				$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
				$where .= "and ( ";
				$count_where .= "and ( ";
				for($i=0;$i<count($search_array);$i++){
					$search_array[$i] = trim($search_array[$i]);
					if($search_array[$i]){
						if($i == count($search_array) - 1){
							$where .= $search_type." = '".trim($search_array[$i])."'";
							$count_where .= $search_type." = '".trim($search_array[$i])."'";
						}else{
							$where .= $search_type." = '".trim($search_array[$i])."' or ";
							$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
						}
					}
				}
				$where .= ")";
				$count_where .= ")";
			}else if(strpos($search_text,"\n") !== false){//\n
				$search_array = explode("\n",$search_text);
				$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
				$where .= "and ( ";
				$count_where .= "and ( ";

				for($i=0;$i<count($search_array);$i++){
					$search_array[$i] = trim($search_array[$i]);
					if($search_array[$i]){
						if($i == count($search_array) - 1){
							$where .= $search_type." = '".trim($search_array[$i])."'";
							$count_where .= $search_type." = '".trim($search_array[$i])."'";
						}else{
							$where .= $search_type." = '".trim($search_array[$i])."' or ";
							$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
						}
					}
				}
				$where .= ")";
				$count_where .= ")";
			}else{
				$where .= " and ".$search_type." = '".trim($search_text)."'";
				$count_where .= " and ".$search_type." = '".trim($search_text)."'";
			}
		}

	}else{	//검색어 단일검색
		if($search_text != ""){
			if($search_type == "gname_gid"){
				$where .= "and (g.gname LIKE '%".$search_text."%' or g.gid LIKE '%".$search_text."%') ";
			}else{
				$where .= "and ".$search_type." LIKE '%".$search_text."%' ";
			}
		}
	}

	if(is_array($cid) && count($cid) > 0){		//품목카테고리 다중검색
		$where .= " and g.cid in (".implode(",",$cid).")";
	}
	
	if($update_type=="1"){//검색한 상품!
		if($update_kind == "basic_place"){
			$sql = "select
						data.*,
						gu.unit,
						gu.gu_ix,
						gbp.gbp_ix,
						gbp.company_id,
						gbp.pi_ix,
						gbp.ps_ix,
						ccd.com_name as company_name,
						pi.place_name,
						ps.section_name
					from (
						select 
							g.gid,g.gname,g.gcode,g.barcode,g.item_account,g.standard
						from 
							inventory_goods g 
						$where 
					) data
					left join inventory_goods_unit gu on (data.gid=gu.gid)
					left join inventory_goods_basic_place gbp on (gu.gid=gbp.gid and gu.gu_ix=gbp.gu_ix)
					left join common_company_detail ccd on (ccd.company_id = gbp.company_id)
					left join  inventory_place_info pi on gbp.pi_ix = pi.pi_ix
					left join  inventory_place_section ps on gbp.ps_ix = ps.ps_ix
					group by gu_ix,ps_ix
			";
		}else{
			$sql = "select
					data.*,
					gu.unit,
					gu.gu_ix,
					pi.company_id,
					pi.pi_ix,
					pi.place_name,
					ccd.com_name as company_name,
					gs.gs_ix,
					gs.safestock,
					gs.titrationstock
				from (
					select 
						g.gid,g.gname,g.gcode,g.barcode,g.item_account,g.standard
					from 
						inventory_goods g 
					$where 
						order by g.gid
				) data
				left join inventory_goods_unit gu on (data.gid=gu.gid)
				left join  inventory_place_info pi on pi.company_id='".$_SESSION["admininfo"]["company_id"]."'
				left join common_company_detail ccd on (ccd.company_id = pi.company_id)
				left join inventory_goods_safestock gs on (gs.gid=gu.gid and gs.gu_ix=gu.gu_ix and gs.company_id=pi.company_id and gs.pi_ix=pi.pi_ix)";
		}

		$db->query($sql);
		$select_unit = $db->fetchall();
	}
	
}

//기본보관장소 설정
if($update_kind == "basic_place"){

	if(is_array($select_unit) && count($select_unit) > 0){
		foreach($select_unit as $key => $val){
			
			if($update_type=="1"){
				$gid=$val["gid"];
				$unit=$val["unit"];
				$gu_ix=$val["gu_ix"];
				$gbp_ix=$val["gbp_ix"];
			}else{
				list($gid,$unit,$gu_ix) = explode("|",$key);
				$gbp_ix=$val;
			}

			$sql = "select * from inventory_goods_basic_place where gid='".$gid."' and unit='".$unit."' and pi_ix='".$pi_ix."' ";
			$db->query($sql);
			if($db->total){
				$db->fetch();
				$gbp_ix = $db->dt[gbp_ix];
				$sql="update inventory_goods_basic_place set ps_ix='".$ps_ix."', editdate = NOW() where gbp_ix='".$gbp_ix."' ";
			}else{
				$sql="insert into inventory_goods_basic_place (gbp_ix,gid,unit,gu_ix,company_id,pi_ix,ps_ix,editdate,regdate) values('','$gid','$unit','$gu_ix','".$company_id."','".$pi_ix."','".$ps_ix."',NOW(),NOW())";
			}

			$db->query($sql);
		}

		echo("<script>alert('기본보관장소 설정이 적상적으로 완료되었습니다.');</script>");
		echo("<script>parent.location.reload()</script>");
	}
}

//품목별 안전/적정재고 개별설정 
if($update_kind == "safestock_once" || $update_kind == "safestock_batch"){

	if(is_array($select_unit) && count($select_unit) > 0){
		foreach($select_unit as $key => $val){
			
			if($update_type=="1"){
				$gid=$val["gid"];
				$unit=$val["unit"];
				$gu_ix=$val["gu_ix"];
				$company_id=$val["company_id"];
				$pi_ix=$val["pi_ix"];
				$gs_ix=$val["gs_ix"];
			}else{
				list($gid,$unit,$gu_ix,$company_id,$pi_ix) = explode("|",$key);
				$gs_ix=$val;
			}
			
			if($update_kind == "safestock_once"){
				$safestock=$safe_stock[$gu_ix."_".$pi_ix];
				$titrationstock=$titration_stock[$gu_ix."_".$pi_ix];
			}else{
				if($batch_safestock_check=="Y")		$safestock = $batch_safestock;
				else												$safestock = "";
				
				if($batch_titrationstock_check=="Y")	$titrationstock = $batch_titrationstock;
				else												$titrationstock = "";
			}

			if($gs_ix!=""){
				$update_str="";
				if($safestock!=""){
					$update_str.=" safestock='".$safestock."', ";
				}

				if($titrationstock!=""){
					$update_str.=" titrationstock='".$titrationstock."', ";
				}

				$sql="update inventory_goods_safestock set ".$update_str." editdate = NOW() where gs_ix='".$gs_ix."' ";
			}else{
				$sql="insert into inventory_goods_safestock(gs_ix,gid,unit,gu_ix,company_id,pi_ix,safestock,titrationstock,editdate,regdate) values('','$gid','$unit','$gu_ix','$company_id','$pi_ix','$safestock','$titrationstock',NOW(),NOW())";
			}
			$db->query($sql);
		}

		echo("<script>alert('안전/적정재고수정이 적상적으로 완료되었습니다.');</script>");
		echo("<script>parent.location.reload()</script>");
	}
}

?>