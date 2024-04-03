<?
//	<div class="itemheadernew"><div class="prodtitleLG">Polar Bear Shawl Cardigan</div><font class=prodourprice>Price: &#036;135.00</font>
//$bs_url = "http://www.luxgirl.com//shop/shopdetail.html?branduid=15341&xcode=008&mcode=000&scode=&type=O&search=&sort=order";

//카페24 용

$results = "";
$soldout_message = "";

//$base_domain = "http://www.luxgirl.com/";

if(substr_count($bs_url,'http://') > 0){
	$tmp_bs_url = explode('/',$bs_url);
	$base_domain = "http://".$tmp_bs_url[2]."/";
}else{
	$tmp_bs_url = explode('/',$bs_url);
	$base_domain = $tmp_bs_url[0]."/";
}

$Tag = curl_init();

curl_setopt( $Tag , CURLOPT_URL , "$bs_url" ); 

ob_start();
curl_exec( $Tag );
curl_close( $Tag );
$results = ob_get_contents();
ob_clean();

//echo $results;
$datas = "";
$datas = split("\n",$results);

/*
preg_match_all("|<span id='mk_pager'><a href='(.*)'><font class='brandpage'>|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
print_r($pname_tmp);
exit;
*/

/*
$snoopy = new Snoopy;
$snoopy->fetch($bs_url);
print $snoopy->results;
//exit;
$datas = split("\n",$snoopy->results);
*/
$productinfo_inner_div_cnt = 0;

$pname_tmp = "";
$pname = "";
$pcode = "";
$price = "";
preg_match_all("|var _HCmz={PC:\"(.*)\",PN:\"(.*)\",PS:\"(.*)\",PT:\".*\"};|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
$pname = $pname_tmp[2][0];
$pname = iconv("CP949","UTF-8",$pname) ;
$pcode = $pname_tmp[1][0];
$price = $pname_tmp[3][0];

$prod_img_src = "";
$prod_img_text = "";
$img_out = "";
$price_text = "";
$price_out = "";
$prod_desc_prod = "";
$option_start_line = "";
$option_end_line = "";
$option2_start_line = "";
$option2_end_line = "";
for($i=0;$i < count($datas);$i++){
	$data = iconv("CP949","UTF-8",$datas[$i]);
	
	if(!$pcode && substr_count($data,"name=branduid")){
		$pcode = $datas[$i];
		$pcode = str_replace(array("<input type=hidden name=branduid value=\"","\">"),"",$pcode);
		$pcode = trim($pcode);

	}
	
	if(!$prod_img_src && substr_count($data,"class=\"detail_image\"")){
		//$image_path = $datas[$i];
		$prod_img_text = strip_tags($datas[$i],"<img>");
		preg_match_all("|<img .*src=\"(.*)\".*>|U",$prod_img_text,$img_out, PREG_PATTERN_ORDER);
		
		//print_r($img_out);
		$prod_img_src = $base_domain.$img_out[1][0];

		$prod_img_text = $base_domain.$prod_img_src;
		
	}	
	
	if(!$pname && substr_count($data,"<b><font color=#666666><br>")){		
		$pname = strip_tags($datas[$i]);
		//$pname = str_replace(array("\r\n","\t"),"",$pname);
		$pname = iconv("CP949","UTF-8",$pname) ;
	}

	if(!$price && (substr_count($data,"<td width=\"*\" style=\"color:#000;\">".iconv("UTF-8","CP949","판매가")) || substr_count($data,"<span class=\"saleprice\">"))){
		$price_text = $datas[$i+2];
		$price = strip_tags($datas[$i+2]);
		
		//preg_match_all("|<b>(.*)</b>|U",$datas[$i],$price_out, PREG_PATTERN_ORDER);
		
		//print_r($img_out);
		//$price = $price_out[1][0];
		
		$price = str_replace(array("&nbsp;","$", "SALE","var num = new Array();"),"",$price);
		$price = trim($price);
		if(!is_numeric($price)){
			//echo "aaa";
			preg_match_all("|<span.*class=\"style2\".*>(.*)<script.*|U",$price_text,$price_out, PREG_PATTERN_ORDER);
			//print_r( $price_out);
			$price = $price_out[1][0];
		}
		//echo $price;
	}
	
	if(!$prod_desc_prod && substr_count($data,"id=\"malltb_video_player\"")){		
		$prod_desc_prod = strip_tags($datas[$i],"<img>");
		//$prod_desc_prod = str_replace(array("\r\n","\t"),"",$prod_desc_prod);
		$prod_desc_prod = iconv("CP949","UTF-8",$prod_desc_prod) ;
	}

	/*
	if(!$prod_desc_start_line && substr_count($data,"id=\"malltb_video_player\"")){
		$prod_desc_start_line = $i-2;
	}
	
	if($prod_desc_start_line && $prod_desc_start_line != $i){
		if($prod_desc_start_line && substr_count($data,"<div ")){
			$prod_desc_inner_div_cnt++;
		}
		7000change_price=function(temptemp2temp3){
		if($prod_desc_start_line && !$prod_desc_end_line && substr_count($data,"</div>")){
			$prod_desc_inner_div_cnt--;
		}
	}
	
	if(($prod_desc_start_line && !$prod_desc_end_line) && $prod_desc_inner_div_cnt == 0 && substr_count($data,"</div>")){
		$prod_desc_end_line = $i+1;
	}
	*/
	
	if(!$option_start_line && substr_count($data,"<select id=\"optionlist_0")){
		$option_start_line = $i-3;
	}
	
	if(($option_start_line && !$option_end_line) && substr_count($data,"</select>")){
		$option_end_line = $i+3;
		//echo $i."<br><br>".$data."<br><br>";
	}
	
	if(!$option2_start_line && substr_count($data,"<select id=\"optionlist_1\"")){
		$option2_start_line = $i-3;
	}
	
	if(($option2_start_line && !$option2_end_line) && substr_count($data,"</select>")){
		$option2_end_line = $i+1;
		//echo $i."<br><br>".$data."<br><br>";
	}

	if(!$soldout_message && substr_count($data,"<span id=\"soldout_out\">")){		
		$soldout_message = "soldout_out";
	}
}


//홍진영 2012-04-20 start
if(!$pname){
	preg_match_all("|_pd =_RP\('(.*)</a>|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
	$pname = strip_tags($pname_tmp[1][0]);
	$pname = iconv("CP949","UTF-8",$pname) ;
}

$price_tmp = "";
$sale_price = "";
if(!$price){
	preg_match_all("|<input type=\"hidden\" id=\"regular_price\" name=\"regular_price\" value=\"(.*)\" />|U",$results,$price_tmp, PREG_PATTERN_ORDER);
	$price=str_replace(",","",$price_tmp[1][0]);
	preg_match_all("|<input type=\"hidden\" id=\"discount_price\" name=\"discount_price\" value=\"(.*)\" />|U",$results,$price_tmp, PREG_PATTERN_ORDER);
	$sale_price=str_replace(",","",$price_tmp[1][0]);
	
	if($sale_price > 0){
		$listprice = $price;
		$price = $sale_price;
	}
}

//$price=$price*0.77;
$pcode = $bs_site."_".$pcode;
//홍진영 2012-04-20 end

if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}
/*
for($i=$prod_desc_start_line;$i < $prod_desc_end_line;$i++){
	$prod_desc_prod .= $datas[$i];
}
*/
//$prod_desc_prod = str_replace("'","\'",$prod_desc_prod);
//$prod_desc_prod = clearUTF($prod_desc_prod);
//setlocale(LC_CTYPE, 'en_AU.utf8');

/*
$prod_desc_prod = iconv("iso-8859-1","utf-8//TRANSLIT",$prod_desc_prod) ;
//$prod_desc_prod = iconv("utf-8","ASCII//TRANSLIT",$prod_desc_prod) ;

$prod_desc_prod = str_replace(array("","","Read More"),"",$prod_desc_prod);
$prod_desc_prod = strip_tags($prod_desc_prod,"<table><tr><td><span><li><img>");
*/
//echo  "appliqués" ;
//$prod_desc_prod = strip_javascript($prod_desc_prod);
//$prod_desc_prod = str_replace(array("Read More"),"",$prod_desc_prod);

$options_str = "";
for($i=$option_start_line;$i < $option_end_line;$i++){
	$options_str .= $datas[$i];	
}
//echo iconv("CP949","UTF-8",$options_str);

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
//$options_str = iconv("UTF8","CP949",$options_str);
//exit;
$option1_spos = "";
$option1_epos = "";
$options1_str = "";
$option_name_tmp = "";
$option_name = "";
$options = "";
$_option = "";
$option = "";
if($options_str){
		$options_str = str_replace("\\","",$options_str);
		//echo strrpos($options_str,"<select  id=\"custom1\"")."::::::::::".strpos($options_str,"</select>");
		$option1_spos = strrpos($options_str,"<select name=\"size\"");
		$option1_epos = strpos($options_str,"</select>");
		$options1_str = substr($options_str,$option1_spos,$option1_epos+1);

		preg_match_all("|<td class=\"vo_title\">(.*)</td>|U",$options1_str,$option_name_tmp, PREG_PATTERN_ORDER);
		$option_name = $option_name_tmp[1][0];
		//$options_str = substr(<select  id="custom1",$options_str
		//preg_match_all("|<img .*src=\"(.*)\".*>|U",$prod_img_text,$img_out, PREG_PATTERN_ORDER);
		//echo $options1_str;
		preg_match_all("|option .*>(.*)<|U",$options1_str,$options, PREG_PATTERN_ORDER);
		//print_r($options);
		
		$_option = $options[1];
		
		$option[0] = $option_name;
 		for($i=1, $j=1;$i < count($_option);$i++){
			if(iconv("CP949","UTF-8",$_option[$i]) != "옵션을 선택하세요" && iconv("CP949","UTF-8",$_option[$i]) != "-----------------"){
				$option[$j] = str_replace("&nbsp;","",trim(iconv("CP949","UTF-8",$_option[$i])));
				$j++;
			}
		}
		//print_r($option);
}

$option2_name_tmp = "";
$option2_name = "";
$options2 = "";
$_option2 = "";
$option2 = "";
if($options2_str){

		
		//$options2_str = substr($options_str,$option1_epos+9,strlen($options_str));
		//echo $options2_str;
		preg_match_all("|<td class=\"vo_title\">(.*)</td>|U",$options2_str,$option2_name_tmp, PREG_PATTERN_ORDER);
		$option2_name = $option2_name_tmp[1][0];

		preg_match_all("|<option .*>(.*)</option>|U",$options2_str,$options2, PREG_PATTERN_ORDER);
		//print_r($options2);
		$_option2 = $options2[1];
		$option2[0] = $option2_name;
		for($i=1;$i < count($_option2);$i++){
			$option2[$i] = str_replace("Select ","",trim(iconv("CP949","UTF-8",$_option2[$i])));
		}
		//print_r($option2);
}


?>