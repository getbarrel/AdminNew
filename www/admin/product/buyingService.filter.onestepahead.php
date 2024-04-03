<?

$results = "";
$soldout_message = "";

$snoopy = new Snoopy;
$snoopy->fetch($bs_url);
//print $snoopy->results;

$datas = "";
$datas = split("\n",$snoopy->results);


$prod_img_src = "";
$prod_img_text = "";
$img_out = "";

$pcode = "";
$pname = "";
$price = "";

$prod_desc_start_line = "";
$prod_desc_end_line = "";
$option_start_line = "";
$option_end_line = "";
for($i=0;$i < count($datas);$i++){
	$data = $datas[$i];
	
	if(!$prod_img_src && substr_count($data,"<div id=\"product_image\">")){
		$prod_img_text = $datas[$i+1];
		$prod_img_text = str_replace("\\","",$prod_img_text);
		preg_match_all("|<img .*src=\"(.*)\".*>|U",$prod_img_text,$img_out, PREG_PATTERN_ORDER);
		//print_r($img_out);
		$prod_img_src = $img_out[1][0];
	}	
	
	if(!$pcode && substr_count($data,"<dl id=\"pdItemNum\">")){
		$pcode = strip_tags($datas[$i+2]);
		$pcode = str_replace(array("Style #",""),"",$pcode);
	}
	
	if(!$pname && substr_count($data,"<div id=\"above_body_content\">")){		
		$pname = strip_tags($datas[$i+1]);
	}
	
	if(!$price && substr_count($data,"<div id=\"pdItemPrice\">")){
		
		$price = strip_tags($datas[$i+1]);
		$price = str_replace(array("Price: ","$","&#036;"),"",$price);
	}
	
	
	if(!$prod_desc_start_line && substr_count($data,"<div id=\"product_desc\">")){
		$prod_desc_start_line = $i;
	}
	
	if(($prod_desc_start_line && !$prod_desc_end_line) && substr_count($data,"</div>")){
		$prod_desc_end_line = $i;
	}
	

	if(!$option_start_line && substr_count($data,"<option value=\"\">Choose Style and Size</option>")){
		$option_start_line = $i;
	}
	
	if(($option_start_line && !$option_end_line) && substr_count($data,"</select>")){
		$option_end_line = $i+1;
	}

	
}


$prod_desc_prod = "";
for($i=$prod_desc_start_line;$i < $prod_desc_end_line;$i++){
	$prod_desc_prod .= $datas[$i];
}
$prod_desc_prod = str_replace(array("<div id=\"product_desc\">","</div>","Read More"),"",$prod_desc_prod);
$prod_desc_prod = strip_tags($prod_desc_prod);
$prod_desc_prod = str_replace(array("Read More"),"",$prod_desc_prod);

$options_str = "";
for($i=$option_start_line;$i < $option_end_line;$i++){
	$options_str .= $datas[$i];
	
}
//echo "option :".$option_start_line." ".$option_end_line."<br>";
//echo $options_str;

$options = "";
$_option = "";
$option = "";
if($options_str){
		$options_str = str_replace("\\","",$options_str);
		//preg_match_all("|<img .*src=\"(.*)\".*>|U",$prod_img_text,$img_out, PREG_PATTERN_ORDER);
		preg_match_all("|<option .*>(.*)</option>|U",$options_str,$options, PREG_PATTERN_ORDER);
		//print_r($options);
		
		$_option = $options[1];
		
		if(substr_count($options_str,"colorDropDowntexts")){
			$option[0] = "Color";
			for($i=0;$i < count($_option);$i++){
				$option[$i+1] = str_replace("Select ","",trim($_option[$i]));
			}
		}else{
			for($i=0;$i < count($_option);$i++){
				$option[$i] = str_replace("Choose ","",trim($_option[$i]));
			}
		}
		
}	
?>