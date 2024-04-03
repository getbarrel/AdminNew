<?

$results = "";
$soldout_message = "";

$loginUrl = "http://danharoo.com/exec/front/Member/login/";

//이 부분은 접속 계정 등의 post 값입니다.

$login_data = "returnUrl=http://danharoo.com/&forbidIpUrl=index.html&certificationUrl=/intro/adult_certification.html?returnUrl=&sIsSnsCheckid=&sProvider=&member_id=forbizkorea&member_passwd=shin0606";

//쿠키 생성 파일 입니다.
$cookie_nm = "./files/cookie.txt";

//실제 로그인이 이루어지는 Curl 입니다.
$ch = curl_init();
curl_setopt ($ch, CURLOPT_URL,$loginUrl);                      // 접속할 URL 주소
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);   // SSL 관련 설정 입니다.
curl_setopt ($ch, CURLOPT_SSLVERSION,1);                  // 이부분 또한 윗 설정과 같이 SSL 관련 부분입니다.
curl_setopt ($ch, CURLOPT_HEADER, 1);        // 페이지 상단에 헤더값 노출 유뮤 입니다. 0일경우 노출하지 않습니다.
curl_setopt ($ch, CURLOPT_POST, 1);           // 값 전송을 POST값을 전송 하겠다는 선언 입니다.
curl_setopt($ch, CURLOPT_POSTFIELDS, $login_data);     // 전송할 POST 값입니다.
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);     // 설정 파일에 쿠키 데이터를 굽습니다.
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);    // 설정 파일의 쿠키 데이터를 페이지에 넣습니다.
curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec ($ch);

/*
   curl_close 를 하지 않으면 세션을 유지한 상태에서 페이지 이동이 가능 합니다.
*/
//exit;
curl_setopt ($ch, CURLOPT_URL,$bs_url);   // 로그인후 이동할 페이지 입니다.
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);
$results = curl_exec ($ch);
//curl_close ($ch);
//echo $results;

//$results = iconv("euc-kr","utf-8",$results);


/**
 * 변수명
 *
 * $pcode = 상품코드(사이트명_코드)
 *
 * $listprice = 원래상품가격
 *
 * $price = 현재상품가격
 *
 * $pname = 상품명
 *
 * $prod_img_src = 상품 이미지
 *
 * $$goods_detail_images = 상품 상세 이미지
 *
 *
 * *************** 특이사항 *****************
 *
 *
 * id 나 name 없이 태그만 있는 값이 많아서 이슈발생 가능성 높음
 *
 */


//curl로 받은 데이터 줄바꿈
//$datas = split("\n",$results);
//print_r($results);

//상품코드
$pcode_tmp = "";
$pcode = "";
preg_match_all("|var iProductNo = '(.*)';|U",$results,$pcode_tmp, PREG_PATTERN_ORDER);
$pcode = $bs_site."_".$pcode_tmp[1][0];


//할인적용가(or 비할인시 판매가)
$price_tmp = "";
$price = "";
//<meta property="product:sale_price:amount" content="240000" />
preg_match_all("|<meta property=\"product\:sale_price\:amount\" content=\"(.*)\".*>|U",$results,$price_tmp, PREG_PATTERN_ORDER);
//print_r($price_tmp);
$price = str_replace(",","",$price_tmp[1][0]);
$sellprice = $price;
//echo "할인가(판매가)".$price;

//상품명
$pname_tmp = "";
$pname = "";
//<meta property="og:title" content="UTO-FB15 g-1 flight jacket[brown(UNISEX)]" />
preg_match_all("|<meta property=\"og\:title\" content=\"(.*)\".*>|U",$results,$pname_tmp, PREG_PATTERN_ORDER);
//print_r($pname_tmp);
$pname = strip_tags($pname_tmp[1][0]);

$shotinfo_tmp = "";
$shotinfo = "";
//<meta property="og:title" content="UTO-FB15 g-1 flight jacket[brown(UNISEX)]" />
preg_match_all("|<meta property=\"og\:description\" content=\"(.*)\".*>|U",$results,$shotinfo_tmp, PREG_PATTERN_ORDER);
//print_r($shotinfo_tmp);
$shotinfo = strip_tags($shotinfo_tmp[1][0]);

//기본 이미지
$images_tmp = "";
$prod_img_src = "";
//preg_match_all("|<div><img src=\"(.*)\".*alt=\"\"/></div>|U",$results,$images_tmp, PREG_PATTERN_ORDER);
preg_match_all("|<meta property=\"og\:image\" content=\"(.*)\".*>|U",$results,$images_tmp, PREG_PATTERN_ORDER);
$prod_img_src = $images_tmp[1][0];

if(substr($prod_img_src,0,5) != "http:"){
    $prod_img_src = "http:".$prod_img_src;
}

preg_match_all("|var option_name_mapper = '(.*)';|U",$results,$option_title_tmp, PREG_PATTERN_ORDER);
$option_title = $option_title_tmp[1][0];

preg_match_all("|var option_stock_data = '(.*)';|U",$results,$option_datas_tmp, PREG_PATTERN_ORDER);
$option_datas = json_decode(str_replace("\\","",$option_datas_tmp[1][0]), true);
//echo "<pre>";
//print_r($option_datas);
//exit;
$datas = "";
$datas = split("\n",$results);

//옵션의 위치를 찾기위한 루프
$option_start_line = "";
$option_end_line = "";
$option2_start_line = "";
$option2_end_line = "";
for($i=0;$i < count($datas);$i++){
    $data = $datas[$i];

    if(!$option_start_line && substr_count($data,"option_title=\"SIZE\"")){
        $option_start_line = $i;
    }

    if(($option_start_line && !$option_end_line) && substr_count($data,"</select>")){
        $option_end_line = $i+1;
    }
    if(!$option2_start_line && substr_count($data,"option2")){
        $option2_start_line = $i;
    }

    if(($option2_start_line && !$option2_end_line) && substr_count($data,"</select>") && ($option_end_line != $i+1)){
        $option2_end_line = $i+1;
    }

    if(!$prod_desc_start_line && substr_count($data,"class=\"cont\"")){
        $prod_desc_start_line = $i;
    }

    if($prod_desc_start_line && $prod_desc_start_line != $i){
        if($prod_desc_start_line && substr_count($data,"<div ")){
            $prod_desc_inner_div_cnt++;
        }

        if($prod_desc_start_line && !$prod_desc_end_line && substr_count($data,"</div>")){
            $prod_desc_inner_div_cnt--;
        }
    }

    if(($prod_desc_start_line && !$prod_desc_end_line) && $prod_desc_inner_div_cnt == 0 && substr_count($data,"</div>")){
        $prod_desc_end_line = $i+1;
    }
}

// 상세 이미지
$optionnal_img_tmp = "";
$goods_detail_images = "";

preg_match_all("|<P><IMG src=\"(.*)\"></P>|U",$results,$optionnal_img_tmp, PREG_PATTERN_ORDER);

for($i=0 ;$i < count($optionnal_img_tmp[1]);$i++){

    if(substr_count($optionnal_img_tmp[1][$i],"http:") == 0){
        $optionnal_img_tmp[1][$i] = "http://www.bqueen.co.kr/mall/".$optionnal_img_tmp[1][$i];
    }
    $goods_detail_images[$i] = $optionnal_img_tmp[1][$i];
}
//print_r($goods_detail_images_name);
//print_r($goods_detail_images);
//$goods_detail_images_name = $detail_images_name[1];

$_prod_desc = "";
$prod_desc_prod = "";

for($i=$prod_desc_start_line;$i < $prod_desc_end_line;$i++){
    $prod_desc_prod .= $datas[$i];
}
$prod_desc_prod = str_replace("src=\"/item","src=\"//www.ddpopstyle.co.kr/item",$prod_desc_prod);
//echo $prod_desc_prod;
//exit;
/*
if(count($goods_detail_images) > 0){
	$_prod_desc = "<table align=center>";
	$_prod_desc .= "<tr><td align=center><div style='text-align:left;'>".$prod_desc_prod."</div></td></tr>";
	for($i=0;$i < count($goods_detail_images);$i++){
		$_prod_desc .= "<tr><td align=center style='padding:5px 0px 30px 0px;'><IMG src=\"".$goods_detail_images[$i]."\"></td></tr>";
	}
	$_prod_desc .= "</table>";

	$prod_desc_prod = $_prod_desc;
}
*/

// 옵션의 문자열 저장
$options_str = "";
for($i=$option_start_line;$i < $option_end_line;$i++){
    $options_str .= $datas[$i];
}
$options2_str = "";
for($i=$option2_start_line;$i < $option2_end_line;$i++){
    $options2_str .= $datas[$i];
}
// 옵션의 문자열 배열로 저장
$options = "";
$_option = "";
$option = "";
//echo $options_str;
//exit;
if(is_array($option_datas)){
    $options[1][option_type] = "9";
    $options[1][option_name] = convertWrongUnicode($option_title);//"COLOR-SIZE";
    $options[1][option_kind] = "b";
    $options[1][option_use] = "1";
    //for($i=1;$i < count($option_datas);$i++){
    $i = 0;
    foreach($option_datas as $key => $option_data){
        $options[1][details][$i][option_div] = convertWrongUnicode($option_data[option_value]);
        $options[1][details][$i][global_odinfo] = "";//$option_data[option_value];
        $options[1][details][$i][price] = $option_data[option_price];

        $options[1][details][$i][etc1] = $key;
        $i++;
    }
    //echo "<pre>";
    //print_r($options);
}else{
    if($options_str){

        preg_match_all("|<option .*>(.*)</option>|U",$options_str,$options, PREG_PATTERN_ORDER);

        $_option = $options[1];
        $option[0] = "옵션";
        for($i=1,$j=1;$i < count($_option);$i++){
            $option[$i] = trim($_option[$j]);
            $j++;
        }
        //echo "#option";
        print_r($option);

    }

    $options2 = "";
    $_option2 = "";
    $option2 = "";
    if($options2_str){

        preg_match_all("|<option .*>(.*)</option>|U",$options2_str,$options2, PREG_PATTERN_ORDER);

        $_option2 = $options2[1];
        $option2[0] = "옵션";
        for($i=1,$j=1;$i < count($_option2);$i++){
            $option2[$i] = trim($_option2[$j]);
            $j++;
        }
        //echo "#option2";
        print_r($option2);

    }
}

if($soldout_message || $price == "" ){
    $price = "";
    $stock_bool = false;
}else{
    $stock_bool = true;
}

$goods_desc_copy = 1;

?>