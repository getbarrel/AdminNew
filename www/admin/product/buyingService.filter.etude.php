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
 * 코리아바이즈 스크래핑 -> 상품원가를 가져옴. 할인가는 일단 버림.
 * 
 */


//curl로 받은 데이터 줄바꿈
//$datas = split("\n",$results); 
//print_r($results); 

//상품코드
$pcode_tmp = "";
$pcode = "";
preg_match_all("|<input type=\"hidden\" name=\"prdCd\" value=\"(.*)\" />|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
$pcode = $bs_site."_".$pcode_tmp[1][0];


//원가
$price_tmp = "";
$price = "";
preg_match_all("|<dd class=\"special\">(.*)</dd>|U",$results,$price_tmp, PREG_PATTERN_ORDER);
$price = strip_tags(str_replace("원","",$price_tmp[1][0]));

//echo "할인가(판매가)".$price;

//상품명
$pname_tmp = "";
$pname = "";
preg_match_all("|<input type=\"hidden\" name=\"prdNm\" value=\"(.*)\" />|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
$pname = strip_tags($pname_tmp[1][0]);

//기본 이미지
$images_tmp = "";
$prod_img_src = "";
preg_match_all("|<img id=\"detailView\" src=\"(.*)\"|U",$results,$images_tmp, PREG_PATTERN_ORDER);
$prod_img_src = $images_tmp[1][0];

if(substr($prod_img_src,0,5) != "http:"){
	$prod_img_src = "http://www.etude.co.kr".$prod_img_src;
}

$datas = "";
$datas = split("\n",$results);

//상품 상세이미지의 위치를 찾기위한 루프
$detailimg_start_line = "";
$detailimg_end_line = "";
for($i=0;$i < count($datas);$i++){
	$data = $datas[$i];
 
    //상품 상세이미지 위치
    if(!$detailimg_start_line && substr_count($data,"<textarea name=\"prdDesc\" style=\"display:none;\">")){
		$detailimg_start_line = $i;
	}
	
	if(($detailimg_start_line && !$detailimg_end_line) && substr_count($data,"</table>")){
		$detailimg_end_line = $i-1;
	}
    
    
    if(substr_count($data,"<img src=\"http://image.etude.co.kr/images/common/btn/btn_soldOut.gif\" />")){
        $soldout_message = true;
    }
}

// 상세 이미지
$detailimg_str = "";
for($i=$detailimg_start_line;$i <= $detailimg_end_line;$i++){
	$detailimg_str .= $datas[$i];
}

$optionnal_img_tmp = "";
if($detailimg_str){
	preg_match_all("|<img.*src=\"(.*)\".*>|U",$detailimg_str,$optionnal_img_tmp, PREG_PATTERN_ORDER);
}

$goods_detail_images = "";
for($i=0 ;$i < count($optionnal_img_tmp[1]);$i++){
    if(substr_count($optionnal_img_tmp[1][$i],"http:") == 0){
        $optionnal_img_tmp[1][$i] = "http://www.etude.co.kr/".$optionnal_img_tmp[1][$i];    
    }
	$goods_detail_images[$i] = $optionnal_img_tmp[1][$i];
}
//print_r($goods_detail_images_name);
//print_r($goods_detail_images);
//$goods_detail_images_name = $detail_images_name[1];

$prod_desc_prod = "";
if(count($goods_detail_images) > 0){
	$_prod_desc = "<table align=center>";
	$_prod_desc .= "<tr><td align=center><div style='text-align:left;'>".$prod_desc_prod."</div></td></tr>";
	for($i=0;$i < count($goods_detail_images);$i++){
		$_prod_desc .= "<tr><td align=center style='padding:5px 0px 30px 0px;'><IMG src=\"".$goods_detail_images[$i]."\"></td></tr>";
	}
	$_prod_desc .= "</table>";

	$prod_desc_prod = $_prod_desc;
}

//판매중인 옵션만 가져오기 salStatCdNm -> 판매중,일시품절
$options = "";
$_option = "";
$option = "";
preg_match_all("|salStatCdNm='판매중'>(.*)</option>|U",$results,$options, PREG_PATTERN_ORDER);

$_option = $options[1];
$option[0] = "옵션";
for($i=0;$i < count($_option);$i++){
	$option[$i] = trim($_option[$i]);
}
	//echo "#option";
    //print_r($option);

if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}

$goods_desc_copy = 1;

?>