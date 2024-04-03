<?
include("../class/layout.class");
//include("./mail.config.php");



$db = new Database;
$db->query("SELECT * FROM shop_mail_box where mail_ix= '$mail_ix'");
$db->fetch();

if($db->total){
	$mail_ix = $db->dt[mail_ix];
	$mail_title = $db->dt[mail_title];
	$mail_text = $db->dt[mail_text];

	$disp = $db->dt[disp];
	$act = "update";


}else{
	$act = "insert";
	$mail_use_sdate = "";
	$mail_use_edate = "";
	$disp = "1";


}


$Script = "
<Script Language='JavaScript'>
function SubmitX(frm){
	//alert(iView.document.body.innerHTML);
	frm.content.value = iView.document.body.innerHTML;
	return true;
}

function SendMailCheck(frm){
	if(frm.mt_ix.value == ''){
		alert('타겟군을 선택해주세요');
		return false;
	}

	frm.mail_content.value = iView.document.body.innerHTML;
	return true;
}

function Content_Input(){
	document.INPUT_FORM.content.value = document.INPUT_FORM.mail_text.value;
}


function init(){
	var frm = document.INPUT_FORM;
	Content_Input();

}


function clearAll(frm){
		for(i=0;i < frm.mh_ix.length;i++){
				frm.mh_ix[i].checked = false;
		}
}
function checkAll(frm){
       	for(i=0;i < frm.mh_ix.length;i++){
				frm.mh_ix[i].checked = true;
		}
}
function fixAll(frm){
	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;

	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
}



function SelectDelete(frm){
	var check_bool = false;


		for(i=0;i < frm.mh_ix.length;i++){
			if(frm.mh_ix[i].checked){
				check_bool = true	;
			}
		}

		if(!check_bool){
			alert('한명 이상의 회원이 선택되어야 합니다');
			return false;
		}

		return true;

}

function deleteMailHistory(act, mh_ix){
	if(confirm('해당 메일목록을 정말 삭제하시겠습니까? '))
	{
		document.frames['iframe_act'].location.href= 'email_manage.act.php?act=history_delete&mh_ix='+mh_ix;
		//
	}
}

</Script>";



$Contents = "
<table width='100%' border='0' align='left' >
 <tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("이메일설정", "리셀러관리 > 리셀러설정 > 이메일설정")."</td>
</tr>
<tr>
    <td>

        <form name='INPUT_FORM' method='post' onSubmit=\"return SubmitX(this)\" action='email_manage.act.php'><input type='hidden' name=act value='$act'><input type='hidden' name=mail_ix value='$mail_ix'>

		<table border='0' cellpadding=3 cellspacing=0 width='100%' class='input_table_box' >
		  <tr height=28>
			<td class='input_box_title' width='20%' nowrap>메일 제목</td>
			<td class='input_box_item' style='padding:0px 20px 0px 5px;'><input type='text' name='mail_title' value='".$db->dt[mail_title]."' class='textbox' validation=true maxlength='50' style='width:100%;'></td>
		  </tr>
		  <tr height=28>
			<td class='input_box_title'>표시여부 </td>
			<td class='input_box_item'>
			<input type='hidden' name='pop' value='1'> <input type='radio' name='disp' id='disp_1' value='1' ".CompareReturnValue("1",$disp,"checked")."> <label for='disp_1' >표시</label> <input type='radio' name='disp' id='disp_0' value='0' ".CompareReturnValue("0",$disp,"checked")."><label for='disp_0' >표시하지 않음</label>
			</td>
		  </tr>
		  <tr bgcolor='#F8F9FA'>
			<td colspan=2>
			 <table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>
				<tr>
				  <td height='30' colspan='3'>
						  <table id='tblCtrls' width='100%' border='0' cellspacing='1' cellpadding='0' align='center'>
							<tr>
							  <td bgcolor='F5F6F5'>
								 <table width='100%' border='0' cellspacing='0' cellpadding='0'>
								  <tr>
									<td width='18%' height='56'>
										<table width='100%' height='56' border='0' align='center' cellpadding='0' cellspacing='0'>
										<tr align='center' valign='bottom'>
										  <td height='26'><a href='javascript:doBold();' onMouseOver=\"MM_swapImage('editImage1','','../webedit/image/wtool1_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool1.gif' name='editImage1' width='19' height='18' border='0' id='editImage1'></a></td>
										  <td><a href='javascript:doItalic();' onMouseOver=\"MM_swapImage('editImage2','','../webedit/image/wtool2_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool2.gif' name='editImage2' width='19' height='18' border='0' id='editImage2'></a></td>
										  <td><a href='javascript:doUnderline();' onMouseOver=\"MM_swapImage('editImage3','','../webedit/image/wtool3_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool3.gif' name='editImage3' width='19' height='18' border='0' id='editImage3'></a></td>
										</tr>
										<tr>
										  <td height='3' colspan='3'></td>
										</tr>
										<tr align='center' valign='top'>
										  <td height='27'><a href='javascript:doLeft();' onMouseOver=\"MM_swapImage('editImage8','','../webedit/image/wtool8_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool8.gif' name='editImage8' width='19' height='18' border='0' id='editImage8'></a></td>
										  <td><a href='javascript:doCenter();' onMouseOver=\"MM_swapImage('editImage9','','../webedit/image/wtool9_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool9.gif' name='editImage9' width='19' height='18' border='0' id='editImage9'></a></td>
										  <td><a href='javascript:doRight();' onMouseOver=\"MM_swapImage('editImage10','','../webedit/image/wtool10_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool10.gif' name='editImage10' width='19' height='18' border='0' id='editImage10'></a></td>
										</tr>
									  </table>
										 </td>
									<td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
									<td width='19%'>
											<table width='100%' border='0' cellspacing='0' cellpadding='0'>
										<tr>
										  <td width='100%' height='27' align='center' valign='bottom'><a href='javascript:doFont();' onMouseOver=\"MM_swapImage('editImage4','','../webedit/image/wtool4_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool4.gif' name='editImage4' width='84' height='22' border='0' id='editImage4'></a></td>
										</tr>
										<tr>
										  <td height='2'></td>
										</tr>
										<tr>
										  <td height='27' align='center' valign='top'><a href='javascript:doSize();' onMouseOver=\"MM_swapImage('editImage11','','../webedit/image/wtool11_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool11.gif' name='editImage11' width='84' height='22' border='0' id='editImage11'></a></td>
										</tr>
									  </table>
										 </td>
									<td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
									<td width='20%'>
											<table width='100%' border='0' cellspacing='0' cellpadding='0'>
										<tr>
										  <td height='27' align='center' valign='bottom'><a href='javascript:doForcol();' onMouseOver=\"MM_swapImage('editImage5','','../webedit/image/wtool5_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool5.gif' name='editImage5' width='95' height='22' border='0' id='editImage5'></a></td>
										</tr>
										<tr>
										  <td height='2'></td>
										</tr>
										<tr>
										  <td height='27' align='center' valign='top'><a href='javascript:doBgcol();' onMouseOver=\"MM_swapImage('editImage12','','../webedit/image/wtool12_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool12.gif' name='editImage12' width='95' height='22' border='0' id='editImage12'></a></td>
										</tr>
									  </table>
										 </td>
									<td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
									<td width='18%'>
											<table width='100%' border='0' cellspacing='0' cellpadding='0'>
										<tr>
										  <td height='27' align='center' valign='bottom'><a href='javascript:doImage();' onMouseOver=\"MM_swapImage('editImage6','','../webedit/image/wtool6_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool6.gif' name='editImage6' width='73' height='22' border='0' id='editImage6'></a></td>
										</tr>
										<tr>
										  <td height='2'></td>
										</tr>
										<tr>
										  <td height='27' align='center' valign='top'><a href='javascript:doTable();' onMouseOver=\"MM_swapImage('editImage13','','../webedit/image/wtool13_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool13.gif' name='editImage13' width='73' height='22' border='0' id='editImage13'></a></td>
										</tr>
									  </table>
										 </td>
									<td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
									<td width='25%'>
									<table width='100%' border='0' cellspacing='0' cellpadding='0'>
										<tr>
										  <td height='27' align='center' valign='bottom'><a href='javascript:doLink();' onMouseOver=\"MM_swapImage('editImage7','','../webedit/image/wtool7_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool7.gif' name='editImage7' width='74' height='22' border='0' id='editImage7'></a></td>
										</tr>
										<tr>
										  <td height='2'></td>
										</tr>
										<tr>
										  <td height='27' align='center' valign='top'><a href='javascript:doMultilink();' onMouseOver=\"MM_swapImage('editImage14','','../webedit/image/wtool14_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool14.gif' name='editImage14' width='111' height='22' border='0' id='editImage14'></a></td>
										</tr>
									  </table>
										 </td>
								  </tr>
								 </table>
							  </td>
							</tr>
						  </table>
						  <input type='hidden' name='content' value=''>
						  <input type='hidden' name='text' value=''>
						  <iframe align='right' id='iView' style='width: 100%; height:310;' scrolling='YES' hspace='0' vspace='0'></iframe>
						  <textarea name='mail_text'  style='display:none'>".$mail_text."</textarea>
						  <!-- html편집기 메뉴 종료 -->
				  </td>
				</tr>
				<tr>
				  <td align='right' nowrap>&nbsp;
								<a href='javascript:doToggleText();' onMouseOver=\"MM_swapImage('editImage15','','../webedit/image/bt_html_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_html.gif' name='editImage15' width='93' height='23' border='0' id='editImage15'></a>
					  <a href='javascript:doToggleHtml();' onMouseOver=\"MM_swapImage('editImage16','','../webedit/image/bt_source_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_source.gif' name='editImage16' width='93' height='23' border='0' id='editImage16'></a>
				  </td>
				</tr>
			  </table>
			</td>
		  </tr>
		</table>
		<tr>
			<td align=right style='padding-bottom:75px;'>
			<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle valign='top'>
			<a href='email_manage.php'><img src='../images/".$admininfo["language"]."/b_cancel.gif' align=absmiddle  border=0 valign='top'></a>
			</td>
		</tr>

          ";


$Contents .= "
		</form>
    </td>
  </tr>";
if($mail_ix){
if(false){
$Contents .= "
    <form name='mail_send' method='post' onSubmit=\"return SendMailCheck(this)\" action='email_manage.act.php' target='act'><!---->
    <input type='hidden' name=act value='send'>
    <input type='hidden' name='mail_content' value=''>
    <input type='hidden' name=mail_ix value='$mail_ix'>
    <tr>
	    <td align='left' style='padding:20 0 10 0;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5 5 5 10;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u> 메일보내기</b></div>")."</td>
	  </tr>
    <tr>
    	<td bgcolor=#F8F9FA style='padding:10px;' align=center>
    	<table width='100%' cellpadding=3 cellspacing=1 border='0' bgcolor='#c0c0c0' >
			  <tr height=28 bgcolor=#ffffff>
          <td width='20%'  align=left class='leftmenu' nowrap><img src='../image/title_head.gif' align=absmiddle> 메일 제목</td>
          <td><input class='input' type='text' name='mail_title' value='".$db->dt[mail_title]."' maxlength='50' style='font-weight:bold;border:0px;width:100%' readonly></td>
        </tr>
			  <tr bgcolor=#ffffff >
			    <td width=150 class='leftmenu'><img src='../image/title_head.gif' align=absmiddle> 타겟군 선택 : </td>
			    <td>
			    <!--input type='radio' name='target_type' value=''-->
			    	".mailTargetSelect($mt_ix)."
			    </td>
			  </tr>
			  <tr bgcolor=#ffffff >
			    <td width=150 class='leftmenu'><img src='../image/title_head.gif' align=absmiddle> 한번에 발송할 수량 : </td>
			    <td>
			    <select name=max>
							<option value='5' >5</option>
							<option value='10'  >10</option>
							<option value='20' >20</option>
							<option value='50' >50</option>
							<option value='100' selected>100</option>
							<option value='200' >200</option>
							<option value='300' >300</option>
							<option value='400' >400</option>
							<option value='500' >500</option>
							<option value='1000' >1000</option>
						</select>
						<span class=small>스트립트 타임아웃 때문에 한번에 너무 많은 양을 보내면 오류가 날수 있습니다.</span>
			    </td>
			  </tr>
			  <tr bgcolor=#ffffff >
			    <td width=150 class='leftmenu'><img src='../image/title_head.gif' align=absmiddle> 진행상태 : </td>
			    <td>
			    전체 발송수량 : <b id='total_mail_cnt'> - </b>&nbsp;&nbsp;
			    현재 발송 완료 수량 : <b id='sended_mail_cnt'> - </b>&nbsp;&nbsp;
			    남은 발송수량 : <b id='remainder_mail_cnt'> - </b>
			    </td>
			  </tr>
			 </table>
    	</td>
    </tr>
    <tr bgcolor=#F8F9FA height=40>
    	<td>
    	<table width=100%>
	    <td align=left style='padding:0 10 10 10;'>
	    	위 메일 내용과 선택하신 타겟군으로 메일발송을 합니다
	    </td>
	    <td align=right style='padding:0 10 10 10;'>
	    	<input type=image src='../image/btn_send.gif' align=absmiddle>
	    </td>
	    </table>
	    </td>
	  </tr>
	  </form>";
}
$Contents .= "
  <tr>
    <td align='left'>";

if(!$max){
	$max = 15; //페이지당 갯수
}

if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}


$sql = 	"SELECT count(*) as total FROM shop_mailling_history where mail_ix = '$mail_ix' order by regdate desc ";
$db->query($sql);
$db->fetch();
$total = $db->dt[total];

$sql = "select mh.mh_ix, mh.sended_mail, mh.regdate, mh.mail_open, mh.mail_click, mh.open_date, ml.user_name, com_name, phone
			from shop_mailling_history mh
			left join shop_addressbook ml on mh.ab_ix = ml.ab_ix and mail_ix = '$mail_ix'
			where mail_ix = '$mail_ix' and mh.ab_ix = ml.ab_ix and mh.ab_ix != '0'
			union
			select mh.mh_ix, mh.sended_mail, mh.regdate, mh.mail_open, mh.mail_click, mh.open_date,
			AES_DECRYPT(UNHEX(IFNULL(cmd.name,'-')),'".$db->ase_encrypt_key."')  as user_name ,
			AES_DECRYPT(UNHEX(IFNULL(ccd.com_name,'-')),'".$db->ase_encrypt_key."') as com_name,
			AES_DECRYPT(UNHEX(IFNULL(cmd.tel,'-')),'".$db->ase_encrypt_key."')  as phone
			from shop_mailling_history mh
			left join common_member_detail cmd on mh.ucode = cmd.code  and mail_ix = '$mail_ix'
			left join common_user cu on mh.ucode = cu.code
			left join common_company_detail ccd on cu.company_id = ccd.company_id
			where mail_ix = '$mail_ix' and mh.ucode != ''
			limit $start , $max ";



//echo nl2br($sql);
		$db->query($sql);

$Contents .= "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'>
	  <tr>
	    <td align='left' colspan=11> ".colorCirCleBox("#efefef","100%","<div style='padding:5 5 5 10;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  메일발송  목록 (총 : ".number_format($total)." 개)</b></div>")."</td>
	  </tr>
	 <form name='listform' action='email_manage.act.php' method='POST' onsubmit='return SelectDelete(this);' target='act'><input type='hidden' name='act' value='history_select_delete'>
	  <tr height=10><td colspan=8 ></td></tr>
	  <tr height=25 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td ><input type=checkbox class=nonborder id='all_fix' name='all_fix' onclick='fixAll(document.listform)'></td>
	    <td style='width:50px;'> no</td>
	    <td style='width:200px;'> 회사명</td>
	    <td style='width:100px;'> 담당자</td>
	    <td style='width:100px;'> 일반전화</td>
	    <td style='width:150px;'> 이메일</td>
	    <td style='width:40px;'> 오픈</td>
	    <td style='width:40px;'> 방문</td>
	    <td style='width:100px;'> 수신일시</td>
	    <td style='width:100px;'> 발송일시</td>
	    <td style='width:50px;'> 관리</td>
	  </tr>";




if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);

	$no = $total - ($page - 1) * $max - $i;

	$Contents .= "

		  <tr bgcolor=#ffffff height=30 align=center>
		  	<td bgcolor='#ffffff'><input type=checkbox class=nonborder id='mh_ix' name='mh_ix[]' value='".$db->dt[mh_ix]."|".$db->dt[email]."'></td>
		    <td align=center>".$no."</td>
		    <td align=left>".$db->dt[com_name]."</td>
		    <td onclick=\"show_mailling_info(document.getElementById('mailling_info_".$db->dt[mh_ix]."'))\" style='cursor:hand;'>".$db->dt[user_name]."</td>
		    <td>".$db->dt[phone]."</td>
		    <td>".$db->dt[sended_mail]."</td>
		    <td>".($db->dt[mail_open] == "0" ? "×":"○")."</td>
		    <td>".($db->dt[mail_click] == "0" ? "×":"○")."</td>
		    <td>".$db->dt[open_date]."</td>
		    <td nowrap>".$db->dt[regdate]."</td>
		    <td>
	    		<a href=\"javascript:deleteMailHistory('history_delete','".$db->dt[mh_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
		    </td>
		  </tr>
		  <tr height=70 style='display:none' id='mailling_info_".$db->dt[mh_ix]."'>
		  	<td colspan=10 style='padding:5px;'>
		  	팩스 : ".$db->dt[fax]."<br>
		  	일반전화 : ".$db->dt[phone]."<br>
		  	핸드폰 : ".$db->dt[mobile]."<br>
		  	홈페이지 : ".$db->dt[homepage]."<br>
		  	회사주소 : ".$db->dt[com_address]."<br>
		  	</td>
		  </tr>	  ";
	}
}else{
	$Contents .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=10>발송된 메일 발송 목록이 없습니다 </td>
		  </tr>  ";
}
$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
$Contents .= "
	  <tr height=50><td colspan=10 ><input type=image src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0></td></tr>
	   <tr height=50><td colspan=10 align=center>".page_bar($total, $page, $max,$query_string,"")."</td></tr>
    </table>

    </td>
  </tr>
  </form>";
}
$Contents .= "
</table>
<!--iframe id='act' frameborder='0' scrolling='no' width='0' height='0' src=''></iframe-->
<form name='lyrstat'><input type='hidden' name='opend' value=''></form>";


$Script = "<script language='JavaScript' src='../webedit/webedit.js'></script>\n$Script";
$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "리셀러관리 > 리셀러설정 > 이메일설정";
$P->title = "이메일설정";
$P->OnloadFunction = "init();Init(document.INPUT_FORM);"; //showSubMenuLayer('storeleft');
$P->strLeftMenu = reseller_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>