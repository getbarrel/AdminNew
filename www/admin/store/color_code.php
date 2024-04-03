<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2019-07-18
 * Time: 오후 3:38
 */
include("../class/layout.class");


$sql = "select * from shop_color_code order by idx desc";
$db->query($sql);
$total = $db->total;
$max = 1000;
$page = 1;
$datas = $db->fetchall();

if(is_array($datas) && count($datas) > 0){
    $i = 0;
    $data_html = "";
    foreach($datas as $key=>$val){
        $no = $total - ($page - 1) * $max - $i;
        $data_html .= "
        <tr bgcolor=#ffffff height=30 align=center>
            <td class='list_box_td '>".$no."</td>
            <td class='list_box_td list_bg_gray'>".$val['color_code']."</td>
            <td class='list_box_td '>".$val['color_code_name']."</td>
		    <td class='list_box_td list_bg_gray'>
		        <input type='button' value='삭제' onclick=\"deleteColor('".$val['idx']."')\"/>
		    </td>
        </tr>";
        $i++;
    }
}


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 align='left'>
        <tr >
            <td align='left' colspan=2> ".GetTitleNavigation("색상 코드 설정", "상점관리 > 색상 코드 설정 ")."</td>
        </tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 align='left' class='input_table_box' style='margin-top:3px;'>
        <tr>
            <td class='input_box_title' style='width:150px;'> <b>색상코드 <img src='".$required3_path."'></b> </td>
            <td class='input_box_item'>
                <input type='text' class=textbox  name='color_code' style='width:200px' value='' validation='true' title='색상코드' />
            </td>
        </tr>
        <tr>
            <td class='input_box_title' style='width:150px;'> <b>색상코드명 <img src='".$required3_path."'></b> </td>
            <td class='input_box_item'>
                <input type='text' class=textbox name='color_code_name' style='width:200px' value='' validation='true' title='색상코드명'>
            </td>
        </tr>
	</table>";

$Contents02 = "
    <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
        <tr>
            <td align='left'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;' ><img src='../image/title_head.gif' align=absmiddle> <b>색상코드</b></div>")."</td>
        </tr>
    </table>
    <div style='width:100%;height:350px;'>
        <table width='100%' cellpadding=0 cellspacing=0 align='left' class='list_table_box' style='margin-top:3px;'>
            <col width='10%' />
            <col width='30%' />
            <col width='30%' />
            <col width='10%' />
            <tr height=27 bgcolor=#efefef align=center style='font-weight:bold'>
                <td class='s_td' style='text-align:center;' class='s_td'> 순번</td>
                <td class='m_td' style='text-align:center;' class='s_td'> 색상코드</td>
                <td class='m_td' style='text-align:center;' class='m_td'> 색상코드명칭</td>
                <td class='e_td' style='text-align:center;' class='e_td'> 관리</td>
            </tr>
            ".$data_html."
        </table> 
         <div style='width:100%;text-align:center;padding-top:10px;clear:both;'>".$str_page_bar."</div>
    </div>
	  ";


if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
    $ButtonString = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$_SESSION["admininfo"]["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
	</table>
	";
}else{
    $ButtonString = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff ><td colspan=4 align=center><a href=\"".$auth_update_msg."\"><img src='../images/".$_SESSION["admininfo"]["language"]."/b_save.gif' border=0 align=absmiddle ></a></td></tr>
	</table>
	";

}


$Contents = "<form name='colorFrm' action='color_code.act.php' method='post' onsubmit='return CheckFormValue(this)' target='act' >
<input name='act' type='hidden' value='insert'>
<input name='idx' type='hidden' value=''>";
$Contents = $Contents."<table width='100%' border=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</table >";
$Contents = $Contents."</form>";
$Contents = $Contents."<table width='100%' border=0>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."</table >";

$Script = "
<script>
    function deleteColor(idx){
        if(confirm('코드를 삭제 하시겠습니까?')){
            window.frames['act'].location.href='color_code.act.php?act=delete&idx='+idx;
        }
    }

</script>
";

if($mmode == "pop"){
    $P = new ManagePopLayOut();
    $P->addScript = $Script;
    $P->Navigation = "상점관리 > 쇼핑몰 설정 > 색상 코드 설정";
    $P->NaviTitle = "색상 코드 설정";
    $P->strContents = $Contents;
    echo $P->PrintLayOut();
}else{
    $P = new LayOut();
    $P->addScript = $Script;
    //$P->strLeftMenu = design_menu();
    $P->strLeftMenu = store_menu();
    $P->Navigation = "상점관리 > 쇼핑몰 설정 > 색상 코드 설정";
    $P->title = "색상 코드 설정";
    $P->strContents = $Contents;
    echo $P->PrintLayOut();
}