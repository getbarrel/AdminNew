<?

function get_relation_code($length){
	
	$db = new Database;

	$sql = "select
				MAX(relation_code) as code
			from
				".TBL_COMMON_COMPANY_RELATION."
			where
				LENGTH(relation_code) = '$length'";
	
	$db->query($sql);
	$data = $db->fetch();

	if($data[code]){
		$data = explode('C',$data[code]);
		$i = $data[1] + 1;
		$code = "C".sprintf("%04d",$i);
		return $code;
	}else{
		return "C0001";
	}
}
function check_relation($relation_code,$length=""){

	$db = new Database;
	if($length > 0){
	$length_1 = $length +10;
	}else if($length==""){
		$length = strlen($relation_code);
	}
	$sql = "select
					MAX(relation_code) as code
				from
					".TBL_COMMON_COMPANY_RELATION."
				where
					relation_code like '".$relation_code."%'
			";

	$db->query($sql);
	$data = $db->fetch();
	if($data[code]){
		$code = substr($data[code],$length,10);
		//echo $code."<br>";
		if(empty($code)){
			$i = 1000000001;
		}else{
			$i = $code +1;
		}
		//$code = substr($data[code],0,$length).sprintf("%04d",$i);
		$code = substr($data[code],0,$length).$i;
		//echo $code;
	}

	return $code;
}

function get_custseq($seq){
	
	$db = new Database;
	if(!$seq){ return false;}
	
	$sql = "select max(custseq) as custseq from	common_company_detail where custseq like '".$seq."%'";
	
	$db->query($sql);
	$db->fetch();

	$custseq = $db->dt[custseq];

	if($custseq){
		$custseq = $custseq +1;
	}else{
		$custseq = '1400000';
	}

	return $custseq;
}

function check_seq($relation_code , $length=""){
	$db = new Database;
	if($length){
		$length = $length + 10;
	}else{
		$length = strlen($relation_code);
	}
	$sql = "select
					MAX(seq) as seq
				from
					".TBL_COMMON_COMPANY_RELATION."
				where
					length(relation_code) = '".$length."'
					and relation_code like '".$relation_code."%'
			";
	$db->query($sql);
	$data = $db->fetch();

	if($data[seq]){
		$seq = $data[seq] + 1;
		return $seq;
	}else{
		return 1;
	}
}

function get_seq($length){
	
	$db = new Database;
	
	$sql = "select
						MAX(seq) as seq
					from
						".TBL_COMMON_COMPANY_RELATION."
					where
						LENGTH(relation_code) = '$length'
			";
	
	$db->query($sql);
	$data = $db->fetch();

	if($data[seq]){
		$seq = $data[seq] + 1;
		return $seq;
	}else{
		return 1;
	}

}


function getCompanyList($category_text ="본사", $object_name="cid", $onchange_handler="", $depth=5, $cid="",$type = "member")
{
	$mdb = new Database;

	if($type == "member"){
		$where = " and com_type not in ('G','S') "; 
	}

	if($depth == 5 || $cid != ""){
		$sql = "SELECT 
					c.com_name,
					c.company_id,
					cr.relation_code
				FROM 
					".TBL_COMMON_COMPANY_DETAIL." as c 
					inner join ".TBL_COMMON_COMPANY_RELATION." as cr on (c.company_id = cr.company_id)
				where
					length(cr.relation_code ) = '$depth'
					$where 
					order by cr.relation_code ASC";

		$mdb->query($sql);

	}

	if($depth == '5'){
		$validation = "true";
	}else{
		$validation = "false";
	}

	if ($mdb->total){
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler validation='".$validation."' style='width:140px;font-size:12px;' validation='false'>\n";
		$mstring = $mstring."<option value=''>$category_text</option>\n";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			if(substr($cid,0,$depth) == substr($mdb->dt[relation_code],0,$depth)){

				$mstring = $mstring."<option value='".$mdb->dt[relation_code]."' selected>".$mdb->dt[com_name]."</option>\n";
			}else{
				$mstring = $mstring."<option value='".$mdb->dt[relation_code]."' >".$mdb->dt[com_name]."</option>\n";
			}
		}
	}else{
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler validation=false  style='width:140px;font-size:12px;'>\n";
		$mstring = $mstring."<option value=''> $category_text</option>\n";
	}

	$mstring = $mstring."</Select>\n";
	
	return $mstring;

}

function getgroup($dp_ix = ""){
	global $admininfo;
	$mdb = new Database;
	
	$sql = "select
			group_ix
		from
			".TBL_SHOP_COMPANY_DEPARTMENT."
		where
			dp_ix = '".$dp_ix."'
			and disp = '1'
	";

	$mdb->query($sql);
	$mdb->fetch();

	$group_ix = $mdb->dt[group_ix];
	$sql = 	"SELECT *
			FROM ".TBL_SHOP_COMPANY_GROUP."
			where  1
			and disp = '1'
			order by seq asc ";

	$mdb->query($sql);
	if(!$dp_ix){
		$disabled = "disabled";
	}
	$mstring = "<select id = 'department_group' name='department_group' ".$disabled." style='width:140px;font-size:12px;'>";
	$mstring .= "<option value=''>본부</option>";
	if($mdb->total){

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[group_ix] == $group_ix){
				$mstring .= "<option value='".$mdb->dt[group_ix]."' selected>".$mdb->dt[group_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[group_ix]."'>".$mdb->dt[group_name]."</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}


////////////////부서설정 시작///////////////////////////

function getgroup1($group_ix = "", $load_js="",$validation="false"){	//본부

	global $admininfo;
	$mdb = new Database;
	
	$sql = 	"SELECT *
			FROM ".TBL_SHOP_COMPANY_GROUP."
			where  1
			order by seq asc ";

	$mdb->query($sql);

	$mstring = "<select id = 'com_group' $load_js name='com_group' validation='".$validation."' style='width:140px;font-size:12px;'>";
	$mstring .= "<option value=''>본부</option>";
	if($mdb->total){

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[group_ix] == $group_ix){
				$mstring .= "<option value='".$mdb->dt[group_ix]."' selected>".$mdb->dt[group_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[group_ix]."'>".$mdb->dt[group_name]."</option>";
			}
		}
	}

	$mstring .= "</select>";

	return $mstring;
}

function getdepartment($dp_ix = "", $load_js="",$validation="false"){	//부서
	global $admininfo;
	$mdb = new Database;
	
	$sql = 	"SELECT *
			FROM ".TBL_SHOP_COMPANY_DEPARTMENT."
			where  1
			and disp = '1'
			order by seq asc ";

	$mdb->query($sql);

	$mstring = "<select id = 'department' $load_js name='department' validation='".$validation."' style='width:140px;font-size:12px;'>";
	$mstring .= "<option value=''>부서</option>";
	if($mdb->total){

		for($i=0;$i < $mdb->total;$i++){
		
			$mdb->fetch($i);
			if($mdb->dt[dp_ix] == $dp_ix){
				$mstring .= "<option value='".$mdb->dt[dp_ix]."' selected>".$mdb->dt[dp_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[dp_ix]."'>".$mdb->dt[dp_name]."</option>";
			}
		}
	}
	$mstring .= "</select>";

	return $mstring;
}

function getposition($ps_ix = "", $load_js="",$validation="false"){		//직위
	global $admininfo;
	$mdb = new Database;
	
	$sql = 	"SELECT *
			FROM ".TBL_SHOP_COMPANY_POSITION."
			where  1
			and disp = '1'
			order by seq asc ";

	$mdb->query($sql);

	$mstring = "<select id = 'position' $load_js name='position' validation='".$validation."'  style='width:140px;font-size:12px;'>";
	$mstring .= "<option value=''>직위</option>";
	if($mdb->total){

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[ps_ix] == $ps_ix){
				$mstring .= "<option value='".$mdb->dt[ps_ix]."' selected>".$mdb->dt[ps_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[ps_ix]."'>".$mdb->dt[ps_name]."</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}

function getduty($cu_ix = "", $load_js="",$validation="false"){	//직책
	global $admininfo;
	$mdb = new Database;
	
	$sql = 	"SELECT *
			FROM ".TBL_SHOP_COMPANY_DUTY."
			where  1
			order by seq asc ";

	$mdb->query($sql);

	$mstring = "<select id = 'duty' name='duty' validation='".$validation."' $load_js style='width:140px;font-size:12px;'>";
	$mstring .= "<option value=''>직책</option>";
	if($mdb->total){

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[cu_ix] == $cu_ix){
				$mstring .= "<option value='".$mdb->dt[cu_ix]."' selected>".$mdb->dt[duty_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[cu_ix]."'>".$mdb->dt[duty_name]."</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}
////////////////부서설정 끝///////////////////////////



function getGroupname($type,$ix){
	
	$db = new Database;

	if($type == "group"){
		$sql = "select group_name from ".TBL_SHOP_COMPANY_GROUP." where group_ix = '".$ix."'";
		$db->query($sql);
		$db->fetch();
	
		return $db->dt[group_name];
	}else if($type == "department"){
		$sql = "select dp_name from ".TBL_SHOP_COMPANY_DEPARTMENT." where dp_ix = '".$ix."'";
		$db->query($sql);
		$db->fetch();

		return $db->dt[dp_name];
	}else if($type == "position"){
		$sql = "select ps_name from ".TBL_SHOP_COMPANY_POSITION." where ps_ix = '".$ix."'";
		$db->query($sql);
		$db->fetch();

		return $db->dt[ps_name];
	}else if($type == "duty"){
	
		$sql = "select duty_name from ".TBL_SHOP_COMPANY_DUTY." where cu_ix = '".$ix."'";
		$db->query($sql);
		$db->fetch();

		return $db->dt[duty_name];
	}

}

function getCompanyname($relation_code, $length=""){
	$db = new Database;
	
if($length){
	if(strlen($relation_code) >= $length){

	$relation_code = substr($relation_code,0,$length);
	
	$sql = "select
				com_name
			from
				".TBL_COMMON_COMPANY_DETAIL." as cd 
				inner join ".TBL_COMMON_COMPANY_RELATION." as cr on (cd.company_id = cr.company_id)
			where
				cr.relation_code = '".$relation_code."'
	";

	$db->query($sql);
	$db->fetch();
	return $db->dt[com_name];

	}else{
		return "-";
	}
}else{
	$sql = "select
				com_name
			from
				".TBL_COMMON_COMPANY_DETAIL." as cd 
				inner join ".TBL_COMMON_COMPANY_RELATION." as cr on (cd.company_id = cr.company_id)
			where
				cr.relation_code = '".$relation_code."'
	";

	$db->query($sql);
	$db->fetch();
	return $db->dt[com_name];

}
}

function get_person($com_group,$department,$position,$duty,$company_id,$person_code=""){

	$mdb = new Database;
	$sql = "select
			AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
			ccd.company_id,
			cmd.code
		from
			".TBL_COMMON_USER." as cu
			inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)
			inner join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (cu.company_id = ccd.company_id)
		where
			cu.mem_type in ('A')
			and cu.id !='forbiz'
			$where
	"; 
	$mdb->query($sql);
	$data_array = $mdb->fetchall();
	
	$mstring = "<select id = 'com_person' name='com_person'  style='width:140px;font-size:12px;'>";
	$mstring .= "<option value=''>담당자</option>";
	if(count($data_array) > 0){

		for($i=0;$i < count($data_array);$i++){
	
			if($data_array[$i][code] == $person_code){
				$mstring .= "<option value='".$data_array[$i][code]."' selected>".$data_array[$i][name]."</option>";
			}else{
				$mstring .= "<option value='".$data_array[$i][code]."'>".$data_array[$i][name]."</option>";
			}
		}

	}

	$mstring .= "</select>";

	return $mstring;
}

function getmembercount($company_id){
	$mdb = new Database;

	$sql = "select
				count(*) as cnt
			from
				".TBL_COMMON_COMPANY_DETAIL." as ccd
				inner join ".TBL_COMMON_USER." as cu on (ccd.company_id = cu.company_id)
			where
				ccd.company_id = '".$company_id."'
				and cu.mem_type in ('A')
				and cu.id != 'forbiz'
	";
	$mdb->query($sql);
	$mdb->fetch();

	return $mdb->dt[cnt];
	
}

function personName($code){

	$mdb = new Database;

	$sql = "select
		AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
	from
		".TBL_COMMON_MEMBER_DETAIL." as cmd 
	where
	 cmd.code = '".$code."'
	"; 
	//echo "$sql"."<br>";
	$mdb->query($sql);
	$mdb->fetch();
	 if($mdb->dt[name]){
		return $mdb->dt[name];
	 }else{
		return "-";
	 }

}


?>