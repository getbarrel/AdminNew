<?
include("../../class/database.class");
include("../logstory/class/sharedmemory.class");

$db = new Database;

if($act == "update"){

	$data = urlencode(serialize($_POST));
	$path = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";

	$shmop_file = $company_id."_shortage_results_setup";
	if(!is_dir($path)){
		mkdir($path, 0777);
		chmod($path,0777);
	}else{
		chmod($path,0777);
	}
	
	$shmop = new Shared($shmop_file);
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$shmop->setObjectForKey($data,$shmop_file);

	echo("<script>alert('저장 되었습니다.');parent.document.location.href = 'shortage_results_setup.php';
	self.close();
	opener.parent.parent.document.location.reload();
	</script>");
}
?>