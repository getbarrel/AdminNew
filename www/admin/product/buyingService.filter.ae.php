<?

//$bs_url="http://www.ae.com/77kids/browse/product.jsp?catId=cat2090067&productId=K6790_K7855";
//$bs_url="http://www.ae.com/77kids/browse/product.jsp?catId=cat2100133&productId=K6327_K2721";

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

$result = "";
$result=str_replace("\n","",$results);


//상품코드
$pcode_tmp = "";
$expcode = "";
$pcode = "";
preg_match_all("|\"productId\":\"(.*)\"|U",$result,$pcode_tmp, PREG_PATTERN_ORDER);
$expcode = $pcode_tmp[1][0];
$pcode = $bs_site."_".$expcode;

//가격
$price_tmp = "";
$price = "";
$sale_price = "";
preg_match_all("|<div class=\"listPrice\">(.*)</div>|U",$result,$price_tmp, PREG_PATTERN_ORDER);

	if(count($price_tmp[1])){
		$price = str_replace("$","",$price_tmp[1][0]);
		preg_match_all("|<div class=\"price salePrice js_toPrice\">(.*)</div>|U",$result,$price_tmp, PREG_PATTERN_ORDER);
		$sale_price = str_replace("$","",$price_tmp[1][0]);
	}else{
		preg_match_all("|<div class=\"price js_toPrice\">(.*)</div>|U",$result,$price_tmp, PREG_PATTERN_ORDER);
		$price = str_replace("$","",$price_tmp[1][0]);
	}

if($sale_price > 0){
	$listprice = $price;
	$price = $sale_price;
}

//상품이름
$pname_tmp = "";
$pname = "";
preg_match_all("|<h1 class=\"pName\">(.*)</h1>|U",$result,$pname_tmp, PREG_PATTERN_ORDER);
$pname = $pname_tmp[1][0];

//상품 설명
$prod_desc_tmp = "";
$prod_desc_prod = "";
preg_match_all("|<div class=\"addlEquity\">(.*)</div></div>|U",$result,$prod_desc_tmp, PREG_PATTERN_ORDER);
$prod_desc_prod = $prod_desc_tmp[1][0];
$prod_desc_prod = str_replace("&bull;","",$prod_desc_prod);
//$prod_desc_prod = strip_tags($prod_desc_prod,"<span><style><table><tr><td><li><img>");


//기본 이미지
$images_tmp = "";
preg_match_all("|\"productName\"\:\"$pname\"(.*)\"showColor\"|U",$results,$images_tmp, PREG_PATTERN_ORDER);

if(!$images_tmp[1][0]){
	preg_match_all("|\"classStyle\"\:\"$expcode\"(.*)\"showColor\"|U",$results,$images_tmp, PREG_PATTERN_ORDER);
}
if(!$images_tmp[1][0]){
	preg_match_all("|\"productId\"\:\"$expcode\"(.*)\"showColor\"|U",$results,$images_tmp, PREG_PATTERN_ORDER);
}

$_images_tmp1 = "";
preg_match_all("|,\"imgLinks\"\:\[\"(.*)\"\],|U",$images_tmp[1][0],$_images_tmp1, PREG_PATTERN_ORDER);

$_images_tmp2 = "";
$_images_tmp3 = "";
$prod_img = "";
$z=0;
for($i=0; $i < count($_images_tmp1[1]); $i++){

	$_images_tmp2[$i]=explode('","',$_images_tmp1[1][$i]);

	for($j=0; $j < count($_images_tmp2[$i]); $j++){
	
		$_images_tmp3[$z]=$_images_tmp2[$i][$j];

		if(substr($_images_tmp3[$z],0,5) != "http:"){
				$prod_img[$z] = "http:".$_images_tmp3[$z];
		}

		$z++;
	}
}


/*
preg_match_all("|\"imgViews\"\:\[\"(.*)\"\],|U",$images_tmp[1][0],$_images_tmp2, PREG_PATTERN_ORDER);

for($i=0; $i < count($_images_tmp1[1]); $i++){

	$prod_imgs[$i]='http://pics.ae.com/is/image/aeo/'.$_images_tmp1[1][$i];
}


for($i=0; $i < count($_images_tmp2[1]); $i++){
	$_images_tmp3[$i]=$_images_tmp2[1][$i];
	$_images_tmp3[$i]=str_replace("\"","",$_images_tmp3[$i]);
	$_images_tmp3[$i]=explode(',',$_images_tmp3[$i]);
}

$z=0;
for($i=0; $i < count($_images_tmp1[1]); $i++){
	for($j=0; $j < count($_images_tmp3[$i]); $j++){
		$prod_img[$z]=$prod_imgs[$i].$_images_tmp3[$i][$j];
		$z++;
	}	
}
*/

$prod_img_src = "";
$prod_img_src=$prod_img[0];


//다른색상 상품이미지
$goods_detail_images = "";
for($i=0 ;$i < count($prod_img);$i++){
	$goods_detail_images[$i] = $prod_img[$i];
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


//옵션1
$options = "";
$option1_spos = "";
$option = "";
preg_match_all("|{\"name\"\:\"(.*)\",\"id\".*\"colorAvailable\"\:true|U",$images_tmp[1][0],$options, PREG_PATTERN_ORDER);
//$option[0] = "COLOR";
for($i=0; $i < count($options[1]) ; $i++){
	$option1_spos[$i] = strrpos($options[1][$i],"\"");
	$option[$i]= substr($options[1][$i],$option1_spos[$i]+1);
}
//echo "#option1-";
//print_r($option);
//exit;


// 옵션2
$options = "";
$option2 = "";
preg_match_all("|\"availableProductSizes\"\:.*\:\[{\"(.*)\"\}\],\"|U",$images_tmp[1][0],$options, PREG_PATTERN_ORDER);

$options = split("\"\},\{\"",$options[1][0]);

//$option2[0] = "SIZE";
for($i=0; $i < count($options) ; $i++){
	$option2[$i]=str_replace('name":"',"",$options[$i]);
}
	
//echo "#option2-";
//print_r($option2);


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


$options = "";
$option_key = 0;

if(is_array($option)){


		//앞면 이미지들만
		
		for($i=0;$i < count($goods_detail_images) ;$i++){

			$goods_detail_images[$i]=substr($goods_detail_images[$i],0,-2);

			if(substr($goods_detail_images[$i],-1)=="_"){

				$goods_detail_images[$i]=substr($goods_detail_images[$i],0,-1);
			}
		}

		$goods_detail_images=array_values(array_unique($goods_detail_images));
		//Thumb 이미지 or goods_images
		$thumb_images = "";
		for($i=0;$i < count($goods_detail_images) ; $i++){
			$thumb_images[$i]=$goods_detail_images[$i].'_s?wid=28&size=30,30&fit=crop&qlt=70,0';
			$goods_detail_images[$i]=$goods_detail_images[$i].'_f';
		}

		//컬러별 사이즈
		if(count($thumb_images) > 0){
			$option_size_tmp = "";
			$result_relation_size = "";
			$relation_size_ex = "";
			$relation_size_tmp = "";
			$relation_size = "";
			preg_match_all("|\"colorIndex\".*\"colorAvailable\"\:true,(.*)\"name2\"|U",$images_tmp[1][0],$option_size_tmp, PREG_PATTERN_ORDER);
			
			for($i=0; $i < count($thumb_images); $i++){
			
				$result_relation_size=$option_size_tmp[1][$i];

				for($j=0; $j < count($option2); $j++){

					preg_match_all("|\"name\"\:\"$option2[$j]\"(.*)\"available\"\:true|U",$result_relation_size,$relation_size_ex, PREG_PATTERN_ORDER);
												
					//옷, 안경
					if(strpos($relation_size_ex[1][0],"sku")){
				
						if(strpos($relation_size_ex[1][0],"id")){
							$relation_size_tmp[$i][$j]=$option2[$j];
				
						}
					//신발
					}else{
						preg_match_all("|\{\"name\"\:\"(.*)\"\}|U",$result_relation_size,$relation_size_ex, PREG_PATTERN_ORDER);

						if($relation_size_ex[1][$j]==$option2[$j]){
							$relation_size_tmp[$i][$j]=$option2[$j];
						}
					}
					
				}
				
				$relation_size[$i] = implode("^",array_values($relation_size_tmp[$i]));
			}
		}
			
		if(is_array($option)){
			$options[$option_key][option_type] = "9";
			$options[$option_key][option_name] = "COLOR/SIZE";
			$options[$option_key][option_kind] = "r";
			$options[$option_key][option_use] = "1";

			for($i=0;$i < count($option);$i++){
				$options[$option_key][details][$i][option_div] = $option[$i];
				$options[$option_key][details][$i][price] = "";
				$options[$option_key][details][$i][etc1] = "사이즈:".$relation_size[$i];
				$options[$option_key][details][$i][thumb_images] = $thumb_images[$i];
				$options[$option_key][details][$i][goods_images] = $goods_detail_images[$i];
				
			}

		}
		/*
		else{

			if(is_array($option)){
				$options[0][option_type] = "9";
				$options[0][option_name] = $option[0];
				$options[0][option_kind] = "s";
				$options[0][option_use] = "1";
				
				for($i=1;$i < count($option);$i++){
					$options[0][details][$i-1][option_div] = $option[$i];
					$options[0][details][$i-1][price] = "";
					$options[0][details][$i-1][etc1] = $option[$i];
									
				}
			}

			if(is_array($option2)){
				$options[1][option_type] = "9";
				$options[1][option_name] = $option2[0];
				$options[1][option_kind] = "s";
				$options[1][option_use] = "1";
				
				for($i=1;$i < count($option2);$i++){
					$options[1][details][$i-1][option_div] = $option2[$i];
					$options[1][details][$i-1][price] = "";
					$options[1][details][$i-1][etc1] = $option2[$i];
								
				}
			}

		}

		$option_key++;
		*/
}

 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//print_r($options);
//exit;
$option = "";
$option2 = "";

// 재고 일시코드
if($soldout_message || $price == "" ){
	$price = "";
	$stock_bool = false;
}else{
	$stock_bool = true;
}

$goods_desc_copy = 1;

?>