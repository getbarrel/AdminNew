<?php

@include($_SERVER["DOCUMENT_ROOT"]."/logstory.config.php");
include("../class/reportpage.class");
include("../include/commerce.lib.php");

$db = new Database();
//include($_SERVER["DOCUMENT_ROOT"]."/forbiz/report/etcreferer.chart.php");

if ($search_sdate == ""){
    $before10day = mktime(0, 0, 0, date("m")  , date("d")-10, date("Y"));

    $sDate = date("Y/m/d", $before10day);
    $eDate = date("Y/m/d");

    $search_sdate = date("Ymd", $before10day);
    $search_edate = date("Ymd");
    //echo $search_edate;

}

if(empty($colum_name)){
    $colum_name = "cu.id";
}


if(empty($groupbytype)){
    $groupbytype="day";
}
if(empty($age)){
    $age[] = "";
}

if($_GET['mall_ix'] !="" ){
    if($_GET["mall_ix"] == "dcb33fdbf7c6f40e334a43ce42194637"){
        $where .="and od.buyer_type = '1' ";
    }else if($_GET["mall_ix"] == "dcb33fdbf7c6f40e334a43ce42194638"){
        $where .="and od.buyer_type = '2' ";
    }
}

if($_GET['seller_type'] !="" ){
    $where .="and seller_type = '".$_GET['seller_type']."' ";
}

if($status_disp == "IC"){
    $date_str = "od.ic_date like '%Y%m%d'";
}else if($status_disp == "DI"){
    $date_str = "od.di_date like %Y%m%d'";
}else if($status_disp == "OC" || $status_disp == ""){
    $date_str = "od.regdate like '%Y%m%d'";
}
$date_str = "vdate";

if($_GET["sdate"] && $_GET["edate"]){
    $sdate = $_GET["sdate"];
    $edate = $_GET["edate"];
    //$startDate = $_GET["sdate"];
    //$endDate = $_GET["edate"];

    $where .= "and ".$date_str." between '$sdate' and '$edate' ";
    //$where .= "and ".$date_str." between $startDate and $endDate ";
}else{
    $sdate = date("Ymd");
    $edate = date("Ymd");
    $where .= "and ".$date_str." between '$sdate' and '$edate' ";
}

if(!empty($cid2)){
    $where .= " and od.cid LIKE '".substr($cid2,0,($depth+1)*3)."%'";
}

if(is_array($age)){
    for($i=0;$i < count($age);$i++){
        if($age[$i] != ""){

            if($age_str == ""){
                if($age[$i] == 10){
                    $age_str .= " o.age between 0 and ".($age[$i]+9)." ";
                }else if($age[$i] == 60){
                    $age_str .= " o.age >= ".$age[$i]."  ";
                }else{
                    $age_str .= " o.age between ".$age[$i]." and ".($age[$i]+9)." ";
                }
            }else{
                if($age[$i] == 10){
                    $age_str .= " or o.age between 0 and ".($age[$i]+9)." ";
                }else if($age[$i] == 60){
                    $age_str .= " or o.age >= ".$age[$i]."  ";
                }else{
                    $age_str .= " or o.age between ".$age[$i]." and ".($age[$i]+10)." ";
                }
            }
        }
    }

    if(!empty($age_str)){
        $where .= "and ($age_str) ";
    }
}else{
    if($age){
        $where .= "and o.age between ".$age[$i]." and ".($age[$i]+10)." ";
    }
}

if($_GET['member_div'] =="member" ){
    $where .="and o.user_code != '' ";
}else if($_GET['member_div'] =="nonmember" ){
    $where .="and o.user_code = '' ";
}

//promotion_cupon_code

if($_GET["brand_code"]){
    $brand_code_array = str_replace(" ","",$_GET["brand_code"]);
    $brand_code_array = explode(",",$brand_code_array);
    if(is_array($brand_code_array)){
        $where .= " AND od.brand_code IN ('".implode("','",$brand_code_array)."')";
    }else{
        $where .= " AND od.brand_code = '".$_GET["brand_code"]."' ";
    }
}

if($_GET["promotion_cupon_code"]){
    $use_coupon_code_array = explode(",",$_GET["promotion_cupon_code"]);
    if(is_array($use_coupon_code_array)){
        $where .= " AND od.use_coupon_code IN ('".implode("','",$use_coupon_code_array)."')";
    }else{
        $where .= " AND od.use_coupon_code = '".$_GET["promotion_cupon_code"]."' ";
    }
}

if($_GET["product_code"]){
    $product_code_array = explode(",",$_GET["product_code"]);
    if(is_array($product_code_array)){
        $where .= " AND od.pid IN ('".implode("','",$product_code_array)."')";
    }else{
        $where .= " AND od.pid = '".$_GET["product_code"]."' ";
    }
}

if($_GET["company_v_code"]){
    $company_v_code_array = explode(",",$_GET["company_v_code"]);
    if(is_array($company_v_code_array)){
        $where .= " AND od.company_id IN ('".implode("','",$company_v_code_array)."')";
    }else{
        $where .= " AND od.company_id = '".$_GET["company_v_code"]."' ";
    }
}

if($_GET["trade_company_code"]){
    $trade_company_code_array = explode(",",$_GET["trade_company_code"]);
    if(is_array($trade_company_code_array)){
        $where .= " AND od.trade_company IN ('".implode("','",$trade_company_code_array)."')";
    }else{
        $where .= " AND od.trade_company = '".$_GET["trade_company_code"]."' ";
    }
}
// promotion_cupon_code

if($db->dbms_type == "oracle"){
    if($search_type != "" && $search_text != ""){
        if($search_type == "jumin"){
            $search_text = substr($search_text,0,6)."-".md5(substr($search_text,6,7));
            $where .= " and AES_DECRYPT(jumin,'".$db->ase_encrypt_key."') = '$search_text' ";

            $count_where .= " and AES_DECRYPT(jumin,'".$db->ase_encrypt_key."') = '$search_text' ";

        }else if($search_type == "name" || $search_type == "mail" || $search_type == "pcs" || $search_type == "tel" ){
            $where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
            $count_where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
        }else{
            $where .= " and $search_type LIKE  '%$search_text%' ";
            $count_where .= " and $search_type LIKE  '%$search_text%' ";
        }

    }
}else{
    if($search_type != "" && $search_text != ""){
        if($search_type == "jumin"){
            $search_text = substr($search_text,0,6)."-".md5(substr($search_text,6,7));
            $where .= " and AES_DECRYPT(UNHEX(jumin),'".$db->ase_encrypt_key."') = '$search_text' ";
            $count_where .= " and AES_DECRYPT(UNHEX(jumin),'".$db->ase_encrypt_key."') = '$search_text' ";
        }else if($search_type == "name" || $search_type == "mail" || $search_type == "pcs" || $search_type == "tel" ){
            $where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
            $count_where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
        }else{
            $where .= " and $search_type LIKE  '%$search_text%' ";
            $count_where .= " and $search_type LIKE  '%$search_text%' ";
        }
    }
}


if($_GET["vat_type"]){
    $vat_type = $_GET["vat_type"];
}else{
    $vat_type = "Y";
}

if($_GET["status_disp"]){
    $status_disp = $_GET["status_disp"];
}else{
    $status_disp = "OC";
}


if($_GET["groupbytype"]){
    $groupbytype = $_GET["groupbytype"];
}else{
    $groupbytype = "day";
}

if(!empty($_GET["sdate"]) && !empty($_GET["edate"])){
    $PickPeriod = ( strtotime($edate) - strtotime($sdate) ) / 86400;

    if($PickPeriod > 22){
        echo "<script>alert('조회 기간은 3주를 초과할 수 없습니다');location.href='./searchmember.php'</script>";
        //echo "<script>location.reload();</script>";
        exit;
    }
}

function ReportTable($vdate,$SelectReport=1){
    global $depth,$cid;
    global $non_sale_status, $report_type;
    global $where;
    $pageview01 = 0;
    //$cid = $referer_id;
    if($SelectReport == ""){
        $SelectReport = 1;
    }
    $fordb = new forbizDatabase();
    $fordb2 = new forbizDatabase();


    if($depth == ""){
        $depth = 0;
    }else{
        $depth = $depth+1;
    }

    if($SelectReport == "4"){
        $vdate = $search_sdate;
        $vweekenddate = $search_edate;
    }


    if($_GET["sdate"] && $_GET["edate"]){
        $sdate = $_GET["sdate"];
        $edate = $_GET["edate"];
    }else{
        $sdate = date("Ymd");
        $edate = date("Ymd");
    }

    if($vdate == ""){
        $vdate = date("Ymd", time());
        $vyesterday = date("Ymd", time()-84600);
        $voneweekago = date("Ymd", time()-84600*7);
    }else{
        if($SelectReport ==3){
            if(strlen($vdate) == 6){
                $vdate = $vdate."01";
            }
        }
        $vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
        $vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
    }

    //$vdate = date("Ymd", time());
    $today = date("Ymd", time());
    //$vyesterday = date("Ymd", time()-84600);
    //$voneweekago = date("Ymd", time()-84600*6);
    $vtwoweekago = date("Ymd", time()-84600*14);
    $vfourweekago = date("Ymd", time()-84600*28);
    $vyesterday = date("Ymd",mktime(0,0,0,substr($today,4,2),substr($today,6,2),substr($today,0,4))-60*60*24);
    //$voneweekago = date("Ymd",mktime(0,0,0,substr($today,4,2),substr($today,6,2),substr($today,0,4))-60*60*24*6);
    $v15ago = date("Ymd",mktime(0,0,0,substr($today,4,2),substr($today,6,2),substr($today,0,4))-60*60*24*15);
    $vfourweekago = date("Ymd",mktime(0,0,0,substr($today,4,2),substr($today,6,2),substr($today,0,4))-60*60*24*28);
    $vonemonthago = date("Ymd",mktime(0,0,0,substr($today,4,2)-1,substr($today,6,2)+1,substr($today,0,4)));
    $v2monthago = date("Ymd",mktime(0,0,0,substr($today,4,2)-2,substr($today,6,2)+1,substr($today,0,4)));
    $v3monthago = date("Ymd",mktime(0,0,0,substr($today,4,2)-3,substr($today,6,2)+1,substr($today,0,4)));

    if(isset($_GET["orderby"]) && $_GET["orderby"] != "" && isset($_GET["ordertype"]) && $_GET["ordertype"] != ""){
        $orderbyString = " order by ".$_GET["orderby"]." ".$_GET["ordertype"].", login_total_cnt desc, nview_cnt desc  ";
    }else{
        $orderbyString = " order by login_total_cnt desc , nview_cnt desc ";
    }

    if(!empty($_GET["max"])){
        $max = $_GET["max"];
    }else{
        $max = 10;
    }

    if (empty($_GET["page"])){
        $start = 0;
        $page  = 1;
    }else{
        $start = ($_GET["page"] - 1) * $max;
        $page  = $_GET["page"];
    }
    $orderbyString .= "	limit $start, $max ";

    //echo $vdate;

    //$sql = "Select r.cid, r.cname, b.visit_cnt from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYREFERER." b where b.vdate = '$vdate' and substr(r.cid,0,6) = substr(b.vreferer_id,0,6) and r.depth = $depth group by r.cid, r.cname order by visit_cnt desc";

    $sql2 = "select count(*) as total
					from 
					(Select b.vdate as vdate, b.ucode as ucode, sum(b.nview_cnt) as nview_cnt , 0 as order_goods_cnt, 0 as pcnt , 0 login_total_cnt , 0 as ptprice
					from ".TBL_COMMERCE_VIEWINGVIEW." b  	
					where b.vdate between '".$sdate."' and '".$edate."'			
					group by ucode
					union
					select DATE_FORMAT(od.regdate,'%Y%m%d') as vdate, o.user_code as ucode, 0 as nview_cnt , count(*) as order_goods_cnt,  sum(od.pcnt) as pcnt,  0 as login_total_cnt , sum(od.pt_dcprice) as ptprice
					from shop_order o, shop_order_detail od 
					where o.oid = od.oid and od.regdate between '".substr($sdate, 0, 4)."-".substr($sdate, 4, 2)."-".substr($sdate, 6, 2)." 00:00:00' and '".substr($edate, 0, 4)."-".substr($edate, 4, 2)."-".substr($edate, 6, 2)." 23:59:59'  
					and od.status IN ('IC','DR','DC', 'BF') 
					and od.order_from = 'self'
					and o.user_code != ''
					group by ucode 
					union
					Select lh.vdate as vdate, mem_ix as ucode , 0 as nview_cnt , 0 as order_goods_cnt, 0 as pcnt , sum(ncnt) as login_total_cnt , 0 as ptprice
					from logstory_login_history lh  	
					where lh.vdate between '".$sdate."' and '".$edate."'  
					group by ucode
					) data left join ".TBL_COMMON_MEMBER_DETAIL." cmd on data.ucode = cmd.code
					left join  ".TBL_COMMON_USER." cu on data.ucode = cu.code
					where ucode != '' 
					".$where."
					";
    $sql2 .= "group by ucode ";

    $fordb->query($sql2);
    $total = $fordb->total;
    //echo $sql2;

//echo nl2br($sql2);

    $sql = "select  data.ucode,  AES_DECRYPT(UNHEX(cmd.name),'".$fordb->ase_encrypt_key."') as name, cu.id, sum(nview_cnt) as nview_cnt, sum(order_goods_cnt) as order_goods_cnt, 
					sum(pcnt) as pcnt, sum(login_total_cnt) as login_total_cnt, sum(ptprice) as ptprice,
					case when sum(order_goods_cnt) = 0 then 0 else sum(login_total_cnt)/sum(order_goods_cnt) end as loginperorder,
					case when sum(order_goods_cnt) = 0 then 0 else sum(ptprice)/sum(order_goods_cnt) end as saleperorder, 
					case when sum(pcnt) = 0 then 0 else sum(login_total_cnt)/sum(pcnt) end as loginperpcnt,
					case when sum(pcnt) = 0 then 0 else sum(ptprice)/sum(pcnt) end as saleperpcnt,
					case when sum(login_total_cnt) = 0 then 0 else sum(ptprice)/sum(login_total_cnt) end as saleperlogin
					from 
					(Select b.vdate as vdate, b.ucode as ucode, sum(b.nview_cnt) as nview_cnt , 0 as order_goods_cnt, 0 as pcnt , 0 login_total_cnt , 0 as ptprice
					from ".TBL_COMMERCE_VIEWINGVIEW." b  	
					where b.vdate between '".$sdate."' and '".$edate."'			
					and b.ucode != ''
					group by ucode
					union
					select DATE_FORMAT(od.regdate,'%Y%m%d') as vdate, o.user_code as ucode, 0 as nview_cnt , count(*) as order_goods_cnt,  sum(od.pcnt) as pcnt,  0 as login_total_cnt , sum(od.pt_dcprice) as ptprice
					from shop_order o, shop_order_detail od 
					where o.oid = od.oid and od.regdate between '".substr($sdate, 0, 4)."-".substr($sdate, 4, 2)."-".substr($sdate, 6, 2)." 00:00:00' and '".substr($edate, 0, 4)."-".substr($edate, 4, 2)."-".substr($edate, 6, 2)." 23:59:59'  
					and od.status IN ('IC','DR','DC', 'BF') 
					and od.order_from = 'self'
					and o.user_code != ''
					group by ucode 
					union
					Select lh.vdate as vdate, mem_ix as ucode , 0 as nview_cnt , 0 as order_goods_cnt, 0 as pcnt , sum(ncnt) as login_total_cnt , 0 as ptprice
					from logstory_login_history lh  	
					where lh.vdate between '".$sdate."' and '".$edate."'  
					and mem_ix != ''
					group by ucode
					) data left join ".TBL_COMMON_MEMBER_DETAIL." cmd on data.ucode = cmd.code
					left join  ".TBL_COMMON_USER." cu on data.ucode = cu.code
					where ucode != '' 
					".$where."
					";

    $sql .= "group by ucode ";
    $sql .= $orderbyString;

    //echo nl2br($sql);



    //echo $depth."<br>";
    //echo nl2br($sql);

    $fordb->query($sql);
    //$total = $fordb->total;
//	$str_page_bar = page_bar($total, $page, $max, "&max=$max&info_type=$info_type","");


    if(isset($_GET["mode"]) && $_GET["mode"] == "excel"){
        include '../../include/phpexcel/Classes/PHPExcel.php';
        PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

        //date_default_timezone_set('Asia/Seoul');

        $sheet = new PHPExcel();

        // 속성 정의
        $sheet->getProperties()->setCreator("포비즈 코리아")
            ->setLastModifiedBy("Mallstory.com")
            ->setTitle("accounts plan price List")
            ->setSubject("accounts plan price List")
            ->setDescription("generated by forbiz korea")
            ->setKeywords("mallstory")
            ->setCategory("accounts plan price List");
        $col = 'A';


        $start=3;
        $i = $start;

        $sheet->getActiveSheet(0)->mergeCells('A2:N2');
        $sheet->getActiveSheet(0)->setCellValue('A2', "최다로그인고객 분석 - 구매전환분석");
        $sheet->getActiveSheet(0)->mergeCells('A3:N3');
        $sheet->getActiveSheet(0)->setCellValue('A3', $dateString);

        $sheet->getActiveSheet(0)->mergeCells('A'.($i+1).':A'.($i+2));
        $sheet->getActiveSheet(0)->mergeCells('B'.($i+1).':B'.($i+2));
        $sheet->getActiveSheet(0)->mergeCells('C'.($i+1).':C'.($i+2));
        $sheet->getActiveSheet(0)->mergeCells('D'.($i+1).':D'.($i+2));
        $sheet->getActiveSheet(0)->mergeCells('E'.($i+1).':E'.($i+2));
        $sheet->getActiveSheet(0)->mergeCells('F'.($i+1).':F'.($i+2));

        $sheet->getActiveSheet(0)->mergeCells('G'.($i+1).':I'.($i+1));
        $sheet->getActiveSheet(0)->mergeCells('J'.($i+1).':L'.($i+1));
        $sheet->getActiveSheet(0)->mergeCells('M'.($i+1).':N'.($i+1));

        $sheet->getActiveSheet(0)->setCellValue('A' . ($i+1), "순");
        $sheet->getActiveSheet(0)->setCellValue('B' . ($i+1), "회원명");
        $sheet->getActiveSheet(0)->setCellValue('C' . ($i+1), "회원아이디");
        $sheet->getActiveSheet(0)->setCellValue('D' . ($i+1), "로그인(a)");
        $sheet->getActiveSheet(0)->setCellValue('E' . ($i+1), "로그인점유율");
        $sheet->getActiveSheet(0)->setCellValue('F' . ($i+1), "조회수");
        $sheet->getActiveSheet(0)->setCellValue('G' . ($i+1), "건별분석");
        $sheet->getActiveSheet(0)->setCellValue('J' . ($i+1), "수량별분석");
        $sheet->getActiveSheet(0)->setCellValue('M' . ($i+1), "로그인별분석");

        $sheet->getActiveSheet(0)->setCellValue('G' . ($i+2), "구매건수(b)");
        $sheet->getActiveSheet(0)->setCellValue('H' . ($i+2), "구매건당\r평균로그인수(a/b)");
        $sheet->getActiveSheet(0)->setCellValue('I' . ($i+2), "구매건당\r평균매출액");
        $sheet->getActiveSheet(0)->setCellValue('J' . ($i+2), "구매수량");
        $sheet->getActiveSheet(0)->setCellValue('K' . ($i+2), "구매수량당\r평균로그인수");
        $sheet->getActiveSheet(0)->setCellValue('L' . ($i+2), "구매수량당\r평균매출액");
        $sheet->getActiveSheet(0)->setCellValue('M' . ($i+2), "로그인당\r평균매출액");
        $sheet->getActiveSheet(0)->setCellValue('N' . ($i+2), "매출액");



        $sheet->setActiveSheetIndex(0);
        //$i = $i + 2;
        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);
            $nview_cnt_sum += $fordb->dt['nview_cnt'];
            $order_goods_cnt_sum += $fordb->dt['order_goods_cnt'];
            $login_total_cnt_sum += $fordb->dt['login_total_cnt'];
            $pcnt_sum += $fordb->dt['pcnt'];
            $loginperorder_sum += $fordb->dt['loginperorder'];

            $ptprice_sum += $fordb->dt['ptprice'];

        }

        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);


            if($login_total_cnt_sum > 0){
                $login_rate = $fordb->dt['login_total_cnt']/$login_total_cnt_sum;
            }else{
                $login_rate = 0;
            }

            $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 3), ($i + 1));
            $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 3), $fordb->dt['name']);
            $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 3), $fordb->dt['id']);

            $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 3), $fordb->dt['login_total_cnt']);
            $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 3), $login_rate);
            $sheet->getActiveSheet()->getStyle('E' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

            $sheet->getActiveSheet()->setCellValue('F' . ($i + $start + 3), $fordb->dt['nview_cnt']);
            $sheet->getActiveSheet()->getStyle('F' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('G' . ($i + $start + 3), $fordb->dt['order_goods_cnt']);
            $sheet->getActiveSheet()->getStyle('G' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('H' . ($i + $start + 3), $fordb->dt['loginperorder']);
            $sheet->getActiveSheet()->getStyle('H' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('I' . ($i + $start + 3), $fordb->dt['saleperorder']);
            $sheet->getActiveSheet()->getStyle('I' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('J' . ($i + $start + 3), $fordb->dt['pcnt']);
            $sheet->getActiveSheet()->getStyle('J' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('K' . ($i + $start + 3), $fordb->dt['loginperpcnt']);
            $sheet->getActiveSheet()->getStyle('K' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('L' . ($i + $start + 3), $fordb->dt['saleperpcnt']);
            $sheet->getActiveSheet()->getStyle('L' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('M' . ($i + $start + 3), $fordb->dt['saleperlogin']);
            $sheet->getActiveSheet()->getStyle('M' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('N' . ($i + $start + 3), $fordb->dt['ptprice']);
            $sheet->getActiveSheet()->getStyle('N' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('O' . ($i + $start + 3), $sale_rate);
            $sheet->getActiveSheet()->getStyle('O' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);


            $nview_cnt_sum = $nview_cnt_sum + returnZeroValue($fordb->dt['nview_cnt']);
            $order_change_rate_sum += $change_rate*100;


        }


        //$i++;
        $sheet->getActiveSheet(0)->mergeCells('A'.($i + $start + 3).':C'.($i+ $start+3));
        $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 3), '합계');
        //$sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 3), getIventoryCategoryPathByAdmin($goods_infos[$i]['cid'], 4));
        $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 3), $login_total_cnt_sum);
        $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 3), "100%");
        $sheet->getActiveSheet()->setCellValue('F' . ($i + $start + 3), $nview_cnt_sum);
        $sheet->getActiveSheet()->setCellValue('G' . ($i + $start + 3), $order_goods_cnt_sum);
        $sheet->getActiveSheet()->setCellValue('H' . ($i + $start + 3), ($order_goods_cnt_sum > 0 ? $login_total_cnt_sum/$order_goods_cnt_sum*100:0));
        $sheet->getActiveSheet()->setCellValue('I' . ($i + $start + 3), ($order_goods_cnt_sum > 0 ? $ptprice_sum/$order_goods_cnt_sum:0));
        $sheet->getActiveSheet()->setCellValue('J' . ($i + $start + 3), $order_goods_cnt_sum);
        $sheet->getActiveSheet()->setCellValue('K' . ($i + $start + 3), ($order_goods_cnt_sum > 0 ? $login_total_cnt_sum/$order_goods_cnt_sum:0));
        $sheet->getActiveSheet()->setCellValue('L' . ($i + $start + 3), ($order_goods_cnt_sum > 0 ? $ptprice_sum/$order_goods_cnt_sum:0));
        $sheet->getActiveSheet()->setCellValue('M' . ($i + $start + 3), ($login_total_cnt_sum > 0 ? $ptprice_sum/$login_total_cnt_sum:0));
        $sheet->getActiveSheet()->setCellValue('N' . ($i + $start + 3), $ptprice_sum);
        $sheet->getActiveSheet()->setCellValue('O' . ($i + $start + 3), "-");




        $sheet->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $sheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        //$sheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $sheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $sheet->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $sheet->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $sheet->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $sheet->getActiveSheet()->getColumnDimension('G')->setWidth(9);
        $sheet->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $sheet->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $sheet->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $sheet->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $sheet->getActiveSheet()->getColumnDimension('L')->setWidth(10);
        $sheet->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $sheet->getActiveSheet()->getColumnDimension('N')->setWidth(15);
        $sheet->getActiveSheet()->getColumnDimension('O')->setWidth(15);

        $sheet->getActiveSheet()->getRowDimension($start+2)->setRowHeight(30);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.iconv("utf-8","cp949","최다로그인고객 분석_구매전환분석.xls").'"');
        header('Cache-Control: max-age=0');



        // $objWriter->setUseInlineCSS(true);
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $sheet->getActiveSheet()->getStyle('A'.($start+1).':N'.($i+$start+3))->applyFromArray($styleArray);
        $sheet->getActiveSheet()->getStyle('A'.$start.':N'.($i+$start+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $sheet->getActiveSheet()->getStyle('A'.($start).':N'.($i+$start+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getActiveSheet()->getStyle('A'.($start).':N'.($start+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setWrapText(true); // 타이틀 영역 가운데 정렬
        $sheet->getActiveSheet()->getStyle('B'.($start+3).':B'.($i+$start+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)->setWrapText(true); ; // 카테고리 영역 왼쪽 정렬

        $sheet->getActiveSheet()->getStyle('A'.$start.':N'.($i+$start+3))->getFont()->setSize(10)->setName('돋움');  // 리포트 영역 폰트 설정

        $sheet->getActiveSheet()->getStyle('A2')->getFont()->setSize(15)->setBold(true)->setName('돋움'); // 타이틀 폰트 설정
        $sheet->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // 타이틀 정렬
        $sheet->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT); // 리포트 타입 정렬
        $sheet->getActiveSheet()->getStyle('A'.($i+$start+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // 합계텍스트 정렬
        //

        unset($styleArray);
        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel5');
        $objWriter->save('php://output');

        exit;
    }



    $mstring .= "<table cellpadding=0 cellspacing=0 width=100%>
	<tr height=150>
		<td   >
			 <form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
			<input type='hidden' name='mode' value='search'>
			<input type='hidden' name='cid2' value='$cid2'>
			<input type='hidden' name='depth' value='$depth'>
			<input type='hidden' name='groupbytype' value='$groupbytype'>
			<input type='hidden' name='sprice' value='0' />
			<input type='hidden' name='eprice' value='1000000' />
			<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'><!---mbox04-->
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05 align=center' style='padding:1px'>
						<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='search_table_box'>
							<col width='15%'>
							<col width='35%'>
							<col width='15%'>
							<col width='35%'>";
    if($_SESSION["admin_config"]['front_multiview'] == "Y"){
        $mstring .= "
					<tr>
						<td class='search_box_title' > 쇼핑몰타입</td>
						<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
					</tr>";
    }
    $mstring .= "
							<tr height=27>
							  <td class='search_box_title'><b>구매일자</b></td>
							  <td class='search_box_item' colspan=3 >
								<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
									<col width=70>
									<col width=20>
									<col width=70>
									<col width=*>
									<tr>
										<TD nowrap>
										<input type='text' name='sdate' class='textbox' value='".$sdate."' style='height:20px;width:70px;text-align:center;' id='search_sdate'>
										<!--SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년
										<SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월
										<SELECT name=FromDD></SELECT> 일 -->
										</TD>
										<TD align=center> ~ </TD>
										<TD nowrap>
										<input type='text' name='edate' class='textbox' value='".$edate."' style='height:20px;width:70px;text-align:center;' id='search_edate'>
										<!--SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년
										<SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월
										<SELECT name=ToDD></SELECT> 일 -->
										</TD>
										<TD style='padding:0px 10px'>
											<a href=\"javascript:setSelectDate('$today','$today',1);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_today.gif'></a>
											<a href=\"javascript:setSelectDate('$vyesterday','$vyesterday',1);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_yesterday.gif'></a>
											<a href=\"javascript:setSelectDate('$voneweekago','$today',1);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_1week.gif'></a>
											<a href=\"javascript:setSelectDate('$v15ago','$today',1);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_15days.gif'></a>
											<!--a href=\"javascript:setSelectDate('$vonemonthago','$today',1);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_1month.gif'></a>
											<a href=\"javascript:setSelectDate('$v2monthago','$today',1);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_2months.gif'></a>
											<a href=\"javascript:setSelectDate('$v3monthago','$today',1);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_3months.gif'></a-->
										</TD>
									</tr>
								</table>
							  </td>
							</tr>
							<tr>
								<td class='search_box_title'><b>전시 카테고리선택</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'>".getCategoryList3("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $cid2)."</td>
											<td style='padding-right:5px;'>".getCategoryList3("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $cid2)."</td>
											<td style='padding-right:5px;'>".getCategoryList3("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $cid2)."</td>
											<td>".getCategoryList3("세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $cid2)."</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr  height=27>
							  <td class='search_box_title' width='13%' bgcolor='#efefef' align=center >연령대</td>
							  <td class='search_box_item' width='*' align=left style='padding-left:5px;'  >
							       
								  <input type='checkbox' name='age[]' id='age_' value='' ".CompareReturnValue("",$age,"checked")."><label for='age_'>전체</label>
								  <input type='checkbox' name='age[]' id='age_10' value='10'  ".CompareReturnValue("10",$age,"checked")."><label for='age_10'  style='width:200px;'>~10대</label>
								  <input type='checkbox' name='age[]' id='age_20' value='20'  ".CompareReturnValue("20",$age,"checked")."><label for='age_20' >20대</label>
								  <input type='checkbox' name='age[]' id='age_30' value='30'  ".CompareReturnValue("30",$age,"checked")."><label for='age_30' >30대</label>
								  <input type='checkbox' name='age[]' id='age_40' value='40'  ".CompareReturnValue("40",$age,"checked")."><label for='age_40' >40대</label>
								  <input type='checkbox' name='age[]' id='age_50' value='50'  ".CompareReturnValue("50",$age,"checked")."><label for='age_50'  >50대</label>
								  <input type='checkbox' name='age[]' id='age_60' value='60'  ".CompareReturnValue("60",$age,"checked")."><label for='age_60' >ETC</label>
								</select>
							  </td> 
								<td class='search_box_title' bgcolor='#efefef' align=center>성별검색 </td>
								  <td class='search_box_item' align=left style='padding-left:5px;'>
								  <input type=radio name='sex' value='' id='sex_all'  ".CompareReturnValue("0",$sex,"checked")." checked><label for='sex_all'>모두</label>
								  <input type=radio name='sex' value='M' id='sex_man'  ".CompareReturnValue("M",$sex,"checked")."><label for='sex_man'>남자</label>
								  <input type=radio name='sex' value='W' id='sex_women' ".CompareReturnValue("W",$sex,"checked")."><label for='sex_women'>여자</label>
								</td>
							</tr>
							";
    if(false){
        $mstring .= "
							<tr  height=27>
							  <td class='search_box_title' width='13%' bgcolor='#efefef' align=center >지역선택</td>
							  <td class='search_box_item' width='*' align=left style='padding-left:5px;'>
							  <select name='region' >
								  <option value=''>-- 선택 --</option>
								  <option value='서울'  ".CompareReturnValue("서울",$region,"selected").">서울</option>
								  <option value='충북'  ".CompareReturnValue("충북",$region,"selected").">충북</option>
								  <option value='충남'  ".CompareReturnValue("충남",$region,"selected").">충남</option>
								  <option value='전북'  ".CompareReturnValue("전북",$region,"selected").">전북</option>
								  <option value='제주'  ".CompareReturnValue("제주",$region,"selected").">제주</option>
								  <option value='전남'  ".CompareReturnValue("전남",$region,"selected").">전남</option>
								  <option value='경북'  ".CompareReturnValue("경북",$region,"selected").">경북</option>
								  <option value='경남'  ".CompareReturnValue("경남",$region,"selected").">경남</option>
								  <option value='경기'  ".CompareReturnValue("경기",$region,"selected").">경기</option>
								  <option value='부산'  ".CompareReturnValue("부산",$region,"selected").">부산</option>
								  <option value='대구'  ".CompareReturnValue("대구",$region,"selected").">대구</option>
								  <option value='인천'  ".CompareReturnValue("인천",$region,"selected").">인천</option>
								  <option value='광주'  ".CompareReturnValue("광주",$region,"selected").">광주</option>
								  <option value='대전'  ".CompareReturnValue("대전",$region,"selected").">대전</option>
								  <option value='울산'  ".CompareReturnValue("울산",$region,"selected").">울산</option>
								  <option value='강원'  ".CompareReturnValue("강원",$region,"selected").">강원</option>
								</select>
							  </td>
							<td class='search_box_title' bgcolor='#efefef' align=center>성별검색 </td>
							  <td class='search_box_item' align=left style='padding-left:5px;'>
							  <input type=radio name='sex' value='' id='sex_all'  ".CompareReturnValue("0",$sex,"checked")." checked><label for='sex_all'>모두</label>
							  <input type=radio name='sex' value='M' id='sex_man'  ".CompareReturnValue("M",$sex,"checked")."><label for='sex_man'>남자</label>
							  <input type=radio name='sex' value='W' id='sex_women' ".CompareReturnValue("W",$sex,"checked")."><label for='sex_women'>여자</label>
							</td>
							</tr>
							";
    }
    if(false){
        $mstring .= "
							<tr>
								<td class='search_box_title'><b>제휴사 카테고리선택</b></td>
								<td class='search_box_item' colspan=3>
									<select name='company_cid1' id='com_cid1' onchange='loadComCategory(this);'><option value=''>대분류</option>".getCompanyCateSelectBoxOption(1,$company_cid1)."</select>
									";
        if(!empty($company_cid)){
            $mstring .="
													<select name='company_cid' id='com_cid2'>".getCompanyCateSelectBoxOption(2,$company_cid)."</select>";
        }else{
            $mstring .="
													<select name='company_cid' id='com_cid2'><option value=''>중분류</option></select>";
        }
        $mstring .=	"
								</td>
							</tr>";
    }

    $mstring .= "
							<tr>
								<td class='search_box_title'><b>브랜드</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../../code_search.php?search_type=brand',600,380,'code_search')\"  style='cursor:pointer;'>
											<img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"$('#brand_code').val('');\" style='cursor:pointer;' alt='삭제' title='삭제'/>
											</td>
											<td><input type=text class='textbox' name='brand_code' id='brand_code' value='".$brand_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>상품코드</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../../code_search.php?search_type=product',600,380,'code_search')\"  style='cursor:pointer;'>
											<img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"$('#product_code').val('');\" style='cursor:pointer;' alt='삭제' title='삭제'/>
											</td>
											<td><input type=text class='textbox' name='product_code' id='product_code' value='".$product_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>";

    if($admininfo['admin_level'] == 9){
        $mstring .="<tr>
								<td class='search_box_title'><b>셀러업체</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../../code_search.php?search_type=company_v',600,380,'code_search')\"  style='cursor:pointer;'>
											<img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"$('#company_v_code').val('');\" style='cursor:pointer;' alt='삭제' title='삭제'/>
											</td>
											<td><input type=text class='textbox' name='company_v_code' id='company_v_code' value='".$company_v_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>매입업체</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../../code_search.php?search_type=trade_company',600,380,'code_search')\"  style='cursor:pointer;'>
											<img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"$('#trade_company_code').val('');\" style='cursor:pointer;' alt='삭제' title='삭제'/>
											</td>
											<td><input type=text class='textbox' name='trade_company_code' id='trade_company_code' value='".$trade_company_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>";
    }
    if(false){
        $mstring .="<tr>
								<td class='search_box_title'><b>프로모션(카드)</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../../code_search.php?search_type=promotion_card',600,380,'code_search')\"  style='cursor:pointer;'></td>
											<td><input type=text class='textbox' name='promotion_card_code' id='promotion_card_code' value='".$promotion_card_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>";
    }

    $mstring .="
							<tr>
								<td class='search_box_title'><b>프로모션(쿠폰)</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../../code_search.php?search_type=promotion_cupon',600,380,'code_search')\"  style='cursor:pointer;'>
											<img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"$('#promotion_cupon_code').val('');\" style='cursor:pointer;' alt='삭제' title='삭제'/>
											</td>
											<td><input type=text class='textbox' name='promotion_cupon_code' id='promotion_cupon_code' value='".$promotion_cupon_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>";

    if($admininfo['admin_id'] == "forbiz"){
        if($admininfo['admin_level'] == 9 && false){
            $mstring .="<tr>
								<td class='search_box_title'><b>제휴사</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../../code_search.php?search_type=company_j',600,380,'code_search')\"  style='cursor:pointer;'>
											<img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"$('#company_j_code').val('');\" style='cursor:pointer;' alt='삭제' title='삭제'/>
											</td>
											<td><input type=text class='textbox' name='company_j_code' id='company_j_code' value='".$company_j_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>";
        }
    }

    $mstring .="
							<tr height=27>
							  <td class='search_box_title' >조건검색 </td>
							  <td class='search_box_item'>
									<table cellpadding=0 cellspacing=0 width=100%>
										<col width='80'>
										<col width='*'>
										<tr>
											<td>
											  <select name=search_type>
													<option value='name' ".CompareReturnValue("name",$search_type,"selected").">고객명</option>
													<option value='cu.id' ".CompareReturnValue("id",$search_type,"selected").">아이디</option>
													<option value='jumin' ".CompareReturnValue("jumin",$search_type,"selected").">주민번호</option>
													<option value='tel' ".CompareReturnValue("tel",$search_type,"selected").">전화번호</option>
													<option value='pcs' ".CompareReturnValue("pcs",$search_type,"selected").">휴대전화</option>
													<option value='com_name' ".CompareReturnValue("ccd.com_name",$search_type,"selected").">회사명</option>
													<option value='com_phone' ".CompareReturnValue("ccd.com_phone",$search_type,"selected").">회사전화</option>
													<option value='com_fax' ".CompareReturnValue("ccd.com_fax",$search_type,"selected").">회사팩스</option>
													<option value='mail' ".CompareReturnValue("mail",$search_type,"selected").">이메일</option>
													<option value='addr1' ".CompareReturnValue("addr1",$search_type,"selected").">주소</option>
											  </select>
											</td>
											<td>
												<input type=text name='search_text' class='textbox' value='".$search_text."' style='width:70%;font-size:12px;padding:1px;' >
											</td>
										</tr>
									</table>
							  </td>
							  <td class='search_box_title' >회원그룹 </td>
							  <td class='search_box_item' >
							  ".makeGroupSelectBox($fordb2,"gp_ix",$gp_ix)."
							  </td>
								</tr>
							";

    $mstring .=	"
							 
						</table>
					</td>
					<th class='box_06'></th>
				</tr>
				<tr>
					<th class='box_07'></th>
					<td class='box_08'></td>
					<th class='box_09'></th>
				</tr>
				<tr >
					<td colspan=2 align=center style='padding:10px 0px'><input type=image src='../../images/".$_SESSION["admininfo"]["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
	</table>";
    $exce_down_str = "<img src=\"../../images/korea/btn_print.gif\" onclick=\"PopSWindow('?mode=print&".str_replace("&mode=iframe", "",$_SERVER["QUERY_STRING"])."',1150,500,'logstory_print');\" style=\"margin-right:3px;cursor:pointer;\"  >";

    $exce_down_str .= "<a href='?mode=excel&".str_replace(array("&mode=iframe","mode=iframe&"), "",$_SERVER["QUERY_STRING"])."'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_excel_save.gif' border=0></a>";
    //$mstring = $mstring.TitleBar("최다로그인고객 분석 : ".($cid ? getCategoryPath($cid,4):""),$dateString);




    $mstring .= "<form name='list_frm' method='post' action='/admin/member/member_batch.act.php'  target='act' >
	<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
	<table width='100%' border=0>";

    $mstring .= "<!--tr>
						<td height=30> </td>
						<td align=right>
						<select name=max style=\"font-size:12px;height: 20px; width: 80px;\" align=absmiddle onchange=\"document.location.href='?max='+this.value \">
							<option value='5' ".($max == 5 ? "selected":"").">5</option>
							<option value='10' ".($max == 10 ? "selected":"").">10</option>
							<option value='20' ".($max == 20 ? "selected":"").">20</option>
							<option value='50' ".($max == 50 ? "selected":"").">50</option>
							<option value='100' ".($max == 100 ? "selected":"").">100</option>
							<option value='200' ".($max == 200 ? "selected":"").">200</option>
							<option value='500' ".($max == 500 ? "selected":"").">500</option>
							<option value='1000' ".($max == 1000 ? "selected":"").">1000</option>
							<option value='2000' ".($max == 2000 ? "selected":"").">2000</option>
							<option value='10000' ".($max == 10000 ? "selected":"").">10000</option>
						</select> 개씩 보기
						</td>
						</tr-->";
    /*
    $mstring .= "
                        <tr height=50>
                            <td >
                                <div class='tab'>
                                            <table class='s_org_tab'>
                                            <tr>
                                                <td class='tab'>
                                                    <table id='tab_01'  ".(($report_type == '1' || $report_type == '') ? "class=on":"").">
                                                    <tr>
                                                        <th class='box_01'></th>
                                                        <td class='box_02' onclick=\"document.location.href='?report_type=1'\">매출상세 분석</td>
                                                        <th class='box_03'></th>
                                                    </tr>
                                                    </table>
                                                    <table id='tab_01'  ".(($report_type == '2' ) ? "class=on":"").">
                                                    <tr>
                                                        <th class='box_01'></th>
                                                        <td class='box_02' onclick=\"document.location.href='?report_type=2'\">구매전환 분석</td>
                                                        <th class='box_03'></th>
                                                    </tr>
                                                    </table>
                                                </td>
                                                <td class='btn' style='padding:5px 0px 0px 10px;'>
                                                <input type=checkbox name='category_view' id='category_view' value='1' onclick=\"reloadView()\"  ".($_COOKIE['category_view'] == 1 ? "checked":"")." ><label for='category_view'> 카테고리 함께보기</label>
                                                </td>
                                            </tr>
                                            </table>
                                        </div>
                            </td>
                          </tr>
                    </table>";
                    */

    $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>
						<col width='3%'>
						<col width='3%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='5%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='7%'>
						<col width='7%'>
						<col width='7%'>
						<col width='6%'>
						<col width='10%'>";
    $mstring .= "<tr height=30 align=center>
						<td class=s_td rowspan=2><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
						<td class=m_td rowspan=2>순</td>
						<td class=m_td rowspan=2> ".OrderByLink("회원명", "name", $ordertype)."</td>
						<td class=m_td rowspan=2>회원아이디</td>
						<td class=m_td rowspan=2>로그인(a)</td>
						<td class=m_td rowspan=2>로그인<br>점유율</td>
						<td class=m_td rowspan=2>".OrderByLink("조회수", "nview_cnt", $ordertype)."</td>
						<td class=m_td colspan=3>건별분석</td>
						<td class=m_td colspan=3>수량별분석</td>
						<td class=m_td colspan=2>로그인별분석</td>
						<td class=m_td rowspan=2>프로모션</td> 
					   </tr>\n";
    $mstring .= "<tr height=30 align=center>
							
							<td class=m_td >".OrderByLink("구매건수<br>(b)", "order_goods_cnt", $ordertype)."</td>
							<td class=m_td >".OrderByLink("구매건당<br>평균로그인수(a/b)", "loginperorder", $ordertype)."</td>
							<td class=m_td >".OrderByLink("구매건당<br>평균매출액", "saleperorder", $ordertype)."</td>
							<td class=m_td >".OrderByLink("구매수량", "pcnt", $ordertype)."</td>
							<td class=m_td >".OrderByLink("구매수량당<br>평균로그인수", "loginperpcnt", $ordertype)." </td>
							<td class=m_td >".OrderByLink("구매수량당<br>평균매출액", "saleperpcnt", $ordertype)."</td>

							<td class=m_td >".OrderByLink("로그인당<br>평균매출액", "saleperlogin", $ordertype)." </td>
							<td class=m_td >".OrderByLink("매출액", "ptprice", $ordertype)."</td>
						</tr>\n
						<!--tr height=25 align=right>
							<td class=s_td align=center colspan=4>합계</td>
							<td class=e_td style='padding-right:20px;'>{login_total_cnt_sum}</td>
							<td class=e_td style='padding-right:20px;'>100%</td>
							<td class=e_td style='padding-right:20px;'>{nview_cnt_sum}</td>
							<td class=e_td style='padding-right:20px;'>{order_goods_cnt_sum}</td>
							<td class=e_td style='padding-right:20px;'>{login_total_cnt_sum/order_goods_cnt_sum}</td>
							<td class=e_td style='padding-right:20px;'>{ptprice_sum/order_goods_cnt_sum}</td>
							<td class=e_td style='padding-right:20px;'>{order_goods_cnt_sum}</td>
							<td class=e_td style='padding-right:20px;'>{login_total_cnt_sum/order_goods_cnt_sum}</td>
							<td class=e_td style='padding-right:20px;'>{ptprice_sum/order_goods_cnt_sum}</td>
							<td class=e_td style='padding-right:20px;'>{ptprice_sum/login_total_cnt_sum}</td>
							<td class=e_td style='padding-right:20px;'>{ptprice_sum}</td> 
							<td class=e_td style='padding-right:20px;'>-</td>
							</tr-->";
    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);
        $nview_cnt_sum += $fordb->dt['nview_cnt'];
        $order_goods_cnt_sum += $fordb->dt['order_goods_cnt'];
        $login_total_cnt_sum += $fordb->dt['login_total_cnt'];
        $pcnt_sum += $fordb->dt['pcnt'];
        $loginperorder_sum += $fordb->dt['loginperorder'];

        $ptprice_sum += $fordb->dt['ptprice'];

    }

    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);


        if($login_total_cnt_sum > 0){
            $login_rate = $fordb->dt['login_total_cnt']/$login_total_cnt_sum*100;
        }else{
            $login_rate = 0;
        }


        $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
		<td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$fordb->dt['ucode']."'></td>
		<td class='list_box_td list_bg_gray' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($i+1)."</td>
		<td class='list_box_td point' style='text-align:center;line-height:150%;padding:5px;' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">
		<a href=\"javascript:PoPWindow('../../member/member_view.php?code=".$fordb->dt['ucode']."',950,700,'member_view')\">".$fordb->dt['name']."</a> ";
        $mstring .= "
		</td>
		<td class='list_box_td' style='text-align:center;line-height:150%;padding:5px;' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">
		<a href=\"javascript:PoPWindow('../../member/member_view.php?code=".$fordb->dt['ucode']."',950,700,'member_view')\">".$fordb->dt['id']."</a>
		</td>
		
		<td class='list_box_td  number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(returnZeroValue($fordb->dt['login_total_cnt']),0)."&nbsp;</td>
		<td class='list_box_td  number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(returnZeroValue($login_rate),2)."%</td>
		<td class='list_box_td  number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(returnZeroValue($fordb->dt['nview_cnt']),0)."&nbsp;</td>
		<td class='list_box_td point number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(returnZeroValue($fordb->dt['order_goods_cnt']),0)."&nbsp;</td>
		<td class='list_box_td list_bg_gray number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(returnZeroValue($fordb->dt['loginperorder']),1)."</td>
		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['saleperorder'])."</td>
		<td class='list_box_td point number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['pcnt'])."</td>
		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['loginperpcnt'],1)."</td>
		<td class='list_box_td list_bg_gray number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(returnZeroValue($fordb->dt['saleperpcnt']),0)." </td> 
		<td class='list_box_td  number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(returnZeroValue($fordb->dt['saleperlogin']),0)."&nbsp;</td>
		<td class='list_box_td point number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['ptprice'])."</td> 
		<td class='list_box_td list_bg_gray number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='text-align:center;padding:5px;'>
			 <img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_sms.gif' align=absmiddle onclick=\"PoPWindow('../../sms.pop.php?pid=".$fordb->dt['pid']."&vdate=".$vdate."&SelectReport=".$SelectReport."',500,380,'sendsms')\" style='cursor:pointer;margin:1px;' alt='문자발송' title='문자발송'>
        	 <img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_email.gif' align=absmiddle onclick=\"PoPWindow('../../mail.pop.php?pid=".$fordb->dt['pid']."&vdate=".$vdate."&SelectReport=".$SelectReport."',550,535,'sendmail')\" style='cursor:pointer;margin:1px;' alt='이메일발송' title='이메일발송'><br>
			 
			 <img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_popup.gif' align=absmiddle onclick=\"PoPWindow('../../display/popup.write.php?mmode=pop&target_type=cart&pid=".$fordb->dt['pid']."&vdate=".$vdate."&SelectReport=".$SelectReport."',800,680,'popupwrite')\" style='cursor:pointer;margin:1px;' alt='팝업설정' title='팝업설정'>
			 <img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_coupon.gif' align=absmiddle onclick=\"PoPWindow('../../sms.pop.php?mmode=pop&target_type=cart&pid=".$fordb->dt['pid']."&vdate=".$vdate."&SelectReport=".$SelectReport."',500,380,'sendsms')\" style='cursor:pointer;margin:1px;' alt='쿠폰발송' title='쿠폰발송'>
        	 <img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_mem_list.gif' align=absmiddle onclick=\"PoPWindow('../../mail.pop.php?mmode=pop&target_type=cart&pid=".$fordb->dt['pid']."&vdate=".$vdate."&SelectReport=".$SelectReport."',550,535,'sendmail')\" style='cursor:pointer;margin:1px;' alt='회원목록' title='회원목록'>
		</td> 
		</tr>\n";

        $nview_cnt_sum = $nview_cnt_sum + returnZeroValue($fordb->dt['nview_cnt']);

        $order_goods_cnt_sum +=  returnZeroValue($fordb->dt['order_goods_cnt']);
        $cart_total_cnt_sum +=  returnZeroValue($fordb->dt['cart_total_cnt']);

        $order_change_rate_sum += $change_rate;


    }

    if ($nview_cnt_sum == 0){
        $mstring .= "<tr  align=center height=200><td colspan=16 class='list_box_td'  >결과값이 없습니다.</td></tr>\n";
    }
    //$mstring .= "</table>\n";
    /*
    $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box' style='margin-top:5px;'>
                        <col width='10%'>
                        <col width='*'>
                        <col width='30%'>";
    */
    //$mstring .= "<tr height=2 bgcolor=#ffffff align=center><td colspan=2></td></tr>\n";
    /*
    $mstring .= "<tr height=25 align=right>
    <td class=s_td align=center colspan=4>합계</td>
    <td class=e_td style='padding-right:20px;'>".number_format($login_total_cnt_sum,0)."</td>
    <td class=e_td style='padding-right:20px;'>100%</td>
    <td class=e_td style='padding-right:20px;'>".number_format($nview_cnt_sum,0)."</td>
    <td class=e_td style='padding-right:20px;'>".number_format($order_goods_cnt_sum,0)."</td>
    <td class=e_td style='padding-right:20px;'>".number_format(($order_goods_cnt_sum > 0 ? $login_total_cnt_sum/$order_goods_cnt_sum*100:0),1)."</td>
    <td class=e_td style='padding-right:20px;'>".number_format(($order_goods_cnt_sum > 0 ? $ptprice_sum/$order_goods_cnt_sum:0))."</td>
    <td class=e_td style='padding-right:20px;'>".number_format($order_goods_cnt_sum)."%</td>
    <td class=e_td style='padding-right:20px;'>".number_format(($order_goods_cnt_sum > 0 ? $login_total_cnt_sum/$order_goods_cnt_sum:0),0)."</td>
    <td class=e_td style='padding-right:20px;'>".number_format(($order_goods_cnt_sum > 0 ? $ptprice_sum/$order_goods_cnt_sum:0),0)."</td>
    <td class=e_td style='padding-right:20px;'>".number_format(($login_total_cnt_sum > 0 ? $ptprice_sum/$login_total_cnt_sum:0),0)."</td>
    <td class=e_td style='padding-right:20px;'>".number_format($ptprice_sum,0)."</td>
    <td class=e_td style='padding-right:20px;'>-</td>
    </tr>\n";
    */
    /*
    $mstring = str_replace("{login_total_cnt_sum}",$login_total_cnt_sum, $mstring);
    $mstring = str_replace("{nview_cnt_sum}",$nview_cnt_sum, $mstring);
    $mstring = str_replace("{order_goods_cnt_sum}",$order_goods_cnt_sum, $mstring);
    $mstring = str_replace("{login_total_cnt_sum/order_goods_cnt_sum}",number_format(($order_goods_cnt_sum > 0 ? $login_total_cnt_sum/$order_goods_cnt_sum*100:0),1), $mstring);
    $mstring = str_replace("{ptprice_sum/order_goods_cnt_sum}",number_format(($order_goods_cnt_sum > 0 ? $ptprice_sum/$order_goods_cnt_sum:0)), $mstring);
    $mstring = str_replace("{order_goods_cnt_sum}",$order_goods_cnt_sum, $mstring);
    $mstring = str_replace("{login_total_cnt_sum/order_goods_cnt_sum}",number_format(($order_goods_cnt_sum > 0 ? $login_total_cnt_sum/$order_goods_cnt_sum:0),0), $mstring);
    $mstring = str_replace("{ptprice_sum/order_goods_cnt_sum}",number_format(($order_goods_cnt_sum > 0 ? $ptprice_sum/$order_goods_cnt_sum:0),0), $mstring);
    $mstring = str_replace("{ptprice_sum/login_total_cnt_sum}",number_format(($login_total_cnt_sum > 0 ? $ptprice_sum/$login_total_cnt_sum:0),0), $mstring);
    $mstring = str_replace("{ptprice_sum}",number_format($ptprice_sum,0), $mstring);
    */


    $mstring .= "</table>\n
	<table>
		<tr><td>".$str_page_bar."</td></tr>
	</table>";

    $query_string =$_SERVER["QUERY_STRING"];
    //echo $query_string;
    //$query_string = str_replace("max=".$_GET["max"],"",$query_string);
    //$query_string = str_replace("page=".$_GET["page"],"",$query_string);


    $query_string = str_replace("nset=".$_GET["nset"]."&page=".$_GET["page"]."&","",$query_string);
    $query_string = "&".$query_string;

    $mstring .= "<table cellpadding=0 cellspacing=0 width=100%  >
						<tr height=50><td colspan=6 align='center' >&nbsp;".page_bar($total, $page, $max,$query_string,"")."&nbsp;</td></tr>";
    $mstring .= "</table>\n";

    $mstring .= "</form>";
    return $mstring;
}

function ReportTable1($vdate,$SelectReport=1){
    global  $search_type, $search_text,	$search_sdate, $search_edate;
//	global $where;

    $pageview01 = 0;
    $fordb = new forbizDatabase();
    $fordb2 = new forbizDatabase();

    if(!$_GET["sdate"]){
        $search_sdate = date("Ymd", time()-86400*7);
    }else{
        $search_sdate = $_GET["sdate"];
    }

    if(!$_GET["edate"]){
        $search_edate = date("Ymd");
    }else{
        $search_edate = $_GET["edate"];
    }

    if($search_type && $search_text){
        if($search_type == "all"){
            $search_str = "and (cu.id LIKE '%$search_text%' or cmd.name LIKE '%$search_text%')";
        }else{

            if($search_type == "cmd.name"){
                $search_str = "and AES_DECRYPT(UNHEX(cmd.name),'".$fordb->ase_encrypt_key."')  LIKE '%$search_text%' ";
            }else{
                $search_str = "and $search_type LIKE '%$search_text%' ";
            }
        }

        $sql = 	"SELECT distinct v.ucode, v.pid, p.pname, AES_DECRYPT(UNHEX(cmd.name),'".$fordb->ase_encrypt_key."') as name, cu.id as userid, v.nview_cnt , v.vdate, p.coprice, p.sellprice, p.stock, v.nview_cnt , v.vdate
				FROM ".TBL_COMMERCE_VIEWINGVIEW." v, ".TBL_SHOP_PRODUCT." p, ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd
				where  v.pid = p.id and ".($fordb->dbms_type == "oracle" ? "v.ucode is not null " : " v.ucode != '' " )." and v.vdate between '$search_sdate' and '$search_edate'
				and v.ucode = cmd.code and cu.code=cmd.code ".$search_str."
				".$where."
				order by v.vdate desc
				LIMIT 0,100";


        //echo nl2br($sql);
        //exit;


        $fordb->query($sql);
    }

    $dateString = "기간 : ". getNameOfWeekday(date("w",mktime(0,0,0,substr($search_sdate,4,2),substr($search_sdate,6,2),substr($search_sdate,0,4))),$search_sdate, "priodname")."~".getNameOfWeekday(date("w",mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4))),$search_edate,"priodname");


    $mstring = $mstring.TitleBar("상품 조회정보",$dateString, false);


    $mstring = $mstring."<table cellpadding=0 cellspacing=0 width=100% class='list_table_box'>\n
	<col width='3%'> 
						<col width='7%' >
						<col width=7% nowrap>
						<col width=7% nowrap>
						<col width='*' nowrap>
						<col width=7% nowrap>
						<col width='7%' >
						<col width='7%' >
						<col width='7%' >
						<col width='7%' > ";
    $mstring = $mstring."<tr height=25 align=center style='font-weight:bold'>
	<td class=s_td >번호</td>
	<td class=m_td>날짜</td>
	<td class=m_td>회원명</td>
	<td class=m_td nowrap>회원아이디</td>
	<td class=m_td nowrap>상품명</td>
	<td class=m_td nowrap>조회횟수</td>
	<td class=m_td nowrap>원가</td>
	<td class=m_td nowrap>판매가</td>
	<td class=e_td nowrap>마진율(%)</td> 
	</tr>\n";

    if($fordb->total == 0){
        if($search_type && $search_text){
            $result_message = " '$search_sdate' ~ '$search_edate' 기간동안  <b>'$search_text'</b> 검색어로 검색된 결과값이 없습니다. ";
        }else if($search_sdate && $search_edate){
            $result_message = " '$search_sdate' ~ '$search_edate' 기간동안   검색된 결과값이 없습니다.";
        }else{
            $result_message = "결과값이 없습니다.";
        }
        $mstring = $mstring."<tr height=100 bgcolor=#ffffff align=center><td colspan=9>$result_message</td></tr>\n";
    }else{

        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);

            $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i' onmouseover=\"mouseOnTD('".$i."',true,'Report')\" onmouseout=\"mouseOnTD('$i',false,'Report')\">
			<td bgcolor=#efefef style='padding-left:5px;padding-right:5px' align=center>".($i+1)." </td>
			<td bgcolor=#ffffff align=center nowrap>".ReturnDateFormat($fordb->dt['vdate'])." </td>
			<td bgcolor=#efefef align=center>".$fordb->dt['name']."</td>
			<td bgcolor=#ffffff align=center><a href=\"javascript:PoPWindow('../../member/member_view.php?code=".$fordb->dt['ucode']."',950,700,'member_view')\">".$fordb->dt['userid']."</a></td>
			<td bgcolor=#efefef align=left style='padding-left:10px;'>".$fordb->dt['pname']."</td>
			<td bgcolor=#ffffff align=center style='padding-left:20px'>".$fordb->dt['nview_cnt']."</td>
			<td class='list_box_td list_bg_gray'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['coprice'],0)."</td>
			<td class='list_box_td'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['sellprice'],0)."</td>
			<td class='list_box_td list_bg_gray'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt['sellprice']-$fordb->dt['coprice'])/$fordb->dt['sellprice']*100,0)."%</td>
			<!--td bgcolor=#ffffff align=center style='padding-left:20px'>".number_format($fordb->dt['vprice'],0)."</td-->
			</tr>";

        }
    }
    $mstring = $mstring."</table>\n";



    return $mstring;
}

function ReportTable2($vdate,$SelectReport=1){
    global $search_type, $search_text, 	$search_sdate, $search_edate;
    $pageview01 = 0;
    $fordb = new forbizDatabase();


    if($SelectReport == ""){
        $SelectReport = 1;
    }

    if(!$_GET["sdate"]){
        $search_sdate = date("Ymd", time()-86400*7);
    }else{
        $search_sdate = $_GET["sdate"];
    }

    if(!$_GET["edate"]){
        $search_edate = date("Ymd");
    }else{
        $search_edate = $_GET["edate"];
    }

    if($search_type && $search_text){
        $sql = "Select AES_DECRYPT(UNHEX(cmd.name),'".$fordb->ase_encrypt_key."') as name, cu.id as userid,ucode, c.pname,  a.vdate, a.vtime, a.vquantity, a.vprice,
				case when a.step6 = 1 then '구매완료' when a.step3 = 1 then '결제정보확인' when a.step2 = 1 then '결제정보입력' when a.step1 = 1 then '쇼핑카트' end as exitname
				from ".TBL_COMMERCE_SALESTACK." a, ".TBL_SHOP_PRODUCT." c  , ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd
				where $search_type LIKE '%$search_text%' and a.ucode = cmd.code and a.pid = c.id and cu.code = cmd.code and vdate between '$search_sdate' and '$search_edate' and step6 <> 1
				order by a.vdate, vtime";
        //echo $sql;



        $fordb->query($sql);
    }

    $dateString = "기간 : ". getNameOfWeekday(date("w",mktime(0,0,0,substr($search_sdate,4,2),substr($search_sdate,6,2),substr($search_sdate,0,4))),$search_sdate, "priodname")."~".getNameOfWeekday(date("w",mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4))),$search_edate,"priodname");

    $mstring = $mstring.TitleBar("구매단계이탈정보",$dateString, false);
    $mstring = $mstring."<table cellpadding=0 cellspacing=0 width=100% class='list_table_box'>\n";
    $mstring = $mstring."<tr height=25 align=center style='font-weight:bold'><td class=s_td width=15%>시간</td><td class=m_td width=10%>회원명</td><td class=m_td width=15% nowrap>회원아이디</td><td class=m_td width=10% nowrap>이탈단계</td><td class=m_td width='*' nowrap>상품명</td><td class=m_td width=5% nowrap>갯수</td><td class=e_td width=10% nowrap>단가</td></tr>\n";

    if($fordb->total == 0){
        if($search_type && $search_text){
            $result_message = " '$search_sdate' ~ '$search_edate' 기간동안  <b>'$search_text'</b> 검색어로 검색된 결과값이 없습니다.";
        }else if($search_sdate && $search_edate){
            $result_message = " '$search_sdate' ~ '$search_edate' 기간동안   검색된 결과값이 없습니다.";
        }else{
            $result_message = "결과값이 없습니다.";
        }
        $mstring = $mstring."<tr height=100 bgcolor=#ffffff align=center><td colspan=7>$result_message</td></tr>\n";
    }else{

        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);

            $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report100$i' onmouseover=\"mouseOnTD('".$i."',true,'Report100')\" onmouseout=\"mouseOnTD('".$i."',false,'Report100')\">
			<td bgcolor=#efefef align=center nowrap>".ReturnDateFormat($fordb->dt['vdate'])." : ".$fordb->dt['vtime']."</td>
			<td bgcolor=#ffffff align=center>".$fordb->dt['name']."</td>
			<td bgcolor=#ffffff align=center><a href=\"javascript:PoPWindow('../../member/member_view.php?code=".$fordb->dt['ucode']."',950,700,'member_view')\">".$fordb->dt['userid']."</a></td>
			<td bgcolor=#efefef align=center>".$fordb->dt['exitname']."</td>
			<td bgcolor=#ffffff align=center>".$fordb->dt['pname']."</td>
			<td bgcolor=#efefef align=center>".$fordb->dt['vquantity']."</td>
			<td bgcolor=#ffffff style='padding:0px 10px 0px 0px' align=right>".number_format($fordb->dt['vprice'],0)."</td>
			</tr>";

        }
    }
    $mstring = $mstring."</table>\n";
//	$mstring = $mstring."<table cellpadding=3 cellspacing=0 width=745  >\n";


    return $mstring;
}


function ReportTable3($vdate,$SelectReport=1){
    global $search_type, $search_text;
    global $search_sdate, $search_edate;
    $pageview01 = 0;
    $fordb = new forbizDatabase();


    if($SelectReport == ""){
        $SelectReport = 1;
    }

    if(!$_GET["sdate"]){
        $search_sdate = date("Ymd", time()-86400*7);
    }else{
        $search_sdate = $_GET["sdate"];
    }

    if(!$_GET["edate"]){
        $search_edate = date("Ymd");
    }else{
        $search_edate = $_GET["edate"];
    }

    if($search_type && $search_text){
        $sql = "Select AES_DECRYPT(UNHEX(cmd.name),'".$fordb->ase_encrypt_key."') as name , cu.id as userid,ucode, c.pname,  a.vdate, a.vtime, a.vquantity, a.vprice,
				case when a.step6 = 1 then '구매완료' when a.step3 = 1 then '결제정보확인' when a.step2 = 1 then '결제정보입력' when a.step1 = 1 then '쇼핑카트' end as exitname
				from ".TBL_COMMERCE_SALESTACK." a, ".TBL_SHOP_PRODUCT." c , ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd
				where $search_type LIKE '%$search_text%' and a.ucode = cmd.code and cu.code=cmd.code and a.pid = c.id and vdate between '".$search_sdate."' and '".$search_edate."' and step6 = 1 order by a.vdate, vtime";




        $fordb->query($sql);
    }

    $dateString = "기간 : ". getNameOfWeekday(date("w",mktime(0,0,0,substr($search_sdate,4,2),substr($search_sdate,6,2),substr($search_sdate,0,4))),$search_sdate, "priodname")."~".getNameOfWeekday(date("w",mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4))),$search_edate,"priodname");


    $mstring = $mstring.TitleBar("구매정보",$dateString, false);
    $mstring = $mstring."<table cellpadding=0 cellspacing=0 width=100% class='list_table_box'>\n";
    $mstring = $mstring."<tr height=25 align=center style='font-weight:bold'><td class=s_td width=15%>시간</td><td class=m_td width=10%>회원명</td><td class=m_td width=15% nowrap>회원아이디</td><td class=m_td width='*' nowrap>상품명</td><td class=m_td width=5% nowrap>구매갯수</td><td class=e_td width=15% nowrap>단가</td></tr>\n";

    if($fordb->total == 0){
        if($search_type && $search_text){
            $result_message = " '$search_sdate' ~ '$search_edate' 기간동안 <b>'$search_text'</b> 검색어로 검색된 결과값이 없습니다.";
        }else if($search_sdate && $search_edate){
            $result_message = " '$search_sdate' ~ '$search_edate' 기간동안   검색된 결과값이 없습니다.";
        }else{
            $result_message = "결과값이 없습니다.";
        }
        $mstring = $mstring."<tr height=100 bgcolor=#ffffff align=center><td colspan=6>$result_message</td></tr>\n";
    }else{

        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);

            $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report300$i' onmouseover=\"mouseOnTD('".$i."',true,'Report300')\" onmouseout=\"mouseOnTD('$i',false,'Report300')\">
			<td bgcolor=#efefef align=center nowrap>".ReturnDateFormat($fordb->dt['vdate'])." : ".$fordb->dt['vtime']."</td>
			<td bgcolor=#ffffff align=center>".$fordb->dt['name']."</td>
			<td bgcolor=#efefef align=center><a href=\"javascript:PoPWindow('../../member/member_view.php?code=".$fordb->dt['ucode']."',950,700,'member_view')\">".$fordb->dt['userid']."</a></td>
			<td bgcolor=#ffffff align=center>".$fordb->dt['pname']."</td>
			<td bgcolor=#efefef align=center>".$fordb->dt['vquantity']."</td>
			<td bgcolor=#ffffff align=center>".number_format($fordb->dt['vprice'],0)."</td>
			</tr>";

        }
    }
    $mstring = $mstring."</table>\n";
//	$mstring = $mstring."<table cellpadding=3 cellspacing=0 width=745  >\n";


    /*
        $help_text = "
    <table cellpadding=1 cellspacing=0 class='small' >
        <col width=8>
        <col width=*>
        <tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' > 검색 조건에 맞는 특정회원에 대한 <u>상품조회정보</u> 및 <u>구매이탈정보</u> , <u>구매정보</u> 내역입니다 이는 고객문의나 개별고객에 대한 마케팅시 해당회원에 대한 성향을 파악하여 좀더 개인화된 정보를 제공하여 마케팅 성공률을 높일수 있습니다</td></tr>
        <tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' > 조회된 아이디를 클릭하면 회원에 대한 상세 정보및 회원에 대한 상담정보나 특이사항등을 일괄 관리할수 있어 회원관리를 좀더 효율적으로 하실수 있습니다</td></tr>
    </table>
    ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("CRM 회원검색", $help_text);
    return $mstring;
}


if ($mode == "iframe"){
    echo "<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>";
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."<br><br><br><br>".ReportTable1($vdate,$SelectReport)."<br><br><br><br>".ReportTable2($vdate,$SelectReport)."<br><br><br><br>".ReportTable3($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value;</Script>";


//	$ca = new Calendar();
//	$ca->LinkPage = 'searchmemberlist.php';

//	echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
//	echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";


}else{



    $Script = "
<Script Language='JavaScript'>


$(function() {
	$(\"#search_sdate\").datepicker({
		monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		showMonthAfterYear:true,
		dateFormat: 'yymmdd',
		buttonImageOnly: true,
		buttonText: '달력',
		onSelect: function(dateText, inst){
			//alert(dateText);
			if($('#search_edate').val() != '' && $('#search_edate').val() <= dateText){
				$('#search_edate').val(dateText);
			}
		}

		});

		$(\"#search_edate\").datepicker({
		monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		showMonthAfterYear:true,
		dateFormat: 'yymmdd',
		buttonImageOnly: true,
		buttonText: '달력'

		});

	 
});

 
function setSelectDate(FromDate,ToDate,dType) {
	var frm = document.search_form;

	$(\"#search_sdate\").val(FromDate);
	$(\"#search_edate\").val(ToDate);
}

</Script>
";

    if(!$_GET["search_sdate"]){
        $search_sdate = date("Ymd", time()-86400*7);
    }

    if(!$_GET["search_edate"]){
        $search_edate = date("Ymd");
    }

    $searchview = "	<table cellpadding=2 cellspacing=0><form name=frmMain target=act onsubmit='return CheckFormValue(this)' ><input type='hidden' name='SubID' value='$SubID'><input type='hidden' name='mode' value='iframe'>
				<tr>
					<td>
						<select name='search_type' validation=true title='검색조건'>
							<option value='all'>전체</option>
							<option value='cu.id'>아이디</option>
							<option value='cmd.name'>이름</option>
						</select></td>
					<td><input type='text' name='search_text' value='$search_type' size=15 validation=true title='검색어'></td>
				</tr>
				<tr>
					<TD width=50 style='text-align:center;' nowrap> 시작날짜</TD>
					<TD colspan=2 nowrap>
						<input type='text' name='search_sdate' id='search_sdate' value='".$search_sdate."' size=15 validation=false title='시작일자' style='text-align:center;'>
					</TD>
				</tr>
				<tr>
					<TD style='text-align:center;' nowrap> 종료날짜</TD>
					<TD colspan=2 nowrap>
					<input type='text' name='search_edate' id='search_edate' value='".$search_edate."' size=15 validation=false title='종료일자' style='text-align:center;'>
					</TD>
				</tr>
				<tr>
					<TD colspan=2 align=right><button onclick='document.frmMain.submit();' style='padding:5px;width:200px;'> search </button></TD>
				</tr></form>
				</table>";

    $searchview = " ";

    if($mmode == "pop"){
        $P = new ManagePopLayOut();
        $P->addScript = $Script;
        $P->strLeftMenu = commerce_munu('searchmemberlist.php',"",$searchview);
        $P->Navigation = "CRM 검색";
        $P->NaviTitle = "CRM 검색";
        $P->strContents = "<br>".ReportTable($vdate,$SelectReport)."<br><br><br><br>"."<br>".ReportTable1($vdate,$SelectReport)."<br><br><br><br>".ReportTable2($vdate,$SelectReport)."<br><br><br><br>".ReportTable3($vdate,$SelectReport)."<br><br>";
        echo $P->PrintLayOut();
    }else{
        $p = new forbizReportPage();
        $p->TopNaviSelect = "step2";
        $p->addScript = "".$Script;
        $p->OnloadFunction = "";
        $p->forbizLeftMenu = commerce_munu('searchmemberlist.php',"",$searchview);
        $p->forbizContents = ReportTable($vdate,$SelectReport)."<br><br><br><br>".ReportTable1($vdate,$SelectReport)."<br><br><br><br>".ReportTable2($vdate,$SelectReport)."<br><br><br><br>".ReportTable3($vdate,$SelectReport);
        $p->Navigation = "이커머스분석 > 고객리스트 > 회원검색";
        $p->title = "회원검색";
        $p->PrintReportPage();
    }


}
























function __ReportTable($vdate,$SelectReport=1){
    global  $search_type, $search_text,	$search_sdate, $search_edate;
//	global $where;

    $pageview01 = 0;
    $fordb = new forbizDatabase();
    $fordb2 = new forbizDatabase();

    if(!$_GET["search_sdate"]){
        $search_sdate = date("Ymd", time()-86400*7);
    }
    if(!$_GET["search_edate"]){
        $search_edate = date("Ymd");
    }

    if($search_type && $search_text){
        if($search_type == "all"){
            $search_str = "and (cu.id LIKE '%$search_text%' or cmd.name LIKE '%$search_text%')";
        }else{

            if($search_type == "cmd.name"){
                $search_str = "and AES_DECRYPT(UNHEX(cmd.name),'".$fordb->ase_encrypt_key."')  LIKE '%$search_text%' ";
            }else{
                $search_str = "and $search_type LIKE '%$search_text%' ";
            }
        }
    }
    $sql = 	"SELECT distinct v.ucode, v.pid, p.pname, AES_DECRYPT(UNHEX(cmd.name),'".$fordb->ase_encrypt_key."') as name, cu.id as userid, v.nview_cnt , v.vdate, p.coprice, p.sellprice, p.stock, v.nview_cnt , v.vdate
				FROM ".TBL_COMMERCE_VIEWINGVIEW." v, ".TBL_SHOP_PRODUCT." p, ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd
				where  v.pid = p.id and ".($fordb->dbms_type == "oracle" ? "v.ucode is not null " : " v.ucode != '' " )." and v.vdate between '$search_sdate' and '$search_edate'
				and v.ucode = cmd.code and cu.code=cmd.code ".$search_str."
				".$where."
				order by v.vdate desc
				LIMIT 0,100";


    echo nl2br($sql);
    //exit;


    $fordb->query($sql);


    //echo $search_edate;

    $dateString = "기간 : ". getNameOfWeekday(date("w",mktime(0,0,0,substr($search_sdate,4,2),substr($search_sdate,6,2),substr($search_sdate,0,4))),$search_sdate, "priodname")."~".getNameOfWeekday(date("w",mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4))),$search_edate,"priodname");


    $mstring = $mstring.TitleBar("상품 조회정보",$dateString, false);

    $mstring .= "<table cellpadding=0 cellspacing=0 width=100%>
	<tr height=150>
		<td   >
			 <form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
			<input type='hidden' name='mode' value='search'>
			<input type='hidden' name='cid2' value='$cid2'>
			<input type='hidden' name='depth' value='$depth'>
			<input type='hidden' name='groupbytype' value='$groupbytype'>
			<input type='hidden' name='sprice' value='0' />
			<input type='hidden' name='eprice' value='1000000' />
			<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'><!---mbox04-->
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05 align=center' style='padding:1px'>
						<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='search_table_box'>
							<col width='15%'>
							<col width='35%'>
							<col width='15%'>
							<col width='35%'>";
    if($_SESSION["admin_config"]['front_multiview'] == "Y"){
        $mstring .= "
					<tr>
						<td class='search_box_title' > 쇼핑몰타입</td>
						<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
					</tr>";
    }
    $mstring .= "
							<tr height=27>
							  <td class='search_box_title'><b>구매일자</b></td>
							  <td class='search_box_item' colspan=3 >
								<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
									<col width=70>
									<col width=20>
									<col width=70>
									<col width=*>
									<tr>
										<TD nowrap>
										<input type='text' name='sdate' class='textbox' value='".$sdate."' style='height:20px;width:70px;text-align:center;' id='start_datepicker'>
										<!--SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년
										<SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월
										<SELECT name=FromDD></SELECT> 일 -->
										</TD>
										<TD align=center> ~ </TD>
										<TD nowrap>
										<input type='text' name='edate' class='textbox' value='".$edate."' style='height:20px;width:70px;text-align:center;' id='end_datepicker'>
										<!--SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년
										<SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월
										<SELECT name=ToDD></SELECT> 일 -->
										</TD>
										<TD style='padding:0px 10px'>
											<a href=\"javascript:setSelectDate('$today','$today',1);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_today.gif'></a>
											<a href=\"javascript:setSelectDate('$vyesterday','$vyesterday',1);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_yesterday.gif'></a>
											<a href=\"javascript:setSelectDate('$voneweekago','$today',1);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_1week.gif'></a>
											<a href=\"javascript:setSelectDate('$v15ago','$today',1);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_15days.gif'></a>
											<a href=\"javascript:setSelectDate('$vonemonthago','$today',1);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_1month.gif'></a>
											<a href=\"javascript:setSelectDate('$v2monthago','$today',1);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_2months.gif'></a>
											<a href=\"javascript:setSelectDate('$v3monthago','$today',1);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_3months.gif'></a>
										</TD>
									</tr>
								</table>
							  </td>
							</tr>
							<tr>
								<td class='search_box_title'><b>전시 카테고리선택</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'>".getCategoryList3("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $cid2)."</td>
											<td style='padding-right:5px;'>".getCategoryList3("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $cid2)."</td>
											<td style='padding-right:5px;'>".getCategoryList3("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $cid2)."</td>
											<td>".getCategoryList3("세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $cid2)."</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr  height=27>
							  <td class='search_box_title' width='13%' bgcolor='#efefef' align=center >연령대</td>
							  <td class='search_box_item' width='*' align=left style='padding-left:5px;'  >
							  
								  <input type='checkbox' name='age[]' id='age_' value='' ".CompareReturnValue("",$age,"checked")."><label for='age_'>전체</label>
								  <input type='checkbox' name='age[]' id='age_10' value='10'  ".CompareReturnValue("10",$age,"checked")."><label for='age_10'  style='width:200px;'>~10대</label>
								  <input type='checkbox' name='age[]' id='age_20' value='20'  ".CompareReturnValue("20",$age,"checked")."><label for='age_20' >20대</label>
								  <input type='checkbox' name='age[]' id='age_30' value='30'  ".CompareReturnValue("30",$age,"checked")."><label for='age_30' >30대</label>
								  <input type='checkbox' name='age[]' id='age_40' value='40'  ".CompareReturnValue("40",$age,"checked")."><label for='age_40' >40대</label>
								  <input type='checkbox' name='age[]' id='age_50' value='50'  ".CompareReturnValue("50",$age,"checked")."><label for='age_50'  >50대</label>
								  <input type='checkbox' name='age[]' id='age_60' value='60'  ".CompareReturnValue("60",$age,"checked")."><label for='age_60' >ETC</label>
								</select>
							  </td> 
								<td class='search_box_title' bgcolor='#efefef' align=center>성별검색 </td>
								  <td class='search_box_item' align=left style='padding-left:5px;'>
								  <input type=radio name='sex' value='' id='sex_all'  ".CompareReturnValue("0",$sex,"checked")." checked><label for='sex_all'>모두</label>
								  <input type=radio name='sex' value='M' id='sex_man'  ".CompareReturnValue("M",$sex,"checked")."><label for='sex_man'>남자</label>
								  <input type=radio name='sex' value='W' id='sex_women' ".CompareReturnValue("W",$sex,"checked")."><label for='sex_women'>여자</label>
								</td>
							</tr>
							";
    if(false){
        $mstring .= "
							<tr  height=27>
							  <td class='search_box_title' width='13%' bgcolor='#efefef' align=center >지역선택</td>
							  <td class='search_box_item' width='*' align=left style='padding-left:5px;'>
							  <select name='region' >
								  <option value=''>-- 선택 --</option>
								  <option value='서울'  ".CompareReturnValue("서울",$region,"selected").">서울</option>
								  <option value='충북'  ".CompareReturnValue("충북",$region,"selected").">충북</option>
								  <option value='충남'  ".CompareReturnValue("충남",$region,"selected").">충남</option>
								  <option value='전북'  ".CompareReturnValue("전북",$region,"selected").">전북</option>
								  <option value='제주'  ".CompareReturnValue("제주",$region,"selected").">제주</option>
								  <option value='전남'  ".CompareReturnValue("전남",$region,"selected").">전남</option>
								  <option value='경북'  ".CompareReturnValue("경북",$region,"selected").">경북</option>
								  <option value='경남'  ".CompareReturnValue("경남",$region,"selected").">경남</option>
								  <option value='경기'  ".CompareReturnValue("경기",$region,"selected").">경기</option>
								  <option value='부산'  ".CompareReturnValue("부산",$region,"selected").">부산</option>
								  <option value='대구'  ".CompareReturnValue("대구",$region,"selected").">대구</option>
								  <option value='인천'  ".CompareReturnValue("인천",$region,"selected").">인천</option>
								  <option value='광주'  ".CompareReturnValue("광주",$region,"selected").">광주</option>
								  <option value='대전'  ".CompareReturnValue("대전",$region,"selected").">대전</option>
								  <option value='울산'  ".CompareReturnValue("울산",$region,"selected").">울산</option>
								  <option value='강원'  ".CompareReturnValue("강원",$region,"selected").">강원</option>
								</select>
							  </td>
							<td class='search_box_title' bgcolor='#efefef' align=center>성별검색 </td>
							  <td class='search_box_item' align=left style='padding-left:5px;'>
							  <input type=radio name='sex' value='' id='sex_all'  ".CompareReturnValue("0",$sex,"checked")." checked><label for='sex_all'>모두</label>
							  <input type=radio name='sex' value='M' id='sex_man'  ".CompareReturnValue("M",$sex,"checked")."><label for='sex_man'>남자</label>
							  <input type=radio name='sex' value='W' id='sex_women' ".CompareReturnValue("W",$sex,"checked")."><label for='sex_women'>여자</label>
							</td>
							</tr>
							";
    }
    if(false){
        $mstring .= "
							<tr>
								<td class='search_box_title'><b>제휴사 카테고리선택</b></td>
								<td class='search_box_item' colspan=3>
									<select name='company_cid1' id='com_cid1' onchange='loadComCategory(this);'><option value=''>대분류</option>".getCompanyCateSelectBoxOption(1,$company_cid1)."</select>
									";
        if(!empty($company_cid)){
            $mstring .="
													<select name='company_cid' id='com_cid2'>".getCompanyCateSelectBoxOption(2,$company_cid)."</select>";
        }else{
            $mstring .="
													<select name='company_cid' id='com_cid2'><option value=''>중분류</option></select>";
        }
        $mstring .=	"
								</td>
							</tr>";
    }
    $mstring .= "
							<tr>
								<td class='search_box_title'><b>브랜드</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../code_search.php?search_type=brand',600,380,'code_search')\"  style='cursor:pointer;'>
											<img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"$('#brand_code').val('');\" style='cursor:pointer;' alt='삭제' title='삭제'/>
											</td>
											<td><input type=text class='textbox' name='brand_code' id='brand_code' value='".$brand_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>상품코드</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../code_search.php?search_type=product',600,380,'code_search')\"  style='cursor:pointer;'>
											<img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"$('#product_code').val('');\" style='cursor:pointer;' alt='삭제' title='삭제'/>
											</td>
											<td><input type=text class='textbox' name='product_code' id='product_code' value='".$product_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>";
    if($admininfo['admin_level'] == 9){
        $mstring .="<tr>
								<td class='search_box_title'><b>셀러업체</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../code_search.php?search_type=company_v',600,380,'code_search')\"  style='cursor:pointer;'>
											<img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"$('#company_v_code').val('');\" style='cursor:pointer;' alt='삭제' title='삭제'/>
											</td>
											<td><input type=text class='textbox' name='company_v_code' id='company_v_code' value='".$company_v_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>매입업체</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../code_search.php?search_type=trade_company',600,380,'code_search')\"  style='cursor:pointer;'>
											<img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"$('#trade_company_code').val('');\" style='cursor:pointer;' alt='삭제' title='삭제'/>
											</td>
											<td><input type=text class='textbox' name='trade_company_code' id='trade_company_code' value='".$trade_company_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>";
    }
    if(false){
        $mstring .="<tr>
								<td class='search_box_title'><b>프로모션(카드)</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../code_search.php?search_type=promotion_card',600,380,'code_search')\"  style='cursor:pointer;'></td>
											<td><input type=text class='textbox' name='promotion_card_code' id='promotion_card_code' value='".$promotion_card_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>";
    }

    $mstring .="
							<tr>
								<td class='search_box_title'><b>프로모션(쿠폰)</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../code_search.php?search_type=promotion_cupon',600,380,'code_search')\"  style='cursor:pointer;'>
											<img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"$('#promotion_cupon_code').val('');\" style='cursor:pointer;' alt='삭제' title='삭제'/>
											</td>
											<td><input type=text class='textbox' name='promotion_cupon_code' id='promotion_cupon_code' value='".$promotion_cupon_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>";
    if($admininfo['admin_id'] == "forbiz"){
        if($admininfo['admin_level'] == 9 && false){
            $mstring .="<tr>
								<td class='search_box_title'><b>제휴사</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../code_search.php?search_type=company_j',600,380,'code_search')\"  style='cursor:pointer;'>
											<img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"$('#company_j_code').val('');\" style='cursor:pointer;' alt='삭제' title='삭제'/>
											</td>
											<td><input type=text class='textbox' name='company_j_code' id='company_j_code' value='".$company_j_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>";
        }
    }
    $mstring .="
							<!--tr>
								<td class='search_box_title'><b>주문상태</b></td>
								<td class='search_box_item' colspan=3>
								<input type='checkbox' name='status[]'  id='status_0' value=\"DC','BF\" ".CompareReturnValue("DC','BF", $status, " checked")."><label for='status_0'>주문</label>
								<input type='checkbox' name='status[]'  id='status_1' value='CC' ".CompareReturnValue("CC", $status, " checked")."><label for='status_1'>취소</label>
								<input type='checkbox' name='status[]'  id='status_2' value='SO' ".CompareReturnValue("SO", $status, " checked")."><label for='status_2'>품절</label>
								<input type='checkbox' name='status[]'  id='status_3' value='FC' ".CompareReturnValue("FC", $status, " checked")."><label for='status_3'>환불</label>
								</td>
							</tr-->
							";

    $mstring .=	"
							 
						</table>
					</td>
					<th class='box_06'></th>
				</tr>
				<tr>
					<th class='box_07'></th>
					<td class='box_08'></td>
					<th class='box_09'></th>
				</tr>
				<tr >
					<td colspan=2 align=center style='padding:10px 0px'><input type=image src='../../images/".$_SESSION["admininfo"]["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
	</table>";

    $mstring = $mstring."<table cellpadding=0 cellspacing=0 width=100% class='list_table_box'>\n
	<col width='3%'> 
						<col width='7%' >
						<col width=7% nowrap>
						<col width=7% nowrap>
						<col width='*' nowrap>
						<col width=7% nowrap>
						<col width='7%' >
						<col width='7%' >
						<col width='7%' >
						<col width='7%' > ";
    $mstring = $mstring."<tr height=25 align=center style='font-weight:bold'>
	<td class=s_td >번호</td>
	<td class=m_td>날짜</td>
	<td class=m_td>회원명</td>
	<td class=m_td nowrap>회원아이디</td>
	<td class=m_td nowrap>상품명</td>
	<td class=m_td nowrap>조회횟수</td>
	<td class=m_td nowrap>원가</td>
	<td class=m_td nowrap>판매가</td>
	<td class=e_td nowrap>마진율(%)</td> 
	</tr>\n";

    if($fordb->total == 0){
        if($search_type && $search_text){
            $result_message = " '$search_sdate' ~ '$search_edate' 기간동안  <b>'$search_text'</b> 검색어로 검색된 결과값이 없습니다. <br>".nl2br($sql)." ";
        }else if($search_sdate && $search_edate){
            $result_message = " '$search_sdate' ~ '$search_edate' 기간동안   검색된 결과값이 없습니다. $sql ";
        }else{
            $result_message = "결과값이 없습니다.";
        }
        $mstring = $mstring."<tr height=100 bgcolor=#ffffff align=center><td colspan=6>$result_message</td></tr>\n";
    }else{

        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);

            $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i' onmouseover=\"mouseOnTD('".$i."',true,'Report')\" onmouseout=\"mouseOnTD('$i',false,'Report')\">
			<td bgcolor=#efefef style='padding-left:5px;padding-right:5px' align=center>".($i+1)." </td>
			<td bgcolor=#ffffff align=center nowrap>".ReturnDateFormat($fordb->dt['vdate'])." </td>
			<td bgcolor=#efefef align=center>".$fordb->dt['name']."</td>
			<td bgcolor=#ffffff align=center><a href=\"javascript:PoPWindow('../../member/member_view.php?code=".$fordb->dt['ucode']."',950,700,'member_view')\">".$fordb->dt['userid']."</a></td>
			<td bgcolor=#efefef align=left style='padding-left:10px;'>".$fordb->dt['pname']."</td>
			<td bgcolor=#ffffff align=center style='padding-left:20px'>".$fordb->dt['nview_cnt']."</td>
			<td class='list_box_td list_bg_gray'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['coprice'],0)."</td>
			<td class='list_box_td'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['sellprice'],0)."</td>
			<td class='list_box_td list_bg_gray'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt['sellprice']-$fordb->dt['coprice'])/$fordb->dt['sellprice']*100,0)."%</td>
			<!--td bgcolor=#ffffff align=center style='padding-left:20px'>".number_format($fordb->dt['vprice'],0)."</td-->
			</tr>";

        }
    }
    $mstring = $mstring."</table>\n";



    return $mstring;
}


?>
