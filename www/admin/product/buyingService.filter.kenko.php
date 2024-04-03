<?
$results = "";
$soldout_message = "";

//$bs_url="http://www.janieandjack.com/shop/dept_item.jsp?PRODUCT%3C%3Eprd_id=845524443469264&FOLDER%3C%3Efolder_id=2534374303719769&bmUID=1334153960385&productSizeSelected=0";
$snoopy = new Snoopy;
$snoopy->fetch($bs_url);
$results =  $snoopy->results;

//상품코드
$pcode_tmp = "";
$pcode = "";
preg_match_all("|<span class=\"identifier\">(.*)</span>|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
//$pcode = $bs_site."_".$pcode_tmp[1][0];

$bs_url_tmp="";
$bs_url_tmp=explode("_",$bs_url);
$pcode = $bs_site."_".str_replace(".html","",$bs_url_tmp[1]);

//가격
$price_tmp = "";
$price = "";
preg_match_all("|<span itemprop=\"price\">(.*)</span>|U",$results,$price_tmp, PREG_PATTERN_ORDER);
$price = str_replace(array(",","￥","\\"),"",$price_tmp[1][0]);

/*
$listprice_tmp = "";
$list_price = "";
preg_match_all("|<STRIKE>(.*)</STRIKE>|U",$results,$listprice_tmp, PREG_PATTERN_ORDER);
$list_price = str_replace(array(",","￥","\\"),"",$listprice_tmp[1][0]);
//print_r($saleprice_tmp);

if($list_price > 0){
	$listprice = $list_price;
}
*/

//상품이름
$pname_tmp = "";
$pname = "";
preg_match_all("|<meta name=\"kcitemname\" content=\"(.*)\">|U",str_replace("\n","",$results),$pname_tmp, PREG_PATTERN_ORDER);
$pname = iconv("shift_jis","utf-8",$pname_tmp[1][0]);

/*
$pcode_tmp = "";
$expcode = "";
preg_match_all("|<td class=\"bOne\">Item #(.*)</td>|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
$expcode = $pcode_tmp[1][0];
*/

//기본 이미지
/*
$images_tmp = "";
$prod_img_src = "";
preg_match_all("|<img SRC=\"(.*)\" .* NAME=\"p$expcode\"|U",$results,$images_tmp, PREG_PATTERN_ORDER);
$prod_img_src = $images_tmp[1][0];

if(substr($prod_img_src,0,5) != "http:"){
	$prod_img_src = "http:".$prod_img_src;
}
*/

$prod_img_src = "http://photo.kenko.com/".$pcode_tmp[1][0]."_L.jpg";

//상품 설명
$datas = "";
$datas = split("\n",$results);

//상품 설명 위치를 찾기위한 루프
$desc_start_line = "";
$desc_end_line = "";

for($i=0;$i < count($datas);$i++){
	$data = $datas[$i];

    //상품 설명 위치
    if(!$desc_start_line && substr_count($data,"<div class=\"description\" itemprop=\"description\">")){
		$desc_start_line = $i;
	}

	if(($desc_start_line && !$desc_end_line) && substr_count($data,"<div class=\"maker_comment_notice\">")){
		$desc_end_line = $i-3;
	}
}

$prod_desc_prod="";

for($i=$desc_start_line;$i <= $desc_end_line;$i++){
	$prod_desc_prod .= iconv("shift_jis","utf-8",$datas[$i]);
}

//echo $prod_desc_prod;
//exit;
/*
// 디테일 상품이미지
$optionnal_img_tmp = "";
$goods_detail_images = "";

$optionnal_img_tmp[1] = str_replace('?$PRODDETAIL$',"",$images_tmp[1]);
for($i=0 ;$i < count($optionnal_img_tmp[1]);$i++){
	$goods_detail_images[$i] = $optionnal_img_tmp[1][$i];
}
*/
/*
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
*/



// 재고 일시코드
if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}


$goods_desc_copy = 1;


?>