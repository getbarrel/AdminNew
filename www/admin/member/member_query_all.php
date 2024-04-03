<?
	if($max == ""){
		$max = '20';
	}
	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	if ($admininfo[mall_type] == "O"){
		if($db->dbms_type == "oracle"){
			$where .= " where $black_list_where ";//,'S' //and cu.code = cmd.code //cu.date_ < '9999/12/31' and  cu.mem_type in ('M','C','F')
			$count_where .= " where $black_list_where ";//,'S' //cu.date_ < '9999/12/31' and 
		}else{
			$where .= " and cu.code != '' $black_list_where ";//,'S'
			$count_where .= " and cu.code != '' $black_list_where ";//,'S'
		}
	}else{
		if($db->dbms_type == "oracle"){
			$where .= " where $black_list_where ";//and cu.code = cmd.code  //cu.date_ < '9999/12/31' and 
			$count_where .= " where $black_list_where "; //cu.date_ < '9999/12/31' and 
		}else{
			$where .= "  $black_list_where "; //cu.date < '9999/12/31' and 
			$count_where .= "  $black_list_where ";//,'S'
		}
	}
	
	if($mileage != ""){
		if($mileage == "1"){
			$where .= " and cu.mileage > 0";
			$count_where .= " and cu.mileage > 0";
			$cmd_where .= " and cu.mileage > 0";
		}else if($mileage == "2"){
			$where .= " and cu.mileage <= 0";
			$count_where .= " and cu.mileage <= 0";
			$cmd_where .= " and cu.mileage <= 0";
		}
	}

	if($agent_type != ""){
		$where .= " and cu.agent_type = '".$agent_type."' ";
		$count_where .= " and cu.agent_type = '".$agent_type."' ";
		$cmd_where .= " and cu.agent_type = '".$agent_type."' ";
	}

	if($mall_ix!=""){
        $where .= " and cu.mall_ix = '".$mall_ix."' ";
        $count_where .= " and cu.mall_ix = '".$mall_ix."' ";
        $cmd_where .= " and cu.mall_ix = '".$mall_ix."' ";
	}

	if($point != ""){
		if($point == "1"){
			$where .= " and cu.point > 0";
			$count_where .= " and cu.point > 0";
			$cmd_where .= " and cu.point > 0";
		}else if($point == "2"){
			$where .= " and cu.point <= 0";
			$count_where .= " and cu.point <= 0";
			$cmd_where .= " and cu.point <= 0";
		
		}
	}

	if($region != ""){
		$where .= " and AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."') LIKE  '%$region%' ";
		$count_where .= " and AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."') LIKE  '%$region%' ";
		$cmd_where .= " and AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."') LIKE  '%$region%' ";
	}

	if($gp_level != ""){
		$where .= " and mg.gp_level = '".$gp_level."' ";
		$count_where .= " and mg.gp_level = '".$gp_level."' ";
		$mg_where .= " and mg.gp_level = '".$gp_level."' ";
	}

	if($gp_ix != ""){
		$where .= " and cmd.gp_ix = '".$gp_ix."' ";
		$count_where .= " and cmd.gp_ix = '".$gp_ix."' ";
		$cmd_where .= " and cmd.gp_ix = '".$gp_ix."' ";
	}

	if($sex == "M" || $sex == "W"){
		$where .= " and sex_div =  '$sex' ";
		$count_where .= " and sex_div =  '$sex' ";
		$cmd_where .= " and sex_div =  '$sex' ";
	}

	if($mailsend_yn != "A" && $mailsend_yn != ""){
		$where .= " and info =  '$mailsend_yn' ";
		$count_where .= " and info =  '$mailsend_yn' ";
		$cmd_where .= " and info =  '$mailsend_yn' ";
	}

	if($mem_type != ""){
		$where .= " and cu.mem_type =  '$mem_type' ";
		$count_where .= " and cu.mem_type =  '$mem_type' ";
		$cmd_where .= " and cu.mem_type =  '$mem_type' ";
	}

	if($mem_div != ""){
		$where .= " and cu.mem_div =  '$mem_div' ";
		$count_where .= " and cu.mem_div =  '$mem_div' ";
		$cmd_where .= " and cu.mem_div =  '$mem_div' ";
	}

	if($smssend_yn != "A" && $smssend_yn != ""){
		$where .= " and sms =  '$smssend_yn' ";
		$count_where .= " and sms =  '$smssend_yn' ";
		$cmd_where .= " and sms =  '$smssend_yn' ";
	}

	$search_text = trim($search_text);
	if($db->dbms_type == "oracle"){
		if($search_type != "" && $search_text != ""){
			if($search_type == "jumin"){
				$search_text = substr($search_text,0,6)."-".md5(substr($search_text,6,7));
				$where .= " and AES_DECRYPT(jumin,'".$db->ase_encrypt_key."') = '$search_text' ";

				$count_where .= " and AES_DECRYPT(jumin,'".$db->ase_encrypt_key."') = '$search_text' ";

			}else if($search_type == "name" || $search_type == "mail" || $search_type == "pcs" || $search_type == "tel" ){
				if($search_type == "name"){
					$where .= " and (AES_DECRYPT(name,'".$db->ase_encrypt_key."') LIKE '%$search_text%' or AES_DECRYPT(first_name,'".$db->ase_encrypt_key."') LIKE '%$search_text%' or AES_DECRYPT(last_name,'".$db->ase_encrypt_key."') LIKE '%$search_text%'
	                    or AES_DECRYPT(first_kana,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' or AES_DECRYPT(last_kana,'".$db->ase_encrypt_key."') LIKE  '%$search_text%')";
                    $count_where .= " and (AES_DECRYPT(name,'".$db->ase_encrypt_key."') LIKE '%$search_text%' or AES_DECRYPT(first_name,'".$db->ase_encrypt_key."') LIKE '%$search_text%' or AES_DECRYPT(last_name,'".$db->ase_encrypt_key."') LIKE '%$search_text%'
	                    or AES_DECRYPT(first_kana,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' or AES_DECRYPT(last_kana,'".$db->ase_encrypt_key."') LIKE  '%$search_text%')";
				}else{
                    $where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
                    $count_where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
				}
			}else{
				$where .= " and $search_type LIKE  '%$search_text%' ";
				$count_where .= " and $search_type LIKE  '%$search_text%' ";
			}

		}
	}else{
		if($search_type != "" && $search_text != ""){
            if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
                //다중검색 시작 2014-04-10 이학봉
                if($search_text != ""){

                    if(strpos($search_text,",") !== false){
                        $search_array = explode(",",$search_text);
                        $search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));

                        if ($search_type == "cmd.name" || $search_type == "cmd.mail" || $search_type == "cmd.pcs"
							|| $search_type == "cmd.tel" || $search_type == "name" || $search_type == "mail"
							|| $search_type == "pcs" || $search_type == "tel" || $search_type == "cmd.addr1") {
                            $where .= " and AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "') in ( ";
                            $count_where .= " and AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "') in ( ";
						}else{
                            $where .= "and $search_type in ( ";
                            $count_where .= "and $search_type in ( ";
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

                        if ($search_type == "cmd.name" || $search_type == "cmd.mail" || $search_type == "cmd.pcs"
                            || $search_type == "cmd.tel" || $search_type == "name" || $search_type == "mail"
                            || $search_type == "pcs" || $search_type == "tel" || $search_type == "cmd.addr1") {
                            $where .= " and AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "') in ( ";
                            $count_where .= " and AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "') in ( ";
                        }else{
                            $where .= "and $search_type in ( ";
                            $count_where .= "and $search_type in ( ";
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
                        if($search_type == 'cmd.pcs') {
                            $where .= " and AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "') = '".format_phone(trim($search_text))."'";
                            $count_where .= " and AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "') = '".trim($search_text)."'";
                        }else {
                            $where .= " and ".$search_type." = '".trim($search_text)."'";
                            $count_where .= " and ".$search_type." = '".trim($search_text)."'";
                        }
                    }
                }
            }else {

                if ($search_type == "cmd.name" || $search_type == "cmd.mail" || $search_type == "cmd.pcs" || $search_type == "cmd.tel"
                    || $search_type == "name" || $search_type == "mail" || $search_type == "pcs" || $search_type == "tel" || $search_type == "cmd.addr1"
                ) {

                    if ($search_type == "pcs" || $search_type == "tel" || $search_type == "cmd.pcs" || $search_type == "cmd.tel") {
                        $where .= " and (REPLACE(AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "'),'-','') LIKE  '%$search_text%' OR  AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "') LIKE  '%$search_text%')";
                        $count_where .= " and (REPLACE(AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "'),'-','') LIKE  '%$search_text%' OR AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "') LIKE  '%$search_text%')";
                    } else {
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
                        $where .= "and " . $search_type . " = '" . trim($search_text) . "'";
                        $count_where .= "and " . $search_type . " = '" . trim($search_text) . "'";
                    }
                } else {
                    $where .= " and $search_type LIKE  '%$search_text%' ";
                    $count_where .= " and $search_type LIKE  '%$search_text%' ";
                }
            }
		}
	}

	$startDate = $cmd_sdate;
	$endDate = $cmd_edate;
	
	if($regdate == '1'){	//가입일자
		if($startDate != "" && $endDate != ""){
			if($db->dbms_type == "oracle"){
				$where .= " and  to_char(cu.date_ , 'YYYY-MM-DD') between '".$startDate."' and '".$endDate."' ";
				$count_where .= " and  to_char(cu.date_ , 'YYYY-MM-DD') between '".$startDate."' and '".$endDate."' ";
			}else{

				$where .= " and date_format(cu.date,'%Y-%m-%d') between  '".$startDate."' and '".$endDate."' ";
				$count_where .= " and date_format(cu.date,'%Y-%m-%d') between  '".$startDate."' and '".$endDate."' ";
			}
		}
	}

	$vstartDate = $slast;
	$vendDate = $elast;
	
	if($visitdate == '1'){
		if($vstartDate != "" && $vendDate != ""){	//최근방문일
			if($db->dbms_type == "oracle"){
				$where .= " and  to_char(cu.last , 'YYYY-MM-DD') between  '".$vstartDate."' and '".$vendDate."' ";
				$count_where .= " and  to_char(cu.last , 'YYYY-MM-DD') between '".$vstartDate."' and '".$vendDate."' ";
			}else{
				$where .= " and date_format(cu.last,'%Y-%m-%d') between  '".$vstartDate."' and '".$vendDate."' ";
				$count_where .= " and date_format(cu.last,'%Y-%m-%d') between  '".$vstartDate."' and '".$vendDate."' ";
			}
		}
	}
	
	/*휴면회원관리 영역 JK160303*/
	if($sleep_in_date!='' && $sleep_out_date != ''){
		$sleep_in_date_time = date('Ymd',strtotime("-".$sleep_in_date."days",time()));
		$sleep_out_date_time = date('Ymd',strtotime("-".$sleep_out_date."days",time()));
		
		if($sleep_out_date == '0' || $sleep_out_date == ''){
			$sleep_area = " <  '$sleep_in_date_time'";
		}else{
			$sleep_area = " between '$sleep_out_date_time' and  '$sleep_in_date_time'";
		}

		if($db->dbms_type == "oracle"){
			$where .= " and  to_char(if ( isnull(cu.last), cu.date, cu.last ) , 'YYYYMMDD')  $sleep_area";
			$count_where .= " and  to_char(if ( isnull(cu.last), cu.date, cu.last ) , 'YYYYMMDD')  $sleep_area ";
		}else{
			$where .= " and  MID(replace(if ( isnull(cu.last), cu.date, cu.last ),'-',''),1,8)  $sleep_area";
			$count_where .= " and  MID(replace(if ( isnull(cu.last), cu.date, cu.last ),'-',''),1,8)  $sleep_area ";
		}
	}

	//$script_time[count_start] = time();
	// 전체 갯수 불러오는 부분
	$sql = "SELECT 
				count(*) as total 
			FROM 
				".TBL_COMMON_USER." cu
				inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on cu.code = cmd.code
				left join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (ccd.company_id = cu.company_id)
			where 
				1
				$count_where ";
				//left join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (cu.company_id = ccd.company_id)
	
	$db->query($sql);
	$db->fetch();
	$total = $db->dt[total];
	
	if($mode != "excel"){
		$limit_where = "LIMIT $start, $max";
	}


	if($mode == "reseller"){
		$resellerSelect = " rsl_ix, ";
		$resellerJoin = " LEFT JOIN reseller_policy rsl ON cu.code = rsl.rsl_code ";
	}

	$sql = "select ".$resellerSelect."
				mm.gp_name, mm.nationality, mm.mem_type, mm.mem_div, mm.authorized,
				mm.code, mm.visit, mm.last, mm.point, mm.id,
				mm.date, mm.birthday, mm.mileage, mm.is_id_auth, mm.loginType,
				AES_DECRYPT(UNHEX(mm.name),'".$db->ase_encrypt_key."') as name,
				AES_DECRYPT(UNHEX(mm.mail),'".$db->ase_encrypt_key."') as mail,
				AES_DECRYPT(UNHEX(mm.pcs),'".$db->ase_encrypt_key."') as pcs
			from 
			(select ".$resellerSelect."
				cu.mem_type, cu.mem_div, cu.authorized, cu.code, cu.visit, 
				date_format(cu.last,'%Y-%m-%d') as last, cu.point, cu.id, cu.date, cu.mileage,
				cu.is_id_auth, if(left(cu.id,'3') = 'ka@', 'K', 'B') as loginType, 
				cmd.nationality, cmd.name, cmd.mail, cmd.pcs, cmd.birthday,
				mg.gp_name 
			from 
				".TBL_COMMON_USER." as cu USE INDEX (date)
				".$resellerJoin."
				inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)
				left join ".TBL_SHOP_GROUPINFO." as mg on (cmd.gp_ix = mg.gp_ix)
			where
				1 and cu.mem_type != 'A'
				$where 
				order by date desc
				$limit_where ) mm ";

	$script_time[query_start] = time();
	$db->query($sql);

	$script_time[query_end] = time();
	
	$str_page_bar = page_bar($total, $page,$max, "","view");//gp_level 을 사용하지 않아서 gp_ix 로 바꿈 kbk 13/02/19
?>
