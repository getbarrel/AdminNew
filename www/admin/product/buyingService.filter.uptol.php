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
 */


//curl로 받은 데이터 줄바꿈
//$datas = split("\n",$results); 
//print_r($results); 

//상품코드
$pcode_tmp = "";
$pcode = "";
preg_match_all("|<input type=\"hidden\" name=\"product_no\" value=\"(.*)\">|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
$pcode = $bs_site."_".$pcode_tmp[1][0];


//할인적용가(or 비할인시 판매가)
$price_tmp = "";
$price = "";
preg_match_all("|<input type=\"hidden\" name=\"product_price\" value=\"(.*)\">|U",$results,$price_tmp, PREG_PATTERN_ORDER);
$price = str_replace(",","",$price_tmp[1][0]);

//echo "할인가(판매가)".$price;

//상품명
$pname_tmp = "";
$pname = "";
preg_match_all("|<input type=\"hidden\" name=\"product_name\" value=\"(.*)\">|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
$pname = strip_tags($pname_tmp[1][0]);

//기본 이미지
$images_tmp = "";
$prod_img_src = "";
preg_match_all("|<img name='big_img' src=\"(.*)\" width=\".*\"|U",$results,$images_tmp, PREG_PATTERN_ORDER);
$prod_img_src = $images_tmp[1][0];

if(substr($prod_img_src,0,5) != "http:"){
	$prod_img_src = "http://www.uptol.com".$prod_img_src;
}

$datas = "";
$datas = split("\n",$results);

//옵션의 위치를 찾기위한 루프
$option_start_line = "";
$option_end_line = "";
$option2_start_line = "";
$option2_end_line = "";
for($i=0;$i < count($datas);$i++){
	$data = $datas[$i];
    
	if(!$option_start_line && substr_count($data,"option1")){
		$option_start_line = $i;
	}
	
	if(($option_start_line && !$option_end_line) && substr_count($data,"</select>")){
		$option_end_line = $i+1;
	}
    if(!$option2_start_line && substr_count($data,"option2")){
		$option2_start_line = $i;
	}
	
	if(($option2_start_line && !$option2_end_line) && substr_count($data,"</select>") && ($option_end_line != $i+1)){
		$option2_end_line = $i+1;
	}
}

// 상세 이미지
$optionnal_img_tmp = "";
$goods_detail_images = "";

preg_match_all("|<P><IMG src=\"(.*)\"></P>|U",$results,$optionnal_img_tmp, PREG_PATTERN_ORDER);

for($i=0 ;$i < count($optionnal_img_tmp[1]);$i++){
	
    if(substr_count($optionnal_img_tmp[1][$i],"http:") == 0){
        $optionnal_img_tmp[1][$i] = "http://www.bqueen.co.kr/mall/".$optionnal_img_tmp[1][$i];    
    }
	$goods_detail_images[$i] = $optionnal_img_tmp[1][$i];
}
//print_r($goods_detail_images_name);
//print_r($goods_detail_images);
//$goods_detail_images_name = $detail_images_name[1];

$_prod_desc = "";
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


// 옵션의 문자열 저장
$options_str = "";
for($i=$option_start_line;$i < $option_end_line;$i++){
	$options_str .= $datas[$i];	
}
$options2_str = "";
for($i=$option2_start_line;$i < $option2_end_line;$i++){
	$options2_str .= $datas[$i];	
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

$options2 = "";
$_option2 = "";
$option2 = "";
if($options2_str){

	preg_match_all("|<option .*>(.*)</option>|U",$options2_str,$options2, PREG_PATTERN_ORDER);
    
	$_option2 = $options2[1];
    $option2[0] = "옵션";
	for($i=1,$j=1;$i < count($_option2);$i++){
			$option2[$i] = trim($_option2[$j]);
            $j++;
	}
	//echo "#option2";
    //print_r($option2);
    
}

if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}

$goods_desc_copy = 1;

?>