<?
	// /admin/member/member_query2.php 를 이용해서
	// 푸시에 맞게 설정한다.
	// 푸시와 함께 쓴다.
	include_once ($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	$db = new Mysql;

	$max = 15; //페이지당 갯수
	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}
	else
	{
		$start = ($page - 1) * $max;
	}

	//검색 1주일단위 디폴트
	if ($startDate == ""){
		$before7day = mktime(0, 0, 0, date("m")  , date("d")-7, date("Y"));

		$startDate = date("Y-m-d", $before7day);
		$endDate = date("Y-m-d");
	}

	if($mode!="search"){
		$orderdate=1;
		//$where .= "and date_format(cu.date,'%Y-%m-%d') between '".$startDate."' and '".$endDate."' ";
	}

	if($mode == "search"){

		$where = " and 1 ";

		if($orderdate){
			$where .= " and date_format(".$date_type.",'%Y-%m-%d') between '".$startDate."' and '".$endDate."' ";
		}

		//회원타입 검색
		if(is_array($mem_type)){
			$where .= " AND cu.mem_type in ('".implode("','",$mem_type)."') ";
		}else{
			if($mem_type){
				$where .= " AND cu.mem_type = '$mem_type' ";
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

		//이벤트당첨자 검색
		if(is_array($eid)){
			for($i=0;$i < count($eid);$i++){
				if($eid[$i] != ""){
					if($eid_str == ""){
						$eid_str .= "'".$eid[$i]."'";
					}else{
						$eid_str .= ",'".$eid[$i]."' ";
					}
				}
			}

			if($eid_str != ""){
				$where .= " AND ea.event_ix in ($eid_str) AND ea.is_winner = '1' ";
			}
		}else{
			if($eid){
				$where .= " AND ea.event_ix = '$eid' AND ea.is_winner = '1' ";
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

		$search_text = trim($search_text);
		if($db->dbms_type == "oracle"){
			if($search_type != "" && $search_text != ""){
				if($search_type == "name" || $search_type == "mail" || $search_type == "pcs" || $search_type == "tel" ){
					$where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
					$count_where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
				}else{
					$where .= " and $search_type LIKE  '%$search_text%' ";
					$count_where .= " and $search_type LIKE  '%$search_text%' ";
				}

			}
		}else{
            if($mult_search_use == '1') {
                if ($search_type != "" && $search_text != "") {
                    if(strpos($search_text,",") !== false){
                        $search_array = explode(",",$search_text);
                        $search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));

                        if($search_type == 'cu.id') {
                            $where .= "and $search_type in ( ";
                            $count_where .= "and $search_type in ( ";
                        }else{
                            $where .= "and AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "') in ( ";
                            $count_where .= "and AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "') in ( ";
						}
                        for($i=0;$i<count($search_array);$i++){
                            if($search_type == 'cmd.pcs') {
                                $search_array[$i] = format_phone(trim($search_array[$i]));
                            }else {
                                $search_array[$i] = trim($search_array[$i]);
                            }
                            if($search_array[$i]){
                                if($i == count($search_array) - 1){
                                    $where .= "'".trim($search_array[$i])."'";
                                    $count_where .= "'".trim($search_array[$i])."'";
                                }else{
                                    $where .= "'".trim($search_array[$i])."' , ";
                                    $count_where .= "'".trim($search_array[$i])."' , ";
                                }
                            }
                        }
                        $where .= ")";
                        $count_where .= ")";
                    }else if(strpos($search_text,"\n") !== false){//\n
                        $search_array = explode("\n",$search_text);
                        $search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));

                        if($search_type == 'cu.id') {
                            $where .= "and $search_type in ( ";
                            $count_where .= "and $search_type in ( ";
                        }else{
                            $where .= "and AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "') in ( ";
                            $count_where .= "and AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "') in ( ";
                        }

                        for($i=0;$i<count($search_array);$i++){
                            if($search_type == 'cmd.pcs') {
                                $search_array[$i] = format_phone(trim($search_array[$i]));
                            }else {
                                $search_array[$i] = trim($search_array[$i]);
                            }
                            if($search_array[$i]){
                                if($i == count($search_array) - 1){
                                    $where .= "'".trim($search_array[$i])."'";
                                    $count_where .= "'".trim($search_array[$i])."'";
                                }else{
                                    $where .= "'".trim($search_array[$i])."' , ";
                                    $count_where .= "'".trim($search_array[$i])."' , ";
                                }
                            }
                        }
                        $where .= ")";
                        $count_where .= ")";
                    }else{
                        if($search_type == 'cmd.pcs' ) {
                            $where .= " and AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "') = '".format_phone(trim($search_text))."'";
                            $count_where .= " and AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "') = '".trim($search_text)."'";
                        }else if($search_type == 'cu.id'){
                            $where .= " and ".$search_type." = '".trim($search_text)."'";
                            $count_where .= " and ".$search_type." = '".trim($search_text)."'";
						}else {
                            $where .= " and AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "') = '".trim($search_text)."'";
                            $count_where .= " and AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "') = '".trim($search_text)."'";
                        }
                    }
                }
            }else{
                if ($search_type != "" && $search_text != "") {
                    if ($search_type == "cmd.name" || $search_type == "cmd.mail" || $search_type == "cmd.pcs" || $search_type == "cmd.tel") {
                       if($search_type == "cmd.pcs"){
                           $where .= " and ( REPLACE(AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "'),'-','') LIKE '%" . trim($search_text) . "%' or AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "') LIKE '%" . trim($search_text) . "%' ) ";
                           $count_where .= " and ( REPLACE(AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "'),'-','') LIKE '%" . trim($search_text) . "%' or AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "') LIKE '%" . trim($search_text) . "%' ) ";
					   }else{
                           $where .= " and AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "') LIKE  '%$search_text%' ";
                           $count_where .= " and AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "') LIKE  '%$search_text%' ";
					   }

                    } else if ($search_type == "cu.id") {

                        if (strpos($search_text, ",") !== false) {
                            $search_array = explode(",", $search_text);
                            $where .= "and ( ";
                            $count_where .= "and ( ";
                            for ($i = 0; $i < count($search_array); $i++) {

                                if ($i == count($search_array) - 1) {
                                    $where .= $search_type . " = '" . trim($search_array[$i]) . "'";
                                    $count_where .= $search_type . " = '" . trim($search_array[$i]) . "'";
                                } else {
                                    $where .= $search_type . " = '" . trim($search_array[$i]) . "' or ";
                                    $count_where .= $search_type . " = '" . trim($search_array[$i]) . "' or ";
                                }
                            }
                            $where .= ")";
                            $count_where .= ")";
                        } else if (strpos($search_text, "\n") !== false) {//\n

                            $search_array = explode("\n", $search_text);

                            $where .= "and ( ";
                            $count_where .= "and ( ";
                            for ($i = 0; $i < count($search_array); $i++) {

                                if ($i == count($search_array) - 1) {
                                    $where .= $search_type . " = '" . trim($search_array[$i]) . "'";
                                    $count_where .= $search_type . " = '" . trim($search_array[$i]) . "'";
                                } else {
                                    $where .= $search_type . " = '" . trim($search_array[$i]) . "' or ";
                                    $count_where .= $search_type . " = '" . trim($search_array[$i]) . "' or ";
                                }
                            }
                            $where .= ")";
                            $count_where .= ")";
                        } else {
                            $where .= " and $search_type LIKE  '%$search_text%' ";
                            $count_where .= " and $search_type LIKE  '%$search_text%' ";
                        }
                    } else {
                        $where .= " and $search_type LIKE  '%$search_text%' ";
                        $count_where .= " and $search_type LIKE  '%$search_text%' ";
                    }
                }
            }
		}

	}

	//푸시 수신여부 검색
	if(is_array($is_allowable_yn)){
		for($i=0;$i < count($is_allowable_yn);$i++){
			if($is_allowable_yn[$i] != ""){
				if($push_yn_str == ""){
					$is_allowable_yn_str .= "'".$is_allowable_yn[$i]."'";
				}else{
					$is_allowable_yn_str .= ",'".$is_allowable_yn[$i]."' ";
				}
			}
		}

		if($is_allowable_yn_str != ""){
			$where .= " AND mps.is_allowable in ($is_allowable_yn_str) ";
		}
	}else{
		if($is_allowable_yn){
			$where .= " AND mps.is_allowable = '$is_allowable_yn' ";
		}
	}


	//push_request_member에서 호출되는것이면 distinct 를 붙여준다.
	if ($is_api == 'T') {
		$distinct = "";
		$where .= " AND mps.is_allowable = '1' AND mps.os = '".strtolower($device_type)."' ";
		if($push_start){
			$limit = " LIMIT $push_start, $push_max";
		}
	}
	else {
		//일단 기계별로 다 나오게 한다.
		//$distinct = " DISTINCT ";
		//$where .= " and mps.is_allowable = '1' ";
		$limit = " LIMIT $start, $max";
	}


	if( ! empty($where) ){

		// 전체 갯수 불러오는 부분
		//INNER JOIN mobile_push_service as mps ON mps.user_code = cmd.code 추가
		//이곳은 모바일앱으로 접속및 로그인해서 device_id와 회원 코드가 있는 사람만 검색되어야한다.

		$sql = "SELECT COUNT(*) AS total FROM
				(
					SELECT $distinct cu.code
					FROM ".TBL_COMMON_USER." cu
					INNER JOIN mobile_push_service as mps ON mps.user_code = cu.code
					INNER JOIN ".TBL_COMMON_MEMBER_DETAIL." as cmd ON cu.code = cmd.code
					LEFT JOIN ".TBL_COMMON_COMPANY_DETAIL." as ccd ON (cu.company_id = ccd.company_id)
					where 1 $where
				) A";
		$count_sql = $sql;
		$db->query($sql);
		$db->fetch();

		$total = $db->dt[total];

		//$script_time[count_end] = time();
		//$script_time[query_start] = time();


		if($db->dbms_type == "oracle"){

			$sql = "select $distinct
					cu.code, cu.id,cmd.sex_div, cmd.pcs,
					cu.company_id, ccd.com_name,cu.mileage,cu.point,cu.mem_div,
					AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, mg.gp_level,
					AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail, cu.authorized as authorized, cu.is_id_auth as is_id_auth, cu.mem_type as mem_type,
					cu.visit,
					date_format(cu.date,'%Y.%m.%d') as regdate, mg.gp_name,
					cu.last AS last, cmd.gp_ix,cmd.nationality, cmd.info, mps.device_id, mps.is_allowable, mps.key_value, mps.os
				from
					".TBL_COMMON_USER." as cu
					INNER JOIN mobile_push_service as mps ON mps.user_code = cu.code
					inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)
					left join ".TBL_SHOP_GROUPINFO." as mg on (cmd.gp_ix = mg.gp_ix)
					left join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (ccd.company_id = cu.company_id)
					left join shop_event_applicant as ea on (ea.mem_ix = cmd.code)
				where
					1 $where
					ORDER BY cu.date_ DESC
					$limit";

		}else{
			$sql = "select $distinct
						cu.code, cu.id,cmd.sex_div,
						cu.company_id, ccd.com_name,cu.mileage,cu.point,cu.mem_div,
						AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs,cmd.sms,
						AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, mg.gp_level,
						AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail, cu.authorized as authorized, cu.is_id_auth as is_id_auth, cu.mem_type as mem_type,
						cu.visit,
						date_format(cu.date,'%Y.%m.%d') as regdate, mg.gp_name,
						cu.last AS last, cmd.gp_ix,cmd.nationality, cmd.info, mps.device_id, mps.is_allowable, mps.key_value, mps.os
					from
						".TBL_COMMON_USER." as cu
						INNER JOIN mobile_push_service as mps ON mps.user_code = cu.code
						inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)
						left join ".TBL_SHOP_GROUPINFO." as mg on (cmd.gp_ix = mg.gp_ix)
						left join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (ccd.company_id = cu.company_id)

					where 1 $where
					ORDER BY cu.date DESC
					$limit";
		}
        $db->query($sql);

	}else{
		$search_false=true;
	}

	//PUSH에서 호출한 것이면 감쳐준다.
	if ($is_api != 'T') {
		if($QUERY_STRING == "nset=$nset&page=$page"){
			$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
		}else{
			$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
		}
		$str_page_bar = page_bar($total, $page,$max,$query_string ,"view");//gp_level 을 사용하지 않아서 gp_ix 로 바꿈 kbk 13/02/19
	}

?>
