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
 * 
 * ************특이사항*************
 * 
 * 다른 상품을 클릭하여도 주소값이 변하지 않는 경우가 있음.
 * 주소의 productId값이 변하지 않아 주소를 복사해서 접근할때 이전에 선택한 상품의 정보가 나올 수 있습니다.
 * 
 */
 
//받은 데이터 줄바꿈
$datas = "";
$datas = split("\n",$results);

//상품코드
//preg_match_all("|var omnProductIDs = \"(.*)\";|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
//$pcode = $bs_site."_".$pcode_tmp[1][0];

//상품코드를 url에서 추출
$pcode_tmp = "";
$pcode = "";
$pcode_tmp = explode("#",$bs_url);
$pcode = $bs_site."_".$pcode_tmp[1];

//상품 json받기
//<link rel="canonical" href="http://www.coach.com/online/handbags/genWCM-10551-10051-en-/Coach_US/StaticPage/handbags-?LOC=LN1" />	
$storeid_tmp = "";
$storeid = "";
preg_match_all("|<input type=\"hidden\" id=\"gwtStoreId\" value=\"(.*)\"/>|U",$results,$storeid_tmp,PREG_PATTERN_ORDER);
$storeid = $storeid_tmp[1][0];

$catalogid_tmp = "";
$catalogid = "";
preg_match_all("|<input type=\"hidden\" id=\"gwtCatalogId\" value=\"(.*)\"/>|U",$results,$catalogid_tmp,PREG_PATTERN_ORDER);
$catalogid = $catalogid_tmp[1][0];

$langid_tmp = "";
$langid = "";
preg_match_all("|<input type=\"hidden\" id=\"gwtlangId\" value=\"(.*)\"/>|U",$results,$langid_tmp,PREG_PATTERN_ORDER);
$langid = $langid_tmp[1][0];

/**
 *  /handbags/ 는 기본으로 모든 주소에 있는듯함
 */
//http://www.coach.com/online/handbags/GetProductInfoView?storeId=10551&catalogId=10051&langId=-1&productId=107538
$json_src = "http://www.coach.com/online/handbags/GetProductInfoView?storeId=".$storeid."&catalogId=".$catalogid."&langId=".$langid."&productId=".$pcode_tmp[1]; 

//echo $json_src;
$json_results = "";
$snoopy_sub = new Snoopy;
$snoopy_sub->fetch($json_src);
$json_results =  $snoopy_sub->results;

//print_r($json_results);

//상품가격
$price_tmp = "";
$price = "";
preg_match_all("|\"listPrice\":\"(.*)\"|U",$json_results,$price_tmp, PREG_PATTERN_ORDER);
$price = str_replace("$","",$price_tmp[1][0]);


//상품명
$pname_tmp = "";
$pname = "";
preg_match_all("|\"productName\":\"(.*)\"|U",$json_results,$pname_tmp, PREG_PATTERN_ORDER);
$pname = $pname_tmp[1][0];

//신상 유무
$isnew_tmp = "";
$isnew = "";
preg_match_all("|\"isNew\":(.*),|U",$json_results,$isnew_tmp, PREG_PATTERN_ORDER);
$isnew = $isnew_tmp[1][0];
//echo $isnew;
if($isnew == "true"):
    $pname = "NEW ".$pname;
endif;

/** 완성형 http://s7d2.scene7.com/is/image/Coach/19391_svva_a0?$mainlarge$
 * 
 * 기본 이미지 주소 *http://s7d2.scene7.com/is/image/Coach/
 * $results 
 */
$img_src_tmp = "";
$base_img_src = "";
preg_match_all("|<input type=\"hidden\" id=\"gwt_scene7_unsecure_url\" value=\"(.*)\" />|U",$results,$img_src_tmp, PREG_PATTERN_ORDER);
$base_img_src = $img_src_tmp[1][0];
//print_r($base_img_src);

/** 스타일 넘버 (보통 숫자인데 첫글자에 알파벳 대문자로 오는경우가 있음)
 *  $json_results 
 */
$style_tmp = "";
$style = "";
preg_match_all("|\"style\":\"(.*)\"|U",$json_results,$style_tmp, PREG_PATTERN_ORDER);
$style = mb_strtolower($style_tmp[1][0]);

//컬러코드
$colorcode_tmp = "";
preg_match_all("|\"selectedColorCode\":\"(.*)\",|U",$json_results,$colorcode_tmp, PREG_PATTERN_ORDER);


//이미지주소 + 스타일+ 컬러코드 + 기본이미지옵션
$prod_img_src = "";
$prod_img_src = $base_img_src.$style."_".$colorcode_tmp[1][0].'_a0?$mainlarge$';
//print_r($prod_img_src);

if(substr($prod_img_src,0,5) != "http:"){
	$prod_img_src = "http:".$prod_img_src;
}

//상품설명
$prod_desc_prod_tmp = "";
$prod_desc_prod = "";
preg_match_all("|\"longDescription\":\"(.*)\"|U",$json_results,$prod_desc_prod_tmp);
$prod_desc_prod = htmlspecialchars_decode($prod_desc_prod_tmp[1][0]);
$prod_desc_prod = str_replace('&lt;li&gt;',"<li>",$prod_desc_prod);
$prod_desc_prod = str_replace("\\","",$prod_desc_prod);

// 다른색상 상품이미지
$optionnal_colorcode_tmp = "";
$colorcode = "";
$goods_detail_images = "";
$img_name_tmp = "";
$goods_detail_images_name = "";
preg_match_all("|\"productColorString\":\"(.*)\"|U",$json_results,$optionnal_colorcode_tmp, PREG_PATTERN_ORDER);
//print_r($optionnal_colorcode_tmp);
for($i=0 ;$i < count($optionnal_colorcode_tmp[1]);$i++){

    $colorcode[$i] = $optionnal_colorcode_tmp[1][$i];
    $goods_detail_images[$i] = $base_img_src.$style."_".$colorcode[$i].'_a0?$mainlarge$';
    preg_match_all("|\"color\":\"(.*)\"|U",$json_results,$img_name_tmp, PREG_PATTERN_ORDER);
    $goods_detail_images_name[$i] = $img_name_tmp[1][$i];
}
//print_r($goods_detail_images_name);
//print_r($goods_detail_images);
//$goods_detail_images_name = $detail_images_name[1];

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


//옵션 배열에 넣기
$option_tmp = "";
$option = "";
preg_match_all("|\"color\":\"(.*)\"|U",$json_results,$option_tmp, PREG_PATTERN_ORDER);

for($i=0;$i<count($option_tmp[1]);$i++){
    $option[$i] = $option_tmp[1][$i]; 
}

//print_r($option);

//옵션2 배열에 넣기
$option2_tmp = "";
$option2 = "";
preg_match_all("|\"size\":\"(.*)\"|U",$json_results,$option2_tmp, PREG_PATTERN_ORDER);

for($i=0;$i<count(array_unique($option2_tmp[1]));$i++){
    
    $option2[$i] = $option2_tmp[1][$i]; 
}

//print_r($option2);

//Warning: copy(http://s7d2.scene7.com/is/image/Coach/70487__swatch?$swatch$) [function.copy]: failed to open stream: HTTP request failed! HTTP/1.0 403 Forbidden 
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


$options = "";
$option_key = 0;

if(is_array($option)){

		//thumb_images
		$thumb_images = "";
		$relation_size = "";
		for($i=0; $i < count($option); $i++ ){
			$thumb_images[$i] = $base_img_src.$style."_".$colorcode[$i].'_swatch?$swatch$';
		}

		//컬러별 사이즈

		if(count($thumb_images) > 0){

			for($i=0; $i < count($thumb_images); $i++){
				$relation_size[$i] = implode("^",array_values($option2));
			}
		}
			
		if(is_array($option)){
			$options[$option_key][option_type] = "9";
			$options[$option_key][option_name] = "COLOR/SIZE";
			$options[$option_key][option_kind] = "r";
			$options[$option_key][option_use] = "1";

			for($i=0;$i < count($option);$i++){
				$options[$option_key][details][$i][option_div] = $option[$i];
				$options[$option_key][details][$i][price] = "";
				$options[$option_key][details][$i][etc1] = "사이즈:".$relation_size[$i];
				$options[$option_key][details][$i][thumb_images] = $thumb_images[$i];
				$options[$option_key][details][$i][goods_images] = $goods_detail_images[$i];
				
			}

		}
		/*
		else{

			if(is_array($option)){
				$options[0][option_type] = "9";
				$options[0][option_name] = $option[0];
				$options[0][option_kind] = "s";
				$options[0][option_use] = "1";
				
				for($i=1;$i < count($option);$i++){
					$options[0][details][$i-1][option_div] = $option[$i];
					$options[0][details][$i-1][price] = "";
					$options[0][details][$i-1][etc1] = $option[$i];
									
				}
			}

			if(is_array($option2)){
				$options[1][option_type] = "9";
				$options[1][option_name] = $option2[0];
				$options[1][option_kind] = "s";
				$options[1][option_use] = "1";
				
				for($i=1;$i < count($option2);$i++){
					$options[1][details][$i-1][option_div] = $option2[$i];
					$options[1][details][$i-1][price] = "";
					$options[1][details][$i-1][etc1] = $option2[$i];
								
				}
			}

		}

		$option_key++;
		*/
}


 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}

$option = "";
$option2 = "";

$goods_desc_copy = 1;

?>