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


$db = new Database;





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
	
	

}else{
	if($search_type && $search_text){
		if($search_type == "name" || $search_type == "pcs"){
			$search_str = " and AES_DECRYPT(UNHEX(".$search_type."),'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
		}else{
			$search_str = " and $search_type LIKE '%$search_text%' ";
		}
	}

	
}

$db->query("select count(*) as total from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd where cu.code = cmd.code $search_str $code_str  ");
$db->fetch();

$total = $db->dt[total];


$sql = "select cu.code, cu.id, cu.company_id,
			AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, 
			AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs, 
			AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail ,  
			mg.gp_name
			from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_SHOP_GROUPINFO." mg
			where cu.code = cmd.code and cmd.gp_ix = mg.gp_ix and gp_level != 0 $search_str  $code_str
			limit $start , $max";
//echo $sql;

$db->query($sql);

$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&buying_status=$buying_status&sdate=$sdate&edate=$edate","");

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

function SelectMember(mem_ix, mem_name, company_id){
	//alert($('#mem_ix',opener.document).parent().html());
	$('#mem_ix',opener.document).val(mem_ix);
	$('#buying_mem_name',opener.document).val(mem_name);";
	if($mode=="getcominfo"){
		$Script .= "opener.get_com_info(company_id)";
	}
	$Script .= "
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
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 회원검색
							</td>
							<td width='90%' align='right' valign='top' >
								&nbsp;
							</td>
						</tr>
					</table>
				</td>
			</tr-->
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("회원검색", "사입관리 > 회원검색", false)."</td>
			</tr>
			<tr height=30><td class='p11 ls1' style='padding:0 0 0 5px;'  align='left' > - 찾으실 아이디 또는 이름을 입력하세요.</td></tr>	
			<tr>				
				<td align=center style='padding: 0 5px 0 5px'>
				<form name='z' method='post'  action=''  onSubmit='return CheckSearch(this)'>
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
									<col width='170'>
									<col width='*'>
									<tr height='40' valign='middle'>
										<td align='center'  colspan=2 class='input_box_title'><b>회원검색</b>
											<select name='search_type'>
												<option value=''> 회원검색</option>
												<option value='id'> 아이디</option>
												<option value='name' selected> 이름 </option>
												<option value='pcs'> 핸드폰 </option>
											</select>
										</td>									
										<td class='input_box_item' style='padding-left:15px;'>
											<input type='text' class='textbox' name='search_text' size='30' value=''>
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
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> - 메세지 내용을 <b>80</b> 자 이내료 입력해주세요.</td>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> - 메세지를 보낼 <b>회원 목록</b>입니다.</td>
	</tr>	
	<tr><form name='send_mail' method='post' action='sms.act.php' onsubmit='return CheckSMS(this);' ><input type=hidden name='act' value='send_mail' >		
		<td colspan=2 style='padding: 0 5px 0 5px' width=100% valign=top>
		<table width=100% class='list_table_box'>
		<tr height='28' bgcolor='#ffffff'>			
			<td width='8%' align='center' class='m_td'><font color='#000000'><b>그룹</b></font></td>
			<td width='10%' align='center' class=m_td><font color='#000000'><b>이름</b></font></td>
			<td width='10%' align='center' class=m_td><font color='#000000'><b>아이디</b></font></td>
			<td width='14%' align='center' class=m_td><font color='#000000'><b>이메일</b></font></td>
			<td width='10%' align='center' class=m_td><font color='#000000'><b>전화번호</b></font></td>
		  </tr>";


if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);
		
		$Contents .= "<tr height=25 style='text-align:center;cursor:pointer;' onclick=\"SelectMember('".$db->dt[code]."','".$db->dt[name]."','".$db->dt[company_id]."');\">
								<td class='list_box_td list_bg_gray'>".$db->dt[gp_name]."</td>
								<td class='list_box_td point'>".$db->dt[name]."</td>
								<td class='list_box_td list_bg_gray'>".$db->dt[id]."</td>
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
$P->Navigation = "사입관리 > 회원검색";
$P->NaviTitle = "회원검색";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>





