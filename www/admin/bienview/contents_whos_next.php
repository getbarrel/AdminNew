<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2017-08-01
 * Time: 오후 6:33
 */

include $_SERVER['DOCUMENT_ROOT']."/admin/class/layout.class";
include $_SERVER['DOCUMENT_ROOT']."/admin/bienview/bienview_lib.php";

$db = new Database;
$Contents01 .= "
    
	<form name='search_frm' style='display:inline;'>
	<input type='hidden' name='mode' value='search'>
	<table border='0' cellpadding='0' cellspacing='0' width='100%'>	
	<tr>
		<td align='left' colspan=2  width='100%' valign=top style='padding-top:0px;'>
			<table border='0' cellpadding='0' cellspacing='0' width='100%'>
			<tr>
				<td class='box_05' valign=top style='padding:0px;'>
					<table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
				
					<col width=15%>
					<col width=*>
					<tr height=27>						
						<td class='search_box_title' >제목 </td>
						<td class='search_box_item' >
							<input class='textbox' value='".$title."' autocomplete='off' name=title  title='제목'>							
						</td>
					</tr>
					<tr height='27'>
						<td class='search_box_title' bgcolor='#efefef' align=center>
							<label for='search_check'><b>등록일</b></label>
						</td>
						<td class='search_box_item'>
							".search_date('sdate','edate',$sdate,$edate)."
						</td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=3 align=center style='padding:10px 0;'>
			<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
		</td>
	</tr>
	</table>
	</form>";

$max = 15; //페이지당 갯수

if ($page == ''){
    $start = 0;
    $page  = 1;
}else{
    $start = ($page - 1) * $max;
}


$where = "where div_depth = 'whos_next' ";
if($_GET['mode'] == 'search'){
    if($_GET['title']){
        $where .= " and title LIKE '%".$_GET['title']."%' ";
    }
    if($_GET['sdate'] && $_GET['edate']){
        $where .= " and regdate between '".$_GET['sdate']." 00:00:00' and '".$_GET['edate']." 23:59:59' ";
    }
}
$sql = "select count(*) cnt from bienview_contents_info $where";
$db->query($sql);
$db->fetch();
$total = $db->dt['cnt'];

$sql = "select * from bienview_contents_info $where order by co_ix desc limit $start , $max ";
$db->query($sql);
$contents_array = $db->fetchall();




$Contents01 .= "
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
	<tr height=30 >
		<td>
			<b>전체 : ".$total." 개</b> 
		</td>
		<td colspan=5 align=right>
		    목록 수 :
            <select name=max style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle>
                <option value='5' ".CompareReturnValue(5,$max).">5</option>
                <option value='10' ".CompareReturnValue(10,$max).">10</option>
                <option value='20' ".CompareReturnValue(20,$max).">20</option>
                <option value='40' ".CompareReturnValue(40,$max).">40</option>
                <option value='50' ".CompareReturnValue(50,$max).">50</option>
                <option value='100' ".CompareReturnValue(100,$max).">100</option>
            </select> 
		</td>
	</tr>
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>
	<col width=5%>
	<col width=20%>
	<col width=12%>
	<col width=12%>
	<col width=12%>
	<col width=12%>
	<col width=12%>
	<col width=*>
	<tr bgcolor=#efefef style='font-weight:bold' align=center height=28>		
		<td class='s_td'>번호</td>
		<td class='m_td'>제목</td>	
		<td class='m_td'>전시상태</td>
		<td class='m_td'>등록일</td>	
		<td class='m_td'>조회수</td>
		<td class='m_td'>관심등록수</td>	
		<td class='m_td'>질문수</td>	
		<td class='e_td'>관리</td>
	</tr>";
if(is_array($contents_array) && $total > 0){
    $i = 0;
    foreach($contents_array as $key => $val) {
        $no = $total - ($page - 1) * $max - $i;
        switch($val['disp']){
            case 'Y':
                $disp_text = "전시";
                break;
            case 'N':
                $disp_text ="미전시";
                break;
        }
        $question_cnt = 0;
        $sql = "select count(*)cnt from bienview_whos_next_question where co_ix = '".$val['co_ix']."' ";
        $db->query($sql);
        if($db->total) {
            $db->fetch();
            $question_cnt = $db->dt['cnt'];
        }
        $Contents01 .= "
	<tr height=28 align=center>        
        <td class='list_box_td' bgcolor='#ffffff'>".$no."</td>		
        <td class='list_box_td' >".$val['title']."</td>	
        <td class='list_box_td list_bg_gray'>".$disp_text."</td>
        <td class='list_box_td' >".$val['regdate']."</td>	
        <td class='list_box_td list_bg_gray'>".number_format($val['view_cnt'])."</td>
        <td class='list_box_td' >".SearchLikeCnt($val['co_ix'])."</td>
        <td class='list_box_td' ><a href='./question_whos_next.php?co_ix=".$val['co_ix']."' >".$question_cnt."</a></td>
        <td class='list_box_td list_bg_gray'> 
            <input type='button' value='수정' onclick=\"BienViewInput('whos_next', '$val[co_ix]');\"/>
            <input type='button' value='삭제' onclick=\"BienViewDelete('whos_next', '$val[co_ix]');\"/>
        </td>
    </tr>";
        $i++;
    }
}else{
$Contents01 .= "
    <tr height=28 align=center>        
        <td class='list_box_td' colspan='8'>
            <span>등록된 Who`s Nest 목록이 없습니다.</span>            
        </td>
    </tr> ";
}
$Contents01 .= "  
    </table>
    
    <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
        <tr height=40>
         <td colspan='9' align='right'><input type='button' onclick=\"BienViewInput('whos_next','');\" value='신규등록'></td>
        </tr>
    </table>
	";
$Contents01 .= "
    <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
        <tr height=40>
         <td colspan='13' align='right'>&nbsp;".page_bar($total, $page, $max,$query_string."#list_top","")."&nbsp;</td>
        </tr>
    </table>";



$Contents = $Contents01;


$P = new LayOut();
$P->addScript = "<script language='javascript' src='./bienview.js'></script>\n".$Script;//<script language='javascript' src='../include/DateSelect.js'></script>\n
$P->OnloadFunction = "";
$P->strLeftMenu = bienview_menu();
$P->Navigation = "BIENVIEW관리 > 콘텐츠 관리 > Who`s Next 목록 ";
$P->title = "Who`s Next 목록";
$P->strContents = $Contents;
echo $P->PrintLayOut();