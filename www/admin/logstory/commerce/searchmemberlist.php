<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../include/campaign.lib.php");
//include($_SERVER["DOCUMENT_ROOT"]."/forbiz/report/etcreferer.chart.php");
$script_time['start'] = time();
function ReportTable($vdate,$SelectReport=1){
    global $page;
    global $search_sdate, $search_edate;
    global $script_time;
    global $LargeImageSize;



    $pageview01 = 0;
    $script_time['connect_start'] = time();
    $fordb = new forbizDatabase();
    $script_time['connect_end'] = time();
    //$fordb2 = new forbizDatabase();

    /*
    $sql = "select * from ".TBL_COMMERCE_SALESTACK." s where step6 = '1' order by vdate asc  ";
    $fordb->query($sql);
    $order_goods = $fordb->fetchall();
    //echo count($order_goods);
    //exit;

    for($i=0;$i < count($order_goods);$i++){

        $sql = "update  ".TBL_COMMERCE_VIEWINGVIEW." set is_order = 1 where ucode = '".$order_goods[$i]['ucode']."' and pid = '".$order_goods[$i]['pid']."' and vdate <= '".$order_goods[$i]['vdate']."'  ";
        echo $sql."<br>";

        $fordb->query($sql);

    }
    echo "완료";
    exit;
    */

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
        //$sql = "Select b.name, b.id as uid,c.pname, a.* from ".TBL_COMMERCE_SALESTACK." a left outer join ".TBL_COMMON_MEMBER_DETAIL." b on a.ucode = b.code, ".TBL_SHOP_PRODUCT." c where  a.pid = c.id and vdate = '$vdate' and step6 = 1 order by a.vdate, vtime";
        $sql = 	"SELECT count(*) as total
				FROM ".TBL_COMMERCE_VIEWINGVIEW." v, ".TBL_SHOP_PRODUCT." p, ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_COMMON_USER." cu
				where v.pid = p.id and v.is_order = 0
				and ".($fordb->dbms_type == "oracle" ? "v.ucode is not null " : "v.ucode != '' " )." 
				and v.vdate = '$vdate' 				
				and v.ucode = cmd.code 
				and cu.code = cmd.code
				";
        $script_time['count_start'] = time();
        $fordb->query($sql);
        $script_time['count_end'] = time();
        $fordb->fetch();
        $total = $fordb->dt['total'];

        if($fordb->dbms_type == "oracle"){//uid 안됨
            $sql = 	"SELECT distinct v.ucode, v.pid, p.pname, cmd.name , cu.id as userid, p.coprice, p.sellprice, p.stock, v.nview_cnt, v.vdate
				FROM ".TBL_COMMERCE_VIEWINGVIEW." v,  ".TBL_SHOP_PRODUCT." p, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_COMMON_USER." cu
				where v.pid = p.id and v.ucode is not null and v.vdate = '$vdate' 
				and s.vdate = '$vdate' and v.vdate =s.vdate and v.ucode != s.ucode and v.ucode = cmd.code and cu.code = cmd.code
				order by v.vdate desc
				limit $start, $max ";
        }else{
            $sql = 	"SELECT distinct v.ucode, v.pid, p.pname, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name , cu.id as userid, p.coprice, p.sellprice, p.stock, v.nview_cnt, v.vdate
				FROM ".TBL_COMMERCE_VIEWINGVIEW." v, ".TBL_SHOP_PRODUCT." p, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_COMMON_USER." cu
				where v.pid = p.id and v.ucode != '' and v.is_order = 0
				and v.vdate = '$vdate' 
				and v.ucode = cmd.code 
				and cu.code = cmd.code
				order by v.vdate desc
				limit $start, $max ";
        }

        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2 || $SelectReport == "4"){
        if($SelectReport == "4"){
            $vdate = $search_sdate;
            $vweekenddate = $search_edate;
        }

        $sql = 	"SELECT count(*) as total
				FROM ".TBL_COMMERCE_VIEWINGVIEW." v, ".TBL_SHOP_PRODUCT." p, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_COMMON_USER." cu
				where v.pid = p.id and ".($fordb->dbms_type == "oracle" ? "v.ucode is not null " : "v.ucode != '' " )." 
				and v.vdate between '$vdate' and '$vweekenddate'   and v.is_order = 0 
				and v.ucode = cmd.code 
				and cu.code = cmd.code
				";
        $script_time['count_start'] = time();
        $fordb->query($sql);
        $script_time['count_end'] = time();
        $fordb->fetch();
        $total = $fordb->dt['total'];

        if($fordb->dbms_type == "oracle"){
            $sql = 	"SELECT distinct v.ucode, v.pid, p.pname, cmd.name , cu.id as userid, p.coprice, p.sellprice, p.stock, v.nview_cnt , v.vdate
				FROM ".TBL_COMMERCE_VIEWINGVIEW." v, ".TBL_SHOP_PRODUCT." p, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_COMMON_USER." cu
				where v.pid = p.id and v.ucode is not null 
				and v.vdate between '$vdate' and '$vweekenddate'  and v.is_order = 0 
				and v.ucode = cmd.code 
				and cu.code = cmd.code
				order by v.vdate desc
				limit $start, $max";
        }else{
            $sql = 	"SELECT distinct v.ucode, v.pid, p.pname, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name , cu.id as userid, p.coprice, p.sellprice, p.stock,  v.nview_cnt , v.vdate
				FROM ".TBL_COMMERCE_VIEWINGVIEW." v,  ".TBL_SHOP_PRODUCT." p, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_COMMON_USER." cu
				where v.pid = p.id 
				and v.ucode != '' and v.vdate between '$vdate' and '$vweekenddate' and v.is_order = 0 
				and v.ucode = cmd.code 
				and cu.code = cmd.code
				order by v.vdate desc
				limit $start, $max";
        }


        if($SelectReport == 2){
            $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
        }else if($SelectReport == 4){
            $s_week_num = date("w",mktime(0,0,0,substr($search_sdate,4,2),substr($search_sdate,6,2),substr($search_sdate,0,4)));
            $e_week_num = date("w",mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4)));
            $dateString = "기간별 : ".getNameOfWeekday($s_week_num,$search_sdate,"priodname")."~".getNameOfWeekday($e_week_num,$search_edate,"priodname");
        }
    }else if($SelectReport == 3){

        $sql = 	"SELECT count(*) as total
				FROM ".TBL_COMMERCE_VIEWINGVIEW." v,".TBL_SHOP_PRODUCT." p, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_COMMON_USER." cu
				where v.pid = p.id and ".($fordb->dbms_type == "oracle" ? "v.ucode is not null " : "v.ucode != '' " )." 
				and v.is_order = 0
				and v.vdate between '".substr($selected_date,0,6)."01' and '".substr($selected_date,0,6)."31' 
				and v.ucode = cmd.code 
				and cu.code = cmd.code
				";
        $script_time['count_start'] = time();
        $fordb->query($sql);
        $script_time['count_end'] = time();
        $fordb->fetch();
        $total = $fordb->dt['total'];

        //$sql = "Select b.name, b.id as userid,c.pname, a.* from ".TBL_COMMERCE_SALESTACK." a left outer join ".TBL_COMMON_MEMBER_DETAIL." b on a.ucode = b.code, ".TBL_SHOP_PRODUCT." c where a.pid = c.id and vdate LIKE '".substr($vdate,0,6)."%' and step6 = 1 order by a.vdate, vtime";
        if($fordb->dbms_type == "oracle"){
            $sql = 	"SELECT distinct v.ucode, v.pid, p.pname, cmd.name , cu.id as userid, p.coprice, p.sellprice, p.stock, v.nview_cnt , v.vdate
				FROM ".TBL_COMMERCE_VIEWINGVIEW." v, ".TBL_SHOP_PRODUCT." p, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_COMMON_USER." cu
				where v.pid = p.id and v.ucode is not null and v.is_order = 0
				and v.vdate LIKE '".substr($vdate,0,6)."%'
				and v.ucode = cmd.code and cu.code = cmd.code
				order by v.vdate desc
				limit $start, $max";
        }else{
            $sql = 	"SELECT distinct v.ucode, v.pid, p.pname, AES_DECRYPT(UNHEX(cmd.name),'".$fordb->ase_encrypt_key."') as name , cu.id as userid, p.coprice, p.sellprice, p.stock, v.nview_cnt , v.vdate
				FROM ".TBL_COMMERCE_VIEWINGVIEW." v, ".TBL_SHOP_PRODUCT." p, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_COMMON_USER." cu
				where v.pid = p.id and v.ucode != '' and v.is_order = 0
				and v.vdate between '".substr($selected_date,0,6)."01' and '".substr($selected_date,0,6)."31' 
				and v.ucode = cmd.code and cu.code = cmd.code
				order by v.vdate desc
				limit $start, $max";
        }

        $dateString = getNameOfWeekday(0,$vdate,"monthname");
    }

    //echo nl2br($sql);
    $script_time['report_query_start'] = time();
    $sql = "select data.*, (select IFNULL(sum(vquantity),0)  from commerce_salestack where ucode = data.ucode and pid = data.pid and step6 = 0) as cart_cnt from ($sql) data ";
    $fordb->query($sql);
    $script_time['report_query_end'] = time();
    //$script_time['report_query_total_start'] = time();
    //$total = $fordb->total;
    //$script_time['report_query_total_end'] = time();


    $mstring = $mstring.TitleBar("조회이탈고객",$dateString);


    $mstring .= "<form name='list_frm' method='post' action='/admin/member/member_batch.act.php'  target='act' >
					<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
					<table cellpadding=0 cellspacing=0 width=100% class='list_table_box'  >
						<col width='3%'>
						<col width='3%'>
						<col width='7%' >
						<col width=7% nowrap>
						<col width=7% nowrap>
						<col width='*' nowrap>
						<col width=7% nowrap>
						<col width='7%' >
						<col width='7%' >
						<col width='7%' >
						<col width='7%' >
						<col width='7%' >
						";
    $mstring .= "<tr height=35 align=center style='font-weight:bold'>
						<td class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
						<td class=m_td >번호</td>
						<td class=m_td >날짜</td>
						<td class=m_td >회원명</td>
						<td class=m_td nowrap>회원아이디</td>
						<td class=m_td  nowrap>상품명</td>
						<td class=m_td  nowrap>조회수</td>
						<td class='m_td '  nowrap>장바구니<br>담은수</td>
						<td class=m_td nowrap>원가</td>
						<td class=m_td nowrap>판매가</td>
						<td class=m_td nowrap>마진율(%)</td>
						<td class=e_td nowrap>재고</td>
						</tr>\n";

    if($fordb->total == 0){
        $mstring .= "<tr height=150 bgcolor=#ffffff align=center><td colspan=11>결과값이 없습니다.</td></tr>\n";
    }else{

        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);

            if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($_SESSION["admin_config"]["mall_data_root"]."/images/product", $fordb->dt['pid'], "s", $fordb->dt)) || $image_hosting_type=='ftp'){
                $img_str = PrintImage($_SESSION["admin_config"]["mall_data_root"]."/images/product", $fordb->dt['pid'], "s", $fordb->dt);
            }else{
                $img_str = "../../image/no_img.gif";
            }

            $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
			<td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$fordb->dt['ucode']."'></td>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:5px;padding-right:5px' align=center>".($i+1)." </td>
			<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:5px;padding-right:5px' nowrap>".ReturnDateFormat($fordb->dt['vdate'])." </td>
			<td class='list_box_td point'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:20px'>".$fordb->dt['name']."</td>
			<td class='list_box_td'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt['userid']."</td>
			<td class='list_box_td list_bg_gray'  style='text-align:left;padding:5px;' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">
			<a href='../../goods_input.php?id=".$fordb->dt['id']."' class='screenshot'  rel='".PrintImage($_SESSION["admin_config"]["mall_data_root"]."/images/product", $fordb->dt['pid'], $LargeImageSize, $fordb->dt)."'  ><img src='".$img_str."' align=absmiddle width=30 height=30 style='float:left;margin-right:10px;border:1px solid silver'></a>
			<div style='padding-top:10px;'>".$fordb->dt['pname']."</div>
			</td>
			<td class='list_box_td'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt['nview_cnt']."</td>
			<td class='list_box_td point'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt['cart_cnt']."</td>
			<td class='list_box_td list_bg_gray'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['coprice'],0)."</td>
			<td class='list_box_td'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['sellprice'],0)."</td>
			<td class='list_box_td list_bg_gray'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb->dt['sellprice']-$fordb->dt['coprice'])/$fordb->dt['sellprice']*100,0)."%</td>
			<td class='list_box_td'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['stock'],0)."</td>
			</tr>";

            /*
                    $mstring .= "<tr height=30>
                    <td style='padding-left:20px' class=s_td>결제완료</td><td align=center class=m_td width=125>".$fordb->dt['step6']."</td><td align=center class=m_td width=125>-</td><td align=center class=e_td width=125>-</td>
                    </tr>\n";
            */
            //	$exitcnt = $pageview01 + returnZeroValue($fordb->dt['visit_cnt']);


        }
    }
    $mstring .= "</table>";

    $query_string =$_SERVER["QUERY_STRING"];
    //echo $query_string;
    //$query_string = str_replace("max=".$_GET["max"],"",$query_string);
    //$query_string = str_replace("page=".$_GET["page"],"",$query_string);


    $query_string = str_replace("nset=".$_GET["nset"]."&page=".$_GET["page"]."&","",$query_string);
    $query_string = "&".$query_string;

    $mstring .= "<table cellpadding=0 cellspacing=0 width=100%  >
						<tr height=50><td colspan=6 align='center' >&nbsp;".page_bar($total, $page, $max,$query_string,"")."&nbsp;</td></tr>";
    $mstring .= "</table>\n";
//	$mstring .= "<table cellpadding=3 cellspacing=0 width=745  >\n";
//	if ($pageview01 == 0){
//		$mstring .= "<tr height=50 bgcolor=#ffffff align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
//	}
//	$mstring .= "<tr height=2 bgcolor=#ffffff align=center><td colspan=5 width=190></td></tr>\n";
//	$mstring .= "<tr height=25 align=center><td width=50 class=s_td width=30 colspan=2>합계</td><td class=e_td width=190>".$pageview01."</td></tr>\n";
//	$mstring .= "</table>\n";
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                조회이탈고객이란? 상품을 둘러보고 장바구니에도 담지 않은 상태에서 이탈한 회원의 정보입니다 이는 구매이탈보다는 구매력이 약한 잠재고객이지만 각 회원의 관심상품을 알고 프로모션을 진행하실수 있습니다
                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );
    $mstring .= SendCampaignBox($total);
    $mstring .= "</form>";
    $mstring .= HelpBox("조회이탈고객", $help_text);
    return $mstring;
}

if ($mode == "iframe"){
//	echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
//	echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'searchmemberlist.php';

    echo "<html>";
    echo "<meta http-equiv='content-type' content='text/html; charset=euc-kr'>";
    echo "<body>";
    echo "<div id='report_view' >".ReportTable($vdate,$SelectReport)."</div>";
    echo "<div id='calendar_view' >".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</div>";
    echo "</body>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.getElementById('report_view').innerHTML</Script>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.getElementById('calendar_view').innerHTML;parent.vdate;parent.ChangeCalenderView($SelectReport);</Script>";
    echo "</html>";

//	echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
//	echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";


}else{
    $p = new forbizReportPage();
    $p->TopNaviSelect = "step2";
    $p->forbizLeftMenu = commerce_munu('searchmemberlist.php');
    $script_time['report_start'] = time();
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $script_time['report_end'] = time();
    $p->Navigation = "이커머스분석 > 고객리스트 > 조회이탈고객";
    $p->title = "조회이탈고객";
    $p->PrintReportPage();
    $script_time['end'] = time();
}
?>
