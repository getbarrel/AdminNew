<?
include("../class/layout.class");

$Script = "<script language='javascript' >
function idsearch_in(formname,a,b,c,d,e,f,g,h,i,j,k,l,m,n){
	var obj = formname;
	var cArr = c.split('-');
	var fArr = f.split('-');
	var gArr = g.split('-');
	var kArr = k.split('-');

	obj.ucode.value = a;
	obj.name_a.value = b;
	obj.zipcode1.value = cArr[0];
	obj.zipcode2.value = cArr[1];
	obj.addr1.value = d;
	obj.addr2.value = e;
	obj.tel1_a.value = fArr[0];
	obj.tel2_a.value = fArr[1];
	obj.tel3_a.value = fArr[2];
	obj.pcs1_a.value = gArr[0];
	obj.pcs2_a.value = gArr[1];
	obj.pcs3_a.value = gArr[2];
	obj.mail_a.value = h;
	obj.sc_name2.value = i;
	obj.sc_name.value = i;
	obj.sc_number.value = j;
	obj.sc_zip1.value = kArr[0];
	obj.sc_zip2.value = kArr[1];
	obj.sc_addr1.value = l;
	obj.sc_addr2.value = m;
	obj.sc_ceo.value = n;
	
	opener.isEQ_sc();
	self.close();
}
</script>";
$Contents = "

			<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>	
				
				<tr>
					<td align=center>
					<form name='z' method='post'  onSubmit='return CheckFormValue(z);'>
					<input type='hidden' name='act' value='search'>
					
						<table class='box_shadow' style='width:100%;' cellpadding=0>
							<tr>
								<th class='box_01'></th>
								<td class='box_02'></td>
								<th class='box_03'></th>
							</tr>
							<tr>
								<th class='box_04'></th>
								<td class='box_05' align=center>	
								
									<table border='0' width='100%' cellspacing='1' cellpadding='0'>
										<col width='60'>
										<col width='80'>
										<col width='*'>
										<tr height='30' valign='middle'>
											<td align='center' ><b>아이디</b></td>
											<td align='center' >
												<select name=search_type>
													<option value='name' ".CompareReturnValue("name",$search_type,"selected").">고객명</option>
													<option value='id' ".CompareReturnValue("id",$search_type,"selected").">아이디</option>
													<option value='tel' ".CompareReturnValue("tel",$search_type,"selected").">전화번화</option>
													<option value='pcs' ".CompareReturnValue("pcs",$search_type,"selected").">휴대전화</option>
													<option value='com_name' ".CompareReturnValue("com_name",$search_type,"selected").">회사명</option>
													<option value='com_phone' ".CompareReturnValue("com_phone",$search_type,"selected").">회사전화</option>
													<option value='com_fax' ".CompareReturnValue("com_fax",$search_type,"selected").">회사팩스</option>
													<option value='mail' ".CompareReturnValue("mail",$search_type,"selected").">이메일</option>
													<option value='addr1' ".CompareReturnValue("addr1",$search_type,"selected").">주소</option>
											  </select>
											</td>
											<td align='center' >
												<input type=text name='search_text' class='textbox' value='".$search_text."' style='width:100%;font-size:12px;padding:1px;' >
											</td>
											<td>
												<input type='image' src='/admin/image/btn_search.gif' style='border:0px;'>
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
			<div id='comment' align='center'></div>
			<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>
				<tr>
					<td align=center style='padding-top:10px;'>
					<div style='overflow:auto;width:100%;height:155px;'>
						<table border='0' width='100%' cellspacing='0' cellpadding='0'>
							<col width='15%'>
							<col width='*'>
							<col width='15%'>
							<col width='20%'>
							<col width='20%'>
							<!--col width='10%'-->
							<tr  height=2><td align=center bgcolor=gray colspan=6></td></tr>
							<tr bgcolor='#efefef' height=25>
								<th align='center'>그룹</th>
								<th>이름</th>
								<th>아이디</th>
								<th>전화번호</th>
								<th>휴대폰</th>
								<!--th>주소</th-->
							</tr>
							<tr  height=1><td align=center bgcolor=silver colspan=6></td></tr>";

$db = new Database;
if($act == "search"){
	if ($admininfo[mall_type] == "O"){
		$where = " where cu.code != '' and cu.code = cmd.code and cmd.gp_ix = mg.gp_ix and gp_level != 0 and cu.mem_type in ('M','C','F','S') ";
	}else{
		$where = " where cu.code != '' and cu.code = cmd.code and cmd.gp_ix = mg.gp_ix and gp_level != 0 and cu.mem_type in ('M','C','F') ";
	}
	if($search_type != "" && $search_text != ""){
		if($search_type == "name" || $search_type == "mail" || $search_type == "pcs" || $search_type == "tel" ){
			$where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
		}else{
			$where .= " and $search_type LIKE  '%$search_text%' ";
		}
	}

	$sql = "SELECT mg.gp_name, cmd.code, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cu.id, 
				AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."') tel, AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') pcs, 
				AES_DECRYPT(UNHEX(cmd.zip),'".$db->ase_encrypt_key."') as zip, 
				AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."') as addr1, AES_DECRYPT(UNHEX(cmd.addr2),'".$db->ase_encrypt_key."') as addr2, AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail  
				FROM ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_SHOP_GROUPINFO." mg 
				".$where."  
				ORDER BY name";

	$db->query($sql);
	
	if($db->total){
		for($i=0;$i < $db->total ; $i++){
			$db->fetch($i);
			$code = $db->dt[code];
			$gp_name = $db->dt[gp_name];
			$name = $db->dt[name];
			$id = $db->dt[id];
			$tel = $db->dt[tel];
			$pcs = $db->dt[pcs];
			$zip = $db->dt[zip];
			$addr1 = $db->dt[addr1];
			$addr2 = $db->dt[addr2];
			$mail = $db->dt[mail];
			$com_number = $db->dt[com_number];
			$zip_code = $db->dt[zip_code];
			
			$Contents .= "
					<tr height=25 bgcolor='#F8F9FA' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; this.style.cursor='hand';\" onMouseOut=\"this.style.backgroundColor='';\" onClick=\"javscript:idsearch_in(opener.document.form, '$code', '$name','$zip','$addr1','$addr2','$tel','$pcs','$mail','$sc_nm','$com_number','$zip_code','$cs_addr1','$cs_addr2','$com_ceo');\">
								<td align='center'>$gp_name</td>
								<td align='center'>$name</td>
								<td align='center'>$id</td>
								<td align='center'>$tel</td>
								<td align='center'>$pcs</td>
								<!--td align='center'>$addr1 $addr2</td-->
							</tr>
							<tr  height=1><td align=center background='/admin/image/dot.gif' colspan=6></td></tr>";
			//echo $list;
		}
	}else{
			$Contents .= "
				<tr bgcolor='#F8F9FA' height=70>
					<td align='center' colspan=7>							
						아이디 입력해주시기 바랍니다.					
					</td>
				</tr>";
			//echo $list;
	}
}else{
		$Contents .= "
				<tr bgcolor='#F8F9FA' height=120>
					<td align='center' colspan=7>							
						아이디 입력해주시기 바랍니다.					
					</td>
				</tr>";
}

$Contents .= "							
						</table>
					</div>
					</td>
				</tr>
			</table>
		";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "수동주문 > 아이디검색";
$P->NaviTitle = "아이디검색";
$P->strContents = $Contents;
echo $P->PrintLayOut();