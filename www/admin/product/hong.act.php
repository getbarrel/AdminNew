<?
header("Content-type: text/html; charset=utf-8");
include "../class/Snoopy.class.php";
include("../../class/database.class");

if(!$bs_url){
	
	//$bs_url = "http://www.animate-onlineshop.jp/pn/%E3%80%90%E3%82%B0%E3%83%83%E3%82%BA-%E3%82%AD%E3%83%BC%E3%83%9B%E3%83%AB%E3%83%80%E3%83%BC%E3%80%91W+%E3%83%A9%E3%83%90%E3%83%BC%E3%83%9E%E3%82%B9%E3%82%B3%E3%83%83%E3%83%88%E3%81%A1%E3%82%89%E3%81%A3%E3%81%A8+To+LOVE%E3%82%8B-%E3%81%A8%E3%82%89%E3%81%B6%E3%82%8B-%E3%83%80%E3%83%BC%E3%82%AF%E3%83%8D%E3%82%B9/pd/1221809/";
	//$bs_url = "http://www.animate-onlineshop.jp/pn/%E3%80%90%E3%82%B3%E3%82%B9%E3%83%97%E3%83%AC-%E8%A1%A3%E8%A3%85%E3%80%91DIABOLIK+LOVERS+%E5%B6%BA%E5%B8%9D%E5%AD%A6%E9%99%A2%E9%AB%98%E6%A0%A1%E5%88%B6%E6%9C%8D%28%E7%94%B7%E5%AD%90%29+/SIZE-L/pd/1210433/";

	//$bs_url = "http://www.kenko.com/product/item/itm_6930490472.html";
	$bs_url	=	"http://bananarepublic.gap.co.jp/browse/product.do?cid=1008572&vid=1&pid=934086036";
	//$bs_url = "http://shop.akachan.jp//shopping/g/g801000400";

	//$bs_url = "http://shop.beams.co.jp/shop/raybeams/goods.html?gid=1061157&pl_on=2";
	//$bs_url = "http://gumzzi.co.kr/shop/shopdetail.html?branduid=122698&special=3";
	$bs_url = "http://www.gap.com/browse/product.do?cid=65891&vid=1&pid=941820002";
	//$bs_url = "http://www.gap.co.jp/browse/product.do?cid=1009078&vid=1&pid=481881006";

}

if(!$bs_site){
	
	//$bs_site = "akachan";
	//$bs_site = "kenko";
	//$bs_site = "beams";
	//$bs_site = "pinkboll";
	//$bs_site = "gap_jp";
	//$bs_site = "gap";
	$bs_site = "bananarepublic.gap.co.jp";
}

include "buyingService.filter.".$bs_site.".php";
//echo 1;
/*
echo "pcode:".$pcode."<br>";
echo "pname:".$pname."<br>";
echo "price:".$price."<br>";

echo "prod_img_src:".$prod_img_src."<br>";
echo "option:";
print_r($option);
echo "<br>";
echo "prod_desc_prod:<br>".$prod_desc_prod."<br>";
//;
exit;
*/

$db = new Database;

$sql = "select * from shop_buyingservice_info order by regdate desc limit 1 ";

$db->query ($sql);

if($db->total){
	$db->fetch();
	if($clearance_type == '9'){
		$exchange_rate = 1; // 환율 정보 최근 환율 정보를 가져옴
		$bs_basic_air_shipping = 0; // 기본 1파운드 항공 운송료  최근 정보를 가져옴
		$bs_add_air_shipping = 0; // 추가 1파운드 항공 운송료 최근정보를 가져옴
	}else{
		$exchange_rate = $db->dt[exchange_rate];
		$bs_basic_air_shipping = $db->dt[bs_basic_air_shipping];
		$bs_add_air_shipping = $db->dt[bs_add_air_shipping];
	}
	
	
	if($clearance_type == '1' && $clearance_type == '9'){ // 통관타입이 1 : 목록통관일경우 관세/부가세, 통관수수료 면제
		$bs_duty_rate = 0;
		$clearance_fee = 0;
		$bs_supertax_rate = 0;
	}else{
		$bs_duty_rate = $db->dt[bs_duty];
		$clearance_fee = $db->dt[clearance_fee];
		$bs_supertax_rate = $db->dt[bs_supertax_rate];
	}
}

if($bs_fee_rate == ""){
	$bs_fee_rate = "15";
}


if($bs_air_wt <= 1){
	$bs_air_shipping = $bs_basic_air_shipping;
}else{
	$bs_air_shipping = $bs_basic_air_shipping + ($bs_add_air_shipping * ($bs_air_wt - 1));
}

if($listprice == ""){
	$listprice = $price;
}
$price = str_replace(",","",$price);
$bs_duty_basis = ($price+$bs_air_shipping)*$exchange_rate;
$bs_duty = round($bs_duty_basis*$bs_duty_rate/100,-1);
$bs_supertax = round(($bs_duty_basis+$bs_duty)*$bs_supertax_rate/100,-1);

$buyingservice_listprice = round(($listprice+$bs_air_shipping)*$exchange_rate+$bs_duty+$bs_supertax+$clearance_fee,0);
$buyingservice_coprice = round(($price+$bs_air_shipping)*$exchange_rate+$bs_duty+$bs_supertax+$clearance_fee,0);
$bs_list_fee = round($buyingservice_listprice*$bs_fee_rate/100,-1);
$bs_fee = round($buyingservice_coprice*$bs_fee_rate/100,-1);

$bs_info["orgin_price"] = $price;
$bs_info["exchange_rate"] = $exchange_rate;
$bs_info["air_wt"] = $bs_air_wt;
$bs_info["air_shipping"] = $bs_air_shipping;
$bs_info["duty_rate"] = $bs_duty_rate;
$bs_info["clearance_fee"] = $clearance_fee;
$bs_info["buyingservice_coprice"] = $buyingservice_coprice;


$mstring = "<html>
<body>
<div id='bs_url' >$bs_url</div>
<div id='xml_url' >$xml_url</div>
<div id='pcode' >$pcode</div>
pname : <div id='pname' >$pname</div>
prod_img_src : <div id='prod_img_src' >$prod_img_src</div>
<br>
<img src='$prod_img_src'>
<div id='bs_orgin_coprice' >$price</div>

<div id='prod_img_text' >$prod_img_text</div>
<div id='prod_desc_prod' >$prod_desc_prod</div>
<div id='bs_air_shipping' >$bs_air_shipping</div>
<div id='clearance_fee' >$clearance_fee</div>
<div id='bs_duty' >".($bs_duty+$bs_supertax)."</div>
<div id='buyingservice_coprice' >".($buyingservice_coprice)."</div>
<div id='bs_fee' >".($bs_fee)."</div>
<div id='listprice' >".(round($buyingservice_listprice+$bs_list_fee,-1))."</div>
<div id='sellprice' >".(round($buyingservice_coprice+$bs_fee,-1))."</div>
<div id='coprice' >".(round($buyingservice_coprice,-1))."</div>
<div id='stock_bool' >".(!$stock_bool ? "soldout":"")."</div>

</body>
<script language='javascript' src='../js/jquery-1.7.1.min.js'></script>
<script language='javascript' src='../_language/language.php'></script>
<script language='javascript'>
	var bobj ;
	if(parent.opener.document.forms['product_input'].pname.value.length > 0){
		alert(language_data['buyingService.act.php']['A'][language]);
		//'이미 상품정보가 입력되었습니다.'
		//exit;
	
	}else{
	
	
	
	insertBuyingServiceInfo(parent.opener.document.forms['product_input'].pname, document.getElementById('pname').innerHTML);
	insertBuyingServiceInfo(parent.opener.document.forms['product_input'].pcode, document.getElementById('pcode').innerHTML);
	insertBuyingServiceInfo(parent.opener.document.forms['product_input'].stock, 999999);
	insertBuyingServiceInfo(parent.opener.document.forms['product_input'].safestock, 10);	
//	insertBuyingServiceInfo(parent.opener.document.forms['product_input'].buyingservice_coprice, document.getElementById('buyingservice_coprice').innerHTML);
	
	
	insertBuyingServiceInfo(parent.opener.document.forms['product_input'].orgin_price, document.getElementById('bs_orgin_coprice').innerHTML);
	insertBuyingServiceInfo(parent.opener.document.forms['product_input'].exchange_rate, '".$exchange_rate."');
	insertBuyingServiceInfo(parent.opener.document.forms['product_input'].air_wt, parent.document.getElementById('bs_air_wt').value);
	insertBuyingServiceInfo(parent.opener.document.forms['product_input'].air_shipping, document.getElementById('bs_air_shipping').innerHTML);
	insertBuyingServiceInfo(parent.opener.document.forms['product_input'].duty, document.getElementById('bs_duty').innerHTML);
	insertBuyingServiceInfo(parent.opener.document.forms['product_input'].clearance_fee, document.getElementById('clearance_fee').innerHTML);
	insertBuyingServiceInfo(parent.opener.document.forms['product_input'].bs_fee, document.getElementById('bs_fee').innerHTML);
	insertBuyingServiceInfo(parent.opener.document.forms['product_input'].bs_fee_rate, '".$bs_fee_rate."');
	insertBuyingServiceInfo(parent.opener.document.forms['product_input'].bs_goods_url, '".$bs_url."');
	insertBuyingServiceInfo(parent.opener.document.forms['product_input'].bs_site, '".$bs_site."');
	
	
	
	insertBuyingServiceInfo(parent.opener.document.forms['product_input'].listprice, document.getElementById('listprice').innerHTML);
	insertBuyingServiceInfo(parent.opener.document.forms['product_input'].sellprice, document.getElementById('sellprice').innerHTML);
	insertBuyingServiceInfo(parent.opener.document.forms['product_input'].coprice, document.getElementById('coprice').innerHTML);
	//insertBuyingServiceInfo(parent.opener.document.forms['product_input'].shotinfo, document.getElementById('prod_desc_prod').innerHTML);
	//alert(document.getElementById('prod_desc_prod').innerHTML);
	//insertBuyingServiceInfo(parent.opener.document.forms['product_input'].basicinfo, '111');
	//insertBuyingServiceInfo(parent.opener.document.forms['product_input'].content, '111');

	insertBuyingServiceInfo(parent.opener.document.forms['product_input'].basicinfo, document.getElementById('prod_desc_prod').innerHTML);
	
	//insertBuyingServiceInfo(parent.opener.document.forms['product_input'].content, document.getElementById('prod_desc_prod').innerHTML);
	
	insertBuyingServiceInfo(parent.opener.document.forms['product_input'].bimg_text, document.getElementById('prod_img_src').innerHTML);
	
	parent.opener.document.getElementById('clearance_type_".$clearance_type."').checked = true;
	
	parent.opener.document.getElementById('chimg').src = document.getElementById('prod_img_src').innerHTML;
	parent.opener.document.getElementById('img_url_copy').checked = true;

	//parent.document.getElementById('bs_orgin_price').value = document.getElementById('bs_orgin_coprice').innerHTML;
	//parent.document.getElementById('bs_air_shipping').value = document.getElementById('bs_air_shipping').innerHTML;
	//parent.document.getElementById('clearance_fee').value = document.getElementById('clearance_fee').innerHTML;
	
	parent.opener.Content_Input();	
	parent.opener.Init(parent.opener.document.product_input);
	
	";
	
	if(is_array($options[1])){
$mstring .= "parent.opener.copyOptions('options_input');";		
	}
	if(is_array($option2)){
$mstring .= "parent.opener.copyOptions('options_input');";		
	}
	if(is_array($options_price_stock)){
		$mstring .= "
		var stock_price_option_div = new Array();		
		var stock_price_option_price = new Array();
		var stock_price_option_stock = new Array();
		var stock_price_option_orgin_price = new Array();
		";
		//echo count($options_price_stock[0][details]);
		for($i=0; $i < count($options_price_stock[option_div]) ;$i++){
			//echo "<b>".$i."</b><br>";
			/*
			echo "bs_air_shipping:".$bs_air_shipping."<br>";
			echo "exchange_rate:".$exchange_rate."<br>";
			echo "bs_duty:".$bs_duty."<br>";
			echo "bs_supertax:".$bs_supertax."<br>";
			echo "clearance_fee:".$clearance_fee."<br>";
			echo "bs_fee_rate:".$bs_fee_rate."<br>";
			
			*/
			
			//echo $options_price_stock[price][$i];
			//exit;
			$buyingservice_listprice = round(($options_price_stock[price][$i]+$bs_air_shipping)*$exchange_rate+$bs_duty+$bs_supertax+$clearance_fee,0);
			$buyingservice_coprice = round(($options_price_stock[price][$i]+$bs_air_shipping)*$exchange_rate+$bs_duty+$bs_supertax+$clearance_fee,0);
			$bs_list_fee = round($buyingservice_listprice*$bs_fee_rate/100,-1);
			$bs_fee = round($buyingservice_coprice*$bs_fee_rate/100,-1);

			
			$mstring .= "stock_price_option_div[".($i)."] = '".$options_price_stock[option_div][$i]."';\n";
			$mstring .= "stock_price_option_price[".($i)."] = '".(number_format($buyingservice_coprice + $bs_fee,0))."';\n";
			$mstring .= "stock_price_option_stock[".($i)."] = '".$options_price_stock[option_stock][$i]."';\n";
			$mstring .= "stock_price_option_orgin_price[".($i)."] = '".$options_price_stock[etc1][$i]."';\n";
			if($i < count($options_price_stock[option_div])-1){
				
				$mstring .= "parent.opener.copyOptions('options_basic_item_input_0');\n";
			}
		}
		
		$mstring .= "
		var price_option_names = parent.opener.document.all.options_price_stock_option_div;
		var price_option_price = parent.opener.document.all.options_price_stock_option_price;
		var price_option_code = parent.opener.document.all.options_price_stock_option_code;
		var price_option_stock = parent.opener.document.all.options_price_stock_option_stock;
		//alert(price_option_names.length);
		
		parent.opener.document.getElementById('options_price_stock_option_name').value = '".$options_price_stock[option_name]."';
		parent.opener.document.getElementById('options_price_stock_option_use').checked = true
		
		// 부모창???�는 hidden object ??건너?�고 1부???�작?�다. 
		for(i=1;i < price_option_names.length;i++){	
		//alert(i+':::::'+stock_price_option_div[i]);
			price_option_names[i].style.background = '#fedaa0';		
			price_option_names[i].value = stock_price_option_div[i-1];
			
			price_option_price[i].style.background = '#fedaa0';		
			price_option_price[i].value = stock_price_option_price[i-1];
			
			price_option_stock[i].style.background = '#fedaa0';		
			price_option_stock[i].value = stock_price_option_stock[i-1];
			
			price_option_code[i].style.background = '#fedaa0';		
			price_option_code[i].value = stock_price_option_orgin_price[i-1];
			
		}
		
		";
	}
	
	if(is_array($options[0])){
		$mstring .= "	
		
		var option_names = parent.opener.document.all.option_name;
		var option_uses = parent.opener.document.all.options_option_use;
		var option = new Array();
		var option_etc = new Array();
		var option_code = new Array();
		option_names[1].value = '".$options[0][option_name]."';
		option_names[1].style.background = '#fedaa0';		
		option_uses[1].checked = true;
		\n";
		
		
		for($i=0;$i < count($options[0][details]);$i++){
			$mstring .= "option[".($i)."] = '".$options[0][details][$i][option_div]."';\n";
			//$mstring .= "alert(option[".($i)."])";
			$mstring .= "option_etc[".($i)."] = '".$options[0][details][$i][etc1]."';\n";
			$mstring .= "option_code[".($i)."] = '".$options[0][details][$i][option_code]."';\n";
			//$mstring .="alert('".$option[$i]."');";
			if($i < count($options[0][details])-1){
				$mstring .= "parent.opener.copyOptions('options_item_input_0');";
			}
		}
		
		$mstring .= "	
		//alert(option.length);
		var option_divs = parent.opener.document.all.options_item_option_div_0;
		var option_codes = parent.opener.document.all.options_item_option_code_0;
		var option_etcs = parent.opener.document.all.options_item_option_etc1_0;
		//alert(1);
		// 0 번째 option_divs[0] 값�? hidden 값이므�?1번째 부???�는??
		//alert(option_etcs.length);
		for(i=1;i < option_divs.length;i++){	
			//alert(option_divs[i].outerHTML);
			//alert('option['+(i-1)+']'+option[i-1]);
			option_divs[i].style.background = '#fedaa0';		
			option_divs[i].value = option[i-1];
			option_codes[i].style.background = '#fedaa0';		
			option_codes[i].value = option_code[i-1];
			option_etcs[i].style.background = '#fedaa0';		
			option_etcs[i].value = option_etc[i-1];
			
		}
		
		";
	}

	if(is_array($options[1])){
		$mstring .= "	
		
		var option_names = parent.opener.document.all.option_name;
		var option_uses = parent.opener.document.all.options_option_use;
		var option2 = new Array();
		//var option2_etc = new Array();
		var option2_code = new Array();
		option_names[2].value = '".$options[1][option_name]."';
		option_names[2].style.background = '#fedaa0';		
		option_uses[2].checked = true;
		\n";
		
		
		for($i=0;$i < count($options[1][details]);$i++){
			$mstring .= "option2[".($i)."] = '".$options[1][details][$i][option_div]."';\n";
			//$mstring .= "option2_etc[".($i)."] = '".$options[1][details][$i][etc1]."';\n";
			$mstring .= "option2_code[".($i)."] = '".$options[1][details][$i][option_code]."';\n";
			if($i < count($options[1][details])-1){
				$mstring .= "parent.opener.copyOptions('options_item_input_1');\n";
			}
		}
		
		$mstring .= "	
		//alert(option.length);
		var option_divs2 = parent.opener.document.all.options_item_option_div_1;
		var option_codes2 = parent.opener.document.all.options_item_option_code_1;
		//var option_etcs2 = parent.opener.document.all.options_item_option_etc1_1;
		//alert(option_divs2.length+'::::'+option_etcs2.length);
		for(i=0;i < option_divs2.length;i++){		
			option_divs2[i].style.background = '#fedaa0';		
			option_divs2[i].value = option2[i-1];
			option_codes2[i].style.background = '#fedaa0';		
			option_codes2[i].value = option2_code[i-1];
			//option_etcs2[i].style.background = '#fedaa0';		
			//option_etcs2[i].value = option2_etc[i-1];
		}
	
		";
	}

	if(is_array($option)){
		$mstring .= "
		var option_names = parent.opener.document.all.option_name;
		var option_uses = parent.opener.document.all.options_option_use;
		var option = new Array();
		option_names[1].value = '".$option[0]."';
		option_names[1].style.background = '#fedaa0';		
		option_uses[1].checked = true;
		\n";
		
		
		for($i=1;$i < count($option);$i++){
			$mstring .= "option[".($i-1)."] = '".$option[$i]."';\n";
			//$mstring .="alert('".$option[$i]."');";
			if($i < count($option)-1){
				$mstring .= "parent.opener.copyOptions('options_item_input_0');";
			}
		}
		
		$mstring .= "	
		//alert(option.length);
		
		//var option_divs = parent.opener.document.all.options_detail_option_div_0;
		//alert($('.options_detail_option_div_0'));
		//var option_divs = $('.options_detail_option_div_0');
		
		//var option_codes = parent.opener.document.all.options_item_option_code_0;
		//alert(1);
		// 0 번째 option_divs[0] 값�? hidden 값이므�?1번째 부???�는??
		
		//alert(option_divs.length);
		$('.options_detail_option_div_0').each(function(){
			alert(1);
			$(this).css('background','#fedaa0');
		});


		/*
		for(i=1;i < option_divs.length;i++){	
			//alert(option_divs[i].outerHTML);
			option_divs[i].style.background = '#fedaa0';		
			option_divs[i].value = option[i-1];
			//option_codes[i].style.background = '#fedaa0';		
			//option_codes[i].value = option[i-1];
			
		}
		*/
		";
	}
	
	if(is_array($option2)){
		$mstring .= "	
		var option_names = parent.opener.document.all.option_name;
		var option_uses = parent.opener.document.all.options_option_use;
		var option2 = new Array();
		option_names[2].value = '".$option2[0]."';
		option_names[2].style.background = '#fedaa0';		
		option_uses[2].checked = true;
		
		\n";
		
		
		for($i=1;$i < count($option2);$i++){
			$mstring .= "option2[".($i-1)."] = '".$option2[$i]."';\n";
			if($i < count($option2)-1){
				$mstring .= "parent.opener.copyOptions('options_item_input_1');\n";
			}
		}
		
		$mstring .= "	
		//alert(option.length);
		var option_divs2 = parent.opener.document.all.options_item_option_div_1;
		var option_etcs2 = parent.opener.document.all.options_item_option_code_1;
	//alert(option_divs2.length+'::::'+option_etcs2.length);
		for(i=1;i < option_divs2.length;i++){		
			option_divs2[i].style.background = '#fedaa0';		
			option_divs2[i].value = option2[i-1];
			option_etcs2[i].style.background = '#fedaa0';		
			option_etcs2[i].value = option2[i-1];
		}
	
		";
	}
	
$mstring .= "		
	
	}// º¹µ?�?¶§..
	
	
	
	function insertBuyingServiceInfo(obj, value){
		//obj.style.border = '3px solid silver';
		obj.style.background = '#fedaa0';		
		obj.value = value;
		bobj = obj;
		//setTimeout('initBuyingInfoInputBox()',1000);
	}
	
	function initBuyingInfoInputBox(){
		//alert(bobj.style.border);
		bobj.style.border = '1px solid silver';
		bobj.style.background = '#ffffff';
	}



</script>
</html>
";	
	
echo $mstring;


?>
