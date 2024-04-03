<?
/* CRM 회원등록소스 2014-07-14 JBG
*
*/
	include("../class/layout.class");
	include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
	include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");
	
	//SMS 발송
	if($mode == "sms"){

		$cominfo = getcominfo();
		$db = new Database;
		$sdb = new Database;

		if($send_time_type == "1"){
			$send_time = $send_time_sms." ".$send_time_hour.":".$send_time_minite.":00";
		}else{
			$send_time = 0;
		}
		
		
		$s = new SMS();
		$s->send_phone = $cominfo[com_phone];
		$s->send_name = $cominfo[com_name];
		$s->admin_mode = true;
		$s->send_type = $send_type;
		$s->send_date = substr($sFromYY,2,2).$sFromMM.$sFromDD;
		$s->send_time = $send_time;
		
		
		$sms_text_array = explode('^|^',$_REQUEST[sms_text_array][0]);

		if($select_sms_type == 'SMS' && $sms_text_count > 79){
			for($z=0; $z < count($sms_text_array); $z ++){
				
				$s->dest_phone = str_replace("-","",$receive_phone);
				$s->dest_name = $dest_name;
				$s->dest_code = $code;
				
				$s->msg_body =$sms_text_array[$z];					
				$s->send_title	=	"";
				$s->send_type = "1";
				$s->sendbyone($admininfo);
				sleep(1);
			}
		}else{
		
			$s->dest_phone = str_replace("-","",$receive_phone);
			$s->dest_name = $dest_name;
			$s->dest_code = $code;
			$s->msg_body =$sms_text;					
			
			$s->sendbyone($admininfo);
		}
		
		if($oid!=""){
			//set_order_status($oid,"","SMS 전송 - ".$sms_text,$_SESSION["admininfo"]["charger"]."(".$_SESSION["admininfo"]["charger_id"].")","");
			$user_mood_state="3";
			$bbs_div="122"; //SMS 분류

            if(empty($order_date)) {
                $sql = "SELECT order_date FROM shop_order WHERE oid = '$oid'";
                $db->query($sql);
                $oinfo = $db->fetch();
                $order_date = $oinfo['order_date'];
            }

			$db->query("insert into shop_order_memo(om_ix,ucode,oid,order_date,order_from,memo,counselor,counselor_ix,charger,charger_ix,urgency_yn,call_type,call_action_yn,call_action_date,call_action_time,call_action_state,user_mood_state,memo_div,memo_state,memo_state_change_date,complete_date,regdate) values('','$ucode','$oid','$order_date','$order_from','".str_replace("'",'&#39;',$sms_text)."','".$_SESSION["admininfo"]["charger"]."','".$_SESSION["admininfo"]["charger_ix"]."','$md_name','$md_code','$urgency_yn','$call_type','$call_action_yn','$call_action_date','$call_action_time','$call_action_state','$user_mood_state','$bbs_div','$memo_state','$memo_state_change_date','$complete_date',NOW())");
		}

		echo "발송되었습니다";
		
	}
	
	//이메일 발송
	if ($mode == "email"){

		$cominfo = getcominfo();
		$db = new Database;
		$idb = new Database;

		if($save_mail){
			$mail_info[mail_content] = $mail_content;
			$mail_info[mail_subject] = $email_subject;
			$mail_info[mail_ix] = $_POST[mail_ix];

			$mail_ix = mail_box("insert", $mail_info);
		}else{
			$mail_ix = $_POST[mail_ix];
		}
			
		$mail_subject = str_replace("{mem_name}",$db->dt[user_name],$email_subject);

		//if (ValidateDNS($db->dt['ns'])){
			$mail_info[mem_name] = '';
			$mail_info[mem_mail] = $mail;
			$mail_info[mem_id] = '';
			if($i==0) $mail_info[mail_cc] = $mail_cc;

			$check_key = md5(uniqid());
			$mail_content = str_replace("{check_key}",$check_key,$mail_content);
			$__mail_content = $mail_content."<P style='DISPLAY: none'><IMG src='http://".$HTTP_HOST."/mailling/mail_open.php?check_key=$check_key'></P>";

			//if (SendMail($mail_info, $mail_subject,$__mail_content,"")){
			if (SendMail($mail_info, $mail_subject,$__mail_content,"","","Y")){//SendMail 함수에 전달인자 값이 추가되었기에 여기서도 추가해야 사용자한테 발송됨 kbk 13/09/17
				//$message = "<b>".$mail_info[mem_name]."</b> 님의 이메일이 발송되었습니다.<br>";
				$sql = "insert into shop_mailling_history
						(mh_ix,mail_ix,ucode, sended_mail, check_key, regdate)
						values
						('','".$mail_ix."','".$code[$i]."','".$db->dt[mail]."','$check_key', NOW())";
				//echo $sql;
				$idb->sequences = "SHOP_MAILLING_HISTORY_SEQ";
				$idb->query($sql);
				echo("이메일이 발송되었습니다");
			}else{
				//$message = "<b>".$mail_info[mem_name]."</b> 님의 이메일 발송이 실패했습니다.<br>";
				$sql = "insert into shop_mailling_history
						(mh_ix,mail_ix,ucode, sended_mail, check_key, is_error, error_text, regdate)
						values
						('','".$mail_ix."','".$code[$i]."','".$db->dt[mail]."','$check_key','1','SEND_ERROR', NOW())
						";
				$idb->sequences = "SHOP_MAILLING_HISTORY_SEQ";
				$idb->query($sql);
			}

	//echo("<script>show_alert('선택회원에게 E-mail이 정상적으로 발송되었습니다');</script>");
	}
	
	//예치금 적립
	if ($act == "deposit_insert"){
		
		InsertDepositInfo($use_type,$state,$use_state,$oid,$deposit_ix,$deposit,$uid,$etc,$admininfo);
		
		echo("예치금이 등록되었습니다");

	}

	//적립금 적립
	if ($act == "reserve_insert"){
		if($state == "2"){
			$use_state = $use_state_cancel;
			
		}else{
			$use_state = $use_state_add;

		}
		
		InsertReserveInfo($uid,$oid,$od_ix,$id,$reserve,$state,$use_state,$etc,'mileage',$admininfo);

		//New 마일리지 시스템 JK160323
		switch ($state) {
			case '1' : 
				$type = '7';  //적립완료 (수동적립 - 관리자)
				$state_type = 'add';
				break;
			case '2' : 
				$type = '2'; //사용내역 (수동사용 - 관리자)
				$state_type = 'use';
				break;
			default : 
				$type = '';  //사용안함
				break;
		}
		if(!empty($type)){
			$mileage_data[uid] = $uid;
			$mileage_data[type] = $type;
			$mileage_data[mileage] = abs($reserve);
			$mileage_data[message] = $etc;
			$mileage_data[state_type] = $state_type;
			$mileage_data[save_type] = 'mileage';
			InsertMileageInfo($mileage_data);
		}
	}

	//포인트 적립
	if ($act == "point_insert"){

		if($state == "2"){
			$use_state = $use_state_cancel;
		}else{
			$use_state = $use_state_add;
		}

		InsertReserveInfo($uid,$oid,$od_ix,$id,$reserve,$state,$use_state,$etc,'point',$admininfo);
		
		/*신규 포인트,마일리지 접립 함수 JK 160405*/
		switch ($state) {
			case '1' : 
				$type = '7';  //적립완료 (수동적립 - 관리자)
				$state_type = 'add';
				break;
			case '2' : 
				$type = '2'; //사용내역 (수동사용 - 관리자)
				$state_type = 'use';
				break;
			default : 
				$type = '';  //사용안함
				break;
		}
		if(!empty($type)){
			$mileage_data[uid] = $uid;
			$mileage_data[type] = $type;
			$mileage_data[mileage] = abs($reserve);
			$mileage_data[message] = $etc;
			$mileage_data[state_type] = $state_type;
			$mileage_data[save_type] = 'point';
			$mileage_data[oid] = $oid;
			$mileage_data[od_ix] = $od_ix;
			$mileage_data[pid] = $id;
			InsertMileageInfo($mileage_data);
		}
	
	}
	
	//쿠폰발급
	if($act == "coupon"){
	
	
	for($i=0;$i < count($coupon_ix);$i++){

		$sql = "SELECT
					publish_ix,use_date_type,publish_date_differ,publish_type,publish_date_type ,
					regist_date_type, regist_date_differ, date_format(use_sdate,'%Y%m%d') as use_sdate, 
					date_format(use_edate,'%Y%m%d') as use_edate,date_format(regdate,'%Y%m%d') as regdate,
					regist_count
				FROM ".TBL_SHOP_CUPON_PUBLISH."
				WHERE publish_ix = '".$coupon_ix[$i]."'";
		$db->query($sql);
		$db->fetch();
		$publish_ix = $db->dt[publish_ix];
        $regist_count = ($db->dt[regist_count] > 0 ? $db->dt[regist_count] : 1);
		$p_year=substr($db->dt["regdate"],0,4);
		$p_month=substr($db->dt["regdate"],4,2);
		$p_day=substr($db->dt["regdate"],6,2);

		if($db->dt[use_date_type] == 1){
			if($db->dt[publish_date_type] == 1){
				$publish_year = $p_year + $db->dt[publish_date_differ];
			}else{
				$publish_year = $p_year;
			}
			if($db->dt[publish_date_type] == 2){
				$publish_month = $p_month + $db->dt[publish_date_differ];
			}else{
				$publish_month = $p_month;
			}
			if($db->dt[publish_date_type] == 3){
				$publish_day = $p_day + $db->dt[publish_date_differ];
			}else{
				$publish_day = $p_day;
			}
			$use_sdate=mktime(0,0,0,$p_month,$p_day,$p_year);
			$use_date_limit = mktime(0,0,0,$publish_month,$publish_day,$publish_year);

			//$event_date = mktime(0,0,0,$event_meonth,$event_day+$order[end_date_differ],$evet_year);

		}else if($db->dt[use_date_type] == 2){
			if($db->dt[regist_date_type] == 1){
				$regist_year = date("Y") + $db->dt[regist_date_differ];
			}else{
				$regist_year = date("Y");
			}
			if($db->dt[regist_date_type] == 2){
				$regist_month = date("m") + $db->dt[regist_date_differ];
			}else{
				$regist_month = date("m");
			}
			if($db->dt[regist_date_type] == 3){
				$regist_day = date("d") + $db->dt[regist_date_differ];
			}else{
				$regist_day = date("d");
			}
			$use_sdate = time();
			$use_date_limit = mktime(0,0,0,$regist_month,$regist_day,$regist_year);
		}else if($db->dt[use_date_type] == 3){
			$use_sdate = mktime(0,0,0,substr($db->dt[use_sdate],4,2),substr($db->dt[use_sdate],6,2),substr($db->dt[use_sdate],0,4));
			$use_date_limit = mktime(0,0,0,substr($db->dt[use_edate],4,2),substr($db->dt[use_edate],6,2),substr($db->dt[use_edate],0,4));

		}

		if($db->dt[publish_type] == "1" || $db->dt[publish_type] == "2"){
			$use_sdate = date("Ymd",$use_sdate);
			$use_date_limit = date("Ymd",$use_date_limit);

			if($dupe_check){
		
				$sql2 = "insert into ".TBL_SHOP_CUPON_REGIST." (regist_ix,publish_ix,mem_ix,open_yn,use_yn,use_sdate, use_date_limit, regdate)
						values
						('','".$publish_ix."','".$code."','1','0','$use_sdate','$use_date_limit',NOW())";

				//echo $sql2;
				$db->sequences = "SHOP_CUPON_REGIST_SEQ";
                for ($rc = 0; $rc < $regist_count; $rc++) {
                    $db->query($sql2);
                }
	
			}else{
			
				$db->query("Select publish_ix from ".TBL_SHOP_CUPON_REGIST." where publish_ix='$publish_ix' and mem_ix = '".$code[$i]."' ");

				if(!$db->total){
					$sql2 = "insert into ".TBL_SHOP_CUPON_REGIST." (regist_ix,publish_ix,mem_ix,open_yn,use_yn,use_sdate, use_date_limit, regdate)
							values
							('','".$publish_ix."','".$code."','1','0','$use_sdate','$use_date_limit',NOW())";

					//echo $sql2;
					$db->sequences = "SHOP_CUPON_REGIST_SEQ";
                    for ($rc = 0; $rc < $regist_count; $rc++) {
                        $db->query($sql2);
                    }
				}
				
			}
		}
	}
	
	echo "쿠폰이 발급되었습니다";
	exit;
}

?>