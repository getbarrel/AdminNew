<?
include_once("../class/layout.class");
require $_SERVER["DOCUMENT_ROOT"].'/class/sphinxfb.class';

if($admininfo[company_id] == ""){
	echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert(language_data['common']['C'][language]);location.href='/admin/admin.php'</script>"; //'관리자 로그인후 사용하실수 있습니다.'
	exit;
}

$db = new Database;
$db2 = new Database;
if($_SESSION['admin_config']['search_engine_yn'] == 'Y' && $_SESSION['admin_config']['search_engine_type'] == 'S'){
	$sfb = new sphinxfb(); // mysql 데이터베이스
}

if($act == "state_update"){

	if($state == 1){
		$sql = "UPDATE ".TBL_SHOP_PRODUCT." SET state='0', editdate = NOW() Where id = ".$pid." ";
		$db->query($sql);

        $sql = "UPDATE ".TBL_SHOP_PRODUCT_GLOBAL." SET state='0', editdate = NOW() Where id = ".$pid." ";
        $db->query($sql);
		$state_txt = "<a href='product_list.act.php?act=state_update&pid=".$pid."&state=0'   target='iframe_act'><span style='color:red;font-weight:bold;'><img src='../images/".$admininfo["language"]."/btn_sold_out.gif' align=absmiddle></span></a>";
		//echo "<script language='javascript'>alert(parent.document.getElementById('state_txt_".$pid."').innerHTML);</script>";
		
		if($_SESSION['admin_config']['search_engine_yn'] == 'Y' && $_SESSION['admin_config']['search_engine_type'] == 'S'){	
			$sfb->rebuild_index(" id =".(int)$pid." ");
		}

		echo "<script language='javascript'>parent.document.getElementById('state_txt_".$pid."').innerHTML = \"".$state_txt."\";</script>";
	}else if($state == 0){
        $sql = "UPDATE ".TBL_SHOP_PRODUCT." SET state='1' , editdate = NOW() Where id = ".$pid." ";
        $db->query($sql);

        $sql = "UPDATE ".TBL_SHOP_PRODUCT_GLOBAL." SET state='1' , editdate = NOW() Where id = ".$pid." ";
        $db->query($sql);

		$state_txt = "<a href='product_list.act.php?act=state_update&pid=".$pid."&state=1'   target='iframe_act'><span style='color:blue;font-weight:bold;'><img src='../images/".$admininfo["language"]."/btn_sell.gif' align=absmiddle></span></a>";
		//echo "<script language='javascript'>alert(parent.document.getElementById('state_txt_".$pid."').innerHTML);</script>";
		
		if($_SESSION['admin_config']['search_engine_yn'] == 'Y' && $_SESSION['admin_config']['search_engine_type'] == 'S'){
			$sfb->rebuild_index(" id =".(int)$pid." ");
		}

		echo "<script language='javascript'>parent.document.getElementById('state_txt_".$pid."').innerHTML = \"".$state_txt."\";</script>";
	}

}

if($act == "disp_update"){

	if($disp == 1){
        $sql = "UPDATE ".TBL_SHOP_PRODUCT." SET disp='0', editdate = NOW() Where id = ".$pid." ";
        $db->query($sql);

        $sql = "UPDATE ".TBL_SHOP_PRODUCT_GLOBAL." SET disp='0', editdate = NOW() Where id = ".$pid." ";
        $db->query($sql);
		$disp_txt = "<a href='product_list.act.php?act=disp_update&pid=".$pid."&disp=0'   target='iframe_act'><span style='color:red;font-weight:bold;'><img src='../images/".$admininfo["language"]."/btn_on_view.gif' align=absmiddle></span></a>";
		//echo "<script language='javascript'>alert(parent.document.getElementById('disp_txt_".$pid."').innerHTML);</script>";
		usleep(1000000);

		if($_SESSION['admin_config']['search_engine_yn'] == 'Y' && $_SESSION['admin_config']['search_engine_type'] == 'S'){
			$result = $sfb->rebuild_index(" id =".(int)$pid." ");
			
			if($result ==  true){
				echo "<script language='javascript'>parent.document.getElementById('disp_txt_".$pid."').innerHTML = \"".$disp_txt."\";</script>";
			}
		}else{
			echo "<script language='javascript'>parent.document.getElementById('disp_txt_".$pid."').innerHTML = \"".$disp_txt."\";</script>";
		}
	}else if($disp == 0){
        $sql = "UPDATE ".TBL_SHOP_PRODUCT." SET disp='1', editdate = NOW() Where id = ".$pid." ";
        $db->query($sql);

        $sql = "UPDATE ".TBL_SHOP_PRODUCT_GLOBAL." SET disp='1', editdate = NOW() Where id = ".$pid." ";
        $db->query($sql);

		$disp_txt = "<a href='product_list.act.php?act=disp_update&pid=".$pid."&disp=1'   target='iframe_act'><span style='color:blue;font-weight:bold;'><img src='../images/".$admininfo["language"]."/btn_off_view.gif' align=absmiddle></span></a>";
		//echo "<script language='javascript'>alert(parent.document.getElementById('disp_txt_".$pid."').innerHTML);</script>";
		usleep(1000000);

		if($_SESSION['admin_config']['search_engine_yn'] == 'Y' && $_SESSION['admin_config']['search_engine_type'] == 'S'){
			$result = $sfb->rebuild_index(" id =".(int)$pid." ");
			if($result ==  true){	
				echo "<script language='javascript'>parent.document.getElementById('disp_txt_".$pid."').innerHTML = \"".$disp_txt."\";</script>";
			}
		}else{
			echo "<script language='javascript'>parent.document.getElementById('disp_txt_".$pid."').innerHTML = \"".$disp_txt."\";</script>";
		}
	}

}


if ($act == "delete" || $act == "delete_excel"){

    $db->query("update ".TBL_SHOP_PRODUCT." p SET  editdate = NOW(), is_delete='1' , disp='0'  WHERE id='".$id."' and state not in ('1') ");
    $db->query("update ".TBL_SHOP_PRODUCT_GLOBAL." p SET  editdate = NOW(), is_delete='1' , disp='0'  WHERE id='".$id."' and state not in ('1') ");

    $db->query("insert into shop_product_delete_log (pid,admin_id,regdate) values ('".$id."','".$_SESSION['admininfo']['admin_id']."',NOW()) ");

    if($_SESSION['admin_config']['search_engine_yn'] == 'Y' && $_SESSION['admin_config']['search_engine_type'] == 'S'){
        $sfb->remove(" id =".(int)$id." ");
    }

/*
	$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product", $id, 'Y');
	if ($id && file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/")){
		rmdir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/");// rmdirr 로 되어 있던 것을 rmdir 로 수정함 (소스 맨 아래에 rmdirr() 함수가 있는데 주석 처리되어있음) kbk 13/04/22
	}

	$db->query("DELETE FROM ".TBL_SHOP_PRODUCT." WHERE id='".$id."'");
	$db->query("DELETE FROM ".TBL_SHOP_PRICEINFO." WHERE pid='".$id."'");
	$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE pid='".$id."'");
	$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_RELATION." WHERE pid='".$id."'");
	$db->query("DELETE FROM shop_product_buyingservice_priceinfo WHERE pid='".$id."'");
	$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid='".$id."'");
	$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_DISPLAYINFO." WHERE pid='".$id."'");
	$db->query("DELETE FROM ".TBL_SHOP_CART." WHERE id='$id'");
	$db->query("DELETE FROM shop_product_delivery WHERE pid='".$id."'");

    $db->query("DELETE FROM ".TBL_SHOP_PRODUCT_GLOBAL." WHERE id='".$id."'");
    $db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_GLOBAL." WHERE pid='".$id."'");
    $db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_GLOBAL." WHERE pid='".$id."'");

	if($id && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/$id")){
		rmdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/$id");// rmdirr 로 되어 있던 것을 rmdir 로 수정함 (소스 맨 아래에 rmdirr() 함수가 있는데 주석 처리되어있음) kbk 13/04/22
	}

	$adduploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg", $id, 'Y');
	if ($id && is_dir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/")){
		rmdir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/");// rmdirr 로 되어 있던 것을 rmdir 로 수정함 (소스 맨 아래에 rmdirr() 함수가 있는데 주석 처리되어있음) kbk 13/04/22
	}
	
*/
	/*
	if($type == "nonecategory"){
		echo "<script language='javascript'>parent.document.location.href='product_list_noncategory.php?cid=$cid&depth=$depth&max=$max&product_type=$product_type';</script>";
	}else{
		echo "<script language='javascript'>parent.document.location.reload();</script>";
	}
	*/
	
	if($type == "nonecategory"){
		//echo "<script language='javascript'>parent.document.location.href='product_list_noncategory.php?cid=$cid&depth=$depth&max=$max&product_type=$product_type';</script>";
		echo "<script language='javascript'>if(parent.window.frames['act'].location == 'about:blank'){parent.document.location.reload();}else{parent.window.frames['act'].location.reload();}</script>";
	}else{
		echo "<script language='javascript'>if(parent.window.frames['act'].location == 'about:blank'){parent.document.location.reload();}else{parent.window.frames['act'].location.reload();}</script>";
		//echo "<script language='javascript'>parent.document.location.reload();</script>";
	}

}


if($act == "select_delete"){
	if($select_pid){
		$cpid = $select_pid;
	}
	if($goodss_pid){
		$cpid = $goodss_pid;
	}
	//$db->debug = true;
	for($i=0;$i<count($cpid);$i++){

        $db->query("update ".TBL_SHOP_PRODUCT." p SET  editdate = NOW(), is_delete='1' , disp='0'  WHERE id='".$cpid[$i]."' and state not in ('1') ");
        $db->query("update ".TBL_SHOP_PRODUCT_GLOBAL." p SET  editdate = NOW(), is_delete='1' , disp='0'  WHERE id='".$cpid[$i]."' and state not in ('1') ");

        $db->query("insert into shop_product_delete_log (pid,admin_id,regdate) values ('".$cpid[$i]."','".$_SESSION['admininfo']['admin_id']."',NOW()) ");

        if($_SESSION['admin_config']['search_engine_yn'] == 'Y' && $_SESSION['admin_config']['search_engine_type'] == 'S'){
            $sfb->remove(" id =".(int)$cpid[$i]." ");
        }

		/*
		$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product", $cpid[$i], 'Y');
		if ($uploaddir && file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/")){
			rmdir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/");// rmdirr 로 되어 있던 것을 rmdir 로 수정함 (소스 맨 아래에 rmdirr() 함수가 있는데 주석 처리되어있음) kbk 13/04/22
		}

		$db->query("DELETE FROM ".TBL_SHOP_PRODUCT." WHERE id='".$cpid[$i]."'");// limit 1 2012-11-07 홍진영 오라클 쿼리 오류
		$db->query("DELETE FROM ".TBL_SHOP_PRICEINFO." WHERE pid='".$cpid[$i]."'");
		$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_RELATION." WHERE pid='".$cpid[$i]."'");
		$db->query("DELETE FROM shop_product_buyingservice_priceinfo WHERE pid='".$cpid[$i]."'");
		$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE pid='".$cpid[$i]."'");
		$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid='".$cpid[$i]."'");
		$db->query("DELETE FROM ".TBL_SHOP_CART." WHERE id='".$cpid[$i]."'");
		$db->query("DELETE FROM shop_product_delivery WHERE pid='".$cpid[$i]."'");
		//$db->query("DELETE FROM ".TBL_SHOP_ORDER." WHERE id='".$cpid[$i]."'");
        
        $db->query("DELETE FROM ".TBL_SHOP_PRODUCT_GLOBAL." WHERE id='".$cpid[$i]."'");
        $db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_GLOBAL." WHERE pid='".$cpid[$i]."'");
        $db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_GLOBAL." WHERE pid='".$cpid[$i]."'");

		if($cpid[$i] && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/".$cpid[$i])){
			rmdir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product_detail/".$cpid[$i]);// rmdirr 로 되어 있던 것을 rmdir 로 수정함 (소스 맨 아래에 rmdirr() 함수가 있는데 주석 처리되어있음) kbk 13/04/22
		}

		$adduploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg", $cpid[$i], 'Y');
		if ($cpid[$i] && is_dir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/")){
			rmdir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/");// rmdirr 로 되어 있던 것을 rmdir 로 수정함 (소스 맨 아래에 rmdirr() 함수가 있는데 주석 처리되어있음) kbk 13/04/22
		}

		*/

	}

	if($type == "nonecategory"){

		echo "<script language='javascript'>if(parent.window.frames['act'].location == 'about:blank'){parent.document.location.reload();}else{parent.window.frames['act'].location.reload();}</script>";
	}else{
		echo "<script language='javascript'>if(parent.window.frames['act'].location == 'about:blank'){parent.document.location.reload();}else{parent.window.frames['act'].location.reload();}</script>";
		//echo "<script language='javascript'>parent.document.location.reload();</script>";
	}

}


if ($act == "update"){

//echo count($cpid);
	for($i=0;$i<count($cpid);$i++){//disp='".$_POST["disp".$cpid[$i]]."',

		if($admininfo[charger_id] == "forbiz"){
			if($_POST["state_".$cpid[$i]] != ""){
				$state_str = ", state='".$_POST["state_".$cpid[$i]]."' ";
			}
		}else{
			$state_str ="";
		}

		$sql = "UPDATE ".TBL_SHOP_PRODUCT."
			SET pcode='".$_POST["pcode".$cpid[$i]]."', reserve='".$_POST["reserve".$cpid[$i]]."',reserve_rate='".$_POST["reserve_rate".$cpid[$i]]."',
			coprice='".str_replace(",","",$_POST["coprice".$cpid[$i]])."', sellprice='".str_replace(",","",$_POST["sellprice".$cpid[$i]])."' ,
			listprice='".str_replace(",","",$_POST["listprice".$cpid[$i]])."' , wholesale_price='".str_replace(",","",$_POST["wholesale_price".$cpid[$i]])."' ,
			wholesale_sellprice='".str_replace(",","",$_POST["wholesale_sellprice".$cpid[$i]])."' ,
			search_keyword='".$_POST["search_keyword".$cpid[$i]]."',
			editdate = NOW()
			$state_str
			Where id = ".$cpid[$i]." ";
		//echo $sql."<br><br>";
		$db->query ($sql);

		$sql = "INSERT INTO ".TBL_SHOP_PRICEINFO." (id, pid, listprice, sellprice, coprice, reserve,  company_id, charger_info,regdate,wholesale_sellprice,wholesale_price) ";
		$sql = $sql." values('', '".$cpid[$i]."','".str_replace(",","",$_POST["listprice".$cpid[$i]])."','".str_replace(",","",$_POST["sellprice".$cpid[$i]])."', '".str_replace(",","",$_POST["coprice".$cpid[$i]])."', '".$_POST["reserve".$cpid[$i]]."',  '".$admininfo[company_id]."','[".$admininfo[company_name]."] ".$admininfo[charger]."(".$admininfo[charger_id].")',NOW(),'".str_replace(",","",$_POST["wholesale_sellprice".$cpid[$i]])."','".str_replace(",","",$_POST["wholesale_price".$cpid[$i]])."') ";
		//echo $sql;
		$db->sequences = "SHOP_PRICEINFO_SEQ";
		$db->query($sql);

		if($_SESSION['admin_config']['search_engine_yn'] == 'Y' && $_SESSION['admin_config']['search_engine_type'] == 'S'){
			$sfb->rebuild_index(" id =".(int)$cpid[$i]." ");
		}

	}

	echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('정상적으로 수정되었습니다. ');top.location.reload();//kbk	</script>";
	//header("Location:./product_list.php?view=innerview");//kbk
}


if($act == "update_one"){
	if($admininfo[charger_id] == "forbiz"){
		if($state != ""){
			$state_str = ", state='".$state."' ";
		}
	}else{
		$state_str ="";
	}

	$sql = "UPDATE ".TBL_SHOP_PRODUCT."
		SET  pcode='$pcode',reserve = '$reserve',reserve_rate = '$reserve_rate',
		coprice='".str_replace(",","",$coprice)."' , sellprice='".str_replace(",","",$sellprice)."' ,
		listprice='".str_replace(",","",$listprice)."' , wholesale_price='".str_replace(",","",$wholesale_price)."' ,
		wholesale_sellprice='".str_replace(",","",$wholesale_sellprice)."' ,
		search_keyword='$search_keyword',
		editdate = NOW()
		$state_str
		Where id = '".$pid."' ";
	$db->query ($sql);


	$sql = "INSERT INTO ".TBL_SHOP_PRICEINFO." (id, pid, listprice, sellprice, coprice, reserve,  company_id, charger_info,regdate,wholesale_sellprice,wholesale_price) ";
	$sql = $sql." values('', '".$cpid[$i]."','".str_replace(",","",$listprice)."','".str_replace(",","",$sellprice)."', '".str_replace(",","",$coprice)."', '$reserve',  '".$admininfo[company_id]."','[".$admininfo[company_name]."] ".$admininfo[charger]."(".$admininfo[charger_id].")',NOW(),'".str_replace(",","",$wholesale_sellprice)."','".str_replace(",","",$wholesale_price)."') ";
	//echo $sql;
	$db->sequences = "SHOP_PRICEINFO_SEQ";
	$db->query($sql);

	if($_SESSION['admin_config']['search_engine_yn'] == 'Y' && $_SESSION['admin_config']['search_engine_type'] == 'S'){
		$sfb->rebuild_index(" id =".(int)$pid." ");
	}

	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');parent.location.reload();//kbk</script>";
	//header("Location:./product_list.php?view=innerview");//kbk
}

if($mode == "excel_search"){

	include '../include/phpexcel/Classes/PHPExcel.php';
	include("../logstory/class/sharedmemory.class");

	$path = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	if(!is_dir($path)){
		mkdir($path, 0777);
		chmod($path,0777);
	}else{
		chmod($path,0777);
	}
	$memory_file_name = "excel_search_".$admininfo[charger_ix];

	$shmop = new Shared($memory_file_name);
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);
	date_default_timezone_set('Asia/Seoul');
	
	$search_excel_file_name = $admininfo[charger_ix]."_".$search_excel_file_name;
	if ($search_excel_file_size > 0){
		copy($search_excel_file, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$search_excel_file_name);
	}

	$objPHPExcel = PHPExcel_IOFactory::load($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$search_excel_file_name);
	//데이터는 2줄부터 시작, 1 줄은 제목+코드명

	$rownum = 1;
	$columnVar = $objPHPExcel->setActiveSheetIndex(0)->getCell('B' . ($rownum))->getValue();

	while (($objPHPExcel->getActiveSheet()->getCell('A' . $rownum)->getValue() != "") && ($i < 11000)) {
		//////////////////////////// 데이터를 가져옴 //////////////////////////////////

		$search_text = $objPHPExcel->getActiveSheet()->getCell('A' . $rownum)->getValue();	//검색데이타

		if(!empty($search_text)){
			$data_array[] .= trim($search_text);	//엑셀 검색 값을 배열로 저장
		}
		$rownum++;
	}

	$data = urlencode(serialize($data_array));
	$shmop->setObjectForKey($data,$memory_file_name);	//메모리 저장
	
	// 다 쓴 파일 삭제
	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$search_excel_file_name)){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$search_excel_file_name);
	}

	echo "$search_excel_file_name";

}

?>