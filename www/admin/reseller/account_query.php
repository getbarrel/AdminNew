<?php

	$max = 20; //페이지당 갯수

	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}
	else
	{
		$start = ($page - 1) * $max;
	}

	$where = " WHERE r.rsl_code != ''";

	//[S] 검색조건

	if($search_type && $search_text){
		if($search_type == "id" || $search_type == "manager"){
			$sql = "SELECT code FROM common_user WHERE id LIKE '%".$search_text."%'";
		}else if($search_type == "name"){
			$sql = "SELECT code FROM common_member_detail WHERE AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') LIKE '%".$search_text."%'";
		}

		$db->query($sql);
		$codeArr = $db->fetchAll();

		for($i=0; $i<count($codeArr); $i++){
			$codeSort[] = $codeArr[$i]["code"];
		}

		if($codeSort > 0){
			$codeWhere = implode("','", $codeSort);
		}else{
			$codeWhere = "";
		}

		if($search_type == "manager"){
			$where .= " AND r.rsl_manager IN ('".$codeWhere."')";
		}else{
			$where .= " AND r.rsl_code IN ('".$codeWhere."')";
		}
	}

	if($regdate == 1){
		$where .= " AND i.ac_day >= '".$startDate." 00:00:00' AND i.ac_day <= '".$endDate." 23:59:59' ";
	}

	if($rsl_div){
		$where .= " AND r.rsl_div = '".$rsl_div."' ";
	}

	if($status){
		$where .= " AND a.status = '".$status."' ";
	}

	//[E] 검색조건

	$sql = "SELECT
				r.rsl_code,
				r.rsl_manager,
				r.rsl_div,
				a.ac_ix,
				a.regdate,
				a.ac_day,
				a.fee_rate,
				a.sum_incentive,
				a.status,
				a.way,
				(SELECT AES_DECRYPT(UNHEX(d.name),'".$db->ase_encrypt_key."') FROM common_member_detail d WHERE d.code = r.rsl_code) as name,
				(SELECT id FROM common_user u WHERE u.code = r.rsl_code) as id
			FROM
				reseller_accounts a
			INNER JOIN
				reseller_policy r
			ON
				a.rsl_code = r.rsl_code
			".$where."
	";

	$db->query($sql);
	$total = $db->total;

?>