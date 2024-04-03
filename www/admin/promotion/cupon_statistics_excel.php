<?php

include("../class/layout.class");
include '../include/phpexcel/Classes/PHPExcel.php';
//error_reporting(E_ALL);
PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

date_default_timezone_set('Asia/Seoul');


$db = new Database;
$odb = new Database;

if ($admininfo[mall_type] == "O"){
    if($db->dbms_type == "oracle"){
        $where = " where cu.code = cmd.code and cmd.gp_ix = mg.gp_ix and gp_level != 0  ";// and cu.mem_type in ('M','C','F','S')
    }else{
        $where = " where cu.code != '' and cu.code = cmd.code and cmd.gp_ix = mg.gp_ix and gp_level != 0   ";//and cu.mem_type in ('M','C','F','S')
    }
}else{
    if($db->dbms_type == "oracle"){
        $where = " where cu.code = cmd.code and cmd.gp_ix = mg.gp_ix and gp_level != 0  "; //and cu.mem_type in ('M','C','F')
    }else{
        $where = " where cu.code != '' and cu.code = cmd.code and cmd.gp_ix = mg.gp_ix and gp_level != 0  ";//and cu.mem_type in ('M','C','F')
    }
}

if($db->dbms_type == "oracle"){
    if($search_type != "" && $search_text != ""){
        if($search_type == "jumin"){
            $search_text = substr($search_text,0,6)."-".md5(substr($search_text,6,7));
            $where .= " and jumin = '$search_text' ";
        }else if($search_type=="name" || $search_type=="mail"  || $search_type == "pcs" || $search_type == "tel" ) {
            $where .= " and AES_DECRYPT(".$search_type.") LIKE '%".$search_text."%' ";
        } else {
            $where .= " and ".$search_type." LIKE '%$search_text%' ";
        }
    }
}else{
    if($multi_search == 1){
        $search_array = explode("\r\n",$search_texts);
        if($search_type == "jumin"){
            $search_text = substr($search_text,0,6)."-".md5(substr($search_text,6,7));
            $where .= " and jumin = '$search_text' ";
        }else if($search_type=="name" || $search_type=="mail"  || $search_type == "pcs" || $search_type == "tel" ) {
            //$where .= " and AES_DECRYPT(UNHEX(".$search_type."),'".$db->ase_encrypt_key."') LIKE '%".$search_text."%' ";
            $where .= " and AES_DECRYPT(UNHEX(".$search_type."),'".$db->ase_encrypt_key."') in ('".implode("','",$search_array)."') ";
        } else {
            $where .= " and ".$search_type." in ('".implode("','",$search_array)."') ";
        }


    }else{
        if($search_type != "" && $search_text != ""){
            if($search_type == "jumin"){
                $search_text = substr($search_text,0,6)."-".md5(substr($search_text,6,7));
                $where .= " and jumin = '$search_text' ";
            }else if($search_type=="name" || $search_type=="mail"  || $search_type == "pcs" || $search_type == "tel" ) {
                $where .= " and AES_DECRYPT(UNHEX(".$search_type."),'".$db->ase_encrypt_key."') LIKE '%".$search_text."%' ";
            } else {
                $where .= " and ".$search_type." LIKE '%$search_text%' ";
            }
        }
    }
}

if($gp_ix != ""){
    $where .= " and cmd.gp_ix = '".$gp_ix."' ";
}

if($mmode == "personalization"){
    $where .= " and cmd.code = '".$mem_ix."' ";
}

//$startDate = $FromYY.$FromMM.$FromDD;
//$endDate = $ToYY.$ToMM.$ToDD;

if($startDate != "" && $endDate != ""){
    if($publish_div == "2"){
        $where .= " and  date_format(cu.date,'%Y-%m-%d') between  '$startDate' and '$endDate' ";
    }else if($publish_div == "3"){
        $where .= " and  date_format(cmd.recent_order_date,'%Y-%m-%d') between  '$startDate' and '$endDate' ";
    }else if($publish_div == "4"){
        $where .= " and  date_format(cu.last,'%Y-%m-%d') between  '$startDate' and '$endDate' ";
    }
}

if($db->dbms_type == "oracle"){
    //and mem_type in ('M','C','F','S') ,AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name
    $sql = "select cr.regist_ix,  cp.use_date_type,cr.mem_ix, cu.id, cr.regdate, AES_DECRYPT(cmd.mail,'".$db->ase_encrypt_key."') as mail, cr.use_yn ,cr.use_sdate, cr.use_date_limit, 
			case when (cr.use_sdate <= ".date("Ymd")." and ".date("Ymd")." <= cr.use_date_limit) then 1 else 0 end use_priod_yn
			from ".TBL_SHOP_CUPON_PUBLISH." cp, shop_cupon_regist cr, ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_SHOP_GROUPINFO." mg
			".$where." and cp.publish_ix = '".$publish_ix."' AND cp.publish_ix = cr.publish_ix and cr.mem_ix = cu.code and cr.use_yn != '0' 
			 ";
    //echo $sql ;
}else{
    //and mem_type in ('M','C','F','S') AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
    $sql = "select cr.regist_ix, cp.use_date_type,cr.mem_ix, cu.id, cr.regdate, AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail, cr.use_yn ,cr.use_sdate, cr.use_date_limit, cr.use_oid, cr.use_pid,
			case when (cr.use_sdate <= ".date("Ymd")." and ".date("Ymd")." <= cr.use_date_limit) then 1 else 0 end use_priod_yn,odd.od_ix,odd.dc_price
			from 
			    ".TBL_SHOP_CUPON_PUBLISH." cp
			    left join
			    shop_cupon_regist cr on cp.publish_ix = cr.publish_ix
			    left join
			    ".TBL_COMMON_USER." cu  on cr.mem_ix = cu.code
			    left join
			    ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code
			     left join
			    ".TBL_SHOP_GROUPINFO." mg on cmd.gp_ix = mg.gp_ix
			left join
			    shop_order_detail_discount odd ON cr.regist_ix = odd.dc_ix and cr.use_oid = odd.oid
			left join
			    shop_order_detail od on odd.od_ix = od.od_ix
			".$where." and cp.publish_ix = '".$publish_ix."'  and cr.use_yn != '0' and odd.dc_type = 'CP'
			and od.status not in ('SR')
			order by name
			 ";
}

//echo nl2br($sql);
//echo "<br>".$where;

$db->query($sql);

$productListXL = new PHPExcel();

// 속성 정의
$productListXL->getProperties()->setCreator("포비즈 코리아")
    ->setLastModifiedBy("Mallstory.com")
    ->setTitle("Product List")
    ->setSubject("Product List")
    ->setDescription("generated by forbiz korea")
    ->setKeywords("mallstory")
    ->setCategory("Product List");



// 데이터 등록
//$productListXL->getActiveSheet(0)->setCellValue('A' . 1, "이름");
$productListXL->getActiveSheet(0)->setCellValue('A' . 1, "아이디");
$productListXL->getActiveSheet(0)->setCellValue('B' . 1, "주문번호");
$productListXL->getActiveSheet(0)->setCellValue('C' . 1, "상품명");
$productListXL->getActiveSheet(0)->setCellValue('D' . 1, "상품코드");
$productListXL->getActiveSheet(0)->setCellValue('E' . 1, "옵션명");
$productListXL->getActiveSheet(0)->setCellValue('F' . 1, "수량");
$productListXL->getActiveSheet(0)->setCellValue('G' . 1, "상품금액");
$productListXL->getActiveSheet(0)->setCellValue('H' . 1, "할인금액");
$productListXL->getActiveSheet(0)->setCellValue('I' . 1, "업체명");
//------ row 1 data ------
for($i=0;$i<$db->total;$i++){
    $db->fetch($i);

    $sql = "select od.dcprice, od.oid,od.pname, od.option_text, od.pcode, od.gid, od.company_name, od.pcnt from ".TBL_SHOP_ORDER_DETAIL." od where od.oid = '".$db->dt[use_oid]."' and od.od_ix = '".$db->dt[od_ix]."' ";
    $odb->query($sql);
    $odb->fetch();



    //$productListXL->getActiveSheet()->setCellValue('A' . ($i + 2), $db->dt[name]);
    $productListXL->getActiveSheet()->setCellValue('A' . ($i + 2), $db->dt[id]);
    $productListXL->getActiveSheet()->setCellValue('B' . ($i + 2), $db->dt[use_oid]);
    $productListXL->getActiveSheet()->setCellValue('C' . ($i + 2), $odb->dt[pname]);
    $productListXL->getActiveSheet()->setCellValue('D' . ($i + 2), $odb->dt[gid]);
    $productListXL->getActiveSheet()->setCellValue('E' . ($i + 2), $odb->dt[option_text]);
    $productListXL->getActiveSheet()->setCellValue('F' . ($i + 2), $odb->dt[pcnt]);
    $productListXL->getActiveSheet()->setCellValue('G' . ($i + 2), $odb->dt[dcprice]);
    $productListXL->getActiveSheet()->setCellValue('H' . ($i + 2), $db->dt[dc_price]);
    $productListXL->getActiveSheet()->setCellValue('I' . ($i + 2), $odb->dt[company_name]);
}

// 시트이름등록
$productListXL->getActiveSheet()->setTitle('상품리스트');

// 첫번째 시트 선택
$productListXL->setActiveSheetIndex(0);
//$productListXL->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$productListXL->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$productListXL->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$productListXL->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$productListXL->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$productListXL->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$productListXL->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$productListXL->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$productListXL->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="cupon_statistics.xls"');
header('Cache-Control: max-age=0');


$objWriter = PHPExcel_IOFactory::createWriter($productListXL, 'Excel5');
$objWriter->save('php://output');

exit;