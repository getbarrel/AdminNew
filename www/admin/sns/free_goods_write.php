<?
include("../class/layout.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');

$db = new Database();

if($fp_ix){
	$db->query("SELECT * FROM ".TBL_SNS_FREEPRODUCT." where fp_ix ='$fp_ix' ");
	$db->fetch();
	$act = "update";
	$div_ix = $db->dt[div_ix];
	$fp_title = $db->dt[fp_title];
	$fp_info = $db->dt[fp_info];
	$fp_sdate = $db->dt[fp_sdate];
	$fp_edate = $db->dt[fp_edate];
	$fp_usesdate = $db->dt[fp_usesdate];
	$fp_useedate = $db->dt[fp_useedate];
	$disp = $db->dt[disp];
	$fp_zone = $db->dt[fp_zone];
	$fp_zipcode = $db->dt[fp_zipcode];
	$fp_addr1 = $db->dt[fp_addr1];
	$fp_addr2 = $db->dt[fp_addr2];
	$fp_url = $db->dt[fp_url];
	$fp_count = $db->dt[fp_count];
	$fp_contents = $db->dt[fp_contents];
	$fp_zipcode = explode("-", $fp_zipcode);
	$fp_file = $db->dt[fp_file];

}else{
	$act = "insert";
	$fp_sdate = mktime(0,0,0,date('m'),date('d'),date('Y'));
	$fp_edate = mktime(23,59,59,date('m'),date('d')+1,date('Y'));
	$fp_usesdate = mktime(0,0,0,date('m'),date('d'),date('Y'));
	$fp_useedate = mktime(23,59,59,date('m'),date('d')+1,date('Y'));
	$fp_url = "http://";
}

$Script = "

<link rel='stylesheet' media='all' type='text/css' href='/admin/js/themes/base/ui.all.css' />

<script type='text/javascript' src='/js/ui/jquery-ui-1.8.9.custom.js'></script>
<script type='text/javascript' src='/js/ui/jquery.ui.core.js'></script>
<script type='text/javascript' src='/js/ui/jquery.ui.widget.js'></script>
<script type='text/javascript' src='/js/ui/jquery.ui.mouse.js'></script>
<script type='text/javascript' src='/admin/js/ms_productSearch.js'></script>

<script  id='dynamic'></script>
<Script Language='JavaScript'>


$(function() {
	$(\"#fp_sdate_datepicker\").datepicker({
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		dateFormat: 'yymmdd',
		buttonImageOnly: true,
		buttonText: '달력',
		onSelect: function(dateText, inst){

		}
	});

	$(\"#fp_edate_datepicker\").datepicker({
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		dateFormat: 'yymmdd',
		buttonImageOnly: true,
		buttonText: '달력'
	});

	$(\"#fp_usesdate_datepicker\").datepicker({
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		dateFormat: 'yymmdd',
		buttonImageOnly: true,
		buttonText: '달력',
		onSelect: function(dateText, inst){

		}
	});

	$(\"#fp_useedate_datepicker\").datepicker({
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		dateFormat: 'yymmdd',
		buttonImageOnly: true,
		buttonText: '달력'
	});
});

function init()
{
	Content_Input();
	Init(document.group_frm);
}

function Content_Input(){
	document.group_frm.content.value = document.group_frm.fp_contents.value;
	//alert(document.group_frm.content.value);
}
function zipcode(type) {
	var zip = window.open('../member/zipcode.php?type='+type,'','width=440,height=300,scrollbars=yes,status=no');
}

function ProductInput(frm)
{
	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}

	frm.fp_contents.value = document.getElementById('iView').contentWindow.document.body.innerHTML;
}
</script>
";


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=2> ".GetTitleNavigation("무료 쿠폰등록", "소셜커머스 > 무료 쿠폰등록 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle> <b>무표쿠폰 등록/수정</b></div>")."</td>
	  </tr>
	 </table>
	 <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='search_table_box'>
		<col width='20%' />
		<col width='80%' />
	   <tr>
	    <td class='input_box_title'><b>무료쿠폰 분류 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'>".getFirstDIV($div_ix)."</td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>무료쿠폰제목 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'><input type=text class='textbox' name='fp_title' value='".$fp_title."' title='무료쿠폰제목' validation=true style='width:530px;'> <span class=small></span></td>
	  </tr>
	  <tr>
	    <td class='input_box_title'><b>무료쿠폰 간략설명 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'><input type=text class='textbox' name='fp_info' value='".$fp_info."' title='무료쿠폰 간략설명' validation=true style='width:530px;'> <span class=small></span></td>
	  </tr>
	  <tr>
	    <td class='input_box_title'><b>무료쿠폰 노출기간 <img src='".$required3_path."'></b>  </td>
	    <td class='input_box_item'><input type='text' name='fp_sdate' class='textbox' value='".date('Ymd',$fp_sdate)."' style='width:80px;text-align:center;' id='fp_sdate_datepicker' validation=true title='무료쿠폰시간시작일' /> ~ <input type='text' name='fp_edate' class='textbox' value='".date('Ymd',$fp_edate)."' style='width:80px;text-align:center;' id='fp_edate_datepicker' validation=true title='무료쿠폰시간종료일' /></td>
	  </tr>
	  <tr>
	    <td class='input_box_title'><b>무료쿠폰 사용기간 <img src='".$required3_path."'></b>  </td>
	    <td class='input_box_item'><input type='text' name='fp_usesdate' class='textbox' value='".date('Ymd',$fp_usesdate)."' style='width:80px;text-align:center;' id='fp_usesdate_datepicker' validation=true title='무료쿠폰시간시작일' /> ~ <input type='text' name='fp_useedate' class='textbox' value='".date('Ymd',$fp_useedate)."' style='width:80px;text-align:center;' id='fp_useedate_datepicker' validation=true title='무료쿠폰시간종료일' /></td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> 표시여부  </td>
	    <td class='input_box_item'><input type='radio' name='disp' value='1' ".(($disp == '1' || $disp == '') ? ' checked':'')."> 표시 <input type='radio' name='disp' value='0' ".(($disp == '0') ? ' checked':'')."> 표시하지 않음</td>
	  </tr>
	  <tr>
	    <td class='input_box_title'><b>지역설정 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'>
			<select name='fp_zone' title='지역설정' validation=true>
			<option value=''>선택</option>
			<option value='서울' ".($fp_zone == "서울" ? " selected" : "").">서울</option>
			<option value='경기' ".($fp_zone == "경기" ? " selected" : "").">경기</option>
			<option value='인천' ".($fp_zone == "인천" ? " selected" : "").">인천</option>
			<option value='부산' ".($fp_zone == "부산" ? " selected" : "").">부산</option>
			<option value='대구' ".($fp_zone == "대구" ? " selected" : "").">대구</option>
			<option value='대전' ".($fp_zone == "대전" ? " selected" : "").">대전</option>
			<option value='광주' ".($fp_zone == "광주" ? " selected" : "").">광주</option>
			<option value='충청' ".($fp_zone == "충청" ? " selected" : "").">충청</option>
			<option value='강원' ".($fp_zone == "강원" ? " selected" : "").">강원</option>
			<option value='경상' ".($fp_zone == "경상" ? " selected" : "").">경상</option>
			<option value='전라' ".($fp_zone == "전라" ? " selected" : "").">전라</option>
			<option value='제주' ".($fp_zone == "제주" ? " selected" : "").">제주</option>
			<option value='전국' ".($fp_zone == "전국" ? " selected" : "").">전국</option>
			</select>
		</td>
	  </tr>
	  <tr>
	    <td class='input_box_title'>주소</td>
	    <td class='input_box_item'>
	    	<table border='0' cellpadding='0' cellspacing='0' width=100%>
				<tr>
					<td width=110>
						<input type='text' class='textbox' name='zipcode1' id='zipcode1' value='".$fp_zipcode[0]."' size='4' maxlength='3' readonly title='우편번호' validation='false'> -
						<input type='text' class='textbox' name='zipcode2' id='zipcode2' value='".$fp_zipcode[1]."' size='4' maxlength='3' readonly title='우편번호' validation='false'>
					</td>
					<td width='*' style='padding:0px 0 0 5px;text-align:left'>
						<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('2');\" style='cursor:pointer;'>
					</td>
				</tr>
				<tr height=30>
					<td colspan=2>
						<input type=text name='addr' id='addr' value='".$fp_addr1."' class='textbox'  style='width:95%' title='주소' validation='false'>
					</td>
				</tr>
				<tr>
					<td colspan=2>
	    			<input type=text name='addr2' value='".$fp_addr2."' class='textbox'  style='width:95%'>
					</td>
				</tr>
				</table>

		</td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> 상점URL  </td>
	    <td class='input_box_item'><input type=text class='textbox' name='fp_url' value='".$fp_url."' title='상점URL' validation=true style='width:530px;'> <span class=small></span></td>
	  </tr>
	  <tr>
	    <td class='input_box_title'><b>쿠폰수량 <img src='".$required3_path."'></b>  </td>
	    <td class='input_box_item'><input type=text class='textbox' name='fp_count' value='".$fp_count."' title='쿠폰수량' validation=true style='width:130px;'> <span class=small></span></td>
	  </tr>
	  <tr>
	    <td class='input_box_title'><b>무료쿠폰 E-mail정보 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item' style='padding-bottom:3px;'>
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
			<textarea name=\"fp_contents\"  style='display:none' >".$fp_contents."</textarea>
			  <input type='hidden' name='content' value=''>
			  <iframe align='right' id='iView' style='width: 100%; height:310px;' scrolling='YES' hspace='0' vspace='0'></iframe>
			  <!-- html편집기 메뉴 종료 -->
	  </td>
	</tr>
	</table>
	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
	<tr>
	   <td colspan='2' align='right'>&nbsp;
			  <a href='javascript:doToggleText(document.product_input);' onMouseOver=\"MM_swapImage('editImage15','','../webedit/image/bt_html_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_html.gif' name='editImage15' width='93' height='23' border='0' id='editImage15'></a>
		  <a href='javascript:doToggleHtml(document.product_input);' onMouseOver=\"MM_swapImage('editImage16','','../webedit/image/bt_source_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_source.gif' name='editImage16' width='93' height='23' border='0' id='editImage16'></a>
	  </td>
	</tr>
	</table>
	<table width='100%' border='0' cellspacing='0' cellpadding='0' class='input_table_box'>
		<col width='20%' />
		<col width='80%' />
	  <tr>
	    <td class='input_box_title'><b>썸네일이미지 </b>  </td>
	    <td class='input_box_item'><input type=file class='textbox' name='fp_file' style='width:330px;'> <span class=small>".($fp_file != "" ? "<img src='".$admin_config[mall_data_root]."/images/cupon/".$fp_file."' width='80'>" : "")."</span></td>
	  </tr>
	  </table>";


if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){

$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
}

$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='group_frm' action='free_goods.act.php' method='post' onsubmit='return ProductInput(this)' enctype='multipart/form-data'>
<input name='act' type='hidden' value='$act'>
<input name='fp_ix' type='hidden' value='$fp_ix'>
";

$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";

$Contents = $Contents."</table >";
/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >배너를 등록하신후 치환함수를 이용해 디자인에 적용하실 수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >페이지를 선택하시면 추후 배너관리시 편리합니다.</td></tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');


$Contents .= HelpBox("SNS 무표쿠폰 관리", $help_text,70);


$Script = "
<script type='text/javascript' src='./js/ui/ui.datepicker.js'></script>
<script type='text/javascript' src='./js/ui/jquery-ui-timepicker-addon-0.5.js'></script>
<script language='JavaScript' src='../js/scriptaculous.js'></script>
<script language='JavaScript' src='../webedit/webedit.js'></script>
<script Language='JavaScript' src='../include/zoom.js'></script>
<script Language='JavaScript' src='addoption.js'></script>
<script language='JavaScript' src='../js/dd.js'></script>
<!--script language='JavaScript' src='../js/mozInnerHTML.js'></script-->
<script type='text/javascript' src='../marketting/relationAjaxForEvent.js'></script>
<script Language='JavaScript' src='../include/DateSelect.js'></script>
$Script";

$P = new LayOut();
$P->OnloadFunction = "init();";
$P->addScript = $Script;
$P->strLeftMenu = sns_menu();
$P->Navigation = "소셜커머스 > 무료쿠폰 > 무료쿠폰 등록";
$P->title = "무료쿠폰 등록";
$P->strContents = $Contents;
echo $P->PrintLayOut();



function getFirstDIV($selected=""){
	$mdb = new Database;

	$sql = 	"SELECT *
			FROM ".TBL_SNS_FREEPRODUCT_DIV."
			where disp=1 ";

	$mdb->query($sql);

	$mstring = "<select name='div_ix' id='div_ix' validation=true title='무료쿠폰 분류'>";
	$mstring .= "<option value=''>무료쿠폰 분류</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[div_ix] == $selected){
				$mstring .= "<option value='".$mdb->dt[div_ix]."' selected>".$mdb->dt[div_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[div_ix]."'>".$mdb->dt[div_name]."</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}
?>