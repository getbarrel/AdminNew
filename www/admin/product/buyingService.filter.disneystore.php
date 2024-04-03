<?
//$bs_url = "http://www.disneystore.com/disney/store/DSIProcessWidget?catalogId=10002&langId=-1&storeId=10051&templateId=Width-3_4-ProductList&widgetName=items_listing&widgetObjId=objItemsListing&sectionName=Right&initialN=1000228+1000763&navNum=96&numDim=&N=1000228+1000763&zoneName=DisneyNavigationPageZone";
/**
 * 디즈니 사이트 리뉴얼로 인해 필터 재작업 2012-10-12 bgh
 */

$results = "";
$soldout_message = "";

if(false){
$snoopy = new Snoopy;
$snoopy->fetch($bs_url);
$results =  $snoopy->results;
}else{
	$Tag = curl_init();

	curl_setopt( $Tag , CURLOPT_URL , "$bs_url" ); 

	ob_start();
	curl_exec( $Tag );
	curl_close( $Tag );
	$results = ob_get_contents();
	ob_clean();

	//echo $results;

}
//필터 맨아래쪽 별도 팝업페이지에서 상세 이미지 가져올수 있도록 처리
//$results = json_decode($results);
//echo count($results->items);
//print_r($results->items[1]->link);
//print_r($results);
//exit;

/**
 * 변수명
 * 
 * $pcode = 상품코드(사이트명_코드) #주로productId
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
//curl로 받은 데이터 줄바꿈
$datas = "";
$datas = split("\n",$results); 

//상품코드<div id="productId1" class="productId">1294731</div>
$pcode_tmp = "";
$pcode = "";
preg_match_all("|s.products=\";(.*)\";|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
$pcode = $bs_site."_".$pcode_tmp[1][0];

//상품가격
$price_tmp = "";
$price = "";

//할인일때
preg_match_all("|<span itemprop=\"price\">(.*)</span>|U",$results,$price_tmp, PREG_PATTERN_ORDER);
$price = str_replace("$","",$price_tmp[1][0]);
//echo $price;

//할인아닐때 형태
if(empty($price)){
    preg_match_all("|<p class=\"price\" itemprop=\"price\">(.*)</p>|U",$results,$price_tmp, PREG_PATTERN_ORDER);
    $price = str_replace("$","",$price_tmp[1][0]);
}

$listprice_tmp = "";
$listprice = "";
preg_match_all("|<p class=\"price regular\">.*[$](.*)[,].*</p>|U",$results,$listprice_tmp, PREG_PATTERN_ORDER);
$listprice = str_replace("$","",$listprice_tmp[1][0]);


//상품명
$pname_tmp = "";
$pname = "";
preg_match_all("|<h1 itemprop=\"name\">(.*)</h1>|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
$pname = strip_tags($pname_tmp[1][0]);

//기본 이미지
$images_tmp = "";
$prod_img_src = "";
preg_match_all("|<link rel=\"image_src\" href=\"(.*)\" />|U",$results,$images_tmp, PREG_PATTERN_ORDER);
$prod_img_src = str_replace("thumb","yetidetail",$images_tmp[1][0]);

if(substr($prod_img_src,0,5) != "http:"){
	$prod_img_src = "http:".$prod_img_src;
}
//상품설명
$prod_desc_prod_tmp = "";
$prod_desc_prod = "";
preg_match_all("|<p class=\"description\">(.*)</p>|U",$results,$prod_desc_prod_tmp, PREG_PATTERN_ORDER);
$prod_desc_prod = $prod_desc_prod_tmp[1][0];


//상품설명과 옵션의 위치를 찾기위한 루프
$prod_desc_start_line = "";
$prod_desc_end_line = "";
$option_start_line = "";
$option_end_line = "";
for($i=0;$i < count($datas);$i++){
	$data = $datas[$i];
	
    if(!$prod_desc_start_line && substr_count($data,"<div class=\"longDescription column columnLeft\">")){
		$prod_desc_start_line = $i+1;
	}

    if(($prod_desc_start_line && !$prod_desc_end_line)&& substr_count($data,"</div>")){
		$prod_desc_end_line = $i+1;
	} 
    
	if(!$option_start_line && substr_count($data,"<div id=\"objVariantSelector1_data\" class=\"dojodata\">")){
		$option_start_line = $i;
	}
	
	if(($option_start_line && !$option_end_line) && substr_count($data,"</div>")){
		$option_end_line = $i+1;
	}
    // 품절인 경우
    if(substr_count($data,"type\":\"soldout")){
        $soldout_message = "Sold Out";
    }

}
//추가상품설명
$prod_desc_prod .= "<br>";
for($i=$prod_desc_start_line;$i < $prod_desc_end_line;$i++){
	$prod_desc_prod .= $datas[$i];
}

$prod_desc_prod = iconv("iso-8859-1","utf-8//TRANSLIT",$prod_desc_prod) ;

$prod_desc_prod = str_replace(array("","","Shipping"),"",$prod_desc_prod);
$prod_desc_prod = strip_tags($prod_desc_prod,"<style><h3><table><tr><td><span><li><img>");
//print_r($prod_desc_prod);


// 다른색상 상품이미지
$optionnal_img_tmp = "";
$goods_detail_images = "";
//echo count($goods_detail_images);
//추가이미지 갯수
preg_match_all("|data-numaltimages=\"(.*)\">|U",$results,$optionnal_img_tmp, PREG_PATTERN_ORDER);
$optionnal_img_count = $optionnal_img_tmp[1][0];

//print_r($optionnal_img_count);
//echo count($optionnal_img_tmp[1]);
if($optionnal_img_count > 0){
	for($i=0 ;$i < $optionnal_img_count;$i++){
		
		$goods_detail_images[$i] = str_replace("?\$yetidetail","-".($i+1)."?\$yetidetail",$prod_img_src);
		if(substr_count($goods_detail_images[$i],"http:") == 0){
			$goods_detail_images[$i] = "http:".$goods_detail_images[$i];
		}
		//preg_match_all("|<option value=\"".$optionnal_img_tmp[2][$i]."\".*>(.*)</option>|U",$results,$img_name_tmp, PREG_PATTERN_ORDER);
		//$goods_detail_images_name[$i] = $img_name_tmp[1][0];
		
	}
}
//print_r($goods_detail_images_name);
//print_r($goods_detail_images);
//exit;
//echo is_array($goods_detail_images);


	$_prod_desc = "<table align=center>";
	$_prod_desc .= "<tr><td align=center><div style='text-align:left;'>".$prod_desc_prod."</div></td></tr>";
if(count($goods_detail_images) > 0 && is_array($goods_detail_images)){
	for($i=0;$i < count($goods_detail_images);$i++){
		$_prod_desc .= "<tr><td align=center style='padding:0px 0px 0px 0px;'><b>".$goods_detail_images_name[$i]."</b></td></tr>";
		$_prod_desc .= "<tr><td align=center style='padding:5px 0px 30px 0px;'><IMG src=\"".$goods_detail_images[$i]."\"></td></tr>";
	}
    $_prod_desc .= "<tr><td align=center style='padding:5px 0px 30px 0px;'><IMG src=\"".$prod_img_src."\"></td></tr>";
}else{
	$_prod_desc .= "<tr><td align=center style='padding:5px 0px 30px 0px;'><IMG src=\"".$prod_img_src."\"></td></tr>";
}
	$_prod_desc .= "</table>";

	$prod_desc_prod = $_prod_desc;


// 옵션의 문자열 저장
$options_str = "";
for($i=$option_start_line;$i < $option_end_line;$i++){
	$options_str .= $datas[$i];	
}
//$options_str = str_replace("\n","",$options_str);
//
$cut_array = array("<div id=\"objVariantSelector1_data\" class=\"dojodata\">","</div>","<!--","-->","\r","\n"," ");
$options_str = str_replace($cut_array,"",$options_str);
//print_r(str_replace($cut_array,"",$options_str));

$options_array = "";
$option = "";
$label = "";
$options_array = objectToArray(json_decode($options_str));
//print_r($options_array); 
if(is_array($options_array)&&$options_array[variance][0][item]!=""){
    
    $option_num = 1;
    $option[0] = "OPTION";
    if($options_array[control][1] !== NULL){
        //두번 째 옵션있는 경우
        $label = $options_array[control][0][label];
        
        $i = 0; //item
        $a = 0; //available
        while(true){
            
            $option[$option_num] = $label.":".$options_array[variance][$i][item]." / ".$options_array[variance][$i][availability][$a][available];
            if($options_array[variance][$i][availability][$a+1][available] !== NULL){
                $a++;
                
            }else{
                $i++;
                $a = 0;
                if($options_array[variance][$i] == NULL){
                    break;
                }
            }
            $option_num++;
            
        }
        
        //  재고있는 색상 $options_array[variance][$i][availability][$j][abailable]
        
        
        
    }else{
        //단일 옵션
        $label = $options_array[control][0][label];
        
        for($i=0;$i<count($options_array[variance]);$i++ ){
            if($options_array[variance][$i][availability][0][available] == "true")
                $option[$i+1] = $label.":".$options_array[variance][$i][item];
        }
    }


}

//print_r($option);

if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}

$goods_desc_copy = 1;

?>