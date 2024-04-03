<?

$results = "";
$soldout_message = "";

$snoopy = new Snoopy;
$snoopy->fetch($bs_url);

$results = $snoopy->results;

//print_r($results);
//줄바꿈 제거
$results2 = "";
$results2 = str_replace("\n","",$results);

$datas = "";
$datas = split("\n",$results);
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

$pcode_tmp = "";
$pcode = "";
preg_match_all("|\"styleId\":(.*),|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
$pcode = $pcode_tmp[1][0];		
$pcode = trim($pcode);
$pcode = $bs_site."_".$pcode;

$pname_tmp = "";
$pname = "";
preg_match_all("|styleName\":\"(.*)\"|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
$pname = $pname_tmp[1][0];

$price_tmp = "";
$price = "";
preg_match_all("|coremetricsPrice\":\"(.*)\"|U",$results,$price_tmp, PREG_PATTERN_ORDER);
$price = $price_tmp[1][0];
$price = str_replace(array("&nbsp;","$"),"",$price);
$price = trim($price);

$img_tmp = "";
$prod_img_src = "";
preg_match_all("|<div class=\"fashion-photo-wrapper\".*<img alt=\".*\" src=\"(.*)\" />|U",$results2,$img_tmp, PREG_PATTERN_ORDER);
$prod_img_src = $img_tmp[1][0];

$prod_desc_start_line = "";
$prod_desc_end_line = "";
$prod_desc_start_line2 = "";
$prod_desc_end_line2 = "";
for($i=0;$i < count($datas);$i++){
	$data = $datas[$i];
	
	if(!$prod_desc_start_line && substr_count($data,"<div class=\"content\">")){
		$prod_desc_start_line = $i+1;
	}
    if(!$prod_desc_end_line && substr_count($data,"<!-- End StyleFeatures -->")){
		$prod_desc_end_line = $i+1;
	}

	if(!$prod_desc_start_line2 && substr_count($data,"<div id=\"sizeinfochart\">")){
		$prod_desc_start_line2 = $i+1;
	}
    if(!$prod_desc_end_line2 && substr_count($data,"<!-- End SizeFitInformation -->")){
		$prod_desc_end_line2 = $i;
	}
    
    if(!$prod_color_start_line && substr_count($data,"<!-- Begin SkuSelector -->")){
		$prod_color_start_line = $i+1;
	}
    if(!$prod_color_end_line && substr_count($data,"<!-- End SkuSelector -->")){
		$prod_color_end_line = $i;
	}
}
//print_R($prod_color_end_line);
$prod_desc_prod = "";
for($i=$prod_desc_start_line;$i < $prod_desc_end_line;$i++){
	$prod_desc_prod .= $datas[$i];
}

$prod_desc_prod = strip_tags($prod_desc_prod,"<table><tr><td><span><ul><li><img>");

$prod_desc_prod2 = "";
if($prod_desc_start_line2){
    for($i=$prod_desc_start_line2;$i < $prod_desc_end_line2;$i++){
    	$prod_desc_prod2 .= $datas[$i];
    }
    
    $prod_desc_prod2 = strip_tags($prod_desc_prod2,"<table><tr><td><span><ul><li><img>");
    
    $prod_desc_prod .="<br /><br />".$prod_desc_prod2;
}
/** 
 *  기본색상의 추가이미지 추가
 */
 $optionnal_img_tmp = "";
 $goods_detail_images = "";
preg_match_all("|<img src=\"(.*)\" alt=\"Alternate Product Image .*\" width=\"35\" id=\"thumbnail_.*\" />|U",$results, $optionnal_img_tmp, PREG_PATTERN_ORDER);

for($i=0 ;$i < count($optionnal_img_tmp[1]);$i++){
	
	$goods_detail_images[$i] = str_replace("Mini","Large",$optionnal_img_tmp[1][$i]);
    $img_count = $i;
}
//print_r($goods_detail_images);
$color_box_flag = false;

/** 
 * 컬러선택시 나오는 이미지
 */ 
preg_match_all("|var imageViewerProperties=(.*);|U",$results,$optionnal_img_tmp, PREG_PATTERN_ORDER);
$img_array = objectToArray(json_decode($optionnal_img_tmp[1][0]));
//print_r($img_array);
$img_name_array = array_keys($img_array[imageRegistry]);
//print_r($img_name_array);
$key = 0;
foreach($img_array["swatchRegistry"] as $sr):
    $instock_img_list[$key] = $sr[imagePath];
    $key++;
endforeach;
//print_r($instock_img_list);
$img_base_name = substr("$prod_img_src",0,strrpos($prod_img_src,"Large"));
//echo $img_base_name;
$key = $img_count + 1;
for($i=0 ;$i < count($img_name_array);$i++){
    for($j=0;$j < count($instock_img_list);$j++){
	   if($instock_img_list[$j] == $img_name_array[$i] && substr_count($prod_img_src,$img_name_array[$i]) == 0){
	       $goods_detail_images[$key] = $img_base_name."Large/".$img_name_array[$i].".jpg";
           $color_box_flag = true; // 다른 컬러가 있는 경우에만 컬러명 값 추가.
           $key++;
       }
    }
    //$goods_detail_images[$i] = $img_base_name."Large/".$img_name_array[$i].".jpg";

}

//print_r($goods_detail_images);

if($color_box_flag){
    if($prod_color_start_line){
        for($i=$prod_color_start_line;$i < $prod_color_end_line;$i++){
        	$prod_color_box .= $datas[$i];
        }
    
    $prod_color_box = strip_tags($prod_color_box,"<div><table><tr><td><span><ul><li><img>");
    
    $prod_desc_prod .="<br /><br />".$prod_color_box;
    
    }
    //print_r("-----------------------||");
    //print_r($prod_color_box);
    //print_r("||-----------------------");
}
/**/
//print_r($goods_detail_images);

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

$option_tmp = "";
$option_array = "";
$firstKey = "";
$secondKeys = "";
$option1_name = "";
$option2_name = "";
preg_match_all("|\"styleJson\":(.*);|U",$results,$option_tmp, PREG_PATTERN_ORDER);
$option_array = objectToArray(json_decode(substr($option_tmp[1][0],0,strrpos($option_tmp[1][0],"}")),true));
$firstKey = array_keys($option_array);
$firstKey = $firstKey[0];
//print_r($option_array);

$secondKeys = array_keys($option_array[$firstKey][skus]);
$option1_name = $option_array[$firstKey][dropDown1Caption];
$option2_name = $option_array[$firstKey][dropDown2Caption];
//print_r($secondKeys);
$i = 0;

$option_tmp = "";
while(true){
    
    $option_tmp[$i] = $option_array[$firstKey][skus][$secondKeys[$i]][dropDown2Value]." / ";
    $option_tmp[$i] .= $option_array[$firstKey][skus][$secondKeys[$i]][dropDown1Value]." ".$option1_name;
    if($option_array[$firstKey][skus][$secondKeys[$i]][priceFilterValue] !== NULL){
        $option_tmp[$i] .= " / ".$option_array[$firstKey][skus][$secondKeys[$i]][priceFilterValue];
    }
    
    $_price[$i] = $option_array[$firstKey][skus][$secondKeys[$i]][coremetricsPrice];
    
    if($option_array[$firstKey][skus][$secondKeys[$i+1]] !== NULL){
        $i++;
    }else{
        break;
    }
}
//print_r($option_tmp);
$option_key = 0;
$options = "";
if(is_array($option_tmp)){
    $options[$option_key][option_type] = "9";
    $options[$option_key][option_name] = "OPTION";
    $options[$option_key][option_kind] = "b";
    $options[$option_key][option_use] = "1";
    
    for($i=0;$i < count($option_tmp);$i++){
    	$options[$option_key][details][$i][option_div] = $option_tmp[$i];
    	$options[$option_key][details][$i][price] = $_price[$i];
    	$options[$option_key][details][$i][etc1] = $option_tmp[$i];
    	//$options[$option_key][details][$i][thumb_images] = $thumb_images[$i];
    	//$options[$option_key][details][$i][goods_images] = $goods_detail_images[$i];
    	
    }
}
//asort($options[0][details]);
//print_r($options);

if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}

$goods_desc_copy = 1;

?>