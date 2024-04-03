<?
include("../../class/database.class");
include("../logstory/class/sharedmemory.class");

$db = new Database;

if($act == "config_update"){

	$data = urlencode(serialize($_POST));

	//print_r($admininfo);
	$path = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";

	//echo $path;
	if(!is_dir($path)){
		mkdir($path, 0777);
		chmod($path,0777);
	}else{
		chmod($path,0777);
	}

	$file_name = $company_id."_goods_multi_price_setup";

	$shmop = new Shared($file_name);
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$shmop->setObjectForKey($data,$file_name);

	echo("<script>alert('저장 되었습니다.');parent.document.location.href = 'goods_mult_price_setup.php';</script>");
}
?>