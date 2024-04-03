<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2017-07-27
 * Time: 오후 4:57
 */

include $_SERVER['DOCUMENT_ROOT']."/admin/class/layout.class";

$db = new Database;
$Contents01 .= "
    
	<form name='searchmember' style='display:inline;'>
	<input type='hidden' name='info_type' value='$info_type'>
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
						<td class='search_box_title' >분류 </td>
						<td class='search_box_item' >
							<select name='info_type' id='info_type'  style=\"font-size:12px;\">
                                <option value='' ".CompareReturnValue("",$info_type,'selected').">전체</option>
                                <option value='H' ".CompareReturnValue("H",$info_type,'selected').">병원</option>
                                <option value='F' ".CompareReturnValue("F",$info_type,'selected').">약국</option>
                            </select>
						</td>
					</tr>
					<tr height=27>
						
						<td class='search_box_title' >업체명 </td>
						<td class='search_box_item' >
							<input class='textbox' value='".$com_name."' autocomplete='off'  name='com_name' title='업체명'>							
						</td>
					</tr>
					<tr height='27'>
						<td class='search_box_title' bgcolor='#efefef' align=center>
							<label for='search_check'><b>지역</b></label>
						</td>
						<td class='search_box_item'>
						    ".SearchArea('sido','gugun',$sido,$gugun)."							                    
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

$where = " where ho_ix <> '0' ";


if($_GET['mode'] == 'search'){
    if(!empty($_GET['info_type'])){
        $where .= " and info_type = '".$_GET['info_type']."' ";
    }
    if(!empty($_GET['com_name'])){
        $where .= " and com_name LIKE '%".$_GET['com_name']."%' ";
    }
    if($_GET['sido'] || $_GET['gugun']){
        $seach_addr = $_GET['sido']." ".$_GET['gugun'];
        $where .= " and com_addr1 LIKE '".$seach_addr."%' ";
    }
}

$sql = "select count(*) cnt from bienview_hospital_info $where ";
$db->query($sql);
$db->fetch();
$total = $db->dt['cnt'];

$sql = "select * from bienview_hospital_info $where order by regdate desc limit $start,$max";
$db->query($sql);
$hosptal_array = $db->fetchall();

$Contents01 .= "
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
	<tr height=30 >
		<td>
			<b>전체 : ".$total." 개</b> 
		</td>
		<td colspan=5 align=right>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
    $Contents01 .= " <a href='?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
}else{
    $Contents01 .= " <a href=".$auth_excel_msg."><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
}

$Contents01 .= "
		</td>
	</tr>
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>
	<col width=5%>
	<col width=12%>
	<col width=12%>
	<col width=12%>
	<col width=12%>
	<col width=12%>
	<col width=12%>
	<col width=12%>
	<col width=*>
	<tr bgcolor=#efefef style='font-weight:bold' align=center height=28>		
		<td class='s_td'>번호</td>
		<td class='m_td'>분류</td>
		<td class='m_td'>업체명</td>	
		<td class='m_td'>주소</td>	
		<td class='m_td'>전화번호</td>	
		<td class='m_td'>홈페이지주소</td>	
		<td class='m_td'>좌표</td>	
		<td class='m_td'>전시상태</td>	
		<td class='e_td'>관리</td>
	</tr>";
if(is_array($hosptal_array) && count($hosptal_array) > 0){
    $i=0;
    foreach($hosptal_array as $data){
        $no = $total - ($page - 1) * $max - $i;
        switch ($data['info_type']){
            case 'H':
                $info_type_text = "병원";
                break;
            case 'F':
                $info_type_text = "약국";
                break;
        }
        switch ($data['disp']){
            case '1':
                $disp_text = "전시";
                break;
            case '0':
                $disp_text = "미전시";
                break;
        }
        $Contents01 .= "
        <tr height=28 align=center>        
            <td class='list_box_td' bgcolor='#ffffff'>".$no."</td>			
            <td class='list_box_td list_bg_gray'>".$info_type_text."</td>
            <td class='list_box_td' >".$data['com_name']."</td>
            <td class='list_box_td list_bg_gray'>[".$data['com_zip']."] ".$data['com_addr1']." ".$data['com_addr2']." </td>
            <td class='list_box_td' >".$data['phone']."</td>
            <td class='list_box_td list_bg_gray'>".$data['homepage']." </td>
            <td class='list_box_td' > 
                x :".$data['x_code']." <br/>
                y :".$data['y_code']." <br/>
                경도 :".$data['longitude']." <br/>
                위도 :".$data['latitude']." <br/>
            </td>
            <td class='list_box_td list_bg_gray'>".$disp_text." </td>
            <td class='list_box_td' >
                <input type='button' onclick=\"HospitalInfo('".$data['ho_ix']."')\" value='수정' />
                <input type='button' onclick=\"delete_hospital('".$data['ho_ix']."')\" value='삭제' />
            </td>
        </tr>
        ";
    }
}else{
    $Contents01 .= "
       <tr height=28 align=center>        
            <td class='list_box_td' colspan='9'>
                <span>등록된 병원&약국 목록이 없습니다.</span>            
            </td>
        </tr>
    ";
}
$Contents01 .= "
	
    </table>
    <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
        <tr height=40>
         <td colspan='9' align='right'><input type='button' onclick=\"HospitalInfo('');\" value='신규등록'></td>
        </tr>
    </table>
	";
$Contents01 .= "
    <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
        <tr height=40>
         <td colspan='9' align='right'>&nbsp;".page_bar($total, $page, $max,$query_string."#list_top","")."&nbsp;</td>
        </tr>
    </table>";



$Contents = $Contents01;

$Script="
<script>
    function HospitalInfo(ho_ix){
        window.open('./hospital_input.php?ho_ix='+ho_ix+'', 'hospital_input', 'top=100, left=300, width=727px, height=312px, resizble=no, scrollbars=yes');
    }    
    function delete_hospital(ho_ix){
        window.frames['iframe_act'].location.href= './hospital_input.act.php?act=delete&ho_ix='+ho_ix;
    }
    
</script>
";
$P = new LayOut();
$P->addScript = "".$Script;//<script language='javascript' src='../include/DateSelect.js'></script>\n
$P->OnloadFunction = "";
$P->strLeftMenu = bienview_menu();
$P->Navigation = "BIENVIEW관리 > 병원&약국 관리 > 병원&약국 목록 ";
$P->title = "병원&약국 목록";
$P->strContents = $Contents;
echo $P->PrintLayOut();