<?
/**
 * 필수 사용 변수 초기화 12.05.14 bgh 
 */
unset($price);
unset($listprice);
unset($saleprice);
unset($pname);
unset($pcode);
unset($prod_img_src);
unset($saleprice);
unset($option);
unset($options);
unset($prod_desc_prod);
unset($soldout_message);

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
preg_match_all("|todayList.putTodayList\('(.*)',|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
$pcode = $bs_site."_".$pcode_tmp[1][0];


//원가
$price_tmp = "";
$price = "";
preg_match_all("| <span class=\"thr\">(.*)</span>|U",$results,$price_tmp, PREG_PATTERN_ORDER);
$price = trim(str_replace("원","",$price_tmp[1][0]));

//echo "할인가(판매가)".$price;

//상품명
$pname_tmp = "";
$pname = "";
preg_match_all("|<div class=\"goods_name\">.*\n.*<span>(.*)</span>|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
$pname = strip_tags($pname_tmp[1][0]);

//기본 이미지
$images_tmp = "";
$prod_img_src = "";
preg_match_all("|<img id=\"imgPrt_x2\" src=\"(.*)\".* />|U",$results,$images_tmp, PREG_PATTERN_ORDER);
$prod_img_src = $images_tmp[1][0];

if(substr($prod_img_src,0,5) != "http:"){
	$prod_img_src = "http://www.aganet.co.kr".$prod_img_src;
}

$datas = "";
$datas = split("\n",$results);

//상품 상세이미지의 위치를 찾기위한 루프
$detailimg_start_line = "";
$detailimg_end_line = "";
$option_start_line = "";
$option_end_line = "";
$option2_start_line = "";
$option2_end_line = "";
for($i=0;$i < count($datas);$i++){
	$data = $datas[$i];
 

    //상품 상세이미지 위치
    if(!$detailimg_start_line && substr_count($data,"<div class=\"spec\">")){
		$detailimg_start_line = $i;
	}
	
	if(($detailimg_start_line && !$detailimg_end_line) && substr_count($data,"<!-- 상품상세정보_끝 -->")){
		$detailimg_end_line = $i-2;
	}
    
    /*
    if(substr_count($data,"<img src=\"http://image.etude.co.kr/images/common/btn/btn_soldOut.gif\" />")){
        $soldout_message = true;
    }
	*/
	
	if(!$option_start_line && substr_count($data,"class=\"SD_STYLE07\"")){
		$option_start_line = $i+1;
	}
	
	if(($option_start_line && !$option_end_line) && substr_count($data,"</select>")){
		$option_end_line = $i-2;
		//echo $i."<br><br>".$data."<br><br>";
	}
	
	if($option_end_line && !$option2_start_line && substr_count($data,"class=\"SD_STYLE07\"")){
		$option2_start_line = $i+1;
	}
	
	if(($option2_start_line && !$option2_end_line) && substr_count($data,"</select>")){
		$option2_end_line = $i-2;
		//echo $i."<br><br>".$data."<br><br>";
	}

}

// 상세 이미지
$detailimg_str = "";
for($i=$detailimg_start_line;$i <= $detailimg_end_line;$i++){
	$detailimg_str .= $datas[$i];
}

//print_r($detailimg_start_line);
//exit;
$optionnal_img_tmp = "";
if($detailimg_str){
	if(substr_count($detailimg_str,"IMG") && substr_count($detailimg_str,"src")){
		preg_match_all("|<IMG.*src=\"(.*)\".*>|U",$detailimg_str,$optionnal_img_tmp, PREG_PATTERN_ORDER);
	}elseif(substr_count($detailimg_str,"IMG") && substr_count($detailimg_str,"SRC")){
		preg_match_all("|<IMG.*SRC=\"(.*)\".*>|U",$detailimg_str,$optionnal_img_tmp, PREG_PATTERN_ORDER);
	}elseif(substr_count($detailimg_str,"img") && substr_count($detailimg_str,"src")){
		preg_match_all("|<img.*src=\"(.*)\".*>|U",$detailimg_str,$optionnal_img_tmp, PREG_PATTERN_ORDER);
	}
}
$goods_detail_images = "";
for($i=0 ;$i < count($optionnal_img_tmp[1]);$i++){
	/*
    if(substr_count($optionnal_img_tmp[1][$i],"http:") == 0){
        $optionnal_img_tmp[1][$i] = "http://www.etude.co.kr/".$optionnal_img_tmp[1][$i];    
    }
	*/
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


//판매중
unset($options_str);
unset($options2_str);
$options_str = "";
$options2_str = "";

for($i=$option_start_line;$i < $option_end_line;$i++){
	$options_str .= $datas[$i];	
}
//echo iconv("CP949","UTF-8",$options_str);

for($i=$option2_start_line;$i < $option2_end_line;$i++){
	$options2_str .= $datas[$i];	
}

$options = "";
$option = "";
$_option = "";
$options2 = "";
$_option2 = "";
$option2 = "";
if($options_str){

		preg_match_all("|<option.*>(.*)</option>|U",$options_str,$options, PREG_PATTERN_ORDER);

		$_option = $options[1];

 		for($i=0, $j=0;$i < count($_option);$i++){
			if((substr_count($options[0][$i],"onchange=\"chgMax(this)\"") && !substr_count($options[0][$i],"max=\"0\""))){

			}else{
				$option[$j] = str_replace("&nbsp;","",trim($_option[$i]));
				$j++;
			}
		}
}

if($options2_str){

		preg_match_all("|<option.*>(.*)</option>|U",$options2_str,$options2, PREG_PATTERN_ORDER);

		$_option2 = $options2[1];

		for($i=0, $j=0;$i < count($_option2);$i++){
			if((substr_count($options2[0][$i],"onchange=\"chgMax(this)\"") && !substr_count($options2[0][$i],"max=\"0\""))){

			}else{
				$option2[$j] = str_replace("&nbsp;","",trim($_option2[$i]));
				$j++;
			}
		}
		//print_r($option2);
		
}

if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}

unset($results);
unset($datas);
unset($dimensionType_tmp);
unset($images_tmp);
unset($img_name_tmp);
unset($old_price_tmp);
unset($option1_epos);
unset($option1_spos);
unset($option2_end_line);
unset($option2_start_line);
unset($option3_end_line);
unset($option3_start_line);
unset($option_end_line);
unset($option_start_line);
unset($optionnal_img_tmp);
unset($options1_str);
unset($options2_str);
unset($options3_str);
unset($pcode_tmp);
unset($price_tmp);
unset($listprice_tmp);
unset($prod_desc_end_line);
unset($prod_desc_prod_tmp);
unset($goods_detail_images_tmp);
unset($goods_detail_images_tmp2);
unset($_option);

$goods_desc_copy = 1;

?>