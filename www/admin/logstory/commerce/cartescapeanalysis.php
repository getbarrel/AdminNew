<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../include/ReportReferTree.php");
include("../include/commerce.lib.php");




$script = "<script language='javascript'>
function reloadView(){
	
		if($('#category_view').attr('checked') == true || $('#category_view').attr('checked') == 'checked'){		
			$.cookie('category_view', '1', {expires:1,domain:document.domain, path:'/', secure:0});
		}else{		
			$.cookie('category_view', '0', {expires:1,domain:document.domain, path:'/', secure:0});
		}
		
		document.location.reload();
	
	}

function reloadSaleView(){
	
	if($('#view_self_sale').attr('checked') == true || $('#view_self_sale').attr('checked') == 'checked'){		
		$.cookie('view_self_sale', '1', {expires:1,domain:document.domain, path:'/', secure:0});
	}else{		
		$.cookie('view_self_sale', '0', {expires:1,domain:document.domain, path:'/', secure:0});
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
    $ca->LinkPage = 'cartanalysis.php';

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
    $P->Navigation = "이커머스분석 > 상품별 종합분석 > 장바구니 이탈상품";

    $P->title = "장바구니 이탈상품";
    //$P->strContents = ReportTable($vdate,$SelectReport);

    $P->NaviTitle = "장바구니 이탈상품 - 구매전환분석";
    $P->strContents = ReportTable2($vdate,$SelectReport);

    $P->OnloadFunction = "";
    //	$P->layout_display = false;
    echo $P->PrintLayOut();
}else{


    $p = new forbizReportPage();
    $p->TopNaviSelect = "step2";
    $p->forbizLeftMenu = commerce_munu('cartanalysis.php', "<div id=TREE_BAR style=\"margin:5px;\">".GetTreeNode('cartanalysis.php',date("Ymd", time()),'product')."</div>");

    $p->forbizContents = ReportTable2($vdate,$SelectReport);

    $p->addScript = "<script language='JavaScript' src='../include/manager.js'></script>\n<script language='JavaScript' src='../include/Tree.js'></script>\n$script ";
    //$p->treemenu = "<div id=TREE_BAR style=\"margin:5;\">".GetTreeNode()."</div>";
    $p->Navigation = "이커머스분석 > 상품별 종합분석 > 장바구니 이탈상품";
    $p->title = "장바구니 이탈상품";
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
        $selected_date = date("Y-m-d", time());
        $vdate = date("Ymd", time());
        $vyesterday = date("Ymd", time()-84600);
        $voneweekago = date("Ymd", time()-84600*7);
    }else{
        if($SelectReport ==3){
            $vdate = $vdate."01";
        }

        $selected_date = date("Y-m-d", mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)));
        $vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
        $vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
    }

    if(isset($_GET["orderby"]) && $_GET["orderby"] != "" && isset($_GET["ordertype"]) && $_GET["ordertype"] != ""){
        $orderbyString = " order by ".$_GET["orderby"]." ".$_GET["ordertype"].", nview_cnt desc , order_goods_cnt desc ";
    }else{
        $orderbyString = " order by nview_cnt desc , order_goods_cnt desc ";
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
    }
    $orderbyString .= "	limit $start, $max ";



    //$sql = "Select r.cid, r.cname, b.visit_cnt from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." r, ".TBL_LOGSTORY_BYREFERER." b where b.vdate = '$vdate' and substr(r.cid,0,6) = substr(b.vreferer_id,0,6) and r.depth = $depth group by r.cid, r.cname order by visit_cnt desc";
    if ($SelectReport == 1){
        if($depth == 0){
            $sql = "select data.cid, data.pid, p.pname as pname, sum(nview_cnt) as nview_cnt, sum(order_goods_cnt) as order_goods_cnt, sum(escape_cart_pcnt) as escape_cart_pcnt, sum(cart_total_cnt) as cart_total_cnt, sum(ptprice) as ptprice,
						case when sum(cart_total_cnt) = 0 then 0 else sum(order_goods_cnt)/sum(cart_total_cnt)*100 end as cart_change_rate
						from 
						(Select LPAD(b.pid,10,'0') as pid, b.cid as cid, sum(b.nview_cnt) as nview_cnt , 0 as order_goods_cnt, 0 as escape_cart_pcnt , 0 cart_total_cnt , 0 as ptprice
						from ".TBL_COMMERCE_VIEWINGVIEW." b  	
						where b.vdate = '".$vdate."' 			
						group by pid
						union
						select LPAD(od.pid,10,'0') as pid, od.cid as cid, 0 as nview_cnt , count(*) as order_goods_cnt,  0 as escape_cart_pcnt,  0 as cart_total_cnt , sum(od.pt_dcprice) as ptprice
						from shop_order_detail od 
						where od.regdate  between '".$selected_date." 00:00:00' and '".$selected_date." 23:59:59'
						and od.status NOT IN ('".implode("','",$non_sale_status)."') ";

            if(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1){
                $sql .= "and od.order_from = 'self'  ";
            }

            $sql .= " group by pid
						union
						Select LPAD(ss.pid,10,'0') as pid, ss.cid as cid, 0 as nview_cnt , 0 as order_goods_cnt, sum(step1) as escape_cart_pcnt , 0 as cart_total_cnt , 0 as ptprice
						from ".TBL_COMMERCE_SALESTACK." ss  	
						where ss.vdate = '".$vdate."' and step1 = '1' and step6 != '1'	
						group by pid
						union
						Select LPAD(ss.pid,10,'0') as pid, ss.cid as cid, 0 as nview_cnt , 0 as order_goods_cnt, 0 as escape_cart_pcnt , sum(step1) as cart_total_cnt , 0 as ptprice
						from ".TBL_COMMERCE_SALESTACK." ss  	
						where ss.vdate = '".$vdate."' and step1 = '1' 
						group by pid
						) data left join ".TBL_SHOP_PRODUCT." p on data.pid = p.id
						";
            //where date_format(od.regdate,'%Y%m%d') = '".$vdate."'
            $sql .= "group by pid ";
            $sql .= $orderbyString;

            //echo nl2br($sql);
        }else if($depth > 0){

            $sql = "select data.cid, data.pid, p.pname as pname, sum(nview_cnt) as nview_cnt, sum(order_goods_cnt) as order_goods_cnt, sum(escape_cart_pcnt) as escape_cart_pcnt, sum(cart_total_cnt) as cart_total_cnt, sum(ptprice) as ptprice,
						case when sum(cart_total_cnt) = 0 then 0 else sum(order_goods_cnt)/sum(cart_total_cnt)*100 end as cart_change_rate
						from 
						(Select LPAD(b.pid,10,'0') as pid, b.cid as cid, sum(b.nview_cnt) as nview_cnt , 0 as order_goods_cnt, 0 as escape_cart_pcnt , 0 cart_total_cnt , 0 as ptprice
						from ".TBL_COMMERCE_VIEWINGVIEW." b  	
						where b.vdate = '".$vdate."' 			
						group by pid
						union
						select LPAD(od.pid,10,'0') as pid, od.cid as cid, 0 as nview_cnt , count(*) as order_goods_cnt,  0 as escape_cart_pcnt,  0 as cart_total_cnt , sum(od.pt_dcprice) as ptprice
						from shop_order_detail od 
						where od.regdate  between '".$selected_date." 00:00:00' and '".$selected_date." 23:59:59'  
						and od.status NOT IN ('".implode("','",$non_sale_status)."') ";

            if(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1){
                $sql .= "and od.order_from = 'self'  ";
            }

            $sql .= " group by pid
						union
						Select LPAD(ss.pid,10,'0') as pid, ss.cid as cid, 0 as nview_cnt , 0 as order_goods_cnt, sum(step1) as escape_cart_pcnt , 0 as cart_total_cnt , 0 as ptprice
						from ".TBL_COMMERCE_SALESTACK." ss  	
						where ss.vdate = '".$vdate."' and step1 = '1' and step6 != '1'	
						group by pid
						union
						Select LPAD(ss.pid,10,'0') as pid, ss.cid as cid, 0 as nview_cnt , 0 as order_goods_cnt, 0 as escape_cart_pcnt , sum(step1) as cart_total_cnt , 0 as ptprice
						from ".TBL_COMMERCE_SALESTACK." ss  	
						where ss.vdate = '".$vdate."' and step1 = '1'  
						group by pid
						) data left join ".TBL_SHOP_PRODUCT." p on data.pid = p.id
						where  substr(data.cid,1,".(($depth)*3).") = '".substr($cid,0,(($depth)*3))."'
						";
            //where date_format(od.regdate,'%Y%m%d') = '".$vdate."'

            $sql .= "group by pid ";
            $sql .= $orderbyString;
        }

        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2 || $SelectReport == 4){
        if($SelectReport == "4"){
            $vdate = $search_sdate;
            $vweekenddate = $search_edate;
        }


        if($depth == 0){

            $sql = "select data.cid, data.pid, p.pname as pname, sum(nview_cnt) as nview_cnt, sum(order_goods_cnt) as order_goods_cnt, sum(escape_cart_pcnt) as escape_cart_pcnt, sum(cart_total_cnt) as cart_total_cnt, sum(ptprice) as ptprice,
						case when sum(cart_total_cnt) = 0 then 0 else sum(order_goods_cnt)/sum(cart_total_cnt)*100 end as cart_change_rate
						from 
						(Select LPAD(b.pid,10,'0') as pid, b.cid as cid, sum(b.nview_cnt) as nview_cnt , 0 as order_goods_cnt, 0 as escape_cart_pcnt , 0 cart_total_cnt , 0 as ptprice
						from ".TBL_COMMERCE_VIEWINGVIEW." b  	
						where b.vdate between '".$vdate."' and '".$vweekenddate."'	
						group by pid
						union
						select LPAD(od.pid,10,'0') as pid, od.cid as cid, 0 as nview_cnt , count(*) as order_goods_cnt,  0 as escape_cart_pcnt,  0 as cart_total_cnt , sum(od.pt_dcprice) as ptprice
						from shop_order_detail od 
						where od.regdate  between '".$selected_date." 00:00:00' and '".substr($vweekenddate, 0, 4)."-".substr($vweekenddate, 4, 2)."-".substr($vweekenddate, 6, 2)." 23:59:59' 
						and od.status NOT IN ('".implode("','",$non_sale_status)."') ";

            if(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1){
                $sql .= "and od.order_from = 'self'  ";
            }

            $sql .= " group by pid
						union
						Select LPAD(ss.pid,10,'0') as pid, ss.cid as cid, 0 as nview_cnt , 0 as order_goods_cnt, sum(step1) as escape_cart_pcnt , 0 as cart_total_cnt , 0 as ptprice
						from ".TBL_COMMERCE_SALESTACK." ss  	
						where ss.vdate between '".$vdate."' and '".$vweekenddate."' and step1 = '1'	and step6 != '1'	
						group by pid
						union
						Select LPAD(ss.pid,10,'0') as pid, ss.cid as cid, 0 as nview_cnt , 0 as order_goods_cnt, 0 as escape_cart_pcnt , sum(step1) as cart_total_cnt , 0 as ptprice
						from ".TBL_COMMERCE_SALESTACK." ss  	
						where ss.vdate between '".$vdate."' and '".$vweekenddate."' and step1 = '1'	 
						group by pid
						) data left join ".TBL_SHOP_PRODUCT." p on data.pid = p.id
						";
            //where date_format(od.regdate,'%Y%m%d') between '".$vdate."' and '".$vweekenddate."'

            $sql .= "group by pid ";
            $sql .= $orderbyString;

            //echo nl2br($sql);
        }else if($depth > 0){

            $sql = "select data.cid, data.pid, p.pname as pname, sum(nview_cnt) as nview_cnt, sum(order_goods_cnt) as order_goods_cnt, sum(escape_cart_pcnt) as escape_cart_pcnt, sum(cart_total_cnt) as cart_total_cnt, sum(ptprice) as ptprice,
						case when sum(cart_total_cnt) = 0 then 0 else sum(order_goods_cnt)/sum(cart_total_cnt)*100 end as cart_change_rate
						from 
						(Select LPAD(b.pid,10,'0') as pid, b.cid as cid, sum(b.nview_cnt) as nview_cnt , 0 as order_goods_cnt, 0 as escape_cart_pcnt , 0 cart_total_cnt , 0 as ptprice
						from ".TBL_COMMERCE_VIEWINGVIEW." b  	
						where b.vdate between '".$vdate."' and '".$vweekenddate."'	
						group by pid
						union
						select LPAD(od.pid,10,'0') as pid, od.cid as cid, 0 as nview_cnt , count(*) as order_goods_cnt,  0 as escape_cart_pcnt,  0 as cart_total_cnt , sum(od.pt_dcprice) as ptprice
						from shop_order_detail od 
						where od.regdate between '".substr($vdate,0,4)."-".substr($vdate,4,2)."-".substr($vdate,6,2)." 00:00:00' and '".substr($vweekenddate,0,4)."-".substr($vweekenddate,4,2)."-".substr($vweekenddate,6,2)." 23:59:59'	
						and od.status NOT IN ('".implode("','",$non_sale_status)."') ";

            if(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1){
                $sql .= "and od.order_from = 'self'  ";
            }

            $sql .= " group by pid
						union
						Select LPAD(ss.pid,10,'0') as pid, ss.cid as cid, 0 as nview_cnt , 0 as order_goods_cnt, sum(step1) as escape_cart_pcnt , 0 as cart_total_cnt , 0 as ptprice
						from ".TBL_COMMERCE_SALESTACK." ss  	
						where ss.vdate between '".$vdate."' and '".$vweekenddate."' and step1 = '1' and step6 != '1'	
						group by pid
						union
						Select LPAD(ss.pid,10,'0') as pid, ss.cid as cid, 0 as nview_cnt , 0 as order_goods_cnt, 0 as escape_cart_pcnt , sum(step1) as cart_total_cnt , 0 as ptprice
						from ".TBL_COMMERCE_SALESTACK." ss  	
						where ss.vdate between '".$vdate."' and '".$vweekenddate."' and step1 = '1'  
						group by pid
						) data left join ".TBL_SHOP_PRODUCT." p on data.pid = p.id
						where   substr(data.cid,1,".(($depth)*3).") = '".substr($cid,0,(($depth)*3))."'
						";

            $sql .= "group by pid ";
            $sql .= $orderbyString;
        }

        if($SelectReport == 2){
            $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
        }else if($SelectReport == 4){
            //echo $search_sdate;
            $s_week_num = date("w",mktime(0,0,0,substr($search_sdate,4,2),substr($search_sdate,6,2),substr($search_sdate,0,4)));
            $e_week_num = date("w",mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4)));
            $dateString = "기간별 : ".getNameOfWeekday($s_week_num,$search_sdate,"priodname")."~".getNameOfWeekday($e_week_num,$search_edate,"priodname");
        }
    }else if($SelectReport == 3){

        if($depth == 0){

            $sql = "select data.cid, data.pid, p.pname as pname, sum(nview_cnt) as nview_cnt, sum(order_goods_cnt) as order_goods_cnt, sum(escape_cart_pcnt) as escape_cart_pcnt, sum(cart_total_cnt) as cart_total_cnt, sum(ptprice) as ptprice,
						case when sum(cart_total_cnt) = 0 then 0 else sum(order_goods_cnt)/sum(cart_total_cnt)*100 end as cart_change_rate
						from 
						(Select LPAD(b.pid,10,'0') as pid, b.cid as cid, sum(b.nview_cnt) as nview_cnt , 0 as order_goods_cnt, 0 as escape_cart_pcnt , 0 cart_total_cnt , 0 as ptprice
						from ".TBL_COMMERCE_VIEWINGVIEW." b  	
						where b.vdate LIKE '".substr($vdate,0,6)."%' 			
						group by pid
						union
						select LPAD(od.pid,10,'0') as pid, od.cid as cid, 0 as nview_cnt , count(*) as order_goods_cnt,  0 as escape_cart_pcnt,  0 as cart_total_cnt , sum(od.pt_dcprice) as ptprice
						from shop_order_detail od 
						where od.regdate between '".substr($selected_date,0,7)."-01 00:00:00' and '".substr($selected_date,0,7)."-31 23:59:59' 
						and od.status NOT IN ('".implode("','",$non_sale_status)."') ";

            if(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1){
                $sql .= "and od.order_from = 'self'  ";
            }

            $sql .= " group by pid
						union
						Select LPAD(ss.pid,10,'0') as pid, ss.cid as cid, 0 as nview_cnt , 0 as order_goods_cnt, sum(step1) as escape_cart_pcnt , 0 as cart_total_cnt , 0 as ptprice
						from ".TBL_COMMERCE_SALESTACK." ss  	
						where ss.vdate LIKE '".substr($vdate,0,6)."%' and step1 = '1' and step6 != '1'	
						group by pid
						union
						Select LPAD(ss.pid,10,'0') as pid, ss.cid as cid, 0 as nview_cnt , 0 as order_goods_cnt, 0 as escape_cart_pcnt , sum(step1) as cart_total_cnt , 0 as ptprice
						from ".TBL_COMMERCE_SALESTACK." ss  	
						where ss.vdate LIKE '".substr($vdate,0,6)."%' and step1 = '1' 
						group by pid
						) data left join ".TBL_SHOP_PRODUCT." p on data.pid = p.id
						";
            //where date_format(od.regdate,'%Y%m%d') LIKE '".substr($vdate,0,6)."%'

            $sql .= "group by pid ";
            $sql .= $orderbyString;

            //echo nl2br($sql);
        }else if($depth > 0){

            $sql = "select data.cid, data.pid, p.pname as pname, sum(nview_cnt) as nview_cnt, sum(order_goods_cnt) as order_goods_cnt, sum(escape_cart_pcnt) as escape_cart_pcnt, sum(cart_total_cnt) as cart_total_cnt, sum(ptprice) as ptprice,
						case when sum(cart_total_cnt) = 0 then 0 else sum(order_goods_cnt)/sum(cart_total_cnt)*100 end as cart_change_rate
						from 
						(Select LPAD(b.pid,10,'0') as pid, b.cid as cid, sum(b.nview_cnt) as nview_cnt , 0 as order_goods_cnt, 0 as escape_cart_pcnt , 0 cart_total_cnt , 0 as ptprice
						from ".TBL_COMMERCE_VIEWINGVIEW." b  	
						where b.vdate LIKE '".substr($vdate,0,6)."%' 			
						group by pid
						union
						select LPAD(od.pid,10,'0') as pid, od.cid as cid, 0 as nview_cnt , count(*) as order_goods_cnt,  0 as escape_cart_pcnt,  0 as cart_total_cnt , sum(od.pt_dcprice) as ptprice
						from shop_order_detail od 
						where od.regdate between '".substr($selected_date,0,7)."-01 00:00:00' and '".substr($selected_date,0,7)."-31 23:59:59' 
						and od.status NOT IN ('".implode("','",$non_sale_status)."') ";

            if(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1){
                $sql .= "and od.order_from = 'self'  ";
            }

            $sql .= " group by pid
						union
						Select LPAD(ss.pid,10,'0') as pid, ss.cid as cid, 0 as nview_cnt , 0 as order_goods_cnt, sum(step1) as escape_cart_pcnt , 0 as cart_total_cnt , 0 as ptprice
						from ".TBL_COMMERCE_SALESTACK." ss  	
						where ss.vdate LIKE '".substr($vdate,0,6)."%' and step1 = '1' and step6 != '1'	
						group by pid
						union
						Select LPAD(ss.pid,10,'0') as pid, ss.cid as cid, 0 as nview_cnt , 0 as order_goods_cnt, 0 as escape_cart_pcnt , sum(step1) as cart_total_cnt , 0 as ptprice
						from ".TBL_COMMERCE_SALESTACK." ss  	
						where ss.vdate LIKE '".substr($vdate,0,6)."%' and step1 = '1' 
						group by pid
						) data left join ".TBL_SHOP_PRODUCT." p on data.pid = p.id
						where substr(data.cid,1,".(($depth)*3).") = '".substr($cid,0,(($depth)*3))."'
						";
            //where date_format(od.regdate,'%Y%m%d') LIKE '".substr($vdate,0,6)."%'


            $sql .= "group by pid ";
            $sql .= $orderbyString;

        }

        $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");
    }
    //echo $depth."<br>";
    //echo nl2br($sql);

    $fordb->query($sql);
    $total = $fordb->total;

    $str_page_bar = page_bar($total, $page, $max, "&max=$max","");


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

        $sheet->getActiveSheet(0)->mergeCells('A2:J2');
        $sheet->getActiveSheet(0)->setCellValue('A2', "장바구니 이탈상품 - 구매전환분석");
        $sheet->getActiveSheet(0)->mergeCells('A3:J3');
        $sheet->getActiveSheet(0)->setCellValue('A3', $dateString);

        $sheet->getActiveSheet(0)->mergeCells('A'.($i+1).':A'.($i+2));
        $sheet->getActiveSheet(0)->mergeCells('B'.($i+1).':B'.($i+2));
        $sheet->getActiveSheet(0)->mergeCells('C'.($i+1).':D'.($i+1));

        $sheet->getActiveSheet(0)->mergeCells('E'.($i+1).':j'.($i+1));

        $sheet->getActiveSheet(0)->setCellValue('A' . ($i+1), "순");
        $sheet->getActiveSheet(0)->setCellValue('B' . ($i+1), "카테고리명");
        $sheet->getActiveSheet(0)->setCellValue('C' . ($i+1), "상품조회정보");
        $sheet->getActiveSheet(0)->setCellValue('E' . ($i+1), "상품판매정보");

        $sheet->getActiveSheet(0)->setCellValue('C' . ($i+2), "조회횟수(a)");
        $sheet->getActiveSheet(0)->setCellValue('D' . ($i+2), "점유율");
        $sheet->getActiveSheet(0)->setCellValue('E' . ($i+2), "구매건수(b)");
        $sheet->getActiveSheet(0)->setCellValue('F' . ($i+2), "구매점유율");
        $sheet->getActiveSheet(0)->setCellValue('G' . ($i+2), "구매전환율(b/a)");
        $sheet->getActiveSheet(0)->setCellValue('H' . ($i+2), "상품수량");
        $sheet->getActiveSheet(0)->setCellValue('I' . ($i+2), "매출액");
        $sheet->getActiveSheet(0)->setCellValue('J' . ($i+2), "매출점유율");



        $sheet->setActiveSheetIndex(0);
        //$i = $i + 2;
        $order_goods_cnt_sum =0;
        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);

            $nview_cnt_sum += $fordb->dt['nview_cnt'];
            $order_goods_cnt_sum += $fordb->dt['order_goods_cnt'];
            $pcnt_sum += $fordb->dt['pcnt'];
            $ptprice_sum += $fordb->dt['ptprice'];

        }

        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);

            if($nview_cnt_sum > 0){
                $view_rate = $fordb->dt['nview_cnt']/$nview_cnt_sum; // 엑셀 포맷 정의시 자동으로 *100 이 처리됨
            }else{
                $view_rate = 0;
            }

            if($order_goods_cnt_sum > 0){
                $order_rate = $fordb->dt['order_goods_cnt']/$order_goods_cnt_sum; // 엑셀 포맷 정의시 자동으로 *100 이 처리됨
            }else{
                $order_rate = 0;
            }

            if($ptprice_sum > 0){
                $sale_rate = $fordb->dt['ptprice']/$ptprice_sum; // 엑셀 포맷 정의시 자동으로 *100 이 처리됨
            }else{
                $sale_rate = 0;
            }

            if($fordb->dt['nview_cnt'] > 0){
                $change_rate = $fordb->dt['order_goods_cnt']/$fordb->dt['nview_cnt'];
            }else{
                $change_rate = 0;
            }

            $pname = $fordb->dt['pname']." ";
            if($fordb->dt['cid'] != "" && $_COOKIE['category_view'] == 1){
                $pname .= ($fordb->dt['cid'] == "9999999999" ? "\r기타" : "\r".strip_tags(getCategoryPath($fordb->dt['cid'],4)));
            }

            $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 3), ($i + 1));
            $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 3), $pname);

            $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 3), $fordb->dt['nview_cnt']);
            $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 3), $view_rate);
            $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

            $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 3), $fordb->dt['order_goods_cnt']);
            $sheet->getActiveSheet()->setCellValue('F' . ($i + $start + 3), $order_rate);
            $sheet->getActiveSheet()->getStyle('F' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

            $sheet->getActiveSheet()->setCellValue('G' . ($i + $start + 3), $change_rate); // 엑셀 포맷 정의시 자동으로 *100 이 처리됨
            $sheet->getActiveSheet()->getStyle('G' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);


            $sheet->getActiveSheet()->setCellValue('H' . ($i + $start + 3), $fordb->dt['pcnt']);
            $sheet->getActiveSheet()->getStyle('H' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('I' . ($i + $start + 3), $fordb->dt['ptprice']);
            $sheet->getActiveSheet()->getStyle('I' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('J' . ($i + $start + 3), $sale_rate);
            $sheet->getActiveSheet()->getStyle('J' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);


            $nview_cnt_sum = $nview_cnt_sum + returnZeroValue($fordb->dt['nview_cnt']);
            $order_change_rate_sum += $change_rate*100;


        }

        //$i++;
        $sheet->getActiveSheet(0)->mergeCells('A'.($i + $start + 3).':B'.($i+ $start+3));
        $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 3), '합계');
        //$sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 3), getIventoryCategoryPathByAdmin($goods_infos[$i]['cid'], 4));
        $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 3), $nview_cnt_sum);
        $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 3), "100%");
        $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 3), $order_goods_cnt_sum);
        $sheet->getActiveSheet()->setCellValue('F' . ($i + $start + 3), "100%");
        $sheet->getActiveSheet()->setCellValue('G' . ($i + $start + 3), number_format($order_change_rate_sum,1));
        $sheet->getActiveSheet()->setCellValue('H' . ($i + $start + 3), $pcnt_sum);
        $sheet->getActiveSheet()->setCellValue('I' . ($i + $start + 3), $ptprice_sum);
        $sheet->getActiveSheet()->setCellValue('J' . ($i + $start + 3), "100%");




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
        $sheet->getActiveSheet()->getColumnDimension('L')->setWidth(10);
        $sheet->getActiveSheet()->getColumnDimension('M')->setWidth(15);

        $sheet->getActiveSheet()->getRowDimension($start+2)->setRowHeight(30);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.iconv("utf-8","cp949","장바구니 이탈상품_구매전환분석.xls").'"');
        header('Cache-Control: max-age=0');



        // $objWriter->setUseInlineCSS(true);
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $sheet->getActiveSheet()->getStyle('A'.($start+1).':J'.($i+$start+3))->applyFromArray($styleArray);
        $sheet->getActiveSheet()->getStyle('A'.$start.':J'.($i+$start+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $sheet->getActiveSheet()->getStyle('A'.($start).':J'.($i+$start+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getActiveSheet()->getStyle('A'.($start).':J'.($start+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setWrapText(true); // 타이틀 영역 가운데 정렬
        $sheet->getActiveSheet()->getStyle('B'.($start+3).':B'.($i+$start+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)->setWrapText(true); ; // 카테고리 영역 왼쪽 정렬

        $sheet->getActiveSheet()->getStyle('A'.$start.':J'.($i+$start+3))->getFont()->setSize(10)->setName('돋움');  // 리포트 영역 폰트 설정

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
    if(false) {
        $exce_down_str = "<img src=\"../../images/korea/btn_print.gif\" onclick=\"PopSWindow('?mode=print&" . str_replace("&mode=iframe", "", $_SERVER["QUERY_STRING"]) . "',1150,500,'logstory_print');\" style=\"margin-right:3px;cursor:pointer;\"  >";
    }

    $exce_down_str .= "<a href='?mode=excel&".str_replace("&mode=iframe", "",$_SERVER["QUERY_STRING"])."'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_excel_save.gif' border=0></a>";
    //$mstring = $mstring.TitleBar("장바구니 이탈상품 : ".($cid ? getCategoryPath($cid,4):""),$dateString);
    $mstring = "<table width='100%' border=0>";
    if(isset($_GET["mode"]) && ($_GET["mode"] == "pop" || $_GET["mode"] == "print")){
        $mstring .= "<tr><td colspan=2>".TitleBar("장바구니 이탈상품 : ",$dateString."".($cid ? "-".getCategoryPath($cid,4):""),false, $exce_down_str)."</td></tr>";
    }else{
        $mstring .= "<tr><td colspan=2>".TitleBar("장바구니 이탈상품 : ",$dateString."".($cid ? "-".getCategoryPath($cid,4):""),true, $exce_down_str)."</td></tr>";
    }
    $mstring .= "<tr>
						<td height=30><input type=checkbox name='category_view' id='category_view' value='1' onclick=\"reloadView()\"  ".($_COOKIE['category_view'] == 1 ? "checked":"")." ><label for='category_view'> 카테고리 함께보기</label>
						<input type=checkbox name='view_self_sale' id='view_self_sale' value='1' onclick=\"reloadSaleView()\"  ".(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1 ? "checked":"")." ><label for='view_self_sale'> 자사매출만 보기</label>
						</td>
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
						<td class=s_td rowspan=2>순</td>
						<td class=m_td rowspan=2>상품명 <br>".OrderByLink("카테고리명", "cid", $ordertype)."</td>
						<td class=m_td colspan=2>상품조회정보</td>
						<td class=m_td colspan=2>상품판매정보</td>
						<td class=m_td colspan=3>장바구니 정보</td>
						<td class=m_td colspan=4>상품판매 정보</td>
						
					   </tr>\n";
    $mstring .= "<tr height=30 align=center>
							<td class=m_td >".OrderByLink("조회수(a)", "nview_cnt", $ordertype)."</td>
							<td class=m_td >점유율</td>
							<td class=m_td >".OrderByLink("구매건수(b)", "order_goods_cnt", $ordertype)."</td>
							<td class=m_td >조회<br>구매전환율(b/a)</td>
							<td class=m_td >".OrderByLink("전체담은건수(c)", "cart_total_cnt", $ordertype)."</td>
							<td class=m_td >".OrderByLink("장바구니<br>이탈횟수", "escape_cart_pcnt", $ordertype)."</td>
							<td class=m_td >이탈율</td>
							<td class=m_td >이탈비율<br>(구매건/이탈건)</td>
							<td class=m_td >".OrderByLink("장바구니<br>구매전환율", "cart_change_rate", $ordertype)." </td>
							<td class=m_td >매출액</td>
							<td class=m_td >매출액<br>점유율</td>
							<!--td class=m_td >프로모션</td-->
							<!--td class=m_td >쿠폰발급/사용/사용율</td-->
							<!--td class=e_td >SMS 발송/확인</td-->
						</tr>\n";
    $order_goods_cnt_sum = 0;
    $cart_total_cnt_sum = 0;
    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);

        $nview_cnt_sum += $fordb->dt['nview_cnt'];
        $order_goods_cnt_sum += $fordb->dt['order_goods_cnt'];
        $pcnt_sum += $fordb->dt['pcnt'];
        $ptprice_sum += $fordb->dt['ptprice'];

    }

    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);

        if($nview_cnt_sum > 0){
            $view_rate = $fordb->dt['nview_cnt']/$nview_cnt_sum*100;
        }else{
            $view_rate = 0;
        }

        if($order_goods_cnt_sum > 0){
            $order_rate = $fordb->dt['order_goods_cnt']/$order_goods_cnt_sum*100;
        }else{
            $order_rate = 0;
        }

        if($ptprice_sum > 0){
            $sale_rate = $fordb->dt['ptprice']/$ptprice_sum*100;
        }else{
            $sale_rate = 0;
        }

        if($fordb->dt['nview_cnt'] > 0){
            $change_rate = $fordb->dt['order_goods_cnt']/$fordb->dt['nview_cnt']*100;
        }else{
            $change_rate = 0;
        }

        $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
		<td class='list_box_td list_bg_gray' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($i+1)."</td>
		<td class='list_box_td point' style='text-align:left;line-height:150%;padding:5px;' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">
		".$fordb->dt['pname']." <br>";
        if($fordb->dt['cid'] != "" && $_COOKIE['category_view'] == 1){
            $mstring .= "<span style='font-weight:normal'>".($fordb->dt['cid'] == "9999999999" ? "기타" : strip_tags(getCategoryPath($fordb->dt['cid'],4)))." </span>";
        }
        $mstring .= "
		</td>
		<td class='list_box_td  number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(returnZeroValue($fordb->dt['nview_cnt']),0)."&nbsp;</td>
		<td class='list_box_td list_bg_gray number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(returnZeroValue($view_rate),1)."%</td>
		<td class='list_box_td  number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(returnZeroValue($fordb->dt['order_goods_cnt']),0)."&nbsp;</td>
		<td class='list_box_td list_bg_gray number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(returnZeroValue($order_rate),1)."%</td>
		<td class='list_box_td point number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['cart_total_cnt'])."</td>
		<td class='list_box_td  number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(returnZeroValue($fordb->dt['escape_cart_pcnt']),0)."&nbsp;</td>
		<td class='list_box_td  number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($fordb->dt['cart_total_cnt'] > 0 ? number_format(($fordb->dt['escape_cart_pcnt']/$fordb->dt['cart_total_cnt'])*100,0)."%":"-")."&nbsp;</td>
		<td class='list_box_td  number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($fordb->dt['escape_cart_pcnt'] > 0 ? number_format(($fordb->dt['order_goods_cnt']/$fordb->dt['escape_cart_pcnt'])*100,0)."%":"-")."&nbsp;</td>
		<td class='list_box_td list_bg_gray number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(returnZeroValue($fordb->dt['cart_change_rate']))." %</td>
		
		<td class='list_box_td point number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['ptprice'])."</td>
		<td class='list_box_td list_bg_gray number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(returnZeroValue($sale_rate),1)."%</td>
		<!--td class='list_box_td list_bg_gray number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='text-align:center;padding:5px;'>
			 <img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_sms.gif' align=absmiddle onclick=\"PoPWindow('../../sms.pop.php?pid=".$fordb->dt['pid']."&vdate=".$vdate."&SelectReport=".$SelectReport."',500,380,'sendsms')\" style='cursor:pointer;margin:1px;' alt='문자발송' title='문자발송'>
        	 <img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_email.gif' align=absmiddle onclick=\"PoPWindow('../../mail.pop.php?pid=".$fordb->dt['pid']."&vdate=".$vdate."&SelectReport=".$SelectReport."',550,535,'sendmail')\" style='cursor:pointer;margin:1px;' alt='이메일발송' title='이메일발송'><br>
			 
			 <img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_popup.gif' align=absmiddle onclick=\"PoPWindow('../../display/popup.write.php?mmode=pop&target_type=cart&pid=".$fordb->dt['pid']."&vdate=".$vdate."&SelectReport=".$SelectReport."',800,680,'popupwrite')\" style='cursor:pointer;margin:1px;' alt='팝업설정' title='팝업설정'>
			 <img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_coupon.gif' align=absmiddle onclick=\"PoPWindow('../../sms.pop.php?mmode=pop&target_type=cart&pid=".$fordb->dt['pid']."&vdate=".$vdate."&SelectReport=".$SelectReport."',500,380,'sendsms')\" style='cursor:pointer;margin:1px;' alt='쿠폰발송' title='쿠폰발송'>
        	 <img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_mem_list.gif' align=absmiddle onclick=\"PoPWindow('../../mail.pop.php?mmode=pop&target_type=cart&pid=".$fordb->dt['pid']."&vdate=".$vdate."&SelectReport=".$SelectReport."',550,535,'sendmail')\" style='cursor:pointer;margin:1px;' alt='회원목록' title='회원목록'>
		</td--> 
		</tr>\n";

        $nview_cnt_sum = $nview_cnt_sum + returnZeroValue($fordb->dt['nview_cnt']);

        $order_goods_cnt_sum +=  returnZeroValue($fordb->dt['order_goods_cnt']);
        $cart_total_cnt_sum +=  returnZeroValue($fordb->dt['cart_total_cnt']);

        $order_change_rate_sum += $change_rate;


    }

    if ($nview_cnt_sum == 0){
        $mstring .= "<tr  align=center height=200><td colspan=10 class='list_box_td'  >결과값이 없습니다.</td></tr>\n";
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
	<td class=s_td align=center colspan=2>합계</td>
	<td class=e_td style='padding-right:20px;'>".number_format($nview_cnt_sum,0)."</td>
	<td class=e_td style='padding-right:20px;'>100%</td>
	<td class=e_td style='padding-right:20px;'>".number_format($order_goods_cnt_sum,0)."</td>
	<td class=e_td style='padding-right:20px;'>".number_format(($nview_cnt_sum > 0 ? $order_goods_cnt_sum/$nview_cnt_sum*100:0),1)."%</td>
	<td class=e_td style='padding-right:20px;'>".number_format($cart_total_cnt_sum)."</td>
	<td class=e_td style='padding-right:20px;'>".number_format(($cart_total_cnt_sum > 0 ? $cart_total_cnt_sum/$cart_total_cnt_sum*100:0))."%</td>
	<td class=e_td style='padding-right:20px;'>".number_format($order_goods_cnt_sum,0)."</td>
	<td class=e_td style='padding-right:20px;'>".number_format($ptprice_sum,0)."</td>
	<td class=e_td style='padding-right:20px;'>100%</td>
	<td class=e_td style='padding-right:20px;'>-</td>
	<td class=e_td style='padding-right:20px;'>-</td>
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


    $mstring .= HelpBox("장바구니 이탈상품", $help_text);
    return $mstring;
}
?>