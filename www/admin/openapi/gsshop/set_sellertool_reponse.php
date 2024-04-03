<?

set_time_limit(9999999);

include_once($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/openapi.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/sellertool/sellertool.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/include/lib.function.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/include/admin.util.php");


$db = new Database();
$db2 = new Database();

$result="S|상품가(이) 저장성공하였습니다.|0000018606|1014327|20709823|20709823001^429212,20709823002^429213,20709823003^429214,20709823004^429215,20709823005^429216,20709823006^429217,20709823007^429218,20709823008^429219,20709823009^429220,20709823010^429221,20709823011^429222,20709823012^429223,20709823013^429224,20709823014^429225,20709823015^429226,20709823016^429227,20709823017^429228,20709823018^429229,20709823019^429230,20709823020^429231,20709823021^429232,20709823022^429233,20709823023^429234,20709823024^429235,20709823025^429236,20709823026^429237,20709823027^429238,20709823028^429239,20709823029^429240,20709823030^429241,20709823031^429242,20709823032^429243,20709823033^429244,20709823034^429245,20709823035^429246,20709823036^429247,20709823037^429248,20709823038^429249";
list($rcode, $message, $pid, $supCd, $co_pid, $co_option_id_text ) = explode("|",$result);

if( ! empty($co_pid) ){
	$sql = "SELECT * FROM sellertool_reponse WHERE site_code = 'gsshop' and shop_key='pid' and shop_value = '".$pid."' and sellertool_key = 'prdCd' and sellertool_value ='".$co_pid."' ";
	echo $sql."<br/>";
	$db->query($sql);
	
	if( empty( $db->total ) ){
		$sql = "insert into sellertool_reponse 
			(site_code,shop_key, shop_value, sellertool_key, sellertool_value, is_basic, regdate ) values('gsshop','pid','".$pid."','prdCd','".$co_pid."','0', NOW())";
		$db->query($sql);
	}
}

if( ! empty($co_option_id_text) ){

	$co_option_ids = explode(",",$co_option_id_text);

	foreach($co_option_ids as $co_option_id){
		list($attrPrdListAttrPrdCd , $option_id) = explode("^",$co_option_id);
		$sql = "SELECT * FROM sellertool_reponse WHERE site_code = 'gsshop' and shop_key='pid|id' and shop_value = '".$pid."|".$option_id."' and sellertool_key = 'attrPrdListAttrPrdCd' and sellertool_value ='".$attrPrdListAttrPrdCd."' ";
		echo $sql."<br/>";
		$db->query($sql);
		
		if( empty( $db->total ) ){
			$sql = "insert into sellertool_reponse 
				(site_code,shop_key, shop_value, sellertool_key, sellertool_value, is_basic, regdate ) values('gsshop','pid|id','".$pid."|".$option_id."','attrPrdListAttrPrdCd','".$attrPrdListAttrPrdCd."','0', NOW())";
			$db->query($sql);
		}
	}
}

?>