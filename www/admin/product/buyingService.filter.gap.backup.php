<?

$results = "";
$soldout_message = "";

$aryURL = explode("?",$bs_url);
parse_str($aryURL[1], $url_info);	
//parse_str($bs_url);
//$pid = $url_info[pid];
$scid = $url_info[scid];
$bs_url_tmp = "http://www.gap.com/browse/productData.do?pid=".$url_info[pid]."&vid=1&scid=".$url_info[scid]."&actFltr=false&locale=en_US&internationalShippingCurrencyCode=&internationalShippingCountryCode=us&globalShippingCountryCode=us";
//echo $bs_url_tmp;
if(true){
//echo $bs_url_tmp;
$snoopy = new Snoopy;
$snoopy->fetch($bs_url_tmp);
$results = $snoopy->results;
}else{
	//include_once("../lib/imageResize.lib.php");

	$cookie_nm = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_cookies/cookie_".$bs_site."_".session_id().".txt"; 
	
		//echo $cookie_nm;
		//실제 로그인이 이루어지는 Curl 입니다.
		//echo $bs_site_domain;
		

		$ch = curl_init(); 
		curl_setopt ($ch, CURLOPT_URL,"http://www.gap.com");                      // 접속할 URL 주소 
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);   // SSL 관련 설정 입니다.
		curl_setopt ($ch, CURLOPT_SSLVERSION,1);                  // 이부분 또한 윗 설정과 같이 SSL 관련 부분입니다.
		curl_setopt ($ch, CURLOPT_HEADER, 0);        // 페이지 상단에 헤더값 노출 유뮤 입니다. 0일경우 노출하지 않습니다.
		curl_setopt ($ch, CURLOPT_POST, 1);           // 값 전송을 POST값을 전송 하겠다는 선언 입니다. 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $login_data);     // 전송할 POST 값입니다.
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);     // 설정 파일에 쿠키 데이터를 굽습니다.
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);    // 설정 파일의 쿠키 데이터를 페이지에 넣습니다.
		curl_setopt ($ch, CURLOPT_TIMEOUT, 30); 
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
		 curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		$results = curl_exec ($ch); 
		//echo $results;

		
			//print_r($_POST);
			//echo $list_url;
			
			//$list_url = "http://www.gap.com/browse/categoryProductGrid.do?cid=".$url_info[cid]."&actFltr=false&sortBy=0&pageID=0&globalShippingCountryCode=us";
			//echo $list_url."<br>";

		curl_setopt ($ch, CURLOPT_URL,$bs_url_tmp);   // 로그인후 이동할 페이지 입니다.
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm); 
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm); 
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		$results = curl_exec ($ch); 
		curl_close ($ch); 
		
		
}


$soldout_msg = "";
preg_match_all("|out of stock|U",$results,$soldout_msg, PREG_PATTERN_ORDER);
if(trim($soldout_msg[0][0]) == "out of stock"){
	$soldout_message = trim($soldout_msg[0][0]);
}

preg_match_all("|new objP.StyleColor\(\"".$scid."\",\"(.*)\",.*,.*,(.*),.*\"\);|U",$results,$stock_infos, PREG_PATTERN_ORDER);
//print_r($stock_infos);
//echo $stock_infos[2][0];
if($stock_infos[2][0] == "false"){
	$soldout_message = "out of stock";
}
//echo "'".$soldout_message."'" ;
//exit;
// 연두색 cn4343596.jpg
//echo $pid;
//$datas = split("\n",$results);
//$productinfo_inner_div_cnt = 0;

//<span class="priceDisplay"><span class="priceDisplayStrike">$39.95</span><span class="brandBreak">&#160;</span><span class="priceDisplaySale">$29.99</span></span>

//print_r($priceDisplayStrike);
//exit;

//print_r($priceDisplaySale);
//exit;
//echo $results;
//exit;
//imageObj.reg="http://www.ralphlauren.com/graphics/product_images/pPOLO2-11733188_lifestyle_v360x480.jpg"; 
//preg_match_all("|'.*': '(.*)'|U",$results,$prod_img, PREG_PATTERN_ORDER);
//echo "arrayVariantStyleColors[(.*)].*\(\"".$scid."\"";
//preg_match_all("|objP.arrayVariantStyles\[\"1\"\].arrayVariantStyleColors\[(.*)\] = new objP.StyleColor\(\"".$scid."\",\".*\^,\^\"\);|U",$results,$_prod_img_key, PREG_PATTERN_ORDER);

//preg_match_all("|objP.arrayVariantStyles\[\"1\"\].arrayVariantStyleColors\[(.*)\] = new objP.StyleColor\(\"".$scid."\",\".*\);|U",$results,$_prod_img_key, PREG_PATTERN_ORDER);
//echo "|objP\.arrayVariantStyles\[\"1\"\]\.arrayVariantStyleColors\[(.*)\] = new objP\.StyleColor\(\"".$scid."\".*\);|U";

$result = "";
$gap_goods_info = "";
$pname = "";
$activeColor = "";
$result = shell_exec("phantomjs  --load-images=no ".$_SERVER["DOCUMENT_ROOT"]."/admin/scrape/gap.js '".$bs_url."'"); 
$gap_goods_info = (array)json_decode($result);

$pname = $gap_goods_info[name];
$activeColor = $gap_goods_info[activeColor];
//$variantStyleColors = $gap_goods_info[variantStyleColors];

if($pname == ""){
	$result = shell_exec("phantomjs  --load-images=no ".$_SERVER["DOCUMENT_ROOT"]."/admin/scrape/gap.js '".$bs_url."'"); 
	$gap_goods_info = (array)json_decode($result);
	$pname = $gap_goods_info[name];
}
//print_r($gap_goods_info);
$gap_stock_infos = "";
$goods_image_info = "";
$variantStyleColors = "";
$variantStyleColor = "";

$gap_stock_infos = (array)$gap_goods_info[stocks];
$goods_image_info = (array)$gap_goods_info[images];
$variantStyleColors = (array)$gap_goods_info[variantStyleColors];
$variantStyleColor = (array)$variantStyleColors[$activeColor];

//print_r($variantStyleColor );
//echo $variantStyleColor[regularPrice];

$price = "";
if($variantStyleColor[regularPrice] != ""){
	$price = trim(str_replace(array("Now","now","$"),"",$variantStyleColor[regularPrice]));
}

if($variantStyleColor[strSalePrice] != ""){
	$price = trim(str_replace(array("Now","now","$"),"",$variantStyleColor[strSalePrice]));
}

if($variantStyleColor[salePrice] != ""){
	$price = trim(str_replace(array("Now","now","$"),"",$variantStyleColor[salePrice]));
}

// 할인 가격이 아닌 메시지인경우 price값으로 변경되는것 막음 12.07.02 bgh
// is_numeric 으로 체크 하던 부분을 $ 값이 들어 가 있는지 판단하는거로 바꿈 2012.09.14
//Buy 2 or more save 6.95 each See All 2012.09.14 앞과 같은 스타일일때 에러남

if($variantStyleColor[strPartialMupMessage] != "" && substr_count($variantStyleColor[strPartialMupMessage],"$")){
	$__price = trim(str_replace(array("Now","now","$"),"",$variantStyleColor[strPartialMupMessage]));
	if(is_numeric($__price)){
		$price = $__price;
		//exit;
	}
}

//$pname = $gap_goods_info[name];
//$gap_goods_info[infoTabs];

//echo "test". $results;
//exit;

//$option = $option_colors[1];
//print_r($option);
//exit;

$option2 = "";
$gap_stock_info = "";
if(is_array($gap_stock_infos)){
	$option2[0] = "사이즈";
	for($i=0, $j=1; $i < count($gap_stock_infos) ; $i++){
		$gap_stock_info = (array)$gap_stock_infos[$i];
		if($gap_stock_info[bStock]){
			if($gap_stock_info[dimension2Name] != ""){
				$option2[$j]= $gap_stock_info[dimension1Name]."-".$gap_stock_info[dimension2Name];
			}else{
				$option2[$j]= $gap_stock_info[dimension1Name];
			}
			$j++;
		}
	}
}
//print_r($option2);

$_prod_info = "";
preg_match_all("|objP\.arrayVariantStyles\[\".*\"\]\.arrayVariantStyleColors\[(.*)\] = new objP\.StyleColor\(\"".$url_info[scid]."\",.*,.*,.*,.*,.*,.*,.*,(.*),(.*),.*,.*,.*,.*,\".*\"\);|U",str_replace(");",");\n",$results),$_prod_info, PREG_PATTERN_ORDER);

//print_r($_prod_info);
$prod_img_key = "";
if($_prod_info[1][0] != ""){
	$prod_img_key = $_prod_info[1][0];
}

if($prod_img_key == ""){
	$prod_img_key = 0;
}
/*
if($price == ""){
	if($_prod_info[2][0] != ""){
		$price = str_replace(array("$","\""),"",$_prod_info[2][0]);
	}
	if($_prod_info[3][0] != ""){
		$sale_price = str_replace(array("$","\""),"",$_prod_info[3][0]);
	}
}
*/
//echo $price;
//echo $prod_img_key;
//print_r($_prod_img_key);
//exit;

$prod_img = "";
preg_match_all("|'VLI': '(.*)','|U",$results,$prod_img, PREG_PATTERN_ORDER);
//print_r($prod_img);
if($scid == ""){
	$prod_img_key = count($prod_img[1])-1;
}

$prod_img_src = "";
$prod_img_src = $goods_image_info[VLI];//$prod_img[1][$prod_img_key];
$goods_detail_images = "";
$goods_detail_images = $prod_img[1];
// 큰이미지로 교체 해달라고 요청해서 변경
//preg_match_all("|Main\^,\^(.*)\|\||U",$results,$goods_detail_images_url, PREG_PATTERN_ORDER);
//print_r($goods_detail_images_url);

$thumb_images_tmp = "";
$thumb_images = "";
preg_match_all("|S': '(.*)','|U",$results,$thumb_images_tmp, PREG_PATTERN_ORDER);
$thumb_images = $thumb_images_tmp[1];
//print_r($thumb_images_tmp);

$option_colors = "";
if($url_info[scid]){
	preg_match_all("|StyleColor\(\"".$url_info[scid]."\",\"(.*)\",|U",$results,$option_colors, PREG_PATTERN_ORDER);
}else{
	preg_match_all("|StyleColor\(\".*\",\"(.*)\",|U",$results,$option_colors, PREG_PATTERN_ORDER);
}
//print_r($option_colors);

$option_key = 0;
$options = "";
if(is_array($option_colors[1])){
	$options[$option_key][option_type] = "9";
	$options[$option_key][option_name] = "색상";
	$options[$option_key][option_kind] = "s";
	$options[$option_key][option_use] = "1";
	
	// 현재 상품의 색상만 분리 
	$options[$option_key][details][0][option_div] = str_replace("Select ","",trim($option_colors[1][$activeColor]));
	$options[$option_key][details][0][price] = "";
	$options[$option_key][details][0][etc1] = "";
	$options[$option_key][details][0][thumb_images] = $thumb_images[$prod_img_key];
	$options[$option_key][details][0][goods_images] = $goods_detail_images[$prod_img_key];
//print_r($options);
	/*
	for($i=0;$i < count($option_colors[1]);$i++){
		$options[$option_key][details][$i][option_div] = str_replace("Select ","",trim($option_colors[1][$i]));
		$options[$option_key][details][$i][price] = "";
		$options[$option_key][details][$i][etc1] = "사이즈:".$relation_size[$i];
		$options[$option_key][details][$i][thumb_images] = $thumb_images[$i];
		$options[$option_key][details][$i][goods_images] = $goods_detail_images[$i];
	}
	*/
	/*
	$option[0] = "COLOR";
	for($i=0; $i < count($option_colors[1]) ; $i++){
		$option[$i+1] = $option_colors[1][$i];
	}
	*/
}

if(false){
		preg_match_all("|SizeInfoSummary\(.*[0-9]\",\"\",\"(.*)\",\"\".*\);|U",$results,$option_sizes, PREG_PATTERN_ORDER);
		//preg_match_all("|(.*)\^,\^[0-9]{4}\|\||U",$option_sizes[1][0],$option_sizes_array, PREG_PATTERN_ORDER);
		//$option2 = $option_sizes_array[1];
		//print_r($option_sizes);
		//echo $option_sizes[1][0]."<br>";
		//$option_sizes_array = preg_split("/\^,\^[0-9]{4}\|\|/",$option_sizes[1][0]);
		$option_sizes_array1 = preg_split("/\|\|/",$option_sizes[1][0]);
		//print_r($option_sizes[1][0]);
		//print_r($option_sizes_array1);
		//exit;
		if(is_array($option_sizes_array1)){
			$option2[0] = "사이즈";
			for($i=0; $i < count($option_sizes_array1) ; $i++){
				$option_sizes_array2 = preg_split("/\^,\^/",$option_sizes_array1[$i]);
				$option2[$i+1] = $option_sizes_array2[0];
			}
		}
}
//print_r($option2);
//exit;
/*
if(count($prod_img_src) > 0){
	$_prod_desc = "<table align=center>";
	$_prod_desc .= "<tr><td align=center><div style='text-align:left;'>".$prod_desc_prod."</div></td></tr>";
	for($i=0;$i < count($prod_img_src);$i++){
		$_prod_desc .= "<tr><td align=center style='padding:20px 0px;'><IMG src=\"".$prod_img_src[$i]."\"></td></tr>";
	}
	$_prod_desc .= "</table>";

	$prod_desc_prod = $_prod_desc;
}
*/
//echo $prod_desc_prod;
//exit;
//$goods_detail_images = $detail_images[1];
//echo $results;

//exit;
//$datas = split("\n",$results);

//$pcode=$pid;


//objP.setFabricContent(objP.arrayFabricContent,"500478^,^Cotton^,^100.0");
$product_addinfo_tmp = "";
$ProductAddInfos_tmp = "";
$__ProductAddInfos_tmp = "";
$fabric_info = "";
preg_match_all("|objP\.setFabricContent\(objP.arrayFabricContent,\"(.*)\"\)|U",$results,$product_addinfo_tmp, PREG_PATTERN_ORDER);
$ProductAddInfos_tmp = split("[||]",$product_addinfo_tmp[1][0]);
//print_r($product_addinfo_tmp);
for($i=0;$i <count($ProductAddInfos_tmp);$i++){
	if($ProductAddInfos_tmp[$i] != ""){
		$__ProductAddInfos_tmp = split("\^,\^",$ProductAddInfos_tmp[$i]);
		
		if($i == 0){
			$fabric_info = $__ProductAddInfos_tmp[2]." % ". $__ProductAddInfos_tmp[1] ."   ";
		}else{
			$fabric_info.= " , ".$__ProductAddInfos_tmp[2]." % ". $__ProductAddInfos_tmp[1] ."   ";
		}
	}
}

$InfoTabInfoBlocks_tmp = "";
$_InfoTabInfoBlocks_tmp = "";
$__InfoTabInfoBlocks_tmp = "";
$___InfoTabInfoBlocks = "";
$InfoTabInfoBlocks = "";
preg_match_all("|objP\.setArrayInfoTabInfoBlocks\(.*\"(.*)\"\)|U",$results,$InfoTabInfoBlocks_tmp, PREG_PATTERN_ORDER);
$_InfoTabInfoBlocks_tmp = $InfoTabInfoBlocks_tmp[1];
//print_r($InfoTabInfoBlocks_tmp)

//print_r($_InfoTabInfoBlocks_tmp);
for($i=0;$i < count($_InfoTabInfoBlocks_tmp);$i++){
	$__InfoTabInfoBlocks_tmp = split("\|\|",$_InfoTabInfoBlocks_tmp[$i]);
	
	for($j=0;$j <count($__InfoTabInfoBlocks_tmp);$j++){
		$___InfoTabInfoBlocks = split("\^,\^",$__InfoTabInfoBlocks_tmp[$j]);
		$InfoTabInfoBlocks[$i][$j] = $___InfoTabInfoBlocks[2]."   ";
	}
}

//print_r($InfoTabInfoBlocks);
//exit;
//preg_match_all("|objProduct".$url_info[pid]." = new ProductStyle.*</span>.*,\"(.*)\",'Color'|U",$results,$pname_array, PREG_PATTERN_ORDER);
//echo "objProduct$pid = new ProductStyle\((.*)\);";
//echo $results;

$pname_array = "";
$ProductStyleInfo = "";
preg_match_all("|objProduct".$url_info[pid]." = new ProductStyle\((.*)\);|U",$results,$pname_array, PREG_PATTERN_ORDER);
$ProductStyleInfo = split(",",str_replace(", "," ",$pname_array[1][0]));


//print_r($pname_array);
define("brandCode",							0);
define("strProductId",							1);
define("strVendorId",							2);
define("strCatalogItemId",					3);
define("strProductType",						4);
define("hasFitAttributeOverlayImages",	5);
define("hasAlternateImage",					6);
define("hasMarketingFlag",					7);
define("hasMarketingCallOut",				8);
define("strMupMessage",					9);
define("strGIDPromoMessage",			10);
define("sizeChartId",							11);
define("hasZoomEnabled",					12);
define("strProductPriceRange",			13);
define("isInStock",								14);
define("isOnSale",								15);
define("isOnClearence",						16);
define("isGiftCard",								17);
define("productClassTypId",				18);
define("hasCrossSell",						19);
define("hasSplitVariants",					20);
define("hasMergeVariants",					21);
define("isImported",							22);
define("intMaxOrderQuantity",				23);
define("intMaxQuantity",						24);
define("strProductStyleName",				25);
define("strStyleColorDisplayName",		26);
define("strAllowableReturnCode",			27);
define("strCareInstructionText",			28);
define("strFlammableWarningText",		29);
define("isHazardousMaterial",				30);
define("isNonGiftWrap",						31);
define("isWaterResistant",					32);
define("intBestSellingScore",				33);
define("intNewnessScore",					34);
define("strTaxExemptCode",				35);
define("strDefaultVariantId",					36);
define("strVendorName",						37);
define("intlShip",									38);
define("objProductStyleImages",			39);
define("arrayInfoTabs",						40);
define("arrayVariantStyles",					41);
define("objCrossSellInfo",					42);
define("arrayFabricContent",				43);
define("objMarketingFlag",					44);
define("objMarketingCallOut",				45);

//print_r($ProductStyleInfo);

$priceDisplayStrike = "";
$priceDisplay = "";
$priceDisplaySale = "";
$sale_price = "";
if($price == ""){
	preg_match_all("|<span class=\"priceDisplayStrike\">(.*)</span>|U",$ProductStyleInfo[strProductPriceRange],$priceDisplayStrike, PREG_PATTERN_ORDER);
	$price = str_replace("$","",$priceDisplayStrike[1]);
	if(is_array($price)){
		preg_match_all("|<span class=\"priceDisplay\">(.*)</span>|U",$ProductStyleInfo[strProductPriceRange],$priceDisplay, PREG_PATTERN_ORDER);
		$price = str_replace("$","",strip_tags($priceDisplay[0][0]));
		//print_r($price);
		//exit;
	}else{
		preg_match_all("|<span class=\"priceDisplaySale\">(.*)</span>|U",$ProductStyleInfo[strProductPriceRange],$priceDisplaySale, PREG_PATTERN_ORDER);
		$sale_price = str_replace("$","",strip_tags($priceDisplaySale[1]));
	}

	if($sale_price > 0){
		$listprice = $price;
		$price = $sale_price;
	}
}
/*
if(trim($pname) == ""){
	for($i=$pname_start_line;$i < $pname_end_line;$i++){
		$pname_text .= $datas[$i];
	}
	//echo $pname_text;
	//exit;
	$pname_text = str_replace(array("<div id = \"longDescDiv\" style=\"margin-bottom:10px\">","</div>"),"",$pname_text);
	$pname = strip_tags($pname_text);
}
*/

if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}


//echo $prod_desc_prod;
//exit;
//$prod_desc_prod = str_replace(array("<div id = \"longDescDiv\" style=\"margin-bottom:10px\">","</div>"),"",$prod_desc_prod);
//$prod_desc_prod = strip_tags($prod_desc_prod);

$_prod_desc = "";
$prod_desc_prod = "";
if(count($goods_detail_images) > 0){
	$_prod_desc = "<table align=center>";
	$_prod_desc .= "<tr><td align=center><div style='text-align:left;'><b>fabric & care</b></div></td></tr>";
	$_prod_desc .= "<tr><td align=center>
									<div style='text-align:left;'>";
			if($fabric_info != ""){
			$_prod_desc .=  "<li>".str_replace("\"","",$fabric_info)."";
			}
			$_prod_desc .=  "
									<li>".str_replace("\"","",$ProductStyleInfo[strCareInstructionText])."
									<li>".(str_replace("\"","",$ProductStyleInfo[isImported]) ? "Imported":"Made in USA")."
									</div>
									</td>
							</tr>";

	for($i=0 ; $i < count($InfoTabInfoBlocks);$i++){
	$_prod_desc .= "<tr><td align=center><div style='text-align:left;'><b>".($i == 0 ? "overview":"fit & sizing")."</b></div></td></tr>";
	$_prod_desc .= "<tr><td align=center><div style='text-align:left;'>";
	if(is_array($InfoTabInfoBlocks[$i])){
		for($j=0 ; $j < count($InfoTabInfoBlocks[$i]);$j++){
		$_prod_desc .= "<li>".$InfoTabInfoBlocks[$i][$j]."";
		}
	}
	$_prod_desc .= "</div></td></tr>";
	}
	/*
	for($i=0;$i < count($goods_detail_images[$prod_img_key]);$i++){
		$_prod_desc .= "<tr><td align=center style='padding:20px 0px;'><IMG src=\"".$goods_detail_images[$i]."\"></td></tr>";
	}
	*/
	$_prod_desc .= "<tr><td align=center style='padding:20px 0px;'><IMG src=\"".$prod_img_src."\"></td></tr>";
	
	$_prod_desc .= "</table>";

	$prod_desc_prod = $_prod_desc;
}

//echo $prod_desc_start_line.":::".$prod_desc_end_line;
//exit;
/*
for($i=$option_start_line;$i < $option_end_line;$i++){
	$options_str .= $datas[$i];
	
}
*/
//echo "option :".$option_start_line." ".$option_end_line."<br>";
//echo $options_str;
//exit;
/*
if($options_str){
		$options_str = str_replace("\\","",$options_str);
	//	if(substr_count($options_str, "</option") > substr_count($options_str, "\n")){
	//		$options_str = str_replace("</option>","</option>\n",$options_str);
	//	}

	//	echo $options_str;
//exit;
		//preg_match_all("|<img .*src=\"(.*)\".*>|U",$prod_img_text,$img_out, PREG_PATTERN_ORDER);
		preg_match_all("|<option .*>(.*)</option>|U",$options_str,$options, PREG_PATTERN_ORDER);
		
		$_option = $options[1];
		//print_r($options);
		//exit;
		if(substr_count($options_str,"colorDropDowntexts")){
			$option[0] = "Color";
			for($i=0;$i < count($_option);$i++){
				$option[$i+1] = str_replace("Select ","",trim($_option[$i]));
			}
		}else if(substr_count($options_str,"sizeDropDowntexts")){
			$option[0] = "Size";
			for($i=0;$i < count($_option);$i++){
				$option[$i+1] = str_replace("Select ","",trim($_option[$i]));
			}
		}else{
			for($i=0;$i < count($_option);$i++){
				$option[$i] = str_replace("Select ","",trim($_option[$i]));
			}
		}
}	
*/
$goods_desc_copy = 1;

//print_r($option);
if($url_info[scid]){
	$pcode = $bs_site."_".$url_info[scid];
}else{
	$pcode = $bs_site."_".$url_info[pid];
}

?>
