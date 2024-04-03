<?
include("../../class/database.class");
include("../logstory/class/sharedmemory.class");


$get_date[coupon_use_yn]=$coupon_use_yn;
$get_date[default_coupon_kind]=$default_coupon_kind;
$get_date[cp_standard_price]=$cp_standard_price;
$get_date[restore_cc1]=$restore_cc1;
$get_date[restore_cc2] = $restore_cc2;
$get_date[restore_bf] = $restore_bf;

$data = urlencode(serialize($get_date));
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