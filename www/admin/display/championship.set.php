<?php
include("../class/layout.class");

$db = new Database;
$mdb = new Database;

if($act == 'update') {
    $usdate = $sdate.' '.$sdate_h.':'.$sdate_i.':'.$sdate_s;
    $uedate = $edate.' '.$edate_h.':'.$edate_i.':'.$edate_s;

    $sql = "update championship_set set year = '$year', sdate = '$usdate', edate = '$uedate', max = '$max', display_use = '$display_use' where cs_ix = 1";
    $db->query($sql);
}

$sql="select * from championship_set";
$mdb->query($sql);
$info = $mdb->fetch();

$year           = $info['year'];
$sdate          = $info['sdate'];
$edate          = $info['edate'];
$max            = $info['max'];
$display_use    = $info['display_use'];


$Contents = "<form method='post'><input type='hidden' name='act' value='update' /><table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='25%' />
	<col width='*' />
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>시행년도 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		    <input type='text' name='year' value='$year' maxlength='4'/>
		</td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>시작일 / 종료일 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		    ".search_date('sdate','edate',$sdate,$edate,'Y','')."
		</td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>모집인원 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<input type='text' name='max' value='$max'/>
		</td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>미리보기 </b></td>
		<td class='input_box_item'>
			<input type='radio' name='display_use' id='display_use_Y' value='Y' ".("Y" == $display_use ? "checked":"")."><label for='display_use_Y'> 사용</label>
			<input type='radio' name='display_use' id='display_use_N' value='N' ".("N" == $display_use ? "checked":"")."><label for='display_use_N'> 미사용</label>
		</td>
	</tr>
	</table>";

$Contents .= "<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff height=70><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table></form>";

$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "프로모션 전시관리 > 챔피언십관리 > 스프린트 챔피언십 설정";
$P->title = "스프린트 챔피언십 설정";
$P->strLeftMenu = display_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();