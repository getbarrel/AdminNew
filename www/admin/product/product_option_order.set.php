<?php
include("../class/layout.class");

if(empty($ordertype)) {
    $ordertype = 'asc';
}

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 align='left'>
	  <tr >
		<td align='left' colspan=2> ".GetTitleNavigation("상품등록", "상품등록 > 상품옵션순서설정 ")."</td>
	  </tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 align='left'>
	 <tr>
	    <td align='left'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle> <b>옵션순서추가하기</b></div>")."</td>
	  </tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 align='left' class='input_table_box' style='margin-top:3px;'>
        <tr>
            <td class='input_box_title' style='width:150px;'> <b>옵션명 <img src='".$required3_path."'></b> </td>
            <td class='input_box_item'>
                <input type='text' class=textbox name='option_name' style='width:200px' id='option_name' value='' validation='true' title='옵션명'>
             </td>
        </tr>
	
        <tr >
            <td class='input_box_title'> <b>옵션순서 <img src='".$required3_path."'></b> </td>
            <td class='input_box_item'><input type=text class='textbox' name='option_sort' value='' style='width:230px;' validation=true title='옵션순서'> </td>
        </tr>
	</table>";

$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr>
	    <td align='left'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;' ><img src='../image/title_head.gif' align=absmiddle> <b>옵션목록</b></div>")."</td>
	  </tr>
	</table>
	<div style='width:100%;height:350px;'>
	<table width='100%' cellpadding=0 cellspacing=0 align='left' class='list_table_box' style='margin-top:3px;'>
	  <tr height=27 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='m_td' style='width:20%; padding:0; text-align:center;' class='s_td'> 옵션명</td>
	    <td class='m_td' style='width:20%; padding:0; text-align:center;' class='m_td'> <a href='?page=$page&max=$max&ordertype=".($ordertype == "asc"?"desc":"asc")."'>순서 <img src='/admin/images/ico_arrow_".(($ordertype == "asc") ? "up":"down").".png' border=0></a></td>
	    <td class='m_td' style='width:20%; padding:0; text-align:center;' class='m_td'> 등록일자</td>
	    <td class='m_td' style='width:20%; padding:0; text-align:center;' class='m_td'> 수정일자</td>
	    <td class='e_td' style='width:10%; padding:0; text-align:center;' class='e_td'> 관리</td>
	  </tr>";
$max = 20; //페이지당 갯수

if ($page == '')
{
    $start = 0;
    $page  = 1;
}
else
{
    $start = ($page - 1) * $max;
}
$db = new Database;


$db->query("SELECT * FROM shop_product_options_sort_by_value ");
$total = $db->total;
$str_page_bar = page_bar($total, $page,$max, "&max=$max&mmode=pop","");

if($total){
    $db->query("SELECT * FROM shop_product_options_sort_by_value order by view_order $ordertype limit $start , $max ");
    for($i=0;$i < $db->total;$i++){
        $db->fetch($i);

        $Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
		    <td class='list_box_td list_bg_gray'>".$db->dt['value']."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt['view_order']."</td>
		    <td class='list_box_td'>".$db->dt['regdate']."</td>
		    <td class='list_box_td'>".$db->dt['editdate']."</td>
		    <td class='list_box_td list_bg_gray'>
			";

        if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
            $Contents02 .= "<a href=\"javascript:updateOptionInfo('".$db->dt['idx']."','".$db->dt['value']."','".$db->dt['view_order']."')\"><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_modify.gif' border=0 align=absmiddle></a> ";
        }else{
            $Contents02 .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_modify.gif' border=0 align=absmiddle ></a> ";
        }

        //$mString .= "<img  src='../image/btn_view_source.gif'  border=0 align=absmiddle>";
        if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
            $Contents02 .= "<a href=\"javascript:deleteOptionInfo('delete','".$db->dt['idx']."')\"><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' border=0 align=absmiddle></a> ";
        }else{
            $Contents02 .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' border=0 align=absmiddle ></a> ";
        }
        $Contents02 .= "


		    </td>
		  </tr>	  ";
    }
}else{
    $Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=6>등록된 옵션순서가 없습니다. </td>
		  </tr>	  ";
}
$Contents02 .= "
	  </table>
	  <div style='width:100%;text-align:center;padding-top:10px;clear:both;'>".$str_page_bar."</div>
	  </div>";

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




$Contents = "<form name='bank_form' action='product_option_order.act.php' method='post' onsubmit='return CheckFormValue(this)' target='act' enctype='multipart/form-data'><input name='act' type='hidden' value='insert'><input name='idx' type='hidden' value=''><input name='ordertype' type='hidden' value='$ordertype'/>";
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
 <script language='javascript'>
 function updateOptionInfo(idx,option_name,option_sort){
 	var frm = document.bank_form;

 	frm.act.value = 'update';
 	frm.idx.value = idx;
 	frm.option_name.value = option_name;
 	frm.option_sort.value = option_sort;
}

 function deleteOptionInfo(act, idx){
 	if(confirm('해당 옵션명을 정말로 삭제하시겠습니까?')){
 		var frm = document.bank_form;
 		frm.act.value = act;
 		frm.idx.value = idx;
 		frm.submit();
 	}
}

 </script>
 ";

$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->Navigation = "상품관리 > 상품등록 > 상품옵션순서설정";
	$P->title = "상품옵션순서설정";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();