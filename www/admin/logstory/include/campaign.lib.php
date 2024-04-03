<?php

include("../../campaign/mail.config.php");

function getCampaignGroupInfoSelect($obj_id, $obj_txt, $parent_group_ix, $selected, $depth=1, $property=""){

    $mdb = new Database;
    if($depth == 1){
        $sql = 	"SELECT abg.*
				FROM shop_addressbook_group abg
				where group_depth = '$depth' and abg.company_id = '".$_SESSION["admininfo"]['company_id']."'
				 order by vieworder asc";
        //group by group_ix 오라클때문에 제거
    }else if($depth == 2){
        $sql = 	"SELECT abg.*
				FROM shop_addressbook_group abg
				where group_depth = '$depth' and parent_group_ix = '$parent_group_ix' and abg.company_id = '".$_SESSION["admininfo"]['company_id']."'
				 order by vieworder asc ";
        //group by group_ix 오라클때문에 제거
    }
    //echo $sql;
    $mdb->query($sql);

    $mstring = "<select name='$obj_id' id='$obj_id' $property>";
    $mstring .= "<option value=''>$obj_txt</option>";
    if($mdb->total){


        for($i=0;$i < $mdb->total;$i++){
            $mdb->fetch($i);
            if($mdb->dt['group_ix'] == $selected){
                $mstring .= "<option value='".$mdb->dt['group_ix']."' selected>".$mdb->dt['group_name']."</option>";
            }else{
                $mstring .= "<option value='".$mdb->dt['group_ix']."'>".$mdb->dt['group_name']."</option>";
            }
        }

    }
    $mstring .= "</select>";

    return $mstring;
}


function SendCampaignBox($total){
    include_once($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
    include_once($_SERVER["DOCUMENT_ROOT"]."/campaign/mail.config.php");
    include_once($_SERVER["DOCUMENT_ROOT"]."/webedit/webedit.lib.php");

    $sms_design = new SMS;
    if(!$update_kind){
        $update_kind = "sms";
    }

    $help_text = "
		 
		<div id='batch_update_sms' ".($update_kind == "sms" ? "style='display:block'":"style='display:none'")." >
		<div style='padding:14px 0 4px 0'> <b>SMS 일괄발송</b> <span class=small style='color:gray'><!--검색/선택된 회원에게 SMS 를 발송합니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')."</span></div>
		<table cellpadding=0 cellspacing=0>
			<tr>
				<td>
					<table class='box_shadow' style='width:132px;height:120px;'  cellpadding=0 cellspacing=0>
						<tr>
							<th class='box_01'></th>
							<td class='box_02'></td>
							<th class='box_03'></th>
						</tr>
						<tr>
							<th class='box_04'></th>
							<td class='box_05'  valign=top>
								<table cellpadding=0 cellspacing=0><!--CheckSpecialChar(this);-->
							
								<tr><td style='padding-left:5px;'><textarea style='width:106px;height:100px;background-color:#efefef;border:1px solid #e6e6e6;padding:2px;overflow:hidden;' name='sms_text' onkeyup=\"fc_chk_byte(this,80, this.form.sms_text_count);\" ></textarea></td></tr>
								<tr><td height=20 align=right><input type=text name='sms_text_count' style='display:inline;border:0px;text-align:right' size=3 value=0> / 80 byte </td></tr>
								</table>

							</td>
							<th class='box_06'></th>
						</tr>
						<tr>
							<th class='box_07'></th>
							<td class='box_08'></td>
							<th class='box_09'></th>
						</tr>
					</table>
				</td>
			<!--/tr>
			<tr-->";
    $cominfo = getcominfo();
    $help_text .= "
				<td valign=top style='padding:0 0 0 10px'>
					<table cellpadding=0 cellspacing=0><input type=hidden name='sms_send_page' value='1' >
						<tr height=26><td align=left width=90 class=small>보내는사람 : </td><td><input type=text name='send_phone' class=textbox style='display:inline;' size=12 value='".$cominfo['com_phone']."'></td></tr>
						<tr height=22><td align=left class=small>SMS 잔여건수 : </td><td>".$sms_design->getSMSAbleCount($_SESSION["admininfo"])." 건 </td></tr>
						<tr height=22><td align=left class=small>발송수/발송대상 : </td><td><b id='sended_sms_cnt' class=blu>0</b> 건 / <b id='remainder_sms_cnt'>$total</a> 명</td></tr>
						<tr height=22>
								<td align=left class=small>발송수량(1회) : </td>
								<td>
								<select name=sms_max>
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
								</td>
						</tr>
						<tr height=22><td align=left class=small>일시정지 : </td><td><input type='checkbox' name='stop' id='sms_stop'><label for='sms_stop'>정지</label></td></tr>";
    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
        $help_text.="
							<tr height=50><td align=center colspan=2><input type=image src='/admin/images/".$_SESSION["admininfo"]["language"]."/btn_send.gif' border=0> </td></tr>";
    }else{
        $help_text.="
							<tr height=50><td align=center colspan=2><a href=\"".$auth_write_msg."\"><img src='/admin/images/".$_SESSION["admininfo"]["language"]."/btn_send.gif' border=0></a> </td></tr>";
    }
    $help_text.="
					</table>
				</td>
			</tr>
		</table>
		</div>
		<div id='batch_update_sendemail' ".($update_kind == "sendemail" ? "style='display:block'":"style='display:none'")." >
		<div style='padding:4px 0 4px 0'><img src='/admin/images/dot_org.gif' align=absmiddle> <b>email 일괄발송</b> <span class=small style='color:gray'><!--검색/선택된 회원에게 email 을 발송합니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'E')."</span></div>
		<table cellpadding=3 cellspacing=0 width=100%  class='input_table_box'>
			<col width=17%>
			<col width=35%>
			<col width=15%>
			<col width=32%>
			<tr>
				<td class='input_box_title'> <b>이메일 제목</b></td>
				<td class='input_box_item' colspan=3 >
				<table cellpadding=0>
					<tr>
						<td><input type=text name='email_subject' id='email_subject_text'   class=textbox value=''  style='width:250px;height:21px;margin:0px;' /></td>

						<td>
						<input type='radio' name='email_type' id='email_type_new' value='new' ".($email_type == "new" || $email_type == "" ? "checked":"")." onclick=\"LoadEmail('new');\"><label for='email_type_new'>새로작성</label>
						<input type='radio' name='email_type' id='email_type_box' value='box' ".CompareReturnValue("box",$update_kind,"checked")." onclick=\"LoadEmail('box');\"><label for='email_type_box'>기존이메일선택</label>
						</td>
					<!--/tr>
					<tr-->
						<td colspan=2 id='email_select_area' style='padding-left:10px;display:none;'>
						".getMailList("","","display:inline;width:250px;")."
						</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr><td class='input_box_title'> <b>참조</b></td><td class='input_box_item' style='padding-left:7px;' colspan=3> <input type=text name='mail_cc'  class=textbox value='' style='width:350px;height:21px;'> <span class='small blu'><!--콤마(,) 구분으로 이메일을 입력해주세요-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'F')."</span></td></tr>
			<tr height=22><input type=hidden name='email_send_page' value='1'>
				<td class='input_box_title'> <b>발송수/발송대상 </b> </td>
				<td class='input_box_item'><b id='sended_email_cnt' class=blu>0</b> 건 / <b id='remainder_email_cnt'>$total</a> 명</td>
				<td class='input_box_title'> <b>발송수량(1회) </b> </td>
				<td class='input_box_item'>
				<select name=email_max>
					<option value='5' >5</option>
					<option value='10' >10</option>
					<option value='20' >20</option>
					<option value='50' >50</option>
					<option value='100' selected>100</option>
					<option value='200' >200</option>
					<option value='300' >300</option>
					<option value='400' >400</option>
					<option value='500' >500</option>
					<option value='1000' >1000</option>
				</select>
				</td>
			</tr>
			<tr height=22><td class='input_box_title'> <b>일시정지 </b> </td><td class='input_box_item' colspan=3><input type='checkbox' name='email_stop' id='email_stop'><label for='email_stop'>정지</label></td></tr>
			<tr>
				<td bgcolor='#f5f6f5' colspan=4>
					<table cellpadding=0 cellspacing=3 width=100% >
						<tr>
						<td bgcolor='#ffffff'> <input type='hidden' name='mail_content' value=''>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table cellpadding=5 cellspacing=1 width=100% >
			<tr bgcolor=#ffffff>
				<td colspan=2 align=right valign=top style='padding:0px;padding-right:20px;'>
				<a href='javascript:doToggleText();' onMouseOver=\"MM_swapImage('editImage15','','../webedit/image/bt_html_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_html.gif' name='editImage15' width='93' height='23' border='0' id='editImage15'></a>
			<a href='javascript:doToggleHtml();' onMouseOver=\"MM_swapImage('editImage16','','../webedit/image/bt_source_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_source.gif' name='editImage16' width='93' height='23' border='0' id='editImage16'></a>
				</td>
			</tr>
		</table>
		<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
			<tr height=50>
				<td colspan=4 align=center>
					<input type=checkbox name='save_mail' id='save_mail' value='1' align=absmiddle>
					<label for='save_mail'>메일함에 저장하기</label>";
    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
        $help_text.="
						<input type=image src='/admin/images/".$_SESSION["admininfo"]["language"]."/btn_send.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle >";
    }else{
        $help_text.="
						<a href=\"".$auth_write_msg."\"><img src='/admin/images/".$_SESSION["admininfo"]["language"]."/btn_send.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle ></a>";
    }
    $help_text.="
				</td>
			</tr>
		</table>
		</div>
		<div id='batch_update_hotcon' ".($update_kind == "hotcon" ? "style='display:block'":"style='display:none'")." >
		<div style='padding:4px 0 4px 0'><img src='/admin/images/dot_org.gif'> <b>기프티콘 일괄발송</b> <span class=small style='color:gray'><!--이벤트 코드와 기프티콘 상품코드를 입력해주세요-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'G')."</span></div>
		<table cellpadding=3 cellspacing=0 width=100% class='input_table_box'>
			<col width=170>
			<col width=*>
			<tr><td class='input_box_title'> <b>기프티콘 이벤트코드</b></td><td class='input_box_item'> <input type=text name='hotcon_event_id'  class=textbox value='' onkeydown='onlyNumber(this)' onkeyup='onlyNumber(this)'  style='width:150' > <span class='small blu'><!--4자리 이벤트 코드를 입력해주세요-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'H')."</span></td></tr>
			<tr>
				<td class='input_box_title'> <b>기프티콘 상품코드</b></td>
				<td class='input_box_item'> <input type=text name='hotcon_pcode'  class=textbox value='' style='width:250' > <span class='small blu'><!--10 자리 기프티콘 상품 코드를 입력해주세요-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'I')."</span></td></tr>
			<tr>
				<td class='input_box_title'> <b>발송수/발송대상 : </b><input type=hidden name='hotcon_send_page' value='1'></td>
				<td class='input_box_item'><b id='sended_hotcon_cnt' class=blu>0</b> 건 / <b id='remainder_hotcon_cnt'>$total</a> 명</td>
			</tr>
			<tr>
					<td class='input_box_title'> <b>발송수량(1회) : </b></td>
					<td class='input_box_item'>
					<select name=hotcon_max>
						<option value='5' >5</option>
						<option value='10' >10</option>
						<option value='20' >20</option>
						<option value='50' >50</option>
						<option value='100' selected>100</option>
						<option value='200' >200</option>
						<option value='300' >300</option>
						<option value='400' >400</option>
						<option value='500' >500</option>
						<option value='1000' >1000</option>
					</select>
					</td>
			</tr>
			<tr><td class='input_box_title'> <b>일시정지 :</b> </td><td class='input_box_item'><input type='checkbox' name='stop' id='hotcon_stop'><label for='hotcon_stop'>정지</label></td></tr>
		</table>
		<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
			<tr height=50>
			<td valign=top style='padding:0 0 0 10px'>
					<table cellpadding=0 cellspacing=0>

					</table>";
    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
        $help_text.="
			<td colspan=4 align=center><input type=image src='/admin/images/".$_SESSION["admininfo"]["language"]."/btn_send.gif' border=0 style='cursor:pointer;border:0px;' ></td>";
    }else{
        $help_text.="
			<td colspan=4 align=center><a href=\"".$auth_write_msg."\"><img src='/admin/images/".$_SESSION["admininfo"]["language"]."/btn_send.gif' border=0 style='cursor:pointer;border:0px;' ></a></td>";
    }
    $help_text.="
			</tr>
		</table>
		</div>
		";

    $select = "
		<select name='update_type' >
							<option value='2'>선택한회원 전체에게</option>
							<option value='1'>검색한 회원 전체에게</option>
						</select>";
    if($list_type == "addressbook_list"){
        $select .= "
						<input type='radio' name='update_kind' id='update_kind_group' value='group' ".CompareReturnValue("group",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_group');\"><label for='update_kind_group'>주소록 그룹 일괄변경</label>";
        $mstring .= "".HelpBox($select, $help_text,300)."</form>";
    }else{
        $select .= "
						<input type='radio' name='update_kind' id='update_kind_sms' value='sms' ".CompareReturnValue("sms",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_sms');\"><label for='update_kind_sms'>SMS 일괄발송</label>
						<input type='radio' name='update_kind' id='update_kind_sendemail' value='sendemail' ".CompareReturnValue("sendemail",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_sendemail');\"><label for='update_kind_sendemail'>이메일 일괄발송</label>
						<!--input type='radio' name='update_kind' id='update_kind_hotcon' value='hotcon' ".CompareReturnValue("hotcon",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_hotcon');\"><label for='update_kind_hotcon'>기프티콘 일괄발송</label-->";
        $mstring .= "".HelpBox($select, $help_text,400)."</form>";
    }

    $mstring .= "<script language='JavaScript' src='/admin/ckeditor/ckeditor.js'></script>\n
<script language='javascript'>
$(document).ready(function() {
  CKEDITOR.replace('mail_content',{
	  startupFocus : false,height:300
	  });

	$('select#email_subject_select').change(function(){
		if($(this).val() != ''){
			$.ajax({
				type: 'GET',
				data: {'act': 'mail_info', 'mail_ix': $(this).val()},
				url: '../../campaign/mail.act.php',
				dataType: 'json',
				async: true,
				beforeSend: function(){

				},
				success: function(mail_info){
					//alert(mail_info.mail_text);
					$('#email_subject_text').val(mail_info.mail_title);
					//document.getElementById('iView').contentWindow.document.body.innerHTML = mail_info.mail_text;
					
					$('input[name=mail_content]').val(mail_info.mail_text);	
					//alert(mail_info);
					//$('#row_'+wl_ix).slideRow('up',500);
				}
			});
		}
	});
});
function BatchSubmit(frm){

	if(frm.update_type.value == 1){
		if(frm.search_searialize_value.value.length < 1){
			alert('적용대상중 [검색회원전체]는  검색후 사용 가능합니다. 확인후 다시 시도해주세요');
			return false;
		}

	}else if(frm.update_type.value == 2){
		var code_checked_bool = false;
		for(i=0;i < frm.code.length;i++){
			if(frm.code[i].checked){
				code_checked_bool = true;
			}
			//	frm.code[i].checked = false;
		}
		if(!code_checked_bool){
			alert('선택된 수신자가 없습니다. 변경/발송 하시고자 하는 수신자를 선택하신 후 저장/보내기 버튼을 클릭해주세요');
			return false;
		}
	}

	if(frm.update_kind.value == 'group'){
		if(frm.parent_update_group_ix.value == ''){
			alert('변경하시고자 하는 그룹을 선택해주세요');
			frm.parent_update_group_ix.focus();
			return false;
		}

	}else{

		if(frm.update_kind[0].checked){
			if(frm.sms_text.value.length < 1){
				alert('SMS 발송 내역을 입력하신후 보내기 버튼을 클릭해주세요');
				frm.sms_text.focus();
				return false;
			}
		}else if(frm.update_kind[1].checked){

			if(frm.email_subject.value.length < 1){
				alert('이메일 제목을 입력해주세요');
				frm.email_subject.focus();
				return false;
			}

			frm.mail_content.value = iView.document.body.innerHTML;

			if(frm.mail_content.value.length < 1 || frm.mail_content.value == '<P>&nbsp;</P>'|| frm.mail_content.value == '<p>&nbsp;</p>'){ //크롬용
				alert('이메일 내용을 입력하신후 보내기 버튼을 클릭해주세요');
				//frm.mail_content.focus();
				return false;
			}

		}else if(frm.update_kind[2].checked){
			if(frm.hotcon_event_id.value.length < 1){
				alert('기프티콘 이벤트 아이디를 입력해주세요');
				frm.hotcon_event_id.focus();
				return false;
			}

			if(frm.hotcon_pcode.value.length < 1){
				alert('기프티콘 상품코드를 입력해주세요');
				frm.hotcon_pcode.focus();
				return false;
			}
		}
	}

	if(frm.update_type.value == 1){
		if(frm.update_kind.value == 'group'){
			if(confirm('검색회원 전체의 메일링/SMS 그룹변경을 하시겠습니까?')){return true;}else{return false;}
		}else if(frm.update_kind[0].checked){
			if(confirm('검색회원 전체에게 SMS 발송을 하시겠습니까?')){return true;}else{return false;}
		}else if(frm.update_kind[1].checked){
			if(confirm('검색회원 전체에게 이메일발송을 하시겠습니까?')){return true;}else{return false;}
		}
	} else {
		if(frm.update_kind.value == 'group'){

			if(confirm('선택한 회원의 메일링/SMS 그룹변경을 하시겠습니까?')){return true;}else{return false;}
		}else if(frm.update_kind[0].checked){
			if(confirm('선택한 회원에게 SMS 발송을 하시겠습니까?')){return true;}else{return false;}
		}else if(frm.update_kind[1].checked){
			if(confirm('선택한 회원에게 이메일발송을 하시겠습니까?')){return true;}else{return false;}
		}
	}
}

function ChangeUpdateForm(selected_id){
	var area = new Array('batch_update_sendemail','batch_update_hotcon','batch_update_sms');

	for(var i=0; i<area.length; ++i){
		if(area[i]==selected_id){
			document.getElementById(selected_id).style.display = 'block';
			$.cookie('campaign_update_kind', selected_id.replace('batch_update_',''), {expires:1,domain:document.domain, path:'/', secure:0});
		}else{
			document.getElementById(area[i]).style.display = 'none';
		}
	}
}


function LoadEmail(email_type){
	if(email_type == 'new'){
		//$('#email_subject_text').css('display','inline');
		$('#email_select_area').css('display','none');
	}else if(email_type == 'box'){
		//$('#email_subject_text').css('display','none');
		$('#email_select_area').css('display','inline');
	}
}



function clearAll(frm){
		for(i=0;i < frm.code.length;i++){
				frm.code[i].checked = false;
		}
}

function checkAll(frm){
       	for(i=0;i < frm.code.length;i++){
				frm.code[i].checked = true;
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

</script>

	";

    return $mstring;
}