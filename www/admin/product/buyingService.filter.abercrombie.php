<?
//	<div class="itemheadernew"><div class="prodtitleLG">Polar Bear Shawl Cardigan</div><font class=prodourprice>Price: &#036;135.00</font>

$results = "";
$soldout_message = "";

if(false){
	$snoopy = new Snoopy;
	$snoopy->agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)";
	$snoopy->referer = "http://www.abercrombie.com/";

	// set some cookies:
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
	curl_setopt ($ch, CURLOPT_URL,"http://www.abercrombie.com/");                      // 접속할 URL 주소 
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

	//$bs_url = "http://www.abercrombie.com/webapp/wcs/stores/servlet/CategoryDisplay?catalogId=10901&storeId=10051&langId=-1&topCategoryId=12202&categoryId=60496&parentCategoryId=68948";
	
	 curl_setopt($ch, CURLOPT_URL, $bs_url); 
	 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE); 
	 curl_setopt($ch, CURLOPT_TIMEOUT, 10); 
	 curl_setopt($ch, CURLOPT_HEADER, 0); 
	 curl_setopt($ch, CURLOPT_POST, true); 
	 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
	 
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
	 curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

	 if ($proxy_ip) { //프록시사용시
	  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	  curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, TRUE);
	  curl_setopt($ch, CURLOPT_PROXY, $proxy_ip);
	 }

	$results = curl_exec ($ch); 
	
	curl_close ($ch); 

}

/**
 * 필수 사용 변수 초기화 12.05.14 bgh 
 */

//imageObj.reg="http://www.ralphlauren.com/graphics/product_images/pPOLO2-11733188_lifestyle_v360x480.jpg"; 
//필터 맨아래쪽 별도 팝업페이지에서 상세 이미지 가져올수 있도록 처리

$pcode_tmp = "";
$pcode = "";
preg_match_all("|<input type=\"hidden\" name=\"productId\" value=\"(.*)\"/>|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
$pcode = $bs_site."_".$pcode_tmp[1][0];

$price_tmp = "";
$price = "";
preg_match_all("|<input type=\"hidden\" name=\"price\" value=\"(.*)\"/>|U",$results,$price_tmp, PREG_PATTERN_ORDER);
$price = str_replace("$","",$price_tmp[1][0]);

$saleprice_tmp = "";
$sale_price = "";
preg_match_all("|<input type=\"hidden\" name=\"promoPrice\" value=\"(.*)\"/>|U",$results,$saleprice_tmp, PREG_PATTERN_ORDER);
$sale_price = str_replace("$","",$saleprice_tmp[1][0]);

$image_wrap_tmp = "";
$image_wrap = "";
preg_match_all("|<div class=\"image-wrap.*\" id=\"(.*)\"> |U",$results,$image_wrap_tmp, PREG_PATTERN_ORDER);
$image_wrap = $image_wrap_tmp[1][0];

$collection_tmp = "";
$collection = "";
preg_match_all("|<input type=\"hidden\" name=\"collection\" value=\"(.*)\"/>|U",$results,$collection_tmp, PREG_PATTERN_ORDER);
$collection = $collection_tmp[1][0];

$pname_tmp = "";
$pname = "";
preg_match_all("|<h2 class=\"name\">(.*)</h2>|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
$pname = $pname_tmp[1][0];

if(!$pname){
	preg_match_all("|<h1 class=\"name\">(.*)</h1>|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
	$pname = $pname_tmp[1][0];
}

$images_tmp = "";
$prod_img_src = "";
preg_match_all("|<img class=\"prod-img\" src=\"(.*)\".*>|U" ,$results,$images_tmp, PREG_PATTERN_ORDER);
$prod_img_src = $images_tmp[1][0];

if(substr($prod_img_src,0,5) != "http:"){
	$prod_img_src = "http:".$prod_img_src;
}

$detail_images_seq = "";
$goods_detail_images = "";
preg_match_all("|<a class=\"swatch-link\" href=\".*seq=(.*)&newCheckout.*\"|U",$results,$detail_images_seq, PREG_PATTERN_ORDER);

for($i=0 ;$i < count($detail_images_seq[1]);$i++){
	
	$goods_detail_images[$i] = str_replace($image_wrap,$collection."_".$detail_images_seq[1][$i],$prod_img_src);
}

if(!$goods_detail_images){
	$goods_detail_images[0]=$prod_img_src;
}

$thumb_images_tmp = "";
$thumb_images = "";
preg_match_all("|imageObj.swatch=\"(.*)\";|U",$results,$thumb_images_tmp, PREG_PATTERN_ORDER);
$thumb_images = $thumb_images_tmp[1];

$product_desc_tmp = "";
$prod_desc_prod = "";
preg_match_all("|<p class=\"copy\">(.*)</p>|U",$results,$product_desc_tmp, PREG_PATTERN_ORDER);

$prod_desc_prod = $product_desc_tmp[1][0];


//옵션 찾기
$datas = "";
$datas = split("\n",$results);

$option_start_line = "";
$option_end_line = "";
$option2_start_line = "";
$option2_end_line = "";
for($i=0;$i < count($datas);$i++){
	$data = $datas[$i];
	

	if(!$option_start_line && substr_count($data,"<select id=\"swatch-".$collection."\" name=\"swatch\">")){
		$option_start_line = $i;
	}
	
	if(($option_start_line && !$option_end_line) && substr_count($data,"</select>")){
		$option_end_line = $i+1;
	}

	if(!$option2_start_line && substr_count($data,"<select id=\"partNumber-".$collection."\" name=\"partNumber\" class=\"size-select\">")){
		$option2_start_line = $i;
	}
	
	if(($option2_start_line && !$option2_end_line) && substr_count($data,"</select>")){
		$option2_end_line = $i+1;
	}

	
}

$options_str = "";
for($i=$option_start_line;$i < $option_end_line;$i++){
	$options_str .= $datas[$i];
}

$options2_str = "";
for($i=$option2_start_line;$i <= $option2_end_line;$i++){
	$options2_str .= $datas[$i];
}

$color_option_tmp = "";
$_option_tmp = "";
//goods_detail_images_name or option_div
$options_str = str_replace("\\","",$options_str);
preg_match_all("|<option .*>(.*)</option>|U",$options_str,$color_option_tmp, PREG_PATTERN_ORDER);
$_option_tmp = $color_option_tmp[1];

$goods_detail_images_name = "";
for($i=0;$i < count($_option_tmp);$i++){
	$goods_detail_images_name[$i]=str_replace("Select ","",trim($_option_tmp[$i]));
}

if($sale_price > 0){
	$listprice = $price;
	$price = $sale_price;
}

/*
$pname_text = "";
if(trim($pname) == ""){
	for($i=$pname_start_line;$i < $pname_end_line;$i++){
		$pname_text .= $datas[$i];
	}
	//echo $pname_text;
	//exit;
	$pname_text = str_replace(array("<div id = \"longDescDiv\" style=\"margin-bottom:10px\">","</div>"),"",$pname_text);
	$pname = strip_tags($pname_text);
}
*/

if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}

/*
for($i=$prod_desc_start_line;$i < $prod_desc_end_line;$i++){
	$prod_desc_prod .= $datas[$i];
}
*/
//echo $prod_desc_prod;
//exit;

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


$json_url_tmp = "";
$json_url = "";
// 옵션2 컬러별 사이즈
$json_url_tmp=str_replace("ProductDisplay","GetColorJSON",$bs_url);

for($i=0 ;$i < count($detail_images_seq[1]);$i++){
	$json_url[$i]=$json_url_tmp.'&seq='.$detail_images_seq[1][$i];
}

$json_results = "";
$relation_size_tmp = "";
$relation_size = "";
$snoopy = new Snoopy;
for($i=0 ;$i < count($json_url);$i++){

	$snoopy->fetch($json_url[$i]);
	$json_results =  $snoopy->results;

	preg_match_all("|.*\"size\"\:.*\"(.*)\".*\n.*\n.*\n.*\n.*\n.*\"soldOut\"\:.*\"false\"|U",$json_results,$relation_size_tmp, PREG_PATTERN_ORDER);

	$relation_size[$i] = implode("^",$relation_size_tmp[1]);
}

// 옵션s1
$option_key = 0;
$options = "";
if($options_str||$options2_str){

		//thumb_images 
		/*이미지가 하나로 되어있음
		preg_match_all("|<a class=\"swatch-link\".*style=\"background-image\:url\(\'(.*)\'\);\"|U",$results,$thumb_images_tmp, PREG_PATTERN_ORDER);
		$thumb_images_url='http:'.$thumb_images_tmp[1][0];

		for($i=0 ;$i < count($_option_tmp);$i++){
			$thumb_images[$i]='<img style="width:20px; height:10px; font-size:0px; background-image:url('.$thumb_images_url.'); background-position:0 '.-10*$i.'px;" />';
		}
		*/

		//실제로 이것만 사용
		if(array_count_values ($_option_tmp)){
			$options[$option_key][option_type] = "9";
			$options[$option_key][option_name] = "COLOR/SIZE";
			$options[$option_key][option_kind] = "r";
			$options[$option_key][option_use] = "1";
			for($i=0;$i < count($_option_tmp);$i++){
				$options[$option_key][details][$i][option_div] = str_replace("Select ","",trim($_option_tmp[$i]));
				$options[$option_key][details][$i][price] = "";
				$options[$option_key][details][$i][etc1] = "사이즈:".$relation_size[$i];
				//$options[$option_key][details][$i][thumb_images] = $thumb_images[$i];
				$options[$option_key][details][$i][goods_images] = $goods_detail_images[$i];
				
			}
			/*
			$option[$option_key] = "Color";
			for($i=0;$i < count($_option_tmp);$i++){
				$option[$i+1] = str_replace("Select ","",trim($_option_tmp[$i]));
			}
			*/
		

		}else{
			preg_match_all("|<option .*>(.*)</option>|U",$options2_str,$options2_tmp, PREG_PATTERN_ORDER);
			$_option_tmp2 = $options2_tmp[1];

			$options[$option_key][option_type] = "9";
			$options[$option_key][option_name] = $_option_tmp2[0];
			$options[$option_key][option_kind] = "s";
			$options[$option_key][option_use] = "1";
			for($i=1;$i < count($_option_tmp2);$i++){
				$options[$option_key][details][$i-1][option_div] = trim($_option_tmp2[$i]);
				$options[$option_key][details][$i-1][price] = "";
				$options[$option_key][details][$i-1][etc1] = trim($_option_tmp2[$i]);

			}
			/*
			//$option[$option_key] = "Size";
			for($i=0;$i < count($_option_tmp);$i++){
				$option[$i+1] = str_replace("Select ","",trim($_option_tmp[$i]));
			}
			*/
		}
		$option_key++;

}	



//옵션2
/*
if($options2_str){
		$options_str = str_replace("\\","",$options_str);


		preg_match_all("|<option .*>(.*)</option>|U",$options2_str,$options2_tmp, PREG_PATTERN_ORDER);
		
		$_option_tmp2 = $options2_tmp[1];

		if(substr_count($options2_str,"swatch")){
			$options[$option_key][option_type] = "9";
			$options[$option_key][option_name] = "색상";
			$options[$option_key][option_kind] = "r";
			$options[$option_key][option_use] = "1";
			for($i=0;$i < count($_option_tmp2);$i++){
				$options[$option_key][details][$i+1][option_div] = str_replace("Select ","",trim($_option_tmp2[$i]));
				$options[$option_key][details][$i+1][price] = "";
				$options[$option_key][details][$i+1][etc1] = "사이즈:".$relation_size[$i];

			}

		}else if(substr_count($options2_str,"size-select")){
			$options[$option_key][option_type] = "9";
			$options[$option_key][option_name] = "사이즈";
			$options[$option_key][option_kind] = "s";
			$options[$option_key][option_use] = "1";
			for($i=0;$i < count($_option_tmp2)-1;$i++){
				$options[$option_key][details][$i][option_div] = str_replace("Select ","",trim($_option_tmp2[$i+1]));
				$options[$option_key][details][$i][price] = "";
				$options[$option_key][details][$i][etc1] = "";

			}
			
		}else{
			$options[$option_key][option_type] = "9";
			$options[$option_key][option_name] = "옵션";
			$options[$option_key][option_kind] = "s";
			$options[$option_key][option_use] = "1";
			for($i=0;$i < count($_option_tmp2);$i++){
				$options[$option_key][details][$i][option_div] = str_replace("Select ","",trim($_option_tmp2[$i]));
				$options[$option_key][details][$i][price] = "";
				$options[$option_key][details][$i][etc1] = "";

			}

		}
		//사용안함
		if(substr_count($options2_str,"colors_0")){
			$option2[0] = "Color";
			for($i=0;$i < count($_option_tmp2);$i++){
				$option2[$i+1] = str_replace("Select ","",trim($_option_tmp2[$i]));
			}
		}else if(substr_count($options2_str,"sizeDropDowntexts")){
			//$option2[0] = "Size";
			for($i=0;$i < count($_option_tmp2);$i++){
				$option2[$i] = str_replace("Select ","",trim($_option_tmp2[$i]));
			}
		}else{
			for($i=0;$i < count($_option_tmp2);$i++){
				$option2[$i] = str_replace("Select ","",trim($_option_tmp2[$i]));
			}
		}
		
}	

//사용안함
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