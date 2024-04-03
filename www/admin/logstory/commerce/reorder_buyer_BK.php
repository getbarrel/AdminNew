<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../report/etcreferer.chart.php");
include("../include/commerce.lib.php");
include("../include/campaign.lib.php");
$where = "";

if((!empty($_GET['mall_ix']) && $_GET['mall_ix']) !="" ){
    if($_GET["mall_ix"] == "dcb33fdbf7c6f40e334a43ce42194637"){
        $where .="and od.buyer_type = '1' ";
    }else if($_GET["mall_ix"] == "dcb33fdbf7c6f40e334a43ce42194638"){
        $where .="and od.buyer_type = '2' ";
    }
}
if((!empty($_GET['seller_type']) && $_GET['seller_type']) !="" ){
    $where .="and seller_type = '".$_GET['seller_type']."' ";
}

if($status_disp == "IC"){
    $date_str = "od.ic_date";
}else if($status_disp == "DI"){
    $date_str = "od.di_date";
}else if($status_disp == "OC" || $status_disp == ""){
    $date_str = "od.regdate";
}

//		$date_str = "date_format(od.ic_date,'%Y%m%d')";
//		$date_str = "date_format(od.di_date,'%Y%m%d')";
//		$date_str = "date_format(od.regdate,'%Y%m%d')";

if(!empty($_GET["memreg_sdate"]) && $_GET["memreg_sdate"] && !empty($_GET["memreg_edate"]) &&  $_GET["memreg_edate"]){
    $memreg_sdate = $_GET["memreg_sdate"];
    $memreg_edate = $_GET["memreg_edate"];

    $where .= "and cu.date between '".substr($memreg_sdate, 0, 4)."-".substr($memreg_sdate, 4, 2)."-".substr($memreg_sdate, 6, 2)." 00:00:00' and '".substr($memreg_edate, 0, 4)."-".substr($memreg_edate, 4, 2)."-".substr($memreg_edate, 6, 2)." 23:59:59' ";
}

if(!empty($_GET["sdate"]) && $_GET["sdate"] && !empty($_GET["edate"])  && $_GET["edate"]){
    $startDate = $_GET["sdate"];
    $endDate = $_GET["edate"];

    $order_where = "and ".$date_str." between '".substr($startDate, 0, 4)."-".substr($startDate, 4, 2)."-".substr($startDate, 6, 2)." 00:00:00' and '".substr($endDate, 0, 4)."-".substr($endDate, 4, 2)."-".substr($endDate, 6, 2)." 23:59:59' ";
    $where .= $order_where;
}else{
    $sdate = date("Y-m-d");
    $edate = date("Y-m-d");

    $order_where = "and ".$date_str." between '".substr($sdate, 0, 4)."-".substr($sdate, 4, 2)."-".substr($sdate, 6, 2)." 00:00:00' and '".substr($edate, 0, 4)."-".substr($edate, 4, 2)."-".substr($edate, 6, 2)." 23:59:59' ";
    $where .= $order_where;
}

if((!empty($cid2) && $cid2) != ""){
    $where .= " and od.cid LIKE '".substr($cid2,0,($depth+1)*3)."%'";
}


if(is_array($age)){
    $age_str = "";
    for($i=0;$i < count($age);$i++){
        if($age[$i] != ""){
            if(empty($age_str)){
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

if((!empty($_GET['member_div']) && $_GET['member_div'] =="member") ){
    $where .="and o.user_code != '' ";
}else if((!empty($_GET['member_div']) && $_GET['member_div'] =="nonmember") ){
    $where .="and o.user_code = '' ";
}

if(!empty($_GET['sex'])){
    //$where .="and sex = '".$_GET['sex']."' ";
    //컬럼 추가후
}

//promotion_cupon_code
if((!empty($_GET["brand_code"]) && $_GET["brand_code"])){
    $brand_code_array = str_replace(" ","",$_GET["brand_code"]);
    $brand_code_array = explode(",",$brand_code_array);
    if(is_array($brand_code_array)){
        $where .= " AND od.brand_code IN ('".implode("','",$brand_code_array)."')";
    }else{
        $where .= " AND od.brand_code = '".$_GET["brand_code"]."' ";
    }
}

if((!empty($_GET["promotion_cupon_code"]) && $_GET["promotion_cupon_code"])){
    $use_coupon_code_array = explode(",",$_GET["promotion_cupon_code"]);
    if(is_array($use_coupon_code_array)){
        $where .= " AND od.use_coupon_code IN ('".implode("','",$use_coupon_code_array)."')";
    }else{
        $where .= " AND od.use_coupon_code = '".$_GET["promotion_cupon_code"]."' ";
    }
}

if((!empty($_GET["product_code"]) && $_GET["product_code"])){
    $product_code_array = explode(",",$_GET["product_code"]);
    if(is_array($product_code_array)){
        $where .= " AND od.pid IN ('".implode("','",$product_code_array)."')";
    }else{
        $where .= " AND od.pid = '".$_GET["product_code"]."' ";
    }
}

if((!empty($_GET["company_v_code"]) && $_GET["company_v_code"])){
    $company_v_code_array = explode(",",$_GET["company_v_code"]);
    if(is_array($company_v_code_array)){
        $where .= " AND od.company_id IN ('".implode("','",$company_v_code_array)."')";
    }else{
        $where .= " AND od.company_id = '".$_GET["company_v_code"]."' ";
    }
}

if((!empty($_GET["trade_company_code"]) && $_GET["trade_company_code"])){
    $trade_company_code_array = explode(",",$_GET["trade_company_code"]);
    if(is_array($trade_company_code_array)){
        $where .= " AND od.trade_company IN ('".implode("','",$trade_company_code_array)."')";
    }else{
        $where .= " AND od.trade_company = '".$_GET["trade_company_code"]."' ";
    }
}

function ReportTable($vdate,$SelectReport=1){
    global $LargeImageSize;
    global $where, $order_where, $memreg_sdate, $memreg_edate, $SubID;
    global $startDate, $endDate, $report_type, $report_group_type;
    global $v15ago, $vonemonthago, $v2monthago, $v3monthago;
    global $all_sale_status, $cancel_status, $return_status,$member_div,$mall_ix;



    $pageview01 = 0;
    $fordb = new forbizDatabase();
    $fordb2 = new forbizDatabase();


    if($SelectReport == ""){
        $SelectReport = 1;
    }
    if($vdate == ""){
        $vdate = date("Ymd", time());
        $today = date("Ymd", time());
        $vyesterday = date("Ymd", time()-84600);
        $voneweekago = date("Ymd", time()-84600*7);
        $v15ago = date("Ymd", time()-84600*15);
        $vonemonthago = date("Ymd", time()-84600*30);
        $v2monthago = date("Ymd", time()-84600*60);
        $v3monthago = date("Ymd", time()-84600*90);

    }else{
        if($SelectReport ==3){
            $vdate = $vdate."01";
        }
        $today = date("Ymd", time());
        $vdate = date("Ymd", time());
        $vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
        $vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
        $v15ago = date("Ymd", time()-84600*15);
        $vonemonthago = date("Ymd", time()-84600*30);
        $v2monthago = date("Ymd", time()-84600*60);
        $v3monthago = date("Ymd", time()-84600*90);

    }



    if((!empty($groupbytype) && $groupbytype) == ""){
        $groupbytype="day";
    }

    if(!is_array($age)){
        $age[] = "";
    }

    // promotion_cupon_code


    if((!empty($_GET["vat_type"]) && $_GET["vat_type"])){
        $vat_type = $_GET["vat_type"];
    }else{
        $vat_type = "Y";
    }

    if((!empty($_GET["status_disp"]) && $_GET["status_disp"])){
        $status_disp = $_GET["status_disp"];
    }else{
        $status_disp = "OC";
    }


    if(!empty($_GET["groupbytype"]) && $_GET["groupbytype"]){
        $groupbytype = $_GET["groupbytype"];
    }else{
        $groupbytype = "day";
    }






    if($SelectReport == 1 || true){

        if($fordb->dbms_type == "oracle"){//user_code 안됨
            /*
            $sql = "Select cmd.name , cu.id as userid,c.pname, a.*
                from ".TBL_COMMERCE_SALESTACK." a left outer join ".TBL_COMMON_USER." cu on a.ucode = cu.code, ".TBL_SHOP_PRODUCT." c , ".TBL_COMMON_MEMBER_DETAIL." cmd
                where  a.pid = c.id and cu.code = cmd.code and vdate = '$vdate' and step6 = 1 order by a.vdate, vtime";
            */
            //like '%Y%m%d' as vdate
            $sql = "Select AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, od.regdate, od.regdate like '%H' as vtime,  
					cu.id as userid, o.user_code, od.pcnt as vquantity , od.pname, od.pid,  od.psprice as sellprice
					from ".TBL_SHOP_ORDER." o 
					right join ".TBL_SHOP_ORDER_DETAIL." od on o.oid = od.oid
					left join ".TBL_COMMON_USER." cu on o.user_code = cu.code
					left join  ".TBL_COMMON_MEMBER_DETAIL." cmd on  cu.code = cmd.code
					where od.regdate between '".substr($vdate, 0, 4)."-".substr($vdate, 4, 2)."-".substr($vdate, 6, 2)." 00:00:00' and '".substr($vdate, 0, 4)."-".substr($vdate, 4, 2)."-".substr($vdate, 6, 2)." 23:59:59'  
					 
					".$where."
					order by od.regdate like '%Y%m%d' ";
        }else{

            /*
            $sql = "Select AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cu.id as userid,c.pname, a.*
                from ".TBL_COMMERCE_SALESTACK." a left outer join ".TBL_COMMON_USER." cu on a.ucode = cu.code, ".TBL_SHOP_PRODUCT." c , ".TBL_COMMON_MEMBER_DETAIL." cmd
                where  a.pid = c.id and cu.code = cmd.code and vdate = '$vdate' and step6 = 1 order by a.vdate, vtime";
            */
            if((!empty($_GET["member_div"]) && $_GET["member_div"]) == "nonmember"){
                $name_str = " o.bname ";
            }else{
                $name_str = " AES_DECRYPT(UNHEX(cmd.name),'".$fordb->ase_encrypt_key."') ";
            }
            //like '%Y%m%d' as vdate
            $sql = "Select ".$name_str." as name, od.regdate, od.regdate like '%H' as vtime,  cu.date as mem_regdate, mg.gp_name , cu.visit, 
							cu.id as userid, o.user_code, count(*) as order_cnt,  sum(od.pcnt) as vquantity , sum(od.dcprice) as order_total_price, sum(od.ptprice) as sellprice ";
            //echo DateDiff();
            $startDate_unixtimestamp = mktime(0,0,0,substr((int)$startDate,4,2),substr((int)$startDate,6,2),substr((int)$startDate,0,4));
            $endDate_unixtimestamp = mktime(0,0,0,substr((int)$endDate,4,2),substr((int)$endDate,6,2),substr((int)$endDate,0,4));
            $nLoop = intval(($endDate_unixtimestamp - $startDate_unixtimestamp)/86400);

            if(empty($nLoop)){
                $nLoop = 30;
            }
//echo "startDate_unixtimestamp:".$startDate_unixtimestamp."<br>";
//echo "endDate_unixtimestamp:".$endDate_unixtimestamp."<br>";

            for($i = 0; $i <= $nLoop;$i++){
                $LoopDate = date("Ymd",mktime(0,0,0,substr((int)$startDate,4,2),substr((int)$startDate,6,2),substr((int)$startDate,0,4))+60*60*24*$i);
                $sql .= ",sum(case when od.regdate between '".substr($LoopDate, 0, 4)."-".substr($LoopDate, 4, 2)."-".substr($LoopDate, 6, 2)." 00:00:00' and '".substr($LoopDate, 0, 4)."-".substr($LoopDate, 4, 2)."-".substr($LoopDate, 6, 2)." 23:59:59' then od.ptprice else 0 end) as sellprice_".$LoopDate."
					";
                //$sql .= ",sum(case when od.regdate like '%Y%m%d' = '".$LoopDate."' then od.ptprice else 0 end) as sellprice_".$LoopDate."";
            }
            $sql .= "from ".TBL_SHOP_ORDER." o 
							right join (
								select oid, regdate, ic_date, di_date, sum(od.pcnt) as pcnt, sum(psprice) as psprice ,sum(dcprice) as dcprice , sum(ptprice) as ptprice    
								";


            $sql .= "	from ".TBL_SHOP_ORDER_DETAIL." od 
								where 1=1 ".$order_where." 
								and od.status not in ('".implode("','",$all_sale_status)."') 
								and od.status not in ('".implode("','",$cancel_status)."')
								and od.status not in ('".implode("','",$return_status)."')
								group by oid 
							) od on o.oid = od.oid
							left join ".TBL_COMMON_USER." cu on o.user_code = cu.code
							left join  ".TBL_COMMON_MEMBER_DETAIL." cmd on  cu.code = cmd.code
							left join ".TBL_SHOP_GROUPINFO." mg on cmd.gp_ix = mg.gp_ix
							where 1
							".$where."";
            if((!empty($_GET["member_div"]) && $_GET["member_div"]) == "nonmember"){
                $sql .= "group by o.bname";
            }else{
                $sql .= "group by o.user_code";
            }
            $sql .= "
							order by sellprice desc";//date_format(od.regdate,'%Y%m%d')
        }

        //echo nl2br($sql);
        //exit;
        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");

    }else if($SelectReport == 2 || $SelectReport == 4){
        if($SelectReport == "4"){
            $vdate = $search_sdate;
            $vweekenddate = $search_edate;
        }

        if($fordb->dbms_type == "oracle"){
            /*
            $sql = "Select cmd.name, cu.id as userid,c.pname, a.* from ".TBL_COMMERCE_SALESTACK." a left outer join ".TBL_COMMON_USER." cu on a.ucode = cu.code, ".TBL_SHOP_PRODUCT." c , ".TBL_COMMON_MEMBER_DETAIL." cmd
            where a.pid = c.id and cu.code = cmd.code and vdate between '$vdate' and '$vweekenddate' and step6 = 1
            order by a.vdate, vtime";
            */
            //od.regdate like '%Y%m%d' as vdate
            $sql = "Select AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, od.regdate, od.regdate like '%H' as vtime,  
				cu.id as userid, o.user_code, od.pcnt as vquantity , od.pname, od.pid,  od.psprice as sellprice
				from ".TBL_SHOP_ORDER." o 
				right join ".TBL_SHOP_ORDER_DETAIL." od on o.oid = od.oid
				left join ".TBL_COMMON_USER." cu on o.user_code = cu.code
				left join  ".TBL_COMMON_MEMBER_DETAIL." cmd on  cu.code = cmd.code
				where od.regdate between '".substr($vdate, 0, 4)."-".substr($vdate, 4, 2)."-".substr($vdate, 6, 2)." 00:00:00' and '".substr($vweekenddate, 0, 4)."-".substr($vweekenddate, 4, 2)."-".substr($vweekenddate, 6, 2)." 23:59:59'  
				 
				".$where."
				order by od.regdate ";
            //order by od.regdate like '%Y%m%d'
        }else{
            /*
            $sql = "Select AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cu.id as userid,c.pname, a.* from ".TBL_COMMERCE_SALESTACK." a left outer join ".TBL_COMMON_USER." cu on a.ucode = cu.code, ".TBL_SHOP_PRODUCT." c , ".TBL_COMMON_MEMBER_DETAIL." cmd
            where a.pid = c.id and cu.code = cmd.code and vdate between '$vdate' and '$vweekenddate' and step6 = 1
            order by a.vdate, vtime";
            */
            if((!empty($_GET["member_div"]) && $_GET["member_div"]) == "nonmember"){
                $name_str = " o.bname ";
            }else{
                $name_str = " AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') ";
            }
            //od.regdate like '%Y%m%d' as vdate
            $sql = "Select ".$name_str." as name, od.regdate, od.regdate like '%H' as vtime,  
				cu.id as userid, o.user_code, od.pcnt as vquantity , od.pname, od.pid,  od.psprice as sellprice
				from ".TBL_SHOP_ORDER." o 
				right join ".TBL_SHOP_ORDER_DETAIL." od on o.oid = od.oid
				left join ".TBL_COMMON_USER." cu on o.user_code = cu.code
				left join  ".TBL_COMMON_MEMBER_DETAIL." cmd on  cu.code = cmd.code
				where od.regdate between '".substr($vdate, 0, 4)."-".substr($vdate, 4, 2)."-".substr($vdate, 6, 2)." 00:00:00' and '".substr($vweekenddate, 0, 4)."-".substr($vweekenddate, 4, 2)."-".substr($vweekenddate, 6, 2)." 23:59:59' 
				 
				".$where."
				order by od.regdate ";
            //order by od.regdate like '%Y%m%d'
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
            /*
            $sql = "Select cmd.name, cu.id as userid,c.pname, a.*
                from ".TBL_COMMERCE_SALESTACK." a left outer join ".TBL_COMMON_USER." cu on a.ucode = cu.code, ".TBL_SHOP_PRODUCT." c , ".TBL_COMMON_MEMBER_DETAIL." cmd
                where a.pid = c.id and cu.code = cmd.code and vdate LIKE '".substr($vdate,0,6)."%' and step6 = 1
                order by a.vdate, vtime";
            */
            //od.regdate like '%Y%m%d' as vdate
            $sql = "Select AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, od.regdate, od.regdate like '%H' as vtime,  
				cu.id as userid, o.user_code, od.pcnt as vquantity , od.pname, od.pid,  od.psprice as sellprice
				from ".TBL_SHOP_ORDER." o 
				right join ".TBL_SHOP_ORDER_DETAIL." od on o.oid = od.oid
				left join ".TBL_COMMON_USER." cu on o.user_code = cu.code
				left join  ".TBL_COMMON_MEMBER_DETAIL." cmd on  cu.code = cmd.code
				where od.regdate between '".substr($vdate, 0, 4)."-".substr($vdate, 4, 2)."-01 00:00:00' and '".substr($vdate, 0, 4)."-".substr($vdate, 4, 2)."-31 23:59:59'   
				 
				".$where."
				order by od.regdate ";
            //order by od.regdate like '%Y%m%d'

        }else{
            if((!empty($_GET["member_div"]) && $_GET["member_div"]) == "nonmember"){
                $name_str = " o.bname ";
            }else{
                $name_str = " AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') ";
            }
            //od.regdate like '%Y%m%d' as vdate
            $sql = "Select ".$name_str." as name, od.regdate, od.regdate like '%H' as vtime,  
				cu.id as userid, o.user_code, od.pcnt as vquantity , od.pname, od.pid,  od.psprice as sellprice
				from ".TBL_SHOP_ORDER." o 
				right join ".TBL_SHOP_ORDER_DETAIL." od on o.oid = od.oid
				left join ".TBL_COMMON_USER." cu on o.user_code = cu.code
				left join  ".TBL_COMMON_MEMBER_DETAIL." cmd on  cu.code = cmd.code
				where od.regdate between '".substr($vdate, 0, 4)."-".substr($vdate, 4, 2)."-01 00:00:00' and '".substr($vdate, 0, 4)."-".substr($vdate, 4, 2)."-31 23:59:59'  
				 
				".$where."
				order by od.regdate 
				 ";
            //order by od.regdate like '%Y%m%d'
        }

        $dateString = getNameOfWeekday(0,$vdate,"monthname");
    }

    //echo nl2br($sql);
    //exit;
    $fordb->query($sql);
    //$datas = $fordb->fetchall();
    //print_r($datas);

    $total = $fordb->total;



    $mstring = $mstring.TitleBar("재구매고객",$dateString, false);
//	$mstring = $mstring."<table cellpadding=0 cellspacing=0 width=745 border=0 >\n";
//	$mstring = $mstring."<tr  align=center><td colspan=3 style='padding-bottom:10px;'>".etcrefererGraph($vdate,$SelectReport)."</td></tr>\n";
//	$mstring = $mstring."<tr  align=center><td colspan=3 ></td></tr>\n";
//	$mstring = $mstring."</table>";
    /*
        $mstring = $mstring."<table cellpadding=3 cellspacing=0 width=745  >\n";
        $mstring = $mstring."<tr height=25 align=center><td width=150 class=s_td width=30>항목</td><td class=m_td width=200>페이지 명</td><td class=e_td width=50>페이지뷰</td></tr>\n";
        $mstring = $mstring."<tr height=25 bgcolor=#ffffff>
            <td bgcolor=#efefef align=center id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">최다 요청</td>
            <td align=right id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">오후 2:00:00 ∼ 오후 3:00:00</td>
            <td bgcolor=#efefef  align=right id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">543</td>
            </tr>\n";
        $mstring = $mstring."<tr height=25 bgcolor=#ffffff>
            <td bgcolor=#efefef align=center id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">최소 요청</td>
            <td align=right id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">오후 2:00:00 ∼ 오후 3:00:00</td>
            <td bgcolor=#efefef  align=right id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">33</td>
            </tr>\n";
        $mstring = $mstring."</table><br>\nword-break:keep-all";
    */

    $mstring .="<table cellpadding=0 cellspacing=0 width=100%>
	<tr height=150>
		<td   >
			 <form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
			<input type='hidden' name='mode' value='search'>
			<input type='hidden' name='cid2' value='$cid2'>
			<input type='hidden' name='depth' value='$depth'>
			<input type='hidden' name='groupbytype' value='$groupbytype'>
			<input type='hidden' name='SubID' value='$SubID' />
			<input type='hidden' name='eprice' value='1000000' />
			<table class='box_shadow' style='width:1100px;' cellpadding='0' cellspacing='0' border='0'><!---mbox04-->
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
										<input type='text' name='sdate' class='textbox' value='".$startDate."' style='height:20px;width:70px;text-align:center;' id='start_datepicker'>
										<!--SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년
										<SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월
										<SELECT name=FromDD></SELECT> 일 -->
										</TD>
										<TD align=center> ~ </TD>
										<TD nowrap>
										<input type='text' name='edate' class='textbox' value='".$endDate."' style='height:20px;width:70px;text-align:center;' id='end_datepicker'>
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
							<tr height=27>
							  <td class='search_box_title'><b>회원가입일자</b></td>
							  <td class='search_box_item' colspan=3 >
								<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
									<col width=70>
									<col width=20>
									<col width=70>
									<col width=*>
									<tr>
										<TD nowrap>
										<input type='text' name='memreg_sdate' class='textbox' value='".$memreg_sdate."' style='height:20px;width:70px;text-align:center;' id='memreg_sdate'>
										</TD>
										<TD align=center> ~ </TD>
										<TD nowrap>
										<input type='text' name='memreg_edate' class='textbox' value='".$memreg_edate."' style='height:20px;width:70px;text-align:center;' id='memreg_edate'>
										</TD>
										<TD style='padding:0px 10px'>
											<a href=\"javascript:setSelectDate('$today','$today',2);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_today.gif'></a>
											<a href=\"javascript:setSelectDate('$vyesterday','$vyesterday',2);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_yesterday.gif'></a>
											<a href=\"javascript:setSelectDate('$voneweekago','$today',2);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_1week.gif'></a>
											<a href=\"javascript:setSelectDate('$v15ago','$today',2);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_15days.gif'></a>
											<a href=\"javascript:setSelectDate('$vonemonthago','$today',2);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_1month.gif'></a>
											<a href=\"javascript:setSelectDate('$v2monthago','$today',2);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_2months.gif'></a>
											<a href=\"javascript:setSelectDate('$v3monthago','$today',2);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_3months.gif'></a>
										</TD>
									</tr>
								</table>
							  </td>
							</tr>
							<!--
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
							-->
							<!--
							<tr  height=27>
							  <td class='search_box_title' width='13%' bgcolor='#efefef' align=center >연령대</td>
							  <td class='search_box_item' width='*' align=left style='padding-left:5px;' colspan=3>
							  
								  <input type='checkbox' name='age[]' id='age_' value='' ".CompareReturnValue("",$age,"checked")."><label for='age_'>전체</label>
								  <input type='checkbox' name='age[]' id='age_10' value='10'  ".CompareReturnValue("10",$age,"checked")."><label for='age_10'  style='width:200px;'>~10대</label>
								  <input type='checkbox' name='age[]' id='age_20' value='20'  ".CompareReturnValue("20",$age,"checked")."><label for='age_20' >20대</label>
								  <input type='checkbox' name='age[]' id='age_30' value='30'  ".CompareReturnValue("30",$age,"checked")."><label for='age_30' >30대</label>
								  <input type='checkbox' name='age[]' id='age_40' value='40'  ".CompareReturnValue("40",$age,"checked")."><label for='age_40' >40대</label>
								  <input type='checkbox' name='age[]' id='age_50' value='50'  ".CompareReturnValue("50",$age,"checked")."><label for='age_50'  >50대</label>
								  <input type='checkbox' name='age[]' id='age_60' value='60'  ".CompareReturnValue("60",$age,"checked")."><label for='age_60' >ETC</label>
								</select>
							  </td>
							 </tr>
							 -->
							<tr  height=27>
								<td class='search_box_title'><b>회원/비회원 여부</b></td>
								<td class='search_box_item' colspan='3'>
									<select name='member_div' style='font-size:12px;'>
										<option value='' ".ReturnStringAfterCompare($member_div, "", " selected").">전체</option>
										<option value='member' ".ReturnStringAfterCompare($member_div, "member", " selected").">회원</option>
										<option value='nonmember' ".ReturnStringAfterCompare($member_div, "nonmember", " selected").">비회원</option>
									</select>
								</td>
								<!--
								<td class='search_box_title' bgcolor='#efefef' align=center>성별검색 </td>
								  <td class='search_box_item' align=left style='padding-left:5px;'>
								  <input type=radio name='sex' value='' id='sex_all'  ".CompareReturnValue("0",$sex,"checked")." checked><label for='sex_all'>모두</label>
								  <input type=radio name='sex' value='M' id='sex_man'  ".CompareReturnValue("M",$sex,"checked")."><label for='sex_man'>남자</label>
								  <input type=radio name='sex' value='W' id='sex_women' ".CompareReturnValue("W",$sex,"checked")."><label for='sex_women'>여자</label>
								</td>
								-->
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
                            <!--
							<tr>
								<td class='search_box_title'><b>브랜드</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;' nowrap><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('/code_search.php?search_type=brand',600,380,'code_search')\"  style='cursor:pointer;'>
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
							</tr>
							-->
							";
    if($_SESSION['admininfo']['admin_level'] == 9 && false){
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
    }
    if($_SESSION['admininfo']['admin_id'] == "forbiz"){
        if($_SESSION['admininfo']['admin_level'] == 9 && false){
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
							<tr>
							    <!--
								<td class='search_box_title'><b>VAT</b></td>
                                <td class='search_box_item'  >
                                    <input type='radio' name='vat_type'  id='vat_y' value='Y' ".ReturnStringAfterCompare($vat_type, "Y", " checked")."><label for='vat_y'>포함</label>
                                    <input type='radio' name='vat_type' id='vat_n' value='N' ".ReturnStringAfterCompare($vat_type,"N"," checked")."><label for='vat_n'>제외</label>
                                </td>
                                -->        
								<!--td class='search_box_title'>
										<select name='search_type'  style=\"font-size:12px;height:20px;\">
											<option value='pname'".ReturnStringAfterCompare($search_type, "pname", " selected").">상품명</option>
											<option value='pid'".ReturnStringAfterCompare($search_type, "pid", " selected").">상품코드</option>
										</select>
								</td>
								<td class='search_box_item' style='padding-right:5px;margin-top:3px;'>
									<table cellpadding=0 cellspacing=0 >
										<tr >
											<td><INPUT id=search_texts  class='textbox1' value='".$search_text."' onclick='findNames();'  clickbool='false' style='height:16px;FONT-SIZE: 12px; WIDTH: 160px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
											<DIV id=popup style='DISPLAY: none; WIDTH: 160px; POSITION: absolute; HEIGHT: 150px; BACKGROUND-COLOR: #fffafa' >
												<table cellSpacing=0 cellPadding=0 border=0 width=100% height=100% bgColor=#efefef>
													<tr height=20>
														<td width=100%  style='padding:0 0 0 5'>
															<table width=100% cellpadding=0 cellspacing=0 border=0>
																<tr>
																	<td class='p11 ls1'>검색어 자동완성</td>
																	<td class='p11 ls1' onclick='focusOutBool=false;clearNames()' style='cursor:pointer;padding:0 10 0 0' align=right>닫기</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr height=100% >
														<td valign=top bgColor=#efefef style='padding:0 6 5 6' colspan=2>
															<table width=100% height=100% bgcolor=#ffffff>
																<tr>
																	<td valign=top >
																	<div style='POSITION: absolute; overflow-y:auto;HEIGHT: 120px;' id='search_data_area'>
																		<TABLE id=search_table style='table-layout:fixed;'  width=100% cellSpacing=0 cellPadding=1 bgColor=#ffffff border=0>
																		<TBODY id=search_table_body></TBODY>
																		</TABLE>
																	<div>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
												</DIV>
											</td>
											<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'></span></td>
										</tr>
									</table>
								</td--> 
								<td class='search_box_title'><b>집계기준</b></td>
                                <td class='search_box_item' colspan='3' >
                                    <input type='radio' name='status_disp'  id='oc' value='OC' ".ReturnStringAfterCompare($status_disp, "OC", " checked")."><label for='oc'>주문일 기준</label>
									<input type='radio' name='status_disp'  id='ic' value='IC' ".ReturnStringAfterCompare($status_disp, "IC", " checked")."><label for='ic'>결제완료 기준</label>
                                    <input type='radio' name='status_disp' id='di' value='DI' ".ReturnStringAfterCompare($status_disp,"DI"," checked")."><label for='di'>출고완료 기준</label>
                                </td> 
                            </tr>
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

    $mstring .= "
    <form name='list_frm' method='post' action='/admin/member/member_batch.act.php'  target='act' >
					<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
    <div style='overflow: scroll; '>
					<table cellpadding=3 cellspacing=0 width='100%' class='list_table_box'  >
								<col width='3%'>
								<col width='3%'>
								<col width='9%'>
								<col width='9%'>
								<col width='10%' >
								<col width=9% nowrap>
								<col width=9% nowrap>
								<col width=9% nowrap>
								<col width=8% nowrap>
								<col width=8% nowrap> 
								<col width=9% nowrap>
									";
    for($i = 0; $i <= $nLoop;$i++){
        $mstring .= "<col width=9% nowrap>";
    }
    $mstring .= "<tr height=40 align=center style='font-weight:bold'>
							<td class=s_td ><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
							<td class=m_td>순</td>
							<td class=m_td nowrap>회원그룹</td>
							<td class=m_td >회원명</td>
							<td class=m_td nowrap>회원아이디</td>
							<td class=m_td>날짜/시간</td>
							<td class=m_td >회원가입일</td>							
							<td class=m_td  nowrap>총방문수</td>
							<td class=m_td  nowrap>총주문금액(원)</td>
							<td class=m_td  nowrap>주문건수(건)</td>
							<td class=m_td nowrap>구매수량(개)</td> 
							<td class=e_td nowrap>매출액(원)</td>
							<td class=e_td nowrap>매출액<br>점유율(%) / 누적점유율(%)</td>";

    for($i = 0; $i <= $nLoop;$i++){
        $w = date("w",mktime(0,0,0,substr((int)$startDate,4,2),substr((int)$startDate,6,2),substr((int)$startDate,0,4))+60*60*24*$i);
        $LoopDate = date("y.m.d D",mktime(0,0,0,substr((int)$startDate,4,2),substr((int)$startDate,6,2),substr((int)$startDate,0,4))+60*60*24*$i);

        //$week_num = date("w",mktime(0,0,0,substr($startDate,4,2),substr($startDate,6,2),substr($startDate,0,4)));

        $mstring .= "<td class=m_td style='".($w == 0 ? "color:red;":"")."".($w == 6 ? "color:blue;":"")."'>".$LoopDate."  </td>";
    }

    $mstring .= "</tr>\n";

    if($fordb->total == 0){
        $mstring = $mstring."<tr height=150 bgcolor=#ffffff align=center><td colspan=24>결과값이 없습니다.</td></tr>\n";
    }else{
        $sale_sum =0;
        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);
            $sale_sum += $fordb->dt['sellprice']; // ptprice 와 같은값
        }
        //echo $i."<br>";
        //echo number_format($sale_sum);
        $order_rate = 0;
        $visit = 0;
        $order_total_price = 0;
        $order_cnt = 0;
        $vquantity = 0;
        $sellprice = 0;

        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);
            //print_r($fordb->dt);


            if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($_SESSION["admin_config"]["mall_data_root"]."/images/product", $fordb->dt['pid'], "s", $fordb->dt)) || $image_hosting_type=='ftp'){
                $img_str = PrintImage($_SESSION["admin_config"]["mall_data_root"]."/images/product", $fordb->dt['pid'], "s", $fordb->dt);
            }else{
                $img_str = "../../image/no_img.gif";
            }

            $order_rate += ($sale_sum > 0 ? $fordb->dt['sellprice']/$sale_sum*100:0);

            //".ReturnDateFormat($fordb->dt['vdate'])." : ".$fordb->dt['vtime']."     =>      ".substr($fordb->dt['regdate'], 0, 10)." : ".substr($fordb->dt['regdate'], 11, 2)."
            $mstring .= "<tr height=40 bgcolor=#ffffff  id='Report$i' onclick=\"if( $('#Report".$i."').css('background-color') != ''){ $('#Report".$i."').css('background-color','')}else{ $('#Report".$i."').css('background-color','red')}\" >
			<td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$fordb->dt['user_code']."'></td>
			<td class='list_box_td' nowrap>".($i+1)."</td>
			<td class='list_box_td '  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".$fordb->dt['gp_name']."</td>
			<td class='list_box_td point'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>							
			<a href=\"javascript:PoPWindow('../../member/member_view.php?code=".$fordb->dt['user_code']."',950,700,'member_view')\" >".$fordb->dt['name']."</a></td>
			
			<td class='list_box_td '  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".$fordb->dt['userid']."</td>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:5px;padding-right:5px' nowrap>".substr($fordb->dt['regdate'], 0, 10)." : ".substr($fordb->dt['regdate'], 11, 2)."</td>
			<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:5px;padding-right:5px' nowrap>".$fordb->dt['mem_regdate']." </td>
			
			
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:5px;padding-right:5px' nowrap>".$fordb->dt['visit']." </td>
			<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:5px;padding-right:5px' nowrap>".number_format($fordb->dt['order_total_price'])." </td>
			<td class='list_box_td point' style='' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">
			".$fordb->dt['order_cnt']."
			</td>
			<td class='list_box_td list_bg_gray'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt['vquantity']."</td> 
			<td class='list_box_td point'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['sellprice'],0)."</td>
			<td class='list_box_td'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($sale_sum > 0 ? $fordb->dt['sellprice']/$sale_sum*100:0),2)."% / ".number_format($order_rate,2)." %</td>";

            for($j = 0; $j <= $nLoop;$j++){
                $LoopDate = date("Ymd",mktime(0,0,0,substr($startDate,4,2),substr($startDate,6,2),substr($startDate,0,4))+60*60*24*$j);
                $w = date("w",mktime(0,0,0,substr($startDate,4,2),substr($startDate,6,2),substr($startDate,0,4))+60*60*24*$j);

                $mstring .= "<td class=number style='".($w == 0 ? "color:red;":"")."".($w == 6 ? "color:blue;":"")."'>".number_format($fordb->dt["sellprice_".$LoopDate])." </td>";
                $sum_name = "sellprice_".$LoopDate;
                $sum_name += $fordb->dt["sellprice_".$LoopDate];
            }


            $mstring .= "</tr>";

            $visit += $fordb->dt['visit'];
            $order_total_price += $fordb->dt['order_total_price'];

            $order_cnt += $fordb->dt['order_cnt'];
            $vquantity += $fordb->dt['vquantity'];
            $sellprice += $fordb->dt['sellprice'];


            /*
                    $mstring .= "<tr height=30>
                    <td style='padding-left:20px' class=s_td>결제완료</td><td align=center class=m_td width=125>".$fordb->dt['step6']."</td><td align=center class=m_td width=125>-</td><td align=center class=e_td width=125>-</td>
                    </tr>\n";
            */
            //	$exitcnt = $pageview01 + returnZeroValue($fordb->dt['visit_cnt']);


        }
    }

    $mstring .= "<tr height=25 align=center>
			<td width=50 class=s_td width=30 colspan=7>합계</td>
			<td class=e_td >".number_format($visit)."</td>
			<td class=e_td >".number_format($order_total_price)."</td>
			<td class=e_td >".number_format($order_cnt)."</td>
			<td class=e_td >".number_format($vquantity)."</td>
			<td class=e_td >".number_format($sellprice)."</td>
			<td class=e_td >".number_format($order_rate)."</td> ";
    for($j = 0; $j <= $nLoop;$j++){
        $LoopDate = date("Ymd",mktime(0,0,0,substr((int)$startDate,4,2),substr((int)$startDate,6,2),substr((int)$startDate,0,4))+60*60*24*$j);
        //			echo "sellprice_".$LoopDate."<br>";
        $sum_name = "sellprice_".$LoopDate;

        $mstring .= "<td class='e_td number'>".number_format((int)$sum_name)."</td>";

    }
    $mstring .= "
			</tr>\n";
    $mstring = $mstring."</table></div>\n";
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
                해당기간동안 구매한 회원의 정보입니다 <br>
                재구매고객을 대상으로 이벤트나 프로모션을 진행하실수 있습니다
                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );
//    $mstring .= SendCampaignBox($total);
    $mstring .= "</form>
";

    $mstring .= HelpBox("재구매고객", $help_text);



    return $mstring;
}

if ($mode == "iframe"){
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'reorder_buyer.php';

    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";


}else{


    $Script = "
<script Language='JavaScript' type='text/javascript'>
$(function() {
	$(\"#start_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
			$('#end_datepicker').val(dateText);
		}else{
			$('#end_datepicker').datepicker('setDate','+0d');
		}
	}

	});

	$(\"#end_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	$(\"#memreg_sdate\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		if($('#memreg_edate').val() != '' && $('#memreg_edate').val() <= dateText){
			$('#memreg_edate').val(dateText);
		}else{
			$('#memreg_edate').datepicker('setDate','+0d');
		}
	}

	});

	$(\"#memreg_edate\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	//$('#end_timepicker').timepicker();
});


function loadCategory(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	//var depth = sel.getAttribute('depth');
	var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
	//alert(depth);
	//dynamic.src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	//alert(1);
	// 크롬과 파폭에서 동작을 한번밖에 안하기에 iframe으로 처리 방식을 바꿈 2011-04-07 kbk
	window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

}



function setSelectDate(FromDate,ToDate,dType) {
	var frm = document.searchmember;
	if(dType == 2){
		$(\"#memreg_sdate\").val(FromDate);
		$(\"#memreg_edate\").val(ToDate);
	}else{
		$(\"#start_datepicker\").val(FromDate);
		$(\"#end_datepicker\").val(ToDate);
	}
}

</script>";

    $searchview = " ";
    $p = new forbizReportPage();
    $p->TopNaviSelect = "step2";
    $p->addScript = "".$Script;
    $p->forbizLeftMenu = commerce_munu('reorder_buyer.php',"",$searchview);
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->Navigation = "이커머스분석 > 고객종합분석(CRM) > 재구매고객";
    $p->title = "재구매고객";
    $p->PrintReportPage();
}
?>
