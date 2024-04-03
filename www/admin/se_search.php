<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include("./class/layout.class");

if($max == ""){
	$max = 10; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

$db = new Database;

if($search_text){
	if($search_type == "cmd.name"){
		$where .= " and AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
	}else{
		$where .= " and $search_type LIKE '%$search_text%' ";
	}
}

	$sql = "SELECT
				distinct ccd.company_id,
				ccd.*,
				AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
				AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs,
				cu.id,
				cu.code,
				csd.minishop_use,
				csd.seller_cid,
				csd.seller_msg,
				ci.cname
			FROM
				common_company_detail as ccd
				inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
				left join common_user as cu on (csd.charge_code = cu.code)
				left join common_member_detail as cmd on (cu.code = cmd.code)
				left join shop_category_info as ci on (csd.seller_cid = ci.cid)
			WHERE 1	and ccd.com_type  = 'S'	$where
			GROUP BY ccd.company_id
			ORDER BY csd.regdate desc
			";

$db->query($sql);
$db->fetch();
$total = $db->total;
	
		$sql = "SELECT
					distinct ccd.company_id,
					ccd.*,
					AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
					AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs,
					cu.id,
					cu.code,
					csd.minishop_use,
					csd.seller_cid,
					csd.seller_msg,
					ci.cname
				FROM
					common_company_detail as ccd
					inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
					left join common_user as cu on (csd.charge_code = cu.code)
					left join common_member_detail as cmd on (cu.code = cmd.code)
					left join shop_category_info as ci on (csd.seller_cid = ci.cid)
				WHERE 1	and ccd.com_type  = 'S'	$where
				GROUP BY ccd.company_id
				ORDER BY csd.regdate desc
				LIMIT $start,$max
				";
	//echo $sql;
	$db->query($sql);

$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&company_id=$company_id&sdate=$sdate&edate=$edate","");

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

function SearchCharger(user_name,user_id,user_tel,user_phone,code,com_name,company_id){
	
	user_tel	=	user_tel.split('-');
	user_phone	=	user_phone.split('-');

	opener.insertData('#md_name".$num."', user_name , '#md_code".$num."' , code);
	opener.insertCominfo('#company_id".$num."', company_id);
	opener.back_show('#md_name".$num."');	
	self.close();
}

function changeBgColor(obj){
	var objTop = obj.parentNode.parentNode;	
	for(j=0;j < objTop.rows.length;j++){
		$(objTop.rows[j]).find('td').each(function(){
			$(this).css('background-color','');	
		});
	}
	$(obj).find('td').css('background-color','#f9ded1');
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
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle>셀러 검색
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
			<tr height=30><td class='p11 ls1' style='padding:0 0 0 5px;'  align='left' > - 찾으실 회원 셀러이름 또는 아이디를 입력하세요.</td></tr>
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
										<td align='center'  colspan=2 class='input_box_title'><b>셀러 검색</b>
											<select name='search_type'>
												<option value='ccd.com_name' ".CompareReturnValue("com_name",$search_type,"selected").">업체명</option>
												<option value='cmd.name' ".CompareReturnValue("com_name",$search_type,"selected")."> 셀러명</option>
												<option value='ccd.com_ceo' ".CompareReturnValue("com_ceo",$search_type,"selected").">대표자명</option>
												<option value='csd.shop_name' ".CompareReturnValue("shop_name",$search_type,"selected").">상호명</option>
											</select>
										</td>
										<td class='input_box_item' style='padding-left:15px;'>
											<input type='text' class='textbox' name='search_text' size='30' value='".$search_text."'>
											<input type='image' src='./images/".$admininfo['language']."/btn_search.gif' align=absmiddle>
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
			<td width='10%' align='center' class='m_td'><font color='#000000'><b>상호명</b></font></td>
			<td width='10%' align='center' class=m_td><font color='#000000'><b>대표자명</b></font></td>
			<td width='14%' align='center' class=m_td><font color='#000000'><b>담당자</b></font></td>
			<td width='10%' align='center' class=m_td><font color='#000000'><b>아이디</b></font></td>
			<td width='10%' align='center' class=m_td><font color='#000000'><b>대표전화번호</b></font></td>
			<td width='10%' align='center' class=m_td><font color='#000000'><b>담당자 전화번호</b></font></td>
		  </tr>";


if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);

		$Contents .= "<tr height=25 style='text-align:center;cursor:pointer;' onclick=\"if($(this).children().css('background-color').replace(/\s/g,'')=='rgb(249,222,209)'){SearchCharger('".$db->dt[name]."','".$db->dt[id]."','".$db->dt[tel]."','".$db->dt[pcs]."','".$db->dt[code]."','".$db->dt[com_name]."','".$db->dt[company_id]."');}else{changeBgColor(this)}\">
								<td class='list_box_td list_bg_gray'>".$db->dt[com_name]."</td>
								<td class='list_box_td point'>".$db->dt[com_ceo]."</td>
								<td class='list_box_td '>".$db->dt[name]."</td>
								<td class='list_box_td list_bg_gray'>".$db->dt[id]."</td>
								<td class='list_box_td '>".$db->dt[com_phone]."</td>
								<td class='list_box_td '>".$db->dt[pcs]."</td>
								</tr>";
	}
}else{
	$Contents .= "
		<tr height=25 style='text-align:center;'> 
			<td colspan='6'>데이터가 없습니다</td>
		</tr>
	";
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

?>





