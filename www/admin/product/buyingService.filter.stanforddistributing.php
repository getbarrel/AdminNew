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
*/


//curl로 받은 데이터 줄바꿈
//$datas = split("\n",$results); 
//print_r($results); 


//상품코드
$pcode_tmp = "";
$pcode = "";
preg_match_all("|<input type=\"hidden\" name=\"shop\" value=\"(.*)\">|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
$pcode = $bs_site."_".$pcode_tmp[1][0];


//원가
$price_tmp = "";
$price = "";
preg_match_all("|<strike>(.*)</strike>|U",$results,$price_tmp, PREG_PATTERN_ORDER);
$price_tmp = iconv("euc-kr","utf-8",$price_tmp[1][0]);
$price = trim(strip_tags(str_replace("원","",$price_tmp)));

if(!$price){
	preg_match_all("|<input type=\"hidden\" name=\"mprice\" value=\"(.*)\">|U",$results,$price_tmp, PREG_PATTERN_ORDER);
	$price = trim(strip_tags(str_replace("원","",$price_tmp[1][0])));
}

//상품명
$pname_tmp = "";
$pname = "";
preg_match_all("|<meta name=\"nate:title\" content=\"(.*)\">|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
$pname = strip_tags($pname_tmp[1][0]);
$pname = iconv("euc-kr","utf-8",$pname);


//기본 이미지
$images_tmp = "";
$prod_img_src = "";
preg_match_all("|name=PicMedium src=\"(.*)\" |U",$results,$images_tmp, PREG_PATTERN_ORDER);
$prod_img_src = $images_tmp[1][0];

if(substr($prod_img_src,0,5) != "http:"){
	$prod_img_src = "http://lioele.com".str_replace('..',"",$prod_img_src);
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
	$data = iconv("euc-kr","utf-8",$datas[$i]);
	
    //상품 상세이미지 위치
    if(!$detailimg_start_line && substr_count($data,"상품상세설명 시작")){
		$detailimg_start_line = $i+1;
	}
	
	if(($detailimg_start_line && !$detailimg_end_line) && substr_count($data,"상품상세설명 끝")){
		$detailimg_end_line = $i-1;
	}
    
    if(substr_count($data,"품절입니다")){
        $soldout_message = true;
    }

	if((!$option_start_line && substr_count($data,"name='opt_name")) && (substr_count($data,"value='타입'") || substr_count($data,"value='옵션'") || substr_count($data,"value='선택'") || substr_count($data,"value='색상선택'") || substr_count($data,"value='색상'") )){
		$option_start_line = $i;
	}
	
	if(($option_start_line && !$option_end_line) && substr_count($data,"</SELECT>")){
		$option_end_line = $i+1;
		//echo $i."<br><br>".$data."<br><br>";
	}
	
	if((!$option_start_line && substr_count($data,"name='opt_name")) && (substr_count($data,"value='타입'") || substr_count($data,"value='옵션'") || substr_count($data,"value='선택'") || substr_count($data,"value='색상선택'") || substr_count($data,"value='색상'") )){
		$option2_start_line = $i;
	}
	
	if(($option2_start_line && !$option2_end_line) && substr_count($data,"</SELECT>")){
		$option2_end_line = $i+1;
		//echo $i."<br><br>".$data."<br><br>";
	}

}


// 상세 이미지
$detailimg_str = "";
for($i=$detailimg_start_line;$i <= $detailimg_end_line;$i++){
	$detailimg_str .= $datas[$i];
}

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
$prod_desc_prod = "";
for($i=0 ;$i < count($optionnal_img_tmp[1]);$i++){
    if(substr_count($optionnal_img_tmp[1][$i],"http:") == 0){
        $optionnal_img_tmp[1][$i] = "http://www.etude.co.kr/".$optionnal_img_tmp[1][$i];    
    }
	$goods_detail_images[$i] = $optionnal_img_tmp[1][$i];
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


//판매중
$options_str = "";
for($i=$option_start_line;$i < $option_end_line;$i++){
	$data = iconv("euc-kr","utf-8",$datas[$i]);
	$options_str .= $data;	
}
//echo iconv("CP949","UTF-8",$options_str);

$options2_str = "";
for($i=$option2_start_line;$i < $option2_end_line;$i++){
	$data = iconv("euc-kr","utf-8",$datas[$i]);
	$options2_str .= $data;	
}

$options = "";
$_option = "";
$option = "";
if($options_str){

		preg_match_all("|name='opt_name.*value=[\",'](.*)[\",']|U",$options_str,$options, PREG_PATTERN_ORDER);

		$option[0] = $options[1][0];

		preg_match_all("|VALUE.*>(.*)<|U",$options_str,$options, PREG_PATTERN_ORDER);

		$_option = $options[1];

 		for($i=0, $j=1;$i < count($_option);$i++){
			$option[$j] = str_replace("&nbsp;","",trim($_option[$i]));
			$j++;
		}
}

$options2 = "";
$_option2 = "";
$option2 = "";
if($options2_str){

		preg_match_all("|name='opt_name.*value=[\",'](.*)[\",']|U",$options2_str,$options2, PREG_PATTERN_ORDER);

		$option2[0] = $options2[1][0];

		preg_match_all("|VALUE.*>(.*)<|U",$options_str,$options, PREG_PATTERN_ORDER);

		$_option2 = $options2[1];

		for($i=0, $j=1;$i < count($_option2);$i++){
			$option2[$j] = str_replace("&nbsp;","",trim($_option2[$i]));
			$j++;
		}		
}

//print_r($option);
//exit;

if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}


$goods_desc_copy = 1;

?>