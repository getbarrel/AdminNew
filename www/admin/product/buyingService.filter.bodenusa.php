<?php
if(true){
	$snoopy = new Snoopy;
	$snoopy->agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)";
	$snoopy->referer = "http://www.bodenusa.com/";

	// set some cookies:
	/*
	$snoopy->cookies["emailPopUnder"] = 'yes';
	$snoopy->cookies["jcrew_homeimage"] = "A";
	$snoopy->cookies["bn_u"] = "UNASSIGNED";
	$snoopy->cookies["jcrew_country"] = "US";
	$snoopy->cookies["JSESSIONID"] = "PH0HSNbpvGFCywGnpXX6HdDBkBT4F8WTwRGQf66NJ1JGZvn4JT1V!-436849081";
	$snoopy->cookies["jcrew_country_phase1"] = "KR";
	$snoopy->cookies["__psrw"] = "3a12c7ca-5d6d-11e1-a98c-12313d1ca226";
	$snoopy->cookies["s_vi"] = "[CS]v1|27A287A00548A510-60000101800B801F[CE]";
	$snoopy->cookies["bn_guide"] = true;
	$snoopy->cookies["stop_mobi"] = "yes";
	$snoopy->cookies["s_cc"] = true;
	$snoopy->cookies["s_sq"] = "%5B%5BB%5D%5D";
	$snoopy->cookies["bn_cd"] = "d%26g%26s";

	$snoopy->rawheaders["Pragma"] = "no-cache";
*/
	//http://m.jcrew.com/$snoopy->fetch("http://www.jcrew.com/index.jsp");
	//echo $bs_url;
	$snoopy->fetch($bs_url);
	$results =  $snoopy->results;

}else{

	$cookie_nm = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_cookies/cookie_".$bs_site."_".session_id().".txt"; 
	//echo $cookie_nm;
	//실제 로그인이 이루어지는 Curl 입니다.
	//echo $bs_site_domain;
	
//echo $bs_url;
	$ch = curl_init(); 
	curl_setopt ($ch, CURLOPT_URL,$bs_url);                      // 접속할 URL 주소 
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);   // SSL 관련 설정 입니다.
	curl_setopt ($ch, CURLOPT_SSLVERSION,1);                  // 이부분 또한 윗 설정과 같이 SSL 관련 부분입니다.
	curl_setopt ($ch, CURLOPT_HEADER, 0);        // 페이지 상단에 헤더값 노출 유뮤 입니다. 0일경우 노출하지 않습니다.
	curl_setopt ($ch, CURLOPT_POST, 1);           // 값 전송을 POST값을 전송 하겠다는 선언 입니다. 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $login_data);     // 전송할 POST 값입니다.
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);     // 설정 파일에 쿠키 데이터를 굽습니다.
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);    // 설정 파일의 쿠키 데이터를 페이지에 넣습니다.
	curl_setopt ($ch, CURLOPT_TIMEOUT, 30); 
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	$results = curl_exec ($ch); 
	//echo $results;

	
	
	curl_setopt ($ch, CURLOPT_URL,$bs_url);   // 로그인후 이동할 페이지 입니다.
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm); 
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm); 
	$results = curl_exec ($ch); 
	
	curl_close ($ch); 

}

//	echo $results;
//	exit;


/**
 * 필수 사용 변수 초기화 12.05.14 bgh 
 */
unset($price);
unset($saleprice);
unset($pname);
unset($pcode);
unset($prod_img_src);
unset($saleprice);
unset($option);
unset($options);
unset($prod_desc_prod);
unset($goods_detail_images);
unset($goods_detail_images_tmp2);
unset($goods_detail_images_tmp3);


preg_match_all("|var GroupID = \"(.*)\";|U",$results,$groupid_tmp, PREG_PATTERN_ORDER);
//print_r($groupid_tmp);
$GroupID = $groupid_tmp[1][0];

preg_match_all("|var GenderCode = \"(.*)\";|U",$results,$gendercode_tmp, PREG_PATTERN_ORDER);
//print_r($groupid_tmp);
$GenderCode = $gendercode_tmp[1][0];

preg_match_all("|var LanguageID = \"(.*)\";|U",$results,$LanguageID_tmp, PREG_PATTERN_ORDER);
//print_r($groupid_tmp);
$LanguageID = $LanguageID_tmp[1][0];

preg_match_all("|var SegmentID = \"(.*)\";|U",$results,$SegmentID_tmp, PREG_PATTERN_ORDER);
//print_r($groupid_tmp);
$SegmentID = $SegmentID_tmp[1][0];

preg_match_all("|var Tier2 = \"(.*)\";|U",$results,$Tier2_tmp, PREG_PATTERN_ORDER);
//print_r($groupid_tmp);
$Tier2 = $Tier2_tmp[1][0];



preg_match_all("|tmParam\[\"ProductName\"\] = \"(.*)\";|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
$pname = $pname_tmp[1][0];
//print_r($pname_tmp);
//exit;

preg_match_all("|tmParam\[\"ProductID\"\] = \"(.*)\";|U",$results,$boden_pid_tmp, PREG_PATTERN_ORDER);
$boden_pid = $boden_pid_tmp[1][0];

preg_match_all("|<span .* class=\"prices smallText boldText price\".*>(.*)</span>|U",str_replace(array("\n","\r","\t"),"",$results),$price_tmp, PREG_PATTERN_ORDER);
//print_r($price_tmp);
$price = str_replace(array("\t"," ","$"),"",trim($price_tmp[1][0]));

preg_match_all("|<span .*class=\"NowPrice\" data-price-section=\"nowPrice\".*>(.*)</span>|U",str_replace(array("\n","\r","\t"),"",$results),$price_tmp, PREG_PATTERN_ORDER);
//print_r($price_tmp);
$price = str_replace(array("\t"," ","$"),"",trim($price_tmp[1][0]));
if(!is_numeric($price)){
	$__price = explode("to",$price);
	if(is_numeric($__price[0])){
		$price = $__price[1];
	}
	//print_r($__price);
}


//echo $price;
//exit;

if($price == ""){
	$datas = split("\n",$results);
	for($i=0;$i < count($datas);$i++){
		$data = $datas[$i];

		if(!$price && substr_count($data,"class=\"noFloat prices boldText price\"")){
			$price = str_replace("$","",strip_tags($datas[$i+1]));
		}
	}
}

if($price == ""){
	preg_match_all("|{\"colour\":\"(.*)\",\"colourDescr\":\"(.*)\".*\"n\":\"(.*)\".*}|U",str_replace(array("\n","\r","\t"),"",$results),$prices_tmp, PREG_PATTERN_ORDER);
	//print_r($prices_tmp);
	//$price = str_replace(array("\t"," ","$"),"",trim($price_tmp[1][0]));
	//echo $Tier2;
	//exit;
	for($i=0;$i < count($prices_tmp[0]);$i++){
		//echo $prices_tmp[1][$i] ."== ".$Tier2." ==>".($prices_tmp[1][$i] == $Tier2 ? "참":"거짓")."<br>";
		if($prices_tmp[1][$i] == $Tier2){
			$price = $prices_tmp[3][$i];
		}
	}
}
//echo $price;

//preg_match_all("|Large:'(.*)', |U",$results,$goods_detail_images_tmp2, PREG_PATTERN_ORDER);
$url_infos = split("/",$bs_url);
//parse_str($bs_url, $url_infos);
$pcode = $url_infos[count($url_infos)-2];
//$product_color_infos = split("-",$url_infos[5]);
$product_color = $Tier2;//$product_color_infos[1];
//echo "product_color:".$product_color."<br>";
//echo "colourImageContainer\('".trim($product_color)."', '', '','(.*)',";
if($product_color != ""){
	preg_match_all("|colourImageContainer\('".trim($product_color)."', '', '', '(.*)',|U",$results,$goods_detail_images_tmp2, PREG_PATTERN_ORDER);
}
// 선택된 이미지 외에 추가이미지 모두 가져오도록 수정 12.07.12 bgh
preg_match_all("|colourImageContainer\('.*', '', '', '(.*)',|U",$results,$goods_detail_images_tmp3, PREG_PATTERN_ORDER);

//for($i=0;$i < count($goods_detail_images_tmp2);$i++){

//}
//print_r($goods_detail_images_tmp2);

for($i=0, $j=0;$i < count($goods_detail_images_tmp3[1]);$i++){
	//parse_url($goods_detail_images_tmp2[1]);
	//echo substr($goods_detail_images_tmp2[1][$i],0,4)."<br>";
	if(substr($goods_detail_images_tmp3[1][$i],0,4) == "http"){
		$goods_detail_images[$j] = $goods_detail_images_tmp3[1][$i];
		$j++;
	}
}
if(is_array($goods_detail_images)){
    $goods_detail_images = array_unique($goods_detail_images);
    
    //선택된 이미지가 있는경우 썸네일이미지로 선택 12.07.12 bgh
    $prod_img_src = $goods_detail_images_tmp2[1][0];
    //선택된 이미지가 없는경우에 모든이미지중에서 하나를 썸네일이미지로 선택 12.07.12 bgh
    if($prod_img_src == "" || $prod_img_src == NULL){
        $prod_img_src = $goods_detail_images[0];
    }
}


//$goods_detail_images = $goods_detail_images_tmp[1];
/*
for($j=0;$j < count($goods_detail_images_tmp[1]);$j++, $i++){
	//parse_url($goods_detail_images_tmp2[1]);

	$goods_detail_images[$i] = str_replace("height=75","height=393",str_replace("width=75","width=393",$goods_detail_images_tmp[1][$j]));
}
*/
preg_match_all("|<meta name=\"description\" content=\"(.*)\" />|U",$results,$prod_desc_tmp, PREG_PATTERN_ORDER);
$prod_desc_prod = $prod_desc_tmp[1][0];
//print_r($prod_desc_tmp);
//exit;

//changeViewImg
//

if(count($goods_detail_images) > 0){
	$_prod_desc = "<table align=center>";
	$_prod_desc .= "<tr><td align=center><div style='text-align:left;'>".$prod_desc_prod."</div></td></tr>";
	for($i=0;$i < count($goods_detail_images);$i++){
		$_prod_desc .= "<tr><td align=center style='padding:20px 0px;'><IMG src=\"".$goods_detail_images[$i]."\"></td></tr>";
	}
	$_prod_desc .= "</table>";

	$prod_desc_prod = $_prod_desc;
}


//echo "http://www.bodenusa.com/ClientControls/Products/SellingGridTab.aspx?Tier1=".$boden_pid."&Tier2=".$Tier2."&GroupID=".$GroupID."&SegmentID=".$SegmentID."&GenderCode=".$GenderCode."&LanguageID=".$LanguageID;
$snoopy->fetch("http://www.bodenusa.com/ClientControls/Products/SellingGridTab.aspx?Tier1=".$boden_pid."&Tier2=".$Tier2."&GroupID=".$GroupID."&SegmentID=".$SegmentID."&GenderCode=".$GenderCode."&LanguageID=".$LanguageID."");
$results =  $snoopy->results;

//echo $results;

preg_match_all("|<th id=\".*\"  col=\"(.*)\">(.*)</th>|U",str_replace(array("\n","\r","\t"),"",$results),$size_tmp, PREG_PATTERN_ORDER);
//echo "<td col=\"(.*)\" cap=\".*\(".$product_color."\).*\" status=\"In stock\".*>";
//preg_match_all("|<td col=\"(.*)\" cap=\".*\(".$product_color."\).*\" status=\"In stock\">|U",$results,$size_stock_status_tmp, PREG_PATTERN_ORDER);

preg_match_all("|<img width=\"14\" height=\"14\" src=\"(.*)\".*alt=\"(.*)\" .*>|U",str_replace("\n", " ",$results),$color_tmp, PREG_PATTERN_ORDER);
//print_r($color_tmp);


preg_match_all("|<td col=\"(.*)\" cap=\"(.*)\" status=\"(.*)\" .*>|U",$results,$size_stock_status_tmp, PREG_PATTERN_ORDER);
//print_r($size_stock_status_tmp);//[In stock|Low stock]

//print_r($size_tmp);
//$option = str_replace(array("\t"," ","$"),"",trim($price_tmp[1][0]));
$option_key = 0;
$stock_bool = false;
$soldout_message = "재고부족";
$options[$option_key][option_type] = "9";
$options[$option_key][option_name] = "색상";
$options[$option_key][option_kind] = "r";
$options[$option_key][option_use] = "1";
for($i=0;$i < count($color_tmp[2]);$i++){
	$options[$option_key][details][$i][option_div] = trim($color_tmp[2][$i]);
	$options[$option_key][details][$i][price] = "";
	unset($relation_size);
	for($j=0, $x=0;$j < count($size_stock_status_tmp[2]);$j++){
		//print_r($size_stock_status_tmp[3][$j]);
		//echo "'".$size_stock_status_tmp[3][$j]."'";
		//echo "<br>";
		if(strtolower ($size_stock_status_tmp[3][$j]) == "in stock" || strtolower ($size_stock_status_tmp[3][$j]) == "low stock"){//
			//echo $size_stock_status_tmp[2][$j].":::".$color_tmp[2][$i]."::". $size_stock_status_tmp[3][$j]."<br>";
			if(substr_count(" ".$size_stock_status_tmp[2][$j],$color_tmp[2][$i])){			
				$relation_size[$x] = trim(str_replace($color_tmp[2][$i],"",$size_stock_status_tmp[2][$j]));			
				$stock_bool = true;
				$soldout_message = "";
				
				$x++;
			}
		}
	}
	if(is_array($relation_size)){
		$options[$option_key][details][$i][etc1] = "사이즈:".implode("^",$relation_size);
	}else{
		unset($options[$option_key][details][$i]);//사이즈 없는것들은 제거
	}
	$options[$option_key][details][$i][thumb_images] = $color_tmp[1][$i];
	$options[$option_key][details][$i][goods_images] = "";
	

}


//print_r($options);
/*
if(count($size_tmp[1]) > 0){
	//$__option[0] = "Select Size";
	$option[0] = "Select Size";
	for($i=0, $j=0;$i < count($size_tmp[1]);$i++){
		if(in_array($size_tmp[1][$i],$size_stock_status_tmp[1])){
			//$__option[$i][col] = trim(strip_tags($size_tmp[1][$i]));
			$option[$j+1] = trim(strip_tags($size_tmp[2][$i]));
			$j++;
		}
	}
}
*/
//print_r($option);
/*
if(count($size_tmp[1]) > 0){
	$option[0] = "Select Size";
	for($i=0, $j=0;$i < count($size_tmp[1]);$i++){
			$option[$i+1] = trim(strip_tags($size_tmp[1][$i]));
	}
}
*/
//print_r($option);

//preg_match_all("|cap=\"<span class='desc'>(.*)</span>\">|U",str_replace(array("\n","\r","\t"),"",$results),$color_tmp, PREG_PATTERN_ORDER);
/*
preg_match_all("|<span class=\"cap\">(.*)</span>|U",str_replace(array("\n","\r","\t"),"",$results),$color_tmp, PREG_PATTERN_ORDER);
//print_r($color_tmp);

if(count($color_tmp[1]) > 0){
	$option2[0] = "Select Color";
	for($i=0, $j=0;$i < count($color_tmp[1]);$i++){
			$option2[$i+1] = trim($color_tmp[1][$i]);
	}
}
*/
//print_r($option2);
//echo $results;
//exit;
//echo  $soldout_message;

if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}

$pcode = $bs_site."_".$pcode;