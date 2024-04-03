<?
include_once("../../class/database.class");
include_once("../include/admin.util.php");
include_once("../../include/lib.function.php");
include_once "../class/Snoopy.class.php";
//print_r($_POST);
//exit;
if($admininfo[company_id] == ""){
	echo "<script language='javascript'>alert('관리자 로그인후 사용하실수 있습니다.');location.href='/admin/admin.php'</script>";
	exit;
}
$db = new Database;

if($bs_act == "bsgoods_auto_reg"){
			$db = new Database;
			$bs_db = new Database;
			// 기본 구매대행 환율정보 및 수수료 정보를 읽어온다.
			$sql = "select * from shop_buyingservice_info order by regdate desc limit 1 ";

			$db->query ($sql);

			if($db->total){
				$db->fetch();

				$exchange_rate = $db->dt[exchange_rate];
				$bs_basic_air_shipping = $db->dt[bs_basic_air_shipping];
				$bs_add_air_shipping = $db->dt[bs_add_air_shipping];

				$base_bs_duty_rate = $db->dt[bs_duty];
				$base_clearance_fee = $db->dt[clearance_fee];
				$base_bs_supertax_rate = $db->dt[bs_supertax_rate];
			}

			//$db->debug = true;
			//$bs_url = str_replace("&amp;","&",$goods_detail_link);
			//$bs_url = str_replace(" ","%20",$bs_url);

			if(true){
				//$pid = "0000048020";
				if($pid){
					$sql = "select id, pname,pcode, bs_site, bs_goods_url, buyingservice_coprice, coprice, listprice, sellprice, currency_ix, round_type, round_precision
							from shop_product  where product_type = 1 and bs_site != 'izabel' and id = '".$pid."'
							 "; // rand() regdate desc
				}else{
					$sql = "select id, pname,pcode, bs_site, bs_goods_url, buyingservice_coprice, coprice, listprice, sellprice, currency_ix, round_type, round_precision
							from shop_product  where product_type = 1 and bs_site != 'izabel'
							order by rand() limit 1 "; // rand() regdate desc
				}
				//echo $sql;
				$db->query ($sql);
				$goods_infos = $db->fetchall("object");
			}


			if(count($goods_infos)){
				//print_r($goods_infos);
				for($_x=0; $_x < count($goods_infos);$_x++){
						//$db->fetch($_x);
						$bs_site = $goods_infos[$_x][bs_site];
						//echo "bs_site : ".$bs_site."<br>";
						//exit;
						$bs_url = $goods_infos[$_x][bs_goods_url];
						$pid = $goods_infos[$_x][id];
						$pcode = $goods_infos[$_x][pcode];
						$currency_ix = $goods_infos[$_x][currency_ix];
						$round_type = $db->dt[round_type];
						$round_precision = $db->dt[round_precision];

						//if($currency_ix == ""){
						$sql = "select  pbp.clearance_type, pbp.bs_fee_rate, pbp.air_wt
						from shop_product p, shop_product_buyingservice_priceinfo pbp
						where p.id = pbp.pid and pbp.bs_use_yn = '1' and p.pcode = '".trim($pcode)."'  ";
						//echo $sql;
						//exit;
						//echo $sql;//orgin_price, exchange_rate, air_wt, air_shipping , duty, clearance_fee, bs_fee_rate, bs_fee, clearance_type
						$db->query ($sql);

						if($db->total){
							$db->fetch();
							$buying_service_priceinfo = $db->dt;
							// 상품 정보가 있을경우 기존 상품 정보에서 통관타입
							$clearance_type = $buying_service_priceinfo[clearance_type]; // 상품정보가 있을경우 통관타입은 기존 상품 정보에서 가져옴
							if(!$bs_fee_rate){
								$bs_fee_rate = $buying_service_priceinfo[bs_fee_rate];	 // 상품정보가 있을경우 구매대행 수수료율 기존 상품 정보에서 가져옴
							}
							$bs_air_wt = $buying_service_priceinfo[air_wt];	 // 상품정보가 있을경우 구매대행 수수료율 기존 상품 정보에서 가져옴
						}else{
							$sql = "update shop_product set pcode = '".$pcode."' where id = '".$pid."' ";
							//echo $sql;
							$db->query ($sql);

							$sql = "select p.id, p.pcode, pbp.clearance_type, pbp.bs_fee_rate, pbp.air_wt , p.round_type, p.round_precision
									from shop_product p, shop_product_buyingservice_priceinfo pbp
									where p.id = pbp.pid and pbp.bs_use_yn = '1' and p.pcode = '".trim($pcode)."'  ";

							$db->query ($sql);

							if($db->total){
								// 상품 정보가 있을경우 기존 상품 정보에서 통관타입
								$db->fetch();
								$buying_service_priceinfo = $db->dt;

								$clearance_type = $buying_service_priceinfo[clearance_type]; // 상품정보가 있을경우 통관타입은 기존 상품 정보에서 가져옴
								if(!$bs_fee_rate){
									$bs_fee_rate = $buying_service_priceinfo[bs_fee_rate];	 // 상품정보가 있을경우 구매대행 수수료율 기존 상품 정보에서 가져옴
								}
								$bs_air_wt = $buying_service_priceinfo[air_wt];	 // 상품정보가 있을경우 구매대행 수수료율 기존 상품 정보에서 가져옴
							}
						}

						// 상품등록일 경우 기본 구매대행 환율정보 및 수수료 정보를 읽어온다.
						$sql = "select * from shop_buyingservice_info where exchange_type = '".$currency_ix."' order by regdate desc limit 1 ";
						//echo $sql;
						$db->query ($sql);

						if($db->total){
							$db->fetch();
							$buying_service_cfginfo = $db->dt;

							if($clearance_type == '9'){
								$exchange_rate = 1; // 환율 정보 최근 환율 정보를 가져옴
								$bs_basic_air_shipping = 0; // 기본 1파운드 항공 운송료  최근 정보를 가져옴
								$bs_add_air_shipping = 0; // 추가 1파운드 항공 운송료 최근정보를 가져옴
							}else{
								$exchange_rate = $buying_service_cfginfo[exchange_rate]; // 환율 정보 최근 환율 정보를 가져옴
								$bs_basic_air_shipping = $buying_service_cfginfo[bs_basic_air_shipping]; // 기본 1파운드 항공 운송료  최근 정보를 가져옴
								$bs_add_air_shipping = $buying_service_cfginfo[bs_add_air_shipping]; // 추가 1파운드 항공 운송료 최근정보를 가져옴
							}


							if($clearance_type == '1' || $clearance_type == '9'){ // 통관타입이 1 : 목록통관일경우 관세/부가세, 통관수수료 면제
								$bs_duty_rate = 0; // 관세율 최근정보를 가져옴
								$clearance_fee = 0; // 통관수수료 최근정보를 가져옴
								$bs_supertax_rate = 0; // 부가세율 통관수수료 면제
							}else{
								$bs_duty_rate = $buying_service_cfginfo[bs_duty];
								$clearance_fee = $buying_service_cfginfo[clearance_fee];
								$bs_supertax_rate = $buying_service_cfginfo[bs_supertax_rate];
							}
							$bs_fee_rate = $buying_service_cfginfo[bs_fee_rate];

						}


						include "buyingService.filter.".$bs_site.".php";

						//exit;
						//echo $pname."<br><br>";
						if($pname && $price && $stock_bool){
								$pname = str_replace("'","\'",trim($pname));
								$pname = str_replace("\t"," ",$pname);
								$pcode = $pcode;
								$bs_goods_url = $bs_url;
								$delivery_company = "MI";
								$stock = "999999";
								$safestock = "10";
								$stock_use_yn = "N";
								$surtax_yorn = "N";


								if($bs_air_wt <= 1){
									$air_shipping = $bs_basic_air_shipping;
								}else{
									$air_shipping = $bs_basic_air_shipping + ($bs_add_air_shipping * ($bs_air_wt - 1));
								}
								$price = str_replace(",","",$price);
								$bs_duty_basis = ($price+$air_shipping)*$exchange_rate;
								$bs_supertax = $bs_duty_basis*$bs_supertax_rate/100;
								$bs_duty = round($bs_duty_basis*$bs_duty_rate/100+$bs_supertax,-1);


								$buyingservice_coprice = round(($price+$air_shipping)*$exchange_rate+$bs_duty+$clearance_fee,0);
								$bs_fee = round($buyingservice_coprice*$bs_fee_rate/100,-1);

								//if($cid2 != ""){
									$category[0] = $cid2;
								//}
								$basic = $cid2; // 기본카테고리지정



								$bimg_text = $prod_img_src;
								$img_url_copy = 1;
								$prod_desc_prod = str_replace("'","\'",$prod_desc_prod);

								$shotinfo = $prod_desc_prod;
								$basicinfo = $prod_desc_prod;

								$orgin_price = $price;
								$exchange_rate = $exchange_rate;
								$bs_basic_air_shipping = $air_shipping;
								//$air_wt = "1";  // 예상무계 어떻게 할껀지 확인필요

								$duty = $bs_duty;
								$clearance_fee = $clearance_fee;

								$coprice = round($buyingservice_coprice+$bs_fee,-1);
								if($round_type && $round_precision){//$usable_round == "Y"
									//exit;
									if($round_type == "round"){
										$listprice = roundBetterUp($buyingservice_coprice+$bs_fee,-1*$round_precision);
										$sellprice = roundBetterUp($buyingservice_coprice+$bs_fee,-1*$round_precision);
									}else if($round_type == "floor"){
										$listprice = roundBetterDown($buyingservice_coprice+$bs_fee,-1*$round_precision);
										$sellprice = roundBetterDown($buyingservice_coprice+$bs_fee,-1*$round_precision);
									}

								}else{
									$listprice = round($buyingservice_coprice+$bs_fee,-1);
									$sellprice = round($buyingservice_coprice+$bs_fee,-1);
								}


								$act = "insert";
								$product_type = "1"; // 상품 타입이 구매대행으로 설정
								//print_r($option);
								//print_r($option2);
								//exit;
								if(is_array($option)){
									$options[0][option_type] = "9";
									$options[0][option_name] = $option[0];
									$options[0][option_kind] = "s";

									for($i=1;$i < count($option);$i++){
										$options[0][details][$i-1][option_div] = $option[$i];
										$options[0][details][$i-1][price] = "";
										$options[0][details][$i-1][etc1] = $option[$i];

									}
								}

								if(is_array($option2)){
									$options[1][option_type] = "9";
									$options[1][option_name] = $option2[0];
									$options[1][option_kind] = "s";

									for($i=1;$i < count($option2);$i++){
										$options[1][details][$i-1][option_div] = $option2[$i];
										$options[1][details][$i-1][price] = "";
										$options[1][details][$i-1][etc1] = $option2[$i];

									}
								}
						}

						/*
						if($db->total){
							$db->fetch();

							$orgin_price = $db->dt[orgin_price];
							$exchange_rate = $db->dt[exchange_rate];
							$air_wt = $db->dt[air_wt];
							$air_shipping = $db->dt[air_shipping];
							$duty = $db->dt[duty];
							$clearance_fee = $db->dt[clearance_fee];
							$clearance_type = $db->dt[clearance_type];
							$bs_fee_rate = $db->dt[bs_fee_rate];
							$bs_fee = $db->dt[bs_fee];
						}
						*/
						echo "buyingservice_coprice = round((".$price."+".$bs_air_shipping.")*".$exchange_rate."+".$bs_duty."+".$clearance_fee.",0);";
$mstring =  "<table border=1>";
$mstring .=   "<tr><td colspan=10>".$bs_url."</td></tr> ";
$mstring .=   "<tr><td colspan=10><a href='?bs_act=bsgoods_auto_reg' >새로운 상품 보기</a> <a href='./goods_input.php?id=".$pid."' target=_blank>상품정보 보기</a>  <a href='?bs_act=bsgoods_auto_reg&pid=".$pid."' >상품정보 다시 보기</a></td></tr> ";
$mstring .=   "<tr>
						<td>통관타입 :".($clearance_type == 1 ? "목록통관":"일반통관")." </td>
						<td>사진 </td>
						<td>스크래핑사이트 </td>
						<td>키값 </td>
						<td>상품명 </td>
						<td> 상품코드</td>
						<td>orgin 원가</td>
						<td>예상무게</td>
						<td> 공급원가</td>
						<td> 공급가</td>
						<td> 가격</td>
						<td> 판매가</td>
					</tr> ";
if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $goods_infos[$_x][id], "s"))) {
	$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $goods_infos[$_x][id], "s");
}else{
	$img_str = "../image/no_img.gif";
}

$mstring .=   "<tr>
						<td>기존 정보 : </td>
						<td><img src='".$img_str."' width=50 height=50></td>
						<td>".$goods_infos[$_x][bs_site]." </td>
						<td>".$goods_infos[$_x][id]." </td>
						<td>".$goods_infos[$_x][pname]." </td>
						<td> ".$goods_infos[$_x][pcode]."</td>
						<td>".$bs_info[orgin_price]."</td>
						<td>".$bs_info[air_wt]."</td>

						<td> ".$goods_infos[$_x][buyingservice_coprice]."</td>
						<td> ".$goods_infos[$_x][coprice]."</td>
						<td> ".$goods_infos[$_x][listprice]."</td>
						<td> ".$goods_infos[$_x][sellprice]."</td>
						</tr> ";
$mstring .=   "<tr>
						<td>새로운 정보 : </td>
						<td><img src='".$img_str."' width=50 height=50></td>
						<td>".$bs_site." </td>
						<td>".$pid." </td>
						<td>".$pname." </td>
						<td> ".$pcode."</td>
						<td>".$orgin_price."</td>
						<td>".$air_wt."</td>

						<td>".$buyingservice_coprice."</td>
						<td> ".$coprice."</td>
						<td> ".$listprice."</td>
						<td> ".$sellprice."</td>
					</tr> ";
$mstring .=   "</table>";
echo $mstring;
//print_r($options);
					  //print_r($options);



					  $sql = "insert into shop_product_buyingservice_priceinfo
								(bsp_ix,pid,orgin_price,exchange_rate,air_wt,air_shipping,duty,clearance_fee,clearance_type,bs_fee_rate,bs_fee,regdate)
								values('','".$pid."','$orgin_price','$exchange_rate','$air_wt','$air_shipping','$duty','$clearance_fee','$clearance_type','$bs_fee_rate','$bs_fee',NOW()) ";
					  echo $sql."<br>";
					  //exit;
						if($_GET["pid"] || $act == "cron"){
							$bs_db->query($sql);
						}else{
							exit;
						}
						if($pname && $price && $stock_bool){
							//include "goods_input.act.php";
							$sql = "update shop_product set
										pcode = '$pcode', pname='$pname',
										buyingservice_coprice = '$buyingservice_coprice', coprice = '$coprice', listprice = '$listprice', sellprice = '$sellprice' , editdate = NOW()
										where  bs_site = '$bs_site' and id = '".$pid."' ";

							$bs_db->query ($sql);

							$sql = "insert into shop_product_buyingservice_priceinfo(bsp_ix,pid,orgin_price,exchange_rate,air_wt,air_shipping,duty,clearance_fee,clearance_type,bs_fee_rate,bs_fee,regdate)
											values('','".$pid."','$orgin_price','$exchange_rate','$air_wt','$air_shipping','$duty','$clearance_fee','$clearance_type','$bs_fee_rate','$bs_fee',NOW()) ";
							//echo $sql;
							$bs_db->query($sql);



							$db->debug = true;


							if($options_price_stock["option_name"]){
								$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and option_name = '".trim($options_price_stock["option_name"])."' and option_kind = 'b'");

								if($db->total){
									$db->fetch();
									$opn_ix = $db->dt[opn_ix];
									$sql = "update  ".TBL_SHOP_PRODUCT_OPTIONS." set
													option_name='".trim($options_price_stock["option_name"])."', option_kind='".$options_price_stock["option_kind"]."', option_type='".$options_price_stock["option_type"]."',
													option_use='".$options_price_stock["option_use"]."'
													where opn_ix = '".$opn_ix."' ";
									$db->query($sql);


								}else{
									$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS." (opn_ix, pid, option_name, option_kind, option_type, option_use, regdate)
													VALUES
													('','$pid','".$options_price_stock["option_name"]."','".$options_price_stock["option_kind"]."','".$options_price_stock["type"]."','".$options_price_stock["use"]."',NOW())";

									$db->query($sql);
									$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE opn_ix=LAST_INSERT_ID()");
									$db->fetch();
									$opn_ix = $db->dt[0];
								}
								//echo $sql."<br>";
								//exit;


								$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set insert_yn='N' where opn_ix='".$opn_ix."' ";
								//echo $sql."<br><br>";
								$db->query($sql);
								$option_stock_yn = "";
								for($j=0;$j < count($options_price_stock["option_div"]);$j++){
									if($options_price_stock[option_div][$j]){
										$db->query("SELECT id FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE option_div = '".trim($options_price_stock[option_div][$j])."' and opn_ix = '".$opn_ix."' ");

										if($db->total){
											$db->fetch();
											$opnd_ix = $db->dt[id];
											/*
											$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set
														option_div='".$options_price_stock[option_div][$j]."',option_price='".$options_price_stock[price][$j]."',option_m_price='".$options_price_stock[price][$j]."',option_d_price='".$options_price_stock[price][$j]."',
														option_a_price='".$options_price_stock[price][$j]."',option_useprice='".$options_price_stock[price][$j]."', option_stock='".$options_price_stock[stock][$j]."', option_safestock='".$options_price_stock[safestock][$j]."' ,
														option_etc1='".$options_price_stock[etc1][$j]."', insert_yn='Y'
														where id ='".$opnd_ix."' and opn_ix = '".$opn_ix."'";
											*/
											$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set
														option_div='".$options_price_stock[option_div][$j]."',option_price='".$options_price_stock[price][$j]."',option_stock='".$options_price_stock[stock][$j]."', option_safestock='".$options_price_stock[safestock][$j]."' ,
														option_etc1='".$options_price_stock[etc1][$j]."', insert_yn='Y'
														where id ='".$opnd_ix."' and opn_ix = '".$opn_ix."'";
										}else{
											//$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price,option_m_price, option_d_price, option_a_price, option_stock, option_safestock, option_etc1) ";
											$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price, option_stock, option_safestock, option_etc1) ";
											//$sql = $sql." values('','$pid','$opn_ix','".$options_price_stock[option_div][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[stock][$j]."','".$options_price_stock[safestock][$j]."','".$options_price_stock[etc1][$j]."') ";
											$sql = $sql." values('','$pid','$opn_ix','".$options_price_stock[option_div][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[stock][$j]."','".$options_price_stock[safestock][$j]."','".$options_price_stock[etc1][$j]."') ";
										}

										//echo $sql."<br><br>";
										$db->query($sql);

										if($options_price_stock[stock][$j] == 0 && ($option_stock_yn == "" || $option_stock_yn == "R")){
											$option_stock_yn = "N";
										}

										if($options_price_stock[stock][$j] < $options_price_stock[safestock][$j] && $option_stock_yn == ""){
											$option_stock_yn = "R";
										}
									}
								}
								$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$opn_ix."' and insert_yn = 'N' ";
								//echo $sql;
								$db->query($sql);

								if($option_stock_yn){
									$db->query("update ".TBL_SHOP_PRODUCT." set option_stock_yn = '$option_stock_yn' where id ='$pid'");
								}

							}else{

								$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and option_kind = 'b'");

								if($db->total){
									$db->fetch();
									$opn_ix = $db->dt[0];
									$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$opn_ix."'  ";
									$db->query($sql);
								}
								$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and option_kind = 'b' ";
								$db->query($sql);
							}

							$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS." set insert_yn='N' 	where pid = '$pid' and option_kind in ('s','p','r') ";
							//echo $sql."<br><br>";

							$db->query($sql);

							for($i=0;$i < count($options);$i++){
								//echo $options[$i][option_name].":::".$options[$i][opn_ix]."<br>";
								//exit;
								if($options[$i]["option_name"]){
									$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and option_name = '".trim($options[$i]["option_name"])."' and option_kind in ('s','p') ");
									//$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and opn_ix = '".$options[$i]["opn_ix"]."' and option_kind in ('s','p') ");



									if($db->total){
										$db->fetch();
										$opn_ix = $db->dt[opn_ix];
										$sql = "update  ".TBL_SHOP_PRODUCT_OPTIONS." set
														option_name='".trim($options[$i]["option_name"])."', option_kind='".$options[$i]["option_kind"]."',
														option_type='".$options[$i]["option_type"]."', option_use='1',insert_yn='Y'
														where opn_ix = '".$opn_ix."' ";

										$db->query($sql);

									}else{
										$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS." (opn_ix, pid, option_name, option_kind, option_type, option_use, regdate)
														VALUES
														('','$pid','".$options[$i]["option_name"]."','".$options[$i]["option_kind"]."','".$options[$i]["option_type"]."','1',NOW())";
										$db->query($sql);
										$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE opn_ix=LAST_INSERT_ID()");
										$db->fetch();
										$opn_ix = $db->dt[0];
									}


									//echo $sql."<br><br>";
									//


									$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set insert_yn='N'	where opn_ix='".$opn_ix."' ";
									$db->query($sql);
									for($j=0;$j < count($options[$i]["details"]);$j++){
										if($options[$i][details][$j][option_div]){
												$db->query("SELECT id FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE option_div = '".trim($options[$i][details][$j][option_div])."' and opn_ix = '".$opn_ix."' ");

												if($db->total){
													$db->fetch();
													$opnd_ix = $db->dt[id];
													/*
													$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set
															option_div='".$options[$i][details][$j][option_div]."',option_price='".$options[$i][details][$j][price]."',option_m_price='".$options[$i][details][$j][price]."',option_d_price='".$options[$i][details][$j][price]."',
															option_a_price='".$options[$i][details][$j][price]."',option_useprice='".$options[$i][details][$j][price]."', option_stock='0', option_safestock='0' ,
															option_etc1='".$options[$i][details][$j][etc1]."', insert_yn='Y'
															where id ='".$opnd_ix."' and opn_ix = '".$opn_ix."'";
													*/
													$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set
															option_div='".$options[$i][details][$j][option_div]."',option_price='".$options[$i][details][$j][price]."', option_stock='0', option_safestock='0' ,
															option_etc1='".$options[$i][details][$j][etc1]."', insert_yn='Y'
															where id ='".$opnd_ix."' and opn_ix = '".$opn_ix."'";
													$db->query($sql);
												}else{
													//$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price,option_m_price, option_d_price, option_a_price, option_stock, option_safestock, option_etc1) ";
													$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price, option_stock, option_safestock, option_etc1) ";
													//$sql = $sql." values('','$pid','".$opn_ix."','".trim($options[$i][details][$j][option_div])."','".$options[$i][details][$j][price]."','".$options[$i][details][$j][price]."','".$options[$i][details][$j][price]."','".$options[$i][details][$j][price]."','0','0','".$options[$i][details][$j][etc1]."') ";
													$sql = $sql." values('','$pid','".$opn_ix."','".trim($options[$i][details][$j][option_div])."','".$options[$i][details][$j][price]."','0','0','".$options[$i][details][$j][etc1]."') ";

													$db->query($sql);

													$db->query("SELECT id FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE id=LAST_INSERT_ID()");
													$db->fetch();
													$opnd_ix = $db->dt[0];
												}

												//echo $sql."<br><br>";
												if($options[$i]["details"][$j][thumb_images] || $options[$i]["details"][$j][goods_images]){
													$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product", $pid, 'Y');
													if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/options/")){
														mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/options/");
														chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/options/",0777);
													}

													//$options[$option_key][details][$i][thumb_images] = $thumb_images[$i];
													//$options[$option_key][details][$i][goods_images] = $goods_detail_images[$i];
													if($options[$i]["details"][$j][thumb_images]){
														copy($options[$i]["details"][$j][thumb_images], $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/options/options_detail_".$opnd_ix."_s.gif");
													}
													if($options[$i]["details"][$j][goods_images]){
														copy($options[$i]["details"][$j][goods_images], $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/options/options_detail_".$opnd_ix."_b.gif");
													}
												}


										}
									}
									$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$opn_ix."' and insert_yn = 'N' ");
								}
							}
							$sql = "select opn_ix from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and insert_yn = 'N' ";
							//echo $sql."<br><br>";
							$db->query($sql);
							if($db->total){
								$del_options = $db->fetchall();
								//print_r($del_options);
								for($i=0;$i < count($del_options);$i++){
									$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$del_options[$i][opn_ix]."' and pid = '$pid' ");
								}
							}
							$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and insert_yn = 'N' ");
							echo " $pname 상품을 등록중입니다. <b>pcode : $pcode </b>";
						}else{
							echo " [정보부족 ] $pname $price  <b>pcode : $pcode </b>";
							$write = "[정보부족]".$bs_site." ".$bs_url." 상품코드:".$pcode.", 상품가격:".$price." \n\n";
							$path = $_SERVER["DOCUMENT_ROOT"]."/_logs/";

							if(!is_dir($path)){
								mkdir($path, 0777);
								chmod($path,0777);
							}else{
								//chmod($path,0777);
							}
							$sql = "update shop_product set state = '0' where pcode = '$pcode'  ";
							$bs_db->query ($sql);

							$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/_logs/buyingservice.txt","a+");
							fwrite($fp,$write);
							fclose($fp);
						}

						unset($pname);
						unset($pcode);
						unset($prod_img_src);
						unset($price);
						unset($prod_desc_start_line);
						unset($prod_desc_end_line);
						unset($prod_desc_inner_div_cnt);
						unset($option_start_line);
						unset($option_end_line);
						unset($prod_desc_prod);
						//unset($pcode);
				} // for loop end


			}
			//set_time_limit(30);
			//$snoopy->fetch($goods_detail_links[$i]);



}


function BuyServiceProductPriceUpdate($pdb){
	global $mdb;

	$bs_goods_url = $pdb->dt[bs_goods_url];
	$bs_site = $pdb->dt[bs_site];

	$sql = "select * from shop_buyingservice_info order by regdate desc limit 1 ";
	$mdb->query ($sql);

	if($mdb->total){
		$mdb->fetch();

		$exchange_rate = $mdb->dt[exchange_rate];
		$bs_basic_air_shipping = $mdb->dt[bs_basic_air_shipping];
		$bs_add_air_shipping = $mdb->dt[bs_add_air_shipping];


		if($clearance_type){
			$bs_duty_rate = 0;
			$clearance_fee = 0;
			$bs_supertax_rate = 0;
		}else{
			$bs_duty_rate = $mdb->dt[bs_duty];
			$clearance_fee = $mdb->dt[clearance_fee];
			$bs_supertax_rate = $mdb->dt[bs_supertax_rate];
		}
	}



	if($bs_air_wt <= 1){
		$bs_air_shipping = $bs_basic_air_shipping;
	}else{
		$bs_air_shipping = $bs_basic_air_shipping + ($bs_add_air_shipping * ($bs_air_wt - 1));
	}

	$bs_duty_basis = ($price+$bs_air_shipping)*$exchange_rate;
	$bs_supertax = $bs_duty_basis*$bs_supertax_rate/100;
	$bs_duty = round($bs_duty_basis*$bs_duty_rate/100+$bs_supertax,-1);


	$buyingservice_coprice = round(($price+$bs_air_shipping)*$exchange_rate+$bs_duty+$clearance_fee,0);
	$bs_fee = round($buyingservice_coprice*$bs_fee_rate/100,-1);

	$bs_info["orgin_price"] = $price;
	$bs_info["exchange_rate"] = $exchange_rate;
	$bs_info["air_wt"] = $bs_air_wt;
	$bs_info["air_shipping"] = $bs_air_shipping;
	$bs_info["duty_rate"] = $bs_duty_rate;
	$bs_info["clearance_fee"] = $clearance_fee;
	$bs_info["buyingservice_coprice"] = $buyingservice_coprice;

	$bs_info["bs_fee"] = $bs_fee;
	$bs_info["bs_duty"] = $bs_duty;

	$bs_info["listprice"] = round($buyingservice_coprice+$bs_fee,-1);
	$bs_info["sellprice"] = round($buyingservice_coprice+$bs_fee,-1);
	$bs_info["coprice"] = $buyingservice_coprice;


	$sql = "insert into shop_product_buyingservice_priceinfo(bsp_ix,pid,orgin_price,exchange_rate,air_wt,air_shipping,duty,clearance_fee,clearance_type,bs_fee_rate,bs_fee,regdate)
					values('','".$pid."','$orgin_price','$exchange_rate','$air_wt','$air_shipping','$duty','$clearance_fee','$clearance_type','$bs_fee_rate','$bs_fee',NOW()) ";

	$mdb->query($sql);


	$sql = "update shop_product set  order by regdate desc limit 1 ";

}
?>