<?
$results = "";
$soldout_message = "";

$snoopy = new Snoopy;
$snoopy->fetch($bs_url);
$results =  $snoopy->results;

//상품코드
$pcode_tmp = "";
$pcode = "";
preg_match_all("|<input name=\"goods\" type=\"hidden\" value=\"(.*)\">|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
$pcode = $bs_site."_".$pcode_tmp[1][0];


//가격
$price_tmp = "";
$price = "";
preg_match_all("|<div id=\"goods_title_right\">.*<em>(.*)</em>|U",$results,$price_tmp, PREG_PATTERN_ORDER);
$price = str_replace(array(",","￥","\\"),"",$price_tmp[1][0]);


//상품이름
$pname_tmp = "";
$pname = "";
preg_match_all("|s.pageName=\"pd:(.*)\"|U",str_replace("\n","",$results),$pname_tmp, PREG_PATTERN_ORDER);
$pname = trim(iconv("shift_jis","utf-8",$pname_tmp[1][0]));


/*
$pcode_tmp = "";
$expcode = "";
preg_match_all("|<td class=\"bOne\">Item #(.*)</td>|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
$expcode = $pcode_tmp[1][0];
*/

//기본 이미지
$images_tmp = "";
$prod_img_src = "";
preg_match_all("|<div id=\"image_change\"><a href=\"(.*)\"|U",$results,$images_tmp, PREG_PATTERN_ORDER);
$prod_img_src = "http://shop.akachan.jp/".$images_tmp[1][0];

/*
if(substr($prod_img_src,0,5) != "http:"){
	$prod_img_src = "http:".$prod_img_src;
}
*/

//상품 설명
$datas = "";
$datas = split("\n",$results);

//상품 설명 위치를 찾기위한 루프
$desc_start_line = "";
$desc_end_line = "";

for($i=0;$i < count($datas);$i++){
	$data = $datas[$i];

    //상품 설명 위치
    if(!$desc_start_line && substr_count($data,"<div id=\"goods_detail_icon\">")){
		$desc_start_line = $i+3;
	}

	if(($desc_start_line && !$desc_end_line) && substr_count($data,"<div id=\"color_size_select\">")){
		$desc_end_line = $i-1;
	}
}



$prod_desc_prod="";

for($i=$desc_start_line;$i <= $desc_end_line;$i++){
	$prod_desc_prod .= iconv("shift_jis","utf-8",$datas[$i]);
}

$prod_desc_prod = strip_tags($prod_desc_prod,'<p><br>');


// 디테일 상품이미지
$detail_images_tmp = "";
$goods_detail_images="";
preg_match_all("|<img onmouseover=\"javascript:document.ch.src = \'(.*)\'; void\(0\);\" onmouseout|U",$results,$detail_images_tmp, PREG_PATTERN_ORDER);
for($i=0 ;$i < count($detail_images_tmp[1]);$i++){
	$goods_detail_images[$i] = "http://shop.akachan.jp/".$detail_images_tmp[1][$i];
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


// 재고 일시코드
if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}

$goods_desc_copy = 1;


?>