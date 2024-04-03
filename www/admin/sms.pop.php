<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include("./class/layout.class");

$db = new Database;

$Script = "
<script language='JavaScript' >
function CheckSMS(frm){

	if(frm.sms_contents.value.length < 1){
		alert('SMS 내용을 입력해주세요');
		return false;
	}

	if(frm.mobiles.value.length < 1){
		alert('SMS 보낼 회원이 한명이상이어야 합니다.');
		return false;
	}

	return true;
}

function CheckSearch(frm){
	if(frm.search_text.value.length < 1){
		alert('검색어를 입력해주세요');
		return false;
	}
}
</Script>";

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<!--tr><td  align=left class='top_orange'  ></td></tr>
			<tr height=35 bgcolor=#efefef>
				<td  style='padding:0 0 0 0;'>
					<table width='100%' border='0' cellspacing='0' cellpadding='0' >
						<tr>
							<td width='10%' height='31' valign='middle' style='font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding-left:10px;' nowrap>
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> SMS 보내기
							</td>
							<td width='90%' align='right' valign='top' >
								&nbsp;
							</td>
						</tr>
					</table>
				</td>
			</tr-->
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("SMS 보내기", "회원관리 > SMS 보내기", false)."</td>
			</tr>
			<tr height=30><td class='p11 ls1' style='padding:0 0 0 5px;'  align='left' > - 찾으실 아이디 또는 이름을 입력하세요.</td></tr>
			<tr>
				<td align=center style='padding: 0 5px 0 5px'>
				<form name='z' method='post'  action='sms.pop.php'  onSubmit='return CheckSearch(this)'>
				<input type='hidden' name='act' value='search'>
					<table class='box_shadow' style='width:100%;' cellpadding=0 cellspacing=0>
						<tr>
							<th class='box_01'></th>
							<td class='box_02'></td>
							<th class='box_03'></th>
						</tr>
						<tr>
							<th class='box_04'></th>
							<td class='box_05' align=right style='padding: 0 20 0 20'>
								<table border='0' width='100%' cellspacing='0' cellpadding='0' class='input_table_box'>
									<tr height='30' valign='middle'>
										<td align='center'  colspan=2 class='input_box_title'><b>회원검색</b>
											<select name='search_type'>
												<option value='id'> 아이디</option>
												<option value='name' selected> 이름 </option>
												<option value='pcs'> 핸드폰 </option>
											</select>
										</td>
										<td class='input_box_item'>
											<input type='text' class='textbox' name='search_text' size='20' value=''>
											<input type='image' src='./images/".$admininfo['language']."/btn_search.gif' align=absmiddle>
										</td>
										<!--td><input type='image' src='/data/sample/templet/basic/images/bt_search.gif' ></td-->
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
	<tr height=30>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> - 메세지 내용을 <b>80</b> 자 이내료 입력해주세요.</td>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> - 메세지를 보낼 <b>회원 목록</b>입니다.</td>
	</tr>";

if($od_ix){
	$db->query("select pname, option_text from ".TBL_SHOP_ORDER_DETAIL." where od_ix = '".$od_ix."' ");
	$db->fetch();
	//print_r($db->dt);
	$msg = $db->dt[pname].($db->dt[option_text] ? '-'.strip_tags($db->dt[option_text]) : '' ).'이 배송지연되고 있습니다.';
	//echo $msg;
}

$Contents .= "
	<tr><form name='send_mail' method='post' action='sms.act.php' onsubmit='return CheckSMS(this);' ><input type=hidden name='act' value='send_mail' >
		<td style='vertical-align:top;padding: 0 5px 0 5px' width=50%>
		<div style='width:225px;height:146px;border:1px solid silver;'>
		<textarea name='sms_contents' class=textbox cols=20 rows=20 style='border:0px;width:200px;height:116px;'>".$msg."</textarea>
		</div>
		</td>
		<td style='padding: 0 5px 0 5px' width=50% valign=top>
		<div style='width:225px;height:146px;border:1px solid silver;'>
		<select name='mobiles[]' id='mobiles' style='height:132px;width:225px;border:0px;background:transparent;' multiple>";


if($code){
	if(count($code) > 1){
		for($i=0;$i < count($code);$i++){
			if(!$code_str){
				$code_str = " '".$code[$i]."' ";
			}else{
				$code_str .= " ,'".$code[$i]."'";
			}
		}

		$code_str = " and cu.code in ($code_str)";

	}else{
		$code_str = " and cu.code = '$code' ";
	}

	if($db->dbms_type == "oracle"){
		$db->query("select cu.code, cu.id, AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name, AES_DECRYPT(cmd.pcs,'".$db->ase_encrypt_key."') as pcs from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd where cu.code = cmd.code $code_str ");
	}else{
		$sql = "select cu.code, cu.id, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs 
					from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd 
					where cu.code = cmd.code $code_str ";

		//echo nl2br($sql);
		$db->query($sql);
		
	}

}else{
	if($search_type && $search_text){
		$search_str = " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
	

		if($db->dbms_type == "oracle"){
			$db->query("select cu.code, cu.id, AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name, AES_DECRYPT(cmd.pcs,'".$db->ase_encrypt_key."') as pcs from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd where cu.code = cmd.code $search_str ");
		}else{
			$db->query("select cu.code, cu.id, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd where cu.code = cmd.code $search_str ");
		}
	}
}


if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);
		$Contents .= "<option value='".$db->dt[name]."|".$db->dt[id]."|".$db->dt[pcs]."' selected>".$db->dt[name]."(".$db->dt[id].")	&nbsp;&nbsp;&nbsp;".$db->dt[pcs]."</option>";
	}
}else{
	if($pcs!=""){
		$Contents .= "<option value='".$name."|".$user_id."|".$pcs."' selected>".$name.($user_id !="" ? "(".$user_id.")" : "").":	&nbsp;&nbsp;&nbsp;".$pcs."</option>";
	}
}


$Contents .= "
		</select>
		</div>
		</td>
	</tr>
	<tr>
		<td align=center style='padding:0 10px 0 10px' colspan=2>
		</td>
	</tr>
	<tr>
		<td align=center style='padding:10px 0 0 0' colspan=2>
			<input type=image src='./images/".$admininfo["language"]."/btn_send_sms_01.png' border=0 align=absmiddle> <a href='javascript:self.close();'><img src='./images/".$admininfo["language"]."/btn_close.gif' border=0 align=absmiddle></a>
		</td>
	</tr></form>
</TABLE>
";




$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "회원관리 > SMS 보내기";
$P->NaviTitle = "SMS 보내기";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>





