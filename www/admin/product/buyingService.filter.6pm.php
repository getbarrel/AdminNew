<?

/**
 * json_decode로 생성한 객체를 배열로 변환
 * 
 * @param obj
 * @return array
 */
if (!function_exists('objectToArray')) {
	function objectToArray($d) {
		if (is_object($d)) {
			// Gets the properties of the given object
			// with get_object_vars function
			$d = get_object_vars($d);
		}
		
		if (is_array($d)) {
			/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			return array_map(__FUNCTION__, $d);
		}
		else {
			// Return array
			return $d;
		}
	}  
}
            
/////////////////////////////////////////////////////
$results = "";
$soldout_message = "";

//$bs_url = "http://www.zappos.com/girls-dresses~1p";
$snoopy = new Snoopy;
$snoopy->fetch($bs_url);
$results =  $snoopy->results;


print_r($results);
//줄바꿈 제거된 results
$results_oneline = "";
$results_oneline = str_replace("\n"," ",$results);

//echo $results;

/////////////////////////////////////////////////
 


//필터 맨아래쪽 별도 팝업페이지에서 상세 이미지 가져올수 있도록 처리



/**
 * 변수명
 * 
 * $pcode = 상품코드(사이트명_코드)
 * 
 * $listprice = 원래상품가격
 * 
 * $price = 현재상품가격
 * 
 * $pname = 상품명
 * 
 * $prod_img_src = 상품 이미지
 * 
 * $prod_desc_prod = 상품설명
 * 
 * $dimensionType = 사이즈옵션의 종류(제품마다 다름)
 * 
 * $$goods_detail_images = 다른색상 상품이미지
 * 
 * 
 * 
 */


//json_decode로 생성한 객체를 배열로 변환
if (!function_exists('objectToArray')) {
    function objectToArray($d) {
        if (is_object($d)) {
        	// Gets the properties of the given object
        	// with get_object_vars function
        	$d = get_object_vars($d);
        }
        
        if (is_array($d)) {
        	/*
        	* Return array converted to object
        	* Using __FUNCTION__ (Magic constant)
        	* for recursive call
        	*/
        	return array_map(__FUNCTION__, $d);
        }
        else {
        	// Return array
        	return $d;
        }
    }
}
//받은 데이터 줄바꿈
$datas = "";
$datas = split("\n",$results); 
 
//상품코드
$pcode_tmp = "";
$pcode = "";
preg_match_all("|<input type=\"hidden\" name=\"productId\" value=\"(.*)\" />|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
$pcode = $bs_site."_".$pcode_tmp[1][0];

//상품가격
$old_price_tmp = "";
$old_price = "";
preg_match_all("|<span class=\"oldprice\">(.*)</span>|U",$results,$old_price_tmp, PREG_PATTERN_ORDER);
$old_price = str_replace("$","",$old_price_tmp[1][0]);

$listprice = "";
$sale_price_tmp = "";
$price = "";
$price_tmp = "";
if(trim($old_price) != ""):
    $listprice = $old_price;
    preg_match_all("|<div class=\"price\">(.*)</div>|U",$results,$sale_price_tmp, PREG_PATTERN_ORDER);
    //세일가격을 현재가격으로
    $price = str_replace("$","",$sale_price_tmp[1][0]);
    //echo "on sale";
else:
    preg_match_all("|<div class=\"price\">(.*)</div>|U",$results,$price_tmp, PREG_PATTERN_ORDER);
    $price = str_replace("$","",$price_tmp[1][0]);
    //echo "not on sale";
endif;
//price없는경우
if(empty($price)){
    preg_match_all("|<span id=\"price\">(.*)<span|U",$results_oneline,$price_tmp, PREG_PATTERN_ORDER);
    $price = str_replace("$","",$price_tmp[1][0]);
}



//상품명
$pname_tmp = "";
$pname = "";
preg_match_all("|<meta property=\"og:title\" content=\"(.*)\"/>|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
$pname = strip_tags($pname_tmp[1][0]);

//기본 이미지
$images_tmp = "";
$prod_img_src = "";
preg_match_all("|<a id=\"frontrow-p\" href=\"(.*)\" target=\"_blank\">|U",$results,$images_tmp, PREG_PATTERN_ORDER);
$prod_img_src = $images_tmp[1][0];
if(empty($prod_img_src)){
    preg_match_all("|<img src=\"(.*)\" id=\"detailImage\"|U",$results,$images_tmp, PREG_PATTERN_ORDER);
    $prod_img_src = $images_tmp[1][0];
}
if(empty($prod_img_src)){
    preg_match_all("|<img src=\"(.*)\" class=\"zoomHover|U",$results,$images_tmp, PREG_PATTERN_ORDER);
    $prod_img_src = $images_tmp[1][0];
}
if(empty($prod_img_src)){
    preg_match_all("|<img src=\"(.*)\" class=\" gae-click|U",$results,$images_tmp, PREG_PATTERN_ORDER);
    $prod_img_src = $images_tmp[1][0];
}
if(substr($prod_img_src,0,5) != "http:"){
	$prod_img_src = "http:".$prod_img_src;
}

//상품설명의 위치를 찾기위한 루프
$prod_desc_start_line = "";
$prod_desc_end_line = "";
$color_start_line = "";
$color_end_line = "";
for($i=0;$i < count($datas);$i++){
	$data = $datas[$i];
	
    if(!$prod_desc_start_line && (substr_count($data,"<div class=\"description\">") || substr_count($data,"class=\"product-description\">"))){
		$prod_desc_start_line = $i+1;
	}

    if(($prod_desc_start_line && !$prod_desc_end_line)&& (substr_count($data,"</ul></div>") || substr_count($data,"</div>"))){
		$prod_desc_end_line = $i+1;
	}
    if(!$color_start_line && substr_count($data,"<select id=\"color\"")){
		$color_start_line = $i+1;
	}

    if(($color_start_line && !$color_end_line)&& substr_count($data,"</select>")){
		$color_end_line = $i+1;
	}
}
//print_r($datas);
//echo "start: ".$prod_desc_start_line."- end: ".$prod_desc_end_line;
//상품설명
$prod_desc_prod = "";
for($i=$prod_desc_start_line;$i < $prod_desc_end_line;$i++){
	$prod_desc_prod .= $datas[$i];
}

$prod_desc_prod = iconv("iso-8859-1","utf-8//TRANSLIT",$prod_desc_prod) ;

$prod_desc_prod = str_replace(array("","","Read More"),"",$prod_desc_prod);
$prod_desc_prod = strip_tags($prod_desc_prod,"<style><table><tr><td><span><li>");

$color_select_area = "";
for($i=$color_start_line;$i < $color_end_line;$i++){
	$color_select_area .= $datas[$i];
}

// 다른색상 상품이미지
$optionnal_img_tmp = "";
$goods_detail_images = "";
$img_name_tmp = "";
$goods_detail_images_name = "";
preg_match_all("|\[(.*)\]\['MULTIVIEW'\]\['.*'\] = '(.*)';|U",$results,$optionnal_img_tmp, PREG_PATTERN_ORDER);    
/** 고객 요청으로 제한 취소. 12.12.28 bgh
if(!empty($color_select_area)){ //컬러별로 있는경우 사진이 너무 많아서 정면이미지만 가져오도록 제한.
    preg_match_all("|\[(.*)\]\['MULTIVIEW'\]\['p'\] = '(.*)';|U",$results,$optionnal_img_tmp, PREG_PATTERN_ORDER);
}else{
    preg_match_all("|\[(.*)\]\['MULTIVIEW'\]\['.*'\] = '(.*)';|U",$results,$optionnal_img_tmp, PREG_PATTERN_ORDER);    
}
*/
//print_r($optionnal_img_tmp);

$_key = 0;
$_option_name = "";
$_next_option_name = "";
for($i=0 ;$i < count($optionnal_img_tmp[2]);$i++){
    
	$goods_detail_images[$i] = $optionnal_img_tmp[2][$i];
    preg_match_all("|<option .*>(.*)</option>|U",$color_select_area,$img_name_tmp, PREG_PATTERN_ORDER);
    
    $goods_detail_images_name[$i] = $img_name_tmp[1][$_key];
    $_option_name = $optionnal_img_tmp[1][$i];
	$_next_option_name = $optionnal_img_tmp[1][$i + 1];
    if($_option_name != $_next_option_name){
        $_key++;
    }
    
    
}
//print_r($img_name_tmp);
//이미지 못찾을경우
if(empty($optionnal_img_tmp[2])){
    $goods_detail_images[0] = $prod_img_src;
}



//print_r($goods_detail_images_name);
//print_r($goods_detail_images);
//$goods_detail_images_name = $detail_images_name[1];

$_prod_desc = "";
if(count($goods_detail_images) > 0){
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
    $prod_desc_prod = $_prod_desc;
}




/**
 * 컬러이름
 */
 $colorname_tmp = "";
 $colorname_array = "";
preg_match_all("|var colorNames = (.*);|U",$results_oneline,$colorname_tmp, PREG_PATTERN_ORDER);
$colorname_array = objectToArray(json_decode(str_replace("'","\"",$colorname_tmp[1][0])));
//print_r($colorname_array);
/**
 * 옵션 종류
 * ex)  [0] -> d15
 *      [1] -> d46
 */
$dimensions_tmp = "";
$dimensions_array = "";
preg_match_all("|var dimensions = (.*);|U",$results, $dimensions_tmp, PREG_PATTERN_ORDER);
$dimensions_array = objectToArray(json_decode($dimensions_tmp[1][0]));
//print_r($dimensions_array);
/**
 * 옵션이름 dimension name
 * ex) [d15] -> size
 *     [d46] -> inseam
 */
$dimensionsname_tmp = "";
$dimensionsname_array = "";
preg_match_all("|var dimensionIdToNameJson = (.*);|U",$results,$dimensionsname_tmp, PREG_PATTERN_ORDER);
$dimensionsname_array = objectToArray(json_decode($dimensionsname_tmp[1][0]));
//print_r($dimensionsname_array);

/**
 * 옵션 값의 이름
 * ex) 28,30,31,32
 */
$valuename_tmp = "";
$valuename_array = "";
preg_match_all("|var valueIdToNameJSON = (.*);|U",$results, $valuename_tmp, PREG_PATTERN_ORDER);
$valuename_array = objectToArray(json_decode($valuename_tmp[1][0]));
//print_r($valuename_array);

/**
 * 재고여부
 */
$stock_tmp = "";
$stock_array = "";
preg_match_all("|var stockJSON = (.*);|U",$results,$stock_tmp, PREG_PATTERN_ORDER);
$stock_array = objectToArray(json_decode($stock_tmp[1][0]));
//print_r($stock_array);

/**
 * 컬러별 가격 
 */
$price_by_color_tmp = "";
$price_by_color_array = "";
preg_match_all("|var colorPrices = (.*);|U",$results_oneline,$price_by_color_tmp, PREG_PATTERN_ORDER);
$price_by_color_array = objectToArray(json_decode(str_replace("'","\"",$price_by_color_tmp[1][0])));
//print_r($price_by_color_array);

/**
 * 알아볼수 있게 변환해서 options[]에 넣기
 * 
 */
$option_key = 0;
$option_tmp = "";
for($i = 0; $i < count($stock_array); $i++){

    $_price[$i] = str_replace("$","",$price_by_color_array[$stock_array[$i][color]][now]);
    
    $stock_array[$i][color] = $colorname_array[$stock_array[$i][color]];
    if($stock_array[$i][color] != ""){
        $stock_array[$i][$dimensions_array[0]] = $valuename_array[$stock_array[$i][$dimensions_array[0]]][value];
        $option_tmp[$i] = $stock_array[$i][color]." / ".$stock_array[$i][$dimensions_array[0]]." ".$dimensionsname_array[$dimensions_array[0]];
        if(isset($dimensions_array[1])){
            $stock_array[$i][$dimensions_array[1]] = $valuename_array[$stock_array[$i][$dimensions_array[1]]][value];
            $option_tmp[$i] .= " / ".$stock_array[$i][$dimensions_array[1]]." ".$dimensionsname_array[$dimensions_array[1]];
        }
        $option_tmp[$i] .= " / 재고:".$stock_array[$i][onHand];
    }
    
}

$options = "";
if(is_array($option_tmp)){
    $options[$option_key][option_type] = "9";
    $options[$option_key][option_name] = "OPTION";
    $options[$option_key][option_kind] = "b";
    $options[$option_key][option_use] = "1";
    
    $insert_key = 0;
    foreach($option_tmp as $key => $value):
        $options[$option_key][details][$insert_key][option_div] = str_replace("'","",$value);
    	$options[$option_key][details][$insert_key][price] = $_price[$key];
        $insert_key++;
    endforeach;
}
//print_r($stock_array);
//print_r($options);



if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}

$goods_desc_copy = 1;

?>