<?

//$bs_url="http://www.ae.com/77kids/browse/product.jsp?catId=cat2090067&productId=K6790_K7855";
//$bs_url="http://www.ae.com/77kids/browse/product.jsp?catId=cat2100133&productId=K6327_K2721";

//$main_submission=array("get"=>"$bs_url","pfserverDropdown"=>"https://tx.proxfree.com/request.php?do=go","allowCookies"=>"on","pfipDropdown"=>"default");


 $ch = curl_init();
 //curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);   // SSL 관련 설정 입니다.
 //curl_setopt ($ch, CURLOPT_SSLVERSION,1);                  // 이부분 또한 윗 설정과 같이 SSL 관련 부분입니다.
 curl_setopt($ch, CURLOPT_TIMEOUT, 10); 
 curl_setopt($ch, CURLOPT_HEADER, 0); 
 curl_setopt($ch, CURLOPT_POST, 1);           // 값 전송을 POST값을 전송 하겠다는 선언 입니다. 
 //curl_setopt($ch, CURLOPT_POSTFIELDS, $main_submission);     // 전송할 POST 값입니다.
 //curl_setopt($ch, CURLOPT_URL,"https://tx.proxfree.com/request.php?do=go");
 curl_setopt($ch, CURLOPT_URL,$bs_url);
 curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);     // 설정 파일에 쿠키 데이터를 굽습니다.
 curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);    // 설정 파일의 쿠키 데이터를 페이지에 넣습니다.
 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
 curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

/*
 if ($proxy_ip) { //프록시사용시
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, TRUE);
  curl_setopt($ch, CURLOPT_PROXY, $proxy_ip);
 }
*/
$results = "";
$soldout_message = "";

$results = curl_exec ($ch); 

curl_close ($ch); 
//print_r($results);
//exit;
$result = "";
$result=str_replace("\n","",$results);


//상품코드
$pcode_tmp = "";
$expcode = "";
$pcode = "";
preg_match_all("|name=\"productId\" value=\"(.*)\"|U",$result,$pcode_tmp, PREG_PATTERN_ORDER);
$expcode = $pcode_tmp[1][0];
$pcode = $bs_site."_".$expcode;

//가격
$price_tmp = "";
$price = "";
$sale_price = "";
$sale_price_tmp = "";
$list_price_tmp = "";
preg_match_all("|<span id=\"pricesContainer".$expcode."\">(.*)</span>|U",$result,$price_tmp, PREG_PATTERN_ORDER);
//print_r($price_tmp);
if(substr_count($price_tmp[1][0],"$") == 1 ){
    $price = str_replace("$","",$price_tmp[1][0]);
    
}else{
    preg_match_all("|<ins><strong>(.*)</strong></ins>|U",$result,$sale_price_tmp, PREG_PATTERN_ORDER);    
    $sale_price = str_replace("$","",$sale_price_tmp[1][0]);
     
    preg_match_all("|<strike><strong>(.*)</strong></strike>|U",$result,$list_price_tmp, PREG_PATTERN_ORDER);
    $price = str_replace("$","",$list_price_tmp[1][0]);
}

//print_r($list_price_tmp);
if(!empty($sale_price)){
	$listprice = $price;
	$price = $sale_price;
}

//상품이름
$pname_tmp = "";
$pname = "";
preg_match_all("| <meta name=\"DC.title\" content=\"(.*)\" />|U",$result,$pname_tmp, PREG_PATTERN_ORDER);
$pname = $pname_tmp[1][0];

//상품 설명
$prod_desc_tmp = "";
$prod_desc_prod = "";
preg_match_all("|<div class=\"addlEquity\">(.*)</div></div>|U",$result,$prod_desc_tmp, PREG_PATTERN_ORDER);
$prod_desc_prod = $prod_desc_tmp[1][0];
$prod_desc_prod = str_replace("&bull;","",$prod_desc_prod);
//$prod_desc_prod = strip_tags($prod_desc_prod,"<span><style><table><tr><td><li><img>");


//기본 이미지
$image_tmp = "";
preg_match_all("|loadTumbnailImage\('.*', '(.*)'\)|U",$results,$image_tmp, PREG_PATTERN_ORDER);
if(substr($image_tmp[1][0],0,5) != "http:"){
	$image_tmp[1][0] = "http://www.77kids.com/".$image_tmp[1][0];
}
$prod_img_src = $image_tmp[1][0];

//카테고리아이디
$cateid = "";
preg_match_all("|&catId=(.*)&|U",$results,$cateid_tmp, PREG_PATTERN_ORDER);
$cateid = $cateid_tmp[1][0];

//다른색상 상품이미지
$_color_url = "http://www.77kids.com/77kids/browse/gadgets/productColorSelection.jsp?productId=".$expcode."&catId=".$cateid;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$_color_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
$_color_result = curl_exec ($ch); 
curl_close ($ch);
//print_r($_color_result); 
preg_match_all("|<img .* src=\"(.*)\".*alt=\"(.*)\"|U",$_color_result,$images_name_tmp, PREG_PATTERN_ORDER);
//print_r($images_name_tmp); //array[1] = img , array[2] = name
$goods_detail_images = "";
for($i=0 ;$i < count($images_name_tmp[1]);$i++){
	$goods_detail_images[$i] = "http://www.77kids.com".str_replace("swatch","large",$images_name_tmp[1][$i]);
    $goods_detail_images_name[$i] = $images_name_tmp[2][$i];
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

//옵션
$options = "";
$option_key = 0;
if(!empty($images_name_tmp[2])){
    $options[$option_key][option_type] = "9";
    $options[$option_key][option_name] = "OPTION";
    $options[$option_key][option_kind] = "b";
    $options[$option_key][option_use] = "1";
    
    $j = 0;
    for($i=0;$i < count($images_name_tmp[2]);$i++){
        $name = urlencode($images_name_tmp[2][$i]);
        //옵션(재고)
        $_stock_url = "http://www.77kids.com/77kids/browse/gadgets/skuSizeSelection.jsp?productId=".$expcode."&selectedColor=".$name."&catId=".$cateid;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$_stock_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
        $_stock_result = curl_exec ($ch); 
        curl_close ($ch);
        
        //가격
        $_price_url = "http://www.77kids.com/77kids/browse/gadgets/productPriceInfo.jsp?productId=".$expcode."&selectedColor=".$name;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$_price_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
        $_price_result = curl_exec ($ch); 
        curl_close ($ch);
        
        preg_match_all("|<option value=\"(.*)\">.*</option>|U",$_stock_result,$stock_tmp, PREG_PATTERN_ORDER);
        
        foreach($stock_tmp[1] as $st):
            if(!empty($st)){
            	$options[$option_key][details][$j][option_div] = $images_name_tmp[2][$i]." / ".$st;
                $options[$option_key][details][$j][price] = $_price_result;
                $j++;
            }
         endforeach;
        
    }
}
//print_r($options);
//exit;
$option = "";


// 재고 일시코드
if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}

$goods_desc_copy = 1;

?>