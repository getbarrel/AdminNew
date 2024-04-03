<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
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


if($target == '4'){
	$target_id = "participation";
}else if($target == '5'){
	$target_id = "participation_1";
}else if($target == '6'){
	$target_id = "participation_2";
}else if($target == '7'){
	$target_id = "participation";
}else if($target == '8'){
	$target_id = "participation_1";
}else if($target == '9'){
	$target_id = "participation_2";
}


$db = new Database;

if($db->dbms_type == "oracle"){
	if($search_type && $search_text){
			$search_str = " and AES_DECRYPT(".$search_type.",'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
	}
}else{
	if($search_type && $search_text){
			$search_str = " and AES_DECRYPT(UNHEX(".$search_type."),'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";

	}
}

if($search_type && $search_text){
	$fromWhere = "  left join ".TBL_COMMON_USER." cu on cmd.code = cu.code  ";
}

$sql = "SELECT  
			count(*) as total
		FROM 
			".TBL_COMMON_MEMBER_DETAIL." cmd
			".$fromWhere."
		where 
			1
			$search_str  ";
$db->query($sql);
$db->fetch();

$total = $db->dt[total];

$sql = "SELECT 
			cu.code, cu.id, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,  
			AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
			AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs
		FROM 
			".TBL_COMMON_USER." cu 
			left join ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code
		where 
			cmd.level_ix !=  '".$target."'
			$search_str
			limit $start, $max";
$db->query($sql);
//			order by  name asc 

$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&company_id=$company_id&sdate=$sdate&edate=$edate&target=$target&search_type=$search_type&search_text=$search_text","");

$Script = "
<script language='JavaScript' >
if(window.dialogArguments){
	var opener = window.dialogArguments;
}else{
	var opener = window.opener;
}

function CheckSMS(frm){

	if(frm.sms_contents.value.length < 1){
		alert('SMS 내용을 입력해주세요');
		return false;
	}

	if(frm.mobiles.value.length < 1){
		alert('SMS 보낼 셀러이 한명이상이어야 합니다.');
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

function SearchCharger(charger_ix, charger_name,target_id,charger_id){
	//alert($('#charger_ix',opener.document).parent().html());
	//	$('#charger_ix',opener.document).val(charger_ix);
	//	$('#charger_name',opener.document).val(charger_name);
		//alert(charger_id);
	if(target_id == 'participation'){
		$('.participation',opener.document).append('<option value=\"'+charger_ix+'\">'+charger_name+'('+charger_id+')'+'</option>');
	}else if(target_id == 'participation_1'){
		$('.participation_1',opener.document).append('<option value=\"'+charger_ix+'\">'+charger_name+'('+charger_id+')'+'</option>');
	}else if(target_id == 'participation_2'){
		$('.participation_2',opener.document).append('<option value=\"'+charger_ix+'\">'+charger_name+'('+charger_id+')'+'</option>');
	}

	//self.close();
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
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 회원 검색
							</td>
							<td width='90%' align='right' valign='top' >
								&nbsp;
							</td>
						</tr>
					</table>
				</td>
			</tr-->
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("회원 검색", "회원 검색", false)."</td>
			</tr>
			<tr height=30>
				<td class='p11 ls1' style='padding:0 0 0 5px;'  align='left' > - 찾으실 회원정보를 입력하세요.</td>
			</tr>
			<tr>
				<td align=center style='padding: 0 5px 0 5px'>
				<form name='z' method='post'  action=''  onSubmit='return CheckSearch(this)'>
				<input type='hidden' name='act' value='search'>
				<input type='hidden' name='company_id' value='".$company_id."'>
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
									<col width='170'>
									<col width='*'>
									<tr height='40' valign='middle'>
										<td align='center'  colspan=2 class='input_box_title'><b>회원 검색</b>
											<select name='search_type'>
												<option value='name' ".($search_type == "name"?"selected":"")."> 이름</option>
												<option value='id' ".($search_type == "id"?"selected":"")."> 아이디 </option>
												<option value='mail' ".($search_type == "mail"?"selected":"")."> 이메일 </option>
												<option value='pcs' ".($search_type == "pcs"?"selected":"")."> 전화번호 </option>
											</select>
										</td>
										<td class='input_box_item' style='padding-left:15px;'>
											<input type='text' class='textbox' name='search_text' size='30' value='".$search_text."'>
											<input type='image' src='../images/".$admininfo['language']."/btn_search.gif' align=absmiddle>
										</td>
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
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> ".($search_text == "" ? "회원 전체 목록입니다.":"'".$search_text ."' 로 검색된 결과 입니다.")."</td>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> </td>
	</tr>
	<tr>
		<form name='send_mail' method='post' action='sms.act.php' onsubmit='return CheckSMS(this);' ><input type=hidden name='act' value='send_mail' >
		<td colspan=2 style='padding: 0 5px 0 5px' width=100% valign=top>
		<table width=100% class='list_table_box'>
		<tr height='28' bgcolor='#ffffff'>
			<td width='20%' align='center' class='m_td'><font color='#000000'><b>아이디</b></font></td>
			<td width='*' align='center' class=m_td><font color='#000000'><b>이름</b></font></td>
			<td width='24%' align='center' class=m_td><font color='#000000'><b>이메일</b></font></td>
			<td width='20%' align='center' class=m_td><font color='#000000'><b>전화번호</b></font></td>
		</tr>";

if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);

	$Contents .= "<tr height=25 style='text-align:center;cursor:pointer;' >
					<td class='list_box_td list_bg_gray'>".$db->dt[id]."</td>
					<td class='list_box_td point' onclick=\"SearchCharger('".$db->dt[code]."','".$db->dt[name]."','".$target_id."','".$db->dt[id]."');\">".$db->dt[name]."</td>
					<td class='list_box_td '>".$db->dt[mail]."</td>
					<td class='list_box_td list_bg_gray'>".$db->dt[pcs]."</td>
				</tr>";
	}
}else{

}

$Contents .= "
		</table>
		</td>
	</tr>
	<tr>
		<td align=center style='padding:0 10px 0 10px' colspan=2>
		</td>
	</tr>
	<tr>
		<td align=center style='padding:10px 0 0 0' colspan=2>
			".$str_page_bar."
		</td>
	</tr>
	</form>
</TABLE>";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "회원 검색";
$P->NaviTitle = "회원 검색";
$P->strContents = $Contents;
echo $P->PrintLayOut();
?>





