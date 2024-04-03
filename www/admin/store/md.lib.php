<?
function getFirstDIV($selected=""){
	global $admininfo;
	$mdb = new Database;

	$sql = 	"SELECT cr.*
			FROM common_region cr
			where depth = 1
			order by vieworder asc ";

	$mdb->query($sql);

	$mstring = "<select name='parent_rg_ix' id='parent_rg_ix' disabled>";
	$mstring .= "<option value=''>1차지역</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[rg_ix] == $selected){
				$mstring .= "<option value='".$mdb->dt[rg_ix]."' selected>".$mdb->dt[region_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[rg_ix]."'>".$mdb->dt[region_name]."</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}

function getSellerManager($branch, $team, $selected="",$txt=""){
	global $admininfo;
	$mdb = new Database;

	$where .= "";
	if($branch){
		$where .= " and cmd.branch = '$branch'  ";
	}

	if($branch){
		$where .= " and cmd.team = '$team' ";
	}
	if($txt=="") {
		if($mdb->dbms_type == "oracle"){
			$sql = 	"SELECT cmd.code , AES_DECRYPT(cmd.name,'".$mdb->ase_encrypt_key."') as name
					FROM common_user cu, common_member_detail cmd
					where cu.code = cmd.code and cu.mem_type = 'MD'
					order by cmd.name asc ";
		}else{
			$sql = 	"SELECT cmd.code , AES_DECRYPT(UNHEX(cmd.name),'".$mdb->ase_encrypt_key."') as name
					FROM common_user cu, common_member_detail cmd
					where cu.code = cmd.code and cu.mem_type = 'MD'
					order by cmd.name asc ";
		}
		$mdb->query($sql);

		$mstring = "<select name='md_code' id='md_code' >";
		$mstring .= "<option value=''>MD </option>";
		if($mdb->total){


			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);
				if($mdb->dt[code] == $selected){
					$mstring .= "<option value='".$mdb->dt[code]."' selected>".$mdb->dt[name]."</option>";
				}else{
					$mstring .= "<option value='".$mdb->dt[code]."'>".$mdb->dt[name]."</option>";
				}
			}

		}
		$mstring .= "</select>";
	} else {
		if($mdb->dbms_type == "oracle"){
			$sql = 	"SELECT AES_DECRYPT(cmd.name,'".$mdb->ase_encrypt_key."') as name
					FROM common_user cu, common_member_detail cmd
					where cu.code = cmd.code and cu.mem_type = 'MD'
					AND cmd.code='".$selected."' ";
		}else{
			$sql = 	"SELECT AES_DECRYPT(UNHEX(cmd.name),'".$mdb->ase_encrypt_key."') as name
					FROM common_user cu, common_member_detail cmd
					where cu.code = cmd.code and cu.mem_type = 'MD'
					AND cmd.code='".$selected."' ";
		}

		$mdb->query($sql);
		if($mdb->total){
			$mdb->fetch();
			$mstring=$mdb->dt["name"];
		} else {
			$mstring="";
		}
	}

	return $mstring;
}
function md_tab($selected = "md"){
	global $admininfo;
//print_r($admininfo);
	$mstring = " <div class='tab'>
		<table class='s_org_tab'>
			<tr>
				<td class='tab'>
				<!--
					<table id='tab_01' ".($selected == "list" ? "class='on'":"")." >
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='md_manage_list.php'>MD 목록</a></td>
						<th class='box_03'></th>
					</tr>
					</table>-->";
	if($admininfo[admin_level] == 9 && $admininfo[mem_type] != "MD" ){
	$mstring .= "
					<table id='tab_02' ".($selected == "md" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='md_manage.php'>MD 등록</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_03' ".($selected == "region" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='region.php'>지역관리</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_04' ".($selected == "branch" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='branch.php'>지사관리</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_05' ".($selected == "team" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='team.php'>팀관리</a></td>
						<th class='box_03'></th>
					</tr>
					</table>";
	}
	$mstring .= "
				</td>
			</tr>
			</table>
			</div>";

	return $mstring;
}




function makeBranchSelectBox($mdb, $select_name, $rg_ix='', $cb_ix="", $display_name = '전체보기', $property="", $txt=""){
	global $admininfo;
	$mdb = new Database;
	if($txt=="") {

		if($mdb->dbms_type == "oracle"){
			$mdb->query("SELECT * FROM common_branch where disp=1 and rg_ix = '$rg_ix'  order by level_ asc ");
		}else{
			$mdb->query("SELECT * FROM common_branch where disp=1 and rg_ix = '$rg_ix'  order by level asc ");
		}

		$mstring = "<select name='$select_name' id='$select_name' style='width:110px;font-size:11px;' $property>";
		$mstring .= "<option value=''>".$display_name."</option>";
		if($mdb->total){
			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);
				$mstring .= "<option value='".$mdb->dt[cb_ix]."' ".($mdb->dt[cb_ix] == $cb_ix ? "selected":"").">".$mdb->dt[branch_name]."</option>";
			}
		}else{
			$mstring .= "<option value=''>".$msg."</option>";
		}
		$mstring .= "</select>";
	} else {
		$mdb->query("SELECT branch_name FROM common_branch where disp=1 and rg_ix = '$rg_ix' AND cb_ix='".$cb_ix."' ");
		if($mdb->total){
			$mdb->fetch();
			$mstring=$mdb->dt["branch_name"]." &gt ";
		} else {
			$mstring="";
		}
	}

	return $mstring;
}



function makeTeamSelectBox($mdb,$select_name,$branch='', $cb_ix="",  $display_name = '전체보기', $property="",$txt=""){
	global $admininfo;
	$mdb = new Database;

	if($txt==""){
		if($mdb->dbms_type == "oracle"){
			$sql = "SELECT * FROM common_team where disp=1 and branch = '$branch'  order by level_ asc ";
		}else{
			$sql = "SELECT * FROM common_team where disp=1 and branch = '$branch'  order by level asc ";
		}
		//echo $sql;
		$mdb->query($sql);

		$mstring = "<select name='$select_name' id='$select_name' style='width:110px;font-size:11px;' $property>";
		$mstring .= "<option value=''>".$display_name."</option>";
		if($mdb->total){
			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);
				$mstring .= "<option value='".$mdb->dt[ct_ix]."' ".($mdb->dt[ct_ix] == $cb_ix ? "selected":"").">".$mdb->dt[team_name]."</option>";
			}
		}else{
			$mstring .= "<option value=''>".$msg."</option>";
		}
		$mstring .= "</select>";
	} else {
		$sql = "SELECT team_name FROM common_team where disp=1 and branch = '$branch' AND ct_ix='".$cb_ix."' ";
		//echo $sql;
		$mdb->query($sql);
		if($mdb->total){
			$mdb->fetch();
			$mstring=$mdb->dt["team_name"]." &gt ";
		} else {
			$mstring="";
		}
	}
	return $mstring;
}


function getRegionInfoSelect($obj_id, $obj_txt, $parent_rg_ix, $selected, $depth=1, $property="",$txt=""){
	global $admininfo;

	$mdb = new Database;
	if($txt=="") {
		if($depth == 1){
			/*
			$sql = 	"SELECT abg.*
					FROM common_region abg
					where depth = '$depth'
					group by rg_ix order by vieworder asc";
			*/
			$sql = 	"SELECT abg.*
					FROM common_region abg
					where depth = '$depth'
					order by vieworder asc";
		}else if($depth == 2){
			/*
			$sql = 	"SELECT abg.*
					FROM common_region abg
					where depth = '$depth' and parent_rg_ix = '$parent_rg_ix'
					group by rg_ix order by vieworder asc ";
			*/
			$sql = 	"SELECT abg.*
					FROM common_region abg
					where depth = '$depth' and parent_rg_ix = '$parent_rg_ix'
					order by vieworder asc ";
		}
		//echo $sql;
		$mdb->query($sql);

		$mstring = "<select name='$obj_id' id='$obj_id' $property style='width:110px;'>";
		$mstring .= "<option value=''>$obj_txt</option>";
		if($mdb->total){


			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);
				if($mdb->dt[rg_ix] == $selected){
					$mstring .= "<option value='".$mdb->dt[rg_ix]."' selected>".$mdb->dt[region_name]."</option>";
				}else{
					$mstring .= "<option value='".$mdb->dt[rg_ix]."'>".$mdb->dt[region_name]."</option>";
				}
			}

		}
		$mstring .= "</select>";
	} else {
		$sql = 	"SELECT abg.region_name FROM common_region abg WHERE rg_ix='".$selected."' ";
		$mdb->query($sql);
		if($mdb->total){
			$mdb->fetch();
			$mstring = $mdb->dt["region_name"]." &gt ";
		} else {
			$mstring = "";
		}
	}

	return $mstring;
}

?>