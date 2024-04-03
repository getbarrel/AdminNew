<?
include("../class/layout.class");






$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
	<col width='25%' />
	<col width='*' />
	  <tr >
		<td align='left' colspan=2> ".GetTitleNavigation("스타일관리", "상품관리 > 스타일관리 ")."</td>
	  </tr>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U") ){
$Contents01 .= "
	  <tr>
	    <td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' align=absmiddle> <b  class=blk>스타일추가하기</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	  <col width='20%' />
	  <col width='30%' />
	  <col width='20%' />
	  <col width='30%' />
	  <tr bgcolor=#ffffff height='34'>
	    <td class='input_box_title'> <b>스타일명 <img src='".$required3_path."'></b> </td>
		<td class='input_box_item'>
			<input type='text' class='textbox' name='style_name' id='style_name' value='' validation='true' title='스타일명'>
	     <span class=small><!--추가하시고자 하는 은행의 이름을 기재해주세요.--></span> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </td>
	   
	    <td class='input_box_title'> <b>스타일코드 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'><input type=text class='textbox' name='style_code' value='".$db->dt[style_code]."' style='width:230px;' validation=true title='스타일코드'> <span class=small></span></td>
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
			  <u>무통장 스타일</u>로 이용하실 스타일를 입력해주세요<br>
			  사용을 체크하신 스타일는 <u>고객이 주문시 입금은행 선택때</u> 노출됩니다.
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
	    <td align='left' colspan=6> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' align=absmiddle> <b  class=blk>스타일목록</b></div>")."</td>
	  </tr>
	  <tr height=5><td colspan=6 ></td></tr>
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>
	  <col width='15%'>
	  <col width='*'>
	  <col width='20%'>
	  <col width='20%'>
	  <col width='20%'>
	  <tr height=25 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td'> 스타일명</td>
	    <td class='m_td'> 스타일코드</td> 
	    <td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>";
$db = new MySQL;

$db->query("SELECT * FROM shop_product_style ");


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);

	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
		    <td class='list_box_td list_bg_gray'>".$db->dt[style_name]."</td>
		    <td class='list_box_td point'>".$db->dt[style_code]."</td> 
		    <td class='list_box_td '>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
		    <td class='list_box_td '>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents02 .= "
		    	<a href=\"javascript:updateStyleInfo('".$db->dt[style_ix]."','".$db->dt[style_name]."','".$db->dt[style_code]."', '".$db->dt[disp]."')\"><img src='../images/".$admininfo["style"]."/btc_modify.gif' border=0></a>";
			}else{
				$Contents02 .= "
		    	<a href=\"javascript:alert('수정권한이 없습니다.');\"><img src='../images/".$admininfo["style"]."/btc_modify.gif' border=0></a>";
			}
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
			$Contents02 .= "
	    		<a href=\"javascript:deleteStyleInfo('delete','".$db->dt[style_ix]."')\"><img src='../images/".$admininfo["style"]."/btc_del.gif' border=0></a>";
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
		    <td align=center colspan=5>등록된 스타일가 없습니다. </td>
		  </tr>";
}
$Contents02 .= "
	  <!--tr height=1><td colspan=6 ><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small>사용을 원하시는 PG 모듈을 선택해 주세요</span></td></tr-->

	  </table>";


$ContentsDesc02 = "
<table width='660' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td align=left style='padding:10px;'>
		<img src='../image/emo_3_15.gif' align=absmiddle>  거래은행 및 스타일 정보는 결제시 이용됩니다. 정확하게 입력해주세요
	</td>
</tr>
</table>
";


$Contents = "<table width='100%' border=0 cellpadding=0 cellspacing=0 style='margin-bottom:420px;'>";
$Contents = $Contents."<form name='style_form' action='style.act.php' method='post' onsubmit='return CheckFormValue(this)' style='display:inline;' act='iframe_act'><input name='act' type='hidden' value='insert'><input name='style_ix' type='hidden' value=''>";
$Contents = $Contents."<tr><td>".$Contents01."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";

 $Script = "
 <script style='javascript'>
 function updateStyleInfo(style_ix,style_name,style_code,disp){
 	var frm = document.style_form;

 	frm.act.value = 'update';
 	frm.style_ix.value = style_ix;
 	frm.style_name.value = style_name;
 	frm.style_code.value = style_code; 
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}
	frm.style_name.focus();

}

 function deleteStyleInfo(act, style_ix){
 	if(confirm('해당스타일 정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.style_form;
 		frm.act.value = act;
 		frm.style_ix.value = style_ix;
 		frm.submit();
 	}
}

function etcBank(etc){
	if(etc == 'etc'){
		document.getElementById('etc').disabled = false;
	}else{
		document.getElementById('etc').disabled = true;
	}
}
 </script>
 ";


$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = product_menu();
$P->Navigation = "상품관리 > 상품분류관리 > 스타일관리";
$P->title = "스타일관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();



/*

create table shop_product_style (
style_ix int(4) unsigned not null auto_increment  ,
style_name varchar(20) null default null,
style_code varchar(20) null default null, 
disp char(1) default '1' ,
regdate datetime not null,
primary key(style_ix));
*/
?>