<?
include("../../class/database.class");

session_start();

if($admininfo[company_id] == ""){
	echo "<script language='javascript'>alert('관리자 로그인후 사용하실수 있습니다.');location.href='/admin/admin.php'</script>";
	exit;	
}

$db = new Database;
$db2 = new Database;



if ($act == "delete")
{
	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_$id.gif")){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_$id.gif");
	}
	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_$id.gif")){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_$id.gif");
	}
	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_$id.gif")){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_$id.gif");
	}
	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_$id.gif")){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_$id.gif");
	}
	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_$id.gif")){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_$id.gif");
	}

	$db->query("DELETE FROM ".TBL_SHOP_PRODUCT." WHERE id='$id'");
	$db->query("DELETE FROM ".TBL_SHOP_PRICEINFO." WHERE pid='$id'");
	
	echo "<script language='javascript'>parent.document.location.reload();</script>";

	//header("Location:../product_list.php");
}

if ($act == "update"){
	
	for($i=0;$i<count($pid);$i++){
		$db2->query ("select * from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where pid = '".$pid[$i]."' ");
		
		for($j=0;$j < $db2->total;$j++){
			$db2->fetch($j);
			//echo ("UPDATE ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." SET  option_stock='".$_POST["option_stock".$pid[$i]."_".$db2->dt[id]]."',option_safestock = '".$_POST["option_safestock".$pid[$i]."_".$db2->dt[id]]."' Where id = '".$db2->dt[id]."' ");
			$db->query ("UPDATE ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." SET  option_stock='".$_POST["option_stock".$pid[$i]."_".$db2->dt[id]]."',option_safestock = '".$_POST["option_safestock".$pid[$i]."_".$db2->dt[id]]."' Where id = '".$db2->dt[id]."' ");
		}
		//echo ("UPDATE ".TBL_SHOP_PRODUCT." SET  stock='".$_POST["stock".$pid[$i]]."',safestock = '".$_POST["safestock".$pid[$i]]."', disp='".$_POST["dispaly".$pid[$i]]."' Where id = ".$pid[$i]." ");
		
		$db->query ("UPDATE ".TBL_SHOP_PRODUCT." SET  stock='".$_POST["stock".$pid[$i]]."',safestock = '".$_POST["safestock".$pid[$i]]."', disp='".$_POST["dispaly".$pid[$i]]."' Where id = ".$pid[$i]." ");
		
	}
	//$db->query("UPDATE ".TBL_SHOP_PRODUCT." SET  stock='$stock',safestock = '$safestock', display='$display' Where id = $id ");

	
	
	//echo "<script language='javascript'>document.location.href='product_stock.php?view=innerview&cid=$cid&depth=$depth';</script>";
	//header("Location:./product_stock.php?view=innerview&cid=$cid&depth=$depth");
	echo "<script language='javascript'>parent.document.location.reload();</script>";
}

/*
create table ".TBL_SHOP_PRICEINFO." (
id int(10) unsigned zerofill not null auto_increment,
pid int(10) unsigned zerofill not null,
sellprice int(10) unsigned not null default '0',
coprice int(10) unsigned not null default '0',
nointerest mediumtext null default null,
reserve smallint(5) unsigned not null default '0',
admin varchar(32) null default null,
regdate datetime null,
primary key(id));

*/
/*	
	

*/
/*
create table ".TBL_SHOP_PRODUCT." (
id int(10) unsigned zerofill not null auto_increment,
pname varchar(100) null default null,
company varchar(20) null default null,
shotinfo mediumtext null default null,
sellprice int(10) unsigned not null default '0',
coprice int(10) unsigned not null default '0',
reserve smallint(5) unsigned not null default '0',
bimg varchar(100) null default null,
mimg varchar(100) null default null,
simg varchar(100) null default null,
basicinfo mediumtext null default null,
new char(1) null default '0',
hot char(1) null default '0',
event char(1) null default '0',
state char(1) null default '0',
disp char(1) null default '0',
movie varchar(100) null default null,
admin varchar(32) null default null,
regdate datetime null,
primary key(id));

create table shop_reserveinfo (
id int(8) not null auto_increment,
uid varchar(32) null default null,
oid int(17) not null,
ptprice int(10) unsigned not null default '0',
payprice int(10) unsigned not null default '0',
reserve smallint(5) unsigned not null default '0',
state char(1) null default '0',
regdate datetime null,
primary key(id));

*/
?>