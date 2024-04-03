<?
include("../../class/database.class");

session_start();
$department = $_GET['trigger'];
$depth = $_GET['depth'];
$target = $_GET['target'];
$form = $_GET['form'];

//header("Content-Type: application/x-javascript");

$db = new Database;

echo "<script language='javascript'>
\n";

if($department){
	/*
	$sql = 	"SELECT abg.* FROM work_group abg where group_depth = '$depth' and parent_group_ix = '$cid'";
	*/

	$sql = "select cmd.code, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
			from common_user cu, common_member_detail cmd , service_ing si
			where cu.code = cmd.code and cmd.code = si.mem_ix
			and cu.authorized = 'Y' and si.solution_div = 'WORK' and si_status = 'SI'
			and company_id ='".$admininfo["company_id"]."'
			and department in (".$department.") ";
	if($db->dbms_type == "oracle"){
		$sql = "select cmd.code , ps_name, AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name
				from common_user cu left join common_company_detail ccd on cu.company_id = ccd.company_id and cu.company_id ='".$admininfo["company_id"]."'
				left join  common_member_detail cmd on cu.code = cmd.code
				left join shop_company_position cp on cmd.position = cp.ps_ix
				where cu.authorized = 'Y'
				and cu.company_id ='".$admininfo["company_id"]."'
				and cmd.department = '".$department."' ";

	}else{
		$sql = "select cmd.code , ps_name, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
				from common_user cu left join common_company_detail ccd on cu.company_id = ccd.company_id and cu.company_id ='".$admininfo["company_id"]."'
				left join  common_member_detail cmd on cu.code = cmd.code
				left join shop_company_position cp on cmd.position = cp.ps_ix
				where cu.authorized = 'Y'
				and cu.company_id ='".$admininfo["company_id"]."'
				and cmd.department = '".$department."' ";
	}
	//echo $sql;
	//exit;
	$db->query($sql);
	$department_total = $db->total;


	if ($db->total){

				//if($target == "cid0_1" || $target == "cid1_1" || $target == "cid2_1" || $target == "cid3_1"){

		  //}



			if($wl_ix){
				if($db->dbms_type == "oracle"){
					$sql = 	"SELECT cmd.code, AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name FROM work_charger_relation cr, common_member_detail cmd  where wl_ix ='$wl_ix' and cr.charger_ix = cmd.code ";
				}else{
					$sql = 	"SELECT cmd.code, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name FROM work_charger_relation cr, common_member_detail cmd  where wl_ix ='$wl_ix' and cr.charger_ix = cmd.code ";
				}
				//echo $sql;
				$db->query($sql);

				echo "parent.document.forms['$form'].elements['".$target."'].length = ".($department_total+$db->total+1)."; \n";
				//echo "parent.document.forms['$form'].elements['".$target."'].validation = 'true'; \n";
				echo "parent.document.forms['$form'].elements['".$target."'].options[0].selected = false; \n";

				for($i=0,$j=0; $i < $db->total; $i++, $j++){
					$db->fetch($i);

				//	echo "parent.document.forms['$form'].elements['".$target."'].options[".($j+1)."].text = '".$db->dt[name]."'; \n";
				//	echo "parent.document.forms['$form'].elements['".$target."'].options[".($j+1)."].value = '".$db->dt[code]."'; \n";
				//	echo "parent.document.forms['$form'].elements['".$target."'].options[".($j+1)."].selected = true; \n";
				//	echo "parent.document.forms['$form'].elements['".$target."'].options[".($i+1)."].setAttribute('ps_name') = '".$db->dt[ps_name]."'; \n";
				//echo "alert(parent.document.forms['$form'].elements['".$target."'].options[".($i+1)."].setAttribute('ps_name'));";


					if($selected_charger){
						$selected_charger .= ",'".$db->dt[code]."'";
					}else{
						$selected_charger .= "'".$db->dt[code]."'";
					}
				}
				if($selected_charger){
					$selected_charger_where = " and charger_ix not in (".$selected_charger.") ";
				}
			}else{
				echo "parent.document.forms['$form'].elements['".$target."'].length = ".($department_total+1)."; \n";
				//echo "parent.document.forms['$form'].elements['".$target."'].validation = 'true'; \n";
				echo "parent.document.forms['$form'].elements['".$target."'].options[0].selected = true; \n";
			}

		//echo $sql;
			if($department){
				$department_str = " and department in (".$department.") ";
			}

			$sql = "select cmd.code, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name from common_user cu, common_member_detail cmd , service_ing si
				where cu.code = cmd.code and cmd.code = si.mem_ix and cu.authorized = 'Y' and si.solution_div = 'WORK' and si_status = 'SI'
				and company_id ='".$admininfo["company_id"]."'
				 $department_str  $selected_charger_where ";

			if($db->dbms_type == "oracle"){
				$sql = "select cmd.code , ps_name, AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name
				from common_user cu left join common_company_detail ccd on cu.company_id = ccd.company_id and cu.company_id ='".$admininfo["company_id"]."'
				left join  common_member_detail cmd on cu.code = cmd.code
				left join shop_company_position cp on cmd.position = cp.ps_ix
				where cu.authorized = 'Y'
				and cu.company_id ='".$admininfo["company_id"]."'
				  $department_str  $selected_charger_where ";
			}else{
				$sql = "select cmd.code , ps_name, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
				from common_user cu left join common_company_detail ccd on cu.company_id = ccd.company_id and cu.company_id ='".$admininfo["company_id"]."'
				left join  common_member_detail cmd on cu.code = cmd.code
				left join shop_company_position cp on cmd.position = cp.ps_ix
				where cu.authorized = 'Y'
				and cu.company_id ='".$admininfo["company_id"]."'
				  $department_str  $selected_charger_where ";
			}
			//echo $sql;
			$db->query($sql);

			for($i=0, $j=$j; $i < $db->total; $i++, $j++){
					$db->fetch($i);

					echo "parent.document.forms['$form'].elements['".$target."'].options[".($j+1)."].text = '".$db->dt[name]."'; \n";
					echo "parent.document.forms['$form'].elements['".$target."'].options[".($j+1)."].value = '".$db->dt[code]."'; \n";
					echo "parent.document.forms['$form'].elements['".$target."'].options[".($j+1)."].selected = false; \n";
					echo "parent.document.forms['$form'].elements['".$target."'].options[".($i+1)."].setAttribute('ps_name','".$db->dt[ps_name]."') ; \n";
				//echo "alert(parent.document.forms['$form'].elements['".$target."'].options[".($i+1)."].setAttribute('ps_name'));";

			}




	}else{
				//if($target == "cid0_1" || $target == "cid1_1" || $target == "cid2_1" || $target == "cid3_1"){
		  //  echo "parent.document.forms['$form'].elements['cid'].value = '".$cid."'; \n";
		  //}
			//echo "parent.document.forms['$form'].elements['".$target."'].value = '".$cid."'; \n";
			echo "parent.document.forms['$form'].elements['".$target."'].length = 1; \n";
			//echo "parent.document.forms['$form'].elements['".$target."'].validation = 'false'; \n";

	}

}else{
	//$db->query("SELECT cid, cname FROM shop_category_info where depth ='".($depth+1)."' and cid = '' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ");
	echo "parent.document.forms['$form'].elements['".$target."'].length = 1; \n";
	//exit;
}

echo "</script> \n";




?>