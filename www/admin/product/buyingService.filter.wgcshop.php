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
 * 
 * 
 */

//curl로 받은 데이터 줄바꿈
//$datas = split("\n",$results); 
//print_r($results); 
//exit;

//상품코드
$pcode_tmp = "";
$pcode = "";
$__bs_url ="";

$__bs_url = split("[?]",$bs_url);
parse_str($__bs_url[1],$pcode_tmp);
$pcode = $bs_site."_".$pcode_tmp[item];

/*
preg_match_all("|<input type=hidden name=item value='(.*)'>|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
$pcode = $bs_site."_".$pcode_tmp[1][0];
*/

//원가
$price_tmp = "";
$price = "";

preg_match_all("|var orig_total_price=(.*);|U",$results,$price_tmp, PREG_PATTERN_ORDER);
$price = trim($price_tmp[1][0]);


//상품명
$pname_tmp = "";
$pname = "";
preg_match_all("|<div class=\"product_desc\">(.*)</div>|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
$pname = trim($pname_tmp[1][0]);
//$pname = iconv("euc-kr","utf-8",$pname);


//기본 이미지
$images_tmp = "";
$prod_img_src = "";
preg_match_all("|<link rel=\"image_src\" href=\"(.*)\" />|U",$results,$images_tmp, PREG_PATTERN_ORDER);
$prod_img_src = trim($images_tmp[1][0]);
/*
if(substr($prod_img_src,0,5) != "http:"){
	$prod_img_src = "http://lioele.com".str_replace('..',"",$prod_img_src);
}
*/

//상세 이미지
$goods_detail_images = "";
$goods_detail_images_tmp = "";
preg_match_all("|another.document.write\(\"<img src=(.*)/>\"\);|U",$results,$goods_detail_images_tmp, PREG_PATTERN_ORDER);
$goods_detail_images[0] = trim($goods_detail_images_tmp[1][0]);

//echo $goods_detail_images[0];
if($goods_detail_images[0] == '../../WGC_Shop/images/' ){
	$goods_detail_images[0] = $prod_img_src;
}else{
	if(substr($goods_detail_images[0],0,5) != "http:"){
		$goods_detail_images[0] = "http://www.wgcshop.com/".str_replace('../../',"",$goods_detail_images[0]);
	}
}

$datas = "";
$datas = split("\n",$results);

//상품 설명의 위치를 찾기위한 루프
$desc_prod_start_line = "";
$desc_prod_end_line = "";


for($i=0;$i < count($datas);$i++){

	$data =$datas[$i];

    //상품 상세이미지 위치
    if(!$desc_prod_start_line && substr_count($data,"id=\"table10\"")){
		$desc_prod_start_line = $i;
	}
	
	if(($desc_prod_start_line && !$desc_prod_end_line) && substr_count($data,"</table>")){
		$desc_prod_end_line = $i;
	}
 
    if(substr_count($data,"<span class=page_error>Out of Stock</span>") || substr_count($data,"<span class=page_error>Discontinued</span>")){
        $soldout_message = true;
    }

}

for($i=$desc_prod_start_line;$i <= $desc_prod_end_line;$i++){
	$prod_desc_prod .= $datas[$i];
}

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