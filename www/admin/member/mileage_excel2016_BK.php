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
   
set_time_limit(9999999999);
ini_set('memory_limit',-1);

if($info_type == "list" or $info_type == ""){
	$mileage_table = "shop_mileage_log";
	$mileage_ix = "ml_ix";
	$mileage_data = "ml_mileage";
	$mileage_state = "ml_state";
    $log_ix = "ml_ix";
    $log_where = "";
    $mileage_type = "";

}else if($info_type == "add"){
	$mileage_table = "shop_add_mileage";
	$mileage_ix = "am_ix";
	$mileage_data = "am_mileage";
	$mileage_state = "am_state";
    $log_ix = "type_ix";
    $log_where = " and log_type = 'add' ";
    $mileage_type = "add_type";

}else if($info_type == "use"){
	$mileage_table = "shop_use_mileage";
	$mileage_ix = "um_ix";
	$mileage_data = "um_mileage";
	$mileage_state = "um_state";
    $log_ix = "type_ix";
    $log_where = " and log_type = 'use' ";
    $mileage_type = "use_type";

}


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
	$where = " where cmd.code = ri.uid_ and cmd.code = cu.code ";
}else{
	$where = " where  cmd.code = ri.uid and cu.code = cmd.code ";
}

if($mmode == "personalization"){
	$where .= " and ri.uid = '".$mem_ix."' ";
}

if($gp_ix != ""){
	$where .= " and cmd.gp_ix = '".$gp_ix."' ";
}

if($mem_type !=""){
	$where .= " and cu.mem_type = '".$mem_type."' ";
}

if($nationality !=""){
	$where .= " and cmd.nationality = '".$nationality."' ";
}

if(is_array($mileage_info_type)){
    for($i=0;$i < count($mileage_info_type);$i++){
        if($mileage_info_type[$i] != ""){
            if($mileage_info_type_str == ""){
                $mileage_info_type_str .= "'".$mileage_info_type[$i]."'";
            }else{
                $mileage_info_type_str .= ", '".$mileage_info_type[$i]."' ";
            }
        }
    }

    if($mileage_info_type_str != ""){
        $where .= "and ri.".$mileage_type." in ($mileage_info_type_str) ";
    }
}else{
    if($mileage_info_type){
        $where .= "and ri.".$mileage_type." = '$mileage_info_type' ";
    }
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

if($mall_ix){
    $where .=" and cu.mall_ix = '".$mall_ix."' ";
}

$startDate = $sdate;
$endDate = $edate;

if($regdate == '1'){	//신청일
	if($startDate != "" && $endDate != ""){
		if($db->dbms_type == "oracle"){
			$where .= " and  to_char(ri.regdate , 'YYYY-MM-DD') between '".$startDate."' and '".$endDate."' ";
			$count_where .= " and  to_char(ri.regdate , 'YYYY-MM-DD') between '".$startDate."' and '".$endDate."' ";
		}else{
			$where .= " and date_format(ri.regdate,'%Y-%m-%d') between  '".$startDate."' and '".$endDate."' ";
			$count_where .= " and date_format(ri.regdate,'%Y-%m-%d') between  '".$startDate."' and '".$endDate."' ";
		}
	}
}
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
				cu.id as member_id,
				AES_DECRYPT(IFNULL(cmd.name,'-'),'".$db->ase_encrypt_key."') as name
			from 
				$mileage_table as ri 
				left join ".TBL_COMMON_USER." as cu on (ri.uid = cu.code)
				left join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)

			$where 
				order by ri.regdate desc ";

}else{

	$sql = "select 
				ri.*,
				ri.message,
				ri.".$mileage_ix." as mileage_ix,
				ri.".$mileage_data." as mileage,
				ri.".$mileage_state." as state,
				cmd.code,
				cmd.gp_ix,
				cmd.level_ix,
				cu.id as member_id,
				AES_DECRYPT(UNHEX(IFNULL(cmd.name,'-')),'".$db->ase_encrypt_key."') as name 
			from 
				$mileage_table as ri 
				left join ".TBL_COMMON_USER." as cu on (ri.uid = cu.code)
				left join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)

			$where
				order by ri.".$mileage_ix." desc ";

}
	$db->query($sql);

	$memberXL = new PHPExcel();

		// 속성 정의
	$memberXL->getProperties()->setCreator("포비즈 코리아")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle("Reserve List")
								 ->setSubject("Reserve List")
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory("Reserve List");

	// 데이터 등록
	$memberXL->getActiveSheet(0)->setCellValue('A' . 1, "적립/사용일자");
	$memberXL->getActiveSheet(0)->setCellValue('B' . 1, "아이디");
	$memberXL->getActiveSheet(0)->setCellValue('C' . 1, "회원명");
	$memberXL->getActiveSheet(0)->setCellValue('D' . 1, "적립내용");
    $memberXL->getActiveSheet(0)->setCellValue('E' . 1, "마일리지");
    $memberXL->getActiveSheet(0)->setCellValue('F' . 1, "잔여마일리지");
	$memberXL->getActiveSheet(0)->setCellValue('G' . 1, "적립상태");
	$memberXL->getActiveSheet(0)->setCellValue('H' . 1, "회원그룹");
	$memberXL->getActiveSheet(0)->setCellValue('I' . 1, "주문번호");


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

		if($info_type == 'list' || $info_type == ''){
			if($db->dt[state] !='2'){	//적립상태,사용구분 선택후 수정가능 부분
				$mstate = '적립완료(+)';
			}else {
				$mstate = '사용(-)';
			}	
		}else if($info_type == 'add'){
			$mstate = '적립완료(+)';
		}else if($info_type == 'use'){
			$mstate = '사용(-)';
		}
		
		if($db->dt[gp_ix]){
			$sql = "select gp_name from shop_groupinfo where gp_ix = '".$db->dt[gp_ix]."'";
			$mdb->query($sql);
			$mdb->fetch();

			$gp_name = $mdb->dt[gp_name];
		}

        $sql = "select total_mileage from shop_mileage_log where ".$log_ix." = '".$db->dt['mileage_ix']."' $log_where ";
        $mdb->query($sql);
        $mdb->fetch();

        $total_mileage = $mdb->dt[total_mileage];

		$memberXL->getActiveSheet()->setCellValue('A' . ($i + 2), $db->dt[regdate]);
		$memberXL->getActiveSheet()->setCellValue('B' . ($i + 2), $db->dt[member_id]);
		$memberXL->getActiveSheet()->setCellValue('C' . ($i + 2), $db->dt[name]);
		$memberXL->getActiveSheet()->setCellValue('D' . ($i + 2), $db->dt[message]);
        $memberXL->getActiveSheet()->setCellValue('E' . ($i + 2), $db->dt[mileage]);
        $memberXL->getActiveSheet()->setCellValue('F' . ($i + 2), $total_mileage);
		$memberXL->getActiveSheet()->setCellValue('G' . ($i + 2), $mstate);
		$memberXL->getActiveSheet()->setCellValue('H' . ($i + 2), $gp_name);
		$memberXL->getActiveSheet()->setCellValue('I' . ($i + 2), $db->dt[oid]);
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
    $memberXL->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);


	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="mileage_list_'.date('Ymd').'.xls"');
	header('Cache-Control: max-age=0');


	$objWriter = PHPExcel_IOFactory::createWriter($memberXL, 'Excel5');
	$objWriter->save('php://output');

	exit;