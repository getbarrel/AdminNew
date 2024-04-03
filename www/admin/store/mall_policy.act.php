<?
include("../../class/database.class");

$db = new Database;

if($act_value=="mall_policy_act") {

	foreach ($pi_code as $k => $v ){
		$pi_code_v=$pi_code[$k];
		//echo $pi_code_v;
		$contents_type_v=$contents_type[$pi_code_v];
		$pi_contents_v=addslashes($pi_contents[$pi_code_v][$contents_type_v]);
		$disp_v=$disp[$pi_code_v];


		if($sql_type[$v]=="insert") {	//if($type_value=="insert") { 각 배열별  update, insert , 타입이 있는데 최상위 타입으로만 조건을 줘서 새로추가한 약관은 insert 안됨 .  그래서 각 타입별 update, insert ,로 수정했음.
		//2013-05-09 이학봉
			$sql="INSERT INTO shop_policy_info (pi_code,pi_contents,contents_type,disp,regdate) VALUES ('".$pi_code_v."','".$pi_contents_v."','".$contents_type_v."','".$disp_v."',NOW()) ";

		} else {
			$sql="UPDATE shop_policy_info SET pi_contents='".$pi_contents_v."',contents_type='".$contents_type_v."',disp='".$disp_v."',modidate=NOW() WHERE pi_code='".$pi_code_v."' ";
		}
		$db->query($sql);
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert(\"정상적으로 저장되었습니다 \");</script>");
	echo("<script>parent.document.location.reload();location.href='about:blank';</script>");
}

?>