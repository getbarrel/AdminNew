<?
include("../../class/database.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');



if($admininfo[company_id] == ""){
	echo "<script language='javascript'>alert(language_data['common']['C'][language]);location.href='/admin/admin.php'</script>";//'관리자 로그인후 사용하실수 있습니다.'
	exit;
}

$db = new Database;
$db2 = new Database;

if($act == "state_update"){


	if($state == 1){
		$sql = "UPDATE ".TBL_SNS_PRODUCT." SET state='0' Where id = ".$pid." ";
		$db->query($sql);
		$state_txt = "<a href='product_list.act.php?act=state_update&pid=".$pid."&state=0'   target='iframe_act'><span style='color:red;font-weight:bold;'>[일시품절]</span></a>";
		//echo "<script language='javascript'>alert(parent.document.getElementById('state_txt_".$pid."').innerHTML);</script>";
		echo "<script language='javascript'>parent.document.getElementById('state_txt_".$pid."').innerHTML = \"".$state_txt."\";</script>";
	}else if($state == 0){
		$sql = "UPDATE ".TBL_SNS_PRODUCT." SET state='1' Where id = ".$pid." ";
		$db->query($sql);
		$state_txt = "<a href='product_list.act.php?act=state_update&pid=".$pid."&state=1'   target='iframe_act'><span style='color:blue;font-weight:bold;'>[판매중]</span></a>";
		//echo "<script language='javascript'>alert(parent.document.getElementById('state_txt_".$pid."').innerHTML);</script>";
		echo "<script language='javascript'>parent.document.getElementById('state_txt_".$pid."').innerHTML = \"".$state_txt."\";</script>";
	}


}

if($act == "disp_update"){


	if($disp == 1){
		$sql = "UPDATE ".TBL_SNS_PRODUCT." SET disp='0' Where id = ".$pid." ";
		$db->query($sql);
		$disp_txt = "<a href='product_list.act.php?act=disp_update&pid=".$pid."&disp=0'   target='iframe_act'><span style='color:red;font-weight:bold;'><img src='../images/".$admininfo["language"]."/btn_on_view.gif' align=absmiddle title='[노출안함]'></span></a>";
		//echo "<script language='javascript'>alert(parent.document.getElementById('disp_txt_".$pid."').innerHTML);</script>";
		echo "<script language='javascript'>parent.document.getElementById('disp_txt_".$pid."').innerHTML = \"".$disp_txt."\";</script>";
	}else if($disp == 0){
		$sql = "UPDATE ".TBL_SNS_PRODUCT." SET disp='1' Where id = ".$pid." ";
		$db->query($sql);
		$disp_txt = "<a href='product_list.act.php?act=disp_update&pid=".$pid."&disp=1'   target='iframe_act'><span style='color:blue;font-weight:bold;'><img src='../images/".$admininfo["language"]."/btn_off_view.gif' align=absmiddle title='[노출함]'></span></a>";
		//echo "<script language='javascript'>alert(parent.document.getElementById('disp_txt_".$pid."').innerHTML);</script>";
		echo "<script language='javascript'>parent.document.getElementById('disp_txt_".$pid."').innerHTML = \"".$disp_txt."\";</script>";
	}


}



if ($act == "delete" || $act == "delete_excel"){

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

	$db->query("DELETE FROM ".TBL_SNS_PRODUCT." WHERE id='".$id."'");
	$db->query("DELETE FROM ".TBL_SNS_PRICEINFO." WHERE pid='".$id."'");
	$db->query("DELETE FROM ".TBL_SNS_PRODUCT_OPTIONS_DETAIL." WHERE pid='".$id."'");
	$db->query("DELETE FROM ".TBL_SNS_PRODUCT_RELATION." WHERE pid='".$id."'");
	$db->query("DELETE FROM ".TBL_SNS_PRODUCT_BUYINGSERVICE_PRICEINFO." WHERE pid='".$id."'");
	$db->query("DELETE FROM ".TBL_SNS_PRODUCT_OPTIONS." WHERE pid='".$id."'");


	if($id && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/$id")){
		rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/$id");
	}

	//echo "<script language='javascript'>parent.document.location.reload();</script>";


	if($type == "nonecategory"){
		echo "<script language='javascript'>parent.document.location.href='product_list_noncategory.php?cid=$cid&depth=$depth&max=$max&product_type=$product_type';</script>";
	}else{
		echo "<script language='javascript'>parent.document.location.reload();</script>";
	}

	//header("Location:../product_list.php");
}


if ($act == "select_delete"){

	for($i=0;$i<count($cpid);$i++){

		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$cpid[$i].".gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$cpid[$i].".gif");
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$cpid[$i].".gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$cpid[$i].".gif");
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$cpid[$i].".gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$cpid[$i].".gif");
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$cpid[$i].".gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$cpid[$i].".gif");
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$cpid[$i].".gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$cpid[$i].".gif");
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$cpid[$i].".gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$cpid[$i].".gif");
		}

		$db->query("DELETE FROM ".TBL_SNS_PRODUCT." WHERE id='".$cpid[$i]."' limit 1");
		$db->query("DELETE FROM ".TBL_SNS_PRICEINFO." WHERE pid='".$cpid[$i]."'");

		$db->query("DELETE FROM ".TBL_SNS_PRODUCT_RELATION." WHERE pid='".$cpid[$i]."'");
		$db->query("DELETE FROM ".TBL_SNS_PRODUCT_BUYINGSERVICE_PRICEINFO." WHERE pid='".$cpid[$i]."'");

		$db->query("DELETE FROM ".TBL_SNS_PRODUCT_OPTIONS_DETAIL." WHERE pid='".$cpid[$i]."'");
		$db->query("DELETE FROM ".TBL_SNS_PRODUCT_OPTIONS." WHERE pid='".$cpid[$i]."'");
		//$db->query("DELETE FROM ".TBL_SHOP_ORDER." WHERE id='".$cpid[$i]."'");



		if($cpid[$i] && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/".$cpid[$i])){
			rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/".$cpid[$i]);
		}


	}

	//echo "<script language='javascript'>parent.document.location.reload();</script>";
	if($type == "nonecategory"){
		echo "<script language='javascript'>parent.document.location.href='product_list_noncategory.php?cid=$cid&depth=$depth&max=$max&product_type=$product_type';</script>";
	}else{
		//echo "<script language='javascript'>document.location.href='product_list.php?view=innerview&cid=$cid&depth=$depth&max=$max&product_type=$product_type';</script>";
		echo "<script language='javascript'>parent.document.location.reload();</script>";
	}


	//header("Location:../product_list.php");
}


if ($act == "update"){
//echo count($pid);
	for($i=0;$i<count($pid);$i++){//disp='".$_POST["disp".$pid[$i]]."',
		/*if($_POST["onew".$pid[$i]] == ""){
			$new = 0;
		}else{
			$new = $_POST["onew".$pid[$i]];
		}
		if($_POST["hot".$pid[$i]] == ""){
			$hot = 0;
		}else{
			$hot = $_POST["hot".$pid[$i]];
		}
		if($_POST["sale".$pid[$i]] == ""){
			$sale = 0;
		}else{
			$sale = $_POST["sale".$pid[$i]];
		}
		if($_POST["event".$pid[$i]] == ""){
			$event = 0;
		}else{
			$event = $_POST["event".$pid[$i]];
		}
		if($_POST["best".$pid[$i]] == ""){
			$best = 0;
		}else{
			$best = $_POST["best".$pid[$i]];
		}*/

		if($admininfo[charger_id] == "forbiz"){
			if($_POST["state_".$pid[$i]] != ""){
				$state_str = ", state='".$_POST["state_".$pid[$i]]."' ";
			}
		}else{
			$state_str ="";
		}

		$sql = "UPDATE ".TBL_SNS_PRODUCT."
			SET pcode='".$_POST["pcode".$pid[$i]]."', reserve='".$_POST["reserve".$pid[$i]]."',reserve_rate='".$_POST["reserve_rate".$pid[$i]]."',
			coprice='".str_replace(",","",$_POST["coprice".$pid[$i]])."', sellprice='".str_replace(",","",$_POST["sellprice".$pid[$i]])."' ,
			listprice='".str_replace(",","",$_POST["listprice".$pid[$i]])."' , search_keyword='".$_POST["search_keyword".$pid[$i]]."'
			$state_str
			Where id = ".$pid[$i]." ";



		$db->query ($sql);

	}

	//$db->query("UPDATE ".TBL_SHOP_PRODUCT." SET  stock='$stock',safestock = '$safestock', display='$display' Where id = $id ");

	echo "<script language='javascript'>
		alert(language_data['common']['E'][language]);//'정상적으로 수정되었습니다. '
		top.location.reload();//kbk
	</script>";
	//header("Location:./product_list.php?view=innerview");//kbk
}

if ($act == "update_one"){//disp='$disp',
	if($admininfo[charger_id] == "forbiz"){
		if($state != ""){
			$state_str = ", state='".$state."' ";
		}
	}else{
		$state_str ="";
	}

	$sql = "UPDATE ".TBL_SNS_PRODUCT."
		SET  pcode='$pcode',reserve = '$reserve',reserve_rate = '$reserve_rate',
		coprice='".str_replace(",","",$coprice)."' , sellprice='".str_replace(",","",$sellprice)."' ,
		listprice='".str_replace(",","",$listprice)."' , search_keyword='$search_keyword'
		$state_str
		Where id = '".$pid."' ";

	$db->query ($sql);

	echo "<script language='javascript'>alert('정상적으로 수정되었습니다.');top.location.reload();//kbk</script>";
	//header("Location:./product_list.php?view=innerview");//kbk
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



function rmdirr($target,$verbose=false)
// removes a directory and everything within it
{
$exceptions=array('.','..');
if (!$sourcedir=@opendir($target))
   {
   if ($verbose)
       echo '<strong>Couldn&#146;t open '.$target."</strong><br />\n";
   return false;
   }
while(false!==($sibling=readdir($sourcedir)))
   {
   if(!in_array($sibling,$exceptions))
       {
       $object=str_replace('//','/',$target.'/'.$sibling);
       if($verbose)
           echo 'Processing: <strong>'.$object."</strong><br />\n";
       if(is_dir($object))
           rmdirr($object);
       if(is_file($object))
           {
           $result=@unlink($object);
           if ($verbose&&$result)
               echo "File has been removed<br />\n";
           if ($verbose&&(!$result))
               echo "<strong>Couldn&#146;t remove file</strong>";
           }
       }
   }
closedir($sourcedir);
if($result=@rmdir($target))
   {
   if ($verbose)
       echo "Target directory has been removed<br />\n";
   return true;
   }
if ($verbose)
   echo "<strong>Couldn&#146;t remove target directory</strong>";
return false;
}
?>