<?

$results = "";
$soldout_message = "";

//	<div class="itemheadernew"><div class="prodtitleLG">Polar Bear Shawl Cardigan</div><font class=prodourprice>Price: &#036;135.00</font>
/**
$Tag = curl_init();
curl_setopt( $Tag , CURLOPT_URL , "$bs_url" ); 

ob_start();
curl_exec( $Tag );
curl_close( $Tag );
$results = ob_get_contents();
ob_clean();
*/

$snoopy = new Snoopy;
$snoopy->agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)";
$snoopy->referer = "http://www.ralphlauren.com/";

// set some cookies:
$snoopy->cookies["s7js.flyout.InfoMessage.displayed"]="true";
$snoopy->cookies["QVY2"]="34N2NVP7jIB04Fl-yzsWtUvJ3EfoQfP7dnBaU08E-y2r0Fm7p3lmBJQ";
$snoopy->cookies["browser_id"]="174238280994";
$snoopy->cookies["__g_u"]="195300258969036_0"; 
$snoopy->cookies["ClrOSSID"]="1352810151107-33794"; 
$snoopy->cookies["ClrSCD"]="1352810151107"; 
$snoopy->cookies["s_nr"]="1352810311416"; 
$snoopy->cookies["JSESSIONID"]="6yH4QjRFG336rsyymGdRhp9VXRGVCL6xGxgsG30QwlgR01QjqvL3!1627326834"; 
$snoopy->cookies["user_interstitial"]="true"; 
$snoopy->cookies["user_redirected"]="true"; 
$snoopy->cookies["__g_c"]="a%3A0"; 
//$snoopy->cookies["s_rdcvpc"]="%5B%5B%27iselina.com%27%2C%271352810311415%27%5D%2C%5B%27www.mallstory.com%27%2C%271352880550568%27%5D%5D"; 
$snoopy->cookies["rvdata"]="XR7b544059195e43161d114144595d545c5748054f01171a1c0a090c"; 
$snoopy->cookies["ClrSSID"]="1352810151107-33794"; 
$snoopy->cookies["s_cc"]="true"; 
$snoopy->cookies["s_sq"]="%5B%5BB%5D%5D"; 
$snoopy->cookies["__utma"]="256506888.1327840192.1352810151.1352810151.1352880551.2"; 
$snoopy->cookies["__utmb"]="256506888.9.9.1352881768988"; 
$snoopy->cookies["__utmc"]="256506888"; 
//$snoopy->cookies["__utmz"]="256506888.1352880551.2.2.utmcsr=mallstory.com|utmccn=(referral)|utmcmd=referral|utmcct=/admin/bbsmanage/bbs.php"; 
$snoopy->cookies["viewsizecookie"]="99"; 
$snoopy->cookies["mt.v"]="2.1015567263.1352810150922"; 
$snoopy->cookies["s_vi"]="[CSS]v1|28511F5785012E3F-60000101E0006E00[CE]"; 
//$snoopy->cookies["s_previousPageName"]="en_US%3A%20Product%20Detail%3A%2015689706%3A%20Children%3A%20Girls%202-6X%3A%20Chino%20Short";

$snoopy->rawheaders["Pragma"] = "no-cache";

//http://m.jcrew.com/$snoopy->fetch("http://www.jcrew.com/index.jsp");
$snoopy->fetch($bs_url);
$results =  $snoopy->results;
    
//print_r($results);
//exit;
//echo $results;
//imageObj.reg="http://www.ralphlauren.com/graphics/product_images/pPOLO2-11733188_lifestyle_v360x480.jpg"; 
//필터 맨아래쪽 별도 팝업페이지에서 상세 이미지 가져올수 있도록 처리
$detail_images = "";
preg_match_all("|imageObj.enh=\"(.*)\";|U",$results,$detail_images, PREG_PATTERN_ORDER);
//http://www.ralphlauren.com/graphics/product_images/pPOLO2-12168006_standard_dt.jpg
//exit;
$pname_tmp = "";
$pname = "";
preg_match_all("|<meta property=\"og:title\" content=\"(.*)\" />|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
//print_r($pname_tmp);
$pname = $pname_tmp[1][0];

$goods_detail_images = "";
$prod_img_src = "";
$goods_detail_images = $detail_images[1];
$prod_img_src = $goods_detail_images[0];

$detail_images_name = "";
$goods_detail_images_name = "";
$color_option_tmp = "";
preg_match_all("|imageObj.name=\"(.*)\";|U",$results,$detail_images_name, PREG_PATTERN_ORDER);
$goods_detail_images_name = $detail_images_name[1];
$color_option_tmp = $detail_images_name;
//exit;

$thumb_images_tmp = "";
$thumb_images = "";
preg_match_all("|imageObj.swatch=\"(.*)\";|U",$results,$thumb_images_tmp, PREG_PATTERN_ORDER);
//print_r($thumb_images_tmp);
$thumb_images = $thumb_images_tmp[1];

$relation_size_tmp = "";
$relation_size = "";
if(count($thumb_images) > 0){
	for($i=0; $i < count($thumb_images);$i++){
		preg_match_all("|sizeHolder".$i."\[.*\] = \"(.*)\";|U",$results,$relation_size_tmp, PREG_PATTERN_ORDER);
		$relation_size[$i] = implode("^",$relation_size_tmp[1]);
	}

	//print_r($relation_size); 
}

if(false){
	$promotion_salerate_tmp = "";
	$promotion_salerate = "";
	preg_match_all("|Receive an additional (.*)% off|U",$results,$promotion_salerate_tmp, PREG_PATTERN_ORDER);
	if(is_array($promotion_salerate_tmp)){
		if(is_numeric($promotion_salerate_tmp[1][0])){
			$promotion_salerate = $promotion_salerate_tmp[1][0];
		}
	}
}

//echo $promotion_salerate;



$datas = "";
$datas = split("\n",$results);

$prod_img_src = "";
$prod_img_text = "";
$img_out = "";
$ralphlauren_pcode = "";
$_price = "";
$_sale_price = "";
$prod_desc_start_line = "";
$prod_desc_inner_div_cnt = "";
$prod_desc_end_line = "";
$pname_start_line = "";
$pname_end_line = "";
$option_start_line = "";
$option_end_line = "";
$option2_start_line = "";
$option2_end_line = "";
for($i=0;$i < count($datas);$i++){
	$data = $datas[$i];
	
	if(!$prod_img_src && substr_count($data,"<div id=\"imageDiv\">")){
		
		$prod_img_text = $data;
		$prod_img_text = str_replace("\\","",$prod_img_text);
		preg_match_all("|<img .*SRC=\"(.*)\".*>|U",$prod_img_text,$img_out, PREG_PATTERN_ORDER);
		//print_r($img_out);
		$prod_img_src = $img_out[1][0];

		if($prod_img_src == ""){
			$prod_img_text = $datas[$i+1];
			$prod_img_text = str_replace("\\","",$prod_img_text);
			preg_match_all("|<img .*SRC=\"(.*)\".*>|U",$prod_img_text,$img_out, PREG_PATTERN_ORDER);
			//print_r($img_out);
			$prod_img_src = $img_out[1][0];
		}
	}	
	
	if(!$ralphlauren_pcode && substr_count($data,"<span class=\"productStyle\">")){
		$ralphlauren_pcode = strip_tags($data);
		$ralphlauren_pcode = str_replace(array("Style #",""),"",$ralphlauren_pcode);
		$ralphlauren_pcode = $bs_site."_".trim($ralphlauren_pcode);
	}
	
	
	
	if(!$_price && (substr_count($data,"<font class=\"prodourprice\">") || substr_count($data,"<span class=\"prodourprice\">"))){
		$_price = split("Price: ",$data);
		$_price = strip_tags($_price[1]);
		$_price = str_replace(array("Price: ","$","&#036;"),"",$_price);
	}

	if(!$_sale_price && (substr_count($data,"<font class=templateSalePrice>") || substr_count($data,"<span class=templateSalePrice>"))){
		$_sale_price = split("Price: ",$data);
		$_sale_price = strip_tags($_sale_price[1]);
		$_sale_price = str_replace(array("Sale Price: ","$","&#036;"),"",$_sale_price);
	
	}
	
	
	if(!$prod_desc_start_line && (substr_count($data,"<div id = \"longDescDiv\" style=\"margin-bottom:10px\">") || substr_count($data,"<div id = \"padDescDiv\">"))){
		$prod_desc_start_line = $i;
	}
	
//	if(($prod_desc_start_line && !$prod_desc_end_line) && substr_count($data,"</div>")){
//		$prod_desc_end_line = $i;
//	}
	
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


	

	if(!$pname_start_line && substr_count($data,"<div class=\"prodtitleLG\">")){
		$pname_start_line = $i;
	}
	
	if(($pname_start_line && !$pname_end_line) && substr_count($data,"</div>")){
		$pname_end_line = $i+1;
	}


	if(!$option_start_line && substr_count($data,"<select name=colors_0")){
		$option_start_line = $i;
	}
	
	if(($option_start_line && !$option_end_line) && substr_count($data,"</select>")){
		$option_end_line = $i+1;
	}

	if(!$option2_start_line && substr_count($data,"<select name=prod_0")){
		$option2_start_line = $i;
	}
	
	if(($option2_start_line && !$option2_end_line) && substr_count($data,"</select>")){
		$option2_end_line = $i+1;
	}

	
}
$pcode = "";
$pcode = $ralphlauren_pcode;

$price = "";
$price = $_price;
$sale_price = "";
$sale_price = $_sale_price;
if($sale_price > 0){
	$listprice = $price;
	$price = $sale_price;
}

if($promotion_salerate > 0){
	$price = number_format($price- $price*$promotion_salerate/100,2);
}

$pname_text = "";
if(trim($pname) == ""){
	for($i=$pname_start_line;$i < $pname_end_line;$i++){
		$pname_text .= $datas[$i];
	}
	//echo $pname_text;
	//exit;
	$pname_text = str_replace(array("<div id = \"longDescDiv\" style=\"margin-bottom:10px\">","</div>"),"",$pname_text);
	$pname = trim(strip_tags($pname_text));
}




$prod_desc_prod = "";
$_prod_desc = "";
for($i=$prod_desc_start_line;$i < $prod_desc_end_line;$i++){
	$prod_desc_prod .= $datas[$i];
}
//echo $prod_desc_prod;
//exit;
//$prod_desc_prod = str_replace(array("<div id = \"longDescDiv\" style=\"margin-bottom:10px\">","</div>"),"",$prod_desc_prod);
//$prod_desc_prod = strip_tags($prod_desc_prod);

if(count($goods_detail_images) > 0){
	$_prod_desc = "<table align=center>";
	$_prod_desc .= "<tr><td align=center><div style='text-align:left;'>".$prod_desc_prod."</div></td></tr>";
	for($i=0;$i < count($goods_detail_images);$i++){
		$_prod_desc .= "<tr><td align=center style='padding:0px 0px 0px 0px;'><b>".$goods_detail_images_name[$i]."</b></td></tr>";
		$_prod_desc .= "<tr><td align=center style='padding:5px 0px 30px 0px;'><IMG src=\"".$goods_detail_images[$i]."\"></td></tr>";
	}
	$_prod_desc .= "</table>";

	$prod_desc_prod = $_prod_desc;
}

//echo $prod_desc_start_line.":::".$prod_desc_end_line;
//exit;


for($i=$option_start_line;$i < $option_end_line;$i++){
	$options_str .= $datas[$i];
	
}

//echo "option :".$option_start_line." ".$option_end_line."<br>";
//echo $options_str;
//exit;
$option_key = 0;
$_option_tmp = "";
$options = "";
if(is_array($color_option_tmp)){
		$options_str = str_replace("\\","",$options_str);
	//	if(substr_count($options_str, "</option") > substr_count($options_str, "\n")){
	//		$options_str = str_replace("</option>","</option>\n",$options_str);
	//	}

	//	echo $options_str;
//exit;
		//preg_match_all("|<img .*src=\"(.*)\".*>|U",$prod_img_text,$img_out, PREG_PATTERN_ORDER);
		//preg_match_all("|<option .*>(.*)</option>|U",$options_str,$color_option_tmp, PREG_PATTERN_ORDER);
		
		$_option_tmp = $color_option_tmp[1];
		//print_r($color_option_tmp);
		//exit;
        if(empty($_option_tmp)){
            $soldout_message = true;
        }
		
		if(is_array($_option_tmp)){
			$options[$option_key][option_type] = "9";
			$options[$option_key][option_name] = "색상";
			$options[$option_key][option_kind] = "r";
			$options[$option_key][option_use] = "1";
			for($i=0;$i < count($_option_tmp);$i++){
				$options[$option_key][details][$i][option_div] = str_replace("Select ","",trim($_option_tmp[$i]));
				$options[$option_key][details][$i][price] = "";
				$options[$option_key][details][$i][etc1] = "사이즈:".$relation_size[$i];
				$options[$option_key][details][$i][thumb_images] = $thumb_images[$i];
				$options[$option_key][details][$i][goods_images] = $goods_detail_images[$i];
				

			}
			/*
			$option[$option_key] = "Color";
			for($i=0;$i < count($_option_tmp);$i++){
				$option[$i+1] = str_replace("Select ","",trim($_option_tmp[$i]));
			}
			*/
		}else if(substr_count($options_str,"sizeDropDowntexts")){
			$options[$option_key][option_type] = "9";
			$options[$option_key][option_name] = "Size";
			$options[$option_key][option_kind] = "s";
			$options[$option_key][option_use] = "1";
			for($i=0;$i < count($_option_tmp);$i++){
				$options[$option_key][details][$i+1][option_div] = str_replace("Select ","",trim($_option_tmp[$i]));
				$options[$option_key][details][$i+1][price] = "";
				$options[$option_key][details][$i+1][etc1] = "";

			}
			/*
			//$option[$option_key] = "Size";
			for($i=0;$i < count($_option_tmp);$i++){
				$option[$i+1] = str_replace("Select ","",trim($_option_tmp[$i]));
			}
			*/
		}else{
			$options[$option_key][option_type] = "9";
			$options[$option_key][option_name] = "옵션";
			$options[$option_key][option_kind] = "s";
			$options[$option_key][option_use] = "1";
			for($i=0;$i < count($_option_tmp);$i++){
				$options[$option_key][details][$i+1][option_div] = str_replace("Select ","",trim($_option_tmp[$i]));
				$options[$option_key][details][$i+1][price] = "";
				$options[$option_key][details][$i+1][etc1] = "";

			}
			/*
			for($i=0;$i < count($_option_tmp);$i++){
				$option[$i] = str_replace("Select ","",trim($_option_tmp[$i]));
			}
			*/
		}
		$option_key++;
		//print_r($options);
}	

$options2_str = "";
for($i=$option2_start_line;$i <= $option2_end_line;$i++){
	$options2_str .= $datas[$i];
	
}

if($options2_str && false){
		$options_str = str_replace("\\","",$options_str);
	//	if(substr_count($options_str, "</option") > substr_count($options_str, "\n")){
	//		$options_str = str_replace("</option>","</option>\n",$options_str);
	//	}

	//	echo $options_str;
//exit;
		//preg_match_all("|<img .*src=\"(.*)\".*>|U",$prod_img_text,$img_out, PREG_PATTERN_ORDER);
		preg_match_all("|<option .*>(.*)</option>|U",$options2_str,$options2_tmp, PREG_PATTERN_ORDER);
		
		$_option_tmp2 = $options2_tmp[1];
		//print_r($_option_tmp2);
		//exit;
        
		if(substr_count($options2_str,"colorDropDowntexts")){
			$options[$option_key][option_type] = "9";
			$options[$option_key][option_name] = "Color";
			$options[$option_key][option_kind] = "r";
			$options[$option_key][option_use] = "1";
			for($i=0;$i < count($_option_tmp2);$i++){
				$options[$option_key][details][$i+1][option_div] = str_replace("Select ","",trim($_option_tmp2[$i]));
				$options[$option_key][details][$i+1][price] = "";
				$options[$option_key][details][$i+1][etc1] = $relation_size[$i];

			}
			/*
			$option[0] = "Color";
			for($i=0;$i < count($_option_tmp2);$i++){
				$option[$i+1] = str_replace("Select ","",trim($_option_tmp2[$i]));
			}
			*/
		}else if(substr_count($options2_str,"sizeDropDowntexts")){
			$options[$option_key][option_type] = "9";
			$options[$option_key][option_name] = "Size";
			$options[$option_key][option_kind] = "s";
			$options[$option_key][option_use] = "1";
			for($i=0;$i < count($_option_tmp2);$i++){
				$options[$option_key][details][$i+1][option_div] = str_replace("Select ","",trim($_option_tmp2[$i]));
				$options[$option_key][details][$i+1][price] = "";
				$options[$option_key][details][$i+1][etc1] = "";

			}
			/*
			//$option[0] = "Size";
			for($i=0;$i < count($_option_tmp2);$i++){
				$option[$i+1] = str_replace("Select ","",trim($_option_tmp2[$i]));
			}
			*/
		}else{
			$options[$option_key][option_type] = "9";
			$options[$option_key][option_name] = "옵션";
			$options[$option_key][option_kind] = "s";
			$options[$option_key][option_use] = "1";
			for($i=0;$i < count($_option_tmp2);$i++){
				$options[$option_key][details][$i+1][option_div] = str_replace("Select ","",trim($_option_tmp2[$i]));
				$options[$option_key][details][$i+1][price] = "";
				$options[$option_key][details][$i+1][etc1] = "";

			}
			/*
			for($i=0;$i < count($_option_tmp2);$i++){
				$option[$i] = str_replace("Select ","",trim($_option_tmp2[$i]));
			}
			*/
		}
		/*
		if(substr_count($options2_str,"colors_0")){
			$option2[0] = "Color";
			for($i=0;$i < count($_option_tmp2);$i++){
				$option2[$i+1] = str_replace("Select ","",trim($_option_tmp2[$i]));
			}
		}else if(substr_count($options2_str,"sizeDropDowntexts")){
			//$option2[0] = "Size";
			for($i=0;$i < count($_option_tmp2);$i++){
				$option2[$i] = str_replace("Select ","",trim($_option_tmp2[$i]));
			}
		}else{
			for($i=0;$i < count($_option_tmp2);$i++){
				$option2[$i] = str_replace("Select ","",trim($_option_tmp2[$i]));
			}
		}\
		*/
}	
//print_r($options);
/*
echo "http://www.ralphlauren.com/contentPopup/index.jsp?productId=".$pcode;
$Tag = curl_init();
curl_setopt( $Tag , CURLOPT_URL , "http://www.ralphlauren.com/contentPopup/index.jsp?productId=".$pcode); 

ob_start();
curl_exec( $Tag );
curl_close( $Tag );
$results = ob_get_contents();
ob_clean();
echo $results;
preg_match_all("|enh:.*'(.*)'.*|U",$results,$detail_images, PREG_PATTERN_ORDER);
print_r($detail_images);
exit;
*/
if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}

$goods_desc_copy = 1;

?>