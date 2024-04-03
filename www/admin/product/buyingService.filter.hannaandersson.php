<?
//$bs_url="http://www.hannaandersson.com/category.aspx?id=girls+6-12+years_skirts&cm_re=Spring%202012-_-Mouse%20Over%20Navigation-_-Girls%206-12%20years%20Skirts";

$result = "";
$results = "";
$soldout_message = "";

$snoopy = new Snoopy;
$snoopy->fetch($bs_url);
$results =  $snoopy->results;
$result=str_replace("\n","",$results);

/*
$search_detail_regxp = "|<div class=\"thumbName\"><a href=\"(.*)&simg.*\".*</div>|U";
preg_match_all($search_detail_regxp,$results,$pcode_tmp, PREG_PATTERN_ORDER);
print_r($result);
exit;
*/

//상품코드
$pcode_tmp = "";
$pcode = "";
preg_match_all("|fromInfo = \"(.*)\";|U",$result,$pcode_tmp, PREG_PATTERN_ORDER);
$pcode = trim(str_replace("|","",$pcode_tmp[1][0]));
$pcode = $bs_site."_".$pcode;

//상품이름
$pname_tmp = "";
$pname = "";
preg_match_all("|<title>(.*)</title>|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
$pname = str_replace("Product Information","",$pname_tmp[1][0]);
$pname = iconv("ISO-8859-1","UTF-8",$pname) ;

//기본 이미지
$images_tmp = "";
$prod_img_src = "";
preg_match_all("|dtmTag.dtmc_prod_img = \"(.*)\";|U",$result,$images_tmp, PREG_PATTERN_ORDER);
$prod_img_src = str_replace('?$FlatFullW$',"",$images_tmp[1][0]);

if(substr($prod_img_src,0,5) != "http:"){
	$prod_img_src = "http:".$prod_img_src;
}

//가격
$price_tmp = "";
$price = "";
preg_match_all("|nowPriceLow = \"(.*)\";|U",$result,$price_tmp, PREG_PATTERN_ORDER);
$price=$price_tmp[1][0];

// sold out. 일때
if($price == 0){
	$price = "";
}

/* 2012-05-07 가격수정
	if(count($price_tmp2[1])==1){
		$pCvalue=substr($prod_img_src, -3);
		preg_match_all("|<option value=\"$pCvalue\" .*>(.*)</option>|U",$result,$price_tmp1, PREG_PATTERN_ORDER);
		$spos = strrpos($price_tmp1[1][0],"$");
		$price=substr($price_tmp1[1][0], $spos+1);
	}else{
		$pCvalue=substr($prod_img_src, -3);
		preg_match_all("|<option value=\"$pCvalue\" .*>(.*)</option>|U",$result,$price_tmp1, PREG_PATTERN_ORDER);
		$spos = strrpos($price_tmp1[1][0],"$");
		$sale_price=substr($price_tmp1[1][0], $spos+1);
		preg_match_all("|<span .*>reg.&nbsp;(.*)</span>|U",$price_tmp2[0][1],$price_tmp, PREG_PATTERN_ORDER);
		$price = str_replace("$","",$price_tmp[1][0]);
	}
*/
/* 가격 관한 수정 $30-30.40 이런 상품에는 안됨
preg_match_all("|<span .*>(.*)</span>|U",$price_tmp1[1][0],$price_tmp2, PREG_PATTERN_ORDER);

	if(count($price_tmp2[1])==1){
		$price =str_replace("".$price_tmp2[0][0]."","",$price_tmp1[1][0]);
		$price = str_replace("$","",$price);
	}else{
		preg_match_all("|<span .*><span .*>(.*)</span>|U",$price_tmp2[0][0],$price_tmp, PREG_PATTERN_ORDER);
		$sale_price = str_replace("now $","",$price_tmp[1][0]);
		preg_match_all("|<span .*>reg.&nbsp;(.*)</span>|U",$price_tmp2[0][1],$price_tmp, PREG_PATTERN_ORDER);
		$price = str_replace("$","",$price_tmp[1][0]);
	}
*/
if($sale_price > 0){
	$listprice = $price;
	$price = $sale_price;
}

//상품 설명
$prod_desc_tmp = "";
$prod_desc_prod = "";
preg_match_all("|<div id=\"mainDescO\">(.*)</div>|U",$result,$prod_desc_tmp, PREG_PATTERN_ORDER);
$prod_desc_prod = $prod_desc_tmp[1][0];
//$prod_desc_prod = strip_tags($prod_desc_prod,"<span><style><table><tr><td><li><img>");
$prod_desc_prod = iconv("ISO-8859-1","UTF-8",$prod_desc_prod) ;

//다른색상 상품이미지
$optionnal_img_tmp = "";
$img_src_code = "";
$optionnal_img_src = "";
$goods_detail_images = "";
$optionnal_img1 = "";
$optionnal_img2 = "";
preg_match_all("|javascript:swatchClicked(.*);|U",$result,$optionnal_img_tmp, PREG_PATTERN_ORDER);
$img_src_code=substr($prod_img_src,-9);
$optionnal_img_src=str_replace("$img_src_code","",$prod_img_src);

if(!array_values($optionnal_img_tmp[1])){
	$goods_detail_images[0]=$prod_img_src;
}else{
	for($i=0 ;$i < count($optionnal_img_tmp[1]);$i++){
		$optionnal_img1[$i]=substr($optionnal_img_tmp[1][$i], 2, 5);
		$optionnal_img2[$i]=substr($optionnal_img_tmp[1][$i], 10, 3);
		$goods_detail_images[$i] = $optionnal_img_src.$optionnal_img1[$i].'_'.$optionnal_img2[$i];
	}
}

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

//curl로 받은 데이터 줄바꿈
$datas = "";
$datas = split("\n",$results);

//옵션1,2의 위치를 찾기위한 루프
$option_start_line = "";
$option_end_line = "";
$option2_start_line = "";
$option2_end_line = "";
for($i=0;$i < count($datas);$i++){
	$data = $datas[$i];
	
	if(!$option_start_line && substr_count($data,"<div id=\"colorSelDiv\" ")){
		$option_start_line = $i;
	}
	
	if(($option_start_line && !$option_end_line) && substr_count($data,"</select>")){
		$option_end_line = $i+1;
	}

	if(!$option2_start_line && substr_count($data,"select a size</option>")){
		$option2_start_line = $i;
	}
	
	if(($option2_start_line && !$option2_end_line) && substr_count($data,"</select>")){
		$option2_end_line = $i+1;
	}

}

// 옵션1,2의 문자열 저장
$options_str = "";
for($i=$option_start_line;$i < $option_end_line;$i++){
	$options_str .= $datas[$i];	
}

$options2_str = "";
for($i=$option2_start_line;$i < $option2_end_line;$i++){
	$options2_str .= $datas[$i];	
}


// 옵션1,2의 문자열 배열로 저장
$option1_spos = "";
$option1_epos = "";
$options1_str = "";
$options = "";
$_option = "";
$option = "";
if($options_str){
	
    $options_str = str_replace("\\","",$options_str);
	$option1_spos = strrpos($options_str,"<select");
	$option1_epos = strpos($options_str,"</select>");
	$options1_str = substr($options_str,$option1_spos,$option1_epos+1);
	preg_match_all("|<option .*>(.*)</option>|U",$options1_str,$options, PREG_PATTERN_ORDER);
	$_option = $options[1];


	for($i=0;$i < count($_option);$i++){
			$option[$i] = str_replace("&nbsp;","",trim($_option[$i]));
	}
	//echo "#option1-";
    //print_r($option);
}

$options2 = "";
$_option2 = "";
$option2 = "";
if($options2_str){
	preg_match_all("|<option .*>(.*)</option>|U",$options2_str,$options2, PREG_PATTERN_ORDER);
	$_option2 = $options2[1];
	
	for($i=0;$i < count($_option2);$i++){
		$option2[$i] = str_replace("Select a","",trim($_option2[$i]));
	}
	//echo "#option2-";
    //print_r($option2);
}	


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$options = "";
$price_tmp1 = "";
$Plusprice = "";
$pCvalue = "";
$spos = "";
$spos = "";
$option_key = 0;
if(is_array($option)){

		//추가 가격
		preg_match_all("|<option value=\"(.*)\".*>.*</option>|U",$options1_str,$price_tmp1, PREG_PATTERN_ORDER);
		$pCvalue=$price_tmp1[1];

		for($i=0; $i < count($option); $i++){
			preg_match_all("|<option value=\"$pCvalue[$i]\".*>(.*)</option>|U",$result,$price_tmp1, PREG_PATTERN_ORDER);
			$spos[$i] = strrpos($price_tmp1[1][0],"$");
			$Plusprice[$i]=substr($price_tmp1[1][0], $spos[$i]+1);
		}

		if(is_array($option)){
			$options[$option_key][option_type] = "9";
			$options[$option_key][option_name] = "COLOR";
			$options[$option_key][option_kind] = "b";
			$options[$option_key][option_use] = "1";

			for($i=0;$i < count($option);$i++){
				$options[$option_key][details][$i][option_div] = $option[$i];
				$options[$option_key][details][$i][price] = $Plusprice[$i];
				$options[$option_key][details][$i][etc1] = $option[$i];
				//$options[$option_key][details][$i][thumb_images] = $thumb_images[$i];
				$options[$option_key][details][$i][goods_images] = $goods_detail_images[$i];
				
				//"sold out"일떄 
				if(substr_count($Plusprice[$i],"sold out")){
					unset($options[$option_key][details][$i]);
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
//print_r($options);
//exit;

// 재고 일시코드
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