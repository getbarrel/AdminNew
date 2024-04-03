<?
include("../class/layout.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");

$db = new Database;
// 조건절 셋팅

if($search_searialize_value!="") {
	//	echo $search_searialize_value;
	$unserialize_search_value = unserialize(urldecode($search_searialize_value));
	//print_r ($unserialize_search_value);
	//exit;
	extract($unserialize_search_value);
}

if ($FromYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", $before10day);
	$eDate = date("Y/m/d");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;
}

switch ($depth){
	case 0:
		$cut_num = 3;
		break;
	case 1:
		$cut_num = 6;
		break;
	case 2:
		$cut_num = 9;
		break;
	case 3:
		$cut_num = 9;
		break;
	default :
		$cut_num = 3;
		break;
}


if($admininfo[admin_level] == 9){
	$where = "where od.product_type = '5' ";
}else{
	$where = "where od.product_type = '5' and od.company_id ='".$admininfo[company_id]."'  ";
}
if($date_type){
	if ($FromYY != "")	$where .= "and date_format(".$date_type.",'%Y%m%d') between $startDate and $endDate ";
}

if(is_array($type)){
	for($i=0;$i < count($type);$i++){


		if($type[$i]){
			if($type_str == ""){
				$type_str .= "'".$type[$i]."'";
			}else{
				$type_str .= ", '".$type[$i]."' ";
			}
		}
	}

	if($type_str != ""){
		$where .= "and od.status in ($type_str) ";
	}
}else{
	if($type){
		$where .= "and od.status = '$type' ";
	}

}
//echo $where;
if($search_type && $search_text){
	if($search_type == "combi_name"){
		$where .= "and (o.bname LIKE '%".trim($search_text)."%'  or o.rname LIKE '%".trim($search_text)."%') ";
	}else{
		$where .= "and $search_type LIKE '%".trim($search_text)."%' ";
	}
}

if($dispathpoint!="") {
	$where.=" AND od.dispathpoint='".$dispathpoint."'";
}

if($company_id != ""){
	$where .= " and od.company_id = '".$company_id."'";
}

//echo $where;exit;

//************SMS 발송 *************
if ($update_kind == "sms"){


	$cominfo = getcominfo();
	$sdb = new Database;
	$s = new SMS();
	$s->send_phone = $send_phone;
	$s->send_name = $cominfo[com_name];
	$s->admin_mode = true;
	$s->send_type = $send_type;
	//$s->send_date = substr($sFromYY,2,2).$sFromMM.$sFromDD;
	//$s->send_time = $sDateTime;
	$s->send_date = substr(date("Y"),2,2).date("m").date("d");
	$s->send_time = time();


	if($update_type == 2){// 선택회원일때
		for($i=0; $i <count($ci_ix);$i++){
				$sql = "SELECT ci.coupon_no,o.bmobile, o.bname, od.pname
				FROM ".TBL_SNS_COUPON_INFO." ci
				LEFT JOIN ".TBL_SNS_ORDER_DETAIL." od on ci.od_ix = od.od_ix
				LEFT JOIN ".TBL_SNS_ORDER." o on ci.oid = o.oid
				LEFT JOIN ".TBL_SNS_MEMBER." m on ci.code = m.code
				LEFT JOIN ".TBL_SNS_GROUPINFO." mg on m.gp_ix = mg.gp_ix
				$where AND ci.ci_ix='".$ci_ix[$i]."' ";
			//echo $sql;
				$db->query($sql);
				$db->fetch();

				$mc_sms_text = str_replace("{name}",$db->dt["bname"],$sms_text);
				$mc_sms_text = str_replace("{publish_coupon_name_tt}",cut_str($db->dt["pname"],18),$mc_sms_text);
				$mc_sms_text = str_replace("{coupon_number_txt}",$db->dt["coupon_no"],$mc_sms_text);

				//echo $db->dt["bname"]."<br />".$db->dt["pname"]."<br />".$db->dt["bmobile"]."<br />".$db->dt["bname"];
				//echo strlen($mc_sms_text);
				//echo $mc_sms_text;
				//exit;
				$s->dest_phone = str_replace("-","",$db->dt["bmobile"]);
				$s->dest_name = $db->dt["bname"];
				$s->msg_body =$mc_sms_text;



				$s->sendbyone($admininfo);

		}
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택회원에게 SMS 가 정상적으로 발송되었습니다');</script>");
	}else{// 검색회원일때
			if(!$sms_max){
				$sms_max = 100;
			}
			if ($sms_send_page == ''){
				$start = 0;
				$sms_send_page  = 1;
			}else{
				$start = ($sms_send_page - 1) * $sms_max;
			}
			//$db->query("INSERT INTO ".TBL_SHOP_RESERVE_INFO." (id,uid,oid,pid,ptprice,payprice,reserve,state,etc,regdate) VALUES ('','$uid','','','','','$reserve','$state','$etc',NOW())");
			/*$sql = "SELECT count(ci.ci_ix)
			FROM ".TBL_SNS_COUPON_INFO." ci
			LEFT JOIN ".TBL_SNS_ORDER_DETAIL." od on ci.od_ix = od.od_ix
			LEFT JOIN ".TBL_SNS_ORDER." o on ci.oid = o.oid
			LEFT JOIN ".TBL_SNS_MEMBER." m on ci.code = m.code
			LEFT JOIN ".TBL_SNS_GROUPINFO." mg on m.gp_ix = mg.gp_ix
			$where ";
			echo $sql;exit;
			$db->query($sql);
			$db->fetch();
			$total = $db->total;*/


			$sql = "SELECT ci.coupon_no,o.bmobile, o.bname, od.pname
			FROM ".TBL_SNS_COUPON_INFO." ci
			LEFT JOIN ".TBL_SNS_ORDER_DETAIL." od on ci.od_ix = od.od_ix
			LEFT JOIN ".TBL_SNS_ORDER." o on ci.oid = o.oid
			LEFT JOIN ".TBL_SNS_MEMBER." m on ci.code = m.code
			LEFT JOIN ".TBL_SNS_GROUPINFO." mg on m.gp_ix = mg.gp_ix
			$where ";
			//echo $sql;
			$db->query($sql);
			$total = $db->total;


			for($i=0;$i < $db->total;$i++){
				$db->fetch($i);

				$mc_sms_text = str_replace("{name}",$db->dt["bname"],$sms_text);
				$mc_sms_text = str_replace("{publish_coupon_name_tt}",cut_str($db->dt["pname"],18),$mc_sms_text);
				$mc_sms_text = str_replace("{coupon_number_txt}",$db->dt["coupon_no"],$mc_sms_text);

				//echo $mail_info[mem_name]."::::".$mail_info[mem_mail].":::	".$mail_info[mem_id];
				$s->dest_phone = str_replace("-","",$db->dt["bmobile"]);
				$s->dest_name = $db->dt["bname"];
				$s->msg_body =$mc_sms_text;

				$s->sendbyone($admininfo);

			}

			if($total > ($start+$sms_max)){
				echo("<script>
				parent.document.getElementById('sended_sms_cnt').innerHTML = '".($start+$sms_max)."';
				parent.document.getElementById('remainder_sms_cnt').innerHTML = '".($total-($start+$sms_max))."';
				if(!parent.document.forms['list_frm'].stop.checked){
					parent.document.forms['list_frm'].sms_send_page.value = ".($sms_send_page+1).";
					parent.document.forms['list_frm'].submit();
				}
				</script>");
			}else{
				echo("<script language='javascript' src='../_language/language.php'></script><script>
				parent.document.getElementById('sended_sms_cnt').innerHTML = '".($total)."';
				parent.document.getElementById('remainder_sms_cnt').innerHTML = '0';
				alert('".$total." '+language_data['sns_coupon_orders_list.act.php']['A'][language]);//건의 SMS 가 정상적으로 발송되었습니다 
				</script>");
			}
	}
	//echo("<script>top.location.href = 'baymoney.pop.php?ab_ix=$uid';</script>");

}
?>