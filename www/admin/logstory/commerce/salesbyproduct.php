<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../include/ReportReferTree.php");
include("../include/commerce.lib.php");


function ReportTable($vdate,$SelectReport=1){

    global $depth,$cid, $non_sale_status, $order_status, $cancel_status, $return_status, $all_sale_status;
    global $search_sdate, $search_edate, $report_type;
    $nview_cnt = 0;
    //$cid = $referer_id;



    $order_sale_sum = 0;
    $real_sale_coprice_sum = 0;
    $order_sale_cnt = 0;
    $sale_all_cnt = 0;
    $sale_all_sum = 0;
    $real_sale_cnt_sum = 0;
    $cancel_sale_sum = 0;
    $sale_coprice_sum = 0;
    $cancel_sale_cnt = 0;
    $margin_sum = 0;
    $return_sale_cnt = 0;
    $return_sale_sum = 0;

    if($SelectReport == ""){
        $SelectReport = 1;
    }
    $fordb = new forbizDatabase();
    if($depth === ""){
        $depth = 0;
    }else{
        $depth = $_GET['depth']+1;
    }
    if($SelectReport == "4"){
        $vdate = $search_sdate;
        $vweekenddate = $search_edate;
    }


    if($vdate == ""){
        $vdate = date("Ymd", time());
        $vyesterday = date("Ymd", time()-84600);
        $voneweekago = date("Ymd", time()-84600*7);
        $search_sdate = date("Ymd", time()-84600*7);
        $search_edate = date("Ymd", time());
    }else{
        if($SelectReport ==3){
            $vdate = $vdate."01";
        }
        $vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
        $vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
    }

    if(isset($_GET["orderby"]) && $_GET["orderby"] != "" && isset($_GET["ordertype"]) && $_GET["ordertype"] != ""){
        $orderbyString = " order by ".$_GET["orderby"]." ".$_GET["ordertype"].", real_sale_sum desc ";
    }else{
        $orderbyString = " order by order_sale_cnt desc  ";
    }

    if(isset($_GET["max"])){
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
    $limit_str = "	limit $start, $max ";

    $coprice_str = "case when (od.commission > 0 and od.coprice = 0) then od.psprice-(od.psprice*od.commission/100) else od.coprice end ";

    if ($SelectReport == 1){
        if($depth == "" || $depth < 0){

            $sql = "Select IFNULL(od.pid,'9999999999') as pid, od.cid as cid,  IFNULL(concat(od.pname,' ',od.add_info),'기타') as pname, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_sale_cnt, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pt_dcprice else 0 end) as order_sale_sum, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then ".$coprice_str." *od.pcnt else 0 end) as order_coprice_sum, 
							
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) as sale_all_cnt, 
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) as sale_all_sum, 
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as coprice_all_sum, 

							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt, 
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end) as cancel_sale_sum, 
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as cancel_coprice_sum, 

							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt, 
							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pt_dcprice else 0 end) as return_sale_sum, 
							sum(case when od.status IN ('".implode("','",$return_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as return_coprice_sum, 
							sum(od.pcnt) as amount_cnt, sum(od.pt_dcprice) as sale_sum, 
							(sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) 
							- sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end)
							- sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end)
							) as real_sale_cnt,
							(sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) 
							- sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end)
							- sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pt_dcprice else 0 end)
							) as real_sale_sum
							from  shop_order_detail od 							
							where od.regdate between '".substr($vdate,0,4)."-".substr($vdate,4,2)."-".substr($vdate,6,2)." 00:00:00' and '".substr($vdate,0,4)."-".substr($vdate,4,2)."-".substr($vdate,6,2)." 23:59:59'
							and od.status NOT IN ('".implode("','",$non_sale_status)."')  ";

            if(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1){
                $sql .= "and od.order_from = 'self'  ";
            }
            //date_format(od.regdate,'%Y%m%d')  = '".$vdate."'


            $sql .= "group by od.pid ";
            $sql .= $orderbyString;
            $union_sql = "".$sql;
            $sql .= $limit_str;

            $sql = "select * from ( ".$sql." ) b ";



            $sql .= "\n union all \n select  pid, cid, '외 합계' as pname,  
										sum(order_sale_cnt) as order_sale_cnt,
										sum(order_sale_sum) as order_sale_sum,
										sum(order_coprice_sum) as order_coprice_sum,
										sum(sale_all_cnt) as sale_all_cnt,
										sum(sale_all_sum) as sale_all_sum,
										sum(coprice_all_sum) as coprice_all_sum,

										sum(cancel_sale_cnt) as cancel_sale_cnt,
										sum(cancel_sale_sum) as cancel_sale_sum,
										sum(cancel_coprice_sum) as cancel_coprice_sum,

										sum(return_sale_cnt) as return_sale_cnt,
										sum(return_sale_sum) as return_sale_sum,
										sum(return_coprice_sum) as return_coprice_sum,
										sum(amount_cnt) as amount_cnt,
										sum(sale_sum) as sale_sum,
										sum(real_sale_cnt) as real_sale_cnt,
										sum(real_sale_sum) as real_sale_sum from ( ".$union_sql."";
            //$sql .= $orderbyString;
            //$sql .= "group by od.pid ";
            $sql .= " limit $max, 100 ) a";

            //$sql .= $orderbyString;
            //echo "<pre>";
            //echo nl2br($sql);
            //exit;
        }else if($depth >= 0){
            $sql = "Select IFNULL(od.pid,'9999999999') as pid, od.cid as cid,    IFNULL(concat(od.pname,' ',od.add_info),'기타') as pname,
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_sale_cnt, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pt_dcprice else 0 end) as order_sale_sum, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as order_coprice_sum, 
							
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) as sale_all_cnt, 
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) as sale_all_sum, 
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as coprice_all_sum, 

							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt, 
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end) as cancel_sale_sum, 
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as cancel_coprice_sum, 

							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt, 
							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pt_dcprice else 0 end) as return_sale_sum, 
							sum(case when od.status IN ('".implode("','",$return_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as return_coprice_sum, 

							sum(od.pcnt) as amount_cnt, sum(od.pt_dcprice) as sale_sum, 
							(sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) 
							- sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end)
							- sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end)
							) as real_sale_cnt,
							(sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) 
							- sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end)
							- sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pt_dcprice else 0 end)
							) as real_sale_sum
							from  shop_order_detail od 							
							where od.regdate between '".substr($vdate,0,4)."-".substr($vdate,4,2)."-".substr($vdate,6,2)." 00:00:00' and '".substr($vdate,0,4)."-".substr($vdate,4,2)."-".substr($vdate,6,2)." 23:59:59' 
							and od.status NOT IN ('".implode("','",$non_sale_status)."') 
							and substr(od.cid,1,".(($depth)*3).") = '".substr($cid,0,(($depth)*3))."' ";

            if(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1){
                $sql .= "and od.order_from = 'self'  ";
            }
            //date_format(od.regdate,'%Y%m%d')  = '".$vdate."'

            $sql .= "group by od.pid ";

            $sql .= $orderbyString;

            $union_sql = "".$sql;
            $sql .= $limit_str;

            $sql = "select * from ( ".$sql." ) b ";



            $sql .= "\n union all \n select  pid, cid, '외 합계' as pname,  
										sum(order_sale_cnt) as order_sale_cnt,
										sum(order_sale_sum) as order_sale_sum,
										sum(order_coprice_sum) as order_coprice_sum,
										sum(sale_all_cnt) as sale_all_cnt,
										sum(sale_all_sum) as sale_all_sum,
										sum(coprice_all_sum) as coprice_all_sum,

										sum(cancel_sale_cnt) as cancel_sale_cnt,
										sum(cancel_sale_sum) as cancel_sale_sum,
										sum(cancel_coprice_sum) as cancel_coprice_sum,

										sum(return_sale_cnt) as return_sale_cnt,
										sum(return_sale_sum) as return_sale_sum,
										sum(return_coprice_sum) as return_coprice_sum,
										sum(amount_cnt) as amount_cnt,
										sum(sale_sum) as sale_sum,
										sum(real_sale_cnt) as real_sale_cnt,
										sum(real_sale_sum) as real_sale_sum from ( ".$union_sql."";
            //$sql .= $orderbyString;
            //$sql .= "group by od.pid ";
            $sql .= " limit $max, 100 ) a";

        }
        $weekNum = date('w',strtotime($vdate));
        $dateString = "일간 : ". getNameOfWeekday($weekNum,$vdate,"dayname");
    }else if($SelectReport == 2 || $SelectReport == 4){
        if($SelectReport == "4"){
            $vdate = $search_sdate;
            $vweekenddate = $search_edate;
        }

        if($depth === '' || $depth < 0){
            $sql = "Select IFNULL(od.pid,'9999999999') as pid, od.cid as cid,    IFNULL(concat(od.pname,' ',od.add_info),'기타') as pname, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_sale_cnt, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pt_dcprice else 0 end) as order_sale_sum, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as order_coprice_sum, 
							
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) as sale_all_cnt, 
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) as sale_all_sum, 
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as coprice_all_sum, 

							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt, 
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end) as cancel_sale_sum, 
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as cancel_coprice_sum, 

							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt, 
							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pt_dcprice else 0 end) as return_sale_sum, 
							sum(case when od.status IN ('".implode("','",$return_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as return_coprice_sum, 

							sum(od.pcnt) as amount_cnt, sum(od.pt_dcprice) as sale_sum, 
							(sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) 
							- sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end)
							- sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end)
							) as real_sale_cnt,
							(sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) 
							- sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end)
							- sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pt_dcprice else 0 end)
							) as real_sale_sum
							from  shop_order_detail od 							
							where od.regdate between '".substr($vdate,0,4)."-".substr($vdate,4,2)."-".substr($vdate,6,2)." 00:00:00' and '".substr($vweekenddate,0,4)."-".substr($vweekenddate,4,2)."-".substr($vweekenddate,6,2)." 23:59:59' 
							and od.status NOT IN ('".implode("','",$non_sale_status)."')  ";

            if(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1){
                $sql .= "and od.order_from = 'self'  ";
            }
            //date_format(od.regdate,'%Y%m%d')  between '".$vdate."' and '".$vweekenddate."'
            $sql .= "group by od.pid ";

            $sql .= $orderbyString;

            $union_sql = "".$sql;
            $sql .= $limit_str;

            $sql = "select * from ( ".$sql." ) b ";



            $sql .= "\n union all \n select  pid, cid, '외 합계' as pname,  
										sum(order_sale_cnt) as order_sale_cnt,
										sum(order_sale_sum) as order_sale_sum,
										sum(order_coprice_sum) as order_coprice_sum,
										sum(sale_all_cnt) as sale_all_cnt,
										sum(sale_all_sum) as sale_all_sum,
										sum(coprice_all_sum) as coprice_all_sum,

										sum(cancel_sale_cnt) as cancel_sale_cnt,
										sum(cancel_sale_sum) as cancel_sale_sum,
										sum(cancel_coprice_sum) as cancel_coprice_sum,

										sum(return_sale_cnt) as return_sale_cnt,
										sum(return_sale_sum) as return_sale_sum,
										sum(return_coprice_sum) as return_coprice_sum,
										sum(amount_cnt) as amount_cnt,
										sum(sale_sum) as sale_sum,
										sum(real_sale_cnt) as real_sale_cnt,
										sum(real_sale_sum) as real_sale_sum from ( ".$union_sql."";
            //$sql .= $orderbyString;
            //$sql .= "group by od.pid ";
            $sql .= " limit $max, 100 ) a";


        }else if($depth >= 0){
            $sql = "Select IFNULL(od.pid,'9999999999') as pid, od.cid as cid,    IFNULL(concat(od.pname,' ',od.add_info),'기타') as pname, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_sale_cnt, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pt_dcprice else 0 end) as order_sale_sum, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as order_coprice_sum, 
							
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) as sale_all_cnt, 
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) as sale_all_sum, 
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as coprice_all_sum, 

							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt, 
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end) as cancel_sale_sum, 
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as cancel_coprice_sum, 

							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt, 
							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pt_dcprice else 0 end) as return_sale_sum, 
							sum(case when od.status IN ('".implode("','",$return_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as return_coprice_sum, 

							sum(od.pcnt) as amount_cnt, sum(od.pt_dcprice) as sale_sum, 
							(sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) 
							- sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end)
							- sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end)
							) as real_sale_cnt,
							(sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) 
							- sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end)
							- sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pt_dcprice else 0 end)
							) as real_sale_sum
							from  shop_order_detail od 							
							where od.regdate between '".substr($vdate,0,4)."-".substr($vdate,4,2)."-".substr($vdate,6,2)." 00:00:00' and '".substr($vweekenddate,0,4)."-".substr($vweekenddate,4,2)."-".substr($vweekenddate,6,2)." 23:59:59' 
							and od.status NOT IN ('".implode("','",$non_sale_status)."') 
							and substr(od.cid,1,".(($depth)*3).") = '".substr($cid,0,(($depth)*3))."' ";

            if(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1){
                $sql .= "and od.order_from = 'self'  ";
            }

            //date_format(od.regdate,'%Y%m%d')  between '".$vdate."' and '".$vweekenddate."'

            $sql .= "group by od.pid ";

            $sql .= $orderbyString;

            $union_sql = "".$sql;
            $sql .= $limit_str;

            $sql = "select * from ( ".$sql." ) b ";



            $sql .= "\n union all \n select  pid, cid, '외 합계' as pname,  
										sum(order_sale_cnt) as order_sale_cnt,
										sum(order_sale_sum) as order_sale_sum,
										sum(order_coprice_sum) as order_coprice_sum,
										sum(sale_all_cnt) as sale_all_cnt,
										sum(sale_all_sum) as sale_all_sum,
										sum(coprice_all_sum) as coprice_all_sum,

										sum(cancel_sale_cnt) as cancel_sale_cnt,
										sum(cancel_sale_sum) as cancel_sale_sum,
										sum(cancel_coprice_sum) as cancel_coprice_sum,

										sum(return_sale_cnt) as return_sale_cnt,
										sum(return_sale_sum) as return_sale_sum,
										sum(return_coprice_sum) as return_coprice_sum,
										sum(amount_cnt) as amount_cnt,
										sum(sale_sum) as sale_sum,
										sum(real_sale_cnt) as real_sale_cnt,
										sum(real_sale_sum) as real_sale_sum from ( ".$union_sql."";
            //$sql .= $orderbyString;
            //$sql .= "group by od.pid ";
            $sql .= " limit $max, 100 ) a";

        }

        if($SelectReport == 2){
            $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate,'dayname');
        }else if($SelectReport == 4){
            //echo $search_sdate;
            $s_week_num = date("w",mktime(0,0,0,substr($search_sdate,4,2),substr($search_sdate,6,2),substr($search_sdate,0,4)));
            $e_week_num = date("w",mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4)));
            $dateString = "기간별 : ".getNameOfWeekday($s_week_num,$search_sdate,"priodname")."~".getNameOfWeekday($e_week_num,$search_edate,"priodname");
        }
    }else if($SelectReport == 3){
        if($depth == '' || $depth < 0 ){


            $sql = "Select IFNULL(od.pid,'9999999999') as pid, od.cid as cid,    IFNULL(concat(od.pname,' ',od.add_info),'기타') as pname, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_sale_cnt, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pt_dcprice else 0 end) as order_sale_sum, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as order_coprice_sum, 
							
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) as sale_all_cnt, 
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) as sale_all_sum, 
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as coprice_all_sum, 

							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt, 
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end) as cancel_sale_sum, 
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as cancel_coprice_sum, 

							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt, 
							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pt_dcprice else 0 end) as return_sale_sum, 
							sum(case when od.status IN ('".implode("','",$return_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as return_coprice_sum, 

							sum(od.pcnt) as amount_cnt, sum(od.pt_dcprice) as sale_sum,  
							(sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) 
							- sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end)
							- sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end)
							) as real_sale_cnt,
							(sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) 
							- sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end)
							- sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pt_dcprice else 0 end)
							) as real_sale_sum
							from  shop_order_detail od 							
							where od.regdate between '".substr($vdate,0,4)."-".substr($vdate,4,2)."-01 00:00:00' and '".substr($vdate,0,4)."-".substr($vdate,4,2)."-31 23:59:59'  
							and od.status NOT IN ('".implode("','",$non_sale_status)."')  ";

            if(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1){
                $sql .= "and od.order_from = 'self'  ";
            }

            //date_format(od.regdate,'%Y%m%d') LIKE '".substr($vdate,0,6)."%'
            $sql .= "group by od.pid ";

            $sql .= $orderbyString;

            $union_sql = "".$sql;
            $sql .= $limit_str;

            $sql = "select * from ( ".$sql." ) b ";



            $sql .= "\n union all \n select  pid, cid, '외 합계' as pname,  
										sum(order_sale_cnt) as order_sale_cnt,
										sum(order_sale_sum) as order_sale_sum,
										sum(order_coprice_sum) as order_coprice_sum,
										sum(sale_all_cnt) as sale_all_cnt,
										sum(sale_all_sum) as sale_all_sum,
										sum(coprice_all_sum) as coprice_all_sum,

										sum(cancel_sale_cnt) as cancel_sale_cnt,
										sum(cancel_sale_sum) as cancel_sale_sum,
										sum(cancel_coprice_sum) as cancel_coprice_sum,

										sum(return_sale_cnt) as return_sale_cnt,
										sum(return_sale_sum) as return_sale_sum,
										sum(return_coprice_sum) as return_coprice_sum,
										sum(amount_cnt) as amount_cnt,
										sum(sale_sum) as sale_sum,
										sum(real_sale_cnt) as real_sale_cnt,
										sum(real_sale_sum) as real_sale_sum from ( ".$union_sql."";
            //$sql .= $orderbyString;
            //$sql .= "group by od.pid ";
            $sql .= " limit $max, 100 ) a";

            //echo nl2br($sql);
            //exit;

        }else if($depth >= 0){
            $depth = $depth;
            $sql = "Select IFNULL(od.pid,'9999999999') as pid, od.cid as cid,    IFNULL(concat(od.pname,' ',od.add_info),'기타') as pname, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_sale_cnt, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pt_dcprice else 0 end) as order_sale_sum, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as order_coprice_sum, 
							
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) as sale_all_cnt, 
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) as sale_all_sum, 
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as coprice_all_sum, 

							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt, 
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end) as cancel_sale_sum, 
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as cancel_coprice_sum, 

							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt, 
							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pt_dcprice else 0 end) as return_sale_sum, 
							sum(case when od.status IN ('".implode("','",$return_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as return_coprice_sum, 

							sum(od.pcnt) as amount_cnt, sum(od.pt_dcprice) as sale_sum, 
							(sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) 
							- sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end)
							- sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end)
							) as real_sale_cnt,
							(sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) 
							- sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end)
							- sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pt_dcprice else 0 end)
							) as real_sale_sum
							from  shop_order_detail od 							
							where od.regdate between '".substr($vdate,0,4)."-".substr($vdate,4,2)."-01 00:00:00' and '".substr($vdate,0,4)."-".substr($vdate,4,2)."-31 23:59:59' 
							and od.status NOT IN ('".implode("','",$non_sale_status)."') 							
							and substr(od.cid,1,".(($depth)*3).") = '".substr($cid,0,(($depth)*3))."' ";

            if(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1){
                $sql .= "and od.order_from = 'self'  ";
            }
            // date_format(od.regdate,'%Y%m%d') LIKE '".substr($vdate,0,6)."%'
            $sql .= "group by od.pid ";

            $sql .= $orderbyString;

            $union_sql = "".$sql;
            $sql .= $limit_str;

            $sql = "select * from ( ".$sql." ) b ";



            $sql .= "\n union all \n select  pid, cid, '외 합계' as pname,  
										sum(order_sale_cnt) as order_sale_cnt,
										sum(order_sale_sum) as order_sale_sum,
										sum(order_coprice_sum) as order_coprice_sum,
										sum(sale_all_cnt) as sale_all_cnt,
										sum(sale_all_sum) as sale_all_sum,
										sum(coprice_all_sum) as coprice_all_sum,

										sum(cancel_sale_cnt) as cancel_sale_cnt,
										sum(cancel_sale_sum) as cancel_sale_sum,
										sum(cancel_coprice_sum) as cancel_coprice_sum,

										sum(return_sale_cnt) as return_sale_cnt,
										sum(return_sale_sum) as return_sale_sum,
										sum(return_coprice_sum) as return_coprice_sum,
										sum(amount_cnt) as amount_cnt,
										sum(sale_sum) as sale_sum,
										sum(real_sale_cnt) as real_sale_cnt,
										sum(real_sale_sum) as real_sale_sum from ( ".$union_sql."";
            //$sql .= $orderbyString;
            //$sql .= "group by od.pid ";
            $sql .= " limit $max, 100 ) a";


        }

        $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");
    }
    //echo "cid:".$cid."<br>";
    //echo "depth:".$depth."<br>";
    //echo time()."<br>";

    if($_SERVER["REMOTE_ADDR"] == "175.209.244.68"){
        //echo nl2br($sql);
        //exit;
    }

    if($sql){
        $fordb->query($sql);
    }



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
        $sheet->getActiveSheet(0)->setCellValue('A2', "상품판매종합 분석");
        $sheet->getActiveSheet(0)->mergeCells('A3:O3');
        $sheet->getActiveSheet(0)->setCellValue('A3', $dateString);

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

        $sheet->getActiveSheet(0)->setCellValue('A' . ($i+1), "순");
        $sheet->getActiveSheet(0)->setCellValue('B' . ($i+1), "상품명\r카테고리명");
        $sheet->getActiveSheet(0)->setCellValue('C' . ($i+1), "주문매출");
        $sheet->getActiveSheet(0)->setCellValue('E' . ($i+1), "매출");
        $sheet->getActiveSheet(0)->setCellValue('M' . ($i+1), "실매출액원가");
        $sheet->getActiveSheet(0)->setCellValue('N' . ($i+1), "수익");

        $sheet->getActiveSheet(0)->setCellValue('C' . ($i+2), "전체주문\r(입금예정포함)");
        $sheet->getActiveSheet(0)->setCellValue('E' . ($i+2), "전체매출액(전체)");
        $sheet->getActiveSheet(0)->setCellValue('G' . ($i+2), "취소매출액(-)");
        $sheet->getActiveSheet(0)->setCellValue('I' . ($i+2), "반품매출액(-)");
        $sheet->getActiveSheet(0)->setCellValue('K' . ($i+2), "실매출액(+)");


        $sheet->getActiveSheet(0)->setCellValue('C' . ($i+3), "수량(개)");
        $sheet->getActiveSheet(0)->setCellValue('D' . ($i+3), "주문액(원)");
        $sheet->getActiveSheet(0)->setCellValue('E' . ($i+3), "수량(개)");
        $sheet->getActiveSheet(0)->setCellValue('F' . ($i+3), "주문액(원)");
        $sheet->getActiveSheet(0)->setCellValue('G' . ($i+3), "수량(개)");
        $sheet->getActiveSheet(0)->setCellValue('H' . ($i+3), "주문액(원)");
        $sheet->getActiveSheet(0)->setCellValue('I' . ($i+3), "수량(개)");
        $sheet->getActiveSheet(0)->setCellValue('J' . ($i+3), "주문액(원)");
        $sheet->getActiveSheet(0)->setCellValue('K' . ($i+3), "수량(개)");
        $sheet->getActiveSheet(0)->setCellValue('L' . ($i+3), "주문액(원)");
        $sheet->getActiveSheet(0)->setCellValue('N' . ($i+3), "마진(원)");
        $sheet->getActiveSheet(0)->setCellValue('O' . ($i+3), "마진율(%)");


        $sheet->setActiveSheetIndex(0);
        //$i = $i + 2;

        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);


            $pname = $fordb->dt['pname']." ";
            if($fordb->dt['cid'] != "" && $_COOKIE['category_view'] == 1){
                $pname .= ($fordb->dt['cid'] == "9999999999" ? "\r기타" : "\r".strip_tags(getCategoryPath($fordb->dt['cid'],4)));
            }


            $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 4), ($i + 1));
            $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 4), $pname);
            $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 4), $fordb->dt['order_sale_cnt']);
            $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);




            $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 4), $fordb->dt['order_sale_sum']);
            $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 4), $fordb->dt['sale_all_cnt']);
            $sheet->getActiveSheet()->getStyle('E' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('F' . ($i + $start + 4), $fordb->dt['sale_all_sum']);
            $sheet->getActiveSheet()->getStyle('F' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('G' . ($i + $start + 4), $fordb->dt['cancel_sale_cnt']);
            $sheet->getActiveSheet()->getStyle('G' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('H' . ($i + $start + 4), $fordb->dt['cancel_sale_sum']);
            $sheet->getActiveSheet()->getStyle('H' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('I' . ($i + $start + 4), $fordb->dt['return_sale_cnt']);
            $sheet->getActiveSheet()->getStyle('I' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('J' . ($i + $start + 4), $fordb->dt['return_sale_sum']);
            $sheet->getActiveSheet()->getStyle('J' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);


            $real_sale_cnt = $fordb->dt['sale_all_cnt']-$fordb->dt['cancel_sale_cnt']-$fordb->dt['return_sale_cnt'];
            $sheet->getActiveSheet()->setCellValue('K' . ($i + $start + 4), $real_sale_cnt);
            $sheet->getActiveSheet()->getStyle('K' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $real_sale_coprice = $fordb->dt['sale_all_sum']-$fordb->dt['cancel_sale_sum']-$fordb->dt['return_sale_sum'];
            $sheet->getActiveSheet()->setCellValue('L' . ($i + $start + 4), $real_sale_coprice);
            $sheet->getActiveSheet()->getStyle('L' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sale_coprice = $fordb->dt['coprice_all_sum']-$fordb->dt['cancel_coprice_sum']-$fordb->dt['return_coprice_sum'];
            $sheet->getActiveSheet()->setCellValue('M' . ($i + $start + 4), $sale_coprice);
            $sheet->getActiveSheet()->getStyle('M' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $margin = $real_sale_coprice - $sale_coprice;
            $sheet->getActiveSheet()->setCellValue('N' . ($i + $start + 4), $margin);
            $sheet->getActiveSheet()->getStyle('N' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);


            //	$mstring .= "<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($margin,0)."&nbsp;</td>";
            if($real_sale_coprice > 0){
                $margin_rate = $margin/$real_sale_coprice;// 엑셀 포맷 정의시 자동으로 *100 이 처리됨
            }else{
                $margin_rate = 0;
            }
            $sheet->getActiveSheet()->setCellValue('O' . ($i + $start + 4), $margin_rate);
            $sheet->getActiveSheet()->getStyle('O' . ($i + $start + 3))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);


            $order_sale_cnt = $order_sale_cnt + returnZeroValue($fordb->dt['order_sale_cnt']);
            $order_sale_sum = $order_sale_sum + returnZeroValue($fordb->dt['order_sale_sum']);

            $sale_all_cnt = $sale_all_cnt + returnZeroValue($fordb->dt['sale_all_cnt']);
            $sale_all_sum = $sale_all_sum + returnZeroValue($fordb->dt['sale_all_sum']);

            $cancel_sale_cnt = $cancel_sale_cnt + returnZeroValue($fordb->dt['cancel_sale_cnt']);
            $cancel_sale_sum = $cancel_sale_sum + returnZeroValue($fordb->dt['cancel_sale_sum']);

            $return_sale_cnt = $return_sale_cnt + returnZeroValue($fordb->dt['return_sale_cnt']);
            $return_sale_sum = $return_sale_sum + returnZeroValue($fordb->dt['return_sale_sum']);

            $real_sale_cnt_sum = $real_sale_cnt_sum + returnZeroValue($real_sale_cnt);
            $real_sale_coprice_sum = $real_sale_coprice_sum + returnZeroValue($real_sale_coprice);
            $sale_coprice_sum = $sale_coprice_sum + returnZeroValue($sale_coprice);
            $margin_sum = $margin_sum + returnZeroValue($margin);


        }

        if($real_sale_coprice_sum > 0){
            $margin_sum_rate = $margin_sum/$real_sale_coprice_sum;// 엑셀 포맷 정의시 자동으로 *100 이 처리됨
        }else{
            $margin_sum_rate = 0;
        }

        //$i++;
        $sheet->getActiveSheet(0)->mergeCells('A'.($i + $start+4).':C'.($i+ $start+4));
        $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 4), '합계');
        //$sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 4), getIventoryCategoryPathByAdmin($goods_infos[$i]['cid'], 4));
        $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 4), $order_sale_cnt);
        $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 4), $order_sale_sum);
        $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 4), $sale_all_cnt);
        $sheet->getActiveSheet()->getStyle('E' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('F' . ($i + $start + 4), $sale_all_sum);
        $sheet->getActiveSheet()->getStyle('F' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('G' . ($i + $start + 4), $cancel_sale_cnt);
        $sheet->getActiveSheet()->getStyle('G' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('H' . ($i + $start + 4), $cancel_sale_sum);
        $sheet->getActiveSheet()->getStyle('H' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('I' . ($i + $start + 4), $return_sale_cnt);
        $sheet->getActiveSheet()->getStyle('I' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('J' . ($i + $start + 4), $return_sale_sum);
        $sheet->getActiveSheet()->getStyle('J' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);


        $sheet->getActiveSheet()->setCellValue('K' . ($i + $start + 4), $real_sale_cnt_sum);
        $sheet->getActiveSheet()->getStyle('K' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('L' . ($i + $start + 4), $real_sale_coprice_sum);
        $sheet->getActiveSheet()->getStyle('L' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('M' . ($i + $start + 4), $sale_coprice_sum);
        $sheet->getActiveSheet()->getStyle('M' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('N' . ($i + $start + 4), $margin_sum);
        $sheet->getActiveSheet()->getStyle('N' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('O' . ($i + $start + 4), $margin_sum_rate);
        $sheet->getActiveSheet()->getStyle('O' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);


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
        $sheet->getActiveSheet()->getColumnDimension('N')->setWidth(10);
        $sheet->getActiveSheet()->getColumnDimension('O')->setWidth(10);

        $sheet->getActiveSheet()->getRowDimension($start+2)->setRowHeight(30);

        //header('Content-Type: application/vnd.ms-excel');
        //header('Content-Disposition: attachment;filename="'.iconv("utf-8","cp949","상품판매종합 분석_매출상세분석.xls").'"');
        //header('Cache-Control: max-age=0');

		$filename = "상품판매종합분석_매출상세분석";

        // $objWriter->setUseInlineCSS(true);
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $sheet->getActiveSheet()->getStyle('A'.($start+1).':O'.($i+$start+4))->applyFromArray($styleArray);
        $sheet->getActiveSheet()->getStyle('A'.$start.':O'.($i+$start+4))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $sheet->getActiveSheet()->getStyle('A'.$start.':O'.($i+$start+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getActiveSheet()->getStyle('A'.($start).':O'.($start+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setWrapText(true);
        $sheet->getActiveSheet()->getStyle('B'.($start+4).':B'.($i+$start+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)->setWrapText(true);
        //$sheet->getActiveSheet()->getStyle('A'.$start.':O'.($i+$start+4))->getAlignment()->setIndent(1);
        //$sheet->getActiveSheet()->getStyle('A10')->getAlignment()->setIndent(10); 왼쪽 들여쓰기
        $sheet->getActiveSheet()->getStyle('A'.$start.':O'.($i+$start+4))->getFont()->setSize(10)->setName('돋움');

        $sheet->getActiveSheet()->getStyle('A2')->getFont()->setSize(15)->setBold(true)->setName('돋움');
        $sheet->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getActiveSheet()->getStyle('A'.($i+$start+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        unset($styleArray);
        //$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel5');
        //$objWriter->save('php://output');

		$sheet->getActiveSheet()->setTitle('상품판매종합분석');
		$sheet->setActiveSheetIndex(0);

		//	wel_ 엑셀 다운로드 히스토리 저장
		$ig_db = new Database;
		$ig_excel_dn_history_SQL = "
			INSERT INTO
				ig_excel_dn_history
			SET
				code = '".$_SESSION['admininfo']['charger_ix']."',
				ip = '". $_SERVER['REMOTE_ADDR']."',
				dn_type = 'order_salesbydate_paymenttype',
				dn_reason = '".addslashes($_GET['irs'])."',
				dn_text = '".addslashes($_SERVER['HTTP_REFERER'].$QUERY_STRING)."',
				regDt = '".date("Y-m-d H:i:s")."'
		";
		$ig_db->query($ig_excel_dn_history_SQL);
		//	//wel_ 엑셀 다운로드 히스토리 저장

		$download_filename = $filename.'_'.date("YmdHis").'.zip'; 
		$igExcel_file = '../../excelDn/'.$filename.'_'.date("YmdHis").'.xls';
	
		$ig_dnFile_full = '../../excelDn/'.$download_filename;

		$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel5');
		$objWriter->save($igExcel_file);

		if(trim($_GET['ipw']) == "") {
			$ig_pw = "barrel";
		} else {
			$ig_pw = $_GET['ipw'];
		}

		shell_exec('zip -P '.$ig_pw.' -r ../../excelDn/'.$download_filename.' '.$igExcel_file);

		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $download_filename . '"'); // 저장될 파일 이름
		header('Content-Transfer-Encoding: binary');
		header('Content-length: ' . filesize($ig_dnFile_full));
		header('Expires: 0');
		header("Pragma: public");

		ob_clean();
		flush();
		readfile($ig_dnFile_full);

		unlink($igExcel_file);
		unlink($ig_dnFile_full);

        exit;
    }
    $exce_down_str = "";
    if(false) {
        $exce_down_str = "<img src=\"../../images/korea/btn_print.gif\" onclick=\"PopSWindow('?mode=print&" . str_replace("&mode=iframe", "", $_SERVER["QUERY_STRING"]) . "',1150,500,'logstory_print');\" style=\"margin-right:3px;cursor:pointer;\"  >";
    }
    //$exce_down_str .= "<a href='?mode=excel&".str_replace("&mode=iframe", "",$_SERVER["QUERY_STRING"])."'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_excel_save.gif' border=0></a>";

	$exce_down_str = "<img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_excel_save.gif' border=0 style='cursor:pointer' 
	onclick=\"ig_excel_dn_chk('?mode=excel&".str_replace("&mode=iframe", "",$_SERVER["QUERY_STRING"])."');\">";

    $mstring = "<table width='100%' border=0>";
    if(isset($_GET["mode"]) && ($_GET["mode"] == "pop" || $_GET["mode"] == "print")){
        $mstring .= "<tr><td >".TitleBar("상품군별 분석 : ",$dateString."".($cid ? "-".getCategoryPath($cid,4):""),false, $exce_down_str)."</td></tr>";
    }else{
        $mstring .= "<tr><td >".TitleBar("상품군별 분석 : ",$dateString."".($cid ? "-".getCategoryPath($cid,4):""),true, $exce_down_str)."</td></tr>";
    }
    $mstring .= " 
						<tr height=50>
							<td >
								<div class='tab'>
									<table class='s_org_tab'>
									<tr>
										<td class='tab'> 
											<table id='tab_01'  ".(($report_type == '3') ? "class=on":"").">
											<tr>
												<th class='box_01'></th>
												<td class='box_02' onclick=\"document.location.href='?report_type=3&".str_replace("report_type=$report_type&", "",str_replace("mode=iframe&", "",$_SERVER["QUERY_STRING"]))."'\">매출요약 분석</td>
												<th class='box_03'></th>
											</tr>
											</table> 
											<table id='tab_02'  ".(($report_type == '1' || $report_type == '') ? "class=on":"").">
											<tr>
												<th class='box_01'></th>
												<td class='box_02' onclick=\"document.location.href='?report_type=1&".str_replace("report_type=$report_type&", "",$_SERVER["QUERY_STRING"])."'\">매출상세 분석</td>
												<th class='box_03'></th>
											</tr>
											</table>
											 <!--
											<table id='tab_03'  >
											<tr>
												<th class='box_01'></th>
												<td class='box_02' onclick=\"document.location.href='?report_type=2&".str_replace("report_type=$report_type&", "",$_SERVER["QUERY_STRING"])."'\">구매전환 분석</td>
												<th class='box_03'></th>
											</tr>
											</table>
											 -->
										</td>
										<td class='btn' style='padding:5px 0px 0px 10px;'>
											<input type=checkbox name='category_view' id='category_view' value='1' onclick=\"reloadView()\"  ".((!empty($_COOKIE['category_view']) && $_COOKIE['category_view'] == 1) ? "checked":"")." ><label for='category_view'> 카테고리 함께보기</label>
											<input type=checkbox name='view_self_sale' id='view_self_sale' value='1' onclick=\"reloadSaleView()\"  ".(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1 ? "checked":"")." ><label for='view_self_sale'> 자사매출만 보기</label>
										</td>
									</tr>
									</table>
								</div>
							</td>
						  </tr>
					</table>";
    $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>
						<col width='3%'>
						<col width='*'>
						<col width='5%'>
						<col width='6%'>
						<col width='5%'>
						<col width='6%'>
						<col width='5%'>
						<col width='6%'>
						<col width='5%'>
						<col width='6%'>
						<col width='5%'>
						<col width='6%'>
						<col width='6%'>
						<col width='5%'>
						<col width='6%'>";
    $mstring .= "
		<tr height=30>
			<td class=s_td rowspan=3>순</td>
			<td class=m_td rowspan=3 style='line-height:140%;'>상품명<br>카테고리명</td>
			<td class=m_td colspan=2>주문매출</td>
			<td class=m_td colspan=8>매출</td>
			<td class=m_td rowspan=3>실매출액<br>원가</td>
			<td class=m_td colspan=2 rowspan=2>수익</td>
		</tr>
		<tr height=30>
			<td class='m_td small' colspan=2 style='line-height:140%;'><b>전체주문</b><br>(입금예정포함)</td>
			<td class=m_td colspan=2>전체매출액(전체)</td>
			<td class=m_td colspan=2>취소매출액(-)</td>
			<td class=m_td colspan=2>반품매출액(-)</td>
			<td class=m_td colspan=2>실매출액(+)</td>
		</tr>
		<tr height=30 align=center>			
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>".OrderByLink("수량(개)", "real_sale_cnt", $ordertype)."</td>
			<td class=m_td >".OrderByLink("주문액(원)", "real_sale_sum", $ordertype)."</td>
			<td class=m_td >마진(원)</td>
			<td class=m_td >마진율(%)</td>
			</tr>\n";
    /*

    sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_sale_cnt,
                    sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pt_dcprice else 0 end) as order_sale_sum,
                    sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt,
                    sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end) as cancel_sale_sum,
                    sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt,
                    sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pt_dcprice else 0 end) as return_sale_sum,

                    */

    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);
        $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
		<td class='list_box_td list_bg_gray' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($i+1)."</td>
		<td class='list_box_td point' style='text-align:left;line-height:150%;padding:5px;' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">
		".$fordb->dt['pname']."  <br>";
        if($fordb->dt['cid'] != "" && (!empty($_COOKIE['category_view']) && $_COOKIE['category_view'] == 1)){
            $mstring .= "<span style='font-weight:normal'>".($fordb->dt['cid'] == "9999999999" ? "기타" : strip_tags(getCategoryPath($fordb->dt['cid'],4)))." </span>";
        }
        $mstring .= "
		</td>
		

		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['order_sale_cnt'],0)."&nbsp;</td>
		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['order_sale_sum'],0)."&nbsp;</td>

		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['sale_all_cnt'],0)."&nbsp;</td>
		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['sale_all_sum'],0)."&nbsp;</td>

		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['cancel_sale_cnt'],0)."&nbsp;</td>
		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['cancel_sale_sum'],0)."&nbsp;</td>

		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['return_sale_cnt'],0)."&nbsp;</td>
		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['return_sale_sum'],0)."&nbsp;</td>";
        //
        $real_sale_cnt = $fordb->dt['sale_all_cnt']-$fordb->dt['cancel_sale_cnt']-$fordb->dt['return_sale_cnt'];

        $mstring .= "<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($real_sale_cnt,0)."&nbsp;</td>";

        $real_sale_coprice = $fordb->dt['sale_all_sum']-$fordb->dt['cancel_sale_sum']-$fordb->dt['return_sale_sum'];
        $mstring .= "<td class='list_box_td number point' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($real_sale_coprice,0)."&nbsp;</td>";

        $sale_coprice = $fordb->dt['coprice_all_sum']-$fordb->dt['cancel_coprice_sum']-$fordb->dt['return_coprice_sum'];
        //echo $fordb->dt['coprice_all_sum']."-".$fordb->dt['cancel_coprice_sum']."-".$fordb->dt['return_coprice_sum']."<br>";
        $mstring .= "<td class='list_box_td number blue_point' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($sale_coprice,0)."&nbsp;</td>";

        $margin = $real_sale_coprice - $sale_coprice;
        $mstring .= "<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($margin,0)."&nbsp;</td>";
        if($real_sale_coprice > 0){
            $margin_rate = $margin/$real_sale_coprice*100;
        }else{
            $margin_rate = 0;
        }
        $mstring .= "<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($margin_rate,1)."&nbsp;</td>";




        $mstring .= "</tr>\n";

        $order_sale_cnt = $order_sale_cnt + returnZeroValue($fordb->dt['order_sale_cnt']);
        $order_sale_sum = $order_sale_sum + returnZeroValue($fordb->dt['order_sale_sum']);

        $sale_all_cnt = $sale_all_cnt + returnZeroValue($fordb->dt['sale_all_cnt']);
        $sale_all_sum = $sale_all_sum + returnZeroValue($fordb->dt['sale_all_sum']);

        $cancel_sale_cnt = $cancel_sale_cnt + returnZeroValue($fordb->dt['cancel_sale_cnt']);
        $cancel_sale_sum = $cancel_sale_sum + returnZeroValue($fordb->dt['cancel_sale_sum']);

        $return_sale_cnt = $return_sale_cnt + returnZeroValue($fordb->dt['return_sale_cnt']);
        $return_sale_sum = $return_sale_sum + returnZeroValue($fordb->dt['return_sale_sum']);

        $real_sale_cnt_sum = $real_sale_cnt_sum + returnZeroValue($real_sale_cnt);
        $real_sale_coprice_sum = $real_sale_coprice_sum + returnZeroValue($real_sale_coprice);
        $sale_coprice_sum = $sale_coprice_sum + returnZeroValue($sale_coprice);
        $margin_sum = $margin_sum + returnZeroValue($margin);


    }

    if ($order_sale_sum == 0){
        $mstring .= "<tr  align=center height=200><td colspan=15 class='list_box_td'  >결과값이 없습니다.</td></tr>\n";
    }
    //$mstring .= "</table>\n";
    /*
    $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box' style='margin-top:5px;'>
                        <col width='3%'>
                        <col width='*'>
                        <col width='5%'>
                        <col width='6%'>
                        <col width='5%'>
                        <col width='6%'>
                        <col width='5%'>
                        <col width='6%'>
                        <col width='5%'>
                        <col width='6%'>
                        <col width='5%'>
                        <col width='6%'>
                        <col width='6%'>
                        <col width='5%'>
                        <col width='6%'>";
    */
    //$mstring .= "<tr height=2 bgcolor=#ffffff align=center><td colspan=2></td></tr>\n";
    if($real_sale_coprice_sum > 0){
        $margin_sum_rate = $margin_sum/$real_sale_coprice_sum*100;
    }else{
        $margin_sum_rate = 0;
    }

    $mstring .= "<tr height=25 align=right>
	<td class=s_td align=center colspan=2>합계</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($order_sale_cnt,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($order_sale_sum,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($sale_all_cnt,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($sale_all_sum,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($cancel_sale_cnt,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($cancel_sale_sum,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($return_sale_cnt,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($return_sale_sum,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_cnt_sum,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_coprice_sum,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($sale_coprice_sum,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($margin_sum,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($margin_sum_rate,1)."</td>
	</tr>\n";
    $mstring .= "</table>\n";
    $mstring .= "<table width='100%'><tr><td> </td><td align=right style='padding-top:10px;'>VAT 포함 (단위:원)  </td></tr></table>";

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


    $mstring .= HelpBox("상품판매종합 분석", $help_text);
    return $mstring;
}


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
    if($report_type == 2){
        ReportTable2($vdate,$SelectReport);
    }else if($report_type == 3){
        ReportTable3($vdate,$SelectReport);
    }else{
        ReportTable($vdate,$SelectReport);
    }
}else if ($mode == "iframe"){
//	echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
//	echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'salesbyproduct.php';

    echo "<html>";
    echo "<meta http-equiv='content-type' content='text/html; charset=euc-kr'>";
    echo "<body>";
    if($report_type == 2){
        echo "<div id='report_view'>".ReportTable2($vdate,$SelectReport)."</div>";
    }else if($report_type == 3){
        echo "<div id='report_view'>".ReportTable3($vdate,$SelectReport)."</div>";
    }else{
        echo "<div id='report_view'>".ReportTable($vdate,$SelectReport)."</div>";
    }
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
    $P->Navigation = "이커머스분석 > 상품별 종합분석 > 상품판매종합 분석";

    $P->title = "상품판매종합 분석";
    //$P->strContents = ReportTable($vdate,$SelectReport);
    if($report_type == 2){
        $P->NaviTitle = "상품판매종합 분석 - 구매전환분석";
        $P->strContents = ReportTable2($vdate,$SelectReport);
    }else if($report_type == 3){
        $P->NaviTitle = "상품판매종합 분석 - 매출요약 분석";
        $P->strContents = ReportTable2($vdate,$SelectReport);
    }else{
        $P->NaviTitle = "상품판매종합 분석 - 매출상세분석";
        $P->strContents = ReportTable($vdate,$SelectReport);
    }
    $P->OnloadFunction = "";
    //	$P->layout_display = false;
    echo $P->PrintLayOut();
}else{


    $p = new forbizReportPage();
    $p->TopNaviSelect = "step2";
    $p->forbizLeftMenu = commerce_munu('salesbyproduct.php', "<div id=TREE_BAR style=\"margin:5px;\">".GetTreeNode('salesbyproduct.php',$vdate,'product')."</div>");
    if($report_type == 2){
        $p->forbizContents = ReportTable2($vdate,$SelectReport);
    }else if($report_type == 3){
        $p->forbizContents = ReportTable3($vdate,$SelectReport);
    }else{
        $p->forbizContents = ReportTable($vdate,$SelectReport);
    }
    $p->addScript = "<script language='JavaScript' src='../include/manager.js'></script>\n<script language='JavaScript' src='../include/Tree.js'></script>\n$script ";
    //$p->treemenu = "<div id=TREE_BAR style=\"margin:5;\">".GetTreeNode()."</div>";
    $p->Navigation = "이커머스분석 > 상품별 종합분석 > 상품판매종합 분석";
    $p->title = "상품판매종합 분석";
    $p->PrintReportPage();

}





function ReportTable2($vdate,$SelectReport=1){
    global $depth,$cid;
    global $non_sale_status, $report_type;
    global $search_sdate, $search_edate;


    $nview_cnt_sum = 0;
    $visit_cnt_sum_new = 0;
    $nview_cnt_sum_new = 0;
    $buyer_cnt_sum = 0;
    $order_detail_cnt_sum = 0;
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
        $vyesterday = date("Ymd", time()-84600);
        $voneweekago = date("Ymd", time()-84600*7);
    }else{
        if($SelectReport ==3){
            $vdate = $vdate."01";
        }
        $vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
        $vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
    }

    if(isset($_GET["orderby"]) && $_GET["orderby"] != "" && isset($_GET["ordertype"]) && $_GET["ordertype"] != ""){
        $orderbyString = " order by ".$_GET["orderby"]." ".$_GET["ordertype"].", ptprice desc ";
    }else{
        $orderbyString = " order by nview_cnt desc  ";
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
    $coprice_str = "case when (od.commission > 0 and od.coprice = 0) then od.psprice-(od.psprice*od.commission/100) else od.coprice end ";

    if ($SelectReport == 1){
        if($depth == 0){
            $sql = "select data.cid, data.pid, p.pname as pname, sum(nview_cnt) as nview_cnt, sum(pcnt) as pcnt, sum(ptprice) as ptprice, sum(order_detail_cnt) as order_detail_cnt,
						case when sum(nview_cnt) = 0 then 0 else sum(order_detail_cnt)/sum(nview_cnt) end as change_rate
						from 
						(
						select od.pid as pid, od.cid as cid, 0 nview_cnt , sum(od.pcnt) as pcnt, sum(od.ptprice) as ptprice, count(*) as order_detail_cnt  
						from shop_order_detail od 
						where od.regdate between '".substr($vdate,0,4)."-".substr($vdate,4,2)."-".substr($vdate,6,2)." 00:00:00' and '".substr($vdate,0,4)."-".substr($vdate,4,2)."-".substr($vdate,6,2)." 23:59:59' 
						and od.status NOT IN ('".implode("','",$non_sale_status)."') ";

            if(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1){
                $sql .= "and od.order_from = 'self'  ";
            }

            $sql .= " group by pid
						union
						Select LPAD(b.pid,10,'0') as pid, b.cid as cid, sum(b.nview_cnt) as nview_cnt , 0 pcnt , 0 ptprice , 0 order_detail_cnt
						from ".TBL_COMMERCE_VIEWINGVIEW." b  	
						where b.vdate = '".$vdate."' 			
						group by pid
						) data left join ".TBL_SHOP_PRODUCT." p on data.pid = p.id
						
						";
            //date_format(od.regdate,'%Y%m%d') = '".$vdate."'

            $sql .= "group by pid ";
            $sql .= $orderbyString;



        }else if($depth > 0){
            $sql = "select data.cid, data.pid as pid,  p.pname as pname, sum(nview_cnt) as nview_cnt, sum(pcnt) as pcnt, sum(ptprice) as ptprice, sum(order_detail_cnt) as order_detail_cnt,
						case when sum(nview_cnt) = 0 then 0 else sum(order_detail_cnt)/sum(nview_cnt) end as change_rate
						from 
						(select od.pid as pid,  od.cid as cid, 0 nview_cnt , sum(od.pcnt) as pcnt, sum(od.ptprice) as ptprice, count(*) as order_detail_cnt  
						from shop_order_detail od 
						where od.regdate between '".substr($vdate,0,4)."-".substr($vdate,4,2)."-".substr($vdate,6,2)." 00:00:00' and '".substr($vdate,0,4)."-".substr($vdate,4,2)."-".substr($vdate,6,2)." 23:59:59'
						and od.status NOT IN ('".implode("','",$non_sale_status)."') ";

            if(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1){
                $sql .= "and od.order_from = 'self'  ";
            }

            $sql .= " group by pid
						union
						Select LPAD(b.pid,10,'0') as pid, b.cid as cid, sum(b.nview_cnt) as nview_cnt , 0 pcnt , 0 ptprice , 0 order_detail_cnt
						from ".TBL_COMMERCE_VIEWINGVIEW." b  						
						where b.vdate = '".$vdate."' 			
						group by pid
						) data left join ".TBL_SHOP_PRODUCT." p on data.pid = p.id
						where  substr(data.cid,1,".(($depth)*3).") = '".substr($cid,0,(($depth)*3))."'
						";

            $sql .= "group by pid ";

            //date_format(od.regdate,'%Y%m%d') = '".$vdate."'
            $sql .= $orderbyString;
        }

        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2 || $SelectReport == 4){
        if($SelectReport == "4"){
            $vdate =$search_sdate;
            $vweekenddate =$search_edate;
        }

        if($depth == 0){
            $sql = "select data.cid, data.pid as pid,  p.pname as pname, sum(nview_cnt) as nview_cnt, sum(pcnt) as pcnt, sum(ptprice) as ptprice, sum(order_detail_cnt) as order_detail_cnt,
						case when sum(nview_cnt) = 0 then 0 else sum(order_detail_cnt)/sum(nview_cnt) end as change_rate
						from 
						(select od.pid as pid, od.cid as cid, 0 nview_cnt , sum(od.pcnt) as pcnt, sum(od.ptprice) as ptprice, count(*) as order_detail_cnt  
						from shop_order_detail od 
						where od.regdate between '".substr($vdate,0,4)."-".substr($vdate,4,2)."-".substr($vdate,6,2)." 00:00:00' and '".substr($vweekenddate,0,4)."-".substr($vweekenddate,4,2)."-".substr($vweekenddate,6,2)." 23:59:59' 
						and od.status NOT IN ('".implode("','",$non_sale_status)."') ";

            if(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1){
                $sql .= "and od.order_from = 'self'  ";
            }

            $sql .= " group by pid
						union
						Select LPAD(b.pid,10,'0') as pid,b.cid as cid, sum(b.nview_cnt) as nview_cnt , 0 pcnt , 0 ptprice , 0 order_detail_cnt
						from ".TBL_COMMERCE_VIEWINGVIEW." b  						
						where b.vdate between '".$vdate."' and '".$vweekenddate."'
						group by pid
						) data left join ".TBL_SHOP_PRODUCT." p on data.pid = p.id
						 ";
            //date_format(od.regdate,'%Y%m%d') between '".$vdate."' and '".$vweekenddate."'
            $sql .= "group by pid ";

            $sql .= $orderbyString;

            //echo nl2br($sql);
        }else if($depth > 0){
            $sql = "select data.cid, data.pid as pid,  p.pname as pname, sum(nview_cnt) as nview_cnt, sum(pcnt) as pcnt, sum(ptprice) as ptprice, sum(order_detail_cnt) as order_detail_cnt,
						case when sum(nview_cnt) = 0 then 0 else sum(order_detail_cnt)/sum(nview_cnt) end as change_rate
						from 
						(select od.pid as pid, od.cid as cid, 0 nview_cnt , sum(od.pcnt) as pcnt, sum(od.ptprice) as ptprice, count(*) as order_detail_cnt  
						from shop_order_detail od 
						where od.regdate between '".substr($vdate,0,4)."-".substr($vdate,4,2)."-".substr($vdate,6,2)." 00:00:00' and '".substr($vweekenddate,0,4)."-".substr($vweekenddate,4,2)."-".substr($vweekenddate,6,2)." 23:59:59' 
						and od.status NOT IN ('".implode("','",$non_sale_status)."') ";

            if(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1){
                $sql .= "and od.order_from = 'self'  ";
            }

            $sql .= " group by pid
						union
						Select LPAD(b.pid,10,'0') as pid,b.cid as cid, sum(b.nview_cnt) as nview_cnt , 0 pcnt , 0 ptprice , 0 order_detail_cnt
						from ".TBL_COMMERCE_VIEWINGVIEW." b  						
						where b.vdate between '".$vdate."' and '".$vweekenddate."'
						group by pid
						) data left join ".TBL_SHOP_PRODUCT." p on data.pid = p.id
						where   substr(data.cid,1,".(($depth)*3).") = '".substr($cid,0,(($depth)*3))."'";

            $sql .= "group by pid ";
            //date_format(od.regdate,'%Y%m%d') between '".$vdate."' and '".$vweekenddate."'
            $sql .= $orderbyString;
        }

        if($SelectReport == 2){
            $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
        }else if($SelectReport == 4){
            //echo$search_sdate;
            $s_week_num = date("w",mktime(0,0,0,substr($search_sdate,4,2),substr($search_sdate,6,2),substr($search_sdate,0,4)));
            $e_week_num = date("w",mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4)));
            $dateString = "기간별 : ".getNameOfWeekday($s_week_num,$search_sdate,"priodname")."~".getNameOfWeekday($e_week_num,$search_edate,"priodname");
        }
    }else if($SelectReport == 3){

        if($depth == 0){
            $sql = "select data.cid, data.pid as pid,  p.pname as pname, sum(nview_cnt) as nview_cnt, sum(pcnt) as pcnt, sum(ptprice) as ptprice, sum(order_detail_cnt) as order_detail_cnt,
						case when sum(nview_cnt) = 0 then 0 else sum(order_detail_cnt)/sum(nview_cnt) end as change_rate
						from 
						(select od.pid as pid, od.cid as cid, 0 nview_cnt , sum(od.pcnt) as pcnt, sum(od.ptprice) as ptprice, count(*) as order_detail_cnt  
						from shop_order_detail od 
						where od.regdate between '".substr($vdate,0,4)."-".substr($vdate,4,2)."-01 00:00:00' and '".substr($vdate,0,4)."-".substr($vdate,4,2)."-31 23:59:59' 
						and od.status NOT IN ('".implode("','",$non_sale_status)."') ";

            if(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1){
                $sql .= "and od.order_from = 'self'  ";
            }

            $sql .= " group by pid
						union
						Select LPAD(b.pid,10,'0') as pid,b.cid as cid, sum(b.nview_cnt) as nview_cnt , 0 pcnt , 0 ptprice , 0 order_detail_cnt
						from ".TBL_COMMERCE_VIEWINGVIEW." b  						
						where b.vdate LIKE '".substr($vdate,0,6)."%'		
						group by pid
						) data left join ".TBL_SHOP_PRODUCT." p on data.pid = p.id
						";

            $sql .= "group by pid ";
            //date_format(od.regdate,'%Y%m') = '".substr($vdate,0,6)."'
            $sql .= $orderbyString;

            //echo nl2br($sql);
        }else if($depth > 0){
            $sql = "select data.cid, data.pid as pid,  p.pname as pname, sum(nview_cnt) as nview_cnt, sum(pcnt) as pcnt, sum(ptprice) as ptprice, sum(order_detail_cnt) as order_detail_cnt ,
						case when sum(nview_cnt) = 0 then 0 else sum(order_detail_cnt)/sum(nview_cnt) end as change_rate
						from 
						(select od.pid as pid, od.cid as cid, 0 nview_cnt , sum(od.pcnt) as pcnt, sum(od.ptprice) as ptprice, count(*) as order_detail_cnt  
						from shop_order_detail od 
						where od.regdate between '".substr($vdate,0,4)."-".substr($vdate,4,2)."-01 00:00:00' and '".substr($vdate,0,4)."-".substr($vdate,4,2)."-31 23:59:59' 
						and od.status NOT IN ('".implode("','",$non_sale_status)."') ";

            if(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1){
                $sql .= "and od.order_from = 'self'  ";
            }

            $sql .= " group by pid
						union
						Select LPAD(b.pid,10,'0') as pid,b.cid as cid, sum(b.nview_cnt) as nview_cnt , 0 pcnt , 0 ptprice , 0 order_detail_cnt
						from ".TBL_COMMERCE_VIEWINGVIEW." b  						
						where b.vdate LIKE '".substr($vdate,0,6)."%'
						group by pid
						) data left join ".TBL_SHOP_PRODUCT." p on data.pid = p.id
						where substr(data.cid,1,".(($depth)*3).") = '".substr($cid,0,(($depth)*3))."'";

            $sql .= "group by pid ";
            //date_format(od.regdate,'%Y%m') = '".substr($vdate,0,6)."'
            $sql .= $orderbyString;
        }

        $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");
    }
    //echo $depth."<br>";
    //echo nl2br($sql);

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

        $sheet->getActiveSheet(0)->mergeCells('A2:J2');
        $sheet->getActiveSheet(0)->setCellValue('A2', "상품판매종합 분석 - 구매전환분석");
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
        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);
            $nview_cnt_sum += $fordb->dt['nview_cnt'];
            $order_detail_cnt_sum += $fordb->dt['order_detail_cnt'];
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

            if($order_detail_cnt_sum > 0){
                $order_rate = $fordb->dt['order_detail_cnt']/$order_detail_cnt_sum; // 엑셀 포맷 정의시 자동으로 *100 이 처리됨
            }else{
                $order_rate = 0;
            }

            if($ptprice_sum > 0){
                $sale_rate = $fordb->dt['ptprice']/$ptprice_sum; // 엑셀 포맷 정의시 자동으로 *100 이 처리됨
            }else{
                $sale_rate = 0;
            }

            if($fordb->dt['nview_cnt'] > 0){
                $change_rate = $fordb->dt['order_detail_cnt']/$fordb->dt['nview_cnt'];
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

            $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 3), $fordb->dt['order_detail_cnt']);
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
        $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 3), $order_detail_cnt_sum);
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

        //header('Content-Type: application/vnd.ms-excel');
        //header('Content-Disposition: attachment;filename="'.iconv("utf-8","cp949","상품판매종합 분석_구매전환분석.xls").'"');
        //header('Cache-Control: max-age=0');

		$filename = "상품판매종합분석_매출상세분석";

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
        //$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel5');
        //$objWriter->save('php://output');

		$sheet->getActiveSheet()->setTitle('상품판매종합분석');
		$sheet->setActiveSheetIndex(0);

		//	wel_ 엑셀 다운로드 히스토리 저장
		$ig_db = new Database;
		$ig_excel_dn_history_SQL = "
			INSERT INTO
				ig_excel_dn_history
			SET
				code = '".$_SESSION['admininfo']['charger_ix']."',
				ip = '". $_SERVER['REMOTE_ADDR']."',
				dn_type = 'order_salesbydate_paymenttype',
				dn_reason = '".addslashes($_GET['irs'])."',
				dn_text = '".addslashes($_SERVER['HTTP_REFERER'].$QUERY_STRING)."',
				regDt = '".date("Y-m-d H:i:s")."'
		";
		$ig_db->query($ig_excel_dn_history_SQL);
		//	//wel_ 엑셀 다운로드 히스토리 저장

		$download_filename = $filename.'_'.date("YmdHis").'.zip'; 
		$igExcel_file = '../../excelDn/'.$filename.'_'.date("YmdHis").'.xls';
	
		$ig_dnFile_full = '../../excelDn/'.$download_filename;

		$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel5');
		$objWriter->save($igExcel_file);

		if(trim($_GET['ipw']) == "") {
			$ig_pw = "barrel";
		} else {
			$ig_pw = $_GET['ipw'];
		}

		shell_exec('zip -P '.$ig_pw.' -r ../../excelDn/'.$download_filename.' '.$igExcel_file);

		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $download_filename . '"'); // 저장될 파일 이름
		header('Content-Transfer-Encoding: binary');
		header('Content-length: ' . filesize($ig_dnFile_full));
		header('Expires: 0');
		header("Pragma: public");

		ob_clean();
		flush();
		readfile($ig_dnFile_full);

		unlink($igExcel_file);
		unlink($ig_dnFile_full);

        exit;
    }
    $exce_down_str = "";
    if(false) {
        $exce_down_str = "<img src=\"../../images/korea/btn_print.gif\" onclick=\"PopSWindow('?mode=print&" . str_replace("&mode=iframe", "", $_SERVER["QUERY_STRING"]) . "',1150,500,'logstory_print');\" style=\"margin-right:3px;cursor:pointer;\"  >";
    }
    //$exce_down_str .= "<a href='?mode=excel&".str_replace("&mode=iframe", "",$_SERVER["QUERY_STRING"])."'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_excel_save.gif' border=0></a>";

	$exce_down_str = "<img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_excel_save.gif' border=0 style='cursor:pointer' 
	onclick=\"ig_excel_dn_chk('?mode=excel&".str_replace("&mode=iframe", "",$_SERVER["QUERY_STRING"])."');\">";

    //$mstring = $mstring.TitleBar("상품군별 분석 : ".($cid ? getCategoryPath($cid,4):""),$dateString);
    $mstring = "<table width='100%' border=0>";
    if(isset($_GET["mode"]) && ($_GET["mode"] == "pop" || $_GET["mode"] == "print")){
        $mstring .= "<tr><td >".TitleBar("상품군별 분석 : ",$dateString."".($cid ? "-".getCategoryPath($cid,4):""),false, $exce_down_str)."</td></tr>";
    }else{
        $mstring .= "<tr><td >".TitleBar("상품군별 분석 : ",$dateString."".($cid ? "-".getCategoryPath($cid,4):""),true, $exce_down_str)."</td></tr>";
    }
    $mstring .= " 
						<tr height=50>
							<td >
								<div class='tab'>
									<table class='s_org_tab'>
									<tr>
										<td class='tab'> 
											<table id='tab_01'  ".(($report_type == '3') ? "class=on":"").">
											<tr>
												<th class='box_01'></th>
												<td class='box_02' onclick=\"document.location.href='?report_type=3&".str_replace("report_type=$report_type&", "",str_replace("mode=iframe&", "",$_SERVER["QUERY_STRING"]))."'\">매출요약 분석</td>
												<th class='box_03'></th>
											</tr>
											</table> 
											<table id='tab_02'  ".(($report_type == '1' || $report_type == '') ? "class=on":"").">
											<tr>
												<th class='box_01'></th>
												<td class='box_02' onclick=\"document.location.href='?report_type=1&".str_replace("report_type=$report_type&", "",$_SERVER["QUERY_STRING"])."'\">매출상세 분석</td>
												<th class='box_03'></th>
											</tr>
											</table>
											 <!--
											<table id='tab_03'  ".(($report_type == '2' ) ? "class=on":"").">
											<tr>
												<th class='box_01'></th>
												<td class='box_02' onclick=\"document.location.href='?report_type=2&".str_replace("report_type=$report_type&", "",$_SERVER["QUERY_STRING"])."'\">구매전환 분석</td>
												<th class='box_03'></th>
											</tr>
											</table>
											 -->
										</td>
										<td class='btn' style='padding:5px 0px 0px 10px;'>
										<table width=100%>
											<tr>
												<td>	<input type=checkbox name='category_view' id='category_view' value='1' onclick=\"reloadView()\"  ".($_COOKIE['category_view'] == 1 ? "checked":"")." ><label for='category_view'> 카테고리 함께보기</label>
												<input type=checkbox name='view_self_sale' id='view_self_sale' value='1' onclick=\"reloadSaleView()\"  ".(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1 ? "checked":"")." ><label for='view_self_sale'> 자사매출만 보기</label>

												</td>
												<td>
												
												</td>
											</tr>
										</table>
										</td>
									</tr>
									</table>
								</div>
							</td>
						  </tr>
					</table>";
    $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>
						<col width='5%'>
						<col width='*'>
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
						<td class=m_td rowspan=2>".OrderByLink("카테고리명", "cid", $ordertype)."</td>
						<td class=m_td colspan=2>상품조회정보</td>
						<td class=e_td colspan=6>상품판매정보</td>
					   </tr>\n";
    $mstring .= "<tr height=30 align=center>
							<td class=m_td >".OrderByLink("조회횟수(a)", "nview_cnt", $ordertype)."</td>
							<td class=m_td >점유율</td>
							<td class=m_td >".OrderByLink("구매건수(b)", "order_detail_cnt", $ordertype)."</td>
							<td class=m_td >구매점유율</td>
							<td class=m_td >".OrderByLink("구매전환율(b/a)", "change_rate", $ordertype)."</td>
							<td class=m_td >".OrderByLink("상품수량", "pcnt", $ordertype)." </td>
							<td class=m_td >".OrderByLink("매출액", "ptprice", $ordertype)." </td>
							<td class=e_td >매출점유율</td>
						</tr>\n";
    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);
        $nview_cnt_sum += $fordb->dt['nview_cnt'];
        $order_detail_cnt_sum += $fordb->dt['order_detail_cnt'];
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

        if($order_detail_cnt_sum > 0){
            $order_rate = $fordb->dt['order_detail_cnt']/$order_detail_cnt_sum*100;
        }else{
            $order_rate = 0;
        }

        if($ptprice_sum > 0){
            $sale_rate = $fordb->dt['ptprice']/$ptprice_sum*100;
        }else{
            $sale_rate = 0;
        }

        if($fordb->dt['nview_cnt'] > 0){
            $change_rate = $fordb->dt['order_detail_cnt']/$fordb->dt['nview_cnt']*100;
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
		<td class='list_box_td  number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(returnZeroValue($fordb->dt['order_detail_cnt']),0)."&nbsp;</td>
		<td class='list_box_td list_bg_gray number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(returnZeroValue($order_rate),1)."%</td>
		<td class='list_box_td point number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($change_rate,1)."%</td>
		<td class='list_box_td list_bg_gray number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(returnZeroValue($fordb->dt['pcnt']),0)."&nbsp;</td>
		<td class='list_box_td  number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(returnZeroValue($fordb->dt['ptprice']),0)."&nbsp;</td>
		<td class='list_box_td list_bg_gray number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(returnZeroValue($sale_rate),1)."%</td>

		</tr>\n";

        $nview_cnt_sum = $nview_cnt_sum + returnZeroValue($fordb->dt['nview_cnt']);
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
	<td class=e_td style='padding-right:20px;'>".number_format($order_detail_cnt_sum,0)."</td>
	<td class=e_td style='padding-right:20px;'>100%</td>
	<td class=e_td style='padding-right:20px;'>".number_format($order_change_rate_sum,1)."</td>
	<td class=e_td style='padding-right:20px;'>".number_format($pcnt_sum,0)."</td>
	<td class=e_td style='padding-right:20px;'>".number_format($ptprice_sum,0)."</td>
	<td class=e_td style='padding-right:20px;'>100%</td>
	</tr>\n";
    $mstring .= "</table>\n";

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


    $mstring .= HelpBox("상품판매종합 분석", $help_text);
    return $mstring;
}



function ReportTable3($vdate,$SelectReport=1){

    global $depth,$cid, $non_sale_status, $order_status, $cancel_status, $return_status, $all_sale_status;
    global $search_sdate, $search_edate, $report_type;
    $nview_cnt = 0;
    //$cid = $referer_id;



    $order_sale_sum = 0;
    $real_sale_coprice_sum = 0;
    $order_sale_cnt = 0;
    $sale_all_cnt = 0;
    $sale_all_sum = 0;
    $real_sale_cnt_sum = 0;
    $real_sale_cnt_new = 0;
    $real_sale_sum_new = 0;
    $real_sale_cnt_reorder = 0;
    $real_sale_sum_reorder = 0;
    $real_sale_cnt_web = 0;
    $real_sale_sum_web = 0;
    $real_sale_cnt_mobile = 0;
    $real_sale_sum_mobile = 0;
    $order_cnt_web = 0;
    $order_cnt_mobile = 0;
    $sale_coprice_sum = 0;
    $margin_sum = 0;
    $sale_coprice = 0;
    $margin = 0;

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
        $vyesterday = date("Ymd", time()-84600);
        $voneweekago = date("Ymd", time()-84600*7);
        $search_sdate = date("Ymd", time()-84600*7);
        $search_edate = date("Ymd", time());
    }else{
        if($SelectReport ==3){
            $vdate = $vdate."01";
        }
        $vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
        $vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
    }

    if(isset($_GET["orderby"]) && $_GET["orderby"] != "" && isset($_GET["ordertype"]) && $_GET["ordertype"] != ""){
        $orderbyString = " order by ".$_GET["orderby"]." ".$_GET["ordertype"].", ptprice desc ";
    }else{
        $orderbyString = " order by ptprice desc  ";
    }

    $coprice_str = "case when (od.commission > 0 and od.coprice = 0) then od.psprice-(od.psprice*od.commission/100) else od.coprice end ";

    if ($SelectReport == 1){
        if($depth == "" || $depth < 0){


            $sql = "Select IFNULL(od.pid,'9999999999') as pid, od.pid as pid, od.cid as cid,  IFNULL(concat(od.pname,' ',od.add_info),'기타') as pname, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then 1 else 0 end) as order_cnt, 
							sum(case when (o.payment_agent_type = 'W' and od.status NOT IN ('".implode("','",$non_sale_status)."'))  then 1 else 0 end) as order_cnt_web, 
							sum(case when (o.payment_agent_type = 'M' and od.status NOT IN ('".implode("','",$non_sale_status)."'))  then 1 else 0 end) as order_cnt_mobile, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_sale_cnt, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pt_dcprice else 0 end) as order_sale_sum, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as order_coprice_sum, 
							
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) as sale_all_cnt, 
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) as sale_all_sum, 
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as coprice_all_sum, 

							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt, 
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end) as cancel_sale_sum, 
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as cancel_coprice_sum, 

							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt, 
							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pt_dcprice else 0 end) as return_sale_sum, 
							sum(case when od.status IN ('".implode("','",$return_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as return_coprice_sum, 

							(
								sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) - 
								sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) - 
								sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end)
							) as real_sale_cnt , 

							(
								sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) - 
								sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end) - 
								sum(case when od.status IN ('".implode("','",$return_status)."')  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum , 

							(
								sum(case when (o.mem_reg_date = o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
							) as real_sale_cnt_new , 

							(
								sum(case when (o.mem_reg_date = o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum_new , 

							(
								sum(case when (o.mem_reg_date != o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
							) as real_sale_cnt_reorder , 

							(
								sum(case when (o.mem_reg_date != o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum_reorder , 

							(
								sum(case when (o.payment_agent_type = 'W' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
							) as real_sale_cnt_web , 

							(
								sum(case when (o.payment_agent_type = 'W' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum_web , 

							(
								sum(case when (o.payment_agent_type = 'M' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
							) as real_sale_cnt_mobile , 

							(
								sum(case when (o.payment_agent_type = 'M' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum_mobile , 

							sum(od.pcnt) as amount_cnt, sum(od.pt_dcprice) as sale_sum
							from  shop_order_detail od join shop_order o on od.oid = o.oid 
							
							where od.regdate between '".substr($vdate,0,4)."-".substr($vdate,4,2)."-".substr($vdate,6,2)." 00:00:00' and '".substr($vdate,0,4)."-".substr($vdate,4,2)."-".substr($vdate,6,2)." 23:59:59' 
							and od.status NOT IN ('".implode("','",$non_sale_status)."')  ";

            if(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1){
                $sql .= "and od.order_from = 'self'  ";
            }
            $sql .= "group by od.pid ";
            $sql .= $orderbyString;
            //and od.order_from = 'self'
            //date_format(od.regdate,'%Y%m%d')  = '".$vdate."'
            //echo nl2br($sql);
        }else if($depth >= 0){
            $sql = "Select IFNULL(c.cid,'9') as cid, IFNULL(c.cname,'기타') as cname,
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then 1 else 0 end) as order_cnt, 
							sum(case when (o.payment_agent_type = 'W' and od.status NOT IN ('".implode("','",$non_sale_status)."'))  then 1 else 0 end) as order_cnt_web, 
							sum(case when (o.payment_agent_type = 'M' and od.status NOT IN ('".implode("','",$non_sale_status)."'))  then 1 else 0 end) as order_cnt_mobile, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_sale_cnt, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pt_dcprice else 0 end) as order_sale_sum, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as order_coprice_sum, 
							
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) as sale_all_cnt, 
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) as sale_all_sum, 
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as coprice_all_sum, 

							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt, 
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end) as cancel_sale_sum, 
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as cancel_coprice_sum, 

							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt, 
							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pt_dcprice else 0 end) as return_sale_sum, 
							sum(case when od.status IN ('".implode("','",$return_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as return_coprice_sum, 

							(
								sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) - 
								sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) - 
								sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end)
							) as real_sale_cnt , 

							(
								sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) - 
								sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end) - 
								sum(case when od.status IN ('".implode("','",$return_status)."')  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum , 

							(
								sum(case when (o.mem_reg_date = o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
							) as real_sale_cnt_new , 

							(
								sum(case when (o.mem_reg_date = o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum_new , 

							(
								sum(case when (o.mem_reg_date != o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
							) as real_sale_cnt_reorder , 

							(
								sum(case when (o.mem_reg_date != o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum_reorder , 

							(
								sum(case when (o.payment_agent_type = 'W' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
							) as real_sale_cnt_web , 

							(
								sum(case when (o.payment_agent_type = 'W' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum_web , 

							(
								sum(case when (o.payment_agent_type = 'M' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
							) as real_sale_cnt_mobile , 

							(
								sum(case when (o.payment_agent_type = 'M' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum_mobile , 

							sum(od.pcnt) as amount_cnt, sum(od.pt_dcprice) as sale_sum
							from  shop_order_detail od join shop_order o on od.oid = o.oid 
							left join shop_category_info c on od.cid = c.cid 
							where od.regdate between '".substr($vdate,0,4)."-".substr($vdate,4,2)."-".substr($vdate,6,2)." 00:00:00' and '".substr($vdate,0,4)."-".substr($vdate,4,2)."-".substr($vdate,6,2)." 23:59:59' 
							and substr(od.cid,1,".(($depth)*3).") = '".substr($cid,0,(($depth)*3))."' 
							and od.status NOT IN ('".implode("','",$non_sale_status)."')  ";

            if(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1){
                $sql .= "and od.order_from = 'self'  ";
            }
            //date_format(od.regdate,'%Y%m%d')  = '".$vdate."'
            $sql .= "group by od.pid ";
            $sql .= $orderbyString;


        }
        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2 || $SelectReport == 4){
        if($SelectReport == "4"){
            $vdate = $search_sdate;
            $vweekenddate = $search_edate;
        }

        if($depth == '' || $depth < 0){
            $sql = "Select IFNULL(od.pid,'9999999999') as pid, od.pid as pid, od.cid as cid,  IFNULL(concat(od.pname,' ',od.add_info),'기타') as pname, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then 1 else 0 end) as order_cnt, 
							sum(case when (o.payment_agent_type = 'W' and od.status NOT IN ('".implode("','",$non_sale_status)."'))  then 1 else 0 end) as order_cnt_web, 
							sum(case when (o.payment_agent_type = 'M' and od.status NOT IN ('".implode("','",$non_sale_status)."'))  then 1 else 0 end) as order_cnt_mobile, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_sale_cnt, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pt_dcprice else 0 end) as order_sale_sum, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as order_coprice_sum, 
							
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) as sale_all_cnt, 
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) as sale_all_sum, 
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as coprice_all_sum, 

							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt, 
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end) as cancel_sale_sum, 
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as cancel_coprice_sum, 

							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt, 
							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pt_dcprice else 0 end) as return_sale_sum, 
							sum(case when od.status IN ('".implode("','",$return_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as return_coprice_sum, 

							(
								sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) - 
								sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) - 
								sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end)
							) as real_sale_cnt , 

							(
								sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) - 
								sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end) - 
								sum(case when od.status IN ('".implode("','",$return_status)."')  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum , 

							(
								sum(case when (o.mem_reg_date = o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
							) as real_sale_cnt_new , 

							(
								sum(case when (o.mem_reg_date = o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum_new , 

							(
								sum(case when (o.mem_reg_date != o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
							) as real_sale_cnt_reorder , 

							(
								sum(case when (o.mem_reg_date != o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum_reorder , 

							(
								sum(case when (o.payment_agent_type = 'W' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
							) as real_sale_cnt_web , 

							(
								sum(case when (o.payment_agent_type = 'W' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum_web , 

							(
								sum(case when (o.payment_agent_type = 'M' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
							) as real_sale_cnt_mobile , 

							(
								sum(case when (o.payment_agent_type = 'M' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum_mobile , 

							sum(od.pcnt) as amount_cnt, sum(od.pt_dcprice) as sale_sum
							from  shop_order_detail od join shop_order o on od.oid = o.oid 
							 
							where od.regdate between '".substr($vdate,0,4)."-".substr($vdate,4,2)."-".substr($vdate,6,2)." 00:00:00' and '".substr($vweekenddate,0,4)."-".substr($vweekenddate,4,2)."-".substr($vweekenddate,6,2)." 23:59:59' 
							and od.status NOT IN ('".implode("','",$non_sale_status)."')  ";

            if(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1){
                $sql .= "and od.order_from = 'self'  ";
            }
            //date_format(od.regdate,'%Y%m%d')  between '".$vdate."' and '".$vweekenddate."'
            $sql .= "group by od.pid ";
            $sql .= $orderbyString;

        }else if($depth >= 0){
            $sql = "Select IFNULL(od.pid,'9999999999') as pid, od.pid as pid, od.cid as cid,  IFNULL(concat(od.pname,' ',od.add_info),'기타') as pname, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then 1 else 0 end) as order_cnt, 
							sum(case when (o.payment_agent_type = 'W' and od.status NOT IN ('".implode("','",$non_sale_status)."'))  then 1 else 0 end) as order_cnt_web, 
							sum(case when (o.payment_agent_type = 'M' and od.status NOT IN ('".implode("','",$non_sale_status)."'))  then 1 else 0 end) as order_cnt_mobile, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_sale_cnt, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pt_dcprice else 0 end) as order_sale_sum, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as order_coprice_sum, 
							
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) as sale_all_cnt, 
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) as sale_all_sum, 
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as coprice_all_sum, 

							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt, 
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end) as cancel_sale_sum, 
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as cancel_coprice_sum, 

							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt, 
							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pt_dcprice else 0 end) as return_sale_sum, 
							sum(case when od.status IN ('".implode("','",$return_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as return_coprice_sum, 

							(
								sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) - 
								sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) - 
								sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end)
							) as real_sale_cnt , 

							(
								sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) - 
								sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end) - 
								sum(case when od.status IN ('".implode("','",$return_status)."')  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum , 

							(
								sum(case when (o.mem_reg_date = o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
							) as real_sale_cnt_new , 

							(
								sum(case when (o.mem_reg_date = o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum_new , 

							(
								sum(case when (o.mem_reg_date != o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
							) as real_sale_cnt_reorder , 

							(
								sum(case when (o.mem_reg_date != o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum_reorder , 

							(
								sum(case when (o.payment_agent_type = 'W' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
							) as real_sale_cnt_web , 

							(
								sum(case when (o.payment_agent_type = 'W' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum_web , 

							(
								sum(case when (o.payment_agent_type = 'M' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
							) as real_sale_cnt_mobile , 

							(
								sum(case when (o.payment_agent_type = 'M' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum_mobile , 

							sum(od.pcnt) as amount_cnt, sum(od.pt_dcprice) as sale_sum
							from  shop_order_detail od join shop_order o on od.oid = o.oid 
							
							where od.regdate between '".substr($vdate,0,4)."-".substr($vdate,4,2)."-".substr($vdate,6,2)." 00:00:00' and '".substr($vweekenddate,0,4)."-".substr($vweekenddate,4,2)."-".substr($vweekenddate,6,2)." 23:59:59' 
							and od.status NOT IN ('".implode("','",$non_sale_status)."') 							
							and substr(od.cid,1,".(($depth)*3).") = '".substr($cid,0,(($depth)*3))."' ";

            if(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1){
                $sql .= "and od.order_from = 'self'  ";
            }
            //date_format(od.regdate,'%Y%m%d')  between '".$vdate."' and '".$vweekenddate."'
            $sql .= "group by od.pid ";
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
        if($depth == '' || $depth < 0 ){
            //$real_sale_cnt = $fordb->dt['sale_all_cnt']-$fordb->dt['cancel_sale_cnt']-$fordb->dt['return_sale_cnt'];
            //$real_sale_coprice = $fordb->dt['sale_all_sum']-$fordb->dt['cancel_sale_sum']-$fordb->dt['return_sale_sum'];

            $sql = "Select IFNULL(od.pid,'9999999999') as pid, od.pid as pid, od.cid as cid,  IFNULL(concat(od.pname,' ',od.add_info),'기타') as pname, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then 1 else 0 end) as order_cnt, 
							sum(case when (o.payment_agent_type = 'W' and od.status NOT IN ('".implode("','",$non_sale_status)."'))  then 1 else 0 end) as order_cnt_web, 
							sum(case when (o.payment_agent_type = 'M' and od.status NOT IN ('".implode("','",$non_sale_status)."'))  then 1 else 0 end) as order_cnt_mobile, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_sale_cnt, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pt_dcprice else 0 end) as order_sale_sum, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as order_coprice_sum, 
							
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) as sale_all_cnt, 
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) as sale_all_sum, 
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as coprice_all_sum, 

							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt, 
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end) as cancel_sale_sum, 
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as cancel_coprice_sum, 

							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt, 
							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pt_dcprice else 0 end) as return_sale_sum, 
							sum(case when od.status IN ('".implode("','",$return_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as return_coprice_sum, 

							(
								sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) - 
								sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) - 
								sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end)
							) as real_sale_cnt , 

							(
								sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) - 
								sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end) - 
								sum(case when od.status IN ('".implode("','",$return_status)."')  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum , 

							(
								sum(case when (o.mem_reg_date = o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
							) as real_sale_cnt_new , 

							(
								sum(case when (o.mem_reg_date = o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum_new , 

							(
								sum(case when (o.mem_reg_date != o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
							) as real_sale_cnt_reorder , 

							(
								sum(case when (o.mem_reg_date != o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum_reorder , 

							(
								sum(case when (o.payment_agent_type = 'W' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
							) as real_sale_cnt_web , 

							(
								sum(case when (o.payment_agent_type = 'W' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum_web , 

							(
								sum(case when (o.payment_agent_type = 'M' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
							) as real_sale_cnt_mobile , 

							(
								sum(case when (o.payment_agent_type = 'M' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum_mobile , 

							sum(od.pcnt) as amount_cnt, sum(od.pt_dcprice) as sale_sum
							from  shop_order_detail od join shop_order o on od.oid = o.oid 
							
							where od.regdate between '".substr($vdate,0,4)."-".substr($vdate,4,2)."-01 00:00:00' and '".substr($vdate,0,4)."-".substr($vdate,4,2)."-31 23:59:59'   
							and od.status NOT IN ('".implode("','",$non_sale_status)."')  ";

            if(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1){
                $sql .= "and od.order_from = 'self'  ";
            }
            //date_format(od.regdate,'%Y%m%d') LIKE '".substr($vdate,0,6)."%'
            $sql .= "group by od.pid ";
            $sql .= $orderbyString;


        }else if($depth >= 0){
            $depth = $depth -1;
            $sql = "Select IFNULL(od.pid,'9999999999') as pid, od.pid as pid, od.cid as cid,  IFNULL(concat(od.pname,' ',od.add_info),'기타') as pname, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then 1 else 0 end) as order_cnt, 
							sum(case when (o.payment_agent_type = 'W' and od.status NOT IN ('".implode("','",$non_sale_status)."'))  then 1 else 0 end) as order_cnt_web, 
							sum(case when (o.payment_agent_type = 'M' and od.status NOT IN ('".implode("','",$non_sale_status)."'))  then 1 else 0 end) as order_cnt_mobile, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_sale_cnt, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pt_dcprice else 0 end) as order_sale_sum, 
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as order_coprice_sum, 
							
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) as sale_all_cnt, 
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) as sale_all_sum, 
							sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as coprice_all_sum, 

							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt, 
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end) as cancel_sale_sum, 
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as cancel_coprice_sum, 

							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt, 
							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pt_dcprice else 0 end) as return_sale_sum, 
							sum(case when od.status IN ('".implode("','",$return_status)."')  then ".$coprice_str."*od.pcnt else 0 end) as return_coprice_sum, 

							(
								sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) - 
								sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) - 
								sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end)
							) as real_sale_cnt , 

							(
								sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pt_dcprice else 0 end) - 
								sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end) - 
								sum(case when od.status IN ('".implode("','",$return_status)."')  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum , 

							(
								sum(case when (o.mem_reg_date = o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
							) as real_sale_cnt_new , 

							(
								sum(case when (o.mem_reg_date = o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.mem_reg_date = o.static_date and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum_new , 

							(
								sum(case when (o.mem_reg_date != o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
							) as real_sale_cnt_reorder , 

							(
								sum(case when (o.mem_reg_date != o.static_date and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.mem_reg_date != o.static_date and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum_reorder , 

							(
								sum(case when (o.payment_agent_type = 'W' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
							) as real_sale_cnt_web , 

							(
								sum(case when (o.payment_agent_type = 'W' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.payment_agent_type = 'W' and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum_web , 

							(
								sum(case when (o.payment_agent_type = 'M' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pcnt else 0 end) - 
								sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$return_status)."'))  then od.pcnt else 0 end)
							) as real_sale_cnt_mobile , 

							(
								sum(case when (o.payment_agent_type = 'M' and od.status not in ('".implode("','",$all_sale_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$cancel_status)."'))  then od.pt_dcprice else 0 end) - 
								sum(case when (o.payment_agent_type = 'M' and od.status IN ('".implode("','",$return_status)."'))  then ".$coprice_str."*od.pcnt else 0 end)
							) as real_sale_sum_mobile , 

							sum(od.pcnt) as amount_cnt, sum(od.pt_dcprice) as sale_sum
							from  shop_order_detail od join shop_order o on od.oid = o.oid 
							left join shop_category_info c on od.cid = c.cid
							where od.regdate between '".substr($vdate,0,4)."-".substr($vdate,4,2)."-01 00:00:00' and '".substr($vdate,0,4)."-".substr($vdate,4,2)."-31 23:59:59' 
							and od.status NOT IN ('".implode("','",$non_sale_status)."') 
							
							and substr(c.cid,1,".(($depth)*3).") = '".substr($cid,0,(($depth)*3))."' ";

            if(isset($_COOKIE['view_self_sale']) && $_COOKIE['view_self_sale'] == 1){
                $sql .= "and od.order_from = 'self'  ";
            }
            //date_format(od.regdate,'%Y%m%d') LIKE '".substr($vdate,0,6)."%'
            $sql .= "group by od.pid ";
            $sql .= $orderbyString;

        }

        $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");
    }
    //echo "cid:".$cid."<br>";
    //echo "depth:".$depth."<br>";
    //echo time()."<br>";

    if($_SERVER["REMOTE_ADDR"] == "175.209.244.68"){
        //echo nl2br($sql);
        //exit;
    }

    if($sql){
        $fordb->query($sql);
    }



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

        $sheet->getActiveSheet(0)->mergeCells('A2:P2');
        $sheet->getActiveSheet(0)->setCellValue('A2', "상품판매종합 분석");
        $sheet->getActiveSheet(0)->mergeCells('A3:P3');
        $sheet->getActiveSheet(0)->setCellValue('A3', $dateString);

        $sheet->getActiveSheet(0)->mergeCells('A'.($i+1).':A'.($i+3));
        $sheet->getActiveSheet(0)->mergeCells('B'.($i+1).':B'.($i+3));
        $sheet->getActiveSheet(0)->mergeCells('C'.($i+1).':D'.($i+1));

        $sheet->getActiveSheet(0)->mergeCells('E'.($i+1).':F'.($i+1));
        $sheet->getActiveSheet(0)->mergeCells('G'.($i+1).':J'.($i+1));
        $sheet->getActiveSheet(0)->mergeCells('K'.($i+1).':N'.($i+1));
        $sheet->getActiveSheet(0)->mergeCells('O'.($i+1).':P'.($i+2));

        $sheet->getActiveSheet(0)->mergeCells('C'.($i+2).':D'.($i+2));
        $sheet->getActiveSheet(0)->mergeCells('E'.($i+2).':F'.($i+2));
        $sheet->getActiveSheet(0)->mergeCells('G'.($i+2).':H'.($i+2));
        $sheet->getActiveSheet(0)->mergeCells('I'.($i+2).':J'.($i+2));
        $sheet->getActiveSheet(0)->mergeCells('K'.($i+2).':L'.($i+2));
        $sheet->getActiveSheet(0)->mergeCells('M'.($i+2).':N'.($i+2));


        $sheet->getActiveSheet(0)->setCellValue('A' . ($i+1), "순");
        $sheet->getActiveSheet(0)->setCellValue('B' . ($i+1), "상품명");
        $sheet->getActiveSheet(0)->setCellValue('C' . ($i+1), "주문매출");
        $sheet->getActiveSheet(0)->setCellValue('E' . ($i+1), "매출");
        //$sheet->getActiveSheet(0)->setCellValue('M' . ($i+1), "실매출액원가");
        $sheet->getActiveSheet(0)->setCellValue('G' . ($i+1), "에이전트별 매출");
        $sheet->getActiveSheet(0)->setCellValue('K' . ($i+1), "신규회원가입 대비매출");
        $sheet->getActiveSheet(0)->setCellValue('O' . ($i+1), "건별매출");

        $sheet->getActiveSheet(0)->setCellValue('C' . ($i+2), "전체주문\r(입금예정포함)");
        //$sheet->getActiveSheet(0)->setCellValue('E' . ($i+2), "전체매출액(전체)");
        //$sheet->getActiveSheet(0)->setCellValue('G' . ($i+2), "취소매출액(-)");
        //$sheet->getActiveSheet(0)->setCellValue('I' . ($i+2), "반품매출액(-)");
        $sheet->getActiveSheet(0)->setCellValue('E' . ($i+2), "실매출액(+)");

        $sheet->getActiveSheet(0)->setCellValue('G' . ($i+2), "신규매출");
        $sheet->getActiveSheet(0)->setCellValue('I' . ($i+2), "재구매매출");

        $sheet->getActiveSheet(0)->setCellValue('K' . ($i+2), "웹매출");
        $sheet->getActiveSheet(0)->setCellValue('M' . ($i+2), "모바일매출");



        $sheet->getActiveSheet(0)->setCellValue('C' . ($i+3), "수량(개)");
        $sheet->getActiveSheet(0)->setCellValue('D' . ($i+3), "주문액(원)");
        $sheet->getActiveSheet(0)->setCellValue('E' . ($i+3), "수량(개)");
        $sheet->getActiveSheet(0)->setCellValue('F' . ($i+3), "주문액(원)");
        $sheet->getActiveSheet(0)->setCellValue('G' . ($i+3), "수량(개)");
        $sheet->getActiveSheet(0)->setCellValue('H' . ($i+3), "주문액(원)");
        $sheet->getActiveSheet(0)->setCellValue('I' . ($i+3), "수량(개)");
        $sheet->getActiveSheet(0)->setCellValue('J' . ($i+3), "주문액(원)");
        $sheet->getActiveSheet(0)->setCellValue('K' . ($i+3), "수량(개)");
        $sheet->getActiveSheet(0)->setCellValue('L' . ($i+3), "주문액(원)");
        $sheet->getActiveSheet(0)->setCellValue('M' . ($i+3), "수량(개)");
        $sheet->getActiveSheet(0)->setCellValue('N' . ($i+3), "주문액(원)");
        $sheet->getActiveSheet(0)->setCellValue('O' . ($i+3), "웹");
        $sheet->getActiveSheet(0)->setCellValue('P' . ($i+3), "모바일");
        //$sheet->getActiveSheet(0)->setCellValue('N' . ($i+3), "마진(원)");
        //$sheet->getActiveSheet(0)->setCellValue('O' . ($i+3), "마진율(%)");


        $sheet->setActiveSheetIndex(0);
        //$i = $i + 2;

        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);

            $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 4), ($i + 1));
            $sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 4), $fordb->dt['pname']);
            $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 4), $fordb->dt['order_sale_cnt']);
            $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);




            $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 4), $fordb->dt['order_sale_sum']);
            $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            /*
            $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 4), $fordb->dt['sale_all_cnt']);
            $sheet->getActiveSheet()->getStyle('E' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('F' . ($i + $start + 4), $fordb->dt['sale_all_sum']);
            $sheet->getActiveSheet()->getStyle('F' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            */



            $real_sale_cnt = $fordb->dt['sale_all_cnt']-$fordb->dt['cancel_sale_cnt']-$fordb->dt['return_sale_cnt'];
            $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 4), $real_sale_cnt);
            $sheet->getActiveSheet()->getStyle('E' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $real_sale_coprice = $fordb->dt['sale_all_sum']-$fordb->dt['cancel_sale_sum']-$fordb->dt['return_sale_sum'];
            $sheet->getActiveSheet()->setCellValue('F' . ($i + $start + 4), $real_sale_coprice);
            $sheet->getActiveSheet()->getStyle('F' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('G' . ($i + $start + 4), $fordb->dt['real_sale_cnt_new']);
            $sheet->getActiveSheet()->getStyle('G' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('H' . ($i + $start + 4), $fordb->dt['real_sale_sum_new']);
            $sheet->getActiveSheet()->getStyle('H' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('I' . ($i + $start + 4), $fordb->dt['real_sale_cnt_reorder']);
            $sheet->getActiveSheet()->getStyle('I' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('J' . ($i + $start + 4), $fordb->dt['real_sale_sum_reorder']);
            $sheet->getActiveSheet()->getStyle('J' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);



            $sheet->getActiveSheet()->setCellValue('K' . ($i + $start + 4), $fordb->dt['real_sale_cnt_web']);
            $sheet->getActiveSheet()->getStyle('K' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('L' . ($i + $start + 4), $fordb->dt['real_sale_sum_web']);
            $sheet->getActiveSheet()->getStyle('L' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('M' . ($i + $start + 4), $fordb->dt['real_sale_cnt_mobile']);
            $sheet->getActiveSheet()->getStyle('M' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('N' . ($i + $start + 4), $fordb->dt['real_sale_sum_mobile']);
            $sheet->getActiveSheet()->getStyle('N' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('O' . ($i + $start + 4), ($fordb->dt['order_cnt_web'] > 0 ? $fordb->dt['real_sale_sum_web']/$fordb->dt['order_cnt_web']:0));
            $sheet->getActiveSheet()->getStyle('O' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $sheet->getActiveSheet()->setCellValue('P' . ($i + $start + 4), ($fordb->dt['order_cnt_mobile'] > 0 ? $fordb->dt['real_sale_sum_mobile']/$fordb->dt['order_cnt_mobile']:0));
            $sheet->getActiveSheet()->getStyle('P' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);


            $order_sale_cnt = $order_sale_cnt + returnZeroValue($fordb->dt['order_sale_cnt']);
            $order_cnt_web = $order_cnt_web + returnZeroValue($fordb->dt['order_cnt_web']);
            $order_cnt_mobile = $order_cnt_mobile + returnZeroValue($fordb->dt['order_cnt_mobile']);

            $order_sale_sum = $order_sale_sum + returnZeroValue($fordb->dt['order_sale_sum']);

            $sale_all_cnt = $sale_all_cnt + returnZeroValue($fordb->dt['sale_all_cnt']);
            $sale_all_sum = $sale_all_sum + returnZeroValue($fordb->dt['sale_all_sum']);



            $real_sale_cnt_sum = $real_sale_cnt_sum + returnZeroValue($real_sale_cnt);
            $real_sale_coprice_sum = $real_sale_coprice_sum + returnZeroValue($real_sale_coprice);



            $real_sale_cnt_new = $real_sale_cnt_new + returnZeroValue($fordb->dt['real_sale_cnt_new']);
            $real_sale_sum_new = $real_sale_sum_new + returnZeroValue($fordb->dt['real_sale_sum_new']);

            $real_sale_cnt_reorder = $real_sale_cnt_reorder + returnZeroValue($fordb->dt['real_sale_cnt_reorder']);
            $real_sale_sum_reorder = $real_sale_sum_reorder + returnZeroValue($fordb->dt['real_sale_sum_reorder']);

            $real_sale_cnt_web = $real_sale_cnt_web + returnZeroValue($fordb->dt['real_sale_cnt_web']);
            $real_sale_cnt_mobile = $real_sale_cnt_mobile + returnZeroValue($fordb->dt['real_sale_cnt_mobile']);

            $real_sale_sum_web = $real_sale_sum_web + returnZeroValue($fordb->dt['real_sale_sum_web']);
            $real_sale_sum_mobile = $real_sale_sum_mobile + returnZeroValue($fordb->dt['real_sale_sum_mobile']);




            $sale_coprice_sum = $sale_coprice_sum + returnZeroValue($sale_coprice);
            $margin_sum = $margin_sum + returnZeroValue($margin);


        }

        if($real_sale_coprice_sum > 0){
            $margin_sum_rate = $margin_sum/$real_sale_coprice_sum;// 엑셀 포맷 정의시 자동으로 *100 이 처리됨
        }else{
            $margin_sum_rate = 0;
        }

        //$i++;
        $sheet->getActiveSheet(0)->mergeCells('A'.($i + $start+4).':C'.($i+ $start+4));
        $sheet->getActiveSheet()->setCellValue('A' . ($i + $start + 4), '합계');
        //$sheet->getActiveSheet()->setCellValue('B' . ($i + $start + 4), getIventoryCategoryPathByAdmin($goods_infos[$i]['cid'], 4));
        $sheet->getActiveSheet()->setCellValue('C' . ($i + $start + 4), $order_sale_cnt);
        $sheet->getActiveSheet()->getStyle('C' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('D' . ($i + $start + 4), $order_sale_sum);
        $sheet->getActiveSheet()->getStyle('D' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('E' . ($i + $start + 4), $real_sale_cnt_sum);
        $sheet->getActiveSheet()->getStyle('E' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('F' . ($i + $start + 4), $real_sale_coprice_sum);
        $sheet->getActiveSheet()->getStyle('F' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('G' . ($i + $start + 4), $real_sale_cnt_new);
        $sheet->getActiveSheet()->getStyle('G' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('H' . ($i + $start + 4), $real_sale_sum_new);
        $sheet->getActiveSheet()->getStyle('H' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);



        $sheet->getActiveSheet()->setCellValue('I' . ($i + $start + 4), $real_sale_cnt_reorder);
        $sheet->getActiveSheet()->getStyle('I' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('J' . ($i + $start + 4), $real_sale_sum_reorder);
        $sheet->getActiveSheet()->getStyle('J' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('K' . ($i + $start + 4), $real_sale_cnt_web);
        $sheet->getActiveSheet()->getStyle('K' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('L' . ($i + $start + 4), $real_sale_sum_web);
        $sheet->getActiveSheet()->getStyle('L' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);




        $sheet->getActiveSheet()->setCellValue('M' . ($i + $start + 4), $real_sale_cnt_mobile);
        $sheet->getActiveSheet()->getStyle('M' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('N' . ($i + $start + 4), $real_sale_sum_mobile);
        $sheet->getActiveSheet()->getStyle('N' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('O' . ($i + $start + 4), ($order_cnt_web > 0 ? $real_sale_sum_web/$order_cnt_web:0));
        $sheet->getActiveSheet()->getStyle('O' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

        $sheet->getActiveSheet()->setCellValue('P' . ($i + $start + 4), ($order_cnt_mobile > 0 ? $real_sale_sum_mobile/$order_cnt_mobile:0));
        $sheet->getActiveSheet()->getStyle('P' . ($i + $start + 4))->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER);



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
        $sheet->getActiveSheet()->getColumnDimension('N')->setWidth(10);
        $sheet->getActiveSheet()->getColumnDimension('O')->setWidth(10);

        $sheet->getActiveSheet()->getRowDimension($start+2)->setRowHeight(30);

        //header('Content-Type: application/vnd.ms-excel');
        //header('Content-Disposition: attachment;filename="'.iconv("utf-8","cp949","상품판매종합 분석_매출요약분석.xls").'"');
        //header('Cache-Control: max-age=0');

		$filename = "상품판매종합분석_매출상세분석";

        // $objWriter->setUseInlineCSS(true);
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $sheet->getActiveSheet()->getStyle('A'.($start+1).':P'.($i+$start+4))->applyFromArray($styleArray);
        $sheet->getActiveSheet()->getStyle('A'.$start.':P'.($i+$start+4))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $sheet->getActiveSheet()->getStyle('A'.$start.':P'.($i+$start+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getActiveSheet()->getStyle('A'.($start).':P'.($start+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setWrapText(true);
        $sheet->getActiveSheet()->getStyle('B'.($start+4).':B'.($i+$start+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        //$sheet->getActiveSheet()->getStyle('A'.$start.':O'.($i+$start+4))->getAlignment()->setIndent(1);
        //$sheet->getActiveSheet()->getStyle('A10')->getAlignment()->setIndent(10); 왼쪽 들여쓰기
        $sheet->getActiveSheet()->getStyle('A'.$start.':P'.($i+$start+4))->getFont()->setSize(10)->setName('돋움');

        $sheet->getActiveSheet()->getStyle('A2')->getFont()->setSize(15)->setBold(true)->setName('돋움');
        $sheet->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getActiveSheet()->getStyle('A'.($i+$start+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        unset($styleArray);
        //$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel5');
        //$objWriter->save('php://output');

		$sheet->getActiveSheet()->setTitle('상품판매종합분석');
		$sheet->setActiveSheetIndex(0);

		//	wel_ 엑셀 다운로드 히스토리 저장
		$ig_db = new Database;
		$ig_excel_dn_history_SQL = "
			INSERT INTO
				ig_excel_dn_history
			SET
				code = '".$_SESSION['admininfo']['charger_ix']."',
				ip = '". $_SERVER['REMOTE_ADDR']."',
				dn_type = 'order_salesbydate_paymenttype',
				dn_reason = '".addslashes($_GET['irs'])."',
				dn_text = '".addslashes($_SERVER['HTTP_REFERER'].$QUERY_STRING)."',
				regDt = '".date("Y-m-d H:i:s")."'
		";
		$ig_db->query($ig_excel_dn_history_SQL);
		//	//wel_ 엑셀 다운로드 히스토리 저장

		$download_filename = $filename.'_'.date("YmdHis").'.zip'; 
		$igExcel_file = '../../excelDn/'.$filename.'_'.date("YmdHis").'.xls';
	
		$ig_dnFile_full = '../../excelDn/'.$download_filename;

		$objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel5');
		$objWriter->save($igExcel_file);

		if(trim($_GET['ipw']) == "") {
			$ig_pw = "barrel";
		} else {
			$ig_pw = $_GET['ipw'];
		}

		shell_exec('zip -P '.$ig_pw.' -r ../../excelDn/'.$download_filename.' '.$igExcel_file);

		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $download_filename . '"'); // 저장될 파일 이름
		header('Content-Transfer-Encoding: binary');
		header('Content-length: ' . filesize($ig_dnFile_full));
		header('Expires: 0');
		header("Pragma: public");

		ob_clean();
		flush();
		readfile($ig_dnFile_full);

		unlink($igExcel_file);
		unlink($ig_dnFile_full);

        exit;
    }
    $exce_down_str = "";
    if(false) {
        $exce_down_str = "<img src=\"../../images/korea/btn_print.gif\" onclick=\"PopSWindow('?mode=print&" . str_replace("mode=iframe&", "", $_SERVER["QUERY_STRING"]) . "',1150,500,'logstory_print');\" style=\"margin-right:3px;cursor:pointer;\"  >";
    }
    //$exce_down_str .= "<a href='?mode=excel&report_type=$report_type&".str_replace("mode=iframe&", "",$_SERVER["QUERY_STRING"])."'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_excel_save.gif' border=0></a>";

	$exce_down_str = "<img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_excel_save.gif' border=0 style='cursor:pointer' 
	onclick=\"ig_excel_dn_chk('?mode=excel&report_type=$report_type&".str_replace("mode=iframe&", "",$_SERVER["QUERY_STRING"])."');\">";

    $mstring = "<table width='100%' border=0>";
    if(isset($_GET["mode"]) && ($_GET["mode"] == "pop" || $_GET["mode"] == "print")){
        $mstring .= "<tr><td >".TitleBar("상품군별 분석 : ",$dateString."".($cid ? "-".getCategoryPath($cid,4):""),false, $exce_down_str)."</td></tr>";
    }else{
        $mstring .= "<tr><td >".TitleBar("상품군별 분석 : ",$dateString."".($cid ? "-".getCategoryPath($cid,4):""),true, $exce_down_str)."</td></tr>";
    }

    $mstring .= " 
						<tr height=50>
							<td >
								<div class='tab'>
									<table class='s_org_tab'>
									<tr>
										<td class='tab'> 
											<table id='tab_01'  ".(($report_type == '3') ? "class=on":"").">
											<tr>
												<th class='box_01'></th>
												<td class='box_02' onclick=\"document.location.href='?report_type=3&".str_replace("report_type=$report_type&", "",str_replace("mode=iframe&", "",$_SERVER["QUERY_STRING"]))."'\">매출요약 분석</td>
												<th class='box_03'></th>
											</tr>
											</table> 
											<table id='tab_02'  ".(($report_type == '1' || $report_type == '') ? "class=on":"").">
											<tr>
												<th class='box_01'></th>
												<td class='box_02' onclick=\"document.location.href='?report_type=1&".str_replace("report_type=$report_type&", "",str_replace("mode=iframe&", "",$_SERVER["QUERY_STRING"]))."'\">매출상세 분석</td>
												<th class='box_03'></th>
											</tr>
											</table>
											 <!--
											<table id='tab_03'  >
											<tr>
												<th class='box_01'></th>
												<td class='box_02' onclick=\"document.location.href='?report_type=2&".str_replace("report_type=$report_type&", "",str_replace("mode=iframe&", "",$_SERVER["QUERY_STRING"]))."'\">구매전환 분석</td>
												<th class='box_03'></th>
											</tr>
											</table>
											 -->
										</td>
										<td class='btn' style='padding:5px 0px 0px 10px;'>
											<input type=checkbox name='view_detail' id='view_detail' value='1' onclick=\"reloadView()\"  ".($_COOKIE['view_detail'] == 1 ? "checked":"")." ><label for='view_detail'> 카테고리 상세분류로 보기</label>
										</td>
									</tr>
									</table>
								</div>
							</td>
						  </tr>
					</table>";
    $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>
						<col width='3%'>
						<col width='*'>
						<col width='5%'>
						<col width='6%'>
						<col width='5%'>
						<col width='6%'>
						<col width='5%'>
						<col width='6%'>
						<col width='5%'>
						<col width='6%'>
						<col width='5%'>
						<col width='6%'>
						<col width='6%'>
						<col width='5%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>
						<col width='6%'>";
    $mstring .= "
		<tr height=30>
			<td class=s_td rowspan=3>순</td>
			<td class=m_td rowspan=3>".OrderByLink("상품명", "pname", $ordertype)."</td>
			<td class=m_td colspan=2>주문매출</td>
			<td class=m_td colspan=2>매출</td>
			<td class=m_td colspan=4>신규회원가입대비<br>매출</td>
			<td class=m_td colspan=4>에이전트별<br>매출</td>
			<td class=m_td rowspan=2 colspan=2>건별매출</td>
		</tr>
		<tr height=30>
			<td class='m_td small' colspan=2 style='line-height:140%;'><b>전체주문</b><br>(입금예정포함)</td>
			<!--td class=m_td colspan=2>전체매출액(전체)</td-->
			
			<td class=m_td colspan=2>실매출액(+)</td>
			<td class=m_td colspan=2>신규매출</td>
			<td class=m_td colspan=2>재구매매출</td>
			<td class=m_td colspan=2>웹매출</td>
			<td class=m_td colspan=2>모바일매출</td>
		</tr>
		<tr height=30 align=center>			
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<!--td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td-->
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td >웹</td>
			<td class=m_td >모바일</td>
			</tr>\n";
    /*

    sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_sale_cnt,
                    sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pt_dcprice else 0 end) as order_sale_sum,
                    sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt,
                    sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pt_dcprice else 0 end) as cancel_sale_sum,
                    sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt,
                    sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pt_dcprice else 0 end) as return_sale_sum,

                    */

    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);
        $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
		<td class='list_box_td list_bg_gray' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($i+1)."</td>
		<td class='list_box_td point' style='text-align:left;' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt['pname'];
        $mstring .= "</td>
		

		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['order_sale_cnt'],0)."&nbsp;</td>
		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['order_sale_sum'],0)."&nbsp;</td>

		<!--td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['sale_all_cnt'],0)."&nbsp;</td>
		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['sale_all_sum'],0)."&nbsp;</td-->

		";

        $real_sale_cnt = $fordb->dt['sale_all_cnt']-$fordb->dt['cancel_sale_cnt']-$fordb->dt['return_sale_cnt'];
        $real_sale_coprice = $fordb->dt['sale_all_sum']-$fordb->dt['cancel_sale_sum']-$fordb->dt['return_sale_sum'];

        $mstring .= "<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($real_sale_cnt,0)."&nbsp;</td>";


        $mstring .= "<td class='list_box_td number point' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($real_sale_coprice,0)."&nbsp;</td>";
        $mstring .= "<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['real_sale_cnt_new'],0)."&nbsp;</td>
		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['real_sale_sum_new'],0)."&nbsp;</td>

		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['real_sale_cnt_reorder'],0)."&nbsp;</td>
		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['real_sale_sum_reorder'],0)."&nbsp;</td>";

        $mstring .= "<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['real_sale_cnt_web'],0)."&nbsp;</td>
		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['real_sale_sum_web'],0)."&nbsp;</td>

		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['real_sale_cnt_mobile'],0)."&nbsp;</td>
		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['real_sale_sum_mobile'],0)."&nbsp;</td>
		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt['order_cnt_web'] > 0 ? $fordb->dt['real_sale_sum_web']/$fordb->dt['order_cnt_web']:0),0)."&nbsp;</td>
		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt['order_cnt_mobile'] > 0 ? $fordb->dt['real_sale_sum_mobile']/$fordb->dt['order_cnt_mobile']:0),0)."&nbsp;</td>
		";

        /*
        $sale_coprice = $fordb->dt['coprice_all_sum']-$fordb->dt['cancel_coprice_sum']-$fordb->dt['return_coprice_sum'];
        //echo $fordb->dt['coprice_all_sum']."-".$fordb->dt['cancel_coprice_sum']."-".$fordb->dt['return_coprice_sum']."<br>";
        $mstring .= "<td class='list_box_td number blue_point' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($sale_coprice,0)."&nbsp;</td>";

        $margin = $real_sale_coprice - $sale_coprice;
        $mstring .= "<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($margin,0)."&nbsp;</td>";
        if($real_sale_coprice > 0){
            $margin_rate = $margin/$real_sale_coprice*100;
        }else{
            $margin_rate = 0;
        }
        $mstring .= "<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($margin_rate,1)."&nbsp;</td>";
        */



        $mstring .= "</tr>\n";

        $order_sale_cnt = $order_sale_cnt + returnZeroValue($fordb->dt['order_sale_cnt']);
        $order_cnt_web = $order_cnt_web + returnZeroValue($fordb->dt['order_cnt_web']);
        $order_cnt_mobile = $order_cnt_mobile + returnZeroValue($fordb->dt['order_cnt_mobile']);

        $order_sale_sum = $order_sale_sum + returnZeroValue($fordb->dt['order_sale_sum']);

        $sale_all_cnt = $sale_all_cnt + returnZeroValue($fordb->dt['sale_all_cnt']);
        $sale_all_sum = $sale_all_sum + returnZeroValue($fordb->dt['sale_all_sum']);

        $real_sale_cnt_new = $real_sale_cnt_new + returnZeroValue($fordb->dt['real_sale_cnt_new']);
        $real_sale_sum_new = $real_sale_sum_new + returnZeroValue($fordb->dt['real_sale_sum_new']);

        $real_sale_cnt_reorder = $real_sale_cnt_reorder + returnZeroValue($fordb->dt['real_sale_cnt_reorder']);
        $real_sale_sum_reorder = $real_sale_sum_reorder + returnZeroValue($fordb->dt['real_sale_sum_reorder']);


        $real_sale_cnt_web = $real_sale_cnt_web + returnZeroValue($fordb->dt['real_sale_cnt_web']);
        $real_sale_cnt_mobile = $real_sale_cnt_mobile + returnZeroValue($fordb->dt['real_sale_cnt_mobile']);

        $real_sale_sum_web = $real_sale_sum_web + returnZeroValue($fordb->dt['real_sale_sum_web']);
        $real_sale_sum_mobile = $real_sale_sum_mobile + returnZeroValue($fordb->dt['real_sale_sum_mobile']);





        $real_sale_cnt_sum = $real_sale_cnt_sum + returnZeroValue($real_sale_cnt);
        $real_sale_coprice_sum = $real_sale_coprice_sum + returnZeroValue($real_sale_coprice);
        $sale_coprice_sum = $sale_coprice_sum + returnZeroValue($sale_coprice);
        $margin_sum = $margin_sum + returnZeroValue($margin);


    }

    if ($order_sale_sum == 0){
        $mstring .= "<tr  align=center height=200><td colspan=16 class='list_box_td'  >결과값이 없습니다.</td></tr>\n";
    }
    //$mstring .= "</table>\n";
    /*
    $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box' style='margin-top:5px;'>
                        <col width='3%'>
                        <col width='*'>
                        <col width='5%'>
                        <col width='6%'>
                        <col width='5%'>
                        <col width='6%'>
                        <col width='5%'>
                        <col width='6%'>
                        <col width='5%'>
                        <col width='6%'>
                        <col width='5%'>
                        <col width='6%'>
                        <col width='6%'>
                        <col width='5%'>
                        <col width='6%'>";
    */
    //$mstring .= "<tr height=2 bgcolor=#ffffff align=center><td colspan=2></td></tr>\n";
    if($real_sale_coprice_sum > 0){
        $margin_sum_rate = $margin_sum/$real_sale_coprice_sum*100;
    }else{
        $margin_sum_rate = 0;
    }

    $mstring .= "<tr height=25 align=right>
	<td class=s_td align=center colspan=2>합계</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($order_sale_cnt,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($order_sale_sum,0)."</td>
	<!--td class='e_td number' style='padding-right:10px;'>".number_format($sale_all_cnt,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($sale_all_sum,0)."</td-->
	 
	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_cnt_sum,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_coprice_sum,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_cnt_new,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_sum_new,0)."</td>	
	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_cnt_reorder,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_sum_reorder,0)."</td>

	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_cnt_web,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_sum_web,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_cnt_mobile,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_sum_mobile,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format(($order_cnt_web > 0 ? $real_sale_sum_web/$order_cnt_web:0),0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format(($order_cnt_mobile > 0 ? $real_sale_sum_mobile/$order_cnt_mobile:0),0)."</td>
	</tr>\n";
    $mstring .= "</table>\n";
    $mstring .= "<table width='100%'><tr><td> </td><td align=right style='padding-top:10px;'>VAT 포함 (단위:원)  </td></tr></table>";

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


    $mstring .= HelpBox("상품판매종합 분석", $help_text);
    return $mstring;
}
?>
<script type="text/javascript">
	//	wel_ 새벽시간(23시~07시)이나 휴무일 등 업무시간 외 다운로드시 검수 member_excel2003
	function ig_excel_dn_chk(s_val_Data) {
		//console.log(s_val_Data);
		var ig_now = new Date();   //현재시간
		var ig_hour = ig_now.getHours();   //현재 시간 중 시간.

			//	새벽시간(23시~07시), 휴무일(일, 토)
		//if(Number(ig_hour) >= "23" || Number(ig_hour) <= "7" || Number(ig_now.getDay()) == "0" || Number(ig_now.getDay()) == "6") {
			var ig_inputString = prompt('사유를 간략하게 입력하세요.\r\n(20자 이내(띄어쓰기포함), 특수문자 제외)');

			if(ig_inputString != null && ig_inputString.trim() != "") {
				//	엑셀다운로드 진행

					var str_length = ig_inputString.length;		// 전체길이

					if(str_length > "20") {
						alert("사유가 20자 이상 입니다.");
						return false;
					} else {
						var ig_inputString_PW = prompt('파일 비밀번호를 지정해 주세요.\r\n(15자 이하, 영문/숫자만 사용, 공백사용금지)');

							if(ig_inputString_PW != null && ig_inputString_PW.trim() != "") {

								var str_PW_length = ig_inputString_PW.length;		// 전체길이

								if(str_PW_length > "15") {
									alert("비밀번호를 15자 이하로 해주세요.");
									return false;
								} else {
									location.href = s_val_Data+"irs="+ig_inputString+"&ipw="+ig_inputString_PW;
								}

							} else {
								alert("비밀번호를 입력해 주세요.");
								return false;
							}
					}


			} else {
				alert("사유를 입력하세요");
				return false;
			}
		/*} else {
			//	일반 업무때 다운로드
			var ig_inputString_PW = prompt('파일 비밀번호를 지정해 주세요.\r\n(15자 이하, 영문/숫자만 사용, 공백사용금지)');

				if(ig_inputString_PW != null && ig_inputString_PW.trim() != "") {

					var str_PW_length = ig_inputString_PW.length;		// 전체길이

					if(str_PW_length > "15") {
						alert("비밀번호를 15자 이하로 해주세요.");
						return false;
					} else {
						location.href = s_val_Data+"&ipw="+ig_inputString_PW;
					}

				} else {
					alert("비밀번호를 입력해 주세요.");
					return false;
				}
		}*/



	}
</script>