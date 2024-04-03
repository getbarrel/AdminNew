<?

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
		
		
		if($orderdate){
			$where .= "and date_format(cu.date,'%Y-%m-%d') between '".$startDate."' and '".$endDate."' ";
		}

		if($mem_type){
			$where .= "and cu.mem_type = '".$mem_type."' ";
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

		if($mall_ix){
			$where .=" and cu.mall_ix = '".$mall_ix."' ";
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

			if($search_type != "" && $search_text != ""){
				if($search_type == "cmd.name" || $search_type == "cmd.mail" || $search_type == "cmd.pcs" || $search_type == "cmd.tel" ){


					if(strpos($search_text,",") !== false){
						$search_array = explode(",",$search_text);
						$search_array = array_filter($search_array);
						
						$where .= "and ( ";
						$count_where .= "and ( ";
						for($i=0;$i<count($search_array);$i++){

							if($i == count($search_array) - 1){
								$where .= "AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') = '".trim($search_array[$i])."'";
								$count_where .= "AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') = '".trim($search_array[$i])."'";
							}else{
								$where .= "AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') = '".trim($search_array[$i])."' or ";
								$count_where .= "AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') = '".trim($search_array[$i])."' or ";
							}
						}
						$where .= ")";
						$count_where .= ")";
					}else if(strpos($search_text,"\n") !== false){
						$search_array = explode("\n",$search_text);
						$search_array = array_filter($search_array);
						
						$where .= "and ( ";
						$count_where .= "and ( ";
						for($i=0;$i<count($search_array);$i++){

							if($i == count($search_array) - 1){
								$where .= "AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') = '".trim($search_array[$i])."'";
								$count_where .= "AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') = '".trim($search_array[$i])."'";
							}else{
								$where .= "AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') = '".trim($search_array[$i])."' or ";
								$count_where .= "AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') = '".trim($search_array[$i])."' or ";
							}
						}
						$where .= ")";
						$count_where .= ")";
					}else{

                        $where .= " and (REPLACE(AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "'),'-','') LIKE  '%$search_text%' OR  AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "') LIKE  '%$search_text%')";
                        $count_where .= " and (REPLACE(AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "'),'-','') LIKE  '%$search_text%' OR AES_DECRYPT(UNHEX($search_type),'" . $db->ase_encrypt_key . "') LIKE  '%$search_text%')";


					}



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
	
	}
	
	if( ! empty($where) ){

		// 전체 갯수 불러오는 부분
		$sql = "SELECT 
					count(*) as total 
				FROM ".TBL_COMMON_USER." cu
				inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on cu.code = cmd.code
				left join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (cu.company_id = ccd.company_id)
				where 1 $where";
		$db->query($sql);
		$db->fetch();
		$total = $db->dt[total];
		//$script_time[count_end] = time();
		//$script_time[query_start] = time();

		if($db->dbms_type == "oracle"){//ccd.com_name, mg.gp_level,mg.gp_name, 
			//$db->add_select_query = " func_get_group_name(gp_ix) as gp_name ";
			$sql = "select
					cu.mall_ix,cu.code, cu.id,cmd.sex_div, cmd.pcs,
					cu.company_id, ccd.com_name,cu.mileage,cu.point,cu.mem_div,
					AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, mg.gp_level,
					AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail, cu.authorized as authorized, cu.is_id_auth as is_id_auth, cu.mem_type as mem_type,
					cu.visit, 
					date_format(cu.date,'%Y.%m.%d') as regdate, mg.gp_name, 
					cu.last AS last, cmd.gp_ix,cmd.nationality, cmd.info
				from 
					".TBL_COMMON_USER." as cu 
					inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)
					left join ".TBL_SHOP_GROUPINFO." as mg on (cmd.gp_ix = mg.gp_ix)
					left join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (ccd.company_id = cu.company_id)
					left join shop_event_applicant as ea on (ea.mem_ix = cmd.code)
				where
					1 $where 
					ORDER BY cu.date_ DESC
					LIMIT $start, $max";

		}else{
			$sql = "select
						cu.mall_ix,cu.code, cu.id,cmd.sex_div, 
						cu.company_id, ccd.com_name,cu.mileage,cu.point,cu.mem_div, 
						AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs,cmd.sms,
						AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, mg.gp_level,
						AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail, cu.authorized as authorized, cu.is_id_auth as is_id_auth, cu.mem_type as mem_type,
						cu.visit,
						date_format(cu.date,'%Y.%m.%d') as regdate, mg.gp_name,
						cu.last AS last, cmd.gp_ix,cmd.nationality, cmd.info
					from 
						".TBL_COMMON_USER." as cu 
						inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)
						left join ".TBL_SHOP_GROUPINFO." as mg on (cmd.gp_ix = mg.gp_ix)
						left join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (ccd.company_id = cu.company_id)
						
					where 1 $where 
					ORDER BY cu.date DESC
					LIMIT $start, $max";
		}
		//ea.event_ix, ea.is_winner,
		//left join shop_event_applicant as ea on (ea.mem_ix = cmd.code)

		//echo $sql;
		//exit;
		//exit;
		$db->query($sql);

	}else{
		$search_false=true;
	}

	if($QUERY_STRING == "nset=$nset&page=$page"){
		$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
	}else{
		$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
	}
	$str_page_bar = page_bar($total, $page,$max,$query_string ,"view");//gp_level 을 사용하지 않아서 gp_ix 로 바꿈 kbk 13/02/19

?>
