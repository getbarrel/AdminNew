<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../report/etcreferer.chart.php");
include("../include/commerce.lib.php");

function ReportTable($vdate,$SelectReport=1){
    global $LargeImageSize;
    global $search_sdate, $search_edate;

    $pageview01 = 0;
    $fordb = new forbizDatabase();
    $fordb2 = new forbizDatabase();


    if($SelectReport == ""){
        $SelectReport = 1;
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

    if($SelectReport == 1){
        $sql = "Select a.cid, a.pid, c.id, c.pname, c.brand_name,  a.vprice, sum(a.vquantity) as vquantity 
				from ".TBL_COMMERCE_SALESTACK." a, ".TBL_SHOP_PRODUCT." c
				where a.pid = c.id and vdate = '$vdate' and step1 = 1 and step6 = 1 
				group by a.cid, a.pid,  c.id, c.pname, c.brand_name,  a.vprice
				order by vquantity desc";



        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2 || $SelectReport == "4"){
        if($SelectReport == "4"){
            $vdate = $search_sdate;
            $vweekenddate = $search_edate;
        }
        $sql = "Select a.cid, a.pid, c.id, c.pname, c.brand_name,  a.vprice, sum(a.vquantity) as vquantity 
				from ".TBL_COMMERCE_SALESTACK." a, ".TBL_SHOP_PRODUCT." c
				where a.pid = c.id and vdate between '$vdate' and '$vweekenddate' and step1 = 1 and step6 = 1 
				group by a.cid, a.pid, c.id, c.pname, c.brand_name,  a.vprice
				order by vquantity desc";

        if($SelectReport == 2){
            $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
        }else if($SelectReport == 4){
            $s_week_num = date("w",mktime(0,0,0,substr($search_sdate,4,2),substr($search_sdate,6,2),substr($search_sdate,0,4)));
            $e_week_num = date("w",mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4)));
            $dateString = "기간별 : ".getNameOfWeekday($s_week_num,$search_sdate,"priodname")."~".getNameOfWeekday($e_week_num,$search_edate,"priodname");
        }
    }else if($SelectReport == 3){
        $sql = "Select a.cid, a.pid, c.id, c.pname ,  a.vprice, sum(a.vquantity) as vquantity
				from ".TBL_COMMERCE_SALESTACK." a, ".TBL_SHOP_PRODUCT." c
				where a.pid = c.id and vdate LIKE '".substr($vdate,0,6)."%' and step1 = 1 and step6 = 1 
				group by a.cid, a.pid, c.pname ,  a.vprice
				order by vquantity desc";

        $dateString = getNameOfWeekday(0,$vdate,"monthname");
    }

    //echo nl2br($sql);
    $fordb->query($sql);


    $mstring = "";
    $mstring = $mstring.TitleBar("최다구매상품",$dateString);


    $mstring = $mstring."<table cellpadding=0 cellspacing=0 width=100%  style='table-layout:fixed'  class='list_table_box' >
									<col width='5%'>
									<col width=* >
									<col width=15% nowrap>
									<col width=15% nowrap>
									<col width=15% nowrap>";
    $mstring = $mstring."<tr height=30 align=center style='font-weight:bold'><td class=s_td >순</td><td class=m_td nowrap>카테고리/상품명</td><td class=m_td  nowrap>갯수</td><td class=m_td  nowrap>단가</td><td class=e_td >매출</td></tr>\n";
    $vquantity = 0;
    $vprice  = 0;
    $vsale = 0;
    if($fordb->total == 0){
        $mstring = $mstring."<tr height=150 bgcolor=#ffffff align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
    }else{

        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);

            if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($_SESSION["admin_config"]["mall_data_root"]."/images/product", $fordb->dt['id'], "s", $fordb->dt)) || $image_hosting_type=='ftp' || true){
                $img_str = PrintImage($_SESSION["admin_config"]["mall_data_root"]."/images/product", $fordb->dt['id'], "s", $fordb->dt);
            }else{
                $img_str = "../../image/no_img.gif";
            }

            $mstring .= "<tr height=40 bgcolor=#ffffff  id='Report$i'>
			<td class='list_box_td list_bg_gray' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=center nowrap>".($i+1)."</td>
			<td class='list_box_td point' style='text-align:left;padding:10px;line-height:140%;' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>
			
			<a href='../../goods_input.php?id=".$fordb->dt['id']."' class='screenshot'  rel='".PrintImage($_SESSION["admin_config"]["mall_data_root"]."/images/product", $fordb->dt['id'], $LargeImageSize, $fordb->dt)."'  ><img src='".$img_str."' align=absmiddle width=50 height=50 style='float:left;margin-right:10px;border:1px solid silver;'></a>
			<div style='padding-top:10px;line-height:140%;'>".($fordb->dt['cid'] ? strip_tags(getCategoryPath($fordb->dt['cid'],4))."<br>":"")." ".($fordb->dt['brand_name'] != "" ? "[".$fordb->dt['brand_name']."]":"")." ".$fordb->dt['pname']."</div>
			</td>
			<td class='list_box_td list_bg_gray' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt['vquantity']."</td>
			<td class='list_box_td ' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['vprice'])."</td>
			<td class='list_box_td list_bg_gray' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['vquantity']*$fordb->dt['vprice'],0)."</td>
			</tr>";

            /*
            $mstring .= "<tr height=30>
            <td style='padding-left:20px' class=s_td>결제완료</td><td align=center class=m_td width=125>".$fordb->dt['step6']."</td><td align=center class=m_td width=125>-</td><td align=center class=e_td width=125>-</td>
            </tr>\n";
    */
            $vquantity = $vquantity + returnZeroValue($fordb->dt['vquantity']);
            $vprice = $vprice + $fordb->dt['vprice'];
            $vsale = $vsale + ($fordb->dt['vprice'] * $fordb->dt['vquantity']);
        }
    }
    $mstring = $mstring."</table>\n";
    $mstring = $mstring."<table cellpadding=0 cellspacing=0 width=100%  style='table-layout:fixed;margin-top:5px;'  class='list_table_box'>
									<col width='5%'>
									<col width=* >
									<col width=15% nowrap>
									<col width=15% nowrap>
									<col width=15% nowrap>";


//	$mstring = $mstring."<tr height=2 bgcolor=#ffffff align=center><td colspan=5></td></tr>\n";
    $mstring = $mstring."<tr height=30 align=center><td width=50 class=s_td width=30 colspan=2>합계</td><td class=m_td >&nbsp;".$vquantity."</td><td class=m_td >".number_format($vprice)."</td><td class=e_td >".number_format($vsale,0)."</td></tr>\n";
    $mstring = $mstring."</table><br><br><br>\n";
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                - 상품구매 수량 으로본   상품의 인기도입니다 <br>
                - 해당상품에 대한 단가 및 해당기간 동안의 매출액을 알수 있습니다 <br><br>
                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("최다구매상품", $help_text);
    return $mstring;
}

if ($mode == "iframe"){
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'maxbuybyproduct.php';

    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";


}else{
    $p = new forbizReportPage();
    $p->TopNaviSelect = "step2";
    $p->forbizLeftMenu = commerce_munu('maxbuybyproduct.php');
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->Navigation = "이커머스분석 > 상품분석 > 최다구매상품";
    $p->title = "최다구매상품";
    $p->PrintReportPage();
}



?>
