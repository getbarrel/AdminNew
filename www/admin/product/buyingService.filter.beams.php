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
preg_match_all("|\'Product ID\'\: \'(.*)\'|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
$pcode = $bs_site."_".$pcode_tmp[1][0];
/*
$bs_url_tmp="";
$bs_url_tmp=explode("_",$bs_url);
$pcode = $bs_site."_".str_replace(".html","",$bs_url_tmp[1]);
*/

//가격
$price_tmp = "";
$price = "";
preg_match_all("|<p class=\"price font_ll\">(.*)<span class=\"font_s\">|U",$results,$price_tmp, PREG_PATTERN_ORDER);
$price = str_replace(array(",","￥","\\","&yen;"),"",$price_tmp[1][0]);

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
//preg_match_all("|<p class=\"item_name font_ll font_b\">(.*)</p>|U",str_replace("\n","",$results),$pname_tmp, PREG_PATTERN_ORDER);
preg_match_all("|<p class=\"item_name font_ll font_b\">(.*)</p>|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
$pname = iconv("shift_jis","utf-8",$pname_tmp[1][0]);



//  상품이미지
$goods_detail_images_tmp = "";
$goods_detail_images = "";

preg_match_all("|<li class=\"color_vari\" id=\".*\" onclick=\"cov_img\(\'(.*)\'|U",$results,$goods_detail_images_tmp, PREG_PATTERN_ORDER);
$goods_detail_images = $goods_detail_images_tmp[1];


$goods_detail_images_tmp = "";
preg_match_all("|<li class=\"i_thumbnail\" id=\".*\" onclick=\"cov_img\(\'(.*)\'|U",$results,$goods_detail_images_tmp, PREG_PATTERN_ORDER);


for($i=0;$i<count($goods_detail_images_tmp[1]);$i++){
	array_push($goods_detail_images,$goods_detail_images_tmp[1][$i]);
}

$prod_img_src=$goods_detail_images[0];



//$prod_img_src = "http://photo.kenko.com/".$pcode_tmp[1][0]."_L.jpg";

//상품 설명
$datas = "";
$datas = split("\n",$results);

//상품 설명 위치를 찾기위한 루프
$desc_start_line = "";
$desc_end_line = "";
$desc_start_line2 = "";
$desc_end_line2 = "";
$desc_start_line3 = "";
$desc_end_line3 = "";
$option_start_line="";
$option_end_line="";

for($i=0;$i < count($datas);$i++){
	$data = $datas[$i];

    //상품 설명 위치
    if(!$desc_start_line && substr_count($data,"<div id=\"JpS\">")){
		$desc_start_line = $i;
	}

	if(($desc_start_line && !$desc_end_line) && substr_count($data,"<div id=\"Jp\" style=\"display:none;\">")){
		$desc_end_line = $i-1;
	}
	
	 //상품 설명 위치2
    if(!$desc_start_line2 && substr_count($data,"<div class=\"i_size\">")){
		$desc_start_line2 = $i;
	}

	if(($desc_start_line2 && !$desc_end_line2) && substr_count($data,"<div class=\"i_size i_detail\">")){
		$desc_end_line2 = $i-1;
	}

	 //상품 설명 위치2
    if(!$desc_start_line3 && substr_count($data,"<div class=\"i_size i_detail\">")){
		$desc_start_line3 = $i;
	}

	if(($desc_start_line3 && !$desc_end_line3) && substr_count($data,"<div class=\"artsit_banner\">")){
		$desc_end_line3 = $i-1;
	}
	
	//옵션위치 찾기
	if(!$option_start_line && substr_count($data,"<div class=\"cs\">")){
		$option_start_line = $i;
	}

	if(($option_start_line && !$option_end_line) && substr_count($data,"<div class=\"rearrival_txt\">")){
		$option_end_line = $i;
	}
	
}


$prod_desc_prod="";

for($i=$desc_start_line;$i <= $desc_end_line;$i++){
	$prod_desc_prod .= iconv("shift_jis","utf-8",$datas[$i]);
}

for($i=$desc_start_line2;$i <= $desc_end_line2;$i++){
	$prod_desc_prod .= iconv("shift_jis","utf-8",$datas[$i]);
}

for($i=$desc_start_line3;$i <= $desc_end_line3;$i++){
	$prod_desc_prod .= iconv("shift_jis","utf-8",$datas[$i]);
}

$options_area ="";
for($i=$option_start_line;$i <= $option_end_line;$i++){
	$options_area .= iconv("shift_jis","utf-8",$datas[$i]);
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

$option_tmp="";
$_option_tmp="";
preg_match_all("|<td class=\"color\">(.*)</td>|U",$results,$option_tmp, PREG_PATTERN_ORDER);
$_option_tmp = $option_tmp[1];


$option_detail_tmp="";
$_option_detail_tmp = "";
$_option_detail="";

preg_match_all("|<option value=\".*\">(.*)</option>|U",$options_area,$_option_detail_tmp, PREG_PATTERN_ORDER);

for($i=0,$j=0;$i<count($_option_detail_tmp[1]);$i++){
	if($_option_detail_tmp[1][$i]=="サイズを選択"){
		if($i!=0)	$j++;
	}else{
		if(!substr_count($_option_detail_tmp[1][$i],'在庫なし') > 0){
			$_option_detail[$j] .= "^".str_replace(' / 在庫あり','',$_option_detail_tmp[1][$i]);
		}
	}
}

$thumb_images_tmp="";
$thumb_images="";

preg_match_all("|<li class=\"color_vari\" .* onclick=\"cov_img\(\'(.*)\'\,.*\)\">|U",$options_area,$thumb_images_tmp, PREG_PATTERN_ORDER);

for($i=0;$i<count($thumb_images_tmp[1]);$i++){
	$thumb_images[$i]=str_replace("_D_500.","_D_35.",$thumb_images_tmp[1][$i]);
	$options_detail_images[$i]=$thumb_images_tmp[1][$i];
}


$option_key = 0;
$options[$option_key][option_type] = "9";
$options[$option_key][option_name] = "색상/사이즈";
$options[$option_key][option_kind] = "r";
$options[$option_key][option_use] = "1";

for($i=0;$i < count($_option_tmp);$i++){
	if($_option_detail[$i] !=""){
		$options[$option_key][details][$i][option_div] = iconv("shift_jis","utf-8",trim($_option_tmp[$i]));
		$options[$option_key][details][$i][etc1] = "사이즈 :".trim(substr($_option_detail[$i],1));
		$options[$option_key][details][$i][thumb_images] = $thumb_images[$i];
		$options[$option_key][details][$i][goods_images] = $options_detail_images[$i];
	}
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