<?
	include("../class/layout.class");
	include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");

	$db = new Database;
	// 조건절 셋팅

	//	echo $search_searialize_value;
	if($search_searialize_value){
		$unserialize_search_value = unserialize(urldecode($search_searialize_value));
		//print_r ($unserialize_search_value);
		//exit;
		extract($unserialize_search_value);
	}
	if($_POST["update_kind"]){
		$update_kind = $_POST["update_kind"];
	}
	//$where = " where cu.code != '' and cu.code = cmd.code and cmd.gp_ix = mg.gp_ix and gp_level != 0 ";

	if ($admininfo[mall_type] == "O"){
		if($db->dbms_type == "oracle"){
			$where = " where cu.code = cmd.code and  cmd.gp_ix = mg.gp_ix and gp_level != 0 and cu.mem_type in ('M','C','F','S') ";
		}else{
			$where = " where cu.code != '' and cu.code = cmd.code and  cmd.gp_ix = mg.gp_ix and gp_level != 0 and cu.mem_type in ('M','C','F','S') ";
		}
	}else{
		if($db->dbms_type == "oracle"){
			$where = " where cu.code = cmd.code and  cmd.gp_ix = mg.gp_ix and gp_level != 0 and cu.mem_type in ('M','C','F') ";
		}else{
			$where = " where cu.code != '' and cu.code = cmd.code and  cmd.gp_ix = mg.gp_ix and gp_level != 0 and cu.mem_type in ('M','C','F') ";
		}
	}

	if($region != ""){
		$where .= " and addr1 LIKE  '%".$region."%' ";
	}

	if($gp_level != ""){
		$where .= " and mg.gp_level = '".$gp_level."' ";
	}

	if($gp_ix != ""){
		$where .= " and mg.gp_ix = '".$gp_ix."' ";
	}

	if($mem_type != ""){//추가 kbk 13/09/23
		$where .= " and cu.mem_type =  '$mem_type' ";
	}




	$birthday = $birMM.$birDD;



	if($sex == "M" || $sex == "W"){
		$where .= " and sex_div =  '$sex' ";
	}

	if($mailsend_yn != "A" && $mailsend_yn != ""){
			$where .= " and info =  '$mailsend_yn' ";
	}

	if($smssend_yn != "A" && $smssend_yn != ""){
		$where .= " and sms =  '$smssend_yn' ";
	}

	if($db->dbms_type == "oracle"){
		if($search_type != "" && $search_text != ""){
			if($search_type == "name" || $search_type == "mail" || $search_type == "pcs" || $search_type == "tel" ){
				$where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
			}else{
				$where .= " and $search_type LIKE  '%$search_text%' ";
			}
		}
	}else{
		if($search_type != "" && $search_text != ""){
			if($search_type == "name" || $search_type == "mail" || $search_type == "pcs" || $search_type == "tel" ){
				$where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
			}else{
				$where .= " and $search_type LIKE  '%$search_text%' ";
			}
		}
	}

	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;

	if($startDate != "" && $endDate != ""){
		$where .= " and  MID(replace(cu.date,'-',''),1,8) between  $startDate and $endDate ";
	}

	$vstartDate = $vFromYY.$vFromMM.$vFromDD;
	$vendDate = $vToYY.$vToMM.$vToDD;

	if($vstartDate != "" && $vendDate != ""){
		$where .= " and  MID(replace(last,'-',''),1,8) between  $vstartDate and $vendDate ";
	}
	//echo $startDate."~".$endDate;

	//************적립금 *************
	if ($update_kind == "reserve"){

		if($update_type == 2){// 선택회원일때

			$code = array_unique($code);	//중복회원코드 삭제
			for($i=1; $i <count($code);$i++){

				//////////////// 마일리지 적립 시작///////////////////////
				InsertReserveInfo($code[$i],$oid,$order_detail_info[od_ix],$id,$reserve,$state,$use_state,$etc,'mileage',$admininfo);	//마일리지,적립금 통합용 함수 2013-10-24 이학봉
				//////////////// 마일리지 적립 끝///////////////////////

				switch ($state) {
					case '1' : 
						$type = '3';  //적립완료 (수동적립 - 관리자)
						$state_type = 'add';
						break;
					case '2' : 
						$type = '1'; //사용내역 (수동사용 - 관리자)
						$state_type = 'use';
						break;
					default : 
						$type = '';  //사용안함
						break;
				}
				if(!empty($type)){
					
					$mileage_data[uid] = $code[$i];
					$mileage_data[type] = $type;
					$mileage_data[mileage] = abs($reserve);
					$mileage_data[message] = $etc;
					$mileage_data[state_type] = $state_type;
					$mileage_data[save_type] = 'mileage';
					InsertMileageInfo($mileage_data);

				}

			}
			echo("<script>alert('선택회원 전체에게 적립금 $reserve 이 처리 완료되었습니다.');parent.document.location.reload();</script>");
		}else{// 검색회원일때

				//$db->query("INSERT INTO ".TBL_SHOP_RESERVE_INFO." (id,uid,oid,pid,ptprice,payprice,reserve,state,etc,regdate) VALUES ('','$uid','','','','','$reserve','$state','$etc',NOW())");

				$sql = "select
							distinct code
						from
							common_member_edit_history
						where
							1
							$group_by
							order by regdate DESC 
							LIMIT $start, $max";
				
				$db->query($sql);
				$mem_array = $db->fetchall();

				for($i=0;$i<count($mem_array);$i++){
					$uid = $mem_array[$i][code];

					//////////////// 마일리지 적립 시작///////////////////////
					InsertReserveInfo($uid,$oid,$order_detail_info[od_ix],$id,$reserve,$state,$use_state,$etc,'mileage',$admininfo);	//마일리지,적립금 통합용 함수 2013-10-24 이학봉
					//////////////// 마일리지 적립 끝///////////////////////

					//New 마일리지 관리 시스템 JK160323
					switch ($state) {
						case '1' : 
							$type = '3';  //적립완료 (수동적립 - 관리자)
							$state_type = 'add';
							break;
						case '2' : 
							$type = '0'; //사용내역 (수동사용 - 관리자)
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
				echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('검색회원 전체에게 적립금 적립이 완료되었습니다.');parent.document.location.reload();</script>");
		}

	}

	//************회원그룹변경 *************
	if ($update_kind == "group"){
	//	echo $search_searialize_value;

		$reg_url = $_SERVER[SERVER_NAME].$_SERVER[REQUEST_URI];

		if($update_type == 2){// 선택회원일때
			
			$compare_value[1] = array("input_name"=>"gp_ix", "column_name"=>"gp_ix", "name_text"=>"회원그룹");
			$code = array_unique($code);	//중복회원코드 삭제

			for($i=1; $i <count($code);$i++){
				$sql = "select
							cmd.gp_ix
						from
							common_user as cu 
							inner join common_member_detail as cmd on (cu.code = cmd.code)
						where
							cu.code = '".$code[$i]."'";

				$db->query($sql);
				$db->fetch();
				$db_value = $db->dt;

				member_edit_history($code[$i],$compare_value[$i][column_name],$compare_value[$i][name_text],$db_value[$compare_value[$i][column_name]],$update_gp_ix,$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger],$reg_url);

				$sql = "update ".TBL_COMMON_MEMBER_DETAIL." set gp_ix = '$update_gp_ix' where code = '".$code[$i]."' ";
				$db->query($sql);
			}
			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택회원 전체의 그룹변경이 완료되었습니다.');parent.document.location.reload();</script>");

		}else{// 검색회원일때

				//$db->query("INSERT INTO ".TBL_SHOP_RESERVE_INFO." (id,uid,oid,pid,ptprice,payprice,reserve,state,etc,regdate) VALUES ('','$uid','','','','','$reserve','$state','$etc',NOW())");

				if($db->dbms_type == "oracle"){
					$sql = "update ".TBL_COMMON_MEMBER_DETAIL." set gp_ix = '$update_gp_ix'
								where code in (select cu.code from ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_SHOP_GROUPINFO." mg $where)";
					$db->query($sql);

				}else{
						$compare_value[0] = array("input_name"=>"gp_ix", "column_name"=>"gp_ix", "name_text"=>"회원그룹");

						$sql = "select
							distinct code
						from
							common_member_edit_history
						where
							1
							$group_by
							order by regdate DESC 
							LIMIT $start, $max";

						$db->query($sql);
						$data_array = $db->fetchall();

						for($j=0;$j<count($data_array);$j++){
							$code = $data_array[$j][code];
							
							$sql = "select
										cmd.gp_ix
									from
										common_user as cu 
										inner join common_member_detail as cmd on (cu.code = cmd.code)
									where
										cu.code = '".$code."'";

							$db->query($sql);
							$db->fetch();
							$db_value = $db->dt;

							for($i=0;$i<count($compare_value);$i++){
								member_edit_history($code,$compare_value[$i][column_name],$compare_value[$i][name_text],$db_value[$compare_value[$i][column_name]],$update_gp_ix,$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger],$reg_url);
							}

							$update_sql = "update common_member_detail set gp_ix = '".$update_gp_ix."' where code = '".$code."'";

							$db->query($update_sql);
						}
				}
				echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('검색회원 전체의 그룹변경이 완료되었습니다.');window.document.location.reload();</script>");
		}
	}


	//************SMS *************
	if ($update_kind == "sms"){


		$cominfo = getcominfo();
		$sdb = new Database;
		$s = new SMS();
		$s->send_phone = $cominfo[com_phone];
		$s->send_name = $cominfo[com_name];
		$s->admin_mode = true;
		$s->send_type = $send_type;
		$s->send_date = substr($sFromYY,2,2).$sFromMM.$sFromDD;
		$s->send_time = $sDateTime;


		if($update_type == 2){// 선택회원일때
			$code = array_unique($code);	//중복회원코드 삭제
			for($i=1; $i <count($code);$i++){
					if($db->dbms_type == "oracle"){
						$sql = "select AES_DECRYPT(cmd.pcs,'".$db->ase_encrypt_key."') as pcs,
								AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name, cu.id
								from ".TBL_COMMON_USER." cu LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code
								where cu.code ='".$code[$i]."'";
					}else{
						$sql = "select AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs,
								AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cu.id
								from ".TBL_COMMON_USER." cu LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code
								where cu.code ='".$code[$i]."'  ";
					}

					//echo($sql);
					$db->query($sql);
					$db->fetch();

					$mc_sms_text = str_replace("{mem_id}",$db->dt[id],$sms_text);
					$mc_sms_text = str_replace("{mem_name}",$db->dt[name],$mc_sms_text);

					//echo $mail_info[mem_name]."::::".$mail_info[mem_mail].":::	".$mail_info[mem_id];
					$s->dest_phone = str_replace("-","",$db->dt["pcs"]);
					$s->dest_name = $db->dt["name"];
					$s->msg_body =$mc_sms_text;

					$s->sendbyone($admininfo);
			}
			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택회원 전체에게 SMS 가 발송 되었습니다.');parent.document.location.reload();</script>");

		}else{// 검색회원일때

				$sql = "select
							distinct code
						from
							common_member_edit_history
						where
							1
							$group_by
							order by regdate DESC 
							LIMIT $start, $max";

				$db->query($sql);
				$db->fetch();
				$total = $db->dt[total];

				if($db->dbms_type == "oracle"){
					$sql = "select AES_DECRYPT(cmd.pcs,'".$db->ase_encrypt_key."') as pcs,
							AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name, cu.id
							from ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_SHOP_GROUPINFO." mg
							$where
							ORDER BY cmd.date_ DESC
							limit $start,$max  ";
				}else{
					$sql = "select AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs,
							AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cu.id
							from ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_SHOP_GROUPINFO." mg
							$where
							ORDER BY cmd.date DESC
							limit $start,$max  ";
				}
				$db->query($sql);

				for($i=0;$i < $db->total;$i++){
					$db->fetch($i);

					$mc_sms_text = str_replace("{mem_id}",$db->dt[id],$sms_text);
					$mc_sms_text = str_replace("{mem_name}",$db->dt[name],$mc_sms_text);

					//echo $mail_info[mem_name]."::::".$mail_info[mem_mail].":::	".$mail_info[mem_id];
					$s->dest_phone = str_replace("-","",$db->dt["pcs"]);
					$s->dest_name = $db->dt["name"];
					$s->msg_body =$mc_sms_text;

					$s->sendbyone($admininfo);

				}

				if($total > ($start+$max)){
					echo("<script>
					parent.document.getElementById('sended_sms_cnt').innerHTML = '".($start+$max)."';
					parent.document.getElementById('remainder_sms_cnt').innerHTML = '".($total-($start+$max))."';
					if(!parent.document.forms['list_frm'].stop.checked){
						parent.document.forms['list_frm'].sms_send_page.value = ".($sms_send_page+1).";
						parent.document.forms['list_frm'].submit();
					}
					</script>");
				}else{
					echo("<script language='javascript' src='../_language/language.php'></script>
					<script>
					parent.document.getElementById('sended_sms_cnt').innerHTML = '".($total)."';
					parent.document.getElementById('remainder_sms_cnt').innerHTML = '0';
					alert('".$total." '+language_data['member_batch.act.php']['F'][language]);//건의 SMS 가 정상적으로 발송되었습니다
					</script>");
				}
		}
	}



	//************쿠폰 일괄지급 *************
	if ($update_kind == "coupon"){

			$sql = "Select publish_ix,use_date_type,publish_date_differ,publish_type,publish_date_type , regist_date_type, regist_date_differ,use_sdate, use_edate, regist_count
						from ".TBL_SHOP_CUPON_PUBLISH."
						where publish_ix = '".$publish_ix."'";

			$db->query($sql);
			$db->fetch();
			$publish_ix = $db->dt[publish_ix];
            $regist_count = ($db->dt[regist_count] > 0 ? $db->dt[regist_count] : 1);

			if($db->dt[use_date_type] == 1){
				if($db->dt[publish_date_type] == 1){
					$publish_year = date("Y") + $db->dt[publish_date_differ];
				}else{
					$publish_year = date("Y");
				}
				if($db->dt[publish_date_type] == 2){
					$publish_month = date("m") + $db->dt[publish_date_differ];
				}else{
					$publish_month = date("m");
				}
				if($db->dt[publish_date_type] == 3){
					$publish_day = date("d") + $db->dt[publish_date_differ];
				}else{
					$publish_day = date("d");
				}
				$use_sdate = time();
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
			$use_sdate = date("Ymd",$use_sdate);
			$use_date_limit = date("Ymd",$use_date_limit);

			if($update_type == 1){
				if($db->dbms_type == "oracle"){
					$sql = "insert into ".TBL_SHOP_CUPON_REGIST."  select SHOP_CUPON_REGIST_SEQ.nextval as regist_ix , '".$publish_ix."' as publish_ix, cu.code,1,0,
								'$use_sdate','$use_date_limit',null,null, NOW() , null, null
								from ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_SHOP_GROUPINFO." mg
								$where ";
				}else{
					$sql = "insert into ".TBL_SHOP_CUPON_REGIST."  select '' as regist_ix , '".$publish_ix."' as publish_ix, cu.code,1,0,
								'$use_sdate','$use_date_limit',null,null, NOW() , null, null
								from ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_SHOP_GROUPINFO." mg
								$where ";
				}
			//	echo $sql;
			//	exit;
                for ($rc = 0; $rc < $regist_count; $rc++) {
                    $db->query($sql);
                }

				echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('검색회원 전체에게 쿠폰 발급이 완료되었습니다.');</script>");
			}else if($update_type == 2){// 선택회원일때

				$code = array_unique($code);	//중복회원코드 삭제
				for($i=0;$i< count($code) ;$i++){
					if($code[$i]){
						$db->query("Select publish_ix from ".TBL_SHOP_CUPON_REGIST." where publish_ix='$publish_ix' and mem_ix = '".$code[$i]."' ");

						if(!$db->total){
							$sql2 = "insert into ".TBL_SHOP_CUPON_REGIST." (regist_ix,publish_ix,mem_ix,open_yn,use_yn,use_sdate,use_date_limit, regdate)
									values
									('','".$publish_ix."','".$code[$i]."','1','0','$use_sdate','$use_date_limit',NOW())";
							$db->sequences = "SHOP_CUPON_REGIST_SEQ";
						//echo $sql2;
                            for ($rc = 0; $rc < $regist_count; $rc++) {
                                $db->query($sql2);
                            }
						}
					}
				}

				echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택회원 전체에게 쿠폰발급이 완료되었습니다.');</script>");
			}

	}



	//************E-mail 발송 *************
	if ($update_kind == "sendemail"){
		include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");

		$cominfo = getcominfo();
		$db = new Database;
		$idb = new Database;

		$sql = "insert into ".TBL_SHOP_TMP." (mall_ix, design_tmp) values ";
		$sql .= " ( '".$admininfo[mall_ix]."', '$mail_content') ";
		$db->query($sql);

		$db->query("select design_tmp as mail_content from ".TBL_SHOP_TMP." where mall_ix ='".$admininfo[mall_ix]."' ");
		$db->fetch();
		$mail_content = $db->dt[mail_content];
		$db->query("delete from ".TBL_SHOP_TMP." where mall_ix ='".$admininfo[mall_ix]."' ");

		if($save_mail){
			$mail_info[mail_content] = $mail_content;
			$mail_info[mail_subject] = $email_subject;
			$mail_info[mail_ix] = $_POST[mail_ix];

			$mail_ix = mail_box("insert", $mail_info);
		}else{
			$mail_ix = $_POST[mail_ix];
		}

		if($update_type == 2){// 선택회원일때
			$code = array_unique($code);	//중복회원코드 삭제
			for($i=1; $i <count($code);$i++){
					if($db->dbms_type == "oracle"){
						$sql = "select  AES_DECRYPT(IFNULL(cmd.name,'-'),'".$db->ase_encrypt_key."') as name,
								cu.id, AES_DECRYPT(cmd.mail,'".$db->ase_encrypt_key."') as mail,
								substr(AES_DECRYPT(cmd.mail,'".$db->ase_encrypt_key."'),(instr(AES_DECRYPT(cmd.mail,'".$db->ase_encrypt_key."'),'@')+1)) AS ns
								from ".TBL_COMMON_USER." cu
								LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code
								where cu.code ='".$code[$i]."'  ";
					}else{
						$sql = "select  AES_DECRYPT(UNHEX(IFNULL(cmd.name,'-')),'".$db->ase_encrypt_key."') as name,
								cu.id, AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
								SUBSTRING_INDEX(AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."'),'@',-1) AS ns
								from ".TBL_COMMON_USER." cu
								LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code
								where cu.code ='".$code[$i]."'  ";
					}
				//echo $sql;
				//exit;
					$db->query($sql);
					$db->fetch();

					$mail_subject = str_replace("{mem_name}",$db->dt[user_name],$email_subject);

					if (ValidateDNS($db->dt['ns'])){
						$mail_info[mem_name] = $db->dt[name];
						$mail_info[mem_mail] = $db->dt[mail];
						$mail_info[mem_id] = $db->dt[id];
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
						}else{
							//$message = "<b>".$mail_info[mem_name]."</b> 님의 이메일 발송이 실패했습니다.<br>";
							$sql = "insert into shop_mailling_history
									(mh_ix,mail_ix,ucode, sended_mail, check_key, is_error, error_text, regdate)
									values
									('','".$mail_ix."','".$code[$i]."','".$db->dt[mail]."','$check_key','1','SEND_ERROR', NOW())
									";
							//echo $sql;
							$idb->sequences = "SHOP_MAILLING_HISTORY_SEQ";
							$idb->query($sql);
						}

					//echo $message;
					}else{
							$sql = "insert into shop_mailling_history
								(mh_ix,mail_ix,ucode, sended_mail, check_key, is_error, error_text, regdate)
								values
								('','".$mail_ix."','".$code[$i]."','".$db->dt[mail]."','$check_key','1','DNS_ERROR', NOW())
								";
							//echo $sql;
							$idb->sequences = "SHOP_MAILLING_HISTORY_SEQ";
							$idb->query($sql);
						//echo "DNS ERROR";
					}


			}

			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택회원에게 E-mail이 정상적으로 발송되었습니다');</script>");
		}else{// 검색회원일때
				if(!$email_max){
					$email_max = 100;
				}
				if ($email_send_page == ''){
					$start = 0;
					$email_send_page  = 1;
				}else{
					$start = ($email_send_page - 1) * $email_max;
				}

				$sql = "select count(*) as total from ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_SHOP_GROUPINFO." mg $where  ";
				//echo $sql;
				$db->query($sql);
				$db->fetch();
				$total = $db->dt[total];

				if($db->dbms_type == "oracle"){
					$sql = "select cu.code, AES_DECRYPT(pcs,'".$db->ase_encrypt_key."') as  pcs,
							AES_DECRYPT(name,'".$db->ase_encrypt_key."') as name, id, AES_DECRYPT(mail,'".$db->ase_encrypt_key."') as mail, substr(AES_DECRYPT(mail,'".$db->ase_encrypt_key."'),(instr(AES_DECRYPT(mail,'".$db->ase_encrypt_key."'),'@')+1)) AS ns
							from ".TBL_COMMON_USER." cu ,
							".TBL_COMMON_MEMBER_DETAIL." cmd ,
							".TBL_SHOP_GROUPINFO." mg $where
							ORDER BY cmd.date_ DESC
							limit $start,$email_max  ";
				}else{
					$sql = "select cu.code, AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as  pcs,
							AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name, id, AES_DECRYPT(UNHEX(mail),'".$db->ase_encrypt_key."') as mail, SUBSTRING_INDEX(AES_DECRYPT(UNHEX(mail),'".$db->ase_encrypt_key."'),'@',-1) AS ns
							from ".TBL_COMMON_USER." cu ,
							".TBL_COMMON_MEMBER_DETAIL." cmd ,
							".TBL_SHOP_GROUPINFO." mg $where
							ORDER BY cmd.date DESC
							limit $start,$email_max  ";
				}
				//echo $sql;
				//exit;
				$db->query($sql);
				//echo $db->total;
				//exit;

				$send_cnt =0;
				for($i=0;$i < $db->total;$i++){
					$db->fetch($i);

					$mail_subject = str_replace("{mem_name}",$db->dt[name],$email_subject);

					if (ValidateDNS($db->dt['ns'])){
						$mail_info[mem_name] = $db->dt[name];
						$mail_info[mem_mail] = $db->dt[mail];
						$mail_info[mem_id] = $db->dt[id];
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
									('','".$mail_ix."','".$db->dt[code]."','".$db->dt[mail]."','$check_key', NOW())";
							//echo $sql;
							$idb->sequences = "SHOP_MAILLING_HISTORY_SEQ";
							$idb->query($sql);
						}else{
							//$message = "<b>".$mail_info[mem_name]."</b> 님의 이메일 발송이 실패했습니다.<br>";
							$sql = "insert into shop_mailling_history
									(mh_ix,mail_ix,ucode, sended_mail, check_key,is_error, error_text, regdate)
									values
									('','".$mail_ix."','".$db->dt[code]."','".$db->dt[mail]."','$check_key', '1','SEND_ERROR', NOW())";
							//echo $sql;
							$idb->sequences = "SHOP_MAILLING_HISTORY_SEQ";
							$idb->query($sql);
						}

						$send_cnt++;
						//echo $message;
					}else{
							$sql = "insert into shop_mailling_history
								(mh_ix,mail_ix,ucode, sended_mail, check_key, is_error, error_text, regdate)
								values
								('','".$mail_ix."','".$db->dt[code]."','".$db->dt[mail]."','$check_key','1','DNS_ERROR', NOW())
								";
							//echo $sql;
							$idb->sequences = "SHOP_MAILLING_HISTORY_SEQ";
							$idb->query($sql);
						echo "DNS ERROR";
					}

				}

				if($total > ($start+$email_max)){
					echo("<script>
					parent.document.getElementById('sended_email_cnt').innerHTML = '".($start+$email_max)."';
					parent.document.getElementById('remainder_email_cnt').innerHTML = '".($total-($start+$email_max))."';
					if(!parent.document.forms['list_frm'].stop.checked){
						parent.document.forms['list_frm'].email_send_page.value = ".($email_send_page+1).";
						parent.document.forms['list_frm'].submit();
					}
					</script>");
				}else{
					if($send_cnt){
						echo("<script language='javascript' src='../_language/language.php'></script>
						<script>
						parent.document.getElementById('sended_email_cnt').innerHTML = '".(($email_send_page-1)*$email_max + $send_cnt)."';
						parent.document.getElementById('remainder_email_cnt').innerHTML = '0';
						alert('".(($email_send_page-1)*$email_max + $send_cnt)." '+language_data['member_batch.act.php']['J'][language]);//건의 이메일이 정상적으로 발송되었습니다
						</script>");
					}else{
						echo("<script language='javascript' src='../_language/language.php'></script>
						<script>
						parent.document.getElementById('sended_email_cnt').innerHTML = '".($send_cnt)."';
						parent.document.getElementById('remainder_email_cnt').innerHTML = '0';
						alert(language_data['member_batch.act.php']['K'][language]);//'발송대상이 존재하지 않습니다. 메일링 수신거부 회원은 메일링 대상이 아닙니다. '
						</script>");
					}
				}
		}
		//echo("<script>top.location.href = 'baymoney.pop.php?ab_ix=$uid';</script>");

	}

	function ValidateDNS($host)
	{
		return (checkdnsrr($host, ANY))? true: false;
	}


?>