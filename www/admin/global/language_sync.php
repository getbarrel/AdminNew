<?
include("../class/layout.class");


if($max == ""){
	$max = 10; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}



$db = new MySQL;

$db->query("SELECT * FROM global_translation al WHERE  al.trans_ix = '".$trans_ix."'");
$db->fetch();

if($db->total){
	$trans_div = $db->dt[trans_div];
	$trans_type = $db->dt[trans_type];
	$act = "update";
}else{
	$act = "insert";
}

$db = new MySQL;

$where = "where trans_ix is not null " ;
if($trans_div){
	$where .= " and trans_div = '$trans_div' ";
}

if($trans_type){
	$where .= " and trans_type = '$trans_type' ";
}

if($search_text != ""){
	$where .= " and (trans_div LIKE '%".$search_text."%' or text_korea LIKE '%".$search_text."%' or trans_text LIKE '%".$search_text."%' )";
}

$db->query("SELECT count(*) as total FROM global_translation $where  ");
$db->fetch();
$total = $db->dt[total];
$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max","");

$db->query("SELECT * FROM global_translation $where order by regdate desc LIMIT $start, $max ");


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
	<col width='25%' />
	<col width='*' />
	  <tr >
		<td align='left' colspan=2> ".GetTitleNavigation("번역데이타 동기화", "번역설정 > 번역데이타 동기화 ")."</td>
	  </tr>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U") || true){
$Contents01 .= "
	  <tr>
	    <td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>추가하기</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
		<col width='20%' />
		<col width='*' />
	  
	  <tr bgcolor=#ffffff height='34'>
	    <td class='input_box_title'> <b>번역언어 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'> 
		".getTranslationType($trans_type,"")."
		<!--input type=text class='textbox' name='trans_div' value='".$db->dt[trans_div]."' style='width:430px;' validation=true title='메뉴구분'> <span class=small></span-->
		</td>
		<td class='input_box_title'> <b>구분 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'>
		<input type=text class='textbox' name='trans_div' validation=true title='구분' value='".$db->dt[trans_div]."'>
		<span class=small></span>
		</td>
	  </tr>
	  
	  ";
}
$Contents01 .= "
	  </table>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center style='padding:10px 0px'><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
}
$SearchForm = "<form name='search_frm' method='GET'><input type='hidden' name='trans_div' value='$trans_div'>
				<table>
					<tr>
						<td><img src='../image/title_head.gif' align=absmiddle> <b>랭귀지 목록</b></td>
						<td>".getTranslationType($trans_type,"")."</td>
						
						<td><input type='text' class=textbox name='search_text' value='$search_text' ></td>
						<td><input type='image' src='../images/".$admininfo['language']."/btn_search.gif' align=absmiddle></td>
						<td>".number_format($total)." 개 </td>
					</tr>
				</table></form>";

$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
		<col style='width:80px;'>
		<col style='width:70px;'>
		<col style='width:150px;'>
		<col style='width:*;'>
		<col style='width:80px;'>
		<col style='width:120px;'>
		<col style='width:110px;'>
	  <tr>
	    <td align='left' colspan=7 style='padding:4px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:0px 3px 0px 13px;'> $SearchForm </div>")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=7 style='padding-bottom:20px;'>
	    <div class='tab'>
			<table class='s_org_tab'>
			<tr>
				<td class='tab'>
					 

				</td>
				<td style='width:145px;text-align:right;vertical-align:bottom;padding:0px 0px 10px 0'>
					 
				</td>
			</tr>
			</table>
		</div>
	    </td>
	</tr>
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box' style='table-layout:fixed;word-wrap:break-word;'>
		<col style='width:50px;'>
		<col style='width:100px;'>
		<col style='width:100px;'>
		<col style='width:100px;'>
		<col style='width:100px;'>
		<col style='width:250px;'>
		<col style='width:*;'>
		<col style='width:100px;'>
		<col style='width:140px;'>
		<col style='width:140px;'>
	  <tr height=25 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td'> 순</td>
		<td class='m_td'> 언어구분</td>
		<td class='m_td'> 구분</td>		
	    <td class='m_td'> 파일경로</td>
		<td class='m_td'> 파일명</td>
		<td class='m_td'> 한글번역</td>
		<td class='m_td' style='width:*;'> 번역문구</td>
	    <td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>"; 

if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
		    <td class='list_box_td'>".($i+1)."</td>
			<td class='list_box_td list_bg_gray'>".$db->dt[trans_type]."</td>
			<td class='list_box_td '>".$db->dt[trans_div]."</td>			
			<td class='list_box_td '>".$db->dt[file_path]."</td>
			<td class='list_box_td '>".$db->dt[file_name]."</td>
		    <td class='list_box_td point'>".$db->dt[text_korea]."</td>
			<td class='list_box_td' style='padding:10px; text-align:left;line-height:150%;' >".$db->dt[trans_text]."<br>
			<!--a href=\"javascript:copyToClipboard('<?=\$_LANGUAGE[\"".$db->dt[trans_key]."\"];//".$db->dt[trans_text]."?>');\"-->
			\$_LANGUAGE['".$db->dt[trans_key]."'];<br>
			//korean : ".$db->dt[text_korea]."<br>
			//".$db->dt[trans_type]." : ".$db->dt[trans_text]."
			<!--/a-->
			</td>
		    <td class='list_box_td list_bg_gray'>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td '>".$db->dt[regdate]."</td>
		    <td class='list_box_td list_bg_gray'>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents02 .= "<a href=\"?trans_div=".$_GET["trans_div"]."&trans_type=".$_GET["trans_type"]."&search_text=".$_GET["search_text"]."&trans_ix=".$db->dt[trans_ix]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
				//$Contents02 .= "<a href=\"javascript:updateTranslationInfo('".$db->dt[trans_ix]."','".$db->dt[trans_type]."','".$db->dt[trans_div]."','".$db->dt[text_korea]."','".$db->dt[text_english]."','".$db->dt[disp]."')\"><img src='../image/btc_modify.gif' border=0></a>";
			}else{
				$Contents02 .= "<a href=\"javascript:alert('수정권한이 없습니다.');\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$Contents02 .= "<a href=\"javascript:deleteTranslationInfo('delete','".$db->dt[trans_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}else{
				$Contents02 .= "<a href=\"javascript:alert('삭제권한이 없습니다.')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}
			$Contents02 .= "
		    </td>
		  </tr> ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=7>등록된 랭귀지 목록이 없습니다. </td>
		  </tr> ";
}
$Contents02 .= "</table>";


$ContentsDesc02 = "
<table width='660' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td align=left style='padding:10px;'>
		<img src='../image/emo_3_15.gif' align=absmiddle>  거래은행 및 계좌 정보는 결제시 이용됩니다. 정확하게 입력해주세요
	</td>
</tr>
</table>
";


$Contents02 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0'  >
<tr>
	<td align=left style='padding:10px;'>
		".$str_page_bar."
	</td>
</tr>
</table>
";



$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='bank_form' action='translation.act.php' method='post' onsubmit='return CheckFormValue(this)' style='display:inline;'>
<input name='act' type='hidden' value='language_sync'>
<input name='trans_ix' type='hidden' value='$trans_ix'>";
$Contents = $Contents."<tr><td>".$Contents01."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";

 $Script = "
 <script language='javascript'>
 function updateTranslationInfo(trans_ix,trans_type,trans_div,text_korea, text_english ,disp){
 	var frm = document.bank_form;

 	frm.act.value = 'update';
 	frm.trans_ix.value = trans_ix;
 	frm.trans_type.value = trans_type;
 	frm.trans_div.value = trans_div;
 	frm.text_korea.value = text_korea;
	frm.text_english.value = text_english;
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}
	//frm.bank_name.focus();

}

 function deleteTranslationInfo(act, trans_ix){
 	if(confirm('해당랭귀지 목록을 정말로 삭제하시겠습니까?')){
 		var frm = document.bank_form;
 		frm.act.value = act;
 		frm.trans_ix.value = trans_ix;
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

function copyToClipboard(text)
{
    if (window.clipboardData) // Internet Explorer
    {  
        window.clipboardData.setData('Text', text);
    }
    else
    {  
        unsafeWindow.netscape.security.PrivilegeManager.enablePrivilege(\"UniversalXPConnect\");  
        const clipboardHelper = Components.classes[\"@mozilla.org/widget/clipboardhelper;1\"].getService(Components.interfaces.nsIClipboardHelper);  
        clipboardHelper.copyString(text);
    }
}

 </script>
 ";


$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = global_menu();
$P->Navigation = "번역설정 > 다국어지원 > 번역데이타 동기화";
$P->title = "번역데이타 동기화";
$P->strContents = $Contents;
echo $P->PrintLayOut();



/*

CREATE TABLE IF NOT EXISTS `global_translation` (
  `trans_ix` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `trans_div` varchar(255) DEFAULT NULL COMMENT '문자열구분',
  `trans_type` varchar(20) DEFAULT 'english' COMMENT '언어',
  `text_name` varchar(255) DEFAULT NULL COMMENT '문자열 명칭',
  `text_korea` mediumtext COMMENT '한글 문자열',
  `trans_text` mediumtext COMMENT '번역한 문자열',
  `disp` char(1) DEFAULT '1' COMMENT '사용여부',
  `regdate` datetime NOT NULL COMMENT '등록일',
  PRIMARY KEY (`trans_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='프론트 번역 사전'

*/
?>