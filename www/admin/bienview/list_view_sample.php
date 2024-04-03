<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2017-07-27
 * Time: 오후 4:54
 */
include $_SERVER['DOCUMENT_ROOT']."/admin/class/layout.class";

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
							<select name='report_div' id='report_div'  style=\"font-size:12px;\">
                                <option value='A' ".CompareReturnValue("div_type",$report_div).">전체</option>
                                <option value='T' ".CompareReturnValue("div_type",$report_div).">단체</option>
                                <option value='S' ".CompareReturnValue("div_type",$div_type).">개인</option>
                            </select>
						</td>
					</tr>
					<tr height=27>
						
						<td class='search_box_title' >단체명 </td>
						<td class='search_box_item' >
							<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='width: 150px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='단체명'>							
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

$total = 0;



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
	<col width=25%>
	<col width=25%>
	<col width=25%>
	<col width=25%>
	<tr bgcolor=#efefef style='font-weight:bold' align=center height=28>		
		<td class='s_td'></td>
		<td class='m_td'></td>
		<td class='m_td'></td>	
		<td class='e_td'></td>
	</tr>
	<tr height=28 align=center>        
        <td class='list_box_td' bgcolor='#ffffff'></td>			
        <td class='list_box_td list_bg_gray'></td>
        <td class='list_box_td' ></td>
        <td class='list_box_td list_bg_gray'> </td>
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
$P->addScript = "".$Script;//<script language='javascript' src='../include/DateSelect.js'></script>\n
$P->OnloadFunction = "";
$P->strLeftMenu = bienview_menu();
$P->Navigation = "BIENVIEW관리 > 제보관리 > 동물보호활동 제보목록 ";
$P->title = "동물보호활동 제보목록";
$P->strContents = $Contents;
echo $P->PrintLayOut();