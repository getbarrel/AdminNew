<?
include($_SERVER["DOCUMENT_ROOT"]."/include/mail_smtp.php");
//include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
$db = new Database;

$where = " where cu.code != '' and cu.code = cmd.code ";
if($region != ""){
	$where .= " and addr1 LIKE  '%".$region."%' ";
}



if($mailsend_yn == "Y"){	
	$where .= " and info =  1 ";
}

if($smssend_yn == "Y"){	
	$where .= " and sms =  1 ";
}

if($search_type != "" && $search_text != ""){	
	$where .= " and $search_type LIKE  '%$search_text%' ";
}

$startDate = $FromYY.$FromMM.$FromDD;
$endDate = $ToYY.$ToMM.$ToDD;
	
if($startDate != "" && $endDate != ""){	
	$where .= " and  MID(replace(date,'-',''),1,8) between  '$startDate' and '$endDate' ";
}

$vstartDate = $vFromYY.$vFromMM.$vFromDD;
$vendDate = $vToYY.$vToMM.$vToDD;
	
if($vstartDate != "" && $vendDate != ""){	
	$where .= " and  MID(replace(last,'-',''),1,8) between  '$vstartDate' and '$vendDate' ";
}


 
$sql = "Select cu.code, cmd.name, cu.id, cmd.mail, cmd.pcs,  SUBSTRING_INDEX(cmd.mail,'@',-1) AS ns from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd $where ";
$db->query($sql);	
//echo $sql;

if($db->total){
	$cominfo = getcominfo();
	$sdb = new Database;
	$s = new SMS();
	$s->send_phone = $cominfo[com_phone];
	$s->send_name = $cominfo[com_name];
	$s->admin_mode = true;
	$s->send_type = $send_type;
	$s->send_date = substr($sFromYY,2,2).$sFromMM.$sFromDD;
	$s->send_time = $sDateTime;

	for($i=0;$i < $db->total; $i++){
		$db->fetch($i);	

			
			$mc_sms_text = str_replace("{mem_id}",$db->dt[id],$mc_sms_text);
			$mc_sms_text = str_replace("{mem_name}",$db->dt[name],$mc_sms_text);
			
			//echo $mail_info[mem_name]."::::".$mail_info[mem_mail].":::	".$mail_info[mem_id];
			$s->dest_phone = str_replace("-","",$db->dt["pcs"]);
			$s->dest_name = $db->dt["name"];
			$s->msg_body =$mc_sms_text;
			
			$s->sendbyone($admininfo);

			//SendSMS($db->dt["code"],$db->dt["id"],$db->dt["pcs"],$mc_sms_text);
			
		
	}
	
	echo "<script>alert('정상적으로 SMS가 ".$db->total."통  발송되었습니다.');history.back();</script>";
	
	
}else{
	echo "DATA 가 존재하지 않습니다.";
}


		
?>