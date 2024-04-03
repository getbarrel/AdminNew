<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../report/etcreferer.chart.php");
include("../include/campaign.lib.php");


function ReportTable($vdate,$SelectReport=1){
    global $LargeImageSize;
    global $search_sdate, $search_edate, $report_type, $report_group_type;



    $order_sale_sum = 0;
    $real_sale_coprice_sum = 0;
    $sale_all_sum = 0;
    $cancel_sale_sum = 0;
    $margin_sum = 0;

    $pageview01 = 0;
    $fordb = new forbizDatabase();
    $fordb2 = new forbizDatabase();

    /*
    $sql = "select * from ".TBL_COMMERCE_SALESTACK." s where step6 = '1' order by vdate asc   ";
    $fordb->query($sql);
    $order_goods = $fordb->fetchall();
    //echo count($order_goods);
    //exit;

    for($i=0;$i < count($order_goods);$i++){

        $sql = "update  ".TBL_COMMERCE_SALESTACK." set is_order = 1 where ucode = '".$order_goods[$i]['ucode']."' and pid = '".$order_goods[$i]['pid']."' and vdate <= '".$order_goods[$i]['vdate']."'  ";
        echo $sql."<br>";

        $fordb->query($sql);

    }
    echo "완료";
    exit;
    */

    if($SelectReport == ""){
        $SelectReport = 1;
    }

    if($vdate == ""){
        $vdate = date("Ymd", time());
        $selected_date = date("Ymd", time());
        $vyesterday = date("Ymd", time()-84600);
        $voneweekago = date("Ymd", time()-84600*7);
        $search_sdate = date("Y-m-d", time()-84600*7);
        $search_edate = date("Y-m-d", time());
    }else{
        if($SelectReport ==3){
            $vdate = $vdate."01";
        }
        $selected_date = date("Ymd", mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)));
        $vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
        $vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
    }



    if($SelectReport == 1){
        if($fordb->dbms_type == "oracle"){//uid 안됨
            $sql = "Select cu.code, cmd.name, cu.id as userid,c.id, c.pname, c.brand_name, c.coprice, c.sellprice, c.stock,  a.vdate, a.vtime, a.vquantity, a.vprice,
			case when a.step6 = 1 then '구매완료' when a.step3 = 1 then '결제정보확인' when a.step2 = 1 then '결제정보입력' when a.step1 = 1 then '쇼핑카트' end as exitname
			from ".TBL_COMMERCE_SALESTACK." a, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_COMMON_USER." cu, ".TBL_SHOP_PRODUCT." c 
			where a.ucode = cmd.code and cu.code = cmd.code and a.pid = c.id and vdate = '$vdate' and step6 <> 1 
			and is_order = 0
			order by a.vdate, vtime";
        }else{
            $sql = "Select cu.code, AES_DECRYPT(UNHEX(IFNULL(cmd.name,'-')),'".$fordb->ase_encrypt_key."') as name , cu.id as userid,c.id, c.pname, c.brand_name, c.coprice, c.sellprice, c.stock,   a.vdate, a.vtime, a.vquantity, a.vprice,
			case when a.step6 = 1 then '구매완료' when a.step3 = 1 then '결제정보확인' when a.step2 = 1 then '결제정보입력' when a.step1 = 1 then '쇼핑카트' end as exitname
			from ".TBL_COMMERCE_SALESTACK." a, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_COMMON_USER." cu, ".TBL_SHOP_PRODUCT." c 
			where a.ucode = cmd.code and cu.code = cmd.code and a.pid = c.id and vdate = '$vdate' and step6 <> 1 
			and is_order = 0
			order by a.vdate, vtime";
        }
        //$sql = "Select cmd.name, cu.id as uid,a.* from ".TBL_COMMERCE_SALESTACK." a left outer join ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_COMMON_USER." cu on a.ucode = cmd.code where  vdate = '$vdate' and step6 <> 1 ";
        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2 || $SelectReport == 4){
        if($SelectReport == "4"){
            $vdate = $search_sdate;
            $vweekenddate = $search_edate;
        }

        if($fordb->dbms_type == "oracle"){
            $sql = "Select cu.code, IFNULL(cmd.name,'-') as name, cu.id as userid,c.id, c.pname, c.brand_name, c.coprice, c.sellprice, c.stock,    a.vdate, a.vtime, a.vquantity, a.vprice,
		case when a.step6 = 1 then '구매완료' when a.step3 = 1 then '결제정보확인' when a.step2 = 1 then '결제정보입력' when a.step1 = 1 then '쇼핑카트' end as exitname
		 from ".TBL_COMMERCE_SALESTACK." a, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_COMMON_USER." cu, ".TBL_SHOP_PRODUCT." c 
		 where a.ucode = cmd.code and cu.code = cmd.code and a.pid = c.id and vdate between '$vdate' and '$vweekenddate' and step6 <> 1 
		 and is_order = 0
		 order by a.vdate, vtime";
        }else{
            $sql = "Select cu.code, AES_DECRYPT(UNHEX(IFNULL(cmd.name,'-')),'".$fordb->ase_encrypt_key."') as name, cu.id as userid,c.id, c.pname, c.brand_name, c.coprice, c.sellprice, c.stock,    a.vdate, a.vtime, a.vquantity, a.vprice,
		case when a.step6 = 1 then '구매완료' when a.step3 = 1 then '결제정보확인' when a.step2 = 1 then '결제정보입력' when a.step1 = 1 then '쇼핑카트' end as exitname
		 from ".TBL_COMMERCE_SALESTACK." a, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_COMMON_USER." cu, ".TBL_SHOP_PRODUCT." c 
		 where a.ucode = cmd.code and cu.code = cmd.code and a.pid = c.id and vdate between '$vdate' and '$vweekenddate' and step6 <> 1 
		 and is_order = 0
		 order by a.vdate, vtime";
        }
        $group_title = "날짜";
        if($SelectReport == 2){
            $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
        }else if($SelectReport == 4){
            //echo $search_sdate;
            $s_week_num = date("w",mktime(0,0,0,substr($search_sdate,4,2),substr($search_sdate,6,2),substr($search_sdate,0,4)));
            $e_week_num = date("w",mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4)));
            $dateString = "기간별 : ".getNameOfWeekday($s_week_num,$search_sdate,"priodname")."~".getNameOfWeekday($e_week_num,$search_edate,"priodname");
        }
    }else if($SelectReport == 3){
        if($fordb->dbms_type == "oracle"){
            $sql = "Select cu.code, IFNULL(cmd.name,'-') as name, cu.id as userid,c.id, c.pname, c.brand_name, c.coprice, c.sellprice, c.stock,    a.vdate, a.vtime, a.vquantity, a.vprice,
			case when a.step6 = 1 then '구매완료' when a.step3 = 1 then '결제정보확인' when a.step2 = 1 then '결제정보입력' when a.step1 = 1 then '쇼핑카트' end as exitname
			 from ".TBL_COMMERCE_SALESTACK." a, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_COMMON_USER." cu, ".TBL_SHOP_PRODUCT." c
			 where a.ucode = cmd.code and cu.code = cmd.code and a.pid = c.id and vdate between '".substr($selected_date,0,6)."01' and '".substr($selected_date,0,6)."31' and step6 <> 1 
			 and is_order = 0
			 order by a.vdate, vtime";

        }else{
            $sql = "Select cu.code, AES_DECRYPT(UNHEX(IFNULL(cmd.name,'-')),'".$fordb->ase_encrypt_key."') as name, cu.id as userid,c.id, c.pname, c.brand_name, c.coprice, c.sellprice, c.stock,    a.vdate, a.vtime, a.vquantity, a.vprice,
			case when a.step6 = 1 then '구매완료' when a.step3 = 1 then '결제정보확인' when a.step2 = 1 then '결제정보입력' when a.step1 = 1 then '쇼핑카트' end as exitname
			 from ".TBL_COMMERCE_SALESTACK." a, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_COMMON_USER." cu, ".TBL_SHOP_PRODUCT." c
			 where a.ucode = cmd.code and cu.code = cmd.code and a.pid = c.id and vdate between '".substr($selected_date,0,6)."01' and '".substr($selected_date,0,6)."31' and step6 <> 1 
			 and is_order = 0
			 order by a.vdate, vtime";
        }
        $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");
    }

    //echo $sql;
    $fordb->query($sql);




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

        $sheet->getActiveSheet(0)->mergeCells('A2:K2');
        $sheet->getActiveSheet(0)->setCellValue('A2', "상품군별분석");
        $sheet->getActiveSheet(0)->mergeCells('A3:K3');
        $sheet->getActiveSheet(0)->setCellValue('A3', $dateString);
        /*
        $sheet->getActiveSheet(0)->mergeCells('A'.($i+1).':A'.($i+3));
        $sheet->getActiveSheet(0)->mergeCells('B'.($i+1).':B'.($i+3));
        $sheet->getActiveSheet(0)->mergeCells('C'.($i+1).':D'.($i+1));

        $sheet->getActiveSheet(0)->mergeCells('E'.($i+1).':L'.($i+1));
        $sheet->getActiveSheet(0)->mergeCells('M'.($i+1).':M'.($i+3));
        $sheet->getActiveSheet(0)->mergeCells('N'.($i+1).':O'.($i+2));

        $sheet->getActiveSheet(0)->mergeCells('C'.($i+2).':D'.($i+2));
        $sheet->getActiveSheet(0)->mergeCells('E'.($i+2).':F'.($i+2));
        $sheet->getActiveSheet(0)->mergeCells('G'.($i+2).':H'.($i+2));
        $sheet->getActiveSheet(0)->mergeCells('I'.($i+2).':J'.($i+2));
        $sheet->getActiveSheet(0)->mergeCells('K'.($i+2).':L'.($i+2));
        */
        $sheet->getActiveSheet(0)->setCellValue('A' . ($i+1), "시간");
        $sheet->getActiveSheet(0)->setCellValue('B' . ($i+1), "회원명");
        $sheet->getActiveSheet(0)->setCellValue('C' . ($i+1), "회원아이디");
        $sheet->getActiveSheet(0)->setCellValue('E' . ($i+1), "이탈단계");
        $sheet->getActiveSheet(0)->setCellValue('F' . ($i+1), "상품명");
        $sheet->getActiveSheet(0)->setCellValue('G' . ($i+1), "구매이탈수");
        $sheet->getActiveSheet(0)->setCellValue('H' . ($i+1), "원가");
        $sheet->getActiveSheet(0)->setCellValue('I' . ($i+1), "판매가");
        $sheet->getActiveSheet(0)->setCellValue('J' . ($i+1), "마진율");
        $sheet->getActiveSheet(0)->setCellValue('K' . ($i+1), "재고");

        $sheet->setActiveSheetIndex(0);
        //$i = $i + 2;

        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);


            $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 2), ($i + 1));
            $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 2), ($fordb->dt['ucode']));
            $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 2), ReturnDateFormat($fordb->dt['vdate'])." : ".$fordb->dt['vtime']);
            //$sheet->getActiveSheet()->getStyle('C' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 2), $fordb->dt['name']);
            //$sheet->getActiveSheet()->getStyle('D' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 2), $fordb->dt['userid']);
            //$sheet->getActiveSheet()->getStyle('E' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('F' . ($i + $start + 2), $fordb->dt['exitname']);
            //$sheet->getActiveSheet()->getStyle('F' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('G' . ($i + $start + 2), $fordb->dt['pname']);
            //$sheet->getActiveSheet()->getStyle('G' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('H' . ($i + $start + 2), $fordb->dt['vquantity']);
            $sheet->getActiveSheet()->getStyle('H' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('I' . ($i + $start + 2), $fordb->dt['coprice']);
            $sheet->getActiveSheet()->getStyle('I' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('J' . ($i + $start + 2), $fordb->dt['sellprice']);
            $sheet->getActiveSheet()->getStyle('J' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('K' . ($i + $start + 2), $fordb->dt['stock']);
            $sheet->getActiveSheet()->getStyle('K' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);



            $vquantity = $vquantity + returnZeroValue($fordb->dt['vquantity']);
            $order_sale_sum = $order_sale_sum + returnZeroValue($fordb->dt['order_sale_sum']);

            $coprice = $coprice + returnZeroValue($fordb->dt['coprice']);
            //$sale_all_sum = $sale_all_sum + returnZeroValue($fordb->dt['sale_all_sum']);

            $sellprice = $sellprice + returnZeroValue($fordb->dt['sellprice']);
            //$cancel_sale_sum = $cancel_sale_sum + returnZeroValue($fordb->dt['cancel_sale_sum']);

            $stock = $stock + returnZeroValue($fordb->dt['stock']);



        }

        if($real_sale_coprice_sum > 0){
            $margin_sum_rate = $margin_sum/$real_sale_coprice_sum;// 엑셀 포맷 정의시 자동으로 *100 이 처리됨
        }else{
            $margin_sum_rate = 0;
        }

        //$i++;
        $sheet->getActiveSheet(0)->mergeCells('A'.($i + $start + 2).':E'.($i+ $start+2));
        $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 2), '합계');
        //$sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 2), getIventoryCategoryPathByAdmin($goods_infos[$i]['cid'], 4));

        $sheet->getActiveSheet()->setCellValue('F' . ($i + $start + 2), $sale_all_sum);
        $sheet->getActiveSheet()->getStyle('F' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('G' . ($i + $start + 2), $sellprice);
        $sheet->getActiveSheet()->getStyle('G' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('H' . ($i + $start + 2), $cancel_sale_sum);
        $sheet->getActiveSheet()->getStyle('H' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('I' . ($i + $start + 2), $stock);
        $sheet->getActiveSheet()->getStyle('I' . ($i + $start + 2))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);


        $sheet->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $sheet->getActiveSheet()->getColumnDimension('B')->setWidth(45);
        //$sheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $sheet->getActiveSheet()->getColumnDimension('C')->setWidth(9);
        $sheet->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $sheet->getActiveSheet()->getColumnDimension('E')->setWidth(9);
        $sheet->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $sheet->getActiveSheet()->getColumnDimension('G')->setWidth(9);
        $sheet->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $sheet->getActiveSheet()->getColumnDimension('I')->setWidth(9);
        $sheet->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $sheet->getActiveSheet()->getColumnDimension('K')->setWidth(9);

        $sheet->getActiveSheet()->getRowDimension($start+2)->setRowHeight(30);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.iconv("utf-8","cp949","구매이탈고객.xls").'"');
        header('Cache-Control: max-age=0');



        // $objWriter->setUseInlineCSS(true);
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $sheet->getActiveSheet()->getStyle('A'.($start+1).':K'.($i+$start+2))->applyFromArray($styleArray);
        $sheet->getActiveSheet()->getStyle('A'.$start.':K'.($i+$start+2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $sheet->getActiveSheet()->getStyle('A'.$start.':K'.($i+$start+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getActiveSheet()->getStyle('A'.($start).':O'.($start+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setWrapText(true);
        $sheet->getActiveSheet()->getStyle('B'.($start+2).':B'.($i+$start+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        //$sheet->getActiveSheet()->getStyle('A'.$start.':K'.($i+$start+2))->getAlignment()->setIndent(1);
        //$sheet->getActiveSheet()->getStyle('A10')->getAlignment()->setIndent(10); 왼쪽 들여쓰기
        $sheet->getActiveSheet()->getStyle('A'.$start.':K'.($i+$start+2))->getFont()->setSize(10)->setName('돋움');

        $sheet->getActiveSheet()->getStyle('A2')->getFont()->setSize(15)->setBold(true)->setName('돋움');
        $sheet->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getActiveSheet()->getStyle('A'.($i+$start+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        unset($styleArray);
        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel5');
        $objWriter->save('php://output');

        exit;
    }


    $exce_down_str = "<a href='?mode=excel&".str_replace("&mode=iframe", "",$_SERVER["QUERY_STRING"])."'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_excel_save.gif' border=0></a>";

    $mstring = $mstring.TitleBar("구매이탈고객",$dateString, true,  $exce_down_str);

    $mstring = $mstring."<form name='list_frm' method='post' action='/admin/member/member_batch.act.php'  target='act' >
					<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
					<table cellpadding=0 cellspacing=0 width=100%  class='list_table_box'  >
									<col width='4%'>
									<col width='7%'>
									<col width='7%' >
									<col width=7% nowrap>
									<col width=7% nowrap>
									<col width='*' nowrap>
									<col width=7% nowrap>ㅁ
									<col width=7% nowrap>
									<col width=7% nowrap>
									<col width=7% nowrap>
									<col width=7% nowrap>
						";
    $mstring = $mstring."<tr height=30 align=center style='font-weight:bold'>
							<td class=s_td ><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
							<td class=m_td >시간</td>
							<td class=m_td >회원명</td><td class=m_td  nowrap>회원아이디</td><td class=m_td  nowrap>이탈단계</td><td class=m_td nowrap>상품명</td>
							<td class=m_td nowrap>구매이탈수</td>
							<td class=m_td nowrap>원가</td>
							<td class=m_td nowrap>판매가</td>
							<td class=m_td nowrap>마진율(%)</td>
							<td class=e_td nowrap>재고</td>
							</tr>\n";

    if($fordb->total == 0){
        $mstring = $mstring."<tr height=150 bgcolor=#ffffff align=center><td colspan=11>결과값이 없습니다.</td></tr>\n";
    }else{

        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);
            //echo $_SERVER["DOCUMENT_ROOT"]."".PrintImage($_SESSION["admin_config"]["mall_data_root"]."/images/product", $fordb->dt['id'], "s", $fordb->dt)."<br>";
            //if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($_SESSION["admin_config"]["mall_data_root"]."/images/product", $fordb->dt['id'], "s", $fordb->dt)) || $image_hosting_type=='ftp'){
            $img_str = PrintImage($_SESSION["admin_config"]["mall_data_root"]."/images/product", $fordb->dt['id'], "s", $fordb->dt);
            //}else{
            //	$img_str = "../../image/no_img.gif";
            //}

            $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
			<td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$fordb->dt['ucode']."'></td>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\"  nowrap>".ReturnDateFormat($fordb->dt['vdate'])." : ".$fordb->dt['vtime']."</td>
			<td class='list_box_td point'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" ><a href=\"javascript:PoPWindow('../../member/member_view.php?code=".$fordb->dt['code']."',950,700,'member_view')\">".$fordb->dt['name']."</a></td>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\"><a href=\"javascript:PoPWindow('../../member/member_view.php?code=".$fordb->dt['code']."',950,700,'member_view')\">".$fordb->dt['userid']."</a></td>
			<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt['exitname']."</td>
			<td class='list_box_td point'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='text-align:left;padding:10px;'> 
				<a href='../../goods_input.php?id=".$fordb->dt['id']."' class='screenshot'  rel='".PrintImage($_SESSION["admin_config"]["mall_data_root"]."/images/product", $fordb->dt['id'], $LargeImageSize, $fordb->dt)."'  ><img src='".$img_str."' align=absmiddle width=30 height=30 style='float:left;margin-right:10px;border:1px solid silver'></a><div style='padding-top:10px;'> ".($fordb->dt['brand_name'] != "" ? $fordb->dt['brand_name']."-":"")." ".$fordb->dt['pname']."</div>
			</td>
			<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt['vquantity']."</td>
			<td class='list_box_td list_bg_gray'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['coprice'],0)."</td>
			<td class='list_box_td'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['sellprice'],0)."</td>
			<td class='list_box_td list_bg_gray'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt['sellprice']-$fordb->dt['coprice'])/$fordb->dt['sellprice']*100,0)."%</td>
			<td class='list_box_td'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['stock'],0)."</td>
			<!--td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['vprice'],0)."</td-->
			</tr>";

            /*
                    $mstring .= "<tr height=30>
                    <td style='padding-left:20px' class=s_td>결제완료</td><td align=center class=m_td width=125>".$fordb->dt['step6']."</td><td align=center class=m_td width=125>-</td><td align=center class=e_td width=125>-</td>
                    </tr>\n";
            */
            //	$exitcnt = $pageview01 + returnZeroValue($fordb->dt['visit_cnt']);


        }
    }
    $mstring = $mstring."</table>\n";
//	$mstring = $mstring."<table cellpadding=3 cellspacing=0 width=745  >\n";
//	if ($pageview01 == 0){
//		$mstring = $mstring."<tr height=50 bgcolor=#ffffff align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
//	}
//	$mstring = $mstring."<tr height=2 bgcolor=#ffffff align=center><td colspan=5 width=190></td></tr>\n";
//	$mstring = $mstring."<tr height=25 align=center><td width=50 class=s_td width=30 colspan=2>합계</td><td class=e_td width=190>".$pageview01."</td></tr>\n";
//	$mstring = $mstring."</table>\n";
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                - <b>구매이탈고객이란?</b> 상품을 둘러보고 장바구니에 상품을 담고 주문서 작성전 또는 작성후 결제를 완료를 하지 않은 상태에서 사이트를 이탈한 고객을 말합니다<br>
                - 구매이탈 고객은 구매 needs 가 강한 고객으로 이벤트나 프로모션을 통해서 쉽게 구매를 유도할수 있는 고객입니다
                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );

   // $mstring .= SendCampaignBox($total);
    $mstring .= "</form>";

    $mstring .= HelpBox("구매이탈고객", $help_text);
    return $mstring;
}

if ($mode == "iframe"){
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";

    $ca = new Calendar();
    $ca->LinkPage = 'purchasestepescaper.php';

    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";

}else{
    $p = new forbizReportPage();
    $p->TopNaviSelect = "step2";
    $p->forbizLeftMenu = commerce_munu('purchasestepescaper.php');
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->Navigation = "이커머스분석 > 고객리스트 > 구매이탈고객";
    $p->title = "구매이탈고객";
    $p->PrintReportPage();
}
?>
