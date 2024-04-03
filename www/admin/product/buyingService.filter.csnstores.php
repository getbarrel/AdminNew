<?
//	<div class="itemheadernew"><div class="prodtitleLG">Polar Bear Shawl Cardigan</div><font class=prodourprice>Price: &#036;135.00</font>

$results = "";
$soldout_message = "";

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

//echo $bs_url;
/*
$snoopy = new Snoopy;
$snoopy->fetch($bs_url);
//print $snoopy->results;
$datas = split("\n",$snoopy->results);
*/

$productinfo_inner_div_cnt = 0;

$productinfo_start_line = "";
$productinfo_inner_div_cnt = "";
$productinfo_end_line = "";
$prod_img_src = "";
$prod_img_text = "";
$img_out = "";
$pcode = "";
$pname = "";
$price = "";
$prod_desc_start_line = "";
$prod_desc_inner_div_cnt = "";
$prod_desc_end_line = "";
$option_start_line = "";
$option_end_line = "";

for($i=0;$i < count($datas);$i++){
	$data = $datas[$i];

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
	
	
	if(!$prod_img_src && substr_count($data,"<img id=\"lgimage\"")){
		$prod_img_text = $datas[$i];
		$prod_img_text = str_replace("\\","",$prod_img_text);
		preg_match_all("|<img .*src=\"(.*)\".*>|U",$prod_img_text,$img_out, PREG_PATTERN_ORDER);
		//print_r($img_out);
		$prod_img_src = $img_out[1][0];
	}	
	
	if(!$pcode && substr_count($data,"SKU #:")){
		$pcode = strip_tags($datas[$i]);
		$pcode = str_replace(array("SKU #:",""),"",$pcode);
	}
	
	if(!$pname && substr_count($data,"<title>")){		
		$pname = strip_tags($datas[$i]);
	}
	
	if(!$price && substr_count($data,"Sale Price:</span>")){
		
		$price = strip_tags($datas[$i]);
		$price = str_replace(array("Sale Price:","$","&#036;"),"",$price);
	}
	
	
	if(!$prod_desc_start_line && substr_count($data,"<div id=\"tab1\" class=\"prodoverview\">")){
		$prod_desc_start_line = $i+1;
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
		$prod_desc_end_line = $i;
	}
	
	


	if(!$option_start_line && substr_count($data,"<select  id=\"custom1\"")){
		$option_start_line = $i;
	}
	
	if(($option_start_line && !$option_end_line) && substr_count($data,"</select>")){
		$option_end_line = $i+1;
		//echo $i."<br><br>".$data."<br><br>";
	}

	
}

if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}

for($i=$productinfo_start_line;$i < $productinfo_end_line;$i++){
	$pname .= $datas[$i];
}

$prod_desc_prod = "";
for($i=$prod_desc_start_line;$i < $prod_desc_end_line;$i++){
	$prod_desc_prod .= $datas[$i];
}
$prod_desc_prod = str_replace(array("","","Read More"),"",$prod_desc_prod);
//$prod_desc_prod = strip_tags($prod_desc_prod);
//$prod_desc_prod = str_replace(array("Read More"),"",$prod_desc_prod);

$options_str = "";
for($i=$option_start_line;$i < $option_end_line;$i++){
	$options_str .= $datas[$i];
	
}
//echo "option :".$option_start_line." ".$option_end_line."<br>";
//echo $options_str;
$option1_spos = "";
$option1_epos = "";
$options1_str = "";
$options = "";
$_option = "";
$option = "";
$options2_str = "";
$options2 = "";
$_option2 = "";
$option2 = "";
if($options_str){
		$options_str = str_replace("\\","",$options_str);
		//echo strrpos($options_str,"<select  id=\"custom1\"")."::::::::::".strpos($options_str,"</select>");
		$option1_spos = strrpos($options_str,"<select  id=\"custom1\"");
		$option1_epos = strpos($options_str,"</select>");
		$options1_str = substr($options_str,$option1_spos,$option1_epos);
		//$options_str = substr(<select  id="custom1",$options_str
		//preg_match_all("|<img .*src=\"(.*)\".*>|U",$prod_img_text,$img_out, PREG_PATTERN_ORDER);
		preg_match_all("|<option .*>(.*)</option>|U",$options1_str,$options, PREG_PATTERN_ORDER);
	//	print_r($options);
		
		$_option = $options[1];
		
		if(substr_count($options1_str,"colorDropDowntexts")){
			$option[0] = "Color";
			for($i=0;$i < count($_option);$i++){
				$option[$i+1] = str_replace("Select ","",trim($_option[$i]));
			}
		}else{
			for($i=0;$i < count($_option);$i++){
				$option[$i] = str_replace("Select ","",trim($_option[$i]));
			}
		}
		
		$options2_str = substr($options_str,$option1_epos+9,strlen($options_str));
		//echo $options2_str;
		preg_match_all("|<option .*>(.*)</option>|U",$options2_str,$options2, PREG_PATTERN_ORDER);
		//print_r($options2);
		$_option2 = $options2[1];
		
		for($i=0;$i < count($_option2);$i++){
			$option2[$i] = str_replace("Select ","",trim($_option2[$i]));
		}
}	
?>