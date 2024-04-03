<?php /////////////////////////////////////////////////////
$results = "";
$soldout_message = "";

$snoopy = new Snoopy;
$snoopy->fetch($bs_url);
$results =  $snoopy->results;

print_r($results);


//상품명
$pname_tmp = "";
$pname = "";
preg_match_all("|<h1 id=\"products_maintitle\" class=\"pName\">(.*)</h1>|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
$pname = strip_tags($pname_tmp[1][0]);


//상품가격
$list_price_tmp = "";
$price = "";
preg_match_all("|id=\"js_scl_unitPrice\">￥(.*)</strong>|U",$results,$list_price_tmp, PREG_PATTERN_ORDER);
$price = $list_price_tmp[1][0];


//상품코드
$pcode_tmp = "";
$pcode = "";
preg_match_all("|prodid: '(.*)',|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
$pcode = $bs_site."_".$pcode_tmp[1][0];


//기본 이미지
$images_tmp = "";
$prod_img_src = "";
preg_match_all("|id=\"mainImg\" src=\"(.*)\"/>|U",$results,$images_tmp, PREG_PATTERN_ORDER);
$prod_img_src = $images_tmp[1][1];


//상품설명
$prod_desc_prod_tmp = "";
$prod_desc_prod = "";
preg_match_all("|<!-- pRecContainer -->(.*)<!-- /pRecContainer -->|U",$results,$prod_desc_prod_tmp, PREG_PATTERN_ORDER);
foreach($prod_desc_prod_tmp[1] as $value):
	$prod_desc_prod .= $value;
endforeach;



?>