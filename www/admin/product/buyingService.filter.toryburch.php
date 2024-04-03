<?

$results = "";
$soldout_message = "";

$snoopy = new Snoopy;
$snoopy->fetch($bs_url);
$results =  $snoopy->results;

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
 * $$goods_detail_images = 다른색상 상품이미지
 * 
 * ************특이사항*************
 * 재고 요청 주소 http://www.toryburch.com/on/demandware.store/Sites-ToryBurch_US-Site/default/Product-GetVariants?pid=14121166&format=json
 * 
 * 색상별 다른가격 상품 간혹 있음.(가격재고 옵션 사용)
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

//curl로 받은 데이터 줄바꿈
$datas = "";
$datas = split("\n",$results);
$result2 = "";
$result2 = str_replace("\n"," ",$results);

 
//상품코드
$pcode_tmp = "";
$pcode = "";
preg_match_all("|var omnProductIDs = \"(.*)\";|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
$pcode = $bs_site."_".$pcode_tmp[1][0];

//상품가격
$old_price_tmp = "";
$old_price = "";
preg_match_all("|<div class=\"standardprice\">(.*)<script|U",$result2,$old_price_tmp, PREG_PATTERN_ORDER);
$old_price = str_replace("$","",$old_price_tmp[1][0]);

$price_tmp = "";
$price = "";
preg_match_all("|<div class=\"salesprice standardP\">(.*)</div>|U",$result2,$price_tmp, PREG_PATTERN_ORDER);
$price = str_replace("$","",$price_tmp[1][0]);

if($old_price == ""){
    $old_price = $price;
}

//상품명
$pname_tmp = "";
$pname = "";
preg_match_all("|<h1 class=\"productname\" itemprop=\"name\">(.*)</h1>|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
$pname = strip_tags($pname_tmp[1][0]);

//기본 이미지
$images_tmp = "";
$prod_img_src_tmp = "";
$prod_img_src = "";
preg_match_all("|pViewer=createPhotoViewer(.*);|U",$results,$images_tmp, PREG_PATTERN_ORDER);
$mixed_search = array("\"","(",")");
$prod_img_src_tmp = explode(",",str_replace($mixed_search,"",$images_tmp[1][0]));
for($i=0; $i < 2 ; $i++){
    $prod_img_src .= $prod_img_src_tmp[$i];
}
//print_r($prod_img_src_tmp);


if(substr($prod_img_src,0,5) != "http:"){
	$prod_img_src = "http:".$prod_img_src;
}

//상품설명
$prod_desc_prod_tmp = "";
$prod_desc_prod = "";
preg_match_all("|<div id=\"pdpTab1\" itemprop=\"description\">(.*)</div>|U",$result2,$prod_desc_prod_tmp);
$prod_desc_prod = $prod_desc_prod_tmp[1][0];
preg_match_all("|<div id=\"pdpTab2\">(.*)</div>|U",$result2,$prod_desc_prod_tmp);
$prod_desc_prod .= $prod_desc_prod_tmp[1][0];
preg_match_all("|<div id=\"pdpTab3\">(.*)</div>|U",$result2,$prod_desc_prod_tmp);
$prod_desc_prod .= $prod_desc_prod_tmp[1][0];

$prod_desc_prod = strip_tags($prod_desc_prod,"<style><table><tr><td><span><li><img><p>");

// 다른색상 상품이미지
$optionnal_img_tmp = "";
preg_match_all("|<img class=\"swatchimage\" src=\"(.*)\" />|U",$results,$optionnal_img_tmp, PREG_PATTERN_ORDER);

$goods_detail_images_tmp2 = "";
$goods_detail_images = "";
$img_name_tmp = "";
$goods_detail_images_name = "";
for($i=0 ;$i < count($optionnal_img_tmp[1]);$i++){
	
    $goods_detail_images_tmp2 = explode("_SW",$optionnal_img_tmp[1][$i]);
    $goods_detail_images[$i] = $goods_detail_images_tmp2[0];  
    preg_match_all("|<div class=\"swatchDispName\">(.*)</div>|U",$results,$img_name_tmp, PREG_PATTERN_ORDER);
    $goods_detail_images_name[$i] = $img_name_tmp[1][$i];
    
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
}

$stock_url = "http://www.toryburch.com/on/demandware.store/Sites-ToryBurch_US-Site/default/Product-GetVariants?pid=".$pcode_tmp[1][0]."&format=json";
$snoopy = new Snoopy;
$snoopy->fetch($stock_url);
$stock_results = "";
$stock_results =  $snoopy->results;

$stock_array = "";
$stock_array = objectToArray(json_decode($stock_results));
//print_r($stock_array);

$_color = "";
$_size = "";
$_stock = "";
$option_tmp = "";
$_price = "";
if($stock_array){
    $i = 0;
    $j = 0;
    while(true){
        
        $_color = $stock_array[variations][variants][$i][attributes][color];
        $_size = $stock_array[variations][variants][$i][attributes][size];
        $_stock = $stock_array[variations][variants][$i][inStock];
        
        if($_stock == 1){
            $option_tmp[$j] = $_color." / ".$_size." Size";
            $_price[$j] = $stock_array[variations][variants][$i][pricing][sale];
            $j++;
        }
        if($stock_array[variations][variants][$i+1] != NULL)
            $i++;
        else
            break;
    }    
}
//print_r($option_tmp);

$options = "";
$option_key = 0;
if(is_array($option_tmp) && $option_tmp[0] != ""){
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

//print_r($options);




if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}

$goods_desc_copy = 1;

?>