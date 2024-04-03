<?
include("../class/layout.class");

$db = new Database;
$db1 = new Database;
$db2 = new Database;
$db3 = new Database;
$odb = new Database;
$tdb = new Database;
session_start();

	$sql = "select *	from 
	mallstory_estimates where est_ix = '".$est_ix."' ";
	$db1->query($sql);
	$db1->fetch();


	$where = " and cart_key = '".$db1->dt[est_id]."'";
	$groupby = " group by cart_key";

$db->query("delete from mallstory_cart where 1=1 $where ");

$sql = "SELECT ed.* from mallstory_estimates_detail ed WHERE est_ix = '".$est_ix."' ";
$db->query($sql);


for($j = 0; $j < $db->total; $j++)
{
	$db->fetch($j);		
	$option = $db->dt[options];
	$pcount = $db->dt[pcount];
	$sellprice = $db->dt[sellprice];
	$options = array();
	$options_text = "";
	if($option) $options = explode("|",$option);
	else $options = "";
	$id = $db->dt[pid];
	//print_r($options);

	//같은상품에 같은옵션일경우 count 만업데이트해야 하므로 serialize로 체크

	$option_serial = "";
	if(is_array($options)){
		$option_serial = urlencode(serialize($options));
	}
	
	$tdb->query("delete from mallstory_cart where product_type = '6' $where ");
	// 현재 카트에 동일한 상품아이디와 옵션명을 가진 상품이 있는지 체크

	$tdb->query("select cart_ix, sellprice from mallstory_cart where id = '$id' and options = '$option_serial' $where");
	//echo "select cart_ix from mallstory_cart where id = '$id' and options = '$option_serial' $where";
	// 결과값이 없다면
	if (!$tdb->total)
	{
		include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class");
		$shmop = new Shared("reserve_rule");
		$reserve_data = $shmop->getObjectForKey("reserve_rule");
		$reserve_data = unserialize(urldecode($reserve_data));
		
		if($reserve_data[reserve_use_yn] == "Y"){
			$reserve_sql = " ,case when reserve_yn is NULL || reserve_yn = 'N' then round(".$sellprice."*(".$reserve_data[goods_reserve_rate]."/100)) else round(".$sellprice."*(reserve_rate/100)) end as reserve";
		}
		//상품에 관한 쿼리문
		$sql = "SELECT brand,brand_name,product_type, pname,delivery_company,surtax_yorn,stock_use_yn, coprice, pcode, admin,state, stock,commission,one_commission,sellprice,free_delivery_yn,free_delivery_count,hotcon_event_id, hotcon_pcode,warehouse_pcode,barcode,
						(select cid from ".TBL_MALLSTORY_PRODUCT_RELATION." where pid = '$id' and basic = 1) as cid $reserve_sql
						FROM ".TBL_MALLSTORY_PRODUCT." WHERE id='$id'";
						
		$db2->query($sql);
	
		//상품이 있다면
		if ($db2->total){
			$db2->fetch();
			
			//옵션텍스트 입력구문 //goods_view.php에서 select박스 값 array로 넘어옴.
			if(is_array($options)){
				for($i=0;$i<count($options);$i++){
					if($options[$i] > 0){
						$sql = "select o.option_div,o.id as select_option_id,o.option_warehouse_pcode,o.option_barcode, o.option_price, o.option_coprice,ot.option_name from ".TBL_MALLSTORY_PRODUCT_OPTION." o,".TBL_MALLSTORY_PRODUCT_OPTIONS." ot where id = '".$options[$i]."' and o.opn_ix = ot.opn_ix";
						$odb->query($sql);
						$odb->fetch();
						//echo $sql;
						$options_text .= $odb->dt[option_name]." : ".$odb->dt[option_div]."<br>";
						$select_option_id = $odb->dt[select_option_id];
						$db2->dt[warehouse_pcode] = $odb->dt[option_warehouse_pcode];
						$db2->dt[barcode] = $odb->dt[option_barcode];
						//if($odb->dt[option_price] > 0) $option_price = $odb->dt[option_price];
						
						if($odb->dt[option_coprice] > 0) $option_coprice = $odb->dt[option_coprice];
						else $option_coprice = $db2->dt[coprice];
					}
				}
			} else {
				$option_coprice = 0;
				$select_option_id = 0;
			}
			if($option_date){
				for($i=0;$i<count($option_date);$i++){
					if($option_date[$i] > 0){
						$options_text .="예약일 : ".$option_date[$i]."<br>";
					}
				}
			}
			
			//등록할 상품의 업체 수수료 셀렉트

			$sql = "select commission, company_name,quick from mallstory_companyinfo where company_id = '".$db2->dt[admin]."'";
			$db3->query($sql);
			$db3->fetch();
			$company_name = $db3->dt[company_name];
			
			if($db2->dt[one_commission] == "N"){				
				$commission = $db3->dt[commission];				
			}else{
				$commission = round(($db2->dt[sellprice]-$db2->dt[coprice])/$db2->dt[sellprice] * 100);
			}

			$sql = "select opn_ix from ".TBL_MALLSTORY_PRODUCT_OPTIONS." where pid = '".$id."' and option_use = '1' ";
			$db3->query($sql);
			if($db3->total){
				$option_yn = "Y";
			}else{
				$option_yn = "N";
			}

			$pname = str_replace("\"","&quot;",$db2->dt[pname]);
			$pname = str_replace("'","&#39;",$pname);

			$brand_name = str_replace("\"","&quot;",$db2->dt[brand_name]);
			$brand_name = str_replace("'","&#39;",$brand_name);

			$options_text = str_replace("\"","&quot;",$options_text);
			$options_text = str_replace("'","&#39;",$options_text);
			$sql = "insert into mallstory_cart (cart_ix,cart_key,cid,product_type, pname,mem_ix,pcount,sellprice,option_price,option_coprice,id,reserve,options,coprice,pcode,select_option_id,totalprice,option_yn,
					options_text,madeorder_text,company_id,company_name,brand,brand_name,stock,delivery_company,one_commission,commission,free_delivery_yn,free_delivery_count,quick,surtax_yorn,stock_use_yn,hotcon_event_id, hotcon_pcode,warehouse_pcode,barcode, regdate) 
					values 
					('','".$db1->dt[est_id]."','".$db2->dt[cid]."','".$db2->dt[product_type]."','".$pname."','".$user[code]."','$pcount','$sellprice','$option_price','$option_coprice','$id','".$db2->dt[reserve]."','$option_serial','".$db2->dt[coprice]."',
					'".$db2->dt[pcode]."','$select_option_id','".$sellprice*$pcount."','".$option_yn."','".$options_text."','".$madeorder_text."','".$db2->dt[admin]."','".$company_name."','".$db2->dt[brand]."','".$brand_name."','".$db2->dt[stock]."','".$db2->dt[delivery_company]."','".$db2->dt[one_commission]."','".$commission."','".$db2->dt[free_delivery_yn]."','".$db2->dt[free_delivery_count]."','".$db3->dt[quick]."','".$db2->dt[surtax_yorn]."','".$db2->dt[stock_use_yn]."','".$db2->dt[hotcon_event_id]."','".$db2->dt[hotcon_pcode]."','".$db2->dt[warehouse_pcode]."','".$db2->dt[barcode]."', NOW())";

			$odb->query($sql);
			$db3->query("SELECT cart_ix FROM mallstory_cart WHERE cart_ix=LAST_INSERT_ID()");
			$db3->fetch();
			$cart_ix = $db3->dt[cart_ix];
		}

	}//같은 상품이 있다면
	else
	{	
		$tdb->fetch();
		//쇼핑카트에 저장된 상품갯수와 현재 입력할 상품갯수가 틀리면 

		if($tdb->dt[pcount] != $pcount || $sellprice != $tdb->dt[sellprice]){
			//쇼핑카트에 입력된 상품의 갯수와 금액을 업데이트

			$sql = "update mallstory_cart set pcount = '$pcount',totalprice='".$sellprice*$pcount."',sellprice='".$sellprice."',stock_use_yn = '".$db2->dt[stock_use_yn]."', madeorder_text = '".$madeorder_text."' where cart_ix = '".$tdb->dt[cart_ix]."'";
			$db2->query($sql);
			$cart_ix = $tdb->dt[cart_ix];
		}
	}
}
$Contents ="
<table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%' valign=top>			
<tr height=50>
	<td align='left' colspan=6 > ".GetTitleNavigation("주문서 작성하기", "주문관리 > 주문서 작성하기 ")."</td>
</tr>
<tr > 
	<td width='100%' valign=top style='padding-top:3px;'>
";

$where = " where cart_key = '".$db1->dt[est_id]."'";

$sql = "select c.company_id,c.company_name,(select delivery_freeprice from mallstory_companyinfo where admin_level = 9) as delivery_freeprice,(select delivery_policy from mallstory_companyinfo where admin_level = 9) as delivery_policy,(select delivery_free_policy from mallstory_companyinfo where admin_level = 9) as delivery_free_policy,(select delivery_product_policy from mallstory_companyinfo where admin_level = 9) as delivery_product_policy from mallstory_cart c $where and delivery_company = 'WE' group by company_id order by regdate desc";

$db->query($sql);
$carts = $db->fetchall();

for($i=0; $i<count($carts); $i++){

$Contents .= "			
	<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center>
		<tr>
		<td colspan='6' style='height:30px;font-weight:bold;'><img src='../images/dot_org.gif' align=absmiddle> 아이스크림몰 직배송 상품</td>
		</tr>
		<tr align=center> 
			<td width=5% height=28 class=s_td>번호</td>
			<td width=30% class=m_td>상품명</td>
			<td width=10% class=m_td>옵션</td>
			<td width=15% class=m_td>수량</td>
			<td width=15% class=m_td>가격</td>
			<td width=10% class=e_td>합계</td>
		</tr>";
$package_y = 0;
$package_n = 0;
$company_totalprice = 0;

$getCompanyCart = getCompanyCartAdmin($carts[$i][company_id],'WE', $db1->dt[est_id]);
for($k=0; $k<count($getCompanyCart); $k++){
	$num = $num + 1;
	if($getCompanyCart[$k][delivery_package] != "Y"){
		$package_y = $package_y + 1;
	} else{
		$package_n = $package_n + 1;
	}
	$company_totalprice = $company_totalprice + $getCompanyCart[$k][totalprice];
	
	if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$getCompanyCart[$k][id].".gif")){
		$img_str = "".$admin_config[mall_data_root]."/images/product/c_".$getCompanyCart[$k][id].".gif";
	}else{
		$img_str = "../image/no_img.gif";
	}

$Contents .= "
		<tr class='dotted'>
			<td align='center'>$num</td>
			<td style='padding:10px;'>
				<p style='float:left;padding-right:10px;'><img src='$img_str' alt='' class='p_img' alt='상품 설명'></p>
				<p style='float:left;'>".$getCompanyCart[$k][pname]."</p><Br>".($getCompanyCart[$k][state] == "0" ? "<img src ='/data/sigong/templet/basic/sigong_img/btn_soldout.jpg' align='absmiddle'>" :"")."
			</td>
			<td><span id='optuion_text_".$getCompanyCart[$k][cart_ix]."'>".$getCompanyCart[$k][options_text]."</span></td>
			<td align='center'>
				<input type=hidden size=3 maxlength=3 value='".$getCompanyCart[$k][pcount]."' name=quantity > 
				".$getCompanyCart[$k][pcount]." 개</td>
			<td align='center'>
				<p>".number_format($getCompanyCart[$k][sellprice])."원</p>
				<p class='green'>(적립포인트 ".$getCompanyCart[$k][reserve]."p)</p>
			</td>
			<td align='center'>
				<p class='price' >".number_format($getCompanyCart[$k][totalprice])."원</span></p>
			</td>
		</tr>
		<tr height=1><td colspan=8 class='dot-x'></td></tr>
		";

}
$delivery_price = getDeliveryPrice($carts[$i][company_id],'WE', 0, $db1->dt[est_id]);
if($carts[$i][delivery_basic_policy] == "2"){
	$total_delivery_price2 = $total_delivery_price2 +  $delivery_price;
}else{
	$total_delivery_price = $total_delivery_price +  $delivery_price;
}
$total_cart_price = $total_cart_price +  $company_totalprice;
$Contents .= "
		<tr><td colspan=8>
		<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center>
		<tr>
			<td class='left'>
				<p>업체명 : <span class='bold'>아이스크림 몰</span>(".deliveryCompanyList($carts[$i][quick],'text').")</p>
			</td><!--f:left-->
			<td align='right'>
				<p>(상품금액 <span class='red bold'>".number_format($company_totalprice)."</span>원 + 배송비 <span class='red bold'>".number_format($delivery_price)."</span>원)
				<p class='bf'>주문금액 : <span class='red bold'>".number_format($company_totalprice + $delivery_price)."</span>원</p>
			</td><!--f:right-->
		</tr><!--f:bep-->
		</table>
		</td></tr>
		<tr height=1><td colspan=8 class='dot-x'></td></tr>
	</table>
		";

}

$db->query("select c.company_id,c.company_name,quick,if((select delivery_policy from mallstory_companyinfo where company_id = c.company_id ) != 1,(select delivery_freeprice from mallstory_companyinfo where company_id = c.company_id ),(select delivery_freeprice from mallstory_companyinfo where admin_level = 9)) as delivery_freeprice,
if((select delivery_policy from mallstory_companyinfo where company_id = c.company_id ) != 1,(select delivery_basic_policy from mallstory_companyinfo where company_id = c.company_id ),(select delivery_basic_policy from mallstory_companyinfo where admin_level = 9)) as delivery_basic_policy,
if((select delivery_policy from mallstory_companyinfo where company_id = c.company_id ) != 1,(select delivery_free_policy from mallstory_companyinfo where company_id = c.company_id ),(select delivery_free_policy from mallstory_companyinfo where admin_level = 9)) as delivery_free_policy,
if((select delivery_policy from mallstory_companyinfo where company_id = c.company_id ) != 1,(select delivery_product_policy from mallstory_companyinfo where company_id = c.company_id ),(select delivery_product_policy from mallstory_companyinfo where admin_level = 9)) as delivery_product_policy
from mallstory_cart c $where and (delivery_company = '' or delivery_company = 'MI') group by company_id order by company_id desc");
$carts2 = $db->fetchall();

for($c=0; $c<count($carts2); $c++){
	$sql = "select shop_name from mallstory_companyinfo where company_id = '".$carts2[$c][company_id]."' ";
	$tdb->query($sql);
	$tdb->fetch();
	$carts2[$c][shop_name] = $tdb->dt[shop_name];
}

for($i=0; $i<count($carts2); $i++){

$Contents .= "
	<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center>
		<tr>
		<td colspan='6' style='height:30px;font-weight:bold;'><img src='../images/dot_org.gif' align=absmiddle> ".$carts2[$i][shop_name]." 직배송 상품</td>
		</tr>
		<tr align=center> 
			<td width=5% height=28 class=s_td>번호</td>
			<td width=30% class=m_td>상품명</td>
			<td width=10% class=m_td>옵션</td>
			<td width=15% class=m_td>수량</td>
			<td width=15% class=m_td>공급가</td>
			<td width=15% class=m_td>가격</td>
			<td width=10% class=e_td>합계</td>
		</tr>";
$package_y = 0;
$package_n = 0;
$company_totalprice = 0;

$getCompanyCart = getCompanyCartAdmin($carts2[$i][company_id],'MI', $db1->dt[est_id]);
//print_r($getCompanyCart);
for($k=0; $k<count($getCompanyCart); $k++){
	$num = $num + 1;
	if($getCompanyCart[$k][delivery_package] != "Y"){
		$package_y = $package_y + 1;
	} else{
		$package_n = $package_n + 1;
	}
	$company_totalprice = $company_totalprice + $getCompanyCart[$k][totalprice];
	
	if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$getCompanyCart[$k][id].".gif")){
		$img_str = "".$admin_config[mall_data_root]."/images/product/c_".$getCompanyCart[$k][id].".gif";
	}else{
		$img_str = "../image/no_img.gif";
	}
	if($getCompanyCart[$k][option_coprice] > 0) $coprice = $getCompanyCart[$k][option_coprice];
	else $coprice = $getCompanyCart[$k][coprice];

$Contents .= "
					<tr class='dotted'>
						<td align='center'>$num</td>
						<td style='padding:10px;'>
							<p style='float:left;padding-right:10px;'><img src='$img_str' alt='' class='p_img' alt='상품 설명'></p>
							<p style='float:left;'>".$getCompanyCart[$k][pname]."</p><Br>".($getCompanyCart[$k][state] == "0" ? "<img src ='/data/sigong/templet/basic/sigong_img/btn_soldout.jpg' align='absmiddle'>" :"")."
						</td>
						<td align='center'><span id='optuion_text_".$getCompanyCart[$k][cart_ix]."'>".$getCompanyCart[$k][options_text]."</span></td>
						<td align='center'>
							<input type=hidden size=3 maxlength=3 value='".$getCompanyCart[$k][pcount]."' name=quantity > 
							".$getCompanyCart[$k][pcount]." 개</td>
						<td align='center'>
						<p>".number_format($coprice)."원</p>
						<p>".number_format($getCompanyCart[$k][coprice])."원</p>
						</td>
						<td align='center'>
							<p>".number_format($getCompanyCart[$k][sellprice])."원</p>
							<p class='green'>(적립포인트 ".$getCompanyCart[$k][reserve]."p)</p>
						</td>
						<td align='center'>
							<p class='price' >".number_format($getCompanyCart[$k][totalprice])."원</span></p>
						</td>
					</tr>
				<tr height=1><td colspan=8 class='dot-x'></td></tr>
					";

}
$delivery_price = getDeliveryPrice($carts2[$i][company_id],'MI', 0, $db1->dt[est_id]);
if($carts2[$i][delivery_basic_policy] == "2"){
	$total_delivery_price2 = $total_delivery_price2 +  $delivery_price;
}else{
	$total_delivery_price = $total_delivery_price +  $delivery_price;
}
$total_cart_price = $total_cart_price +  $company_totalprice;

$Contents .= "
		<tr>
			<td colspan=8>
			<table cellpadding=10 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center>
			<tr>
				<td class='left'>
					<p>업체명 : <span class='bold'>".$carts2[$i][shop_name]."</span>(".deliveryCompanyList($carts2[$i][quick],'text').")</p>
				</td><!--f:left-->
				<td align='right'>
					<p>(상품금액 <span class='red bold'>".number_format($company_totalprice)."</span>원 + 배송비 <span class='red bold'>".number_format($delivery_price)."</span>원)
					<p class='bf'>주문금액 : <span class='red bold'>".number_format($company_totalprice + $delivery_price)."</span>원</p>
				</td><!--f:right-->
			</tr><!--f:bep-->
			</table>
			</td>
		</tr>
		<tr height=1><td colspan=8 class='dot-x'></td></tr>
		</table>
		";
}
$Contents .= "
	<table cellpadding=10 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center>
		<tr>
			<td align='right'>상품금액  : <span class='red'>".number_format($total_cart_price)."원</span>+  배송비 : <span class='red'>".number_format($total_delivery_price)."원</span> = 총 주문금액 : <span class='red'>".number_format($total_cart_price + $total_delivery_price)."원</span></td>
		</tr>
		<tr height=1><td colspan=8 class='dot-x'></td></tr>
	</table><!--f:buttonSection-->
	";

	$est_delivery_zip = explode("-", $db1->dt[est_delivery_zip]);
	$est_tel = explode("-", $db1->dt[est_tel]);
	$est_mobile = explode("-", $db1->dt[est_mobile]);
$Contents .= "
<form  name='form' method='post' onsubmit=\"return CheckFormValue(this)\" action='./estimate.cart.act.php'>
<input type=hidden name=carttype value='$order_cart_type'>          
<input type=hidden name='myreserve_price' value='$total_reserve'> 
<input type=hidden name='cart_totalprice' value='$total_cart_price'>
<input type=hidden name='delivery_total_price' value='$total_delivery_price'>
<input type=hidden name='mall_ix' value='$mall_ix'>
<input type=hidden name='cart_key' value='".$db1->dt[est_id]."'>
<input type='hidden' name='delivery_method' value='TE'>
<input type='hidden' id='code' value=''>
	<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center>
		<tr>
			<td valign='top'>
				<table width=\"420px\">
					<tr>
						<td colspan='2'>
			<p class=\"rightFloat\">
				<img src='../images/dot_org.gif' align=absmiddle> <b>주문자 정보</b> <input type='button' value='아이디 검색' onclick='idsearch();'  class='box' >
				<input type='hidden' name='ucode'>
			</p>
						</td>
						</tr>
						<tr height=1><td colspan=2 class='dot-x'></td></tr>
						<tr class=\"border\">
							<td width=\"100\">이름</td>
							<td><input type='text' name='name_a' id='name_a' size='27' maxlength='20' class='input' value='".$db1->dt[est_charger]."' validation=\"true\"  title=\"이름\"  ></td>
						</tr>
						<tr height=1><td colspan=2 class='dot-x'></td></tr>
						<tr>
							<td class=\"list\">주소</td>
							<td><input type='text' name='zipcode1' id=\"zipcode1\"  size='10' maxlength='3' class='input' value='$est_delivery_zip[0]' validation=\"true\" title=\"우편번호\" >		-
								<input type='text' name='zipcode2' id=\"zipcode2\"  size='10' maxlength='3' class='input' value='$est_delivery_zip[1]' validation=\"true\" title=\"우편번호\" >											
								
								<input type='button' value='주소 찾기'  class='box' onClick=\"zipcode('2')\"  alt=\"주소 찾기\">
								
								<input type='text' name='addr1' id=\"addr1\" size='40' maxlength='80' class='input' value='".$db1->dt[est_delivery_postion]."' validation=\"true\" title=\"주소\" >
								
								<input type='text' name='addr2' id=\"addr2\" size='40' maxlength='80' class='input' value='".$db1->dt[est_delivery_postion2]."' validation=\"true\" title=\"세부주소\"> 세부주소</td>
						</tr>
						<tr height=1><td colspan=2 class='dot-x'></td></tr>
						<tr>
							<td class=\"list\">전화번호</td>
							<td><input type='text' name='tel1_a' id='tel1_a' size='10' maxlength='3' class='input' value='$est_tel[0]' validation=\"true\" title=\"전화번호\" numeric=\"true\"> -
							<input type='text' name='tel2_a' id='tel2_a' size='12' maxlength='4' class='input' value='$est_tel[1]' validation=\"true\" title=\"전화번호\" numeric=\"true\"> -
							<input type='text' name='tel3_a' id='tel3_a' size='12' maxlength='4' class='input' value='$est_tel[2]' validation=\"true\" title=\"전화번호\" numeric=\"true\">
							</td>
						</tr>
						<tr height=1><td colspan=2 class='dot-x'></td></tr>
						<tr>
							<td class=\"list\">핸드폰</td>
							<td><input type='text' name='pcs1_a' id='pcs1_a' size='10' maxlength='3' class='input' value='$est_mobile[0]' validation=\"true\" title=\"핸드폰번호\" numeric=\"true\"> - 
							<input type='text' name='pcs2_a' id='pcs2_a' size='12' maxlength='4' class='input' value='$est_mobile[1]' validation=\"true\" title='핸드폰번호' numeric=\"true\"> - 
							<input type='text' name='pcs3_a' id='pcs3_a' size='12' maxlength='4' class='input' value='$est_mobile[2]' validation=\"true\" title=\"핸드폰번호\" numeric=\"true\"></td>
						</tr>
						<tr height=1><td colspan=2 class='dot-x'></td></tr>
						<tr>
							<td>이메일</td>

							<td><input type='text' name='mail_a' id='mail_a' size='45' maxlength='100' class='input' value='".$db1->dt[est_email]."' validation=\"true\" title=\"이메일\" email=\"true\"> 
							</td>
						</tr>
					</tbody>
				</table>
			</td>

<script>
    // 입력내용 체크 *************************************************************
	function memEdit(){
	
		$.post(\"./infoinput.mem.act.php\", {
		  name_a: $(\"#name_a\").val(),
		  zipcode1: $(\"#zipcode1\").val(),
		  zipcode2: $(\"#zipcode2\").val(),
		  addr1: $(\"#addr1\").val(),
		  addr2: $(\"#addr2\").val(),
		  tel1_a: $(\"#tel1_a\").val(),
		  tel2_a: $(\"#tel2_a\").val(),
		  tel3_a: $(\"#tel3_a\").val(),
		  pcs1_a: $(\"#pcs1_a\").val(),
		  pcs2_a: $(\"#pcs2_a\").val(),
		  pcs3_a: $(\"#pcs3_a\").val(),
		  mail_a: $(\"#mail_a\").val(),
		  code: $(\"#code\").val(),
		  act: \"memEdit\"
		}, function(data){
			if(data == \"Y\") {
				alert(\"성공적으로 변경이 되었습니다.\");

			} else {
				alert(\"정상적으로 처리가 되지 않았습니다 고객센터에 문의하세요\");

				return false;
			}
		});
		
	}

$(document).ready(function(){
	$('#submit_btn').click(function(){
		input_text();
	});
});

function check_gift_yn() {
	var fm=document.form;
	var ch=fm.no_gift;
	var gift_f=fm.gift_id;
	var cnt=gift_f.length;
	if(ch.checked) {
		for(var i=0;i<cnt;i++) {
			gift_f[i].checked=false;
			gift_f[i].disabled=true;
		}
	} else {
		for(var i=0;i<cnt;i++) {
			gift_f[i].disabled=false;
		}
		gift_f[0].checked=true;
	}
}
</script>
		<!--toss:주문자 정보 테이블-->

			<td valign='top'>
				<table width=\"420px\">
					<tr>
						<td colspan='2'>
							<span style='float:left;'>
								<img src='../images/dot_org.gif' align=absmiddle> <b>받는분 정보</b>
							</span>
							<span style='float:right;'>
								<input type=\"radio\" name='same' value='1' id='same_1' onClick='isEQ()'><label for='same_1'>주문자 정보와 동일</label>
								<input type=\"radio\" name='same' id='same_0' value='0' onClick='isEQ()'><label for='same_0'>신규 입력</label>
							</span>
						</td>
					</tr>
					<tr height=1><td colspan=2 class='dot-x'></td></tr>
					<tr>
						<td width=\"100\">이름</td>
						<td><input type='text' name='name_b' size='27' maxlength='20' class='input' value='$mem_name' validation=\"true\"  title=\"이름\" ></td>
					</tr>
					<tr height=1><td colspan=2 class='dot-x'></td></tr>
					<tr>
						<td>주소</td>
						<td><input type='text' name='zipcode1_b' id=\"zipcode1_b\"  size='10' maxlength='3' class='input' value='' validation=\"true\" title=\"사업장 우편번호\" readonly> -
						<input type='text' name='zipcode2_b' id=\"zipcode2_b\"  size='10' maxlength='3' class='input' value='' validation=\"true\" title=\"사업장 우편번호\" readonly>											
							<input type='button' value='주소 찾기'  class='box' onClick=\"zipcode('3')\" alt=\"주소 찾기\"></br>
							
							<input type='text' name='addr1_b' id=\"addr1_b\" size='40' maxlength='80' class='input' value='' validation=\"true\" title=\"사업장주소\" readonly><br>
							
							<input type='text' name='addr2_b' id=\"addr2_b\" size='40' maxlength='80' class='input' value='' validation=\"true\" title=\"사업장 세부주소\"> 세부주소</td>
					</tr>
					<tr height=1><td colspan=2 class='dot-x'></td></tr>
					<tr>
						<td>전화번호</td>

						<td><input type='text' name='tel1_b' size='10' maxlength='3' class='input' value='' validation=\"true\" title=\"전화번호\" numeric=\"true\"> -
						<input type='text' name='tel2_b' size='12' maxlength='4' class='input' value='' validation=\"true\" title=\"전화번호\" numeric=\"true\"> -
						<input type='text' name='tel3_b' size='12' maxlength='4' class='input' value='' validation=\"true\" title=\"전화번호\" numeric=\"true\">
						</td>
					</tr>
					<tr height=1><td colspan=2 class='dot-x'></td></tr>
					<tr>
						<td>핸드폰</td>

						<td><input type='text' name='pcs1_b' size='10' maxlength='3' class='input' value='' validation=\"true\" title=\"핸드폰번호\" numeric=\"true\"> - <input type='text' name='pcs2_b' size='12' maxlength='4' class='input' value='' validation=\"true\" title='핸드폰번호' numeric=\"true\"> - <input type='text' name='pcs3_b' size='12' maxlength='4' class='input' value='' validation=\"true\" title=\"핸드폰번호\" numeric=\"true\"></td>
					</tr>
					<tr height=1><td colspan=2 class='dot-x'></td></tr>
					<tr>
						<td>기타 요구사항</td>

						<td><input type=\"text\" name='msg1' size='40' value='' /></td>
					</tr>
					<tr height=1><td colspan=2 class='dot-x'></td></tr>
					<tr>
						<td>배송시 유의사항</td>

						<td><input type=\"text\" name='msg2' size='40' value=\"EX) 부재시 행정실 배송요망 등\" rel=\"first\" id=\"msg2\" onclick=\"input_text()\" /></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan='2' height='25'><img src='../images/dot_org.gif' align=absmiddle> <b>결제정보입력</b>
			</td>
		</tr>
		<tr height=1><td colspan=2 class='dot-x'></td></tr>
		<tr>
			<td colspan='2' height='25'>
	<!--s:지훈: 신용카드-->

		<div>
			<p class=\"bold\"><input	type=\"radio\" class='check' name='payment_div' value='after_bank' title=\"결제방법\" validation=\"false\" checked/>후불제 (학교예산)
		</div>
			</td>
		</tr>
		<tr height=1><td colspan=2 class='dot-x'></td></tr>
		<tr>
			<td colspan='2'>
		<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center>
				<tr>
					<td width='100' height='25'>상품 총 금액</td>
					<td><span>".number_format($total_cart_price)."원</span></td>
				</tr>
				<tr height=1><td colspan=2 class='dot-x'></td></tr>
				<tr>
					<td height='25'>배송료</td>
					<td><span>".number_format($total_delivery_price)."원</span></td>
				</tr>
				<tr height=1><td colspan=2 class='dot-x'></td></tr>
				<tr>
					<td height='25'>최종 결재 금액</td>
					<td><span>".number_format($total_cart_price)."원 (무료배송!, 배송비".number_format($total_delivery_price)."원 할인)</span></td>
				</tr>
		</table>";
		if($total_cart_price >= 500000){
		
$Contents .= "
		<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center>
		<col width='100' />
		<col width='*' />
		<tr>
			<td colspan='2' height='10'></td>
		</tr>
		<tr>
			<td height='25'><img src='../images/dot_org.gif' align=absmiddle> <b>사은품 선택</b></td>
			<td align='left'><input type='checkbox' name='no_gift' id='no_gift' value='Y' style='cursor:pointer;'
			onClick='check_gift_yn()' /> <label for='no_gift' style='cursor:pointer;'>사은품 선택 안함</label></td>
		</tr>
		<tr height=1><td colspan=2 class='dot-x'></td></tr>
				<tr>
				<td colspan='2'>
					<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center>
						<tr>
						";
$giftRelation = giftRelation($total_cart_price);
for($g=0; $g<count($giftRelation); $g++){
$Contents .= "
				<td>
					<img src='".$admin_config[mall_data_root]."/images/product/s_".$giftRelation[$g][id].".gif' alt=\"예시 이미지\" width=\"95px\" height=\"95px\"><br / >
					<input type=\"radio\" class=\"check bonusck\" name='gift_id' value='".$giftRelation[$g][id]."' title=\"사은품\" ".($g == 0 ? " checked" : "")."/>
					".$giftRelation[$g][pname]."
				</td>";
}
$Contents .= "
						</tr>
					</table>
				</td>
			</tr>
		</table>";
		}
$Contents .= "
	<!--f:지훈: 신용카드-->

			</td>
		</tr>
		<tr>
			<td colspan='2'>
		<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center>
		<tr>
			<td colspan='2' height='10'></td>
		</tr>
		<tr>
			<td colspan='2' height='25'><img src='../images/dot_org.gif' align=absmiddle> <b>증빙문서 정보 입력</b>
			</td>
		</tr>
		<tr height=1><td colspan=2 class='dot-x'></td></tr>
				<tr>
					<td width='100' height='25'>증빙문서</td>
					<td>
						<span id='receipt_type1'><input type=\"radio\" class=\"check receipt_type\" name='receipt_type'  value='1' validation=\"true\" title=\"증빙문서\" onclick=\"receiptChoice('1')\" />(전자)세금 계산서</span>
						<span id='receipt_type2'><input type=\"radio\" class=\"check receipt_type\" name='receipt_type'  value='2' validation=\"true\" title=\"증빙문서\" onclick=\"receiptChoice('2')\" />지출증빙용 현금영수증</span>
						<span id='receipt_type3' style='display:none'><input type=\"radio\" class=\"check receipt_type\" name='receipt_type'  value='3' validation=\"true\" title=\"증빙문서\" onclick=\"receiptChoice('2')\" />소득공제용 현금영수증</span>
						<span id='receipt_type4'><input type=\"radio\" class=\"check receipt_type\" name='receipt_type'  value='4' validation=\"true\" title=\"증빙문서\" onclick=\"receiptChoice('3')\" checked />미발급</span>
					</td>
				</tr>
				<tr height=1><td colspan=2 class='dot-x'></td></tr>
				<tr id='receipt_result1' style='display:none'>
					<td></td>
					<td height='25'>
						결재 창에서 바로 발급 받으실 수 있습니다.

					</td>
				</tr>
				
				<tr id='receipt_result_non' style='display:none'>
					<td></td>
					<td height='25'>
						주문 완료 후 재신청을 원하실 경우에는 고객센터로 문의해주세요 TEL 1544-6040

					</td>
				</tr>
				
				<tr id='receipt_result1_1' style=\"display:none;\">
					<td width='100' height='25'>학교명</td>
					<td>
						<input type='text' name='sc_name2' size='27' maxlength='20' class='valid' id='sc_name2'  validation=\"true\" title=\"학교명\" value='$sc_nm' class=\"align\" readonly>
						<input type='button' value='학교 찾기'  class='box'  onClick=\"PoPWindow('/popup/school.php?school_type=3&school_form=form&company_yn=N&gubun=1','450','360','school')\"><span style='color:red;padding-left:60px' id='com_num_info' >	(사업자 등록번호 : <input type='text' name='sc_number' class=\"school_input\" value='000-00-00000' size=12 readonly style='color:red'> )</span>

					</td>
				</tr>
				<tr id='receipt_result2' style=\"display:none;\">
					<td width='100' height='25'>학교정보</td>
					<td>
						<table cellpadding=10 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center>
							<tr>
								<td height='25'>학교명</td>
								<td><input type=\text\" class='valid' name='sc_name' validation='true' title='학교정보' value='$sc_nm' readonly>
								</td>
							</tr>
							<tr height=1><td colspan=2 class='dot-x'></td></tr>
							<tr>
								<td height='25'>대표자명</td>
								<td><input type='text' name='sc_ceo' class='valid' validation='true' title='대표자명' value='$com_ceo'></td>
							</tr>
							<tr height=1><td colspan=2 class='dot-x'></td></tr>
							<tr>
								<td height='25'>주소</td>
								<td><input type=\"text\" size=\"10\" class='valid' validation='true' title='주소' readonly name='sc_zip1' value='$sc_zip1'> - <input type=\"text\" size=\"10\" class='valid' validation='true' title='주소' name='sc_zip2' readonly value='$sc_zip2'>
								<input type='button' value='주소 찾기'  class='box'   onClick=\"PoPWindow('/popup/zipcode.php?zip_type=6&zip_form=form','450','360','addr')\" ><br />
								<input type=\"text\" class='valid' validation='true' title='상세' name='sc_addr1' readonly value='$sc_addr' style=\"width:250px;\">
								<input type=\"text\" class='valid' title='상세' name='sc_addr2' value='$sc_addr2' style=\"width:250px;\">
								</td>
							</tr>
							<tr height=1><td colspan=2 class='dot-x'></td></tr>

						</table>
					</td>
				</tr>
				<tr id='receipt_result3' style=\"display:none;\">
					<td>&nbsp;서류 수신<br>담당자 정보</td>

					<td>
						<p>
							<input type=\"radio\" class=\"check\" name='same_sc' value='Y' checked onclick=\"isEQ_sc()\"/>주문자 정보와 동일
							<input type=\"radio\" class=\"check\" name='same_sc' value='N' onclick=\"isEQ_sc()\"/>기존 정보 사용(행정실 담당)
						</p>
						<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center>
							<tr>
								<td height='25'>담당자명</td>
								<td><input type=\"text\" name='sc_damdang' class='valid' validation='true' title='담당자명' value='$sc_damdang' readonly><td>
								</td>
							</tr>
							<tr height=1><td colspan=2 class='dot-x'></td></tr>
							<tr>
								<td height='25'>전화번호</td>
								<td>
									<input type='text' name='sc_tel1' size='10' maxlength='3' class='valid' value='$sc_damdang_tel1' validation=\"true\" title=\"전화번호\" numeric=\"true\"> -
									<input type='text' name='sc_tel2' size='12' maxlength='4' class='valid' value='$sc_damdang_tel2' validation=\"true\" title=\"전화번호\" numeric=\"true\"> -
									<input type='text' name='sc_tel3' size='12' maxlength='4' class='valid' value='$sc_damdang_tel3' validation=\"true\" title=\"전화번호\" numeric=\"true\">
								</td>
							</tr>
							<tr height=1><td colspan=2 class='dot-x'></td></tr>
							<tr>
								<td height='25'>핸드폰번호</td>
								<td>	
									<input type='text' name='sc_pcs1' size='10' maxlength='3' class='valid' value='$sc_damdang_pcs1' validation=\"true\" title=\"핸드폰번호\" numeric=\"true\"> - 
									<input type='text' name='sc_pcs2' size='12' maxlength='4' class='valid' value='$sc_damdang_pcs2' validation=\"true\" title='핸드폰번호' numeric=\"true\"> - 
									<input type='text' name='sc_pcs3' size='12' maxlength='4' class='valid' value='$sc_damdang_pcs3' validation=\"true\" title=\"핸드폰번호\" numeric=\"true\">
								</td>
							</tr>
							<tr height=1><td colspan=2 class='dot-x'></td></tr>
							<tr>
								<td height='25'>이메일 주소</td>
								<td>
									<input type='text' name='sc_mail' size='35' maxlength='100' class='valid' value='$sc_damdang_email' validation=\"true\" title=\"이메일\" email=\"true\"> 
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
			</td>
		</tr>
		<tr height=1><td colspan=2 class='dot-x'></td></tr>
		<tr>
			<td colspan='2'>
				<div class=\"buttonSection\" style='padding-top:10px;'>";
				$get_cart_num=0;
				//print_r($getCompanyCart);
				for($k=0; $k<count($getCompanyCart); $k++){
					if (($getCompanyCart[$k][stock_use_yn] == "Y" && $getCompanyCart[$k][sell_yn] == "Y") && ($getCompanyCart[$k][stock] <=0 || $getCompanyCart[$k][disp] == 0)){
						$get_cart_num++;
						if(!$surplus_pname){
							$surplus_pname .= $getCompanyCart[$k][pname];
						}else{
							$surplus_pname .= ", ".$getCompanyCart[$k][pname];
						}
					}
				}
				
				if($get_cart_num>0) {
					$Contents .= "품절중인 상품 <strong>{$surplus_pname}</strong> 이 있습니다. 확인 후 진행 바랍니다.";
				} else {
					$Contents .= "<input type='submit' value='다음단계'  class='box' id='submit_btn'>";
				}
				$Contents .= "
					<!--input type='submit' value='다음단계'  class='box' id='submit_btn'-->
				</div><!--f:buttonSection-->
			</td>
		</tr>
	</table>
</form>
	";

$Contents .= "
	</td>
</tr>
</table>

<form name='sc_info'>
<input type='hidden' name='sc_damdang' value='$sc_damdang'>
<input type='hidden' name='sc_damdang_tel1' value='$sc_damdang_tel1'>
<input type='hidden' name='sc_damdang_tel2' value='$sc_damdang_tel2'>
<input type='hidden' name='sc_damdang_tel3' value='$sc_damdang_tel3'>
<input type='hidden' name='sc_damdang_pcs1' value='$sc_damdang_pcs1'>
<input type='hidden' name='sc_damdang_pcs2' value='$sc_damdang_pcs2'>
<input type='hidden' name='sc_damdang_pcs3' value='$sc_damdang_pcs3'>
<input type='hidden' name='sc_mail' value='$sc_damdang_email'>
</form>
		";
		
$Script = "
<script language='JavaScript' src='/admin/js/admin.js'></Script>
<script language='JavaScript' src='./estimate.js'></Script>
";

if($view == "innerview"){
	
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>".EstimateApplyList($cid,$depth)."</body></html>";	
	
	echo "
	<Script>
	parent.document.getElementById('estimate_product_list').innerHTML = document.body.innerHTML;
	</Script>";
}else{	
	$P = new LayOut;
	$P->addScript = $Script;
	$P->OnloadFunction = "receiptChoice('3')";//MenuHidden(false);
	$P->prototype_use = false;
	$P->jquery_use = true;
	$P->strLeftMenu = order_menu();
	$P->strContents = $Contents;
	$P->Navigation = "HOME > 주문관리 > 견적현황";
	$P->PrintLayOut();
}
function getCompanyCartAdmin($company_id,$delivery_company, $cart_key){
	global $user;
	$where = " cart_key = '$cart_key'";
	if($delivery_company == "MI"){
		$delivery_company_where = " and (c.delivery_company ='MI' or c.delivery_company = '') ";
	}else{
		$delivery_company_where = " and c.delivery_company = '$delivery_company' ";
	}
	$mdb = new Database;

	$sql = "select c.*, 
				p.delivery_package,p.state,p.stock_use_yn,p.sell_yn,p.stock,p.disp,
				if(p.delivery_policy =1,(select if(delivery_policy = 1,(select delivery_price from mallstory_companyinfo where admin_level = 9),delivery_price) from mallstory_companyinfo where company_id = '$company_id' ),delivery_price) as delivery_price,
				if(p.delivery_policy =1,(select if(delivery_policy = 1,(select delivery_price from mallstory_companyinfo where admin_level = 9),delivery_price) from mallstory_companyinfo where company_id = '$company_id' ),delivery_price) as delivery_price
				from mallstory_cart c,".TBL_MALLSTORY_PRODUCT." p where $where and c.id = p.id and company_id = '$company_id' $delivery_company_where order by delivery_price desc ";

	$mdb->query($sql);
	return $mdb->fetchall();
}
function giftRelation($total_price){
	global $db;

	$sql = "select * from ".TBL_MALLSTORY_PRODUCT." where $total_price >= startprice and $total_price < endprice and product_type = '6' ";
	$db->query($sql);

	$gift_product = $db->fetchall();

	return $gift_product;
}

function getScName($sc_code){
	global $db;

	$db->query("select sc_nm from mallstory_comm_sc where sc_code = '$sc_code' ");
	$db->fetch();

	return $db->dt[sc_nm];
}



?>

