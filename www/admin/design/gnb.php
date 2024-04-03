<?
include("../class/layout.class");






$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
	<col width='25%' />
	<col width='*' />
	  <tr >
		<td align='left' colspan=2> ".GetTitleNavigation("상단메뉴(GNB)관리", "디자인관리 > 상단메뉴(GNB)관리 ")."</td>
	  </tr>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U") ){
$Contents01 .= "
	  <tr>
	    <td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' align=absmiddle> <b  class=blk>상단메뉴(GNB)추가하기</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	  <col width='20%' />
	  <col width='30%' />
	  <col width='20%' />
	  <col width='30%' />
	  <tr bgcolor=#ffffff height='34'>
	    <td class='input_box_title'> <b>상단메뉴(GNB)명 <img src='".$required3_path."'></b> </td>
		<td class='input_box_item'>
			<input type='text' class='textbox' name='gnb_name' id='gnb_name' value='' validation='true' title='상단메뉴(GNB)명'>
	     <span class=small></span> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </td>
	   
	    <td class='input_box_title'> <b>상단메뉴(GNB)코드 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'><input type=text class='textbox' name='gnb_code' value='".$db->dt[gnb_code]."' style='width:230px;' validation=true title='상단메뉴(GNB)코드'> <span class=small></span></td>
	  </tr> 
	  <tr bgcolor=#ffffff height='34'>
	    <td class='input_box_title'> <b>상단메뉴 (GNB) 링크 <img src='".$required3_path."'></b> </td>
		<td class='input_box_item' colspan=3>
			<input type='text' class='textbox' name='gnb_link' id='gnb_link' style='width:95%' value='' validation='true' title='상단메뉴(GNB) 링크'>
		</td>	   
	  </tr> 
	  <tr bgcolor=#ffffff height='34'>
	    <td class='input_box_title'> <b>사용유무 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item' colspan=3>
	    	<input type=radio name='disp' id='disp_1' value='1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>";
}
$Contents01 .= "
	  </table>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U") ){
/*
	$ContentsDesc01 = "
	<table cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr>
		<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
		<td align=left style='padding:10px; line-height:120%' class=small>
			  <u>무통장 상단메뉴(GNB)</u>로 이용하실 상단메뉴(GNB)를 입력해주세요<br>
			  사용을 체크하신 상단메뉴(GNB)는 <u>고객이 주문시 입금은행 선택때</u> 노출됩니다.
		</td>
	</tr>
	</table>
	";*/
   $ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');
   $ButtonString = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	<tr bgcolor=#ffffff ><td colspan=4 align=center style='padding:10px;'><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
	</table><br>
	";
}


$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	  <tr>
	    <td align='left' colspan=6> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' align=absmiddle> <b  class=blk>상단메뉴(GNB)목록</b></div>")."</td>
	  </tr>
	  <tr height=5><td colspan=6 ></td></tr>
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>
	  <col width='13%'>
	  <col width='13%'>
	  <col width='*'>
	  <col width='10%'>
	  <col width='13%'>
	  <col width='10%'>
	  <tr height=25 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td'> 상단메뉴(GNB)명</td>
	    <td class='m_td'> 상단메뉴(GNB)코드</td> 
		<td class='m_td'> 상단메뉴 링크</td> 
	    <td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>";
$db = new MySQL;

$db->query("SELECT * FROM shop_design_gnb ");


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);

	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
		    <td class='list_box_td list_bg_gray'>".$db->dt[gnb_name]."</td>
		    <td class='list_box_td point'>".$db->dt[gnb_code]."</td> 
			<td class='list_box_td point'>".$db->dt[gnb_link]."</td> 
			
		    <td class='list_box_td '>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
		    <td class='list_box_td '>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents02 .= "
		    	<a href=\"javascript:updateGnbInfo('".$db->dt[gnb_ix]."','".$db->dt[gnb_name]."','".$db->dt[gnb_link]."','".$db->dt[gnb_code]."', '".$db->dt[disp]."')\"><img src='../images/".$admininfo["style"]."/btc_modify.gif' border=0></a>";
			}else{
				$Contents02 .= "
		    	<a href=\"javascript:alert('수정권한이 없습니다.');\"><img src='../images/".$admininfo["style"]."/btc_modify.gif' border=0></a>";
			}
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
			$Contents02 .= "
	    		<a href=\"javascript:deleteGnbInfo('delete','".$db->dt[gnb_ix]."')\"><img src='../images/".$admininfo["style"]."/btc_del.gif' border=0></a>";
			}else{
			$Contents02 .= "
	    		<a href=\"javascript:alert('삭제권한이 없습니다.');\"><img src='../images/".$admininfo["style"]."/btc_del.gif' border=0></a>";
			}
	$Contents02 .= "
		    </td>
		  </tr> ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=5>등록된 상단메뉴(GNB)가 없습니다. </td>
		  </tr>";
}
$Contents02 .= "
	  <!--tr height=1><td colspan=6 ><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small>사용을 원하시는 PG 모듈을 선택해 주세요</span></td></tr-->

	  </table>";


$ContentsDesc02 = "
<table width='660' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td align=left style='padding:10px;'>
		<img src='../image/emo_3_15.gif' align=absmiddle>  거래은행 및 상단메뉴(GNB) 정보는 결제시 이용됩니다. 정확하게 입력해주세요
	</td>
</tr>
</table>
";


$Contents = "<table width='100%' border=0 cellpadding=0 cellspacing=0 style='margin-bottom:420px;'>";
$Contents = $Contents."<form name='gnb_form' action='gnb.act.php' method='post' onsubmit='return CheckFormValue(this)' style='display:inline;' act='iframe_act'><input name='act' type='hidden' value='insert'><input name='gnb_ix' type='hidden' value=''>";
$Contents = $Contents."<tr><td>".$Contents01."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";

 $Script = "
 <script style='javascript'>
 function updateGnbInfo(gnb_ix,gnb_name,gnb_link, gnb_code,disp){
 	var frm = document.gnb_form;

 	frm.act.value = 'update';
 	frm.gnb_ix.value = gnb_ix;
 	frm.gnb_name.value = gnb_name;
	frm.gnb_link.value = gnb_link;
 	frm.gnb_code.value = gnb_code; 
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}
	frm.gnb_name.focus();

}

 function deleteGnbInfo(act, gnb_ix){
 	if(confirm('해당 상단메뉴(GNB) 정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.gnb_form;
 		frm.act.value = act;
 		frm.gnb_ix.value = gnb_ix;
 		frm.submit();
 	}
}
 
 </script>
 ";


$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = design_menu();
$P->Navigation = "디자인관리 > 상단메뉴관리";
$P->title = "상단메뉴관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();



/*

create table shop_design_gnb (
gnb_ix int(4) unsigned not null auto_increment  ,
gnb_name varchar(20) null default null,
gnb_code varchar(20) null default null, 
disp char(1) default '1' ,
regdate datetime not null,
primary key(gnb_ix));
*/
?>