<?php
include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("../class/layout.class");
require($_SERVER["DOCUMENT_ROOT"]."/include/barcode/php-barcode-0.4/php-barcode.php");

//$db = new Database;
//$db2 = new Database;
//$slave_db = new Database;

$goods_input_upadte_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U");	//업데이트 권한
$goods_input_delete_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D");	//삭제권한

if(!$menu_name){					//메뉴,타이틀명
	$menu_title = '상품리스트';
	$menu_name = "상품리스트";
}

$sql = "select            
			  config_value as front_url
			from
			  shop_mall_config
		    where
			mall_ix = '".$_SESSION['admininfo']['mall_ix']."'
			and config_name = 'front_url'";

$db->query($sql);
$db->fetch();
$front_url = $db->dt['front_url'];


if($_COOKIE[product_max_limit]){
	$max = $_COOKIE[product_max_limit]; //페이지당 갯수
}else{
	$max = 20;
}

include ("../product/product_query.php");			//상품노출 쿼리 실행 페이지
include ("../product/product_list_search.php");		//검색폼 html 페이지

$innerview .= "	
	<table border=0 cellpadding=0 cellspacing=0 width='100%'>
	<tr>
		<td valign=top>
		</td>
		<td valign=top style='padding:0px;padding-top:0px;' id=product_list>
		
			<form name=listform method=post action='product_list.act.php' onsubmit='return false' target='iframe_act'>
			<input type='hidden' name='search_searialize_value' value='".($_GET[mode] == "search" || $_GET[mode] == "excel_search" ? urlencode(serialize($_GET)):"")."'>
			<input type='hidden' name='act' value=''>
			<input type='hidden' name='cid2' value='$cid2'>
			<input type='hidden' name='depth' value='$depth'>
			<input type='hidden' name='product_type' value='$product_type'>
			<input type='hidden' name='max' value='$max'>
			<input type='hidden' name='info_type' value='".$excel_updown_type."'>
			<input type='hidden' name='favorites_excel_idx' value=''>

			<table width='100%' cellpadding=3 cellspacing=0 border=0>
			<col width=10%>
			<col width=55%>
			<col width=*>
			<col width=10%>
			<col width=7%>
			<col width=8%>
			<tr>
				<td>상품수 : ".number_format($total)." 개</td>
				<td align=left height=30>";

	$innerview1 .= "
					<table>
					<tr>
						<td>";

	if($goods_input_delete_auth && $delete_auth){
		$innerview1 .= "<a href=\"JavaScript:SelectDelete(document.forms['listform']);\"><img  src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0 align=absmiddle ></a>";
	}

$sql = "select * from shop_product_favorites_excel_info ";
$db->query($sql);
$favorites_data = $db->fetchall();
$favorites_options = "";
if(is_array($favorites_data)){
    foreach($favorites_data as $key=>$val){
        $favorites_options .="<option value='".$val['idx']."'>".$val['title']."</option>";
    }
}

	$innerview1 .= "
						</td>
						<td>
							<b class=small>판매순</b>
						</td>
						<td>
							<a href='product_list.php?orderby=order_cnt&ordertype=desc".$search_query."' target='act'><img src='../image/orderby_desc_".($orderby == "order_cnt" && $ordertype ==  "desc" ? "on":"off").".gif' border=0 align=absmiddle title='높은판매수순'></a>
						</td>
						<td>
							<a href='product_list.php?orderby=order_cnt&ordertype=asc".$search_query."' target='act'><img src='../image/orderby_asc_".($orderby == "order_cnt" && $ordertype ==  "asc" ? "on":"off").".gif' border=0 align=absmiddle title='낮은판매수순'></a>
						</td>
						<td>
							|
						</td>
						<td>
							<b class=small>조회순</b>
						</td>
						<td>
							<a href='product_list.php?orderby=view_cnt&ordertype=desc".$search_query."' target='act'><img src='../image/orderby_desc_".($orderby == "view_cnt" && $ordertype ==  "desc" ? "on":"off").".gif' border=0 align=absmiddle title='높은조회수순'></a>
						</td>
						<td>
							<a href='product_list.php?orderby=view_cnt&ordertype=asc".$search_query."' target='act'><img src='../image/orderby_asc_".($orderby == "view_cnt" && $ordertype ==  "asc" ? "on":"off").".gif' border=0 align=absmiddle title='낮은조회수순'></a>
						</td>
						</tr>
					</table>";

	$innerview .= "
				</td>				
				<td align=right>
					<select name='favorites_excel' id='favorites_excel'> 
						<option value=''>기본</option>
						".$favorites_options."
					</select>
				</td>
				<td align=right>
					<select name='update_type'>
						<option value='2' selected>선택한 상품 전체에</option>
						<!--<option value='1'>검색한 상품 전체에</option>-->
					</select>
				</td>
				<td align=right>";
/*
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
		$innerview .= "<span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'>
		<a href='excel_config.php?".$QUERY_STRING."&info_type=found_info&excel_type=found_info_excel' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a></span>";
	}else{
		$innerview .= "
		<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
	}
*/

	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
		//$innerview .= " <a href='product_list.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		$innerview .= "<input type='image' src='../images/".$admininfo["language"]."/btn_excel_save.gif' style='cursor:pointer;' onclick='excel_down_submit(document.listform)'>";
	}else{
		$innerview .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
	}

$innerview .= "
				</td>				
				<td align=right>
				목록수 : <select name='max' id='max'>
							<option value='5' ".($_COOKIE[product_max_limit] == '5'?'selected':'').">5</option>
							<option value='10' ".($_COOKIE[product_max_limit] == '10'?'selected':'').">10</option>
							<option value='20' ".($_COOKIE[product_max_limit] == '20'?'selected':'').">20</option>
							<option value='30' ".($_COOKIE[product_max_limit] == '30'?'selected':'').">30</option>
							<option value='50' ".($_COOKIE[product_max_limit] == '50'?'selected':'').">50</option>
							<option value='100' ".($_COOKIE[product_max_limit] == '100'?'selected':'').">100</option>
							<option value='500' ".($_COOKIE[product_max_limit] == '500'?'selected':'').">500</option>
							<option value='1000' ".($_COOKIE[product_max_limit] == '1000'?'selected':'').">1000</option>
							<!--<option value='2000' ".($_COOKIE[product_max_limit] == '2000'?'selected':'').">2000</option>-->
						</select>
				</td>
			</tr>
			</table>

			<table cellpadding=2 cellspacing=0 bgcolor=gray border=0 width=100% class='list_table_box'>
			<tr bgcolor='#ffffff' align=center height=30>
				<td width='5%' class=s_td><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
				<!--td width='10%' class=m_td>상품코드</td-->
				<td width='10%' class=m_td>이미지<span style='padding-left:2px' class='helpcloud' help_width='220' help_height='30' help_html='한번에 클릭을 통해 상품의 판매여부와 노출여부를 설정할 수 있습니다.'><img src='/admin/images/icon_q.gif' /></span></td>
				<td width='*' class=m_td>".OrderByLink("상품정보", "pname", $ordertype)." </td>
				<td width='10%' class=m_td>".OrderByLink("가격", "sellprice", $ordertype)."</td>
				<!--td width='10%' class=m_td></td-->
				<td width='18%' class=e_td>관리/".OrderByLink("등록일자", "regdate", $ordertype)."/".OrderByLink("수정일자", "editdate", $ordertype)."</td>
			</tr>";

if(count($goods_datas) == 0){//$slave_db->total
	if($mode=="search"){
		$innerview .= "<tr bgcolor=#ffffff height=150><td colspan=5 align=center> 등록된 상품이 없습니다.</td></tr>";
	}else{
		$innerview .= "<tr bgcolor=#ffffff height=150><td colspan=5 align=center> 검색후 상품확인 가능합니다.</td></tr>";
	}
}else{
	for ($i = 0; $i < count($goods_datas); $i++){

		//$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $goods_datas[$i][id], "ms", $goods_datas[$i]);
		$img_str = PrintImage($admin_config[mall_data_root]."/images/addimgNew", $goods_datas[$i][id], "slist", $goods_datas[$i]);

		$sql = "select dt_ix from shop_product_delivery where pid = '".$goods_datas[$i][id]."' and is_wholesale = 'R' order by delivery_div limit 0,1";
		$slave_db->query($sql);
		$slave_db->fetch();
		$dt_ix = $slave_db->dt[dt_ix];

		if($layout_config[mall_use_inventory] == "Y" && $admininfo["mall_type"] != "O"){
			$slave_db->query("select h.pi_ix,h.place_name from inventory_place_info h , shop_product p where h.pi_ix = p.inventory_info and id = '".$db->dt[id]."' ");
			if($slave_db->total){
				$slave_db->fetch();
				$i_ix = $slave_db->dt[i_ix];
				$inventory_name = $slave_db->dt[inventory_name];
			}else{
				$i_ix = "";
				$inventory_name = "미등록";
			}
		}
        $md_name = "";
		if($goods_datas[$i][md_code]){
			$sql = "select AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as md_name from ".TBL_COMMON_MEMBER_DETAIL." where code = '".$goods_datas[$i][md_code]."' ";
			$slave_db->query($sql);
			if($slave_db->total){
				$slave_db->fetch();
                $md_name = $slave_db->dt['md_name'];
			}
		}


$innerview .= "	<tr bgcolor='#ffffff'>
					<td class='list_box_td list_bg_gray' bgcolor='#efefef' align=center>
						<input type=checkbox class=nonborder id='cpid' name=cpid[] value='".$goods_datas[$i][id]."' state='".$goods_datas[$i][state]."'>
					</td>
					<td class='list_box_td' align=center style='padding:10px 0px;'>";
					if($admininfo[mall_use_multishop]){
						$innerview .= "<span style='cursor:pointer;' class='helpcloud' help_width='220' help_height='70' help_html='".get_state_info($goods_datas[$i][id],$goods_datas[$i][state])."'>";
						if($goods_datas[$i][state] == 1){
							$innerview .= "<div id='state_txt_".$goods_datas[$i][id]."'><a href='product_list.act.php?act=state_update&pid=".$goods_datas[$i][id]."&state=".$goods_datas[$i][state]."'   target='iframe_act'><img src='../images/".$admininfo["language"]."/btn_sell.gif' align=absmiddle></a></div>";
						}else if($goods_datas[$i][state] == 6){
							$innerview .= "	<span style='color:red;font-weight:bold;'>[등록신청중]</span>";
						}else if($goods_datas[$i][state] == 8){
							$innerview .= "	<span style='color:red;font-weight:bold;'>[승인거부]</span>";
						}else if($goods_datas[$i][state] == 0){
							$innerview .= "<div id='state_txt_".$goods_datas[$i][id]."'><a href='product_list.act.php?act=state_update&pid=".$goods_datas[$i][id]."&state=".$goods_datas[$i][state]."'   target='iframe_act'><img src='../images/".$admininfo["language"]."/btn_sold_out.gif' align=absmiddle></a></div>";
						}
						$innerview .= "</span> 
						<div style='padding:2px;'></div>
						";

						if($goods_datas[$i][disp] == 1){
							$innerview .= "<div id='disp_txt_".$goods_datas[$i][id]."'><a href='product_list.act.php?act=disp_update&pid=".$goods_datas[$i][id]."&disp=".$goods_datas[$i][disp]."'  target='iframe_act'><img src='../images/".$admininfo["language"]."/btn_off_view.gif' align=absmiddle></a></div>";
						}else if($goods_datas[$i][disp] == 0){
							$innerview .= "<div id='disp_txt_".$goods_datas[$i][id]."'><a href='product_list.act.php?act=disp_update&pid=".$goods_datas[$i][id]."&disp=".$goods_datas[$i][disp]."'   target='iframe_act'><img src='../images/".$admininfo["language"]."/btn_on_view.gif' align=absmiddle></a></div>";
						}
						
					}

	$innerview .= "<br><a href='".$front_url."/shop/goodsView/".$goods_datas[$i][id]."' target='_blank' class='screenshot'  rel='".PrintImage($admin_config[mall_data_root]."/images/product", $goods_datas[$i][id], $LargeImageSize, $goods_datas[$i])."'><img src='".$img_str."' width=50 height=50></a><br><div style='padding-top:5px;'>".$goods_datas[$i][etc8]."</div>";
	$innerview .= "<div style='padding:5px;'>";
	$innerview .= "	<div style='padding-top:4px;'>조회수 : ".$goods_datas[$i][view_cnt]."</div>";
	$innerview .= "	<div style='padding-top:4px;'>판매수 : ".$goods_datas[$i][order_cnt]." 개</div>";
	$innerview .= "	<div style='padding-top:4px;'>판매율 : ".($goods_datas[$i][view_cnt] > 0?round($goods_datas[$i][order_cnt]/$goods_datas[$i][view_cnt] * 100,2):'0')." %</div>";
	$innerview .= "</div'>";

	if($_SESSION["admininfo"]["charger_id"] == "forbiz" && false){
	$innerview .= "<img src='/include/barcode/php-barcode-0.4/barcode.php?code=00".$goods_datas[$i][id]."&encoding=EAN&scale=1&mode=png' style='margin:10px 0px 0px 0px'>";
	}

	$innerview .= "
					</td>
					<td class='list_box_td list_bg_point' style='padding:6px 0px;' valign='top'>
						<table cellpadding=0 cellspacing=0 width='100%' border=0 >
						<tr>
							<td style='padding:10px 0px 0px 10px'>
								<table cellpadding=0 cellspacing=0 width='100%' border=0>
								<col width=65%>
								<col width=15%>
								<col width=*>
								<tr>
									<td align='left'>
										<!--img src='../images/".$admininfo["language"]."/benefit_icon03.gif' align=absmiddle style='cursor:pointer;' title='회원할인' alt='회원할인'>
										<img src='../images/".$admininfo["language"]."/benefit_icon02.gif' align=absmiddle style='cursor:pointer;' title='쿠폰할인' alt='쿠폰할인'>
										<img src='../images/".$admininfo["language"]."/benefit_icon04.gif' align=absmiddle style='cursor:pointer;' title='기획/이벤트' alt='기획/이벤트'>
										<img src='../images/".$admininfo["language"]."/benefit_icon06.gif' align=absmiddle style='cursor:pointer;' title='특가할인' alt='특가할인'-->
									</td>
									<td align='center'>
											<!--
											<img src='../images/".$admininfo["language"]."/sms_icon.gif' align=absmiddle style='cursor:pointer;' onclick=\"PoPWindow('../sms.pop.php?code=".$goods_datas[$i][charge_code]."',500,380,'sendsms')\" title='문자' alt='문자'>
											<img src='../images/".$admininfo["language"]."/mail_icon.gif' align=absmiddle style='cursor:pointer;' onclick=\"PoPWindow('../mail.pop.php?code=".$goods_datas[$i][charge_code]."',550,535,'sendmail')\" title='이메일' alt='이메일'>
											<img src='../images/".$admininfo["language"]."/customer_icon.gif' align=absmiddle style='cursor:pointer;' onClick=\"PopSWindow('../seller/found_detail.php?mmode=pop&company_id=".$goods_datas[$i][company_id]."&mmode=pop&type=C',900,710,'member_info')\"  title='문의' alt='문의'>
											-->
									</td>
									<td align='left'>
										<a href='goods_input.php?id=".$goods_datas[$i][id]."' target='_blank'>
											<img src='../images/".$admininfo["language"]."/btn_edit_new.gif' align=absmiddle style='cursor:pointer;' title='상세수정' alt='상세수정'>
										</a>
									</td>
								</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style='padding:0px 10px 10px 10px' > 
								<table border=0 cellpadding=3 cellspacing=0 width='100%' >
									<col width=16%>
									<col width=4%>
									<col width=30%>
									<col width=18%>
									<col width=4%>
									<col width='*'>
								<tr height=17>
									<td style='text-align:left;' colspan=6><b>".getCategoryPathByAdmin($goods_datas[$i][cid], 4)."</b></td>
								</tr>
								<tr height=17> 
									<td align='left' colspan='6' style='font-weight:bold;'>
										<a href='goods_input.php?id=".$goods_datas[$i][id]."' target='_self' style='color:#0054FF;'>
										<span style='cursor:pointer;' class='helpcloud' help_width='220' help_height='70' help_html='".GET_SELLER_INFO($goods_datas[$i][admin])."'>[".$goods_datas[$i][com_name]."]</span>
											".$goods_datas[$i][pname]."
										</a>
									</td>
																
								</tr>
								<tr height=17>
									<td align=left ><b>판매상태</b></td>
									<td> : </td>
									<td align=left class=red>
									<b>".$_SELL_STATUS[$goods_datas[$i][state]]."<b> 	 ".($goods_datas[$i][state_msg] != "" ? "-".$goods_datas[$i][state_msg]:"")."					
									</td> 
									<td align=left nowrap>색상</td>
									<td> : </td>
									<td align='left' colspan='3'>".$goods_datas[$i][add_info]."</td>
								</tr> 
								<tr height=15>
									<td align=left >상품코드</td>
									<td> : </td>
									<td align='left'>".$goods_datas[$i][pcode]."</td>
									<td align=left nowrap>상품시스템코드</td>
									<td> : </td>
									<td align='left' colspan='3'>".$goods_datas[$i][id]."</td>
								</tr>
								
								<tr height=17>
									<td align=left >판매기간</td>
									<td> : </td>
									<td align=left colspan='3'>
									".($goods_datas[$i][is_sell_date] == '1'?$goods_datas[$i][sell_priod_sdate].' ~ '.$goods_datas[$i][sell_priod_edate]:'미적용')."
									
									</td>	
								</tr> 
								<tr height=17 style='display: none;'>
									<td align=left >옵션/수량</td>
									<td> : </td>
									<td  align=left>";
									if($goods_datas[$i][stock_use_yn] == "N"){
								$innerview .= "사용안함";
									}else if($goods_datas[$i][stock_use_yn] == "Q"){
								$innerview .= "빠른재고사용";
									}else if($goods_datas[$i][stock_use_yn] == "Y"){
								$innerview .= "WMS 사용";
									}
					$innerview .= "</td>
									<td align=left >재고수량</td>
									<td> : </td>
									<td align=left>".$goods_datas[$i][stock]."</td>
								</tr>
								<tr height=17>
									<td align=left >배송정책</td>
									<td> : </td>
									<td colspan='4' align=left>".(product_list_policy_text($dt_ix) == ''?'<b>미지정</b>':product_list_policy_text($dt_ix))."</td>
								</tr>
								<tr height=17 style='display: none;'>
									<td align=left >브랜드</td>
									<td> : </td>
									<td align=left>".($goods_datas[$i][brand_name] == '' ? '미지정':$goods_datas[$i][brand_name])."</td>

									<td align=left >원산지</td>
									<td> : </td>
									<td align=left>".($goods_datas[$i][origin] == '' ? '미지정':$goods_datas[$i][origin])."</td>
								</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td align=left style='padding:0px 0px 0px 5px;'>";

			if($goods_info[product_type] == 1){
				if($goods_info[bs_goods_url]){
					if(substr($goods_info[bs_goods_url],0,5) !="http:")		$goods_info[bs_goods_url]= "http://".$goods_info[bs_goods_url];
					$innerview .= "<a href='".$goods_info[bs_goods_url]."' class=small target=_blank><b class=blu><img src='../images/".$admininfo["language"]."/btn_buy_agency.gif' align=absmiddle style='padding:5px 0;'></b></a>";
				}
			}

			$innerview .= "
							".($goods_datas[$i]["new"] == 1 ? "<img src='".$admin_config[mall_data_root]."/images/icon/icon_new.gif' border=0 align=absmiddle>":"")."
							".($goods_datas[$i]["hot"] == 1 ? "<img src='".$admin_config[mall_data_root]."/images/icon/icon_hot.gif' border=0 align=absmiddle>":"")."
							".($goods_datas[$i]["event"] == 1 ? "<img src='".$admin_config[mall_data_root]."/images/icon/icon_event.gif' border=0 align=absmiddle>":"")."
							</td>
						</tr>
						</table>
					</td>
					<td class='list_box_td list_bg_gray' bgcolor='#efefef' align=left style='padding:5px;' valign='middle'>
						<table cellpadding=3 cellspacing=0 align=center border='0' style=' padding-top:2px;'>
							<tr>
								<td align=left style='padding:0px 0px 0px 5px' nowrap>공급가 </td>
								<td>:</td>
								<td colspan='4' align='left'>".$currency_display[$admin_config["currency_unit"]]["front"]."
									<input type=text class=textbox2 size=10 id='coprice".$goods_datas[$i][id]."' name='coprice".$goods_datas[$i][id]."' style='text-align:right; padding-right:3px;' value='".number_format($goods_datas[$i][coprice],0)."' readonly>
								".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
							</tr>";
if($admininfo[admin_level] == '9' && false){
$innerview .= "
							<tr>
								<td align=left style='padding:0px 0px 0px 5px'>도매가 / 할인가</td>
								<td>:</td>
								<td nowrap align=left colspan='4' align='left'> ".$currency_display[$admin_config["currency_unit"]]["front"]." 
									<input type=text class=textbox2 size=10 id='wholesale_price".$goods_datas[$i][id]."' name='wholesale_price".$goods_datas[$i][id]."' style='text-align:right; padding-right:3px;' value='".number_format($goods_datas[$i][wholesale_price],0)."' readonly>
									/ 
									<input type=text class=textbox2 size=10 id='wholesale_sellprice".$goods_datas[$i][id]."' name='wholesale_sellprice".$goods_datas[$i][id]."' style='text-align:right; padding-right:3px;' value='".number_format($goods_datas[$i][wholesale_sellprice],0)."' readonly> 
								".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
							</tr>";
}
$innerview .= "
							<tr>
								<td align=left style='padding:0px 0px 0px 5px'>소매가 </td>
								<td>:</td>
								<td nowrap align=left colspan='4' align='left'> ".$currency_display[$admin_config["currency_unit"]]["front"]." 
								<input type=text class=textbox2 size=10 id='listprice".$goods_datas[$i][id]."' name='listprice".$goods_datas[$i][id]."' style='text-align:right; padding-right:3px;' value='".number_format($goods_datas[$i][listprice],0)."' readonly>								 
								".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
							</tr>
							
							<tr>
								<td align=left style='padding:0px 0px 0px 5px'>할인가</td>
								<td>:</td>
								<td nowrap align=left colspan='4' align='left'> ".$currency_display[$admin_config["currency_unit"]]["front"]."
								<input type=text class=textbox2 size=10 id='sellprice".$goods_datas[$i][id]."' name='sellprice".$goods_datas[$i][id]."' style='text-align:right; padding-right:3px;' value='".number_format($goods_datas[$i][sellprice],0)."' readonly> 
								".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
							</tr>
							<!--
							<tr>
								<td align=left style='padding:0px 0px 0px 5px'>소매 수수료 </td>
								<td>:</td>
								<td align=left><b>".getProductCategoryRate($goods_datas[$i][id],$goods_datas[$i][cid],'R')." %</b></td>

								<td align=left style='padding:0px 0px 0px 5px'>소매 적립금 </td>
								<td>:</td>
								<td align=left><b> 
								".($goods_datas[$i][reserve_yn] == 'Y'?$goods_datas[$i][reserve].' 원':$reserve_data[goods_mileage_rate].' %')."
								</b></td>
							</tr>
							-->
							";

if($admininfo[mall_use_multishop] && $admininfo[admin_level] == '9' && false){
$innerview .= "
							<tr>
								<td align=left style='padding:0px 0px 0px 5px'>도매 수수료</td>
								<td>:</td>
								<td align=left><b>".getProductCategoryRate($goods_datas[$i][id],$goods_datas[$i][cid],'W')." %</b>

								<td align=left style='padding:0px 0px 0px 5px'>도매 적립금 </td>
								<td>:</td>
								<td align=left><b>
								".($goods_datas[$i][wholesale_reserve_yn] == 'Y'?$goods_datas[$i][wholesale_reserve].' 원':$whole_reserve_data[goods_mileage_rate].' %')."
								</b></td>
							</tr>
						";
}

if($admininfo[mall_type] == "BW"){
$innerview .= "				<tr>
								<td align=left style='padding:0px 0px 0px 5px'>도매 정가 </td>
								<td>:</td>
								<td nowrap colspan='4' align='left'> ".$currency_display[$admin_config["currency_unit"]]["front"]." <input type=text class=textbox2 size=10 id='wholesale_price".$goods_datas[$i][id]."' name='wholesale_price".$goods_datas[$i][id]."' style='text-align:right; padding-right:3px;' value='".number_format($goods_datas[$i][wholesale_price],0)."'> ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
							</tr>
							<tr>
								<td align=left style='padding:0px 0px 0px 5px'>도매 판매가(할인가) </td>
								<td>:</td>
								<td nowrap colspan='4' align='left'> ".$currency_display[$admin_config["currency_unit"]]["front"]." <input type=text class=textbox2 size=10 id='wholesale_sellprice".$goods_datas[$i][id]."' name='wholesale_sellprice".$goods_datas[$i][id]."' style='text-align:right; padding-right:3px;' value='".number_format($goods_datas[$i][wholesale_sellprice],0)."'> ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
							</tr>";

$innerview .= "
							<tr>
								<td align=left style='padding:0px 0px 0px 5px'>소매 권장정가 </td>
								<td>:</td>
								<td colspan='4' align='left'> ".$currency_display[$admin_config["currency_unit"]]["front"]." <input type=text class=textbox2 size=10 id='listprice".$goods_datas[$i][id]."' name='listprice".$goods_datas[$i][id]."' style='text-align:right; padding-right:3px;' value='".number_format($goods_datas[$i][listprice],0)."'> ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
							</tr>
							<tr>
								<td align=left style='padding:0px 0px 0px 5px'>소매 권장판매가(할인가) </td>
								<td>:</td>
								<td colspan='4' align='left'> ".$currency_display[$admin_config["currency_unit"]]["front"]." <input type=text class=textbox2 size=10 id='sellprice".$goods_datas[$i][id]."' name='sellprice".$goods_datas[$i][id]."' style='text-align:right; padding-right:3px;' value='".number_format($goods_datas[$i][sellprice],0)."'> ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
							</tr>";
}

$innerview .= "
							<!--
							<tr>
								<td align=left style='padding:0px 0px 0px 5px'>후기수</td>
								<td>:</td>
								<td align=left ><b>".number_format($goods_datas[$i][after_cnt])." </b>
							</tr>
							-->
							<!--
							<tr>
								<td align=left style='padding:0px 0px 0px 5px'>상품평가</td>
								<td>:</td>
								<td align=left colspan='4'><b>*****  98%</b></td>
							</tr>-->
						</table>
					</td>

					<td class='list_box_td' style='padding:10px;' nowrap valign='middle'>
						<table align=center style='padding:10px;text-align:left;' border=0>
							<tr>
								<td>
									등록일자 : ".$goods_datas[$i][regdate]."<br><br>
									최종수정일자 : ".$goods_datas[$i][editdate]."<br><br> 
									상품 MD : ".($md_name != ""?$md_name:'미지정')."<br><br> 
								</td>
							</tr>
							<tr>
								<td>
								<input type='hidden' id='h_pname_".$goods_datas[$i][id]."' name='h_pname_".$goods_datas[$i][id]."' value=\"".$goods_datas[$i][pname]."\" />";
if($goods_input_upadte_auth){
					$innerview .= "<a href='goods_input.php?mode=copy&id=".$goods_datas[$i][id]."'><img src='../images/".$admininfo["language"]."/btc_copy.gif' border=0 align=absmiddle ></a>&nbsp;";
}else{
					$innerview .= "<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btc_copy.gif' border=0 align=absmiddle title=\" ' ".$goods_datas[$i][pname]." '  에 대한 정보를 수정합니다.\"></a>&nbsp;";
}

if($goods_input_upadte_auth){
					//$innerview .= "<img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle title=\" ' ".$goods_datas[$i][pname]." '  에 대한 정보를 수정합니다.\" onclick=\"CopyData('listform', '".$goods_datas[$i][id]."');\" style='cursor:pointer;'>&nbsp;";
}else{
					//$innerview .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle title=\" ' ".$goods_datas[$i][pname]." '  에 대한	정보를 수정합니다.\"></a>&nbsp;";
}
//if($goods_input_delete_auth && $admininfo[admin_level] == '9'){
if($goods_input_delete_auth && ($goods_datas[$i][state]!='1' && $goods_datas[$i][state]!='0')){
					$innerview .= "<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle style='cursor:pointer' border=0 onclick=\"deleteProduct('delete','".$goods_datas[$i][id]."','&cid=$cid&depth=$depth')\" title='삭제'>&nbsp;";
}
$innerview .= "					</td>
							</tr>
							<tr>
								<td>";
if($admininfo[admin_level] == '9'){
/*$innerview .= "					<img src='../images/".$admininfo["language"]."/company_alum.gif' border=0 align=absmiddle style='cursor:pointer' border=0 onclick=\"PoPWindow('product_notice.php?pid=".$goods_datas[$i][id]."&mmode=pop',800,700,'product_notice')\" title='본사알림게시판'>&nbsp;
								<!--img src='../images/".$admininfo["language"]."/write_bbs.gif' border=0 align=absmiddle style='cursor:pointer' border=0 onclick=\"PoPWindow('after_pop.php?pid=".$goods_datas[$i][id]."',850,500,'after_pop')\" title='상품평쓰기'>&nbsp;-->";*/
}

$innerview .= "
								</td>
							</tr>
						</table><br>
					";

	$innerview .= "</td>
				</tr>";

	}

	$script_time[loop_end] = time();

}
$innerview .= "</table>";

$innerview .= "
				<table width='100%'>
				<tr>
					<td height=30>";


if($goods_input_delete_auth){
	//일괄정보 삭제 버튼 하단 2014-04-10 이학봉 주석
	$innerview .= "<a href=\"JavaScript:SelectDelete(document.forms['listform']);\"><img  src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0 align=absmiddle ></a> ";
}
if($goods_input_upadte_auth){
	//$innerview .= "<a href=\"JavaScript:GoodsSelectUpdate(document.forms['listform']);\"><img src='../images/".$admininfo["language"]."/bt_all_modify.gif' border=0 align=absmiddle style='cursor:pointer;'></a> ";
}
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
	//$innerview .= "<a href='product_list_excel2003.php?".$_SERVER["QUERY_STRING"]."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 align=absmiddle ></a>";
}else{
	//$innerview .= "<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 align=absmiddle ></a>";
}


$innerview .= "
					</td>
					<td align=right>".$str_page_bar."</td>
				</tr>
				</table>
				</form>
				";

$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
		<form name=saveform method=get action='./product_list.act.php' target='iframe_act'>
			<input type='hidden' name='act' value='update_one'>
			<input type='hidden' name='pid'>
			<input type='hidden' name='pcode'>
			<input type='hidden' name='disp'>
			<input type='hidden' name='reserve'>
			<input type='hidden' name='reserve_rate'>
			<input type='hidden' name='search_keyword'>
			<input type='hidden' name='wholesale_price'>
			<input type='hidden' name='wholesale_sellprice'>
			<input type='hidden' name='coprice'>
			<input type='hidden' name='sellprice'>
			<input type='hidden' name='listprice'>
			<input type='hidden' name='state'>
		</form>";

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B',$admininfo, 'admininfo');

$Contents .= HelpBox("상품 리스트", $help_text,'100');

$Script = "
		<script language='javascript' src='../include/DateSelect.js'></script>\n
		<Script Language='JavaScript' src='/admin/js/autocomplete.js'></Script>\n
		<script Language='JavaScript' src='../include/zoom.js'></script>\n
		<script Language='JavaScript' src='../product/product_input.js'></script>
		<script Language='JavaScript' src='../product/product_list.js'></script>
		<script src='../js/scriptaculous.js' type='text/javascript'></script>
		<script language='javascript' src='/admin/js/jquery.form.js'></script>
		<script language='javascript' src='/admin/js/jquery.form.min.js'></script>
		<script id='dynamic'></script>";

$Script .= "
<script language='javascript'>

function ChangeRegistDate(frm){
	if(frm.regdate.checked){
		frm.sdate.disabled = false;
		frm.edate.disabled = false;
	}else{
		frm.sdate.disabled = true;
		frm.edate.disabled = true;
	}
}

function init(){
//alert(1);
	var frm = document.search_form;
//	onLoad('$sDate','$eDate');";

if($regdate != "1"){
	$Script .= "
	frm.sdate.disabled = true;
	frm.edate.disabled = true;";
}

$Script .= "
}

$(document).ready(function (){

	$('select[id=max]').change(function(){
		var value= $(this).val();
		$.cookie('product_max_limit', value, {expires:1,domain:document.domain, path:'/', secure:0});
		document.location.reload();
	});

});
</script>";

$script_time[end] = time();


if($view == "innerview"){

	$pageging_info["product_list.php"]["page"] = $page;
	$pageging_info["product_list.php"]["nset"] = $nset;

	session_register("pageging_info");

	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>

<body>$innerview</body></html>";
	$inner_category_path = getCategoryPathByAdmin($cid2, $depth);
	echo "
	<Script>
	parent.document.getElementById('product_list').innerHTML = document.body.innerHTML;
	parent.document.getElementById('select_category_path1').innerHTML=\"".($search_text == "" ? $inner_category_path."(".$total."개)":"<b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."\" ;
	//parent.document.search_form.cid.value ='$cid';
	parent.document.search_form.depth.value ='$depth';
	parent.LargeImageView();
	parent.unblockLoadingBox();
	</Script>";

}else{

	$P = new LayOut();
	if($page_type == "sellertool_goods_list"){
		$P->strLeftMenu = sellertool_menu();
	}else{
		$P->strLeftMenu = product_menu("/admin",$category_str);
	}
	$P->OnloadFunction = "init();";
	$P->addScript = $Script;
	$P->Navigation = "상품관리 > ".$menu_title." > ".$menu_name;
	$P->title = $menu_name;
	$P->strContents = $Contents;
	if ($category_load == "yes"){
		$P->OnLoadFunction = "zoomBox(event,this,200,400,0,90,img3)";
	}
	$P->PrintLayOut();
}

function SellState($select_name, $vstate){
	global $admininfo;

	if($admininfo[admin_level] == 9){
		$mstring = "
		<Select name='$select_name' id='$select_name' style='vertical-align:middle;width:139px;border:1px solid silver;'>
			<option value=0 ".($vstate == 0 ? "selected":"").">일시품절</option>
			<option value=1 ".(($vstate == 1 || $vstate == "") ? "selected":"").">판매중</option>";
		if($admininfo[mall_use_multishop]){
		$mstring .= "<option value=6 ".($vstate == 6 ? "selected":"").">입점업체 등록신청</option>";
		}
		$mstring .= "</Select>";
	}else if($admininfo[admin_level] == 8){
		$mstring = "
		<Select name=state style='vertical-align:middle'>
			<option value=0 ".($vstate == 0 ? "selected":"").">일시품절</option>";
		if ($vstate == 1 ){
		$mstring .= "<option value=1 ".($vstate == 1 ? "selected":"").">판매중</option>";
		}
		if($admininfo[mall_use_multishop]){
		$mstring .= "<option value=6 ".(($vstate == 6 || $vstate == "") ? "selected":"").">입점업체 등록신청</option>";
		}
		$mstring .= "</Select>";
	}
	return $mstring;
}


?>
