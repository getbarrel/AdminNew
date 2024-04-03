<?


$results = "";
$soldout_message = "";


$snoopy = new Snoopy;
$snoopy->fetch($bs_url);
$results =  $snoopy->results;

$results = iconv("euc-kr","utf-8",$results);


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
 * id 나 name 없이 태그만 있는 값이 많아서 이슈발생 가능성 높음
 * 
 * 코리아바이즈 스크래핑 -> 상품원가를 가져옴. 할인가는 일단 버림.
 */


//curl로 받은 데이터 줄바꿈
//$datas = split("\n",$results); 
//print_r($results); 

//상품코드
$pcode_tmp = "";
$pcode = "";
preg_match_all("|<input type=\"hidden\" name=\"ps_goid\" value=\"(.*)\">|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
$pcode = $bs_site."_".$pcode_tmp[1][0];

//상품 원가
$listprice_tmp = "";
$price = "";
$price_tmp = "";
preg_match_all("|<strike>(.*)</strike>|U",$results,$listprice_tmp, PREG_PATTERN_ORDER);
$price = str_replace(",","",$listprice_tmp[1][0]);

if($price == "" || $price == null){ // 할인안하는 상품이 없는듯..
    preg_match_all("|<input type=\"hidden\" name=\"money_\" value=\"(.*)\">|U",$results,$price_tmp, PREG_PATTERN_ORDER);
    $price = $price_tmp[1][0];
}
//echo "할인가(판매가)".$price;

//상품명
$pname_tmp = "";
$pname = "";
preg_match_all("|font-size:16\">(.*)</font>|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
$pname = strip_tags($pname_tmp[1][0]);

//기본 이미지
$images_tmp = "";
$prod_img_src = "";
preg_match_all("|<img src=\".(.*)\" name=mainImage border=\"0\">|U",$results,$images_tmp, PREG_PATTERN_ORDER);
$prod_img_src = $images_tmp[1][0];

if(substr($prod_img_src,0,5) != "http:"){
	$prod_img_src = "http://www.bqueen.co.kr/mall".$prod_img_src;
}

$datas = "";
$datas = split("\n",$results);

//상품설명과 옵션1~3의 위치를 찾기위한 루프

$option_start_line = "";
$option_end_line = "";
$desc_start_line = "";
$desc_end_line = "";
$detailimg_start_line = "";
$detailimg_end_line = "";
for($i=0;$i < count($datas);$i++){
	$data = $datas[$i];
    
	if(!$option_start_line && substr_count($data,"<select name=\"goods_option")){
		$option_start_line = $i;
	}
	
	if(($option_start_line && !$option_end_line) && substr_count($data,"</select>")){
		$option_end_line = $i+1;
	}
    if(!$desc_start_line && substr_count($data,"상품설명")){
		$desc_start_line = $i;
	}
	
	if(($desc_start_line && !$desc_end_line) && substr_count($data,"상품설명") && ($desc_start_line != $i)){
		$desc_end_line = $i+1;
	}

/**
	if(!$desc_start_line && (substr_count($data,"상세설명") || substr_count($data,"제품설명"))){
		$desc_start_line = $i;
	}
	
	if(($desc_start_line && !$desc_end_line) && substr_count($data,"</table>")){
		$desc_end_line = $i+1;
	}
    
    if(!$desc2_start_line && substr_count($data,"사용방법")){
		$desc2_start_line = $i;
	}

	if(($desc2_start_line && !$desc2_end_line) && substr_count($data,"</table>")){
		$desc2_end_line = $i+1;
	}
*/	
    //상품 상세이미지 위치

    if(!$detailimg_start_line && substr_count($data,"쿠폰 시작 끝부분")){
		$detailimg_start_line = $i;
	}
	
	if(($detailimg_start_line && !$detailimg_end_line) && substr_count($data,"<br>")){
		$detailimg_end_line = $i+1;
	}


}
//상세설명의 문자열 저장
$desc_str = "";
$prod_desc_prod = "";
$src_tmp = "";
$src = "";
//img 수정 Hjy 2012-07-09
for($i=$desc_start_line;$i < $desc_end_line;$i++){
	if(substr_count($datas[$i],"src")){
		preg_match_all("|src=\"(.*)\"|U",$datas[$i],$src_tmp, PREG_PATTERN_ORDER);
		$src = $src_tmp[1][0];

		if(substr($src,0,5) != "http:"){
			if(substr($src,0,2) == './'){

				$src_tmp=substr($src,2);
				$datas[$i] = str_replace($src,$src_tmp,$datas[$i]);

				$datas[$i] = str_replace($src_tmp,"http://www.bqueen.co.kr/mall/".$src_tmp,$datas[$i]);
			}else{
				$datas[$i] = str_replace($src,"http://www.bqueen.co.kr".$src,$datas[$i]);
			}
			//$datas[$i] = "http://www.bqueen.co.kr/".$src;
		}
	}
	$desc_str .= $datas[$i];	
}


$prod_desc_prod = str_replace("제목 없음","",strip_tags($desc_str,"<table><tr><td><br><font><b><p><img>"));//img 추가 Hjy 2012-07-09

//print_r($prod_desc_prod);
//exit;
/**
//상세설명의 문자열 저장
$desc_str = "";
for($i=$desc_start_line+1;$i < $desc_end_line;$i++){
	$desc_str .= $datas[$i];	
}
echo $desc_str;
if($desc_str){
	preg_match_all("|<td style=.*>(.*)</td>|U",$desc_str,$desc_tmp, PREG_PATTERN_ORDER);
    if($desc_tmp[1][0] == "" || $desc_tmp[1][0] == " "){
        $prod_desc_prod = "상세설명 : <br />".strip_tags($desc_str);
        //echo $desc_str;
        // 사용방법이 같이 따라왔을때 줄바꿈.
        $prod_desc_prod = str_replace("사용방법 :","<br />사용방법 : <br />",$prod_desc_prod);
        if(substr_count($prod_desc_prod,"<br />사용방법 : <br />") == 0){
            $prod_desc_prod = str_replace("사용방법","<br />사용방법 <br />",$prod_desc_prod);    
        }
        
    }else{
        $prod_desc_prod = "상세설명 : <br />".$desc_tmp[1][0];
    }
}

//사용방법이 처음 긁었을때 안따라왔을때만 수행.
if(substr_count($prod_desc_prod,"사용방법") == 0){
// 사용방법의 문자열 저장
    $desc2_str = "";
    for($i=$desc2_start_line+1;$i < $desc2_end_line;$i++){
    	$desc2_str .= $datas[$i];	
    }
    //echo $desc2_str;
    if($desc2_str){
    	preg_match_all("|<td style=.*>(.*)</td>|U",$desc2_str,$desc2_tmp, PREG_PATTERN_ORDER);
        if($desc2_tmp[1][0] == "" || $desc2_tmp[1][0] == " "){
            $prod_desc_prod .= "<br /><br />사용방법 : <br />".strip_tags($desc2_str); 
        }else{
            $prod_desc_prod .= "<br /><br />사용방법 : <br />".$desc2_tmp[1][0];
        }
    }
}
//센터태그 삭제
$prod_desc_prod = str_replace("align=center","",$prod_desc_prod);
*/
// 상세 이미지
/*
$detailimg_str = "";
for($i=$detailimg_start_line;$i <= $detailimg_end_line;$i++){
	$detailimg_str .= $datas[$i];	
}

$optionnal_img_tmp = "";
if($detailimg_str){
	preg_match_all("|<img src=\"(.*)\".*>|U",$detailimg_str,$optionnal_img_tmp, PREG_PATTERN_ORDER);
}
// 상세 이미지중 상품설명 아닌 이미지 제거
$_optionnal_img_tmp = "";
for($i=0, $j=0;$i < count($optionnal_img_tmp[1]);$i++){
    if(substr_count($optionnal_img_tmp[1][$i],"shop_image") > 0){
        $_optionnal_img_tmp[$j] = $optionnal_img_tmp[1][$i];
        $j++;
    }
}

$goods_detail_images = "";



for($i=0 ;$i < count($_optionnal_img_tmp);$i++){
	if($_optionnal_img_tmp){
		if(substr_count($_optionnal_img_tmp[$i],"http:") == 0){
			$_optionnal_img_tmp[$i] = "http://www.bqueen.co.kr/mall/".$_optionnal_img_tmp[$i];    
		}
		$goods_detail_images[$i] = $_optionnal_img_tmp[$i];
	}
}
*/

//print_r($_optionnal_img_tmp);
//exit;
//print_r($goods_detail_images_name);
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


// 옵션1~3의 문자열 저장
$options_str = "";
for($i=$option_start_line;$i < $option_end_line;$i++){
	$options_str .= $datas[$i];	
}

// 옵션의 문자열 배열로 저장
$options = "";
$_option = "";
$option = "";
if($options_str){

	preg_match_all("|<option .*>(.*)</option>|U",$options_str,$options, PREG_PATTERN_ORDER);
    
	$_option = $options[1];
    $option[0] = "옵션";
	for($i=1,$j=1;$i < count($_option);$i++){
			$option[$i] = trim($_option[$j]);
            $j++;
	}
	//echo "#option";
    //print_r($option);
    
}


if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}


$goods_desc_copy = 1;

?>