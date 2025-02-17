<?

include("../class/layout.class");
include '../include/phpexcel/Classes/PHPExcel.php';
set_time_limit(0);
//error_reporting(E_ALL);
PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

date_default_timezone_set('Asia/Seoul');

$db = new Database;
$mdb = new Database;
/*
	if ($admininfo[mall_type] == "O"){
		if($db->dbms_type == "oracle"){
			$where = " where cu.code = cmd.code and cmd.gp_ix = mg.gp_ix and gp_level != 0 and cu.mem_type in ('M','C','F','S') ";
		}else{
			$where = " where cu.code != '' and cu.code = cmd.code and cmd.gp_ix = mg.gp_ix and gp_level != 0 and cu.mem_type in ('M','C','F','S') ";
		}
	}else{
		if($db->dbms_type == "oracle"){
			$where = " where cu.code = cmd.code and cmd.gp_ix = mg.gp_ix and gp_level != 0 and cu.mem_type in ('M','C','F') ";
		}else{
			$where = " where cu.code != '' and cu.code = cmd.code and cmd.gp_ix = mg.gp_ix and gp_level != 0 and cu.mem_type in ('M','C','F') ";
		}
	}

    if($page_type=="member_vip_list"){
        $where .= " and cmd.level_ix  in ('4','5','6')";
    }else if($page_type=="member_black_list"){
        $where .= " and cmd.level_ix in ('7','8','9')  ";
    }

	if($region != ""){
		$where .= " and addr1 LIKE  '%".$region."%' ";
	}

	if($gp_level != ""){
		$where .= " and mg.gp_level = '".$gp_level."' ";
	}

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

	if($db->dbms_type == "oracle"){
		if($search_type != "" && $search_text != ""){
			if($search_type == "name" || $search_type == "mail" || $search_type == "pcs" || $search_type == "tel" ){
				$where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
			}else{
				$where .= " and $search_type LIKE  '%$search_text%' ";
			}
		}
	}else{
		if($search_type != "" && $search_text != ""){
			if($search_type == "name" || $search_type == "mail" || $search_type == "pcs" || $search_type == "tel" ){
				$where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
			}else{
				$where .= " and $search_type LIKE  '%$search_text%' ";
			}
		}
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

	if($db->dbms_type == "oracle"){
		$sql = "select cmd.code, cu.id, AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name, AES_DECRYPT(cmd.mail,'".$db->ase_encrypt_key."') as mail,
			cu.visit, date_format(cmd.date_,'%Y.%m.%d') as regdate, mg.gp_name,  last, AES_DECRYPT(cmd.pcs,'".$db->ase_encrypt_key."') as pcs,
			AES_DECRYPT(UNHEX(cmd.zip),'".$db->ase_encrypt_key."') as zip,AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."') as addr1,AES_DECRYPT(UNHEX(cmd.addr2),'".$db->ase_encrypt_key."') as addr2
			from common_user cu, common_member_detail cmd , ".TBL_SHOP_GROUPINFO." mg $where  ORDER BY cmd.date_ DESC "; //kbk
	}else{
		$sql = "select cmd.code, cu.id, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
			cu.visit, date_format(cmd.date,'%Y.%m.%d') as regdate, mg.gp_name,  UNIX_TIMESTAMP(last) AS last, AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs,
			AES_DECRYPT(UNHEX(cmd.zip),'".$db->ase_encrypt_key."') as zip,AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."') as addr1,AES_DECRYPT(UNHEX(cmd.addr2),'".$db->ase_encrypt_key."') as addr2
			from common_user cu, common_member_detail cmd , ".TBL_SHOP_GROUPINFO." mg $where  ORDER BY cmd.date DESC "; //kbk
	}
	//echo $sql;
	$db->query($sql);
*/
//if($mode == "search" || $mode == "excel"){
include "member_query.php";
//  }

$memberXL = new PHPExcel();

// 속성 정의
$memberXL->getProperties()->setCreator("포비즈 코리아")
    ->setLastModifiedBy("Mallstory.com")
    ->setTitle("Member List")
    ->setSubject("Member List")
    ->setDescription("generated by forbiz korea")
    ->setKeywords("mallstory")
    ->setCategory("Member List");

// 데이터 등록
$memberXL->getActiveSheet(0)->setCellValue('A' . 1, "번호");
$memberXL->getActiveSheet(0)->setCellValue('B' . 1, "그룹");
$memberXL->getActiveSheet(0)->setCellValue('C' . 1, "국내/해외");
$memberXL->getActiveSheet(0)->setCellValue('D' . 1, "회원구분");
$memberXL->getActiveSheet(0)->setCellValue('E' . 1, "이름");
$memberXL->getActiveSheet(0)->setCellValue('F' . 1, "아이디");
$memberXL->getActiveSheet(0)->setCellValue('G' . 1, "미이용일");
$memberXL->getActiveSheet(0)->setCellValue('H' . 1, "상태변경");
$memberXL->getActiveSheet(0)->setCellValue('I' . 1, "변경일");
$memberXL->getActiveSheet(0)->setCellValue('J' . 1, "관리자");
$memberXL->getActiveSheet(0)->setCellValue('K' . 1, "최근이용일");
$memberXL->getActiveSheet(0)->setCellValue('L' . 1, "회원가입일");
$memberXL->getActiveSheet(0)->setCellValue('M' . 1, "로긴수");
$memberXL->getActiveSheet(0)->setCellValue('N' . 1, "적립금");
$memberXL->getActiveSheet(0)->setCellValue('O' . 1, "핸드폰");


for($i=0;$i<$db->total;$i++){
    $db->fetch($i);

    if($db->dbms_type == "oracle"){
        $mdb->query("SELECT sum(if(state='1',reserve,-reserve)) as reserve_sum FROM ".TBL_SHOP_RESERVE." WHERE uid_ = '".$db->dt[code]."' and state in (1,2)");
    }else{
        $mdb->query("SELECT sum(if(state='1',reserve,-reserve)) as reserve_sum FROM ".TBL_SHOP_RESERVE." WHERE uid = '".$db->dt[code]."' and state in (1,2)");
    }
    $mdb->fetch(0);

    $nationality = GetDisplayDivision($db->dt['mall_ix'], "text");
    $visit_delay = intval((strtotime(date('Ymd', strtotime("+1 day")))-strtotime($db->dt[last])) / 86400); //date('d',strtotime(date('Ymd'))-$db->dt[last]);

    switch($db->dt[status]){
        case "A":
            $sleep_status = "관리자 일관변경";
            break;
        case "S":
            $sleep_status = "시스템 자동";
            break;
    }

    if($db->dt[charger_ix]){
        $charger_name = getChargerinfo($db->dt[charger_ix],'name') ."( ".getChargerinfo($db->dt[charger_ix],'id')." )";
    }else{
        $charger_name = "-";
    }

    switch($db->dt[mem_type]){

        case "M":
            $mem_type = "일반회원";
            break;
        case "C":
            $mem_type = "기업".($db->dt[com_name] != "" ? "(".$db->dt[com_name].")":"");
            break;
        case "A":
            $mem_type = "직원(관리자)";
            break;
    }

    $memberXL->getActiveSheet()->setCellValue('A' . ($i + 2), ($i + 1));
    $memberXL->getActiveSheet()->setCellValue('B' . ($i + 2), $db->dt[gp_name]);
    $memberXL->getActiveSheet()->setCellValue('C' . ($i + 2), $nationality);
    $memberXL->getActiveSheet()->setCellValue('D' . ($i + 2), $mem_type);
    $memberXL->getActiveSheet()->setCellValue('E' . ($i + 2), $db->dt[name]);
    $memberXL->getActiveSheet()->setCellValue('F' . ($i + 2), $db->dt[id]);
    $memberXL->getActiveSheet()->setCellValue('G' . ($i + 2), $visit_delay);
    $memberXL->getActiveSheet()->setCellValue('H' . ($i + 2), $sleep_status);
    $memberXL->getActiveSheet()->setCellValue('I' . ($i + 2), $db->dt[sleep_date]);
    $memberXL->getActiveSheet()->setCellValue('J' . ($i + 2), $charger_name);
    $memberXL->getActiveSheet()->setCellValue('K' . ($i + 2), date('Y.m.d',strtotime($db->dt[last])));
    $memberXL->getActiveSheet()->setCellValue('L' . ($i + 2), $db->dt[regdate]);
    $memberXL->getActiveSheet()->setCellValue('M' . ($i + 2), $db->dt[visit]);
    $memberXL->getActiveSheet()->setCellValue('N' . ($i + 2), $db->dt[mileage]);
    $memberXL->getActiveSheet()->setCellValue('O' . ($i + 2), $db->dt[pcs]);
}

// 시트이름등록
$memberXL->getActiveSheet()->setTitle('회원');

// 첫번째 시트 선택
$memberXL->setActiveSheetIndex(0);
$memberXL->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$memberXL->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$memberXL->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$memberXL->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$memberXL->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$memberXL->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$memberXL->getActiveSheet()->getColumnDimension('G')->setWidth(10);
$memberXL->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
$memberXL->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
$memberXL->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
$memberXL->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
$memberXL->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
$memberXL->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
$memberXL->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
$memberXL->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="sleep_members.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($memberXL, 'Excel5');
$objWriter->save('php://output');

exit;

function getChargerinfo($code,$type){
    $db = new MySQL;

    $sql = "select cu.id, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name from ".TBL_COMMON_USER." cu left join ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code where cu.code = '".$code."'";
    $db->query($sql);
    $db->fetch();

    if($type == 'name'){
        $data = $db->dt[name];
    }else if ($type == 'id'){
        $data = $db->dt[id];
    }
    return $data;
}
?>