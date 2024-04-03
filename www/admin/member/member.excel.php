<?
///////////////// CREATE EXCEL FILE METHOD /////////////////
function xlsBOF() {
echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
return;
}
function xlsEOF() {
echo pack("ss", 0x0A, 0x00);
return;
}
function xlsWriteNumber($Row, $Col, $Value) {
echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
echo pack("d", $Value);
return;
}
function xlsWriteLabel($Row, $Col, $Value, $lang='' ) {
$lang = ($lang)? $lang:'euc-kr';
$Value = mb_convert_encoding($Value,$lang,"utf-8");
$L = strlen($Value);
echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
echo $Value;
return;
}
///////////////////// END //////////////////////////
include("../class/layout.class");

header('Pragma: public');
header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');     // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0');    // HTTP/1.1
header('Content-Type: text/plain; charset=UTF-8');
header('Content-Transfer-Encoding: binary');
header('Content-Type: application/vnd.ms-excel;');                 // This should work for IE & Opera
header("Content-type: application/x-msexcel");                    // This should work for the rest
header('Content-Disposition: attachment; filename='.iconv("utf-8","CP949","회원리스트").'.xls');

$db = new Database;
$mdb = new Database;
$where = " where cu.code = cmd.code and cmd.gp_ix = mg.gp_ix and gp_level != 0 ";
	if($region != ""){
		$where .= " and addr1 LIKE  '%".$region."%' ";
	}

	/*if($mem_level != ""){
		$where .= " and mg.gp_level = '".$mem_level."' ";
	}*/

	if($gp_ix != ""){
		$where .= " and mg.gp_ix = '".$gp_ix."' ";
	}



	$birthday = $birYY.$birMM.$birDD;
	$birthday2 = substr($birYY,2,2).$birMM.$birDD;



	if($sex == "M" || $sex == "W"){
		$where .= " and sex_div =  '$sex' ";
	}

	if($mailsend_yn != "A" && $mailsend_yn != ""){
		$where .= " and info =  '$mailsend_yn' ";
	}

	if($smssend_yn != "A" && $smssend_yn != ""){
		$where .= " and sms =  '$smssend_yn' ";
	}


	if($search_type != "" && $search_text != ""){

		$where .= " and $search_type LIKE  '%$search_text%' ";

	}

	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;

	if($startDate != "" && $endDate != ""){
		$where .= " and  MID(replace(cu.date,'-',''),1,8) between  $startDate and $endDate ";
	}

	$vstartDate = $vFromYY.$vFromMM.$vFromDD;
	$vendDate = $vToYY.$vToMM.$vToDD;

	if($vstartDate != "" && $vendDate != ""){
		$where .= " and  MID(replace(cu.last,'-',''),1,8) between  $vstartDate and $vendDate ";
	}



	$sql = "select cmd.code, cu.id, cmd.name, cmd.mail, cu.visit, date_format(cu.date,'%Y.%m.%d') as regdate, mg.gp_name,  UNIX_TIMESTAMP(cu.last) AS last, cmd.pcs
	from ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd ,".TBL_SHOP_GROUPINFO." mg $where  ORDER BY cmd.date DESC "; //kbk

	//echo $sql;
	$db->query($sql);
xlsBOF();

xlsWriteLabel(0,0,"번호");
xlsWriteLabel(0,1,"그룹");
xlsWriteLabel(0,2,"이름");
xlsWriteLabel(0,3,"아이디");
//xlsWriteLabel(0,3,"권한");
xlsWriteLabel(0,4,"이메일");
xlsWriteLabel(0,5,"등록일");
xlsWriteLabel(0,6,"로긴수");
xlsWriteLabel(0,7,"적립금");
xlsWriteLabel(0,8,"핸드폰");

//------ row 1 data ------
for($i=0;$i<$db->total;$i++){
	$db->fetch($i);
	$mdb->query("SELECT sum(reserve) as reserve_sum FROM ".TBL_SHOP_RESERVE_INFO." WHERE uid = '".$db->dt[code]."' and state in (1,2,5,6,7)");
	$mdb->fetch(0);

	xlsWriteLabel(($i+1), 0, ($i+1));
	xlsWriteLabel(($i+1), 1, $db->dt[gp_name]);
	xlsWriteLabel(($i+1), 2, $db->dt[name]);
	xlsWriteLabel(($i+1), 3, $db->dt[id]);
	//xlsWriteLabel(($i+1), 3, $perm);
	xlsWriteLabel(($i+1), 4, $db->dt[mail]);
	xlsWriteLabel(($i+1), 5, $db->dt[regdate]);
	xlsWriteLabel(($i+1), 6, $db->dt[visit]);
	xlsWriteLabel(($i+1), 7, ($mdb->dt[reserve_sum] == "" ? "-":$mdb->dt[reserve_sum]));
	xlsWriteLabel(($i+1), 8, $db->dt[pcs]);
}


xlsEOF();

?>




