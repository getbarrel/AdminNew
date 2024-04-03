<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2017-07-27
 * Time: 오후 4:54
 */
include $_SERVER['DOCUMENT_ROOT']."/admin/class/layout.class";

if($report_type == 'A'){
    $menu_name = "동물보호활동 제보 목록";
    $report_title_text = "단체명";
    $report_div_array = array('A' => '전체', 'T'=> '단체', 'S' => '개인');
}else if($report_type == 'H'){
    $menu_name = "병원 & 약국 제보 목록";
    $report_title_text = "업체명";
    $report_div_array = array('A' => '전체', 'H'=> '병원', 'F' => '약국');
}

$db = new Database;
$Contents01 .= "
    
	<form name='searchmember' style='display:inline;'>
	<input type='hidden' name='info_type' value='$info_type'>
	<input type='hidden' name='mode' value='search'>
	<input type='hidden' name='mem_ix' value='$mem_ix'>
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
						<td class='search_box_title' >분류 </td>
						<td class='search_box_item' >
							<select name='report_div' id='report_div'  style=\"font-size:12px;\">";
                                if(is_array($report_div_array)){
                                    foreach($report_div_array as $key => $val){
                                        $Contents01 .= "
                                        <option value='".$key."' ".CompareReturnValue($key,$report_div,'selected').">".$val."</option>
                                        ";
                                    }
                                }
                            $Contents01 .= "
                            </select>
						</td>
					</tr>
					<tr height=27>
						
						<td class='search_box_title' >".$report_title_text." </td>
						<td class='search_box_item' >
							<input id=search_texts class='textbox' value='".$report_title."' autocomplete='off' name='report_title' title='단체명'>							
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

if($max > 0){
    $max = $max;
}else {
    $max = 15; //페이지당 갯수
}
if ($page == ''){
    $start = 0;
    $page  = 1;
}else{
    $start = ($page - 1) * $max;
}

$where = " where report_type = '".$report_type."' ";

if($mode == 'search'){
    if (!empty($report_div)){
        $where .= " and report_div = '".$report_div."' ";
    }
    if (!empty($report_title)){
        $where .= " and report_title LIKE '%".$report_title."%' ";
    }
    if (!empty($sdate) && !empty($edate)){
        $where .= " and regdate between '".$sdate." 00:00:00' and '".$edate." 23:59:59' ";
    }
}

$sql = "select count(re_ix) as count from bienview_report_info $where ";
$db->query($sql);
$db->fetch();
$total = $db->dt['count'];


$sql = "select * from bienview_report_info $where  order by regdate desc limit $start,$max";
$db->query($sql);
$report_array = $db->fetchall();


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
	<col width=10%>
	<col width=25%>
	<col width=25%>
	<col width=25%>
	<col width=*>
	<tr bgcolor=#efefef style='font-weight:bold' align=center height=28>		
		<td class='s_td'>번호</td>
		<td class='m_td'>분류</td>
		<td class='m_td'>제목</td>	
		<td class='m_td'>등록일</td>	
		<td class='e_td'>관리</td>
	</tr>";
if(is_array($report_array) && count($report_array) > 0){
   $i = 0;
   foreach($report_array as $report) {
       $no = $total - ($page - 1) * $max - $i;

       switch ($report['report_div']){
           case 'A':
               $report_div_text = "전체";
               break;
           case 'T':
               $report_div_text = "단체";
               break;
           case 'S':
               $report_div_text = "개인";
               break;
           case 'H':
               $report_div_text = "병원";
               break;
           case 'F':
               $report_div_text = "약국";
               break;
           default:
               $report_div_text = "";
               break;
       }

       $Contents01 .= "
        <tr height=28 align=center>        
            <td class='list_box_td' bgcolor='#ffffff'>".$no."</td>			
            <td class='list_box_td list_bg_gray'>".$report_div_text."</td>
            <td class='list_box_td' >".$report['report_title']."</td>
            <td class='list_box_td list_bg_gray'>".$report['regdate']." </td>
            <td class='list_box_td '><input type='button' value='상세보기' onclick=\"detail_view('".$report['re_ix']."','".$report_type."')\"></td>
        </tr>    
        ";
       $i++;
   }
}else{
    $Contents01 .= "
    <tr height=28 align=center>        
        <td class='list_box_td' colspan='5'>
            <span>등록된 제보 목록이 없습니다.</span>            
        </td>
    </tr>    
    ";
}
    $Contents01 .= "	
    </table>
	";
$Contents01 .= "
    <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
        <tr height=40>
         <td colspan='13' align='right'>&nbsp;".page_bar($total, $page, $max,$query_string."#list_top","")."&nbsp;</td>
        </tr>
    </table>";



$Contents = $Contents01;

$Script = "
<script>
    function detail_view(re_ix,report_type){
        window.open('./bienview_report_detail.php?re_ix='+re_ix+'&report_type='+report_type+'', 'bienview_report_detail', 'top=100, left=300, width=727px, height=312px, resizble=no, scrollbars=yes');
    }
</script>
";

$P = new LayOut();
$P->addScript = "".$Script;//<script language='javascript' src='../include/DateSelect.js'></script>\n
$P->OnloadFunction = "";
$P->strLeftMenu = bienview_menu();
$P->Navigation = "BIENVIEW관리 > 제보관리 > ".$menu_name." ";
$P->title = $menu_name;
$P->strContents = $Contents;
echo $P->PrintLayOut();