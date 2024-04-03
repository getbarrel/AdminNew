<?
	include("../class/layout.class");
	
	include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");

	$db = new Database;

	if($search_searialize_value){
		$unserialize_search_value = unserialize(urldecode($search_searialize_value));
		extract($unserialize_search_value);
	}

	if($_POST["update_kind"]){
		$update_kind = $_POST["update_kind"];
	}

	if ($admininfo[mall_type] == "O"){
		if($db->dbms_type == "oracle"){
			$where = " where cu.code = cmd.code    ";
		}else{
			$where = " where cu.code != '' and cu.code = cmd.code  and cmd.gp_ix=mg.gp_ix  ";
		}
	}else{
		if($db->dbms_type == "oracle"){
			$where = " where cu.code = cmd.code     ";
		}else{
			$where = " where cu.code != '' and cu.code = cmd.code  and cmd.gp_ix=mg.gp_ix  ";
		}
	}

	//회원그룹 검색
	if(is_array($gid)){
		for($i=0;$i < count($gid);$i++){
			if($gid[$i] != ""){
				if($gid_str == ""){
					$gid_str .= "'".$gid[$i]."'";
				}else{
					$gid_str .= ",'".$gid[$i]."' ";
				}
			}
		}

		if($gid_str != ""){
			$where .= " AND cmd.gp_ix in ($gid_str) ";
		}
	}else{
		if($gid){
			$where .= " AND cmd.gp_ix = '$gid' ";
		}
	}

	//회원구분 검색
	if(is_array($mem_div)){
		for($i=0;$i < count($mem_div);$i++){
			if($mem_div[$i] != ""){
				if($mem_div_str == ""){
					$mem_div_str .= "'".$mem_div[$i]."'";
				}else{
					$mem_div_str .= ",'".$mem_div[$i]."' ";
				}
			}
		}

		if($mem_div_str != ""){
			$where .= " AND cu.mem_div in ($mem_div_str) ";
		}
	}else{
		if($mem_div){
			$where .= " AND cu.mem_div = '$mem_div' ";
		}
	}

	//SMS수신여부 검색
	if(is_array($smssend_yn)){
		for($i=0;$i < count($smssend_yn);$i++){
			if($smssend_yn[$i] != ""){
				if($smssend_yn_str == ""){
					$smssend_yn_str .= "'".$smssend_yn[$i]."'";
				}else{
					$smssend_yn_str .= ",'".$smssend_yn[$i]."' ";
				}
			}
		}

		if($smssend_yn_str != ""){
			$where .= " AND sms in ($smssend_yn_str) ";
		}
	}else{
		if($smssend_yn){
			$where .= " AND sms = '$smssend_yn' ";
		}
	}

	//메일 수신여부 검색
	if(is_array($mailsend_yn)){
		for($i=0;$i < count($mailsend_yn);$i++){
			if($mailsend_yn[$i] != ""){
				if($mailsend_yn_str == ""){
					$mailsend_yn_str .= "'".$mailsend_yn[$i]."'";
				}else{
					$mailsend_yn_str .= ",'".$mailsend_yn[$i]."' ";
				}
			}
		}

		if($mailsend_yn_str != ""){
			$where .= " AND info in ($mailsend_yn_str) ";
		}
	}else{
		if($mailsend_yn){
			$where .= " AND info = '$mailsend_yn' ";
		}
	}


	//성별검색 
	if(is_array($sex)){
		for($i=0;$i < count($sex);$i++){
			if($sex[$i] != ""){
				if($sex_str == ""){
					$sex_str .= "'".$sex[$i]."'";
				}else{
					$sex_str .= ",'".$sex[$i]."' ";
				}
			}
		}

		if($sex_str != ""){
			$where .= " AND sex_div in ($sex_str) ";
		}
	}else{
		if($sex){
			$where .= " AND sex_div = '$sex' ";
		}
	}

	//가입일 검색
	if($orderdate){
		$where .= " AND regdate between '$startDate' AND '$endDate'";
	}
	
	//다중검색 체크시 (검색어 다중검색)
	$search_text = trim($search_text);
		if($db->dbms_type == "oracle"){
			if($search_type != "" && $search_text != ""){
				if($search_type == "jumin"){
					$search_text = substr($search_text,0,6)."-".md5(substr($search_text,6,7));
					$where .= " and AES_DECRYPT(jumin,'".$db->ase_encrypt_key."') = '$search_text' ";

					$count_where .= " and AES_DECRYPT(jumin,'".$db->ase_encrypt_key."') = '$search_text' ";

				}else if($search_type == "name" || $search_type == "mail" || $search_type == "pcs" || $search_type == "tel" ){
					$where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
					$count_where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
				}else{
					$where .= " and $search_type LIKE  '%$search_text%' ";
					$count_where .= " and $search_type LIKE  '%$search_text%' ";
				}

			}
		}else{
			if($search_type != "" && $search_text != ""){
				if($search_type == "cmd.name" || $search_type == "cmd.mail" || $search_type == "cmd.pcs" || $search_type == "cmd.tel" ){

					$where .= " and (AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' OR REPLACE(AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."'),'-','') LIKE '%$search_text%')";
					$count_where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
				}else if($search_type == "cu.id"){

					if(strpos($search_text,",") !== false){
						$search_array = explode(",",$search_text);
						$where .= "and ( ";
						$count_where .= "and ( ";
						for($i=0;$i<count($search_array);$i++){

							if($i == count($search_array) - 1){
								$where .= $search_type." = '".trim($search_array[$i])."'";
								$count_where .= $search_type." = '".trim($search_array[$i])."'";
							}else{
								$where .= $search_type." = '".trim($search_array[$i])."' or ";
								$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
							}
						}
						$where .= ")";
						$count_where .= ")";
					}else if(strpos($search_text,"\n") !== false){//\n

						$search_array = explode("\n",$search_text);

						$where .= "and ( ";
						$count_where .= "and ( ";
						for($i=0;$i<count($search_array);$i++){

							if($i == count($search_array) - 1){
								$where .= $search_type." = '".trim($search_array[$i])."'";
								$count_where .= $search_type." = '".trim($search_array[$i])."'";
							}else{
								$where .= $search_type." = '".trim($search_array[$i])."' or ";
								$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
							}
						}
						$where .= ")";
						$count_where .= ")";
					}else{
						$where .= "and ".$search_type." = '".trim($search_text)."'";
						$count_where .= "and ".$search_type." = '".trim($search_text)."'";
					}
				}else{
					$where .= " and $search_type LIKE  '%$search_text%' ";
					$count_where .= " and $search_type LIKE  '%$search_text%' ";
				}
			}
		}





	//************적립금 *************
	if ($update_kind == "reserve"){
	//	echo $search_searialize_value;

		if($update_type == 2){// 선택회원일때
			for($i=1; $i <count($code);$i++){

				//////////////// 마일리지 적립 시작///////////////////////
				InsertReserveInfo($code[$i],$oid,$order_detail_info[od_ix],$id,$reserve,$state,$use_state,$etc,'mileage',$admininfo);	//마일리지,적립금 통합용 함수 2013-10-24 이학봉
				//////////////// 마일리지 적립 끝///////////////////////
				
				//New 마일리지 관리 시스템 JK160323
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
            echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택회원 전체에게 적립금 $reserve 이 처리 완료되었습니다.','parent_reload');</script>");
            exit;
		}else{// 검색회원일때

				//$db->query("INSERT INTO ".TBL_SHOP_RESERVE_INFO." (id,uid,oid,pid,ptprice,payprice,reserve,state,etc,regdate) VALUES ('','$uid','','','','','$reserve','$state','$etc',NOW())");

				$sql = "select
							cu.code
						from
							".TBL_COMMON_USER." as cu 
							inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)
							left join ".TBL_SHOP_GROUPINFO." as mg on (cmd.gp_ix = mg.gp_ix)
							left join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (ccd.company_id = cu.company_id)
							left join shop_event_applicant as ea on (ea.mem_ix = cmd.code)
							$where";
				
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
		}

	}



	//************회원그룹변경 *************
	if ($update_kind == "group"){

		if($update_type == 2){// 선택회원일때
			for($i=1; $i <count($code);$i++){

				$sql = "select gp_ix from ".TBL_COMMON_MEMBER_DETAIL." where code = '".$code[$i]."' ";
				$db->query($sql);
				$db->fetch();

				member_edit_history($code[$i],'gp_ix','회원그룹',$db->dt["gp_ix"],$update_gp_ix,$_SESSION["admininfo"]["charger_ix"],$_SESSION["admininfo"]["charger"],"");

				$sql = "update ".TBL_COMMON_MEMBER_DETAIL." set gp_ix = '$update_gp_ix' where code = '".$code[$i]."' ";
				$db->query($sql);

			}
			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택회원 전체의 그룹변경이 완료되었습니다.','parent_reload');</script>");
            exit;
		}else{// 검색회원일때
				
				$sql = "select cmd.code,cmd.gp_ix from ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_SHOP_GROUPINFO." mg $where  ";
				$db->query($sql);
				$tmp_member=$db->fetchall();

				for($i=0; $i <count($tmp_member);$i++){
					member_edit_history($tmp_member[$i]["code"],'gp_ix','회원그룹',$tmp_member[$i]["gp_ix"],$update_gp_ix,$_SESSION["admininfo"]["charger_ix"],$_SESSION["admininfo"]["charger"],"");
				}

				if($db->dbms_type == "oracle"){
					$sql = "update ".TBL_COMMON_MEMBER_DETAIL." set gp_ix = '$update_gp_ix'
								where code in (select cu.code from ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_SHOP_GROUPINFO." mg $where)";
				}else{
					$sql = "update ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_SHOP_GROUPINFO." mg set cmd.gp_ix = '$update_gp_ix'
								$where  ";
				}
				//echo $sql;
				$db->query($sql);

				echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('검색회원 전체의 그룹변경이 완료되었습니다.','parent_reload');</script>");
				exit;
		}
	}



	//************SMS *************
	if ($update_kind == "sms"){
		
		$sms_text_array = explode('^|^',$_POST[sms_text_array][0]);

		if($send_time_type == "1"){
			$send_time = $send_time_sms." ".$send_time_hour.":".$send_time_minite.":00";
		}else{
			$send_time = 0;
		}

		if($lms_title == "제목없음"){
			$lms_title = $_SESSION['shopcfg']['shop_name'];
		}

		if($mms_file){
		
			$t_dir = "mms/upload"; // 업로드된 파일을 저장할 디렉토리
			$s_sm = $mms_file_name; // 실제 파일명
			$ret = move_uploaded_file($mms_file, "$t_dir/$s_sm"); // 파일 지정한 업로드폴더로 이동

			if($ret) {
				$file_source = $t_dir."/".$s_sm;
				$file_loc = array();
				$file_loc[] = "http://".$_SERVER['HTTP_HOST']."/admin/member/".$file_source;
			} else {
				
			}
		}
		
		$cominfo = getcominfo();
		$sdb = new Database;
		$s = new SMS();
		$s->send_phone = $cominfo[com_phone];
		$s->send_name = $cominfo[com_name];
		$s->admin_mode = true;
		$s->send_type = $send_type;
		$s->send_date = substr($sFromYY,2,2).$sFromMM.$sFromDD;
		$s->send_time = $send_time;
		$s->send_title	=	$lms_title;
		$s->mms_file = $file_loc;


		if($update_type == 2){// 선택회원일때

			for($i=1; $i <count($code);$i++){
                if($code[$i]) {
                    if ($db->dbms_type == "oracle") {
                        $sql = "select AES_DECRYPT(cmd.pcs,'" . $db->ase_encrypt_key . "') as pcs,
                            AES_DECRYPT(cmd.name,'" . $db->ase_encrypt_key . "') as name, cu.id
                            from " . TBL_COMMON_USER . " cu LEFT JOIN " . TBL_COMMON_MEMBER_DETAIL . " cmd ON cu.code=cmd.code
                            where cu.code ='" . $code[$i] . "'";
                    } else {
                        $sql = "select AES_DECRYPT(UNHEX(cmd.pcs),'" . $db->ase_encrypt_key . "') as pcs,
                            AES_DECRYPT(UNHEX(cmd.name),'" . $db->ase_encrypt_key . "') as name, 
                            cu.id , cmd.code
                            from " . TBL_COMMON_USER . " cu 
                            LEFT JOIN " . TBL_COMMON_MEMBER_DETAIL . " cmd ON cu.code=cmd.code								
                            where cu.code ='" . $code[$i] . "'  ";
                    }
                    $db->query($sql);
                    $db->fetch();

                    if ($select_sms_type == 'SMS' && $sms_text_count > 79) {
                        for ($z = 0; $z < count($sms_text_array); $z++) {

                            $mc_sms_text = str_replace("{id}", $db->dt[id], $sms_text_array[$z]);
                            $mc_sms_text = str_replace("{name}", $db->dt[name], $mc_sms_text);
                            //$mc_sms_text = str_replace("{site}",$db->dt[name],$mc_sms_text);

                            //echo $mail_info[mem_name]."::::".$mail_info[mem_mail].":::	".$mail_info[mem_id];
                            $s->dest_phone = str_replace("-", "", $db->dt["pcs"]);
                            $s->dest_name = $db->dt["name"];
                            $s->dest_code = $db->dt['code'];
                            $s->msg_body = $mc_sms_text;
                            $s->send_title = "";
                            $s->send_type = "1";
                            $s->sendbyone($admininfo);
                            sleep(1);
                        }
                    } else {
                        $mc_sms_text = str_replace("{id}", $db->dt[id], $sms_text);
                        $mc_sms_text = str_replace("{name}", $db->dt[name], $mc_sms_text);
                        //$mc_sms_text = str_replace("{site}",$db->dt[name],$mc_sms_text);

                        //echo $mail_info[mem_name]."::::".$mail_info[mem_mail].":::	".$mail_info[mem_id];
                        $s->dest_phone = str_replace("-", "", $db->dt["pcs"]);
                        $s->dest_name = $db->dt["name"];
                        $s->dest_code = $db->dt['code'];
                        $s->msg_body = $mc_sms_text;

                        $s->sendbyone($admininfo);
                    }
                }
			}
			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택회원 전체에게 SMS 가 발송 되었습니다.','parent_reload');</script>");
            exit;


		}else{// 검색회원일때

				if(!$max){
					$max = 100;
				}
				if ($sms_send_page == ''){
					$start = 0;
					$sms_send_page  = 1;
				}else{
					$start = ($sms_send_page - 1) * $max;
				}
				$sql = "select count(*) as total
								from ".TBL_COMMON_USER." cu 
								inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code) 
								left join ".TBL_SHOP_GROUPINFO." as mg on (cmd.gp_ix = mg.gp_ix)
								left join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (ccd.company_id = cu.company_id)
								left join shop_event_applicant as ea on (ea.mem_ix = cmd.code)
								$where  ";

				$db->query($sql);
				$db->fetch();
				$total = $db->dt[total];

				if($db->dbms_type == "oracle"){
					$sql = "select AES_DECRYPT(cmd.pcs,'".$db->ase_encrypt_key."') as pcs,
							AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name, cu.id
							from ".TBL_COMMON_USER." cu , 
							".TBL_COMMON_MEMBER_DETAIL." cmd
							left join ".TBL_SHOP_GROUPINFO." as mg on (cmd.gp_ix = mg.gp_ix)
							left join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (ccd.company_id = cu.company_id)
							left join shop_event_applicant as ea on (ea.mem_ix = cmd.code)
							$where
							ORDER BY cmd.date_ DESC
							limit $start,$max  ";
				}else{
					$sql = "select AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs,
							AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cu.id
							from ".TBL_COMMON_USER." cu 
							inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code) 
							left join ".TBL_SHOP_GROUPINFO." as mg on (cmd.gp_ix = mg.gp_ix)
							left join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (ccd.company_id = cu.company_id)
							left join shop_event_applicant as ea on (ea.mem_ix = cmd.code)
							$where
							ORDER BY cmd.date DESC
							limit $start,$max  ";
				}

				$db->query($sql);

				for($i=0;$i < $db->total;$i++){
					$db->fetch($i);


					if($select_sms_type == 'SMS' && $sms_text_count > 79){
						for($z=0; $z < count($sms_text_array); $z ++){
							
							$mc_sms_text = str_replace("{id}",$db->dt[id],$sms_text_array[$z]);
							$mc_sms_text = str_replace("{name}",$db->dt[name],$mc_sms_text);
							//$mc_sms_text = str_replace("{site}",$db->dt[name],$mc_sms_text);

							//echo $mail_info[mem_name]."::::".$mail_info[mem_mail].":::	".$mail_info[mem_id];
							$s->dest_phone = str_replace("-","",$db->dt["pcs"]);
							$s->dest_name = $db->dt["name"];
							$s->msg_body =$mc_sms_text;					
							$s->send_title	=	"";
							$s->send_type = "1";
                            $s->send_time = $send_time;
							$s->sendbyone($admininfo);
							sleep(1);
						}
					}else{
						$mc_sms_text = str_replace("{id}",$db->dt[id],$sms_text);
						$mc_sms_text = str_replace("{name}",$db->dt[name],$mc_sms_text);

						//echo $mail_info[mem_name]."::::".$mail_info[mem_mail].":::	".$mail_info[mem_id];
						$s->dest_phone = str_replace("-","",$db->dt["pcs"]);
						$s->dest_name = $db->dt["name"];
						$s->msg_body =$mc_sms_text;
                        $s->send_time = $send_time;

						$s->sendbyone($admininfo);
						
					}
					
				}

				if($total > ($start+$max)){
					echo("<script language='javascript' src='/admin/js/jquery-1.8.3.js'></script>
					<script> 
					$('#sended_sms_cnt', parent.document).html('".($start+$max)."');
					$('#remainder_sms_cnt', parent.document).html('".($total-($start+$max))."');
					
					if(!$('form[name=list_frm]',parent.document).find('input[name=stop]').is(':checked')){
						$('form[name=list_frm]',parent.document).find('input[name=sms_send_page]').val('".($sms_send_page+1)."');
						$('#confirm_bool', parent.document).val(0);
						$('form[name=list_frm]',parent.document).submit();
					}
					</script>");
				}else{
					echo("<script language='javascript' src='/admin/js/jquery-1.8.3.js'></script>
					<script language='javascript' src='../_language/language.php'></script>
					<script>
					$('#sended_sms_cnt', parent.document).html('".($total)."');
					$('#remainder_sms_cnt', parent.document).html(0);
					//parent.document.getElementById('sended_sms_cnt').innerHTML = '".($total)."';
					//parent.document.getElementById('remainder_sms_cnt').innerHTML = '0';
					alert('".$total." 건의 SMS 가 정상적으로 발송되었습니다');//건의 SMS 가 정상적으로 발송되었습니다
					</script>");
					exit;
				}
		}
	}




	//************쿠폰 일괄지급 *************
	if ($update_kind == "coupon"){

		    if($publish_ix == ''){
                echo("<script>alert('쿠폰 정보가 유효하지 않습니다.');</script>");
                exit;
            }

            $sql = "Select 
                        publish_ix,
                        use_date_type,
                        publish_date_differ,
                        publish_type,
                        publish_date_type, 
                        regist_date_type, 
                        regist_date_differ,
                        regist_date_differ,
                        date_format(use_sdate,'%Y%m%d') as use_sdate, 
                        date_format(use_edate,'%Y%m%d') as use_edate,
                        date_format(regdate,'%Y%m%d') as regdate,
                        regist_count
                    from ".TBL_SHOP_CUPON_PUBLISH."
                    where publish_ix = '".$publish_ix."'";
			$db->query($sql);
			$db->fetch();
			$publish_ix = $db->dt[publish_ix];
            $regist_count = ($db->dt[regist_count] > 0 ? $db->dt[regist_count] : 1);

            $p_year  = date("Y",strtotime($db->dt["regdate"]));
            $p_month = date("m",strtotime($db->dt["regdate"]));
            $p_day   = date("d",strtotime($db->dt["regdate"]));

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

                $use_sdate      = mktime(0,0,0,$p_month,$p_day,$p_year);
				$use_date_limit = mktime(0,0,0,$publish_month,$publish_day,$publish_year);

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

                $sql = "insert into ".TBL_SHOP_CUPON_REGIST."  select '' as regist_ix , '".$publish_ix."' as publish_ix, cu.code,1,0,
								'$use_sdate','$use_date_limit',null,null, NOW() , null, null
								from ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_SHOP_GROUPINFO." mg
								$where ";
                for ($rc = 0; $rc < $coupon_publish_cnt; $rc++) {
                    $db->query($sql);
                }

				echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('검색회원 전체에게 쿠폰 발급이 완료되었습니다.','parent_reload');</script>");

            // 선택회원일때
			}else if($update_type == 2){

				for($i=0;$i< count($code) ;$i++){
					if($code[$i]){
					    for($k=0;$k<$coupon_publish_cnt;$k++) {
                            $sql2 = "insert into " . TBL_SHOP_CUPON_REGIST . " (regist_ix, publish_ix, mem_ix, open_yn, use_yn, use_sdate, use_date_limit, regdate)
                                     values ('','" . $publish_ix . "','" . $code[$i] . "','1','0','$use_sdate','$use_date_limit',NOW())";
                            $db->sequences = "SHOP_CUPON_REGIST_SEQ";
                            $db->query($sql2);
                        }
					}
				}
                echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택회원 전체에게 쿠폰발급이 완료되었습니다.','parent_reload');</script>");
			}
			exit;
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
			
			if($_SESSION["ss_mail_ix"] == ""){
				$mail_info[mail_ix] = $_POST[mail_ix];
			}else{
				$mail_info[mail_ix] = $_SESSION["ss_mail_ix"];
			}

			if($_SESSION["ss_mail_ix"] == ""){
				$mail_ix = mail_box("insert", $mail_info);
				$_SESSION["ss_mail_ix"] = $mail_ix;
			}else{
				$mail_ix = $_SESSION["ss_mail_ix"];
			}
		}else{
			$mail_ix = $_POST[mail_ix];
		}


		if($update_type == 2){// 선택회원일때

			for($i=1; $i <count($code);$i++){

                $sql = "select  AES_DECRYPT(UNHEX(IFNULL(cmd.name,'-')),'".$db->ase_encrypt_key."') as name,
								cu.id, AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
								SUBSTRING_INDEX(AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."'),'@',-1) AS ns
								from ".TBL_COMMON_USER." cu
								LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code
								where cu.code ='".$code[$i]."'  ";

					$db->query($sql);
					$db->fetch();

					$mail_subject = $db->dt[name]." 님, ".$email_subject;

					if (ValidateDNS($db->dt['ns'])){

						$mail_info[mem_name] = $db->dt[name];
						$mail_info[mem_mail] = $db->dt[mail];
						$mail_info[mem_id] = $db->dt[id];

						if($i==1) $mail_info[mail_cc] = $mail_cc;

						$check_key = md5(uniqid());
						$mail_content = str_replace("{check_key}",$check_key,$mail_content);
						$__mail_content = $mail_content."<P style='DISPLAY: none'><IMG src='http://".$HTTP_HOST."/mailling/mail_open.php?check_key=$check_key'></P>";


						if (SendMail($mail_info, $mail_subject,$__mail_content,"","","Y")){
							$sql = "insert into shop_mailling_history
									(mh_ix,mail_ix,ucode, sended_mail, check_key, regdate)
									values
									('','".$mail_ix."','".$code[$i]."','".$db->dt[mail]."','$check_key', NOW())";
							$idb->sequences = "SHOP_MAILLING_HISTORY_SEQ";
							$idb->query($sql);

						}else{
							$sql = "insert into shop_mailling_history
									(mh_ix,mail_ix,ucode, sended_mail, check_key, is_error, error_text, regdate)
									values
									('','".$mail_ix."','".$code[$i]."','".$db->dt[mail]."','$check_key','1','SEND_ERROR', NOW())
									";
							$idb->sequences = "SHOP_MAILLING_HISTORY_SEQ";
							$idb->query($sql);
						}



					}else{
							$sql = "insert into shop_mailling_history
								(mh_ix,mail_ix,ucode, sended_mail, check_key, is_error, error_text, regdate)
								values
								('','".$mail_ix."','".$code[$i]."','".$db->dt[mail]."','$check_key','1','DNS_ERROR', NOW())
								";
							$idb->sequences = "SHOP_MAILLING_HISTORY_SEQ";
							$idb->query($sql);
					}
			}

            echo("<script>alert('선택회원에게 E-mail이 정상적으로 발송되었습니다');parent.window.location.reload();</script>");
            exit;

			//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택회원에게 E-mail이 정상적으로 발송되었습니다');</script>");

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

				$sql = "select count(*) as total,cu.code, AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as  pcs,
							AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name, id, AES_DECRYPT(UNHEX(mail),'".$db->ase_encrypt_key."') as mail, SUBSTRING_INDEX(AES_DECRYPT(UNHEX(mail),'".$db->ase_encrypt_key."'),'@',-1) AS ns
							from ".TBL_COMMON_USER." cu ,
							".TBL_COMMON_MEMBER_DETAIL." cmd,
							".TBL_SHOP_GROUPINFO." mg
							 $where";
				$db->query($sql);
				$db->fetch();
				$total = $db->dt[total];

				if($db->dbms_type == "oracle"){
					$sql = "select cu.code, AES_DECRYPT(pcs,'".$db->ase_encrypt_key."') as  pcs,
							AES_DECRYPT(name,'".$db->ase_encrypt_key."') as name, id, AES_DECRYPT(mail,'".$db->ase_encrypt_key."') as mail, substr(AES_DECRYPT(mail,'".$db->ase_encrypt_key."'),(instr(AES_DECRYPT(mail,'".$db->ase_encrypt_key."'),'@')+1)) AS ns
							from ".TBL_COMMON_USER." cu ,
							".TBL_COMMON_MEMBER_DETAIL." cmd,
							".TBL_SHOP_GROUPINFO." mg
							 $where
							ORDER BY cmd.date_ DESC
							limit $start,$email_max  ";
				}else{
					$sql = "select cu.code, AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as  pcs,
							AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name, id, AES_DECRYPT(UNHEX(mail),'".$db->ase_encrypt_key."') as mail, SUBSTRING_INDEX(AES_DECRYPT(UNHEX(mail),'".$db->ase_encrypt_key."'),'@',-1) AS ns
							from ".TBL_COMMON_USER." cu ,
							".TBL_COMMON_MEMBER_DETAIL." cmd,
							".TBL_SHOP_GROUPINFO." mg
							 $where
							ORDER BY cmd.date DESC
							limit $start,$email_max  ";
				}

				$db->query($sql);

				$send_cnt = 0;
				for($i=0;$i < $db->total;$i++){
					$db->fetch($i);
					
					$mail_subject = $db->dt[name]." 님, ".$email_subject;
					//if(false){
						if (ValidateDNS($db->dt['ns'])){
							$mail_info[mem_name] = $db->dt[name];
							$mail_info[mem_mail] = $db->dt[mail];
							$mail_info[mem_id] = $db->dt[id];
							if($i==0) $mail_info[mail_cc] = $mail_cc;

							$check_key = md5(uniqid());
							$mail_content = str_replace("{check_key}",$check_key,$mail_content);
							$__mail_content = $mail_content."<P style='DISPLAY: none'><IMG src='http://".$HTTP_HOST."/mailling/mail_open.php?check_key=$check_key'></P>";

							if (SendMail($mail_info, $mail_subject,$__mail_content,"","","Y")){//SendMail 함수에 전달인자 값이 추가되었기에 여기서도 추가해야 사용자한테 발송됨 kbk 13/09/17
								//$message = "<b>".$mail_info[mem_name]."</b> 님의 이메일이 발송되었습니다.<br>";
								$sql = "insert into shop_mailling_history
										(mh_ix,mail_ix,ucode, sended_mail, check_key, regdate)
										values
										('','".$mail_ix."','".$db->dt[code]."','".$db->dt[mail]."','$check_key', NOW())";
								$idb->sequences = "SHOP_MAILLING_HISTORY_SEQ";
								$idb->query($sql);
							}else{
								//$message = "<b>".$mail_info[mem_name]."</b> 님의 이메일 발송이 실패했습니다.<br>";
								$sql = "insert into shop_mailling_history
										(mh_ix,mail_ix,ucode, sended_mail, check_key,is_error, error_text, regdate)
										values
										('','".$mail_ix."','".$db->dt[code]."','".$db->dt[mail]."','$check_key', '1','SEND_ERROR', NOW())";
								$idb->sequences = "SHOP_MAILLING_HISTORY_SEQ";
								$idb->query($sql);
							}

							$send_cnt++;
						}else{
								$sql = "insert into shop_mailling_history
									(mh_ix,mail_ix,ucode, sended_mail, check_key, is_error, error_text, regdate)
									values
									('','".$mail_ix."','".$db->dt[code]."','".$db->dt[mail]."','$check_key','1','DNS_ERROR', NOW())
									";
								$idb->sequences = "SHOP_MAILLING_HISTORY_SEQ";
								$idb->query($sql);
							echo "DNS ERROR";
						}
					//}

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
						alert('".(($email_send_page-1)*$email_max + $send_cnt)." '+'건의 이메일이 정상적으로 발송되었습니다');//건의 이메일이 정상적으로 발송되었습니다
						</script>");
					}else{
						echo("<script language='javascript' src='../_language/language.php'></script>
						<script>
						parent.document.getElementById('sended_email_cnt').innerHTML = '".($send_cnt)."';
						parent.document.getElementById('remainder_email_cnt').innerHTML = '0';
						alert('발송대상이 존재하지 않습니다. 메일링 수신거부 회원은 메일링 대상이 아닙니다.');//'발송대상이 존재하지 않습니다. 메일링 수신거부 회원은 메일링 대상이 아닙니다. '
						</script>");
					}
				}
				//$_SESSION["ss_mail_ix"] = $mail_ix;
		}
		//echo("<script>top.location.href = 'baymoney.pop.php?ab_ix=$uid';</script>");
		exit;
	}

	//************대량 E-mail 발송 ************* 2014-05-22 JBG
	if ($update_kind == "bigemail"){

		//$cominfo = getcominfo();

		$cominfo[shop_name] = $sender;
		$cominfo[com_email] = $sendermail;
		
		//발송구분 0 : 즉시발송 1: 예약발송
		if($email_send_time_type == 0){
			
			$send_time = date("Y-m-d H:i:s");
		}else{
			
			$send_time = $send_time_email." ".$email_time_hour.":".$email_time_minite.":00";
		}
		
		//이메일수신거부 자동삽입유무 Y : 삽입 N : 미삽입
		if($sendno_yn == "Y"){

			$mail_receive = "<p>본 메일은 2014년5월21일 기준 회원님의 이메일 수신동의 여부를 확인한 결과, 회원님께서 수신을 동의하였기에 발송되었습니다. 본 메일은 회신되지 않으므로 문의사항은 [고객센터]로 이용하여 주시기 바랍니다.발송하는 메일을 원하지 않으시면 [수신거부]를 클릭해 주십시요.</p>";

		}else if($sendno_yn == "N"){
			
			$mail_receive = "";
		}
		
		//첨부파일 업로드
		if($email_file){
		
			$t_dir = "mail/upload"; // 업로드된 파일을 저장할 디렉토리
			$s_sm = $email_file_name; // 실제 파일명
			$ret = move_uploaded_file($email_file, "$t_dir/$s_sm"); // 파일 지정한 업로드폴더로 이동

			if($ret) {
				
				//대량메일 보낼떄 첨부파일 base64로 변환해서 첨부
				$file_source = $t_dir."/".$s_sm;
				$file_binary = fread(fopen($file_source, "r"), filesize($file_source));
				$file_string = base64_encode($file_binary);

			} else {
				
			}
		}
		
		// 자주쓰는 메일 저장하기
		$mail_info['mail_content']	= $mail_contents;
		$mail_info['mail_subject']	= $email_title;
		$mail_info['mail_ix']		= $_POST[mail_ix];
		$mail_info['disp']			= $save_mail;
		$mail_info['code']			=  date("YmdHi")."-".rand(1000, 9999);

		$mail_ix = mail_box("insert", $mail_info);
		
		if($update_type == 2){// 선택회원일때
			
			for($i=1; $i <count($code);$i++){

					if($slave_db->dbms_type == "oracle"){
						$sql = "select  AES_DECRYPT(IFNULL(cmd.name,'-'),'".$slave_db->ase_encrypt_key."') as name,
								cu.id, AES_DECRYPT(cmd.mail,'".$slave_db->ase_encrypt_key."') as mail,
								substr(AES_DECRYPT(cmd.mail,'".$slave_db->ase_encrypt_key."'),(instr(AES_DECRYPT(cmd.mail,'".$slave_db->ase_encrypt_key."'),'@')+1)) AS ns
								from ".TBL_COMMON_USER." cu
								LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code
								where cu.code ='".$code[$i]."'  ";
					}else{
						$sql = "select  AES_DECRYPT(UNHEX(IFNULL(cmd.name,'-')),'".$slave_db->ase_encrypt_key."') as name,
								cu.id, AES_DECRYPT(UNHEX(cmd.mail),'".$slave_db->ase_encrypt_key."') as mail,
								SUBSTRING_INDEX(AES_DECRYPT(UNHEX(cmd.mail),'".$slave_db->ase_encrypt_key."'),'@',-1) AS ns
								from ".TBL_COMMON_USER." cu
								LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code
								where cu.code ='".$code[$i]."'  ";
					}
			
					$slave_db->query($sql);
					$slave_db->fetch();
						
					$mail_subject = str_replace("{mem_name}",$slave_db->dt[user_name],$email_title); // 이메일제목 회원이름 치환코드 적용
					
					$mail_info[mem_name] = $slave_db->dt[name];
					$mail_info[mem_mail] = str_replace("'","",$slave_db->dt[mail]);
					$mail_info[mem_id] = $slave_db->dt[id];
					if($i==1) $mail_info[mail_cc] = $mail_cc_all;
					$check_key = md5(uniqid());
					$mail_content = str_replace("{check_key}",$check_key,$mail_contents);

					//메일 확인유무 확인 코드
					$mail_code	=	"<P style=\'DISPLAY:none\'><IMG src=\'http://".$HTTP_HOST."/mailling/mail_open.php?check_key=$check_key\'></P>";
					//이메일 본분내용(작성한내용+메일코드+이메일수신거부)
					$__mail_content = "<table width=\'800\' cellpadding=\'0\' cellspacing=\'0\' border=\'0\' style=\'margin:0 auto\'>".$mail_content.$mail_code.$mail_receive."</table>";
							
					//메일 히스토리 insert query
					/*
					$sql = "insert into shop_mailling_history
							(mh_ix,mail_ix, mail_code, mail_sendtype ,ucode, sended_mail, check_key, regdate)
							values
							('','".$mail_ix."', '".$mail_info['code']."' ,'".$email_send_time_type."' ,'".$code[$i]."','".$mail_info[mem_mail]."','$check_key', NOW())";

					$master_db->sequences = "SHOP_MAILLING_HISTORY_SEQ";
					$master_db->query($sql);
					*/
					
					//$db->query("SELECT mh_ix FROM shop_mailling_history WHERE mh_ix=LAST_INSERT_ID()");
					//$db->fetch();
					//$last_idx = $db->dt[0];
				
			}

			//메일서버 DB연결
			$idb = new Database("183.111.154.11","forbiz","forbizlogin1q2w3e","tm001");
			//메일서버 insert query
			$sql = "
				INSERT INTO 
					customer_info
				(user_id,title,content,sender,sender_alias,receiver_alias,send_time,file_name,file_contents,wasRead,wasSend,wasComplete,needRetry,linkYN,regist_date)
				VALUES
				('forbiz','$mail_subject','$__mail_content','$cominfo[com_email]','$cominfo[shop_name]','[\$name]','$send_time','$email_file_name','$file_string','X','X','X','X','N',now())
			";
				
			$idb->query($sql);
			
			$idb->query("SELECT id FROM customer_info WHERE id=LAST_INSERT_ID()");
			$idb->fetch();
			$last_id = $idb->dt['id'];

			for($i=1; $i <count($code);$i++){

				if($slave_db->dbms_type == "oracle"){
					$sql = "select  AES_DECRYPT(IFNULL(cmd.name,'-'),'".$slave_db->ase_encrypt_key."') as name,
							cu.id, AES_DECRYPT(cmd.mail,'".$slave_db->ase_encrypt_key."') as mail,
							substr(AES_DECRYPT(cmd.mail,'".$slave_db->ase_encrypt_key."'),(instr(AES_DECRYPT(cmd.mail,'".$slave_db->ase_encrypt_key."'),'@')+1)) AS ns
							from ".TBL_COMMON_USER." cu
							LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code
							where cu.code ='".$code[$i]."'  ";
				}else{
					$sql = "select  AES_DECRYPT(UNHEX(IFNULL(cmd.name,'-')),'".$slave_db->ase_encrypt_key."') as name,
							cu.id, AES_DECRYPT(UNHEX(cmd.mail),'".$slave_db->ase_encrypt_key."') as mail,
							SUBSTRING_INDEX(AES_DECRYPT(UNHEX(cmd.mail),'".$slave_db->ase_encrypt_key."'),'@',-1) AS ns
							from ".TBL_COMMON_USER." cu
							LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code
							where cu.code ='".$code[$i]."'  ";
				}
		
				$slave_db->query($sql);
				$slave_db->fetch();
					
				$mail_subject = str_replace("{mem_name}",$slave_db->dt[user_name],$email_title); // 이메일제목 회원이름 치환코드 적용
				
				$mail_info[mem_name] = $slave_db->dt[name];
				$mail_info[mem_mail] = str_replace("'","",$slave_db->dt[mail]);
				$mail_info[mem_id] = $slave_db->dt[id];

				$idb = new Database("183.111.154.11","forbiz","forbizlogin1q2w3e","tm001");
				$sql = "INSERT INTO 
							customer_data
						(id,email,first)
						VALUES
						('$last_id','$mail_info[mem_mail]','$mail_info[mem_name]')";

				$idb->query($sql);

			}
			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택회원에게 E-mail이 정상적으로 발송되었습니다','parent_reload');</script>");
            exit;
		}else{// 검색회원일때
	
			
			$bigemail_max = 5000;
			
			$mail_subject = $email_title; // 이메일제목 회원이름 치환코드 적용
			$check_key = md5(uniqid());
			$mail_content = str_replace("{check_key}",$check_key,$mail_contents);
			
			//메일 확인유무 확인 코드
			$mail_code	=	"<P style=\'DISPLAY:none\'><IMG src=\'http://".$HTTP_HOST."/mailling/mail_open.php?check_key=$check_key\'></P>";
			//이메일 본분내용(작성한내용+메일코드+이메일수신거부)
			$__mail_content = "<table width=\'800\' cellpadding=\'0\' cellspacing=\'0\' border=\'0\' style=\'margin:0 auto\'>".$mail_content.$mail_code.$mail_receive."</table>";

			//메일서버 DB연결
			$idb = new Database("183.111.154.11","forbiz","forbizlogin1q2w3e","tm001");

			//메일서버 insert query
			$sql = "
				INSERT INTO 
					customer_info
				(user_id,title,content,sender,sender_alias,receiver_alias,send_time,file_name,file_contents,wasRead,wasSend,wasComplete,needRetry,linkYN,regist_date)
				VALUES
				('forbiz','$mail_subject','$__mail_content','$cominfo[com_email]','$cominfo[shop_name]','[\$name]','$send_time','$email_file_name','$file_string','O','X','X','X','N',now())
				";

			$idb->query($sql);
			
			$idb->query("SELECT id FROM customer_info WHERE id=LAST_INSERT_ID()");
			$idb->fetch();
			$last_id = $idb->dt['id'];


			$sql = "select count(*) as total from ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd $where  ";

			$slave_db->query($sql);
			$slave_db->fetch();
			$total = $slave_db->dt[total];
			$round = (int)($total / $bigemail_max) + 1;
			//max값으로 나눠서 진행
			for($i = 0; $i < $round; $i++){
				$start = ($i * $bigemail_max);
				if($i == 0){
					$start = 0;
				}
				$end = $bigemail_max;

				if($slave_db->dbms_type == "oracle"){
					$sql = "select cu.code, AES_DECRYPT(pcs,'".$slave_db->ase_encrypt_key."') as  pcs,
							AES_DECRYPT(name,'".$slave_db->ase_encrypt_key."') as name, id, AES_DECRYPT(mail,'".$slave_db->ase_encrypt_key."') as mail, substr(AES_DECRYPT(mail,'".$slave_db->ase_encrypt_key."'),(instr(AES_DECRYPT(mail,'".$slave_db->ase_encrypt_key."'),'@')+1)) AS ns
							from ".TBL_COMMON_USER." cu ,
							".TBL_COMMON_MEMBER_DETAIL." cmd 
							 $where
							ORDER BY cmd.date_ DESC
							limit $start,$end  ";
				}else{
					$sql = "select cu.code, AES_DECRYPT(UNHEX(pcs),'".$slave_db->ase_encrypt_key."') as  pcs,
							AES_DECRYPT(UNHEX(name),'".$slave_db->ase_encrypt_key."') as name, id, AES_DECRYPT(UNHEX(mail),'".$slave_db->ase_encrypt_key."') as mail, SUBSTRING_INDEX(AES_DECRYPT(UNHEX(mail),'".$slave_db->ase_encrypt_key."'),'@',-1) AS ns
							from ".TBL_COMMON_USER." cu ,
							".TBL_COMMON_MEMBER_DETAIL." cmd 
							$where
							ORDER BY cmd.date DESC
							limit $start,$end  ";
				}
				//echo $sql;
				//exit;
				$slave_db->query($sql);
								
				$send_cnt =0;
				$result = $slave_db->fetchall();

				$member = array();
				
				for($j=0;$j < count($result);$j++){
					
					$mail_info = array();
					$mail_subject = str_replace("{mem_name}",$result[$j]['name'],$email_title); // 이메일제목 회원이름 치환코드 적용
					$mail_info['mail_subject'] = $mail_subject;
					$mail_info['mem_name'] = $result[$j]['name'];
					$mail_info['mem_mail'] = $result[$j]['mail'];
					$mail_info['mem_id'] = $result[$j]['id'];
					if($j==0) $mail_info['mail_cc'] = $mail_cc_all;
					$check_key = md5(uniqid());
					$mail_content = str_replace("{check_key}",$check_key,$mail_contents);
					
					//메일 확인유무 확인 코드
					$mail_code	=	"<P style=\'DISPLAY:none\'><IMG src=\'http://".$HTTP_HOST."/mailling/mail_open.php?check_key=$check_key\'></P>";
					//이메일 본분내용(작성한내용+메일코드+이메일수신거부)
					$__mail_content = "<table width=\'800\' cellpadding=\'0\' cellspacing=\'0\' border=\'0\' style=\'margin:0 auto\'>".$mail_content.$mail_code.$mail_receive."</table>";
							
					//메일 히스토리 insert query
					/*
					$sql = "insert into shop_mailling_history
							(mh_ix,mail_ix, mail_code, mail_sendtype ,ucode, sended_mail, check_key, regdate)
							values
							('','".$mail_ix."', '".$mail_info['code']."' ,'".$email_send_time_type."' ,'".$result[$j]['code']."','".$mail_info['mem_mail']."','$check_key', NOW())";


					$master_db->sequences = "SHOP_MAILLING_HISTORY_SEQ";
					$master_db->query($sql);
					*/
					
					/*
					$sql = "SELECT mh_ix FROM shop_mailling_history ORDER BY mh_ix DESC LIMIT 1";
					$master_db->query($sql);
					$master_db->fetch();
					$mail_info['mh_ix'] = $master_db->dt['mh_ix'];
					*/
					/*
					echo("<script>
					parent.document.getElementById('sended_email_cnt').innerHTML = '".(count($result))."';
					parent.document.getElementById('remainder_email_cnt').innerHTML = '".($total-count($result))."';
					</script>");
					*/
					$send_cnt++;
				}
				//메일서버 DB연결
				$idb = new Database("183.111.154.11","forbiz","forbizlogin1q2w3e","tm001");

				foreach($result as $rt){

					$sql = "
						INSERT INTO 
							customer_data
						(id,email,first)
						VALUES
						('$last_id','".str_replace("'","",$rt['mail'])."','".$rt['name']."')
					";

					$idb->query($sql);
				}
					
			}
	
				
			//메일서버 update
			$sql = "
				UPDATE 
					customer_info
				SET 
					wasRead = 'X'
				WHERE id = '".$last_id."'
				";

			$idb->query($sql);
			
			//echo json_encode(array('result'=>'success'));
		}
		

	}

	function ValidateDNS($host)
	{
		return (checkdnsrr($host, ANY))? true: false;
	}


?>