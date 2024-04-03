<?php
$results = "";
$soldout_message = "";

$snoopy = new Snoopy;
$snoopy->fetch($bs_url);
$results =  $snoopy->results;

//상품코드
$pcode_tmp = "";
$pcode = "";
preg_match_all("|<input type=\"hidden\" id=\"orgcmid\" name=\"orgcmid\" value=\"(.*)\"/>|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
$pcode = $bs_site."_".$pcode_tmp[1][0];


//가격
$price_tmp = "";
$price = "";
preg_match_all("|<dl class=\"itemDetailPrice\">.*dd><span>(.*)</span>|U",str_replace("\n","",$results),$price_tmp, PREG_PATTERN_ORDER);
$price = str_replace(array(",","￥","\\","円"),"",$price_tmp[1][0]);


//상품이름
$pname_tmp = "";
$pname = "";
preg_match_all("|<h2 id=\"itemDetailName\" class=\"subtitle detailname\">(.*)</h2>|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
$pname = trim($pname_tmp[1][0]);

/*
$pcode_tmp = "";
$expcode = "";
preg_match_all("|<td class=\"bOne\">Item #(.*)</td>|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
$expcode = $pcode_tmp[1][0];
*/

//기본 이미지
$images_tmp = "";
$prod_img_src = "";
preg_match_all("|<p id=\"mainPictZoom\" class=\"itemzoombtn\"><a href=\"(.*)\" rel=\"example1\"|U",$results,$images_tmp, PREG_PATTERN_ORDER);
$prod_img_src = $images_tmp[1][0];




/*
if(substr($prod_img_src,0,5) != "http:"){
	$prod_img_src = "http:".$prod_img_src;
}
*/

//상품 설명

$datas = "";
$datas = split("\n",$results);

/*
//상품 설명 위치를 찾기위한 루프
$desc_start_line = "";
$desc_end_line = "";
*/
$option_start_line="";
$option_end_line="";

for($i=0;$i < count($datas);$i++){
	$data = $datas[$i];
	
	/*
    //상품 설명 위치
    if(!$desc_start_line && substr_count($data,"<div id=\"goods_detail_icon\">")){
		$desc_start_line = $i+3;
	}

	if(($desc_start_line && !$desc_end_line) && substr_count($data,"<div id=\"color_size_select\">")){
		$desc_end_line = $i-1;
	}
	*/

	//옵션위치 찾기
	if(!$option_start_line && substr_count($data,"<select id=\"iSize\" name=\"iSize\"")){
		$option_start_line = $i;
	}

	if(($option_start_line && !$option_end_line) && substr_count($data,"</select>")){
		$option_end_line = $i;
	}

}


/*
$prod_desc_prod="";

for($i=$desc_start_line;$i <= $desc_end_line;$i++){
	$prod_desc_prod .= $datas[$i];
}
*/

$options_area ="";
for($i=$option_start_line;$i <= $option_end_line;$i++){
	$options_area .= $datas[$i];
}


$_option_detail_tmp="";
$_option_detail="";


preg_match_all("|<option value=\".*\".*>(.*)</option>|U",$options_area,$_option_detail_tmp, PREG_PATTERN_ORDER);

for($i=0,$j=0;$i<count($_option_detail_tmp[1]);$i++){
	if($_option_detail_tmp[1][$i]!="サイズの選択"){
		$_option_detail[$j] = $_option_detail_tmp[1][$i];
		$j++;
	}
}

$options="";
$option_key = 0;

if(count($_option_detail) > 0){
	$options[$option_key][option_type] = "9";
	$options[$option_key][option_name] = "사이즈";
	$options[$option_key][option_kind] = "s";
	$options[$option_key][option_use] = "1";

	for($i=0;$i < count($_option_detail);$i++){
		$options[$option_key][details][$i][option_div] = trim($_option_detail[$i]);
	}
}

$prod_desc_prod="";
$prod_desc_prod_tmp="";

preg_match_all("|<meta name=\"description\" content=\"(.*)\" \/>|U",$results,$prod_desc_prod_tmp, PREG_PATTERN_ORDER);
$prod_desc_prod = $prod_desc_prod_tmp[1][0];


// 디테일 상품이미지
$detail_images_tmp = "";
$goods_detail_images="";
preg_match_all("|<a href=\"javascript:changeMainImage\(\'.*', \'(.*)\'\)\;\">|U",$results,$detail_images_tmp, PREG_PATTERN_ORDER);
$goods_detail_images = $detail_images_tmp[1];


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


// 디테일 상품이미지

$soldout_message_tmp="";
preg_match_all("|<span id=\"stockStatus\".*><span class=\"red\">(.*)</span>|U",$results,$soldout_message_tmp, PREG_PATTERN_ORDER);
$soldout_message = trim($soldout_message_tmp[1][0]);


if($soldout_message == "○"){
	$soldout_message="";
}

// 재고 일시코드
if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}

$goods_desc_copy = 1;