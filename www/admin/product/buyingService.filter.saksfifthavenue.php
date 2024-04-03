<?

$results = "";
$soldout_message = "";

$snoopy = new Snoopy;
$snoopy->agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)";
$snoopy->referer = "http://www.saksfifthavenue.com/";

$snoopy->cookies["TLTHID"] = 'E61587306AC2106A000AA93B300D1D48';
$snoopy->cookies["TLTSID"] = "3BF3A9386ABF106A01518F1811596779";
$snoopy->cookies["s_cc"] = true;
$snoopy->cookies["E4X_CURRENCY"] = "USD";
$snoopy->cookies["JSESSIONID"] = "PH0HSNbpvGFCywGnpXX6HdDBkBT4F8WTwRGQf66NJ1JGZvn4JT1V!-436849081";
$snoopy->cookies["E4X_COUNTRY"] = "US";

/*
TLTHID=E61587306AC2106A000AA93B300D1D48; TLTSID=3BF3A9386ABF106A01518F1811596779; s_cc=true; c_m=undefinedmail.forbiz.co.krmail.forbiz.co.kr; tr_PageRefresh=home%20page~%5B1%5D; s_sq=%5B%5BB%5D%5D; mbox=session#1331390538619-806957#1331394182|check#true#1331392382; SA74=30BXkKyiRjggKzrzr0n0ytNTkuncL42wplUDAKI_aFUO0qQ-OTvffPQ; dfa_cookie=sakscomlive; s_depth=17; s_direct=1; s_v49=no%20value; s_ev50=%5B%5B'Referrers'%2C'1331390539553'%5D%5D; s_vi=[CS]v1|27ADB426051D14AB-4000010C4005843D[CE]; JSESSIONID=dGwJPbyJ5csy2yJVtcrwPSNnJpXdG6kT30cXrRYXrfvryz1GXGJH!-626882933; TS54c0f7=5c8ed2c921a37378b26553315f7e79bef330380c1fb6e9ae4f5b6afd55627810c16aa85c286023f2bbbfb8a160ac0ec5d479dbcd866d39d0b12f6551077623711809f75107601fb840b6a4ae; saksBagNumberOfItems=0; E4X_COUNTRY=US; E4X_CURRENCY=USD; EML1145A=TRUE; sessionID=1331390539534fFm-Mr4ugKgkoWGGwKKCH4FC3rD4gcmCCi1Zm1W8tE3xCO92HnqCCqmn
*/
$snoopy->rawheaders["Pragma"] = "no-cache";
$snoopy->fetch($bs_url);
$results =  $snoopy->results;
//print_r($results);
//echo $result;
$_results = "";
$_results = str_replace("\n","",$results);

$pname_tmp = "";
$pname = "";
preg_match_all("|<meta property=\"og:title\" content=\"(.*)\" />|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
$pname = $pname_tmp[1][0];

$pcode_tmp = "";
$pcode = "";
preg_match_all("|<input type=\"hidden\" name=\"PRODUCT.*prd_id\" value=\"(.*)\">|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
$pcode = $pcode_tmp[1][0];
//print_r($pcode_tmp);

$datas = "";
$datas = split("\n",$results);

$option_start_line = "";
$option_end_line = "";
$price_start_line = "";
$price_end_line = "";

for($i=0;$i < count($datas);$i++){
	$data = $datas[$i];
	if(!$option_start_line && substr_count($data,"<select name=\"ADD_CART_ITEM_ARRAY<>sku_id\"")){
		$option_start_line = $i;
	}
	
	if(($option_start_line && !$option_end_line) && substr_count($data,"</select>")){
		$option_end_line = $i+1;
		//echo $i."<br><br>".$data."<br><br>";
	}
    if(!$price_start_line && substr_count($data,"<span class=\"product-price\"")){
        $price_start_line = $i;
    }
    if(($price_start_line && !$price_end_line) && substr_count($data,"</td>")){
		$price_end_line = $i+1;
	}
}

$price_str = "";
for($i=$price_start_line;$i < $price_end_line;$i++){
	$price_str .= $datas[$i];	
}
$price_str = str_replace(array("$","&#36;","&nbsp;","\t"," ","Was"),"",$price_str);
//echo $price_str;

$price_tmp = "";
$price = "";
preg_match_all("|<spanclass=\"product-price\".*>(.*)</span>|U",$price_str,$price_tmp, PREG_PATTERN_ORDER);
$price = $price_tmp[1][0];
//echo $price;
//print_r($price_tmp);

$saleprice_tmp = "";
$saleprice = "";
preg_match_all("|<spanclass=\"product-sale-price\">(.*)</span>|U",$price_str,$saleprice_tmp, PREG_PATTERN_ORDER);
$saleprice = $saleprice_tmp[1][0];

if($saleprice != ""){
	$price = $saleprice;
}


//기본 이미지
$prod_img_src_tmp = "";
$prod_img_src = "";
$prod_code_tmp = "";
//preg_match_all("|<img .*src=\"(.*)\".*name=\"MainImage0\".*>|U",$results,$prod_img_src_tmp, PREG_PATTERN_ORDER);
//$prod_img_src = $prod_img_src_tmp[1][0];
preg_match_all("|productcode=\"(.*)\"|U",$results,$prod_code_tmp, PREG_PATTERN_ORDER);
$productcode = $prod_code_tmp[1][0];
$prod_img_src = "http://image.s5a.com/is/image/saks/".$productcode."_396x528.jpg";
//print_r($prod_img_src);
//추가 이미지
$goods_detail_images_tmp = "";
preg_match_all("|MainSwatchImageArray0\[.*\]='(.*)';|U",$results,$goods_detail_images_tmp, PREG_PATTERN_ORDER);

$goods_detail_images = "";
$img_name_tmp = "";
$goods_detail_images_name = "";
$prod_desc_tmp = "";
$prod_desc_prod = "";
$_prod_desc = "";
for($i=0 ;$i < count($goods_detail_images_tmp[1]);$i++){
	if($goods_detail_images_tmp[1][$i] != ""){
		$goods_detail_images[$i] = $goods_detail_images_tmp[1][$i];
	}
    preg_match_all("|ImageIndexMap0\[\"(.*)\"\]|U",$results,$img_name_tmp, PREG_PATTERN_ORDER);
    $goods_detail_images_name[$i] = $img_name_tmp[1][$i];
}
//print_r($goods_detail_images_name);
preg_match_all("|<span id=\"api_prod_copy1\".*>(.*)</span>|U",$_results,$prod_desc_tmp, PREG_PATTERN_ORDER);
$prod_desc_prod = $prod_desc_tmp[1][0];
$prod_desc_prod = iconv("iso-8859-1","utf-8//TRANSLIT",$prod_desc_prod) ;

//echo $prod_desc_prod;
//exit;

if(count($goods_detail_images) > 0 && is_array($goods_detail_images)){
	$_prod_desc = "<table align=center>";
	$_prod_desc .= "<tr><td align=center><div style='text-align:left;'>".$prod_desc_prod."</div></td></tr>";
	for($i=0;$i < count($goods_detail_images);$i++){
		$_prod_desc .= "<tr><td align=center style='padding:0px 0px 0px 0px;'><b>".$goods_detail_images_name[$i]."</b></td></tr>";
		$_prod_desc .= "<tr><td align=center style='padding:5px 0px 30px 0px;'><IMG src=\"".$goods_detail_images[$i]."\"></td></tr>";
	}
	$_prod_desc .= "</table>";

	$prod_desc_prod = $_prod_desc;
}else{
    $_prod_desc = "<table align=center>";
	$_prod_desc .= "<tr><td align=center><div style='text-align:left;'>".$prod_desc_prod."</div></td></tr>";
    $_prod_desc .= "<tr><td align=center style='padding:5px 0px 30px 0px;'><IMG src=\"".$prod_img_src."\"></td></tr>";
    $prod_desc_prod = $_prod_desc;
}

if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}

$prod_desc_prod = str_replace(array("","","Read More"),"",$prod_desc_prod);

$options_str = "";
for($i=$option_start_line;$i < $option_end_line;$i++){
	$options_str .= $datas[$i];
	
}

$__bs_url = "";
$paraminfos = "";
//echo $options_str."<br>";
$__bs_url = split("[?]",$bs_url);
parse_str($__bs_url[1], $paraminfos);
$pcode = $paraminfos["PRODUCT<>prd_id"];
$pcode = $bs_site."_".$pcode;
//echo "pcode : ".$pcode;
//exit;
	
//echo "option :".$option_start_line." ".$option_end_line."<br>";
//echo $options_str;
//exit;
//print_r($options_str);
$option1_spos = "";
$option1_epos = "";
$options1_str = "";
$options_tmp = "";
$_option = "";
$option_tmp = "";

if($options_str){
		$options_str = str_replace("\\","",$options_str);
		//echo strrpos($options_str,"<select  id=\"custom1\"")."::::::::::".strpos($options_str,"</select>");
		$option1_spos = strrpos($options_str,"<select  id=\"custom1\"");
		$option1_epos = strpos($options_str,"</select>")+9;
		$options1_str = substr($options_str,$option1_spos,$option1_epos);
		//$options_str = substr(<select  id="custom1",$options_str
		//preg_match_all("|<img .*src=\"(.*)\".*>|U",$prod_img_text,$img_out, PREG_PATTERN_ORDER);
		//echo $options1_str;
		preg_match_all("|option .*>(.*)<|U",$options1_str,$options_tmp, PREG_PATTERN_ORDER);
		//print_r($options);
		
		$_option = $options_tmp[1];
		
		if(substr_count($options1_str,"colorDropDowntexts")){
			$option_tmp[0] = "Color";
			for($i=0;$i < count($_option);$i++){
				$option_tmp[$i+1] = str_replace("&nbsp;"," ",trim($_option[$i]));
			}
		}else{
			for($i=0;$i < count($_option);$i++){
				$option_tmp[$i] = str_replace("&nbsp;"," ",trim($_option[$i]));
			}
		}
		
	
}	
//print_r($option_tmp);


$options = "";
$option = "";
$option_key = 0;
if(substr_count($option_tmp[1],"$")){
    $options[$option_key][option_type] = "9";
    $options[$option_key][option_name] = $option_tmp[0];
    $options[$option_key][option_kind] = "b";
    $options[$option_key][option_use] = "1";
    
    for($i=1;$i < count($option_tmp);$i++){
        $temp = explode("$",$option_tmp[$i]);
    	$options[$option_key][details][$i-1][option_div] = $temp[0];
    	$options[$option_key][details][$i-1][price] = $temp[1];
    	$options[$option_key][details][$i-1][etc1] = $temp[0];
    	//$options[$option_key][details][$i][thumb_images] = $thumb_images[$i];
    	//$options[$option_key][details][$i][goods_images] = $goods_detail_images[$i];
    	
    }
}else{
    $option = $option_tmp;
}

$goods_desc_copy = 1;

?>