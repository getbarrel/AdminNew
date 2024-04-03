<?
include("../../class/database.class");
include("../logstory/class/sharedmemory.class");

if($search_searialize_value){
	$unserialize_search_value = unserialize(urldecode($search_searialize_value));
	extract($unserialize_search_value);
}

$db = new Database;
$db2 = new Database;

//search 조건시작
if($mode == 'search'){

	if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
		//다중검색 시작 2014-04-10 이학봉
		/*
		if($search_type == "cmd.name"){
			$search_str .= " and AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
		}
		*/

		if($search_text != ""){
			if(strpos($search_text,",") !== false){
				$search_array = explode(",",$search_text);
				$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
				$search_str .= "and ( ";
				$count_where .= "and ( ";
				for($i=0;$i<count($search_array);$i++){
					$search_array[$i] = trim($search_array[$i]);
					if($search_array[$i]){
						if($i == count($search_array) - 1){
							$search_str .= $search_type." = '".trim($search_array[$i])."'";
							$count_where .= $search_type." = '".trim($search_array[$i])."'";
						}else{
							$search_str .= $search_type." = '".trim($search_array[$i])."' or ";
							$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
						}
					}
				}
				$search_str .= ")";
				$count_where .= ")";
			}else if(strpos($search_text,"\n") !== false){//\n
				$search_array = explode("\n",$search_text);
				$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
				$search_str .= "and ( ";
				$count_where .= "and ( ";

				for($i=0;$i<count($search_array);$i++){
					$search_array[$i] = trim($search_array[$i]);
					if($search_array[$i]){
						if($i == count($search_array) - 1){
							$search_str .= $search_type." = '".trim($search_array[$i])."'";
							$count_where .= $search_type." = '".trim($search_array[$i])."'";
						}else{
							$search_str .= $search_type." = '".trim($search_array[$i])."' or ";
							$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
						}
					}
				}
				$search_str .= ")";
				$count_where .= ")";
			}else{
				$search_str .= " and ".$search_type." = '".trim($search_text)."'";
				$count_where .= " and ".$search_type." = '".trim($search_text)."'";
			}
		}

	}else{	//검색어 단일검색
		if($search_text != ""){
			if(substr_count($search_text,",")){
				$search_str .= " and ".$search_type." in ('".str_replace(",","','",str_replace(" ","",$search_text))."') ";
			}else{
				$search_str .= " and ".$search_type." LIKE '%".trim($search_text)."%' ";
			}
		}
	}

	//승인여부
	if(is_array($seller_auth) && count($seller_auth)>0){		//재고관리 (사용안함,빠른재고,WMS재고 ... )
		$search_str.=" AND ccd.seller_auth IN ('".implode("','",$seller_auth)."')";
	}else{
		if($seller_auth != ""){
			$search_str .= " and ccd.seller_auth = '".$seller_auth."'";
		}else{
			$seller_auth=array();
		}
	}

	//미니샵사용여부
	if(is_array($minishop_use) && count($minishop_use)>0){		//노출여부 
		$search_str.=" AND csd.minishop_use IN ('".implode("','",$minishop_use)."')";
	}else{
		if($disp != ""){
			$search_str .= " and csd.minishop_use = '".$minishop_use."'";
		}else{
			$disp=array();
		}
	}

	if($charge_code !=""){		//협력사 마스터 ID유무
		if($charge_code == "Y"){
		$search_str .= " and csd.charge_code = cu.code ";
		}else{
		$search_str .= " and csd.charge_code != cu.code ";
		}
	}

	if($auth !=""){	//셀러사용자 신청 처리상태
		if($auth == "Y"){
		$search_str .= " and cu.auth = '4' ";
		}else{
		$search_str .= " and cu.auth != '4' ";
		}
	}


	if($_REQUEST['regdate'] == '1'){
		if($sdate != "" && $edate != ""){
			if($db->dbms_type == "oracle"){
				$search_str .= " and  to_char(csd.regdate_ , 'YYYY-MM-DD') between  '".$sdate."' and '".$edate."' ";
			}else{
				$search_str .= " and date_format(csd.regdate,'%Y-%m-%d') between  '".$sdate."' and '".$edate."' ";
			}
		}
	}

}

if($admininfo[admin_level] == 8){
	$search_str .= " and ccd.company_id = '".$admininfo['company_id']."'";
}

// search 조건	끝

/*선택한 회원이나 , 전체 검색한 회원은 같기에 위에서 한번만 선언한다. 2014-04-16 이학봉*/
if($update_kind == "minishop_use"){
	
	if($update_type == '1'){	//검색한 회원
		$sql = "select
				distinct ccd.company_id
			from
				common_company_detail as ccd
				inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
				left join common_user as cu on (csd.charge_code = cu.code)
				left join common_member_detail as cmd on (cu.code = cmd.code)
				left join shop_category_info as ci on (csd.seller_cid = ci.cid)
			where
				ccd.com_type = 'S'
				$search_str
				group by ccd.company_id";
		$db->query ($sql);
		$select_pid = $db->fetchall();
		
		for($i=0;$i<count($select_pid);$i++){
			$select_pid[$i] = $select_pid[$i]['company_id'];
		}

	}else if($update_type == '2'){	//선택한 회원
		$select_pid = $select_pid;
	}

	for($i=0;$i<count($select_pid);$i++){

		$company_id = $select_pid[$i];

		$sql = "update common_seller_detail set
					minishop_use = '".$batch_minishop_use_yn."'
				where
					company_id = '".$company_id."'";
		$db->query($sql);
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('선택업체의 미니샵사용여부 정보변경이 적상적으로 완료되었습니다.');</script>");
	echo("<script>if(top.document.getElementById('act').src != 'about:blank'){top.select_update_unloading();top.location.reload();}else{top.location.reload();}</script>");
}

if($update_kind == "econtract"){		//견적서 일괄적용 2014-07-07 이학봉
	include("../econtract/contract.lib.php");

	if($update_type == '1'){	//검색한 회원
		$sql = "select
				distinct ccd.company_id
			from
				common_company_detail as ccd
				inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
				left join common_user as cu on (csd.charge_code = cu.code)
				left join common_member_detail as cmd on (cu.code = cmd.code)
				left join shop_category_info as ci on (csd.seller_cid = ci.cid)
			where
				ccd.com_type = 'S'
				$search_str
				group by ccd.company_id";
		$db->query ($sql);
		$select_pid = $db->fetchall();
		
		for($i=0;$i<count($select_pid);$i++){
			$select_pid[$i] = $select_pid[$i]['company_id'];
		}

	}else if($update_type == '2'){	//선택한 회원
		$select_pid = $select_pid;
	}

	for($i=0;$i<count($select_pid);$i++){

		$company_id = $select_pid[$i];
		/*
		$sql = "update common_seller_delivery set
					et_ix = '".$et_ix."',
					econtract_commission = '".$electron_contract_commission."'
				where 
					company_id = '".$company_id."'";
		$db->query($sql);
		*/

		//전자계약서 인증 함수
		regContract($db,$_SESSION['admininfo']['company_id'], $company_id, $et_ix);

	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('선택업체의 전자계약서 정보변경이 적상적으로 완료되었습니다.');</script>");
	echo("<script>if(top.document.getElementById('act').src != 'about:blank'){top.select_update_unloading();top.location.reload();}else{top.location.reload();}</script>");
}


if($update_kind == "use_disp"){

	if($update_type == '1'){	//검색한 회원
		$sql = "select
				distinct ccd.company_id
			from
				common_company_detail as ccd
				inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
				left join common_user as cu on (csd.charge_code = cu.code)
				left join common_member_detail as cmd on (cu.code = cmd.code)
				left join shop_category_info as ci on (csd.seller_cid = ci.cid)
			where
				ccd.com_type = 'S'
				$search_str
				group by ccd.company_id";
		
		$sql = "select
					cu.code
				from
					common_user as cu 
					inner join common_member_detail as cmd on (cu.code = cmd.code)
					inner join common_company_detail as ccd on (cu.company_id = ccd.company_id)
					inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
				where
					1
					and cu.mem_div = 'S'
					and ccd.com_type = 'S' 
					$search_str
					group by ccd.company_id
					order by csd.regdate desc";

		$db->query ($sql);
		$select_pid = $db->fetchall();
		
		for($i=0;$i<count($select_pid);$i++){
			$select_pid[$i] = $select_pid[$i]['code'];
		}

	}else if($update_type == '2'){	//선택한 회원
		$select_pid = $select_pid;
	}

	for($i=0;$i<count($select_pid);$i++){

		$code = $select_pid[$i];

		$sql="UPDATE ".TBL_COMMON_USER." SET auth='".$use_disp."' WHERE code='".$code."' ";	//셀러회원 권한 부여 이학봉
		$db2->query($sql);

		

	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('선택한 사용자의 승인여부 정보변경이 적상적으로 완료되었습니다.');</script>");
	echo("<script>if(top.document.getElementById('act').src != 'about:blank'){top.select_update_unloading();top.location.reload();}else{top.location.reload();}</script>");
}




?>