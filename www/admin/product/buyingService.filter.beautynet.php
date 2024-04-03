<?

$results = "";
$soldout_message = "";

if(false){
    $Tag = curl_init();
    
    curl_setopt( $Tag , CURLOPT_URL , "$bs_url" ); 
    
    ob_start();
    curl_exec( $Tag );
    curl_close( $Tag );
    $results = ob_get_contents();
    ob_clean();
}else{
    $snoopy = new Snoopy;
    $snoopy->fetch($bs_url);
    $results =  $snoopy->results;

}
$results = iconv("euc-kr","utf-8",$results);
/**
 * substr_count 
 * array버전
 */
if (!function_exists('substr_count_array')) {
    function substr_count_array($haystack, $needle) {
        $count = 0;
        foreach ($needle as $substring) {
            $count += substr_count($haystack, $substring);
        }
        return $count;
    }
}
 
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
 * $$goods_detail_images = 상품 상세 이미지
 * 
 *  
 * *************** 특이사항 *****************
 * 
 * 미샤 
 * 
 * 할인없음, 옵션 없음
 * 
 * 
 */
//<td class='M_TT_13w'>12,800원   <a 

//curl로 받은 데이터 줄바꿈
//$datas = split("\n",$results); 
//print_r($results); 

//상품코드
$pcode_tmp = "";
$pcode = "";
preg_match_all("|<input type=hidden name='g_id' value='(.*)'>|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
$pcode = $bs_site."_".$pcode_tmp[1][0];


//판매가
$price_tmp = "";
$price = "";
preg_match_all("|<td class='M_TT_13w'>(.*)<a|U",$results,$price_tmp, PREG_PATTERN_ORDER);
$price = $price_tmp[1][0];
$price = str_replace("원","",$price);

//echo "할인가(판매가)".$price;

//상품명
$pname_tmp = "";
$pname = "";
preg_match_all("|<meta property=\"og:title\" content=\"(.*)\" />|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
$pname = strip_tags($pname_tmp[1][0]);

//기본 이미지
$images_tmp = "";
$prod_img_src = "";
preg_match_all("|<meta property=\"og:image\" content=\"(.*)\" />|U",$results,$images_tmp, PREG_PATTERN_ORDER);
$prod_img_src = $images_tmp[1][0];

if(substr($prod_img_src,0,5) != "http:"){
	$prod_img_src = "http://file.beautynet.co.kr/".$prod_img_src;
}

$datas = "";
$datas = split("\n",$results);

//상품설명과 옵션1~3의 위치를 찾기위한 루프
$is_desc = false;
$desc_end_line = "";
$detailimg_start_line = "";
$detailimg_end_line = "";
$desc_start_line = "";
for($i=0;$i < count($datas);$i++){
	$data = $datas[$i];

	if(($desc_start_line && !$desc_end_line) && substr_count($data,"</div>") && ($desc_start_line != $i)){
		$desc_end_line = $i;
	}

    //상품 상세이미지 위치
    if(!$detailimg_start_line && substr_count($data,"class='M_g_detail'")){
		$detailimg_start_line = $i;
	}
	
	if(($detailimg_start_line && !$detailimg_end_line) && substr_count($data,"<table")){
		$detailimg_end_line = $i;
        $desc_start_line = $i;
	}
    //상세설명이 있는지 찾기
    if(substr_count($data,"../skin/layout/goods/v2/images/ingredient_01.jpg") > 0){
        $is_desc = true;
    }
}

//상세설명의 문자열 저장
if($is_desc){
    $desc_str = "";
	$prod_desc_prod = "";
    for($i=$desc_start_line;$i < $desc_end_line;$i++){
        if(substr_count($datas[$i],"../skin") > 0){
            //echo $i;
            $datas[$i] = str_replace("../skin","http://shop.beautynet.co.kr/skin",$datas[$i]);
            //http://shop.beautynet.co.kr/skin/layout/goods/v2/images/ingredient_07.jpg
        }
    	$desc_str .= $datas[$i];	
    }


    $prod_desc_prod = str_replace("제목 없음","",strip_tags($desc_str,"<table><tr><td><br><font><b><p><img>"));
}
//print_r($desc_str);

// 상세 이미지
$detailimg_str = "";
for($i=$detailimg_start_line;$i <= $detailimg_end_line;$i++){
	$detailimg_str .= $datas[$i];	
}

$optionnal_img_tmp = "";
if($detailimg_str){
	preg_match_all("|src=\"(.*)\"|U",$detailimg_str,$optionnal_img_tmp, PREG_PATTERN_ORDER);
}

// 상세 이미지링크 중에서 이미지 아닌것 제거
$_optionnal_img_tmp = "";
$needle = array("jpg","JPG","png","PNG","gif","GIF");
for($i=0, $j=0;$i < count($optionnal_img_tmp[1]);$i++){
    if(substr_count_array($optionnal_img_tmp[1][$i],$needle) > 0){
        $_optionnal_img_tmp[$j] = $optionnal_img_tmp[1][$i];
        $j++;
    }
}

$goods_detail_images = "";
for($i=0 ;$i < count($_optionnal_img_tmp);$i++){
	
    if(substr_count($_optionnal_img_tmp[$i],"http:") == 0){
        $_optionnal_img_tmp[$i] = "http://file.beautynet.co.kr/".$_optionnal_img_tmp[$i];    
    }
	$goods_detail_images[$i] = $_optionnal_img_tmp[$i];
}
//print_r($goods_detail_images_name);
//print_r($goods_detail_images);
//$goods_detail_images_name = $detail_images_name[1];

if(count($goods_detail_images) > 0){
	$_prod_desc = "<table align=center>";
	$_prod_desc .= "<tr><td align=center><div style='text-align:left;'>".$prod_desc_prod."</div></td></tr>";
	for($i=0;$i < count($goods_detail_images);$i++){
		$_prod_desc .= "<tr><td align=center style='padding:5px 0px 30px 0px;'><IMG src=\"".$goods_detail_images[$i]."\"></td></tr>";
	}
	$_prod_desc .= "</table>";

	$prod_desc_prod = $_prod_desc;
}


if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}


$goods_desc_copy = 1;

?>