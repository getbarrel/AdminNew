<?
$results = "";
$soldout_message = "";

$snoopy = new Snoopy;
$snoopy->fetch($bs_url);
$results =  $snoopy->results;



//상품코드
$pcode_tmp = "";
$pcode = "";
preg_match_all("|<input type=\"hidden\" name=\"product_id\" id=\"product_id\" value=\"(.*)\" />|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
$pcode = $bs_site."_".$pcode_tmp[1][0];


//가격
$price_tmp = "";
$price = "";
preg_match_all("|<dl class=\"price\"><dt>.*</dt><dd>(.*)</dd></dl>|U",$results,$price_tmp, PREG_PATTERN_ORDER);
$price = str_replace(array(",","￥","\\"),"",$price_tmp[1][0]);


//상품이름
$pname_tmp = "";
$pname = "";
//preg_match_all("|s.pageName=\"pd:(.*)\"|U",str_replace("\n","",$results),$pname_tmp, PREG_PATTERN_ORDER);
preg_match_all("|<h1>(.*)</h1>|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
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
preg_match_all("|<a href=\"(.*)\" rel=\"lightbox\[roadtrip\]\"|U",$results,$images_tmp, PREG_PATTERN_ORDER);

for($i=0 ;$i < count($images_tmp[1]);$i++){
	$goods_detail_images[$i] = $images_tmp[1][$i];
}

$prod_img_src = $goods_detail_images[0];

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
    if(!$desc_start_line && substr_count($data,"<div id=\"detail_info\">")){
		$desc_start_line = $i;
	}

	if(($desc_start_line && !$desc_end_line) && substr_count($data,"</div>")){
		$desc_end_line = $i;
	}
}

$prod_desc_prod="";

for($i=$desc_start_line;$i <= $desc_end_line;$i++){
	$prod_desc_prod .= $datas[$i];
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