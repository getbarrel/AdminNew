<?
/**
 * 갭,올드네이비,파이퍼라임 통합 스크래핑 필터
 * 팬텀js만 이용하도록 변경
 * 
 * @author bgh
 * @date 2012.11.21 
 * @last 2013.10.28 phantomjs수정 및 기본이미지 수정
 */
$result = "";
$goods_info = "";
//echo "phantomjs  --load-images=no " . $_SERVER ["DOCUMENT_ROOT"] . "/admin/scrape/gap.js '" . $bs_url . "'";
$result = shell_exec ( "phantomjs  --load-images=no " . $_SERVER ["DOCUMENT_ROOT"] . "/admin/scrape/gap.js '" . $bs_url . "'" );
$goods_info = ( array ) json_decode ( $result );
//print_r ( $goods_info );

// 페이지에 노출된 상품컬러
$activeColor = "";
$activeColor = $goods_info [activeColor];

// 상품코드
$pcode = "";
$pcode = $bs_site . "_" . $goods_info [pid];

// 상품명
$pname = "";
$pname = $goods_info [name];

// 기본이미지
$prod_img_src = "";
if( substr_count($goods_info [variantStyleColors] [$activeColor]->VLI, $bs_site) > 0){
	$prod_img_src = $goods_info [variantStyleColors] [$activeColor]->VLI;
}else{
	$prod_img_src = 'http://'.$bs_site . $goods_info [variantStyleColors] [$activeColor]->VLI;
}

// 가격(페이지에 노출된상품가격)
$price = "";
$listprice = "";
$price = $goods_info [variantStyleColors] [$activeColor]->regularPrice;

if (! empty ( $goods_info [variantStyleColors] [$activeColor]->salePrice )) {
	$price = $goods_info [variantStyleColors] [$activeColor]->salePrice;
	$listprice = $goods_info [variantStyleColors] [$activeColor]->regularPrice;
} else {
	$listprice = $price;
}

if (substr_count ( $goods_info [variantStyleColors] [$activeColor]->strPartialMupMessage, "Now $" ) > 0) {
	$price = $goods_info [variantStyleColors] [$activeColor]->strPartialMupMessage;
	$listprice = $goods_info [variantStyleColors] [$activeColor]->regularPrice;
}

$price_replace_array = array (
		"Now",
		"$",
		"¥",
		"," 
);

$price = str_replace ( $price_replace_array, "", $price );
$price = str_replace ( $price_replace_array, "", $price );
$listprice = str_replace ( $price_replace_array, "", $listprice );

// 상세설명
$prod_desc_prod = "";
$prod_desc_prod = "<table align=center>";
if (! empty ( $goods_info [fabricContents] )) {
	$prod_desc_prod .= "<tr><td align=center><div style='text-align:left;'><b>fabric & care</b></div></td></tr>";
	$prod_desc_prod .= "<tr><td align=center>
        						<div style='text-align:left;'>
            						<li>" . $goods_info [fabricContents] [0]->percent . "% " . $goods_info [fabricContents] [0]->name . ".</li>
            						<li>" . $goods_info [careInstructionText] . "</li>
            						<li>" . ($goods_info [isImported] ? "Imported." : "Made in USA.") . "</li>
        						</div>
    						</td>
    					</tr>";
}
for($k = 0; $k < count ( $goods_info [infoTabs] ); $k ++) {
	$prod_desc_prod .= "<tr><td align=center><div style='text-align:left;'><b>" . $goods_info [infoTabs] [$k]->infoTabName . "</b></div></td></tr>";
	$prod_desc_prod .= "<tr><td align=center>
        						<div style='text-align:left;'>";
	for($i = 0; $i < count ( $goods_info [infoTabs] [$k]->InfoTabInfoBlocks ); $i ++) {
		$prod_desc_prod .= "<li>" . $goods_info [infoTabs] [$k]->InfoTabInfoBlocks [$i]->displayText . "</li>";
	}
	
	$prod_desc_prod .= "		</div>
    						</td>
    					</tr>";
}
$prod_desc_prod .= "<tr><td align=center>";
for($i = 0; $i < count ( $goods_info [variantStyleColors] ); $i ++) {
	$img_src = '';
	if( substr_count($goods_info [variantStyleColors] [$activeColor]->VLI, $bs_site) > 0){
		$img_src = $goods_info [variantStyleColors] [$i]->VLI;
	}else{
		$img_src = 'http://'.$bs_site . $goods_info [variantStyleColors] [$i]->VLI;
	}
	$prod_desc_prod .= "<tr><td align=center style='padding:0px 0px 0px 0px;'><b>" . $goods_info [variantStyleColors] [$i]->colorName . "</b></td></tr>";
	$prod_desc_prod .= "<tr><td align=center style='padding:5px 0px 30px 0px;'><IMG src=\"" . $img_src . "\"></td></tr>";
}
$prod_desc_prod .= "</table>";

// 옵션
$option_key = 0;
$options = "";
if (is_array ( $goods_info [stocks] )) {
	$options [$option_key] [option_type] = "9";
	$options [$option_key] [option_name] = "OPTION";
	$options [$option_key] [option_kind] = "b";
	$options [$option_key] [option_use] = "1";
	
	for($i = 0; $i < count ( $goods_info [stocks] ); $i ++) {
		if ($goods_info [stocks] [$i]->bStock) {
			$_color = "";
			$_optionDiv = "";
			$_optionPrice = "";
			
			$_color = $goods_info [stocks] [$i]->colorIndex;
			$_optionDiv = $goods_info [variantStyleColors] [$_color]->colorName;
			$_optionDiv .= " / " . $goods_info [stocks] [$i]->dimension1Name;
			if (! empty ( $goods_info [stocks] [$i]->dimension2Name )) {
				$_optionDiv .= " / " . $goods_info [stocks] [$i]->dimension2Name;
			}
			$options [$option_key] [details] [$i] [option_div] = $_optionDiv;
			
			$_optionPrice = $goods_info [variantStyleColors] [$_color]->regularPrice;
			if (! empty ( $goods_info [variantStyleColors] [$_color]->salePrice )) {
				$_optionPrice = $goods_info [variantStyleColors] [$_color]->salePrice;
			}
			if (substr_count ( $goods_info [variantStyleColors] [$_color]->strPartialMupMessage, "Now $" ) > 0) {
				$_optionPrice = $goods_info [variantStyleColors] [$_color]->strPartialMupMessage;
			}
			
			// 20130906 Hong
			$_optionPrice = str_replace ( $price_replace_array, "", $_optionPrice );
			$options [$option_key] [details] [$i] [price] = $_optionPrice;
		}
	}
}


if ($goods_info [isInStock]) {
	$stock_bool = true;
} else {
	$price = "";
	$stock_bool = false;
}

$goods_desc_copy = 1;
