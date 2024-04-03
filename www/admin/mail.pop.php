<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include("./class/layout.class");


$Script = "
<script language='JavaScript' src='./ckeditor/ckeditor.js'></script>
<script language='JavaScript' >
function CheckMail(frm){
	//frm.content.value = iView.document.body.innerHTML;
}

function CheckSearch(frm){
	if(frm.search_text.value.length < 1){
		alert('검색어를 입력해주세요');
		return false;
	}
}

$(document).ready(function() {
	//Init(document.send_mail);

	CKEDITOR.replace('content',{
		startupFocus : false,height:200
	});

});

</Script>";

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center >
		<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>

			<!--tr><td  align=left class='top_orange'  ></td></tr>
			<tr height=35 bgcolor=#efefef>
				<td  style='padding:0 0 0 0;'>
					<table width='100%' border='0' cellspacing='0' cellpadding='0' >
						<tr>
							<td width='10%' height='31' valign='middle' style='font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding-left:10px;' nowrap>
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 메일보내기
							</td>
							<td width='90%' align='right' valign='top' >
								&nbsp;
							</td>
						</tr>
					</table>
				</td>
			</tr-->
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("메일 보내기", "회원관리 > 메일 보내기", false)."</td>
			</tr>
			<tr height=30><td class='p11 ls1' style='padding:0 0 0 20px;' align='left'> - 찾으실 아이디 또는 이름을 입력하세요.</td></tr>
			<tr>
				<td align=center style='padding-bottom:10px;'>
				<form name='z' method='post'  action='mail.pop.php'  onSubmit='CheckSearch(this)'>
				<input type='hidden' name='act' value='search'>
					<table class='box_shadow' style='width:99%;' cellpadding=0 cellspacing=0 >
						<tr>
							<th class='box_01'></th>
							<td class='box_02'></td>
							<th class='box_03'></th>
						</tr>
						<tr>
							<th class='box_04'></th>
							<td class='box_05' align=center>
								<table border='0' width='100%' cellspacing='1' cellpadding='0'>
									<tr height='30' valign='middle'>
										<td align='center' width='70'><b>회원검색</b></td>
										<td align='center' >
											<input type='radio'  name='search_type' value='id'> 아이디
											<input type='radio'  name='search_type' value='name' checked> 이름
										</td>
										<td>
											<input type='text' class=textbox name='search_text' size='20' value=''>
										</td>
										<!--td>	<input type='image' src='/data/sample/templet/basic//images/bt_search.gif' ></td-->
										<td>	<input type='image' src='./images/".$admininfo["language"]."/btn_search.gif' align=absmiddle></td>
									</tr>
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
				</form>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr><form name='send_mail' method='post' action='mail.act.php' onsubmit='return CheckMail(this);' ><input type=hidden name='act' value='send_mail' >
		<td style='padding:0 5px 0 5px'>
		<select name='mails[]' style='height:60px;width:100%;border:1px solid silver;' multiple>";

$db = new Database;

if($code != ""){
	if($db->dbms_type == "oracle"){
		$db->query("select cu.code, cu.id, AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name, AES_DECRYPT(cmd.mail,'".$db->ase_encrypt_key."') as mail from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd where cu.code = cmd.code and cu.code = '$code' ");
	}else{
		$db->query("select cu.code, cu.id, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd where cu.code = cmd.code and cu.code = '$code' ");
	}
}else{

	if($search_type && $search_text){

		if($search_type=="id")		$search_str = " and $search_type LIKE '%$search_text%' ";
		else									$search_str = " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";

		if($db->dbms_type == "oracle"){
			$db->query("select cu.code, cu.id, AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name, AES_DECRYPT(cmd.mail,'".$db->ase_encrypt_key."') as mail from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd where cu.code = cmd.code $search_str ");
		}else{
		$db->query("select cu.code, cu.id, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd where cu.code = cmd.code $search_str ");
		}
	}
}


if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);
		$Contents .= "<option value='".$db->dt[name]."|".$db->dt[id]."|".$db->dt[mail]."' selected>".$db->dt[name]."(".$db->dt[id].")	&nbsp;&nbsp;&nbsp;".$db->dt[mail]."</option>";
	}
}else{
	if($mail!=""){
		$Contents .= "<option value='".$name."|".$user_id."|".$mail."' selected>".$name.($user_id !="" ? "(".$user_id.")" : "").":	&nbsp;&nbsp;&nbsp;".$mail."</option>";
	}
}
$Contents .= "
		</select>
		</td>
	</tr>
	<tr>
		<td align=center style='padding:10px 5px 0 5px'>
		<table width=100% cellpadding=0 cellspacing=0>
			<tr><td width='10%' height=30><b>제목 :</b> </td><td><input type=text class=textbox name='subject' value='' style='height:20px;width:96%;'></td></tr>
		</table>
		</td>
	</tr>
	<tr>
		<td align=center style='padding:0 5px 0 5px'>
			<textarea name=\"content\" id='content' style='display:none' ></textarea>
		</td>
	</tr>
	<tr>
		<td align=center style='padding:18px 0 0 0'>
			<input type=image src='./images/".$admininfo["language"]."/btn_send_mail_01.png' border=0 align=absmiddle> <a href='javascript:self.close();'><img src='./images/".$admininfo["language"]."/btn_close.gif' border=0 align=absmiddle></a>
		</td>
	</tr></form>
</TABLE>";



$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "회원관리 > 메일 보내기";
$P->NaviTitle = "메일 보내기";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>




