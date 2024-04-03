<?
include("../../class/database.class");

//session_start();
$ix = $_GET['trigger'];
$key = $_GET['key'];
$target = $_GET['target'];
$form = $_GET['form'];

//header("Content-Type: application/x-javascript");

$db = new Database;

if($ix){

	if($key == "com_group" and $ix !=""){
		$where = " and cmd.com_group = '$ix' ";
	}else if($key == "department"  and $ix !=""){
		$where = " and cmd.department = '$ix' ";
	}else if($key == "position" and $ix !=""){
		$where = " and cmd.position = '$ix' ";
	}else if($key == "duty" and $ix !=""){
		$where = " and cmd.duty = '$ix' ";
	}else if($key == "company_id" and $ix !=""){
		$where = " and cu.company_id = '$ix' ";
	}

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
				$where
		";
		//echo "$sql";
	$db->query($sql);

}else{

	//$db->query("SELECT cid, cname FROM shop_category_info where depth ='".($depth+1)."' and cid = '' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ");
	echo "<script type='text/javascript'>
		parent.document.forms['$form'].elements['".$target."'].length = 1;
	</script>";
	exit;
}

//echo "document.forms['$form'].elements['selected_cid'].value = '".$cid."'; \n";
//echo "document.forms['$form'].elements['selected_depth'].value = '".$depth."'; \n";
//echo "$target";
if ($db->total){

	//if($target == "cid0_1" || $target == "cid1_1" || $target == "cid2_1" || $target == "cid3_1"){

      //}
//echo "$form";

		echo "<script type='text/javascript'>
			parent.document.forms['$form'].elements['".$target."'].length = ".($db->total+1).";
			parent.document.forms['$form'].elements['".$target."'].options[0].selected = true;
		</script>\n";

        for($i=0; $i < $db->total; $i++){
                $db->fetch($i);
		
				echo "<script type='text/javascript'>
					parent.document.forms['$form'].elements['".$target."'].options[".($i+1)."].text = '".$db->dt[name]."';
					parent.document.forms['$form'].elements['".$target."'].options[".($i+1)."].value = '".$db->dt[code]."';
				</script>";
        }

		exit;

}else{
	  //if($target == "cid0_1" || $target == "cid1_1" || $target == "cid2_1" || $target == "cid3_1"){
      //  echo "document.forms['$form'].elements['cid'].value = '".$cid."'; \n";
      //}
      	//echo "document.forms['$form'].elements['".$target."'].value = '".$cid."'; \n";
		echo "<script type='text/javascript'>
			parent.document.forms['$form'].elements['".$target."'].length = 1;;
		</script>";
		exit;
        //echo "document.forms['$form'].elements['".$target."'].validation = 'false'; \n";

}

?>