<?php
include("../class/reportpage.class");
include("../report/etcreferer.chart.php");

function ReportTable($vdate,$SelectReport=1){
    global $page, $nset, $admininfo;
    $pageview01 = 0;
    $fordb = new forbizDatabase();
    if($SelectReport == ""){
        $SelectReport = 1;
    }
    if($vdate == ""){
        $vdate = date("Ymd", time());
        $vyesterday = date("Ymd", time()-84600);
        $voneweekago = date("Ymd", time()-84600*7);
    }else{
        if($SelectReport ==3){
            //		$vdate = $vdate."01";
        }
        $vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
        $vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
    }

    $max = 15; //페이지당 갯수

    if ($page == '')
    {
        $start = 0;
        $page  = 1;
    }
    else
    {
        $start = ($page - 1) * $max;
    }

    if($SelectReport == 1){
        $where = " and b.vdate = '".$vdate."' ";
    }else if($SelectReport == 2){
        $where = " and  b.vdate between '".$vdate."' and '".$vweekenddate."'	 ";
    }else if($SelectReport == 3){
        $where = " and b.vdate LIKE '".substr($vdate,0,6)."%' ";
    }

    $sql = "Select count(*) as total from ".TBL_LOGSTORY_PAGEINFO." p, ".TBL_LOGSTORY_BYPAGE." b where p.pageid = b.pageid ".$where." ";
    //echo nl2br($sql);
    $fordb->query($sql);
    $fordb->fetch();
    $total = $fordb->dt[total];

    $sql = "Select p.pageid,vurl,page_ko_name from ".TBL_LOGSTORY_PAGEINFO." p, ".TBL_LOGSTORY_BYPAGE." b where p.pageid = b.pageid ".$where." order by ncnt desc limit $start, $max";


    $fordb->query($sql);

    $mstring = $mstring.TitleBar("페이지 정보 관리");
    /*	$mstring .= "<table cellpadding=0 cellspacing=0 width=615 border=0 >\n";
        $mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'>".etcrefererGraph($vdate,$SelectReport)."</td></tr>\n";
        $mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
        $mstring .= "</table>";
    */
    /*
        $mstring .= "<table cellpadding=3 cellspacing=0 width=615  >\n";
        $mstring .= "<tr height=25 align=center><td width=150 class=s_td width=30>항목</td><td class=m_td width=200>페이지 명</td><td class=e_td width=50>페이지뷰</td></tr>\n";
        $mstring .= "<tr height=25 bgcolor=#ffffff>
            <td bgcolor=#efefef align=center id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">최다 요청</td>
            <td align=right id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">오후 2:00:00 ∼ 오후 3:00:00</td>
            <td bgcolor=#efefef  align=right id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">543</td>
            </tr>\n";
        $mstring .= "<tr height=25 bgcolor=#ffffff>
            <td bgcolor=#efefef align=center id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">최소 요청</td>
            <td align=right id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">오후 2:00:00 ∼ 오후 3:00:00</td>
            <td bgcolor=#efefef  align=right id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">33</td>
            </tr>\n";
        $mstring .= "</table><br>\nword-break:keep-all";
    */

    $mstring .= "<form name='page_frm' action='page.act.php' method='POST' target='act' style='display:inline;'><input type=hidden name=act value='updates'>
								<table cellpadding=3 cellspacing=0 width=100%  STYLE='TABLE-LAYOUT:fixed' class='list_table_box'>\n";
    $mstring .= "<col width='50'><col width='*'><col width='300'>
		<tr height=25 align=center>
	<td align=center class='s_td'><input type='checkbox' name='page_id_all' onclick='fixAll(document.page_frm)'></td>
	<td align=center class='m_td' >페이지명</td>
	<td align=center  class='e_td' >페이지명(한글)</td>
	</tr>\n";
    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);
        $no = $total - ($page - 1) * $max - $i;

        $page_ko_name = str_replace("\"","&quot;",$fordb->dt[page_ko_name]);
        $page_ko_name = str_replace("'","&#39;",$page_ko_name);
        //$page_ko_name = str_replace("'","\'",$page_ko_name);

        $mstring .= "<tr height=40 bgcolor=#ffffff  id='Report$i'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\"><input type='checkbox' value='".$fordb->dt[pageid]."' name='page_id[]' id='page_id'></td>
		<td class='point' align=left onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" width=390 title='".urldecode($fordb->dt[vurl])."' style='padding:10px;' wrap>".(!$page_ko_name ? "<span style='color:silver'>페이지 명이 입력되지 않았습니다</span>":$fordb->dt[page_ko_name])."<br><a href='".$fordb->dt[vurl]."' target=_blank>".$fordb->dt[vurl]."</a></td>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" width=390 title='".urldecode($page_ko_name)."' wrap>
			<input type='text' class='textbox' value='".$page_ko_name."' name='page_ko_name[".$fordb->dt[pageid]."]' style='width:90%;padding:5px 0px 2px 3px;font-size:16px;font-weight:bold;' >
		</td>
		</tr>";

    }
    $mstring .= "</table>";

    $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  STYLE='TABLE-LAYOUT:fixed' >";
    $mstring .= "<tr height=50><td colspan=3 align='right' >&nbsp;".page_bar($total, $page, $max,$query_string,"")."&nbsp;</td></tr>";
    $mstring .= "<tr bgcolor=#ffffff height=30><td colspan=3 align=center><input type='image' src='../../images/".$admininfo[language]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>";
    $mstring .= "</form></table>\n";
    /*
        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  >\n";
        if ($pageview01 == 0){
            $mstring .= "<tr height=150 bgcolor=#ffffff align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
        }
        $mstring .= "<tr height=2 bgcolor=#ffffff align=center><td colspan=5 width=190></td></tr>\n";
        $mstring .= "<tr height=25 align=center><td width=50 class=s_td width=30 colspan=2>합계</td><td class=e_td width=190>".$pageview01."</td></tr>\n";
        $mstring .= "</table>\n";
    */
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                - 페이지 정보 관리란? 리포트중 페이지뷰와 관련된 리포트에서 관리자가 리포트를 읽기 쉽게 각페이지의 명칭을 입력하고 관리하는 메뉴입니다.<br>

                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("페이지 정보 관리", $help_text);
    return $mstring;
}

$Script = "
<script language='javascript'>
function clearAll(frm){
			for(i=0;i < frm.page_id.length;i++){
					frm.page_id[i].checked = false;
			}
	}
	function checkAll(frm){
	    for(i=0;i < frm.page_id.length;i++){
					frm.page_id[i].checked = true;
			}
	}
	function fixAll(frm){
		if (!frm.page_id_all.checked){
			clearAll(frm);
			frm.page_id_all.checked = false;

		}else{
			checkAll(frm);
			frm.page_id_all.checked = true;
		}
	}
</script>
";

if ($mode == "iframe"){
    echo "<html>";
    echo "<meta http-equiv='content-type' content='text/html; charset=euc-kr'>";
    echo "<body>";
    echo "<Script>parent.vdate;parent.ChangeCalenderView($SelectReport);alert(language_data['page.php']['A'][language]);</Script>";//'통계 관리자 모드에서는 달력을 사용 하실 수 없습니다.'
    echo "</body>";
    echo "</html>";
}else{
    $p = new forbizReportPage();

    $p->Navigation = "로그분석 > 관리자모드 > 페이지 정보 관리";
    $p->title = "페이지 정보 관리";
    $p->forbizLeftMenu = Stat_munu('page.php');
    $p->addScript = $Script;
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->PrintReportPage();
}
?>
