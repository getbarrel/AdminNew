<?

$results = "";
$soldout_message = "";

$base_domain = "http://www.izabel.co.kr/";
$bs_url = "http://www.amberi.co.kr/front/php/product.php?product_no=11742&main_cate_no=67&display_group=1";
$Tag = curl_init();

curl_setopt( $Tag , CURLOPT_URL , "$bs_url" ); 

ob_start();
curl_exec( $Tag );
curl_close( $Tag );
$results = ob_get_contents();
ob_clean();

echo $results;
exit;

$datas = "";
$datas = split("\n",$results);

/*
$snoopy = new Snoopy;
$snoopy->fetch($bs_url);
print $snoopy->results;
//exit;
$datas = split("\n",$snoopy->results);
*/
$productinfo_inner_div_cnt = 0;

for($i=0;$i < count($datas);$i++){
	$data = $datas[$i];
	
	if(!$pcode && substr_count($data,"name=branduid")){
		$pcode = $datas[$i];
		$pcode = str_replace(array("<input type=hidden name=branduid value=\"","\">"),"",$pcode);
		$pcode = trim($pcode);
		$pcode = $bs_site."_".$pcode;
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
	


	if(!$option_start_line && substr_count($data,"<select name=spcode")){
		$option_start_line = $i;
	}
	
	if(($option_start_line && !$option_end_line) && substr_count($data,"</select>")){
		$option_end_line = $i+3;
		//echo $i."<br><br>".$data."<br><br>";
	}
	
	if(!$option2_start_line && substr_count($data,"<select name=\"color\"")){
		$option2_start_line = $i;
	}
	
	if(($option2_start_line && !$option2_end_line) && substr_count($data,"</select>")){
		$option2_end_line = $i+1;
		//echo $i."<br><br>".$data."<br><br>";
	}

	if(!$soldout_message && substr_count($data,"Sorry, we couldn't find item")){		
		$soldout_message = "Sorry, we couldn't find item";
	}
}

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

for($i=$option_start_line;$i < $option_end_line;$i++){
	$options_str .= $datas[$i];	
}
//echo iconv("CP949","UTF-8",$options_str);

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
if($options_str){
		$options_str = str_replace("\\","",$options_str);
		//echo strrpos($options_str,"<select  id=\"custom1\"")."::::::::::".strpos($options_str,"</select>");
		$option1_spos = strrpos($options_str,"<select name=\"size\"");
		$option1_epos = strpos($options_str,"</select>");
		$options1_str = substr($options_str,$option1_spos,$option1_epos+1);
		//$options_str = substr(<select  id="custom1",$options_str
		//preg_match_all("|<img .*src=\"(.*)\".*>|U",$prod_img_text,$img_out, PREG_PATTERN_ORDER);
		//echo $options1_str;
		preg_match_all("|option .*>(.*)<|U",$options1_str,$options, PREG_PATTERN_ORDER);
		//print_r($options);
		
		$_option = $options[1];
		
		
		for($i=0, $j=0;$i < count($_option);$i++){
			if(iconv("CP949","UTF-8",$_option[$i]) != "옵션을 선택하세요" && iconv("CP949","UTF-8",$_option[$i]) != "-----------------"){
				$option[$j] = str_replace("&nbsp;","",trim(iconv("CP949","UTF-8",$_option[$i])));
				$j++;
			}
		}
		//print_r($option);
}
if($options2_str){

		
		//$options2_str = substr($options_str,$option1_epos+9,strlen($options_str));
		//echo $options2_str;
		preg_match_all("|<option .*>(.*)</option>|U",$options2_str,$options2, PREG_PATTERN_ORDER);
		//print_r($options2);
		$_option2 = $options2[1];
		
		for($i=0;$i < count($_option2);$i++){
			$option2[$i] = str_replace("Select ","",trim($_option2[$i]));
		}
		print_r($option2);
}	

?>