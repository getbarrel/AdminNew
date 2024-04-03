<?
/*
* 서버 셋팅 시 연동 제외 파일 및 디렉터리 정보
 /home/omnichannel/www/data/omnichannel_data/BatchUploadImages/
 /home/omnichannel/www/data/omnichannel_data/_cache/
 /home/omnichannel/www/data/omnichannel_data/_cookies/
 /home/omnichannel/www/data/omnichannel_data/_logs/
 /home/omnichannel/www/data/omnichannel_data/_pg/
 /home/omnichannel/www/data/omnichannel_data/_session/	
 /home/omnichannel/www/data/omnichannel_data/_shared/
 /home/omnichannel/www/data/omnichannel_data/bbs_data/
 /home/omnichannel/www/data/omnichannel_data/compile_/
 /home/omnichannel/www/data/omnichannel_data/detail_img/
 /home/omnichannel/www/data/omnichannel_data/photoskin/
 /home/omnichannel/www/data/omnichannel_data/images/product/00/
 /home/omnichannel/www/data/omnichannel_data/images/addimg/
 /home/omnichannel/www/data/omnichannel_data/images/push_img/
 /home/omnichannel/www/data/omnichannel_data/images/inventory/
 /home/omnichannel/www/data/omnichannel_data/images/upfile/
*/
include("./class/layout.class");
$debug = false;
$db = new Database();


$sql = "INSERT INTO shop_groupinfo (gp_ix, gp_name, organization_img, sale_rate, gp_level, disp, basic, regdate) 
		VALUES
		(1, '일반회원', '', '0', 9, '0', 'Y', '2008-01-09 17:07:10'),
		(2, '회원등급8', '', '0', 8, '0', 'Y', '2011-04-18 09:52:36'),
		(3, '회원등급7', '', '0', 7, '0', 'Y', '2012-01-05 11:46:58'),
		(4, '회원등급6', '', '0', 6, '0', 'Y', '2011-04-07 11:43:55'),
		(5, '회원등급5', '', '0', 5, '0', 'Y', '2009-07-07 18:06:54'),
		(6, '회원등급4', '', '0', 4, '0', 'Y', '2012-01-05 11:47:35'),
		(7, '회원등급3', '', '0', 3, '0', 'Y', '2010-02-22 13:31:32'),
		(8, '회원등급2', '', '0', 2, '0', 'Y', '2009-12-28 12:44:14'),
		(9, '회원등급1', '', '0', 1, '0', 'Y', '2010-02-23 13:28:18') ";
if($debug){
	echo $sql;
}else{
	$db->query($sql);
}

$company_id  = md5(uniqid(rand()));

// 관리자 회원정보 입력
$code  = md5(uniqid(rand()));					
$language = "korea";

$sql = "INSERT INTO ".TBL_COMMON_USER."
		(code, id, pw, mem_type, language, company_id, date, visit, last, ip, authorized , auth)
		VALUES
		('$code','forbiz','".hash("sha256", 'shin0606')."','A','".$language."','".$company_id."',NOW(),'0',NOW(),'".$_SERVER["REMOTE_ADDR"]."','Y','1')";

if($debug){
	echo $sql;
}else{
	$db->query($sql);
}


$name = "관리자";
$com_name = "기본업체";
$shop_name = "기본상점";
$gp_ix = 1;

$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
				(code, name, mail, tel, pcs, date, recom_id,   gp_ix)
				VALUES
				('$code',HEX(AES_ENCRYPT('$name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),NOW(),'".$admininfo[charger_id]."','$gp_ix')";
if($debug){
	echo $sql;
}else{
	$db->query($sql);
}

$code  = md5(uniqid(rand()));					
$language = "korea";

$sql = "INSERT INTO ".TBL_COMMON_USER."
		(code, id, pw, mem_type, language, company_id, date, visit, last, ip, authorized , auth)
		VALUES
		('$code','admin','".hash("sha256", '1234')."','A','".$language."','".$company_id."',NOW(),'0',NOW(),'".$_SERVER["REMOTE_ADDR"]."','Y','3')";

if($debug){
	echo $sql;
}else{
	$db->query($sql);
}


$name = "관리자";
$com_name = "기본업체";
$shop_name = "기본상점";
$gp_ix = 1;

$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
				(code, name, mail, tel, pcs, date, recom_id,   gp_ix)
				VALUES
				('$code',HEX(AES_ENCRYPT('$name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),NOW(),'".$admininfo[charger_id]."','$gp_ix')";

if($debug){
	echo $sql;
}else{
	$db->query($sql);
}

$sql = "insert into  
		common_company_detail SET
		com_name='$com_name', 
		com_div='P', 
		com_type='A', 
		company_id='$company_id', 
		seller_auth ='Y'
	
	"; // 이름에 대한 수정을 없앰 kbk

if($debug){
	echo $sql;
}else{
	$db->query($sql);
}

$sql = "insert into common_seller_detail set 
		company_id='$company_id',
		shop_name='$shop_name',
		shop_desc='$shop_desc',
		homepage='$homepage'";

if($debug){
	echo $sql;
}else{
	$db->query($sql);
}
$delivery_company = 1;
$delivery_policy = 1;
$delivery_basic_policy = "2";
$delivery_price = 3000;
$delivery_freeprice = 30000;
$delivery_free_policy = 1;
$delivery_product_policy = 1;

$sql = "insert into common_seller_delivery set
	company_id='$company_id',
	commission='$commission',
	delivery_company='$delivery_company',
	delivery_policy='$delivery_policy',
	delivery_basic_policy='$delivery_basic_policy',
	/*delivery_price='$delivery_price',*/
	/*delivery_freeprice='$delivery_freeprice',*/
	/*delivery_free_policy='$delivery_free_policy',*/
	delivery_product_policy='$delivery_product_policy'
	 ";



if($debug){
	echo $sql;
}else{
	$db->query($sql);
}


$sql = "INSERT INTO `common_company_relation` (`relation_ix`, `company_id`, `relation_code`, `seq`, `depth`, `edit_date`, `reg_date`) VALUES
							('', '".$company_id."', 'C0001', 1, 0, '0000-00-00 00:00:00', NOW())";
if($debug){
	echo $sql;
}else{
	$db->query($sql);
}


$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/";
//echo $path;
//exit;
if(!is_dir($path)){
	mkdir($path, 0777);
}

//$make_dirs[] = "_shared";
$make_dirs[] = array("dir"=>"BatchUploadImages");		
$make_dirs[] = array("dir"=>"_cache");// 캐쉬데이타 
$make_dirs[] = array("dir"=>"_logs"); 
$make_dirs[] = array("dir"=>"_cookies"); 
$make_dirs[] = array("dir"=>"_pg"); 
$make_dirs[] = array("dir"=>"_session");
$make_dirs[] = array("dir"=>"_shared");
$make_dirs[] = array("dir"=>"bbs_data"); // 게시판 업로드 데이타
//$make_dirs[] = array("dir"=>"bbs_templet");
$make_dirs[] = array("dir"=>"blogbbs_data"); 
$make_dirs[] = array("dir"=>"cafebbs_data");
$make_dirs[] = array("dir"=>"category"); //
$make_dirs[] = array("dir"=>"cms");
//$make_dirs[] = "compile_");		
//$make_dirs[] = "minishop_templet");
//$make_dirs[] = "mobile_templet");
$make_dirs[] = array("dir"=>"compile_");
$make_dirs[] = array("dir"=>"dbbackup");
$make_dirs[] = array("dir"=>"deepzoom"); 
$make_dirs[] = array("dir"=>"detail_img");
$make_dirs[] = array("dir"=>"photoskin");

$make_dirs[] = array("dir"=>"images", "sub_dir"=> array("product","product_detail","brand","cafe","company","cooperation","cupon","dbbackup","deepzoom","department","event","main","member_group","memberimg","popup","position","shopimg","stamps","upfile","useafter","watermark","inventory"));
$make_dirs[] = array("dir"=>"work");
//"banner","category","addimg","flash_data","icon","mail",

for($i = 0; $i < count($make_dirs);$i++){
	
	if(is_dir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/".$make_dirs[$i]["dir"]."/")){
		if(!$debug){
			//echo count($make_dirs[$i]["sub_dir"]);
			if(count($make_dirs[$i]["sub_dir"]) == 0){
				rmdirr($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/".$make_dirs[$i]["dir"]."/");
				echo "디렉터리 삭제 : ".$_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/".$make_dirs[$i]["dir"]."/ 서브디렉터리 갯수 : ".count($make_dirs[$i]["sub_dir"])."<br>";
			}
		}else{
			echo ($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/".$make_dirs[$i]["dir"]."/<br><br>");
		}
	}
	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/".$make_dirs[$i]["dir"]."/";
	if(!is_dir($path)){
		if(!$debug){
			mkdir($path, 0777);
			chmod($path, 0777);
			echo "디렉터리 생성 : ".$path."/<br>";
		}else{
			echo "디렉터리 생성 : ".$path."<br>";
		}
	}

	for($j = 0; $j < count($make_dirs[$i]["sub_dir"]);$j++){
		if(is_dir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/".$make_dirs[$i]["dir"]."/".$make_dirs[$i]["sub_dir"][$j]."/")){
			if(!$debug){
				rmdirr($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/".$make_dirs[$i]["dir"]."/".$make_dirs[$i]["sub_dir"][$j]."/");
				echo "***디렉터리 삭제 : ".$_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/".$make_dirs[$i]["dir"]."/".$make_dirs[$i]["sub_dir"][$j]."/<br>";
			}else{
				echo ($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/".$make_dirs[$i]["dir"]."/".$make_dirs[$i]["sub_dir"][$j]."/<br><br>");
			}
		}
		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/".$make_dirs[$i]["dir"]."/".$make_dirs[$i]["sub_dir"][$j]."/";
		if(!is_dir($path)){
			if(!$debug){
				mkdir($path, 0777);
				chmod($path, 0777);
				echo "서브 디렉터리 생성  : ".$path."<br>";
			}else{
				echo "서브 디렉터리 생성 : ".$path."<br>";
			}
		}
	}
	
}


?>