<?php


	
	//echo $cookie_nm;
	//실제 로그인이 이루어지는 Curl 입니다.
	//echo $bs_site_domain;

if(!$ch){
$loginUrl = "http://www.mstyleshop.co.kr/login_exec.php";

//이 부분은 접속 계정 등의 post 값입니다.
$login_data = '?Surl=&aUrl=%2Flogin.php%3F&q_type=&kind_id=&kind_1=&kind_2=&kind_3=&kind_4=&kind_value=&page=&list_display=&q_goods_code&user_id=test001&user_pwd=test001'; 

//쿠키 생성 파일 입니다.
//$cookie_nm = "./files/cookie_mstyleshop.txt"; 
$cookie_nm = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_cookies/cookie_".$bs_site."_".session_id().".txt"; 

//echo session_id();

//if(true && !file_exists($cookie_nm)){
//실제 로그인이 이루어지는 Curl 입니다.
$ch = curl_init(); 
curl_setopt ($ch, CURLOPT_URL,$loginUrl);                      // 접속할 URL 주소 
//curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);   // SSL 관련 설정 입니다.
//curl_setopt ($ch, CURLOPT_SSLVERSION,1);                  // 이부분 또한 윗 설정과 같이 SSL 관련 부분입니다.
curl_setopt ($ch, CURLOPT_HEADER, 0);        // 페이지 상단에 헤더값 노출 유뮤 입니다. 0일경우 노출하지 않습니다.
curl_setopt ($ch, CURLOPT_POST, 1);           // 값 전송을 POST값을 전송 하겠다는 선언 입니다. 
curl_setopt($ch, CURLOPT_POSTFIELDS, $login_data);     // 전송할 POST 값입니다.
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);     // 설정 파일에 쿠키 데이터를 굽습니다.
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);    // 설정 파일의 쿠키 데이터를 페이지에 넣습니다.
curl_setopt ($ch, CURLOPT_TIMEOUT, 30); 
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
$result = curl_exec ($ch); 
}
//}
//echo $result;
//exit;
/*
   curl_close 를 하지 않으면 세션을 유지한 상태에서 페이지 이동이 가능 합니다.
*/
curl_setopt ($ch, CURLOPT_POST, 0);
curl_setopt ($ch, CURLOPT_URL,$bs_url);   // 로그인후 이동할 페이지 입니다.
//curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm); 
//curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm); 
$results = curl_exec ($ch); 
//curl_close ($ch); 
//echo "<textarea style='width:400px;height:400px;'>".$result."</textarea>";
$results = str_replace("src=\"./","src=\"http://www.mstyleshop.co.kr/",$results);
$results = str_replace("href=\"./","href=\"http://www.mstyleshop.co.kr/",$results);

//echo $result;

//	echo $results;
//	exit;


/**
 * 필수 사용 변수 초기화 12.05.14 bgh 
 */
unset($listprice);
unset($price);
unset($saleprice);
unset($pname);
unset($pcode);
unset($prod_img_src);
unset($saleprice);
unset($make_company);
unset($option);
unset($options);
unset($prod_desc_prod);
unset($goods_detail_images);
unset($goods_detail_images_tmp2);
unset($goods_detail_images_tmp3);



preg_match_all("|<META NAME=\"Description\" CONTENT=\",(.*)\">|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
$pname = $pname_tmp[1][0];
//print_r($pname_tmp);
//exit;

preg_match_all("|<input type=\"hidden\" name=\"defaultPrice\" value=\"(.*)\">|U",$results,$listprice_tmp, PREG_PATTERN_ORDER);
//print_r($listprice_tmp);
$listprice = str_replace(array(",","원"),"",trim($listprice_tmp[1][0]));

preg_match_all("|<span class=\"selling_oran17b\"><b>(.*)</b></span>|U",$results,$sellprice_tmp, PREG_PATTERN_ORDER);
//print_r($sellprice_tmp);
$price = str_replace(array(",","원"),"",trim($sellprice_tmp[1][0]));
//echo $price;


//echo $listprice;
//exit;
//$listprice = $listprice_tmp[1][0];

preg_match_all("|<input type=hidden name=good_code value='(.*)'>|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
//print_r($pcode_tmp);
$pcode = $pcode_tmp[1][0];

preg_match_all("|<img src=\"(.*)\"  border=\"0\" id=\"gmimg_view\">|U",$results,$prod_img_src_tmp, PREG_PATTERN_ORDER);
//print_r($pcode_tmp);
$prod_img_src = $prod_img_src_tmp[1][0];
$prod_img_src = str_replace("cart_img4","cart_img5",trim($prod_img_src));


preg_match_all("|<td align=\"center\" valign=\"top\" style=\"padding:10px;\"><CENTER>(.*)</CENTER></td>|U",$results,$prod_desc_prod_tmp, PREG_PATTERN_ORDER);
//print_r($prod_desc_prod_tmp);
$prod_desc_prod = str_replace("<!--br-->","\n",$prod_desc_prod_tmp[1][0]);
//echo $prod_desc_prod;

$_results = str_replace("\n","<!--br-->",$results);

preg_match_all("|제조사</b></td>.*<td>(.*)</td>|U",$_results,$make_company_tmp, PREG_PATTERN_ORDER);
$make_company .= str_replace("<!--br-->","\n",$make_company_tmp[1][0]);
//print_r($make_company_tmp);

preg_match_all("|제조국</b></td>.*<td>(.*)</td>|U",$_results,$orgin_tmp, PREG_PATTERN_ORDER);
$orgin .= str_replace("<!--br-->","\n",$orgin_tmp[1][0]);
//print_r($make_company_tmp);

preg_match_all("|<!-- 상품기본정보 S -->(.*)<!-- 상품기본정보 E -->|U",$_results,$prod_desc_prod_tmp, PREG_PATTERN_ORDER);
//print_r($prod_desc_prod_tmp);
$prod_desc_prod .= str_replace("<!--br-->","\n",$prod_desc_prod_tmp[1][0]);



//echo $price;
//exit;
//echo $results;

preg_match_all("|<select name=op1 onChange=\"check_option.*\">(.*)</select>|U",$_results,$option_tmp, PREG_PATTERN_ORDER);
$_option_tmp = str_replace("<!--br-->","\n",$option_tmp[1][0]);
//print_r($_option_tmp);

preg_match_all("|<option value='(.*)'.*>.*</option>|U",$_option_tmp,$option_detail_tmp, PREG_PATTERN_ORDER);
//print_r($option_detail_tmp);



//echo "<td col=\"(.*)\" cap=\".*\(".$product_color."\).*\" status=\"In stock\".*>";
//preg_match_all("|<td col=\"(.*)\" cap=\".*\(".$product_color."\).*\" status=\"In stock\">|U",$results,$size_stock_status_tmp, PREG_PATTERN_ORDER);
/*
preg_match_all("|<img width=\"14\" height=\"14\" src=\"(.*)\".*alt=\"(.*)\" .*>|U",str_replace("\n", " ",$results),$color_tmp, PREG_PATTERN_ORDER);
//print_r($color_tmp);


preg_match_all("|<td col=\"(.*)\" cap=\"(.*)\" status=\"(.*)\" .*>|U",$results,$size_stock_status_tmp, PREG_PATTERN_ORDER);
//print_r($size_stock_status_tmp);//[In stock|Low stock]
*/
//print_r($size_tmp);
//$option = str_replace(array("\t"," ","$"),"",trim($price_tmp[1][0]));
$option_key = 0;
$stock_bool = false;
$soldout_message = "재고부족";
$options[$option_key][option_type] = "9";
$options[$option_key][option_name] = "색상";
$options[$option_key][option_kind] = "r";
$options[$option_key][option_use] = "1";

for($i=1;$i < count($option_detail_tmp[1]);$i++){
	$_option_detail = explode("|",$option_detail_tmp[1][$i]);
	
	$options[$option_key][details][($i-1)][option_code] = trim($_option_detail[0]);
	$options[$option_key][details][($i-1)][option_div] = trim($_option_detail[4]);
	$options[$option_key][details][($i-1)][price] = $price + $_option_detail[1];
	$options[$option_key][details][($i-1)][option_stock] = trim($_option_detail[3]);
	if($_option_detail[3] > 0){
		$stock_bool = true;
		$soldout_message = "";
	}

}

//echo $price;
//print_r($options);
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