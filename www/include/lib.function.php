<?php
$master_db = new Database;
$master_db->master_db_setting();

$slave_db = new Database;
$slave_db->slave_db_setting();

$slave_mdb = new Database;
$slave_mdb->slave_db_setting();

//include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
Function Xml2Array($string,$parser_str = '"'){
    $match_ele_exp = '/<(\S+)([^>]*)>(.*?)<\/\\1>/s';
    $match_att_exp = "/(\S+)\=$parser_str([^$parser_str]*)$parser_str/s";
    preg_match_all($match_ele_exp, $string, $match);
    for($i=0,$count=count($match[1]);$i<$count;$i++){
        $key = $i;
        $val = $match[1][$i];
        $tmparray=array();
        preg_match_all($match_att_exp, $match[2][$i], $matchatt);
        for($s=0,$scount=count($matchatt[1]);$s<$scount;$s++)
            $tmparray[$matchatt[1][$s]] = $matchatt[2][$s];

        $match[3][$key] = trim($match[3][$key]);
        if(eregi("^<!\[CDATA\[(.*)\]\]>$",$match[3][$key],$cdatatmp)){
            $match[3][$key] = $cdatatmp[1];
            if($tmparray) $array[$val][] = array("att"=>$tmparray, "body" => $match[3][$key]);
            else $array[$val][] = array("body" => $match[3][$key]);
        }
        else if (preg_match($match_ele_exp, $match[3][$key]) ) {
            if($tmparray) $array[$val][] = array("att"=>$tmparray, "body" => Xml2Array($match[3][$key],$parser_str));
            else $array[$val][] = array("body" => Xml2Array($match[3][$key],$parser_str));
        }
        else {
            if($tmparray) $array[$val][] = array("att"=>$tmparray, "body" => $match[3][$key]);
            else $array[$val][] = array("body" => $match[3][$key]);
        }
    }
    return $array;
}

function titleDesign($title, $title_desc, $navigation, $templet=""){
    return displayDesignModule("title", array("title"=>$title, "title_desc"=>$title_desc, "navigation"=>$navigation), $templet);
}

function displayDesignModule($type, $data, $templet=""){
    global $DOCUMENT_ROOT, $CART, $order, $layout_config, $order_details;

    include_once($_SERVER["DOCUMENT_ROOT"]."/class/Template_.class.php");

    if(!$templet && file_exists($_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"][mall_data_root]."/module/".$type."_templet/title.xml")){

        $doc = new DOMDocument();
        $doc->load($_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"][mall_data_root]."/module/".$type."_templet/title.xml");

        $templet = trim($doc->documentElement->nodeValue);


    }

    if($templet){
        $tpl = new Template_();
        $tpl->template_dir =$_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"][mall_data_root]."/module/".$type."_templet";
        $tpl->compile_dir  = $_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"][mall_data_root]."/compile_/module/".$type."_templet";
        $tpl->define($type,$templet);
        $tpl->assign($data);
        $tpl->assign($type."_templet",$_SESSION["layout_config"][mall_data_root]."/module/".$type."_templet");


        return $tpl->fetch($type);
    }
}


//상품아이디로 폴더명 지정하기...
function UploadDirText($basedir, $Pid, $mode="N"){

    $Pid = zerofill($Pid);
    //echo $Pid."<br>";
    $fstdir = "/".substr($Pid, 0,2);
    $sedir = "/".substr($Pid, 2,2);
    $thdir = "/".substr($Pid, 4,2);
    $fordir =  "/".substr($Pid, 6,2);
    $fifdir =  "/".substr($Pid, 8,2);
    if($mode == "Y") {
        if(!is_dir($basedir.$fstdir)){

            mkdir($basedir.$fstdir);
            chmod($basedir.$fstdir,0777);
        }
        if(!is_dir($basedir.$fstdir.$sedir)){
            mkdir($basedir.$fstdir.$sedir);
            chmod($basedir.$fstdir.$sedir,0777);
        }
        //echo $basedir.$fstdir.$sedir.$thdir;
        if(!is_dir($basedir.$fstdir.$sedir.$thdir)){
            mkdir($basedir.$fstdir.$sedir.$thdir);
            chmod($basedir.$fstdir.$sedir.$thdir,0777);
        }
        if(!is_dir($basedir.$fstdir.$sedir.$thdir.$fordir)){
            mkdir($basedir.$fstdir.$sedir.$thdir.$fordir);
            chmod($basedir.$fstdir.$sedir.$thdir.$fordir,0777);
        }
        if(!is_dir($basedir.$fstdir.$sedir.$thdir.$fordir.$fifdir)){
            mkdir($basedir.$fstdir.$sedir.$thdir.$fordir.$fifdir);
            chmod($basedir.$fstdir.$sedir.$thdir.$fordir.$fifdir,0777);
        }

    }
    //exit;
    return $fstdir.$sedir.$thdir.$fordir.$fifdir;
}
 

//상품아이디로 이미지 불러오기...
function PrintImage($basedir, $Pid, $type="b", $image_db="", $noimgType="shop", $image_type=""){
    global $admin_config, $DOCUMENT_ROOT,$slave_mdb;
    global $image_hosting_type;
    //return $Pid;
    $Pid = zerofill($Pid);
    $imgdir = UploadDirText($basedir, $Pid);
    $imgpath = $basedir.$imgdir;

    $is_adult_bool = true;

    //19금 이미지 처리
    if($_SESSION['user'][age] < '19' && !substr_count($_SERVER['PHP_SELF'],'/admin/')){
        $is_adult_bool =  false;
    }

    if(!$is_adult_bool){
        $sql="select id from shop_product where id='".$Pid."' and is_adult='1' ";
        $slave_mdb->query($sql);
    }

    if(!$is_adult_bool && $slave_mdb->total){
        $imageSrc = $basedir."/product_19_200.gif";
    }else{

        if($image_hosting_type == "ftp"){
            //echo $type."<br>";
            $imageSrc = $image_db[$type."img"];
        }else{
            if (is_dir($imgpath)){
                $handle  = opendir($imgpath); // 디렉토리 open

                // 디렉토리의 파일을 전체 삭제.
                while (false !== ($filename = readdir($handle))) {
                    // 파일인 경우만 목록에 추가한다.
                    if(is_file($imgpath . "/" . $filename)){
                        $imgName = explode("_", $filename);
                        $imgDate = $imgName['2'];
                        break;
                    }
                }
                closedir($handle); // 디렉토리 close
            }

            if(strlen($imgDate) == 14){
                //$imageSrc = $imgpath."/".$type."_".$Pid."".$image_type.".gif";
                $imageSrc = $imgpath."/".$type."_".$Pid."_".$imgDate."".$image_type."_0.gif";
                //return $imageSrc;
                $imageSrc2 = $imgpath."/".$type."_".$Pid."_".$imgDate."".$image_type.".jpg";	 // 외부이미지서버에서 jpg 파일을 퍼왔을경우 jpg도 체크하도록 추가 : 이현우(2013-05-06)
                //echo $DOCUMENT_ROOT.$imageSrc."<br>";
            }else{
                //$imageSrc = $imgpath."/".$type."_".$Pid."".$image_type.".gif";
                $imageSrc = $imgpath."/".$type."_".$Pid."".$image_type."_0.gif";
                //return $imageSrc;
                $imageSrc2 = $imgpath."/".$type."_".$Pid."".$image_type.".jpg";	 // 외부이미지서버에서 jpg 파일을 퍼왔을경우 jpg도 체크하도록 추가 : 이현우(2013-05-06)
                //echo $DOCUMENT_ROOT.$imageSrc."<br>";
            }

            if(!file_exists($DOCUMENT_ROOT.$imageSrc)){
                if(!file_exists($DOCUMENT_ROOT.$imageSrc2)){
                    if(!file_exists($DOCUMENT_ROOT.$basedir."/".$noimgType."/noimg.gif")){
                        $imageSrc = $basedir."/noimg.gif";
                    }else{
                        $imageSrc = $basedir."/".$noimgType."/noimg.gif";
                    }
                }else{
                    $imageSrc = $imageSrc2;
                }
            }
        }

    }



    return constant("IMAGE_SERVER_DOMAIN").$imageSrc;
}

//Socials 정보 view 공통화 작업 jk140612
function getSocials($url="", $withWrapper = true, $full = false) {

    if ($url == ""){
        $url = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    }
    elseif ($full) {
        $url = $url;
    }
    else{
        $url = $url = "http://" . $_SERVER["HTTP_HOST"] .$_SERVER["PHP_SELF"] . $url;
    }

    $shareCounts = getAllShareCounts(array('facebook', 'twitter', 'google-plus', 'pinterest', 'tumblr', 'linkedin'), $url);

    $mstring = $withWrapper ? "<div class='socials'>" : '';
    $mstring .= "	<a class='facebook' href='#' onclick=\"share_sns('facebook','".$url."');\"><span class='title'>Facebook</span> <span class='counter'><span>".  $shareCounts['facebook'] ."</span></span></a>";
    $mstring .= "	<a class='twitter' href='#' onclick=\"share_sns('twitter','".$url."');\"><span class='title'>Twitter</span> <span class='counter'><span>". $shareCounts['twitter'] ."</span></span></a>";
    $mstring .= "	<a class='google-plus' href='#' onclick=\"share_sns('google-plus','".$url."');\"><span class='title'>Google+</span> <span class='counter'><span>". $shareCounts['google-plus'] ."</span></span></a>";
    $mstring .= "	<a class='pinterest' href='#' onclick=\"share_sns('pinterest','".$url."');\"><span class='title'>Pinterest</span> <span class='counter'><span>". $shareCounts['pinterest'] ."</span></span></a>";
    /* $mstring .= "	<a class='tumblr' href='#' onclick=\"share_sns('tumblr','".$url."');\"><span class='title'>Tumblr</span> <span class='counter'><span>". $shareCounts['tumblr'] ."</span></span></a>"; */
    $mstring .= "	<a class='linkedin' href='#' onclick=\"share_sns('linkedin','".$url."');\"><span class='title'>LinkedIn</span> <span class='counter'><span>". $shareCounts['linkedin'] ."</span></span></a>";
    $mstring .= $withWrapper ? "</div>" : '';

    return $mstring;
}



function getAllShareCounts($types, $url) {
    $responses = array();

    $apiUrls = array();
    foreach ($types as $key => $social) {
        switch($social) {
            case 'facebook':
                $apiUrls['facebook'] = "http://graph.facebook.com/?id=" . urlencode($url) . "&format=json";
                break;

            case 'twitter':
                $apiUrls['twitter'] = "http://urls.api.twitter.com/1/urls/count.json"."?callback=?&url=" . urlencode($url);
                break;

            case 'linkedin':
                $apiUrls['linkedin'] = "http://www.linkedin.com/countserv/count/share?url=" . urlencode($url) . "&format=json";
                break;

            case 'pinterest':
                $apiUrls['pinterest'] = "http://api.pinterest.com/v1/urls/count.json?callback=receiveCount&url=" . urlencode($url);
                break;

            case 'google-plus':
                $apiUrls['google-plus'] = "https://clients6.google.com/rpc?key=AIzaSyCKSbrvQasunBoV16zDH9R33D88CeLr9gQ";
                break;

            case 'tumblr':
                //	$apiUrls['tumblr'] =
                break;

            default:
                break;
        }
    }

    $mh = curl_multi_init();
    $channels = array();
    foreach($apiUrls as $key => $value) {
        $channels[$key] = curl_init($value);
        if ($key == 'google-plus') {
            curl_setopt($channels[$key], CURLOPT_URL, $value);
            curl_setopt($channels[$key], CURLOPT_POST, 1);
            curl_setopt($channels[$key], CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($channels[$key], CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $url . '","source":"widget","userId":"@viewer","groupId":"@self"},
		"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
            curl_setopt($channels[$key], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($channels[$key], CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        }
        else {
            curl_setopt($channels[$key], CURLOPT_URL, $value);
            curl_setopt($channels[$key], CURLOPT_RETURNTRANSFER, 1);
        }
        curl_multi_add_handle($mh, $channels[$key]);
    }
    $running = null;
    do {
        $mrc = curl_multi_exec($mh, $running);
    } while ($mrc == CURLM_CALL_MULTI_PERFORM || $running);

    foreach($channels as $key => $value) {
        $json = curl_multi_getcontent($channels[$key]);

        switch($key) {
            case 'facebook':
                $info = (array)json_decode($json);
                $responses[$key] = intval($info['shares']);
                break;

            case 'google-plus':
                $info = (array)json_decode($json);
                $responses[$key] = $info[0]->result -> metadata -> globalCounts -> count;
                break;

            case 'pinterest':
                $json = preg_replace('/^receiveCount\((.*)\)$/', "\\1", $json);
                $info = (array)json_decode($json);
                $responses[$key] = intval($info['count']);
                break;

            default:
                $info = (array)json_decode($json);
                $responses[$key] = intval($info['count']);
                break;
        }

        curl_multi_remove_handle($mh, $channels[$key]);
    }

    curl_multi_close($mh);

    return $responses;
}



function InventoryPrintImage($basedir, $Pid, $type="b", $image_db="", $noimgType="shop"){
    global $DOCUMENT_ROOT;
    global $image_hosting_type;

    $Pid = str_replace("/","_",$Pid);
    $Pid = str_replace(":","_",$Pid);
    $Pid = str_replace(" ","_",$Pid);


    $imgdir = UploadDirText($basedir, $Pid);
    $imgpath = $basedir.$imgdir;

    $imageSrc = $imgpath."/".$type."_".$Pid.".gif";

    if(!file_exists($DOCUMENT_ROOT.$imageSrc)) $imageSrc = $basedir."/".$noimgType."/noimg_".$type.".gif";

    return $imageSrc;
}

//상품아이디로 추가 이미지 불러오기...
function PrintImageAdd($basedir, $Pid, $Addid, $type="b", $image_db=""){
    global $DOCUMENT_ROOT;
    global $image_hosting_type;

    $imgdir = UploadDirText($basedir, $Pid);
    $imgpath = $basedir."/addimg".$imgdir;

    if($image_hosting_type == "ftp"){
        $imageSrc = $image_db[imgpath];
    }else{
        $imageSrc = $imgpath."/".$type."_".$Addid."_add.gif";
        //if(!file_exists($DOCUMENT_ROOT.$imageSrc)) $imageSrc = $basedir."/product/noimg_".$type.".gif";
    }

    return constant("IMAGE_SERVER_DOMAIN").$imageSrc;
}

//회원이미지 불러오기
function PrintImageMember($code, $img_width="100", $img_height="100"){
    global $layout_config;
    global $slave_mdb;
    //$slave_mdb = $slave_mdb

    $memimg = "";

    if(!$code)	{
        $imageSrc = "<img src='".$_SESSION["layout_config"]["mall_data_root"]."/images/member_noimg.gif' width='$img_width' height='$img_height'>";
    } else {

        $sql = "select cmd.file, cu.id from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd where cu.code = cmd.code AND cu.code = '".$code."'  ";

        $slave_mdb->query($sql);
        $slave_mdb->fetch(0);

        $memimg = $slave_mdb->dt[file];
        $id = $slave_mdb->dt[id];

        if($memimg != "") {
            $mem_image = $_SESSION["layout_config"]["mall_data_root"]."/images/memberimg/$id/$memimg";
            if(!file_exists($_SERVER['DOCUMENT_ROOT'].$mem_image)) $mem_image = $_SESSION["layout_config"]["mall_data_root"]."/images/member_noimg.gif";
        } else {
            $mem_image = $_SESSION["layout_config"]["mall_data_root"]."/images/member_noimg.gif";
        }
        $imageSrc = "<img src='$mem_image' width='$img_width' height='$img_height'>";
    }
    return $imageSrc;
}


//미니샵이미지 불러오기
function miniShopImg($admin){
    global $layout_config;
    global $slave_mdb;
    //$slave_mdb = $slave_mdb

    if(!$admin)	{
        $imageSrc = $_SESSION["layout_config"]["mall_templet_webpath"]."/images/mini_shop_title_img.gif";
    } else {
        $imageSrc = $_SESSION["layout_config"]["mall_image_path"]."/shopimg/shop_thum_$admin.gif";
        if(!file_exists($_SERVER['DOCUMENT_ROOT'].$imageSrc)) $imageSrc = $_SESSION["layout_config"]["mall_templet_webpath"]."/images/mini_shop_title_img.gif";
        else $imageSrc = $_SESSION["layout_config"]["mall_image_path"]."/shopimg/shop_thum_$admin.gif";
    }
    return $imageSrc;
}


//회원이미지 불러오기
function levelMember($code){
    global $slave_mdb;

    //$slave_mdb = $slave_mdb
    $slave_mdb->query("SELECT mem_type FROM common_user WHERE code = '".$code."' ");
    $slave_mdb->fetch();

    if($slave_mdb->dt[mem_type] == "M") $levelName = "일반회원";
    else if($slave_mdb->dt[mem_type] == "C") $levelName = "기업회원";
    else if($slave_mdb->dt[mem_type] == "F") $levelName = "외국인회원";
    else if($slave_mdb->dt[mem_type] == "S") $levelName = "셀러회원";
    else if($slave_mdb->dt[mem_type] == "A") $levelName = "관리자";
    else if($slave_mdb->dt[mem_type] == "MD") $levelName = "MD회원";

    return $levelName;
}
///////////////////////////////////////// QR Code ////////////////////////////////////////////////////////
// Created By 2011.07.25 이철성
//////////////////////////////////////////////////////////////////////////////////////////////////////////

function GenerateQRCode($base_dir, $id){
    include_once $_SERVER["DOCUMENT_ROOT"].'/include/qrcode/phpqrcode/qrlib.php';

    $PNG_TEMP_DIR = $_SERVER["DOCUMENT_ROOT"].$base_dir."/product".UploadDirText($base_dir, $id)."/";//'../qrcode/temp/'; //$_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_qrcode/";
    $PNG_WEB_DIR = $base_dir."/product".UploadDirText($base_dir, $id)."/";//'../qrcode/temp/';

    //$address = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    $address = 'http://'.$_SERVER['SERVER_NAME']."/shop/goods_view.php?id=".$id;

    $errorCorrectionLevel = 'L';
    $matrixPointSize = 3; //
    $data = $address;

    $filename = $PNG_TEMP_DIR.md5($address.$data.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';

    QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
    //echo '<img src="'.$PNG_WEB_DIR.basename($filename).'" />';

    $qrCodeImageAddress = '<img src="'.$PNG_WEB_DIR.basename($filename).'" />';
    return $qrCodeImageAddress;
}

//타입별 명칭
function productTypeName($type){

    switch($type){
        case '0': $typeName = "일반상품";break;
        case '1': $typeName = "해외구매대행";break;
        case '2': $typeName = "최저가경매";break;
        case '3': $typeName = "하트콘 상품";break;
        case '4': $typeName = "배송상품";break;
        case '5': $typeName = "쿠폰상품";break;
        case '6': $typeName = "여행상품";break;
    }
    return $typeName;
}

function RecommMiniItem($limit = "5") {
    global $shop_product_type;
    global $slave_mdb;

    //$slave_mdb = new Database;
    $sql = "SELECT cd.company_id, com_name  FROM common_company_detail cd WHERE cd.recommend = 'Y' and cd.seller_auth='Y' LIMIT $limit ";
    $slave_mdb->query($sql);
    $companys = $slave_mdb->fetchall();

    $recommitems = array();
    if(is_array($companys)) {
        foreach($companys as $_key=>$_value) {
            $slave_mdb->query("SELECT pname, id, sellprice, admin, '".$_value[com_name]."' as com_name FROM shop_product WHERE admin = '".$_value[company_id]."' AND state = 1 AND disp = 1 AND product_type in (".implode(' , ',$shop_product_type).") ORDER BY regdate DESC LIMIT 1 ");
            if($slave_mdb->total > 0) $recommitems[$_key] = $slave_mdb->fetch();
        }
    }
    //print_r($companys);
    return $recommitems;
}

function SellerAfterRating($board,$rating_value,$basic_return="100",$return_type="rate", $after_rating_type="company") {
    global $slave_mdb;
    /*global $shop_product_type;
	$slave_mdb = new Database;

	$starArr = array("0"=>"Terburuk","1"=>"Terburuk","2"=>"Tidak bagus","3"=>"Normal","4"=>"Rekomendasi","5"=>"Sangat Direkomendasi");

	$sql = "SELECT avg(bu.uf_valuation) as uf_valuation FROM ".TBL_SHOP_BBS_USEAFTER." bu, ".TBL_SHOP_PRODUCT." p WHERE bu.pid = p.id and p.admin = '$admin' ";
	$db->query($sql);
	$db->fetch();

	//print_r($companys);
	$no = round($db->dt[uf_valuation],0);
	return $starArr[$no];*/

    //$slave_mdb=new Database;



    $arr_board=explode(";",$board);
    $all_total=0;
    $all_sum_rating=0;

    for($i=0;$i<count($arr_board);$i++) {
        if($after_rating_type == "company"){
            $sql="SELECT
							SUM(
							CASE WHEN Ceil(p.after_score/p.after_cnt)='1' THEN 80
							WHEN Ceil(p.after_score/p.after_cnt)='2' THEN 85
							WHEN Ceil(p.after_score/p.after_cnt)='3' THEN 90
							WHEN Ceil(p.after_score/p.after_cnt)='4' THEN 95
							WHEN Ceil(p.after_score/p.after_cnt)='5' THEN 100 END) AS sum_rating,
							COUNT(p.after_cnt) AS cnt
							FROM ".TBL_SHOP_PRODUCT." p WHERE p.admin='".$rating_value."' ";
        }else if($after_rating_type == "goods"){
            $sql="SELECT
							SUM(
							CASE WHEN Ceil(p.after_score/p.after_cnt)='1' THEN 80
							WHEN Ceil(p.after_score/p.after_cnt)='2' THEN 85
							WHEN Ceil(p.after_score/p.after_cnt)='3' THEN 90
							WHEN Ceil(p.after_score/p.after_cnt)='4' THEN 95
							WHEN Ceil(p.after_score/p.after_cnt)='5' THEN 100 END) AS sum_rating,
							COUNT(p.after_cnt) AS cnt
							FROM ".TBL_SHOP_PRODUCT." p WHERE p.id='".$rating_value."' ";
        }
        //echo $sql;
        $slave_mdb->query($sql);

        $slave_mdb->fetch();
        $all_total+=$slave_mdb->dt["cnt"];
        $all_sum_rating+=$slave_mdb->dt["sum_rating"];
    }

    if($all_total==0 || $all_total == "") {
        return $basic_return;
    } else {
        $average=ceil($all_sum_rating/$all_total);
        if($return_type=="rate") {
            $return_value=$average;
        } else {
            if($average>84) {
                if($average>89) {
                    if($average>94) {
                        if($average>99) {
                            $return_value=5;
                        } else {
                            $return_value=4;
                        }
                    } else {
                        $return_value=3;
                    }
                } else {
                    $return_value=2;
                }
            } else {
                $return_value=1;
            }
        }
        return $return_value;
    }

}

//난수 발생
function getCodePrint($len){
    $SID = strtoupper(md5(uniqid(rand())));   //난수발생 후 대문자로 치환
    $code = substr($SID, 0, $len);    //0번째 자리를 기준으로 자른후
    return $code;       //8자리가 될때 까지 리턴
}


function GetAddressZone()
{
    global $slave_mdb;
    //$slave_mdb = new Database;
    $sql = "SELECT * FROM shop_address_data GROUP BY province ORDER BY ad_ix ASC ";

    $slave_mdb->query($sql);

    return $slave_mdb->fetchall();

}

//스타샵 탭베너 출력
function GetPrintStarshop($mf_type, $width='630', $height='318')
{
    global $layout_config;
    global $slave_mdb;
    //$slave_mdb = new Database;

    $imgPath = $_SESSION["layout_config"][mall_data_root]."/images/starshop_data/".$mf_type."/";

    $sql = "select mf_effect from shop_manage_starshop where mf_type = '".$mf_type."' and disp = 1  ";
    $slave_mdb->query($sql);
    $slave_mdb->fetch();
    $mf_effect = $slave_mdb->dt[mf_effect]; //관리자에서 설정된 효과값을 가져온다.

    $sql = "select mfd.mf_file, mfd.thum_on, mfd.thum_off, mfd.mf_link, mfd.mf_title, '$imgPath' as imgpath from shop_manage_starshop mf LEFT OUTER JOIN shop_manage_starshop_detail mfd on mf.mf_ix = mfd.mf_ix where mf_type = '".$mf_type."' and mf.disp = 1 order by mfd.mfd_ix asc ";

    $slave_mdb->query($sql);

    $i_no = 0;
    $btn_no = "";
    $tcnt = $slave_mdb->total;
    $printflash = $slave_mdb->fetchall();
    $mString = "
	<style>
	.{$mf_type}_main_tab	{float:left;width:{$width}px;}
	.{$mf_type}_main_tab	li	{float:left;}
	</style>
	";
    foreach($printflash as $_key => $_val){
        if($printflash[$_key][mf_file] != ""){ //이미지값과 네비게이션 숫자 데이터를 담아둔다
            $i_no++;
            if($i_no == 1) {
                $display = "block";
                $firstID = " id='{$mf_type}_onpage'";
            } else {
                $display = "none";
                $firstID = "";
            }

            $oreg = explode(".", $printflash[$_key][thum_off]);

            $tabimgString .= "<li><a href='javascript:;' onfocus='this.blur();'><img src=\"".$printflash[$_key][imgpath].$printflash[$_key][thum_off]."\" rel=\"".$printflash[$_key][imgpath].$oreg[0]."\" {$firstID} align=\"absmiddle\" class='{$mf_type}_tabmenu_img' imgid='{$mf_type}_starimg_{$_key}' /></a></li>";

            $imgString .= "<div id='{$mf_type}_starimg_{$_key}' class='{$mf_type}_big_img' style='display:{$display};'><a href='".$printflash[$_key][mf_link]."'><img src='".$printflash[$_key][imgpath].$printflash[$_key][mf_file]."' title='".$printflash[$_key][mf_title]."' ></a></div>";
        }
    }
    $mString .= "<ul class='{$mf_type}_main_tab'>";
    $mString .= "{$tabimgString}";
    $mString .= "</ul>";
    $mString .= "{$imgString}";
    $mString .= "
	<script>
	$(document).ready(function(){
		$('#{$mf_type}_onpage').attr('src', $('#{$mf_type}_onpage').attr('rel')+'_on.gif');
		$('.{$mf_type}_tabmenu_img').click(
			function(){
				$('.{$mf_type}_tabmenu_img').each(function(){
					$(this).attr('src', $(this).attr('rel')+'.gif');
				});
				$(this).attr('src', $(this).attr('rel')+'_on.gif'),
				$('.{$mf_type}_big_img').css('display', 'none'),
				$('#'+$(this).attr('imgid')).css('display', 'block');
			}
		);
	});
	</script>
	";

    return $mString;
}


function GetPrintFlash01($mf_type, $width='630', $height='318')// 상위 카테고리 이름을 반환하는 함수
{
    global $layout_config;
    global $slave_mdb;

    //$slave_mdb = new Database;

    $imgPath = $_SESSION["layout_config"][mall_data_root]."/images/flash_data/".$mf_type."/";

    $sql = "select mf_effect from ".TBL_SHOP_MANAGE_FLASH." where mf_type = '".$mf_type."' and disp = 1  ";
    $slave_mdb->query($sql);
    $slave_mdb->fetch();
    $mf_effect = $slave_mdb->dt[mf_effect]; //관리자에서 설정된 효과값을 가져온다.

    $sql = "select mfd.mf_file, mfd.mf_link, mfd.mf_title, '$imgPath' as imgpath from ".TBL_SHOP_MANAGE_FLASH." mf LEFT OUTER JOIN shop_manage_flash_detail mfd on mf.mf_ix = mfd.mf_ix where mf_type = '".$mf_type."' and mf.disp = 1 order by mfd.regdate asc ";

    $slave_mdb->query($sql);

    $i_no = 0;
    $btn_no = "";
    $printflash = $slave_mdb->fetchall();
    if (is_array($printflash)){
        foreach($printflash as $_key => $_val){
            if($printflash[$_key][mf_file] != ""){ //이미지값과 네비게이션 숫자 데이터를 담아둔다
                $i_no++;

                $imgString .= "<div><a href='".$printflash[$_key][mf_link]."'><img src='".$printflash[$_key][imgpath].$printflash[$_key][mf_file]."' title='".$printflash[$_key][mf_title]."' width='".$width."' height='".$height."'></a></div>";
            }
        }
    }
    $mString .= "<div id='{$mf_type}_banner1' style=''>";
    $mString .= "<script src='/js/main_flash/main_slider.js' type='text/javascript'></script>";
    $mString .= "<div id='{$mf_type}_banner' style='display:none'>";
    $mString .= "{$imgString}";
    $mString .= "</div>";
    $mString .= "
	<script>
	var config = {
		'id':'{$mf_type}_banner',
		";
    if($mf_effect == "S"){
        $mString .= " 'effect':'FILTER: progid:DXImageTransform.Microsoft.GradientWipe(GradientSize=0.00,wipestyle=0,motion=forward,Duration=0.5,spokes=6)',";
    } else if($mf_effect == "F"){
        $mString .= " 'effect':'FILTER: progid:DXImageTransform.Microsoft.Fade(Overlap=1.00,Duration=0.7)',";
    } else if($mf_effect == "R"){
        $mString .= " 'effect':'FILTER: progid:DXImageTransform.Microsoft.RandomBars(orientation=vertical,Duration=0.5)',";
    } else if($mf_effect == "T"){
        $mString .= " 'effect':'FILTER: progid:DXImageTransform.Microsoft.Zigzag(GridSizeX=8,GridSizeY=8,Duration=0.5)',";
    }
    $mString .= "
		'width':{$width},
		'height':{$height},
		'wait':4000,
		'numDisplay':'block',
		'numimg':[
			['".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/btn_off.gif','".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/btn_on.gif'],
			['".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/btn_off.gif','".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/btn_on.gif'],
			['".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/btn_off.gif','".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/btn_on.gif'],
			['".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/btn_off.gif','".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/btn_on.gif'],
			['".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/btn_off.gif','".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/btn_on.gif'],
			['".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/btn_off.gif','".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/btn_on.gif'],
			['".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/btn_off.gif','".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/btn_on.gif'],
			['".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/btn_off.gif','".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/btn_on.gif'],
			['".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/btn_off.gif','".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/btn_on.gif']

		]
	}
	slider2 = new main_slider(config);//GetPrintFlash()와 같이 사용할 경우 스크립트 에러 발생하여 변수명을 바꿈 slider -> slider2 kbk 13/07/26
	</script>
	";
    $mString .= "</div>";

    /**
    if($i_no > 1){
    $mString .= "
    <script type='text/javascript'>
    $(window).load(function() {";
    if($mf_effect == "S"){
    $mString .= "
    $('#slider').nivoSlider({
    effect:'fade',
    pauseTime:5000,
    pauseOnHover:true
    });
    ";
    } else if($mf_effect == "F"){
    $mString .= "
    $('#slider').nivoSlider({
    effect:'fade',
    pauseTime:5000,
    pauseOnHover:true
    });";
    } else if($mf_effect == "T"){
    $mString .= "
    $('#slider').nivoSlider({
    effect:'fold',
    pauseTime:5000,
    pauseOnHover:true
    });";
    } else if($mf_effect == "R"){
    $mString .= "
    $('#slider').nivoSlider({
    effect:'random',
    animSpeed:1500,
    pauseTime:5000,
    startSlide:2,
    directionNav:false,
    controlNav:true,
    keyboardNav:false,
    pauseOnHover:false
    });";
    }
    $mString .= "
    });
    </script>
    ";
    }
     **/

    return $mString;
}


function GetPrintFlash02($mf_type, $width='630', $height='318')
{
    global $layout_config;
    global $slave_mdb;
    //$slave_mdb = new Database;

    $imgPath = $_SESSION["layout_config"][mall_data_root]."/images/flash_data/".$mf_type."/";

    $sql = "select mf_effect from ".TBL_SHOP_MANAGE_FLASH." where mf_type = '".$mf_type."' and disp = 1  ";
    $slave_mdb->query($sql);
    $slave_mdb->fetch();
    $mf_effect = $slave_mdb->dt[mf_effect]; //관리자에서 설정된 효과값을 가져온다.

    $sql = "select mfd.mf_file, mfd.mf_link, mfd.mf_title, '$imgPath' as imgpath from ".TBL_SHOP_MANAGE_FLASH." mf LEFT OUTER JOIN shop_manage_flash_detail mfd on mf.mf_ix = mfd.mf_ix where mf_type = '".$mf_type."' and mf.disp = 1 order by mfd.regdate asc ";

    $slave_mdb->query($sql);

    $i_no = 0;
    $btn_no = "";
    $printflash = $slave_mdb->fetchall();
    if(count($printflash) > 0){
        foreach($printflash as $_key => $_val){
            if($printflash[$_key][mf_file] != ""){ //이미지값과 네비게이션 숫자 데이터를 담아둔다
                $i_no++;

                $imgString .= "<div><a href='".$printflash[$_key][mf_link]."'><img src='".$printflash[$_key][imgpath].$printflash[$_key][mf_file]."' title='".$printflash[$_key][mf_title]."' width='".$width."' height='".$height."'></a></div>";
            }
        }
        $mString .= "<div id='{$mf_type}_banner1' style='margin-top:20px;'>";
        $mString .= "<script src='/js/main_flash/main_slider.js' type='text/javascript'></script>";
        $mString .= "<div id='{$mf_type}_banner' style='display:none'>";
        $mString .= "{$imgString}";
        $mString .= "</div>";
        $mString .= "
			<script>
			var config = {
				'id':'{$mf_type}_banner',
				";
        if($mf_effect == "S"){
            $mString .= " 'effect':'FILTER: progid:DXImageTransform.Microsoft.GradientWipe(GradientSize=0.00,wipestyle=0,motion=forward,Duration=0.5)',";
        } else if($mf_effect == "F"){
            $mString .= " 'effect':'FILTER: progid:DXImageTransform.Microsoft.Fade(Overlap=1.00,Duration=0.7)',";
        } else if($mf_effect == "R"){
            $mString .= " 'effect':'FILTER: progid:DXImageTransform.Microsoft.RandomBars(orientation=vertical,Duration=0.5)',";
        } else if($mf_effect == "T"){
            $mString .= " 'effect':'FILTER: progid:DXImageTransform.Microsoft.Zigzag(GridSizeX=8,GridSizeY=8,Duration=0.5)',";
        }

        //print_r($_SESSION["layout_config"][mall_templet_webpath]);
        $mString .= "
				'width':{$width},
				'height':{$height},
				'wait':4000,
				'numDisplay':'block',
				'numimg':[
					['".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/no_01.gif','".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/no_01_on.gif'],
					['".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/no_02.gif','".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/no_02_on.gif'],
					['".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/no_03.gif','".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/no_03_on.gif'],
					['".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/no_04.gif','".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/no_04_on.gif'],
					['".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/no_05.gif','".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/no_05_on.gif'],
					['".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/no_06.gif','".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/no_06_on.gif'],
					['".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/no_07.gif','".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/no_07_on.gif'],
					['".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/no_08.gif','".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/no_08_on.gif'],
					['".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/no_09.gif','".$_SESSION["layout_config"][mall_templet_webpath]."/images/gnb/no_09_on.gif']
				]
			}
			slider{$mf_type} = new main_slider(config);
			</script>
			";
        $mString .= "</div>";
    }

    return $mString;
}


function GetPrintFlash($mf_type, $width='630', $height='318',$templet_src="")// 상위 카테고리 이름을 반환하는 함수
{
    global $layout_config;
    global $slave_mdb;
    //$slave_mdb = new Database;

    $imgPath = $_SESSION["layout_config"][mall_data_root]."/images/flash_data/".$mf_type."/";

    $sql = "select mf_effect from ".TBL_SHOP_MANAGE_FLASH." where mf_type = '".$mf_type."' and disp = 1  ";
    $slave_mdb->query($sql);
    $slave_mdb->fetch();
    $mf_effect = $slave_mdb->dt[mf_effect]; //관리자에서 설정된 효과값을 가져온다.

    $sql = "select mfd.mf_file, mfd.mf_link, mfd.mf_title, '$imgPath' as imgpath from ".TBL_SHOP_MANAGE_FLASH." mf LEFT OUTER JOIN shop_manage_flash_detail mfd on mf.mf_ix = mfd.mf_ix where mf_type = '".$mf_type."' and mf.disp = 1 order by mfd.regdate asc  , mfd.mfd_ix asc ";

    $slave_mdb->query($sql);

    $i_no = 0;
    $btn_no = "";
    $printflash = $slave_mdb->fetchall();
    if (is_array($printflash)){
        foreach($printflash as $_key => $_val){
            if($printflash[$_key][mf_file] != ""){ //이미지값과 네비게이션 숫자 데이터를 담아둔다
                $i_no++;
                //echo $printflash[$_key][imgpath].$printflash[$_key][mf_file]."<br>";
                $imgString .= "<a href='".$printflash[$_key][mf_link]."'><img src='".$printflash[$_key][imgpath].$printflash[$_key][mf_file]."' title='".$printflash[$_key][mf_title]."' width='".$width."' height='".$height."' ".($i_no==1?"style='display:inline'":"")."></a>";
            }
        }
    }
    if($templet_src != ""){
        $mString .= "
	<link rel='stylesheet' href='".$templet_src."/css/nivo-slider.css' type='text/css'>
	<!--link rel='stylesheet' href='".$templet_src."/css/main_flash/style.css' type='text/css'--><!--하나 파일로 통합-->
	<script src='".$templet_src."/js/jquery.nivo.slider.pack.js' type='text/javascript'></script>";
    }else{
        $mString .= "
	<link rel='stylesheet' href='/css/main_flash/nivo-slider.css' type='text/css'>
	<link rel='stylesheet' href='/css/main_flash/style.css' type='text/css'>
	<script src='/js/main_flash/jquery.nivo.slider.pack.js' type='text/javascript'></script>";
    }
    $mString .= "

<STYLE>
#slider-wrapper {
    background:url(/images/slider.png) no-repeat;
    width:".$width."px;
    height:".$height."px;
    margin:0 auto;
    padding-top:74px;
    margin-top:50px;
}

#slider {
	position:relative;
    width:".$width."px;
    height:".$height."px;
    margin-left:0px;
    margin-bottom:10px;
	background:url(/images/loading.gif) no-repeat 50% 50%;
}
</STYLE>
	";

    $mString .= "<div id='slider' class='nivoSlider'>";
    $mString .= "{$imgString}";
    $mString .= "</div>";
    if($i_no > 1){
        $mString .= "
			<script type='text/javascript'>
			$(window).load(function() {";
        if($mf_effect == "S"){
            $mString .= "
				$('#slider').nivoSlider({
					effect:'fade',
					pauseTime:5000,
					pauseOnHover:true
				});
			";
        } else if($mf_effect == "F"){
            $mString .= "
				$('#slider').nivoSlider({
					effect:'fade',
					pauseTime:5000,
					pauseOnHover:true
				});";
        } else if($mf_effect == "T"){
            $mString .= "
				$('#slider').nivoSlider({
					effect:'fold',
					pauseTime:5000,
					pauseOnHover:true
				});";
        } else if($mf_effect == "R"){
            $mString .= "
				$('#slider').nivoSlider({
					effect:'random',
					animSpeed:1500,
					pauseTime:5000,
					startSlide:2,
					directionNav:false,
					controlNav:true,
					keyboardNav:false,
					pauseOnHover:false
				});";
        }
        $mString .= "
		});
			</script>
			";
    }

    return $mString;
}



function GetPrintFlashEvent($mf_type, $width='630', $height='318', $textWidth='200')
{
    global $layout_config;
    global $slave_mdb;
    //$slave_mdb = new Database;

    $imgPath = $_SESSION["layout_config"][mall_data_root]."/images/flash_data/".$mf_type."/";

    $sql = "select mf_effect from ".TBL_SHOP_MANAGE_FLASH." where mf_type = '".$mf_type."' and disp = 1  ";
    $slave_mdb->query($sql);
    $slave_mdb->fetch();
    $mf_effect = $slave_mdb->dt[mf_effect]; //관리자에서 설정된 효과값을 가져온다.

    $sql = "select mfd.mf_file, mfd.mf_link, mfd.mf_title, '$imgPath' as imgpath from ".TBL_SHOP_MANAGE_FLASH." mf LEFT OUTER JOIN shop_manage_flash_detail mfd on mf.mf_ix = mfd.mf_ix where mf_type = '".$mf_type."' and mf.disp = 1 order by mfd.mfd_ix asc ";

    $slave_mdb->query($sql);

    $i_no = 0;
    $btn_no = "";
    $printflash = $slave_mdb->fetchall();
    foreach($printflash as $_key => $_val){
        if($printflash[$_key][mf_file] != ""){ //이미지값과 네비게이션 숫자 데이터를 담아둔다
            $i_no++;

            $imgString .= "<div><a href='".$printflash[$_key][mf_link]."'><img src='".$printflash[$_key][imgpath].$printflash[$_key][mf_file]."' title='".$printflash[$_key][mf_title]."'  height='".$height."'></a><p>".$printflash[$_key][mf_title]."</p></div>";
            $navString .= "<li><a href='#'>".$printflash[$_key][mf_title]."</a></li>";
        }
    }
    $mString .= "<div id='{$mf_type}_banner1' class='desSlideshow'>";
    $mString .= "<div class='switchBigPic'>";
    $mString .= "{$imgString}";
    $mString .= "</div>";
    $mString .= "<ul class='nav'>";
    $mString .= "{$navString}";
    $mString .= "</ul>";
    $mString .= "
	<link rel='stylesheet' href='/css/main_flash/desSlideshow.css' type='text/css'>
	<script src='/js/main_flash/desSlideshow.js' type='text/javascript'></script>
	<script>
	$(function() {
		$(\"#{$mf_type}_banner1\").desSlideshow({
			autoplay: 'enable',//option:enable,disable
			slideshow_width: '{$width}',//slideshow window width
			slideshow_height: '{$height}',//slideshow window height
			thumbnail_width: '{$textWidth}',//thumbnail width
			time_Interval: '4000',//Milliseconds
			directory: '../js/main_flash/images/'// flash-on.gif and flashtext-bg.jpg directory
		});
	});
	</script>
	";
    $mString .= "</div>";

    return $mString;
}

function GetPrintFlash_fetch01($mf_type)// 상위 카테고리 이름을 반환하는 함수
{
    global $layout_config;
    global $slave_mdb;
    //$slave_mdb = new Database;

    $imgPath = $_SESSION["layout_config"][mall_data_root]."/images/flash_data/".$mf_type."/";

    $sql = "select mf_effect from ".TBL_SHOP_MANAGE_FLASH." where mf_type = '".$mf_type."' and disp = 1  ";
    $slave_mdb->query($sql);
    $slave_mdb->fetch();
    $mf_effect = $slave_mdb->dt[mf_effect]; //관리자에서 설정된 효과값을 가져온다.

    $sql = "select mfd.mf_file, mfd.mf_link, mfd.mf_title, '$imgPath' as imgpath from ".TBL_SHOP_MANAGE_FLASH." mf LEFT OUTER JOIN shop_manage_flash_detail mfd on mf.mf_ix = mfd.mf_ix where mf_type = '".$mf_type."' and mf.disp = 1 order by mfd.mfd_ix asc ";

    $slave_mdb->query($sql);
    //echo $sql;
    $i_no = 0;
    $btn_no = "";
    $printflash = $slave_mdb->fetchall();
    return $printflash;
}

// 추천상품평
function RecommReviewList($max = 5)
{
    global $slave_mdb;
    //$slave_mdb = new Database;

    //$sql = "SELECT p.*, bu.uf_subject, count(bu.pid) as ucnt, avg(uf_valuation) as uf_valuation,bu.uf_contents, u.id as uid FROM ".TBL_SHOP_BBS_USEAFTER." bu, shop_product p, common_user u WHERE bu.pid = p.id AND bu.ucode = u.code GROUP BY bu.pid ORDER BY uf_valuation DESC, ucnt DESC LIMIT $max ";
    $sql = "SELECT p.*, bu.uf_subject, bu.uf_valuation,bu.uf_contents, u.id as uid FROM ".TBL_SHOP_BBS_USEAFTER." bu, shop_product p, common_user u WHERE bu.pid = p.id  ORDER BY uf_valuation DESC, rand() LIMIT $max ";// 상품 그룹핑으로 평균을 구해서 가져오는 방식을 하나 가져오는 것으로 바꿈 12/04/04 kbk   이거 왜들어가있는지 모르겠슴 AND bu.ucode = u.code

    $sql = "SELECT p.*, bu.uf_subject, bu.uf_valuation,bu.uf_contents, u.id as uid FROM ".TBL_SHOP_BBS_USEAFTER." bu LEFT JOIN shop_product p ON bu.pid=p.id INNER JOIN common_user u ON bu.ucode=u.code ORDER BY uf_valuation DESC, rand() LIMIT $max ";//상위 쿼리를 과부하 문제로 수정함 누군지 몰라도 AND bu.ucode = u.code 이걸 왜 뺐는지 모르겠음 kbk 12/11/27

    $slave_mdb->query($sql);
    $recommrebiew = $slave_mdb->fetchall();

    return $recommrebiew	;
}


// shop/common/util.php 에서 이동
function GetParentCategory($subcid,$subdepth,$type=NULL)// 상위 카테고리 이름을 반환하는 함수
{
    global $slave_mdb;
    //$slave_mdb = new Database;

    if($type == "minishop"){
        $tb = "shop_minishop_category_info";
    }else{
        $tb = "shop_category_info";
    }

    $sql = "select c.cid,c.cname from ".$tb." c where cid LIKE '".substr($subcid,0,$subdepth*3)."%' and depth = ".($subdepth-1)."  ";

    $slave_mdb->query($sql);
    $slave_mdb->fetch(0);

    $category_string = $slave_mdb->dt[cname];

    if ($subdepth > 1){// 3depth 이상일 경우 카테고리 값을 제대로 못 불러와서 위의 것을 수정함 kbk 12/02/27		// 2차분류부터 사용가능함???
        for($i=($subdepth-1);$i>=1;$i--) {
            $sql = "select c.cid,c.cname from ".$tb." c where cid LIKE '".substr($subcid,0,($i)*3)."%' and depth = ".($i-1)."  ";
            $slave_mdb->query($sql);
            $slave_mdb->fetch(0);
            $category_string = $slave_mdb->dt[cname]." > ".$category_string;
        }
    }

    return $category_string	;
}

function GetParentCategory_2($subcid,$subdepth)// 상위 카테고리 이름을 반환하는 함수 2014-01-06 이학봉(위에 함수 제한적인 부분이 많아서 새로 추가)	//상품관리>브랜드리스트 카테고리검색시 사용됨
{
    global $slave_mdb;
    //$slave_mdb = new Database;

    $sql = "select c.cid,c.cname from ".TBL_SHOP_CATEGORY_INFO." c where cid = '".$subcid."' ";
    $slave_mdb->query($sql);
    $slave_mdb->fetch();

    for($i=0;$i<=$subdepth;$i++){
        $sql = "select c.cid,c.cname from ".TBL_SHOP_CATEGORY_INFO." c where cid like '".substr($subcid,0,($i == '0' ? '3':$i*3+3))."%' and depth='".$i."'";
        $slave_mdb->query($sql);
        $slave_mdb->fetch();
        $category_string .= $slave_mdb->dt[cname].($subdepth != $i?" > ":"");
    }

    return $category_string	;

}


function GetParentStandardCategory2($subcid,$subdepth)// 상위 카테고리 이름을 반환하는 함수 2014-01-06 이학봉(위에 함수 제한적인 부분이 많아서 새로 추가)	//상품관리>브랜드리스트 카테고리검색시 사용됨
{
    global $slave_mdb;
    //$slave_mdb = new Database;

    $sql = "select c.cid,c.cname from standard_category_info c where cid = '".$subcid."' ";
    $slave_mdb->query($sql);
    $slave_mdb->fetch();

    for($i=0;$i<=$subdepth;$i++){
        $sql = "select c.cid,c.cname from standard_category_info c where cid like '".substr($subcid,0,($i == '0' ? '3':$i*3+3))."%' and depth='".$i."'";
        $slave_mdb->query($sql);
        $slave_mdb->fetch();
        $category_string .= $slave_mdb->dt[cname].($subdepth != $i?" > ":"");
    }

    return $category_string	;

}


function fetch_sns_product($limit="5", $option = ""){
    global $slave_mdb, $sns_product_type;

    if($option == "week") {
        $time_Sselldate = time();
        $time_Eselldate = ($time_Sselldate+(84600*7));
        $timeWhere = " AND e.spei_eDate>=$time_Sselldate AND e.spei_eDate < $time_Eselldate";
    } else {
        $timeWhere = " AND e.spei_sDate<UNIX_TIMESTAMP(NOW())";
    }
    $sql = 	"SELECT p.id, p.pcode, p.pname , shotinfo,  p.company, p.sellprice, r.cid, e.spei_eDate,
			sellprice , icons,listprice,reserve_yn,icons, e.spei_discountRate, e.spei_dispDiscountRate	$reserve_sql
			FROM shop_product p, sns_product_relation r, sns_product_etcInfo e, ".TBL_COMMON_COMPANY_DETAIL." ccd, ".TBL_COMMON_SELLER_DELIVERY." csd where ccd.company_id = p.admin AND ccd.company_id = csd.company_id AND p.id = e.pid and p.id = r.pid and p.disp = 5 and p.state = 1 and product_type in (".implode(' , ',$sns_product_type).") $timeWhere
			GROUP BY p.id ORDER BY p.vieworder asc
			limit $limit";
    //echo $sql;
    $slave_mdb->query($sql);

    return $slave_mdb->fetchall();
    exit;
}


//적립금 관련 정보 가져오기
function reserveInfo($type){
    global $DOCUMENT_ROOT,$layout_config;
    include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class");
    $shmop = new Shared("reserve_rule");
    $shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/_shared/";
    $shmop->SetFilePath();
    $reserve_data = $shmop->getObjectForKey("reserve_rule");
    $reserve_data = unserialize(urldecode($reserve_data));

    $total_order_price = $reserve_data[total_order_price];
    $min_reserve_price = $reserve_data[min_reserve_price];
    $reserve_one_use_type = $reserve_data[reserve_one_use_type];
    $use_reseve_max = $reserve_data[use_reseve_max];
    $max_goods_sum_rate = $reserve_data[max_goods_sum_rate];

    return ${$type};
}

// 배송정책가져오기	2014-06-11 이학봉 수정 (테이블 컬럼수정)
function displayDeleveryText($company_id) {
    global $layout_config;
    global $slave_mdb;

    //$slave_mdb = new Database;
//	$slave_mdb->query("SELECT delivery_policy_text, company_id   FROM ".TBL_COMMON_SELLER_DELIVERY." where company_id = '$company_id' ");
//	$slave_mdb->fetch();

    $slave_mdb->query("SELECT delivery_policy_text, company_id   FROM shop_delivery_template where company_id = '$company_id' ");
    $slave_mdb->fetch();

    if(is_mobile()){
        /*
		if(file_exists($_SERVER["DOCUMENT_ROOT"].$layout_config[mall_data_root]."/images/delivery/".$company_id.".gif")){
			return "<img src = '".$layout_config[mall_data_root]."/images/delivery/".$company_id.".gif' style='width:100%'>";
		}*/
        if($slave_mdb->dt['delivery_policy_text'] != ""){
            return $slave_mdb->dt['delivery_policy_text'];
        } else {
            return $_SESSION["layout_config"]['delivery_policy_text'];
        }
    }else{
        if($slave_mdb->dt['delivery_policy_text'] != ""){
            return $slave_mdb->dt['delivery_policy_text'];
        } else {
            return $_SESSION["layout_config"]['delivery_policy_text'];
        }
    }
}

function fetch_addoptions($pid){
    global $slave_mdb;

    //$slave_mdb = new Database;
    $slave_mdb->query("SELECT dp_title, dp_desc   FROM ".TBL_SHOP_PRODUCT_DISPLAYINFO." where pid = '$pid' ");
    return $slave_mdb->fetchall("object");
    //$tpl->assign('addoptions',$addoptions);

}

function fetch_options($pid){
    global $slave_mdb;
    //$slave_mdb = new Database;
    //옵션 설정
    $slave_mdb->query("SELECT option_name , opn_ix, option_kind  FROM ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and option_use ='1' ");
    $options = $slave_mdb->fetchall();
    //print_r($options);
    return $options;
    //$tpl->assign('options',$options);
}

function fetch_brand($brand="", $cid="",$max="",$subQueryType="")
{
    global $slave_mdb;

    //$slave_mdb = new Database;
    //$slave_mdb->debug =true;

    if($max!="") $add_limit="LIMIT ".$max;
    else $add_limit="";

    if($subQueryType=="pcnt"){
        $subQuery = ",(select count(*) as total from shop_product p where mall_ix='".$_SESSION["layout_config"]['mall_ix']."' and p.brand=b.b_ix and p.state in ('1','0') and p.disp in ('1','3')) as pcnt";
    }

    if($cid){
        $sql = "SELECT b.* ".$subQuery." FROM ".TBL_SHOP_BRAND." b right join shop_brand_relation br on b.b_ix = br.b_ix where  b.disp=1 and br.cid = '$cid' ORDER BY b.brand_name asc,  b.regdate DESC ".$add_limit;
        $slave_mdb->query($sql);
    }else{

        $sql = "SELECT b.* ".$subQuery." FROM ".TBL_SHOP_BRAND." b right join shop_brand_relation br on b.b_ix = br.b_ix where  b.disp=1 ORDER BY b.vieworder asc, b.regdate ASC ".$add_limit;

        $slave_mdb->query($sql);

        //$slave_mdb->query("SELECT b.* ".$subQuery." FROM ".TBL_SHOP_BRAND." b right join shop_brand_relation br on b.b_ix = br.b_ix where  b.disp=1 ORDER BY b.brand_name asc, b.regdate DESC ".$add_limit);
    }

    $brands = $slave_mdb->fetchall();

    if(is_array($brands)){
        foreach($brands as $key => $val){
            $brands[$key]["brand_name"]=getGlobalTargetName($val["brand_name"],$val["global_binfo"],'brand_name');
        }
    }

    return $brands;//$slave_mdb->fetchall();

}

function fetch_brand_cid_character_all($cid="", $depth="0", $max="", $brand="") {//브랜드 자모음 별로 배열구성한다음 리턴
    //global $slave_mdb;

    $character=array("ㄱ","ㄴ","ㄷ","ㄹ","ㅁ","ㅂ","ㅅ","ㅇ","ㅈ","ㅊ","ㅌ","ㅋ","ㅍ","ㅎ","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");

    $return=fetch_brand_cid_all($cid, $depth, $max, $brand);

    for($i=0;$i<count($return);$i++){
        if(in_array(strtoupper(mb_substr($return[$i][brand_name_division],0,1,'UTF-8')),$character)){
            $data[strtoupper(mb_substr($return[$i][brand_name_division],0,1,'UTF-8'))][]=array("b_ix"=>$return[$i][b_ix],"b_name"=>$return[$i][brand_name]);
        }else{
            $data["기타"][]=array("b_ix"=>$return[$i][b_ix],"b_name"=>$return[$i][brand_name]);
        }

    }
    return $data;
}

function fetch_brand_cid_all($cid="", $depth="0", $max="", $brand="") {//카테고리 별 브랜드 정보. 카테고리의 하위 카테고리까지 불러옴 kbk 13/06/30
    global $slave_mdb;

    //$slave_mdb = new Database;

    if($max!="") $add_limit="LIMIT ".$max;
    else $add_limit="";

    if($cid){
        $sql = "SELECT DISTINCT b.* FROM ".TBL_SHOP_BRAND." b
		left join shop_brand_relation br on b.b_ix = br.b_ix
					where  b.disp=1 and LEFT(br.cid,".(($depth+1)*3).") = '".substr($cid,0,($depth+1)*3)."'
					ORDER BY b.brand_name asc, b.regdate DESC  ".$add_limit;
        //echo nl2br($sql);
        $slave_mdb->query($sql);

        if(!$slave_mdb->total && $depth != 0){
            $depth = $depth-1;
            return fetch_brand_cid_all($cid, $depth);
        }
    }else{
        $slave_mdb->query("SELECT b.* FROM ".TBL_SHOP_BRAND." b left join shop_brand_relation br on b.b_ix = br.b_ix where  b.disp=1 ORDER BY b.brand_name asc, b.regdate DESC ".$add_limit);
    }

    $data = $slave_mdb->fetchall();

    if(is_array($data)){
        foreach($data as $key => $val){
            $data[$key]["brand_name"]=getGlobalTargetName($val["brand_name"],$val["global_binfo"],'brand_name');
        }
    }
    return $data;
}

function fetch_recommend_brand_cid_all($cid="", $depth="0", $max="", $brand="") {//카테고리 별 브랜드 정보. 카테고리의 하위 카테고리까지 불러옴 kbk 13/06/30
//global $slave_mdb;
    global $slave_mdb;
    //$slave_mdb = new Database;

    if($max!="") $add_limit="LIMIT ".$max;
    else $add_limit="";


    if($cid){
        $sql = "SELECT DISTINCT b.*
					FROM ".TBL_SHOP_BRAND." b
					LEFT JOIN shop_display_brand_relation br ON b.b_ix=br.b_ix
					right join shop_display_brand db on br.db_ix = db.db_ix and LEFT(db.cid,".(($depth+1)*3).") = '".substr($cid,0,($depth+1)*3)."'
					right join shop_category_info ci on db.cid = ci.cid
					where b.disp=1 and LEFT(db.cid,".(($depth+1)*3).") = '".substr($cid,0,($depth+1)*3)."'
					ORDER BY br.vieworder asc ,  b.regdate DESC ".$add_limit; //and ci.depth = '".$depth."'
        //echo nl2br($sql)."<br><br>";
        $slave_mdb->query($sql);
        if(!$slave_mdb->total  && $depth != 0){
            $depth = $depth-1;//주석되어 있던 부분 품 kbk 13/07/21

            return fetch_recommend_brand_cid_all($cid, $depth);

            /*
			$sql = "SELECT DISTINCT b.*
						FROM ".TBL_SHOP_BRAND." b
						LEFT JOIN shop_display_brand_relation br ON b.b_ix=br.b_ix
						right join shop_display_brand db on br.db_ix = db.db_ix
						where b.disp=1 and LEFT(db.cid,".(($depth+1)*3).") = '".substr($cid,0,($depth+1)*3)."'
						ORDER BY br.vieworder asc ,  b.regdate DESC ".$add_limit;
			$slave_mdb->query($sql);
			*/
        }
    }
    return $slave_mdb->fetchall();

}

function fetch_brand_zone() {//카테고리 별 브랜드 정보. 카테고리의 하위 카테고리까지 불러옴 kbk 13/06/30
//global $slave_mdb;
    global $slave_mdb;
    //$slave_mdb = new Database;

    if($max!="") $add_limit="LIMIT ".$max;
    else $add_limit="";
    $sql = "SELECT  b.* ,  db.db_ix, db.db_title, dbg.group_code, dbg.group_name
				FROM ".TBL_SHOP_BRAND." b
				LEFT JOIN shop_display_brand_relation br ON b.b_ix=br.b_ix
				right join shop_display_brand db on br.db_ix = db.db_ix
				right join shop_display_brand_group dbg on db.db_ix = dbg.db_ix
				where b.disp=1 and db.display_type = 'C'
				ORDER BY br.vieworder asc , db.db_ix , dbg.group_code ".$add_limit;
    //echo nl2br($sql);
    $slave_mdb->query($sql);

    $_brand_zones = $slave_mdb->fetchall();
    for($i = 0; $i < count($_brand_zones);$i++){
        $brand_zones[$_brand_zones[$i][db_ix]][db_ix] = $_brand_zones[$i][db_ix];
        $brand_zones[$_brand_zones[$i][db_ix]][db_title] = $_brand_zones[$i][db_title];
        $brand_zones[$_brand_zones[$i][db_ix]][group_code] = $_brand_zones[$i][group_code];
        $brand_zones[$_brand_zones[$i][db_ix]][group_name] = $_brand_zones[$i][group_name];//그룹네임 추가 yws 13/12/10
        $brand_zones[$_brand_zones[$i][db_ix]][brands][$i][b_ix] = $_brand_zones[$i][b_ix];
        $brand_zones[$_brand_zones[$i][db_ix]][brands][$i][brand_name] = $_brand_zones[$i][brand_name];
        $brand_zones[$_brand_zones[$i][db_ix]][brands][$i][shotinfo] = $_brand_zones[$i][shotinfo];

    }
    return ($brand_zones);

}

function fetch_product_brand($pid)
{
//global $slave_mdb;
    global $slave_mdb;
    //$slave_mdb = new Database;

    $slave_mdb->query("SELECT b.brand_name, b.global_binfo FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_BRAND." b where p.brand = b.b_ix AND p.id = '".$pid."' ");

    $slave_mdb->fetch();

    $brand_name=getGlobalTargetName($slave_mdb->dt[brand_name],$slave_mdb->dt[global_binfo],'brand_name');

    if($slave_mdb->dt[brand_name] != "") return "[".$brand_name."]";

}

function fetch_company_name($company_id)
{
    global $slave_mdb;
    //$slave_mdb = new Database;
    $slave_mdb->query("SELECT com_name FROM ".TBL_COMMON_COMPANY_DETAIL." where company_id='".$company_id."' ");
    $slave_mdb->fetch();
    return $slave_mdb->dt[com_name];

}


function fetch_company_shopname($company_id)
{
    global $slave_mdb;
    //$slave_mdb = new Database;

    $slave_mdb->query("SELECT shop_name FROM ".TBL_COMMON_SELLER_DETAIL." where company_id='".$company_id."' ");
    $slave_mdb->fetch();
    return $slave_mdb->dt[shop_name];

}

/**
 * sehyun 20170807 goods_view.php 사업자 정보
 */
function fetch_company_info($company_id){
    global $slave_mdb;

    $sql = "select ccd.com_name, ccd.com_ceo, ccd.com_number, ccd.online_business_number
                 , ccd.com_phone, ccd.com_email, ccd.com_zip, ccd.com_addr1, ccd.com_addr2, ccd.com_div
              from common_company_detail ccd
		         , common_seller_detail csd 
		     where ccd.company_id = csd.company_id
		       and ccd.company_id = '".$company_id."'";
    $slave_mdb->query($sql);
    $company_info = $slave_mdb->fetch();
    return $company_info;
}

function fetch_seller_goods($company_id) {
    global $slave_mdb;


    $sql = sprintf("SELECT sd.company_id, sd.shop_name, p.id as pid, p.pname, p.sellprice
						  FROM common_seller_detail sd INNER JOIN shop_minishop_product_group pg on sd.company_id = pg.company_id
						   INNER JOIN shop_minishop_product_relation pr on sd.company_id = pr.company_id and pg.group_code = pr.group_code
						   INNER JOIN shop_product p on pr.pid = p.id
						 WHERE topseller_display = 1
						   AND pg.group_code = 4
						   AND sd.company_id = '%s'
					  ORDER BY p.vieworder LIMIT 2;
						", $company_id);
    $slave_mdb->query($sql);
    $goods_infos = $slave_mdb->fetchall();


    return $goods_infos;
}

function fetch_table($table, $size=5)
{
    global $slave_mdb;


    $slave_mdb->query("SELECT * FROM $table  limit 0,$size");

    return $slave_mdb->fetchall("object");
    exit;
}


function fetch_bbs_group_list($group="고객센타")
{
    global $slave_mdb;

    if($group=="커뮤니티") {
        $slave_mdb->query("SELECT bmc.board_name, bmc.board_ename FROM bbs_manage_config bmc, bbs_group bg where bmc.board_group = bg.div_ix and div_name = '$group' and board_ename NOT IN ('after','local_news')");//구매후기,지역뉴스 제외 kbk
    } else {
        $slave_mdb->query("SELECT bmc.board_name, bmc.board_ename FROM bbs_manage_config bmc, bbs_group bg where bmc.board_group = bg.div_ix and div_name = '$group'");
    }

    return $slave_mdb->fetchall();
    exit;
}

function fetch_bbs($table="bbs_notice", $size=5)
{
    global $slave_mdb;
    if($table=="bbs_qna") {//1:1문의의 경우 자신의 것만 나오도록 kbk 13/07/22
        if($_SESSION["user"]["code"]!="") $add_query=" WHERE mem_ix='".$_SESSION["user"]["code"]."' ";
    }
    if($slave_mdb->dbms_type == "oracle"){
        $sql = "SELECT ".$table.".*, TO_DATE(regdate,'YYYY.MM.DD') AS day, case when regdate > sysdate - 3 then 1 else 0 end as new FROM $table ".$add_query." ORDER BY regdate DESC limit 0,$size";// where rownum < 5  게시글의 갯수를 4개로 제한두어서 그 부분을 빼고 limit 0,$size 이 부분을 추가함 kbk 13/03/20
    }else{
        $sql = "SELECT ".$table.".*, DATE_FORMAT(regdate,'%Y.%m.%d') AS day, case when regdate > DATE_SUB(now(), interval 3 day) then 1 else 0 end as new FROM $table ".$add_query." ORDER BY regdate DESC limit 0,$size";
    }
    $slave_mdb->query($sql);

    return $slave_mdb->fetchall();
    exit;
}


function fetch_bbs_best($table="bbs_notice", $size=5)
{
    global $slave_mdb;
    if($table!="bbs_faq") $add_query=" where bbs_file_1 != '' ";//faq 테이블엔  bbs_file_1 이 없기에 조건을 따로 줌 kbk 13/12/30
    $slave_mdb->query("SELECT *, DATE_FORMAT(regdate,'%Y.%m.%d') AS day, case when regdate > DATE_SUB(now(), interval 3 day) then 1 else 0 end as new FROM $table ".$add_query." ORDER BY bbs_hit DESC limit 0,$size");

    return $slave_mdb->fetchall();
    exit;
}

function get_bbs_img($table, $bbs_ix)
{
    global $slave_mdb;
    $slave_mdb->query("SELECT * FROM bbs_".$table." where bbs_ix='".$bbs_ix."'");
    $slave_mdb->fetch();
    $bbs_file_1 = $slave_mdb->dt['bbs_file_1'];

    return $bbs_file_1;
    //exit;
}

function fetch_product($type="", $pcnt=5, $multi_line=true)
{
    global $slave_mdb, $layout_config, $user,$DOCUMENT_ROOT, $shop_product_type;
    //적립금 기본 정책을 가져옴
    include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class");
    $shmop = new Shared("reserve_rule");
    $reserve_data = $shmop->getObjectForKey("reserve_rule");
    $reserve_data = unserialize(urldecode($reserve_data));

    if($reserve_data[reserve_use_yn] == "Y"){
        $reserve_sql = " ,case when p.reserve_yn = 'N' then round(sellprice*(".$reserve_data[goods_reserve_rate]."/100)) else round(sellprice*(reserve_rate/100)) end as reserve";
    }
    if($type == ""){
        $slave_mdb->query("SELECT p.id,p.pname, r.rid,r.cid,p.stock,p.stock_use_yn,icons, listprice,sellprice $reserve_sql  FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and p.disp in (1,3) and p.sellprice != 0 and p.state = 1 and product_type in (".implode(' , ',$shop_product_type).") and r.basic = 1 order by rand() limit 0,$pcnt");
    }

    $products = $slave_mdb->fetchall();

    if($products){
        foreach ($products as $key => $sub_array) {
            $select_ = array("icons_list"=>explode(";",$sub_array[icons]));
            array_insert($sub_array,14,$select_);
            $products[$key] = $sub_array;
        }
    }
    return $products;
    exit;

}
//한주간 많이 팔린 상품 리스트
function fetch_product_week($start,$size){
    global  $vdate ;
    global $slave_mdb;
    //$slave_mdb = new MySQl;

    $vdate = date("Y-m-d H:i:s", time());
    $vyesterday = date("Y-m-d H:i:s", time()-84600);
    $voneweekago = date("Y-m-d H:i:s", time()-84600*7);


    $vdate_str = " and od.regdate between '$voneweekago' and '$vdate'";

    $sql = "select * from shop_product p
				left join shop_order_detail od on od.status != 'SR' and p.id = od.pid $vdate_str
				where p.id = od.pid order by p.order_cnt desc
				limit $start, $size " ;

    //$sql = 	"SELECT banner_img,banner_ix,banner_link,banner_target,banner_width,banner_height FROM shop_bannerinfo where banner_ix = '".$banner_ix."' ";
    /*$sql = "select b.banner_ix, banner_img,banner_link,banner_target,banner_width,banner_height,
			IFNULL(sum(bc.ncnt),0) as ncnt
			from shop_bannerinfo b left join logstory_banner_click bc
			on b.banner_ix = bc.banner_ix $vdate_str
			where b.banner_page = '$page'
			group by b.banner_ix
			limit 0, $size  ";*/
    //echo $sql;

    //echo $sql;
    $slave_mdb->query($sql);
    return $slave_mdb->fetchall();
}



function fetch_product_category($orderby_type, $cid, $depth=1, $size=5,$start=0)
{
    global $slave_mdb;

    if($cid){
        $where = "where p.id = r.pid and r.cid LIKE '".substr($cid,0,($depth+1)*3)."%'  and p.disp in (1,3)  ";
    }else{
        $where = "where p.id = r.pid and p.disp in (1,3)  ";
    }

    if($orderby_type == "BEST_SELL"){
        $orderby = " order_cnt DESC ";
    }else if($orderby_type == "NEW"){
        $orderby = " p.regdate DESC ";
    }else if($orderby_type == "VIEW"){
        $orderby = " view_cnt DESC ";
    }else if($orderby_type == "RANDOM"){
        $orderby = " rand() ";
    }else{
        $orderby = " p.regdate DESC ";
    }
    if($start == 0){
        $sql = 	"SELECT p.id, p.pcode, p.pname,p.brand,p.brand_name, sellprice, sellprice as non_member_price, case when p.shotinfo = '' then '-' else  shotinfo end as shotinfo , p.reserve, p.company, r.rid,r.cid, $depth as depth,icons
		FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r $where
		order by $orderby
		limit 0,$size";
    }else if($start != 0){
        $sql = 	"SELECT p.id, p.pcode, p.pname,p.brand,p.brand_name, sellprice, sellprice as non_member_price, case when p.shotinfo = '' then '-' else  shotinfo end as shotinfo ,p.reserve, p.company, r.rid,r.cid, $depth as depth ,icons
		FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r $where
		order by $orderby
		limit $start,$size";
    }

    $slave_mdb->query($sql);
    $products = $slave_mdb->fetchall();

    if(is_array($products))
    {
        foreach ($products as $key => $sub_array) {
            $select_ = array("icons_list"=>explode(";",$sub_array[icons]));
            array_insert($sub_array,14,$select_);
            $products[$key] = $sub_array;
        }
    }
    return $products;
    exit;
}


function fetch_product_seller($orderby_type, $company_id,$depth=1, $size=5,$start=0)
{
    global $slave_mdb;

    if($company_id){
        $where = "where p.id = r.pid and p.company_id = '".$company_id."'  and p.disp in (1,3)  ";
    }else{
        $where = "where p.id = r.pid and p.disp in (1,3)  ";
    }

    if($orderby_type == "BEST_SELL"){
        $orderby = " order_cnt DESC ";
    }else if($orderby_type == "NEW"){
        $orderby = " p.regdate DESC ";
    }else if($orderby_type == "VIEW"){
        $orderby = " view_cnt DESC ";
    }else if($orderby_type == "RANDOM"){
        $orderby = " rand() ";
    }else{
        $orderby = " p.regdate DESC ";
    }
    if($start == 0){
        $sql = 	"SELECT p.id, p.pcode, p.pname,p.brand,p.brand_name, sellprice, sellprice as non_member_price, case when p.shotinfo = '' then '-' else  shotinfo end as shotinfo , p.reserve, p.company, r.rid,r.cid, $depth as depth,icons
		FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r $where
		order by $orderby
		limit 0,$size";
    }else if($start != 0){
        $sql = 	"SELECT p.id, p.pcode, p.pname,p.brand,p.brand_name, sellprice, sellprice as non_member_price, case when p.shotinfo = '' then '-' else  shotinfo end as shotinfo ,p.reserve, p.company, r.rid,r.cid, $depth as depth ,icons
		FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r $where
		order by $orderby
		limit $start,$size";
    }

    $slave_mdb->query($sql);
    $products = $slave_mdb->fetchall();

    if(is_array($products))
    {
        foreach ($products as $key => $sub_array) {
            $select_ = array("icons_list"=>explode(";",$sub_array[icons]));
            array_insert($sub_array,14,$select_);
            $products[$key] = $sub_array;
        }
    }
    return $products;
    exit;
}

function fetch_category($company_id = "",$depth=""){
    global $slave_mdb, $shop_product_type;
    if($company_id == ""){
        $slave_mdb->query("SELECT * FROM ".TBL_SHOP_CATEGORY_INFO." where category_use = 1 and depth = 0 order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");
    }else{

        $sql = "select ci.cname, ci.cid, ci.depth, goods_cnt from ".TBL_SHOP_CATEGORY_INFO." ci,
				(SELECT substr(ci.cid,1,3) as cid , ci.depth, ci.cname, count(*) goods_cnt
				FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_SHOP_CATEGORY_INFO." ci
				where p.id = r.pid and p.disp = 1 and p.state = 1 and r.cid = ci.cid and product_type in (".implode(' , ',$shop_product_type).")
				and p.admin = '".$company_id."'
				and ci.category_use = 1
				group by cid order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5) seller_ci
				where seller_ci.cid = substr(ci.cid,1,3) and ci.depth = 0 ";

        //			and ci.depth = 0
        //echo nl2br($sql);
        $slave_mdb->query($sql);
    }
    $categorys = $slave_mdb->fetchall();;
    //print_r($categorys);
    return $categorys;
    exit;
}



function fetch_normal_category($company_id = ""){//GNB에서 전체 메뉴 보기할 때 스페셜 카테고리 안보이게 함 KBK 11-10-04
    global $slave_mdb, $shop_product_type;

    if($company_id == ""){
        $slave_mdb->query("SELECT * FROM ".TBL_SHOP_CATEGORY_INFO." where category_use = 1 and depth = 0 AND LEFT(cid,3)!='006' AND LEFT(cid,3)!='007' AND LEFT(cid,3)!='008' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");//GNB에서 전체 메뉴 보기할 때 스페셜 카테고리 안보이게 함 KBK 11-10-04
    }else{
        $sql = "SELECT ci.cid , ci.depth, ci.cname, count(*) goods_cnt FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_SHOP_CATEGORY_INFO." ci
			where p.id = r.pid and p.disp = 1 and p.state = 1 and r.cid = ci.cid and product_type in (".implode(' , ',$shop_product_type).") and p.admin = '$company_id'
			and ci.category_use = 1  group by ci.cid order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5  ";
        //echo $sql;and ci.depth = 0
        $slave_mdb->query($sql);
    }
    $categorys = $slave_mdb->fetchall();;
    //print_r($categorys);
    return $categorys;
    exit;
}



function fetch_event($company_id = ""){
    global $slave_mdb, $shop_product_type;
    if($company_id == ""){
        $slave_mdb->query("SELECT * FROM ".TBL_SHOP_EVENT." where disp = 1 order by regdate ");
    }else{
        $sql = "SELECT e.event_ix , e.event_title, e.event_use_sdate, e.event_use_edate, count(*) goods_cnt FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." epr, ".TBL_SHOP_EVENT." e
			where p.id = epr.pid and p.disp = 1 and p.state = 1 and epr.event_ix = e.event_ix and p.product_type in (".implode(' , ',$shop_product_type).") and p.admin = '$company_id'
			and event_use_sdate <= UNIX_TIMESTAMP(".date("Ymd").") and event_use_edate >= UNIX_TIMESTAMP(".date("Ymd").")
			group by e.event_ix order by e.event_use_edate desc  ";
        //echo nl2br($sql);
        $slave_mdb->query($sql);
    }
    $events = $slave_mdb->fetchall();;
    //print_r($categorys);
    return $events;
    exit;
}


function getEventProductInfo($company_id, $event_ix, $type="data"){
    global $user, $HTTP_URL, $shop_product_type;
    global $slave_mdb;
//$slave_mdb = new Database;

    $sql = "SELECT p.id, p.pname, p.sellprice FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." epr
		where p.id = epr.pid and p.disp = 1 and p.state = 1 and epr.event_ix = '".$event_ix."' and p.product_type in (".implode(' , ',$shop_product_type).") and p.admin = '$company_id'
		group by p.id order by epr.vieworder asc  ";
    //echo nl2br($sql);
    $slave_mdb->query($sql);

    //echo $sql;
    if($type == "data"){
        $slave_mdb->query($sql);
        $datas = $slave_mdb->fetchall();
        //print_r($datas);
        return $datas;
    }else{
        $slave_mdb->query($sql);

        for($i=0;$i < $slave_mdb->total;$i++){
            $slave_mdb->fetch($i);
            if($slave_mdb->total == 1){
                $mstring .= " <a href='goods_list.php?cid=".$slave_mdb->dt[cid]."&depth=".$slave_mdb->dt[depth]."'><b>".$slave_mdb->dt[cname]."</b></a>";
            }else{
                if($i == 0){
                    $mstring .= "<a href='goods_list.php?cid=".$slave_mdb->dt[cid]."&depth=".$slave_mdb->dt[depth]."'>".($type == "color" ? "":"").$slave_mdb->dt[cname].($type == "color" ? "":"")."</a>";
                }else if($i + 1 == $slave_mdb->total){
                    $mstring .= " > <a href='goods_list.php?cid=".$slave_mdb->dt[cid]."&depth=".$slave_mdb->dt[depth]."'><b>".$slave_mdb->dt[cname]."</b></a>";
                }else{
                    $mstring .= " > <a href='goods_list.php?cid=".$slave_mdb->dt[cid]."&depth=".$slave_mdb->dt[depth]."'>".($type == "color" ? "":"").$slave_mdb->dt[cname].($type == "color" ? "":"")."</a>";
                }
            }
        }
        return $mstring;
    }
}



function fetch_snscategory(){
    global $slave_mdb;
    $slave_mdb->query("SELECT * FROM ".TBL_SNS_CATEGORY_INFO." where category_use = 1 and depth = 0 order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");

    return $slave_mdb->fetchall();
    exit;
}

function sns_subcategorys($cid, $depth="1"){
    global $slave_mdb;
    //$slave_mdb = new Database;
    /*if($depth == 1){
		$sql = "SELECT * FROM ".TBL_SHOP_CATEGORY_INFO." where depth in (2,3) and cid LIKE '".substr($cid,0,6)."%' and category_use = '1' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5";
		//echo $sql;
		$slave_mdb->query($sql);
	}else{
		$sql = "SELECT *,".($depth+1)." FROM ".TBL_SHOP_CATEGORY_INFO." where depth in (".($depth+1).") and cid LIKE '".substr($cid,0,($depth+1)*3)."%' and category_use = '1' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5";
		//echo $sql;
		$slave_mdb->query($sql);
	}*/
    //echo $depth."<br>";
    $sql = "SELECT *  FROM ".TBL_SNS_CATEGORY_INFO."
				where depth in (".($depth+1).")
				and cid LIKE '".substr($cid,0,($depth+1)*3)."%'
				and category_use = '1'
				order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5";// 조건절을 뺌 kbk 12/01/03 //,".($depth+1)." as r_depth 2012-10-29 신훈식 뺌
    //echo nl2br($sql);
    $slave_mdb->query($sql);
    $categorys = $slave_mdb->fetchall();
//print_r($categorys);
    return $categorys;//$slave_mdb->fetchall();

}

function getCategoryPath($cid, $depth='-1',$type=""){
    global $user, $HTTP_URL;
    global $slave_mdb;
//$slave_mdb = new Database;
    if($depth == '0' && $cid){
        if($cid == '000000000000000'){
            $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where cid LIKE '".substr($cid,0,3)."%' order by depth asc";
        }else{
            $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where depth = 0 and cid LIKE '".substr($cid,0,3)."%' order by depth asc";
        }

    }else if($depth == '1' && $cid){
        $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where depth <= 1 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%'))  order by depth asc";
    }else if($depth == '2' && $cid){
        $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where depth <= 2 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%'))  order by depth asc";
    }else if($depth == '3' && $cid){
        $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where depth <= 3 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%'))  order by depth asc";
    }else if($depth == '4' && $cid){
        $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
        //$sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where (depth = 0 and cid '".substr($cid,0,3)."%') or (depth = 0 and cid '".substr($cid,0,3)."%')LIKE cid LIKE '".substr($cid,0,3*($depth+1))."%' and depth <= '$depth' order by depth asc";
    }else{
        return "상세정보";
    }
    //echo $sql;
    if($type == "data"){
        $slave_mdb->query($sql);
        $datas = $slave_mdb->fetchall();

        if(is_array($datas)){
            foreach($datas as $key => $val){
                $datas[$key]["cname"]=getGlobalTargetName($val["cname"],$val["global_cinfo"],'cname');
            }
        }
        return $datas;

    }else{
        $slave_mdb->query($sql);

        for($i=0;$i < $slave_mdb->total;$i++){
            $slave_mdb->fetch($i);
            if($slave_mdb->total == 1){
                $mstring .= " <a href='goods_list.php?cid=".$slave_mdb->dt[cid]."&depth=".$slave_mdb->dt[depth]."'><b>".$slave_mdb->dt[cname]."</b></a>";
            }else{
                if($i == 0){
                    $mstring .= "<a href='goods_list.php?cid=".$slave_mdb->dt[cid]."&depth=".$slave_mdb->dt[depth]."'>".($type == "color" ? "":"").$slave_mdb->dt[cname].($type == "color" ? "":"")."</a>";
                }else if($i + 1 == $slave_mdb->total){
                    $mstring .= "<em>&gt;</em> <a href='goods_list.php?cid=".$slave_mdb->dt[cid]."&depth=".$slave_mdb->dt[depth]."'><b>".$slave_mdb->dt[cname]."</b></a>";
                }else{
                    $mstring .= "<em>&gt;</em> <a href='goods_list.php?cid=".$slave_mdb->dt[cid]."&depth=".$slave_mdb->dt[depth]."'>".($type == "color" ? "":"").$slave_mdb->dt[cname].($type == "color" ? "":"")."</a>";
                }
            }
        }
        return $mstring;
    }
}


function getSubCategoryInfo($cid, $depth='-1',$type=""){
    global $user, $HTTP_URL;
    global $slave_mdb;
    //$slave_mdb = new Database;
    if($depth && $cid){
        $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where cid LIKE '".substr($cid,0,($depth)*3)."%' and depth = '$depth' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5, depth asc";
    }else{
        return "상세정보";
    }
    //echo $sql;
    if($type == "data"){
        $slave_mdb->query($sql);
        $datas = $slave_mdb->fetchall();
        //print_r($datas);
        return $datas;
    }else{
        $slave_mdb->query($sql);

        for($i=0;$i < $slave_mdb->total;$i++){
            $slave_mdb->fetch($i);
            if($slave_mdb->total == 1){
                $mstring .= " <a href='goods_list.php?cid=".$slave_mdb->dt[cid]."&depth=".$slave_mdb->dt[depth]."'><b>".$slave_mdb->dt[cname]."</b></a>";
            }else{
                if($i == 0){
                    $mstring .= "<a href='goods_list.php?cid=".$slave_mdb->dt[cid]."&depth=".$slave_mdb->dt[depth]."'>".($type == "color" ? "":"").$slave_mdb->dt[cname].($type == "color" ? "":"")."</a>";
                }else if($i + 1 == $slave_mdb->total){
                    $mstring .= " > <a href='goods_list.php?cid=".$slave_mdb->dt[cid]."&depth=".$slave_mdb->dt[depth]."'><b>".$slave_mdb->dt[cname]."</b></a>";
                }else{
                    $mstring .= " > <a href='goods_list.php?cid=".$slave_mdb->dt[cid]."&depth=".$slave_mdb->dt[depth]."'>".($type == "color" ? "":"").$slave_mdb->dt[cname].($type == "color" ? "":"")."</a>";
                }
            }
        }
        return $mstring;
    }
}

function getCategoryPathmywgc ($cid, $depth='-1',$type=""){	//건마스터 네비를 위한 추가함수
    global $user, $HTTP_URL;
    global $slave_mdb;
//$slave_mdb = new Database;
    if($depth == '0' && $cid){
        $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where depth = 0 and cid LIKE '".substr($cid,0,3)."%' order by depth asc";
    }else if($depth == '1' && $cid){
        $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where depth <= 1 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%'))  order by depth asc";
    }else if($depth == '2' && $cid){
        $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where depth <= 2 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%'))  order by depth asc";
    }else if($depth == '3' && $cid){
        $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where depth <= 3 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%'))  order by depth asc";
    }else if($depth == '4' && $cid){
        $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
        //$sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where (depth = 0 and cid '".substr($cid,0,3)."%') or (depth = 0 and cid '".substr($cid,0,3)."%')LIKE cid LIKE '".substr($cid,0,3*($depth+1))."%' and depth <= '$depth' order by depth asc";
    }else{
        return "상세정보";
    }

    $URL=$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $geturl=str_replace('.html','',$URL);
    $geturl = explode("/",$geturl);

    if($type == "data"){
        $slave_mdb->query($sql);
        $datas = $slave_mdb->fetchall();
        return $datas;
    }else{
        $slave_mdb->query($sql);

        for($i=0;$i < $slave_mdb->total;$i++){
            $slave_mdb->fetch($i);
            if($slave_mdb->total == 1){
                $mstring .= " <a href ='http://".$geturl[0]."'><li class='sub_dot_img01' style='color:#666666;'>Home</li></a>";
                $mstring .= " <a href='goods_list.php?cid=".$slave_mdb->dt[cid]."&depth=".$slave_mdb->dt[depth]."'><li class='sub_dot_img02' style='color:#84bccd;'>".$slave_mdb->dt[cname]."</li></a>";
            }else{
                if($i == 0){
                    $mstring .= " <a href ='http://".$geturl[0]."'><li class='sub_dot_img01' style='color:#666666;'>Home</li></a>";
                    $mstring .= "<a href='goods_list.php?cid=".$slave_mdb->dt[cid]."&depth=".$slave_mdb->dt[depth]."'><li class='sub_dot_img02' style='color:#84bccd;'>".$slave_mdb->dt[cname]."</li></a>";
                }else if($i + 1 == $slave_mdb->total){

                    $mstring .= "<a href='goods_list.php?cid=".$slave_mdb->dt[cid]."&depth=".$slave_mdb->dt[depth]."'><li class='sub_dot_img02' style='color:#666666;'>".$slave_mdb->dt[cname]."</li></a>";
                }else{

                    $mstring .= "<a href='goods_list.php?cid=".$slave_mdb->dt[cid]."&depth=".$slave_mdb->dt[depth]."'><li class='sub_dot_img02' style='color:#666666;'>".$slave_mdb->dt[cname]."</li></a>";
                }
            }
        }
        return $mstring;
    }
}


function getSubCategoryInfomywgc ($cid, $depth='-1',$type=""){	// 건마스터를 위한 네비함수 추가
    global $user, $HTTP_URL;
    global $slave_mdb;

    //$slave_mdb = new Database;

    $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where  cid LIKE '".substr($cid,0,3)."%' order by depth asc";
    $slave_mdb->query($sql);

    $all_array = $slave_mdb->fetchall();
    $all_count = count($all_array);

    for($i=0;$i<$all_count;$i++){
        if($i >0){
            $all_depth[] = $all_array[$i][depth];	//카테고리 모든 depth 배열에 담고 중복제거
        }
    }

    if(count($all_depth) >0){

        $depth_array = array_unique($all_depth);	//중복제거
        $tmpArr2= implode("//",$depth_array);
        $depth_array= explode("//",$tmpArr2);

        for($j = 0 ; $j < count($depth_array); $j++){

            $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where depth='1' and cid LIKE '".substr($cid,0,3)."%' order by cid asc";
            $slave_mdb->query($sql);

            $detph_1 = $slave_mdb->fetchall(); //depth = 1
            //print_r ($detph_1);
            $mstring = "<tr>";
            for($k = 0;$k<count($detph_1);$k++){

                $mstring .="<th style='text-align:left; height:25px;'><span style='padding-left:17px; color:#818181; font-weight:bold;'><a href='goods_list.php?cid=".$detph_1[$k][cid]."&depth=".$detph_1[$k][depth]."'>".$detph_1[$k][cname]."</a></span></th>";

                $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where depth='2' and cid LIKE '".substr($detph_1[$k][cid],0,6)."%' order by depth asc";

                $slave_mdb->query($sql);

                $depth_2 = $slave_mdb->fetchall();
                $mstring .="<td>
							<div class='depth_03'>
								<ul>";
                for($a = 0; $a<count($depth_2); $a++){

                    $mstring .= "<li><a href='goods_list.php?cid=".$depth_2[$a][cid]."&depth=".$depth_2[$a][depth]."'>".$depth_2[$a][cname]."</a></li>";

                    if($a != count($depth_2)-1){
                        $mstring .= "<li>|</li>";
                    }
                }
                $mstring .= "	</ul>
							</div>
						</td>";
                $mstring .= "</tr>";
            }
        }
    }else{
        $mstring = "<tr>
				<th style='text-align:left; height:25px;'><span style='padding-left:17px; color:#818181; font-weight:bold;'>전체보기</span></th>
				<td>
					<div class='depth_03'>
						<ul>
							<li></li>
						</ul>
					</div>
				</td>
			</tr>";
    }

    return $mstring;

}

function getCategoryPath2($cid, $depth='-1'){
    global $user, $HTTP_URL;
    global $slave_mdb;
//	$slave_mdb = new Database;
    if($depth == '0' && $cid){
        $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where depth = 0 and cid LIKE '".substr($cid,0,3)."%' order by depth asc";
    }else if($depth == '1' && $cid){
        $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where depth <= 1 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%'))  order by depth asc";
    }else if($depth == '2' && $cid){
        $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where depth <= 2 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%'))  order by depth asc";
    }else if($depth == '3' && $cid){
        $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where depth <= 3 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%'))  order by depth asc";
    }else if($depth == '4' && $cid){
        $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
        //$sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where (depth = 0 and cid '".substr($cid,0,3)."%') or (depth = 0 and cid '".substr($cid,0,3)."%')LIKE cid LIKE '".substr($cid,0,3*($depth+1))."%' and depth <= '$depth' order by depth asc";
    }else{
        return "상세정보";
    }

    $slave_mdb->query($sql);

    for($i=0;$i < $slave_mdb->total;$i++){
        $slave_mdb->fetch($i);

        if($i == 0){
            $mstring .= "<a href='goods_list.php?cid=".$slave_mdb->dt[cid]."&depth=".$slave_mdb->dt[depth]."'>전체보기</a>";
        }else{
            $mstring .= " > <a href='goods_list.php?cid=".$slave_mdb->dt[cid]."&depth=".$slave_mdb->dt[depth]."'>전체보기</a>";
        }
    }
    return $mstring;
}

function getCategoryName($cid){
    global $slave_mdb;
    //$slave_mdb = new Database;

    $slave_mdb->query("SELECT catimg FROM ".TBL_SHOP_CATEGORY_INFO." where category_use = 1 and depth = 0 and cid LIKE '".substr($cid,0,3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");

    $slave_mdb->fetch();
    $mstring = "<img src='".$_SESSION["layout_config"]["mall_data_root"]."/templet/".$_SESSION["layout_config"]["mall_use_templete"]."/images/category/".$slave_mdb->dt[catimg]."'>";
    return $mstring;
}

function getCategorySub($cid){
    global $slave_mdb;
    //$slave_mdb = new Database;

    $sql = "SELECT * FROM ".TBL_SHOP_CATEGORY_INFO." where category_use = 1 and depth = 1 and cid LIKE '".$cid."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5";
    $slave_mdb->query($sql);
    //echo $sql;
    return $slave_mdb->fetchall();

    //return $mstring;
}


function fetch_product_category_main($orderby_type, $cid, $depth=1, $size=5,$start=0)
{
    global $slave_mdb;

    if($cid){
        $where = "where p.id = r.pid and r.cid LIKE '".substr($cid,0,($depth+1)*3)."%'  and p.disp in (1,3) and main='1' ";
    }else{
        $where = "where p.id = r.pid and p.disp in (1,3) and main='1' ";
    }

    if($orderby_type == "BEST_SELL"){
        $orderby = " order_cnt DESC ";
    }else if($orderby_type == "NEW"){
        $orderby = " p.regdate DESC ";
    }else if($orderby_type == "VIEW"){
        $orderby = " view_cnt DESC ";
    }else if($orderby_type == "RANDOM"){
        $orderby = " rand() ";
    }else{
        $orderby = " p.mainorder ";
    }
    $new_sql =  ", case when p.regdate > DATE_SUB(now(), interval 1 DAY) then 1 else 0 end as new_icon";
    if($start == 0){
        $sql = 	"SELECT p.id,p.stock,p.sale_sub, p.brand,p.pcode,p.listprice, p.pname, sellprice as non_member_price, shotinfo , p.reserve, p.company, r.rid,r.cid, $depth as depth ,mainorder,reserve_rate,state,icons $new_sql
		FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r $where
		order by $orderby
		limit 0,$size";//group by id
        //return $sql;
        //echo $sql;
    }else if($start != 0){
        $sql = 	"SELECT p.id,p.stock,p.sale_sub, p.brand,p.pcode, p.pname, sellprice as non_member_price,shotinfo ,  p.reserve, p.company, r.rid,r.cid, $depth as depth ,reserve_rate,state,icons $new_sql
		FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r $where
		order by mainorder
		limit $start,$size";//group by id
    }
    $slave_mdb->query($sql);
    $products = $slave_mdb->fetchall();
    foreach ($products as $key => $sub_array) {
        $select_ = array("icons_list"=>explode(";",$sub_array[icons]));
        array_insert($sub_array,14,$select_);
        $products[$key] = $sub_array;
    }
    return $products;
    exit;
}

function getHotstuff($type,$div_ix){
    global $slave_mdb;
    //$slave_mdb = new Database;
    $today = date('Ymd');
    $sql = "select md_ix from shop_recommend where md_use_sdate <= '".$today."' and md_use_edate >= '".$today."' and disp ='1' and div_ix = '$div_ix' order by regdate asc";

    //return $sql;
    $slave_mdb->query($sql);
    //echo $sql;
    if($type == "1"){
        return $slave_mdb->fetchall();
    }else{
        return $slave_mdb->total - 1;
    }
    exit;
}

function getHotstuffProduct($div_ix,$size,$width,$image_size){

    global $slave_mdb,$DOCUMENT_ROOT;
    //$slave_mdb = new Database;
    $rdb = new Database;
    $rdb->slave_db_setting();

    $today = date('Ymd');
    $sql = "select md_ix from shop_recommend where md_use_sdate <= '".$today."' and md_use_edate >= '".$today."' and disp = '1' and div_ix = '$div_ix' order by regdate asc ";
    //return $sql;
    $slave_mdb->query($sql);
    $hotstuffsize = $slave_mdb->total;

    session_register("hotstuffsize");
    session_start();
    //echo $DOCUMENT_ROOT;
    for($i=0; $i<$slave_mdb->total;$i++){
        $slave_mdb->fetch($i);

        $sql = "select p.id,p.brand,p.pname,p.sellprice,p.listprice from shop_recommend_product_relation mp,shop_product p where mp.md_ix = '".$slave_mdb->dt[md_ix]."' and mp.pid = p.id and disp = '1' order by mp.vieworder limit $size ";
        //echo $sql;
        $slave_mdb->query($sql);

        if($i == 0){
            $mstring = "<table align='center' border=0 cellpadding='0' cellspacing='0' width='$width' ID='TABS_TBL".$div_ix."_".$i."' style=' FILTER: progid:DXImageTransform.Microsoft.GradientWipe(GradientSize=1.0,wipestyle=0,motion=forward);'>";
            $mstring .= "<tr><td width='100%' style='padding-left:13px'>";
            for($j=0;$j<$slave_mdb->total;$j++){
                $slave_mdb->fetch($j);
                $sql = "select r.cid,c.depth from shop_product_relation r,shop_category_info c where r.pid = '".$slave_mdb->dt[id]."' and c.cid = r.cid";
                $rdb->query($sql);
                $rdb->fetch();
                $mstring .= "<table border=0 cellpadding=0 cellspacing=0 style='table-layout: fixed;width:122px;height:177px;float:left;'><tr>";
                $mstring .="<td width='112' align='center' height='120'><a href='/shop/goods_view.php?id=".$slave_mdb->dt[id]."&cid=".$rdb->dt[cid]."&depth=".$rdb->dt[depth]."&b_ix=".$slave_mdb->dt[brand]."' onFocus='this.blur();'><img src='/data/suns/images/product/ms_".$slave_mdb->dt[id].".gif' width='$image_size' height='$image_size' style='border:1px solid #e9e9e9'></a></td>";
                $mstring .= "</tr><tr><td align='center' valign='top'  width='$image_size'>".cut_str($slave_mdb->dt[pname],10)."<br>".($slave_mdb->dt[listprice] == "0" || $slave_mdb->dt[listprice] == $slave_mdb->dt[sellprice] ? number_format($slave_mdb->dt[sellprice]):"<s>".number_format($slave_mdb->dt[listprice])."원</s><br><span style='color:red'>".number_format($slave_mdb->dt[sellprice]))."원</span></td></tr></table>";
            }
            $mstring .= "</td></tr></table>";
        }else{
            $mstring .= "<table align='center' border=0 cellpadding='0' cellspacing='0' width='$width' ID='TABS_TBL".$div_ix."_".$i."' style=' display:none;FILTER: progid:DXImageTransform.Microsoft.GradientWipe(GradientSize=1.0,wipestyle=0,motion=forward);'>";
            $mstring .= "<tr><td width='100%' style='padding-left:13px'>";
            for($j=0;$j<$slave_mdb->total;$j++){
                $slave_mdb->fetch($j);
                $sql = "select r.cid,c.depth from shop_product_relation r,shop_category_info c where r.pid = '".$slave_mdb->dt[id]."' and r.cid not LIKE '008%' and c.cid = r.cid";
                $rdb->query($sql);
                $rdb->fetch();
                $mstring .= "<table border=0 cellpadding=0 cellspacing=0 style='table-layout: fixed;width:122px;height:177px;float:left;'><tr>";
                $mstring .="<td width='112' align='center' height='120'><a href='/shop/goods_view.php?id=".$slave_mdb->dt[id]."&cid=".$rdb->dt[cid]."&depth=".$rdb->dt[depth]."&b_ix=".$slave_mdb->dt[brand]."' onFocus='this.blur();'><img src='/data/suns/images/product/ms_".$slave_mdb->dt[id].".gif' width='$image_size' height='$image_size' style='border:1px solid #e9e9e9'></a></td>";
                $mstring .= "</tr><tr><td align='center' valign='top' width=$image_size>".cut_str($slave_mdb->dt[pname],10)."<br>".($slave_mdb->dt[listprice] == "0" || $slave_mdb->dt[listprice] == $slave_mdb->dt[sellprice] ? number_format($slave_mdb->dt[sellprice]):"<s>".number_format($slave_mdb->dt[listprice])."원</s><br><span style='color:red'>".number_format($slave_mdb->dt[sellprice]))."원</span></td></tr></table>";
            }
            $mstring .= "</td></tr></table>";
        }
    }
    //return "<div style='position:absolute;'><div style='position:relative;' >".$mstring."</div></div>";
    return $mstring;
}





function getCategoryEventTotal($cid){
    global $slave_mdb;
    $sql = "select count(*) as total from shop_event where cid = '".$cid."' and disp = 1";
    $slave_mdb->query($sql);
    $slave_mdb->fetch();

    return $slave_mdb->dt[total];
}

function getCategoryTotal($cid, $type){
    global $slave_mdb;
    $depth = '0';
    if($type == "new"){
        $sql = "select pg_ix from shop_promotion_goods where disp = 1 and div_ix = 1 and ".date("Ymd")." between pg_use_sdate and pg_use_edate limit 0, 1";
        $slave_mdb->query($sql);
        if($slave_mdb->total){
            $slave_mdb->fetch();
            $pg_ix = $slave_mdb->dt[pg_ix];

        }
    }else{
        $sql = "select pg_ix from shop_promotion_goods where disp = 1 and div_ix = 2 and ".date("Ymd")." between pg_use_sdate and pg_use_edate limit 0, 1";
        $slave_mdb->query($sql);
        if($slave_mdb->total){
            $slave_mdb->fetch();
            $pg_ix = $slave_mdb->dt[pg_ix];

        }
    }
    $sql = "SELECT distinct p.id,p.pname, p.sellprice,p.listprice, p.reserve, pg_ix
						FROM ".TBL_SHOP_PRODUCT." p, shop_promotion_goods_relation rpr ,shop_product_relation pr
						where p.id = rpr.pid and p.id = pr.pid and  pr.pid = rpr.pid and rpr.pg_ix = '".$pg_ix."' and p.disp = 1 and p.state = 1 and pr.cid LIKE '".substr($cid,0,($depth+1)*3)."%' ";

    $slave_mdb->query($sql);


    return $slave_mdb->total;
}




function getCategoryTotal2($cid){
    global $slave_mdb;
    $sql = "SELECT *
			FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and r.cid = '".$cid."' and p.disp in (1,3)  and p.state = 1 group by id ";
    $slave_mdb->query($sql);
//	$slave_mdb->fetch();

    return $slave_mdb->total;
}

function getCategoryTotal3($cid){
    global $slave_mdb;
    $sql = "SELECT *
			FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and r.cid like '006%' and p.disp in (1,3)  and p.state = 1 group by id ";
    $slave_mdb->query($sql);
//	$slave_mdb->fetch();

    return $slave_mdb->total;
}


function fetch_banner($page,$limit, $start=0){
    global $viewtype, $vdate, $SelectReport;
    global $slave_mdb;
    //$slave_mdb = new MySQl;
    if($viewtype == "analysis"){
        if($vdate == ""){
            $vdate = date("Ymd", time());
            $vyesterday = date("Ymd", time()-84600);
            $voneweekago = date("Ymd", time()-84600*7);
        }else{
            if($SelectReport ==3){
                $vdate = $vdate."01";
            }
            $vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
            $vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
            $voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
        }

        if($SelectReport == 1){
            $vdate_str = " and vdate = '$vdate'";
        }else if($SelectReport == 2){
            $vdate_str = " and vdate between '$vdate' and '$vweekenddate'";
        }else if($SelectReport == 3){
            $vdate_str = " and vdate LIKE '".substr($vdate,0,6)."%'  ";
        }
        //$sql = 	"SELECT banner_img,banner_ix,banner_link,banner_target,banner_width,banner_height FROM shop_bannerinfo where banner_ix = '".$banner_ix."' ";
        $sql = "select b.banner_ix, banner_img,banner_link,banner_target,banner_width,banner_height,
			IFNULL(sum(bc.ncnt),0) as ncnt
			from shop_bannerinfo b left join logstory_banner_click bc
			on b.banner_ix = bc.banner_ix $vdate_st
			where b.banner_page = '$page'
			group by b.banner_ix, banner_img,banner_link,banner_target,banner_width,banner_height
			limit $start, $limit  ";
        //echo $sql;

    }else{
        $sql = "select * from shop_bannerinfo where banner_page = $page limit $start, $limit";
    }
    $slave_mdb->query($sql);
    return $slave_mdb->fetchall();
}


function categoryName(){
    global $slave_mdb;
    //$slave_mdb = new Database;

    $slave_mdb->query("SELECT cname,cid,depth FROM ".TBL_SHOP_CATEGORY_INFO." where depth = 0 order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");

    return $slave_mdb->fetchall();
}

function sub_categoryName($cid,$id=""){
    global $_SERVER;
    global $slave_mdb;

    //$slave_mdb = new Database;
    if($id=="") {//상품상세에서 왼쪽 메뉴를 보여줘야 할 때 cid 값이 없고 상품 id 값만 받으므로 왼쪽 메뉴 카테고리를 보이게 만듬 kbk 12/03/06
        $slave_mdb->query("SELECT * FROM ".TBL_SHOP_CATEGORY_INFO." where depth = 1 and cid LIKE '".substr($cid,0,($depth+1)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");
    } else {
        if($cid=="") {
            $referer=$_SERVER["HTTP_REFERER"];
            if(strstr($referer,"cid=")!="") $cid=substr($referer,strpos($referer,"cid=")+4,15);//이전 페이지 주소를 검사하여 cid 값을 추출한다 kbk 12/03/06
            if($cid=="") {//이전 페이지 주소에서 cid 값을 받아오지 못한다면 상품의 기본 cid 값을 가져온다 kbk 12/03/06
                $sql="SELECT cid FROM ".TBL_SHOP_PRODUCT_RELATION." WHERE pid='".$id."' AND basic='1' AND insert_yn='Y' ";
                $slave_mdb->query($sql);
                if($slave_mdb->total>0) {
                    $slave_mdb->fetch();
                    $cid=$slave_mdb->dt["cid"];
                } else {//상품이 기본 카테고리가 정해진게 없을 경우 kbk 12/03/06
                    $sql="SELECT cid FROM ".TBL_SHOP_PRODUCT_RELATION." WHERE pid='".$id."' AND insert_yn='Y' ORDER BY regdate DESC LIMIT 1 ";
                    $slave_mdb->query($sql);
                    $slave_mdb->fetch();
                    $cid=$slave_mdb->dt["cid"];
                }
            }
        }
        //echo $cid;
        $slave_mdb->query("SELECT * FROM ".TBL_SHOP_CATEGORY_INFO." where depth = 1 and cid LIKE '".substr($cid,0,($depth+1)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");
    }

    return $slave_mdb->fetchall();
}

function fetch_banner_dis($size){
    global $slave_mdb;

    //$slave_mdb = new MySQl;
    $slave_mdb->query("select * from shop_bannerinfo where banner_page = 9 order by rand() limit 0, 1");
    $slave_mdb->fetch();
    $mstring = "<table border=0 cellpadding=0 cellspacing=0 style='table-layout:fixed;width:184px;height:20px;float:left;'>
										<tr>
											<td style='padding:6px 5px 3px 28px;font-size:11px;'><a href='".$slave_mdb->dt[banner_link]."'>".cut_str($slave_mdb->dt[banner_desc],18)."</a></td>
										</tr>
									</table>";

    return $mstring;
}

function fetch_event_group($agent_type = "W",$er_ix=""){
    global $slave_mdb;
    //$slave_mdb = new Database;

    if( ! empty($er_ix) ){
        $where = " and er_ix='".$er_ix."' ";
    }

    $sql=" select * from shop_event_relation where  use_yn = 'Y' and agent_type = '".$agent_type."' ".$where." " .$limit_str ;

    $slave_mdb->query($sql);
    $event_group = $slave_mdb->fetchall();

    return $event_group;
}

function fetch_event_top($cid, $size, $orderby_type = "", $sub_where="", $surely_size=false, $start = 0){
    global $slave_mdb;
    //$slave_mdb = new Database;

    $where = "where disp = 1 and ".time()." between event_use_sdate and event_use_edate ";

    if(is_mobile()){
        $where .= " and agent_type = 'M' ";
    }else{
        $where .= " and agent_type = 'W' ";
    }
    if($cid){
        $cid_where = " and cid LIKE '$cid%' ";
    }

    if($orderby_type == "BEST_SELL"){
        $orderby = " order_cnt DESC ";
    }else if($orderby_type == "NEW"){
        $orderby = " regdate DESC ";
    }else if($orderby_type == "VIEW"){
        $orderby = " view_cnt DESC ";
    }else if($orderby_type == "RANDOM"){
        $orderby = " rand() ";
    }else if($orderby_type == "EDATE"){
        $orderby = " event_use_edate DESC ";
    }else if($orderby_type == "EDATE2"){
        $orderby = " event_use_edate ASC ";
    }else if($orderby_type == "SDATE"){
        $orderby = " event_use_sdate DESC ";
    }else if($orderby_type == "SDATE2"){
        $orderby = " event_use_sdate ASC ";
    }else{
        $orderby = " regdate DESC ";
    }

    if($size > 0){
        $limit_str = " limit " . $start . ",$size ";
    }

    $sql=" select * from shop_event $where $cid_where $sub_where order by $orderby " .$limit_str ;

    $slave_mdb->query($sql);
    if(($surely_size && false) || is_mobile() == true){	//강태웅주임의 요청으로 연관되지 않는 기획전은 비노출 처리함 2014-08-21 이학봉
        //모바일경우 해당 로직 타야됨 2014-08-25 이학봉
        //if($surely_size){	//강태웅주임의 요청으로 연관되지 않는 기획전은 비노출 처리함 2014-08-21 이학봉

        if($slave_mdb->total < $size){
            $event_ix=array();
            if($slave_mdb->total){
                $result_data=$slave_mdb->fetchall("object");
                for($i=0;$i<count($result_data);$i++){
                    $e_date = date('Y-m-d H:i:s',$result_data[$i]['event_use_edate']);
                    $result_data[$i]['date_diff'] = dateDiff(date('Y-m-d H:i:s'),$e_date,'day');
                    $result_data[$i]['display_date'] = changeDisplayDate($e_date);
                }
            }else{
                $result_data=array();
            }

            for($i=0;$i<count($result_data);$i++){
                $event_ix[]=$result_data[$i][event_ix];
            }


            $sub_where.=" and event_ix not in ('".implode("','",$event_ix)."') ";
            $size = $size - $slave_mdb->total;
            $limit_str = " limit ". $start .",$size ";
            $sql=" select * from shop_event $where $sub_where order by $orderby ".$limit_str;

            $slave_mdb->query($sql);
            $event_data=$slave_mdb->fetchall("object");
            for($i=0;$i<count($event_data);$i++){
                $e_date = date('Y-m-d H:i:s',$event_data[$i]['event_use_edate']);
                $event_data[$i]['date_diff'] = dateDiff(date('Y-m-d H:i:s'),$e_date,'day');
                $event_data[$i]['display_date'] = changeDisplayDate($e_date);

                array_push($result_data,$event_data[$i]);
            }

            return $result_data;
        }
    }
    $result = $slave_mdb->fetchall("object");
    for($i=0;$i<count($result);$i++){
        $e_date = date('Y-m-d H:i:s',$result[$i]['event_use_edate']);
        $result[$i]['date_diff'] = dateDiff(date('Y-m-d H:i:s'),$e_date,'day');
        $result[$i]['display_date'] = changeDisplayDate($e_date);
    }
    return $result;
}


function fetch_best_review($size="5",$pid="",$valuation="goods"){
    global $slave_mdb;
    //$slave_mdb = new Database;

    if($valuation=="goods"){
        $select="valuation_goods";
    }elseif($valuation=="goods_info"){
        $select="valuation_goods_info";
    }elseif($valuation=="delivery"){
        $select="valuation_delivery";
    }elseif($valuation=="package"){
        $select="valuation_package";
    }else{
        $select="avg(valuation_goods+valuation_goods_info+valuation_delivery+valuation_package)";
    }

    $where =" where $select > 3 ";

    if($pid!=""){
        if(is_array($pid)){
            $where .=" and bbs_etc1 in ('".implode("','",$pid)."') ";
        }else{
            $where .=" and bbs_etc1 ='".$pid."' ";
        }

    }

    $sql="select * from
	(
		select $select as score,bbs_subject,bbs_contents,regdate from bbs_after ".$where."  limit 0,".$size."
		union all
		select $select as score,bbs_subject,bbs_contents,regdate from bbs_premium_after ".$where." limit 0,".$size."
	) a
	order by regdate desc
	limit 0,".$size."";

    $slave_mdb->query($sql);
    return $slave_mdb->fetchall("object");
}


function hit_keyword($type,$size){
    global $slave_mdb;
    //$slave_mdb = new Database;
    if($type == "best"){
        $slave_mdb->query("select * from shop_search_keyword where recommend = '1' order by rand() limit 0,$size");
    }else{
        $slave_mdb->query("select * from shop_search_keyword where recommend = '0' order by searchcnt desc limit 0,$size");
    }
    return $slave_mdb->fetchall();
}

function randomImg(){
    $rand = rand(1,5);
    if($rand == 1){
        $mstring = "<table border=0 cellpadding=0 cellspacing=0>
								<tr>
									<td><a href='/event/goods_brand.php?b_ix=1466&cid='><img src='/data/suns/templet/suns/new_image/top_banner01.jpg'></a></td>
								</tr>
							</table>";
    }else if($rand == 2){
        $mstring = "<table border=0 cellpadding=0 cellspacing=0>
								<tr>
									<td><a href='/event/goods_brand.php?b_ix=1261&cid=003000000000000'><img src='/data/suns/templet/suns/new_image/top_banner02.jpg'></a></td>
								</tr>
							</table>";
    }else if($rand == 3){
        $mstring = "<table border=0 cellpadding=0 cellspacing=0>
								<tr>
									<td><a href='/shop/goods_view.php?cid=&depth=&id=026448'><img src='/data/suns/templet/suns/new_image/top_banner03.jpg'></a></td>
								</tr>
							</table>";
    }else if($rand == 4){
        $mstring = "<table border=0 cellpadding=0 cellspacing=0>
								<tr>
									<td><a href='/shop/goods_view.php?cid=&depth=&id=023836'><img src='/data/suns/templet/suns/new_image/top_banner04.jpg'></a></td>
								</tr>
							</table>";
    }else{
        $mstring = "<table border=0 cellpadding=0 cellspacing=0>
								<tr>
									<td><a href='/shop/goods_view.php?cid=&depth=&id=023387'><img src='/data/suns/templet/suns/new_image/top_banner05.jpg'></a></td>
								</tr>
							</table>";
    }

    return $mstring;
}



function bestseller($size=7)
{
    global $slave_mdb;
    //$slave_mdb = new Database;

    $sql = "select pid, pname, psprice from shop_order_detail group by pid order by count(pid) desc limit $size";
    $slave_mdb->query($sql);
    $total = $slave_mdb->total;


    if($total >= 5)
    {
        $slave_mdb->query("select o.pid, o.pname, o.psprice,c.cname from shop_order_detail o, shop_product_relation r, shop_category_info c where o.pid = r.pid and r.cid = c.cid group by pid order by count(o.pid) desc limit $size");
        return $slave_mdb->fetchall();
    }
    else
    {
        $sql = "select id as pid, pname, sellprice as psprice from shop_product order by regdate desc limit $size";
        $slave_mdb->query($sql);
        return $slave_mdb->fetchall();
    }
}

function newProduct($size=10)
{
    global $slave_mdb;
    //$slave_mdb = new Database;

    $sql = "select p.id, p.pname, p.brand_name, p.sellprice, c.cname from shop_product p, shop_product_relation r, shop_category_info c where p.id = r.pid and r.cid = c.cid and p.disp in (1,3)  and p.state = 1 order by p.regdate desc limit $size";
    $slave_mdb->query($sql);
    return $slave_mdb->fetchall();
}

function hitItem($size=10)
{
    global $slave_mdb;
    //$slave_mdb = new Database;

    $sql = "select p.id as pid, p.pname, p.sellprice as psprice, c.cname from shop_product p, shop_product_relation r, shop_category_info c where p.id = r.pid and r.cid = c.cid and p.disp in (1,3)  and p.state = 1 order by p.view_cnt desc limit $size";
    $slave_mdb->query($sql);
    return $slave_mdb->fetchall();
}


function getCategoryProduct($cid,$size=5)
{
    global $slave_mdb;
    //$slave_mdb = new Database;

    $sql ="select p.id, c.cname, p.sellprice, p.pname from shop_product p, shop_product_relation r, shop_category_info c where p.id = r.pid and r.cid = c.cid and r.cid like '".$cid."%' and p.disp in (1,3) and p.state = 1 order by p.regdate desc limit $size";

    $slave_mdb->query($sql);
    return $slave_mdb->fetchall();
}

function mainPromotion($type,$size){
    global $slave_mdb;
    //$slave_mdb = new Database;
    $sql = "SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,p.coprice,
							 p.reserve, pg_ix, pgr_ix, rpr.vieworder
							FROM ".TBL_SHOP_PRODUCT." p, shop_promotion_goods_relation rpr
							where p.id = rpr.pid and rpr.pg_ix = '$type'  and p.disp = 1 order by rpr.vieworder asc limit $size ";

    $slave_mdb->query($sql);
    return $slave_mdb->fetchall();
}

function getMainProduct($productNumber){
    global $slave_mdb;
    //$slave_mdb = new Database;
    $sql = "select p.id,p.brand,p.pname,p.sellprice,p.listprice from shop_recommend_product_relation mp,shop_product p where mp.md_ix = '".$productNumber."' and mp.pid = p.id and disp = '1' order by mp.vieworder limit 5 ";
    $slave_mdb->query($sql);

    return $slave_mdb->fetchall();
}


function getCategoryMainProduct($cid,$group_code){
    global $slave_mdb;
    //$slave_mdb = new Database;
    $sql="SELECT cmpg.cmg_ix, cmpg.product_cnt, cmpg.goods_display_type, cmpg.display_auto_type FROM shop_category_main_div cmd LEFT JOIN shop_category_main_product_group cmpg ON cmd.div_ix=cmpg.div_ix WHERE cmd.cid LIKE '".substr($cid,0,3)."%' AND cmpg.group_code='".$group_code."' AND cmpg.use_yn='Y' ";
    $slave_mdb->query($sql);
    //echo $sql;
    //return $sql;
    if($slave_mdb->total > 0) {
        $slave_mdb->fetch();
        $cmg_ix=$slave_mdb->dt["cmg_ix"];
        $limit_cnt=$slave_mdb->dt["product_cnt"];
        $goods_display_type=$slave_mdb->dt["goods_display_type"];
        $display_auto_type=$slave_mdb->dt["display_auto_type"];
        if($goods_display_type=="A") {
            $sql="SELECT p.id, p.pcode, p.shotinfo, p.pname, p.sellprice, p.listprice,  p.reserve, p.brand_name
			FROM ".TBL_SHOP_PRODUCT." p LEFT JOIN ".TBL_SHOP_PRODUCT_RELATION." pr ON p.id=pr.pid WHERE pr.cid LIKE '".substr($cid,0,3)."%' AND p.disp = 1 GROUP BY p.id ORDER BY ".$display_auto_type." DESC LIMIT 0,".$limit_cnt;
            //echo $sql;
        } else {
            $sql="SELECT p.id, p.pcode, p.shotinfo, p.pname, p.sellprice, p.listprice,  p.reserve, cmpr_ix, cmpr.vieworder, cmpr.group_code, p.brand_name
			FROM shop_product p, shop_category_main_product_relation cmpr, shop_category_main_product_group cmpg
			where cmpg.cmg_ix ='".$cmg_ix."'
			AND cmpg.group_code='".$group_code."'
			and cmpg.cmg_ix = cmpr.cmg_ix
			and p.id = cmpr.pid
			and cmpr.group_code = '".$group_code."'
			and cmpr.group_code = cmpg.group_code
			and p.disp = 1 ORDER BY cmpr.vieworder ASC LIMIT 0,".$limit_cnt;
        }

        //return $sql;
        $slave_mdb->query($sql);
        return $slave_mdb->fetchall();
    }
}

function getWishList_info()
{
    global $user;
    global $slave_mdb;
    //$slave_mdb = new Database;

    $slave_mdb->query("select wid, id, pname,  reserve, sellprice, format(sellprice,0) as price from ".TBL_SHOP_WISHLIST." w, ".TBL_SHOP_PRODUCT." p where w.pid = p.id and mid ='".$user[code]."'  limit 3 ");

    return $slave_mdb->fetchall();
}


function ondayProductList($cid, $depth=0, $max=3) {
    global $shop_product_type;
    global $slave_mdb;
    //$slave_mdb = new Database;

    $todate = time();

    $yesterday = date("Y-m-d", $todate-(1*86400));
    $sdate = $yesterday . " 00:00:00";
    $edate = $yesterday . " 23:59:59";

    //and product_type in (".implode(' , ',$shop_product_type).")
    $sql = "select p.* from ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r
			where p.id = r.pid
			and p.disp = 1
			and p.state = 1

			and r.cid LIKE '".substr($cid,0,($depth+1)*3)."%'
			and p.regdate <= '$edate'
			GROUP BY p.id ORDER BY p.regdate limit $max";
    $slave_mdb->query($sql);

    return $slave_mdb->fetchall();
}

function getCart_info()
{
    global $user,$shop_product_type;
    global $slave_mdb;
    //$slave_mdb = new Database;

    $sql = "select * from shop_cart where mem_ix='".$user[code]."' and product_type in (".implode(' , ',$shop_product_type).") order by regdate desc limit 3";
    $slave_mdb->query($sql);

    return $slave_mdb->fetchall();
}

function getCart_cnt()
{
    global $user,$shop_product_type;
    global $slave_mdb;
    //$slave_mdb = new Database;

    if($user[code] != "")
    {
        $where = "mem_ix='".$user[code]."' and product_type in (".implode(' , ',$shop_product_type).") ";
    }
    else
    {
        $where = "cart_key='".session_id()."' and product_type in (".implode(' , ',$shop_product_type).") ";
    }

    $sql = "select * from shop_cart where $where order by regdate desc";
    $slave_mdb->query($sql);

    return $slave_mdb->fetchall();
}

function getWishList_cnt()
{
    global $user;
    global $slave_mdb;
    //$slave_mdb = new Database;

    $slave_mdb->query("select wid, id, pname,   reserve, sellprice, format(sellprice,0) as price from ".TBL_SHOP_WISHLIST." w, ".TBL_SHOP_PRODUCT." p where w.pid = p.id and mid ='".$user[code]."'");

    return $slave_mdb->fetchall();
}

function getQna_cnt() {
    global $slave_mdb;
    session_start();

    $bm_ix = 8;
    $userCode = $_SESSION["user"]["code"];

    $sql = "select bbs.* ,
			(select cmt_name from bbs_qna_comment qc where qc.bbs_ix = bbs.bbs_ix order by regdate desc limit 1) as cmt_name,
			(select cmt_contents from bbs_qna_comment qc where qc.bbs_ix = bbs.bbs_ix order by regdate desc limit 1) as cmt_comments,
			(select regdate from bbs_qna_comment qc where qc.bbs_ix = bbs.bbs_ix order by regdate desc limit 1) as cmt_regdate
			from
			(
			Select case when bsdiv.div_name != '' then concat(bdiv.div_name,' > ',bsdiv.div_name) else bdiv.div_name end as div_name,
			bbs.*,
			CONCAT(SUBSTR(bbs_name,1,1),'*',SUBSTR(bbs_name,3)) AS changed_bbs_name,
			IF(LENGTH(SUBSTRING(bbs.bbs_subject,(20+1-bbs.bbs_ix_level),1))>0,CONCAT(SUBSTRING(bbs.bbs_subject,1,20-bbs.bbs_ix_level),'...'),bbs.bbs_subject) as changed_bbs_subject,
			bbs_subject as nocut_bbs_subject,
			1 as cno,
			concat('?mode=read&board=qna&bbs_ix=',bbs_ix,'&page=','1') as link ,
			case when bbs.regdate > DATE_SUB(now(), interval 24 HOUR) then 1 else 0 end as new
			from bbs_qna bbs left join bbs_manage_div bdiv on bdiv.div_ix = bbs.bbs_div and bdiv.bm_ix = '".$bm_ix."'
			left join bbs_manage_div bsdiv on bsdiv.div_ix = bbs.sub_bbs_div and bsdiv.bm_ix = '".$bm_ix."'
			where bbs_ix > 0 and mem_ix = '".$userCode."'
			and is_notice!='Y'
			order by bbs_top_ix desc,bbs_ix_level asc  ) bbs ";

    $slave_mdb->query($sql);
    $db_results = $slave_mdb->fetchall();
    return count($db_results);
}


function textStr($text,$max=3){
    $input = $text;
    if($max != 0){
        $str= substr($text, 0, $max);
    }
    for($i=0; $i<(strlen($input)-$max); $i++){
        $str .= '*';
    }
    return $str;
}

function getTopDeliveryPolicy($slave_mdb, $retur_type = "result"){
    global $slave_mdb;
    //$slave_mdb = new Database;
    if($slave_mdb->dbms_type == "oracle"){
        $sql = "SELECT
				delivery_free_policy,
				delivery_product_policy,
				free_cost_price as delivery_freeprice,
				delivery_basic_policy,
				delivery_product_policy,
				basic_send_cost_tekbae as delivery_price
			FROM
				".TBL_SHOP_SHOPINFO."
			where
				mall_div = 'B' and rownum <= 1 ";

    }else{
        $sql = "SELECT
				delivery_free_policy,
				delivery_product_policy,
				free_cost_price as delivery_freeprice,
				delivery_basic_policy,
				delivery_product_policy,
				basic_send_cost_tekbae as delivery_price,
				return_shipping_price,
				return_shipping_cnt
			FROM
				".TBL_SHOP_SHOPINFO."
			where
				mall_div = 'B' limit 0, 1 ";
    }

    if($retur_type == "sql"){
        return $sql;
    }else{
        $slave_mdb->query($sql);
        $slave_mdb->fetch();

        return $slave_mdb->dt;
    }
}

function getCompanyCart($company_id,$choice_prod="0",$set_group="",$delivery_policy,$delivery_type,$delivery_package,$delivery_pay_method,$delivery_method,$id,$delivery_addr_use,$factory_info_addr_ix,$est_ix=''){
    global $master_db;
    /*
	$delivery_policy	조건배송 타입(배송정책)	1:무료배송 2:고정배송비 3:주문결제금액 할인 4:수량별할인 5:출고지별 배송비 6: 상품1개단위 배송비
	$delivery_type		통합배송여부 (1:통합배송, 2: 입점업체 배송)
	$delivery_package	묶음배송 여부 (Y:개별,N:묶음)
	$delivery_pay_method	배송결제 방법	(1.선불 2. 착불)
	$delivery_method		배송방법(1:택배,2:화물,3:직배송,4:방문수령)

	carts 에 group by 를 조건으로 group by delivery_type, company_id, delivery_method, delivery_pay_method, delivery_package
	*/

    global $user, $shop_product_type, $sns_product_type ;

    //$slave_mdb = new Database;

    //$admin_delievery_policy = getTopDeliveryPolicy($master_db);//$master_db->dt;

    if($choice_prod == "1"){
        $whereplus = " and choice_prod = '1'";
    }else{
        $whereplus = "";
    }

    if($_SERVER["PHP_SELF"]=="/shop/payment.php" || $_SERVER["PHP_SELF"]=="/shop/securepay_card.php" || $_SERVER["PHP_SELF"]=="/shop/securepay_bank.php") {
        $whereplus = " and choice_prod = '2'";//결제할 때 장바구니에 구분 추가 kbk 13/08/23
    }

    if(TBL_SNS_CART=="shop_cart"){
        $in_product_type=$sns_product_type;
    }else{
        $in_product_type=$shop_product_type;
    }

    if($set_group!=""){ $add_set_group=" AND c.set_group='".$set_group."' ";}

    if($user[code] != ""){
        $where = " mem_ix = '".$user[code]."'" . $whereplus." and c.product_type in (".implode(' , ',$in_product_type).") $add_set_group ";
        $set_group_where="mem_ix = c.mem_ix";//비회원에 대한 조건이 없어서 회원/비회원 구분 지어서 조건 검 kbk 13/08/30
    }else{
        $where = " cart_key = '".session_id()."'" . $whereplus ." and c.product_type in (".implode(' , ',$in_product_type).") $add_set_group ";
        $set_group_where="cart_key = '".session_id()."'";//비회원에 대한 조건이 없어서 회원/비회원 구분 지어서 조건 검 kbk 13/08/30
    }

    if($delivery_type == '1'){
        $where_delivery_type = " and c.ori_company_id = '".$company_id."'";
    }else{
        $where_delivery_type = " and c.company_id = '".$company_id."'";
    }

    if($delivery_package == 'Y'){
        $where .= " and c.id = '".$id."' ";
    }

    if($est_ix != ""){	//견적서에서 넘어온 건 땜에 견적서 번호가 잇을경우 번호에 해당된 상품만 불러오기 2014-09-10 이학봉
        $where .= " and c.est_ix = '".$est_ix."'";
    }

    if($master_db->dbms_type == "oracle"){
        $sql = "select c.*,
			p.delivery_package,p.admin,
			CASE p.delivery_policy WHEN '1' THEN
				(select CASE delivery_policy WHEN '1' THEN ".$admin_delievery_policy[delivery_price]." ELSE delivery_price END delivery_price from ".TBL_COMMON_SELLER_DELIVERY." where company_id = '$company_id' )
			ELSE delivery_price END delivery_price,
				(select CASE delivery_policy WHEN '1' THEN '".$admin_delievery_policy[delivery_basic_policy]."' ELSE delivery_basic_policy END delivery_basic_policy from ".TBL_COMMON_SELLER_DELIVERY." where company_id = '".$company_id."')
			as delivery_basic_policy
			from shop_cart c,shop_product p
			where $where and c.id = p.id and company_id = '".$company_id."'
			order by c.regdate desc, c.cart_ix ASC";
        //정렬이 delivery_price 인 것을 regdate 로 바꿈 kbk 11.10.10  limit 0,1 는 임시 지워도됨
        //c.cart_ix ASC 추가(직배송 상품때문에) kbk 13/09/03

    }else{

        $sql = "select

					c.*,
					c.delivery_package, ci.depth,  dc_detail as discount_desc,
					if(c.delivery_type = '1',c.ori_company_id,c.company_id) as company_id,
					if(c.delivery_type = '1',c.ori_company_name,c.company_name) as company_name,
					(select count(*) from shop_cart where $set_group_where and product_type = c.product_type and set_group = c.set_group and id = c.id and
						(case when option_kind in ('','a') then '' else option_kind end)  = (case when c.option_kind in ('','a') then '' else c.option_kind end)
					) as set_group_order_cnt,
					(select count(distinct product_type, id, set_group) from shop_cart where mem_ix = c.mem_ix and product_type = c.product_type and set_group = c.set_group and id = c.id and option_kind = c.option_kind ) as set_cnt,
					(select sum((listprice+option_price)*pcount) from shop_cart where mem_ix = c.mem_ix and product_type = c.product_type and set_group = c.set_group and id = c.id and option_kind = c.option_kind ) as set_listprice,
					(select sum((sellprice+option_price)*pcount) from shop_cart where mem_ix = c.mem_ix and product_type = c.product_type and set_group = c.set_group and id = c.id and option_kind = c.option_kind ) as set_sellprice,
					(select sum((sellprice+option_price)*pcount) from shop_cart where mem_ix = c.mem_ix and product_type = c.product_type and set_group = c.set_group and id = c.id and option_kind = c.option_kind ) as set_totalprice,
					(select sum(reserve) from shop_cart where mem_ix = c.mem_ix and product_type = c.product_type and set_group = c.set_group and id = c.id) as set_reserve,
					c.delivery_pay_method as delivery_basic_policy,
					p.admin,
					p.stock,
					p.available_stock,
					p.state,
					p.premiumprice
				from
					shop_cart c,
					shop_product p,
					shop_category_info ci
				where
					$where
					and c.id = p.id
					and c.cid = ci.cid
					and c.delivery_type = '".$delivery_type."'
					$where_delivery_type
					and c.delivery_package = '".$delivery_package."'
					and c.delivery_pay_method = '".$delivery_pay_method."'
					and c.delivery_method = '".$delivery_method."'
					and c.delivery_addr_use = '".$delivery_addr_use."'
					and c.factory_info_addr_ix = '".$factory_info_addr_ix."'
					order by c.product_type, c.id, c.set_group,
					case when option_kind = 'a' then 10 else 1 end ,
					c.select_option_id, c.regdate desc";
        /*
					작업자 : shs
					작업내용
					1. 2014-07-11 일반상품이 카트에서 그룹핑이 안되는 이류로 c.set_group,  case when  앞으로 이동

					*/

    }

    //echo nl2br($sql)."<br><br>";
    //exit;
    $master_db->query($sql);
    $carts = $master_db->fetchall();
    //print_r($carts);
    for($i=0 ; $i < count($carts) ;$i++){
        $_array_pid[] = $carts[$i][id];
        $_array_amount[$carts[$i][id]] = $carts[$i][pcount];
    }

    if(is_array($carts)){

        foreach ($carts as $key => $sub_array) {

            //array_insert($sub_array,53,json_decode($sub_array[discount_desc]));
            /*
			unset($goods_infos);
			$goods_infos[$sub_array[id]][pid] = $sub_array[id];
			$goods_infos[$sub_array[id]][amount] = $sub_array[pcount];
			$goods_infos[$sub_array[id]][cid] = $sub_array[cid];
			$goods_infos[$sub_array[id]][depth] = $sub_array[depth];
			//echo $sub_array[id].":::".$sub_array[cid].":::".$sub_array[depth].":::".$sub_array[pcount]."<br>";
			$discount_info = DiscountRult($goods_infos, $sub_array[cid], $sub_array[depth], $sub_array[pcount]);

			$select_ = array("icons_list"=>explode(";",$sub_array[icons]));
			array_insert($sub_array,50,$select_);
			$discount_item = $discount_info[$sub_array[id]];
			$_dcprice = $sub_array[sellprice];

			if(is_array($discount_item)){
				foreach($discount_item as $_key => $_item){
					if($_item[discount_value_type] == "1"){ // %
						//echo $_item[discount_value]."<br>";
						$_dcprice = $_dcprice*(100 - $_item[discount_value])/100;
					}else if($_item[discount_value_type] == "2"){// 원
						$_dcprice = $_dcprice - $_item[discount_value];
					}
					$discount_desc[] = $_item[discount_type]."-".$_item[discount_value]." ".($_item[discount_value_type] == "1" ? "%":"원")."-".$_dcprice."원";
				}

			}
			$_dcprice = array("dcprice"=>$_dcprice);
			array_insert($sub_array,52,$_dcprice);
			$discount_desc = array("discount_desc"=>$discount_desc);
			array_insert($sub_array,53,$discount_desc);
			unset($discount_desc);
			*/
            $carts[$key] = $sub_array;
            if($carts[$key][uf_valuation] != "") $carts[$key][uf_valuation] = round($carts[$key][uf_valuation], 0);
            else $carts[$key][uf_valuation] = 0;
        }
        //print_r($carts);
    }

    return $carts;
}

/*
예전 배송방법 정책 함수라서 주석처리했음. 이부분으로 인한 오류는 이학봉님한테로 문의바람 ~ 2014-06-12
function getDeliveryPolicy($id,$company_id){
	$slave_mdb = new Database;

	$admin_delievery_policy = getTopDeliveryPolicy($slave_mdb);//$slave_mdb->dt;

	$sql = "select
				csd.company_id,
				delivery_policy,
				free_cost_price as delivery_freeprice,
				basic_send_cost_tekbae as delivery_price,
				delivery_basic_policy,
				delivery_product_policy
			from
				".TBL_COMMON_SELLER_DELIVERY." csd
			where
				company_id = '$company_id' ";
	$slave_mdb->query($sql);
	$slave_mdb->fetch();
	$seller_delievery_policy = $slave_mdb->dt;

	if($slave_mdb->dbms_type == "oracle"){
		$sql = "select CASE p.delivery_policy WHEN '1' THEN (CASE '".$seller_delievery_policy[delivery_policy]."' WHEN '1' THEN ".$admin_delievery_policy[delivery_price]." ELSE ".$seller_delievery_policy[delivery_price]." END) ELSE ( CASE p.delivery_product_policy WHEN  '3' THEN 0 ELSE p.delivery_price END ) END delivery_freeprice , p.delivery_policy
		from shop_product p where id = '$id' ";
	}else{
		$sql = "select if(p.delivery_policy = 1,
			if( '".$seller_delievery_policy[delivery_policy]."' != '1' , '".$seller_delievery_policy[delivery_price]."' , '".$admin_delievery_policy[delivery_price]."'),
			if(p.delivery_product_policy = 3,0,p.delivery_price )) as delivery_price,
			if( '".$seller_delievery_policy[delivery_policy]."' != '1' , '".$seller_delievery_policy[delivery_freeprice]."' , '".$admin_delievery_policy[delivery_freeprice]."') as delivery_freeprice , p.delivery_policy
			from shop_product p where id = '$id' ";
	}

	$slave_mdb->query($sql);
	$slave_mdb->fetch();

	if($slave_mdb->dt[delivery_price] == 0){
		return "";
	}else{
		if ($slave_mdb->dt['delivery_policy'] == 2){
			return "(".number_format($slave_mdb->dt[delivery_price])."원)";
		}else {
			return "(".number_format($slave_mdb->dt[delivery_freeprice])."원 미만 배송비 ".number_format($slave_mdb->dt[delivery_price])."원)";
		}
	}

}
*/

/*
새로운 배송정책 적용후 배송비 금액 2014-02-25 이학봉	2014-05-19 작업시작
company_id = shop_product.admin
delivery_company = we, mi (통합,입점업체)배송 구분 => delivery_type = 1:통합, 2:입점업체 로 교체
delivery_yn = shop_cart.choice_prod
arr_cart_ix = shop_cart.cart_ix
oid = 주문번호(반품시 사용)
set_group = 옵션 그룹
addr = 도서산간배송비 용(수취인주소)

$delivery_type = 통합배송여부 1:통합배송, 2:입점업체배송
$delivery_package = 묶음배송 여부 (Y:개별,N:묶음)
$delivery_policy = 조건배송 타입(배송정책)	1:무료배송 2:고정배송비 3:주문결제금액 할인 4:수량별할인 5:출고지별 배송비 6: 상품1개단위 배송비
$delivery_method = 배송방법(1:택배,2:화물,3:직배송,4:방문수령)
$delivery_pay_method = 배송결제 방법	(1.선불 2. 착불)
$company_id_r = 통합배송일때 본사 키값을 담는 컬럼 (신규생성)
$id = 상품아이디 (개별배송일경우 상품아이디로 조건더 추가)
$delivery_addr_use = 출고지별 배송비 사용
$factroy_info_addr_ix = 출고지 키값
session_id() = shop_cart.cart_key
$coupon_price = ''	쿠폰사용시 할인금액을 제외한 총 금액으로 배송비 측정하기에 할인된 금액만 받아오기
$est_ix='' 견적서 키값 :  견적서 상세내역에서 배송비 구하기 : estimate 일경우 shop_estimates_detail 테이블과 조인해야함 2014-09-10 이학봉
$coupon_sale_price=array()	$coupon_sale_price[0을 제외한 상품아이디][cart_ix]	각 배송비별 할인금액으로서 배열로 처리함
*/

function getDeliveryPrice($company_id,$delivery_type, $delivery_yn=0, $arr_cart_ix=array(),$oid_info="",$set_group="",$addr='',$delivery_package,$delivery_policy,$delivery_method,$delivery_pay_method,$company_id_r,$id,$delivery_addr_use,$factroy_info_addr_ix,$coupon_price='0',$est_ix='0',$coupon_sale_price=array()){
//echo "$company_id"." ==> "."$company_id_r"."<br>";


    global $layout_config,$user,$shop_product_type,$sns_product_type;
    global $master_db;
    session_start();

    if($_SERVER["PHP_SELF"]=="/shop/plugin.php" || $_SERVER["PHP_SELF"]=="/shop/payment.php" || $_SERVER["PHP_SELF"]=="/shop/securepay_card.php" || $_SERVER["PHP_SELF"]=="/shop/securepay_bank.php") {
        $delivery_yn = "2";//결제할 때 장바구니에 구분 추가 kbk 13/08/23
    }

    if($est_ix){	//견적서 상세 내역페이일경우 shop_estimates_detail 테이블과 조인시킴
        $table = "shop_estimates_detail";
        $select_pid = "pid";
        $select_ix = "estd_ix";
        $delivery_yn = '0';
        $where = " and c.est_ix = '".$est_ix."'";
    }else{
        $table = 'shop_cart';
        $select_pid = "id";
        $select_ix = "cart_ix";
    }

    if($oid_info == ""){
        if($set_group!=""){
            $add_set_group=" AND c.set_group='".$set_group."' ";
        }

        if($delivery_yn == '0'){	//장바구니에 배송비 추출시 장바구니에 담긴 모든상품을 포함 2014-07-21 이학봉
            if($user[code] != ""){
                $where .= " and mem_ix = '".$user[code]."'  and choice_prod in ('0','1','2') $add_set_group ";
                $groupby = " group by mem_ix ";
            }else{
                if($cart_key != ""){
                    $where .= " and cart_key = '".$cart_key."'  and choice_prod in ('0','1','2') $add_set_group ";
                }else{
                    $where .= " and cart_key = '".session_id()."'  and choice_prod in ('0','1','2') $add_set_group ";
                }
                $groupby = " group by cart_key ";
            }
        }else{
            if($user[code] != ""){
                $where .= " and mem_ix = '".$user[code]."'  and choice_prod = '$delivery_yn' $add_set_group ";
                $groupby = " group by mem_ix ";
            }else{
                if($cart_key != ""){
                    $where .= " and cart_key = '".$cart_key."'  and choice_prod = '$delivery_yn' $add_set_group ";
                }else{
                    $where .= " and cart_key = '".session_id()."'  and choice_prod = '$delivery_yn' $add_set_group ";
                }
                $groupby = " group by cart_key ";
            }
        }

        if(is_array($arr_cart_ix)){
            $where.=" AND c.cart_ix NOT IN ('".implode("','",$arr_cart_ix)."')";
        }

        $where .= " and c. delivery_type = '".$delivery_type."'
					and c.delivery_package = '".$delivery_package."'
					and c.delivery_method = '".$delivery_method."'
					and c.delivery_pay_method = '".$delivery_pay_method."'";

        if($delivery_package == 'Y'){	//개별배송일경우 상품id조건으로 주문분리
            $where .= " and c.".$select_pid." = '".$id."' ";
        }

    }else{//주문 내역에서 환불 관련된 배송비 추출 kbk 13/07/27

        $oinfo = unserialize($oid_info);

        $oid=$oinfo[oid];
        $ode_ix=$oinfo[ode_ix];
        $not_od_ix=$oinfo[not_od_ix];
        $od_refund_info=$oinfo[od_refund_info];

        $where .=" o.oid='".$oid."' AND od.ode_ix='".$ode_ix."' AND od.od_ix not in ('".implode("','",$not_od_ix)."')  AND od.refund_status not in ('".ORDER_STATUS_REFUND_APPLY."','".ORDER_STATUS_REFUND_COMPLETE."')  ";
    }

    if($delivery_type == '1'){	//통합배송일경우 본사 입점업체 키값
        $where_company_id = $company_id_r;
    }else{
        $where_company_id = $company_id;
    }
    if($master_db->dbms_type == "oracle"){
        $sql = "select
					delivery_product_policy
				from
					".TBL_COMMON_SELLER_DELIVERY.";
				where
					company_id = '$where_company_id' ";
    }else{
        $sql = "select
					delivery_product_policy
				from
					".TBL_COMMON_SELLER_DELIVERY."
				where
					company_id = '$where_company_id' ";
    }

    $master_db->query($sql);
    $master_db->fetch();
    $delivery_product_policy = $master_db->dt[delivery_product_policy]; // 1. 배송비 큰금액 2.배송비 낮은금액

    if(TBL_SNS_CART=="shop_cart"){
        $add_product_type_query="product_type IN (".implode(' , ',$sns_product_type).")";//상품 타입별 금액 가져오는 부분 변경 kbk 13/08/26
    }else{
        $add_product_type_query="product_type IN (".implode(' , ',$shop_product_type).")";//상품 타입별 금액 가져오는 부분 변경 kbk 13/08/26
    }

    $add_option_kind_query="option_kind IN ('x','x2','s2','c')";//옵션별 배송비를 가져오기 위해 추가 kbk 13/08/26
    $add_option_kind_query_not="option_kind NOT IN ('x','x2','s2','c')";//옵션별 배송비를 가져오기 위해 추가 kbk 13/08/26

    if($oid ==""){
        //상품에 대한 배송정책 가져오는 select
        //if($_SESSION["user"]["sale_rate"] != ""){$delivery_package,$delivery_policy,$delivery_method,$delivery_pay_method,
        $sql = "select
						c.".$select_ix.",
						c.".$select_pid.",
						sum(c.totalprice) as totalprice,
						sum(c.pcount) as pcount,
						c.delivery_policy,
						c.delivery_package,
						c.delivery_method,
						c.delivery_pay_method,
						c.dt_ix,
						p.product_weight
					from
						".$table." c ,
						shop_product p
					where
						c.".$select_pid." = p.id
						and c.ori_company_id = '$where_company_id'
						$where
						AND c.".$add_option_kind_query_not."
						AND c.".$add_product_type_query."
						GROUP BY c.".$select_pid.",c.set_group,c.dt_ix,c.delivery_policy
				UNION ALL
					select
						c.".$select_ix.",
						c.".$select_pid.",
						SUM(c.totalprice) as totalprice,
						SUM(pcount),
						c.delivery_policy,
						c.delivery_package,
						c.delivery_method,
						c.delivery_pay_method,
						c.dt_ix,
						p.product_weight
					from
						".$table." c,
						shop_product p
					where
						c.".$select_pid." = p.id
						and c.ori_company_id = '$where_company_id'
						$where
						AND c.".$add_option_kind_query."
						AND c.".$add_product_type_query."
						GROUP BY c.".$select_pid.", c.set_group,c.dt_ix,c.delivery_policy order by ".$select_pid." ASC"; //c.totalprice,

        //같은 배송정책과 배송비 조건을 사용하는 건은 하나로 묶어서 배송비 계산해야함 c.dt_ix,c.delivery_policy 2014-05-19 이학봉
        //echo nl2br($sql)."<br><br>";
        //exit;
        //세트 상품을 적용하기 위해 UNION 사용하고 금액과 수량(pcount 대신에 set_count)을 SUM 함 kbk 13/07/19
        //}
    } else {
        $sql="SELECT
			sum(totalprice) as totalprice,
			sum(pcount) as pcount,
			dt_ix,
			delivery_policy,
			delivery_method,
			delivery_package,
			product_weight
		FROM (
			SELECT
				od.pt_dcprice AS totalprice,
				od.pcnt AS pcount,
				od.dt_ix,
				od.delivery_policy,
				od.delivery_method,
				od.delivery_package,
				p.product_weight
			FROM
				".TBL_SHOP_ORDER." o
				LEFT JOIN ".TBL_SHOP_ORDER_DETAIL." od ON o.oid=od.oid
				LEFT JOIN ".TBL_SHOP_PRODUCT." p ON p.id=od.pid
			WHERE
				$where";

        if(is_array($od_refund_info)){
            foreach($od_refund_info as $od_ix => $cnt){
                $sql.=" UNION ALL
				SELECT
					((od.dcprice + od.option_price) * '".$cnt."') AS totalprice,
					'".$cnt."' AS pcount,
					od.dt_ix,
					od.delivery_policy,
					od.delivery_method,
					od.delivery_package,
					p.product_weight
				FROM
					".TBL_SHOP_ORDER." o
					LEFT JOIN ".TBL_SHOP_ORDER_DETAIL." od ON o.oid=od.oid
					LEFT JOIN ".TBL_SHOP_PRODUCT." p ON p.id=od.pid
				WHERE
					o.oid='".$oid."' and od.od_ix ='".$od_ix."'
				";
            }
        }

        $sql .=") a";
    }

    $master_db->query($sql);
    $rows = array();
    $rows = $master_db->fetchall();

    /*
		* 배송비 조건을 소스를 수정시 admin.util.php 파일에 PorudctBasicDeliveryPrice 함수도 동일하게 수정해야함 2014-08-11 이학봉
	*/
    for($i=0;$i<count($rows);$i++){	//총주문수량과 총주문금액을 가져오기 2014-07-24 이학봉
        //$total_pcount += $rows[$i][pcount];	//주문수량
        $total_totalprice += $rows[$i][totalprice];	//총주문금액
    }
    $total_product_weight = 0;

    for($i=0;$i<count($rows);$i++){	//총무게  가져오기 JK 170623

        $total_product_weight += ($rows[$i]['product_weight'] * $rows[$i]['pcount']);	//총주문금액
    }


    for($i=0;$i<count($rows);$i++){	//장바구니 분리별로 배송비 구하기 시작

        $cart_ix = $rows[$i][$select_ix];	//쿠폰금액용키 2014-09-30
        $pid = $rows[$i][$select_pid];		//쿠폰금액용키 2014-09-30 이학봉

        $dt_ix = $rows[$i][dt_ix];
        $delivery_policy = $rows[$i][delivery_policy];	//배송비 조건타입
        $delivery_method = $rows[$i][delivery_method];	//배송방법
        $pcount = $rows[$i][pcount];	//주문수량
        $totalprice = $rows[$i][totalprice];	//총주문금액
        $delivery_package = $rows[$i][delivery_package];	//Y:묶음배송 N:개별배송

        $template_infos = delivery_template_info($dt_ix);

        //조건배송 타입(배송정책)	1:무료배송 2:고정배송비 3:주문결제금액 할인 4:수량별할인 5:출고지별 배송비 6: 상품1개단위 배송비
        if($delivery_policy == "1"){	//1:무료배송

            $delivery_price_array[99] = '0';	//무료배송이 잇을경우 키값을 99 로 넣어서 구분하게 처리함

        }else if($delivery_policy == "2"){	//2:고정배송비

            $delivery_price = $template_infos[delivery_price];

        }else if($delivery_policy == "3"){	//3:주문결제금액 할인

            $sql = "select * from shop_delivery_terms where dt_ix = '".$dt_ix."' and product_sell_type = '".$template_infos[product_sell_type]."' and delivery_policy_type = '".$delivery_policy."' order by delivery_basic_terms ASC";
            $master_db->query($sql);
            $delivery_terms_array = $master_db->fetchall();

            $delivery_price = '0';	//$coupon_price
            for($k=0;$k<count($delivery_terms_array);$k++){
                if(($total_totalprice - (count($coupon_sale_price) > 0?$coupon_sale_price[str_replace("0","",$pid)][$cart_ix]:0)) < $delivery_terms_array[$k][delivery_basic_terms]){
                    $delivery_price = $delivery_terms_array[$k][delivery_price];
                    break;
                }
            }
            /*
			$sql = "select * from shop_delivery_terms where dt_ix = '".$dt_ix."' and product_sell_type = '".$template_infos[product_sell_type]."' and delivery_policy_type = '".$delivery_policy."' order by delivery_basic_terms DESC";
			$master_db->query($sql);
			$delivery_terms_array = $master_db->fetchall();

			$delivery_price = '0';	//최초 시작을 0으로 설정하고 주문할인별, 수량별 체크시 사용 2014-08-01 이학봉
			for($k=0;$k<count($delivery_terms_array);$k++){
				if($total_totalprice <= $delivery_terms_array[$k][delivery_basic_terms]){
					//echo "$total_totalprice"." == ".$delivery_terms_array[$k][delivery_basic_terms]."<br>";
					$delivery_price = $delivery_terms_array[$k][delivery_price];
				}

				if($delivery_price == '0'){
					//$delivery_price = $delivery_terms_array[0][delivery_price];
				}
			}
			*/

        }else if($delivery_policy == "4"){	//4:수량별할인

            if($template_infos[extra_charge] == '1'){	//할인율 적용	이부분은 필요없을거 같음
                $asc = 'ASC';
            }else{										//할증율 적용
                $asc = 'DESC';
            }

            $sql = "select * from shop_delivery_terms where dt_ix = '".$dt_ix."' and product_sell_type = '".$template_infos[product_sell_type]."' and delivery_policy_type = '".$delivery_policy."' order by delivery_price DESC";	//수량별 체크는 DESC 로 해야함 2014-08-21 이학봉

            $master_db->query($sql);
            $delivery_terms_array = $master_db->fetchall();

            $delivery_price = '0';	//최초 시작을 0으로 설정하고 주문할인별, 수량별 체크시 사용 2014-08-01 이학봉
            $is_pcount_delivery = false;
            for($k=0;$k<count($delivery_terms_array);$k++){
                //echo "$k"." == "."$pcount"." == ".$delivery_terms_array[$k][delivery_price]."<br>";
                if($pcount >= $delivery_terms_array[$k][delivery_price]){
                    $delivery_price = $delivery_terms_array[$k][delivery_basic_terms];
                    $is_pcount_delivery = true;
                    break;
                }
                //echo "$delivery_price"."<br>";
                if($is_pcount_delivery == false){
                    $delivery_price = $template_infos[delivery_cnt_price];
                }
            }

        }else if($delivery_policy == "5"){	//5:출고지별 배송비 //잠시 보류

            $delivery_price = '0';
        }else if($delivery_policy == "6"){	//6:상품1개단위 배송비
            //echo "$delivery_policy"."<br>";
            $delivery_price = $pcount * $template_infos[delivery_unit_price];
        }else if($delivery_policy == "7"){//7:무게에 따른 배송비 부과

            $sql = "select * from shop_delivery_terms where dt_ix = '".$dt_ix."' and product_sell_type = '".$template_infos[product_sell_type]."' and delivery_policy_type = '".$delivery_policy."'";

            $master_db->query($sql);
            $delivery_terms_array = $master_db->fetchall();

            $delivery_price = '0';	//최초 시작을 0으로 설정하고 주문할인별, 수량별 체크시 사용 2014-08-01 이학봉

            $free_shipping_term = $template_infos['free_shipping_term'];
            if($free_shipping_term > 0 && $free_shipping_term <= $total_totalprice){
                //$delivery_price = '0';
                break;
            }
            for($k=0;$k<count($delivery_terms_array);$k++){
                if($delivery_terms_array[$k]['delivery_basic_terms'] >= $total_product_weight ){
                    $delivery_price = $delivery_terms_array[$k][delivery_price];
                    break;
                }
            }
        }

        if($delivery_method == '4'){	//배송방법이 방문수령일 경우 무조건 0원
            $delivery_price = '0';
        }else{
            $delivery_price = $delivery_price;
        }

        //도서산간 배송비 관련 시작 2014-05-19 이학봉
        //$addr = "서울시 서초구 양재동";
        if($addr !=""){

            $delivery_region_use = $template_infos[delivery_region_use]; //도서산간 배송정책 사용유무

            if( $delivery_region_use == '1'){	//무료배소상품정책 = 2 이고 도서산간배송비 정책 = 1 일경우 추가배송비 부여한다.
                $sql = "select * from shop_product_region_delivery where region_delivery_type = 1 and dt_ix='".$dt_ix."' and product_sell_type = '".$template_infos[product_sell_type]."' order by prd_ix ASC";
                //echo nl2br($sql)."<br><br>";
                $master_db->query($sql);
                $data_array = $master_db->fetchall();

                for($j=0;$j<count($data_array);$j++){
                    if(strpos($addr,$data_array[$j][region_name_text]) !== false){
                        $delivery_region_price = $data_array[$j][region_name_price];
                        break;
                    }
                }
            }

        }
        //도서산간 배송비 관련 끝 2014-05-19 이학봉

        $delivery_price_array[$i] = $delivery_price + $delivery_region_price;

        unset($delivery_price);
    }

    //echo "$delivery_product_policy"."<br>";
    if(is_array($delivery_price_array)){	//임시처리
        /*무료배송 배송비 조건이 잇을경우 해당 배송비는 전체 무료로 처리함 시작 2014-05-19 이학봉*/
        if(array_key_exists('99',$delivery_price_array)){
            unset($delivery_price_array); //배열 초기화후 0으로 다시 넣어준다.
            $delivery_price_array[0] = '0';
        }
        /*무료배송 배송비 조건이 잇을경우 해당 배송비는 전체 무료로 처리함 끝 2014-05-19 이학봉*/

        if($delivery_product_policy == '1'){	// 1: 큰배송비 2:적은배송비
            $pos = array_search(max($delivery_price_array),$delivery_price_array);	//max
        }else{
            $pos = array_search(min($delivery_price_array),$delivery_price_array);	//min
        }
    }

    return $delivery_price_array[$pos];
}

function getOneDeliveryPrice($pid){

    global $layout_config,$user,$shop_product_type,$sns_product_type;
    global $master_db;

    if($pid == ""){
        return 0;
    }else{

        $sql = "select
					dt.*,
					p.delivery_type
				from
					shop_product as p
					inner join shop_product_delivery as pd on (p.id = pd.pid)
					inner join shop_delivery_template as dt on (pd.dt_ix = dt.dt_ix)
				where
					p.id = '".$pid."' and pd.pid = '".$pid."'
					and dt.product_sell_type = 'R'";

        $master_db->query($sql);
        $rows = $master_db->fetchall();

        for($i=0;$i<count($rows);$i++){	//총주문수량과 총주문금액을 가져오기 2014-07-24 이학봉
            //$total_pcount += $rows[$i][pcount];	//주문수량
            $total_totalprice += $rows[$i][totalprice];	//총주문금액
        }

        for($i=0;$i<count($rows);$i++){	//장바구니 분리별로 배송비 구하기 시작

            $dt_ix = $rows[$i][dt_ix];
            $delivery_policy = $rows[$i][delivery_policy];	//배송비 조건타입
            $delivery_method = $rows[$i][delivery_method];	//배송방법
            $pcount = $rows[$i][pcount];	//주문수량
            $totalprice = $rows[$i][totalprice];	//총주문금액
            $delivery_package = $rows[$i][delivery_package];	//Y:묶음배송 N:개별배송

//echo "$delivery_package"."<br>";

            $template_infos = delivery_template_info($dt_ix);

            //조건배송 타입(배송정책)	1:무료배송 2:고정배송비 3:주문결제금액 할인 4:수량별할인 5:출고지별 배송비 6: 상품1개단위 배송비
            if($delivery_policy == "1"){	//1:무료배송

                $delivery_price_array[99] = '0';	//무료배송이 잇을경우 키값을 99 로 넣어서 구분하게 처리함

            }else if($delivery_policy == "2"){	//2:고정배송비

                $delivery_price = $template_infos[delivery_price];

            }else if($delivery_policy == "3"){	//3:주문결제금액 할인
                /*
			$sql = "select * from shop_delivery_terms where dt_ix = '".$dt_ix."' and product_sell_type = '".$template_infos[product_sell_type]."' and delivery_policy_type = '".$delivery_policy."' order by delivery_basic_terms DESC";
			$master_db->query($sql);
			$delivery_terms_array = $master_db->fetchall();

			$delivery_price = '0';	//최초 시작을 0으로 설정하고 주문할인별, 수량별 체크시 사용 2014-08-01 이학봉
			for($k=0;$k<count($delivery_terms_array);$k++){
				if($total_totalprice <= $delivery_terms_array[$k][delivery_basic_terms]){
					//echo "$total_totalprice"." == ".$delivery_terms_array[$k][delivery_basic_terms]."<br>";
					$delivery_price = $delivery_terms_array[$k][delivery_price];
				}

				if($delivery_price == '0'){
					//$delivery_price = $delivery_terms_array[0][delivery_price];
				}

			}
			*/

                $sql = "select * from shop_delivery_terms where dt_ix = '".$dt_ix."' and product_sell_type = '".$template_infos[product_sell_type]."' and delivery_policy_type = '".$delivery_policy."' order by delivery_basic_terms ASC";
                $master_db->query($sql);
                $delivery_terms_array = $master_db->fetchall();

                $delivery_price = '0';
                for($k=0;$k<count($delivery_terms_array);$k++){
                    if($total_totalprice <= $delivery_terms_array[$k][delivery_basic_terms]){
                        $delivery_price = $delivery_terms_array[$k][delivery_price];
                        break;
                    }
                }



            }else if($delivery_policy == "4"){	//4:수량별할인

                if($template_infos[extra_charge] == '1'){	//할인율 적용	이부분은 필요없을거 같음
                    $asc = 'ASC';
                }else{										//할증율 적용
                    $asc = 'DESC';
                }

                $sql = "select * from shop_delivery_terms where dt_ix = '".$dt_ix."' and product_sell_type = '".$template_infos[product_sell_type]."' and delivery_policy_type = '".$delivery_policy."' order by delivery_price DESC";	//수량별 체크는 DESC 로 해야함 2014-08-21 이학봉
                //echo nl2br($sql)."<br><br>";

                $master_db->query($sql);
                $delivery_terms_array = $master_db->fetchall();

                $delivery_price = '0';	//최초 시작을 0으로 설정하고 주문할인별, 수량별 체크시 사용 2014-08-01 이학봉
                $is_pcount_delivery = false;
                for($k=0;$k<count($delivery_terms_array);$k++){
                    //echo "$k"." == "."$pcount"." == ".$delivery_terms_array[$k][delivery_price]."<br>";
                    if($pcount >= $delivery_terms_array[$k][delivery_price]){
                        $delivery_price = $delivery_terms_array[$k][delivery_basic_terms];
                        $is_pcount_delivery = true;
                        break;
                    }
                    //echo "$delivery_price"."<br>";
                    if($is_pcount_delivery == false){
                        $delivery_price = $template_infos[delivery_cnt_price];
                    }
                }

            }else if($delivery_policy == "5"){	//5:출고지별 배송비 //잠시 보류

                $delivery_price = '0';
            }else if($delivery_policy == "6"){	//6:상품1개단위 배송비
                //echo "$delivery_policy"."<br>";
                $delivery_price = $pcount * $template_infos[delivery_unit_price];
            }

            if($delivery_method == '4'){	//배송방법이 방문수령일 경우 무조건 0원
                $delivery_price = '0';
            }else{
                $delivery_price = $delivery_price;
            }

            //도서산간 배송비 관련 시작 2014-05-19 이학봉
            //$addr = "서울시 서초구 양재동";
            if($addr !=""){

                $delivery_region_use = $template_infos[delivery_region_use]; //도서산간 배송정책 사용유무

                if( $delivery_region_use == '1'){	//무료배소상품정책 = 2 이고 도서산간배송비 정책 = 1 일경우 추가배송비 부여한다.
                    $sql = "select * from shop_product_region_delivery where region_delivery_type = 1 and dt_ix='".$dt_ix."' and product_sell_type = '".$template_infos[product_sell_type]."' order by prd_ix ASC";
                    //echo nl2br($sql)."<br><br>";
                    $master_db->query($sql);
                    $data_array = $master_db->fetchall();

                    for($j=0;$j<count($data_array);$j++){
                        if(strpos($addr,$data_array[$j][region_name_text]) !== false){
                            $delivery_region_price = $data_array[$j][region_name_price];
                            break;
                        }
                    }
                }

            }
            //도서산간 배송비 관련 끝 2014-05-19 이학봉

            $delivery_price_array[$i] = $delivery_price + $delivery_region_price;

            unset($delivery_price);
        }

        //echo "$delivery_product_policy"."<br>";
        if(is_array($delivery_price_array)){	//임시처리
            /*무료배송 배송비 조건이 잇을경우 해당 배송비는 전체 무료로 처리함 시작 2014-05-19 이학봉*/
            if(array_key_exists('99',$delivery_price_array)){
                unset($delivery_price_array); //배열 초기화후 0으로 다시 넣어준다.
                $delivery_price_array[0] = '0';
            }
            /*무료배송 배송비 조건이 잇을경우 해당 배송비는 전체 무료로 처리함 끝 2014-05-19 이학봉*/

            if($delivery_product_policy == '1'){	// 1: 큰배송비 2:적은배송비
                $pos = array_search(max($delivery_price_array),$delivery_price_array);	//max
            }else{
                $pos = array_search(min($delivery_price_array),$delivery_price_array);	//min
            }
        }


        if($delivery_price_array[$pos] == ''){
            return "<font color='red'><b>배송비 미지정</b></font>";
        }else{
            return $delivery_price_array[$pos];
        }
    }

}


function ProductDeliveryPackage($pid){

    global $layout_config,$user,$shop_product_type,$sns_product_type;
    global $master_db;

    if($pid == ""){
        return false;
    }else{
        $sql = "select
					dt.*
				from
					shop_product as p
					inner join shop_product_delivery as pd on (p.id = pd.pid)
					inner join shop_delivery_template as dt on (pd.dt_ix = dt.dt_ix)
				where
					p.id = '".$pid."' and pd.pid = '".$pid."'
					and dt.product_sell_type = 'R'";
        $master_db->query($sql);
        $master_db->fetch();

        $delivery_package = $master_db->dt[delivery_package];

        return $delivery_package;
    }

}

function getRegionDeliveryPrice($company_id_array,$pid_array,$addr){
    global $layout_config,$user;
    global $slave_mdb;

    //$slave_mdb = new Database;


    //추가배송비 관련 2014-01-17 이학봉 시작
    if(count($company_id_array) > 0 && $addr !=""){
        for($i=0;$i<count($company_id_array);$i++){
            $company_id = $company_id_array[$i];
            $sql = "select * from common_seller_delivery where company_id = '".$company_id."'";
            $slave_mdb->query($sql);
            $slave_mdb->fetch();
            $delivery_free_policy = $slave_mdb->dt[delivery_free_policy];	//무료배송 상품정책 사용 1: 전체무료 2:도서산간배송비 추가
            $delivery_policy = $slave_mdb->dt[delivery_policy];	//본사기본배송정책 사용유무 1 사용 2 입점업체 배송정책
            $delivery_region_policy = $slave_mdb->dt[delivery_region_policy]; //도서산간 배송정책 사용유무

            if($delivery_policy == '1'){
                $company_id = $_SESSION[shopcfg][company_id];
            }else{
                $company_id = $company_id;
            }

            //echo "$company_id"." :: "."delivery_free_policy  :: "."$delivery_free_policy"."<br>";
            //echo "delivery_region_policy  :: "."$delivery_region_policy"."<br>";

            if($delivery_free_policy == '2' && $delivery_region_policy == '1'){	//무료배소상품정책 = 2 이고 도서산간배송비 정책 = 1 일경우 추가배송비 부여한다.
                $sql = "select * from shop_region_delivery where region_delivery_type = 1 and company_id='".$company_id."' order by rd_ix ASC";
                $slave_mdb->query($sql);
                $data_array = $slave_mdb->fetchall();

                for($j=0;$j<count($data_array);$j++){
                    //echo $data_array[$j][region_name_text]." ::: ".$addr." ::: ".$data_array[$j][region_name_price]."<br>";
                    //echo $addr,$data_array[$j][region_name_text];
                    if(strpos($addr,$data_array[$j][region_name_text]) !== false){
                        $delivery_region_price_company_id = $data_array[$j][region_name_price];
                        break;
                    }
                }
            }

            $total_region_price_company_id += $delivery_region_price_company_id;

        }
    }

    if(count($pid_array) > 0 && $addr !=""){
        for($i=0;$i<count($pid_array);$i++){
            $pid = $pid_array[$i];

            $sql = "select
					csd.*,
					p.admin
					from
						shop_product as p
						left join common_seller_delivery as csd on (p.admin = csd.company_id)
					where
						p.id = '".$pid."'";

            $slave_mdb->query($sql);
            $slave_mdb->fetch();
            $delivery_free_policy = $slave_mdb->dt[delivery_free_policy];	//무료배송 상품정책 사용 1: 전체무료 2:도서산간배송비 추가
            $delivery_policy = $slave_mdb->dt[delivery_policy];	//본사기본배송정책 사용유무 1 사용 2 입점업체 배송정책
            $delivery_region_policy = $slave_mdb->dt[delivery_region_policy]; //도서산간 배송정책 사용유무

            if($delivery_policy == '1'){
                $company_id = $_SESSION[shopcfg][company_id];
            }else{
                $company_id = $slave_mdb->dt[admin];
            }
            //echo "$company_id"." :: "."delivery_free_policy  :: "."$delivery_free_policy"."<br>";
            //echo "delivery_region_policy  :: "."$delivery_region_policy"."<br>";
            if($delivery_free_policy == '2' && $delivery_region_policy == '1'){	//무료배소상품정책 = 2 이고 도서산간배송비 정책 = 1 일경우 추가배송비 부여한다.
                $sql = "select * from shop_region_delivery where region_delivery_type = 1 and company_id='".$company_id."' order by rd_ix ASC";
                //	echo nl2br($sql)."<br>";
                $slave_mdb->query($sql);
                $data_array = $slave_mdb->fetchall();

                for($j=0;$j<count($data_array);$j++){
                    //echo $data_array[$j][region_name_text]."<br>";
                    if(strpos($addr,$data_array[$j][region_name_text]) !== false){
                        $delivery_region_price_pid = $data_array[$j][region_name_price];
                        //echo "$delivery_region_price_pid";
                        break;
                    }
                }
            }

            $total_region_price += $delivery_region_price_pid;
        }
    }

//추가배송비 관련 2014-01-17 이학봉 시작

    return $total_region_price_company_id + $total_region_price;

}


function getDeliveryPrice5($company_id,$delivery_company, $delivery_yn=0){//수정본 kbk 11/11/02
    global $layout_config,$user;
    global $slave_mdb;

    //print_r($_SESSION);
    if($user[code] != ""){
        $where = " and mem_ix = '".$user[code]."'  and choice_prod = '$delivery_yn'";
        $groupby = " group by mem_ix ";
    }else{
        if($cart_key != "") $where = " and cart_key = '".$cart_key."'  and choice_prod = '$delivery_yn'";
        else $where = " and cart_key = '".session_id()."'  and choice_prod = '$delivery_yn'";
        $groupby = " group by cart_key ";
    }
    if($delivery_company == "WE"){
        $delivery_company_where = " and c.delivery_company = 'WE' ";
        $delivery_company_where2 = " and p.delivery_company = 'WE' ";
    }else if($delivery_company == "MI"){
        $delivery_company_where = " and c.delivery_company != 'WE' ";
        $delivery_company_where2 = " and p.delivery_company != 'WE' ";
    }
    //$slave_mdb = new Database;

    //업체별로 배송비 부가 정책 셀렉트
    /*
	$sql = "select csd.company_id, delivery_policy, delivery_freeprice , delivery_price, delivery_basic_policy , delivery_product_policy  from ".TBL_COMMON_SELLER_DELIVERY." csd, ".TBL_COMMON_COMPANY_DETAIL." ccd where csd.company_id = ccd.company_id and ccd.com_type = 'A' ";
	$slave_mdb->query($sql);
	$slave_mdb->fetch();
	*/
    $admin_delievery_policy = getTopDeliveryPolicy($slave_mdb);//$slave_mdb->dt;

    $sql = "select csd.company_id, delivery_policy, delivery_freeprice , delivery_price, delivery_basic_policy, delivery_product_policy  from ".TBL_COMMON_SELLER_DELIVERY." csd where company_id = '$company_id' ";
    $slave_mdb->query($sql);
    $slave_mdb->fetch();
    $seller_delievery_policy = $slave_mdb->dt;


    if($delivery_company == "MI" || $delivery_company == ""){
        $sql = "select case when delivery_policy = 1 then  '".$admin_delievery_policy[delivery_free_policy]."' else delivery_free_policy end as delivery_free_policy ,
		case when delivery_policy = 1 then  '".$admin_delievery_policy[delivery_product_policy]."' else delivery_product_policy end as delivery_product_policy ,
		case when delivery_policy = 1 then  '".$admin_delievery_policy[delivery_freeprice]."' else delivery_freeprice end as delivery_freeprice,
		case when delivery_policy = 1 then '".$admin_delievery_policy[delivery_basic_policy]."' else delivery_basic_policy end as delivery_basic_policy ,
		case when delivery_policy = 1 then '".$admin_delievery_policy[delivery_price]."' else delivery_price end as delivery_price
		from ".TBL_COMMON_SELLER_DELIVERY." where company_id = '$company_id' ";
    }else{
        /*
		$sql = "select delivery_free_policy,delivery_product_policy,delivery_freeprice,delivery_basic_policy,delivery_price
				from ".TBL_COMMON_SELLER_DELIVERY." csd, ".TBL_COMMON_COMPANY_DETAIL." ccd
				where csd.company_id = ccd.company_id and ccd.com_type = 'A'  ";
		*/
        $sql = getTopDeliveryPolicy($slave_mdb,"sql");

    }
    //echo $sql;
    $slave_mdb->query($sql);
    $slave_mdb->fetch();
    $delivery_free_policy = $slave_mdb->dt[delivery_free_policy]; //업체 배송부가정보 무료배송에대한 정책
    $delivery_product_policy = $slave_mdb->dt[delivery_product_policy]; //업체 배송부가정보 배송비 가격에대한 정책
    $delivery_freeprice = $slave_mdb->dt[delivery_freeprice]; //업체 무료배송 제한금액
    $delivery_basic_policy = $slave_mdb->dt[delivery_basic_policy]; //업체 선불,착불,무료 에대한 정책
    $delivery_price = $slave_mdb->dt[delivery_price]; //업체 기본 배송비
    if($delivery_product_policy == "2"){ //업체 배송부가정보 배송비 가격에대한 정책이 큰금액이면
        $orderby = "desc"; //배송금액 높은순으로 select
    }else if($delivery_product_policy == "3"){ //업체 배송부가정보 배송비 가격에대한 정책이 작은금액이면
        $orderby = "asc"; //배송금액 낮은순으로 select
    }
    //상품에 대한 배송정책 가져오는 select
    if($_SESSION["user"]["sale_rate"] != ""){
        $sql = "select c.id,(c.totalprice*(100-".$_SESSION["user"]["sale_rate"].")/100) as totalprice, pcount,p.delivery_price,p.delivery_package,delivery_product_policy,p.free_delivery_yn,p.free_delivery_count,p.delivery_policy
			from shop_cart c , shop_product p where c.id = p.id and company_id = '$company_id' $delivery_company_where $where
			order by p.delivery_price $orderby"; //c.totalprice,
    }else{
        $sql = "select c.id,c.totalprice, pcount,p.delivery_price,p.delivery_package,delivery_product_policy,p.free_delivery_yn,p.free_delivery_count,p.delivery_policy
			from shop_cart c , shop_product p where c.id = p.id and company_id = '$company_id' $delivery_company_where $where
			order by p.delivery_price $orderby"; //c.totalprice as totalprice_for_delivery_price,
    }

    //echo nl2br($sql)."<br>";
    $slave_mdb->query($sql);
    $rows = array();
    $rows = $slave_mdb->fetchall();

    $isDefault = 0;
    $isFree = false;
    $isPackage = false;
    $isFree_sum = false;
    $isFree_sum2 = false;
    $maxPrice = 0;
    $minPrice = 0;
    $packagePrice = 0;		// 묶음 배송 가격
    $individualPrice = 0;	// 개별 배송 가격
    $individualPrice2=0;
    $individualPrice3=0;
    $indivPrice = 0;		// 상품별배송비+기본배송비 인 경우
    if(is_array($rows)){

        foreach($rows as $_key=>$_val)	{
            if($_val['delivery_policy'] == '2')	{	// 상품별 배송 정책 인 경우
                // 상품별 배송비가 무료
                if($_val['delivery_product_policy'] == 3)	{
                    $isFree = true;
                    if($_val['delivery_package'] == 'Y')	{
                        $isPackage = true;
                        //$individualPrice += $delivery_price;
                    } else {
                        $isFree_sum2 = true;
                    }
                    continue;
                }

                // 구매 수량당 무료배송인 경우
                if($_val['free_delivery_yn'] == 'Y' && $_val['free_delivery_count'] <= $_val['pcount'])	{
                    $isFree = true;
                    if($_val['delivery_package'] == 'Y')	{
                        $isPackage = true;
                        //$individualPrice += $delivery_price;
                    } else {
                        $isFree_sum = true;
                        $individualPrice2 += $_val['delivery_price'];
                    }
                    continue;
                }

                // 묶음배송 불가 상품인 경우 - 무조건 배송비 추가 (구매합계금액과 상관없음)
                if($_val['delivery_package'] == 'Y')	{
                    if($_val['free_delivery_yn'] == 'Y') $individualPrice += $_val['delivery_price'];
                    else if($_val['free_delivery_yn'] == 'N') {
                        if($_val['totalprice'] >= $delivery_freeprice) $individualPrice += 0;
                        else  $individualPrice += $_val['delivery_price'];
                    }
                    continue;
                }

                // 묶음 배송 가능한 상품인 경우
                $packagePrice += $_val['totalprice'];
                $individualPrice3 = $_val['delivery_price'];
                //echo $_val['totalprice']."<br><br>";

                // 개별 상품 가격저장
                //$indivPrice += ($_val['totalprice'] < $delivery_freeprice)	?	$_val['delivery_price']:0;

                // 묶음 배송비 정책에 따른 저장
                if(!$minPrice)	$minPrice = $_val['delivery_price'];
                if($minPrice > $_val['delivery_price'])	$minPrice = $_val['delivery_price'];	// 작은값 지정
                if($maxPrice < $_val['delivery_price'])	$maxPrice = $_val['delivery_price'];	// 큰값 지정
            }	else	{
                // 배송비 무료인 경우
                if($delivery_basic_policy == 3)	{
                    $isFree = true;
                    if($_val['delivery_package'] == 'Y')	{
                        $isPackage = true;
                        //$individualPrice += $delivery_price;
                    } else {
                        $isFree_sum = true;
                        $individualPrice2 += $delivery_price;
                    }
                    continue;
                }

                // 구매 수량당 무료배송인 경우
                if($_val['free_delivery_yn'] == 'Y' && $_val['free_delivery_count'] <= $_val['pcount'])	{
                    $isFree = true;
                    if($_val['delivery_package'] == 'Y')	{
                        $isPackage = true;
                        //$individualPrice += $delivery_price;
                    } else {
                        $isFree_sum = true;
                        $individualPrice2 += $delivery_price;
                    }
                    continue;
                }

                // 묶음배송 불가 상품인 경우 - 무조건 배송비 추가 (구매합계금액과 상관없음)
                if($_val['delivery_package'] == 'Y')	{
                    if($_val['free_delivery_yn'] == 'Y') $individualPrice += $delivery_price;
                    else if($_val['free_delivery_yn'] == 'N') {
                        if($_val['totalprice'] >= $delivery_freeprice) $individualPrice += 0;
                        else  $individualPrice += $delivery_price;
                    }
                    continue;
                }

                // 묶음 배송 가능한 상품인 경우
                $packagePrice += $_val['totalprice'];
                $individualPrice3 = $delivery_price;

                // 기본 배송비 포함 유무
                $isDefault = $delivery_price;

                //echo $_val['totalprice'];

                /*if($_val['totalprice'] >= $delivery_freeprice) {
					if($_val['delivery_package'] != 'Y') {
						$individualPrice2 += $delivery_price;
					}
				}*/

                if(!$minPrice)	$minPrice = $delivery_price;
                if($minPrice > $delivery_price)	$minPrice = $delivery_price;	// 작은값 지정
                if($maxPrice < $delivery_price)	$maxPrice = $delivery_price;	// 큰값 지정
            }
        }

    }

    // 무료 배송상품이 있고 무료배송상품이 있을때 전체 배송비 무료(delivery_free_policy = 1)일 경우
    if($isFree && $delivery_free_policy == 1 && !$isPackage)	{
        echo 1;
        if($packagePrice < $delivery_freeprice) {
            if($delivery_product_policy  == 2)	{
                $packagePrice2 = $maxPrice;
            }	elseif($delivery_product_policy == 3)	{
                $packagePrice2 = $minPrice;
            }	else	{
                $packagePrice2 = $indivPrice + $isDefault;
            }
            $packagePrice = 0;
            //if($isFree_sum2) $individualPrice2=0;
            $packagePrice =$packagePrice+$packagePrice2-$individualPrice2;
            if($isFree_sum2) $packagePrice -= $individualPrice3;
        } else {
            $packagePrice = 0;
            //if($isFree_sum2) $individualPrice2=0;
            $packagePrice =$packagePrice+$packagePrice2-$individualPrice2;
        }

    } else if($isFree && $delivery_free_policy == 1 && $isPackage)	{
        echo 4;
        if($packagePrice >= $delivery_freeprice) {
            if($isFree_sum) $packagePrice2 = 0;
            else $packagePrice2 = $individualPrice3;
        }
        if($delivery_product_policy  == 2)	{
            $packagePrice = $maxPrice;
        }	elseif($delivery_product_policy == 3)	{
            $packagePrice = $minPrice;
        }	else	{
            $packagePrice = $indivPrice + $isDefault;
        }

        $packagePrice -= $individualPrice2;
        $packagePrice -= $packagePrice2;
        if($isFree_sum2) $packagePrice -= $individualPrice3;

    }	elseif($packagePrice < $delivery_freeprice)	{
        echo 2;
        if($delivery_product_policy  == 2)	{
            $packagePrice = $maxPrice;
        }	elseif($delivery_product_policy == 3)	{
            $packagePrice = $minPrice;
        }	else	{
            $packagePrice = $indivPrice + $isDefault;
        }
        if($isFree_sum2) $packagePrice -= $individualPrice3;
    }	else	{
        echo 3;
        $packagePrice = 0;
    }
    echo "<br />".$packagePrice."<br />".$individualPrice;
    $totalPrice = $packagePrice + $individualPrice;
    return $totalPrice;
}


function goods_review($start=0,$size=5)
{
    global $slave_mdb;
    //$slave_mdb = new Database;

    $sql = "SELECT uf_subject,uf_ix,pid,ucode, uf_name,uf_contents,uf_hit,IFNULL(uf_valuation,0) as uf_valuation,regdate,(select pname from shop_product where id = pid) as pname,
			(select sellprice from shop_product where id = pid) as sellprice FROM ".TBL_SHOP_BBS_USEAFTER." order by regdate desc limit $start,$size";

    $slave_mdb->query($sql);
    return $slave_mdb->fetchall();

    exit;
}

function bestsell($start,$size)
{
    global $slave_mdb;
    //$slave_mdb = new Database;

    $sql = "select p.id,p.pname,p.shotinfo,p.listprice, p.sellprice,r.cid
			from shop_product p left join shop_product_relation r
			on p.id = r.pid
			where p.disp = '1'
			order by p.order_cnt desc limit $start,$size";

    $slave_mdb->query($sql);

    return $slave_mdb->fetchall();

    exit;
}

function parentbbsSelect($bbs_top_ix){
    global $slave_mdb;
    $sql = "select mem_ix from bbs_counsel where bbs_ix = $bbs_top_ix ";
    $slave_mdb->query($sql);

    $slave_mdb->fetch();

    return $slave_mdb->dt[mem_ix];
}

//실아이피주소를 가져온다
function get_userip() {
    $first_ip = getenv(REMOTE_ADDR);
    $second_ip = getenv(HTTP_X_FORWARDED_FOR); // 방화벽 + 사설아이피
    $third_ip = getenv(HTTP_CLIENT_IP); // 방화벽 + 공인아이피

    return $first_ip;

    if (!$second_ip && !$third_ip) {
        return $first_ip;
    } else {
        if($second_ip){
            return "$first_ip/$second_ip";
        } else {
            return "$first_ip/$third_ip";
        }
    }
}
function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) //check ip from share internet
    {
        $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) //to check ip is pass from proxy
    {
        $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
        $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function filter_by_value ($array, $index, $value){
    if(is_array($array) && count($array)>0)
    {
        foreach(array_keys($array) as $key){
            $temp[$key] = $array[$key][$index];

            if ($temp[$key] == $value){
                $newarray[$key] = $array[$key];
            }
        }
    }
    return $newarray;
}


function real_visit_member($cf_ix)
{
    $shmop = new Shared("cafe_visitinfo");
    $cafe_visitinfo = $shmop->getObjectForKey("cafe_visitinfo");
//	print_r($cafe_visitinfo);
    return filter_by_value($cafe_visitinfo, 'cf_ix', $cf_ix);
}

function real_visit_cnt($cf_ix)
{
    $shmop = new Shared("cafe_visitinfo");
    $cafe_visitinfo = $shmop->getObjectForKey("cafe_visitinfo");
    $this_cafe_visitinfo = filter_by_value($cafe_visitinfo, 'cf_ix', $cf_ix);
    return count($this_cafe_visitinfo);
}


function point_add($id,$recom_id = "",$code = "")
{
    global $user;
    global $slave_mdb, $master_db;
    //$slave_mdb = new Database;

    $slave_mdb->query("select * from mallstory_point_policy where id = '$id'");

    $slave_mdb->fetch();

    $div_name = $slave_mdb->dt[div_name];
    $variation = $slave_mdb->dt[variation];
    $point_use = $slave_mdb->dt[point_use];
    $point = $slave_mdb->dt[point];


    if($recom_id != "")
    {
        $slave_mdb->query("select code from ".TBL_COMMON_USER." where id = '$recom_id'");
        $slave_mdb->fetch();
        $uid = $slave_mdb->dt[code];
    }
    else
    {
        if($code != "")
        {
            $uid = $code;
        }
        else
        {
            $uid = $user[code];
        }
    }


    if(!($variation == "M" && $user[auth_yn] == "E"))
    {

        $slave_mdb->query("select sum(point) as point from mallstory_point where uid = '$uid'");
        $slave_mdb->fetch();

        if($variation == "M" && $slave_mdb->dt[point] < $point && $id == "4")
        {
            echo "<script>alert('포인트가 최소한 ".$point."point 이상 필요합니다.');history.back();</script>";
            exit;
        }


        if($point_use == "Y")
        {

            if($variation == "M")
            {
                if($slave_mdb->dt[point] < $point)
                {
                    $point = "-".$slave_mdb->dt[point];
                }
                else
                {
                    $point = "-".$point;
                }
            }


            $master_db->query("insert into mallstory_point(uid,point,div_name,regdate) values('".$uid."','$point','$div_name',NOW())");
        }
    }
}



function memorder($code, $pid) {
    global $slave_mdb;
    //$slave_mdb = new Database;
    //장바구니
    $sql = "SELECT od.option_text FROM shop_order o, shop_order_detail od WHERE o.oid = od.oid AND od.pid = '".$pid."' AND uid = '".$code."' limit 0, 1";
    $slave_mdb->query($sql);
    $slave_mdb->fetch();
    $option_text = str_replace("<br>", " ", $slave_mdb->dt[option_text]);
    return $option_text;
}
function cartList($code, $cnt = 5) {

    global $shop_product_type;
    global $slave_mdb;
    //$slave_mdb = new Database;
    //장바구니
    $sql = "select c.*, p.shotinfo from shop_cart c, ".TBL_SHOP_PRODUCT." p 
			where c.id = p.id and c.mem_ix='".$code."' and c.product_type in (".implode(' , ',$shop_product_type).") 
			AND est_ix = '0' 
			GROUP BY id
			order by c.regdate
			limit 0, $cnt
			";	//AND c.delivery_package = 'N'

    //$sql = "select c.*, p.shotinfo from shop_cart c, ".TBL_SHOP_PRODUCT." p where c.id = p.id and c.mem_ix='".$code."' and c.product_type in (".implode(' , ',$shop_product_type).") order by c.regdate desc limit 0,$cnt";
    $slave_mdb->query($sql);
    $carts = $slave_mdb->fetchall();
    if(is_array($carts)){
        foreach ($carts as $key => $sub_array) {
            $goods_infos[$carts[$key][id]][pid] = $carts[$key][id];
            $goods_infos[$carts[$key][id]][amount] = 1;
            $goods_infos[$carts[$key][id]][cid] = $carts[$key][cid];
            $goods_infos[$carts[$key][id]][depth] = 1;//$carts[0][depth];
        }
        $discount_info = DiscountRult($goods_infos);// $carts[0][cid], $carts[0][depth]);
    }

    if(is_array($carts)){
        foreach ($carts as $key => $sub_array) {

            $select_ = array("icons_list"=>explode(";",$sub_array[icons]));
            array_insert($sub_array,14,$select_);

            $discount_item = $discount_info[$sub_array[id]];
            //print_r($discount_item);
            $_dcprice = $sub_array[sellprice];
            if(is_array($discount_item)){
                foreach($discount_item as $_key => $_item){
                    if($_item[discount_value_type] == "1"){ // %
                        //echo $_item[discount_value]."<br>";
                        $_dcprice = roundBetter($_dcprice*(100 - $_item[discount_value])/100, $_item[round_position], $_item[round_type]) ;//$_dcprice*(100 - $_item[discount_value])/100;
                        /*
						echo "round_position : ".$_item[round_position]."<br>";
						echo "round_type : ".$_item[round_type]."<br>";
						echo "이전할인가 : ".$_dcprice."<br>";
						echo roundBetter($_dcprice*(100 - $_item[discount_value])/100, $_item[round_position], $_item[round_type]) ;			;
						*/
                    }else if($_item[discount_value_type] == "2"){// 원
                        $_dcprice = $_dcprice - $_item[discount_value];
                    }
                    $_item[expectation_discount_price] = $sub_array[sellprice]-$_dcprice;
                    //$discount_desc[] = $_item[discount_type]."-".$_item[discount_value]." ".($_item[discount_value_type] == "1" ? "%":"원")."-".$_dcprice."원";
                    $discount_desc[] = $_item;

                }
                //print_r($discount_desc);
            }
            $_dcprice = array("dcprice"=>$_dcprice);
            array_insert($sub_array,52,$_dcprice);
            $discount_desc = array("discount_desc"=>$discount_desc);
            array_insert($sub_array,53,$discount_desc);

            $carts[$key] = $sub_array;
        }
    }

    if(count($carts))
    {
        for($i=0 ; $i < count($carts) ;$i++){
            $carts[$i]['listprice'] = getExchangeNationPrice($carts[$i]['listprice']);
            $carts[$i]['sellprice'] = getExchangeNationPrice($carts[$i]['sellprice']);
            $carts[$i]['dcprice'] = getExchangeNationPrice($carts[$i]['dcprice']);
        }
    }


    return $carts;
}
function historyList() {
    global $HISTORY;
    //print_r($HISTORY);

    //exit;
    if(is_array($HISTORY[$_SESSION["layout_config"]['mall_ix']])){
        foreach ($HISTORY[$_SESSION["layout_config"]['mall_ix']] as $key => $sub_array) {
            $goods_infos[$HISTORY[$_SESSION["layout_config"]['mall_ix']][$key][id]][pid] = $HISTORY[$_SESSION["layout_config"]['mall_ix']][$key][id];
            $goods_infos[$HISTORY[$_SESSION["layout_config"]['mall_ix']][$key][id]][amount] = 1;
            $goods_infos[$HISTORY[$_SESSION["layout_config"]['mall_ix']][$key][id]][cid] = $HISTORY[$_SESSION["layout_config"]['mall_ix']][$key][cid];
            $goods_infos[$HISTORY[$_SESSION["layout_config"]['mall_ix']][$key][id]][depth] = 1;//$HISTORY[0][depth];
        }
        if(count($goods_infos)>0){
            $discount_info = DiscountRult($goods_infos);// $HISTORY[0][cid], $HISTORY[0][depth]);
        }
    }
    //print_r($goods_infos);



    //print_r($discount_info);


    if(is_array($HISTORY[$_SESSION["layout_config"]['mall_ix']])){
//        print_r($HISTORY[$_SESSION["layout_config"]['mall_ix']]);
        foreach ($HISTORY[$_SESSION["layout_config"]['mall_ix']] as $key => $sub_array) {

            $select_ = array("icons_list"=>explode(";",$sub_array[icons]));
            array_insert($sub_array,14,$select_);

            $discount_item = $discount_info[$sub_array[id]];
            //print_r($discount_item);
            $_dcprice = $sub_array[sellprice];
            if(is_array($discount_item)){
                foreach($discount_item as $_key => $_item){
                    if($_item[discount_value_type] == "1"){ // %
                        //echo $_item[discount_value]."<br>";
                        $_dcprice = roundBetter($_dcprice*(100 - $_item[discount_value])/100, $_item[round_position], $_item[round_type]) ;//$_dcprice*(100 - $_item[discount_value])/100;
                        /*
						echo "round_position : ".$_item[round_position]."<br>";
						echo "round_type : ".$_item[round_type]."<br>";
						echo "이전할인가 : ".$_dcprice."<br>";
						echo roundBetter($_dcprice*(100 - $_item[discount_value])/100, $_item[round_position], $_item[round_type]) ;			;
						*/
                    }else if($_item[discount_value_type] == "2"){// 원
                        $_dcprice = $_dcprice - $_item[discount_value];
                    }
                    $_item[expectation_discount_price] = $sub_array[sellprice]-$_dcprice;
                    //$discount_desc[] = $_item[discount_type]."-".$_item[discount_value]." ".($_item[discount_value_type] == "1" ? "%":"원")."-".$_dcprice."원";
                    $discount_desc[] = $_item;

                }
                //print_r($discount_desc);
            }
            $_dcprice = array("dcprice"=>$_dcprice);
            array_insert($sub_array,52,$_dcprice);
            $discount_desc = array("discount_desc"=>$discount_desc);
            array_insert($sub_array,53,$discount_desc);

            $HISTORY[$_SESSION["layout_config"]['mall_ix']][$key] = $sub_array;
        }
    }

    /*
	if(count($HISTORY[$_SESSION["layout_config"]['mall_ix']]))
	{
		foreach($HISTORY[$_SESSION["layout_config"]['mall_ix']] as $key => $val){
			$HISTORY[$_SESSION["layout_config"]['mall_ix']][$key]['listprice'] = getExchangeNationPrice($HISTORY[$_SESSION["layout_config"]['mall_ix']][$key]['listprice']);
			$HISTORY[$_SESSION["layout_config"]['mall_ix']][$key]['sellprice'] = getExchangeNationPrice($HISTORY[$_SESSION["layout_config"]['mall_ix']][$key]['sellprice']);
			$HISTORY[$_SESSION["layout_config"]['mall_ix']][$key]['dcprice'] = getExchangeNationPrice($HISTORY[$_SESSION["layout_config"]['mall_ix']][$key]['dcprice']);
		}
	}
	*/

    return $HISTORY[$_SESSION["layout_config"]['mall_ix']];
}

function historyCnt() {
    global $HISTORY;
    $h_cnt=count($HISTORY[$_SESSION["layout_config"]['mall_ix']]);
    return "(".($h_cnt?$h_cnt:"0").")";
}

function historyCnt2() {
    global $HISTORY;
    $h_cnt=count($HISTORY[$_SESSION["layout_config"]['mall_ix']]);
    return ($h_cnt?$h_cnt:"0");
}
function wishList($code, $cnt = 5) {
    global $slave_mdb;
    //$slave_mdb = new Database;
    //관심상품
    if($slave_mdb->dbms_type == "oracle"){
        $slave_mdb->query("select w.wid, p.id, p.pname,p.global_pinfo, p.reserve, p.sellprice, trunc(p.sellprice,0) as price, p.shotinfo,p.brand_name from ".TBL_SHOP_WISHLIST." w, ".TBL_SHOP_PRODUCT." p where w.pid = p.id and w.mid ='".$code."' limit 0,$cnt ");
    }else{
        $slave_mdb->query("select wid, id, pname,global_pinfo,reserve, sellprice, format(sellprice,0) as price, shotinfo ,p.brand_name from ".TBL_SHOP_WISHLIST." w, ".TBL_SHOP_PRODUCT." p where w.pid = p.id and mid ='".$code."' limit 0,$cnt ");
    }
    $wishList = $slave_mdb->fetchall("object");

    if(is_array($wishList)){
        foreach ($wishList as $key => $val) {
            $wishList[$key][pname] = getGlobalTargetName($val[pname],$val[global_pinfo],'pname');
            //echo $wishList[$key][pname];
        }
    }
    //echo $wishlist[0]["pname"];

    if(is_array($wishList)){
        foreach ($wishList as $key => $sub_array) {
            $goods_infos[$wishList[$key][id]][pid] = $wishList[$key][id];
            $goods_infos[$wishList[$key][id]][amount] = 1;
            $goods_infos[$wishList[$key][id]][cid] = $wishList[$key][cid];
            $goods_infos[$wishList[$key][id]][depth] = 1;//$wishList[0][depth];
        }

        if(count($wishList)>0){
            $discount_info = DiscountRult($goods_infos);// $wishList[0][cid], $wishList[0][depth]);
        }
    }
    //print_r($goods_infos);
    //print_r($discount_info);

    if(is_array($wishList)){
        foreach ($wishList as $key => $sub_array) {

            $select_ = array("icons_list"=>explode(";",$sub_array[icons]));
            array_insert($sub_array,14,$select_);
            $discount_item = $discount_info[$sub_array[id]];
            //print_r($discount_item);
            $_dcprice = $sub_array[sellprice];
            if(is_array($discount_item)){
                foreach($discount_item as $_key => $_item){
                    if($_item[discount_value_type] == "1"){ // %
                        //echo $_item[discount_value]."<br>";
                        $_dcprice = roundBetter($_dcprice*(100 - $_item[discount_value])/100, $_item[round_position], $_item[round_type]) ;//$_dcprice*(100 - $_item[discount_value])/100;
                        /*
						echo "round_position : ".$_item[round_position]."<br>";
						echo "round_type : ".$_item[round_type]."<br>";
						echo "이전할인가 : ".$_dcprice."<br>";
						echo roundBetter($_dcprice*(100 - $_item[discount_value])/100, $_item[round_position], $_item[round_type]) ;			;
						*/
                    }else if($_item[discount_value_type] == "2"){// 원
                        $_dcprice = $_dcprice - $_item[discount_value];
                    }
                    $_item[expectation_discount_price] = $sub_array[sellprice]-$_dcprice;
                    //$discount_desc[] = $_item[discount_type]."-".$_item[discount_value]." ".($_item[discount_value_type] == "1" ? "%":"원")."-".$_dcprice."원";
                    $discount_desc[] = $_item;

                }
                //print_r($discount_desc);
            }
            $_dcprice = array("dcprice"=>$_dcprice);
            array_insert($sub_array,52,$_dcprice);
            $discount_desc = array("discount_desc"=>$discount_desc);
            array_insert($sub_array,53,$discount_desc);

            $wishList[$key] = $sub_array;
        }
    }

    if(count($wishList))
    {
        for($i=0 ; $i < count($wishList) ;$i++){
            $wishList[$i]['listprice'] = getExchangeNationPrice($wishList[$i]['listprice']);
            $wishList[$i]['sellprice'] = getExchangeNationPrice($wishList[$i]['sellprice']);
            $wishList[$i]['dcprice'] = getExchangeNationPrice($wishList[$i]['dcprice']);
        }
    }

    return $wishList;
}
function cartCnt($code = "") {

    global $shop_product_type;
    global $slave_mdb;
    //$slave_mdb = new Database;
    //장바구니

    //AND est_ix = '0' AND delivery_package = 'N' 추가  // AND delivery_package = 'N' 제거 왜 들어가있는거야?? JK160422
    if(!$code) $where = " where cart_key = '".session_id()."' and product_type in (".implode(' , ',$shop_product_type).") AND est_ix = '0'";
    else $where = " where mem_ix='".$code."' and product_type in (".implode(' , ',$shop_product_type).") AND est_ix = '0' ";

    $sql = "select * from shop_cart " . $where . " group by id, select_option_id";

    //echo $sql;
    $slave_mdb->query($sql);
    $total = $slave_mdb->total;

    if($total > 0){
        return $slave_mdb->total;
    }else{
        return 0;
    }
}
function wishCnt($code) {
    global $slave_mdb;
    //$slave_mdb = new Database;
    //관심상품
    $slave_mdb->query("select * from ".TBL_SHOP_WISHLIST." w, ".TBL_SHOP_PRODUCT." p where w.pid = p.id and mid ='".$code."' and p.disp in ('1') and state in ('0','1') ");
    $total = $slave_mdb->total;
    if($total > 0){
        return $slave_mdb->total;
    }else{
        return 0;
    }

}
function ingEventCnt() {
    global $slave_mdb;
    //$slave_mdb = new Database;

    $today = date("Ymd");
    //관심상품
    $slave_mdb->query("select * from shop_event where '".$today."' between event_use_sdate and event_use_edate ");
    $total = $slave_mdb->total;
    if($total > 0) return $slave_mdb->total;
}
function categoryMainMDChoiceGroup($cmg_ix) {
    global $slave_mdb;
    //$slave_mdb= new Database;

    $whereStr = " WHERE cmg.cmg_ix = cmpg.cmg_ix AND cmg.cmg_ix = cmpg.cmg_ix AND cmg.cmg_ix = '".$cmg_ix."' ";
    $sql = "SELECT cmpg.cmg_ix, cmpg.group_code FROM shop_category_main_goods cmg, shop_category_main_product_group cmpg  " . $whereStr;
    $slave_mdb->query($sql);
    $groupList = $slave_mdb->fetchall();

    return $groupList;
}

function categoryMainMDChoiceItem($cmg_ix, $group_code, $start="0", $max="8") {
    global $slave_mdb;
    //$slave_mdb= new Database;

    $whereStr = " WHERE p.id = cmpr.pid AND cmpr.cmg_ix = '".$cmg_ix."' AND cmpr.group_code = '".$group_code."' ";
    $sql = "SELECT p.* FROM shop_product p, shop_category_main_product_relation cmpr  " . $whereStr . " ORDER BY cmpr.vieworder desc LIMIT $start, $max";
    $slave_mdb->query($sql);
    $prodList = $slave_mdb->fetchall();

    return $prodList;
}

function print_shop_bank() {
    global $slave_mdb;
    $sql="SELECT * FROM ".TBL_SHOP_BANKINFO." WHERE disp='1' ";
    $slave_mdb->query($sql);
    if($slave_mdb->total>0) {
        return $slave_mdb->fetchall();
    }
}

function get_product_name($pid) {
    global $slave_mdb;
    $sql="SELECT pname,id,shotinfo FROM ".TBL_SHOP_PRODUCT." WHERE id='".$pid."' ";
    $slave_mdb->query($sql);

    return $slave_mdb->fetchall();
}

function get_order_detail_count($company_id,$oid) {
    global $slave_mdb;
    //$slave_mdb=new Database;
    $sql="SELECT count(od_ix) detail_cnt  FROM ".TBL_SHOP_ORDER_DETAIL." WHERE oid='".$oid."' AND company_id='".$company_id."' and status not in ('CA','CC','RA','RC','RI','EA','EC','ED','RD','EI')";
    $slave_mdb->query($sql);
    return $slave_mdb->fetchall();
}

function get_order_detail_count_status($company_id,$oid,$status) {//상태값 제한 없이 카운트 kbk 13/06/25
    global $slave_mdb;
    //$slave_mdb=new Database;
    $sql="SELECT count(od_ix) detail_cnt  FROM ".TBL_SHOP_ORDER_DETAIL." WHERE oid='".$oid."' AND company_id='".$company_id."' AND status='".$status."' ";
    $slave_mdb->query($sql);
    return $slave_mdb->fetchall();
}

function get_order_detail_count_no_status($company_id,$oid) {//상태값 제한 없이 카운트 kbk 13/06/25
    global $slave_mdb;
    //$slave_mdb=new Database;
    $sql="SELECT count(od_ix) detail_cnt  FROM ".TBL_SHOP_ORDER_DETAIL." WHERE oid='".$oid."' AND company_id='".$company_id."' ";
    $slave_mdb->query($sql);
    return $slave_mdb->fetchall();
}

function compare_domain() {
    global $_SERVER;
    $c_url=str_replace("www.","",$_SERVER["HTTP_HOST"]);
    $c_url=trim($c_url);
    return $c_url;
}

//리셀러 사용여부
function resellerInfo(){
    global $DOCUMENT_ROOT;
    include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class");
    $shmop = new Shared("reseller_rule");
    $shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/_shared/";
    $shmop->SetFilePath();
    $reseller_data = $shmop->getObjectForKey("reseller_rule");
    $reseller_data = unserialize(urldecode($reseller_data));

    $rsl_use = $reseller_data[rsl_use];

    return $rsl_use;
}




function fetch_product_event_info($pid,$r_type="",$s_num=0,$limit_num=2){
    global $shop_product_type;
    global $slave_mdb;
    //$slave_mdb=new Database;

    if(is_mobile()){
        $where .= " and agent_type = 'M' ";
    }else{
        $where .= " and agent_type = 'W' ";
    }


    if($r_type=="count") {
        $select_column=" COUNT(DISTINCT e.event_ix) AS cnt ";
        $add_query="";
    } else {
        $select_column=" DISTINCT e.event_ix, e.event_title, e.kind ";
        $add_query=" ORDER BY e.event_use_sdate DESC, e.regdate DESC LIMIT $s_num, $limit_num ";
    }

    $sql="SELECT ".$select_column."
			FROM ".TBL_SHOP_EVENT." e
			right JOIN shop_event_product_group epg ON e.event_ix=epg.event_ix AND epg.use_yn='Y' AND epg.insert_yn='Y'
			right JOIN ".TBL_SHOP_EVENT_PRODUCT_RELATION." epr ON epg.event_ix=epr.event_ix AND epg.group_code=epr.group_code AND epr.insert_yn='Y' AND epr.pid='".$pid."'
			right JOIN ".TBL_SHOP_PRODUCT." p ON epr.pid=p.id AND p.product_type in (".implode(' , ',$shop_product_type).") AND p.disp IN (1,3) AND p.state IN (0,1) AND p.id='".$pid."'
			WHERE e.event_use_sdate <= UNIX_TIMESTAMP(".date("Ymd").") and e.event_use_edate >= UNIX_TIMESTAMP(".date("Ymd").") AND e.disp=1 AND p.id='".$pid."' ".$where." ".$add_query;

    //echo nl2br($sql);
    $slave_mdb->query($sql);
    if($r_type=="count") {
        $slave_mdb->fetch();
        return $slave_mdb->dt["cnt"];
    } else {
        $fetch_result = $slave_mdb->fetchall();;
        //print_r($categorys);
        return $fetch_result;
    }
    exit;
}

function sum_order_reserve($oid,$basic_return="") {//주문에 대한 적립금 총 합 kbk 13/06/25
    global $slave_mdb;
    //$slave_mdb=new Database;

    $sql="SELECT SUM(reserve) AS reserve FROM ".TBL_SHOP_ORDER_DETAIL." WHERE oid='".$oid."' ";
    $slave_mdb->query($sql);
    $slave_mdb->fetch();
    $reserve=$slave_mdb->dt["reserve"];

    if($reserve==0) {
        if($basic_return=="") return "";
        else return $basic_return;
    } else {
        return $reserve;
    }
}

function shop_cart_option_stock($cart_ix,$pcnt) {//cart.php 에서 옵션 수량 체크 kbk 13/06/27
    global $slave_mdb;
    //$slave_mdb=new Database;

    $sql="SELECT o.option_kind, od.option_sell_ing_cnt, od.option_stock FROM shop_cart_options co LEFT JOIN ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." od ON co.opn_d_ix=od.id LEFT JOIN ".TBL_SHOP_PRODUCT_OPTIONS." o ON od.opn_ix=o.opn_ix WHERE co.cart_ix='".$cart_ix."' ";
    $slave_mdb->query($sql);

    if($slave_mdb->total) {
        $slave_mdb->fetch();
        $option_kind=$slave_mdb->dt["option_kind"];
        $option_sell_ing_cnt=$slave_mdb->dt["option_sell_ing_cnt"];
        $option_stock=$slave_mdb->dt["option_stock"];
        $cu_cnt=$option_stock - $option_sell_ing_cnt;

        if($option_kind=="b") {
            if($cu_cnt>0 && $cu_cnt>=$pcnt) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    } else {
        return false;
    }
}

function get_banner_by_cid($div_ix,$cid) {//카테고리 정보로 베너를 불러옴 kbk 13/06/30
    global $slave_mdb;
    //$slave_mdb=new Database;

    $today_srch = date("Y-m-d H:i:s");

    $where = "WHERE b.use_sdate <= '".$today_srch."' AND b.use_edate >= '".$today_srch."' AND b.banner_page='".$div_ix."' AND bp.bp_name LIKE '%".$cid."%' AND b.disp='1'";
    //$where = "WHERE '".$today_srch."' BETWEEN b.use_sdate AND b.use_edate AND bp.bp_name LIKE '%".$cid."%' ";

    $imgPath = $_SESSION["layout_config"][mall_data_root]."/images/banner/";

    $sql = "select b.banner_kind, b.banner_html
			from shop_bannerinfo b LEFT JOIN shop_banner_position bp ON b.banner_position=bp.bp_ix
			$where ORDER BY b.use_edate ASC LIMIT 1 ";
    $slave_mdb->query($sql);
    $slave_mdb->fetch();
    $banner_kind	= $slave_mdb->dt[banner_kind];

    if ($banner_kind == 1){	// 일반배너

        $sql = "select CONCAT('".$imgPath."',b.banner_ix,'/') AS imgPath, b.banner_ix, b.banner_kind, change_effect, banner_img,banner_img_on, banner_link,banner_target,banner_width,banner_height,b.disp
			from shop_bannerinfo b LEFT JOIN shop_banner_position bp ON b.banner_position=bp.bp_ix
			$where ORDER BY b.use_edate ASC LIMIT 1 ";
        $slave_mdb->query($sql);
        $slave_mdb->fetch();

        $banner_ix			= $slave_mdb->dt[banner_ix];
        $banner_img		= $slave_mdb->dt[banner_img];
        $banner_width	= $slave_mdb->dt[banner_width];
        $banner_height	= $slave_mdb->dt[banner_height];
        $banner_img_on	= $slave_mdb->dt[banner_img_on];
        $banner_on_use	= $slave_mdb->dt[banner_on_use];
        $banner_target	= $slave_mdb->dt[banner_target];

        if(substr_count($banner_img,'.swf') > 0){
            $mString .= "<script language='javascript'>generate_flash('".$layout_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img."', '".$banner_width."', '".$banner_height."');</script>";
        }else if ($banner_on_use=="Y" || $banner_img_on){	// 마우스오버시 바로 이미지가 바뀌는 오버기능 사용시
            $mString .= "<a href='/banner.link.php?banner_ix=".$banner_ix."' target='".$banner_target."'><img src='".$_SESSION["layout_config"][mall_data_root]."/images/banner/".$banner_ix."/".$banner_img."' width='".$banner_width."' height='".$banner_height."' onmouseover='this.src=\"".$imgPath.$banner_img_on."\"' onmouseout='this.src=\"".$imgPath.$banner_img."\"'></a>";//<li ".$class."></li>
        }else if ($banner_img_on){	// 롤오버 이미지가 있으면
            //$mString .= "<li>";
            $mString .= "<a href='/banner.link.php?banner_ix=".$banner_ix."' target='".$banner_target."'><img src='".$_SESSION["layout_config"][mall_data_root]."/images/banner/".$banner_ix."/".$banner_img."' width='".$banner_width."' height='".$banner_height."'></a>";
            //$mString .= "</li>";
            //$mString .= "<li>";
            $mString .= "<a href='/banner.link.php?banner_ix=".$banner_ix."' target='".$banner_target."'><img src='".$_SESSION["layout_config"][mall_data_root]."/images/banner/".$banner_ix."/".$banner_img_on."'  width='".$banner_width."' height='".$banner_height."'></a>";
            //$mString .= "</li>";
        }else{

            $mString .= "<a href='/banner.link.php?banner_ix=".$banner_ix."' target='".$banner_target."'><img src='".$_SESSION["layout_config"][mall_data_root]."/images/banner/".$banner_ix."/".$banner_img."'  width='".$banner_width."' height='".$banner_height."'></a>";//<li ".$class."></li>
        }
    } else if($banner_kind == 2) {	// 플래시배너
        $sql = "select b.banner_ix, b.banner_kind, change_effect, banner_img,banner_link,banner_target,banner_width,banner_height,b.disp, bd.*,
			IFNULL(sum(bc.ncnt),0) as ncnt
			from shop_bannerinfo b left join shop_bannerinfo_detail bd on b.banner_ix = bd.banner_ix
			left join logstory_banner_click bc on b.banner_ix = bc.banner_ix LEFT JOIN shop_banner_position bp ON b.banner_position=bp.bp_ix
			$where
			group by b.banner_ix, bd.bd_ix, banner_img,banner_link,banner_target,banner_width,banner_height ";
        $slave_mdb->query($sql);
        $i_no = 0;
        $btn_no = "";
        $printflash = $slave_mdb->fetchall();
        $banner_ix	= $printflash[0][banner_ix];
        $banner_width	= $printflash[0][banner_width];
        $banner_height	= $printflash[0][banner_height];
        $change_effect = $printflash[0][change_effect];

        if (is_array($printflash)){
            foreach($printflash as $_key => $_val){
                if($printflash[$_key][bd_file] != ""){ //이미지값과 네비게이션 숫자 데이터를 담아둔다
                    $i_no++;
                    $imgPath = $_SESSION["layout_config"][mall_data_root]."/images/banner/".$banner_ix."/"; //$printflash[$_key][bd_ix]

                    $imgString .= "<a href='".$printflash[$_key][bd_link]."'><img src='".$imgPath.$printflash[$_key][bd_file]."' title='".$printflash[$_key][bd_title]."' width='".$banner_width."' height='".$banner_height."'></a>";
                }
            }
        }
        //echo $imgString;
        //exit;
        $bd_ix = $printflash[0][bd_ix];


        $time_sec = $printflash[0][time_sec] * 1000;
        if (!$time_sec) $time_sec = 4000;


        $mString .= "

		<STYLE>
		#slider-wrapper {
			background:url(/images/slider.png) no-repeat;
			width:".$banner_width."px;
			height:".$banner_height."px;
			margin:0 auto;
			padding-top:74px;
			margin-top:50px;
		}

		#slider {
			position:relative;
			width:".$banner_width."px;
			height:".$banner_height."px;
			margin-left:0px;
			margin-bottom:10px;
			background:url(/images/loading.gif) no-repeat 50% 50%;
		}
		</STYLE>
			";

        $mString .= "<div id='slider_".$banner_ix."' class='nivoSlider'>";
        $mString .= "{$imgString}";
        $mString .= "</div>";
        if($i_no > 1){
            $mString .= "
				<script type='text/javascript'>
				$(window).load(function() {";
            if($change_effect == "S"){
                $mString .= "
					$('#slider_".$banner_ix."').nivoSlider({
						effect:'fade',
						pauseTime:".$time_sec.",
						pauseOnHover:true
					});
				";
            } else if($change_effect == "F"){
                $mString .= "
					$('#slider_".$banner_ix."').nivoSlider({
						effect:'fade',
						pauseTime:".$time_sec.",
						pauseOnHover:true
					});";
            } else if($change_effect == "T"){
                $mString .= "
					$('#slider_".$banner_ix."').nivoSlider({
						effect:'fold',
						pauseTime:".$time_sec.",
						pauseOnHover:true
					});";
            } else if($change_effect == "R"){
                $mString .= "
					$('#slider_".$banner_ix."').nivoSlider({
						effect:'random',
						animSpeed:1500,
						pauseTime:".$time_sec.",
						startSlide:2,
						directionNav:false,
						controlNav:true,
						keyboardNav:false,
						pauseOnHover:false
					});";
            }
            $mString .= "
			});
				</script>
				";
        }
    }else if ($banner_kind == 3){	// 슬라이드 배너
        $sql = "select b.banner_ix, b.banner_kind, change_effect, banner_img,banner_link,banner_target,banner_width,banner_height,disp, bd.*,
			IFNULL(sum(bc.ncnt),0) as ncnt
			from shop_bannerinfo b left join shop_bannerinfo_detail bd on b.banner_ix = bd.banner_ix
			left join logstory_banner_click bc on b.banner_ix = bc.banner_ix LEFT JOIN shop_banner_position bp ON b.banner_position=bp.bp_ix
			$where
			group by b.banner_ix, bd.bd_ix, banner_img,banner_link,banner_target,banner_width,banner_height ";
        $slave_mdb->query($sql);

        $printflash = $slave_mdb->fetchall();
        $banner_ix	= $printflash[0][banner_ix];
        $banner_width	= $printflash[0][banner_width];
        $banner_height	= $printflash[0][banner_height];
        $change_effect = $printflash[0][change_effect];
        //echo "change_effect:".$change_effect;

        //print_r($printflash);
        if (is_array($printflash)){
            $html = '<div style="position:relative;float:left;width:'.$banner_width.'px;overflow:hidden;height:'.$banner_height.'px; " id="main_scroll_width1">
							<div id="slide_banner_'.$banner_ix.'" class="goods" style="float:left;width:1400px;white-space:nowrap;margin:0; height:115px; overflow:hidden; z-index:-10;">';
            foreach($printflash as $_key => $_val){
                if($printflash[$_key][bd_file] != ""){ //이미지값과 네비게이션 숫자 데이터를 담아둔다
                    $i_no++;
                    $imgPath = $_SESSION["layout_config"][mall_data_root]."/images/banner/".$banner_ix."/".$printflash[$_key][bd_file]; //$printflash[$_key][bd_ix]

                    $_html .= "<ul class='banners' style='float:left;z-index:-5;'>\n";
                    $_html .= "	<li><a href='".$printflash[$_key][bd_link]."'><img src='".$imgPath."' title='".$printflash[$_key][bd_title]."' ></a></li>\n";
                    $_html .= "</ul>\n";
                }
            }
            $img_size = getimagesize($_SERVER["DOCUMENT_ROOT"].$imgPath);
            $width = $img_size[0];
            $height = $img_size[1];
            //$_html .= $_html.$_html;
            $html .= $_html."</div>
					<div class='s_l_b' style='position:absolute; z-index:10; top:43px; left:".($banner_width-33)."px;'>
						<a href=\"javascript:clickbannerScroll('slide_banner_".$banner_ix."',-".$width.");\"><img src='".$_SESSION["layout_config"][mall_templet_webpath]."/images/common/right.png' on_src='".$_SESSION["layout_config"][mall_templet_webpath]."/images/common/right_on.png' out_src='".$_SESSION["layout_config"][mall_templet_webpath]."/images/common/right.png' onmouseover=\"$(this).attr('src',$(this).attr('on_src'))\" onmouseout=\"$(this).attr('src',$(this).attr('out_src'))\"  alt='' title='' /></a>
					</div>
					<div class='s_l_b' style='position:absolute; z-index:10; top:43px; left:7px;'>
						<a href=\"javascript:clickbannerScroll('slide_banner_".$banner_ix."',-".$width.");\"><img src='".$_SESSION["layout_config"][mall_templet_webpath]."/images/common/left.png' on_src='".$_SESSION["layout_config"][mall_templet_webpath]."/images/common/left_on.png' out_src='".$_SESSION["layout_config"][mall_templet_webpath]."/images/common/left.png' onmouseover=\"$(this).attr('src',$(this).attr('on_src'))\" onmouseout=\"$(this).attr('src',$(this).attr('out_src'))\"  alt='' title=''/></a>
					</div>
					</div>";
        }


        $html .= "<script language='javascript'>
						<!--
							var slideBanner = setInterval(\"bannerScroll('slide_banner_".$banner_ix."',186)\", 2000);
							$('div#slide_banner_".$banner_ix."').hover(function()	{
								clearInterval(slideBanner);
							}, function()
							{
								slideBanner = setInterval(\"bannerScroll('slide_banner_".$banner_ix."',186)\", 2000);
							});
						//-->
						</script>";
        $mString = $html;
    }else if ($banner_kind == 4){	// 동영상 배너
        $mString = $slave_mdb->dt[banner_html];
    }

    return $mString;
}

function check_bbs_recom($board_table,$bbs_ix,$ucode) {//게시물 추천여부 검사 kbk 13/07/08
    $slave_mdb=new MySQL;

    $sql="SELECT br_ix FROM shop_bbs_recommend WHERE ucode='".$ucode."' AND bbs_ix='".$bbs_ix."' AND board_table='".$board_table."' ";
    $slave_mdb->query($sql);
    $total=$slave_mdb->total;

    return $total;
}


function getPromotionListGroup($div_code,$group_code="")
{
    global $slave_mdb;
    //$slave_mdb=new MySQL;
    if($group_code!="") $add_where=" AND group_code='".$group_code."' ";
    else $add_where="";
    $sql="SELECT ppg_ix,group_name,product_cnt,goods_display_type,group_code
			FROM shop_promotion_product_group
			WHERE div_code='".$div_code."' AND insert_yn='Y' AND use_yn='Y' ".$add_where."
			ORDER BY group_code ASC ";
    //echo nl2br($sql);
    $slave_mdb->query($sql);
    return $slave_mdb->fetchall();
}

function getPromotionListproducts($group_code,$display_goods_cnt,$div_code) {
    global $shop_product_type, $layout_config;
    global $slave_mdb;
    //$slave_mdb=new MySQL;
    include_once($_SERVER['DOCUMENT_ROOT'].'/admin/logstory/class/sharedmemory.class');
    $shmop = new Shared("reserve_rule");
    $shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"][mall_data_root]."/_shared/";
    $shmop->SetFilePath();
    $reserve_data = $shmop->getObjectForKey("reserve_rule");
    $reserve_data = unserialize(urldecode($reserve_data));

    if($reserve_data[reserve_use_yn] == "Y"){
        $reserve_sql = " ,case when p.reserve_yn = 'N' then round(sellprice*(".$reserve_data[goods_reserve_rate]."/100)) else round(sellprice*(reserve_rate/100)) end as reserve";
    }

    $sql = "SELECT * FROM shop_promotion_product_group where group_code='".$group_code."'  and use_yn ='Y' AND div_code='".$div_code."' ORDER BY group_code ASC ";
    //echo nl2br($sql);
    $slave_mdb->query($sql);
    $displayGoods = $slave_mdb->fetch();

    if($_SESSION['user']['price_view'] == '1'){
        $select_price = 'p.wholesale_price as listprice, p.wholesale_sellprice as sellprice, p.sellprice AS ori_sellprice, p.listprice AS ori_listprice, (listprice-sellprice)/listprice*100 as sale_rate , ';
    }else{
        $select_price = 'p.sellprice, p.listprice, (listprice-sellprice)/listprice*100 as sale_rate,  ';
    }

    if($displayGoods["goods_display_type"] == "A") {

        //$sql = "SELECT cid FROM shop_promotion_category_relation where group_code='".$group_code."'  and insert_yn ='Y' AND div_code='".$div_code."' ORDER BY vieworder ASC ";
        $sql = "SELECT smcr.cid, sci.depth FROM shop_promotion_category_relation smcr, shop_category_info sci
					where smcr.cid = sci.cid and group_code='".$group_code."'  and insert_yn ='Y' AND div_code='".$div_code."'
					ORDER BY vieworder ASC ";
        //echo nl2br($sql);
        $slave_mdb->query($sql);
        $cateRelation = $slave_mdb->fetchall();
        if(is_array($cateRelation)){
            $cids="";
            $cidNo = 0;
            foreach($cateRelation as $_keys=>$_values){
                if($cidNo == 0) $cids .= "'".$_values[cid]."'";
                else $cids .= ",'".$_values[cid]."'";
                if(!$_cid_where){
                    $_cid_where = " r.cid LIKE '".substr($_values[cid], 0, (($_values[depth]+1) * 3))."%' ";
                }else{
                    $_cid_where .= " or r.cid LIKE '".substr($_values[cid], 0, (($_values[depth]+1) * 3))."%' ";
                }
                $cidNo++;
            }
            $cid_where = " and (".$_cid_where.")" ;
        }
        if($displayGoods[display_auto_type]) {
            $orderBy = "ORDER BY p.".$displayGoods[display_auto_type]." ";
            if($displayGoods[display_auto_type] == "sellprice") $orderBy .= "ASC";
            else $orderBy .= "DESC";
        } else {
            $orderBy = "";
        }
        if($cids!="") $add_cids=" and r.cid in (".$cids.") ";
        else $add_cids="";
        $sql = "SELECT ".$display_goods_cnt." AS goods_list_cnt,p.id,p.pname, ".$select_price."  p.stock, p.stock_use_yn, p.brand_name, p.shotinfo, option_kind, sum(case when bbs_etc1 != '' then 1 else 0 end) as after_cnt, icons $reserve_sql
					FROM ".TBL_SHOP_PRODUCT." p
					right join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid
					right join ".TBL_SHOP_CATEGORY_INFO." c on r.cid = c.cid and category_use ='1'
					left join shop_product_options po on p.id = po.pid and option_kind in ('x','x2','s2','c') and option_use = '1'
					left join bbs_after a on p.id = a.bbs_etc1
					where  p.disp = 1 and p.state = 1 and product_type in (".implode(' , ',$shop_product_type).") ".$cid_where."
					group by p.id
					$orderBy

					limit ".($display_goods_cnt ? $display_goods_cnt:5)." ";
    } else {
        // 메인페이지 분석 모드일때 각 상품에 조회수를 이미지 위에 노출하기위해서 통계데이타와 연동
        if($_GET["viewtype"] == "analysis" || $_SESSION["viewtype"]  == "analysis" ){
            if(!$vdate){
                $vdate = date("Ymd");
            }

            $sql = "SELECT ".$display_goods_cnt." AS goods_list_cnt,p.id,p.pname, ".$select_price."  p.stock, p.stock_use_yn, p.brand_name, p.shotinfo, p.admin, erp.ppr_ix, IFNULL(mc.ncnt,0) as ncnt, option_kind,  icons $reserve_sql
						FROM ".TBL_SHOP_PRODUCT." p
						right join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid
						right join ".TBL_SHOP_CATEGORY_INFO." c on r.cid = c.cid and category_use ='1'
						right join shop_promotion_product_relation erp on p.id = erp.pid
						left join shop_product_options po on p.id = po.pid and option_kind in ('x','x2','s2','c') and option_use = '1'
						left join logstory_maingoods_click mc on erp.ppr_ix = mc.ppr_ix and mc.vdate = '".$vdate."'
						where group_code = '".$group_code."' and p.disp = 1 and p.state = 1 and product_type != 2 AND div_code='".$div_code."' order by erp.vieworder asc  limit ".($display_goods_cnt ? $display_goods_cnt:5)." ";
        }else{
            $sql = "SELECT ".$display_goods_cnt." AS goods_list_cnt,p.id,p.pname, ".$select_price."  p.stock, p.stock_use_yn, p.brand_name, p.shotinfo, p.admin, erp.ppr_ix,option_kind, icons $reserve_sql
						FROM ".TBL_SHOP_PRODUCT." p
						right join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid
						right join ".TBL_SHOP_CATEGORY_INFO." c on r.cid = c.cid and category_use ='1'
						right join shop_promotion_product_relation erp on p.id = erp.pid
						left join shop_product_options po on p.id = po.pid and option_kind in ('x','x2','s2','c') and option_use = '1'
						where group_code = '".$group_code."' and p.disp = 1 and p.state = 1 and product_type != 2 AND div_code='".$div_code."' order by erp.vieworder asc  limit ".($display_goods_cnt ? $display_goods_cnt:5)." ";
            //and p.brand = b.b_ix 삭제 ,,".TBL_SHOP_BRAND." b 삭제 , b.brand_name --> p.brand_name 변경
        }
    }
    $slave_mdb->query($sql);
    //echo nl2br($sql);
    //$displayGoods[$i][goods] = $slave_mdb->fetchall();
    return $slave_mdb->fetchall();
}




function getCouponInfoByGoods($pid, $sellprice, $pcount, $whole = FALSE){

    $slave_mdb = new MySQL;

    $sql ="select * from ".TBL_SHOP_PRODUCT." where id='".$pid."' and coupon_use_yn ='Y' ";
    $slave_mdb->query($sql);
    if($slave_mdb->total){
        if($slave_mdb->dbms_type == "oracle"){

            $sql = "select 5 as qnum, cupon_sale_type, cupon_sale_value, c.cupon_ix, cupon_kind, cupon_no, cp.* ,
							case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price and publish_limit_price > 0 then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice
							from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp, ".TBL_SHOP_CUPON_RELATION_PRODUCT." crp
							where c.cupon_ix = cp.cupon_ix
							and cp.publish_condition_price <= ".$sellprice*$pcount."
							and cp.publish_ix = crp.publish_ix
							and crp.pid = '".$pid."' and cp.use_product_type = 3 and cp.publish_type in (1, 2, 4)
							and ((cp.use_date_type!='9' AND TO_DATE('".date("m-d-Y H:i:s")."','MM-DD-YYYY HH24:MI:SS') between cp.use_sdate and cp.use_edate) OR cp.use_date_type=9 OR cp.use_date_type=2)
							and cp.disp='1' and cp.is_use = '1'
							";


            $sql .= "union
								select  6 as qnum, cupon_sale_type, cupon_sale_value, c.cupon_ix, cupon_kind, cupon_no, cp.*	,
								case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price and publish_limit_price > 0 then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice
								from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp
								where c.cupon_ix = cp.cupon_ix and cp.publish_condition_price <= ".$sellprice*$pcount."
								and cp.use_product_type = 1 and cp.publish_type in (1, 2, 4)
								and ((cp.use_date_type!='9' AND TO_DATE('".date("m-d-Y H:i:s")."','MM-DD-YYYY HH24:MI:SS') between cp.use_sdate and cp.use_edate) OR cp.use_date_type=9 OR cp.use_date_type=2)
								and cp.disp='1' and cp.is_use = '1'
								";

            $sql .= "union
								select  7 as qnum, cupon_sale_type, cupon_sale_value, c.cupon_ix, cupon_kind, cupon_no , cp.*,
								case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price and publish_limit_price > 0 then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice
								from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp, ".TBL_SHOP_CUPON_RELATION_CATEGORY." crc, ".TBL_SHOP_PRODUCT_RELATION." pr
								where c.cupon_ix = cp.cupon_ix and cp.publish_condition_price <= ".$sellprice*$pcount."
								and cp.publish_ix = crc.publish_ix and SUBSTRING(crc.cid,1,(crc.depth+1)*3) = SUBSTRING(pr.cid,1,(crc.depth+1)*3)
								and pr.pid = '".$pid."' and cp.use_product_type = 2 and cp.publish_type in (1, 2, 4)
								and ((cp.use_date_type!='9' AND TO_DATE('".date("m-d-Y H:i:s")."','MM-DD-YYYY HH24:MI:SS') between cp.use_sdate and cp.use_edate) OR cp.use_date_type=9 OR cp.use_date_type=2)
								and cp.disp='1'
								";

            $sql .= "union
								select 8 as qnum, cupon_sale_type, cupon_sale_value, c.cupon_ix, cupon_kind, cupon_no , cp.*,
								case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price and publish_limit_price > 0 then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice
								from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp, ".TBL_SHOP_CUPON_RELATION_BRAND." crb, ".TBL_SHOP_PRODUCT." p
								where c.cupon_ix = cp.cupon_ix and cp.publish_condition_price <= ".$sellprice*$pcount."
								and cp.publish_ix = crb.publish_ix and crb.b_ix = p.brand
								and p.id = '".$pid."' and cp.use_product_type = 4 and cp.publish_type in (1, 2, 4)
								and ((cp.use_date_type!='9' AND TO_DATE('".date("m-d-Y H:i:s")."','MM-DD-YYYY HH24:MI:SS') between cp.use_sdate and cp.use_edate) OR cp.use_date_type=9 OR cp.use_date_type=2)
								and cp.disp='1' and cp.is_use = '1'
								";

            $sql .= " order by saleprice desc  ";
        }else{

            /**
            use_product_type
            1 : 전체상품

            use_date_type
            3 : 사용기간지정
            1 : 발행일로부터  publish_date_differ 기간 만큼  publish_date_type( 1: 년, 2:월, 3:일)
            2 : 발급일로부터  regist_date_differ  기간 만큼  regist_date_type( 1: 년, 2:월, 3:일)
            9 : 무기한

            publish_type : 발급구분
            1: 고객지정발행
            2: 일반발행
            3: 회원가입발행


             **/
            /*상품별 쿠폰 , 일반발행*/
            $sql = "
								select cp.* , 5 as qnum, cupon_sale_type, cupon_sale_value, c.cupon_ix, cupon_kind, cupon_no,
								case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price and publish_limit_price > 0 then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice
								from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp, ".TBL_SHOP_CUPON_RELATION_PRODUCT." crp
								where c.cupon_ix = cp.cupon_ix
								and cp.publish_condition_price <= ".$sellprice*$pcount."
								and cp.publish_ix = crp.publish_ix
								and crp.pid = '".$pid."' and cp.use_product_type = 3 and cp.publish_type in (1, 2, 4)
								and ((cp.use_date_type != '9' AND '".date("Y-m-d H:i:s")."' between cp.use_sdate and cp.use_edate) OR cp.use_date_type=9 OR cp.use_date_type=2)
								and cp.disp='1' and cp.is_use = '1' and cp.cupon_use_sdate < '" . strtotime(date("Y-m-d H:i:s")) . "' and cp.cupon_use_edate > '" . strtotime(date("Y-m-d H:i:s")) . "'
								";

            $sql .= "union
								select cp.*	,  6 as qnum, cupon_sale_type, cupon_sale_value, c.cupon_ix, cupon_kind, cupon_no, case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price and publish_limit_price > 0 then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice
								from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp
								where c.cupon_ix = cp.cupon_ix and cp.publish_condition_price <= ".$sellprice*$pcount."
								and cp.use_product_type = 1 and cp.publish_type in (1, 2, 4)
								and ((cp.use_date_type != '9' AND '".date("Y-m-d H:i:s")."' between cp.use_sdate and cp.use_edate) OR cp.use_date_type=9 OR cp.use_date_type=2)
								and cp.disp='1' and cp.is_use = '1' and cp.cupon_use_sdate < '" . strtotime(date("Y-m-d H:i:s")) . "' and cp.cupon_use_edate > '" . strtotime(date("Y-m-d H:i:s")) . "'
								";
            /*특정카테고리 , 일반 발행의 경우*/
            $sql .= "union
								select cp.*, 7 as qnum, cupon_sale_type, cupon_sale_value, c.cupon_ix, cupon_kind, cupon_no ,
								case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price and publish_limit_price > 0 then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice
								from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp, ".TBL_SHOP_CUPON_RELATION_CATEGORY." crc, ".TBL_SHOP_PRODUCT_RELATION." pr
								where c.cupon_ix = cp.cupon_ix and cp.publish_condition_price <= ".$sellprice*$pcount."
								and cp.publish_ix = crc.publish_ix and SUBSTRING(crc.cid,1,(crc.depth+1)*3) = SUBSTRING(pr.cid,1,(crc.depth+1)*3)
								and pr.pid = '".$pid."' and cp.use_product_type = 2 and cp.publish_type in (1, 2, 4)
								and ((cp.use_date_type != '9' AND '".date("Y-m-d H:i:s")."' between cp.use_sdate and cp.use_edate) OR cp.use_date_type=9 OR cp.use_date_type=2)
								and cp.disp='1' and cp.is_use = '1' and cp.cupon_use_sdate < '" . strtotime(date("Y-m-d H:i:s")) . "' and cp.cupon_use_edate > '" . strtotime(date("Y-m-d H:i:s")) . "'
								";
            //and SUBSTRING(crc.cid,1,(crc.depth+1)*3) = SUBSTRING(pr.cid,1,(crc.depth+1)*3)
            /*브랜드 , 일반발행일경우 */
            $sql .= "union
								select cp.*, 8 as qnum, cupon_sale_type, cupon_sale_value, c.cupon_ix, cupon_kind, cupon_no ,
								case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price and publish_limit_price > 0 then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice
								from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp, ".TBL_SHOP_CUPON_RELATION_BRAND." crb, ".TBL_SHOP_PRODUCT." p
								where c.cupon_ix = cp.cupon_ix and cp.publish_condition_price <= ".$sellprice*$pcount."
								and cp.publish_ix = crb.publish_ix and crb.b_ix = p.brand
								and p.id = '".$pid."' and cp.use_product_type = 4 and cp.publish_type in (1, 2, 4)
								and ((cp.use_date_type != '9' AND '".date("Y-m-d H:i:s")."' between cp.use_sdate and cp.use_edate) OR cp.use_date_type=9 OR cp.use_date_type=2)
								and cp.disp='1' and cp.is_use = '1' and cp.cupon_use_sdate < '" . strtotime(date("Y-m-d H:i:s")) . "' and cp.cupon_use_edate > '" . strtotime(date("Y-m-d H:i:s")) . "'
								";

            $sql .= " order by saleprice desc";
            $sql .= $whole ? "" : " limit 1";

        }

        $slave_mdb->query($sql);
        $results = $slave_mdb->fetchall("object");
    }

    //print_r($results);
    return $whole ? $results : $results[0];


}

function return_event_type($event_ix="") {//이벤트인지 기획전인지 구분값 리턴 -> event_title.htm 에 쓰기 위해서 kbk 13/07/15
    global $slave_mdb;
    //$slave_mdb=new Database;

    if($_SERVER["PHP_SELF"]=="/event/promotion_list.php") {
        return "P";
    } else if($_SERVER["PHP_SELF"]=="/event/event_list.php") {
        return "E";
    } else {
        $sql="SELECT kind FROM ".TBL_SHOP_EVENT." WHERE event_ix='".$event_ix."' ";
        $slave_mdb->query($sql);
        if($slave_mdb->total) {
            $slave_mdb->fetch();

            return $slave_mdb->dt["kind"];
        } else {
            return "E";
        }
    }
}

function get_product_after_cnt($board,$pid,$basic_return="") {//상품에 대한 상품평 건수 kbk 13/07/15
    global $slave_mdb;
    //$slave_mdb=new Database;

    $arr_board=explode(";",$board);
    $cnt=0;
    for($i=0;$i<count($arr_board);$i++) {
        $sql="SELECT COUNT(bbs_ix) AS cnt FROM bbs_".$arr_board[$i]." WHERE bbs_etc1='".$pid."' and status=1 ";
        $slave_mdb->query($sql);
        $slave_mdb->fetch();
        $cnt+=$slave_mdb->dt["cnt"];
    }
    if($cnt==0) {
        return $basic_return;
    } else {
        return $cnt;
    }
}

function get_product_valuation($pid){
    global $slave_mdb;

    $sql = "select round(sum(valuation)/sum(valuation_goods), 1) as average_star 
              from (
                    select sum(valuation_goods) as valuation
                         , count(valuation_goods) as valuation_goods
                      from bbs_premium_after 
                     where bbs_etc1 = '".$pid."'
                       and status = 1
                 union all
                    select sum(round(IFNULL((valuation_goods + valuation_goods_info + valuation_delivery + valuation_package) / 4, 0))) as valuation
                         , count(valuation_goods) as valuation_goods
                      from bbs_after
                     where bbs_etc1 ='".$pid."'
                       and status = 1
                 ) as tmp;";

    $slave_mdb->query($sql);
    $slave_mdb->fetch();
    $average_star = $slave_mdb->dt['average_star'];

    return $average_star;
}

function get_product_after_rating($board,$pid,$basic_return="80",$return_type="rate") {//상품에 대한 상품 평가 평균 kbk 13/07/15
    global $slave_mdb;
    //$slave_mdb=new Database;

    $arr_board=explode(";",$board);

    $all_total=0;
    $all_sum_rating=0;

    for($i=0;$i<count($arr_board);$i++) {

        $sql="SELECT SUM(CASE WHEN bbs_etc3='1' THEN 80 WHEN bbs_etc3='2' THEN 85 WHEN bbs_etc3='3' THEN 90 WHEN bbs_etc3='4' THEN 95 WHEN bbs_etc3='5' THEN 100 END) AS sum_rating, COUNT(bbs_ix) AS cnt FROM bbs_".$arr_board[$i]." WHERE bbs_etc1='".$pid."' ";
        $slave_mdb->query($sql);

        $slave_mdb->fetch();
        $all_total+=$slave_mdb->dt["cnt"];
        $all_sum_rating+=$slave_mdb->dt["sum_rating"];
    }
    if($all_total==0) {
        return $basic_return;
    } else {
        $average=ceil($all_sum_rating/$all_total);
        if($return_type=="rate") {
            $return_value=$average;
        } else {
            if($average>84) {
                if($average>89) {
                    if($average>94) {
                        if($average>99) {
                            $return_value=5;
                        } else {
                            $return_value=4;
                        }
                    } else {
                        $return_value=3;
                    }
                } else {
                    $return_value=2;
                }
            } else {
                $return_value=1;
            }
        }
        return $return_value;
    }
}

function get_order_status_count($oid,$status,$ode_ix="") {//주문에 대한 상태값 수를 구함 kbk 13/07/18
    global $slave_mdb;
    //$slave_mdb=new Database;
    $add_query="";
    if($status==ORDER_STATUS_DELIVERY_COMPLETE) {
        $add_query.=" AND od.dc_date > '".date("Y-m-d H:i:s",strtotime("-7 Day"))."'";
    }

    if($ode_ix!=""){
        $add_query.=" AND od.ode_ix ='".$ode_ix."' ";
    }

    $sql="SELECT COUNT(od.od_ix) AS cnt FROM ".TBL_SHOP_ORDER." o LEFT JOIN ".TBL_SHOP_ORDER_DETAIL." od ON o.oid=od.oid WHERE od.oid='".$oid."' AND od.status='".$status."'  ".$add_query;
    $slave_mdb->query($sql);
    $slave_mdb->fetch();
    return $slave_mdb->dt["cnt"];
}

function get_reserve_rate($pid) {
    global $slave_mdb;
    //$slave_mdb=new Database;


    //적립금정보 가져옴
    $reserve_data = GetReserveRate();

    if($reserve_data[mileage_info_use] == "Y"){
        $reserve_rate=$reserve_data[goods_mileage_rate];
    }elseif($reserve_data[mileage_info_use] == "P"){
        $reserve_rate=$reserve_data["goods_mileage_rate_".$reserve_data[basic_rate]];
    }else{
        $reserve_rate=0;
    }

    $sql="SELECT wholesale_reserve_yn, wholesale_reserve_rate, reserve_yn, reserve_rate FROM ".TBL_SHOP_PRODUCT." WHERE id='".$pid."' ";
    $slave_mdb->query($sql);
    $slave_mdb->fetch();
    if(UserSellingType()=="R") {
        $p_reserve_yn=$slave_mdb->dt["reserve_yn"];
        $p_reserve_rate=$slave_mdb->dt["reserve_rate"];
    } else {
        $p_reserve_yn=$slave_mdb->dt["wholesale_reserve_yn"];
        $p_reserve_rate=$slave_mdb->dt["wholesale_reserve_rate"];
    }

    if($p_reserve_yn=="Y"){
        $reserve_rate=$p_reserve_rate;
    }
    return $reserve_rate;
}


function PayMethodReserveUdate($oid,$method){	//결제타입별 적립되는 적립금 업데이트 2014-05-29 이학봉

    global $_SESSION;
    global $slave_mdb, $master_db;

    //$slave_mdb=new Database;


    $mem_type = $_SESSION['user']['mem_type'];
    //적립금정보 가져옴
    $reserve_data = GetReserveRate();

    if($reserve_data[mileage_info_use] == "P"){
        $sql = "select * from shop_order_detail where oid = '$oid'";
        $slave_mdb->query($sql);
        $data_array = $slave_mdb->fetchall();

        switch($method){
            case '10':	//현금
                $basic_rate = '1';
                break;
            case '0':	//무통장
                $basic_rate = '2';
                break;
            case '4':	//가상계좌
                $basic_rate = '3';
                break;
            case '5':	//실시간게좌이체
                $basic_rate = '4';
                break;
            case '1':	//카드결제
                $basic_rate = '5';
                break;
            case '2':	//휴대폰결제
                $basic_rate = '6';
                break;
            case '13':	//예치금결제
                $basic_rate = '7';
                break;
            case '8':	//무료결제
                $basic_rate = '8';
                break;
        }

        for($i=0;$i<count($data_array);$i++){
            $pt_dcprice = $data_array[$i][pt_dcprice];

            $sql = "select if('".UserSellingType()."' = 'W',wholesale_reserve_yn,reserve_yn) as reserve_yn from ".TBL_SHOP_PRODUCT." where id = '".$data_array[$i][pid]."'";
            $slave_mdb->query($sql);
            $slave_mdb->fetch();

            if($slave_mdb->dt[reserve_yn] == 'Y'){
                continue;
            }else{

                $reserve = floor($pt_dcprice * ($reserve_data['goods_mileage_rate_'.$basic_rate] / 100));
                $sql = "update shop_order_detail set reserve = '".$reserve."' where od_ix = '".$data_array[$i][od_ix]."'";
                $master_db->query($sql);
            }

        }
    }
}


function re_input_reserve($fetch_result) {
    global $slave_mdb, $master_db;
    //$slave_mdb=new Database;
    //$slave_mdb2=new Database;

    $c_total=count($fetch_result);

    for($i=0;$i<$c_total;$i++) {

        $c_cart_ix=$fetch_result[$i]["cart_ix"];
        $c_pid=$fetch_result[$i]["id"];
        $c_sellprice=$fetch_result[$i]["sellprice"];
        $c_dcprice=$fetch_result[$i]["dcprice"];
        $c_option_price=$fetch_result[$i]["option_price"];
        $c_use_coupon_price=$_SESSION["order"][use_cupon_detail_price][$c_cart_ix];
        $c_pcount=$fetch_result[$i]["pcount"];
        $est_ix = $fetch_result[$i]["est_ix"];

        $reserve_rate=get_reserve_rate($c_pid);
        $user_rate = get_member_rate($c_pid);

        /*
		if($_SESSION["user"]["code"]!="" && $user_rate>0) {
			$c_reserve=floor(((($c_sellprice+$c_option_price) * $c_pcount -$c_use_coupon_price)*(100-$user_rate)/100) * ($reserve_rate/100));	// * $c_pcount  수량 곱하기를 뺏습니다. 2013-70-20 이학봉
		} else {
			$c_reserve=floor((($c_sellprice+$c_option_price) * $c_pcount-$c_use_coupon_price) * ($reserve_rate/100) ); //* $c_pcount
		}
		*/

        //할인정책이 바뀜에 따라서 변경!
        $c_reserve=floor((($c_dcprice+$c_option_price) * $c_pcount-$c_use_coupon_price) * ($reserve_rate/100));

        if($est_ix){		//견적센터 주문시 적립금은 0 으로 함  2013-09-27 이학봉
            $c_reserve = '0';
        }else{
            $c_reserve = $c_reserve;
        }

        $sql="UPDATE ".TBL_SHOP_CART." SET reserve='".$c_reserve."' WHERE cart_ix='".$c_cart_ix."' ";
        $master_db->query($sql);
    }

}


function getOriginName($origin)
{
    global $slave_mdb;
    //$slave_mdb = new Database;


    $sql = "SELECT origin_name FROM common_origin where og_ix='".$origin."' ";
    //echo $sql;
    $slave_mdb->query($sql);
    $slave_mdb->fetch();

    return $slave_mdb->dt[origin_name];
}

function getOriginList()
{
    global $slave_mdb;

    $sql = "SELECT * FROM common_origin WHERE disp = '1' ORDER BY regdate ASC ";
    $slave_mdb->query($sql);

    return $slave_mdb->fetchall();
}

function get_service_product_info($scode="") {
    global $slave_mdb;
    //$slave_mdb=new Database;

    if($scode!="") {
        $sql="SELECT p.*,pr.cid FROM service_product p LEFT JOIN service_product_relation pr ON p.id=pr.pid LEFT JOIN service_division d ON p.service_code=d.service_code WHERE p.disp=1 AND p.state=1 and p.service_code = '".$scode."' ";
    } else {
        $sql="SELECT p.*,pr.cid FROM service_product p LEFT JOIN service_product_relation pr ON p.id=pr.pid LEFT JOIN service_division d ON p.service_code=d.service_code WHERE p.disp=1 AND p.state=1 ORDER BY d.vieworder ASC ";
    }
    $slave_mdb->query($sql);
    return $slave_mdb->fetchall();
}

function getServiceCart($company_id,$choice_prod="0"){
    global $user, $shop_product_type ;


    $slave_mdb = new Database;

    if($choice_prod == "1") $whereplus = " and choice_prod = '1'";
    else $whereplus = "";
    if($user[code] != ""){
        $where = " mem_ix = '".$user[code]."'" . $whereplus." and c.product_type in (".implode(' , ',$shop_product_type).") ";
        //$groupby = " group by mem_ix";
    }else{
        $where = " cart_key = '".session_id()."'" . $whereplus ." and c.product_type in (".implode(' , ',$shop_product_type).") ";
        //$groupby = " group by cart_key";
    }

    $sql = "select c.*,d.service_name from service_cart c,service_product p, service_division d
			where $where and c.id = p.id and company_id = '".$company_id."' AND p.parent_service_code=d.service_code
			order by c.regdate desc ";//정렬이 delivery_price 인 것을 regdate 로 바꿈 kbk 11.10.10
    //echo nl2br($sql);
    //exit;
    $slave_mdb->query($sql);
    $carts = $slave_mdb->fetchall();
    //print_r($carts);
    return $carts;
}

function fetch_service_category($no_cid="") {
    global $slave_mdb;
    //$slave_mdb=new Database;
    //$sql="SELECT * FROM service_category_info ci LEFT JOIN service_product_relation pr ON ci.cid=pr.cid WHERE p.disp=1 AND p.state=1 ";
    //$sql="SELECT distinct ci.cname , ci.cid as cid FROM service_product p LEFT JOIN service_product_relation pr ON p.id=pr.pid , service_category_info ci WHERE ci.cid=pr.cid and p.disp=1 AND p.state=1 ";
    if($no_cid!="") {
        $add_where=" AND cid NOT IN ('".$no_cid."')";
    }
    $sql="SELECT * FROM service_category_info WHERE category_use=1 AND depth='0' $add_where ORDER BY vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ";
    $slave_mdb->query($sql);
    //echo $sql;
    if($slave_mdb->total) {
        return $slave_mdb->fetchall();
        exit;
    }
}

function fetch_service_product($cid = "") {
    global $slave_mdb;
    //$slave_mdb=new Database;
    if($cid){
        $sql="SELECT p.*,pr.cid FROM service_product p LEFT JOIN service_product_relation pr ON p.id=pr.pid LEFT JOIN service_division d ON p.service_code=d.service_code WHERE p.disp=1 AND p.state=1 and pr.cid = '".$cid."' ORDER BY d.vieworder ASC ";
    }else{
        $sql="SELECT p.*,pr.cid FROM service_product p LEFT JOIN service_product_relation pr ON p.id=pr.pid LEFT JOIN service_division d ON p.service_code=d.service_code WHERE p.disp=1 AND p.state=1 ORDER BY d.vieworder ASC ";
    }

    $slave_mdb->query($sql);
    if($slave_mdb->total) {
        return $slave_mdb->fetchall();
        exit;
    }
}

function fetch_service_options($pid) {
    global $slave_mdb;
    //$slave_mdb=new Database;
    $sql="SELECT o.* FROM service_product p LEFT JOIN service_product_options o ON p.id=o.pid WHERE p.id='".$pid."' AND o.insert_yn='Y' AND o.option_kind='b' ORDER BY o.opn_ix ASC ";
    //echo $sql;
    $slave_mdb->query($sql);
    if($slave_mdb->total) {
        $options=$slave_mdb->fetchall();
        foreach ($options as $key => $sub_array) {
            $sql="SELECT * FROM service_product_options_detail WHERE opn_ix='".$options[$key]["opn_ix"]."' ORDER BY CAST(option_div AS UNSIGNED) ASC ";
            $slave_mdb->query($sql);
            if($slave_mdb->total) {
                $options_detail=$slave_mdb->fetchall();
                array_insert($sub_array,20,array("options_detail"=>$options_detail));
                $options[$key]=$sub_array;
            }
        }
        //print_r($options);
        return $options;
    }
}

function print_service_code_name($code_name) {
    global $slave_mdb;
    //$slave_mdb=new Database;
    $sql="SELECT service_name FROM service_division WHERE service_code='".$code_name."' AND disp=1 ";
    $slave_mdb->query($sql);
    $slave_mdb->fetch();
    return $slave_mdb->dt["service_name"];
}


function getMinishopPromotionListGroup($company_id,$group_code="")
{
    global $slave_mdb;
    $slave_mdb=new MySQL;
    if($group_code!="") $add_where=" AND group_code='".$group_code."' ";
    else $add_where="";
    $sql="SELECT mpg_ix,group_name,product_cnt,goods_display_type,group_code
			FROM shop_minishop_product_group
			WHERE company_id='".$company_id."' AND insert_yn='Y' AND use_yn='Y' ".$add_where."
			ORDER BY group_code ASC ";
    //echo $sql;
    $slave_mdb->query($sql);
    return $slave_mdb->fetchall();
}

function getMinishopPromotionListproducts($group_code,$display_goods_cnt,$company_id) {
    global $shop_product_type, $layout_config;
    global $slave_mdb;
    //$db=new MySQL;

    $reserve_data = GetReserveRate();	//적립금 정보 불러오기 함수 추가 2014-06-04 이학봉

    if($reserve_data[mileage_info_use] == "Y"){	// 개별상품 적립금 우선  적용 2013-07-17 이학봉
        if(UserSellingType() == "R"){
            $reserve_sql = " ,case when p.reserve_yn = 'N' or  p.reserve_yn = '' then floor(p.sellprice*(".$reserve_data[goods_mileage_rate]."/100)) else floor(p.sellprice*(p.reserve_rate/100)) end as reserve";
        }else if(UserSellingType() == "W"){
            $reserve_sql = " ,case when p.wholesale_reserve_yn = 'N' or p.wholesale_reserve_yn = '' then floor(p.wholesale_sellprice*(".$reserve_data[goods_mileage_rate]."/100)) else floor(p.wholesale_sellprice*(p.wholesale_reserve_rate/100)) end as reserve";
        }else{
            $reserve_sql = " ,case when p.reserve_yn = 'N' or p.reserve_yn = '' then floor(p.sellprice*(".$reserve_data[goods_mileage_rate]."/100)) else floor(p.sellprice*(p.reserve_rate/100)) end as reserve";
        }
    }else{
        if(UserSellingType() == "R"){	//일반 회원일경우 b2c 개별상품 적용율
            $reserve_sql = " ,case when p.reserve_yn = 'Y' then floor(p.sellprice*(p.reserve_rate/100)) else 0 end as reserve";
        }else if(UserSellingType() == "W"){	//기업회원일경우 도매가로 적립율 적용
            $reserve_sql = " ,case when p.wholesale_reserve_yn = 'Y' then floor(p.wholesale_sellprice*(p.wholesale_reserve_rate/100)) else 0 end as reserve";
        }else{
            $reserve_sql = " ,case when p.reserve_yn = 'Y' then floor(p.sellprice*(p.reserve_rate/100)) else 0 end as reserve";
        }
    }


    $sql = "SELECT * FROM shop_minishop_product_group where group_code='".$group_code."'  and use_yn ='Y' AND company_id='".$company_id."' ORDER BY group_code ASC ";

    $slave_mdb->query($sql);
    $displayGoods = $slave_mdb->fetch();

    if($_SESSION['layout_config']['mall_type'] == 'BW'){
        $select_price = 'wholesale_price as listprice, wholesale_sellprice as sellprice, sellprice AS ori_sellprice, (wholesale_price-wholesale_sellprice)/wholesale_price*100 as sale_rate ,  (listprice-sellprice)/listprice*100 as b2c_sale_rate  ';
    }else{
        $select_price = 'sellprice, listprice, (listprice-sellprice)/listprice*100 as sale_rate ';
    }
    if($displayGoods[product_cnt] > 0){
        $display_goods_cnt = $displayGoods[product_cnt];
    }
    if($displayGoods["goods_display_type"] == "A") {

        $sql = "SELECT cr.cid,c.depth FROM shop_minishop_category_relation cr left join shop_category_info c on cr.cid = c.cid where group_code='".$group_code."'  and insert_yn ='Y' AND company_id='".$company_id."' ORDER BY vieworder ASC ";

        $slave_mdb->query($sql);
        $cateRelation = $slave_mdb->fetchall();
        if(is_array($cateRelation)){
            $cids=" AND (";
            $cidNo = 0;
            foreach($cateRelation as $_keys=>$_values){
                /*if($cidNo == 0) $cids .= "'".$_values[cid]."'";
				else $cids .= ",'".$_values[cid]."'";
				$cidNo++;*/
                if($cidNo == 0) $cids .= " r.cid LIKE '".substr($_values[cid],0,($_values[depth]+1)*3)."%' ";
                else $cids .= " OR r.cid LIKE '".substr($_values[cid],0,($_values[depth]+1)*3)."%' ";
                $cidNo++;
            }
            $cids.=") ";
        }
        if($displayGoods[display_auto_type]) {
            $orderBy = "ORDER BY p.".$displayGoods[display_auto_type]." ";
            if($displayGoods[display_auto_type] == "sellprice") $orderBy .= "ASC";
            else $orderBy .= "DESC";
        } else {
            $orderBy = "";
        }
        if($cids!="") $add_cids=$cids;
        else $add_cids="";
        $sql = "SELECT ".$display_goods_cnt." AS goods_list_cnt,p.id,p.pname, ".$select_price." , p.stock, p.stock_use_yn, p.brand_name, p.shotinfo,p.state ,icons $reserve_sql
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r
					where p.id = r.pid and p.disp = 1 and p.state = 1 and product_type in (".implode(' , ',$shop_product_type).") and product_type != 2 AND p.admin='".$company_id."' ".$add_cids." $orderBy limit ".($display_goods_cnt ? $display_goods_cnt:5)." ";
    } else {

        $sql = "SELECT ".$display_goods_cnt." AS goods_list_cnt,p.id,p.pname, ".$select_price." , p.stock, p.stock_use_yn, p.brand_name, p.shotinfo,p.state,p.admin, erp.mpr_ix, icons $reserve_sql
					FROM ".TBL_SHOP_PRODUCT." p, shop_minishop_product_relation erp
					where p.id = erp.pid  and group_code = '".$group_code."' and p.disp = 1 and p.state = 1 and product_type != 2 AND company_id='".$company_id."' order by erp.vieworder asc  limit ".($display_goods_cnt ? $display_goods_cnt:5)." ";
        //and p.brand = b.b_ix 삭제 ,,".TBL_SHOP_BRAND." b 삭제 , b.brand_name --> p.brand_name 변경

    }
    $slave_mdb->query($sql);
    //echo $sql;
    $displayGoods = $slave_mdb->fetchall("object");

    if(count($displayGoods)){

        for($i=0 ; $i < count($displayGoods) ;$i++){
            $_array_pid[] = $displayGoods[$i][id];
            $goods_infos[$displayGoods[$i][id]][pid] = $displayGoods[$i][id];
            $goods_infos[$displayGoods[$i][id]][amount] = $displayGoods[$i][pcount];
            $goods_infos[$displayGoods[$i][id]][cid] = $displayGoods[$i][cid];
            $goods_infos[$displayGoods[$i][id]][depth] = $displayGoods[$i][depth];
        }

        $discount_info = DiscountRult($goods_infos);

        if(is_array($displayGoods))
        {
            foreach ($displayGoods as $key => $sub_array) {
                $select_ = array("icons_list"=>explode(";",$sub_array[icons]));
                array_insert($sub_array,50,$select_);
                //echo str_pad($sub_array[id], 10, "0", STR_PAD_LEFT)."<br>";
                $discount_item = $discount_info[$sub_array[id]];
                //print_r($discount_item);
                $_dcprice = $sub_array[sellprice];
                if(is_array($discount_item)){
                    foreach($discount_item as $_key => $_item){
                        if($_item[discount_value_type] == "1"){ // %
                            //echo $_item[discount_value]."<br>";
                            $_dcprice = roundBetter($_dcprice*(100 - $_item[discount_value])/100, $_item[round_position], $_item[round_type]);//$_dcprice*(100 - $_item[discount_value])/100;
                        }else if($_item[discount_value_type] == "2"){// 원
                            $_dcprice = $_dcprice - $_item[discount_value];
                        }
                        //$discount_desc[] = $_item[discount_type]."-".$_item[discount_value]." ".($_item[discount_value_type] == "1" ? "%":"원")."-".$_dcprice."원";
                        $discount_desc[] = $_item;//array("discount_type"=>$_item[discount_type], "haddoffice_value"=>$_item[discount_value], "discount_value"=>$_item[discount_value], "discount_value"=>$_item[discount_value], "discount_value_type"=>$_item[discount_value_type], "_dcprice"=>$_dcprice);
                    }
                }
                $_dcprice = array("dcprice"=>$_dcprice);
                array_insert($sub_array,72,$_dcprice);
                $discount_desc = array("discount_desc"=>$discount_desc);
                array_insert($sub_array,73,$discount_desc);
                unset($discount_desc);
                $displayGoods[$key] = $sub_array;
                if($displayGoods[$key][uf_valuation] != "") $displayGoods[$key][uf_valuation] = round($displayGoods[$key][uf_valuation], 0);
                else $displayGoods[$key][uf_valuation] = 0;
            }
        }
    }
    /*
	if($slave_mdb->total) {
		foreach ($displayGoods as $key => $sub_array) {
			//if($key %
			$select_ = array("icons_list"=>explode(";",$sub_array[icons]));
			array_insert($sub_array,14,$select_);
			$displayGoods[$key]=$sub_array;
		}
	}
	*/

    return $displayGoods;
}

function getEventInfo($event_type=6){
    $slave_mdb=new MySQL;
    //echo "aaa";
    $not_allowed_urls  = array(
        "/shop/infoinput.php",
        "/shop/plugin.php",
        "/shop/payment.php"
    );
    if(!in_array($_SERVER["PHP_SELF"],$not_allowed_urls) ){//&& $_COOKIE["PAGEID"] != $_COOKIE["VIEW_PAGEID"]

        $slave_mdb->query("SELECT e.event_ix, ec.exposure_rate FROM shop_event e, shop_event_config ec where e.event_ix = ec.event_ix and ec.event_type = '".$event_type."' and e.disp = 1 and ".time()." between event_use_sdate and event_use_edate limit 1 ");

        if($slave_mdb->total){
            $slave_mdb->fetch();
            $event_ix = $slave_mdb->dt[event_ix];
            if($slave_mdb->dt[exposure_rate] == 0 || $slave_mdb->dt[exposure_rate] == ""){
                return;
            }else{
                $exposure_rate = 100/$slave_mdb->dt[exposure_rate];
            }
            if(rand(1,100) % $exposure_rate == 0){


                $sql = "select ranking from shop_event_applicant where event_ix = '".$event_ix ."' and mem_ix = '".$_SESSION["user"]["code"]."'  ";
                //echo $sql;
                $slave_mdb->query($sql);
                if(!$slave_mdb->total){
                    //echo $sql;
                    $slave_mdb->query("SELECT * FROM shop_event_picturepuzzle ep  where ep.event_ix = '".$event_ix."' order by rand() limit 1 ");
                    if($slave_mdb->total){
                        $picturepuzzles = $slave_mdb->fetchall();
                        //setcookie("VIEW_PAGEID",$_COOKIE["PAGEID"], time()+3600,"/",str_replace(array("www.","b2b."),"",$_SERVER["HTTP_HOST"]));
                        return $picturepuzzles;
                    }
                }
            }
        }

    }
}

function get_order_com_delivery_price($company_id,$oid) {//업체별 주문 시 배송비 kbk 13/07/29
    global $slave_mdb;

    //$slave_mdb=new Database;
    $sql="SELECT delivery_price, delivery_dcprice, delivery_pay_type FROM shop_order_delivery WHERE oid='".$oid."' AND company_id='".$company_id."' ";
    $slave_mdb->query($sql);
    return $slave_mdb->fetchall();
}

function get_bbs_div($bbs_div) {//게시판 분류명 리턴 kbk 13/07/10
    global $slave_mdb;
    //$slave_mdb=new Database;

    //$sql="SELECT bmd.div_name FROM bbs_manage_config bmc LEFT JOIN bmc.bm_ix=bmd.bm_ix WHERE bmc.";
    $sql="SELECT div_name FROM bbs_manage_div WHERE div_ix='".$bbs_div."' ";
    $slave_mdb->query($sql);

    if($slave_mdb->total) {
        $slave_mdb->fetch();

        return $slave_mdb->dt["div_name"];
    }
}

function ch_set_product_bool($option_kind="NAN") {//옵션이 셋트인지 아닌지 검사 kbk 13/08/26

    $arr_set_ok=array("x","x2","s2",'c','a','b','p');
    if(in_array($option_kind,$arr_set_ok)) return true;
    else return false;
}

function get_count_sns_orders($pid) {//소셜커머스 상품 구매수 kbk 13/08/31
    global $slave_mdb;
    //$slave_mdb=new Database;

    $sql="SELECT COUNT(DISTINCT(o.oid)) AS cnt FROM ".TBL_SHOP_ORDER." o LEFT JOIN ".TBL_SHOP_ORDER_DETAIL." od ON o.oid=od.oid AND od.pid='".$pid."' AND od.status NOT IN ('".ORDER_STATUS_SETTLE_READY."','".ORDER_STATUS_REPAY_READY."','".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."') WHERE od.pid='".$pid."' AND od.status NOT IN ('".ORDER_STATUS_SETTLE_READY."','".ORDER_STATUS_REPAY_READY."','".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."') ";
    $slave_mdb->query($sql);
    $slave_mdb->fetch();

    return $slave_mdb->dt["cnt"];
}

function get_product_setname($pid,$op_id,$basic_text="") {
    global $slave_mdb;
    //$slave_mdb=new Database;

    $sql="SELECT opn_ix, set_group FROM shop_product_options_detail WHERE pid='".$pid."' AND id='".$op_id."' ";
    $slave_mdb->query($sql);

    $option_div="";
    if($slave_mdb->total) {
        $slave_mdb->fetch();
        $opn_ix=$slave_mdb->dt["opn_ix"];
        $set_group=$slave_mdb->dt["set_group"];

        $sql="SELECT option_div FROM shop_product_options_detail WHERE opn_ix='".$opn_ix."' AND set_group='".$set_group."' AND set_group_seq='0' ";
        $slave_mdb->query($sql);
        if($slave_mdb->total) {
            $slave_mdb->fetch();
            $option_div=$slave_mdb->dt["option_div"];
            if($basic_text!="") $option_div.=$basic_text;
        }
    }
    return $option_div;
}

function get_display_search_text(){
    global $slave_mdb;
    //$slave_mdb=new Database;

    $sql="SELECT * FROM shop_search_text WHERE disp='1' and ".time()." between st_sdate AND st_edate order by rand() limit 0,1";
    $slave_mdb->query($sql);
    $slave_mdb->fetch();

    return $slave_mdb->dt[st_type]."|".$slave_mdb->dt[st_title]."|".$slave_mdb->dt[st_ix]."|".$slave_mdb->dt[st_url];
}

function arr_category($cid,$depth){	//상품별 개별배송정책 조회 함수2014-02-05 이학봉
    $arr_category=array();
    for($s=0;$s<$depth;$s++) {
        $sub_categorys2 = subcategorys($cid, $s);
        //print_r($sub_categorys2);
        //$arr_category["s".$s]=$sub_categorys2;
        $cate_txt="";
        $cnt_sub=count($sub_categorys2);
        $cate_txt.="<div class='inputbox_select07'>";
        $cate_txt.="<select name='cid".($s+1)."' style='width:161px;' depth='".($s+1)."' onChange=\"location.href='/shop/goods_list.php?cid='+$(this).val()+'&depth='+$(this).attr('depth')\">";

        for($j=0;$j<$cnt_sub;$j++) {
            $cut_cate_num=($s+1)*3;
            if(substr($sub_categorys2[$j]["cid"],$cut_cate_num,3)==substr($cid,$cut_cate_num,3)) {
                $select_txt="selected";
            } else {
                $select_txt="";
            }
            $cate_txt.="<option value='".$sub_categorys2[$j]["cid"]."' ".$select_txt.">".$sub_categorys2[$j]["cname"]."</option>";
        }
        $cate_txt.="</select>";

        $cate_txt.='</div>';
        array_push($arr_category,array("cate_txt"=>$cate_txt));
    }
    return $arr_category;
}

function getcategoryname_1depth($cid){
    global $slave_mdb;
    //$slave_mdb=new Database;

    $sql = "select
			*
			from
				shop_category_info
			where
				cid = '".$cid."'";
    $slave_mdb->query($sql);
    $slave_mdb->fetch();

    return $slave_mdb->dt[cname];

}

function check_search_category($cid,$search_category){

    if(is_array($search_category)){
        if(in_array($cid,$search_category)){
            return 'checked';
        }else{
            return '';
        }
    }else{
        return '';
    }
}

function getLoanPrice($company_id){
    global $slave_mdb;
    //$slave_mdb=new Database;

    $sql = "select loan_price from
				common_company_detail
				where company_id = '".$company_id."'";
    $slave_mdb->query($sql);
    $slave_mdb->fetch();

    return $slave_mdb->dt[loan_price];

}



function UsableCupon($pid , $sellprice, $pcount, $limit=100, $data_type="html"){
    global $product_price, $user;
    global $slave_mdb;
    //$slave_mdb = new Database;

    $sql ="select * from ".TBL_SHOP_PRODUCT." where id='".$pid."' and coupon_use_yn ='Y' ";
    $slave_mdb->query($sql);
    if($slave_mdb->total){

        if($slave_mdb->dbms_type == "oracle"){

            $sql = "
							select 5 as qnum, cupon_sale_type, cupon_sale_value,cupon_kind, cupon_no, cp.* ,
							case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price and publish_limit_price > 0 then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice
							from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp, ".TBL_SHOP_CUPON_RELATION_PRODUCT." crp
							where c.cupon_ix = cp.cupon_ix
							and cp.publish_condition_price <= ".$sellprice*$pcount."
							and cp.publish_ix = crp.publish_ix
							and crp.pid = '$pid' and cp.use_product_type = 3 and cp.publish_type in (1, 2, 4)
							and ((cp.use_date_type!='9' AND TO_DATE('".date("m-d-Y H:i:s")."','MM-DD-YYYY HH24:MI:SS') between cp.use_sdate and cp.use_edate) OR cp.use_date_type=9 OR cp.use_date_type=2)
							and cp.disp='1'
							";

            if( is_mobile() ){
                $sql .= " and c.cupon_use_div in ('A','M') ";
            }else{
                $sql .= " and c.cupon_use_div in ('A','G') ";
            }

            $sql .= "union
								select  6 as qnum, cupon_sale_type, cupon_sale_value,cupon_kind, cupon_no, cp.*	,
								case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price and publish_limit_price > 0 then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice
								from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp
								where c.cupon_ix = cp.cupon_ix and cp.publish_condition_price <= ".$sellprice*$pcount."
								and cp.use_product_type = 1 and cp.publish_type in (1, 2, 4)
								and ((cp.use_date_type!='9' AND TO_DATE('".date("m-d-Y H:i:s")."','MM-DD-YYYY HH24:MI:SS') between cp.use_sdate and cp.use_edate) OR cp.use_date_type=9 OR cp.use_date_type=2)
								and cp.disp='1'
								";

            if( is_mobile() ){
                $sql .= " and c.cupon_use_div in ('A','M') ";
            }else{
                $sql .= " and c.cupon_use_div in ('A','G') ";
            }

            $sql .= "union
								select  7 as qnum, cupon_sale_type, cupon_sale_value,cupon_kind, cupon_no , cp.*,
								case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price and publish_limit_price > 0 then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice
								from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp, ".TBL_SHOP_CUPON_RELATION_CATEGORY." crc, ".TBL_SHOP_PRODUCT_RELATION." pr
								where c.cupon_ix = cp.cupon_ix and cp.publish_condition_price <= ".$sellprice*$pcount."
								and cp.publish_ix = crc.publish_ix and SUBSTRING(crc.cid,1,(crc.depth+1)*3) = SUBSTRING(pr.cid,1,(crc.depth+1)*3)
								and pr.pid = '$pid' and cp.use_product_type = 2 and cp.publish_type in (1, 2, 4)
								and ((cp.use_date_type!='9' AND TO_DATE('".date("m-d-Y H:i:s")."','MM-DD-YYYY HH24:MI:SS') between cp.use_sdate and cp.use_edate) OR cp.use_date_type=9 OR cp.use_date_type=2)
								and cp.disp='1'
								";

            if( is_mobile() ){
                $sql .= " and c.cupon_use_div in ('A','M') ";
            }else{
                $sql .= " and c.cupon_use_div in ('A','G') ";
            }

            $sql .= "union
								select 8 as qnum, cupon_sale_type, cupon_sale_value,cupon_kind, cupon_no , cp.*,
								case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price and publish_limit_price > 0 then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice
								from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp, ".TBL_SHOP_CUPON_RELATION_BRAND." crb, ".TBL_SHOP_PRODUCT." p
								where c.cupon_ix = cp.cupon_ix and cp.publish_condition_price <= ".$sellprice*$pcount."
								and cp.publish_ix = crb.publish_ix and crb.b_ix = p.brand
								and p.id = '$pid' and cp.use_product_type = 4 and cp.publish_type in (1, 2, 4)
								and ((cp.use_date_type!='9' AND TO_DATE('".date("m-d-Y H:i:s")."','MM-DD-YYYY HH24:MI:SS') between cp.use_sdate and cp.use_edate) OR cp.use_date_type=9 OR cp.use_date_type=2)
								and cp.disp='1'
								";

            if( is_mobile() ){
                $sql .= " and c.cupon_use_div in ('A','M') ";
            }else{
                $sql .= " and c.cupon_use_div in ('A','G') ";
            }

            $sql .= " order by saleprice desc  ";
        }else{

            $sql = "
							select cp.* , 5 as qnum, cupon_sale_type, cupon_sale_value,cupon_kind,
							case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price and publish_limit_price > 0 then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice
							from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp, ".TBL_SHOP_CUPON_RELATION_PRODUCT." crp
							where c.cupon_ix = cp.cupon_ix
							and cp.publish_ix = crp.publish_ix
							and crp.pid = '$pid' and cp.use_product_type = 3 and cp.publish_type in (1, 2, 4)
							and (
							(cp.use_date_type = '3' AND '".date("Y-m-d H:i:s")."' between cp.use_sdate and cp.use_edate)
								OR (cp.use_date_type = '1' AND '".date("Y-m-d H:i:s")."' between cp.regdate and
								case
								when publish_date_type = '1' then date_add(cp.regdate, INTERVAL cp.publish_date_differ YEAR )
								when publish_date_type = '2' then date_add(cp.regdate, INTERVAL cp.publish_date_differ MONTH )
								when publish_date_type = '3' then date_add(cp.regdate, INTERVAL cp.publish_date_differ DAY)
								end)
								OR cp.use_date_type=9
								OR cp.use_date_type=2
							)
							and ".time()." between cupon_use_sdate and cupon_use_edate
							and cp.disp='1'
							and cp.publish_condition_price <= ".$sellprice*$pcount."
							";

            if( is_mobile() ){
                $sql .= " and c.cupon_use_div in ('A','M') ";
            }else{
                $sql .= " and c.cupon_use_div in ('A','G') ";
            }

            //and cp.publish_condition_price <= ".$sellprice*$pcount."
            //2016-01-12 Hong 주석처리 되어 있던거 다시 적용
            //and cp.publish_condition_price <= ".$sellprice*$pcount."

            $sql .= "union
							select cp.*	,  6 as qnum, cupon_sale_type, cupon_sale_value,cupon_kind, case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price and publish_limit_price > 0 then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice
							from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp
							where c.cupon_ix = cp.cupon_ix
							and cp.use_product_type = 1 and cp.publish_type in (1, 2, 4)
							and (
							(cp.use_date_type = '3' AND '".date("Y-m-d H:i:s")."' between cp.use_sdate and cp.use_edate)
								OR (cp.use_date_type = '1' AND '".date("Y-m-d H:i:s")."' between cp.regdate and
								case
								when publish_date_type = '1' then date_add(cp.regdate, INTERVAL cp.publish_date_differ YEAR )
								when publish_date_type = '2' then date_add(cp.regdate, INTERVAL cp.publish_date_differ MONTH )
								when publish_date_type = '3' then date_add(cp.regdate, INTERVAL cp.publish_date_differ DAY)
								end)
								OR cp.use_date_type=9
								OR cp.use_date_type=2
							)
							and ".time()." between cupon_use_sdate and cupon_use_edate
							and cp.disp='1'
							and cp.publish_condition_price <= ".$sellprice*$pcount."
							";

            if( is_mobile() ){
                $sql .= " and c.cupon_use_div in ('A','M') ";
            }else{
                $sql .= " and c.cupon_use_div in ('A','G') ";
            }

            //and cp.publish_condition_price <= ".$sellprice*$pcount."
            $sql .= "union
							select cp.*, 7 as qnum, cupon_sale_type, cupon_sale_value,cupon_kind,
							case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price and publish_limit_price > 0 then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice
							from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp, ".TBL_SHOP_CUPON_RELATION_CATEGORY." crc, ".TBL_SHOP_PRODUCT_RELATION." pr
							where c.cupon_ix = cp.cupon_ix
							and cp.publish_ix = crc.publish_ix and SUBSTRING(crc.cid,1,(crc.depth+1)*3) = SUBSTRING(pr.cid,1,(crc.depth+1)*3)
							and pr.pid = '$pid' and cp.use_product_type = 2 and cp.publish_type in (1, 2, 4)
							and (
							(cp.use_date_type = '3' AND '".date("Y-m-d H:i:s")."' between cp.use_sdate and cp.use_edate)
								OR (cp.use_date_type = '1' AND '".date("Y-m-d H:i:s")."' between cp.regdate and
								case
								when publish_date_type = '1' then date_add(cp.regdate, INTERVAL cp.publish_date_differ YEAR )
								when publish_date_type = '2' then date_add(cp.regdate, INTERVAL cp.publish_date_differ MONTH )
								when publish_date_type = '3' then date_add(cp.regdate, INTERVAL cp.publish_date_differ DAY)
								end)
								OR cp.use_date_type=9
								OR cp.use_date_type=2
							)
							and ".time()." between cupon_use_sdate and cupon_use_edate
							and cp.disp='1'
							and cp.publish_condition_price <= ".$sellprice*$pcount."
							";

            if( is_mobile() ){
                $sql .= " and c.cupon_use_div in ('A','M') ";
            }else{
                $sql .= " and c.cupon_use_div in ('A','G') ";
            }

            //and cp.publish_condition_price <= ".$sellprice*$pcount."
            $sql .= "union
							select cp.*, 8 as qnum, cupon_sale_type, cupon_sale_value,cupon_kind,
							case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price and publish_limit_price > 0 then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice
							from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp, ".TBL_SHOP_CUPON_RELATION_BRAND." crb, ".TBL_SHOP_PRODUCT." p
							where c.cupon_ix = cp.cupon_ix
							and cp.publish_ix = crb.publish_ix
							and (
										(cp.is_include=1 and crb.b_ix = p.brand) or (cp.is_include!=1 and p.brand not in (crb.b_ix) )
								)
							and p.id = '$pid' and cp.use_product_type = 4 and cp.publish_type in (1, 2, 4)
							and (
							(cp.use_date_type = '3' AND '".date("Y-m-d H:i:s")."' between cp.use_sdate and cp.use_edate)
								OR (cp.use_date_type = '1' AND '".date("Y-m-d H:i:s")."' between cp.regdate and
								case
								when publish_date_type = '1' then date_add(cp.regdate, INTERVAL cp.publish_date_differ YEAR )
								when publish_date_type = '2' then date_add(cp.regdate, INTERVAL cp.publish_date_differ MONTH )
								when publish_date_type = '3' then date_add(cp.regdate, INTERVAL cp.publish_date_differ DAY)
								end)
								OR cp.use_date_type=9
								OR cp.use_date_type=2
							)
							and ".time()." between cupon_use_sdate and cupon_use_edate
							and cp.disp='1'
							and cp.publish_condition_price <= ".$sellprice*$pcount."
							";

            if( is_mobile() ){
                $sql .= " and c.cupon_use_div in ('A','M') ";
            }else{
                $sql .= " and c.cupon_use_div in ('A','G') ";
            }

            //and cp.publish_condition_price <= ".$sellprice*$pcount."
            //특정 셀러에 발행된 쿠폰
            $sql .= "union
							select cp.*, 9 as qnum, cupon_sale_type, cupon_sale_value,cupon_kind,
							case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price and publish_limit_price > 0 then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice
							from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp, shop_cupon_relation_seller crs, ".TBL_SHOP_PRODUCT." p
							where c.cupon_ix = cp.cupon_ix
							and cp.publish_ix = crs.publish_ix 
							and (
									(cp.is_include=1 and crs.company_id = p.admin) or (cp.is_include!=1 and p.admin not in (crs.company_id) )
							)
							and p.id = '$pid' and cp.use_product_type = 5 and cp.publish_type in (1, 2, 4)
							and (
							(cp.use_date_type = '3' AND '".date("Y-m-d H:i:s")."' between cp.use_sdate and cp.use_edate)
								OR (cp.use_date_type = '1' AND '".date("Y-m-d H:i:s")."' between cp.regdate and
								case
								when publish_date_type = '1' then date_add(cp.regdate, INTERVAL cp.publish_date_differ YEAR )
								when publish_date_type = '2' then date_add(cp.regdate, INTERVAL cp.publish_date_differ MONTH )
								when publish_date_type = '3' then date_add(cp.regdate, INTERVAL cp.publish_date_differ DAY)
								end)
								OR cp.use_date_type=9
								OR cp.use_date_type=2
							)
							and ".time()." between cupon_use_sdate and cupon_use_edate
							and cp.disp='1'
							and cp.publish_condition_price <= ".$sellprice*$pcount."
							";

            if( is_mobile() ){
                $sql .= " and c.cupon_use_div in ('A','M') ";
            }else{
                $sql .= " and c.cupon_use_div in ('A','G') ";
            }

            //and cp.publish_condition_price <= ".$sellprice*$pcount."
            $sql = "select data.* , cpc.cpc_ix, cpc.r_ix from (".$sql.") data
						left join shop_cupon_publish_config cpc on data.publish_ix = cpc.publish_ix
						having (data.publish_type = '4' and cpc.r_ix = '".$_SESSION["user"]["gp_ix"]."') or (data.publish_type = '1' and cpc.r_ix = '".$_SESSION["user"]["code"]."') or data.publish_type = 2 ";
            $sql .= " order by saleprice desc limit 0,$limit
						 ";
            //and cpc.publish_type = '4'

        }

        //exit;
        //echo nl2br($sql);
        //echo "<br><br><br>";
        $slave_mdb->query($sql);
        $result=$slave_mdb->fetchall();
        if($data_type == "list"){
            return $result;
        }
        //echo $slave_mdb->total."<br>";
        $add_coupon_cnt=$slave_mdb->total;
    }
    $arr_coupon_cnt=array();
    $user_coupon_cnt=0;
    //echo $_SESSION["user"]["code"];
    if (is_array($result)){
        foreach ($result as $key => $sub_array) {
            //echo $sub_array["publish_ix"];
            //$select_ = array("icons_list"=>explode(";",$sub_array[icons]));
            //array_insert($sub_array,14,$select_);
            //$sql = "select count(cr.regist_ix) AS cnt from ".TBL_SHOP_CUPON_PUBLISH." cp ,".TBL_SHOP_CUPON_REGIST." cr where cr.publish_ix = cp.publish_ix and cp.publish_ix ='".$sub_array["publish_ix"]."' AND cr.mem_ix='".$_SESSION["user"]["code"]."' ";//프로세스가 맞지 않는 것 같아 주석 처리 kbk 12/01/20
            $sql = "select regist_ix from ".TBL_SHOP_CUPON_PUBLISH." cp ,".TBL_SHOP_CUPON_REGIST." cr where cr.publish_ix = cp.publish_ix and cp.publish_ix ='".$sub_array["publish_ix"]."' AND cr.mem_ix='".$_SESSION["user"]["code"]."' ";
            $slave_mdb->query($sql);
            //echo $sub_array["publish_ix"]." - ".$slave_mdb->total."<br />";
            if($slave_mdb->total>0) {
                $user_coupon_cnt+=1;
            }
            /*$slave_mdb->fetch();
			$cnt=$slave_mdb->dt["cnt"];
			$select_ = array("cupon_regist_yn"=>$cnt);
			array_insert($sub_array,14,$select_);
			$add_coupon_cnt-=$cnt;

			$result[$key] = $sub_array;*///프로세스가 맞지 않는 것 같아 주석 처리 kbk 12/01/20
        }
    }
    $arr_coupon_cnt[0]["add_coupon_cnt"] = $add_coupon_cnt;
    $arr_coupon_cnt[0]["user_coupon_cnt"] = $add_coupon_cnt - $user_coupon_cnt;
    return $arr_coupon_cnt;exit;//다운 가능한 쿠폰을 비로그인 상태일때는 조건없이 전체 갯수를 보여주고 로그인을 하면 다운받은 쿠폰 검사를 해서 다운 가능한 쿠폰수를 보여준다 kbk 12/01/20
    return $add_coupon_cnt;exit; //kbk

    if($data_type == "array"){
        $datas = $slave_mdb->fetchall();
        return $datas[0];
        exit;
    }

    $mstring =  "<table cellpadding=0 cellspacing=0 width=400>";

    if($slave_mdb->total){
        $mstring .= "       <tr height=23 bgcolor=#efefef align=center><td width=70 >할인금액</td><td >쿠폰명</td><!--td>쿠폰번호</td--></tr>";

        for($i=0;$i < $slave_mdb->total;$i++){
            $slave_mdb->fetch($i);

            $saleprice = ($slave_mdb->dt[cupon_sale_type] == 1 ? intval($slave_mdb->dt[cupon_sale_value]/100*$sellprice*$pcount):$slave_mdb->dt[cupon_sale_value]);

            $mstring .= "       <tr height=23 ><td style='padding:0 10 0 0' align=right>".number_format($slave_mdb->dt[saleprice])." 원</td><td >".$slave_mdb->dt[cupon_kind]." </td><!--td>".$slave_mdb->dt[cupon_no]." </td--></tr>";

        }
    }else{
        $mstring .=  "     <tr><td>쿠폰선택</td></tr>";
    }

    $mstring .= "</table>";

    // return $mstring;

}

//2014-05-13 Hong 추가 (클래임 변동금액 산출함수)
function clameChangePriceCalculate($data,$return_zip=""){
    global $_DISCOUNT_TYPE;
    global $slave_mdb;
    //$slave_mdb = new Database;

    $claim_apply_od_ix = array();
    $claim_apply_info = array();

    for($i = 0; $i < count($data); $i++){
        if($data[$i][claim_apply_yn]=="Y" && $data[$i][delivery_package] == 'N'){//교환요청상품 과 묶음상품만
            $company_package_apply_cnt[$data[$i][company_id]] += $data[$i][claim_apply_cnt];
        }

        if($data[$i][claim_apply_yn]=="Y"){
            $claim_apply_od_ix[] = $data[$i][od_ix];
            $claim_apply_info[$data[$i][od_ix]] = $data[$i][claim_apply_cnt];
        }
    }

    $total_delivery_price=0;
    $total_claim_delivery_price=0;
    $total_etc_dcprice=0;
    $tax_free_price=0;
    $tax_price=0;
    $total_coupon_dcprice=0;
    $total_change_delivery_price=0;
    $addDeliveryPrice = 0;

    for($i = 0; $i < count($data); $i++){

        $total_product_dcprice=0;

        //상품 할인전 취소금액
        if($data[$i][claim_apply_yn]=="N"){//교환배송상품
            $pm_sign = -1;
        }else{
            $pm_sign = 1;
        }

        $product_price = (($data[$i][psprice]+$data[$i][option_price]) * $data[$i][claim_apply_cnt]) * $pm_sign;
        $total_product_price += $product_price;
        //echo $product_price."<br/><br/>";
        //배송비

        //if($b_claim_group != $data[$i][claim_group]){
        if($b_ode_ix != $data[$i][ode_ix]){
            $sql="select * from shop_order_delivery where ode_ix='".$data[$i][ode_ix]."'";
            $slave_mdb->query($sql);
            $slave_mdb->fetch("object");
            $org_delivery_price = $slave_mdb->dt[delivery_dcprice];
            $total_org_delivery_price += $slave_mdb->dt[delivery_dcprice];

            $resulte_data["delivery"][$data[$i][company_id]]["org_delivery_price"] += $org_delivery_price;
        }

        if($data[$i][claim_type]=="C"){//취소요청시!!!
            //배송비 취소금액 (취소는 배송은 안했기 때문에 배송비는 돌려주어야 한다)

            if($data[$i][delivery_pay_method]=="1"){//선불만

                //배송비 조건
                if($data[$i][delivery_package] == 'Y'){
                    $where_package = " and pid = '".$data[$i][pid]."' and delivery_package = 'Y' and delivery_type='".$data[$i][delivery_type]."' and delivery_method='".$data[$i][delivery_method]."'
					and delivery_pay_type='".$data[$i][delivery_pay_method]."' and delivery_addr_use='".$data[$i][delivery_addr_use]."' and factory_info_addr_ix='".$data[$i][factory_info_addr_ix]."' and
						(select ifnull(sum(pcnt),0) as pcnt from shop_order_detail where oid='".$data[$i][oid]."' and pid='".$data[$i][pid]."' and ori_company_id='".$data[$i][ori_company_id]."' and delivery_package = 'Y' and delivery_type='".$data[$i][delivery_type]."' and delivery_method='".$data[$i][delivery_method]."' and delivery_pay_method='".$data[$i][delivery_pay_method]."' and delivery_addr_use='".$data[$i][delivery_addr_use]."' and factory_info_addr_ix='".$data[$i][factory_info_addr_ix]."')
						=
						((select ifnull(sum(pcnt),0) as pcnt from shop_order_detail where oid='".$data[$i][oid]."' and pid='".$data[$i][pid]."' and ori_company_id='".$data[$i][ori_company_id]."' and delivery_package = 'Y' and delivery_type='".$data[$i][delivery_type]."' and delivery_method='".$data[$i][delivery_method]."' and delivery_pay_method='".$data[$i][delivery_pay_method]."' and delivery_addr_use='".$data[$i][delivery_addr_use]."' and factory_info_addr_ix='".$data[$i][factory_info_addr_ix]."' and refund_status in ('".ORDER_STATUS_REFUND_APPLY."','".ORDER_STATUS_REFUND_COMPLETE."')
						) + '".$data[$i][claim_apply_cnt]."' ) ";
                    $delivery_bool=true;
                }else{

                    $where_package = " and delivery_package = 'N' and delivery_type='".$data[$i][delivery_type]."' and delivery_method='".$data[$i][delivery_method]."'
					and delivery_pay_type='".$data[$i][delivery_pay_method]."' and delivery_addr_use='".$data[$i][delivery_addr_use]."' and factory_info_addr_ix='".$data[$i][factory_info_addr_ix]."' and
						(select ifnull(sum(pcnt),0) as pcnt from shop_order_detail where oid='".$data[$i][oid]."' and ori_company_id='".$data[$i][ori_company_id]."' and delivery_package = 'N' and delivery_type='".$data[$i][delivery_type]."' and delivery_method='".$data[$i][delivery_method]."' and delivery_pay_method='".$data[$i][delivery_pay_method]."' and delivery_addr_use='".$data[$i][delivery_addr_use]."' and factory_info_addr_ix='".$data[$i][factory_info_addr_ix]."')
						=
						((select ifnull(sum(pcnt),0) as pcnt from shop_order_detail where oid='".$data[$i][oid]."' and ori_company_id='".$data[$i][ori_company_id]."' and delivery_package = 'N' and delivery_type='".$data[$i][delivery_type]."' and delivery_method='".$data[$i][delivery_method]."' and delivery_pay_method='".$data[$i][delivery_pay_method]."' and delivery_addr_use='".$data[$i][delivery_addr_use]."' and factory_info_addr_ix='".$data[$i][factory_info_addr_ix]."' and refund_status in ('".ORDER_STATUS_REFUND_APPLY."','".ORDER_STATUS_REFUND_COMPLETE."')
						) + '".$company_package_apply_cnt[$data[$i][company_id]]."' )";

                    if($b_company_id != $data[$i][company_id]){
                        $delivery_bool=true;
                    }else{
                        $delivery_bool = false; //한업체에 묶음 배송비는 한번만!
                    }
                }


                if($delivery_bool){

                    $sql="select * from shop_order_delivery where oid='".$data[$i][oid]."' and ori_company_id='".$data[$i][ori_company_id]."' $where_package ";
                    $slave_mdb->query($sql);
                    $total_cancel_bool = $slave_mdb->total;
                    $slave_mdb->fetch();

                    $total_delivery_price += $slave_mdb->dt[delivery_dcprice];
                    $resulte_data["delivery"][$data[$i][company_id]]["delivery_price"] += $slave_mdb->dt[delivery_dcprice];

                    if($data[$i][claim_fault_type]=="B" && !$total_cancel_bool){
                        //조건 배송비 체크하기!!!!
                        $oinfo[oid]=$data[$i][oid];
                        $oinfo[ode_ix]=$data[$i][ode_ix];
                        $oinfo[not_od_ix]=$claim_apply_od_ix;
                        $oinfo[od_refund_info]=$claim_apply_info;
                        $oid_info = serialize($oinfo);
                        $re_delivery_price = getDeliveryPrice("","","","",$oid_info,"","","","","","","","","","");

                        $change_delivery_price = $org_delivery_price - $re_delivery_price;
                        if($change_delivery_price != 0 ){// != 일단 > 로 처리함!

                            $resulte_data["delivery"][$data[$i][company_id]]["delivery_price"] += $change_delivery_price;
                            $resulte_data["delivery"][$data[$i][company_id]]["change_delivery_price"] += $change_delivery_price;
                            $total_change_delivery_price += $change_delivery_price;

                            $resulte_data["delivery"][$data[$i][company_id]]["claim_coment"].="조건배송비변동 ".number_format($org_delivery_price)." -> ".number_format($re_delivery_price)."원<br/>";
                            $resulte_data["delivery"]["claim_coment"].="조건배송비변동 ".number_format($org_delivery_price)." -> ".number_format($re_delivery_price)."원<br/>";
                        }
                    }
                }
            }
        }else{//반품 또는 교환시
            /*
			if($refund_mode && $data[$i][claim_type]=="E" && false){
				if($b_claim_group != $data[$i][claim_group]){//요청한 그룹별로 배송비부가!

					$sql="SELECT * FROM shop_order_delivery WHERE oid='".$data[$i]["oid"]."' and ori_company_id='".$data[$i]["claim_group"]."' and delivery_policy='9' ";
					$slave_mdb->query($sql);
					$slave_mdb->fetch("object");
					$d_info=$slave_mdb->dt;

					$resulte_data["delivery"][$data[$i][company_id]]["claim_delivery_price"] -= $d_info[delivery_price];
					$resulte_data["delivery"][$data[$i][company_id]]["claim_coment"].="교환배송비 ".number_format($d_info[delivery_price])."<br/>";
					$resulte_data["delivery"]["claim_coment"].="교환배송비 ".number_format($d_info[delivery_price])."<br/>";

				}
			}else{
			*/
            if($data[$i][claim_fault_type]=="B"){//구매자귀책시! 배송비 부가!
                if($b_claim_group != $data[$i][claim_group]){//요청한 그룹별로 배송비부가!

                    $sql="select * from shop_delivery_template where dt_ix='".$data[$i][dt_ix]."' ";
                    $slave_mdb->query($sql);
                    $dt_info=$slave_mdb->fetch("object");

                    if ($dt_info['delivery_region_use'] == '1') {
                        if(!empty($return_zip)){
                            $sql = "select price from ".TBL_SHOP_ADD_DELIVERY_AREA." where zip = '".$return_zip."' ";
                            $slave_mdb->query($sql);
                            if($slave_mdb->total){
                                $slave_mdb->fetch();
                                $addDeliveryPrice = $slave_mdb->dt['price'];
                            }

                        }
                    }

                    if($data[$i][claim_type]=="R"){//반품
                        //delivery_policy
                        if($dt_info[delivery_policy]=="1" || $org_delivery_price==0){

                            if($data[$i][delivery_package] == 'Y'){
                                $where_package = " and pid = '".$data[$i][pid]."' and delivery_package = 'Y' and delivery_type='".$data[$i][delivery_type]."' and delivery_method='".$data[$i][delivery_method]."'
									and delivery_pay_type='".$data[$i][delivery_pay_method]."' and delivery_addr_use='".$data[$i][delivery_addr_use]."' and factory_info_addr_ix='".$data[$i][factory_info_addr_ix]."' and
										(select ifnull(sum(pcnt),0) as pcnt from shop_order_detail where oid='".$data[$i][oid]."' and pid='".$data[$i][pid]."' and ori_company_id='".$data[$i][ori_company_id]."' and delivery_package = 'Y' and delivery_type='".$data[$i][delivery_type]."' and delivery_method='".$data[$i][delivery_method]."' and delivery_pay_method='".$data[$i][delivery_pay_method]."' and delivery_addr_use='".$data[$i][delivery_addr_use]."' and factory_info_addr_ix='".$data[$i][factory_info_addr_ix]."')
										=
										((select ifnull(sum(pcnt),0) as pcnt from shop_order_detail where oid='".$data[$i][oid]."' and pid='".$data[$i][pid]."' and ori_company_id='".$data[$i][ori_company_id]."' and delivery_package = 'Y' and delivery_type='".$data[$i][delivery_type]."' and delivery_method='".$data[$i][delivery_method]."' and delivery_pay_method='".$data[$i][delivery_pay_method]."' and delivery_addr_use='".$data[$i][delivery_addr_use]."' and factory_info_addr_ix='".$data[$i][factory_info_addr_ix]."' and refund_status in ('".ORDER_STATUS_REFUND_COMPLETE."')
										) + '".$data[$i][claim_apply_cnt]."' ) ";
                                $delivery_bool=true;
                            }else{

                                $where_package = " and delivery_package = 'N' and delivery_type='".$data[$i][delivery_type]."' and delivery_method='".$data[$i][delivery_method]."'
									and delivery_pay_type='".$data[$i][delivery_pay_method]."' and delivery_addr_use='".$data[$i][delivery_addr_use]."' and factory_info_addr_ix='".$data[$i][factory_info_addr_ix]."' and
										(select ifnull(sum(pcnt),0) as pcnt from shop_order_detail where oid='".$data[$i][oid]."' and ori_company_id='".$data[$i][ori_company_id]."' and delivery_package = 'N' and delivery_type='".$data[$i][delivery_type]."' and delivery_method='".$data[$i][delivery_method]."' and delivery_pay_method='".$data[$i][delivery_pay_method]."' and delivery_addr_use='".$data[$i][delivery_addr_use]."' and factory_info_addr_ix='".$data[$i][factory_info_addr_ix]."')
										=
										((select ifnull(sum(pcnt),0) as pcnt from shop_order_detail where oid='".$data[$i][oid]."' and ori_company_id='".$data[$i][ori_company_id]."' and delivery_package = 'N' and delivery_type='".$data[$i][delivery_type]."' and delivery_method='".$data[$i][delivery_method]."' and delivery_pay_method='".$data[$i][delivery_pay_method]."' and delivery_addr_use='".$data[$i][delivery_addr_use]."' and factory_info_addr_ix='".$data[$i][factory_info_addr_ix]."' and refund_status in ('".ORDER_STATUS_REFUND_COMPLETE."')
										) + '".$company_package_apply_cnt[$data[$i][company_id]]."' )";
                            }

                            $sql="select * from shop_order_delivery where oid='".$data[$i][oid]."' and ori_company_id='".$data[$i][ori_company_id]."' $where_package ";
                            $slave_mdb->query($sql);
                            $total_cancel_bool = $slave_mdb->total;

                            if($total_cancel_bool){
                                if($dt_info[return_shipping_cnt] == 2){//반품 배송비/편도,왕복 1:편도 2:왕복
                                    $claim_delivery_price = ($dt_info[return_shipping_price]*2)+$addDeliveryPrice;
                                    $claim_coment="반품 배송비 왕복(".number_format($claim_delivery_price)."원)*2";
                                }else{
                                    $claim_delivery_price = $dt_info[return_shipping_price]+$addDeliveryPrice;
                                    $claim_coment="반품 배송비 편도(".number_format($claim_delivery_price)."원)";
                                }
                            }else{
                                $claim_delivery_price = $dt_info[return_shipping_price]+$addDeliveryPrice;
                                $claim_coment="반품 배송비 편도(".number_format($claim_delivery_price)."원)";
                            }
                        }else{
                            $claim_delivery_price = $dt_info[return_shipping_price]+$addDeliveryPrice;
                            $claim_coment="반품 배송비 편도(".number_format($claim_delivery_price)."원)";
                        }
                    }elseif($data[$i][claim_type]=="E"){//교환
                        $claim_delivery_price = $dt_info[exchange_shipping_price]*2;
                        $claim_coment="교환 배송비 편도(".number_format($dt_info[exchange_shipping_price])."원)*2 ";
                    }

                    $total_claim_delivery_price-=$claim_delivery_price;
                    $resulte_data["delivery"][$data[$i][company_id]]["claim_delivery_price"] -= $claim_delivery_price;
                    $resulte_data["delivery"][$data[$i][company_id]]["claim_coment"].=$claim_coment."<br/>";
                    $resulte_data["delivery"]["claim_coment"].=$claim_coment."<br/>";
                }
            }else{
                //판매자 귀책 반품시 배송비 금액도 돌려줘야 함
                if($data[$i][delivery_pay_method]=="1"){//선불만

                    //배송비 조건
                    if($data[$i][delivery_package] == 'Y'){
                        $where_package = " and pid = '".$data[$i][pid]."' and delivery_package = 'Y' and delivery_type='".$data[$i][delivery_type]."' and delivery_method='".$data[$i][delivery_method]."'
					and delivery_pay_type='".$data[$i][delivery_pay_method]."' and delivery_addr_use='".$data[$i][delivery_addr_use]."' and factory_info_addr_ix='".$data[$i][factory_info_addr_ix]."' and
						(select ifnull(sum(pcnt),0) as pcnt from shop_order_detail where oid='".$data[$i][oid]."' and pid='".$data[$i][pid]."' and ori_company_id='".$data[$i][ori_company_id]."' and delivery_package = 'Y' and delivery_type='".$data[$i][delivery_type]."' and delivery_method='".$data[$i][delivery_method]."' and delivery_pay_method='".$data[$i][delivery_pay_method]."' and delivery_addr_use='".$data[$i][delivery_addr_use]."' and factory_info_addr_ix='".$data[$i][factory_info_addr_ix]."')
						=
						((select ifnull(sum(pcnt),0) as pcnt from shop_order_detail where oid='".$data[$i][oid]."' and pid='".$data[$i][pid]."' and ori_company_id='".$data[$i][ori_company_id]."' and delivery_package = 'Y' and delivery_type='".$data[$i][delivery_type]."' and delivery_method='".$data[$i][delivery_method]."' and delivery_pay_method='".$data[$i][delivery_pay_method]."' and delivery_addr_use='".$data[$i][delivery_addr_use]."' and factory_info_addr_ix='".$data[$i][factory_info_addr_ix]."' and refund_status in ('".ORDER_STATUS_REFUND_APPLY."','".ORDER_STATUS_REFUND_COMPLETE."')
						) + '".$data[$i][claim_apply_cnt]."' ) ";
                        $delivery_bool=true;
                    }else{

                        $where_package = " and delivery_package = 'N' and delivery_type='".$data[$i][delivery_type]."' and delivery_method='".$data[$i][delivery_method]."'
					and delivery_pay_type='".$data[$i][delivery_pay_method]."' and delivery_addr_use='".$data[$i][delivery_addr_use]."' and factory_info_addr_ix='".$data[$i][factory_info_addr_ix]."' and
						(select ifnull(sum(pcnt),0) as pcnt from shop_order_detail where oid='".$data[$i][oid]."' and ori_company_id='".$data[$i][ori_company_id]."' and delivery_package = 'N' and delivery_type='".$data[$i][delivery_type]."' and delivery_method='".$data[$i][delivery_method]."' and delivery_pay_method='".$data[$i][delivery_pay_method]."' and delivery_addr_use='".$data[$i][delivery_addr_use]."' and factory_info_addr_ix='".$data[$i][factory_info_addr_ix]."')
						=
						((select ifnull(sum(pcnt),0) as pcnt from shop_order_detail where oid='".$data[$i][oid]."' and ori_company_id='".$data[$i][ori_company_id]."' and delivery_package = 'N' and delivery_type='".$data[$i][delivery_type]."' and delivery_method='".$data[$i][delivery_method]."' and delivery_pay_method='".$data[$i][delivery_pay_method]."' and delivery_addr_use='".$data[$i][delivery_addr_use]."' and factory_info_addr_ix='".$data[$i][factory_info_addr_ix]."' and refund_status in ('".ORDER_STATUS_REFUND_APPLY."','".ORDER_STATUS_REFUND_COMPLETE."')
						) + '".$company_package_apply_cnt[$data[$i][company_id]]."' )";

                        if($b_company_id != $data[$i][company_id]){
                            $delivery_bool=true;
                        }else{
                            $delivery_bool = false; //한업체에 묶음 배송비는 한번만!
                        }
                    }


                    if($delivery_bool){

                        $sql="select * from shop_order_delivery where oid='".$data[$i][oid]."' and ori_company_id='".$data[$i][ori_company_id]."' $where_package ";
                        $slave_mdb->query($sql);
                        $total_cancel_bool = $slave_mdb->total;
                        $slave_mdb->fetch();

                        $total_delivery_price += $slave_mdb->dt[delivery_dcprice];
                        $resulte_data["delivery"][$data[$i][company_id]]["delivery_price"] += $slave_mdb->dt[delivery_dcprice];

                        if($data[$i][claim_fault_type]=="B" && !$total_cancel_bool){
                            //조건 배송비 체크하기!!!!
                            $oinfo[oid]=$data[$i][oid];
                            $oinfo[ode_ix]=$data[$i][ode_ix];
                            $oinfo[not_od_ix]=$claim_apply_od_ix;
                            $oinfo[od_refund_info]=$claim_apply_info;
                            $oid_info = serialize($oinfo);
                            $re_delivery_price = getDeliveryPrice("","","","",$oid_info,"","","","","","","","","","");

                            $change_delivery_price = $org_delivery_price - $re_delivery_price;
                            if($change_delivery_price != 0 ){// != 일단 > 로 처리함!

                                $resulte_data["delivery"][$data[$i][company_id]]["delivery_price"] += $change_delivery_price;
                                $resulte_data["delivery"][$data[$i][company_id]]["change_delivery_price"] += $change_delivery_price;
                                $total_change_delivery_price += $change_delivery_price;

                                $resulte_data["delivery"][$data[$i][company_id]]["claim_coment"].="조건배송비변동 ".number_format($org_delivery_price)." -> ".number_format($re_delivery_price)."원<br/>";
                                $resulte_data["delivery"]["claim_coment"].="조건배송비변동 ".number_format($org_delivery_price)." -> ".number_format($re_delivery_price)."원<br/>";
                            }
                        }
                    }
                }
            }
            //}
        }



        //할인취소상세금액
        if($data[$i]["claim_discount_type"]=="array"){//교환시 다른 상품일때 새로운 할인정보!!
            if(count($data[$i]["discount_desc"]) > 0){
                foreach($data[$i]["discount_desc"] as $dc){
                    //discount_type 할인타입(MC:복수구매,MG:그룹,C:카테고리,GP:기획,SP:특별,CP:쿠폰,SCP:중복쿠폰,M:모바일,E:에누리,DCP:배송쿠폰,DE:배송비에누리)

                    if(!in_array($dc['discount_type'],array("CP","SCP","DCP"))){
                        $dc_etc_info[$dc['discount_type']]+=($dc['discount_price'] * $pm_sign);
                        $total_etc_dcprice+=($dc['discount_price'] * $pm_sign);
                        $total_product_dcprice+=($dc['discount_price'] * $pm_sign);

                    }
                }
            }

            //print_r($data[$i]["discount_desc"])."<br/><br/>";
        }elseif($data[$i]["claim_discount_type"]=="cupon"){

            $sql="select * from shop_order_detail_discount where oid='".$data[$i][oid]."' and od_ix='".$data[$i][od_ix]."'";
            $slave_mdb->query($sql);
            $discount = $slave_mdb->fetchall("object");

            //$rate = round($data[$i]["claim_apply_cnt"]/$data[$i]["pcnt"],2);

            if(count($discount) > 0){
                foreach($discount as $dc){
                    if($data[$i]["claim_apply_cnt"]==$data[$i]["pcnt"]){
                        //전체취소,반품,교환일때
                        $dc_etc_info[$dc['dc_type']]+=($dc['dc_price'] * $pm_sign);
                        $total_etc_dcprice+=($dc['dc_price'] * $pm_sign);
                        $total_product_dcprice+=($dc['dc_price'] * $pm_sign);
                    }else{
                        /*
						$dc_etc_info[$dc['dc_type']]+=round($dc['dc_price'] * $rate * $pm_sign);
						$total_etc_dcprice+=round($dc['dc_price'] * $rate * $pm_sign);
						$total_product_dcprice+=round($dc['dc_price'] * $rate * $pm_sign);
						*/
                        $dc_etc_info[$dc['dc_type']]+=round(($dc['dc_price'] / $data[$i]["pcnt"]) * $data[$i]["claim_apply_cnt"] * $pm_sign);
                        $total_etc_dcprice+=round(($dc['dc_price'] / $data[$i]["pcnt"]) * $data[$i]["claim_apply_cnt"] * $pm_sign);
                        $total_product_dcprice+=round(($dc['dc_price'] / $data[$i]["pcnt"]) * $data[$i]["claim_apply_cnt"] * $pm_sign);
                    }
                }
            }

            if(count($data[$i]["discount_desc"]) > 0){
                foreach($data[$i]["discount_desc"] as $dc){
                    //discount_type 할인타입(MC:복수구매,MG:그룹,C:카테고리,GP:기획,SP:특별,CP:쿠폰,SCP:중복쿠폰,M:모바일,E:에누리,DCP:배송쿠폰,DE:배송비에누리)
                    if(in_array($dc['discount_type'],array("CP","SCP",'DCP'))){
                        $dc_etc_info[$dc['discount_type']]+=$dc['discount_price'];
                        $total_etc_dcprice+=($dc['discount_price'] * $pm_sign);
                        $total_product_dcprice+=($dc['discount_price'] * $pm_sign);
                    }
                }
            }

        }else{
            $sql="select * from shop_order_detail_discount where oid='".$data[$i][oid]."' and od_ix='".$data[$i][od_ix]."' ";
            $slave_mdb->query($sql);
            $discount = $slave_mdb->fetchall("object");

            //$rate = round($data[$i]["claim_apply_cnt"]/$data[$i]["pcnt"],2);

            if(count($discount) > 0){
                foreach($discount as $dc){
                    //dc_type 할인타입(MC:복수구매,MG:그룹,C:카테고리,GP:기획,SP:특별,CP:쿠폰,SCP:중복쿠폰,M:모바일,E:에누리,DCP:배송쿠폰,DE:배송비에누리)
                    //배송비 쿠폰은 정책이 정해지면!
                    if(in_array($dc['dc_type'],array("CP","SCP","DCP"))){
                        //반품시 배송비 쿠폰의 경우 할인 혜택 받은 금액에 대해 환불 해주지 않음 (구매자 귀책시)
                        if($dc['dc_type'] == 'DCP') {
                            if ($data[$i]['claim_type'] == 'R') {
                                if ($data[$i]['claim_fault_type'] == 'B') {
                                    //구매자 귀책사유일때 할인 받은 금액 제외처리 하지 않는다는 배럴 정책에 의한 주석 처리
//                                    $resulte_data["product"][$data[$i][od_ix]]["change_coupon_dcprice"] += ($dc['dc_price'] * $pm_sign);
//                                    $resulte_data["product"][$data[$i][od_ix]]["change_coupon_coment"].="<br/><b>". number_format($dc['dc_price'] * $pm_sign) . "원</b> " . str_replace("|"," ",$dc['dc_msg']);
//                                    $resulte_data["product"]["change_coupon_coment"].="<br/><b>". number_format($dc['dc_price'] * $pm_sign) . "원</b> ".str_replace("|"," ",$dc['dc_msg']);
//                                    $total_coupon_dcprice+=($dc['dc_price'] * $pm_sign);
//                                    $total_product_dcprice+=($dc['dc_price'] * $pm_sign);
                                }
                            }
                        }else{
                            $resulte_data["product"][$data[$i][od_ix]]["change_coupon_dcprice"] += ($dc['dc_price'] * $pm_sign);
                            $resulte_data["product"][$data[$i][od_ix]]["change_coupon_coment"].="<br/><b>". number_format($dc['dc_price'] * $pm_sign) . "원</b> " . str_replace("|"," ",$dc['dc_msg']);
                            $resulte_data["product"]["change_coupon_coment"].="<br/><b>". number_format($dc['dc_price'] * $pm_sign) . "원</b> ".str_replace("|"," ",$dc['dc_msg']);
                            $total_coupon_dcprice+=($dc['dc_price'] * $pm_sign);
                            $total_product_dcprice+=($dc['dc_price'] * $pm_sign);
                        }


                    }else{
                        if($data[$i]["claim_apply_cnt"]==$data[$i]["pcnt"]){
                            //전체취소,반품,교환일때
                            $dc_etc_info[$dc['dc_type']]+=($dc['dc_price'] * $pm_sign);
                            $total_etc_dcprice+=($dc['dc_price'] * $pm_sign);
                            $total_product_dcprice+=($dc['dc_price'] * $pm_sign);
                        }else{
                            /*
							$dc_etc_info[$dc['dc_type']]+=round($dc['dc_price'] * $rate * $pm_sign);
							$total_etc_dcprice+=round($dc['dc_price'] * $rate * $pm_sign);
							$total_product_dcprice+=round($dc['dc_price'] * $rate * $pm_sign);
							*/
                            $dc_etc_info[$dc['dc_type']]+=round(($dc['dc_price'] / $data[$i]["pcnt"]) * $data[$i]["claim_apply_cnt"] * $pm_sign);
                            $total_etc_dcprice+=round(($dc['dc_price'] / $data[$i]["pcnt"]) * $data[$i]["claim_apply_cnt"] * $pm_sign);
                            $total_product_dcprice+=round(($dc['dc_price'] / $data[$i]["pcnt"]) * $data[$i]["claim_apply_cnt"] * $pm_sign);
                        }
                    }
                }
            }
        }

        //상품 과세 비과세금액
        if($data[$i][surtax_yorn]=="Y"){
            $tax_free_price+=$product_price-$total_product_dcprice;//비과세금액
        }else{
            $tax_price+=$product_price-$total_product_dcprice;//과세금액
        }


        $b_ode_ix = $data[$i][ode_ix];
        $b_claim_group = $data[$i][claim_group];
        $b_company_id = $data[$i][company_id];
    }

    $total_dcprice = $total_etc_dcprice + $total_coupon_dcprice;

    $total_apply_product_price = $total_product_price - $total_dcprice;
    $total_apply_delivery_price = $total_delivery_price + $total_claim_delivery_price + $total_change_delivery_price;
    $total_apply_price = $total_apply_product_price + $total_apply_delivery_price;

    $dc_price_coment="";


    if(count($dc_etc_info) > 0){
        foreach($dc_etc_info as $type => $price){
            if($price != 0){
                $dc_price_coment.=", ".$_DISCOUNT_TYPE[$type]." ".number_format($price)."원";
            }
        }
    }

    if($dc_price_coment!="")
        $dc_price_coment = substr($dc_price_coment,1);

    $resulte_data["price"]=$total_apply_price;
    $resulte_data["tax_price"]=$tax_price + $total_apply_delivery_price;
    $resulte_data["tax_free_price"]=$tax_free_price;
    $resulte_data["product"]["product_price"] = $total_product_price;
    $resulte_data["product"]["product_dc_price"] = $total_apply_product_price;
    $resulte_data["product"]["dc_price"] = $total_etc_dcprice;
    $resulte_data["product"]["dc_price_coment"] = $dc_price_coment;
    $resulte_data["product"]["change_coupon_dcprice"] = $total_coupon_dcprice;
    $resulte_data["product"]["tax_price"]=$tax_price;
    $resulte_data["product"]["tax_free_price"]=$tax_free_price;
    $resulte_data["delivery"]["org_delivery_price"] = $total_org_delivery_price;
    $resulte_data["delivery"]["delivery_price"] = $total_delivery_price;
    $resulte_data["delivery"]["delivery_dc_price"] = $total_apply_delivery_price;
    $resulte_data["delivery"]["claim_delivery_price"] = $total_claim_delivery_price;
    $resulte_data["delivery"]["change_delivery_price"] = $total_change_delivery_price;

    /*
		+ 환불(받아야 하는돈), - 추가결제(돌려줘야 하는돈)

		//처리해야함! 데이터 내려주고 claim_apply.php 수정하기!! 수정후 주석 삭제!
		$resulte_data["price"]															//총 환불금액
		$resulte_data["tax_price"]														//총 환불중 과세금액
		$resulte_data["tax_free_price"]												//총 환불중 비과세금액

		$resulte_data["product"]														//상품
		$resulte_data["product"]["product_price"]								//할인전 총 상품가격
		$resulte_data["product"]["product_dc_price"]							//할인차감된 총 상품가격(쿠폰포함)
		$resulte_data["product"]["dc_price"]									//총할인금액(쿠폰변동금액제외)
		$resulte_data["product"]["dc_price_coment"]								//총할인금액내역(쿠폰변동금액제외)
		$resulte_data["product"]["change_coupon_dcprice"]					//총변동쿠폰할인금액
		$resulte_data["product"]["change_coupon_coment"]					//총변동쿠폰할인내역
		$resulte_data["product"]["tax_price"]										//할인차감된 총 과세금액
		$resulte_data["product"]["tax_free_price"]								//할인차감된 총 비과세금액

		//환불쪽에서만 사용
		$resulte_data["product"]["주문상세번호"]["change_coupon_dcprice"]		//변동쿠폰할인금액
		$resulte_data["product"]["주문상세번호"]["change_coupon_coment"]		//변동쿠폰할인내역

		$resulte_data["delivery"]														//배송비
		$resulte_data["delivery"]["delivery_price"]									//할인전 총 배송비
		$resulte_data["delivery"]["delivery_dc_price"]							//할인차감된 총 배송비
		$resulte_data["delivery"]["claim_delivery_price"]							//총반품(교환)배송비
		$resulte_data["delivery"]["claim_coment"]									//총반품(교환)배송비내역

		//환불쪽에서만 사용
		$resulte_data["delivery"]["업체코드"]["delivery_price"]					//환불해줘야 하는 배송비
		$resulte_data["delivery"]["업체코드"]["claim_delivery_price"]		//변동배송비
		$resulte_data["delivery"]["업체코드"]["claim_coment"]				//변동배송비내역
	*/
    return $resulte_data;
}

//쿠폰 돌려주는 체크함수 및 할인 정보 리턴!
function orderUseCouponReturnCheck($data,$cnt)
{
    global $_DISCOUNT_TYPE;
    global $slave_mdb;
    //$slave_mdb = new Database;
    //$data["oid"];
    //$data["od_ix"];

    $where="";
    if($data["oid"]!=""){
        $where.=" and odd.oid = '".$data["oid"]."' ";
    }

    if($data["od_ix"]!=""){
        $where.=" and odd.od_ix = '".$data["od_ix"]."' ";
    }

    $sql = "SELECT odd.*,cr.regist_ix FROM shop_order_detail_discount odd left join shop_cupon_regist cr on (odd.dc_ix=cr.regist_ix) WHERE odd.dc_type in ('CP','SCP') ".$where."";
    $slave_mdb->query($sql);
    $discount=$slave_mdb->fetchall("object");

    $return["coupon_str"] = "";
    $return["coupon_height"]=0;
    $return["coupon_width"]=300;
    $return["coupon_total_dc_price"] = 0;
    $return["coupon_dc_info"] = array();

    for($i=0,$j;$i<count($discount);$i++){

        $sql="select c.cupon_ix, c.cupon_sale_type, c.cupon_sale_value, c.haddoffice_rate, c.seller_rate, cp.cupon_no, cp.publish_name, cp.publish_condition_price, cp.publish_limit_price, cr.use_sdate, cr.use_date_limit
		from shop_cupon_regist cr, shop_cupon_publish cp, shop_cupon c
		where cr.publish_ix=cp.publish_ix and cp.cupon_ix=c.cupon_ix and cr.regist_ix='".$discount[$i][regist_ix]."' ";
        $slave_mdb->query($sql);
        $cupon = $slave_mdb->fetch();

        $sql="select * from shop_order_detail where od_ix ='".$discount[$i][od_ix]."' ";
        $slave_mdb->query($sql);
        $order_detail = $slave_mdb->fetch();

        if($cupon["publish_condition_price"] <= (($order_detail["dcprice"]+$order_detail["option_price"])*$cnt)){ //쿠폰 사용조건 체크! (쿠폰할인금액을 뺀 금액으로 처리)

            //할인타입(MC:복수구매,MG:그룹,C:카테고리,GP:기획,SP:특별,CP:쿠폰,SCP:중복쿠폰,M:모바일,E:에누리,DCP:배송쿠폰,DE:배송비에누리)
            $tmp_info["discount_type"]=$discount[$i][dc_type];
            $tmp_info["discount_value_type"]="2";//할인타입//1:% 2:원 쿠폰은 앞에서 계산되어 넘어오기때문에 할인타입은 무조껀 2로!
            $tmp_info["discount_code"]=$discount[$i][regist_ix];//관련 키값 cupon_regist_ix
            $tmp_info["discount_msg"]=$_DISCOUNT_TYPE[$tmp_info["discount_type"]]."번호 : ".$cupon["cupon_no"]."|".$_DISCOUNT_TYPE[$tmp_info["discount_type"]]."발행명 : ".$cupon["publish_name"]."|".$_DISCOUNT_TYPE[$tmp_info["discount_type"]]."사용기간 : ".$cupon["use_sdate"]."~".$cupon["use_date_limit"];//할인 기타정보

            /*
			discount_value_type 1:% 일때
			discount_value:10 이면 headoffice_discount_value:6, seller_discount_value:4 으로 들어감
			discount_value_type 2:원 일때
			discount_value:2000 이면 headoffice_discount_value:1500, seller_discount_value:500 으로 들어감
			*/

            $saleprice = ($cupon[cupon_sale_type] == 1 ? intval($cupon[cupon_sale_value]/100*(($order_detail["dcprice"]+$order_detail["option_price"])*$cnt)):$cupon[cupon_sale_value]);

            if($saleprice > $cupon[publish_limit_price] && $cupon[publish_limit_price] > 0){
                $saleprice = $cupon[publish_limit_price];
            }

            $tmp_info["discount_value"]=$saleprice;//할인된비율 및 가격
            $tmp_info["discount_price"]=$tmp_info["discount_value"];//할인가격( *수량)

            if($cupon["cupon_sale_type"]=="1"){;//할인타입//1:% 2:원 강재로 할인타입을 원으로 바꾸었기 떄문에 원으로 바꾸어서 처리해야함!
                if($cupon["haddoffice_rate"]){//본사부담비율및가격이 없으면 무조껀 100%본사부담으로!
                    //$tmp_info["headoffice_discount_value"]=round($tmp_info["discount_price"]*$cupon["haddoffice_rate"]/$cupon["cupon_sale_value"]);
                    $tmp_info["headoffice_discount_value"]=round($tmp_info["discount_price"]*$cupon["haddoffice_rate"]/$cupon["cupon_sale_value"]);
                }else{
                    $tmp_info["headoffice_discount_value"]=$tmp_info["discount_price"];
                }
                $tmp_info["seller_discount_value"]=$tmp_info["discount_price"]-$tmp_info["headoffice_discount_value"];//셀러부담비율 및 가격
            }else{
                $tmp_info["headoffice_discount_value"]=$cupon["haddoffice_rate"];//본사부담비율 및 가격
                $tmp_info["seller_discount_value"]=$cupon["seller_rate"];//셀러부담비율 및 가격
            }


            if($j!=0) $return["coupon_str"] .= "-----------------------------------------------<br/>";
            $return["coupon_str"] .= $_DISCOUNT_TYPE[$tmp_info["discount_type"]]." : ".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($tmp_info["discount_price"])."".$currency_display[$admin_config["currency_unit"]]["back"]."<br/>".str_replace("|","<br/>",$tmp_info["discount_msg"])."<br/>";
            $return["coupon_height"]+=70;
            $j++;


            $return["coupon_total_dc_price"] += $tmp_info["discount_price"];
            array_push($return["coupon_dc_info"],$tmp_info);
        }
    }

    return $return;
}

//쿠폰 돌려주는 함수!
function orderUseCouponReturn($data){
    global $slave_mdb, $master_db;
    //$slave_mdb = new Database;

    //$data["oid"];
    //$data["od_ix"];
    //$data["ode_ix"];

    $where="";
    if($data["oid"]!=""){
        $where.=" and odd.oid = '".$data["oid"]."' ";
    }

    if($data["od_ix"]!=""){
        $where.=" and odd.od_ix = '".$data["od_ix"]."' ";
    }

    if($data["ode_ix"]!=""){
        $where.=" and odd.ode_ix = '".$data["ode_ix"]."' ";
    }

    //할인타입(MC:복수구매,MG:그룹,C:카테고리,GP:기획,SP:특별,CP:쿠폰,SCP:중복쿠폰,M:모바일,E:에누리,DCP:배송쿠폰,DE:배송비에누리)

    $sql = "SELECT odd.*,cr.use_date_limit,cr.regist_ix FROM shop_order_detail_discount odd left join shop_cupon_regist cr on (odd.dc_ix=cr.regist_ix) WHERE odd.dc_type in ('CP','SCP','DCP') ".$where."";
    $slave_mdb->query($sql);
    $discount=$slave_mdb->fetchall("object");

    //주문 총수와 취소완료 총수가 같아야 함
    for ($i = 0; $i < count($discount); $i++) {
        $sql = "SELECT 
         (SELECT COUNT(*) cnt FROM shop_order_detail od, shop_order_detail_discount odd WHERE od.oid=odd.oid AND od.od_ix=odd.od_ix AND odd.oid = '".$data["oid"]."' AND odd.dc_ix='".$discount[$i]["regist_ix"]."' AND od.gift_type = 'N'  ) AS tot_order
        ,(SELECT COUNT(*) cnt FROM shop_order_detail od, shop_order_detail_discount odd WHERE od.oid=odd.oid AND od.od_ix=odd.od_ix AND odd.oid = '".$data["oid"]."' AND odd.dc_ix='".$discount[$i]["regist_ix"]."' AND od.gift_type = 'N' AND od.STATUS IN ('IB', 'CC', 'EC', 'RC', 'SO') ) AS refund_order";
        $slave_mdb->query($sql);
        $slave_mdb->fetch();
        if($slave_mdb->dt['tot_order'] == $slave_mdb->dt['refund_order']) {
            $regist_ix[] = $discount[$i]["regist_ix"];
        }
    }

    if (count($regist_ix) > 0) {
        $sql = "update shop_cupon_regist set use_yn='0', use_oid='', use_pid='' where regist_ix in ('" . implode("','", $regist_ix) . "') ";
        $master_db->query($sql);
    }

    return true;
}
 

//2014-05-14 Hong 추가 주문분할 함수
function orderSeparate($od_ix,$separate_cnt,$copy_mode=false,$coupon_separate=false){
    global $slave_mdb, $master_db;
    //$slave_mdb = new Database;
    //쿠폰은 무조껀 나누는걸로 처리
    $coupon_separate = true;
    $sql="select * from shop_order_detail where od_ix='".$od_ix."' ";
    $slave_mdb->query($sql);
    $slave_mdb->fetch();
    $oid = $slave_mdb->dt["oid"];
    $origin_cnt = $slave_mdb->dt["pcnt"];
    $status = $slave_mdb->dt["status"];
    $pid = $slave_mdb->dt["pid"];

    if($origin_cnt > $separate_cnt || $copy_mode){//주문분할시 pcnt 가 적을때만,copy모드일때

        $change_cnt = $origin_cnt-$separate_cnt;

        $sql="desc shop_order_detail";
        $slave_mdb->query($sql);
        $od_colum = $slave_mdb->fetchall("object");

        $colum_str="";
        $colum_val_str = "";
        foreach($od_colum as $colum){// 주문컬럼 추가 및 삭제시
            $colum_str .= ",".$colum["Field"];
            if($colum["Extra"]=="auto_increment"){
                $colum_val_str .= ",''";
            }else{
                $colum_val_str .= ",".$colum["Field"];
            }
        }

        //shop_order_detail 생성
        $sql="INSERT INTO shop_order_detail (".substr($colum_str,1).") SELECT ".substr($colum_val_str,1)." FROM shop_order_detail where od_ix='".$od_ix."' ";
        $master_db->query($sql);
        $master_db->query("SELECT od_ix FROM " . TBL_SHOP_ORDER_DETAIL . " WHERE od_ix=LAST_INSERT_ID()");
        $master_db->fetch();
        $new_od_ix = $master_db->dt[od_ix];
        //$new_od_ix = $master_db->insert_id();

        //dc_type 할인타입(MC:복수구매,MG:그룹,C:카테고리,GP:기획,SP:특별,CP:쿠폰,SCP:중복쿠폰,M:모바일,E:에누리,DCP:배송쿠폰,DE:배송비에누리)
        $sql="INSERT INTO shop_order_detail_discount
		SELECT oid,'".$new_od_ix."',ode_ix,dc_type,dc_title,dc_rate,dc_price,dc_rate_admin,dc_price_admin,dc_rate_seller,dc_price_seller,dc_criterion,dc_msg,dc_ix,NOW()
		FROM shop_order_detail_discount WHERE od_ix = '".$od_ix."' and dc_type not in ('DCP'".($coupon_separate ? "" : ",'CP','SCP'" ).") ";
        $master_db->query($sql);

        if(!$copy_mode){

            //NEW discount 가격 UPDATE
            $sql="UPDATE shop_order_detail_discount SET
						dc_price = (round((dc_price_admin / '".$origin_cnt."')*'".$separate_cnt."') + round((dc_price_seller / '".$origin_cnt."')*'".$separate_cnt."')),
						dc_price_admin = round((dc_price_admin / '".$origin_cnt."')*'".$separate_cnt."'),
						dc_price_seller = round((dc_price_seller / '".$origin_cnt."')*'".$separate_cnt."')
					where od_ix='".$new_od_ix."' and dc_type not in ('DCP') ";
            $master_db->query($sql);

            //기존 discount 가격 UPDATE (반올림 이슈로 NEW 금액에서 차감!)
            $sql="UPDATE
					shop_order_detail_discount d
				INNER JOIN
					shop_order_detail_discount d2
				ON
					(d.od_ix='".$od_ix."' and d2.od_ix='".$new_od_ix."' and d.dc_type=d2.dc_type)
				SET
					d.dc_price = d.dc_price - d2.dc_price,
					d.dc_price_admin = d.dc_price_admin - d2.dc_price_admin,
					d.dc_price_seller = d.dc_price_seller - d2.dc_price_seller
				WHERE
					d.od_ix='".$od_ix."'";
            $master_db->query($sql);

            //shop_order_detail pcnt , ptprice , pt_dcprice 업데이트처리하기!
            $sql="UPDATE shop_order_detail SET
						pcnt='".$change_cnt."',
						ptprice=((psprice+option_price)*".$change_cnt."),
						reserve = round((reserve / '".$origin_cnt."')*'".$change_cnt."'),
						pt_dcprice=(
										((psprice+option_price)*".$change_cnt.")-ifnull((select sum(dc_price) as sum_dc_price from shop_order_detail_discount where od_ix='".$od_ix."' ),0)
									)
					where od_ix='".$od_ix."' ";
            $master_db->query($sql);

            $sql="UPDATE shop_order_detail SET
						pcnt='".$separate_cnt."',
						ptprice=((psprice+option_price)*".$separate_cnt."),
						reserve = (reserve-round((reserve / '".$origin_cnt."')*'".$change_cnt."')),
						pt_dcprice=(
										((psprice+option_price)*".$separate_cnt.")-ifnull((select sum(dc_price) as sum_dc_price from shop_order_detail_discount where od_ix='".$new_od_ix."' ),0)
									)
					where od_ix='".$new_od_ix."' ";
            $master_db->query($sql);

            set_order_status($oid,$status,"주문분할[수량:".$origin_cnt."->".$change_cnt."(".$separate_cnt.")]",$_SESSION["admininfo"]["charger"]."(".$_SESSION["admininfo"]["charger_id"].")",$_SESSION["admininfo"]["company_id"],$od_ix,$pid);
        }
    }

    return $new_od_ix;
}

//배송비 관련 함수 추가 시작 2014-05-14 이학봉
function Product_delivery_template($pid,$mem_type){
    global $slave_mdb;
    global $_LANGUAGE;
    //$slave_mdb = new Database;

    if($pid == ""){
        return false;
    }

    $is_wholesale = UserSellingType();

//cd3b8b8689a547e2dd3dd2369fb2c3f2 - 택배
//getLanguageText('a63b816a46aab980df92ac700f50fe09') - 화물
//getLanguageText('a962bc52be39c68c97835bbd50d054d0'); - 직배송
//getLanguageText('465533885476c097d86a2e6848aed65e') - 선불
//getLanguageText('ceb7bd7b3914525d5264d773df220b81') - 착불

    $sql = "select
				pd.*,
				case when pd.delivery_div = '1' then
					'".getLanguageText('cd3b8b8689a547e2dd3dd2369fb2c3f2')."'
					when pd.delivery_div = '2' then
					'".getLanguageText('a63b816a46aab980df92ac700f50fe09')."'
					when pd.delivery_div = '3' then
					'".getLanguageText('a962bc52be39c68c97835bbd50d054d0')."'
				else '".getLanguageText('cd3b8b8689a547e2dd3dd2369fb2c3f2')."' end as delivery_div_name,

				case when dt.delivery_basic_policy = '1' then
					'(".getLanguageText('465533885476c097d86a2e6848aed65e').")'
					when dt.delivery_basic_policy = '2' then
						if(dt.delivery_pay_metho_text,dt.delivery_pay_metho_text,'(".getLanguageText('ceb7bd7b3914525d5264d773df220b81').")')
					when dt.delivery_basic_policy = '5' then
					''
				else '".getLanguageText('cd3b8b8689a547e2dd3dd2369fb2c3f2')."' end as delivery_payment_type

			from
				shop_product_delivery as pd
				inner join shop_delivery_template as dt on (pd.dt_ix = dt.dt_ix)
			where
				pid = '".$pid."' order by delivery_div ASC";

    ///and is_wholesale = '".$is_wholesale."'
    $slave_mdb->query($sql);
    $data_array = $slave_mdb->fetchall();

    return $data_array;

}

function delivery_policy_template($pid,$dt_ix='', $type = 'goods', $delivery_pay_method = ''){
    global $slave_mdb;
    global $_SESSION;
    global $_LANGUAGE;
    //$slave_mdb = new Database;

    /*
	if($_SESSION['user']['mem_type'] == 'C'){
		$is_wholesale = 'W';
	}else{
		$is_wholesale = UserSellingType();
	}*/
    $is_wholesale = UserSellingType();

    if($dt_ix != ""){	//템플릿 키가 잇을경우는 해당 템플릿만 조회
        $where = " and pd.dt_ix = '".$dt_ix."' ";
        $i ='0';
    }else{	//템플릿 키가 없을경우 첫번째 정책으로 조회
        $i = '0';
    }

    $sql = "select
			*
			from
				shop_product_delivery as pd
				left join shop_delivery_template dt on (pd.dt_ix = dt.dt_ix)
			where
				pd.pid = '".$pid."'
				and pd.is_wholesale = '".$is_wholesale."'
				$where
				order by pd.delivery_div ASC";
    $slave_mdb->query($sql);
    $template_array = $slave_mdb->fetchall();

    if($template_array[$i][delivery_package] == 'N'){
        $use_bundle_text = '';  // 묶음배송
    }else{
        $use_bundle_text = '';  // 개밸배송
    }

    $is_free_use = true;	// true : 무료 false : 유료

//0964ddcb5d2136a5ab0db41cd9eeb5ae 상품 수령 후 지불
//f71d58aa52a5d67dd7b7d76c26156d34 상품 상세페이지 내 착불 배송료 확인
//66188dd463f53442fa3a6332c0277c84 무료배송
//241118577eeec3f60a8158fa924a70a4 고정배송비
//3a11804b4283726d931e4153dd83eff4 배송비
//34b1f6ab71e0b836f40dfcb4452d58a3 무료배송조건
//641e9d61dbce9eb703e6ba32f02da270 이상
//ec4f7e06c4353f784bda98770e0fe5ba 기본배송비
//67a0ee7e438d0af2d3de744e69554671 출고지별 배송비
//305986f05ffeae98a32e85eb8ab608f3 상품 1개단위 배송비
//465533885476c097d86a2e6848aed65e 선불
//ceb7bd7b3914525d5264d773df220b81 착불
//c0bd2ac796192bcceb928d41337dabe2 지역별 추가 배송비가 부과될수 있음
//3a3c2da1f7ed5f00bd1c3ff1e5fe81c0 배송비 절감
//965ee301ddbfbbb3244e290bbd0d6cd0 묶음배송 상품보기
//61301963a71ed9516fc3d2e20bb5c99f 개별배송
//e0fe446c06e6a622eddbbaa2e39fe20f 방문수령

    switch($template_array[$i][delivery_policy]){
        case '1':
            if($template_array[$i][delivery_basic_policy] == '2'){
                $template_text = "(".getLanguageText('0964ddcb5d2136a5ab0db41cd9eeb5ae').") / ".getLanguageText('f71d58aa52a5d67dd7b7d76c26156d34')."";
            }else{
                $template_text = $use_bundle_text." ".getLanguageText('66188dd463f53442fa3a6332c0277c84')." ";
                $is_free_use = false;
            }
            break;
        case '2':
            $template_text = $use_bundle_text." ".getLanguageText('241118577eeec3f60a8158fa924a70a4')." ? ".$_SESSION["layout_config"]["currency_unit_front"].getExchangeNationPrice($template_array[$i][delivery_price], true)." ";
            break;
        case '3':
            $sql = "select * from shop_delivery_terms where dt_ix = '".$template_array[$i][dt_ix]."' and delivery_policy_type = '3' order by seq ASC limit 0,1";
            $slave_mdb->query($sql);
            $slave_mdb->fetch();
            $template_text = $use_bundle_text."".getLanguageText('3a11804b4283726d931e4153dd83eff4')." : ? ".$_SESSION["layout_config"]["currency_unit_front"].getExchangeNationPrice($slave_mdb->dt[delivery_price], true)." / ".getLanguageText('34b1f6ab71e0b836f40dfcb4452d58a3')." ? (".$_SESSION["layout_config"]["currency_unit_front"].getExchangeNationPrice($slave_mdb->dt[delivery_basic_terms],true)." ".getLanguageText('641e9d61dbce9eb703e6ba32f02da270').") 미만";

            //ALAND 용으로 변경 JK160722
            //$sql = "select * from shop_delivery_terms where dt_ix = '".$template_array[$i][dt_ix]."' and delivery_policy_type = '3' order by seq ASC limit 0,1";
            //$slave_mdb->query($sql);
            //$slave_mdb->fetch();
            //$template_text = $_SESSION["layout_config"]["currency_unit_front"].getExchangeNationPrice($slave_mdb->dt[delivery_price], true,true)." ("$_SESSION["layout_config"]["currency_unit_front"].format_korean(getExchangeNationPrice($slave_mdb->dt[delivery_basic_terms]))." 이상 구매 시 무료) ";

            break;
        case '4':
            $sql = "select * from shop_delivery_terms where dt_ix = '".$template_array[$i][dt_ix]."' and delivery_policy_type = '4' order by seq ASC limit 0,1";
            $slave_mdb->query($sql);
            $slave_mdb->fetch();
            $template_text = $use_bundle_text." ".getLanguageText('ec4f7e06c4353f784bda98770e0fe5ba')." : ? ".$_SESSION["layout_config"]["currency_unit_front"].getExchangeNationPrice($template_array[$i][delivery_cnt_price])."  ".getExchangeNationPrice($slave_mdb->dt[delivery_price], true)."(".getLanguageText('641e9d61dbce9eb703e6ba32f02da270').") : ? ".$_SESSION["layout_config"]["currency_unit_front"].getExchangeNationPrice($slave_mdb->dt[delivery_basic_terms],true)." ";
            break;
        case '5':
            $sql = "select * from shop_delivery_address where addr_ix = '".$template_array[$i][factory_info_addr_ix]."'";
            $slave_mdb->query($sql);
            $slave_mdb->fetch();

            $template_text = $use_bundle_text." ".getLanguageText('67a0ee7e438d0af2d3de744e69554671')."( ".$slave_mdb->dt[addr_name]." ) ";
            break;
        case '6':
            $template_text = $use_bundle_text." ".getLanguageText('305986f05ffeae98a32e85eb8ab608f3')." ? ".$_SESSION["layout_config"]["currency_unit_front"].getExchangeNationPrice($template_array[$i][delivery_unit_price])." ";
            break;
    }

    if($template_array[$i][delivery_basic_policy] == '5'){
        $method = '<select name=delivery_pay_method id=delivery_basic_policy_1 class=option_selectbox_1><option value=1>'.getLanguageText('465533885476c097d86a2e6848aed65e').'</option><option value=2>'.($template_array[$i][delivery_pay_metho_text]?$template_array[$i][delivery_pay_metho_text]:getLanguageText('ceb7bd7b3914525d5264d773df220b81')).'</option></select>';
    }else if($template_array[$i][delivery_basic_policy] == '1'){
        $method = '<select name=delivery_pay_method id=delivery_basic_policy_1 style=display:none;><option value=1 selected>'.getLanguageText('465533885476c097d86a2e6848aed65e').'</option></select>';
    }else if($template_array[$i][delivery_basic_policy] == '2'){
        $method = '<select name=delivery_pay_method id=delivery_basic_policy_1 style=display:none;><option value=2 selected>'.($template_array[$i][delivery_pay_metho_text]?$template_array[$i][delivery_pay_metho_text]:getLanguageText('ceb7bd7b3914525d5264d773df220b81')).'</option></select>';
    }

    echo "
    <script type='text/javascript'>
    var method = '".$method."';
    $('#delivery_payment_type".($type == 'estimate'?"_".$pid:'')."').html(method);
    //$('select[class=option_selectbox_1]').selectbox();
    </script>";

    if($template_array[$i][delivery_policy] == 3 ){
        $contents .= "".$template_text."";
    }else if($type != 'estimate'){
        $contents .= "
			<div style='line-height:150%;'>
				<span class='color_7b'>".$template_text."</span>
			</div>";
    }

    if($template_array[$i][delivery_package] == "N"){//배송방법 선택 (묶음배송일 경우) 묶음배송 상품보기 버튼 노출이고 무료배송이 아닐경우 노출

        //모바일 노출안함으로 변경 2014.7.27 bgh
        if($is_free_use == true && is_mobile() == false && $type != 'estimate'){//묶음배송이고 무료일경우 노출안함

            //".getLanguageText('3a3c2da1f7ed5f00bd1c3ff1e5fe81c0')." 배.송.비.절.감.
            $contents .= "
			<div style='padding:5px 0;display:none;'>				
				<a href=\"javascript:PopUpWindow('/shop/bundle_goods_pop.php?view_pid=".$pid."&dt_ix=".$dt_ix."','750','800','','yes');\" style='display:NONE;'>
				".getLanguageText('965ee301ddbfbbb3244e290bbd0d6cd0')."
				</a>
			</div>";
        }
    }else{
        $contents .= "
			<div style='padding:5px 0;display:none;'>
				<img src='".$_SESSION[layout_config][mall_templet_webpath]."/images/common/delivery_policy_1.gif' alt='".getLanguageText('61301963a71ed9516fc3d2e20bb5c99f')."' title='".getLanguageText('61301963a71ed9516fc3d2e20bb5c99f')."' align='absmiddle' />
			</div>";
    }

    if($template_array[$i][use_delivery_div_self] == "1"){
        $contents .= "
			<div class='delivery_method floatBox'>
				<ul>
					<li>
						<input type='checkbox' name='delivery_method_self' id='delivery_method_self' value='1'/> <label for='delivery_method_self'>".getLanguageText('e0fe446c06e6a622eddbbaa2e39fe20f')."</label>
						<img src='".$_SESSION[layout_config][mall_templet_webpath]."/images/korea/btns/adress_benefit.gif' alt='주소/혜택' title='' align='absmiddle' />
					</li>
				</ul>
			</div>";
    }else{
        $contents .= "<input type='hidden' name='delivery_method_self' id='delivery_method_self' value='0'/>";
    }

    return $contents;

}

function delivery_template_info($dt_ix){	//배송정책 템플릿 정보 불러오기 공용함수 2014-05-19 이학봉
    global $slave_mdb;
    //$slave_mdb=new Database;

    if($dt_ix == ""){
        return false;
    }

    if($delivery_policy != ""){
        $where = " and delivery_policy = '".$delivery_policy."'";
    }

    $sql = "select * from shop_delivery_template where dt_ix = '".$dt_ix."'";
    $slave_mdb->query($sql);
    $data_array = $slave_mdb->fetch();

    return $data_array;

}

function get_set_delivery_package_row($pid,$delivery_package){

    global $user, $shop_product_type, $sns_product_type ;
    global $slave_mdb;

    //$slave_mdb = $slave_mdb

    $admin_delievery_policy = getTopDeliveryPolicy($slave_mdb);//$slave_mdb->dt;

    if(TBL_SNS_CART=="shop_cart") {
        $in_product_type=$sns_product_type;
    } else {
        $in_product_type=$shop_product_type;
    }
    if($set_group!="") $add_set_group=" AND c.set_group='".$set_group."' ";
    if($user[code] != ""){
        $where = " mem_ix = '".$user[code]."' and c.product_type in (".implode(' , ',$in_product_type).") $add_set_group ";
        //$groupby = " group by mem_ix";
        $set_group_where="mem_ix = c.mem_ix";//비회원에 대한 조건이 없어서 회원/비회원 구분 지어서 조건 검 kbk 13/08/30
    }else{
        $where = " cart_key = '".session_id()."' and c.product_type in (".implode(' , ',$in_product_type).") $add_set_group ";
        //$groupby = " group by cart_key";
        $set_group_where="cart_key = '".session_id()."'";//비회원에 대한 조건이 없어서 회원/비회원 구분 지어서 조건 검 kbk 13/08/30
    }

    $sql = "select
				count(*) as count
			from
				shop_cart as c
			where
				$where
				and c.delivery_package = '".$delivery_package."'
				and c.id = '".$pid."'
			";
    //echo nl2br($sql)."<br>";
    $slave_mdb->query($sql);
    $slave_mdb->fetch();

    return $slave_mdb->dt[count];

}

////////////////////////////////////////////////////////


function allow_max_ordercnt($pid){	//한정수량 구매가능 수량 가져오기 함수 2014-05-15 이학봉
    global $slave_mdb;
    if(!$pid){
        return false;
    }
    //$slave_mdb=$slave_mdb

    /*
	$sql = "select
				(p.allow_order_cnt_byonesell - ifnull(sum(od.pcnt),0)) as total_cnt
			from
				shop_product as p
				inner join shop_order_detail as od on (p.id = od.pid)
			where
				p.id = '".$pid."'
				and od.status in ('IR','IC','DR','CA','DD','DI','DC','BF','EA','EY','EI','ED','ET','EF','EM','EC','RA','RY','RI','RD','RT','RF','RM')";
	*/

    $sql = "select
				(p.allow_order_cnt_byonesell - ifnull( (select sum(od.pcnt) from shop_order_detail od  where od.pid='".$pid."' and od.status in ('IR','IC','DR','CA','DD','DI','DC','BF','EA','EY','EI','ED','ET','EF','EM','EC','RA','RY','RI','RD','RT','RF','RM') ) ,0)) as total_cnt
			from
				shop_product as p
			where
				p.id = '".$pid."' ";

    $slave_mdb->query($sql);
    $slave_mdb->fetch();

    return $slave_mdb->dt[total_cnt];
}

function allow_ordercnt($pid){	//한정수량 구매가능 수량 가져오기 함수 2014-04-13 이학봉
    global $slave_mdb;
    if(!$pid){
        return false;
    }
//	$slave_mdb=$slave_mdb
    /*
	$sql = "select
				ifnull(sum(od.pcnt),0) as total_cnt
			from
				shop_product as p
				inner join shop_order_detail as od on (p.id = od.pid)
			where
				p.id = '".$pid."'
				and od.status in ('IR','IC','DR','CA','DD','DI','DC','BF','EA','EY','EI','ED','ET','EF','EM','EC','RA','RY','RI','RD','RT','RF','RM')";
	*/
    $sql = "select
				ifnull(sum(od.pcnt),0) as total_cnt
			from
				shop_order_detail as od
			where
				od.pid = '".$pid."'
				and od.status in ('IR','IC','DR','CA','DD','DI','DC','BF','EA','EY','EI','ED','ET','EF','EM','EC','RA','RY','RI','RD','RT','RF','RM')";

    $slave_mdb->query($sql);
    $slave_mdb->fetch();

    return $slave_mdb->dt[total_cnt];
}

function check_allow_sell($pid){	//해당상품 한정수량 구매가능 체크 2014-04-29 이학봉
    global $slave_mdb;
    //$slave_mdb=$slave_mdb

    //한정구매수량 체크 시작 2014-04-29
    $sell_count = allow_ordercnt($pid);

    $sql = "select * from shop_product where id = '".$pid."'";

    $slave_mdb->query($sql);
    $slave_mdb->fetch();

    if($slave_mdb->dt[allow_order_type] == '1'){
        if($sell_count >= $slave_mdb->dt[allow_order_cnt_byonesell]){

            return false;		//구매불가
        }else{

            return true;		//구매가능
        }
    }else{

        return true;			//구매가능
    }
    //한정구매수량 체크 끝 2014-04-29
}

function get_member_rate($pid){		//회원그룹별 할인비율 구하기 2014-04-22 이학봉
    global $slave_mdb;
    global $_SESSION;

    //$slave_mdb=$slave_mdb

    $user_info = $_SESSION[user];

    if($user_info[use_discount_type] == 'g'){	//일반할인 (그룹할인)

        return $user_info[sale_rate];		//기존 할인비율 반환(로그인시 세션생성하면서 일반화원,사업자회원 소매/도매 비율 게산되어서 생성되었음)

    }else if($user_info[use_discount_type] == 'c'){	//카테고리할인

        if($pid){	//카테고리가 잇을경우 실행

            $sql = "select
						cd.*,
						c.cid,
						c.depth
					from
						shop_product as p
						inner join shop_product_relation as pr on (p.id = pr.pid and basic = '1')
						inner join shop_category_info as c on (pr.cid = c.cid)
						left join shop_category_discount as cd on (c.cid = cd.cid)
					where
						p.id = '".$pid."'";

            $slave_mdb->query($sql);
            $discount_info =$slave_mdb->fetch();

            if($discount_info[is_use] == '3'){	//개별
                $cid = $discount_info[cid];
            }else{	//상위
                //$cid = substr($discount_info[cid],0,3)."000000000000";
                for($i=$discount_info[depth]-1;$i>=0;$i--){

                    $cid = substr($discount_info[cid],0,($i=='0'?'3':($i+1)*3));
                    switch(strlen($cid)){
                        case '3':
                            $ori_cid = $cid."000000000000";
                            break;
                        case '6':
                            $ori_cid = $cid."000000000";
                            break;
                        case '9':
                            $ori_cid = $cid."000000";
                            break;
                        case '12':
                            $ori_cid = $cid."000";
                            break;
                        case '15':
                            $ori_cid = $cid;
                            break;
                    }

                    $sql = "select
								*
							from
								shop_category_discount
							where
								gp_ix = '".$user_info[gp_ix]."'
								and cid = '".$ori_cid."'";
                    $slave_mdb->query($sql);
                    $slave_mdb->fetch();

                    if($slave_mdb->dt[is_use] == '3'){		//개별로 지정되엇을경우 현재 분류코드로 지정
                        $cid = $ori_cid;
                        break;
                    }else{	//상위		-> 상위로 지정되엇을경우 상위카테고리로 넘겨줌
                        continue;
                    }
                }
            }

            $sql = "select
					*
				from
					shop_category_discount
				where
					gp_ix = '".$user_info[gp_ix]."'
					and cid = '".$cid."'";
            $slave_mdb->query($sql);
            $slave_mdb->fetch();

            if($slave_mdb->total > 0){
                if(UserSellingType() == 'W'){
                    return $slave_mdb->dt[wholesale_dc_rate];
                }else{
                    return $slave_mdb->dt[dc_rate];
                }
            }else{
                return '0';
            }

        }

    }else if($user_info[use_discount_type] == 'w'){	//품목가격별 .. 품목 가격별은 별도 함수 생성 예정
        return '0';									//품목가격별 .. 품목 가격별은 별도 함수 생성 예정
    }else{
        return '0';	//비회원일경우
    }
}

function get_delivery_row($oid='',$ode_ix='',$type =''){
//od.delivery_type,od.delivery_policy,od.delivery_package,od.delivery_method,od.delivery_pay_method,od.ori_company_id,od.delivery_addr_use,od.factory_info_addr_ix,
    global $shop_product_type;
    global $slave_mdb;
    //$slave_mdb = $slave_mdb

    if(empty($ode_ix)){
        return '1';
    }

    if($type == 'index'){	//마이페이지 index.php 용
        $typeWhere = " and product_type in (".implode(' , ',$shop_product_type).")";
        $returnStr = "'".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."','".ORDER_STATUS_RETURN_ING."','".ORDER_STATUS_EXCHANGE_APPLY."','".ORDER_STATUS_EXCHANGE_COMPLETE."','".ORDER_STATUS_EXCHANGE_DELIVERY."','".ORDER_STATUS_RETURN_DELIVERY."','".ORDER_STATUS_EXCHANGE_ING."'";

        $typeWhere .= " and status not in (".$returnStr.")";
    }

    $sql = "select
				*
			from
				shop_order_detail
			where
				oid = '".$oid."'
				and ode_ix = '".$ode_ix."'
				$typeWhere";
    $slave_mdb->query($sql);
    $slave_mdb->fetch();

    return $slave_mdb->total;
}


function CategorySellerCommission($pid){		//카테고리별 셀러정산 수수료 2014-05-23 이학봉
    global $slave_mdb;
    global $_SESSION;

    //$slave_mdb=$slave_mdb

    $mem_type = $_SESSION[user][mem_type];

    if($pid){	//카테고리가 잇을경우 실행

        $sql = "select
					cd.*,
					c.cid,
					c.depth
				from
					shop_product as p
					inner join shop_product_relation as pr on (p.id = pr.pid and basic = '1')
					inner join shop_category_info as c on (pr.cid = c.cid)
					left join shop_category_commission as cd on (c.cid = cd.cid)
				where
					p.id = '".$pid."'";

        $slave_mdb->query($sql);
        $discount_info =$slave_mdb->fetch();

        if($discount_info[is_use] == '3'){	//개별
            $cid = $discount_info[cid];
        }else{	//상위
            //$cid = substr($discount_info[cid],0,3)."000000000000";
            for($i=$discount_info[depth]-1;$i>=0;$i--){

                $cid = substr($discount_info[cid],0,($i=='0'?'3':($i+1)*3));
                switch(strlen($cid)){
                    case '3':
                        $ori_cid = $cid."000000000000";
                        break;
                    case '6':
                        $ori_cid = $cid."000000000";
                        break;
                    case '9':
                        $ori_cid = $cid."000000";
                        break;
                    case '12':
                        $ori_cid = $cid."000";
                        break;
                    case '15':
                        $ori_cid = $cid;
                        break;
                }

                $sql = "select
							*
						from
							shop_category_commission
						where
							cid = '".$ori_cid."'";
                ////echo nl2br($sql)."<br><br>";
                $slave_mdb->query($sql);
                $slave_mdb->fetch();

                if($slave_mdb->dt[is_use] == '3'){		//개별로 지정되엇을경우 현재 분류코드로 지정
                    $cid = $ori_cid;
                    break;
                }else{	//상위		-> 상위로 지정되엇을경우 상위카테고리로 넘겨줌
                    continue;
                }
            }
        }

        $sql = "select
				*
			from
				shop_category_commission
			where
				cid = '".$cid."'";
        $slave_mdb->query($sql);
        $slave_mdb->fetch();

        if($slave_mdb->total > 0){
            if(UserSellingType() == 'W'){
                return $slave_mdb->dt[wholesale_commission];
            }else{
                return $slave_mdb->dt[commission];
            }
        }else{
            return '0';
        }

    }

}




function DiscountRult($goods_infos, $cid="", $depth="", $amount="1"){
    //global $script_times;
    global $slave_mdb;
    //$slave_mdb=new Database;
    if(!is_array($goods_infos)){
        //echo "할인상품 대상이 입력되어야 합니다. ";
        //exit;
        return;
    }
    //$slave_mdb->debug = true;
    //print_r($goods_infos);
    if(is_array($goods_infos)){
        foreach($goods_infos as $key => $value){
            $_array_pid[] = $key;
        }
    }else{
        $_array_pid[] = $goods_infos;
    }
    //print_r($_array_pid);
    //exit;
    if(!is_array($_array_pid)){
        return;
    }
    if($_SESSION[user] && $_SESSION["user"]["use_discount_type"] == "g"){

        // 회원그룹별 할인
        if($_SESSION[user][sale_rate] > 0){
            $sql = "select 'MG' as discount_type, pr.cid, p.id as pid , p.sellprice , 0 as commision,
						'".$_SESSION[user][sale_rate]."' as headoffice_rate , 0 as seller_rate, '".$_SESSION[user][sale_rate]."' as sale_rate
						from shop_product p , shop_product_relation pr
						where p.id = pr.pid and pr.basic = '1'
						and p.id in (".implode(' , ',$_array_pid).") ";

            //and pr.cid LIKE '".substr($value[cid],0,($value[depth]+1)*3)."%'
            $script_times["discount_group_start_".rand()] = time();
            $slave_mdb->query($sql);
            $_discount_info = $slave_mdb->fetchall();
            $script_times["discount_group_end_".rand()] = time();

            for($i=0 ; $i < count($_discount_info) ;$i++){
                //echo $_discount_info[$i][pid]."<br>";
                $pid = str_pad($_discount_info[$i][pid], 10, "0", STR_PAD_LEFT);
                //echo $pid;
                $discount_info[$pid][$_discount_info[$i][discount_type]]["pid"] = $pid;
                $discount_info[$pid][$_discount_info[$i][discount_type]]["discount_type"] = $_discount_info[$i][discount_type];
                //$discount_info[$pid][$_discount_info[$i][discount_type]]["sellprice"] = $_discount_info[$i][sellprice];
                //$discount_info[$pid][$_discount_info[$i][discount_type]]["dcprice"] = $_discount_info[$i][sellprice]*((100-$_discount_info[$i][sale_rate])/100);
                $discount_info[$pid][$_discount_info[$i][discount_type]]["discount_value"] = $_discount_info[$i][sale_rate];
                $discount_info[$pid][$_discount_info[$i][discount_type]]["discount_value_type"] = "1";// 1:%, 2:원
                $discount_info[$pid][$_discount_info[$i][discount_type]]["round_position"] = $_SESSION["user"][round_depth];// 1, 2, 3
                $discount_info[$pid][$_discount_info[$i][discount_type]]["round_type"] = $_SESSION["user"][round_type];// 1: 반올림, 2:반내림, 3: 올림, 4: 내림

                $discount_info[$pid][$_discount_info[$i][discount_type]]["headoffice_discount_value"] = $_discount_info[$i][headoffice_rate];
                $discount_info[$pid][$_discount_info[$i][discount_type]]["seller_discount_value"] =  $_discount_info[$i][seller_rate];
                $discount_info[$pid][$_discount_info[$i][discount_type]]["discount_code"] = $_SESSION[user][gp_ix];

            }
        }
    }

    if($amount == "" || $amount == "NaN"){
        $amount = 1;
    }
    if(UserSellingType() == "W"){
        //복수할인
        $sql = "SELECT 'MC' as discount_type, p.id as pid , p.sellprice,  pmr.rate_price as sale_rate , rate_div as discount_value_type, pmr.mr_id, pmr.round_cnt, pmr.round_type
					FROM shop_product p , shop_product_mult_rate pmr
					where p.id = pmr.pid and is_wholesale = 'W' and p.id in (".implode(' , ',$_array_pid).") and pmr.sell_mult_cnt <= '".$amount."' ";
    }else{
        //복수할인
        $sql = "SELECT 'MC' as discount_type, p.id as pid , p.sellprice,  pmr.rate_price as sale_rate , rate_div as discount_value_type, pmr.mr_id, pmr.round_cnt, pmr.round_type
				FROM shop_product p , shop_product_mult_rate pmr
				where p.id = pmr.pid and is_wholesale = 'R' and p.id in (".implode(' , ',$_array_pid).") and pmr.sell_mult_cnt <= '".$amount."' ";
    }
    //	echo $sql;
    $script_times["discount_multibuying_sale_start_".rand()] = time();
    $slave_mdb->query($sql);
    $_discount_info = $slave_mdb->fetchall();
    $script_times["discount_multibuying_sale_start_".rand()] = time();

    if(is_array($_discount_info)){
        for($i=0 ; $i < count($_discount_info) ;$i++){
            //echo $_discount_info[$i][pid]."<br>";
            $pid = str_pad($_discount_info[$i][pid], 10, "0", STR_PAD_LEFT);
            //echo $pid;
            $discount_info[$pid][$_discount_info[$i][discount_type]]["pid"] = $pid;
            $discount_info[$pid][$_discount_info[$i][discount_type]]["discount_type"] = $_discount_info[$i][discount_type];
            //$discount_info[$pid][$_discount_info[$i][discount_type]]["sellprice"] = $_discount_info[$i][sellprice];
            //$discount_info[$pid][$_discount_info[$i][discount_type]]["dcprice"] = $_discount_info[$i][sellprice]*((100-$_discount_info[$i][sale_rate])/100);
            $discount_info[$pid][$_discount_info[$i][discount_type]]["discount_value"] = $_discount_info[$i][sale_rate];
            $discount_info[$pid][$_discount_info[$i][discount_type]]["discount_value_type"] = $_discount_info[$i][discount_value_type];// 1:%, 2:원
            $discount_info[$pid][$_discount_info[$i][discount_type]]["round_position"] = $_discount_info[$i][round_cnt];// 1, 2, 3
            $discount_info[$pid][$_discount_info[$i][discount_type]]["round_type"] = $_discount_info[$i][round_type];// 1: 반올림, 2:반내림, 3: 올림, 4: 내림

            $discount_info[$pid][$_discount_info[$i][discount_type]]["headoffice_discount_value"] = $_discount_info[$i][headoffice_rate];
            $discount_info[$pid][$_discount_info[$i][discount_type]]["seller_discount_value"] =  $_discount_info[$i][seller_rate];
            $discount_info[$pid][$_discount_info[$i][discount_type]]["discount_code"] = $_discount_info[$i][mr_id];
        }
    }

    if($_SESSION["user"]["use_discount_type"] == "c"){
        // 카테고리별 할인
        include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class");
        $shmop = new Shared("category_discount_info");
        $shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/_shared/";
        $shmop->SetFilePath();
        $category_discount_info = $shmop->getObjectForKey("category_discount_info");
        $category_discount_info = unserialize(urldecode($category_discount_info));

        //echo "<br><Br>";
        //echo substr($cid, 0, ($depth+1)*3);
        //$search_category_discount_info = getParentStackComplete(substr($cid, 0, ($depth+1)*3), $category_discount_info);
        if($cid && false){
            $search_category_discount_info = multidimensional_search($category_discount_info, array("cid"=>substr($cid, 0, ($depth+1)*3), "cid"=>substr($cid, 0, 3)) );
        }else{
            $search_category_discount_info = $category_discount_info[$_SESSION['user']['gp_ix']];
        }

        $cid = ""; //### 2016.03.22

        //print_r($search_category_discount_info);
        if(is_array($search_category_discount_info)){
            unset($sql);
            foreach($search_category_discount_info as $key => $value){

                if( ($cid && substr($value[cid], 0, ($depth+1)*3) == substr($cid, 0, ($depth+1)*3)) || !$cid){

                    if($sql != ""){
                        $sql .= "
										union
										";
                    }
                    //$exclude_cids = multidimensional_search($category_discount_info, array("cid"=>substr($value[cid], 0, ($value[depth]+1)*3)),"key");
                    //echo $value[cid]."::::".$value[depth]."<br><br>";
                    //print_r($exclude_cids);
                    //$filteredArr = array_diff($exclude_cids[cid],array($value[cid]));
                    //print_r($filteredArr);
                    if(UserSellingType() == "R"){
                        $view_reserve = $reserve_rate;
                    }else if(UserSellingType() == "W"){
                        $view_reserve = $whole_reserve_rate;
                    }else{
                        $view_reserve = $reserve_rate;
                    }
                    $sql .= "select 'C' as discount_type, pr.cid, p.id as pid , p.sellprice , 0 as commision,
									".(UserSellingType() == "W" ? $value[wholesale_dc_rate]:$value[dc_rate])." as headoffice_rate , 0 as seller_rate, ".(UserSellingType() == "W" ? $value[wholesale_dc_rate]:$value[dc_rate])." as sale_rate
									from shop_product p , shop_product_relation pr
									where p.id = pr.pid and pr.basic = '1' and pr.cid LIKE '".substr($value[cid],0,($value[depth]+1)*3)."%'
									and p.id in (".implode(' , ',$_array_pid).") ";

                    /*
						* 카테고리 제외
						* 1. 2014-07-12 shs 제외 카테고리 출처를 몰라 주석처리
						*/
                    /*
						if(is_array($filteredArr)){
							foreach($filteredArr as $key => $excude_cid){
								$sql .= "and pr.cid NOT LIKE  '".substr($excude_cid,0,($exclude_cids[depth][$key]+1)*3)."%' ";
							}
						}
						*/
                    //echo nl2br($sql)."<br><br><br>";
                    //exit;
                }
            }
            //	echo "<br><br>".nl2br($sql)."<br><br><br>";
            if($sql){
                $script_times["discount_category_start_".rand()] = time();
                //echo nl2br($sql);

                //$script_times["discount_category_start_".rand()] = $sql;
                $slave_mdb->query($sql);
                $_discount_info = $slave_mdb->fetchall();
                $script_times["discount_category_end_".rand()] = time();
                //echo ($script_times["discount_category_start_".rand()] - $script_times["discount_category_end_".rand()])."<br><br>";
            }
            for($i=0 ; $i < count($_discount_info) ;$i++){
                //echo $_discount_info[$i][pid]."<br>";
                if($_discount_info[$i][sale_rate] > 0){
                    $pid = str_pad($_discount_info[$i][pid], 10, "0", STR_PAD_LEFT);
                    //echo $pid;
                    $discount_info[$pid][$_discount_info[$i][discount_type]]["pid"] = $pid;
                    $discount_info[$pid][$_discount_info[$i][discount_type]]["discount_type"] = $_discount_info[$i][discount_type];
                    //$discount_info[$pid][$_discount_info[$i][discount_type]]["sellprice"] = $_discount_info[$i][sellprice];
                    //$discount_info[$pid][$_discount_info[$i][discount_type]]["dcprice"] = $_discount_info[$i][sellprice]*((100-$_discount_info[$i][sale_rate])/100);
                    $discount_info[$pid][$_discount_info[$i][discount_type]]["discount_value"] = $_discount_info[$i][sale_rate];
                    $discount_info[$pid][$_discount_info[$i][discount_type]]["discount_value_type"] = "1";//$_discount_info[$i][discount_sale_type];
                    $discount_info[$pid][$_discount_info[$i][discount_type]]["round_position"] = 1;// 1, 2, 3
                    $discount_info[$pid][$_discount_info[$i][discount_type]]["round_type"] = 1;// 1: 반올림, 2:반내림, 3: 올림, 4: 내림

                    $discount_info[$pid][$_discount_info[$i][discount_type]]["headoffice_discount_value"] = $_discount_info[$i][headoffice_rate];
                    $discount_info[$pid][$_discount_info[$i][discount_type]]["seller_discount_value"] =  $_discount_info[$i][seller_rate];
                    $discount_info[$pid][$_discount_info[$i][discount_type]]["discount_code"] = $_discount_info[$i][cid];
                }

            }
            //print_r($discount_info);
        }
    }
    //dc_type 할인타입(MC:복수구매,MG:그룹,C:카테고리,GP:기획,SP:특별,CP:쿠폰,SCP:중복쿠폰,M:모바일,E:에누리,DCP:배송쿠폰,DE:배송비에누리)
    // 기획할인 , 특가할인
    if(is_array($_array_pid)){
        $sql = "select d.dc_ix , dpg.dpg_ix
					from shop_discount d
					right join shop_discount_product_group dpg on d.dc_ix = dpg.dc_ix
					right join shop_discount_product_relation dpr on d.dc_ix = dpr.dc_ix and dpg.group_code = dpr.group_code
					where dpr.pid in (".implode(' , ',$_array_pid).") ";
        //echo nl2br($sql)."<br><br>";
        $slave_mdb->query($sql);
        $useable_discount_info = $slave_mdb->fetchall();
        unset($useable_dpg_ix);
        unset($useable_dc_ix);
        if(is_array($useable_discount_info)){
            foreach($useable_discount_info as $key => $value){
                if($value[dc_ix]){
                    $useable_dc_ix[] = $value[dc_ix];
                }
                if($value[dpg_ix]){
                    $useable_dpg_ix[] = $value[dpg_ix];
                }
            }
        }
    }

    $sql = "select d.dc_ix, d.discount_type, dpg.group_code, dpg.dpg_ix, dpg.commission, dpg.headoffice_rate, dpg.seller_rate, dpg.sale_rate, discount_sale_type, goods_display_type, display_auto_sub_type, round_type, ddr.r_ix as gp_ix, ddr2.r_ix
			from shop_discount d
			right join shop_discount_product_group dpg on d.dc_ix = dpg.dc_ix
			left join shop_discount_display_relation ddr on d.dc_ix = ddr.dc_ix and relation_type = 'G'
			left join shop_discount_display_relation ddr2 on d.dc_ix = ddr2.dc_ix and dpg.group_code = ddr2.group_code  and ddr2.relation_type = dpg.display_auto_sub_type
			where dpg.is_display = 'Y'
			and d.week_no_".date("N")." = '1' and d.is_use = '1'
			and ".time()." between discount_use_sdate and discount_use_edate
			and ((use_time = 1 and ".date("h")." between start_time and end_time and ".date("i")." between start_min and end_min) or use_time = 0 ) ";
    if($_SESSION["user"]["gp_ix"]){
        $sql .= "and (member_target = 'A' or (member_target = 'G' and ddr.r_ix = '".$_SESSION["user"]["gp_ix"]."' )) ";
    }else{
        $sql .= "and (member_target = 'A') ";
    }
    if(is_mobile()){
        $sql .= "and d.discount_type = 'M' ";
    }else{
        $sql .= "and d.discount_type != 'M' ";
    }
    if(is_array($useable_dc_ix)){
        //$sql .= "and d.dc_ix in (".implode(' , ',$useable_dc_ix).") ";
    }
    if(is_array($useable_dpg_ix)){
        $sql .= "and ((goods_display_type = 'M' and dpg.dpg_ix in (".implode(' , ',$useable_dpg_ix).")) or goods_display_type = 'A')  ";
    }else{
        $sql .= "and (goods_display_type = 'A')  ";
    }

    //echo nl2br($sql);
    $script_times["discount_plan_start_".rand()] = time();
    $slave_mdb->query($sql);
    $plan_discount_info = $slave_mdb->fetchall();
    $script_times["discount_plan_end_".rand()] = time();
    //echo "<b>".count($plan_discount_info)."</b>";
    //echo "<br><br><br><br><br>";

    if(is_array($plan_discount_info)){
        $sql = "";

        for($i=0;($i < count($plan_discount_info) && $i < 150 );$i++){
            if($plan_discount_info[$i][goods_display_type] == "M"){
                // 상품전시 타입이 수동일때
                $union_sql = "select '".$plan_discount_info[$i][discount_type]."' as discount_type, dpg.dpg_ix, dpg.dc_ix, dpg.group_name, p.id as pid , p.sellprice ,  dpg.commission, dpg.headoffice_rate, dpg.seller_rate, dpg.sale_rate, dpg.discount_sale_type, dpg.round_position, dpg.round_type, dpg.commission
						from shop_discount_product_group dpg , shop_discount_product_relation dpr ,   shop_product p
						where dpg.dc_ix = dpr.dc_ix and dpg.group_code = dpr.group_code and dpr.pid = p.id
						and dpg.dpg_ix = '".$plan_discount_info[$i][dpg_ix]."'
						and p.id in (".implode(' , ',$_array_pid).") ";
            }else if($plan_discount_info[$i][goods_display_type] == "A" && $plan_discount_info[$i][display_auto_sub_type] == "C"){
                // 상품전시 타입이 자동일때 자동타입이 카테고리 일때
                //$category_sql = "select cid, depth from shop_category_info ci , shop_discount_display_relation ddr where ci.cid = ddr.r_ix and ddr.relation_type = 'C' and dc_ix = '".$plan_discount_info[$i][dc_ix]."' and ddr.group_code = '".$plan_discount_info[$i][group_code]."' ";
                $category_sql = "select cid, depth from shop_category_info ci  where cid = '".$plan_discount_info[$i][r_ix]."' ";

                $slave_mdb->query($category_sql);
                $slave_mdb->fetch();
                $cid = $slave_mdb->dt[cid];
                $depth = $slave_mdb->dt[depth];
                //echo $category_sql;
                $union_sql = "select '".$plan_discount_info[$i][discount_type]."' as discount_type,dpg.dpg_ix, dpg.dc_ix, dpg.group_name, p.id as pid , p.sellprice ,  dpg.commission, dpg.headoffice_rate, dpg.seller_rate, dpg.sale_rate, dpg.discount_sale_type, dpg.round_position, dpg.round_type, dpg.commission
						from shop_discount_product_group dpg , shop_discount_display_relation ddr , shop_product_relation pr,   shop_product p
						where dpg.dc_ix = ddr.dc_ix and dpg.group_code = ddr.group_code
						and relation_type = 'C' and substr(ddr.r_ix,0,".(($depth+1)*3).") = substr(pr.cid,0,".(($depth+1)*3).") and pr.pid = p.id
						and dpg.dpg_ix = '".$plan_discount_info[$i][dpg_ix]."' and pr.cid LIKE '".(substr($cid,0,($depth+1)*3))."%'
						and p.id in (".implode(' , ',$_array_pid).") ";
            }else if($plan_discount_info[$i][goods_display_type] == "A" && $plan_discount_info[$i][display_auto_sub_type] == "S"){
                // 상품전시 타입이 자동일때 자동타입이 셀러 일때
                $union_sql = "select '".$plan_discount_info[$i][discount_type]."' as discount_type,dpg.dpg_ix, dpg.dc_ix, dpg.group_name, p.id as pid , p.sellprice ,  dpg.commission, dpg.headoffice_rate, dpg.seller_rate, dpg.sale_rate, dpg.discount_sale_type, dpg.round_position, dpg.round_type, dpg.commission
						from shop_discount_product_group dpg , shop_discount_display_relation ddr ,   shop_product p
						where dpg.dc_ix = ddr.dc_ix and dpg.group_code = ddr.group_code
						and relation_type = 'S' and ddr.r_ix = p.admin
						and dpg.dpg_ix = '".$plan_discount_info[$i][dpg_ix]."'
						and p.id in (".implode(' , ',$_array_pid).") ";
            }else if($plan_discount_info[$i][goods_display_type] == "A" && $plan_discount_info[$i][display_auto_sub_type] == "B"){
                // 상품전시 타입이 자동일때 자동타입이 브랜드 일때
                $union_sql = "select '".$plan_discount_info[$i][discount_type]."' as discount_type, dpg.dpg_ix, dpg.dc_ix, dpg.group_name, p.id as pid , p.sellprice ,  dpg.commission, dpg.headoffice_rate, dpg.seller_rate, dpg.sale_rate, dpg.discount_sale_type, dpg.round_position, dpg.round_type, dpg.commission
						from shop_discount_product_group dpg , shop_discount_display_relation ddr ,   shop_product p
						where dpg.dc_ix = ddr.dc_ix and dpg.group_code = ddr.group_code
						and relation_type = 'B' and ddr.r_ix = p.brand
						and dpg.dpg_ix = '".$plan_discount_info[$i][dpg_ix]."'
						and p.id in (".implode(' , ',$_array_pid).") ";
            }
            if(!$sql){
                $sql = $union_sql."\n";
            }else{
                $sql .= "union \n".$union_sql;
            }

            //echo "<br><br>";
        }
        if($_array_pid[0] == "0000223298"){
            //echo nl2br($sql);
            //exit;
        }
        //exit;
        if($sql){

            //2015-12-01 Hong 할인 순서 때문에 추가
            $sql .= "order by (case when discount_type='GP' then 1 when discount_type='SP' then 2 else 3 end) asc ";

            $script_times["discount_plan_detail_start_".rand()] = time();
            $slave_mdb->query($sql);
            $_discount_info = $slave_mdb->fetchall();
            $script_times["discount_plan_detail_end_".rand()] = time();
        }
        if($_array_pid[0] == "0000223298"){
            //	print_r($_discount_info);
            //	exit;
        }

        for($i=0 ; $i < count($_discount_info) ;$i++){
            //echo $_discount_info[$i][pid]."<br>";
            $pid = str_pad($_discount_info[$i][pid], 10, "0", STR_PAD_LEFT);
            //echo $pid;
            if($_discount_info[$i][discount_sale_type] == 2){
                $discount_price = $_discount_info[$i][sale_rate];
            }else{
                $discount_price = $_discount_info[$i][sellprice]*$_discount_info[$i][sale_rate]/100;
            }

            if($i == 0 || $discount_info[$pid][$_discount_info[$i][discount_type]]["discount_price"] < $discount_price){

                $discount_info[$pid][$_discount_info[$i][discount_type]]["pid"] = $pid;
                $discount_info[$pid][$_discount_info[$i][discount_type]]["discount_type"] = $_discount_info[$i][discount_type];
                $discount_info[$pid][$_discount_info[$i][discount_type]]["discount_desc"] = $_discount_info[$i][group_name];
                $discount_info[$pid][$_discount_info[$i][discount_type]]["discount_price"] = $discount_price;
                //$discount_info[$pid][$_discount_info[$i][discount_type]]["sellprice"] = $_discount_info[$i][sellprice];
                //$discount_info[$pid][$_discount_info[$i][discount_type]]["dcprice"] = $_discount_info[$i][sellprice]*((100-$_discount_info[$i][sale_rate])/100);
                $discount_info[$pid][$_discount_info[$i][discount_type]]["discount_value"] = $_discount_info[$i][sale_rate];
                $discount_info[$pid][$_discount_info[$i][discount_type]]["discount_value_type"] = $_discount_info[$i][discount_sale_type];
                $discount_info[$pid][$_discount_info[$i][discount_type]]["round_position"] = $_discount_info[$i][round_position];// 1, 2, 3
                $discount_info[$pid][$_discount_info[$i][discount_type]]["round_type"] = $_discount_info[$i][round_type];// 1: 반올림, 2:반내림, 3: 올림, 4: 내림

                $discount_info[$pid][$_discount_info[$i][discount_type]]["headoffice_discount_value"] = $_discount_info[$i][headoffice_rate];
                $discount_info[$pid][$_discount_info[$i][discount_type]]["seller_discount_value"] =  $_discount_info[$i][seller_rate];
                $discount_info[$pid][$_discount_info[$i][discount_type]]["discount_code"] = $_discount_info[$i][dpg_ix];
                $discount_info[$pid][$_discount_info[$i][discount_type]]["dc_ix"] = $_discount_info[$i][dc_ix];
                $discount_info[$pid][$_discount_info[$i][discount_type]]["commission"] = $_discount_info[$i][commission];

            }
        }
        //print_r($discount_info);
    }

    //dc_type 할인타입(MC:복수구매,MG:그룹,C:카테고리,GP:기획,SP:특별,CP:쿠폰,SCP:중복쿠폰,M:모바일,APP:앱할인,E:에누리,DCP:배송쿠폰,DE:배송비에누리)
    if(is_array($_array_pid) && $_SESSION["user"][app_dc_rate] > 0){
        foreach($_array_pid as $pid){

            $pid = str_pad($pid, 10, "0", STR_PAD_LEFT);
            $discount_info[$pid]['APP']["pid"] = $pid;
            $discount_info[$pid]['APP']["discount_type"] = 'APP';
            //$discount_info[$pid]['APP']["sellprice"] = $_discount_info[$i][sellprice];
            //$discount_info[$pid]['APP']["dcprice"] = $_discount_info[$i][sellprice]*((100-$_discount_info[$i][sale_rate])/100);
            $discount_info[$pid]['APP']["discount_value"] = $_SESSION["user"][app_dc_rate];
            $discount_info[$pid]['APP']["discount_value_type"] = "1";// 1:%, 2:원
            $discount_info[$pid]['APP']["round_position"] = $_SESSION["user"][round_depth];// 1, 2, 3
            $discount_info[$pid]['APP']["round_type"] = $_SESSION["user"][round_type];// 1: 반올림, 2:반내림, 3: 올림, 4: 내림

            $discount_info[$pid]['APP']["headoffice_discount_value"] = $_SESSION["user"][app_dc_rate];
            $discount_info[$pid]['APP']["seller_discount_value"] =  0;
            $discount_info[$pid]['APP']["discount_code"] = $_SESSION['app_type'];
        }
    }

    return $discount_info;
}


function getProductList($where, $start, $max, $orderby="", $categoryBool=false, $custom=false) {
    global $slave_mdb, $master_db,$mall_id,$page;

    //글로벌 관련 상품 처리
    $where .= " and p.mall_ix in ('','".$_SESSION["layout_config"]['mall_ix']."') ";

    //적립금정보 불러오기 함수로 적용 2014-07-09 이학봉
    $reserve_data = GetReserveRate();
    //적립금정보 가져옴

    if( $reserve_data[mileage_info_use] == "Y" ) {	// 개별상품 적립금 우선  적용 2013-07-17 이학봉
        if ( UserSellingType() == "W" ) {
            if(UserProductPriceType()=='L'){
                $reserve_sql = " ,case when p.wholesale_reserve_yn = 'N' or p.wholesale_reserve_yn = '' then floor(p.wholesale_price*(".$reserve_data[goods_mileage_rate]."/100)) else floor(p.wholesale_price*(p.wholesale_reserve_rate/100)) end as reserve,
				case when p.wholesale_reserve_yn = 'N' or p.wholesale_reserve_yn = '' then ".$reserve_data[goods_mileage_rate]." else p.wholesale_reserve_rate end as reserve_rate";
            }else{
                $reserve_sql = " ,case when p.wholesale_reserve_yn = 'N' or p.wholesale_reserve_yn = '' then floor(p.wholesale_sellprice*(".$reserve_data[goods_mileage_rate]."/100)) else floor(p.wholesale_sellprice*(p.wholesale_reserve_rate/100)) end as reserve,
				case when p.wholesale_reserve_yn = 'N' or p.wholesale_reserve_yn = '' then ".$reserve_data[goods_mileage_rate]." else p.wholesale_reserve_rate end as reserve_rate";
            }
        } else {
            if(UserProductPriceType()=='P'){
                $reserve_sql = " ,case when p.reserve_yn = 'N' or p.reserve_yn = '' then floor(p.premiumprice*(".$reserve_data[goods_mileage_rate]."/100)) else floor(p.premiumprice*(p.reserve_rate/100)) end as reserve,
				case when p.reserve_yn = 'N' or p.reserve_yn = '' then ".$reserve_data[goods_mileage_rate]." else p.reserve_rate end as reserve_rate";
            }elseif(UserProductPriceType()=='L'){
                $reserve_sql = " ,case when p.reserve_yn = 'N' or p.reserve_yn = '' then floor(p.listprice*(".$reserve_data[goods_mileage_rate]."/100)) else floor(p.listprice*(p.reserve_rate/100)) end as reserve,
				case when p.reserve_yn = 'N' or p.reserve_yn = '' then ".$reserve_data[goods_mileage_rate]." else p.reserve_rate end as reserve_rate";
            }else{
                $reserve_sql = " ,case when p.reserve_yn = 'N' or p.reserve_yn = '' then floor(p.sellprice*(".$reserve_data[goods_mileage_rate]."/100)) else floor(p.sellprice*(p.reserve_rate/100)) end as reserve,
				case when p.reserve_yn = 'N' or p.reserve_yn = '' then ".$reserve_data[goods_mileage_rate]." else p.reserve_rate end as reserve_rate";
            }
        }
    }elseif ( $reserve_data[mileage_info_use] == "P" ) {	// 결제수단별 적립금 비율
        if ( UserSellingType() == "W" ) {
            if(UserProductPriceType()=='L'){
                $reserve_sql = " ,case when p.wholesale_reserve_yn = 'N' or p.wholesale_reserve_yn = '' then floor(p.wholesale_price*(".$reserve_data["goods_mileage_rate_".$reserve_data[basic_rate]]."/100)) else floor(p.wholesale_price*(p.wholesale_reserve_rate/100)) end as reserve,
				case when p.wholesale_reserve_yn = 'N' or p.wholesale_reserve_yn = '' ".$reserve_data["goods_mileage_rate_".$reserve_data[basic_rate]]." else p.wholesale_reserve_rate end as reserve_rate";
            }else{
                $reserve_sql = " ,case when p.wholesale_reserve_yn = 'N' or p.wholesale_reserve_yn = '' then floor(p.wholesale_sellprice*(".$reserve_data["goods_mileage_rate_".$reserve_data[basic_rate]]."/100)) else floor(p.wholesale_sellprice*(p.wholesale_reserve_rate/100)) end as reserve,
				case when p.wholesale_reserve_yn = 'N' or p.wholesale_reserve_yn = '' ".$reserve_data["goods_mileage_rate_".$reserve_data[basic_rate]]." else p.wholesale_reserve_rate end as reserve_rate";
            }
        } else {
            if(UserProductPriceType()=='P'){
                $reserve_sql = " ,case when p.reserve_yn = 'N' or p.reserve_yn = '' then floor(p.premiumprice*(".$reserve_data["goods_mileage_rate_".$reserve_data[basic_rate]]."/100)) else floor(p.premiumprice*(p.reserve_rate/100)) end as reserve,
				case when p.reserve_yn = 'N' or p.reserve_yn = '' then ".$reserve_data["goods_mileage_rate_".$reserve_data[basic_rate]]." else p.reserve_rate end as reserve_rate";
            }elseif(UserProductPriceType()=='L'){
                $reserve_sql = " ,case when p.reserve_yn = 'N' or p.reserve_yn = '' then floor(p.listprice*(".$reserve_data["goods_mileage_rate_".$reserve_data[basic_rate]]."/100)) else floor(p.listprice*(p.reserve_rate/100)) end as reserve,
				case when p.reserve_yn = 'N' or p.reserve_yn = '' then ".$reserve_data["goods_mileage_rate_".$reserve_data[basic_rate]]." else p.reserve_rate end as reserve_rate";
            }else{
                $reserve_sql = " ,case when p.reserve_yn = 'N' or p.reserve_yn = '' then floor(p.sellprice*(".$reserve_data["goods_mileage_rate_".$reserve_data[basic_rate]]."/100)) else floor(p.sellprice*(p.reserve_rate/100)) end as reserve,
				case when p.reserve_yn = 'N' or p.reserve_yn = '' then ".$reserve_data["goods_mileage_rate_".$reserve_data[basic_rate]]." else p.reserve_rate end as reserve_rate";
            }
        }
    } else {
        if ( UserSellingType() == "W" ) {	//기업회원일경우 도매가로 적립율 적용
            if(UserProductPriceType()=='L'){
                $reserve_sql = " ,case when p.wholesale_reserve_yn = 'Y' then floor(p.wholesale_price*(p.wholesale_reserve_rate/100)) else 0 end as reserve,
				case when p.wholesale_reserve_yn = 'Y' then p.wholesale_reserve_rate else 0 end as reserve_rate";
            }else{
                $reserve_sql = " ,case when p.wholesale_reserve_yn = 'Y' then floor(p.wholesale_sellprice*(p.wholesale_reserve_rate/100)) else 0 end as reserve,
				case when p.wholesale_reserve_yn = 'Y' then p.wholesale_reserve_rate else 0 end as reserve_rate";
            }
        } else {
            if(UserProductPriceType()=='P'){
                $reserve_sql = " ,case when p.reserve_yn = 'Y' then floor(p.premiumprice*(p.reserve_rate/100)) else 0 end as reserve,
				case when p.reserve_yn = 'Y' then p.reserve_rate else 0 end as reserve_rate";
            }elseif(UserProductPriceType()=='L'){
                $reserve_sql = " ,case when p.reserve_yn = 'Y' then floor(p.listprice*(p.reserve_rate/100)) else 0 end as reserve,
				case when p.reserve_yn = 'Y' then p.reserve_rate else 0 end as reserve_rate";
            }else{
                $reserve_sql = " ,case when p.reserve_yn = 'Y' then floor(p.sellprice*(p.reserve_rate/100)) else 0 end as reserve,
				case when p.reserve_yn = 'Y' then p.reserve_rate else 0 end as reserve_rate";
            }
        }
    }

    if(is_mobile()){
        $is_mobile_use_where = " and p.is_mobile_use in ('A','M')";
    }else{
        $is_mobile_use_where = " and p.is_mobile_use in ('A','W')";
    }

    if($categoryBool){
        $category_where = "and r.cid LIKE '".substr($_GET['cid'],0,($_GET['depth']+1)*3)."%'";
    }

    /*
	$sql = "SELECT COUNT(distinct p.id) as total  FROM ".TBL_SHOP_PRODUCT." p
			left join shop_product_addinfo pa on p.id=pa.pid
			right join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid ".$category_where."
			right join ".TBL_SHOP_CATEGORY_INFO." c on r.cid = c.cid and category_use ='1'
			$add_table
			where ".$where." ".$is_mobile_use_where." and p.disp in ('1','3')  and p.state in ('0','1')
			and if(p.is_sell_date = '1',p.sell_priod_sdate <= '".date('Ymd')."' and p.sell_priod_edate >= '".date('Ymd')."','1=1')";

	$slave_mdb->query($sql);
	$slave_mdb->fetch();
	$ptotal = $slave_mdb->dt[total];
	*/

    /**
     * 도매몰일때 도매가로 결제하도록 wholesale_price를 sellprice로 가져오기 bgh
     *
     * sellprice를 $select_price로 대체
     */

    if(UserSellingType() == 'W'){
        if(UserProductPriceType()=='L'){
            $select_price = 'wholesale_price as listprice, wholesale_price as sellprice, sellprice AS ori_sellprice, 0 as sale_rate ,  (listprice-sellprice)/listprice*100 as b2c_sale_rate , pa.wholesale_free_delivery_yn as free_delivery_yn ';
        }else{
            $select_price = 'wholesale_price as listprice, wholesale_sellprice as sellprice, sellprice AS ori_sellprice, (wholesale_price-wholesale_sellprice)/wholesale_price*100 as sale_rate ,  (listprice-sellprice)/listprice*100 as b2c_sale_rate , pa.wholesale_free_delivery_yn as free_delivery_yn ';
        }
    }else{
        if(UserProductPriceType()=='P'){
            $select_price = 'premiumprice as sellprice, listprice, (listprice-premiumprice)/listprice*100 as sale_rate , pa.free_delivery_yn as free_delivery_yn ';
        }elseif(UserProductPriceType()=='L'){
            $select_price = 'listprice as sellprice, listprice, 0 as sale_rate , pa.free_delivery_yn as free_delivery_yn ';
        }else{
            $select_price = 'sellprice, listprice, (listprice-sellprice)/listprice*100 as sale_rate , pa.free_delivery_yn as free_delivery_yn ';
        }
    }

    /*
	$sql = 	"select
				HIGH_PRIORITY distinct p.id ,
				p.*
			from
				(SELECT
					p.id, p.product_type, p.pcode,
					p.pname ,p.brand_name,
					p.shotinfo, p.state,
					p.company, p.admin,
					p.stock, p.stock_use_yn,
					case when state = 0 then convert(vieworder, SIGNED)-1000000 else vieworder end  as vieworder2,
					$select_price ,
					premiumprice,
					icons,
					reserve_yn,
					p.is_sell_date,
					p.sell_priod_sdate,
					p.sell_priod_edate,
					p.view_cnt,
					p.order_cnt,
					p.recommend_cnt,
					ifnull(ceil(p.after_score/p.after_cnt),'0') as uf_valuation,
					p.after_cnt,
					r.cid,
					p.delivery_price,
					p.delivery_type,
					ccd.com_name,
					csg.group_name
					$reserve_sql
				FROM
					".TBL_SHOP_PRODUCT." p
					left join shop_product_addinfo pa on p.id=pa.pid
					INNER JOIN common_company_detail AS ccd ON p.admin=ccd.company_id
					INNER JOIN common_seller_detail AS csd ON ccd.company_id = csd.company_id
					LEFT JOIN common_seller_group AS csg ON csd.sg_ix = csg.sg_ix
					right join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid ".$category_where."
					right join ".TBL_SHOP_CATEGORY_INFO." c on r.cid = c.cid and category_use ='1'
					$add_table
				where
					".$where." ".$is_mobile_use_where."
					and p.disp in ('1','3') and p.state in ('0','1')
					and if(p.is_sell_date = '1',p.sell_priod_sdate <= '".date('Ymd')."' and p.sell_priod_edate >= '".date('Ymd')."','1=1')
					".$orderby."
					limit $start,".($max+200)."
				) p
			where
				1
				limit 0,".($max)."";

	//force index(regdate_desc)  이슈 있어서 주석처리
	*/

    //				(SELECT COUNT(*) FROM bbs_premium_after bpa, bbs_after ba WHERE bpa.bbs_etc1 = p.id OR ba.bbs_etc1 =  p.id) as total,

    $count_sql = "SELECT count(*) as total
						FROM
						shop_product p  
						left join shop_product_relation r on p.id = r.pid 
						left join ".TBL_SHOP_CATEGORY_INFO." c on r.cid = c.cid 
						left join shop_product_addinfo pa on p.id=pa.pid
						where
						";

    $sql = 	"SELECT
				HIGH_PRIORITY p.id,
				p.product_type, p.pcode, p.product_color_chip,
				p.pname ,p.brand_name,p.global_pinfo,p.brand,
				p.shotinfo, p.state, p.origin,
				p.company, p.admin,
				p.stock, p.stock_use_yn,
				case when state = 0 then convert(p.vieworder, SIGNED)-1000000 else p.vieworder end  as vieworder2,
				p.premiumprice,
				$select_price ,
				icons,
				reserve_yn,
				p.is_sell_date,
				p.sell_priod_sdate,
				p.sell_priod_edate,
				p.view_cnt,
				p.order_cnt,
				p.recommend_cnt,
				ifnull(ceil(p.after_score/p.after_cnt),'0') as uf_valuation,
				p.after_cnt,
				r.cid,
				case when date_sub(NOW(), INTERVAL 7 DAY) < p.regdate then 1 else 0 end as is_new,
				p.regdate, 
				ccd.com_name,
				csd.shop_name, 
				csg.group_name,
				pa.use_coupon_yn
				$reserve_sql
			FROM
				".TBL_SHOP_PRODUCT." p force index(regdate_desc)
				left join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid 
				left join ".TBL_SHOP_CATEGORY_INFO." c on r.cid = c.cid 
				left join shop_product_addinfo pa on p.id=pa.pid
				left join shop_brand sb on p.brand=sb.b_ix
				INNER JOIN common_company_detail AS ccd ON p.admin=ccd.company_id
				INNER JOIN common_seller_detail AS csd ON ccd.company_id = csd.company_id
				LEFT JOIN common_seller_group AS csg ON csd.sg_ix = csg.sg_ix
				
				$add_table
			where";
    if($_SERVER["PHP_SELF"] == '/shop/search.php'){
        if($_SESSION["layout_config"]['search_engine_yn'] == 'Y'){
            if($_SESSION["layout_config"]['search_engine_type'] == 'S'){
                $sql.=" p.id	";
            }else if($_SESSION["layout_config"]['search_engine_type'] == 'D'){

                $count_sql .="	".$where."
					and p.disp in ('1','3') and p.state in ('0','1') and category_use !='0' ".$category_where."
					and if(p.is_sell_date = '1',p.sell_priod_sdate <= '".date('Ymd')."' and p.sell_priod_edate >= '".date('Ymd')."','1=1') ";



                $sql.="	".$where."
						and p.disp in ('1','3') and p.state in ('0','1') and category_use !='0' ".$category_where."
						and if(p.is_sell_date = '1',p.sell_priod_sdate <= '".date('Ymd')."' and p.sell_priod_edate >= '".date('Ymd')."','1=1')
						".$orderby."
						limit $start,".($max)."";
            }else{

                $count_sql .="	".$where."
					and p.disp in ('1','3') and p.state in ('0','1') and category_use !='0' ".$category_where."
					and if(p.is_sell_date = '1',p.sell_priod_sdate <= '".date('Ymd')."' and p.sell_priod_edate >= '".date('Ymd')."','1=1') ";



                $sql.="	".$where."
						and p.disp in ('1','3') and p.state in ('0','1') and category_use !='0' ".$category_where."
						and if(p.is_sell_date = '1',p.sell_priod_sdate <= '".date('Ymd')."' and p.sell_priod_edate >= '".date('Ymd')."','1=1')
						".$orderby."
						limit $start,".($max)."";
            }
        }else{

            $count_sql .="	".$where."
					and p.disp in ('1','3') and p.state in ('0','1') and category_use !='0' ".$category_where."
					and if(p.is_sell_date = '1',p.sell_priod_sdate <= '".date('Ymd')."' and p.sell_priod_edate >= '".date('Ymd')."','1=1') ";


            $sql.="	".$where."
					and p.disp in ('1','3') and p.state in ('0','1') and category_use !='0' ".$category_where."
					and if(p.is_sell_date = '1',p.sell_priod_sdate <= '".date('Ymd')."' and p.sell_priod_edate >= '".date('Ymd')."','1=1')
					".$orderby."
					limit $start,".($max)."";
        }
    }else{
        $count_sql .="	".$where."
					and p.disp in ('1','3') and p.state in ('0','1') and category_use !='0' ".$category_where."
					and if(p.is_sell_date = '1',p.sell_priod_sdate <= '".date('Ymd')."' and p.sell_priod_edate >= '".date('Ymd')."','1=1') ";

        $sql.="	".$where."
					and p.disp in ('1','3') and p.state in ('0','1') and category_use !='0' ".$category_where."
					and if(p.is_sell_date = '1',p.sell_priod_sdate <= '".date('Ymd')."' and p.sell_priod_edate >= '".date('Ymd')."','1=1')
					".$orderby."
					limit $start,".($max)."";

    }

    if($_SERVER["PHP_SELF"] == '/shop/search.php'){
        if($_SESSION["layout_config"]['search_engine_yn'] == 'Y'){
            if($_SESSION["layout_config"]['search_engine_type'] == 'S'){
                require $_SERVER['DOCUMENT_ROOT'].'/include/sphinx_helper.php';

                $sh = new SphinxHelper($mall_id);// isoda product index
                $sh->page =  $page;
                $sh->limit =  $max;
                $sh->set_category_search(TRUE); //카테고리 검색 사용

                $search_result = $sh->search($_GET, $sql);
                //				print_r($search_result[cate_list]);


                $ptotal = $search_result['total'];
                $products = $search_result['pinfo'];
                $cate_list = $search_result['cate_list'];
                $cate_total = count($search_result['cate_list']);
            }else if($_SESSION["layout_config"]['search_engine_type'] == 'D'){
                $slave_mdb->query($sql);
                $products = $slave_mdb->fetchall("object");


                $slave_mdb->query("select FOUND_ROWS() as total ");
                $slave_mdb->fetch();
                $ptotal = $slave_mdb->dt[total];
            }else{
                $slave_mdb->query($sql);
                $products = $slave_mdb->fetchall("object");


                //$slave_mdb->query("select FOUND_ROWS() as total ");
                $slave_mdb->query($count_sql);
                $slave_mdb->fetch();
                $ptotal = $slave_mdb->dt[total];
            }

        }else{
            $slave_mdb->query($sql);
            $products = $slave_mdb->fetchall("object");



            $slave_mdb->query($count_sql);
            $slave_mdb->fetch();
            $ptotal = $slave_mdb->dt[total];
        }
    }else{
//		print_r($sql);
//		exit;
        $slave_mdb->query($sql);
        $products = $slave_mdb->fetchall("object");

//echo $count_sql;
//exit;
        //$slave_mdb->query("select FOUND_ROWS() as total ");
        $slave_mdb->query($count_sql);
        $slave_mdb->fetch();
        $ptotal = $slave_mdb->dt[total];
        //echo $ptotal;
    }
    /*
		case when sdt.delivery_basic_terms <= p.premiumprice then 0 when sdt.delivery_basic_terms > p.premiumprice then sdt.delivery_price END as delivery_price,
		left join shop_product_delivery spd on p.id = spd.pid
		left join shop_delivery_terms sdt on spd.dt_ix = sdt.dt_ix
	*/


    /*
	1. 소팅은 생각하지 않는다.
	2. 할인정책정보를 가져오는 쿼리와 선택된 상품정보를 in 형태로 조건을 걸어준다.
	3. 할인 정책정보를 루프를 돌면서 해당 조건과 맞는 쿼리를 생서해서 유니온으로 조합한다.
	4. 조합된 결과의 데이타를 아래 $products  변수에서 sellprice 가격정보를 변경하거나 별도의 할인가격을 추가한다.
	5. 이벤트 기획전 과같은 프로모션의 상품은 할인정책을 변수에 담아서 계산하는 function 을 만들고 그를 함수를 통해서 할인가를 계산한다.
	*/

    $pinfo[brand_name]= getGlobalTargetName($pinfo["brand_name"],$global_binfo,'brand_name');

    if(count($products)){
        if($_SESSION["user"]["code"]){
            $slave_mdb->query("SELECT pid FROM shop_wishlist where mid = '".$_SESSION["user"]["code"]."' ");
            $favorite_goods = $slave_mdb->getrows();
            $favorite_goods = $favorite_goods[0];
            //print_r($favorite_goods);
        }

        for($i=0 ; $i < count($products) ;$i++){
            $_array_pid[] = $products[$i][id];
            $goods_infos[$products[$i][id]][pid] = $products[$i][id];
            $goods_infos[$products[$i][id]][amount] = $products[$i][pcount];
            $goods_infos[$products[$i][id]][cid] = $products[$i][cid];
            $goods_infos[$products[$i][id]][depth] = $products[$i][depth];

            $products[$i][category_add_infos] = json_decode(urldecode($products[$i][category_add_infos]),true);
            $products[$i][pname] = getGlobalTargetName($products[$i][pname],$products[$i][global_pinfo],'pname');
            $products[$i][is_favorite] = false;
            if(is_array($favorite_goods)){
                if(in_array($products[$i][id], $favorite_goods)){
                    $products[$i][is_favorite] = true;
                }
            }
            $slave_mdb->query("SELECT global_binfo FROM shop_brand where b_ix = '".$products[$i]["brand"]."'");
            $slave_mdb->fetch();
            $global_binfo= $slave_mdb->dt[global_binfo];

            $products[$i][brand_name]=getGlobalTargetName($products[$i]["brand_name"],$global_binfo,'brand_name');
        }

        $discount_info = DiscountRult($goods_infos, $cid, $depth);
        if(is_array($products))
        {
            foreach ($products as $key => $sub_array) {
                //$pname = getGlobalTargetName($products[$key][pname],$products[$key][global_pinfo],'pname');
                $discount_desc = array();
                $select_ = array("icons_list"=>explode(";",$sub_array[icons]));
                array_insert($sub_array,50,$select_);

                $pcolor_ = array("colorchip_list"=>explode(",",str_replace(" ","",$sub_array[product_color_chip])));
                array_insert($sub_array,51,$pcolor_);

                $discount_item = $discount_info[$sub_array[id]];

                $_dcprice = $sub_array[sellprice];
                if(is_array($discount_item)){
                    foreach($discount_item as $_key => $_item){
                        if($_item[discount_value_type] == "1"){ // %
                            $_dcprice = roundBetter($_dcprice*(100 - $_item[discount_value])/100, $_item[round_position], $_item[round_type]);//$_dcprice*(100 - $_item[discount_value])/100;

                        }else if($_item[discount_value_type] == "2"){// 원
                            $_dcprice = $_dcprice - $_item[discount_value];
                        }
                        $discount_desc[] = $_item;
                    }
                }
                $_dcprice = array("dcprice"=>$_dcprice);
                array_insert($sub_array,72,$_dcprice);
                $discount_desc = array("discount_desc"=>$discount_desc);
                array_insert($sub_array,73,$discount_desc);
                $products[$key][pname]='';
                $products[$key] = $sub_array;
                if($products[$key][uf_valuation] != "") $products[$key][uf_valuation] = round($products[$key][uf_valuation], 0);
                else $products[$key][uf_valuation] = 0;
            }
        }
    }

    if(count($products))
    {
        for($i=0 ; $i < count($products) ;$i++){
            $products[$i]['listprice'] = getExchangeNationPrice($products[$i]['listprice']);
            $products[$i]['sellprice'] = getExchangeNationPrice($products[$i]['sellprice']);
            $products[$i]['dcprice'] = getExchangeNationPrice($products[$i]['dcprice']);
        }
    }

    $return['total'] = $ptotal;
    $return['products'] = $products;
    $return['cate_list'] = $cate_list;
    $return['cate_total'] = $cate_total;

    $return['sql'] = $sql;

    if($custom){

        /*2015-11-11 Hong 코웰요청으로 브랜드 , 카테고리만 사용! 속도를 위해 가볍게 코딩처리함
		$select_price = 'min('.UserProductPriceColumn().') as min_price, max('.UserProductPriceColumn().') as max_price, ';

		$sql = "SELECT
					$select_price GROUP_CONCAT(distinct concat(p.brand_name,'|',p.brand) order by concat(p.brand_name,'|',p.brand)) as brand_info,
					GROUP_CONCAT(distinct pod.option_color) as color, GROUP_CONCAT(distinct pod.option_size) as size
				FROM ".TBL_SHOP_PRODUCT." p
				left join shop_product_addinfo pa on p.id=pa.pid
				left join shop_product_options_detail pod on (pod.pid=p.id and pod.option_soldout!='1' and option_stock > 0 and option_color !='' and option_size !='')
				right join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid ".$category_where."
				right join ".TBL_SHOP_CATEGORY_INFO." c on r.cid = c.cid and category_use ='1'
				$add_table
				where ".$where." ".$is_mobile_use_where." and p.disp in ('1','3')  and p.state in ('0','1')
				and if(p.is_sell_date = '1',p.sell_priod_sdate <= '".date('Ymd')."' and p.sell_priod_edate >= '".date('Ymd')."','1=1')";

		$slave_mdb->query($sql);
		$slave_mdb->fetch();

		$return['custom']['min_price'] = $slave_mdb->dt['min_price'];
		$return['custom']['max_price'] = $slave_mdb->dt['max_price'];
		$return['custom']['color'] = $slave_mdb->dt['color'];
		$return['custom']['size'] = $slave_mdb->dt['size'];

		$brand_info_array = explode(',',$slave_mdb->dt['brand_info']);

		for($br=0; $br<count($brand_info_array);$br++){

			$brand_ix = explode('|',$brand_info_array[$br]);

			$slave_mdb->query("SELECT global_binfo FROM shop_brand where b_ix = '".$brand_ix[1]."'");
			$slave_mdb->fetch();
			$global_binfo= $slave_mdb->dt[global_binfo];

			$brand_info=getGlobalTargetName($brand_info_array[$br],$global_binfo,'brand_name');

			if(strpos($brand_info, '|') == false){
				$brand_info_sum[$br] = $brand_info.'|'.$brand_ix[1];
			}else{
				$brand_info_sum[$br] = $brand_info;
			}
		}

		$return['custom']['brand_info'] = implode(',', $brand_info_sum);
		*/

        if($ptotal){

            $sql = "SELECT
						min(".UserProductPriceColumn().") as min_price,
						max(".UserProductPriceColumn().") as max_price,
						GROUP_CONCAT(distinct p.brand) as brand_info,
						GROUP_CONCAT(distinct r.cid) as cate_info,
						sum(case when soho = '1' then 1 else 0 end) soho_cnt, 
						sum(case when designer = '1'  then 1 else 0 end) designer_cnt, 
						sum(case when mirrorpick = '1' then 1 else 0 end) mirrorpick_cnt
					FROM ".TBL_SHOP_PRODUCT." p
					left join shop_product_addinfo pa on p.id=pa.pid
					left join shop_brand sb on p.brand=sb.b_ix
					
					join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid ".$category_where."  
					right join ".TBL_SHOP_CATEGORY_INFO." c on r.cid = c.cid and category_use ='1'
					$add_table
					where ".$where." ".$is_mobile_use_where." and p.disp in ('1','3')  and p.state in ('0','1')
					and if(p.is_sell_date = '1',p.sell_priod_sdate <= '".date('Ymd')."' and p.sell_priod_edate >= '".date('Ymd')."','1=1')";

            //GROUP_CONCAT(distinct pod.option_size) as size,
            //left join shop_product_options_detail pod on (pod.pid=p.id and pod.option_soldout!='1' and option_stock > 0 and option_size !='')

            //	echo nl2br($sql);
            //	exit;
            $slave_mdb->query($sql);
            $slave_mdb->fetch();

            $return['custom']['min_price'] = 1000;//$slave_mdb->dt['min_price'];
            $return['custom']['max_price'] = 2000000;//$slave_mdb->dt['max_price'];
            $return['custom']['size'] = $slave_mdb->dt['size'];
            $return['custom']['soho_cnt'] = $slave_mdb->dt['soho_cnt'];
            $return['custom']['designer_cnt'] = $slave_mdb->dt['designer_cnt'];
            $return['custom']['mirrorpick_cnt'] = $slave_mdb->dt['mirrorpick_cnt'];

            $cate_info_array = explode(',',$slave_mdb->dt['cate_info']);
            $brand_info_array = explode(',',$slave_mdb->dt['brand_info']);


            $slave_mdb->query("SELECT cid, cname, global_cinfo FROM shop_category_info where cid in ('".implode("','",$cate_info_array)."')");
            $cate_info_array = $slave_mdb->fetchall("object");

            $cate_info_sum = array();
            for($br=0; $br<count($cate_info_array);$br++){
                $cid = $cate_info_array[$br][cid];
                $cname = $cate_info_array[$br][cname];
                $global_cinfo = $cate_info_array[$br][global_cinfo];
                $cname=getGlobalTargetName($cname,$global_cinfo,'cname');
                $sub_cname = "";
                if($_SERVER['PHP_SELF']=="/event/goods_brand.php"){
                    if(substr($cid,0,3)=='000'){
                        $sub_cname = "Womens > ";
                    }elseif(substr($cid,0,3)=='001'){
                        $sub_cname = "Mens > ";
                    }
                }
                $cate_info_sum[$br] = $sub_cname.$cname."|".$cid;
            }

            $slave_mdb->query("SELECT b_ix, brand_name, global_binfo FROM shop_brand where b_ix in ('".implode("','",$brand_info_array)."')");
            $brand_info_array = $slave_mdb->fetchall("object");

            for($br=0; $br<count($brand_info_array);$br++){
                $b_ix = $brand_info_array[$br][b_ix];
                $brand_name = $brand_info_array[$br][brand_name];
                $global_binfo = $brand_info_array[$br][global_binfo];
                $brand_name=getGlobalTargetName($brand_name,$global_binfo,'brand_name');
                $brand_info_sum[$br] = $brand_name.'|'.$b_ix;
            }
            if(is_array($cate_info_sum)){
                $return['custom']['cate_info'] = implode(',', $cate_info_sum);
            }
            else{
                $return['custom']['cate_info'] = $cate_info_sum;
            }

            if(is_array($brand_info_sum)){
                $return['custom']['brand_info'] = implode(',', $brand_info_sum);
            }
            else{
                $return['custom']['brand_info'] = $brand_info_sum;
            }
        }else{
            $return['custom']['size']="";
            $return['custom']['cate_info']="";
            $return['custom']['brand_info']="";
        }
    }

    //print_r($return);
    return $return;
}

function getProductAddImage($id){

    global $slave_db;

    $slave_db->query("select id from ".TBL_SHOP_ADDIMAGE." a where pid = '$id'");
    $cliparts = $slave_db->fetchall("object");
    if(!is_array($cliparts)){
        $cliparts = array();
    }

    for($i=0; $i<count($cliparts); $i++){
        if($cliparts[$i] != "") {
            $cliparts[$i][addbimg] = PrintImageAdd($_SESSION['layout_config']['mall_data_root']."/images", $id, $cliparts[$i][id], 'b');
            $cliparts[$i][addmimg] = PrintImageAdd($_SESSION['layout_config']['mall_data_root']."/images", $id, $cliparts[$i][id], 'm');
            $cliparts[$i][addimg] = PrintImageAdd($_SESSION['layout_config']['mall_data_root']."/images", $id, $cliparts[$i][id], 'c');
            $cliparts[$i][action] = " onclick=\"imgChange('".$cliparts[$i][id]."')\" style='cursor:pointer;'";
        } else {
            $cliparts[$i][addbimg] = "";
            $cliparts[$i][addmimg] = "";
            $cliparts[$i][addimg] = "";
            $cliparts[$i][action] = " ";
        }
    }

    return $cliparts;
}

function multidimensional_search($parents, $searched, $return_type = "array") {
    if (empty($searched) || empty($parents)) {
        return false;
    }

    foreach ($parents as $key => $value){
        $exists = true;
        foreach ($searched as $skey => $svalue) {
            //  $exists = ($exists && IsSet($parents[$key][$skey]) && $parents[$key][$skey] == $svalue);
            if(IsSet($parents[$key][$skey]) && substr_count("_".$parents[$key][$skey],$svalue)){
                if($return_type == "array"){
                    $return_array[$key] = $parents[$key];
                }else if($return_type == "key"){
                    $return_array[cid][] = $key;
                    $return_array[depth][] = $parents[$key][depth];
                }
            }
        }
        //if($exists){ return $key; }
    }
    if(is_array($return_array)){
        return $return_array;
    }

    return false;
}

function viewSaleDetail($sale_detail_infos,$return_type="echo"){
    global $_DISCOUNT_TYPE;

    if(!is_array($sale_detail_infos) && $sale_detail_infos != ""){
        $sale_detail_infos = unserialize($sale_detail_infos);
    }

    if(is_array($sale_detail_infos)){
        foreach($sale_detail_infos as $key=>$value){
            if($value[discount_type] != ""){
                if($return_type=="echo"){
                    echo $_DISCOUNT_TYPE[$value[discount_type]]." : ".$value[discount_value]."".($value[discount_value_type] == "1" ? "%":"원")."<br>";
                }else{
                    $return .= $_DISCOUNT_TYPE[$value[discount_type]]." : ".$value[discount_value]."".($value[discount_value_type] == "1" ? "%":"원")."<br>";
                }
            }
        }
    }

    if($return_type!="echo"){
        //echo $return."<br/><br/>";
        return $return;
    }
}

function arr_cateInfo_func($return_val="1",$return_pCode="") {//좌측 카테고리 함수 kbk 13/07/25
    global $slave_mdb;
    //      $slave_mdb=new Database;

    $slave_mdb->query('SELECT cid, depth, cname FROM '.TBL_SHOP_CATEGORY_INFO.' WHERE depth < 2 AND category_use = 1 ORDER BY depth, vlevel1, vlevel2');
    $arr_cateInfo = array();
    $keys = 0;
    foreach($slave_mdb->fetchall() as $_key=>$_val)        {
        $pCode = substr($_val['cid'], 0, ($_val['depth'] * 3));

        if($_val['depth'])      {
            $_val['pCode'] = $pCode;
            $arr_cateInfo2[$pCode][] = $_val;
        }       else    {
            $_val['pCode'] = substr($_val['cid'], 0, 3);
            $arr_cateInfo1[$keys] = $_val;
            $keys++;
        }
    }
    if($return_val=="1") return $arr_cateInfo1;
    else return $arr_cateInfo2[$return_pCode];
}

/**
 * 날짜/시간계산함수
 *
 * @param string $StartDate
 * @param string $EndDate
 * @param string $option
 *
 * @return string | int | array
 */
function dateDiff($StartDate, $EndDate, $option = ''){
    $StartTime = strtotime($StartDate);
    $EndTime = strtotime($EndDate);

    if($StartTime > $EndTime){
        return false;
    }
    $DiffTime = $EndTime - $StartTime;

    if($option == 'minute'){
        $ReturnValue = sprintf("%02d", ($DiffTime/60));

        return $ReturnValue;

    }else if($option == 'day'){
        $ReturnValue = floor($DiffTime/60/60/24);

        return $ReturnValue;

    }else{
        $ReturnValue['d'] = floor($DiffTime/60/60/24);
        //$ReturnValue['d'] = $DiffTime/60/60/24;
        $ReturnValue['H'] = ($DiffTime/60/60)%24;
        $ReturnValue['i'] = sprintf("%02d", ($DiffTime/60)%60);

        return $ReturnValue;
    }
}

/**
 * 날짜 표시형식 변환
 *
 * @param string $input_date
 *
 * @return string
 */
function changeDisplayDate($input_date){
    $diff = dateDiff(date('Y-m-d H:i:s'),$input_date);
    switch($diff['d']){
        case '0':
            $display_date = '<span>'.$diff['H'].'</span> 시간 남음';
            break;
        default:
            $display_date = '<span>'.$diff['d'].'</span> 일 남음';
            break;
    }
    return $display_date;
}


/*도로명 주소 시, 구군 셀렉트 박스로 보여주기 jk140129[S]*/
function getCityList($sido = "")
{
    global $slave_mdb;
    $slave_mdb = new MySQL;
    if($_SERVER[HTTP_HOST] == 'daisodev.forbiz.co.kr'){
        $slave_mdb->setDbHost("mallstory.com");
        $slave_mdb->setDbName("shop_zipnew");
        $slave_mdb->setDbUser("shop_zipnew","shop_zipnew!@#$");
    }else{
        $slave_mdb->setDbHost("192.168.0.9");
        $slave_mdb->setDbName("shop_zipnew");
        $slave_mdb->setDbUser("shop_zipnew","shop_zipnew!@#$");
    }
    $sql="select * from shop_sido_code order by sido_code asc";
    //return $sql;
    $slave_mdb->query($sql);

    $mstring = "<select name='sido' id='sido' style='border:0px;width:170px;height:18px;font-size:11px;' onChange=\"loadSolution(this,'sigugun')\"  validation=false title='시도'>";
    $mstring .= "<option value=''>:: 시/도 ::</option>";
    if($slave_mdb->total){


        for($i=0;$i < $slave_mdb->total;$i++){
            $slave_mdb->fetch($i);
            if($slave_mdb->dt[sido_name] == $sido){
                $mstring .= "<option value='".$slave_mdb->dt[sido_name]."' sido_code='".$slave_mdb->dt[sido_code]."' selected >".$slave_mdb->dt[sido_name]."</option>";
            }else{
                $mstring .= "<option value='".$slave_mdb->dt[sido_name]."' sido_code='".$slave_mdb->dt[sido_code]."' >".$slave_mdb->dt[sido_name]."</option>";
            }
        }

    }
    $mstring .= "</select>";

    return $mstring;

}

function getCountyList($sigugun=""){
    global $slave_mdb , $sido;
    $slave_mdb = new MySQL;
    if($_SERVER[HTTP_HOST] == 'daisodev.forbiz.co.kr'){
        $slave_mdb->setDbHost("mallstory.com");
        $slave_mdb->setDbName("shop_zipnew");
        $slave_mdb->setDbUser("shop_zipnew","shop_zipnew!@#$");
    }else{
        $slave_mdb->setDbHost("192.168.0.9");
        $slave_mdb->setDbName("shop_zipnew");
        $slave_mdb->setDbUser("shop_zipnew","shop_zipnew!@#$");
    }

    if($sigugun != ""){
        $sql="select * from shop_gugun_code where sido_name = '$sido' order by gu_ix asc";
        $slave_mdb->query($sql);
    }

    //return $sido;


    $mstring = "<select name='sigugun' id='sigugun' style='border:0px;width:170px;height:18px;font-size:11px;'  validation=false title='시구군'>";
    $mstring .= "<option value=''>:: 시구군 ::</option>";
    if($slave_mdb->total){


        for($i=0;$i < $slave_mdb->total;$i++){
            $slave_mdb->fetch($i);
            if($slave_mdb->dt[gugun_name] == $sigugun){
                $mstring .= "<option value='".$slave_mdb->dt[gugun_name]."' selected>".$slave_mdb->dt[gugun_name]."</option>";
            }else{
                $mstring .= "<option value='".$slave_mdb->dt[gugun_name]."' >".$slave_mdb->dt[gugun_name]."</option>";
            }
        }

    }
    $mstring .= "</select>";

    return $mstring;
}
/*도로명 주소 시, 구군 셀렉트 박스로 보여주기 jk140129[E]*/



function ProductDliveryInfo($pid,$dt_ix='',$type='policy'){	//상품별 배송정책 문구 가져오기 함수 2014-07-25 이학봉 (상품상세페이지 용)
    global $slave_mdb;
    global $_SESSION, $_LANGUAGE;
    //$slave_mdb = new Database;

    /*
	if($_SESSION['user']['mem_type'] == 'C'){
		$is_wholesale = 'W';
	}else{
		$is_wholesale = UserSellingType();
	}*/
    $is_wholesale = UserSellingType();

    if($dt_ix != ""){	//템플릿 키가 잇을경우는 해당 템플릿만 조회
        $where = " and pd.dt_ix = '".$dt_ix."' ";
        $i ='0';
    }else{	//템플릿 키가 없을경우 첫번째 정책으로 조회
        $i = '0';
    }

    $sql = "select
			*
			from
				shop_product_delivery as pd
				left join shop_delivery_template dt on (pd.dt_ix = dt.dt_ix)
			where
				pd.pid = '".$pid."'
				and pd.is_wholesale = '".$is_wholesale."'
				$where
				order by pd.delivery_div ASC";
    $slave_mdb->query($sql);
    $template_array = $slave_mdb->fetchall();

    $dt_ix = $template_array[0][dt_ix];


    if($template_array[$i][delivery_package] == 'N'){
        $use_bundle_text = getLanguageText('760608370cc7466936c2976d8e8a4c29');
    }else{
        $use_bundle_text = getLanguageText('61301963a71ed9516fc3d2e20bb5c99f');
    }

    $is_free_use = true;	// true : 무료 false : 유료

    switch($template_array[$i][delivery_policy]){
        case '1':
            $template_text = $use_bundle_text." : ".getLanguageText('3390874360c5b4cba706685390ccf804');
            $is_free_use = false;
            break;
        case '2':
            $template_text = $use_bundle_text." : 고정배송비 ".number_format($template_array[$i][delivery_price])." 원 ";
            break;
        case '3':
            $sql = "select * from shop_delivery_terms where dt_ix = '".$template_array[$i][dt_ix]."' and delivery_policy_type = '3' order by seq ASC limit 0,1";
            $slave_mdb->query($sql);
            $slave_mdb->fetch();
            $template_text = $use_bundle_text." : 주문금액 ".number_format($slave_mdb->dt[delivery_basic_terms])." 원 미만일경우 ".number_format($slave_mdb->dt[delivery_price])." 원 ";
            break;
        case '4':
            $sql = "select * from shop_delivery_terms where dt_ix = '".$template_array[$i][dt_ix]."' and delivery_policy_type = '4' order by seq ASC limit 0,1";
            $slave_mdb->query($sql);
            $slave_mdb->fetch();
            $template_text = $use_bundle_text." : 기본배송비 ".number_format($template_array[$i][delivery_cnt_price])." 원 ".number_format($slave_mdb->dt[delivery_price])." 개 이상시 ".number_format($slave_mdb->dt[delivery_basic_terms])." 원 배송비 적용 ";
            break;
        case '5':
            $sql = "select * from shop_delivery_address where addr_ix = '".$template_array[$i][factory_info_addr_ix]."'";
            $slave_mdb->query($sql);
            $slave_mdb->fetch();

            $template_text = $use_bundle_text." : 출고지별 배송비 ( ".$slave_mdb->dt[addr_name]." ) ";
            break;
        case '6':
            $template_text = $use_bundle_text." : 상품 1개단위 배송비 ".number_format($template_array[$i][delivery_unit_price])." 원 ";
            break;
    }


    if($type == 'policy'){	//배송정책
        return $template_text;

    }if($type == 'exchange_addr'){	//반품배송주소

        $sql = "select
					da.zip_code,
					da.address_1,
					da.address_2
				from
					shop_delivery_template as dt
					inner join shop_delivery_address as da on (dt.exchange_info_addr_ix = da.addr_ix)
				where
					dt.dt_ix = '".$dt_ix."'";
        $slave_mdb->query($sql);
        $slave_mdb->fetch();

        if($slave_mdb->total > 0){
            $exchange_addr = $slave_mdb->dt[zip_code]." ".$slave_mdb->dt[address_1]."".$slave_mdb->dt[address_2];
        }else{
            $sql="select
					da.zip_code,
					da.address_1,
					da.address_2
				from
					shop_product as p
					left join shop_delivery_address as da on (p.admin = da.company_id)
				where
					p.id = '".$pid."'
					and da.delivery_type = 'E'
					and da.basic_addr_use = 'Y'";
            $slave_mdb->query($sql);
            $slave_mdb->fetch();
            $exchange_addr = $slave_mdb->dt[zip_code]." ".$slave_mdb->dt[address_1]."".$slave_mdb->dt[address_2];
        }
        return $exchange_addr;
    }else if($type == 'return_shipping_price'){		//교환,반품 배송비 return_shipping_cnt


        if($template_array[0][delivery_policy] == '1'){	//무료배송일경우 왕복/편도 곱하기 , 아닐경우 편도만
            $return_shipping_price = $template_array[0][return_shipping_price] * $template_array[0][return_shipping_cnt];

        }else{
            $return_shipping_price = $template_array[0][return_shipping_price];
        }

        if($template_array[0][return_shipping_cnt] == '2'){
            $return_shipping_cnt = "편도";	//무조건 편도로 나와야 된다고 해서 편도로 했음 2014-07-31 이학봉
        }else{
            $return_shipping_cnt = "편도";
        }

        $return_price = " 교환 : ".number_format($template_array[0][exchange_shipping_price] * 2)."원 / 반품(".$return_shipping_cnt.") :".number_format($return_shipping_price)."원 (무료배송인 경우 왕복배송비 구매자 부담)";

        return $return_price;
    }else{

    }

}


if (!function_exists('is_mobile')) {
    /**
     * 모바일체크
     *
     * @return boolean
     *
     * @date 2013-06-18 bgh
     */
    function is_mobile(){
        $is_mobile = false;

        //PC에서도 확인할 수 있도록
        if(strtolower(substr($_SERVER["HTTP_HOST"],0,2)) == "m."){
            $is_mobile = true;

            //m 도메인 접근시 PC버전 사용 세션 제거
            $_SESSION['use_pc_version'] = 'N';
        }

        //PC버전 사용시 FALSE 리턴
        if($_SESSION['use_pc_version'] == 'Y'){
            return false;
        }

        //agent check
        $mobile_agent_list = array("/android/","/iphone/","/mobile/","/blackberry/","/windows ce/","/lg/","/samsung/","/sonyericsson/");
        foreach($mobile_agent_list as $key => $value):
            if(preg_match($value,strtolower($_SERVER["HTTP_USER_AGENT"]))){
                $is_mobile = true;
            }
        endforeach;

        //webview check
        if($_SERVER["HTTP_USER_AGENT"] == 'webview'){
            $is_mobile = true;
            $_SESSION['is_webview'] = true;
            //syslog(LOG_INFO,'webview session id : '.session_id());
        }else{
            $_SESSION['is_webview'] = false;
        }

        return $is_mobile;
    }
}

function getBrowser()
{
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version = "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    } elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }

    // Next get the name of the useragent yes seperately and for good reason
    if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    } elseif (preg_match('/Firefox/i', $u_agent)) {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    } elseif (preg_match('/Chrome/i', $u_agent)) {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    } elseif (preg_match('/Safari/i', $u_agent)) {
        $bname = 'Apple Safari';
        $ub = "Safari";
    } elseif (preg_match('/Opera/i', $u_agent)) {
        $bname = 'Opera';
        $ub = "Opera";
    } elseif (preg_match('/Netscape/i', $u_agent)) {
        $bname = 'Netscape';
        $ub = "Netscape";
    }

    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    //if (!preg_match_all($pattern, $u_agent, $matches)) {
    // we have no matching number just continue
    //}

    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
            $version = $matches['version'][0];
        } else {
            $version = $matches['version'][1];
        }
    } else {
        $version = $matches['version'][0];
    }

    // check if we have a number
    if ($version == null || $version == "") {
        $version = "?";
    }
    return array('userAgent' => $u_agent, 'name' => $bname, 'version' => $version, 'platform' => $platform, 'pattern' => $pattern);
}


//cowell 모바일 카테고리
function getCategoryForMobile($depth = "0",$cid = "")
{
    global $db;

    if($depth == "0")
    {
        $strWhere = "WHERE category_use = 1 AND depth = " . $depth ;
        $strWhere .= " ORDER BY vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ";

    }
    else
    {
        if($depth == "1")
            $cid = substr($cid,0,3);
        elseif($depth == "2")
            $cid = substr($cid,0,6);
        elseif($depth == "3")
            $cid = substr($cid,0,9);

        $strWhere = "WHERE category_use = 1 AND depth = " . $depth . " AND cid like '" . $cid . "%' ";

        $strWhere .= " ORDER BY vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ";
    }

    $sql = "SELECT * FROM " .TBL_SHOP_CATEGORY_INFO . " " . $strWhere;

    $db->query($sql);

    $data = $db->fetchAll();

    if(is_array($data)){
        foreach($data as $key => $val){
            $data[$key]["cname"]=getGlobalTargetName($val["cname"],$val["global_cinfo"],'cname');
            $data[$key]["total_cnt"]=$db->total();
        }

    }

    return $data;
}

function getCategoryForMobileDCG($depth = "0",$cid = "")
{
    global $db;

    if($depth == "0")
    {
        $strWhere = "WHERE category_use = 1 AND depth = " . $depth ;
        $strWhere .= " ORDER BY vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ";

    }
    else
    {
        if($depth == "1")
            $cid = substr($cid,0,3);
        elseif($depth == "2")
            $cid = substr($cid,0,6);
        elseif($depth == "3")
            $cid = substr($cid,0,9);

        $strWhere = "WHERE category_use = 1 AND depth = " . $depth . " AND cid like '" . $cid . "%' ";

        $strWhere .= " ORDER BY vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ";
    }

    $sql = "SELECT * FROM " .TBL_SHOP_CATEGORY_INFO . " " . $strWhere;

    $db->query($sql);

    $data = $db->fetchAll();

    $return = array();
    $return[0]["cname"]='전체';
    $return[0]["cid"]='ALL';
    $return[0]["total_cnt"]=0;
    $i=1;
    if(is_array($data)){
        foreach($data as $key => $val){
            $return[$i]["cid"]=$val["cid"];
            $return[$i]["cname"]=getGlobalTargetName($val["cname"],$val["global_cinfo"],'cname');
            $return[$i]["total_cnt"]=$db->total();
            $i++;
        }

    }
    //print_r($data);
    return $return;
}
//cowell 브랜드 가져오기.
/*

function getBrandInitial()
{
	global $db;

	$sql = "select left(brand_name_division,1) as initial from shop_brand group by left(brand_name_division,1) order by brand_name_division asc";

	$db->query($sql);

	return $db->fetchall('object');
}
*/

/* 글로벌 관련 함수 2014-10-17 HONG - 추가 JK20151028 */

function getOrderSecondYN(){
    $db = new MySQL;

    if($_SESSION[admininfo][mall_ix]){
        $mall_ix=$_SESSION[admininfo][mall_ix];
    }else{
        $mall_ix=$_SESSION[layout_config][mall_ix];
    }


    $db->query("SELECT config_value FROM `shop_mall_config` where mall_ix = '".$mall_ix."' and config_name='second_order'  ");
    $db->fetch();

    if($db->dt[config_value]=="Y")		return true;
    else								return false;

}

function getGlobalBBSName($bbs_name,$language = ""){

    if( empty($language)){
        //$language = $_SESSION["layout_config"]["front_language"];
        $language = "korea";
    }

    switch($bbs_name) {
        case 'notice':
            if($language == 'english'){
                return 'en_notice';
            }else{
                return 'notice';
            }
            break;

        case 'qna':
            if($language == 'english'){
                return 'en_qna';
            }else{
                return 'qna';
            }
            break;

        case 'faq':
            if($language == 'english'){
                return 'en_faq';
            }else{
                return 'faq';
            }
            break;

    }
}

function getGlobalTargetName($target_name,$global_json,$colum,$language = ""){

    if( empty($language)){
        $language = $_SESSION["layout_config"]["front_language"];
    }

    $global_json = json_decode($global_json,true);
    if($global_json == "null"){
        return $target_name;
    }
    $global_target_name = trim(urldecode($global_json[$colum][$language]));
    if(!empty($global_target_name)){
        return $global_target_name;
    }else{
        return $target_name;
    }
}

function getExchangeNationRate(){
    global $EXCHANGE_RATE_ARRAY;

    include_once($_SERVER["DOCUMENT_ROOT"]."/admin/include/lib/number.function.php");

    if(empty($EXCHANGE_RATE_ARRAY)){
        $db = new MySQL;
        $db->query("select exchange_rate as usd from global_currency  where disp = '1' and currency_code = 'USD' ");
        $db->fetch();
        $EXCHANGE_RATE_ARRAY = $db->dt;
    }

    $rate_type = 'usd';
    return $EXCHANGE_RATE_ARRAY[$rate_type];
}

function getExchangeNationPrice($price, $numFormatBool=false, $input_exchange_rate=false){
    include_once($_SERVER["DOCUMENT_ROOT"]."/admin/include/lib/number.function.php");

    if($_SESSION["layout_config"]["front_language"]=="korean" || $_SESSION["layout_config"]["front_language"]=="Japanese"){
        return ($numFormatBool ? getExchangeNationPriceNumberFormat($price) : $price );
    }

    if($input_exchange_rate === false){
        $exchange_rate = getExchangeNationRate();
    }else{
        $exchange_rate = $input_exchange_rate;
    }
    $return_priece=round($price/$exchange_rate,2);

    return ($numFormatBool ? getExchangeNationPriceNumberFormat($return_priece) : $return_priece );
}

function format_korean($money,$step = 0)
{
    $formater    = array("원","만","억","조","경","해","자");
    $tmp_mon    = (strlen($money)>4) ? format_korean(substr($money,0,strlen($money)-4),$step+1) : "";
    $curmoney    = intval(substr($money,-4));
    if($curmoney) $tmp_mon.= sprintf("%s%s",number_format($curmoney),$formater[$step]);
    return $tmp_mon;
}

function getExchangeNationPriceNumberFormat($price){

    if($_SESSION["layout_config"]["front_language"]=="english"){
        $return_price = number_format($price,2);
    }else{
        $return_price = number_format($price);
    }
    return $return_price;
}

/*사은품 정보 가져오기 JK151112*/
function gift_product_list($cart_price,$gift_key){
    global $db,$layout_config;

    $sql = "select
				config_value
			from
				shop_mall_config
			where
				mall_ix = '".$layout_config['mall_ix']."'
				and config_value is not null
				and config_name = 'gift_selling'";

    $db->query($sql);
    $db->fetch();
    $gift_selling = $db->dt['config_value'];

    if(substr_count($_SERVER["PHP_SELF"],"/shop/infoinput.php") > 0 ){
        $eventBool = 1;
    }else{
        $eventBool = 0;
    }
    if($gift_selling == 'Y'){

        $sql = "select * from ".TBL_SHOP_PRODUCT." where state='1' and disp = '1' and product_type='77' and '".$cart_price."' between gift_sprice and gift_eprice";
        //echo $sql;
        $db->query($sql);

        if($db->total){
            if(is_mobile()){
                $mstring .='<div class="" >';
                $mstring .='	<ul>';
                $mstring .='		<li>';
                $mstring .='			<h3 class="cart_daiso_title2 " style="margin-top:10px;border-bottom:0px;">사은품 선택</h3>';
                $mstring .='		</li>';
                $mstring .='	</ul>';
                $mstring .='</div>';
                $mstring .='<div class="infoinput_sale">';
                $mstring .='	<ul>';
                $mstring .='		<li class="infoinput_buttom" style="padding:0px;">';
                $mstring .='			<table cellspacing="0" cellpadding="0" border="0" width="100%">';
                $mstring .='				<tr>';
                $mstring .='					<td style="text-align:left;">';
                $mstring .='						<select class="" name="gift_product" style="width: 200px; padding: 2px 0px; border:1px solid;" onchange="view_img($(this).find(\'option:selected\').attr(\'imgsrc\'))" '.($gift_key != '' ? "disabled" :"").'>';
                $mstring .='							<option value="">== 선택하세요 ==</option>';

                for($i=0; $i < $db->total; $i++){
                    $db->fetch($i);
                    if($db->dt[stock_use_yn] == 'Y' || $db->dt[stock_use_yn] == 'Q'){
                        if(($db->dt[stock]-$db->dt[sell_ing_cnt]) > 0){
                            $mstring .='									<option value="'.$db->dt[id].'" imgsrc="'.PrintImage($_SESSION["layout_config"][mall_product_imgpath]."", $db->dt[id], "c").'" '.($gift_key == $db->dt[id] ? "selected":"").'>'.$db->dt[pname].'</option>';
                        }
                    }else{
                        $mstring .='									<option value="'.$db->dt[id].'" imgsrc="'.PrintImage($_SESSION["layout_config"][mall_product_imgpath]."", $db->dt[id], "c").'" '.($gift_key == $db->dt[id] ? "selected":"").'>'.$db->dt[pname].'</option>';
                    }
                }
                $mstring .='					</td>';
                $mstring .='					<td style="text-align:right; padding-right:5px;" id="view_array">';
                if($gift_key !=''){
                    $mstring .='						<img src="'.PrintImage($_SESSION["layout_config"][mall_product_imgpath]."", $gift_key, "c").'">';
                }
                $mstring .='					</td>';
                $mstring .='				</tr>';
                $mstring .='			</table>';
                $mstring .='		</li>';
                $mstring .='	</ul>';
                $mstring .='</div>';
            }else{
                $mstring ='<h2>';
                $mstring .='	사은품 선택';
                $mstring .='</h2>';
                $mstring .='<div class="wrap-view-gift">';
                $mstring .='	<select class="" name="gift_product" style="width:200px; padding:2px 0;" onchange="view_img($(this).find(\'option:selected\').attr(\'imgsrc\'))" '.($gift_key != '' ? "disabled" :"").'>';
                $mstring .='		<option value="">== 선택하세요 ==</option>';
                for($i=0; $i < $db->total; $i++){
                    $db->fetch($i);
                    if($db->dt[stock_use_yn] == 'Y' || $db->dt[stock_use_yn] == 'Q'){
                        if(($db->dt[stock]-$db->dt[sell_ing_cnt]) > 0){
                            $mstring .='		<option value="'.$db->dt[id].'" imgsrc="'.PrintImage($_SESSION["layout_config"][mall_product_imgpath]."", $db->dt[id], "s").'" '.($gift_key == $db->dt[id] ? "selected":"").'>'.$db->dt[pname].'</option>';
                        }
                    } else {
                        $mstring .='				<option value="'.$db->dt[id].'" imgsrc="'.PrintImage($_SESSION["layout_config"][mall_product_imgpath]."", $db->dt[id], "s").'" '.($gift_key == $db->dt[id] ? "selected":"").'>'.$db->dt[pname].'</option>';
                    }

                }
                $mstring .='	</select>';

                $mstring .='	<div class="view_gift" '.($gift_key == '' ? "style='display:none;'" :"").'>';
                $mstring .='		<span id="view_array">';
                if($gift_key !=''){
                    $mstring .='		<img src="'.PrintImage($_SESSION["layout_config"][mall_product_imgpath]."", $gift_key, "s").'">';
                }
                $mstring .='		</span>';
                $mstring .='	</div>';
                $mstring .='</div>';

            }
            //echo PrintImage($_SESSION["layout_config"][mall_product_imgpath].",'0000226150','ms'.");
            return $mstring;
        }else{
            return false;
        }
    }else{
        return false;
    }

}


function UsableCupons2($pid , $sellprice, $pcount, $limit=100, $data_type="html"){
    global $product_price, $user;
    $mdb = new Database;

    $cupon_div_where = "";

    if($mdb->dbms_type == "oracle"){

        $sql = "
							select 5 as qnum, cupon_sale_type, cupon_sale_value,  cupon_kind,  cp.* , 
							case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price and publish_limit_price > 0 then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice, ".$sellprice*$pcount." as product_price
							from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp, ".TBL_SHOP_CUPON_RELATION_PRODUCT." crp
							where c.cupon_ix = cp.cupon_ix
							
							and cp.publish_ix = crp.publish_ix
							and crp.pid = '$pid' and cp.use_product_type = 3 and cp.publish_type in (1, 2, 4)
							and ((cp.use_date_type!='9' AND TO_DATE('".date("m-d-Y H:i:s")."','MM-DD-YYYY HH24:MI:SS') between cp.use_sdate and cp.use_edate) OR cp.use_date_type=9 OR cp.use_date_type=2)
							and cp.disp='1'
							and ".time()." between cupon_use_sdate and cupon_use_edate
							";
        //and cp.publish_condition_price <= ".$sellprice*$pcount."


        $sql .= "union
								select  6 as qnum, cupon_sale_type, cupon_sale_value,  cupon_kind,  cp.*	, 
								case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price and publish_limit_price > 0 then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice, ".$sellprice*$pcount." as product_price
								from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp
								where c.cupon_ix = cp.cupon_ix 
								and cp.use_product_type = 1 and cp.publish_type in (1, 2, 4)
								and ((cp.use_date_type!='9' AND TO_DATE('".date("m-d-Y H:i:s")."','MM-DD-YYYY HH24:MI:SS') between cp.use_sdate and cp.use_edate) OR cp.use_date_type=9 OR cp.use_date_type=2)
								and cp.disp='1'
								and ".time()." between cupon_use_sdate and cupon_use_edate
								";
        //and cp.publish_condition_price <= ".$sellprice*$pcount."

        $sql .= "union
								select  7 as qnum, cupon_sale_type, cupon_sale_value,  cupon_kind,  cp.*,
								case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price and publish_limit_price > 0 then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice, ".$sellprice*$pcount." as product_price
								from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp, ".TBL_SHOP_CUPON_RELATION_CATEGORY." crc, ".TBL_SHOP_PRODUCT_RELATION." pr
								where c.cupon_ix = cp.cupon_ix 
								and cp.publish_ix = crc.publish_ix and SUBSTRING(crc.cid,1,(crc.depth+1)*3) = SUBSTRING(pr.cid,1,(crc.depth+1)*3)
								and pr.pid = '$pid' and cp.use_product_type = 2 and cp.publish_type in (1, 2, 4)
								and ((cp.use_date_type!='9' AND TO_DATE('".date("m-d-Y H:i:s")."','MM-DD-YYYY HH24:MI:SS') between cp.use_sdate and cp.use_edate) OR cp.use_date_type=9 OR cp.use_date_type=2)
								and cp.disp='1'
								and ".time()." between cupon_use_sdate and cupon_use_edate
								";
        //and cp.publish_condition_price <= ".$sellprice*$pcount."

        $sql .= "union
								select 8 as qnum, cupon_sale_type, cupon_sale_value,  cupon_kind,  cp.*, 
								case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price and publish_limit_price > 0 then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice, ".$sellprice*$pcount." as product_price
								from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp, ".TBL_SHOP_CUPON_RELATION_BRAND." crb, ".TBL_SHOP_PRODUCT." p
								where c.cupon_ix = cp.cupon_ix 
								and cp.publish_ix = crb.publish_ix and crb.b_ix = p.brand
								and p.id = '$pid' and cp.use_product_type = 4 and cp.publish_type in (1, 2, 4)
								and ((cp.use_date_type!='9' AND TO_DATE('".date("m-d-Y H:i:s")."','MM-DD-YYYY HH24:MI:SS') between cp.use_sdate and cp.use_edate) OR cp.use_date_type=9 OR cp.use_date_type=2)
								and cp.disp='1'
								and ".time()." between cupon_use_sdate and cupon_use_edate
								";
        //and cp.publish_condition_price <= ".$sellprice*$pcount."

        $sql .= " order by saleprice desc  ";
    }else{

        $sql = "
							select cp.* , 5 as qnum, cupon_sale_type, cupon_sale_value,  cupon_kind,  
							case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price and publish_limit_price > 0 then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice, ".$sellprice*$pcount." as product_price
							from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp, ".TBL_SHOP_CUPON_RELATION_PRODUCT." crp
							where c.cupon_ix = cp.cupon_ix							
							and cp.publish_ix = crp.publish_ix
							and crp.pid = '$pid' and cp.use_product_type = 3 and cp.publish_type in (1, 2, 4)
							and (
								(cp.use_date_type = '3' AND '".date("Y-m-d H:i:s")."' between cp.use_sdate and cp.use_edate) 
								OR (cp.use_date_type = '1' AND '".date("Y-m-d H:i:s")."' between cp.regdate and 
								case 
								when publish_date_type = '1' then date_add(cp.regdate, INTERVAL cp.publish_date_differ YEAR )
								when publish_date_type = '2' then date_add(cp.regdate, INTERVAL cp.publish_date_differ MONTH )
								when publish_date_type = '3' then date_add(cp.regdate, INTERVAL cp.publish_date_differ DAY)
								end)
								OR cp.use_date_type=9 
								OR cp.use_date_type=2
								)
							and cp.disp='1'
							and ".time()." between cupon_use_sdate and cupon_use_edate
							".$cupon_div_where."
							";
        //and cp.publish_condition_price <= ".$sellprice*$pcount."

        $sql .= "union
							select cp.*	,  6 as qnum, cupon_sale_type, cupon_sale_value,  cupon_kind,  case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price and publish_limit_price > 0 then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice, ".$sellprice*$pcount." as product_price
							from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp
							where c.cupon_ix = cp.cupon_ix 
							and cp.use_product_type = 1 and cp.publish_type in (1, 2, 4)
							and (
								(cp.use_date_type = '3' AND '".date("Y-m-d H:i:s")."' between cp.use_sdate and cp.use_edate) 
								OR (cp.use_date_type = '1' AND '".date("Y-m-d H:i:s")."' between cp.regdate and 
								case 
								when publish_date_type = '1' then date_add(cp.regdate, INTERVAL cp.publish_date_differ YEAR )
								when publish_date_type = '2' then date_add(cp.regdate, INTERVAL cp.publish_date_differ MONTH )
								when publish_date_type = '3' then date_add(cp.regdate, INTERVAL cp.publish_date_differ DAY)
								end)
								OR cp.use_date_type=9 
								OR cp.use_date_type=2
								)
							and cp.disp='1'
							and ".time()." between cupon_use_sdate and cupon_use_edate
							".$cupon_div_where."
							";
        //and cp.publish_condition_price <= ".$sellprice*$pcount."
        $sql .= "union
							select cp.*, 7 as qnum, cupon_sale_type, cupon_sale_value,  cupon_kind,  
							case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price and publish_limit_price > 0 then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice, ".$sellprice*$pcount." as product_price
							from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp, ".TBL_SHOP_CUPON_RELATION_CATEGORY." crc, ".TBL_SHOP_PRODUCT_RELATION." pr
							where c.cupon_ix = cp.cupon_ix 
							and cp.publish_ix = crc.publish_ix and SUBSTRING(crc.cid,1,(crc.depth+1)*3) = SUBSTRING(pr.cid,1,(crc.depth+1)*3)
							and pr.pid = '$pid' and cp.use_product_type = 2 and cp.publish_type in (1, 2, 4)
							and (
								(cp.use_date_type = '3' AND '".date("Y-m-d H:i:s")."' between cp.use_sdate and cp.use_edate) 
								OR (cp.use_date_type = '1' AND '".date("Y-m-d H:i:s")."' between cp.regdate and 
								case 
								when publish_date_type = '1' then date_add(cp.regdate, INTERVAL cp.publish_date_differ YEAR )
								when publish_date_type = '2' then date_add(cp.regdate, INTERVAL cp.publish_date_differ MONTH )
								when publish_date_type = '3' then date_add(cp.regdate, INTERVAL cp.publish_date_differ DAY)
								end)
								OR cp.use_date_type=9 
								OR cp.use_date_type=2
								)
							and cp.disp='1'
							and ".time()." between cupon_use_sdate and cupon_use_edate
							".$cupon_div_where."
							";
        //and cp.publish_condition_price <= ".$sellprice*$pcount."
        $sql .= "union
							select cp.*, 8 as qnum, cupon_sale_type, cupon_sale_value,  cupon_kind,  
							case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice, ".$sellprice*$pcount." as product_price
							from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp, ".TBL_SHOP_CUPON_RELATION_BRAND." crb, ".TBL_SHOP_PRODUCT." p
							where c.cupon_ix = cp.cupon_ix 
							and cp.publish_ix = crb.publish_ix and crb.b_ix = p.brand
							and p.id = '$pid' and cp.use_product_type = 4 and cp.publish_type in (1, 2, 4)
							and (
								(cp.use_date_type = '3' AND '".date("Y-m-d H:i:s")."' between cp.use_sdate and cp.use_edate) 
								OR (cp.use_date_type = '1' AND '".date("Y-m-d H:i:s")."' between cp.regdate and 
								case 
								when publish_date_type = '1' then date_add(cp.regdate, INTERVAL cp.publish_date_differ YEAR )
								when publish_date_type = '2' then date_add(cp.regdate, INTERVAL cp.publish_date_differ MONTH )
								when publish_date_type = '3' then date_add(cp.regdate, INTERVAL cp.publish_date_differ DAY)
								end)
								OR cp.use_date_type=9 
								OR cp.use_date_type=2
								)
							and cp.disp='1'
							and ".time()." between cupon_use_sdate and cupon_use_edate
							".$cupon_div_where."
							";
        //and cp.publish_condition_price <= ".$sellprice*$pcount."
        //특정 셀러에 발행된 쿠폰
        $sql .= "union
							select cp.*, 9 as qnum, cupon_sale_type, cupon_sale_value,  cupon_kind,  
							case when (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) > publish_limit_price then publish_limit_price else (case when cupon_sale_type = 1 then cupon_sale_value/100*".$sellprice*$pcount." else cupon_sale_value end) end as saleprice, ".$sellprice*$pcount." as product_price
							from ".TBL_SHOP_CUPON." c , ".TBL_SHOP_CUPON_PUBLISH." cp, shop_cupon_relation_seller crs, ".TBL_SHOP_PRODUCT." p
							where c.cupon_ix = cp.cupon_ix 							
							and cp.publish_ix = crs.publish_ix and crs.company_id = p.admin
							and p.id = '$pid' and cp.use_product_type = 5 and cp.publish_type in (1, 2, 4)
							and (
								(cp.use_date_type = '3' AND '".date("Y-m-d H:i:s")."' between cp.use_sdate and cp.use_edate) 
								OR (cp.use_date_type = '1' AND '".date("Y-m-d H:i:s")."' between cp.regdate and 
								case 
								when publish_date_type = '1' then date_add(cp.regdate, INTERVAL cp.publish_date_differ YEAR )
								when publish_date_type = '2' then date_add(cp.regdate, INTERVAL cp.publish_date_differ MONTH )
								when publish_date_type = '3' then date_add(cp.regdate, INTERVAL cp.publish_date_differ DAY)
								end)
								OR cp.use_date_type=9 
								OR cp.use_date_type=2
								)
							and cp.disp='1'
							and ".time()." between cupon_use_sdate and cupon_use_edate
							".$cupon_div_where."
							";
        $sql = "select data.* , cpc.cpc_ix, cpc.r_ix from (".$sql.") data 
						left join shop_cupon_publish_config cpc on data.publish_ix = cpc.publish_ix 
						having (data.publish_type = '4' and cpc.r_ix = '".$_SESSION["user"]["gp_ix"]."') or (data.publish_type = '1' and cpc.r_ix = '".$_SESSION["user"]["code"]."') or data.publish_type = 2 ";
        //
        $sql .= " order by saleprice desc limit 0,$limit  ";

    }

    //echo nl2br($sql);
//	exit;

    $mdb->query($sql);
    $result=$mdb->fetchall();

    if($result){
        //print_r($result);
        foreach ($result as $key => $sub_array) {
            //$select_ = array("icons_list"=>explode(";",$sub_array[icons]));
            //array_insert($sub_array,14,$select_);
            $sql = "select count(cr.regist_ix) AS cnt from ".TBL_SHOP_CUPON_PUBLISH." cp ,".TBL_SHOP_CUPON_REGIST." cr where cr.publish_ix = cp.publish_ix and cp.publish_ix ='".$sub_array["publish_ix"]."' AND cr.mem_ix='".$_SESSION["user"]["code"]."' ";
            $mdb->query($sql);
            $mdb->fetch();
            $cnt=$mdb->dt["cnt"];
            $select_ = array("cupon_regist_yn"=>$cnt);
            array_insert($sub_array,14,$select_);

            if($sub_array[use_date_type] == 1){
                if($sub_array[publish_date_type] == 1){
                    $date_type = '년';
                }else if($sub_array[publish_date_type] == 2){
                    $date_type = '개월';
                }else if($sub_array[publish_date_type] == 3){
                    $date_type = '일';
                }
                $date_differ = $sub_array[publish_date_differ];
                $use_date_type = '발행일';
                $priod_str = $use_date_type."(".substr($sub_array["regdate"],0,10).")로부터 ".$date_differ." ".$date_type."간";

            }else if($sub_array[use_date_type] == 2){
                if($sub_array[regist_date_type] == 1){
                    $date_type = '년';
                }else if($sub_array[regist_date_type] == 2){
                    $date_type = '개월';
                }else if($sub_array[regist_date_type] == 3){
                    $date_type = '일';
                }
                $date_differ = $sub_array[regist_date_differ];
                $use_date_type = '등록일';
                $priod_str = $use_date_type."로부터 ".$date_differ." ".$date_type."간";
            }else if($sub_array[use_date_type] == 3){
                $use_date_type = '사용기간';
                //echo $sub_array[use_sdate]."~".$sub_array[use_edate];
                $priod_str = " ".date("Y-m-d",strtotime($sub_array[use_sdate]))." ~ ".date("Y-m-d",strtotime($sub_array[use_edate]))." ";
            }else if($sub_array[use_date_type] == 9){
                $use_date_type = '사용기간';
                $priod_str = "기간제한없음";
            }

            $select2_ = array("use_priod"=>$priod_str);
            array_insert($sub_array,15,$select2_);
            $result[$key] = $sub_array;
        }
    }

    /*if($data_type == "array"){
		$datas = $mdb->fetchall();
		return $datas[0];
		exit;
	}

	$mstring =  "<table cellpadding=0 cellspacing=0 width=400>";

	if($mdb->total){
		$mstring .= "       <tr height=23 bgcolor=#efefef align=center><td width=70 >할인금액</td><td >쿠폰명</td><!--td>쿠폰번호</td--></tr>";

		for($i=0;$i < $mdb->total;$i++){
		 	$mdb->fetch($i);

		 	$saleprice = ($mdb->dt[cupon_sale_type] == 1 ? intval($mdb->dt[cupon_sale_value]/100*$sellprice*$pcount):$mdb->dt[cupon_sale_value]);

		 	$mstring .= "       <tr height=23 ><td style='padding:0 10 0 0' align=right>".number_format($mdb->dt[saleprice])." 원</td><td >".$mdb->dt[cupon_kind]." </td><!--td>".$mdb->dt[cupon_no]." </td--></tr>";

		}
	}else{
		$mstring .=  "     <tr><td>쿠폰선택</td></tr>";
	}

	$mstring .= "</table>";*/

    return $result;


    // return $mstring;


}

function mall_reseller_use()
{
    $php_self = explode("/", $_SERVER["PHP_SELF"]);
    $url = $php_self[1];
    $self = $php_self[2];

    if($url == "mypage")
    {
        //[S] reseller.lib이 두번 include 되면 안되므로 reseller.lib이 선언된 파일은 제외하고 include

        $reseller_inc = array(
            "account_expect.php",
            "reseller_member.php",
            "account_check.php"
        );

        //[E] reseller.lib이 두번 include 되면 안되므로 reseller.lib이 선언된 파일은 제외하고 include

        if(!in_array($self, $reseller_inc))
        {
            @include ($_SERVER["DOCUMENT_ROOT"]."/admin/reseller/reseller.lib.php");
            $reseller_data = resellerShared("select");
            if(empty($reseller_data["rsl_use"])){
                $rsl_use = "n";
            }else{
                $rsl_use = $reseller_data["rsl_use"];
            }
        }else{
            $rsl_use = "y";
        }

        $_SESSION["layout_config"]["mall_reseller_yn"] = $rsl_use;

        echo $rsl_use;
    }
    /*
	if(empty($_SESSION["layout_config"]["mall_reseller_yn"]))
	{
		//[S] reseller.lib이 두번 include 되면 안되므로 reseller.lib이 선언된 파일은 제외하고 include

		$reseller_inc = array(
			"account_expect.php",
			"reseller_member.php",
			"account_check.php"
		);

		$php_self = explode("/", $_SERVER["PHP_SELF"]);
		$php_self = $php_self[2];

		if(!in_array($php_self, $reseller_inc)){
			@include ($_SERVER["DOCUMENT_ROOT"]."/admin/reseller/reseller.lib.php");
		}

		//[E] reseller.lib이 두번 include 되면 안되므로 reseller.lib이 선언된 파일은 제외하고 include

		$reseller_data = resellerShared("select");

		if(empty($reseller_data["rsl_use"])){
			$rsl_use = "n";
		}else{
			$rsl_use = $reseller_data["rsl_use"];
		}

	}else{
		$rsl_use = "n";
	}

	$_SESSION["layout_config"]["mall_reseller_yn"] = $rsl_use;
*/
}

function show_link_minishop($pid){
    global $mdb;

    $sql = "select p.admin, sd.minishop_use, sd.shop_name from ".TBL_SHOP_PRODUCT." p LEFT JOIN ".TBL_COMMON_SELLER_DETAIL." sd on p.admin = sd.company_id where p.id = '".$pid."'";
    $mdb->query($sql);
    $mdb->fetch();
    $minishop_use = $mdb->dt[minishop_use];
    $admin = $mdb->dt[admin];
    $shop_name = $mdb->dt[shop_name];
    $html = '';

    if($minishop_use !='0'){
        if($admin == _HEAD_OFFICE_CODE || $admin == "c72e0d088cbfdd30452ca85472739747"){
            $html .='<a class=goods_mini href="/online/online_main.php">';
        }else{
            $html .='<a class=goods_mini href="/minishop/main.php?company_id='.$admin.'">';
        }
        $html .='	'.$shop_name.'';
        $html .='</a>';
    }
    return $html;
}

function blindString($len, $sign, $str){
    // $str 문자열을 $len을 제외하고 $sign문자로 변경
    // $len = 3, $sing = *, $str = forbiz => for***
    $cut_len = mb_strlen($str, "utf-8");
    $dot = "";
    for($i=0; $i<($cut_len - $len); $i++){
        $dot .= $sign;
    }
    $replace = str_replace(substr($str, $len, $cut_len), $dot, $str);
    return $replace;
}
function getMemberLevelText($level_ix)
{
    $mdb = new Database;

    $sql = "SELECT * FROM shop_level where level_ix = '".$level_ix."'";

    $mdb->query($sql);

    if ($mdb->total){
        $mdb->fetch($i);
        return $mdb->dt[lv_name];
    }
}


$_LANGUAGE_CHECK_TRANS_KEY = array();
function getLanguageText($trans_key){
    global $_LANGUAGE,$_LANGUAGE_CHECK_TRANS_KEY,$master_db;

    //운영 중일떄는 false 로 해야함
    //if(false){
    if( ! $_LANGUAGE_CHECK_TRANS_KEY[$trans_key] && true ){
        $sql="update global_translation set call_cnt = ifnull(call_cnt,0) + 1 where trans_key = '".$trans_key."' ";
        $master_db->query($sql);

        //호출 페이지당 한번만 업데이트하기 위해서
        $_LANGUAGE_CHECK_TRANS_KEY[$trans_key]=true;
    }

    return htmlspecialchars_decode($_LANGUAGE[$trans_key]);
}

function fetch_product_topnine($id, $cid, $limit_num=9){
// 인기 상품 TOP 9 ( 최근 2주간 판매량이 높은 순 )
    $mdb = new Database;

    $sql = "select p.*
              from ".TBL_SHOP_PRODUCT." p
                 , ".TBL_SHOP_PRODUCT_RELATION." r
             where r.cid = '$cid' 
               and r.pid = p.id
               and r.basic = 1
               and p.disp = 1
          order by p.order_cnt desc limit 0, $limit_num";

    $mdb->query($sql);

    if($mdb->total != 0){
        $topnine = $mdb->fetchall();

        $script_times["pinfo_query_end"] = time();

        for($i = 0 ; $i < count($topnine) ; $i++){
            $goods_infos[$topnine[$i][id]][pid] = $topnine[$i][id];
            $goods_infos[$topnine[$i][id]][amount] = $topnine[$i][pcount];
            $goods_infos[$topnine[$i][id]][cid] = $topnine[$i][cid];
            $goods_infos[$topnine[$i][id]][depth] = $topnine[$i][depth];
        }

        $script_times["pinfo_discount_query_start"] = time();
        $discount_info = DiscountRult($goods_infos);//, $topnine[0][cid], $topnine[0][depth]
        $script_times["pinfo_discount_query_end"] = time();

        foreach ($topnine as $key => $sub_array) {
            $select_ = array("icons_list"=>explode(";",$sub_array[icons]));
            array_insert($sub_array,14, $select_);

            $discount_item = $discount_info[$sub_array[id]];
            $_dcprice = $sub_array[sellprice];

            if(is_array($discount_item)){
                foreach($discount_item as $_key => $_item){
                    if($_item[discount_value_type] == "1"){ // %
                        //echo $_item[discount_value]."<br>";
                        $_dcprice = roundBetter($_dcprice*(100 - $_item[discount_value])/100, $_item[round_position], $_item[round_type]) ;//$_dcprice*(100 - $_item[discount_value])/100;
                    }else if($_item[discount_value_type] == "2"){// 원
                        $_dcprice = $_dcprice - $_item[discount_value];
                    }
                    $_item[expectation_discount_price] = $sub_array[sellprice]-$_dcprice;
                    //$discount_desc[] = $_item[discount_type]."-".$_item[discount_value]." ".($_item[discount_value_type] == "1" ? "%":"원")."-".$_dcprice."원";
                    $discount_desc[] = $_item;

                }
            }

            $_dcprice = array("dcprice"=>$_dcprice);
            array_insert($sub_array,52,$_dcprice);
            $discount_desc = array("discount_desc"=>$discount_desc);
            array_insert($sub_array,53,$discount_desc);

            $topnine[$key] = $sub_array;
        }

    }

    if(count($topnine)){
        for($i=0 ; $i < count($topnine) ;$i++){
            $topnine[$i]['listprice'] = getExchangeNationPrice($topnine[$i]['listprice']);
            $topnine[$i]['sellprice'] = getExchangeNationPrice($topnine[$i]['sellprice']);
            $topnine[$i]['dcprice'] = getExchangeNationPrice($topnine[$i]['dcprice']);
        }
    }

    return $topnine;
}
function getTotalProductListEP(){

    $mdb = new Database;

    $qryTotalValidProduct = "
            SELECT COUNT(*) AS total
            FROM shop_product p
            LEFT JOIN shop_product_relation r ON p.id = r.pid and r.basic = 1
            LEFT JOIN shop_category_info c ON r.cid = c.cid
            WHERE state='1' 
            AND p.disp IN ('1','3') 
            and r.basic = 1
            AND p.state = '1' 
            AND category_use !='0' 
            AND IF(p.is_sell_date = '1', p.sell_priod_sdate <= '".date('Ymd')."' AND p.sell_priod_edate >= '".date('Ymd')."', '1=1')
            GROUP BY p.id ";

    $mdb->query($qryTotalValidProduct);
    $result = $mdb->fetchall();

    return count($result);
}

function getProductListEP($where, $start, $max, $orderby="", $categoryBool=false) {

    global $slave_mdb, $mall_id, $page;

    //글로벌 관련 상품 처리
    //$where .= " and p.mall_ix in ('','".$_SESSION["layout_config"]['mall_ix']."') ";

    if($groupby != ""){
        $groupby_where = " group by ".$groupby." ";
    }else{
        $groupby_where = " group by p.id ";
    }

    if($categoryBool){
        $category_where = "and r.cid LIKE '".substr($_GET['cid'],0,($_GET['depth']+1)*3)."%'";
    }


    if(UserSellingType() == 'W'){

        if(UserProductPriceType()=='L'){
            $select_price = 'wholesale_price as listprice, 
                             wholesale_price as sellprice, 
                             sellprice AS ori_sellprice, 
                             0 as sale_rate ,  
                             (listprice - sellprice) / listprice * 100 as b2c_sale_rate, 
                             pa.wholesale_free_delivery_yn as free_delivery_yn ';
        }else{
            $select_price = 'wholesale_price as listprice, 
                             wholesale_sellprice as sellprice, 
                             sellprice AS ori_sellprice, 
                             (wholesale_price - wholesale_sellprice) / wholesale_price * 100 as sale_rate ,  
                             (listprice-sellprice) / listprice * 100 as b2c_sale_rate , 
                             pa.wholesale_free_delivery_yn as free_delivery_yn ';
        }

    }else{

        if(UserProductPriceType()=='P'){
            $select_price = 'premiumprice as sellprice, 
                             listprice, 
                             (listprice - premiumprice) / listprice * 100 as sale_rate , 
                             pa.free_delivery_yn as free_delivery_yn ';

        }elseif(UserProductPriceType()=='L'){
            $select_price = 'listprice as sellprice, 
                             listprice, 
                             0 as sale_rate , 
                             pa.free_delivery_yn as free_delivery_yn ';
        }else{

            // 쿼리에 할인율 추가 sale_rate
            $select_price = 'sellprice, 
                             listprice, 
                             (listprice - sellprice) / listprice * 100 as sale_rate , 
                             pa.free_delivery_yn as free_delivery_yn ';
        }
    }


    $sql = 	"SELECT
				HIGH_PRIORITY p.id,
				p.disp,
				p.disp_naver,
				p.is_delete,
				p.product_type, 
				p.pcode, 
				p.product_color_chip,
				p.pname ,
				p.brand_name,
				p.global_pinfo,
				p.brand,
				p.shotinfo, 
				p.state, 
				p.origin,
				p.company, 
				p.admin,
				p.stock, 
				p.stock_use_yn,
				case when state = 0 then convert(p.vieworder, SIGNED)-1000000 else p.vieworder end  as vieworder2,
				p.premiumprice,
				$select_price ,
				p.movie,
				icons,
				reserve_yn,
				p.is_sell_date,
				p.sell_priod_sdate,
				p.sell_priod_edate,
				p.view_cnt,
				p.order_cnt,
				p.recommend_cnt,
				ifnull(ceil(p.after_score/p.after_cnt),'0') as uf_valuation,
				p.after_cnt,
				r.cid, 
				c.depth,
				case when date_sub(NOW(), INTERVAL 7 DAY) < p.regdate then 1 else 0 end as is_new,
				p.regdate,
				pa.use_coupon_yn,
				(SELECT ci.cname FROM shop_category_info ci WHERE ci.depth=0 AND ci.cid = CONCAT(SUBSTR(c.cid,1,3),'000000000000')) AS cat0,
				(SELECT ci.cname FROM shop_category_info ci WHERE ci.depth=1 AND ci.cid = CONCAT(SUBSTR(c.cid,1,6),'000000000')) AS cat1,
				(SELECT ci.cname FROM shop_category_info ci WHERE ci.depth=2 AND ci.cid = CONCAT(SUBSTR(c.cid,1,9),'000000')) AS cat2,
				(SELECT ci.cname FROM shop_category_info ci WHERE ci.depth=3 AND ci.cid = CONCAT(SUBSTR(c.cid,1,12),'000')) AS cat3,
				(SELECT ci.cname FROM shop_category_info ci WHERE ci.depth=4 AND ci.cid = SUBSTR(c.cid,1,15)) AS cat4
			FROM
				".TBL_SHOP_PRODUCT." p 
				left join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid and r.basic = 1
				left join ".TBL_SHOP_CATEGORY_INFO." c on r.cid = c.cid
				left join shop_product_addinfo pa on p.id=pa.pid
				left join shop_brand sb on p.brand=sb.b_ix
				INNER JOIN common_company_detail AS ccd ON p.admin=ccd.company_id
                
			where";



    if(strpos($where, "sell_priod_sdate") === false){
        $sell_priod_where = " and if(p.is_sell_date = '1',p.sell_priod_sdate <= '".date('Ymd')."' and p.sell_priod_edate >= '".date('Ymd')."','1=1') ";
    }


    // *** ************************************************************************************************
    // *** ************************************************************************************************
    $sql.="	".$where."
                and p.disp in ('1','3') and p.state = '1' and category_use !='0'  and r.basic = 1 ".$category_where."
                ".$sell_priod_where."
                ".$groupby_where."
                ".$orderby."
                limit $start,".($max)."";
    $slave_mdb->query($sql);
    $products = $slave_mdb->fetchall("object"); // 10개씩


    $pinfo[brand_name]= getGlobalTargetName($pinfo["brand_name"],$global_binfo,'brand_name');


    if(count($products)){

        if($_SESSION["user"]["code"]){
            $slave_mdb->query("SELECT pid FROM shop_wishlist where mid = '".$_SESSION["user"]["code"]."' ");
            $favorite_goods = $slave_mdb->getrows();
            $favorite_goods = $favorite_goods[0];
        }

        for($i=0 ; $i < count($products) ;$i++){

            $_array_pid[] = $products[$i][id];
            $goods_infos[$products[$i][id]][pid] = $products[$i][id];
            $goods_infos[$products[$i][id]][amount] = $products[$i][pcount];
            $goods_infos[$products[$i][id]][cid] = $products[$i][cid];
            $goods_infos[$products[$i][id]][depth] = $products[$i][depth];

            $products[$i][category_add_infos] = json_decode(urldecode($products[$i][category_add_infos]),true);
            $products[$i][pname] = getGlobalTargetName($products[$i][pname],$products[$i][global_pinfo],'pname');
            $products[$i][is_favorite] = false;
            if(is_array($favorite_goods)){
                if(in_array($products[$i][id], $favorite_goods)){
                    $products[$i][is_favorite] = true;
                }
            }
            $slave_mdb->query("SELECT global_binfo FROM shop_brand where b_ix = '".$products[$i]["brand"]."'");
            $slave_mdb->fetch();
            $global_binfo= $slave_mdb->dt[global_binfo];

            $products[$i][brand_name]=getGlobalTargetName($products[$i]["brand_name"],$global_binfo,'brand_name');
        }

        $discount_info = DiscountRult($goods_infos, $cid, $depth);


        if(is_array($products))
        {
            foreach ($products as $key => $sub_array) {

                $discount_desc = array();
                $select_ = array("icons_list"=>explode(";",$sub_array[icons]));
                array_insert($sub_array,50,$select_);

                $pcolor_ = array("colorchip_list"=>explode(",",str_replace(" ","",$sub_array[product_color_chip])));
                array_insert($sub_array,51,$pcolor_);
                $discount_item = $discount_info[$sub_array[id]];
                $_dcprice = $sub_array[sellprice]; // 판매가

                if(is_array($discount_item)){
                    foreach($discount_item as $_key => $_item){

                        // %
                        if($_item[discount_value_type] == "1"){
                            $_dcprice = roundBetter($_dcprice*(100 - $_item[discount_value])/100, $_item[round_position], $_item[round_type]);

                            // 원
                        }else if($_item[discount_value_type] == "2"){
                            $_dcprice = $_dcprice - $_item[discount_value];
                        }

                        $discount_desc[] = $_item;
                    }
                }

                $_dcprice = array("dcprice"=>$_dcprice);
                array_insert($sub_array,72,$_dcprice);

                $discount_desc = array("discount_desc"=>$discount_desc);
                array_insert($sub_array,73,$discount_desc);

                $products[$key][pname]='';
                $products[$key] = $sub_array;

                if($products[$key][uf_valuation] != ""){
                    $products[$key][uf_valuation] = round($products[$key][uf_valuation], 0);
                } else {
                    $products[$key][uf_valuation] = 0;
                }
            }
        }
    }


    if(count($products))
    {
        for($i=0 ; $i < count($products) ;$i++){
            $products[$i]['listprice'] = getExchangeNationPrice($products[$i]['listprice']);
            $products[$i]['sellprice'] = getExchangeNationPrice($products[$i]['sellprice']);
            $products[$i]['dcprice'] = getExchangeNationPrice($products[$i]['dcprice']);
        }
    }

    // $return['total'] = $ptotal;
    $return['products'] = $products;
    $return['cate_list'] = $cate_list;
    $return['cate_total'] = $cate_total;
    $return['sql'] = $sql;

    return $return;
}