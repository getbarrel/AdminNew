<?
include("../../class/database.class");
include("../logstory/class/sharedmemory.class");

if($act == "qna_setting_update"){
	$db = new Database;
	$sql = "update shop_product_qna_div set disp='".$disp."', div_name='".$div_name."', mall_ix='".$mall_ix."' where ix='".$ix."'";
	$db->query($sql);

	echo "<script>alert('정상적으로 수정되었습니다.');top.location.href='./cscenter.manage.div2.php'</script>";
	exit;
}

if($act == "qna_setting_insert"){
	$db = new Database;
	$sql = "insert into shop_product_qna_div (div_name, disp,mall_ix, regdate) values ('".$div_name."', '".$disp."','".$mall_ix."', NOW())";
	$db->query($sql);

	echo "<script>alert('정상적으로 등록되었습니다.');top.location.href='./cscenter.manage.div2.php'</script>";
	exit;
}

$data = urlencode(serialize($_POST));

$path = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";

if(!is_dir($path)){
	mkdir($path, 0777);
	chmod($path,0777);
}

$shmop = new Shared($page_type);
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$shmop->setObjectForKey($data,$page_type);

echo "<script>alert('정상적으로 수정되었습니다.');history.back(-1);</script>";
exit;
?>