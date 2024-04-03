<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../report/etcreferer.chart.php");
include("../include/campaign.lib.php");



function ReportTable($vdate,$SelectReport=1){
    global $search_sdate, $search_edate, $report_type, $report_group_type;



    $pageview01 = 0;
    $fordb = new forbizDatabase();
    $fordb2 = new forbizDatabase();


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
        if($fordb->dbms_type == "oracle"){
            $sql = "Select  a.*, AES_DECRYPT(UNHEX(cmd.name),'".$fordb->ase_encrypt_key."') as name, cu.id as userid
				from ".TBL_LOGSTORY_MEMBERREG_STACK." a left outer join ".TBL_COMMON_USER." cu on a.ucode = cu.code , ".TBL_COMMON_MEMBER_DETAIL." cmd
				where  cu.code = cmd.code and vdate = '$vdate' and step6 = 1 order by a.vdate, vtime";
        }else{
            $sql = "Select a.*, AES_DECRYPT(UNHEX(cmd.name),'".$fordb->ase_encrypt_key."') as name, cu.id as userid
				from ".TBL_LOGSTORY_MEMBERREG_STACK." a left outer join ".TBL_COMMON_USER." cu on a.ucode = cu.code , ".TBL_COMMON_MEMBER_DETAIL." cmd
				where  cu.code = cmd.code and vdate = '$vdate' and step6 = 1 order by a.vdate, vtime";
        }
        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2 || $SelectReport == 4){
        if($SelectReport == "4"){
            $vdate = $search_sdate;
            $vweekenddate = $search_edate;
        }

        if($fordb->dbms_type == "oracle"){
            $sql = "Select a.*, AES_DECRYPT(UNHEX(cmd.name),'".$fordb->ase_encrypt_key."') as name, cu.id as userid
				from ".TBL_LOGSTORY_MEMBERREG_STACK." a left outer join ".TBL_COMMON_USER." cu on a.ucode = cu.code , ".TBL_COMMON_MEMBER_DETAIL." cmd
				where cu.code = cmd.code and vdate between '$vdate' and '$vweekenddate' and step6 = 1 order by a.vdate, vtime";
        }else{
            $sql = "Select a.*, AES_DECRYPT(UNHEX(cmd.name),'".$fordb->ase_encrypt_key."') as name, cu.id as userid
				from ".TBL_LOGSTORY_MEMBERREG_STACK." a left outer join ".TBL_COMMON_USER." cu on a.ucode = cu.code , ".TBL_COMMON_MEMBER_DETAIL." cmd
				where cu.code = cmd.code and vdate between '$vdate' and '$vweekenddate' and step6 = 1 order by a.vdate, vtime";
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
            $sql = "Select a.*, AES_DECRYPT(UNHEX(cmd.name),'".$fordb->ase_encrypt_key."') as name, cu.id as userid
				from ".TBL_LOGSTORY_MEMBERREG_STACK." a left outer join ".TBL_COMMON_USER." cu on a.ucode = cu.code , ".TBL_COMMON_MEMBER_DETAIL." cmd
				where cu.code = cmd.code and vdate between '".substr($selected_date,0,6)."01' and '".substr($selected_date,0,6)."31' and step6 = 1 order by a.vdate, vtime";
        }else{
            $sql = "Select a.*, AES_DECRYPT(UNHEX(cmd.name),'".$fordb->ase_encrypt_key."') as name, cu.id as userid
				from ".TBL_LOGSTORY_MEMBERREG_STACK." a left outer join ".TBL_COMMON_USER." cu on a.ucode = cu.code , ".TBL_COMMON_MEMBER_DETAIL." cmd
				where cu.code = cmd.code and vdate between '".substr($selected_date,0,6)."01' and '".substr($selected_date,0,6)."31' and step6 = 1 order by a.vdate, vtime";
        }

        $dateString = getNameOfWeekday(0,$vdate,"monthname");
    }

//	echo $sql;
    $fordb->query($sql);



    $mstring = $mstring.TitleBar("신규회원가입고객",$dateString);
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

    $mstring .= "<form name='list_frm' method='post' action='/admin/member/member_batch.act.php'  target='act' >
					<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
					<table cellpadding=0 cellspacing=0 width=100% class='list_table_box'>\n";
    $mstring = $mstring."<tr height=25 align=center style='font-weight:bold'>
						<td class=s_td width=4%><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
						<td class=s_td width=10%>순</td>
						<td class=m_td width=15%>시간</td>
						<td class=m_td width=35%>회원명</td><td class=e_td width=40% nowrap>회원아이디</td><!--td class=m_td width=50 nowrap>갯수</td><td class=e_td width=70 nowrap>단가</td--></tr>\n";

    if($fordb->total == 0){
        $mstring = $mstring."<tr height=150 bgcolor=#ffffff align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
    }else{

        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);

            $mstring .= "<tr height=30 bgcolor=#ffffff align=center id='Report$i'>
			<td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$fordb->dt['ucode']."'></td>
			<td bgcolor=#ffffff onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:5px;padding-right:5px' nowrap>".($i+1)."</td>
			<td bgcolor=#efefef onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:5px;padding-right:5px' nowrap>".ReturnDateFormat($fordb->dt['vdate'])." : ".$fordb->dt['vtime']."</td>
			<td bgcolor=#ffffff onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:20px'>".$fordb->dt['name']."</td>
			<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt['userid']."</td>
			<!--td bgcolor=#ffffff align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt['vquantity']."</td>
			<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['vprice'],0)."</td-->
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
                - 해당기간 동안 회원가입한 회원리스트 입니다<br>
                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );

    $mstring .= SendCampaignBox($total);
    $mstring .= "</form>";

    $mstring .= HelpBox("신규회원가입고객", $help_text);
    return $mstring;
}

if ($mode == "iframe"){
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'memberreglist.php';

    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";


}else{
    $p = new forbizReportPage();
    $p->TopNaviSelect = "step2";
    $p->forbizLeftMenu = commerce_munu('memberreglist.php');
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->Navigation = "이커머스분석 > 고객리스트 > 신규회원가입고객";
    $p->title = "신규회원가입고객";
    $p->PrintReportPage();
}
?>
