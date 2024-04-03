<?
include("../../class/database.class");
include("../logstory/class/sharedmemory.class");


$get_date[goods_coupon_use_yn]=$goods_coupon_use_yn;
$get_date[member_coupon_use_yn]=$member_coupon_use_yn;
$get_date[member_publish_ix]=$member_publish_ix;
$get_date[round_position] = $round_position;
$get_date[round_type] = $round_type;

//[Start] coupon_rule.php에 모바일 전용 사용하기 위해 추가 2015-01-20 박윤완
$get_date[mobile_member_coupon_use_yn]=$mobile_member_coupon_use_yn;
$get_date[mobile_member_publish_ix] = $mobile_member_publish_ix;
//[End]

$get_date[appdown_coupon_use_yn]=$appdown_coupon_use_yn;
$get_date[app_down_ix] = $app_down_ix;

$get_date[app_order_coupon_use_yn]=$app_order_coupon_use_yn;
$get_date[app_order_ix] = $app_order_ix;
/*
$arraykeysort = arraykeysort($goods_coupon_price_low);
$get_date[goods_coupon_price_low] = filterval($arraykeysort,$goods_coupon_price_low);
$get_date[goods_coupon_price_high] = filterval($arraykeysort,$goods_coupon_price_high);
$get_date[goods_publish_ix] = filterval($arraykeysort,$goods_publish_ix);
*/


$data = urlencode(serialize($get_date));

//$data = urlencode(serialize($_POST));
//print_r($admininfo);
$path = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admininfo"]["mall_data_root"]."/_shared/";

//echo $path;
if(!is_dir($path)){
	mkdir($path, 0777);
	chmod($path,0777);
}else{
	chmod($path,0777);
}

$shmop = new Shared("b2c_coupon_rule");
$shmop->filepath = $path;
$shmop->SetFilePath();
$shmop->setObjectForKey($data,"b2c_coupon_rule");


/*
$shmop = new Shared("reserve_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$shmop->setObjectForKey($data,"reserve_rule");
*/
echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>";



function arraykeysort($array){

	asort($array);

	$i=0;
	foreach($array as $key => $val){
		if(!empty($val)){
			$sort[$i] = $key;
			$i++;
		}
	}

	krsort($sort);

	$i=0;
	foreach($sort as $key => $val){
		$return[$i] = $val;
		$i++;
	}

	return $return; 
}

function filterval($sort,$array){

	$i=0;
	foreach($sort as $key => $val){
		$return[$i] = $array[$val];
		$i++;
	}

	return $return; 
}

?>