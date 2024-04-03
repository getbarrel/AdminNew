<?
include("../class/layout.class");




//currency_unit

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
	<col width='25%' />
	<col width='*' />
	  <tr >
		<td align='left' colspan=2> ".GetTitleNavigation("랭귀지관리", "글로벌설정 > 랭귀지관리 ")."</td>
	  </tr>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U") ){
$Contents01 .= "
	  <tr>
	    <td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' align=absmiddle> <b  class=blk>언어추가하기</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	  <col width='15%' />
	  <col width='35%' />
	  <col width='15%' />
	  <col width='35%' />";
	  //echo $_SESSION["admin_config"][front_multiview];
	//if($_SESSION["admin_config"][front_multiview] == "Y"){
	/*
	$Contents01 .= "
	<tr>
		<td class='search_box_title' > 프론트 전시 구분</td>
		<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
	</tr>";
	*/
	//}
	$Contents01 .= "
	  <tr bgcolor=#ffffff height='34'>
	    <td class='input_box_title'> <b>언어명 <img src='".$required3_path."'></b> </td>
		<td class='input_box_item'>
			<input type='text' class='textbox' name='language_name' id='language_name' value='' validation='true' title='언어명'>
			<span class=small><!--추가하시고자 하는 은행의 이름을 기재해주세요.--></span> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." 
		</td>
		<td class='input_box_title'> <b>언어코드 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'><input type=text class='textbox' name='language_code' value='".$db->dt[language_code]."' style='width:230px;' validation=true title='언어코드'> <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff height='34'>
	    <td class='input_box_title'> <b>사용유무 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item' colspan='3'>
	    	<input type=radio name='disp' id='disp_1' value='1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>
	  ";
	/*
	   <tr bgcolor=#ffffff height='34'>
		<td class='input_box_title'> <b>화폐단위 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'>
			<input type=text class='textbox' name='currency_unit_front' value='".$db->dt[currency_unit_front]."' style='width:30px;' title='화폐단위(앞)'> 10,000 <input type=text class='textbox' name='currency_unit_back' value='".$db->dt[currency_unit_back]."' style='width:30px;' title='화폐단위(뒤)'>
		</td>
	  </tr>
	  */

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
			  <u>무통장 언어</u>로 이용하실 언어를 입력해주세요<br>
			  사용을 체크하신 언어는 <u>고객이 주문시 입금은행 선택때</u> 노출됩니다.
		</td>
	</tr>
	</table>
	";*/
   $ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');
	$ButtonString = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='padding:10px;'>
	<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
	</table>
	";
}


$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	  <tr>
	    <td align='left' colspan=6> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' align=absmiddle> <b  class=blk>언어목록</b></div>")."</td>
	  </tr>
	  <tr height=5><td colspan=6 ></td></tr>
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>
	  <col width='15%'>
	  <col width='*'>
	  <col width='15%'>
	  <col width='15%'>
	  <col width='15%'>
	  <col width='15%'>
	  <col width='15%'>
	  <tr height=25 bgcolor=#efefef align=center style='font-weight:bold'>
	    <!--td class='s_td'> 프론트구분</td-->
		<td class='m_td'> 언어명</td>
	    <td class='m_td'> 언어코드</td> 
	    <td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>";
$db = new MySQL;

$db->query("SELECT gl.*, si.mall_domain FROM global_language gl left join shop_shopinfo si on gl.mall_ix = si.mall_ix  ");


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);

	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
		    <!--td class='list_box_td list_bg_gray'>".$db->dt[mall_domain]."</td-->
			<td class='list_box_td '>".$db->dt[language_name]."</td>
		    <td class='list_box_td point'>".$db->dt[language_code]."</td> 
		    <td class='list_box_td '>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
		    <td class='list_box_td '>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents02 .= "
		    	<a href=\"javascript:updateLanguageInfo('".$db->dt[language_ix]."','".$db->dt[mall_ix]."','".$db->dt[language_name]."','".$db->dt[language_code]."','".$db->dt[currency_unit_front]."','".$db->dt[currency_unit_back]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
			}else{
				$Contents02 .= "
		    	<a href=\"javascript:alert('수정권한이 없습니다.');\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
			}
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
			$Contents02 .= "
	    		<a href=\"javascript:deleteLanguageInfo('delete','".$db->dt[language_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}else{
			$Contents02 .= "
	    		<a href=\"javascript:alert('삭제권한이 없습니다.');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}
	$Contents02 .= "
		    </td>
		  </tr> ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=6>등록된 언어가 없습니다. </td>
		  </tr>";
}
$Contents02 .= "
	  <!--tr height=1><td colspan=6 ><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small>사용을 원하시는 PG 모듈을 선택해 주세요</span></td></tr-->

	  </table>";


$ContentsDesc02 = "
<table width='660' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td align=left style='padding:10px;'>
		<img src='../image/emo_3_15.gif' align=absmiddle>  거래은행 및 언어 정보는 결제시 이용됩니다. 정확하게 입력해주세요
	</td>
</tr>
</table>
";


$Contents = "<table width='100%' border=0 cellpadding=0 cellspacing=0 style='margin-bottom:420px;'>";
$Contents = $Contents."<form name='language_form' action='language.act.php' method='post' onsubmit='return CheckFormValue(this)' style='display:inline;' act='iframe_act'><input name='act' type='hidden' value='insert'><input name='language_ix' type='hidden' value=''>";
$Contents = $Contents."<tr><td>".$Contents01."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";

 $Script = "
 <script language='javascript'>
 function updateLanguageInfo(language_ix, mall_ix, language_name,language_code,currency_unit_front,currency_unit_back,disp){
 	var frm = document.language_form;

 	frm.act.value = 'update';
 	frm.language_ix.value = language_ix;
 	frm.language_name.value = language_name;
 	frm.language_code.value = language_code; 
	frm.currency_unit_front.value = currency_unit_front;
 	frm.currency_unit_back.value = currency_unit_back; 
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}
	$('#mall_ix').val(mall_ix);
	frm.language_name.focus();

}

 function deleteLanguageInfo(act, language_ix){
 	if(confirm('해당언어 정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.language_form;
 		frm.act.value = act;
 		frm.language_ix.value = language_ix;
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
$P->strLeftMenu = global_menu();
$P->Navigation = "글로벌설정 > 번역설정 > 랭귀지관리";
$P->title = "랭귀지관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();



/*

create table global_language (
language_ix int(4) unsigned not null auto_increment  ,
language_name varchar(20) null default null,
language_code varchar(20) null default null, 
disp char(1) default '1' ,
regdate datetime not null,
primary key(language_ix));
*/
?>