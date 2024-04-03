<?
include("../class/layout.class");




//currency_unit

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
	<col width='25%' />
	<col width='*' />
	  <tr >
		<td align='left' colspan=2> ".GetTitleNavigation("국가관리", "제휴사연동 > 국가관리 ")."</td>
	  </tr>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U") ){
$Contents01 .= "
	  <tr>
	    <td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' align=absmiddle> <b  class=blk>국가추가하기</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	  <col width='15%' />
	  <col width='35%' />
	  <col width='15%' />
	  <col width='35%' />
	  <tr bgcolor=#ffffff height='34'>
	    <td class='input_box_title'> <b>국가명 <img src='".$required3_path."'></b> </td>
		<td class='input_box_item'>
			<input type='text' class='textbox' name='nation_name' id='nation_name' value='' validation='true' title='국가명'>
			<span class=small><!--추가하시고자 하는 은행의 이름을 기재해주세요.--></span> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." 
		</td>
		<td class='input_box_title'> <b>국가코드 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'><input type=text class='textbox' name='nation_code' value='".$db->dt[nation_code]."' style='width:230px;' validation=true title='국가코드'> <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff height='34'>
	    <td class='input_box_title'> <b>언어 <img src='".$required3_path."'></b> </td>
		<td class='input_box_item'>
			".getLanguageType($trans_type,"")."
		</td>
		<td class='input_box_title'><b>화폐단위 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'>".getCurrencyInfo($currency_type,"")."</td>
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
			  <u>무통장 국가</u>로 이용하실 국가를 입력해주세요<br>
			  사용을 체크하신 국가는 <u>고객이 주문시 입금은행 선택때</u> 노출됩니다.
		</td>
	</tr>
	</table>
	";*/
   $ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');
	$ButtonString = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0'  >
	<tr bgcolor=#ffffff ><td colspan=4 align=center style='padding:10px;'><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
	</table>
	";
}


$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	  <tr>
	    <td align='left' colspan=6> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' align=absmiddle> <b  class=blk>국가목록</b></div>")."</td>
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
	    <td class='s_td'> 국가명</td>
	    <td class='m_td'> 국가코드</td> 
		<td class='m_td'> 언어</td> 
		<td class='m_td'> 화폐단위</td> 
	    <td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>";
$db = new MySQL;

$sql = "SELECT gn.*, gc.currency_name, gl.language_name 
			FROM global_nation gn 
			left join global_currency gc on gn.currency_ix  = gc.currency_ix  
			left join global_language gl on gn.language_ix  = gl.language_ix   ";

$db->query($sql);


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);

	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
		    <td class='list_box_td list_bg_gray'>".$db->dt[nation_name]."</td>
		    <td class='list_box_td point'>".$db->dt[nation_code]."</td> 
			<td class='list_box_td point'>".$db->dt[language_name]."</td> 
			<td class='list_box_td point'>".$db->dt[currency_name]."</td> 
		    <td class='list_box_td '>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
		    <td class='list_box_td '>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents02 .= "
		    	<a href=\"javascript:updateNationInfo('".$db->dt[nation_ix]."','".$db->dt[nation_name]."','".$db->dt[nation_code]."','".$db->dt[language_ix]."','".$db->dt[currency_ix]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo["nation"]."/btc_modify.gif' border=0></a>";
			}else{
				$Contents02 .= "
		    	<a href=\"javascript:alert('수정권한이 없습니다.');\"><img src='../images/".$admininfo["nation"]."/btc_modify.gif' border=0></a>";
			}
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
			$Contents02 .= "
	    		<a href=\"javascript:deleteNationInfo('delete','".$db->dt[nation_ix]."')\"><img src='../images/".$admininfo["nation"]."/btc_del.gif' border=0></a>";
			}else{
			$Contents02 .= "
	    		<a href=\"javascript:alert('삭제권한이 없습니다.');\"><img src='../images/".$admininfo["nation"]."/btc_del.gif' border=0></a>";
			}
	$Contents02 .= "
		    </td>
		  </tr> ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=7>등록된 국가가 없습니다. </td>
		  </tr>";
}
$Contents02 .= "
	  <!--tr height=1><td colspan=6 ><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small>사용을 원하시는 PG 모듈을 선택해 주세요</span></td></tr-->

	  </table>";


$ContentsDesc02 = "
<table width='660' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td align=left style='padding:10px;'>
		<img src='../image/emo_3_15.gif' align=absmiddle>  거래은행 및 국가 정보는 결제시 이용됩니다. 정확하게 입력해주세요
	</td>
</tr>
</table>
";


$Contents = "<table width='100%' border=0 cellpadding=0 cellspacing=0 style='margin-bottom:420px;'>";
$Contents = $Contents."<form name='nation_form' action='nation.act.php' method='post' onsubmit='return CheckFormValue(this)' style='display:inline;' act='iframe_act'><input name='act' type='hidden' value='insert'><input name='nation_ix' type='hidden' value=''>";
$Contents = $Contents."<tr><td>".$Contents01."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";

 $Script = "
 <script nation='javascript'>
 function updateNationInfo(nation_ix,nation_name,nation_code,language_ix,currency_ix,disp){
 	var frm = document.nation_form;

 	frm.act.value = 'update';
 	frm.nation_ix.value = nation_ix;
 	frm.nation_name.value = nation_name;
 	frm.nation_code.value = nation_code; 

	$('#language_ix').val(language_ix);
	$('#currency_ix').val(currency_ix);

	//frm.currency_unit_front.value = currency_unit_front;
 	//frm.currency_unit_back.value = currency_unit_back; 
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}
	frm.nation_name.focus();

}

 function deleteNationInfo(act, nation_ix){
 	if(confirm('해당국가 정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.nation_form;
 		frm.act.value = act;
 		frm.nation_ix.value = nation_ix;
 		frm.submit();
 	}
}
 
 </script>
 ";


$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = global_menu();
$P->Navigation = "제휴사연동 > 기본정보 설정 > 국가관리";
$P->title = "국가관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();



/*

create table global_nation (
nation_ix int(4) unsigned not null auto_increment  ,
nation_name varchar(20) null default null,
nation_code varchar(20) null default null, 
disp char(1) default '1' ,
regdate datetime not null,
primary key(nation_ix));
*/
?>