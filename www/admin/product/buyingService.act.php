<?
header("Content-type: text/html; charset=utf-8");
include "../class/Snoopy.class.php";
include("../../class/database.class");

if(!$bs_url){
	
	$bs_url = "http://www1.bloomingdales.com/catalog/product/index.ognc?ID=293508&CategoryID=18229&PageID=18228*1*24*-1*-1*1";
	$bs_url = "http://shop.nordstrom.com/S/3072924/0~2376779~6008000~6024190~6024268?mediumthumbnail=Y";
	$bs_url = "http://shop.nordstrom.com/S/3063644?refsid=268393&refcat=0%7e2376778%7e2372808%7e2372940%7e2376188&SourceID=1&SlotID=2&origin=coordinating";
	$bs_url = "http://www.csnstores.com/DaVinci-M5501E-DV1592.html";
	
	$bs_url = "http://www.izabel.co.kr/shop/shopdetail.html?branduid=119217&xcode=011&mcode=000&scode=&type=O&search=&sort=order";
	
//	$bs_url = "http://www.gymboree.com/shop/dept_item.jsp?PRODUCT%3C%3Eprd_id=845524446004863&FOLDER%3C%3Efolder_id=2534374306269354&ASSORTMENT%3C%3East_id=1408474395917465&bmUID=1329841625159&productSizeSelected=0&fit_type=";
	
	
	//$bs_url = "http://www.gap.com/browse/product.do?cid=64749&vid=1&pid=495541&scid=495541002";
	$bs_url = "http://oldnavy.gap.com/browse/product.do?cid=77201&vid=1&pid=898253&scid=898253062";
	
	//$bs_url =	"http://www.ralphlauren.com/product/index.jsp?productId=12422770&sProdEvar=Recently Viewed";
	
	
	//$bs_url = "http://www.jcrew.com/girls_category/knitstees/teestops/PRDOVR~65771/65771.jsp";
	$bs_url = "http://www.ralphlauren.com/product/index.jsp?productId=11881421";
	
	
	
	
	
	
	
	
	$bs_url = "http://www.gymboree.com/shop/dept_item.jsp?PRODUCT%3C%3Eprd_id=845524446006017&FOLDER%3C%3Efolder_id=2534374306269293&ASSORTMENT%3C%3East_id=1408474395917465&bmUID=1331651834289&productSizeSelected=0&fit_type=";
	
	$bs_url = "http://www.gap.com/browse/product.do?cid=76755&vid=1&pid=150532&scid=150532012";
	$bs_url = "http://www.bodenusa.com/en-US/Girls-Trousers-Jeans/Sweatpants-Leggings/32394-DBL/Girls-Dark-Blue-Towelling-Sweatpants.html";
	
	$bs_url = "http://www.saksfifthavenue.com/main/ProductDetail.jsp?FOLDER%3C%3Efolder_id=2534374306436187&PRODUCT%3C%3Eprd_id=845524446454799&R=885854620617&P_name=Ralph+Lauren&N=306436187&bmUID=jo_PgJM";

	
	$bs_url = "http://oldnavy.gap.com/browse/product.do?cid=54055&vid=1&pid=896170&scid=896170002";
	$bs_url = "http://oldnavy.gap.com/browse/product.do?cid=77643&vid=1&pid=898237&scid=898237022";
	
	
	
	$bs_url = "http://www.gymboree.com/shop/dept_item.jsp?PRODUCT%3C%3Eprd_id=845524446006097&FOLDER%3C%3Efolder_id=2534374306269292&ASSORTMENT%3C%3East_id=1408474395917465&bmUID=1333132837095&productSizeSelected=0&fit_type=";
	

	$bs_url = "http://www.bodenusa.com/en-US/Womens-Tunics-Kaftans/3_4-Sleeved-Tops/WL634/Womens-Printed-Henley.html";
	$bs_url = "http://www.bodenusa.com/en-US/Girls-Trousers-Jeans/Sweatpants-Leggings/32393-GRY/Girls-Grey-Marl_French-Blue-Stripy-Sweatpants.html";
	//$bs_url = "http://www.bodenusa.com/en-US/Teen-Girls-Skirts/92048-DBL/Teen-Girls-Indigo-Flowerburst-Full-Skirt.html";
	
	
	
	
	
	$bs_url = "http://www.michaelkors.com/store/catalog/prod.jhtml?itemId=prod15140036&parentId=cat34802&masterId=cat102&index=16&cmCat=cat000000cat102cat34802&isEditorial=false";
	$bs_url = "http://www.jcrew.com/womens_category/dresses/strapless/PRDOVR~29286/29286.jsp";
	//$bs_url = "http://www.jcrew.com/womens_category/sleepwear/PRDOVR~33274/33274.jsp";
	
	$bs_url = "http://www.ralphlauren.com/product/index.jsp?productId=12564272";
	
	
	$bs_url = "http://oldnavy.gap.com/browse/product.do?cid=77845&vid=1&pid=898538&scid=898538022";
	$bs_url = "http://www.gap.com/browse/product.do?cid=42976&vid=1&pid=772336&scid=772336032";
	
	
	$bs_url = "http://www.abercrombie.com//webapp/wcs/stores/servlet/ProductDisplay?catalogId=10901&storeId=11203&langId=-1&categoryId=12231&parentCategoryId=12837&topCategoryId=12202&size=&productId=858564&seq=04";
	$bs_url = "http://oldnavy.gap.com/browse/product.do?cid=66822&vid=1&pid=108319&scid=108319012";
	$bs_url = "http://www.gymboree.com/shop/dept_item.jsp?PRODUCT%3C%3Eprd_id=845524446003175&FOLDER%3C%3Efolder_id=2534374304778167&ASSORTMENT%3C%3East_id=1408474395917465&bmUID=1332165696401&productSizeSelected=0&fit_type=";
	$bs_url = "http://www.gap.com/browse/product.do?cid=70551&vid=1&pid=892573&scid=892573022";
	$bs_url = "http://www.gymboree.com/shop/dept_item.jsp?PRODUCT%3C%3Eprd_id=845524446005605&FOLDER%3C%3Efolder_id=2534374306233122&ASSORTMENT%3C%3East_id=1408474395917465&bmUID=1332144131183&productSizeSelected=0&fit_type=";
	$bs_url = "http://www.bodenusa.com/en-US/Baby-Accessories/78059/Baby-7-Pack-Sock-Box.html"; // 이미지 copy 시 에러 확인해봐야 함
	
	$bs_url = "http://www.bodenusa.com/en-US/Girls-Trousers-Jeans/32396/Girls-Jersey-Pedal-Pushers.html";
	$bs_url = "http://www.jcrew.com/browse/single_product_detail.jsp?PRODUCT%3C%3Eprd_id=845524441767684&FOLDER%3C%3Efolder_id=2534374302032654&bmUID=1334623855116";
	$bs_url = "http://www.bodenusa.com/en-US/Girls-Tops-T-shirts/31559/Girls-Animal-Print-T-shirt.html";
	$bs_url = "http://www.jcrew.com/womens_category/swim/solids/PRDOVR~21554/21554.jsp";
	$bs_url = "http://www.luxgirl.com/shop/shopdetail.html?branduid=17063&special=1";
	$bs_url = "http://www.gymboree.com/shop/dept_item.jsp?PRODUCT%3C%3Eprd_id=845524446005605&FOLDER%3C%3Efolder_id=2534374306233122&ASSORTMENT%3C%3East_id=1408474395917465&bmUID=1332144131183&productSizeSelected=0&fit_type=";
	
	$bs_url = "http://www.gap.com/browse/product.do?cid=78098&vid=1&pid=149989&scid=149989002";
	$bs_url = "http://oldnavy.gap.com/browse/product.do?cid=49426&vid=1&pid=897646&scid=897646002";
	//청성이 한테 이야기 할꺼
	
	$bs_url = "http://www.zappos.com/product/7830667/color/604";
	$bs_url = "http://www.gymboree.com/shop/dept_item.jsp?PRODUCT%3C%3Eprd_id=845524446002207&FOLDER%3C%3Efolder_id=2534374305348871&ASSORTMENT%3C%3East_id=1408474395917465&bmUID=1332160664868&productSizeSelected=0&fit_type=  ";

	$bs_url = "http://www.gymboree.com/shop/dept_item.jsp?PRODUCT%3C%3Eprd_id=845524446008462&FOLDER%3C%3Efolder_id=2534374303316537&ASSORTMENT%3C%3East_id=1408474395917465&bmUID=1338450460230&productSizeSelected=0&fit_type=";
	$bs_url = "http://www.saksfifthavenue.com/main/ProductDetail.jsp?FOLDER%3C%3Efolder_id=2534374306589790&PRODUCT%3C%3Eprd_id=845524446477361&R=8001470699697&P_name=Naturino&N=306589790&bmUID=jtfz5vz";

	
	
	$bs_url = "http://www.gymboree.com/shop/dept_item.jsp?PRODUCT%3C%3Eprd_id=845524446008481&FOLDER%3C%3Efolder_id=2534374302778163&ASSORTMENT%3C%3East_id=1408474395917465&bmUID=1338451249263&productSizeSelected=0&fit_type=";

	$bs_url = "http://www.hannaandersson.com/style.asp?from=SC%7c4%7c2%7c156%7c45%7c9%7c%7c";
	$bs_url = "http://www.janieandjack.com/shop/dept_item.jsp?PRODUCT%3C%3Eprd_id=845524443468958&FOLDER%3C%3Efolder_id=2534374303716563&bmUID=1334318960767&productSizeSelected=0";

	//
	//http://oldnavy.gap.com/browse/product.do?cid=41571&vid=1&pid=897611&scid=897611002
	//http://oldnavy.gap.com/browse/product.do?cid=16749&vid=1&pid=463469&scid=463469002
	$bs_url = "http://www.gap.com/browse/product.do?cid=83394&vid=1&pid=149291&scid=149291012";
	$bs_url = "http://www.mstyleshop.co.kr/goodview.php?good_code=00350003261";
}

if(!$bs_site){
	$bs_site = "csnstores";
	
	$bs_site = "izabel";
	
	//$bs_site = "gap";
	
	$bs_site = "michaelkors";
	$bs_site = "abercrombie";
	
	
	
	$bs_site = "luxgirl";
	$bs_site = "zappos";
	$bs_site = "gymboree";
	
	
	
	$bs_site = "disneystore";
	$bs_site = "ae";
	$bs_site = "ralphlauren";
	

	
	$bs_site = "saksfifthavenue";
	

	
	
	
	$bs_site = "gymboree";
	
	$bs_site = "hannaandersson";
	
	$bs_site = "oldnavy";
	
	
	$bs_site = "janieandjack";
	
	$bs_site = "jcrew";
	
	$bs_site = "gap";
	$bs_site = "mstyleshop";

}


include "buyingService.filter.".$bs_site.".php";
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
<div id='bs_url' >$bs_url</div><br>
<div id='xml_url' >$xml_url</div><br>
<div id='pcode' >$pcode</div><br>
pname : <div id='pname' >$pname</div><br>
price : <div id='bs_orgin_coprice' >$price</div><br>
make_company : <div id='make_company' >$make_company</div>
orgin : <div id='make_company' >$orgin</div>
prod_img_src : <div id='prod_img_src' >$prod_img_src</div>
<br>
<img src='$prod_img_src'>



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
	if(parent.opener.document.forms['product_input'].pname.value.length > 0 || false){
		alert(language_data['buyingService.act.php']['A'][language]);
		//'이미 상품정보가 입력되었습니다.'
		//exit;
	
	}else{
	
	/*
	
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
*/
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
