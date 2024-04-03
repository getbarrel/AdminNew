<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include("../class/layout.class");
$script_times["page_start"] = time();
if($max == ""){
	$max = 10; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	if($view == 'innerview'){
		$start = ($page - 1) * $max;
	}else{
		//$start = '0';
		$start = ($page - 1) * $max;
	}
}

$db = new Database;

if($db->dbms_type == "oracle"){
	if($search_type && $search_text){
		if($search_type == "name" || $search_type == "pcs"){
			$search_str = " and AES_DECRYPT(".$search_type.",'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
		}else{
			$search_str = " and $search_type LIKE '%$search_text%' ";
		}
	}
}else{
	if($search_type && $search_text){
		if($search_type == "name" || $search_type == "pcs"){
			$search_str = " and AES_DECRYPT(UNHEX(".$search_type."),'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
		}else{
			$search_str = " and $search_type LIKE '%$search_text%' ";
		}
	}
}

/*
$sql = "SELECT 
			count(*) as total
		FROM
			".TBL_COMMON_USER." cu
			left join ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code
		where 
			1
			and cu.mem_div != 'S'
			$search_str";
$script_times["count_start"] = time();
$db->query($sql);
$script_times["count_end"] = time();
$db->fetch();

$total = $db->dt[total];
*/

$sql = "SELECT
			cu.code
		FROM 
			".TBL_COMMON_USER." cu 
			inner join ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code
		where
			1
			$search_str
			and cu.mem_div != 'S'";
$db->query($sql);
$total = $db->total;

$sql = "SELECT
			cu.code,
			cu.id,
			AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
			AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
			AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs,
			AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."') as tel,
			cu.language,
			cu.auth,
			cu.authorized,
			cu.mem_type,
			cu.language as language_type,
			cmd.level_ix as level,
			cmd.level_msg,
			cmd.sex_div,
			cmd.mem_card,
			cmd.birthday,
			cmd.birthday_div,
			cmd.sms,
			cmd.info,
			cmd.gp_ix,
			AES_DECRYPT(UNHEX(cmd.zip),'".$db->ase_encrypt_key."') as zip,
			AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."') as addr1,
			AES_DECRYPT(UNHEX(cmd.addr2),'".$db->ase_encrypt_key."') as addr2
		FROM 
			".TBL_COMMON_USER." cu 
			inner join ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code
		where
			1
			$search_str
			and cu.mem_div != 'S'
			order by  name asc 
			limit $start, $max";

$db->query($sql);

//echo "$start"." :: "."$max";
$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&company_id=$company_id&sdate=$sdate&edate=$edate&search_type=$search_type&search_text=$search_text","");

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

function SearchCharger(charger_ix, charger_name,id,auth,language_type,authorized,mem_type,level,level_msg,sex_div,mem_card,birthday1,birthday2,birthday3,birthday_div,pcs1,pcs2,pcs3,sms,mail,info,zipcode1,zipcode2,addr1,addr2,tel1,tel2,tel3,gp_ix){

	//alert($('#charger_ix',opener.document).parent().html());
	$('#charger_ix',opener.document).val(charger_ix);
	$('#charger_name',opener.document).val(charger_name);
	$('#member_id',opener.document).val(id);
	$('#auth',opener.document).val(auth);

	$('#language_type',opener.document).val(language_type);
	$('#authorized',opener.document).val(authorized);

	$('#mem_type',opener.document).val(mem_type);
	$('#gp_ix',opener.document).val(gp_ix);
	$('#level_ix',opener.document).val(level);

	$('#level_msg',opener.document).val(level_msg);

	if(sex_div == 'M'){
		$('#sex_div_m',opener.document).attr('checked',true);
	}else if(sex_div == 'W'){
		$('#sex_div_w',opener.document).attr('checked',true);
	}else{
		$('#sex_div_d',opener.document).attr('checked',true);
	}
	
	$('input[name=birthday_yyyy]',opener.document).val(birthday1);
	$('input[name=birthday_mm]',opener.document).val(birthday2);
	$('input[name=birthday_dd]',opener.document).val(birthday3);

	$('input[name=pcs1]',opener.document).val(pcs1);
	$('input[name=pcs2]',opener.document).val(pcs2);
	$('input[name=pcs3]',opener.document).val(pcs3);

	if(sms == '1' ){
		$('#sms_1',opener.document).attr('checked',true);
	}else{
		$('#sms_0',opener.document).attr('checked',true);
	}
	
	if(info == '1'){
		$('#info_1',opener.document).attr('checked',true);
	}else{
		$('#info_0',opener.document).attr('checked',true);
	}

	$('#mail',opener.document).val(mail);
	$('#mem_card',opener.document).val(mem_card);
	
	$('#zip1',opener.document).val(zipcode1);
	$('#zip2',opener.document).val(zipcode2);

	$('#addr1',opener.document).val(addr1);
	$('#addr2',opener.document).val(addr2);
	
	$('input[name=tel1]',opener.document).val(tel1);
	$('input[name=tel2]',opener.document).val(tel2);
	$('input[name=tel3]',opener.document).val(tel3);

	self.close();
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
			<tr height=30><td class='p11 ls1' style='padding:0 0 0 5px;'  align='left' > - 찾으실 회사명 또는 상점명을 입력하세요.</td></tr>
			<tr>
				<td align=center style='padding: 0 5px 0 5px'>
				<form name='search_frm' method='get'  action=''  onSubmit='return CheckSearch(this)'>
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
												<option value='name' ".($search_type == "" || $search_type == 'name'?'selected':'')."> 이름</option>
												<option value='id' ".($search_type == 'id'?'selected':'')."> 아이디 </option>
												<option value='mail' ".($search_type == 'mail'?'selected':'')."> 이메일 </option>
												<option value='phone' ".($search_type == 'phone'?'selected':'')."> 전화번호 </option>
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
	<tr><form name='send_mail' method='post' action='sms.act.php' onsubmit='return CheckSMS(this);' ><input type=hidden name='act' value='send_mail' >
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
		// 코드, 이름,아이디,사용자권한,사용자언어,사용자승인,회원구분,사용자그룹,회원레벨,성별,생년월일,오프라인카드정보,휴대폰,주민번호,sms수신여부,이메일,우편번호,주소,집전화
		$pcs = explode("-",$db->dt[pcs]);
		$pcs1 = $pcs[0];
		$pcs2 = $pcs[1];
		$pcs3 = $pcs[2];
		
		$zipcode = explode("-",$db->dt[zip]);
		$zipcode1 = $zipcode[0];
		$zipcode2 = $zipcode[1];
		
		$birthday = explode("-",$db->dt[birthday]);
		$birthday1 = $birthday[0];
		$birthday2 = $birthday[1];
		$birthday3 = $birthday[2];

		$tel = explode("-",$db->dt[tel]);
		$tel1 = $tel[0];
		$tel2 = $tel[1];
		$tel3 = $tel[2];

		$Contents .= "<tr height=25 style='text-align:center;cursor:pointer;' >
					<td class='list_box_td list_bg_gray'>".$db->dt[id]."</td>
					<td class='list_box_td point' onclick=\"SearchCharger('".$db->dt[code]."','".$db->dt[name]."','".$db->dt[id]."','".$db->dt[auth]."','".$db->dt[language_type]."','".$db->dt[authorized]."','".$db->dt[mem_type]."','".$db->dt[level]."','".$db->dt[level_msg]."','".$db->dt[sex_div]."','".$db->dt[mem_card]."','".$birthday1."','".$birthday2."','".$birthday3."','".$db->dt[birthday_div]."','".$pcs1."','".$pcs2."','".$pcs3."','".$db->dt[sms]."','".$db->dt[mail]."','".$db->dt[info]."','".$zipcode1."','".$zipcode2."','".$db->dt[addr1]."','".$db->dt[addr2]."','".$tel1."','".$tel2."','".$tel3."','".$db->dt[gp_ix]."');\">".$db->dt[name]."</td>
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
	</tr></form>
</TABLE>
";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "회원 검색";
$P->NaviTitle = "회원 검색";
$P->strContents = $Contents;
echo $P->PrintLayOut();
$script_times["page_end"] = time();
//print_r($script_times);

?>





