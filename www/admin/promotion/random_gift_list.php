<?php
include $_SERVER['DOCUMENT_ROOT'] . "/admin/class/layout.class";


if (empty($max)) {
    $max = 50; //페이지당 갯수
}

if ($page == '') {
    $start = 0;
    $page = 1;
} else {
    $start = ($page - 1) * $max;
}

$where = "where gc_ix <> ''  and status = 'Y' ";


if($gift_change_state != ""){
    $where .= " and gift_change_state = $gift_change_state ";
}

$gift_type = $_GET["gift_type"];
if($gift_type){
    $where .= " AND gift_type = '".$gift_type."' ";
}

if($search_text != ""){
    if($search_type != ""){
        $where .= " and $search_type LIKE '%".trim($search_text)."%' ";
    }else{
        $where .= " and (gift_certificate_name LIKE '%".trim($search_text)."%' or memo LIKE '%".trim($search_text)."%') ";
    }
}

if($reg_sdate != "" && $reg_edate != ""){
    $where .= " and regdate between '$reg_sdate 00:00:00' and '$reg_edate 23:59:59' ";
}

if($gift_start_date != "" && $gift_end_date != ""){
    $where .= " and  (gift_start_date between  '$gift_start_date' and '$gift_end_date' or gift_start_date between  '$gift_start_date' and '$gift_end_date' )";
}


$sql = "select count(*) as cnt from shop_gift_random_certificate $where ";
$db->query($sql);
$db->fetch();
$total = $db->dt['cnt'];

$sql = "select * from shop_gift_random_certificate $where order by gc_ix desc
			LIMIT $start, $max ";
$db->query($sql);
$gift_datas = $db->fetchall();

$Contents01 = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
    <tr >
        <td align='left' colspan=6 > " . GetTitleNavigation("랜덤 상품권 리스트", "마케팅관리 > 랜덤 상품권 리스트") . "</td>
    </tr>
    <tr>
        <td>
            <form name='searchmember' style='display:inline;'>
            <input type=hidden name=max value='" . $max . "'>
                <table width=100%  border=0 cellpadding=0 cellspacing=0>
                    <tr>
                        <td align='left' colspan=2 width='100%' valign=top >
                            <table cellpadding=0 cellspacing=1 width='100%' class='search_table_box'>
                                <tr>
                                    <th class='search_box_title' width='150'>조건검색 : </th>
                                    <td class='search_box_item' colspan=3>
                                        <table width=100% cellpadding=0 cellspacing=0>
                                        <col width='75px;'>
                                        <col width='*'>
                                            <tr>
                                                <td>
                                                    <select name=search_type>
                                                        <option value='' >통합 검색</option>
                                                        <option value='gift_certificate_name' " . CompareReturnValue("gift_certificate_name", $search_type, "selected") . ">상품권명</option>
                                                        <option value='memo' " . CompareReturnValue("memo", $search_type, "selected") . ">상품권 설명</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type=text name='search_text' class='textbox' value='" . $search_text . "' style='width:50%' >
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr height=27>
                                    <td class='search_box_title' align=center><label for='gift_use_date'><b>사용가능기간</b></label><input type='checkbox' name='gift_use_date' id='gift_use_date' value='1' onclick='ChangeGiftDate(document.searchmember);' " . ($gift_use_date == "1" ? "checked" : "") . "></td>
                                    <td class='search_box_item' colspan=3 style='padding-left:5px;'>
                                    " . search_date('gift_start_date', 'gift_end_date', $gift_start_date, $gift_end_date, 'N', 'D') . "									
                                    </td>
                                </tr>
                                <tr height=27>
                                    <td class='search_box_title' align=center><label for='regdate'><b>등록일자</b></label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' " . ($regdate == "1" ? "checked" : "") . "></td>
                                    <td class='search_box_item' colspan=3 style='padding-left:5px;'>
                                    " . search_date('reg_sdate', 'reg_edate', $reg_sdate, $reg_edate, 'N', 'D') . "									
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr >
                        <td colspan=3 align=center style='padding:10px 0px 0px 0px;'>
                            <input type='image' src='../image/bt_search.gif' border=0>
                        </td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
</table>";
$gift_lists = "";
if(is_array($gift_datas) && count($gift_datas) > 0){
    $i = 0;
    foreach($gift_datas as $key=>$val){
        $no = $total - ($page - 1) * $max - $i;


        if($val['is_use'] == 1) {
            $is_use = "사용";
        }else{
            $is_use = "미사용";
        }


        $sql = "SELECT count(*) as cnt FROM shop_gift_random_certificate_detail where gc_ix = '".$val[gc_ix]."' and gift_change_state ='1' ";
        $db->query($sql);
        $db->fetch();
        $use_cnt = $db->dt[cnt];

        $gift_lists .= "
        <tr height=28 align=center>
            <!--<td class='list_box_td '><input type='checkbox' name='ix[]' value='".$val[gc_ix]."'></td>-->
            <td >".$val[regdate]."</td>
            <td  style='line-height:120%;padding:10px;' nowrap>
                ".$val[gift_certificate_name] ."</b></br>
                <input type='button' value='상품권 상세' style='margin: 10px;' onclick=\"javascript:PoPWindow3('random_gift_detail.php?gc_ix=".$val[gc_ix]."',900,800,'random_gift_detail')\">
            </td>
            <td >".$val[gift_start_date]." ~ ".$val[gift_end_date]."</td>
            
            <td >".$is_use."</td>
            <td >".number_format($val[create_cnt])."</td>
            <td >".number_format($use_cnt)." (".number_format($use_cnt/$val[create_cnt]*100,2)."%)" ."</td>
            <td >
                <a href='random_gift_issue_history.php?gc_ix=".$val[gc_ix]."'>
                    <input type='button' value='발행내역' style='margin: 10px;'>
                </a>
            </td>
            <td class='list_box_td ' nowrap>
                <input type='button' value='수정' onclick=\"modifyCoupon('".$val[gc_ix]."')\" >
                <input type='button' value='삭제' style='margin: 10px;' onclick=\"DeleteGiftCertificateDetail(".$val[gc_ix].")\">
            </td>
        </tr>";

        $i++;
    }
}else{
    $gift_lists .= "<tr height=60><td colspan=11 align=center>상품권 내용이 없습니다.</td></tr>";
}

if($_SERVER["QUERY_STRING"] == "nset=$nset&page=$page"){
    $query_string = str_replace("nset=$nset&page=$page","",$_SERVER["QUERY_STRING"]) ;
}else{
    $query_string = str_replace("nset=$nset&page=$page&","","&".$_SERVER["QUERY_STRING"]) ;
}
$str_page_bar = page_bar($total, $page, $max, $query_string,"");

$Contents02 = "
    <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >    
        <tr height=10>
            
        </tr>
    </table>
	<form id='list_form' name='list_frm' method='POST' action='giftcertificate.act.php' enctype='multipart/form-data'>
	<input type='hidden' name='search_searialize_value' value='" . urlencode(serialize($_GET)) . "'>
	<input type='hidden' name='act' value='delete_selected'>
	<input type='hidden' name='update_type' value=''>
	<input type='hidden' name='act_detail' value=''>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
	  <tr bgcolor=#efefef align=center height=32>			
			<!--<td class=s_td width=3%><input type='checkbox' id='check_all' onclick='checkIx();'></td>-->
			<td class='m_td' width=10% >등록일자</td>
			<td class='m_td' width=*>상품권명 </td>
			<td class='m_td' width=15%>사용기간</td>
			<td class='m_td' width=5%>사용여부</td>
			<td class='m_td' width=5%>발행수</td>
			<td class='m_td' width=6%>사용수</br>(사용율)</td>
			<td class='m_td' width=5%>발행내역</td>
			<td class='e_td' width=7% >관리 </td>
		</tr>
		".$gift_lists."
	</table>
	</form>";

$Contents02 .= "<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >	 ";

$Contents02 .= "<tr height=40><td colspan=8 align=left style='text-align:left;'>" . $str_page_bar . "</td><td colspan=2 align=right>
                ";
if (checkMenuAuth(md5($_SERVER["PHP_SELF"]), "C")) {
    $Contents02 .= "
    <a href='random_gift_info.php'><img  src='../images/btm_reg.gif' border=0 align=absmiddle ></a></td></tr>";
} else {
    $Contents02 .= "
    <a href=\"" . $auth_write_msg . "\"><img  src='../images/btm_reg.gif' border=0 align=absmiddle ></a></td></tr>";
}


$Contents02 .= "</table>";


$Contents = "<table width='100%' border=0>";
$Contents = $Contents . "<tr><td>$Contents01<br></td></tr>";
$Contents = $Contents . "<tr><td>" . $Contents02 . "<br></td></tr>";
$Contents = $Contents . "<tr height=30><td></td></tr>";

$Contents = $Contents . "</table >";


$Script .="
<script>
    function DeleteGiftCertificateDetail(gc_ix){
        if(confirm('상품권 정보를 정말로 삭제하시겠습니까?')){
            window.frames['iframe_act'].location.href='random_gift.act.php?act=delete&gc_ix='+gc_ix;
        }
    }
    
    function modifyCoupon(gc_ix){
        location.href='random_gift_info.php?gc_ix='+gc_ix;
    }
</script>
";


$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n" . $Script;
$P->OnloadFunction = "init();";
$P->strLeftMenu = promotion_menu();
$P->Navigation = "프로모션(마케팅)전시 > 랜덤 상품권 리스트";
$P->title = "랜덤 상품권 리스트";
$P->strContents = $Contents;
echo $P->PrintLayOut();