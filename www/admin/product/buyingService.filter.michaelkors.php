<?

$results = "";
$soldout_message = "";

//	<div class="itemheadernew"><div class="prodtitleLG">Polar Bear Shawl Cardigan</div><font class=prodourprice>Price: &#036;135.00</font>
if(false){
	$snoopy = new Snoopy;
	$snoopy->agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)";
	$snoopy->referer = "http://www.michaelkors.com/";

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
	*/

	$snoopy->rawheaders["Pragma"] = "no-cache";

	//http://m.jcrew.com/$snoopy->fetch("http://www.jcrew.com/index.jsp");
	//echo $bs_url;
	$snoopy->fetch($bs_url);
	$results =  $snoopy->results;

	//echo $result;

}else{

	$cookie_nm = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_cookies/cookie_".$bs_site."_".session_id().".txt"; 
	//echo $cookie_nm;
	//실제 로그인이 이루어지는 Curl 입니다.
	//echo $bs_site_domain;
	
//echo $bs_url;
	$ch = curl_init(); 
	curl_setopt ($ch, CURLOPT_URL,"http://www.michaelkors.com/");                      // 접속할 URL 주소 
	//curl_setopt( $ch, CURLOPT_INTERFACE, "3.23.2.12" ); 
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);   // SSL 관련 설정 입니다.
	curl_setopt ($ch, CURLOPT_SSLVERSION,1);                  // 이부분 또한 윗 설정과 같이 SSL 관련 부분입니다.
	curl_setopt ($ch, CURLOPT_HEADER, 0);        // 페이지 상단에 헤더값 노출 유뮤 입니다. 0일경우 노출하지 않습니다.
	//curl_setopt( $ch, CURLOPT_HTTPHEADER, array("REMOTE_ADDR: 3.1.2.12", "HTTP_X_FORWARDED_FOR: 3.1.2.12"));
	curl_setopt ($ch, CURLOPT_POST, 1);           // 값 전송을 POST값을 전송 하겠다는 선언 입니다. 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $login_data);     // 전송할 POST 값입니다.
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);     // 설정 파일에 쿠키 데이터를 굽습니다.
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);    // 설정 파일의 쿠키 데이터를 페이지에 넣습니다.
	curl_setopt ($ch, CURLOPT_TIMEOUT, 30); 
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_VERBOSE, true); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	$results = curl_exec ($ch); 
	//echo $results;

	
	
	curl_setopt ($ch, CURLOPT_URL,$bs_url);   // 로그인후 이동할 페이지 입니다.
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm); 
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm); 
	$results = curl_exec ($ch); 
	
	curl_close ($ch); 

}
//echo $results;
//imageObj.reg="http://www.ralphlauren.com/graphics/product_images/pPOLO2-11733188_lifestyle_v360x480.jpg"; 
//필터 맨아래쪽 별도 팝업페이지에서 상세 이미지 가져올수 있도록 처리

//맨위 상품정보들만
$result = "";
$results_tmp = "";
$result = str_replace("\n","",$results);
preg_match_all("|<!-- Product -->(.*)<!-- type 3 box/color/size -->|U",$result,$results_tmp, PREG_PATTERN_ORDER);

//상품코드
$pcode_tmp = "";
$pcode = "";
preg_match_all("|<INPUT type=\"hidden\" name=\"prod0\".*value=\"(.*)\">|U",$results_tmp[1][0],$pcode_tmp, PREG_PATTERN_ORDER);
$pcode = $bs_site."_".$pcode_tmp[1][0];
//print_r($results_tmp);

//원가
$price_tmp = "";
$price = "";
preg_match_all("|<font CLASS=Black10V>(.*)</font>|U",$results_tmp[1][0],$price_tmp, PREG_PATTERN_ORDER);
$price = str_replace("$","",$price_tmp[1][0]);
//print_r($price_tmp);

//세일가격
$sale_price_tmp = "";
$sale_price = "";
preg_match_all("|<font CLASS=\"BRIGHTRED10\">(.*)</font>|U",$results_tmp[1][0],$sale_price_tmp, PREG_PATTERN_ORDER);
$sale_price = str_replace("$","",$sale_price_tmp[1][0]);
//print_r($sale_price);

//상품이름
$pname_tmp = "";
$pname = "";
preg_match_all("|<TD CLASS=Styleh>.*<br>(.*)</TD>|U",$results_tmp[1][0],$pname_tmp, PREG_PATTERN_ORDER);
$pname = trim($pname_tmp[1][0]);
//print_r($pname);


//기본 이미지와 기타 이미지
$style_code_tmp = "";
$style_code = "";
preg_match_all("|<TR><TD><FONT CLASS=GRAY10N>(.*)</FONT></TD></TR>|U",$results_tmp[1][0],$style_code_tmp, PREG_PATTERN_ORDER);
$style_code=$style_code_tmp[1][0];
//print_r($style_code);

$goods_detail_images_tmp = "";
$goods_detail_images = "";
preg_match_all("|title=\"$style_code\".*\n.*onClick=\"setImage\('(.*)'\,'|U",$results,$goods_detail_images_tmp, PREG_PATTERN_ORDER);
$goods_detail_images = $goods_detail_images_tmp[1];

for($i=0; $i < count($goods_detail_images); $i++){
	if(substr($goods_detail_images[$i],0,5) != "http:"){
		$goods_detail_images[$i] = "http:".$goods_detail_images[$i];
	}
}

$prod_img_src = "";
$prod_img_src= $goods_detail_images[0];
//print_r($goods_detail_images);


if(!$goods_detail_images){
	$goods_detail_images_tmp = "";
	preg_match_all("|<meta property=\"og:image\" content=\"(.*)\" />|U",$results,$goods_detail_images_tmp, PREG_PATTERN_ORDER);
	$goods_detail_images = $goods_detail_images_tmp[1];
	$prod_img_src=$goods_detail_images[0];
}

// 상품설명
$product_desc_tmp = "";
$prod_desc_prod = "";
preg_match_all("|<div class=\".*line\">(.*)</ul>|U",$results,$product_desc_tmp, PREG_PATTERN_ORDER);
$prod_desc_prod = strip_tags($product_desc_tmp[0][0],"<ul><li>");
//print_r($prod_desc_prod);


//옵션 컬러별 사이즈
$_option_tmp = "";
$_option_color = "";
$relation_size = "";
preg_match_all("|".$pcode_tmp[1][0]."Matrix\[.*\]\[.*\] = new product\('.*','".$pcode_tmp[1][0]."','.*','(.*)','(.*)',|U",$results,$_option_tmp, PREG_PATTERN_ORDER);

$_option_color=array_values(array_unique($_option_tmp[2]));
//print_r($_option_tmp);

for($j=0; $j < count($_option_color); $j++){
	for($i=0; $i < count($_option_tmp[0]); $i++){
		
		if(substr_count($_option_tmp[0][$i],"$_option_color[$j]")){
			$relation_size[$j][$i] = $_option_tmp[1][$i];
		}
	}
	$relation_size[$j] = implode("^",array_values($relation_size[$j]));
}
//print_r($relation_size);

if($sale_price > 0){
	$listprice = $price;
	$price = $sale_price;
}

if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
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

$option_key = 0;


$options = "";
if(is_array($_option_tmp)){

		//print_r($color_option_tmp);
		//exit;
		
		if(is_array($_option_tmp)){
			$options[$option_key][option_type] = "9";
			$options[$option_key][option_name] = "COLOR/SIZE";
			$options[$option_key][option_kind] = "r";
			$options[$option_key][option_use] = "1";
			for($i=0;$i < count($_option_color);$i++){
				$options[$option_key][details][$i][option_div] = ($_option_color[$i]=="null" ? "One" : $_option_color[$i]);
				$options[$option_key][details][$i][price] = "";
				$options[$option_key][details][$i][etc1] = "사이즈:".($relation_size[$i]=="null" ? "One" : $relation_size[$i]);
				//$options[$option_key][details][$i][thumb_images] = $goods_detail_images[$i];
				//$options[$option_key][details][$i][goods_images] = $goods_detail_images[$i];
			}
			
		}else{
			$options[$option_key][option_type] = "9";
			$options[$option_key][option_name] = "옵션";
			$options[$option_key][option_kind] = "s";
			$options[$option_key][option_use] = "1";
			for($i=0;$i < count($_option_tmp);$i++){
				$options[$option_key][details][$i+1][option_div] = str_replace("Select ","",trim($_option_tmp[$i]));
				$options[$option_key][details][$i+1][price] = "";
				$options[$option_key][details][$i+1][etc1] = "";

			}
			/*
			for($i=0;$i < count($_option_tmp);$i++){
				$option[$i] = str_replace("Select ","",trim($_option_tmp[$i]));
			}
			*/
		}
		$option_key++;
		//print_r($options);
}	

//print_r($options[0]);
/*
echo "http://www.ralphlauren.com/contentPopup/index.jsp?productId=".$pcode;
$Tag = curl_init();
curl_setopt( $Tag , CURLOPT_URL , "http://www.ralphlauren.com/contentPopup/index.jsp?productId=".$pcode); 

ob_start();
curl_exec( $Tag );
curl_close( $Tag );
$results = ob_get_contents();
ob_clean();
echo $results;
preg_match_all("|enh:.*'(.*)'.*|U",$results,$detail_images, PREG_PATTERN_ORDER);
print_r($detail_images);
exit;
*/

$goods_desc_copy = 1;

?>