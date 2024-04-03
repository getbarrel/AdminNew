<?php

$results = "";
$soldout_message = "";

if(false){
	$snoopy = new Snoopy;
	$snoopy->agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)";
	$snoopy->referer = "http://www.jcrew.com/";

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
	$snoopy->fetch($bs_url);
	$results =  $snoopy->results;

}else{
	//session_destroy();

	$cookie_nm = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_cookies/cookie_".$bs_site."_".session_id().".txt"; 
	if(true){
		$ch = curl_init(); 
		curl_setopt ($ch, CURLOPT_URL,$bs_url);                      // 접속할 URL 주소 intl/context_chooser.jsp?bmUID=1333009462226&bmLocale=en_US&JCREW_CONTEXT<>country=US&JCREW_CONTEXT<>currency=USD
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);   // SSL 관련 설정 입니다.
		curl_setopt ($ch, CURLOPT_SSLVERSION,1);                  // 이부분 또한 윗 설정과 같이 SSL 관련 부분입니다.
		curl_setopt ($ch, CURLOPT_HEADER, 0);        // 페이지 상단에 헤더값 노출 유뮤 입니다. 0일경우 노출하지 않습니다.
		curl_setopt ($ch, CURLOPT_POST, 1);           // 값 전송을 POST값을 전송 하겠다는 선언 입니다. 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $login_data);     // 전송할 POST 값입니다.
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);     // 설정 파일에 쿠키 데이터를 굽습니다.
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);    // 설정 파일의 쿠키 데이터를 페이지에 넣습니다.
		//curl_setopt( $ch, CURLOPT_COOKIE, "jcrew_country=US;" ); 
		curl_setopt ($ch, CURLOPT_TIMEOUT, 30); 
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
		$results = curl_exec ($ch); 

		curl_close ($ch); 
	}else{
		$main_submission=array("get"=>"$bs_url","pfserverDropdown"=>"https://tx.proxfree.com/request.php?do=go","allowCookies"=>"on","pfipDropdown"=>"default");


		 $ch = curl_init();
		 //curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);   // SSL 관련 설정 입니다.
		 //curl_setopt ($ch, CURLOPT_SSLVERSION,1);                  // 이부분 또한 윗 설정과 같이 SSL 관련 부분입니다.
		 curl_setopt($ch, CURLOPT_TIMEOUT, 10); 
		 curl_setopt($ch, CURLOPT_HEADER, 0); 
		 curl_setopt($ch, CURLOPT_POST, 1);           // 값 전송을 POST값을 전송 하겠다는 선언 입니다. 
		 curl_setopt($ch, CURLOPT_POSTFIELDS, $main_submission);     // 전송할 POST 값입니다.
		 curl_setopt($ch, CURLOPT_URL,"https://tx.proxfree.com/request.php?do=go"); 
		 curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);     // 설정 파일에 쿠키 데이터를 굽습니다.
		 curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);    // 설정 파일의 쿠키 데이터를 페이지에 넣습니다.
		 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
		 curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		$results = curl_exec ($ch); 

		curl_close ($ch); 
	}	

}
	//echo $results;
	//exit;

unset($soldout_message);
preg_match_all("|it has sold out|U",$results,$_soldout_text, PREG_PATTERN_ORDER);
if($_soldout_text[0][0] != ""){
	$soldout_message = $_soldout_text[0][0];
}

$pname_array = "";
$pname = "";
preg_match_all("|<h1 class=\"prodtitle\">(.*)</h1>|U",$results,$pname_array, PREG_PATTERN_ORDER);
$pname = $pname_array[1][0];
//print_r($pname_array);
if($pname == ""){
	preg_match_all("|<meta property=\"og:title\" content=\"(.*)\"/>|U",$results,$pname_array, PREG_PATTERN_ORDER);
	$pname = $pname_array[1][0];
}


$price_tmp = "";
$price = "";

preg_match_all("|<td .*class=\"chips-price-original\">(.*)</td>|U",$results,$price_tmp, PREG_PATTERN_ORDER);

if($price == "" || $price == "0.00"){
	//print_r($price_tmp);
	$price = str_replace("$","",$price_tmp[1][0]);
}

preg_match_all("|<td .*class=\"chips-price\">(.*)</td>|U",$results,$price_tmp, PREG_PATTERN_ORDER);

if($price == "" || $price == "0.00"){
	//print_r($price_tmp);
	$price = str_replace(array("$","now"," "),"",$price_tmp[1][0]);
}

preg_match_all("|<span class=\"price-single\">(.*)</span>|U",$results,$price_tmp, PREG_PATTERN_ORDER);
//$price = str_replace("$","",$price_tmp[1][0]);
if($price == "" || $price == "0.00"){
	//print_r($price_tmp);
	$price = str_replace(array("$","was"),"",$price_tmp[1][0]);
}

preg_match_all("|<span class=\"price\">(.*)</span>|U",$results,$price_tmp, PREG_PATTERN_ORDER);
//$price = str_replace("$","",$price_tmp[1][0]);
if($price == "" || $price == "0.00"){
	//print_r($price_tmp);
	$price = str_replace(array("$","was"),"",$price_tmp[1][0]);
}

preg_match_all("|lpAddVars\('page','ProductValue','(.*)'\);|U",$results,$price_tmp, PREG_PATTERN_ORDER);
//print_r($price_tmp);
//$price = str_replace("$","",$price_tmp[1][0]);
if($price == "" || $price == "0.00"){
	//echo str_replace(array(" ","\r", "\n","\t"),"",$results);
	preg_match_all("|>\$(.*)Item|U",str_replace(array(" ","\r", "\n","\t"),"",$results),$price_tmp, PREG_PATTERN_ORDER);
	//print_r($price_tmp);
	$price = str_replace("$","",$price_tmp[1][0]);
}

$pcode_tmp = "";
$pcode = "";
$__bs_url = "";
$_pcode = "";
preg_match_all("|<span class=\"itemid-single\">item (.*)</span>|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);

$pcode = str_replace("$","",$pcode_tmp[1][0]);

if($pcode == ""){
	$__bs_url = split("[/]",$bs_url);
	$_pcode = split("~",$__bs_url[count($__bs_url)-2]);
	$pcode = $_pcode[1];
}

$prod_img = "";
$prod_img_src = "";
preg_match_all("|<img src=\"(.*)\" name=\"productOnFigureImage.*\".*>|U",$results,$prod_img, PREG_PATTERN_ORDER);

$prod_img_src = str_replace("&amp;","&",$prod_img[1][0]);


if($prod_img_src == ""){
	preg_match_all("|<img name=\"productOnFigureImage.*\" id=\"productOnFigureImage.*\" src=\"(.*)\" alt=\".*\" .*>|U",$results,$prod_img, PREG_PATTERN_ORDER);

	$prod_img_src = str_replace("&amp;","&",$prod_img[1][0]);
}

$goods_detail_images = "";
$goods_detail_images_tmp = "";
preg_match_all("|\"changeViewImg\('(.*)'\);|U",$results,$goods_detail_images_tmp, PREG_PATTERN_ORDER);

for($i=0;$i < count($goods_detail_images_tmp[1]);$i++){

	$goods_detail_images[$i] = str_replace("height=75","height=393",str_replace("width=75","width=393",$goods_detail_images_tmp[1][$i]));
	if($prod_img_src == ""){
        if(substr_count(" ".$prod_img_src,"http://") == 0 && $i == 0){
    		$prod_img_src = $goods_detail_images[$i];
    	}
    }
}

$goods_other_color_images_tmp = "";
preg_match_all("|;changeViewImg\('(.*)'\);|U",$results,$goods_other_color_images_tmp, PREG_PATTERN_ORDER);

for($i=0;$i < count($goods_other_color_images_tmp[1]);$i++){

	$goods_other_color_images[$i] = str_replace("height=75","height=393",str_replace("width=75","width=393",$goods_other_color_images_tmp[1][$i]));
	if($prod_img_src == ""){
        if(substr_count(" ".$prod_img_src,"http://") == 0 && $i == 0){
    		$prod_img_src = $goods_other_color_images[$i];
    	}
    }
}


//print_r($goods_detail_images_tmp);
//print_r($goods_other_color_images);
$prod_desc_tmp = "";
$prod_desc_prod = "";
preg_match_all("|<meta name=\"description\" content=\"(.*)\" />|U",$results,$prod_desc_tmp, PREG_PATTERN_ORDER);
$prod_desc_prod = $prod_desc_tmp[1][0];
//print_r($prod_desc_tmp);
//exit;



$datas = "";
$datas = split("\n",$results);

$option_start_line = "";
$option_end_line = "";
$option2_start_line = "";
$option2_end_line = "";
for($i=0;$i < count($datas);$i++){
	$data = $datas[$i];
	if(!$pname && substr_count($data,"pdp-title")){		
		$pname = strip_tags($datas[$i+1]);
	}
	
	if(!$option_start_line && (substr_count($data,"<select class=\"standard_nopad\" name=\"ADD_CART_ITEM<>ATR_size\"") || substr_count($data,"<select name=\"ADD_CART_ITEM<>ATR_size\""))){
		$option_start_line = $i;
	}
	
	if(($option_start_line && !$option_end_line) && substr_count($data,"</select>")){
		$option_end_line = $i+1;
		//echo $i."<br><br>".$data."<br><br>";
	}
	
	if(!$option2_start_line && (substr_count($data,"<select class=\"standard_nopad\" name=\"ADD_CART_ITEM<>ATR_color\"") || substr_count($data,"<select class=\"dropdown-wide\" name=\"ADD_CART_ITEM<>ATR_color\""))){
		$option2_start_line = $i;
	}
	
	if(($option2_start_line && !$option2_end_line) && substr_count($data,"</select>")){
		$option2_end_line = $i+1;
		//echo $i."<br><br>".$data."<br><br>";
	}

}

$options_str = "";
for($i=$option_start_line;$i < $option_end_line;$i++){
	$options_str .= $datas[$i];	
}
//echo $options_str;
//exit;
//echo iconv("CP949","UTF-8",$options_str);
$options2_str = "";
for($i=$option2_start_line;$i < $option2_end_line;$i++){
	$options2_str .= $datas[$i];	
}

$option1_spos = "";
$option1_epos = "";
$options1_str = "";
$options = "";
$_option = "";
$option = "";
if($options_str){
		$options_str = str_replace("\\","",$options_str);
		//echo strrpos($options_str,"<select  id=\"custom1\"")."::::::::::".strpos($options_str,"</select>");
		$option1_spos = strrpos($options_str,"<select name=\"size\"");
		$option1_epos = strpos($options_str,"</select>");
		$options1_str = substr($options_str,$option1_spos,$option1_epos+1);
		//$options_str = substr(<select  id="custom1",$options_str
		//preg_match_all("|<img .*src=\"(.*)\".*>|U",$prod_img_text,$img_out, PREG_PATTERN_ORDER);
		//echo $options1_str;
		preg_match_all("|option .*>(.*)<|U",$options1_str,$options, PREG_PATTERN_ORDER);
		//print_r($options);
		//exit;
		$_option = $options[1];
		
		
		for($i=0, $j=0;$i < count($_option);$i++){
				$option[$i] = str_replace("&nbsp;","",trim($_option[$i]));
		}
		//print_r($option);
}

$options2 = "";
$_option2 = "";
$option2 = "";
if($options2_str){

		
		//$options2_str = substr($options_str,$option1_epos+9,strlen($options_str));
		//echo $options2_str;
		preg_match_all("|<option .*>(.*)</option>|U",$options2_str,$options2, PREG_PATTERN_ORDER);
		//print_r($options2);
		$_option2 = $options2[1];
		
		for($i=0;$i < count($_option2);$i++){
			$option2[$i] = str_replace("Select ","",trim($_option2[$i]));
		}
		//print_r($option2);
}	
//changeViewImg
//
//print_r($goods_detail_images);

if((count($goods_detail_images) > 0) || (count($goods_other_color_images) > 0)){
	$_prod_desc = "<table align=center>";
	$_prod_desc .= "<tr><td align=center><div style='text-align:left;'>".$prod_desc_prod."</div></td></tr>";
	for($i=0;$i < count($goods_detail_images);$i++){
		$_prod_desc .= "<tr><td align=center style='padding:20px 0px;'><IMG src=\"".$goods_detail_images[$i]."\"></td></tr>";
	}
    for($i=0;$i < count($goods_other_color_images);$i++){
        $_prod_desc .= "<tr><td align=center style='padding:0px 0px 0px 0px;'><b>".$option2[$i+1]."</b></td></tr>";
        $_prod_desc .= "<tr><td align=center style='padding:20px 0px;'><IMG src=\"".$goods_other_color_images[$i]."\"></td></tr>";
    }
	$_prod_desc .= "</table>";

	$prod_desc_prod = $_prod_desc;
}

$phantom_result = shell_exec("phantomjs  --load-images=no ".$_SERVER["DOCUMENT_ROOT"]."/admin/scrape/jcrew.js '".$bs_url."'"); 
//print_r($phantom_result);
if(!empty($phantom_result)){
    $stock_info = (array)json_decode($phantom_result);
    print_r($stock_info);
}

if($soldout_message || $price == "" || $price == "0.00" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}


$goods_desc_copy = 1;

$pcode = $bs_site."_".$pcode;
?>

