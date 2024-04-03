<?
include("../../class/database.class");
session_start();
$team = $_GET['trigger'];
$depth = $_GET['depth'];
$target = $_GET['target'];
$form = $_GET['form'];

//header("Content-Type: application/x-javascript");

$db = new Database;

if($team){

	$sql = 	"SELECT cmd.code, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
			FROM common_user cu, common_member_detail cmd
			where cu.code = cmd.code and cu.mem_type = 'MD' and cmd.team = '$team'
			order by cmd.name asc ";

	//echo $sql;
	$db->query($sql);
}else{

	echo "<script type='text/javascript'>
		parent.document.forms['$form'].elements['".$target."'].length = 1;
	</script>";
	exit;
}

if ($db->total){

			//if($target == "team0_1" || $target == "team1_1" || $target == "team2_1" || $target == "team3_1"){

      //}
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
			
		echo "<script type='text/javascript'>
			parent.document.forms['$form'].elements['".$target."'].length = 1;;
		</script>";
		exit;
        //echo "document.forms['$form'].elements['".$target."'].validation = 'false'; \n";

}





?>