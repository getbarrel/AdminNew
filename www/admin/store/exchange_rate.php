<?
include("../class/layout.class");






$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
	<col width='25%' />
	<col width='*' />
	  <tr >
		<td align='left' colspan=2> ".GetTitleNavigation("환율관리", "상점관리 > 환율관리 ")."</td>
	  </tr>";

//if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U") ){
$Contents01 .= "
	  <tr>
	    <td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>환율관리</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	  <col width='25%' />
	  <col width='*' />
	  <tr bgcolor=#ffffff height='34'>
	    <td class='input_box_title'> <b>달러(USD) <img src='".$required3_path."'></b> </td>
		<td class='input_box_item'>
			<input type='text' class='textbox' name='usd' id='usd' style='width:130px;' value='' validation='true' title='달러(USD) '>
	     <span class=small></td>
	    <td class='input_box_title'> <b>앤화(JPY) <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'><input type=text class='textbox' name='jpy' value='".$db->dt[jpy]."' style='width:130px;' validation=true title='앤화(JPY)'> <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff height='34'>
	    <td class='input_box_title'> <b>위엔화(CNY) <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'><input type=text class='textbox' name='cny' value='".$db->dt[cny]."' style='width:130px;' validation=true title='위엔화(CNY)'> <span class=small></span></td>
		<td class='input_box_title'> <b>유럽연합(EUR) <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'><input type=text class='textbox' name='eur' value='".$db->dt[eur]."' style='width:130px;' validation=true title='유럽연합(EUR)'> <span class=small></span></td>
	  </tr>
	  <!--tr bgcolor=#ffffff height='34'>
	    <td class='input_box_title'> <b>사용유무 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'>
	    	<input type=radio name='is_use' value='1' checked><label for='is_use_1'>사용</label>
	    	<input type=radio name='is_use' value='0' ><label for='is_use_0'>사용하지않음</label>
	    </td>
	  </tr-->";
//}
$Contents01 .= "
	  </table>";
//if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U") ){
/*
	$ContentsDesc01 = "
	<table cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr>
		<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
		<td align=left style='padding:10px; line-height:120%' class=small>
			  <u>무통장 계좌</u>로 이용하실 계좌를 입력해주세요<br>
			  사용을 체크하신 계좌는 <u>고객이 주문시 입금은행 선택때</u> 노출됩니다.
		</td>
	</tr>
	</table>
	";*/
   $ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
	$ButtonString = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='padding:10px;'>
	<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
	</table>
	";
}else{
    $ButtonString = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='padding:10px;'>
    </table>";
}
//}


$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	  <tr>
	    <td align='left' colspan=6> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>환율변경 목록</b></div>")."</td>
	  </tr>
	  <tr height=5><td colspan=6 ></td></tr>
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>
	  <col width='15%'>
	  <col width='15%'>
	  <col width='15%'>
	  <col width='15%'>
	  <col width='10%'>
	  <col width='*'>
	  <col width='10%'>
	  <tr height=25 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td'> 달러(USD)</td>
	    <td class='m_td'> 앤화(JPY)</td>
	    <td class='m_td'> 위엔화(CNY)</td>
		<td class='m_td'> 유럽연합(EUR)</td>
	    <td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>";
$db = new Database;


$db->query("SELECT * FROM common_exchange_rate order by regdate desc ");


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
		    <td class='list_box_td list_bg_gray'>".$db->dt[usd]."</td>
		    <td class='list_box_td point'>".$db->dt[jpy]."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[cny]."</td>
			<td class='list_box_td list_bg_gray'>".$db->dt[eur]."</td>
		    <td class='list_box_td '>".($db->dt[is_use] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
		    <td class='list_box_td '>";

			if($db->dt[is_use] != "1"){
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$Contents02 .= "
					<a href=\"javascript:deleteExchangeRate('delete','".$db->dt[er_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
				}else{
				$Contents02 .= "
					<a href=\"javascript:alert('삭제권한이 없습니다.');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
				}
			}else{
				$Contents02 .= "-";
			}
	$Contents02 .= "
		    </td>
		  </tr> ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=7>등록된 계좌가 없습니다. </td>
		  </tr>";
}
$Contents02 .= "
	  <!--tr height=1><td colspan=7><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small>사용을 원하시는 PG 모듈을 선택해 주세요</span></td></tr-->

	  </table>
	  <table><tr><td style='padding:5px 0px' >환율/1원</td></tr></table>";


$ContentsDesc02 = "
<table width='660' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td align=left style='padding:10px;'>
		<img src='../image/emo_3_15.gif' align=absmiddle>  거래은행 및 계좌 정보는 결제시 이용됩니다. 정확하게 입력해주세요
	</td>
</tr>
</table>
";

$help_text = "
<table cellpadding=2 cellspacing=0  >
	<col width=8>
	<col width=*>
	<tr><td ><img src='/admin/image/icon_list.gif' ></td><td  >각각의 환율정보를 입력후 저장하게 되면 새로운 환율정보가 적용되며 기존의 환율 정보는 히스토리로 남게 됩니다..</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td  >앤화(JPY)의 경우는 100원 기준을 1원 기준으로 환산해서 표시해야 합니다.</td></tr>
	
</table>
";

//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');
$Contents02 .=  HelpBox("환율 관리", $help_text, 100);


$Contents = "<form name='exchange_rate_form' action='exchange_rate.act.php' method='post' onsubmit='return CheckFormValue(this)' style='display:inline;' target=iframe_act><input name='act' type='hidden' value='insert'><input name='er_ix' type='hidden' value=''>";
$Contents = $Contents."<table width='100%' border=0 cellpadding=0 cellspacing=0 style='margin-bottom:420px;'>";
$Contents = $Contents."<tr><td>".$Contents01."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."</table >";
$Contents = $Contents."</form>";

 $Script = "
 <script language='javascript'>
 function updateExchangeRate(er_ix,usd,jpy,cny,is_use){
 	var frm = document.bank_form;

 	frm.act.value = 'update';
 	frm.er_ix.value = er_ix;
 	frm.usd.value = usd;
 	frm.jpy.value = jpy;
 	frm.cny.value = cny;
 	if(is_use == '1'){
 		frm.is_use[0].checked = true;
 	}else{
 		frm.is_use[1].checked = true;
 	}
	frm.usd.focus();

}

 function deleteExchangeRate(act, er_ix){
 	if(confirm('해당계좌 정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.bank_form;
 		frm.act.value = act;
 		frm.er_ix.value = er_ix;
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
$P->strLeftMenu = store_menu();
$P->Navigation = "상점관리 > 결제관련 > 환율관리";
$P->title = "환율관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();



/*

create table common_exchange_rate (
er_ix int(4) unsigned not null auto_increment  ,
usd varchar(20) null default null,
jpy varchar(20) null default null,
cny varchar(20) null default null,
is_use char(1) default '1' ,
regdate datetime not null,
primary key(er_ix));
*/
?>