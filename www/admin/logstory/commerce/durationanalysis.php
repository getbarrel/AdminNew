<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../include/ReportReferTree.php");
include("../include/commerce.lib.php");
include("../include/campaign.lib.php");



$script = "<script language='javascript'>
function reloadView(){
	
		if($('#category_view').attr('checked') == true || $('#category_view').attr('checked') == 'checked'){		
			$.cookie('category_view', '1', {expires:1,domain:document.domain, path:'/', secure:0});
		}else{		
			$.cookie('category_view', '0', {expires:1,domain:document.domain, path:'/', secure:0});
		}
		
		document.location.reload();
	
	}
</script>
";

if($mode == "excel"){

    ReportTable2($vdate,$SelectReport);

}else if ($mode == "iframe"){
//	echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
//	echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'durationanalysis.php';

    echo "<html>";
    echo "<meta http-equiv='content-type' content='text/html; charset=euc-kr'>";
    echo "<body>";

    echo "<div id='report_view'>".ReportTable2($vdate,$SelectReport)."</div>";

    echo "<div id='calendar_view'>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</div>";
    echo "</body>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.getElementById('report_view').innerHTML;parent.unloading()</Script>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.getElementById('calendar_view').innerHTML;parent.vdate;parent.ChangeCalenderView($SelectReport);</Script>";
    //echo "<Script>alert(1);</Script>";
    echo "</html>";

//	echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
//	echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";
}else if($mode == "pop" || $mode == "report" || $mode == "print"){
    $P = new ManagePopLayOut();
    $P->addScript = $Script;
    $P->Navigation = "이커머스분석 > 고객 종합분석 > 최대체류고객 분석";

    $P->title = "최대체류고객 분석";
    //$P->strContents = ReportTable($vdate,$SelectReport);

    $P->NaviTitle = "최대체류고객 분석";
    $P->strContents = ReportTable2($vdate,$SelectReport);

    $P->OnloadFunction = "";
    //	$P->layout_display = false;
    echo $P->PrintLayOut();
}else{


    $p = new forbizReportPage();
    $p->TopNaviSelect = "step2";
    $p->forbizLeftMenu = commerce_munu('durationanalysis.php', "");

    $p->forbizContents = ReportTable2($vdate,$SelectReport);

    $p->addScript = "<script language='JavaScript' src='../include/manager.js'></script>\n<script language='JavaScript' src='../include/Tree.js'></script>\n$script ";
    //$p->treemenu = "<div id=TREE_BAR style=\"margin:5;\">".GetTreeNode()."</div>";
    $p->Navigation = "이커머스분석 > 고객 종합분석 > 최대체류고객 분석";
    $p->title = "최대체류고객 분석";
    $p->PrintReportPage();

}





function ReportTable2($vdate,$SelectReport=1){
    global $depth,$cid;
    global $non_sale_status, $report_type;
    global $search_sdate, $search_edate;


    $nview_cnt_sum = 0;
    $order_change_rate_sum = 0;
    $pcnt_sum = 0;
    $ptprice_sum = 0;

    $pageview01 = 0;
    //$cid = $referer_id;
    if($SelectReport == ""){
        $SelectReport = 1;
    }
    $fordb = new forbizDatabase();
    if($depth == ""){
        $depth = 0;
    }else{
        $depth = $depth+1;
    }

    if($SelectReport == "4"){
        $vdate = $search_sdate;
        $vweekenddate = $search_edate;
    }

    if($vdate == ""){
        $vdate = date("Ymd", time());
        $selected_date = date("Y-m-d", time());
        $vyesterday = date("Ymd", time()-84600);
        $voneweekago = date("Ymd", time()-84600*7);
        $search_sdate = date("Y-m-d", time()-84600*7);
        $search_edate = date("Y-m-d", time());
    }else{
        if($SelectReport ==3){
            $vdate = $vdate."01";
        }
        $selected_date = date("Y-m-d", mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)));
        $vweekenddate = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
        $vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);

    }

    if(isset($_GET["orderby"]) && $_GET["orderby"] != "" && isset($_GET["ordertype"]) && $_GET["ordertype"] != ""){
        $orderbyString = " order by ".$_GET["orderby"]." ".$_GET["ordertype"].", duration_total desc, nview_cnt desc  ";
    }else{
        $orderbyString = " order by duration_total desc , nview_cnt desc ";
    }

    if(!empty($_GET["max"])){
        $max = $_GET["max"];
    }else{
        $max = 100;
    }

    if (empty($_GET["page"])){
        $start = 0;
        $page  = 1;
    }else{
        $start = ($_GET["page"] - 1) * $max;
    }
    $orderbyString .= "	limit $start, $max ";



    //$sql = "Select r.cid, r.cname, b.visit_cnt from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYREFERER." b where b.vdate = '$vdate' and substr(r.cid,0,6) = substr(b.vreferer_id,0,6) and r.depth = $depth group by r.cid, r.cname order by visit_cnt desc";
    if ($SelectReport == 1){
        $sql = "select  data.ucode,  AES_DECRYPT(UNHEX(cmd.name),'".$fordb->ase_encrypt_key."') as name, cu.id, sum(nview_cnt) as nview_cnt, sum(order_goods_cnt) as order_goods_cnt, 
						sum(pcnt) as pcnt, sum(duration_total) as duration_total,sum(login_total_cnt) as login_total_cnt, sum(ptprice) as ptprice,
						case when sum(order_goods_cnt) = 0 then 0 else sum(duration_total)/sum(order_goods_cnt) end as loginperorder,
						case when sum(order_goods_cnt) = 0 then 0 else sum(ptprice)/sum(order_goods_cnt) end as saleperorder, 
						case when sum(pcnt) = 0 then 0 else sum(duration_total)/sum(pcnt) end as loginperpcnt,
						case when sum(pcnt) = 0 then 0 else sum(ptprice)/sum(pcnt) end as saleperpcnt,
						case when sum(duration_total) = 0 then 0 else sum(ptprice)/sum(duration_total) end as saleperlogin
						from 
						(Select b.ucode as ucode, sum(b.nview_cnt) as nview_cnt , 0 as order_goods_cnt, 0 as pcnt , 0 duration_total  , 0 as login_total_cnt , 0 as ptprice
						from ".TBL_COMMERCE_VIEWINGVIEW." b  	
						where b.vdate = '".$vdate."' 			
						group by ucode
						union
						select o.user_code as ucode, 0 as nview_cnt , count(*) as order_goods_cnt,  sum(od.pcnt) as pcnt,  0 as duration_total  , 0 as login_total_cnt , sum(od.pt_dcprice) as ptprice
						from shop_order o, shop_order_detail od 
						where o.oid = od.oid and od.regdate  between '".$selected_date." 00:00:00' and '".$selected_date." 23:59:59'  
						and od.status NOT IN ('".implode("','",$non_sale_status)."') 
						and od.order_from = 'self' 
						group by ucode 
						union
						Select mem_ix as ucode , 0 as nview_cnt , 0 as order_goods_cnt, 0 as pcnt , sum(nduration) as duration_total , 0 as login_total_cnt, 0 as ptprice
						from logstory_duration_history lh  	
						where lh.vdate = '".$vdate."'  
						group by ucode
						union
						Select mem_ix as ucode , 0 as nview_cnt , 0 as order_goods_cnt, 0 as pcnt , 0 as duration_total, sum(ncnt) as login_total_cnt , 0 as ptprice
						from logstory_login_history lh  	
						where lh.vdate = '".$vdate."'  
						group by ucode
						) data left join ".TBL_COMMON_MEMBER_DETAIL." cmd on data.ucode = cmd.code
						left join  ".TBL_COMMON_USER." cu on data.ucode = cu.code
						where ucode != ''
						";

        $sql .= "group by ucode ";
        $sql .= $orderbyString;

        //echo nl2br($sql);


        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2 || $SelectReport == 4){
        $asweekenddate = $search_edate;
        if($SelectReport == "4"){
            $vdate = $search_sdate;
            $vweekenddate = date("Y-m-d", mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4)));
        }


        $sql = "select  data.ucode,  AES_DECRYPT(UNHEX(cmd.name),'".$fordb->ase_encrypt_key."') as name, cu.id, sum(nview_cnt) as nview_cnt, sum(order_goods_cnt) as order_goods_cnt, 
						sum(pcnt) as pcnt, sum(duration_total) as duration_total, sum(ptprice) as ptprice,
						case when sum(order_goods_cnt) = 0 then 0 else sum(duration_total)/sum(order_goods_cnt) end as loginperorder,
						case when sum(order_goods_cnt) = 0 then 0 else sum(ptprice)/sum(order_goods_cnt) end as saleperorder, 
						case when sum(pcnt) = 0 then 0 else sum(duration_total)/sum(pcnt) end as loginperpcnt,
						case when sum(pcnt) = 0 then 0 else sum(ptprice)/sum(pcnt) end as saleperpcnt,
						case when sum(duration_total) = 0 then 0 else sum(ptprice)/sum(duration_total) end as saleperlogin
						from 
						(Select b.ucode as ucode, sum(b.nview_cnt) as nview_cnt , 0 as order_goods_cnt, 0 as pcnt , 0 duration_total , 0 as ptprice
						from ".TBL_COMMERCE_VIEWINGVIEW." b  	
						where b.vdate between '".$vdate."' and '".$asweekenddate."' 	
						group by ucode
						union
						select o.user_code as ucode, 0 as nview_cnt , count(*) as order_goods_cnt,  sum(od.pcnt) as pcnt,  0 as duration_total , sum(od.pt_dcprice) as ptprice
						from shop_order o, shop_order_detail od 
						where o.oid = od.oid and od.regdate between '".$selected_date." 00:00:00' and '".$vweekenddate." 23:59:59' 
						and od.status NOT IN ('".implode("','",$non_sale_status)."') 
						and od.order_from = 'self' 
						group by ucode 
						union
						Select mem_ix as ucode , 0 as nview_cnt , 0 as order_goods_cnt, 0 as pcnt , sum(nduration) as duration_total , 0 as ptprice
						from logstory_duration_history lh  	
						where lh.vdate between '".$vdate."' and '".$asweekenddate."' 
						group by ucode
						) data left join ".TBL_COMMON_MEMBER_DETAIL." cmd on data.ucode = cmd.code
						left join  ".TBL_COMMON_USER." cu on data.ucode = cu.code
						where ucode != ''
						";

        $sql .= "group by ucode ";
        $sql .= $orderbyString;

        if($SelectReport == 2){
            $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
        }else if($SelectReport == 4){
            //echo $search_sdate;
            $s_week_num = date("w",mktime(0,0,0,substr($search_sdate,4,2),substr($search_sdate,6,2),substr($search_sdate,0,4)));
            $e_week_num = date("w",mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4)));
            $dateString = "기간별 : ".getNameOfWeekday($s_week_num,$search_sdate,"priodname")."~".getNameOfWeekday($e_week_num,$search_edate,"priodname");
        }
    }else if($SelectReport == 3){


        $sql = "select  data.ucode,  AES_DECRYPT(UNHEX(cmd.name),'".$fordb->ase_encrypt_key."') as name, cu.id, sum(nview_cnt) as nview_cnt, sum(order_goods_cnt) as order_goods_cnt, 
						sum(pcnt) as pcnt, sum(duration_total) as duration_total, sum(ptprice) as ptprice,
						case when sum(order_goods_cnt) = 0 then 0 else sum(duration_total)/sum(order_goods_cnt) end as loginperorder,
						case when sum(order_goods_cnt) = 0 then 0 else sum(ptprice)/sum(order_goods_cnt) end as saleperorder, 
						case when sum(pcnt) = 0 then 0 else sum(duration_total)/sum(pcnt) end as loginperpcnt,
						case when sum(pcnt) = 0 then 0 else sum(ptprice)/sum(pcnt) end as saleperpcnt,
						case when sum(duration_total) = 0 then 0 else sum(ptprice)/sum(duration_total) end as saleperlogin
						from 
						(Select b.ucode as ucode, sum(b.nview_cnt) as nview_cnt , 0 as order_goods_cnt, 0 as pcnt , 0 duration_total , 0 as ptprice
						from ".TBL_COMMERCE_VIEWINGVIEW." b  	
						where b.vdate LIKE '".substr($vdate,0,6)."%'  			
						group by ucode
						union
						select o.user_code as ucode, 0 as nview_cnt , count(*) as order_goods_cnt,  sum(od.pcnt) as pcnt,  0 as duration_total , sum(od.pt_dcprice) as ptprice
						from shop_order o, shop_order_detail od 
						where o.oid = od.oid and od.regdate between '".substr($selected_date,0,7)."-01 00:00:00' and '".substr($selected_date,0,7)."-31 23:59:59' 
						and od.status NOT IN ('".implode("','",$non_sale_status)."') 
						and od.order_from = 'self' 
						group by ucode 
						union
						Select mem_ix as ucode , 0 as nview_cnt , 0 as order_goods_cnt, 0 as pcnt , sum(nduration) as duration_total , 0 as ptprice
						from logstory_duration_history lh  	
						where lh.vdate LIKE '".substr($vdate,0,6)."%' 
						group by ucode
						) data left join ".TBL_COMMON_MEMBER_DETAIL." cmd on data.ucode = cmd.code
						left join  ".TBL_COMMON_USER." cu on data.ucode = cu.code
						where ucode != ''
						";

        $sql .= "group by ucode ";
        $sql .= $orderbyString;


        $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");
    }
    //echo $depth."<br>";
    //echo nl2br($sql);

    $fordb->query($sql);
    $total = $fordb->total;



    $str_page_bar = page_bar($total, $page, $max, "&max=$max&info_type=$info_type","");


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

        $sheet->getActiveSheet(0)->mergeCells('A2:O2');
        $sheet->getActiveSheet(0)->setCellValue('A2', "최대체류고객 분석 - 구매전환분석");
        $sheet->getActiveSheet(0)->mergeCells('A3:O3');
        $sheet->getActiveSheet(0)->setCellValue('A3', $dateString);

        $sheet->getActiveSheet(0)->mergeCells('A'.($i+1).':A'.($i+2));
        $sheet->getActiveSheet(0)->mergeCells('B'.($i+1).':B'.($i+2));
        $sheet->getActiveSheet(0)->mergeCells('C'.($i+1).':C'.($i+2));
        $sheet->getActiveSheet(0)->mergeCells('D'.($i+1).':D'.($i+2));
        $sheet->getActiveSheet(0)->mergeCells('E'.($i+1).':E'.($i+2));
        $sheet->getActiveSheet(0)->mergeCells('F'.($i+1).':F'.($i+2));
        $sheet->getActiveSheet(0)->mergeCells('G'.($i+1).':G'.($i+2));

        $sheet->getActiveSheet(0)->mergeCells('H'.($i+1).':J'.($i+1));
        $sheet->getActiveSheet(0)->mergeCells('K'.($i+1).':M'.($i+1));
        $sheet->getActiveSheet(0)->mergeCells('N'.($i+1).':O'.($i+1));

        $sheet->getActiveSheet(0)->setCellValue('A' . ($i+1), "순");
        $sheet->getActiveSheet(0)->setCellValue('B' . ($i+1), "회원명");
        $sheet->getActiveSheet(0)->setCellValue('C' . ($i+1), "회원아이디");
        $sheet->getActiveSheet(0)->setCellValue('D' . ($i+1), "로그인");
        $sheet->getActiveSheet(0)->setCellValue('E' . ($i+1), "체류시간(a)");
        $sheet->getActiveSheet(0)->setCellValue('F' . ($i+1), "로그인평균\r체류시간");
        $sheet->getActiveSheet(0)->setCellValue('G' . ($i+1), "조회수");
        $sheet->getActiveSheet(0)->setCellValue('H' . ($i+1), "건별분석");
        $sheet->getActiveSheet(0)->setCellValue('I' . ($i+1), "수량별분석");
        $sheet->getActiveSheet(0)->setCellValue('J' . ($i+1), "체류시간별분석");

        $sheet->getActiveSheet(0)->setCellValue('H' . ($i+2), "구매건수(b)");
        $sheet->getActiveSheet(0)->setCellValue('I' . ($i+2), "구매건당\r평균체류시간(a/b)");
        $sheet->getActiveSheet(0)->setCellValue('J' . ($i+2), "구매건당\r평균매출액");
        $sheet->getActiveSheet(0)->setCellValue('K' . ($i+2), "구매수량");
        $sheet->getActiveSheet(0)->setCellValue('L' . ($i+2), "구매수량당\r평균체류시간");
        $sheet->getActiveSheet(0)->setCellValue('M' . ($i+2), "구매수량당\r평균매출액");
        $sheet->getActiveSheet(0)->setCellValue('N' . ($i+2), "체류시간당\r평균매출액");
        $sheet->getActiveSheet(0)->setCellValue('O' . ($i+2), "매출액");



        $sheet->setActiveSheetIndex(0);
        //$i = $i + 2;
        $order_goods_cnt_sum = 0;
        $duration_total_sum = 0;
        $loginperorder_sum = 0;

        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);

            $order_goods_cnt_sum += $fordb->dt['order_goods_cnt'];
            $duration_total_sum += $fordb->dt['duration_total'];
            $pcnt_sum += $fordb->dt['pcnt'];
            $loginperorder_sum += $fordb->dt['loginperorder'];

            $ptprice_sum += $fordb->dt['ptprice'];

        }

        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);


            if($fordb->dt['login_total_cnt'] > 0){
                $login_rate = $fordb->dt['duration_total']/$fordb->dt['login_total_cnt'];
            }else{
                $login_rate = 0;
            }

            $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 3), ($i + 1));
            $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 3), $fordb->dt['name']);
            $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 3), $fordb->dt['id']);

            $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 3), $fordb->dt['duration_total']);
            $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 3), $fordb->dt['login_total_cnt']);
            $sheet->getActiveSheet()->getStyle('E' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('F' . ($i + $start + 3), $login_rate);
            $sheet->getActiveSheet()->getStyle('F' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

            $sheet->getActiveSheet()->setCellValue('G' . ($i + $start + 3), $fordb->dt['nview_cnt']);
            $sheet->getActiveSheet()->getStyle('G' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('H' . ($i + $start + 3), $fordb->dt['order_goods_cnt']);
            $sheet->getActiveSheet()->getStyle('H' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('I' . ($i + $start + 3), $fordb->dt['loginperorder']);
            $sheet->getActiveSheet()->getStyle('I' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('J' . ($i + $start + 3), $fordb->dt['saleperorder']);
            $sheet->getActiveSheet()->getStyle('J' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('K' . ($i + $start + 3), $fordb->dt['pcnt']);
            $sheet->getActiveSheet()->getStyle('K' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('L' . ($i + $start + 3), $fordb->dt['loginperpcnt']);
            $sheet->getActiveSheet()->getStyle('L' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('M' . ($i + $start + 3), $fordb->dt['saleperpcnt']);
            $sheet->getActiveSheet()->getStyle('M' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('N' . ($i + $start + 3), $fordb->dt['saleperlogin']);
            $sheet->getActiveSheet()->getStyle('N' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('O' . ($i + $start + 3), $fordb->dt['ptprice']);
            $sheet->getActiveSheet()->getStyle('O' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('P' . ($i + $start + 3), $sale_rate);
            $sheet->getActiveSheet()->getStyle('P' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);


            $nview_cnt_sum = $nview_cnt_sum + returnZeroValue($fordb->dt['nview_cnt']);
            $login_total_cnt_sum = $nview_cnt_sum + returnZeroValue($fordb->dt['login_total_cnt']);

            $order_change_rate_sum += $change_rate*100;


        }


        //$i++;
        $sheet->getActiveSheet(0)->mergeCells('A'.($i + $start + 3).':C'.($i+ $start+3));
        $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 3), '합계');
        //$sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 3), getIventoryCategoryPathByAdmin($goods_infos[$i]['cid'], 4));
        $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 3), $duration_total_sum);
        $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 3), $login_total_cnt_sum);
        $sheet->getActiveSheet()->setCellValue('F' . ($i + $start + 3), ($login_total_cnt_sum > 0 ? $duration_total_sum/$login_total_cnt_sum:0));
        $sheet->getActiveSheet()->setCellValue('G' . ($i + $start + 3), $nview_cnt_sum);
        $sheet->getActiveSheet()->setCellValue('H' . ($i + $start + 3), $order_goods_cnt_sum);
        $sheet->getActiveSheet()->setCellValue('I' . ($i + $start + 3), ($order_goods_cnt_sum > 0 ? $duration_total_sum/$order_goods_cnt_sum*100:0));
        $sheet->getActiveSheet()->setCellValue('J' . ($i + $start + 3), ($order_goods_cnt_sum > 0 ? $ptprice_sum/$order_goods_cnt_sum:0));
        $sheet->getActiveSheet()->setCellValue('K' . ($i + $start + 3), $order_goods_cnt_sum);
        $sheet->getActiveSheet()->setCellValue('L' . ($i + $start + 3), ($order_goods_cnt_sum > 0 ? $duration_total_sum/$order_goods_cnt_sum:0));
        $sheet->getActiveSheet()->setCellValue('M' . ($i + $start + 3), ($order_goods_cnt_sum > 0 ? $ptprice_sum/$order_goods_cnt_sum:0));
        $sheet->getActiveSheet()->setCellValue('N' . ($i + $start + 3), ($duration_total_sum > 0 ? $ptprice_sum/$duration_total_sum:0));
        $sheet->getActiveSheet()->setCellValue('O' . ($i + $start + 3), $ptprice_sum);
        $sheet->getActiveSheet()->setCellValue('P' . ($i + $start + 3), "-");




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
        $sheet->getActiveSheet()->getColumnDimension('P')->setWidth(15);

        $sheet->getActiveSheet()->getRowDimension($start+2)->setRowHeight(30);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.iconv("utf-8","cp949","최대체류고객 분석_구매전환분석.xls").'"');
        header('Cache-Control: max-age=0');



        // $objWriter->setUseInlineCSS(true);
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $sheet->getActiveSheet()->getStyle('A'.($start+1).':O'.($i+$start+3))->applyFromArray($styleArray);
        $sheet->getActiveSheet()->getStyle('A'.$start.':O'.($i+$start+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $sheet->getActiveSheet()->getStyle('A'.($start).':O'.($i+$start+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getActiveSheet()->getStyle('A'.($start).':O'.($start+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setWrapText(true); // 타이틀 영역 가운데 정렬
        $sheet->getActiveSheet()->getStyle('B'.($start+3).':B'.($i+$start+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)->setWrapText(true); ; // 카테고리 영역 왼쪽 정렬

        $sheet->getActiveSheet()->getStyle('A'.$start.':O'.($i+$start+3))->getFont()->setSize(10)->setName('돋움');  // 리포트 영역 폰트 설정

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
    $exce_down_str = "";
    if(false){
        $exce_down_str = "<img src=\"../../images/korea/btn_print.gif\" onclick=\"PopSWindow('?mode=print&".str_replace("&mode=iframe", "",$_SERVER["QUERY_STRING"])."',1150,500,'logstory_print');\" style=\"margin-right:3px;cursor:pointer;\"  >";
    }
    $exce_down_str .= "<a href='?mode=excel&".str_replace(array("&mode=iframe","mode=iframe&"), "",$_SERVER["QUERY_STRING"])."'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_excel_save.gif' border=0></a>";
    //$mstring = $mstring.TitleBar("최대체류고객 분석 : ".($cid ? getCategoryPath($cid,4):""),$dateString);
    $mstring = "<form name='list_frm' method='post' action='/admin/member/member_batch.act.php'  target='act' >
					<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
					<table width='100%' border=0>";
    if(isset($_GET["mode"]) && ($_GET["mode"] == "pop" || $_GET["mode"] == "print")){
        $mstring .= "<tr><td colspan=2>".TitleBar("최대체류고객 분석 : ",$dateString."".($cid ? "-".getCategoryPath($cid,4):""),false, $exce_down_str)."</td></tr>";
    }else{
        $mstring .= "<tr><td colspan=2>".TitleBar("최대체류고객 분석 : ",$dateString."".($cid ? "-".getCategoryPath($cid,4):""),true, $exce_down_str)."</td></tr>";
    }
    $mstring .= "<tr>
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
						</tr>";
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
						<col width='*'>
						<col width='7%'>
						<col width='7%'>
						<col width='7%'>
						<col width='7%'>
						<col width='7%'>
						<col width='7%'>
						<col width='7%'>
						<col width='7%'>
						<col width='7%'>
						<col width='7%'>
						<col width='7%'>
						<col width='7%'>";
    $mstring .= "<tr height=30 align=center>
						<td class=s_td rowspan=2><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
						<td class=s_td rowspan=2>순</td>
						<td class=m_td rowspan=2> ".OrderByLink("회원명", "name", $ordertype)."</td>
						<td class=m_td rowspan=2>회원아이디</td>
						<td class=m_td rowspan=2>로그인</td>
						<td class=m_td rowspan=2>체류시간(a)</td>
						<td class=m_td rowspan=2>로그인평균<br>체류시간</td>
						<td class=m_td rowspan=2>".OrderByLink("조회수", "nview_cnt", $ordertype)."</td>
						<td class=m_td colspan=3>건별분석</td>
						<td class=m_td colspan=3>수량별분석</td>
						<td class=m_td colspan=2>체류시간별분석</td>
						<!--td class=m_td rowspan=2>프로모션</td--> 
					   </tr>\n";
    $mstring .= "<tr height=30 align=center>
							
							<td class=m_td >".OrderByLink("구매건수(b)", "order_goods_cnt", $ordertype)."</td>
							<td class=m_td >".OrderByLink("구매건당<br>평균체류시간(a/b)", "loginperorder", $ordertype)."</td>
							<td class=m_td >".OrderByLink("구매건당<br>평균매출액", "saleperorder", $ordertype)."</td>
							<td class=m_td >".OrderByLink("구매수량", "pcnt", $ordertype)."</td>
							<td class=m_td >".OrderByLink("구매수량당<br>평균체류시간", "loginperpcnt", $ordertype)." </td>
							<td class=m_td >".OrderByLink("구매수량당<br>평균매출액", "saleperpcnt", $ordertype)."</td>

							<td class=m_td >".OrderByLink("체류시간당<br>평균매출액", "saleperlogin", $ordertype)." </td>
							<td class=m_td >".OrderByLink("매출액", "ptprice", $ordertype)."</td>
						</tr>\n";

    $order_goods_cnt_sum = 0;
    $duration_total_sum = 0;
    $loginperorder_sum = 0;

    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);
        $nview_cnt_sum += $fordb->dt['nview_cnt'];
        $order_goods_cnt_sum += $fordb->dt['order_goods_cnt'];
        $duration_total_sum += $fordb->dt['duration_total'];
        $pcnt_sum += $fordb->dt['pcnt'];
        $loginperorder_sum += $fordb->dt['loginperorder'];

        $ptprice_sum += $fordb->dt['ptprice'];

    }
    $login_total_cnt_sum = 0;
    if($fordb->total > 0) {
        for ($i = 0; $i < $fordb->total; $i++) {
            $fordb->fetch($i);


            if ($fordb->dt['login_total_cnt'] > 0) {
                $login_rate = $fordb->dt['duration_total'] / $fordb->dt['login_total_cnt'];
            } else {
                $login_rate = 0;
            }


            $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
            <td class='list_box_td'><input type=checkbox name=code[] id='code' value='" . $fordb->dt['ucode'] . "'></td>
            <td class='list_box_td list_bg_gray' onmouseover=\"mouseOnTD('" . $i . "',true)\" onmouseout=\"mouseOnTD('$i',false)\">" . ($i + 1) . "</td>
            <td class='list_box_td point' style='text-align:center;line-height:150%;padding:5px;' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">
            <a href=\"javascript:PoPWindow('../../member/member_view.php?code=" . $fordb->dt['ucode'] . "',950,700,'member_view')\">" . $fordb->dt['name'] . "</a> ";
            $mstring .= "
            </td>
            <td class='list_box_td' style='text-align:center;line-height:150%;padding:5px;' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">
            <a href=\"javascript:PoPWindow('../../member/member_view.php?code=" . $fordb->dt['ucode'] . "',950,700,'member_view')\">" . $fordb->dt['id'] . "</a>
            </td>
            <td class='list_box_td  number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">" . number_format(returnZeroValue($fordb->dt['login_total_cnt']), 0) . "&nbsp;</td>
            <td class='list_box_td  number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">" . number_format(returnZeroValue($fordb->dt['duration_total']), 0) . "&nbsp;</td>
            <td class='list_box_td  number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">" . number_format(returnZeroValue($login_rate), 0) . "</td>
            <td class='list_box_td  number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">" . number_format(returnZeroValue($fordb->dt['nview_cnt']), 0) . "&nbsp;</td>
            <td class='list_box_td point number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">" . number_format(returnZeroValue($fordb->dt['order_goods_cnt']), 0) . "&nbsp;</td>
            <td class='list_box_td list_bg_gray number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">" . number_format(returnZeroValue($fordb->dt['loginperorder']), 1) . "</td>
            <td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">" . number_format($fordb->dt['saleperorder']) . "</td>
            <td class='list_box_td point number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">" . number_format($fordb->dt['pcnt']) . "</td>
            <td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">" . number_format($fordb->dt['loginperpcnt'], 1) . "</td>
            <td class='list_box_td list_bg_gray number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">" . number_format(returnZeroValue($fordb->dt['saleperpcnt']), 0) . " </td> 
            <td class='list_box_td  number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">" . number_format(returnZeroValue($fordb->dt['saleperlogin']), 0) . "&nbsp;</td>
            <td class='list_box_td point number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">" . number_format($fordb->dt['ptprice']) . "</td> 
            </tr>\n";

            $nview_cnt_sum = $nview_cnt_sum + returnZeroValue($fordb->dt['nview_cnt']);

            $order_goods_cnt_sum += returnZeroValue($fordb->dt['order_goods_cnt']);
            $login_total_cnt_sum += returnZeroValue($fordb->dt['login_total_cnt']);

            $order_change_rate_sum += $change_rate;


        }
    }else{
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
    $mstring .= "<tr height=25 align=right>
	<td class=s_td align=center colspan=4>합계</td>
	<td class=e_td style='padding-right:20px;'>".number_format($login_total_cnt_sum,0)."</td>
	<td class=e_td style='padding-right:20px;'>".number_format($duration_total_sum,0)."</td>
	
	
	<td class=e_td style='padding-right:20px;'>".number_format(($login_total_cnt_sum > 0 ? $duration_total_sum/$login_total_cnt_sum:0),1)."</td>
	<td class=e_td style='padding-right:20px;'>".number_format($nview_cnt_sum,0)."</td>
	<td class=e_td style='padding-right:20px;'>".number_format($order_goods_cnt_sum,0)."</td>
	<td class=e_td style='padding-right:20px;'>".number_format(($order_goods_cnt_sum > 0 ? $duration_total_sum/$order_goods_cnt_sum:0),1)."</td>
	<td class=e_td style='padding-right:20px;'>".number_format(($order_goods_cnt_sum > 0 ? $ptprice_sum/$order_goods_cnt_sum:0))."</td>
	<td class=e_td style='padding-right:20px;'>".number_format($order_goods_cnt_sum)."%</td>
	<td class=e_td style='padding-right:20px;'>".number_format(($order_goods_cnt_sum > 0 ? $duration_total_sum/$order_goods_cnt_sum:0),0)."</td>
	<td class=e_td style='padding-right:20px;'>".number_format(($order_goods_cnt_sum > 0 ? $ptprice_sum/$order_goods_cnt_sum:0),0)."</td>
	<td class=e_td style='padding-right:20px;'>".number_format(($duration_total_sum > 0 ? $ptprice_sum/$duration_total_sum:0),0)."</td>
	<td class=e_td style='padding-right:20px;'>".number_format($ptprice_sum,0)."</td> 
	<!--td class=e_td style='padding-right:20px;'>-</td-->
	</tr>\n";
    $mstring .= "</table>\n
	<table>
		<tr><td>".$str_page_bar."</td></tr>
	</table>";

    //$mstring .= $str_page_bar;
    /*
    $help_text = "
    <table>
        <tr>
            <td style='line-height:150%'>
            - 카테고리별 상품조회 회수를 바탕으로 귀사 사이트의 인기카테고리와 비인기 카테고리를 정확히 파악하여 그에 맞는 운영및 마케팅 정책을 수립 수행할수 있습니다<br>
            - 좌측 카테고리를 클릭하면 하부 카테고리에 대한 상세 정보가 표시 됩니다<br><br>
            </td>
        </tr>
    </table>
    ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B' );

    //$mstring .= SendCampaignBox($total);
    $mstring .= HelpBox("최대체류고객 분석", $help_text);
    $mstring .= "</form>";
    return $mstring;
}
?>