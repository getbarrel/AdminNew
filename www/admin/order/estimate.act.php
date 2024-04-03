<?
include("../class/layout.class");

session_start();

$db = new Database;
$db2 = new Database;
$odb = new Database;

//echo $_SERVER["DOCUMENT_ROOT"]."/data/sigong/templet/".$admin_config["mall_use_templete"]."/images/large_order";
//exit;
if($act == "estimate_update"){

	$path = $_SERVER["DOCUMENT_ROOT"]."/data/sigong/templet/".$admin_config["mall_use_templete"]."/images/large_order";
	$es_file_re_name = str_replace(array(" "),array("_"),$es_file_re_name);
	$exp = end(explode('.', $_FILES['es_file_re']['name']));
	$es_file_name = date("Ymdhis_a").".".$exp;
	if($file_status == "D"){
		$db2->query("SELECT * FROM mallstory_large_order where lo_ix = '$lo_ix' ");	
		$db2->fetch();
		if (file_exists($path."/$lo_ix/".$db2->dt[es_file_re])){
			unlink($path."/$lo_ix/".$db2->dt[es_file_re]);
		}
	}else if($file_status == "E"){
		$db2->query("SELECT * FROM mallstory_large_order where lo_ix = '$lo_ix' ");	
		$db2->fetch();
	
		if (file_exists($path."/$lo_ix/".$db2->dt[es_file_re] )){
			unlink($path."/$lo_ix/".$db2->dt[es_file_re]);
			
		}
		//exit;
		if(!is_dir($path."/".$lo_ix)){
			
			if(is_writable($path)){
				//echo $path."/".$bbs_ix;
				mkdir($path."/$lo_ix", 0777);
				chmod($path."/$lo_ix", 0777);	
			}
		}

		$path = $path."/$lo_ix";
		if ($es_file_re_size > 0){
		//echo $path."/".iconv("utf-8","CP949",$es_file_name);
		//exit;
			copy($es_file_re, $path."/".iconv("utf-8","CP949",$es_file_name));
			chmod(($path."/".iconv("utf-8","CP949",$es_file_name)), 0777);
		}
	}
	if($es_file_re_name != "") $updatesql = ", es_file_re = '$es_file_name', es_file_re_name = '$es_file_re_name'";

	$sql = "update mallstory_large_order set es_status = '$es_status' , es_amount = '$es_amount' $updatesql where lo_ix = '".$lo_ix."' ";
	$db->query($sql);


	echo "<script>alert('변경되었습니다.');opener.document.location.reload();window.close();</script>";
exit;
}

if($act == "es_status_update"){
	if(count($lo_ix) == 0 ) {
		echo "<script language='javascript'>alert(' 선택된 견적이 없습니다.');</script>";
		exit;
	}
	$lo_ix_cnt=count($lo_ix);
	for($i=0;$i<$lo_ix_cnt;$i++) {
		$sql = "update mallstory_large_order set es_status = '".$es_status[$lo_ix[$i]]."' where lo_ix = '".$lo_ix[$i]."' ";
		$db->query($sql);
	}


	echo "<script>alert('수정되었습니다.');top.document.location.reload();</script>";

}


if ($act == "intra_insert"){
	
	$est_mobile = $est_mobile1."-".$est_mobile2."-".$est_mobile3;
	$est_tel = $est_tel1."-".$est_tel2."-".$est_tel3;
	$est_delivery_zip = $est_zip1."-".$est_zip2;
	$est_id = date("YmdHi")."-".rand(1000, 9999);
	
	if($mode == "et_update"){
		$sql = "update ".TBL_MALLSTORY_ESTIMATES." set 
			est_company = '$est_company',
			est_charger = '$est_charger',
			est_delivery_zip = '$est_delivery_zip',
			est_delivery_postion = '$est_delivery_postion',
			est_delivery_postion2 = '$est_delivery_postion2',
			est_email = '$est_email',
			est_tel = '$est_tel',
			est_mobile = '$est_mobile',
			est_etc = '$est_etc', 
			mall_ix = '$mall_ix' 
			where est_ix = '$est_ix'		
			";
			
		$db->query($sql);
		$db->query("DELETE FROM ".TBL_MALLSTORY_ESTIMATES_DETAIL." WHERE est_ix = '".$est_ix."'");

		for ( reset($ESTIMATE_INTRA); $key = key($ESTIMATE_INTRA); next($ESTIMATE_INTRA) ){
			
			$value = pos($ESTIMATE_INTRA);
			$pid = $value[id];
			$pname = $value[pname];
			$pname_str .= $value[pname];
			$pcount    = $value[pcount];
			$options    = $value[options];
			$option = "";
			for($j=0; $j<count($options); $j++) {
				if($j == "0") $option .= $options[$j];
				else $option .= "|".$options[$j];
			}
			$option_serial    = $value[option_serial];
			$coprice = $value[coprice];				
			$sellprice = $value[sellprice];				
			$totalprice = $value[totalprice];	
			$estimate_totalprice = $estimate_totalprice + $totalprice;
			$db->query("insert into ".TBL_MALLSTORY_ESTIMATES_DETAIL."(estd_ix,est_ix, pid,pname,pcount,sellprice,totalprice, options,regdate) values('','$est_ix','$pid','$pname','$pcount','$sellprice','$totalprice', '$option',NOW())");	
		}
		
		$db->query("update  ".TBL_MALLSTORY_ESTIMATES." set est_title ='$pname_str'  WHERE est_ix=$est_ix");
		//echo $sql;
		session_unregister("ESTIMATE_INTRA");
		if(!$EstimateBool){
			$EstimateBool = true;
			session_register("EstimateBool");
		}
		echo "<script>alert('변경되었습니다.');document.location.href='./estimate.intra.php?mode=et_update&est_ix=$est_ix';</script>";

	} else {
		$ucode = $admininfo[charger_id];
		$sql = "insert into ".TBL_MALLSTORY_ESTIMATES." 
			(est_ix,mall_ix,ucode,est_id,est_type,est_company,est_charger,est_email,est_mobile,est_tel,est_delivery_zip,est_delivery_postion,est_delivery_postion2,est_etc,regdate) 
			values
			('','$mall_ix','$ucode','$est_id','$est_type','$est_company','$est_charger','$est_email','$est_mobile','$est_tel','$est_delivery_zip','$est_delivery_postion','$est_delivery_postion2','$est_etc',NOW()) ";
			
		$db->query($sql);
		$db->query("SELECT est_ix FROM ".TBL_MALLSTORY_ESTIMATES." WHERE est_ix=LAST_INSERT_ID()");
		$db->fetch();
		$est_ix = $db->dt[0];
		
		for ( reset($ESTIMATE_INTRA); $key = key($ESTIMATE_INTRA); next($ESTIMATE_INTRA) ){
			
			$value = pos($ESTIMATE_INTRA);
			$pid = $value[id];
			$pname = $value[pname];
			$pname_str .= $value[pname];
			$pcount    = $value[pcount];
			$options    = $value[options];
			$option = "";
			for($j=0; $j<count($options); $j++) {
				if($j == "0") $option .= $options[$j];
				else $option .= "|".$options[$j];
			}
			$option_serial    = $value[option_serial];
			$coprice = $value[coprice];				
			$sellprice = $value[sellprice];				
			$totalprice = $value[totalprice];	
			$estimate_totalprice = $estimate_totalprice + $totalprice;
			$db->query("insert into ".TBL_MALLSTORY_ESTIMATES_DETAIL."(estd_ix,est_ix, pid,pname,pcount,sellprice,totalprice, options,regdate) values('','$est_ix','$pid','$pname','$pcount','$sellprice','$totalprice', '$option',NOW())");	
		}
		
		$db->query("update  ".TBL_MALLSTORY_ESTIMATES." set est_title ='$pname_str' WHERE est_ix=$est_ix");
		
		//echo $sql;
		session_unregister("ESTIMATE_INTRA");
		echo("<script>alert('정상적으로 입력되었습니다.');</script>");
		echo("<script>location.href = 'estimate.list.php';</script>");
	}
}

if ($act == "update"){
	
	for($i=0;$i < count($estd_ix);$i++){
		//echo $i."<br>";
		//$pname = $_POST["pname_".$estd_ix[$i]];
		$ucode = $admininfo[charger_id];
		$pcount = $_POST["pcount_".$estd_ix[$i]];
		$sellprice = str_replace(",","",$_POST["sellprice_".$estd_ix[$i]]);
		$expectprice = str_replace(",","",$_POST["expectprice_".$estd_ix[$i]]);
		$totalprice = $expectprice*$pcount;
		$etc1 = $_POST["etc1_".$estd_ix[$i]];
		
		$sql = "update ".TBL_MALLSTORY_ESTIMATES_DETAIL." set 
			pcount='$pcount',expectprice='$expectprice',totalprice='$totalprice'
			where estd_ix='".$estd_ix[$i]."' and est_ix='$est_ix' ";

		
		
		//echo $sql;
		$db->query($sql);	
	}
	
	$sql = "update ".TBL_MALLSTORY_ESTIMATES." set
			est_title='$est_title',ucode='$ucode',est_nation='$est_nation',est_company='$est_company',est_charger='$est_charger',est_email='$est_email',est_mobile='$est_mobile',est_tel='$est_tel',est_receive_method='$est_receive_method',est_zip='$est_zip1-$est_zip2',est_addr1='$est_addr1',est_addr2='$est_addr2',est_status = '$est_status',est_selltype = '$est_selltype' where est_ix = '".$est_ix."' ";
	$db->query($sql);
	
	//echo $sql;

	echo("<script>alert('정상적으로 수정 되었습니다.');</script>");
	echo("<script>location.href = './estimate.detail.php?est_ix=".$est_ix."';</script>");
}

if ($act == "status_update"){
	$db->query("update  ".TBL_MALLSTORY_ESTIMATES." set est_status = '$est_status' WHERE est_ix = '".$est_ix."'");
	echo("<script>location.href = './estimate.list.php';</script>");
}

if ($act == "delete"){
	
	$db->query("DELETE FROM ".TBL_MALLSTORY_ESTIMATES." WHERE est_ix = '".$est_ix."'");
	$db->query("DELETE FROM ".TBL_MALLSTORY_ESTIMATES_DETAIL." WHERE est_ix = '".$est_ix."'");
	
	echo("<script>alert('정상적으로 삭제 되었습니다.');</script>");
	echo("<script>parent.location.href = './estimate.list.php';</script>");
	

}

if ($act == "send_mail"){
	include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");
	//$path = $_SERVER["DOCUMENT_ROOT"]."/mailing/mailing_join.php";	
	//$email_card_contents_basic = join ('', file ($path));			
	
//	$sql = "insert into ".TBL_MALLSTORY_ESTIMATES." 
//		(est_ix,est_type,est_company,est_charger,est_email,est_mobile,est_plan_date,est_delivery_postion,est_receive_method,est_order_method,est_pass,est_etc,regdate) 
//		values";
	
	$db->query("Select * FROM ".TBL_MALLSTORY_ESTIMATES." WHERE est_ix = '".$est_ix."'");
	$db->fetch();
	
	$mail_info[mem_name] = $db->dt[est_charger];
	$mail_info[mem_mail] = $db->dt[est_email];
	$mail_info[mem_id] = $id;
	$email_card_contents_basic = "요청하신 견적서입니다";
	
	copy("http://".$HTTP_HOST."/admin/estimate/estimate.excel.php?company_id=".$admininfo[company_id]."&est_ix=$est_ix",$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/estimate.xls");
	
	$subject = " ".$mail_info[mem_name]." 님, 요청하신 견적서 입니다..";	
	SendMail($mail_info, $subject,$email_card_contents_basic,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/estimate.xls");

	
	//echo $mail_info[mem_mail];
	echo("<script>alert('정상적으로 메일이 발송되었습니다.');</script>");
	echo("<script>self.close();</script>");
}
function getOptionName($ix){
	$mdb = new Database;
	
	$mdb->query("SELECT option_div  FROM ".TBL_MALLSTORY_PRODUCT_OPTION." where id = '$ix' ");	
	$mdb->fetch();
	return $mdb->dt[option_div];
}

?>
