<?
//	<div class="itemheadernew"><div class="prodtitleLG">Polar Bear Shawl Cardigan</div><font class=prodourprice>Price: &#036;135.00</font>
/*
$Tag = curl_init();
curl_setopt( $Tag , CURLOPT_URL , "$bs_url" ); 

ob_start();
curl_exec( $Tag );
curl_close( $Tag );
$results = ob_get_contents();
ob_clean();

echo $results;
$datas = split("\n",$results);
*/

//$bs_url = "http://www.saksfifthavenue.com/main/ProductDetail.jsp?PRODUCT%3C%3Eprd_id=845524446245648&FOLDER%3C%3Efolder_id=282574492712912&ASSORTMENT%3C%3East_id=1408474395222441&bmUID=1260678846169";

$datas = "";
$soldout_message = "";

$snoopy = new Snoopy;
$snoopy->fetch($bs_url);
//print $snoopy->results;
//exit;
$datas = split("\n",$snoopy->results);
$productinfo_inner_div_cnt = 0;

$prod_img_src = "";
$prod_img_text = "";
$pcode = "";
$pcode_out = "";
$pname = "";

$price = "";
$price_out = "";
$prod_desc_start_line = "";
$prod_desc_inner_div_cnt = "";
$prod_desc_end_line = "";
$prod_desc_add_start_line = "";
$prod_desc_add_end_line = "";

$option_start_line = "";
$option_end_line = "";
$option2_start_line = "";
$option2_end_line = "";
for($i=0;$i < count($datas);$i++){
	$data = $datas[$i];
/*	
	if(!$productinfo_start_line && substr_count($data,"<div class=\"productheader\">")){
		$productinfo_start_line = $i;
	}
	
	if($productinfo_start_line && $productinfo_start_line != $i){
		if($productinfo_start_line && substr_count($data,"<div ")){
			$productinfo_inner_div_cnt++;
		}
		
		if($productinfo_start_line && !$productinfo_end_line && substr_count($data,"</div>")){
			$productinfo_inner_div_cnt--;
		}
	}
	
	if(($productinfo_start_line && !$productinfo_end_line) && $productinfo_inner_div_cnt == 0 && substr_count($data,"</div>")){
		$productinfo_end_line = $i;
	}
*/	
	
	if(!$prod_img_src && substr_count($data,"MACYS.pdp.currentProductImg = \"")){
		
		$prod_img_text = trim(str_replace(array("MACYS.pdp.currentProductImg = \"","\";"),"",$datas[$i]));
		$prod_img_text = str_replace("\\","",$prod_img_text);
		$prod_img_src = trim($prod_img_text);
		
		/*
		$prod_img_text = strip_tags($datas[$i],"<img>");
		preg_match_all("|<img .*src=\"(.*)\".*>|U",$prod_img_text,$img_out, PREG_PATTERN_ORDER);
		$prod_img_src = $img_out[1][0];
		*/
	}	
	
	if(!$pcode && substr_count($data,"<li>Web ID")){
		$pcode = $datas[$i];
		
		preg_match_all("|<li>Web ID:(.*)</li>|U",$datas[$i],$pcode_out, PREG_PATTERN_ORDER);
		$pcode = $pcode_out[1][0];
		$pcode = str_replace(array("<li>Web ID:","</li>","<br>"." "),"",$pcode);
		$pcode = trim($pcode);
	}
	
	if(!$pname && substr_count($data,"MACYS.pdp.prodImgDesc = \"")){		
		$pname = strip_tags($datas[$i]);
		$pname = iconv("iso-8859-1","utf-8//TRANSLIT",$pname) ;
		$pname = str_replace(array("MACYS.pdp.prodImgDesc = \"","\";"),"",$pname);
	}
	
	if(!$price && substr_count($data,"<span class=\"productDetailPrice\">")){
		
		//$price = strip_tags($datas[$i]);
		preg_match_all("|<span class=\"productDetailPrice\">(.*)</span>|U",$datas[$i],$price_out, PREG_PATTERN_ORDER);
		
		//print_r($img_out);
		$price = $price_out[1][0];
		$price = str_replace(array("Now","$","&#036;","Sale","Was"),"",$price);
		$price = trim($price);
		
	}

	if( substr_count($data,"<span class=\"productDetailSale\">")){
		
		//$price = strip_tags($datas[$i]);
		preg_match_all("|<span class=\"productDetailSale\">(.*)</span>|U",$datas[$i],$price_out, PREG_PATTERN_ORDER);
		
		//print_r($img_out);
		$price = $price_out[1][0];
		$price = str_replace(array("Now","$","&#036;","Sale","Was","Everyday Value"),"",strip_tags($price));
		$price = trim($price);
		
	}
	
	
	if(!$prod_desc_start_line && substr_count($data,"<div class=\"productDetailLong\">")){
		$prod_desc_start_line = $i;
	}
	
	if($prod_desc_start_line && $prod_desc_start_line != $i){
		if($prod_desc_start_line && substr_count($data,"<div ")){
			$prod_desc_inner_div_cnt++;
		}
		
		if($prod_desc_start_line && !$prod_desc_end_line && substr_count($data,"</div>")){
			$prod_desc_inner_div_cnt--;
		}
	}
	
	if(($prod_desc_start_line && !$prod_desc_end_line) && $prod_desc_inner_div_cnt == 0 && substr_count($data,"</div>")){
		$prod_desc_end_line = $i+1;
	}
	
	if(!$prod_desc_add_start_line && substr_count($data,"<ul class=\"prodInfoList\">")){
		$prod_desc_add_start_line = $i;
	}

	if(($prod_desc_add_start_line && !$prod_desc_add_end_line) && substr_count($data,"</ul>")){
		$prod_desc_add_end_line = $i+1;
	}
	


	if(!$option_start_line && substr_count($data,"<select name=\"size\"")){
		$option_start_line = $i;
	}
	
	if(($option_start_line && !$option_end_line) && substr_count($data,"</select>")){
		$option_end_line = $i+1;
		//echo $i."<br><br>".$data."<br><br>";
	}
	
	if(!$option2_start_line && substr_count($data,"<select name=\"color\"")){
		$option2_start_line = $i;
	}
	
	if(($option2_start_line && !$option2_end_line) && substr_count($data,"</select>")){
		$option2_end_line = $i;
		//echo $i."<br><br>".$data."<br><br>";
	}

	if(!$soldout_message && substr_count($data,"We're sorry. This product is currently unavailable")){		
		$soldout_message = "We're sorry. This product is currently unavailable";
	}
	
	if(!$soldout_message && substr_count($data,"This item is no longer available")){		
		$soldout_message = "This item is no longer available";
	}
}

if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}

$prod_desc_prod = "";
for($i=$prod_desc_start_line;$i < $prod_desc_end_line;$i++){
	$prod_desc_prod .= $datas[$i];
}
for($i=$prod_desc_add_start_line;$i < $prod_desc_add_end_line;$i++){
	$prod_desc_prod .= $datas[$i];
}

//$prod_desc_prod = str_replace("'","\'",$prod_desc_prod);
//$prod_desc_prod = clearUTF($prod_desc_prod);
//setlocale(LC_CTYPE, 'en_AU.utf8');

$prod_desc_prod = iconv("iso-8859-1","utf-8//TRANSLIT",$prod_desc_prod) ;
//$prod_desc_prod = iconv("utf-8","ASCII//TRANSLIT",$prod_desc_prod) ;

$prod_desc_prod = str_replace(array("","","Read More"),"",$prod_desc_prod);
$prod_desc_prod = strip_tags($prod_desc_prod,"<table><tr><td><span><ul><li><img>");

//echo  "appliqués" ;
//$prod_desc_prod = strip_javascript($prod_desc_prod);
//$prod_desc_prod = str_replace(array("Read More"),"",$prod_desc_prod);

$options_str = "";
for($i=$option_start_line;$i < $option_end_line;$i++){
	$options_str .= $datas[$i];	
}

$options2_str = "";
for($i=$option2_start_line;$i < $option2_end_line;$i++){
	$options2_str .= $datas[$i];	
}

//echo $bs_url."<br>";
/*
$__bs_url = split("[?]",$bs_url);
//echo $__bs_url[1];
parse_str($__bs_url[1], $paraminfos);
//print_r($paraminfos);
$pcode = $paraminfos["ID"];
*/
//echo "pcode : ".$pcode;
//exit;
	
//echo "option :".$option_start_line." ".$option_end_line."<br>";
//echo $options_str;
//exit;
$option1_spos = "";
$option1_epos = "";
$options1_str = "";
$options = "";
$_option = "";
$option = "";

$option1_text = "";
$option_size_str = "";

if($options_str){
		$options_str = str_replace("\\","",$options_str);
		//echo strrpos($options_str,"<select  id=\"custom1\"")."::::::::::".strpos($options_str,"</select>");
		$option1_spos = strrpos($options_str,"<select name=\"size\"");
		$option1_epos = strpos($options_str,"</select>");
		$options1_str = substr($options_str,$option1_spos,$option1_epos);
		//$options_str = substr(<select  id="custom1",$options_str
		//preg_match_all("|<img .*src=\"(.*)\".*>|U",$prod_img_text,$img_out, PREG_PATTERN_ORDER);
		//echo $options1_str;
		preg_match_all("|option .*>(.*)<|U",$options1_str,$options, PREG_PATTERN_ORDER);
		//print_r($options);
		
		$_option = $options[1];
		
		
		for($i=0;$i < count($_option);$i++){
			$option[$i] = str_replace("&nbsp;","",trim($_option[$i]));
		}
		//print_r($option);
}else{
		preg_match_all("|<div class=\"pdp_size_txt\">(.*)</div>|U",str_replace(array("\n\r","\n"),"",$snoopy->results),$option1_text, PREG_PATTERN_ORDER);
		
		//print_r($option1_text);
		$option_size_str = trim(strip_tags($option1_text[1][0]));
		if($option_size_str){
			$option[0] = "Select Size";
			$option[1] = $option_size_str;
		}
}

$options2 = "";
$_option2 = "";
$option2 = "";
$option2_text = "";
$option_color_str = "";
if($options2_str){

		
		//$options2_str = substr($options_str,$option1_epos+9,strlen($options_str));
		//echo $options2_str;
		preg_match_all("|<option .*>(.*)</option>|U",$options2_str,$options2, PREG_PATTERN_ORDER);
		//print_r($options2);
		$_option2 = $options2[1];
		
		for($i=0;$i < count($_option2);$i++){
			$option2[$i] = str_replace("Select ","",trim($_option2[$i]));
		}
		//print_r($option2);
}else{
		preg_match_all("|<td>(.*)<input name=\"color\"|U",str_replace(array("\n\r","\n"),"",$snoopy->results),$option2_text, PREG_PATTERN_ORDER);
		//print_r($option2_text);
		$option_color_str = trim(strip_tags($option2_text[1][0]));
		if($option_color_str){
			$option2[0] = "Select Color";
			$option2[1] = $option_color_str;
		}
}	

if(!is_array($option) && is_array($option2)){
	$option = $option2;
	unset($option2);
}
?>