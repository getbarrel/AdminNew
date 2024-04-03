<?php

	include("../class/layout.class");
	include '../include/phpexcel/Classes/PHPExcel.php';

	//error_reporting(E_ALL);
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

	date_default_timezone_set('Asia/Seoul');

	$db = new Database;
	$mdb = new Database;

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
    $vdate = date("Ymd", time());
    $today = date("Y/m/d", time());
    $vyesterday = date("Y/m/d", time()-84600);
    $voneweekago = date("Y/m/d", time()-84600*7);
    $vtwoweekago = date("Y/m/d", time()-84600*14);
    $vfourweekago = date("Y/m/d", time()-84600*28);
    $vyesterday = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
    $voneweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
    $v15ago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
    $vfourweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
    $vonemonthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
    $v2monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
    $v3monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));

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
if($db->dbms_type == "oracle"){
	$where = " where cmd.code = ri.uid_ and cmd.code = u.code ";
}else{
	$where = " where ri.id <> '0' and cmd.code = ri.uid and u.code = cmd.code ";
}

if($info_type == "list" or $info_type == ""){
	//$where .= " and ri.state = '".$state."' ";
}else if($info_type == "1"){
	$where .= " and ri.state = '0' ";
}else if($info_type == "2"){
	$where .= " and ri.state = '9' ";
}else if($info_type == "3"){
	$where .= " and ri.state = '1' ";
}else if($info_type == "4"){
	$where .= " and ri.state = '2' ";
}

if($state !=""){
	$where .= " and ri.state = '".$state."' ";
}

if($ust_status_add !=""){
	$where .= " and ri.use_state = '".$ust_status_add."' ";
}

if($ust_status_cancel !=""){
	$where .= " and ri.use_state = '".$ust_status_cancel."' ";
}

if($gp_ix != ""){

	$where .= " u.gp_ix = '".$gp_ix."' ";
}

if($state != ""){
	$where .= " and ri.state = '$state' ";
}

if($mem_type !=""){
	$where .= " and u.mem_type = '".$mem_type."' ";
}

if($nationality !=""){
	$where .= " and cmd.nationality = '".$nationality."' ";
}

if($db->dbms_type == "oracle"){
	if($search_type != "" && $search_text != ""){
		if($search_type == "cmd.name"){
		   $where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
		}else{
		   $where .= " and $search_type LIKE '%$search_text%' ";
		}
	}
}else{
	if($search_type != "" && $search_text != ""){
		if($search_type == "cmd.name"){
		   $where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
		}else{
		   $where .= " and $search_type LIKE '%$search_text%' ";
		}
	}
}

$startDate = $FromYY.$FromMM.$FromDD;
$endDate = $ToYY.$ToMM.$ToDD;

if($startDate != "" && $endDate != ""){
	if($db->dbms_type == "oracle"){
		$where .= " and  to_char(ri.regdate , 'YYYYMMDD') between  $startDate and $endDate ";
	}else{
		$where .= " and  ri.regdate between  '$startDate' and '$endDate' ";
	}
}


$sql = "select count(*) as total 
		from 
			shop_point as r 
				inner join shop_point_info as ri on (r.reserve_id = ri.reserve_id)
				inner join common_user as u on (r.uid = u.code)
				inner join common_member_detail as cmd on (u.code = cmd.code)
		$where ";
//echo "$sql";
$db->query($sql);
//echo $sql;
$db->fetch();
$total = $db->dt[total];

if($db->dbms_type == "oracle"){
	$sql = "select 
				ri.id,
				ri.etc,
				r.state,
				r.reserve,
				r.oid,
				r.uid_,
				r.use_state,
				cmd.code,
				cmd.gp_ix,
				cmd.level_ix,
				u.id as member_id,
				AES_DECRYPT(IFNULL(cmd.name,'-'),'".$db->ase_encrypt_key."') as name , 
				DATE_FORMAT(r.regdate, '%Y.%m.%d %H:%i:%s') as disp_regdate
			from 
				".TBL_SHOP_POINT." as r
				inner join ".TBL_SHOP_POINT_INFO." as ri on (r.reserve_id = ri.reserve_id),
				".TBL_COMMON_MEMBER_DETAIL." cmd,
				".TBL_COMMON_USER." u
			$where 
				order by r.regdate desc ";

	$db->query($sql); //where uid = '$code'
}else{
	$sql = "select 
				r.*,
				ri.etc,
				ri.id,
				cmd.code,
				cmd.gp_ix,
				cmd.level_ix,
				u.id as member_id,
				AES_DECRYPT(UNHEX(IFNULL(cmd.name,'-')),'".$db->ase_encrypt_key."') as name ,
				DATE_FORMAT(r.regdate, '%Y.%m.%d %H:%i:%s') as disp_regdate 
			from 
				".TBL_SHOP_POINT." as r
				inner join ".TBL_SHOP_POINT_INFO." as ri on (r.reserve_id = ri.reserve_id),
				".TBL_COMMON_MEMBER_DETAIL." as cmd,
				".TBL_COMMON_USER." as u
			$where
				order by r.regdate desc ";
	$db->query($sql); //where uid = '$code'
}

	$memberXL = new PHPExcel();

		// 속성 정의
	$memberXL->getProperties()->setCreator("포비즈 코리아")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle("Point List")
								 ->setSubject("Point List")
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory("Point List");

	// 데이터 등록
	$memberXL->getActiveSheet(0)->setCellValue('A' . 1, "적립/사용일자");
	$memberXL->getActiveSheet(0)->setCellValue('B' . 1, "아이디");
	$memberXL->getActiveSheet(0)->setCellValue('C' . 1, "회원명");
	$memberXL->getActiveSheet(0)->setCellValue('D' . 1, "적립내용");
	$memberXL->getActiveSheet(0)->setCellValue('E' . 1, "마일리지");
	$memberXL->getActiveSheet(0)->setCellValue('F' . 1, "적립상태");
	$memberXL->getActiveSheet(0)->setCellValue('G' . 1, "사용구분");
	$memberXL->getActiveSheet(0)->setCellValue('H' . 1, "주문번호");


	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);

		if($db->dt[state]==0){
			$mstate = "대기";
		}else if($db->dt[state]==1){
			$mstate = "완료";
		}else if($db->dt[state]==2){
			$mstate = "사용";
		}else if($db->dt[state]==5){
			$mstate = "반품";
		}else if($db->dt[state]==9){
			$mstate = "취소)";
		}else{
			$mstate = "";
		}

		switch($db->dt[use_state]){
			case '1' :
				$use_state = "상품구매";
			break;
			case '2' :
				$use_state = "주문취소";
			break;
			case '3' :
				$use_state = "주문반품";
			break;
			case '4' :
				$use_state = "마케팅";
			break;
			case '5' :
				$use_state = "기타";
			break;
			
			case '20' :
				$use_state = "상품구매사용";
			break;
			case '21' :
				$use_state = "적립소멸";
			break;
			case '22' :
				$use_state = "기타";
			break;
		
		}

		$memberXL->getActiveSheet()->setCellValue('A' . ($i + 2), $db->dt[disp_regdate]);
		$memberXL->getActiveSheet()->setCellValue('B' . ($i + 2), $db->dt[member_id]);
		$memberXL->getActiveSheet()->setCellValue('C' . ($i + 2), $db->dt[name]);
		$memberXL->getActiveSheet()->setCellValue('D' . ($i + 2), $db->dt[etc]);
		$memberXL->getActiveSheet()->setCellValue('E' . ($i + 2), $db->dt[reserve]);
		$memberXL->getActiveSheet()->setCellValue('F' . ($i + 2), $mstate);
		$memberXL->getActiveSheet()->setCellValue('G' . ($i + 2), $use_state);
		$memberXL->getActiveSheet()->setCellValue('H' . ($i + 2), $db->dt[oid]);
	}

	// 시트이름등록
	$memberXL->getActiveSheet()->setTitle('적립금 내역');

	// 첫번째 시트 선택
	$memberXL->setActiveSheetIndex(0);
	$memberXL->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$memberXL->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$memberXL->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$memberXL->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$memberXL->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$memberXL->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$memberXL->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$memberXL->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);


	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="point_list.xls"');
	header('Cache-Control: max-age=0');


	$objWriter = PHPExcel_IOFactory::createWriter($memberXL, 'Excel5');
	$objWriter->save('php://output');

	exit;