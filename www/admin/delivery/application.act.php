<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");


session_start();

$db = new Database;



if ($_POST["act"] == "insert"){
	
	if(!$group_ix){
		$group_ix = $parent_group_ix;
	}
	
	//print_r($_POST);
	/*
	$sql = "insert into mallstory_addressbook
					(ab_ix,group_ix,user_name,mobile,email,mail_yn,sms_yn,url,page,com_name,phone,fax,homepage,com_address,marketer,memo,regdate) 
					values
					('$ab_ix','$group_ix','$user_name','$mobile','$email','$mail_yn','$sms_yn','$url','$page','$com_name','$phone','$fax','$homepage','$com_address','$marketer','$memo',NOW())";
	*/
	$application_no = date("YmdHi")."-".rand(1000, 9999);
	$addressee_certification_no = $certification_no1."-".$certification_no2;
	$addressee_zip = $addressee_zip1."-".$addressee_zip2;
	
	$sql =  "insert into delivery_info
					(di_ix,group_ix,application_no, name_type, shipper_company_name, shipper_first_name,shipper_last_name,shipper_phone,shipper_mobile,shipper_fax,shipper_email,shipper_address1,shipper_address2,addressee_div,addressee_name,addressee_eng_name,addressee_phone,
					addressee_mobile,addressee_certification_no,addressee_fax,addressee_email,addressee_zip,addressee_address1,addressee_address2,memo,status,delivery_type,weight, order_name,order_url, order_confirm_no, tracking_no, order_memo, regdate) 
					values
					('$di_ix','$group_ix','$application_no','$name_type','$shipper_company_name','$shipper_first_name','$shipper_last_name','$shipper_phone','$shipper_mobile','$shipper_fax','$shipper_email','$shipper_address1','$shipper_address2','$addressee_div','$addressee_name','$addressee_eng_name','$addressee_phone',
					'$addressee_mobile','$addressee_certification_no','$addressee_fax','$addressee_email','$addressee_zip','$addressee_address1','$addressee_address2','$memo','AC','$delivery_type','$weight','$order_name','$order_url', '$order_confirm_no', '$tracking_no','$order_memo',NOW())";
					
$db->query($sql);
$db->query("SELECT di_ix FROM delivery_info WHERE di_ix=LAST_INSERT_ID()");
$db->fetch();
$di_ix = $db->dt[di_ix];
//echo $sql;
//exit;
//echo count($goodsinfo);
//exit;
	for($i=0;$i < count($goodsinfo);$i++){
		if($goodsinfo[$i][select]){
		$sql = "insert into delivery_detail_info
						(ddi_ix,di_ix, item_name,item_name,brand,price,unit,amount, regdate) 
						values
						('','".$di_ix."','".$goodsinfo[$i]["item_div"]."','".$goodsinfo[$i]["item_name"]."','".$goodsinfo[$i]["brand"]."','".$goodsinfo[$i]["price"]."','".$goodsinfo[$i]["unit"]."','".$goodsinfo[$i]["amount"]."', NOW())";
		//echo $sql;
		$db->query($sql);
		}
	}

	//$db->query($sql);
	echo("<script>alert('정삭적으로 입력 되었습니다.');parent.document.location.href='application_list.php';</script>");
	exit;
}

if ($act == "update"){
	if(!$group_ix){
		$group_ix = $parent_group_ix;
	}			
	
	$addressee_certification_no = $certification_no1."-".$certification_no2;
	$addressee_zip = $addressee_zip1."-".$addressee_zip2;
	/*
	$sql = "update delivery_info set 
					group_ix='$group_ix',shipper_name='$shipper_name',shipper_phone='$shipper_phone',shipper_mobile='$shipper_mobile',
					shipper_fax='$shipper_fax',shipper_email='$shipper_email',shipper_address1='$shipper_address1',shipper_address2='$shipper_address2',addressee_div='$addressee_div',
					addressee_name='$addressee_name',addressee_eng_name='$addressee_eng_name',addressee_phone='$addressee_phone',addressee_mobile='$addressee_mobile',
					addressee_certification_no='$addressee_certification_no',addressee_fax='$addressee_fax',addressee_email='$addressee_email',addressee_zip='$addressee_zip',
					addressee_address1='$addressee_address1',addressee_address2='$addressee_address2',memo='$memo',status='$status',delivery_type='$delivery_type',weight='$weight',order_name='$order_name',order_url='$order_url', order_confirm_no='$order_confirm_no', tracking_no='$tracking_no',order_memo='$order_memo'
					where di_ix='$di_ix' ";
*/
	$sql = "update delivery_info set					
			group_ix='$group_ix',name_type='$name_type',shipper_company_name='$shipper_company_name',shipper_first_name='$shipper_first_name',shipper_last_name='$shipper_last_name',
			shipper_phone='$shipper_phone',shipper_mobile='$shipper_mobile',shipper_email='$shipper_email',
			shipper_address1='$shipper_address1',shipper_address2='$shipper_address2',addressee_div='$addressee_div',addressee_name='$addressee_name',
			addressee_phone='$addressee_phone',addressee_mobile='$addressee_mobile',addressee_certification_no='$addressee_certification_no',
			addressee_email='$addressee_email',addressee_zip='$addressee_zip',addressee_address1='$addressee_address1',addressee_address2='$addressee_address2',
			status='$status',delivery_type='$delivery_type',weight='$weight',order_name='$order_name',order_url='$order_url',order_confirm_no='$order_confirm_no',tracking_no='$tracking_no',
			order_memo='$order_memo',delivery_fee='$delivery_fee',delivery_payment_yn='$delivery_payment_yn' where di_ix='$di_ix' ";
//echo $sql;
//exit;
	$db->query($sql);
	
	
	$sql = "update delivery_detail_info set insert_yn='N' where di_ix='$di_ix' ";
	$db->query($sql);
								
	for($i=0;$i < count($goodsinfo);$i++){
		
	
		if($goodsinfo[$i][select]){
		$db->query("SELECT ddi_ix FROM delivery_detail_info WHERE ddi_ix = '".$goodsinfo[$i][select]."' and di_ix='$di_ix'  ");
		
			if($db->total){
				$db->fetch();
				
				$sql = "update delivery_detail_info set 
								item_div='".$goodsinfo[$i]["item_div"]."',item_name='".$goodsinfo[$i]["item_name"]."',brand='".$goodsinfo[$i]["brand"]."',price='".$goodsinfo[$i]["price"]."',unit='".$goodsinfo[$i]["unit"]."',amount='".$goodsinfo[$i]["amount"]."',insert_yn='Y'
								where ddi_ix='".$db->dt[ddi_ix]."' and di_ix='$di_ix'  ";
				echo $sql;
				$db->query($sql);

			}else{
				$sql = "insert into delivery_detail_info
								(ddi_ix,di_ix, item_div,item_name,brand,price,unit,amount,insert_yn, regdate) 
								values
								('','".$di_ix."','".$goodsinfo[$i]["item_div"]."','".$goodsinfo[$i]["item_name"]."','".$goodsinfo[$i]["brand"]."','".$goodsinfo[$i]["price"]."','".$goodsinfo[$i]["unit"]."','".$goodsinfo[$i]["amount"]."','Y',NOW())";
				//echo $sql;
				$db->query($sql);
			}
		}
	}
	
	$sql = "delete from delivery_detail_info where insert_yn='N' and di_ix='$di_ix'  ";
	$db->query($sql);
	
	
	echo("<script>alert('정삭적으로 수정 되었습니다. ');parent.document.location.reload();</script>");
}


if ($act == "delete"){
	
	$db->query("delete from delivery_info where  di_ix='$di_ix' ");
	
	$sql = "delete from delivery_detail_info where di_ix='$di_ix'  ";
	$db->query($sql);
	
	echo("<script>alert('정삭적으로 삭제 되었습니다.');parent.document.location.reload();</script>");
}


if ($act == "sample_excel_down"){
	header( "Content-type: application/vnd.ms-excel" ); 
	header( "Content-Disposition: attachment; filename=".iconv("utf-8","CP949","메일링/SMS주소록_일괄등록")."_sample.csv" );
	header( "Content-Description: PHP5 Generated Data" ); 
	header("Content-charset=euc-kr");
	
	$mstring = "이름,핸드폰,이메일\n";
	$mstring .= "홍길동,010-1234-1234,sample@mallstory.com\n";
	$mstring .= "갑돌이,010-5678-5678,sample2@mallstory.com\n";

	echo iconv("utf-8","CP949",$mstring);
}
if ($act == "excel_insert"){
	require_once '../product/Excel/reader.php';
	
	if ($addressbook_excel_size > 0){	
		copy($addressbook_excel, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".iconv("utf-8","CP949",$addressbook_excel_name));
	}
	/*
	if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".iconv("utf-8","CP949",$addressbook_excel_name))){
		echo "있음";
	}else{
		echo "없음";
	}
	*/
	//exit;
	// ExcelFile($filename, $encoding);
	$data = new Spreadsheet_Excel_Reader();
	
	
	// Set output Encoding.
	$data->setOutputEncoding('CP949');
	$data->read($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".iconv("utf-8","CP949",$addressbook_excel_name));
	
	error_reporting(E_ALL ^ E_NOTICE);
	$shift_num = 0;
	
//	echo $data->sheets[0]['numRows'];
	//exit;
	for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {		
		//print_r($data->sheets[0]['cells'][$i]);
		//exit;
			$user_name = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][1+$shift_num]);
			$mobile = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][2+$shift_num]);
			$email = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][3+$shift_num]);
						
			if(!$group_ix){
				$group_ix = $parent_group_ix;
			}
			$sql = "insert into mallstory_addressbook
							(ab_ix,group_ix,user_name,mobile,email,mail_yn,sms_yn,url,page,com_name,phone,fax,homepage,com_address,marketer,memo,regdate) 
							values
							('','$group_ix','$user_name','$mobile','$email','$mail_yn','$sms_yn','$url','$page','$com_name','$phone','$fax','$homepage','$com_address','$marketer','$memo',NOW())";
			
			$db->query($sql);
			
	}
	
	echo("<script>alert('메일링/SMS 주소록이 정삭적으로 입력 되었습니다.');parent.document.location='addressbook_list.php';</script>");
}
?>