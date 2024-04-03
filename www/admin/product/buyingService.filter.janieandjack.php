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
preg_match_all("|<input type=\"hidden\" name=\"PRODUCT<>prd_id\" value=\"(.*)\">|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
$pcode = $bs_site."_".$pcode_tmp[1][0];


//가격
$price_tmp = "";
$price = "";
preg_match_all("|<strike>(.*)</strike>|U",$results,$price_tmp, PREG_PATTERN_ORDER);
$price = str_replace("$","",$price_tmp[1][0]);

$saleprice_tmp = "";
$sale_price = "";
preg_match_all("|<span CLASS=\"cprice\">(.*)</span>|U",$results,$saleprice_tmp, PREG_PATTERN_ORDER);
$sale_price = str_replace("$","",$saleprice_tmp[1][0]);
//print_r($saleprice_tmp);

if($sale_price > 0){
	$listprice = $price;
	$price = $sale_price;
}

//상품이름
$pname_tmp = "";
$pname = "";
preg_match_all("|<td class=\"bOne\"><b>(.*)</b><br><br>|U",str_replace("\n","",$results),$pname_tmp, PREG_PATTERN_ORDER);
$pname = $pname_tmp[1][0];


$pcode_tmp = "";
$expcode = "";
preg_match_all("|<td class=\"bOne\">Item #(.*)</td>|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
$expcode = $pcode_tmp[1][0];

//기본 이미지
$images_tmp = "";
$prod_img_src = "";
preg_match_all("|<img SRC=\"(.*)\" .* NAME=\"p$expcode\"|U",$results,$images_tmp, PREG_PATTERN_ORDER);
$prod_img_src = $images_tmp[1][0];

if(substr($prod_img_src,0,5) != "http:"){
	$prod_img_src = "http:".$prod_img_src;
}

//상품 설명
$prod_desc_tmp = "";
$prod_desc_prod = "";
preg_match_all("|class=\"bOne\">(.*)</td>|U",str_replace("\n","",$results),$prod_desc_tmp, PREG_PATTERN_ORDER);

for($i=2;$i < count($prod_desc_tmp[1])-5 ;$i++){
	if(substr_count(" ".$prod_desc_tmp[1][$i],"<span CLASS=\"cprice\">") == 0){
		$prod_desc_prod.="<li>".$prod_desc_tmp[1][$i];
	}else{
		break;
	}
}

// 디테일 상품이미지
$optionnal_img_tmp = "";
$goods_detail_images = "";

$optionnal_img_tmp[1] = str_replace('?$PRODDETAIL$',"",$images_tmp[1]);
for($i=0 ;$i < count($optionnal_img_tmp[1]);$i++){
	$goods_detail_images[$i] = $optionnal_img_tmp[1][$i];
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
for($i=0;$i < count($datas);$i++){
	$data = $datas[$i];
	
	
	if(!$option_start_line && substr_count($data,"<option value=\"0\">Select a")){
		$option_start_line = $i;
	}
	
	if(($option_start_line && !$option_end_line) && substr_count($data,"</select>")){
		$option_end_line = $i+1;
	}
/*
	if(!$option2_start_line && substr_count($data,"<select id=\"".$dimensionType."\" class=\"btn secondary\" name=\"dimensionValues\">")){
		$option2_start_line = $i;
	}
	
	if(($option2_start_line && !$option2_end_line) && substr_count($data,"</select>")){
		$option2_end_line = $i+1;
	}
*/
	
}

// 옵션1,2의 문자열 저장
$options_str = "";
for($i=$option_start_line;$i < $option_end_line;$i++){
	$options_str .= $datas[$i];	
}

/*
$options2_str = "";
for($i=$option2_start_line;$i < $option2_end_line;$i++){
	$options2_str .= $datas[$i];	
}
*/
// 옵션1,2의 문자열 배열로 저장
$option1_spos = "";
$option1_epos = "";
$options1_str = "";
$options = "";
$_option = "";
$option = "";
if($options_str){
	
    $options_str = str_replace("\\","",$options_str);
	$option1_spos = strrpos($options_str,"<option value=\"0\">Select a");
	$option1_epos = strpos($options_str,"</select>");
	$options1_str = substr($options_str,$option1_spos,$option1_epos+1);
	preg_match_all("|<option .*>(.*)</option>|U",$options1_str,$options, PREG_PATTERN_ORDER);
	$_option = $options[1];
	
	$option[0] = "Size";
	for($i=1;$i < count($_option);$i++){
			$option[$i] = str_replace("&nbsp;","",trim($_option[$i]));
	}
	//echo "#option1-";
    //print_r($option);
}else{
	$soldout_message = "sold out";
}
/*
if($options2_str){
	preg_match_all("|<option .*>(.*)</option>|U",$options2_str,$options2, PREG_PATTERN_ORDER);

	$_option2 = $options2[1];
	
	for($i=0;$i < count($_option2);$i++){
		$option2[$i] = str_replace("Select a","",trim($_option2[$i]));
	}
	echo "#option2-";
    print_r($option2);
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