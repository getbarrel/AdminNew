<?
include("../class/layout.class");


$db = new MySQL;
$language_list = getTranslationType("","","array");
$globalInfo = getGlobalInfo();

$db->query("SELECT * FROM global_translation al WHERE  al.trans_ix = '".$trans_ix."'");
$db->fetch();

if($db->total){
	$trans_div = $db->dt[trans_div];
	$trans_type = $db->dt[trans_type];
	$act = "update";
}else{
	$act = "insert";
}


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
	<col width='25%' />
	<col width='*' />
	  <tr >
		<td align='left' colspan=2> ".GetTitleNavigation("번역관리", "상점관리 > 번역관리 ")."</td>
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
		  <tr bgcolor=#ffffff height='34'>
			<td class='input_box_title'> <b>파일경로 <img src='".$required3_path."'></b> </td>
			<td class='input_box_item'>
			<input type=text class='textbox' name='file_path' validation=true title='파일경로' value='".$db->dt[file_path]."'>
			<span class=small></span>
			</td>
			<td class='input_box_title'> <b>파일명 <img src='".$required3_path."'></b> </td>
			<td class='input_box_item'>
			<input type=text class='textbox' name='file_name' validation=true title='파일명' value='".$db->dt[file_name]."'>
			<span class=small></span>
			</td>
		  </tr>
		  <tr bgcolor=#ffffff height='34'>
			<td class='input_box_title'> <b>한글번역 <img src='".$required3_path."'></b> </td>
			<td class='input_box_item' colspan=3>
			<textarea type=text class='textbox' name='text_korea' style='padding:4px;width:97%;height:40px;margin:4px 0px' validation=true title='한글번역'>".$db->dt[text_korea]."</textarea>
			<span class=small></span>
			</td>
		  </tr>
		  <tr bgcolor=#ffffff height='34'>
			<td class='input_box_title' > <b>번역문구 <img src='".$required3_path."'></b> </td>
			<td class='input_box_item' colspan=3>
			<textarea type=text class='textbox' name='trans_text' style='padding:4px;width:97%;height:40px;margin:4px 0px' validation=true title='번역문구'>".$db->dt[trans_text]."</textarea>
			<span class=small></span></td>
		  </tr>
		  <!--tr bgcolor=#ffffff height='34'>
			<td class='input_box_title'> <b>인도네시아어 번역</b> </td>
			<td class='input_box_item'>
			<textarea type=text class='textbox' name='text_indomesian' style='padding:4px;width:700px;height:40px;margin:4px 0px' validation=false title='인도네시아어번역'>".$db->dt[text_indomesian]."</textarea>
			<span class=small></span></td>
		  </tr>
		  <tr bgcolor=#ffffff height='34'>
			<td class='input_box_title'> <b>중국어번역 </b> </td>
			<td class='input_box_item' colspan=3>
			<textarea type=text class='textbox' name='text_chinese' style='padding:4px;width:700px;height:40px;margin:4px 0px' validation=false title='중국어번역'>".$db->dt[text_chinese]."</textarea>
			<span class=small></span></td>
		  </tr-->
		  <tr bgcolor=#ffffff height='34'>
			<td class='input_box_title'> <b>사용유무 <img src='".$required3_path."'></b> </td>
			<td class='input_box_item' colspan=3>
				<input type=radio name='disp' value='1' checked><label for='disp_1'>사용</label>
				<input type=radio name='disp' value='0' ><label for='disp_0'>사용하지않음</label>
			</td>
		  </tr>";
	}
	$Contents01 .= "
		  </table>
	";

	$ButtonString = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff ><td colspan=4 align=center style='padding:10px 0px'><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
	</table>
	";


$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='bank_form' action='translation.act.php' method='post' onsubmit='return CheckFormValue(this)' style='display:inline;'><input name='act' type='hidden' value='".$act."'><input name='trans_ix' type='hidden' value='$trans_ix'>";
$Contents = $Contents."<tr><td>".$Contents01."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."<tr><td>".$Contents02."</td></tr>";
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

$(document).ready(function() {
	$('.trans_text').keyup(function(evt){
		//alert(evt.keyCode);
		var selected_obj = $(this);
		//alert($(this).attr('trans_ix'));
		$.ajax({ 
			type: 'GET', 
			data: {'act': 'trans_text_reg', 'trans_type':selected_obj.attr('trans_type'), 'trans_key':selected_obj.attr('trans_key'), 'trans_ix':selected_obj.attr('trans_ix') , 'trans_text':$(this).val()},
			url: './translation.act.php',  
			dataType: 'text', 
			async: false, 
			beforeSend: function(){ 
					//alert(selected_obj.attr('trans_ix'));
					selected_obj.closest('ul').find('#loading_img').css('display','');
					//alert(selected_obj.closest('ul').html());//find('#loading_img').
			},  
			success: function(result){ 
				//alert(result);
				selected_obj.closest('ul').find('#loading_img').css('display','none');
			}
		});
	});
 

});

 </script>
 ";



$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "글로벌 > 번역 추가하기";
$P->NaviTitle = "번역 추가하기";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>