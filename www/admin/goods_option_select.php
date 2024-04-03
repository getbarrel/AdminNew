<?
include("./class/layout.class");
$admin_delievery_policy = getTopDeliveryPolicy($db);//$db->dt;

	$reserve_data = GetReserveRate();	//적립금 설정값 불러오기

	$sdb = new Database;

	//개별상품 적립금이 잇을경우 우선적용 2013-07-18 이학봉
	$sql = "select * from ".TBL_SHOP_PRODUCT." where id = '".$pid."'";
	$sdb->query($sql);
	$sdb->fetch();

	if($sdb->dt[wholesale_reserve_yn] == "Y"){
		$whole_reserve_rate = $sdb->dt[wholesale_reserve_rate];
	}else{
		$whole_reserve_rate = $reserve_data[goods_mileage_rate];
	}
	if($sdb->dt[reserve_yn] == "Y"){
		$reserve_rate = $sdb->dt[reserve_rate];
	}else{
		$reserve_rate = $reserve_data[goods_mileage_rate];
	}

	if(UserSellingType()== "R"){
		$view_reserve = $reserve_rate;
	}else if(UserSellingType() == "W"){
		$view_reserve = $whole_reserve_rate;
	}else{
		$view_reserve = $reserve_rate;
	}
	
	$reserve_rate = $view_reserve;
	//print_r ($reserve_data);
	
	if($reserve_data[mileage_info_use] == "Y"){	// 개별상품 적립금 우선  적용 2013-07-17 이학봉
		if(UserSellingType() == "R"){
			$reserve_sql = " ,case when p.reserve_yn = 'N' or p.reserve_yn = '' then floor(p.sellprice*(".$reserve_data[goods_mileage_rate]."/100)) else floor(p.sellprice*(p.reserve_rate/100)) end as reserve";
		}else if(UserSellingType() == "W"){
			$reserve_sql = " ,case when p.wholesale_reserve_yn = 'N' or p.wholesale_reserve_yn = '' then floor(p.wholesale_sellprice*(".$reserve_data[goods_mileage_rate]."/100)) else floor(p.wholesale_sellprice*(p.wholesale_reserve_rate/100)) end as reserve";
		}else{
			$reserve_sql = " ,case when p.reserve_yn = 'N' or p.reserve_yn = '' then floor(p.sellprice*(".$reserve_data[goods_mileage_rate]."/100)) else floor(p.sellprice*(p.reserve_rate/100)) end as reserve";
		}
	}else{
		if(UserSellingType() == "R"){	//일반 회원일경우 b2c 개별상품 적용율
		$reserve_sql = " ,case when p.reserve_yn = 'Y' then floor(p.sellprice*(p.reserve_rate/100)) else 0 end as reserve";
		}else if(UserSellingType() == "W"){	//기업회원일경우 도매가로 적립율 적용
		$reserve_sql = " ,case when p.wholesale_reserve_yn = 'Y' then floor(p.wholesale_sellprice*(p.wholesale_reserve_rate/100)) else 0 end as reserve";
		}else{
		$reserve_sql = " ,case when p.reserve_yn = 'Y' then floor(p.sellprice*(p.reserve_rate/100)) else 0 end as reserve";
		}
	}
	
	/**
	 * 도매몰일때 도매가로 결제하도록 wholesale_price를 sellprice로 가져오기 bgh
	 *
	 * sellprice를 $select_price로 대체
	 */
	if(UserSellingType() == 'W'){
		$select_price = 'wholesale_price as listprice, wholesale_sellprice as sellprice, sellprice AS ori_sellprice, (wholesale_price-wholesale_sellprice)/wholesale_price*100 as sale_rate ,  (listprice-sellprice)/listprice*100 as b2c_sale_rate  ';
	}else{
		$select_price = 'sellprice, listprice, (listprice-sellprice)/listprice*100 as sale_rate ';
	}

if($mode == "cart_edit"){
	$sql = "SELECT id, pname, $select_price,  pcode, shotinfo,brand, (stock+available_stock) as stock,  company, stock_use_yn,
		 icons ,brand_name,delivery_company, r.cid, c.depth,c.cname, p.etc9, p.product_type,admin, cmd.com_name as company_name,  is_sell_date, sell_priod_edate, ";
			if(UserSellingType() == "W"){
			$sql .= "
					wholesale_allow_basic_cnt as allow_basic_cnt, wholesale_allow_max_cnt as allow_max_cnt, wholesale_allow_byoneperson_cnt as allow_byoneperson_cnt,";
			}else{
			$sql .= "
					allow_basic_cnt, allow_max_cnt, allow_byoneperson_cnt,";
			}

			$sql .= "
		case when
			p.delivery_policy = '1'
		then
			case when
				(select delivery_policy from ".TBL_COMMON_SELLER_DELIVERY." where company_id = p.admin) != '1'
			then
				(select delivery_basic_policy from ".TBL_COMMON_SELLER_DELIVERY." where company_id = p.admin)
			else
				'".$admin_delievery_policy[delivery_policy]."'
			end
		else
			delivery_product_policy
		end as delivery_policy
		$reserve_sql
		FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_SHOP_CATEGORY_INFO." c , ".TBL_COMMON_COMPANY_DETAIL." cmd where p.id = r.pid and c.cid = r.cid and p.admin = cmd.company_id and id = $pid  limit 0,1";
}else{
	$sql = "SELECT id, pname, $select_price, pcode, shotinfo,brand, (stock+available_stock) as stock,  company, stock_use_yn,
		 icons,brand_name,delivery_company, r.cid, c.depth,c.cname, p.etc9, p.product_type,admin, cmd.com_name as company_name, is_sell_date, sell_priod_edate, ";
			if(UserSellingType() == "W"){
			$sql .= "
					wholesale_allow_basic_cnt as allow_basic_cnt, wholesale_allow_max_cnt as allow_max_cnt, wholesale_allow_byoneperson_cnt as allow_byoneperson_cnt,";
			}else{
			$sql .= "
					allow_basic_cnt, allow_max_cnt, allow_byoneperson_cnt,";
			}

			$sql .= "
		case when
			p.delivery_policy = '1'
		then
			case when
				(select delivery_policy from ".TBL_COMMON_SELLER_DELIVERY." where company_id = p.admin) != '1'
			then
				(select delivery_basic_policy from ".TBL_COMMON_SELLER_DELIVERY." where company_id = p.admin)
			else
				'".$admin_delievery_policy[delivery_policy]."'
			end
		else
			delivery_product_policy
		end as delivery_policy
		$reserve_sql
		FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_SHOP_CATEGORY_INFO." c , ".TBL_COMMON_COMPANY_DETAIL." cmd
		where p.id = r.pid and c.cid = r.cid and p.admin = cmd.company_id
		and id = $pid  limit 0,1";//상품상세에서 stock을 가용재고 합하여 뿌려지게 되어 있어서 옵션 선택 모달에도 똑같이 구현함 (stock+available_stock) as stock kbk 13/10/16
}
	//echo nl2br($sql);
	$db->query($sql);

	$pinfos = $db->fetchall();
	$goods_infos[$pinfos[0][id]][pid] = $pinfos[0][id];
	$goods_infos[$pinfos[0][id]][amount] = $pinfos[0][cid];
	$goods_infos[$pinfos[0][id]][cid] = $pinfos[0][cid];
	$goods_infos[$pinfos[0][id]][depth] = $pinfos[0][depth];
	$discount_info = DiscountRult($goods_infos, $pinfos[0][cid], $pinfos[0][depth]);

	//if($pinfos[0][icons] != ""){
		foreach ($pinfos as $key => $sub_array) {
			$select_ = array("icons_list"=>explode(";",$sub_array[icons]));
			array_insert($sub_array,14,$select_);

			$discount_item = $discount_info[$sub_array[id]];
			//print_r($discount_item);
			$_dcprice = $sub_array[sellprice];
			if(is_array($discount_item)){				
				foreach($discount_item as $_key => $_item){ 
					if($_item[discount_value_type] == "1"){ // %
						//echo $_item[discount_value]."<br>";
						//$_dcprice = $_dcprice*(100 - $_item[discount_value])/100;		
						$_dcprice = roundBetter($_dcprice*(100 - $_item[discount_value])/100, $_item[round_position], $_item[round_type]) ;
					}else if($_item[discount_value_type] == "2"){// 원
						$_dcprice = $_dcprice - $_item[discount_value];
					}

					$discount_desc[] = $_item;					
				}				
			}
			$_dcprice = array("dcprice"=>$_dcprice);
			array_insert($sub_array,52,$_dcprice);
			$discount_desc = array("discount_desc"=>$discount_desc);
			array_insert($sub_array,53,$discount_desc);

			$pinfos[$key] = $sub_array;
		}
	//}

	// arounz 에서는 상품명 필요 : 이현우(2013-05-28)
	
	$pinfo = $pinfos[0];
	//print_r($pinfo);
	



if(UserSellingType() == "W"){
	$db->query("SELECT *  FROM shop_product_mult_rate where pid = '".$pid."' and is_wholesale = 'W' ");
	$multi_selling_price = $db->fetchall();
	$tpl->assign('multi_selling_price',$multi_selling_price);

	// 배송정책
	if($pinfo[delivery_policy] == 2){
		$sql = "select dt.* from shop_delivery_template as dt 
					where dt_ix = '".$pinfo[dt_ix_whole]."' ";
	}else{
		$sql = "select * from common_company_detail as ccd
					inner join shop_delivery_template as dt on (ccd.company_id = dt.company_id)
					where ccd.com_type = 'A' and dt.product_sell_type = 'W' ";
	}
	//echo nl2br($sql);
	$db->query($sql);
	if($db->total){
		$db->fetch();
		//$tpl->assign($db->dt);<-- 확인필요
		//print_r($db->dt);
	}

}else{
	$db->query("SELECT *  FROM shop_product_mult_rate where pid = '".$pid."' and is_wholesale = 'R' ");
	$multi_selling_price = $db->fetchall();
	//$tpl->assign('multi_selling_price',$multi_selling_price);

	// 배송정책
	//echo $pinfo[delivery_policy];
	if($pinfo[delivery_policy] == 2){
		$sql = "select dt.* from shop_delivery_template as dt 
					where dt_ix = '".$pinfo[dt_ix_retail]."' ";
	}else{
		$sql = "select * from common_company_detail as ccd
					inner join shop_delivery_template as dt on (ccd.company_id = dt.company_id)
					where ccd.com_type = 'A' and dt.product_sell_type = 'R' ";
	}
	//echo nl2br($sql);
	$db->query($sql);
	//$db->fetch();
	
//	echo nl2br($sql);

	//$db->query($sql);	
	if($db->total){
		$db->fetch();
		//$tpl->assign($db->dt);  <-- 확인필요
		//print_r($db->dt);
	}
}

 

	// sellprice가 중복되어 상단에 있던 것을 $pinfo 아래로 가져옴 11-10-11 kbk
	if($mode == "cart_edit"){
		$sql = "SELECT set_group, option_price, options, pcount, listprice, sellprice, dcprice, totalprice, stock, stock_use_yn FROM shop_cart WHERE cart_ix='".$cart_ix."'";
		//echo $sql;
		$db->query($sql);//listprice, 정가 추가 kbk 13/06/17
		$db->fetch();
	 
	} else {
		$pcount = 1;
		//$tpl->assign('pcount',1);
	}
	// sellprice가 중복되어 상단에 있던 것을 $pinfo 아래로 가져옴 11-10-11 kbk

//옵션 설정
//$sql = "SELECT option_name , opn_ix, option_kind  FROM ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '".$pid."' and option_use ='1' order by option_kind ASC, opn_ix asc ";
if($user[code] != ""){
	$where = " and mem_ix = '".$user[code]."' and c.product_type in (".implode(' , ',$shop_product_type).")  ";
	$where_cart = " and mem_ix = '".$user[code]."' and product_type in (".implode(' , ',$shop_product_type).")  ";//추가 kbk 13/07/23
	$groupby = " group by mem_ix";
}else{
	$where = " and cart_key = '".session_id()."' and c.product_type in (".implode(' , ',$shop_product_type).") ";
	$where_cart = " and cart_key = '".session_id()."' and product_type in (".implode(' , ',$shop_product_type).") ";//추가 kbk 13/07/23
	$groupby = " group by cart_key";
}
$sql = "SELECT option_name , 			
			box_total,  
			po.opn_ix, 
			'cart_select' as return_type,
			option_kind  
			FROM ".TBL_SHOP_PRODUCT_OPTIONS." po 			
			where po.pid = '".$pid."' and po.option_use ='1'  
			ORDER BY 
			CASE WHEN option_kind='s' THEN 1 
			WHEN option_kind='p' THEN 1 
			WHEN option_kind='r' THEN 1 
			WHEN option_kind='g' THEN 1 
			WHEN option_kind='b' THEN 2 
			WHEN option_kind='x' THEN 2 
			WHEN option_kind='x2' THEN 3 
			WHEN option_kind='s2' THEN 4 
			WHEN option_kind='a' THEN 5 END ";//옵션의 노출 순서를 정해줌 kbk 13/07/01
//echo nl2br($sql);
$db->query($sql);
$options = $db->fetchall();
//$tpl->assign('option_total',$db->total);
//$tpl->assign('options',$options);
//print_r($options);


/*
* 작업자 : 신훈식
* 일시 : 2014년 04월 22일
* 작업내용 : 초이스 세트상품 옵션 수량 변경 폼 구성을 위한 정보생성
*/
if($mode == "cart_edit"){
	if($user[code] != ""){
		$sql = "SELECT id as pid, cart_ix,option_kind, pname, select_option_id, options_text, option_price, options, set_count, pcount, listprice, sellprice, dcprice, totalprice, 'cart_select' as return_type 
					FROM shop_cart WHERE id='".$pid."' and mem_ix = '".$user[code]."' 
					order by case when option_kind = 'a' then 10 else 1 end ";
		$db->query($sql);
	}else{
		$sql = "SELECT id as pid, cart_ix,option_kind, pname, select_option_id, options_text, option_price, options, set_count, pcount, listprice, sellprice, dcprice, totalprice, 'cart_select' as return_type 
					FROM shop_cart WHERE id='".$pid."' and cart_key = '".session_id()."'  
					order by case when option_kind = 'a' then 10 else 1 end ";
		$db->query($sql);
	}
	//echo nl2br($sql);
	$cart_options = $db->fetchall();
	//print_r($cart_options);
	//$tpl->assign('cart_options',$cart_options);
	for($i = 0; $i <= count($cart_options);$i++){
		if($cart_options[$i][option_kind] == "x2" || $cart_options[$i][option_kind] == "s2"){
			$sql = "select id as opnd_ix 
						from shop_product_options_detail 
						where pid='".$cart_options[$i][pid]."' and set_group_seq = '0' 
						and set_group in (SELECT set_group FROM shop_product_options_detail WHERE pid='".$cart_options[$i][pid]."' and id = '".$cart_options[$i][select_option_id]."') ";
			//echo nl2br($sql);
			$db->query($sql);
			$db->fetch();
			$selected_options[$db->dt[opnd_ix]][checked] = true;
			$selected_options[$db->dt[opnd_ix]][set_count] = $cart_options[$i][set_count];
		}
		$selected_options[$cart_options[$i][select_option_id]][opnd_ix] = $cart_options[$i][select_option_id];
		$selected_options[$cart_options[$i][select_option_id]][set_count] = $cart_options[$i][set_count];
		$selected_options[$cart_options[$i][select_option_id]][amount] = $cart_options[$i][pcount];
		$selected_options[$cart_options[$i][select_option_id]][cart_ix] = $cart_options[$i][cart_ix];
		$selected_options[$cart_options[$i][select_option_id]][option_kind] = $cart_options[$i][option_kind];
		$selected_options[$cart_options[$i][select_option_id]][checked] = true;
	}
	//$tpl->assign('selected_options',$selected_options);
	//print_r($selected_options);
	//$tpl->assign('minicart_display',true);
	$minicart_display = true;
}else{
	$minicart_display = false;
}

if(is_array($options)){
	$Contents = '
	<form name="pinfo" action="">
		<input type=hidden name=act value="update">
		<input type=hidden name="id" value="'.$pid.'">
		<input type=hidden name="sellprice" value="'.$pinfo[sellprice].'">
		<input type=hidden name="dcprice" value="'.$pinfo[dcprice].'">
		<input type=hidden name="set_group" value="'.$pinfo[set_group].'">
		<input type=hidden name=stock value="'.$pinfo[stock].'">
		<input type=hidden name=stock_use_yn value="'.$pinfo[stock_use_yn].'">
		<input type=hidden name=option_stock value="">
		<input type=hidden name=option_price value="">
		<input type=hidden name=pname value="'.$pinfo[pname].'">
		<input type=hidden id="delivery_package" value="'.$delivery_package.'">
		<table width="100%" style="margin:0px 0px 20px 0px;">
				<tr><td><img src="./images/dot_org.gif" align="absmiddle"> <b>상품정보</b></td></tr>
				<tr>
					<td align="center" width="70">
					<img src="'.PrintImage($admin_config[mall_data_root]."/images/product", $pid, "m" , $pinfo).'"  onerror=\"this.src=\''.$admin_config[mall_data_root].'/images/noimg_52.gif\'" width=50 style="margin:5px;"<br/>';

					if($pinfo[product_type]=='21'||$pinfo[product_type]=='31'){
						$Contents .= '<label class="helpcloud" help_width="190" help_height="15" help_html="'.($pinfo[product_type]=="21" ? "서브스크립션 커머스(배송상품)" : "로컬딜리버리 커머스(배송상품)").'"><img src="./images/'.$admininfo[language].'/s_product_type_'.$pinfo[product_type].'.gif" align="absmiddle" ></label> ';
					}
					if($pinfo[stock_use_yn]=="Y"){
						$Contents .= '<label class="helpcloud" help_width="140" help_height="15" help_html="(WMS)재고관리 상품"><img src="./images/'.$admininfo[language].'/s_inventory_use.gif" align="absmiddle" ></label>';
					}
		$Contents .= '
					</td>
					<td style="padding:5px 0 5px 0;line-height:150%" align="left">';

			if($admininfo[admin_level] == 9){
				$Contents .= '<b>'.($pinfo[company_name] ? $pinfo[company_name]:"-").'</b><br>';
			}

			if(in_array($pinfo[product_type],$sns_product_type)){
				$Contents .= "".$pinfo[pname]."";
			}else{
				//$Contents .= "<a href=\"/shop/goods_view.php?id=".$pid."\" target=_blank>";
				if($pinfo[product_type]=='99'||$pinfo[product_type]=='21'||$pinfo[product_type]=='31'){
					$Contents .= "<b class='".($pinfo[product_type]=='99' ? "red" : "blue")."' >".$pinfo[pname]."</b><br/><strong>".$pinfo[set_name]."<br /></strong>".$pinfo[sub_pname];
				}else{
					$Contents .= $pinfo[pname];
				}
				//$Contents .= "</a>";
			}
				if($pinfo[sellprice] > $pinfo[dcprice]){
					$Contents .= "<br><s>".number_format($pinfo[sellprice])."원</s> ".number_format($pinfo[dcprice])."원 ";
				}else{
					$Contents .= "<br>".number_format($pinfo[sellprice])."원 ";
				}

		$Contents .= "
					</td>
				</tr>
			</table>
			";

foreach($options as $key => $option){
	$_option_kind  = $option[option_kind];

	if($option[option_kind] == "r"){
		$Contents = '
				<table cellspacing="0" cellpadding="0" border="0" width="100%" class="choice_box goods_option basic-option-area">
				<col width="30%" />
				<col width="*" />
				<tr>
					<td class="Pgap_L16 bg_f7">
						<span class="Pgap_H5 color_7d">'.$option[option_name].'</span>
					</td>
					<td class="Pgap_H5 bg_f7">
						'.getMakeOption($option[option_name],$pid,$option[opn_ix],$option[option_kind]).'				
					</td>
				</tr>
				<tr><td colspan="2" class="big_line"></td></tr>
				</table>';

	}else if(($option[option_kind] == "c1" || $option[option_kind] == "c2" || $option[option_kind] == "i1" || $option[option_kind] == "i2" ||  $option[option_kind] == "p" || $option[option_kind] == "s" || $option[option_kind] == "b")  && $product_type != 99){
				if($key == 0){
		$Contents .= '
				<div style="padding:10px 0px;"><img src="./images/dot_org.gif" align="absmiddle"> <b>필수옵션</b></div>';
				}
		$Contents .= '
				<table cellspacing="0" cellpadding="0" border="0" width="100%" class="table_fix goods_option basic-option-area">
				<col width="30%" />
				<col width="*" />
				<tr>
					<td class="Pgap_L16 bg_f7">
						<span class="Pgap_H5 color_7d">'.$option[option_name].'</span>
					</td>
					<td class="Pgap_H5 bg_f7">
					'.getMakeOption($option[option_name],$pid,$option[opn_ix],$option[option_kind], $option[return_type]).'
					</td>
				</tr>
				<tr><td colspan="2" class="big_line"></td></tr>
				</table>';
			
		}else if($option[option_kind] == "x2" || $option[option_kind] == "s2"){
		$Contents .= '
				<table cellspacing="0" cellpadding="0" border="0" width="100%" class="choice_box goods_option">
					<col width="240" />
					<col width="*" />
					<tr>
						<td colspan="2">
							<table cellspacing="0" cellpadding="0" border="0" width="" class="choice_box_1 goods_option basic-option-area">
								<col width="122" />
								<col width="*" />
								<tr>
									<th class="size_11" style="height:36px;">옵션선택</th>
									<th><span class="main_color">'.$option[option_name].'</span></th>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type=hidden name="option_kind" id="option_kind" class="option_kind"  value="'.$option[option_kind].'">
							<table cellspacing="0" cellpadding="0" border="0" width="100%" class="choice_box_2" id="minicart">';


		foreach(getMakeOption($option[option_name],$pid,$option[opn_ix],$option[option_kind]) as $_key => $_option){

		$Contents .= '
								<tr class="order_detail_rows">
									<th>';
										if($_option[set_group_seq] == '0'){
		$Contents .= '
										<input type="radio" name="set_choice" set_group="'.$_option[set_group].'" id="box_option_pid_'.$_option[opnd_ix].'" opnd_ix="'.$_option[opnd_ix].'" value="'.$_option[opnd_ix].'" onclick="setPackage($(this));CalcurateBoxOption($(this));" '.($selected_options[$_option[opnd_ix]]["checked"] ? "checked":"").' /> 
										<label for="box_option_pid_'.$_option[id].'"><strong>'.cut_str($_option[option_div],36).'</strong>';

											if($_option[option_listprice] - $_option[option_price]/$_option[option_price]*100 > 0){
		$Contents .= '
											<span class="color_R">
												('.number_format(( $_option[option_listprice] - $_option[option_price])/$_option[option_price]*100,0).'% <img src="'.$_SESSION['admininfo']['mall_data_root'].'/templet/'.$_SESSION['admin_config']['mall_use_templete'].'/images/common/icon_down.png" alt="" title="" style="vertical-align:-1px;" />)
											</span>';
											}
		$Contents .= '
										</label>';
										}else{
		$Contents .= '
											<div style="padding:0 0 0 16px;line-height:150%;">
											<input type="hidden" name="set_options['.$_option[opnd_ix].'][opnd_ix]" minicart_id=opnd_ix  opnd_ix="'.$_option[opnd_ix].'" value="'.$selected_options[$_option[opnd_ix]]["opnd_ix"].'"  set_group="'.$_option[set_group].'" />
											<input type="hidden" name="set_options['.$_option[opnd_ix].'][set_cnt]" minicart_id=set_cnt  value="'.$_option[set_cnt].'" />
											<input type="hidden" name="set_options['.$_option[opnd_ix].'][cart_ix]" minicart_id=cart_ix  value="'.$selected_options[$_option[opnd_ix]]["cart_ix"].'" />
											<input type="hidden" name="set_options['.$_option[opnd_ix].'][deleteable]" minicart_id=deleteable  value=0 />
											'.cut_str($_option[option_div],36).'<strong>'.$_option[set_cnt].'</strong>개
											</div>';
										}
										if($_option[set_group_seq] == '0'){
		$Contents .= '
									</th>
									<td style="padding-right:20px;">
										<div class="float02">
											<div class="float01"><strike>'.number_format($_option[option_listprice]).'</strike>&nbsp;&nbsp;<strong class="main_color">'.number_format($_option[option_price]).'</strong></div>
											<ul class="option_up_down">
												<li><input type="text" name="set_options['.$_option[opnd_ix].'][pcount]" minicart_id=amount id="pcount_'.$_option[opnd_ix].'" value="'.($selected_options[$_option[opnd_ix]]["set_count"] ? $selected_options[$_option[opnd_ix]]["set_count"]:"1").'" size=4 maxlength=3 onkeydown="onlyEditableNumber(this)" onkeyup="onlyEditableNumber(this);" />
												<li style="border:1px solid #c2c2c2;line-height:0;font-size:0;"><img src="'.$_SESSION['admininfo']['mall_data_root'].'/templet/'.$_SESSION['admin_config']['mall_use_templete'].'/images/up_arrow.gif" alt="" onclick="option_pcount_cnt($(this).parent().parent().find(\'#pcount_'.$_option[opnd_ix].'\'),\'p\', \''.$_option[opnd_ix].'\');CalcurateBoxOption($(\'#box_option_pid_'.$_option[opnd_ix].'\'));" /></li>
												<li style="border:1px solid #c2c2c2;border-left:0 none;line-height:0;font-size:0;"><img src="'.$_SESSION['admininfo']['mall_data_root'].'/templet/'.$_SESSION['admin_config']['mall_use_templete'].'/images/down_arrow.gif" alt="" onclick="option_pcount_cnt($(this).parent().parent().find(\'#pcount_'.$_option[opnd_ix].'\'),\'m\', \''.$_option[opnd_ix].'\');CalcurateBoxOption($(\'#box_option_pid_'.$_option[opnd_ix].'\'));" /></li>
											</ul>
										</div>
									</td>';
										}
		$Contents .= '
								</tr>';
		}
		$Contents .= '	
								<tr>
									<td colspan="2" valign="middle" style="padding-left:13px;height:46px;">
										<span class="size_12">※</span> 구매하실 세트상품을 체크해 주셔야 구매가 가능합니다. 
										<!--img src="'.$_SESSION['admininfo']['mall_data_root'].'/templet/'.$_SESSION['admin_config']['mall_use_templete'].'/images/korea/btns/btn_insert.gif" alt="담기" title="" align="absmiddle" style="position:relative;bottom:2px;cursor:pointer;" /--> 
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>';

	}else if($option[option_kind] == "a"){

			if($b_option_kind == ''){
			$Contents .= ' 
			<table cellspacing="" cellpadding="0" border="0" width="100%" class="option_table02 goods_option add-option-area">
				<col width="30%" />
				<col width="*" />
				
				<tr>
					<td class="Pgap_L16" colspan="2" style="height:0;padding:10px 0 3px 16px;font-size:12px;"><strong>추가구성상품</strong></td>
				</tr>';
			}
			$Contents .= ' 
				<tr height=35>
					<td class="Pgap_L16">'.$option[option_name].'</td>
					<td>
						'.getMakeOption($option[option_name],$pid,$option[opn_ix],$option[option_kind]).'
					</td>
				</tr>';

			if(count($option) == $key+1){
			$Contents .= ' 
				<tr><td colspan="2" style="height:6px;padding:0;"></td></tr>
			</table>';
			}

			$b_option_kind = $option[option_kind];
		}else if($option[option_kind] == "c"){
			$codi_count = $codi_count + 1;

			if($codi_count == 1){
		$Contents .= ' 
			<input type=hidden name="option_kind" id="option_kind" class="option_kind"  value="'.$option[option_kind].'">
			<table cellspacing="0" cellpadding="0" border="0" width="100%" class="codi_set" id="minicart">
				<col width="122" />
				<col width="*" />
				<tr>
					<th class="size_11">옵션선택</th>
					<th><span class="main_color">코디 세트 상품<span style="font-weight:normal;color:#03848c;">(고정)</span></span></th>
				</tr>
				<tr>
					<td colspan="2">코디상품 옵션은 각 옵션별 상품을 모두 선택하셔야합니다.</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="float01">수량 : </div>
						<ul class="option_up_down">
							<li><input type="text" name="pcount" id="pcount" minicart_id="pcount" value="'.($_GET["set_count"] ? $_GET["set_count"]:1).'" size=4 maxlength=3 onkeydown="onlyEditableNumber(this)" onkeyup="onlyEditableNumber(this);" />
							<li style="border:1px solid #c2c2c2;line-height:0;font-size:0;"><img src="'.$_SESSION['admininfo']['mall_data_root'].'/templet/'.$_SESSION['admin_config']['mall_use_templete'].'/images/up_arrow.gif" alt="" onclick="option_pcount_cnt($(this).parent().parent().find(\'#pcount\'), \'p\');" /></li>
							<li style="border:1px solid #c2c2c2;border-left:0 none;line-height:0;font-size:0;"><img src="'.$_SESSION['admininfo']['mall_data_root'].'/templet/'.$_SESSION['admin_config']['mall_use_templete'].'/images/down_arrow.gif" alt="" onclick="option_pcount_cnt($(this).parent().parent().find(\'#pcount\'), \'m\');" /></li>
						</ul>
					</td>
				</tr>
				<tr><td colspan="2" style="height:10px;border-bottom:1px dotted #b6b6b6;"></td></tr>
				<tr>
					<td colspan="2" style="padding-left:0;">
						<div class="codi_set_01">
							<table cellpadding="0" cellspacing="0" border="0" width="100%" id="minicart_detail">
							<col width="60" />
							<col width="60" />
							<col width="*" />';
			}

			$Contents .= ' 
								<tr class="order_detail_rows">
									<td>$option[option_name}</td>
									<td><input type="checkbox" name="" id="" class="vm" /> <label for="">검색</label></td>
									<td>
										'.getMakeOption($option[option_name], $pid, $option[opn_ix], $option[option_kind], $option[return_type], $option[opnd_ix]).'
									</td>
								</tr>';

			if($key == (count($options)-1)){
				$Contents .= ' 
							</table>
						</div>
					</td>
				</tr>
				<tr><td colspan="2" style="height:6px;padding:0;"></td></tr>
			</table>';
			}
			 

	}else if($option[option_kind] == "x"){
			$Contents .= ' 
				<input type=hidden name="option_kind" id="option_kind" class="option_kind"  value="'.$option[option_kind].'">
				<table cellspacing="0" cellpadding="0" border="0" width="100%" class="choice_box">
					<col width="240" />
					<col width="*" />
					<tr>
						<td colspan="2">
							<table cellspacing="0" cellpadding="0" border="0" width="" class="choice_box_1">
								<col width="122" />
								<col width="*" />
								<tr>
									<th class="size_11">옵션선택</th>
									<th><span class="main_color">$option[option_name}</span></th>
								</tr>
								<tr>
									<td colspan="2" class="size_11">
										초이스 박스옵션 : <strong>1BOX</strong>에 <strong class="color_R">{.box_total}</strong>개 상품입니다.
									</td>
								</tr>
								<tr>
									<td colspan="2" style="padding-bottom:12px;">
										<span class="float01 size_11">박스 수량 : </span>
										<ul class="option_up_down">
											<li><input type="text" name="box_count" id="box_count" value=1 size=4 maxlength=3 onkeydown="onlyEditableNumber(this)" onkeyup="onlyEditableNumber(this);" />
											<li style="border:1px solid #c2c2c2;line-height:0;font-size:0;"><img src="'.$_SESSION['admininfo']['mall_data_root'].'/templet/'.$_SESSION['admin_config']['mall_use_templete'].'/images/up_arrow.gif" alt="" onclick="option_pcount_cnt($(this).parent().parent().find(\'#box_count\'), \'p\');" /></li>
											<li style="border:1px solid #c2c2c2;border-left:0 none;line-height:0;font-size:0;"><img src="'.$_SESSION['admininfo']['mall_data_root'].'/templet/'.$_SESSION['admin_config']['mall_use_templete'].'/images/down_arrow.gif" alt="" onclick="option_pcount_cnt($(this).parent().parent().find(\'#box_count\'), \'m\');" /></li>
										</ul>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="choice_box_total_text">
							선택하신 박스 수량은 <strong><span id="box_total_cnt">1</span>BOX</strong> x <strong id="goods_cnt_per_1box">'.$box_total.'</strong>개 = 총 <span  class="color_R"><strong id="total_goods_cnt">'.$box_total.'</strong>개의 상품을 선택</span>해주세요.
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type=hidden name="option_kind" id="option_kind" value="'.$option[option_kind].'">
							<table cellspacing="0" cellpadding="0" border="0" width="100%" class="choice_box_2" id="minicart">
								<tr>
									<th colspan="2" height="32">'.cut_str($option[option_name],63).'</th>
								</tr>';
								foreach(getMakeOption($option[option_name],$pid,$option[opn_ix],$option[option_kind]) as $_key => $_option){
								$Contents .= ' 
								<tr class="order_detail_rows">
									<th>
										<input type=hidden name="box_options['.$_option[opnd_ix].'][cart_ix]" minicart_id=cart_ix value="'.$selected_options[$_option[opnd_ix]]["cart_ix"].'">
										<input type="checkbox" name="box_options['.$_option[opnd_ix].'][opnd_ix]" minicart_id=opnd_ix id="box_option_pid_'.$_option[opnd_ix].'" opnd_ix="'.$_option[opnd_ix].'" value="'.$_option[opnd_ix].'" onclick="CalcurateBoxOption($(this));" '.($selected_options[$_option[opnd_ix]]["checked"] ? "checked":"").' /> <label for="box_option_pid_'.$_option[id].'">'.cut_str($_option[option_div],36).'</label>
									</th>
									<td style="padding-right:20px;">
										<div class="float02">
											<div class="float01"><strike>'.number_format($_option[option_listprice]).'</strike> <strong class="main_color">'.number_format($_option[option_price] - floor($_option[option_price]*($_SESSION["user"]["sale_rate"])/100),0).'</strong></div>
											<ul class="option_up_down">
												<li><input type="text" name="box_options['.$_option[opnd_ix].'][pcount]" minicart_id=amount id="pcount_'.$_option[opnd_ix].'" value="'.( $selected_options[$_option[opnd_ix]]["amount"] ? $selected_options[$_option[opnd_ix]]["amount"]:"1").'" size=4 maxlength=3 onkeydown="onlyEditableNumber(this)" onkeyup="onlyEditableNumber(this);" />
												<li style="border:1px solid #c2c2c2;line-height:0;font-size:0;"><img src="'.$_SESSION['admininfo']['mall_data_root'].'/templet/'.$_SESSION['admin_config']['mall_use_templete'].'/images/up_arrow.gif" alt="" onclick="option_pcount_cnt($(this).parent().parent().find(\'#pcount_'.$_option[opnd_ix].'\'),\'p\', \''.$_option[opnd_ix].'\');CalcurateBoxOption($(\'#box_option_pid_'.$_option[opnd_ix].'\'));" /></li>
												<li style="border:1px solid #c2c2c2;border-left:0 none;line-height:0;font-size:0;"><img src="'.$_SESSION['admininfo']['mall_data_root'].'/templet/'.$_SESSION['admin_config']['mall_use_templete'].'/images/down_arrow.gif" alt="" onclick="option_pcount_cnt($(this).parent().parent().find(\'#pcount_'.$_option[opnd_ix].'\'),\'m\', \''.$_option[opnd_ix].'\');CalcurateBoxOption($(\'#box_option_pid_'.$_option[opnd_ix].'\'));" /></li>
											</ul>
										</div>
									</td>
								</tr>';
								}
								 $Contents .= '
								<tr>
									<td colspan="2" style="padding-left:13px;">
										<span class="size_12">※</span> 구매하실 세트상품을 체크해 주셔야 구매가 가능합니다.
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<div class="choice_box_total">
					총 <strong id="selected_box_total">0</strong>개 선택 /남은 수량 <span><strong id="remained_box_total">'.$box_total.'</strong>개</span>
				</div>';
		}
	}
}else{
		$Contents .= ' 
			<table cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top:10px;">
				<col width="137" />
				<col width="*" />
				<tr>
					<td class="Pgap_L16"><span class="float01 size_11">수량 : </span></td>
					<td style="padding-bottom:12px;">
						
						<ul class="option_up_down">
							<li><input type="text" name="pcount" id="pcount" value="'.($pcount > 0 ? $pcount:"1").'" size=4 maxlength=3 onkeydown="onlyEditableNumber(this)" onkeyup="onlyEditableNumber(this);" />
							<li style="border:1px solid #c2c2c2;line-height:0;font-size:0;"><img src="'.$_SESSION['admininfo']['mall_data_root'].'/templet/'.$_SESSION['admin_config']['mall_use_templete'].'/images/up_arrow.gif" alt="" onclick="option_pcount_cnt($(this).parent().parent().find(\'#pcount\'), \'p\');" /></li>
							<li style="border:1px solid #c2c2c2;border-left:0 none;line-height:0;font-size:0;"><img src="'.$_SESSION['admininfo']['mall_data_root'].'/templet/'.$_SESSION['admin_config']['mall_use_templete'].'/images/down_arrow.gif" alt="" onclick="option_pcount_cnt($(this).parent().parent().find(\'#pcount\'), \'m\');" /></li>
						</ul>
					</td>
				</tr>
				</table>';
}



		if($_option_kind == "p" || $_option_kind == "s"  || $_option_kind == "a"  || $_option_kind == "b" || $_option_kind == "b" || $_option_kind == "c1" || $_option_kind == "c2" || $_option_kind == "i1" || $_option_kind == "i2"){
			 
		$Contents .= ' 
			<table cellpadding="0" cellspacing="0" border="0" width="100%" style="table-layout:fixed;margin-top:30px;'.($minicart_display ? 'display:block;':'display:none;').'"  class="option_table03" id="minicart">
				<colgroup>
				<col width="*" />
				<col width="68" />
				<col width="101" />
				</colgroup>

					<tr height=28><td colspan="3" class="size_11" style="padding:3px 0 3px 10px;background-color:#efefef;"><strong>주문상품</strong></td></tr>
					<!----minicart 옵션 시작--------->';
				if(is_array($cart_options)){
					foreach($cart_options as $key => $cart_option){

					$sellprice_sum  = $sellprice_sum + ($cart_option[dcprice] + $cart_option[option_price]) * $pcount;
		$Contents .= ' 
					<tr class="order_detail_rows" id="{.pid}" delete="0">
						<td colspan="3" >
							<table cellpadding="0" cellspacing="0" border="0" width="100%" style="table-layout:fixed;" id="minicart_detail">
								<tr height="34" >
									<td class="Pgap_L16">
										<span minicart_id="pname_text">'.$options_text.'</span>
									</td>
									<td style="width:68px;">
										<ul class="option_up_down" style="float:left;">
											<li style="float:left;">
											<input type=hidden name="order_lists[0][basic]" minicart_id=basic value="'.($cart_option[option_kind] == "" ? 1:0).'">
											<input type=hidden name="order_lists[0][cart_ix]" minicart_id=cart_ix value="'.$cart_option[cart_ix].'">
											<input type=hidden name="order_lists[0][pid]" minicart_id=pid value="'.$cart_option[pid].'">
											<input type=hidden name="order_lists[0][opnd_ix]" minicart_id=opnd_ix value="'.$cart_option[select_option_id].'">
											<input type=hidden name="order_lists[0][gu_ix]" minicart_id=gu_ix  value="">
											<input type=hidden name="order_lists[0][sellprice]" minicart_id=sellprice value="'.$cart_option[sellprice].'">
											<input type=hidden name="order_lists[0][dcprice]" minicart_id=dcprice value="'.$cart_option[dcprice].'">
											<input type=hidden name="order_lists[0][option_price]" minicart_id=option_price value="'.$cart_option[option_price].'">
											<input type=hidden name="order_lists[0][option_kind]" minicart_id=option_kind value="'.$cart_option[option_kind].'">
											<input type="hidden" name="order_lists[0][deleteable]" minicart_id=deleteable  value="0" />
											<input type="text" name="order_lists[0][amount]" minicart_id="amount" value="'.$cart_option[pcount].'" allow_basic_cnt="0" size=4 maxlength=3 onkeydown="onlyEditableNumber(this)" onkeyup="onlyEditableNumber(this);" style="margin:0px 2px;"/>
											<li style="border:1px solid #c2c2c2;line-height:0;font-size:0;float:left;">
												<img src="'.$_SESSION['admininfo']['mall_data_root'].'/templet/'.$_SESSION['admin_config']['mall_use_templete'].'/images/up_arrow.gif" alt="" onclick="minicart_pcount_cnt($(this), \'p\');" />
											</li>
											<li style="border:1px solid #c2c2c2;border-left:0 none;line-height:0;font-size:0;float:left;">
												<img src="'.$_SESSION['admininfo']['mall_data_root'].'/templet/'.$_SESSION['admin_config']['mall_use_templete'].'/images/down_arrow.gif" alt="" onclick="minicart_pcount_cnt($(this), \'m\');" />
											</li>
										</ul>
									</td>
									<td align="right" class="Pgap_R8" style="width:101px;padding-left:20px;">
										<strong class="main_color" style="vertical-align:middle;margin-right:7px;" minicart_id="total_price">'.number_format($cart_option[totalprice]).'</strong><img src="'.$_SESSION['admininfo']['mall_data_root'].'/templet/'.$_SESSION['admin_config']['mall_use_templete'].'/images/btns/btn_del02.gif" alt="삭제" align="absmiddle" style="cursor:pointer;" ondblclick="minicart_delete($(this).closest(\'.order_detail_rows\'));minicart_total();"/>
									</td>
								</tr>
							</table>
							<div class="option_table03_1" style="display:none;">
								<table cellspacing="" cellpadding="0" border="0" width="100%">
								<col width="103" />
								<col width="*" />
									<tr>
										<td><span>문구작성</span></td>
										<td>
											<input type="text" id="" name="" class="inputbox_01" style="width:278px;" />
										</td>
									</tr>
									<tr>
										<td><span>파일저장</span></td>
										<td>
											<input type="text" id="fileName" name="" class="inputbox_01" style="width:173px;float:left;" readonly="readonly" />
											<div class="file_input_div">
												<input type="button" value="Search files" class="file_input_button" />
												<input type="file" class="file_input_hidden" name="receipt_file" onchange="javascript: document.getElementById(\'fileName\').value = this.value" />
											</div>
											<img src="'.$_SESSION['admininfo']['mall_data_root'].'/templet/'.$_SESSION['admin_config']['mall_use_templete'].'/images/btns/btn_del02.gif" alt="삭제" align="absmiddle" style="float:right;margin:2px 13px 0 0;cursor:pointer;"/>
										</td>
									</tr>
								</table>
							</div>
						</td>	
					</tr> 
					<tr><td colspan="3" class="dotted_b6" height="1"></td></tr>';
					}
				}else{
		$Contents .= ' 
					<tr class="order_detail_rows" delete=1>
						<td colspan="3" >
							<table cellpadding="0" cellspacing="0" border="0" width="100%" style="table-layout:fixed;" id="minicart_detail">
								<tr height="34" >
									<td class="Pgap_L16">
										<span minicart_id="pname_text">-</span>
									</td>
									<td style="width:78px;">
										<ul class="option_up_down" style="float:left;">
											<li style="float:left;">
											<input type=hidden name="order_lists[0][basic]" minicart_id=basic value="0">
											<input type=hidden name="order_lists[0][cart_ix]" minicart_id=cart_ix value="">
											<input type=hidden name="order_lists[0][pid]" minicart_id=pid value="">
											<input type=hidden name="order_lists[0][opnd_ix]" minicart_id=opnd_ix value="">
											<input type=hidden name="order_lists[0][gu_ix]" minicart_id=gu_ix  value="">
											<input type=hidden name="order_lists[0][sellprice]" minicart_id=sellprice value="">
											<input type=hidden name="order_lists[0][dcprice]" minicart_id=dcprice value="">
											<input type=hidden name="order_lists[0][option_price]" minicart_id=option_price value="">
											<input type=hidden name="order_lists[0][option_kind]" minicart_id=option_kind value="">
											<input type="hidden" name="order_lists[0][deleteable]" minicart_id=deleteable  value="0" />

											<input type="text" name="order_lists[0][amount]" minicart_id="amount" value=1 allow_basic_cnt="0" size=4 maxlength=3 onkeydown="onlyEditableNumber(this)" onkeyup="onlyEditableNumber(this);" style="margin:0px 2px;" />
											</li>
											<li style="border:1px solid #c2c2c2;line-height:0;font-size:0;float:left;">
												<img src="'.$_SESSION['admininfo']['mall_data_root'].'/templet/'.$_SESSION['admin_config']['mall_use_templete'].'/images/up_arrow.gif" alt="" onclick="minicart_pcount_cnt($(this), \'p\');" />
											</li>
											<li style="border:1px solid #c2c2c2;border-left:0 none;line-height:0;font-size:0;float:left;">
												<img src="'.$_SESSION['admininfo']['mall_data_root'].'/templet/'.$_SESSION['admin_config']['mall_use_templete'].'/images/down_arrow.gif" alt="" onclick="minicart_pcount_cnt($(this), \'m\');" />
											</li>
										</ul>
									</td>
									<td align="right" class="Pgap_R8" style="width:101px;padding-left:20px;">
										<strong class="main_color" style="vertical-align:middle;margin-right:7px;" minicart_id="total_price">0</strong><img src="'.$_SESSION['admininfo']['mall_data_root'].'/templet/'.$_SESSION['admin_config']['mall_use_templete'].'/images/btns/btn_del02.gif" alt="삭제" align="absmiddle" style="cursor:pointer;" onclick="minicart_delete($(this).parent().parent().parent().parent().parent().parent());minicart_total();"/>
									</td>
								</tr>
							</table>
							<div class="option_table03_1" style="display:none;">
								<table cellspacing="" cellpadding="0" border="0" width="100%">
								<col width="103" />
								<col width="*" />
									<tr>
										<td><span>문구작성</span></td>
										<td>
											<input type="text" id="" name="" class="inputbox_01" style="width:278px;" />
										</td>
									</tr>
									<tr>
										<td><span>파일저장</span></td>
										<td>
											<input type="text" id="fileName" name="" class="inputbox_01" style="width:173px;float:left;" readonly="readonly" />
											<div class="file_input_div">
												<input type="button" value="Search files" class="file_input_button" />
												<input type="file" class="file_input_hidden" name="receipt_file" onchange="javascript: document.getElementById(\'fileName\').value = this.value" />
											</div>
											<img src="'.$_SESSION['admininfo']['mall_data_root'].'/templet/'.$_SESSION['admin_config']['mall_use_templete'].'/images/btns/btn_del02.gif" alt="삭제" align="absmiddle" style="float:right;margin:2px 13px 0 0;cursor:pointer;"/>
										</td>
									</tr>
								</table>
							</div>
						</td>	
					</tr> 
					<tr><td colspan="3" style="height:1px;background:url('.$_SESSION['admininfo']['mall_data_root'].'/templet/'.$_SESSION['admin_config']['mall_use_templete'].'/images/dot_b6b6b6.png) repeat-x;"></td></tr>';
					}
		$Contents .= ' 
				</table>
					<!----minicart 옵션 끝--------->

		 
				<table cellpadding="0" cellspacing="0" border="0" width="100%" style="'.($minicart_display ? "":"display:none;").'" id="minicart_sum">
					<tr>
						<td style="padding:12px 0 24px 0px;" class="size_11">
							<strong>총 합계금액</strong>(수량)
						</td>
						<td align="right"  style="padding:12px 9px 24px 0;" class="size_14 main_color" id="minicart_total">
							<strong>'.number_format($sellprice_sum).'</strong><span class="size_12 main_color">원 (<strong>2</strong>개)</span>
						</td>
					</tr>
				</table>';
		}
$Contents .= '<div style="text-align:center;padding:20px 0px;"><img src="./images/korea/b_save.gif" border="0" style="cursor:pointer;border:0px;" onclick="SelectGoodsOption(document.pinfo)"></div>';
$Contents .= '</form>'; 

$P = new ManagePopLayOut();
$P->addScript = "<script type='text/javascript'>
<!--
var allow_basic_cnt = 1;
var allow_byoneperson_cnt = 9999;
//-->
</script><script language='javascript' src='./goods_option_select.js'></script><script language='javascript' src='".$_SESSION['admininfo']['mall_data_root']."/templet/".$_SESSION['admin_config']['mall_use_templete']."/js/goods_view.js'></script>";
$P->Navigation = "상품선택";
$P->NaviTitle = "상품선택";
$P->strContents = $Contents;
echo $P->PrintLayOut();

 