<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../class/sharedmemory.class");
include("./realtime_visitor_list.chart.php");

function ReportTable($vdate,$SelectReport=1){
    global $admininfo;
    $visit_cnt = 0;
    $mstring = "";
    $fordb = new forbizDatabase();
    if($SelectReport == ""){
        $SelectReport = 1;
    }
    if($vdate == ""){
        $vdate = date("Ymd", time());
        $vyesterday = date("Ymd", time()-84600);
        $voneweekago = date("Ymd", time()-84600*7);
    }else{
        /*
        if($SelectReport ==3){
            $vdate = $vdate."01";
        }
        */
        $vweekenddate = date("Ymd",mktime(0,0,0,(int)substr($vdate,4,2),(int)substr($vdate,6,2),(int)substr($vdate,0,4))+60*60*24*6);
        $vyesterday = date("Ymd",mktime(0,0,0,(int)substr($vdate,4,2),(int)substr($vdate,6,2),(int)substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Ymd",mktime(0,0,0,(int)substr($vdate,4,2),(int)substr($vdate,6,2),(int)substr($vdate,0,4))-60*60*24*7);
    }

    if($SelectReport == 1){
        $where = " and b.vdate = '".$vdate."' ";
    }else if($SelectReport == 2){
        $where = " and  b.vdate between '".$vdate."' and '".$vweekenddate."'	 ";
    }else if($SelectReport == 3){
        $where = " and b.vdate LIKE '".substr($vdate,0,6)."%' ";
    }

    //$sql = "Select p.pageid, vurl, page_ko_name from ".TBL_LOGSTORY_PAGEINFO." p, ".TBL_LOGSTORY_BYPAGE." b where p.pageid = b.pageid  order by ncnt desc"; //".$where."
    $sql = "Select p.pageid, vurl, page_ko_name from ".TBL_LOGSTORY_PAGEINFO." p  "; //".$where."
    //echo $sql;
    $fordb->query($sql);

    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);

        $pageinfos[$fordb->dt['pageid']] = array("vurl"=> $fordb->dt['vurl'], "page_ko_name"=> $fordb->dt['page_ko_name']);
    }
    //$pageinfos = $fordb->fetchall();
    //print_r($pageinfos);

    $sql = "Select cid, cname from logstory_referer_categoryinfo rci  "; //".$where."
    //echo $sql;
    $fordb->query($sql);

    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);
        $refererinfos[$fordb->dt['cid']] = array("depth"=> $fordb->dt['depth'], "cname"=> $fordb->dt['cname']);
    }
    /*
    if($SelectReport == 1){
        $sql = "Select * from  ".TBL_LOGSTORY_VISITORINFO." b where b.vdate = '$vdate'  order by visit_cnt desc LIMIT 0,50";
        $selected_date = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2){
        $sql = "Select sum(visit_cnt) as visit_cnt, ip_addr, user_agent, regdate from  ".TBL_LOGSTORY_VISITORINFO." b where b.vdate between '$vdate' and '$vweekenddate'  group by ip_addr order by visit_cnt desc LIMIT 0,50";
        $selected_date = "주간 : ". getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
    }else if($SelectReport == 3){
        $sql = "Select sum(visit_cnt) as visit_cnt, ip_addr, user_agent, regdate from ".TBL_LOGSTORY_VISITORINFO." b where b.vdate LIKE '".substr($vdate,0,6)."%'  group by ip_addr order by visit_cnt desc LIMIT 0,50";
        $selected_date = "월간 : ". getNameOfWeekday(0,$vdate,"monthname");
    }


    $fordb->query($sql);
    */


    if(false){
        $shmop = new Shared("realtime_data");
        //	$shmop->clear();
        $shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
        $shmop->SetFilePath();
        $realtime_data = $shmop->getObjectForKey("realtime_data");
    }else{
        try{
            if(class_exists("Memcache")){
                //$memcache = new Memcache;
               // $memcache->connect('192.168.1.9', 11211);
                //$realtime_data = $memcache->get('realtime_data');
            }else{
                $shmop = new Shared("realtime_data");
                //	$shmop->clear();
                $shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
                $shmop->SetFilePath();
                $realtime_data = $shmop->getObjectForKey("realtime_data");
            }
        }catch(Exception  $e){}

    }

    $i =0;
    if(is_array($realtime_data)){
        $referer_visit_infos = array();
        foreach ($realtime_data as $key => $row) {

            if((time()- strtotime($row['recent_visit_date'])) < 600){
                $page_view_infos[$i] = $row['page_id'];
                $ipaddr[$key]  = $row['ipaddr'];
                $page_id[$key] = $row['page_id'];
                $user_id[$key] = $row['user_id'];
                $before_visit_date[$key] = $row['before_visit_date'];
                $recent_visit_date[$key] = $row['recent_visit_date'];

                if($row['RFID']){
                    $referer_visit_infos[$i] = $row['RFID'];
                }

            }else{
                unset($realtime_data[$key]);
            }
            $i++;
        }
        if(is_array($page_view_infos)){
            $_page_view_infos = array_count_values($page_view_infos);
            arsort($_page_view_infos);
        }

        if(is_array($referer_visit_infos)){
            $_referer_visit_infos = array_count_values($referer_visit_infos);
            arsort($_referer_visit_infos);
        }
        //print_r($abcd);
    }

    //print_r(array_count_values ($realtime_data));

    $mstring = $mstring.TitleBar("실시간 방문자 리스트","",false, false);
    $mstring .= "<div style='width:100%'>\n";
    $page_value_str =  "<div style='width:80px;float:left'><div style='height:61px;background:url(../img/icon_page_total.gif) no-repeat;display:block;padding:30px 0 0 30px;margin:5px'> ".(is_array($page_view_infos) ? array_sum($_page_view_infos):"0")."</div><div style='width:80px;text-align:left;margin-left:25px;'>총계</div></div>";
    if(is_array($_page_view_infos)){
        foreach ($_page_view_infos as $key => $values) {
            $page_value_str .=  "<div style='width:80px;float:left'><div style='height:61px;background:url(../img/icon_page.gif) no-repeat;display:block;padding:30px 0 0 30px;margin:5px'> ".$values."</div><div style='width:80px;height:40px;text-align:center'>".strip_tags($pageinfos[$key]['page_ko_name'])."<br>".$key."</div></div>";
        }
    }

    $mstring .= $page_value_str;
    $mstring .= "</div><br><br><div style='clear:both;'></div>";

    $page_value_str =  "<!--div style='width:160px;float:left'><div style='height:61px;background:url(../img/icon_page_total.gif) no-repeat;display:block;padding:30px 0 0 30px;margin:5px'> ".(is_array($page_view_infos) ? array_sum($_referer_visit_infos):"0")."</div><div style='width:80px;text-align:left;margin-left:25px;'>총계</div></div-->";
    if(is_array($_referer_visit_infos)){
        foreach ($_referer_visit_infos as $key => $values) {
            $page_value_str .=  "<div style='width:160px;float:left'><div style='height:61px;background:url(../img/icon_page.gif) no-repeat center;display:block;padding:50px 0 0 0px;margin:5px;text-align:center;'> ".$values."</div><div style='width:160px;height:40px;text-align:center'>".str_replace("전체 > ","",strip_tags(getRefererCategoryPath($key,4)))."</div></div>";
        }
    }

    $mstring .= $page_value_str;
    $mstring .= "</div><br><br>";


    if(is_array($recent_visit_date)){
        array_multisort($recent_visit_date, SORT_DESC, $page_id, SORT_ASC, $realtime_data);
        if(class_exists("Memcache")){
            $result = $memcache->add("realtime_data", $realtime_data, false, 100);
            if(!$result){
                $result = $memcache->set("realtime_data", $realtime_data, false, 100);
            }
        }else{
            $shmop->setObjectForKey($realtime_data, "realtime_data") ;
        }
    }
    $mstring = $mstring."<table border=0 cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>\n";
    $mstring = $mstring."<tr height=25 align=center>
				<td width=50 class=s_td width=30>순</td>
				<td class=m_td width=200>페이지</td>
				<td class=m_td width=150 align=center>최종방문일시</td>
				<td class=m_td width=150>최종갱신</td>
				<td class=m_td width=50>유휴시간(초)</td>
				<td class=m_td width=150>SERVER ADDRESS</td>
				<td class=m_td width=150>IP ADDRESS</td>
				<td class=m_td width=150>APP TYPE</td>
				<td class=e_td width=150>사용자 ID</td>
				</tr>\n";
    $i = 0;

    if(count($realtime_data) > 0){
        foreach ($realtime_data as $key => $row) {


            $mstring = $mstring."<tr height=25 bgcolor=#ffffff  id='Report$i'>
			<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($i+1)."</td>
			<td class='point' align=left onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" >[".$row['page_id']."]<br>".($pageinfos[$row['page_id']]['page_ko_name'] ? $pageinfos[$row['page_id']]['page_ko_name']:$pageinfos[$row['page_id']]['vurl'])."</td>
			<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".$row['recent_visit_date']."</td>
			<td bgcolor=#ffffff align=center onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".$row['before_visit_date']."</td>
			<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".(time()-strtotime($row['recent_visit_date']))."</td>
			<td bgcolor=#ffffff align=center onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$row['server_addr']."</td>
			<td bgcolor=#ffffff align=center onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$row['ipaddr']."</td>
			<td bgcolor=#ffffff align=center onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$row['APP_TYPE']."</td>
			<td class='point' align=center onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\"><a href=\"javascript:PoPWindow('../../member/member_view.php?code=".$row['user_code']."',950,700,'member_view')\">".$row['user_id']."&nbsp;</a>".($row['RFID'] ? str_replace("전체 > ","",strip_tags(getRefererCategoryPath($row['RFID'],4))):"")."</td>
			</tr>\n";
            $i++;
        }
    }else{
        $mstring = $mstring."<tr height=50 bgcolor=#ffffff align=center><td colspan=8>결과값이 없습니다.</td></tr>\n";
    }
    $mstring = $mstring."</table>\n";


    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("실시간 방문자 리스트", $help_text);

    return $mstring;


}

$Script = "
<script language='javascript'>
function realtime_report_reload(){
	//alert(1);
	document.frames['act'].location.href = 'realtime_visitor_list.php?mode=iframe';

	setTimeout('realtime_report_reload()',5000);
}

window.onload =  realtime_report_reload;
</script>


";
if ($mode == "iframe"){
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script language='javascript'>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value;</Script>";




}else{
    $p = new forbizReportPage();

    $p->Navigation = "로그분석 > 방문자분석 > 실시간 방문자 리스트 ";
    $p->title = "실시간 방문자 리스트 ";
    $p->forbizLeftMenu = Stat_munu('realtime_visitor_list.php');
    $p->addScript = $Script;
//$p->OnloadFunction = "realtime_report_reload()";
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->PrintReportPage();

}
?>
