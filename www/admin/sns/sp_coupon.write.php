<?
include("../class/layout.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');

$db = new Database;
$db->query("SELECT * FROM ".TBL_SHOP_SP_COUPON." where coupon_ix= '$coupon_ix'");
$db->fetch();

if($db->total){
	$coupon_ix = $db->dt[coupon_ix];
	$coupon_title = $db->dt[coupon_title];
	$coupon_text = $db->dt[coupon_text];
	$coupon_width = $db->dt[coupon_width];
	$coupon_height = $db->dt[coupon_height];
	$coupon_top = $db->dt[coupon_top];
	$coupon_left = $db->dt[coupon_left];
	$coupon_use_sdate = $db->dt[coupon_use_sdate];
	$coupon_use_edate = $db->dt[coupon_use_edate];
	$cid = $db->dt[cid];
	$pid = $db->dt[pid];
	$full = $db->dt[full];
	$disp = $db->dt[disp];
	$act = "update";



	$sDate = date("Y/m/d", mktime(0, 0, 0, substr($db->dt[coupon_use_sdate],4,2)  , substr($db->dt[coupon_use_sdate],6,2), substr($db->dt[coupon_use_sdate],0,4)));
	$eDate = date("Y/m/d",mktime(0, 0, 0, substr($db->dt[coupon_use_edate],4,2)  , substr($db->dt[coupon_use_edate],6,2), substr($db->dt[coupon_use_edate],0,4)));

	$startDate = $coupon_use_sdate;
	$endDate = $coupon_use_edate;

}else{
	$act = "insert";
	$coupon_use_sdate = "";
	$coupon_use_edate = "";
	$disp = "1";

	$next10day = mktime(0, 0, 0, date("m")  , date("d")+10, date("Y"));

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d");
	$eDate = date("Y/m/d",$next10day);

	$startDate = date("Ymd");
	$endDate = date("Ymd",$next10day);
}


$Script = "
<Script Language='JavaScript'>
function SubmitX(frm){
	//alert(iView.document.body.innerHTML);
	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}
	doToggleText(frm);
	//frm.content.value = iView.document.body.innerHTML;
	frm.content.value = document.getElementById('iView').contentWindow.document.body.innerHTML; //kbk
	return true;
}




function init(){
	var frm = document.event_frm;
	Content_Input();
	Init(frm);
	onLoadDate('$sDate','$eDate');
}

function change_cate(obj,element,pid,cname) {
	var cid=obj.value;
	var ch_type=obj.getAttribute('ch_type');
	if(pid!='') var txt='&pid='+pid;
	else var txt='';
	$('iframe[name=act]').attr('src','change_coupon_cate.php?cid='+cid+'&element='+element+'&ch_type='+ch_type+'&cname='+cname+txt);
}
</Script>";


$Contents = "
<table width='100%' border='0' align='left'>
 <tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("SNS 스페셜쿠폰 관리", "SNS 관리 > SNS 스페셜쿠폰 관리")."</td>
</tr>
  <tr>
    <td>

        <form name='event_frm' method='post' onSubmit=\"return SubmitX(this)\" action='sp_coupon.act.php' enctype='multipart/form-data'><input type='hidden' name=act value='$act'><input type='hidden' name=coupon_ix value='$coupon_ix'>
        <table border='0' width='100%' cellspacing='1' cellpadding='0'>
          <tr>
            <td bgcolor='#6783A8'>
              <table border='0' cellspacing='0' cellpadding='0' width='100%'>
                <tr>
                  <td bgcolor='#ffffff'>
                    <table border='0' cellpadding=3 cellspacing=0 width='100%' class='input_table_box'>
                      <tr>
                        <td class='input_box_title'>스페셜쿠폰 제목 <img src='".$required3_path."'></td>
                        <td class='input_box_item'><input  type='text' class='textbox' name='coupon_title' value='".$db->dt[coupon_title]."' maxlength='50' style='width:98%' validation='true' title='스페셜쿠폰 제목'></td>
                      </tr>
                      <tr>
						  <td class='input_box_title'>스페셜쿠폰 기간 <img src='".$required3_path."'></td>
						  <td class='input_box_item' >
							<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
										<tr>
											<TD  nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월 <SELECT name=FromDD></SELECT> 일 </TD>
											<TD width=20 align=center> ~ </TD>
											<TD nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월 <SELECT name=ToDD></SELECT> 일</TD>
										</tr>
									</table>
						  </td>
						</tr>
						<tr>
                      	<td class='input_box_title'>전시여부 <img src='".$required3_path."'></td>
                        <td class='input_box_item'>
                        <input type='hidden' name='pop' value='1'> <input type='radio' name='disp' id='disp_1' value='1' ".CompareReturnValue("1",$disp,"checked")."> <label for='disp_1' >표시</label> <input type='radio' name='disp' id='disp_0' value='0' ".CompareReturnValue("0",$disp,"checked")."><label for='disp_0' >표시하지 않음</label>
												</td>
                      </tr>
                    	<tr>
												<td class='input_box_title'> 쿠폰 선택 </td>
												<td class='input_box_item'>
													<select name=cid style='width:80px;' onChange=\"change_cate(this,'event_frm','','pid')\" ch_type='A'>
														<option value=''>지역선택</option>
														".load_coupon_category($cid)."
													</select>
													 <select name=pid style='width:400px;'>
														<option value=''>쿠폰선택</option>
														".load_coupon_category2($cid,$pid)."
													  </select>
												</td>
											</tr>
                      <tr bgcolor='#F8F9FA'>
                        <td colspan=2>
                 <table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>
                    <tr>
                      <td height='30' colspan='3' style='padding:10px;'>
						      <table id='tblCtrls' width='100%' border='0' cellspacing='1' cellpadding='0' align='center'>
						        <tr>
						          <td bgcolor='F5F6F5' colspan=2>
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
						        <table width='100%' border='0' cellspacing='1' cellpadding='0' align='center'>
							        <tr>
							        	<td colspan='3' bgcolor='#ffffff'>
							        	  <input type='hidden' name='content' value=''>
										      <input type='hidden' name='text' value=''>
										      <iframe align='right' id='iView' frameborder=0 style='width: 100%; height:310px;border:1px solid silver' scrolling='YES' hspace='0' vspace='0'></iframe>
										      <textarea name='coupon_text'  style='display:none'>".$coupon_text."</textarea>
										      <!-- html편집기 메뉴 종료 -->
							        	</td>
							        </tr>
							        <tr>
												<td width='120' height='25' align='center' ></td>
												<td colspan='2' align='right'>&nbsp;
												<a href='javascript:doToggleText();' onMouseOver=\"MM_swapImage('editImage15','','../webedit/image/bt_html_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_html.gif' name='editImage15' width='93' height='23' border='0' id='editImage15'></a>
												<a href='javascript:doToggleHtml();' onMouseOver=\"MM_swapImage('editImage16','','../webedit/image/bt_source_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_source.gif' name='editImage16' width='93' height='23' border='0' id='editImage16'></a>
												</td>
						          </tr>
							      </table>

                      </td>
                    </tr>";

$Contents .= "

                    <tr><td colspan=3 align=right style='padding:10px;'><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle> <a href='sp_coupon.list.php'><img src='../images/".$admininfo["language"]."/b_cancel.gif' border=0 align=absmiddle></a></td></tr>
                  </table>
                        </td>

                      </tr>

                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
        </form>
    </td>
  </tr>

  ";
  /*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >작업시에는 표시하지 않음으로 선택후 작업하시기 바랍니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >기간이 만료된 쿠폰은 자동으로 노출이 종료됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >스페셜쿠폰 미리보기는 변경된 내용을 저장하신 후 사용하셔야 합니다.</td></tr>
</table>
";*/
	$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

//$help_text = HelpBox("이벤트/기획전  관리", $help_text);
$help_text = HelpBox("<table cellpadding='0' cellspacing='0' border='0'><tr><td><b>스페셜쿠폰 관리</b></td></tr></table>", $help_text,120);

$Contents .= "
  <tr>
    <td align='left' style='padding-bottom:200px;'>
    $help_text

    </td>
  </tr>";

$Contents .= "
	</table>


<form name='lyrstat'><input type='hidden' name='opend' value=''></form>
<Script Language='JavaScript'>
init();
</Script>";



$Script = "<script language='JavaScript' src='../js/scriptaculous.js'></script>\n
<script language='JavaScript' src='../js/dd.js'></script>\n
<script language='javascript' src='sp_coupon.write.js'></script>\n
<script language='JavaScript' src='../webedit/webedit.js'></script>\n
<script language='javascript' src='../include/DateSelect.js'></script>\n
$Script";
$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "소셜커머스 > 스페셜쿠폰 > 스페셜쿠폰 등록";
$P->title = "스페셜쿠폰 등록";
$P->OnloadFunction = "MM_preloadImages('../webedit/images/wtool1_1.gif','../webedit/images/wtool2_1.gif','../webedit/images/wtool3_1.gif','../webedit/images/wtool4_1.gif','../webedit/images/wtool5_1.gif','../webedit/images/wtool6_1.gif','../webedit/images/wtool7_1.gif','../webedit/images/wtool8_1.gif','../webedit/images/wtool9_1.gif','../webedit/images/wtool11_1.gif','../webedit/images/wtool13_1.gif','../webedit/images/wtool10_1.gif','../webedit/images/wtool12_1.gif','../webedit/images/wtool14_1.gif','../webedit/images/bt_html_1.gif','../webedit/images/bt_source_1.gif')";//showSubMenuLayer('storeleft');
$P->strLeftMenu = sns_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>