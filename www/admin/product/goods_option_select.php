<?
include("../class/layout.class");
$admin_delievery_policy = getTopDeliveryPolicy($db);//$db->dt;
/*
2014-09-11 옵션미리보기 용 팝업창
shop_product_temp
shop_product_options_temp
shop_product_options_detail_temp


*/
	
	$reserve_sql = " ,case when p.reserve_yn = 'Y' then floor(p.sellprice*(p.reserve_rate/100)) else 0 end as reserve";
	$select_price = 'sellprice, listprice, (listprice-sellprice)/listprice*100 as sale_rate ';

	$sql = "SELECT 
				id, pname, 
				$select_price, 
				pcode, shotinfo,brand, 
				(stock+available_stock) as stock,  company, stock_use_yn,
				icons,brand_name,delivery_company,   p.etc9, p.product_type,admin, is_sell_date, sell_priod_edate,
				allow_basic_cnt, allow_max_cnt, allow_byoneperson_cnt
			FROM 
				shop_product_temp p
			where
				id = $pid 
				limit 0,1";

	$db->query($sql);

	$pinfos = $db->fetchall();
	$goods_infos[$pinfos[0][id]][pid] = $pinfos[0][id];
	$goods_infos[$pinfos[0][id]][amount] = $pinfos[0][cid];
	$goods_infos[$pinfos[0][id]][cid] = $pinfos[0][cid];
	$goods_infos[$pinfos[0][id]][depth] = $pinfos[0][depth];

	foreach ($pinfos as $key => $sub_array) {
		$select_ = array("icons_list"=>explode(";",$sub_array[icons]));
		array_insert($sub_array,14,$select_);

		$_dcprice = $sub_array[sellprice];

		$_dcprice = array("dcprice"=>$_dcprice);
		array_insert($sub_array,52,$_dcprice);
		$discount_desc = array("discount_desc"=>$discount_desc);
		array_insert($sub_array,53,$discount_desc);

		$pinfos[$key] = $sub_array;
	}

	$pinfo = $pinfos[0];

	$pcount = 1;

$sql = "SELECT 
			option_name ,
			box_total,
			po.opn_ix,
			'select' as return_type,
			option_kind
		FROM
			shop_product_options_temp po
		where
			po.pid = '".$pid."' 
			and po.option_use ='1'  
			ORDER BY 
			CASE WHEN option_kind='s' THEN 1 
			WHEN option_kind='p' THEN 1 
			WHEN option_kind='r' THEN 1 
			WHEN option_kind='g' THEN 1 
			WHEN option_kind='b' THEN 2 
			WHEN option_kind='x' THEN 2 
			WHEN option_kind='x2' THEN 3 
			WHEN option_kind='s2' THEN 4 
			WHEN option_kind='a' THEN 5 END ";

$db->query($sql);
$options = $db->fetchall();

/*
* 작업자 : 신훈식
* 일시 : 2014년 04월 22일
* 작업내용 : 초이스 세트상품 옵션 수량 변경 폼 구성을 위한 정보생성
*/
$minicart_display = false;

if(is_array($options)){
	$Contents = '
	<form name="pinfo" action="">
		
		<input type=hidden name=act value="update">
		<input type=hidden name=session_id value="'.session_id().'">
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
		<input type=hidden name=est_ix value="'.$est_ix.'">

		<table width="100%" style="margin:0px 0px 20px 0px;">
				<tr><td><img src="../images/dot_org.gif" align="absmiddle"> <b>상품정보</b></td></tr>
				<tr>
					<td align="center" width="70">
					<img src="'.PrintImage($admin_config[mall_data_root]."/images/product", $pid, "m" , $pinfo).'"  onerror=\"this.src=\''.$admin_config[mall_data_root].'/images/noimg_52.gif\'" width=50 style="margin:5px;"<br/>';

					if($pinfo[product_type]=='21'||$pinfo[product_type]=='31'){
						$Contents .= '<label class="helpcloud" help_width="190" help_height="15" help_html="'.($pinfo[product_type]=="21" ? "서브스크립션 커머스(배송상품)" : "로컬딜리버리 커머스(배송상품)").'"><img src="../images/'.$admininfo[language].'/s_product_type_'.$pinfo[product_type].'.gif" align="absmiddle" ></label> ';
					}
					if($pinfo[stock_use_yn]=="Y"){
						$Contents .= '<label class="helpcloud" help_width="140" help_height="15" help_html="(WMS)재고관리 상품"><img src="../images/'.$admininfo[language].'/s_inventory_use.gif" align="absmiddle" ></label>';
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
						'.getMakeOptionTmp($option[option_name],$pid,$option[opn_ix],$option[option_kind]).'				
					</td>
				</tr>
				<tr><td colspan="2" class="big_line"></td></tr>
				</table>';

	}else if(($option[option_kind] == "c1" || $option[option_kind] == "c2" || $option[option_kind] == "i1" || $option[option_kind] == "i2" ||  $option[option_kind] == "p" || $option[option_kind] == "s" || $option[option_kind] == "b")  && $product_type != 99){
				if($key == 0){
		$Contents .= '
				<div style="padding:10px 0px;"><img src="../images/dot_org.gif" align="absmiddle"> <b>필수옵션</b></div>';
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
					'.getMakeOptionTmp($option[option_name],$pid,$option[opn_ix],$option[option_kind], $option[return_type]).'
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


		foreach(getMakeOptionTmp($option[option_name],$pid,$option[opn_ix],$option[option_kind]) as $_key => $_option){

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
												('.number_format(( $_option[option_listprice] - $_option[option_price])/$_option[option_price]*100,0).'% <img src="/data/daiso_data/templet/daiso/images/common/icon_down.png" alt="" title="" style="vertical-align:-1px;" />)
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
												<li style="border:1px solid #c2c2c2;line-height:0;font-size:0;"><img src="/data/daiso_data/templet/daiso/images/up_arrow.gif" alt="" onclick="option_pcount_cnt($(this).parent().parent().find(\'#pcount_'.$_option[opnd_ix].'\'),\'p\', \''.$_option[opnd_ix].'\');CalcurateBoxOption($(\'#box_option_pid_'.$_option[opnd_ix].'\'));" /></li>
												<li style="border:1px solid #c2c2c2;border-left:0 none;line-height:0;font-size:0;"><img src="/data/daiso_data/templet/daiso/images/down_arrow.gif" alt="" onclick="option_pcount_cnt($(this).parent().parent().find(\'#pcount_'.$_option[opnd_ix].'\'),\'m\', \''.$_option[opnd_ix].'\');CalcurateBoxOption($(\'#box_option_pid_'.$_option[opnd_ix].'\'));" /></li>
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
										<!--img src="/data/daiso_data/templet/daiso/images/korea/btns/btn_insert.gif" alt="담기" title="" align="absmiddle" style="position:relative;bottom:2px;cursor:pointer;" /--> 
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
						'.getMakeOptionTmp($option[option_name],$pid,$option[opn_ix],$option[option_kind]).'
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
							<li style="border:1px solid #c2c2c2;line-height:0;font-size:0;"><img src="/data/daiso_data/templet/daiso/images/up_arrow.gif" alt="" onclick="option_pcount_cnt($(this).parent().parent().find(\'#pcount\'), \'p\');" /></li>
							<li style="border:1px solid #c2c2c2;border-left:0 none;line-height:0;font-size:0;"><img src="/data/daiso_data/templet/daiso/images/down_arrow.gif" alt="" onclick="option_pcount_cnt($(this).parent().parent().find(\'#pcount\'), \'m\');" /></li>
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
										'.getMakeOptionTmp($option[option_name], $pid, $option[opn_ix], $option[option_kind], $option[return_type], $option[opnd_ix]).'
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
											<li style="border:1px solid #c2c2c2;line-height:0;font-size:0;"><img src="/data/daiso_data/templet/daiso/images/up_arrow.gif" alt="" onclick="option_pcount_cnt($(this).parent().parent().find(\'#box_count\'), \'p\');" /></li>
											<li style="border:1px solid #c2c2c2;border-left:0 none;line-height:0;font-size:0;"><img src="/data/daiso_data/templet/daiso/images/down_arrow.gif" alt="" onclick="option_pcount_cnt($(this).parent().parent().find(\'#box_count\'), \'m\');" /></li>
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
								foreach(getMakeOptionTmp($option[option_name],$pid,$option[opn_ix],$option[option_kind]) as $_key => $_option){
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
												<li style="border:1px solid #c2c2c2;line-height:0;font-size:0;"><img src="/data/daiso_data/templet/daiso/images/up_arrow.gif" alt="" onclick="option_pcount_cnt($(this).parent().parent().find(\'#pcount_'.$_option[opnd_ix].'\'),\'p\', \''.$_option[opnd_ix].'\');CalcurateBoxOption($(\'#box_option_pid_'.$_option[opnd_ix].'\'));" /></li>
												<li style="border:1px solid #c2c2c2;border-left:0 none;line-height:0;font-size:0;"><img src="/data/daiso_data/templet/daiso/images/down_arrow.gif" alt="" onclick="option_pcount_cnt($(this).parent().parent().find(\'#pcount_'.$_option[opnd_ix].'\'),\'m\', \''.$_option[opnd_ix].'\');CalcurateBoxOption($(\'#box_option_pid_'.$_option[opnd_ix].'\'));" /></li>
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
							<li style="border:1px solid #c2c2c2;line-height:0;font-size:0;"><img src="/data/daiso_data/templet/daiso/images/up_arrow.gif" alt="" onclick="option_pcount_cnt($(this).parent().parent().find(\'#pcount\'), \'p\');" /></li>
							<li style="border:1px solid #c2c2c2;border-left:0 none;line-height:0;font-size:0;"><img src="/data/daiso_data/templet/daiso/images/down_arrow.gif" alt="" onclick="option_pcount_cnt($(this).parent().parent().find(\'#pcount\'), \'m\');" /></li>
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
											<input type="text" name="order_lists[0][amount]" minicart_id="amount" value="'.$cart_option[pcount].'" size=4 maxlength=3 onkeydown="onlyEditableNumber(this)" onkeyup="onlyEditableNumber(this);" style="margin:0px 2px;"/>
											<li style="border:1px solid #c2c2c2;line-height:0;font-size:0;float:left;">
												<img src="/data/daiso_data/templet/daiso/images/up_arrow.gif" alt="" onclick="minicart_pcount_cnt($(this), \'p\');" />
											</li>
											<li style="border:1px solid #c2c2c2;border-left:0 none;line-height:0;font-size:0;float:left;">
												<img src="/data/daiso_data/templet/daiso/images/down_arrow.gif" alt="" onclick="minicart_pcount_cnt($(this), \'m\');" />
											</li>
										</ul>
									</td>
									<td align="right" class="Pgap_R8" style="width:101px;padding-left:20px;">
										<strong class="main_color" style="vertical-align:middle;margin-right:7px;" minicart_id="total_price">'.number_format($cart_option[totalprice]).'</strong><img src="/data/daiso_data/templet/daiso/images/btns/btn_del02.gif" alt="삭제" align="absmiddle" style="cursor:pointer;" ondblclick="minicart_delete($(this).closest(\'.order_detail_rows\'));minicart_total();"/>
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
											<img src="/data/daiso_data/templet/daiso/images/btns/btn_del02.gif" alt="삭제" align="absmiddle" style="float:right;margin:2px 13px 0 0;cursor:pointer;"/>
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

											<input type="text" name="order_lists[0][amount]" minicart_id="amount" value=1 size=4 maxlength=3 onkeydown="onlyEditableNumber(this)" onkeyup="onlyEditableNumber(this);" style="margin:0px 2px;" />
											</li>
											<li style="border:1px solid #c2c2c2;line-height:0;font-size:0;float:left;">
												<img src="/data/daiso_data/templet/daiso/images/up_arrow.gif" alt="" onclick="minicart_pcount_cnt($(this), \'p\');" />
											</li>
											<li style="border:1px solid #c2c2c2;border-left:0 none;line-height:0;font-size:0;float:left;">
												<img src="/data/daiso_data/templet/daiso/images/down_arrow.gif" alt="" onclick="minicart_pcount_cnt($(this), \'m\');" />
											</li>
										</ul>
									</td>
									<td align="right" class="Pgap_R8" style="width:101px;padding-left:20px;">
										<strong class="main_color" style="vertical-align:middle;margin-right:7px;" minicart_id="total_price">0</strong><img src="/data/daiso_data/templet/daiso/images/btns/btn_del02.gif" alt="삭제" align="absmiddle" style="cursor:pointer;" onclick="minicart_delete($(this).parent().parent().parent().parent().parent().parent());minicart_total();"/>
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
											<img src="/data/daiso_data/templet/daiso/images/btns/btn_del02.gif" alt="삭제" align="absmiddle" style="float:right;margin:2px 13px 0 0;cursor:pointer;"/>
										</td>
									</tr>
								</table>
							</div>
						</td>	
					</tr> 
					<tr><td colspan="3" style="height:1px;background:url(/data/daiso_data/templet/daiso/images/dot_b6b6b6.png) repeat-x;"></td></tr>';
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
//$Contents .= '<div style="text-align:center;padding:20px 0px;"><img src="../images/korea/b_save.gif" border="0" style="cursor:pointer;border:0px;" onclick="SelectGoodsOption(document.pinfo)"></div>';
$Contents .= '</form>'; 

$P = new ManagePopLayOut();
$P->addScript = "<script language='javascript' src='/admin/estimate/goods_option_select.js'></script><script language='javascript' src='/data/daiso_data/templet/daiso/js/goods_view.js'></script>";
$P->Navigation = "옵션미리보기";
$P->NaviTitle = "옵션미리보기";
$P->strContents = $Contents;
echo $P->PrintLayOut();


function getMakeOptionTmp($option_name, $pid, $opn_ix="", $option_kind="b", $return_type="select",$select_option_id=""){
	global $user, $shop_product_type;
	$mdb = new Database;
	
	$sql = "select
				p.id, p.listprice, p.sellprice 
			from 
				shop_product_temp p
			where 
				p.id = '".$pid."'";

	$mdb->query($sql);
	$mdb->fetch();

	$cid = $mdb->dt[cid];
	$depth = $mdb->dt[depth];
	$listprice = $mdb->dt[listprice];
	$sellprice = $mdb->dt[sellprice];

	if($_SESSION['layout_config']['mall_type'] == 'BW' && ($option_kind=="b" || $option_kind=="x" || $option_kind=="x2" || $option_kind=="s2" || $option_kind=="c")){
		$o_select_price = 'option_wholesale_listprice AS option_listprice, option_wholesale_price AS option_price';//불러오는 컬럼 추가,변경 kbk 13/06/17
	} else {
		$o_select_price = 'option_listprice, pod.option_price';//불러오는 컬럼 추가,변경 kbk 13/06/17
	}

	/*
	select 박스의 option 에 attribute로 l_price(정가) 추가 함 kbk 13/06/17
	*/
	if($option_kind == "x2" || $option_kind == "s2" || $option_kind == "c") {//셋트 옵션의 경우 노출 순서를 달리함 kbk 13/07/01
		$order_by_text=" set_group ASC, id ASC ";
	} else {
		$order_by_text=" id ASC ";
	}

	if($return_type=="select" || $return_type=="cart_select" || $return_type=="table" || $return_type=="info"){
		if($option_kind == "c"){
			$sql = "select
							'".$pid."' as pid,pod.id, pod.id as opnd_ix, 
							option_div, pod.set_group,  set_group_seq, option_etc1 as set_cnt, 
							".$o_select_price.", pod.option_coprice, pod.option_stock, 
							pod.option_sell_ing_cnt, pod.option_soldout, pod.option_etc1, pod.option_code, pod.option_barcode
						from 
							shop_product_options_detail_temp pod 
						where
							pod.pid = '$pid' 
							and pod.opn_ix ='$opn_ix' 
							order by $order_by_text ";

		}else{
			$sql = "select 
						'".$pid."' as pid, id, id as opnd_ix, 
						option_div, set_group,  set_group_seq, option_etc1 as set_cnt,
						".$o_select_price.", option_coprice, option_stock, option_sell_ing_cnt,
						option_soldout, option_etc1, set_group, option_code,option_barcode  
					from 
						shop_product_options_detail_temp pod 
					where
						pid = '$pid'
						and opn_ix ='$opn_ix' 
						order by $order_by_text ";
		}
	}else{
		$sql = "select 
					'".$pid."' as pid, pod.id, pod.id as opnd_ix, pod.option_div, 
					set_group,  set_group_seq, option_etc1 as set_cnt, ".$o_select_price.",
					option_coprice, b.option_name, pod.set_group, 
					pod.option_soldout, pod.option_etc1, option_code,option_barcode  
				from 
					shop_product_options_detail_temp pod ,
					shop_product_options_temp b 
				where 
					pod.pid = '".$pid."' 
					and pod.opn_ix = b.opn_ix ";
	}

	$mdb->query($sql);
	$options = $mdb->fetchall();

	
	if(count($options) > 0){

		$goods_infos[$pid][pid] = $pid;
		$goods_infos[$pid][amount] = 1;
		$goods_infos[$pid][cid] = $cid;
		$goods_infos[$pid][depth] = $depth;

		foreach ($options as $key => $sub_array) {
			$select_ = array("icons_list"=>explode(";",$sub_array[icons]));
			array_insert($sub_array,14,$select_);

			$_dcprice = $sub_array[option_price];

			$_dcprice = array("option_dcprice"=>$_dcprice);
			array_insert($sub_array,52,$_dcprice);
			$discount_desc = array("discount_desc"=>$discount_desc);
			array_insert($sub_array,53,$discount_desc);

			$options[$key] = $sub_array;
		}
	}

	if ($mdb->total == 0){
		return "";
	}else{
		if($return_type=="select" || $return_type=="cart_select"){
			if($option_kind == "b"){
				$mString = "<Select name=options[] id='_goods_options' opt_name='options_opn_ix_".$opn_ix."' class='goods_options' onchange=\"ChangeAddPriceOption('".$user[mem_level]."',$(this), this.selectedIndex,'".$option_kind."');\"  before_price=0 option_kind='".$option_kind."' title='$option_name' validation='".($return_type=="cart_select" ? "true":"true")."' style='width:95%;padding:3px;'>"; // 기본옵션이 왜 선택이었는지? 
				$mString .= "<option value='' stock='0' l_price='0' n_price='0' etc1='' >(필수)선택해주세요</option>";
				$i=0;
				for($i=0;$i < count($options); $i++){
				
					if(($options[$i][option_dcprice]-$sellprice) > 0) {
						$add_op_text="(+".number_format($options[$i][option_dcprice]-$sellprice)."원)";
					}else if(($options[$i][option_dcprice]-$sellprice) == 0) {
						$add_op_text="";	
					}else{
						$add_op_text="(".number_format($options[$i][option_dcprice]-$sellprice)."원)";
					}
					if($select_option_id == $options[$i][id]){

						$mString .= "<option value='".$options[$i][id]."' stock='".($options[$i][option_stock]-$options[$i][option_sell_ing_cnt])."' dc_price='".($options[$i][option_dcprice] > 0 ? ($options[$i][option_dcprice]-$sellprice):$options[$i][option_price])."' c_price='".$options[$i][option_coprice]."' l_price='".$options[$i][option_listprice]."' n_price='".($options[$i][option_dcprice])."' soldout='".($options[$i][option_soldout] == "1" || ($options[$i][option_stock]-$options[$i][option_sell_ing_cnt]) <= 0 ? "1":"0")."' etc1='".$options[$i][option_etc1]."' option_text='".$options[$i][option_div]."' selected>".$options[$i][option_div]."".$add_op_text."".($options[$i][option_soldout] == "1" || ($options[$i][option_stock]-$options[$i][option_sell_ing_cnt]) <= 0 ? "[품절]":"")."</option>\n";
					}else{

						$mString .= "<option value='".$options[$i][id]."' stock='".($options[$i][option_stock]-$options[$i][option_sell_ing_cnt])."' dc_price='".($options[$i][option_dcprice] > 0 ? ($options[$i][option_dcprice]-$sellprice):$options[$i][option_price])."' c_price='".$options[$i][option_coprice]."' l_price='".$options[$i][option_listprice]."' n_price='".($options[$i][option_dcprice])."' soldout='".($options[$i][option_soldout] == "1" || ($options[$i][option_stock]-$options[$i][option_sell_ing_cnt]) <= 0 ? "1":"0")."'  etc1='".$options[$i][option_etc1]."' option_text='".$options[$i][option_div]."' >".$options[$i][option_div]."".$add_op_text."".($options[$i][option_soldout] == "1" || ($options[$i][option_stock]-$options[$i][option_sell_ing_cnt]) <= 0 ? "[품절]":"")."</option>\n";
					}

				}
				$mString .= "</select>";
			}else if($option_kind == "p" || $option_kind == "c2" || $option_kind == "i2"){
				$mString = "<Select name=options[] id='_goods_options' opt='AddPriceOption' opt_name='options_opn_ix_".$opn_ix."' class='goods_options' onchange=\"ChangeAddPriceOption('".$user[mem_level]."',$(this), this.selectedIndex,'".$option_kind."');\"  befor_price=0 option_kind='".$option_kind."' title='$option_name' validation='false' style='width:95%;padding:3px;'>";
				$mString .= "<option value='' stock='0' l_price='0' n_price='0' etc1='' >(선택)선택해주세요</option>";
				$mString .= "<option value='0' stock='0' option_text='' l_price='0' n_price='0' etc1='' >선택하지 않음</option>";
				$i=0;
				for($i=0;$i < count($options); $i++){

					if($options[$i][option_dcprice]>0) {
						$add_op_text="(+".number_format($options[$i][option_dcprice])."원)";
					}
					if($select_option_id == $options[$i][id]){

						$mString .= "<option value='".$options[$i][id]."' stock='".($options[$i][option_stock]-$options[$i][option_sell_ing_cnt])."' dc_price='".($options[$i][option_dcprice] > 0 ? $options[$i][option_dcprice]:$options[$i][option_price])."'  l_price='".$options[$i][option_listprice]."' n_price='".$options[$i][option_price]."' soldout='".($options[$i][option_soldout] == "1" ? "1":"0")."' etc1='".$options[$i][option_etc1]."' option_text='".$options[$i][option_div]."' selected>".$options[$i][option_div].$add_op_text."".($options[$i][option_soldout] == "1" ? "[품절]":"")."</option>\n";
					}else{

						$mString .= "<option value='".$options[$i][id]."' stock='".($options[$i][option_stock]-$options[$i][option_sell_ing_cnt])."' dc_price='".($options[$i][option_dcprice] > 0 ? $options[$i][option_dcprice]:$options[$i][option_price])."'  l_price='".$options[$i][option_listprice]."' n_price='".$options[$i][option_price]."' soldout='".($options[$i][option_soldout] == "1" ? "1":"0")."'  etc1='".$options[$i][option_etc1]."' option_text='".$options[$i][option_div]."' >".$options[$i][option_div].$add_op_text."".($options[$i][option_soldout] == "1" ? "[품절]":"")."</option>\n";
					}
					unset($add_op_text);
				}
				$mString .= "</select>";
			}else if($option_kind == "s"  || $option_kind == "c1" || $option_kind == "i1"){
				$mString = "<Select name=options[] id='_goods_options'  title='$option_name' opt_name='options_opn_ix_".$opn_ix."' validation='".($return_type=="cart_select" ? "false":"true")."' style='width:95%;padding:3px;' class='goods_options' onchange=\"ChangeAddPriceOption('".$user[mem_level]."',$(this), this.selectedIndex,'".$option_kind."');\" option_kind='".$option_kind."'>";
				$mString .= "<option value='' stock='0' l_price='0' n_price='0' etc1='' >(필수)선택해주세요</option>";

				$i=0;
				for($i=0;$i < count($options); $i++){

					if($options[$i][option_dcprice]>0) {
						$add_op_text="(+".number_format($options[$i][option_dcprice])."원)";
					}

					if($select_option_id == $options[$i][id]){

						$mString .= "<option value='".$options[$i][id]."' stock='".($options[$i][option_stock]-$options[$i][option_sell_ing_cnt])."' dc_price='".($options[$i][option_dcprice] > 0 ? $options[$i][option_dcprice]:$options[$i][option_price])."'  l_price='".$options[$i][option_listprice]."' n_price='".$options[$i][option_price]."' soldout='".($options[$i][option_soldout] == "1" ? "1":"0")."'  etc1='".$options[$i][option_etc1]."' option_text='".$options[$i][option_div]."' selected>".$options[$i][option_div].$add_op_text."".($options[$i][option_soldout] == "1" ? "[품절]":"")."</option>\n";
					}else{

						$mString .= "<option value='".$options[$i][id]."' stock='".($options[$i][option_stock]-$options[$i][option_sell_ing_cnt])."' dc_price='".($options[$i][option_dcprice] > 0 ? $options[$i][option_dcprice]:$options[$i][option_price])."'  l_price='".$options[$i][option_listprice]."' n_price='".$options[$i][option_price]."' soldout='".($options[$i][option_soldout] == "1" ? "1":"0")."'  etc1='".$options[$i][option_etc1]."' option_text='".$options[$i][option_div]."' >".$options[$i][option_div].$add_op_text."".($options[$i][option_soldout] == "1" ? "[품절]":"")."</option>\n";
					}
					unset($add_op_text);
				}
				$mString .= "</select>";
			}else if($option_kind == "a"){
				$mString = "<Select name=options[] id='_goods_options'  title='$option_name' opt_name='options_opn_ix_".$opn_ix."' class='goods_options' validation='false' style='width:95%;padding:3px;' onchange=\"ChangeAddPriceOption('".$user[mem_level]."',$(this), this.selectedIndex,'".$option_kind."');\" option_kind='".$option_kind."'>";
				$mString .= "<option value='' stock='0' l_price='0' n_price='0' etc1='' >선택해주세요</option>";

				$i=0;
				for($i=0;$i < count($options); $i++){

					if($options[$i][option_dcprice]>0) {
						$add_op_text="(".number_format($options[$i][option_dcprice]).")";
					}

					if($select_option_id == $options[$i][id]){

						$mString .= "<option value='".$options[$i][id]."' stock='".($options[$i][option_stock]-$options[$i][option_sell_ing_cnt])."' dc_price='".($options[$i][option_dcprice] > 0 ? $options[$i][option_dcprice]:$options[$i][option_price])."'  l_price='".$options[$i][option_listprice]."' n_price='".$options[$i][option_price]."' soldout='".($options[$i][option_soldout] == "1" || ($options[$i][option_stock]-$options[$i][option_sell_ing_cnt]) < 0 ? "1":"0")."'  etc1='".$options[$i][option_etc1]."' option_text='".$options[$i][option_div]."'  selected>".$options[$i][option_div].$add_op_text."".($options[$i][option_soldout] == "1" || ($options[$i][option_stock]-$options[$i][option_sell_ing_cnt]) < 0 ? "[품절]":"")."</option>\n";
					}else{

						$mString .= "<option value='".$options[$i][id]."' stock='".($options[$i][option_stock]-$options[$i][option_sell_ing_cnt])."' dc_price='".($options[$i][option_dcprice] > 0 ? $options[$i][option_dcprice]:$options[$i][option_price])."'  l_price='".$options[$i][option_listprice]."' n_price='".$options[$i][option_price]."' soldout='".($options[$i][option_soldout] == "1" || ($options[$i][option_stock]-$options[$i][option_sell_ing_cnt]) < 0 ? "1":"0")."'  etc1='".$options[$i][option_etc1]."' option_text='".$options[$i][option_div]."' >".$options[$i][option_div].$add_op_text."".($options[$i][option_soldout] == "1" || ($options[$i][option_stock]-$options[$i][option_sell_ing_cnt]) < 0 ? "[품절]":"")."</option>\n";
					}
					unset($add_op_text);
				}
				$mString .= "</select>";
			}else if($option_kind == "r"){

				$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"][mall_data_root]."/images/product", $pid, 'Y');
				$mstring_images = "";

				$i=0;
				for($i=0;$i < count($options); $i++){

					$opnd_ix = $options[$i][id];

					if($i == 0){
						$option_etc1 = $options[$i][option_etc1];
					}

					$option_size = split(":",$options[$i][option_etc1]);

					$option_size_detail = explode("^",$option_size[1]);
					$option_size_detail_html .= "<div id='options_size_".$opn_ix."_".$opnd_ix."' class='option_relation_area' style='cursor:pointer;".($i == 0 ? "display:block;":"display:none;").";'>";
					for($j=0;$j < count($option_size_detail);$j++){
						$option_size_detail_html .= "<div style='float:left;border:1px solid silver;margin:0px 0px 2px 2px;padding:5px;' class='option_sizes' onclick=\"$('#option_size').val($(this).html());$('#options_text').val($('#option_color').val()+'-'+$('#option_size').val());$('.option_sizes').css('background-color','#ffffff');$(this).css('background-color','#efefef');\">".$option_size_detail[$j]."</div>";
					}
					$option_size_detail_html .= "</div>";

					if(file_exists($_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/images/product".$uploaddir."/options/options_detail_".$opnd_ix."_s.gif")){
						if(file_exists($_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/images/product".$uploaddir."/options/options_detail_".$opnd_ix."_b.gif")){
							$b_img_src = $_SESSION["layout_config"][mall_data_root]."/images/product".$uploaddir."/options/options_detail_".$opnd_ix."_b.gif";
						}else{
							$b_img_src = "";
						}

						$mstring_images .= "<img src='".$_SESSION["layout_config"][mall_data_root]."/images/product".$uploaddir."/options/options_detail_".$opnd_ix."_s.gif' class='option_colors' style='cursor:pointer;margin:2px 0px 0px 2px;border:2px solid #ffffff' title=\"".$options[$i][option_div]."\"  onclick=\"SelectRelationOption($(this), '".$opn_ix."', '".$opnd_ix."')\" b_img_src='".$b_img_src."' befor_price='0' n_price='".$options[$i][option_price]."' title='".$options[$i][option_div]."'>
						";
					}else{
						$mstring_images .= "<div class='option_colors' style='float:left;cursor:pointer;padding:4px;margin:1px;border:2px solid silver' title=\"".$options[$i][option_div]."\"  onclick=\"SelectRelationOption($(this), '".$opn_ix."', '".$opnd_ix."')\" >".$options[$i][option_div]."</div>";
					}
				}

				$mString = "<table width=100%>
									<tr><td style='padding-top:5px;'>".$mstring_images."</td></tr>
									<tr><td style='padding:10px 0px 0px 0px'>".$option_size_detail_html."</td></tr>
									<tr><td style='padding:10px 0px 0px 0px'><input type=text name=options_text id='options_text' value='' style='border:0px;width:100%;' readonly></td></tr>
									</table>
								<input type=hidden name=options[] id='select_option_color' value='' title='색상' validation='true'>
								<br>
								<input type=hidden name=option_color id='option_color' value=''><br>
								<input type=hidden name=option_size id='option_size' value='' title='세부옵션' validation='true'>
				<script javascript='language'>
					function SelectRelationOption(obj_id, opn_ix, opnd_ix){
						try{
							//alert(1);
							$('#option_size').val('');
							$('#option_color').val(obj_id.attr('title'));
							$('#options_text').val($('#option_color').val()+'-'+$('#option_size').val());
							$('.option_relation_area').css('display','none');
							$('#options_size_'+$opn_ix+'_'+ opnd_ix).css('display','block');
							if(obj_id.attr('b_img_src') != ''){
							//	alert(obj_id.attr('b_img_src'));
								$('#main_img').attr('src',obj_id.attr('b_img_src'));
							}

							$('#select_option_color').val(opnd_ix);

							$('.option_colors').css('border','2px solid silver');
							obj_id.css('border','2px solid #000000');
							ChangeRelationOption('".$user[mem_level]."',obj_id, '".$options[$i][id]."');
						}catch(e){
							//alert(e.message);
						}
					}
				</script>";
			}else if($option_kind == "c" || $return_type=="cart_select"){
				$mString = "<Select name=set_options[] id='_goods_optionsss'  minicart_id='opnd_ix' title='$option_name' opt_name='options_opn_ix_".$opn_ix."'  validation='".($return_type=="cart_select" ? "false":"true")."' style='width:95%;padding:3px;' class='codi_goods_options' option_kind='".$option_kind."'>"; 
				$mString .= "<option value='' stock='0' l_price='0' n_price='0' etc1='' >선택해주세요</option>";

				$i=0;
				for($i=0;$i < count($options); $i++){

					if($options[$i][option_dcprice]>0) {
						$add_op_text="(+".number_format($options[$i][option_dcprice]).")";
					}

					if($options[$i][select_option_id] == $options[$i][id]){

						$mString .= "<option value='".$options[$i][id]."' opn_ix='".$opn_ix."' stock='".($options[$i][option_stock]-$options[$i][option_sell_ing_cnt])."' dc_price='".($options[$i][option_dcprice] > 0 ? $options[$i][option_dcprice]:$options[$i][option_price])."' l_price='".$options[$i][option_listprice]."' n_price='".$options[$i][option_price]."' soldout='".($options[$i][option_soldout] == "1" || ($options[$i][option_stock]-$options[$i][option_sell_ing_cnt]) < 0 ? "1":"0")."' etc1='".$options[$i][option_etc1]."' cart_ix='".$options[$i][cart_ix]."'  option_text='".$options[$i][option_div]."'  selected>".$options[$i][option_div].$add_op_text."".($options[$i][option_soldout] == "1" || ($options[$i][option_stock]-$options[$i][option_sell_ing_cnt]) < 0 ? "[품절]":"")."</option>\n";
					}else{

						$mString .= "<option value='".$options[$i][id]."' opn_ix='".$opn_ix."' stock='".($options[$i][option_stock]-$options[$i][option_sell_ing_cnt])."' dc_price='".($options[$i][option_dcprice] > 0 ? $options[$i][option_dcprice]:$options[$i][option_price])."'  l_price='".$options[$i][option_listprice]."' n_price='".$options[$i][option_price]."' soldout='".($options[$i][option_soldout] == "1" || ($options[$i][option_stock]-$options[$i][option_sell_ing_cnt]) < 0 ? "1":"0")."'  etc1='".$options[$i][option_etc1]."' option_text='".$options[$i][option_div]."'  cart_ix='".$options[$i][cart_ix]."'>".$options[$i][option_div].$add_op_text."".($options[$i][option_soldout] == "1" || ($options[$i][option_stock]-$options[$i][option_sell_ing_cnt]) < 0 ? "[품절]":"")."</option>\n";
					}
					unset($add_op_text);
				}
				$mString .= "</select>";

			} else if($option_kind == "x" || $option_kind == "x2" || $option_kind == "s2" ){// || $option_kind == "a"

				return $options;
			}
		}else if($return_type=="table"){
			
			$mString = "<table cellpadding=6 width=360 id='table_options_area'>
								<col width='20px'>
								<col width='*'>
								<col width='160px'>";
				$mString .= "<tr> <td><input type=checkbox name='check_all' id='check_all'  onclick=\"if( $(this).attr('checked')){ $('table#table_options_area input.table_options').not(':disabled').attr('checked',true);}else{ $('table#table_options_area input.table_options').attr('checked',false);};\" align=absmiddle></td><td><label for='check_all'>전체 선택</label></td></tr>";


				for($i=0;$i < count($options); $i++){
					//$mdb->fetch($i);

					$option_stock_cnt=$options[$i][option_stock]-abs($options[$i][option_sell_ing_cnt]);//판매진행재고가 - 인 경우도 생기므로 abs(절대값)으로 변환시켜서 계산 kbk 13/06/28
					if($option_stock_cnt>0) {
						$option_stock_cnt_text=" : ".$option_stock_cnt."개";
					} else {
						$option_stock_cnt_text=" : 0개";
						$option_stock_cnt=0;
					}

					if($select_option_id == $options[$i][id]){
						$mString .= "<tr><td height=25><input type=checkbox name='option[]' class='table_options' id='table_option_".$options[$i][id]."' value='".$options[$i][id]."' stock='".$option_stock_cnt."' n_price='".$options[$i][option_price]."' etc1='".$options[$i][option_etc1]."' onclick=\"($('#select_option_id').val($(this).val()))\"/> </td><td> <label for='table_option_".$options[$i][id]."'>".$options[$i][option_div].$option_stock_cnt_text."</label></td>
						<td>
						<div class='btn_option_num'>
							<div class='input_box_border01'>
								<input type='text' name='option_pcount[]' id='option_pcount_".$options[$i][id]."' value=1 size=4 maxlength=3 onkeydown='onlyEditableNumber(this)' onkeyup='onlyEditableNumber(this);' >
							</div>
							<ul>
								<li><img src='".$_SESSION["layout_config"]["mall_templet_webpath"]."/images/up_arrow.gif' alt='' onclick=\"pcount_cnts($('#option_pcount_".$options[$i][id]."'),'p');\" style='cursor:pointer;'></li>
								<li><img src='".$_SESSION["layout_config"]["mall_templet_webpath"]."/images/down_arrow.gif' alt='' onclick=\"pcount_cnts($('#option_pcount_".$options[$i][id]."'),'m');\" style='cursor:pointer;'></li>
							</ul>
						</div>
						</td>
						</tr>\n";
					}else{
						$mString .= "<tr><td height=25 >
						<input type=checkbox name='option[]' class='table_options'  id='table_option_".$options[$i][id]."'  value='".$options[$i][id]."' stock='".$option_stock_cnt."' n_price='".$options[$i][option_price]."' etc1='".$options[$i][option_etc1]."' onclick=\"if( $(this).attr('stock') == 0){ $(this).attr('checked',false);alert('재고수량이 부족해서 구매하실수 없습니다.');}\" ".($option_stock_cnt == 0 ? "disabled":"")."/> </td><td> <label for='table_option_".$options[$i][id]."'>".$options[$i][option_div].$option_stock_cnt_text."</label></td>
						<td>
						<div class='btn_option_num'>
							<div class='input_box_border01'>
								<input type='text' name='option_pcount[]' id='option_pcount_".$options[$i][id]."' value=1 size=4 maxlength=3 onkeydown='onlyEditableNumber(this)' onkeyup='onlyEditableNumber(this);' >
							</div>
							<ul>
								<li><img src='".$_SESSION["layout_config"]["mall_templet_webpath"]."/images/up_arrow.gif' alt='' onclick=\"pcount_cnts($('#option_pcount_".$options[$i][id]."'),'p');\" style='cursor:pointer;'></li>
								<li><img src='".$_SESSION["layout_config"]["mall_templet_webpath"]."/images/down_arrow.gif' alt='' onclick=\"pcount_cnts($('#option_pcount_".$options[$i][id]."'),'m');\" style='cursor:pointer;'></li>
							</ul>
						</div>
						</td>
						</tr>\n";
					}

				}

				$mString .= "</table>";
		}else if($return_type=="info"){
			$mString = "<table cellpadding=6 width=360 id='table_options_area'> ";

				for($i=0;$i < count($options); $i++){
					//$mdb->fetch($i);

					$option_stock_cnt=$options[$i][option_stock]-abs($options[$i][option_sell_ing_cnt]);//판매진행재고가 - 인 경우도 생기므로 abs(절대값)으로 변환시켜서 계산 kbk 13/06/28
					if($option_stock_cnt>0) {
						$option_stock_cnt_text=" : ".$option_stock_cnt."개";
					} else {
						$option_stock_cnt_text=" : 0개";
						$option_stock_cnt=0;
					}

				$mString .= "<tr> <td>[".$option_name."]  ".$options[$i][option_div].$option_stock_cnt_text." </td> </tr>\n";
				}
	
				$mString .= "</table><br> ";
		}else{
			for($i=0;$i < count($options); $i++){
				//$mdb->fetch($i);

				if($select_option_id == $options[$i][id]){
					return $options[$i][option_name] ." : ".$options[$i][option_div];
				}
			}
		}
	}

	return $mString;
}


?>