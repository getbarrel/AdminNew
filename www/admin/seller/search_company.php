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
	if($view == 'innerview'){
		$start = ($page - 1) * $max;
	}else{
		$start = '0';
	}
}

$db = new Database;
$mdb  = new Database;

if($db->dbms_type == "oracle"){
	if($search_type && $search_text){
		if($search_type == "name" || $search_type == "id" || $search_type == "pcs"){
			$search_str = " and AES_DECRYPT(".$search_type.",'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
		}else{
			$search_str = " and $search_type LIKE '%$search_text%' ";
		}
	}
}else{
	if($search_type && $search_text){
		if($search_type == "name" || $search_type == "id" || $search_type == "pcs"){
			$search_str = " and AES_DECRYPT(UNHEX(".$search_type."),'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
		}else{
			$search_str = " and $search_type LIKE '%$search_text%' ";
		}
	}
}


if($seller_type == "1"){
	$where = " and ccd.seller_type like '%1%' ";
}else if($seller_type == "2"){
	$where = " and ccd.seller_type like '%2%' ";
}


$sql = "SELECT
			count(*) as total
		FROM 
			".TBL_COMMON_COMPANY_DETAIL." as ccd 
			inner join ".TBL_COMMON_SELLER_DETAIL." as csd on ccd.company_id = csd.company_id
			inner join ".TBL_COMMON_COMPANY_RELATION." as ccr on ccd.company_id = ccr.company_id
		where 
			ccd.com_type !='A'
			$where
			$search_str";

$db->query($sql);
$db->fetch();

$total = $db->dt[total];
/*
('".$db->dt[company_id]."','".$db->dt[company_code]."','".$db->dt[seller_date]."','".$db->dt[sell_type]."','".$db->dt[seller_division]."','".$db->dt[nationality]."','".$db->dt[seller_level]."','".$db->dt[is_wharehouse]."','".$db->dt[com_name]."','".$db->dt[com_ceo]."','".$com_number_1."','".$com_number_2."','".$com_number_3."','".$corporate_number_1."','".$corporate_number_2."','".$corporate_number_3."','".$com_business_status."','".$com_business_category."','".$db->dt[com_div]."','".$com_phone_1."','".$com_phone_2."','".$com_phone_3."','".$com_mobile_1."','".$com_mobile_2."','".$com_mobile_3."','".$db->dt[com_email]."','".$db->dt[com_homepage]."','".$db->dt[com_zip]."','".$db->dt[com_addr1]."','".$db->dt[com_addr2]."','".$db->dt[company_name]."','".$db->dt[com_person]."','".$db->dt[loan_price]."','".$db->dt[deposit_price]."','".$db->dt[seller_message]."','".$db->dt[seller_auth]."')
AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
*/

$sql = "SELECT
			ccd.company_id,
			ccd.company_code,
			csd.seller_date,
			ccd.seller_type,
			csd.seller_division,
			csd.nationality,
			csd.seller_level,
			ccd.is_wharehouse,
			ccd.com_name,
			ccd.com_ceo,
			ccd.com_number,
			ccd.corporate_number,
			ccd.com_business_status,
			ccd.com_business_category,
			ccd.com_div,
			ccd.com_phone,
			ccd.com_mobile,
			ccd.com_email,
			ccd.com_homepage,
			ccd.com_zip,
			ccd.com_addr1,
			ccd.com_addr2,
			ccd.loan_price,
			csd.deposit_price,
			csd.seller_message,
			ccd.seller_auth
		FROM 
			".TBL_COMMON_COMPANY_DETAIL." as ccd 
			inner join ".TBL_COMMON_SELLER_DETAIL." as csd on ccd.company_id = csd.company_id
			inner join ".TBL_COMMON_COMPANY_RELATION." as ccr on ccd.company_id = ccr.company_id
		where 
			ccd.com_type !='A'
			$where
			$search_str
			limit $start, $max";

$db->query($sql);

$str_page_bar = page_bar($total, $page,$max, "&view=innerview&relation_code=$relation_code&max=$max&company_id=$company_id&sdate=$sdate&edate=$edate&seller_type=$seller_type","");

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

function SearchCharger(company_id, company_code,seller_date,seller_type,seller_division,nationality,seller_level,is_wharehouse,com_name,com_ceo,com_number_1,com_number_2,com_number_3,corporate_number_1,corporate_number_2,corporate_number_3,com_business_status,com_business_category,com_div,com_phone_1,com_phone_2,com_phone_3,com_mobile_1,com_mobile_2,com_mobile_3,com_email,com_homepage,com_zip,com_addr1,com_addr2,company_name,com_person,loan_price,deposit_price,seller_message,seller_auth){

	//alert($('#charger_ix',opener.document).parent().html());
	$('#company_id',opener.document).val(company_id);
	$('#company_code',opener.document).val(company_id);
	$('#com_name',opener.document).val(com_name);

	var seller_day = seller_date.split(' ');
	$('#seller_date',opener.document).val(seller_day[0]);

	//seller_type.split('|');
	if(seller_type == '1'){
		$('#sales_vendor',opener.document).attr('checked',true);
	}else if(seller_type == '2'){
		$('#supply_vendor',opener.document).attr('checked',true);
	}else if(seller_type == '3'){
		$('#oversea_sales',opener.document).attr('checked',true);
	}else if(seller_type == '4'){
		$('#oversea_supply',opener.document).attr('checked',true);
	}else if (seller_type == '5'){
		$('#outsourcing'.opener.document).attr('checked',true);
	}

	if(seller_division == '1'){
		$('#seller_division_1',opener.document).attr('checked',true);
	}else{
		$('#seller_division_2',opener.document).attr('checked',true);
	}

	if(nationality == 'I'){
		$('#nationality_1',opener.document).attr('checked',true);
	}else if(nationality == 'O'){
		$('#nationality_2',opener.document).attr('checked',true);
	}else{
		$('#nationality_3',opener.document).attr('checked',true);
	}
	
	$('#seller_level',opener.document).val(seller_level);

	if(is_wharehouse == '1'){
		$('#is_wharehouse_11',opener.document).attr('checked',true);
	}else{
		$('#is_wharehouse_22',opener.document).attr('checked',true);
	}
	
	$('#com_name',opener.document).val(com_name);
	$('#com_ceo',opener.document).val(com_ceo);

	$('#com_number_1',opener.document).val(com_number_1);
	$('#com_number_2',opener.document).val(com_number_2);
	$('#com_number_3',opener.document).val(com_number_3);

	$('#corporate_number_1',opener.document).val(corporate_number_1);
	$('#corporate_number_2',opener.document).val(corporate_number_2);
	$('#corporate_number_3',opener.document).val(corporate_number_3);
	
	$('#com_business_status',opener.document).val(com_business_status);
	$('#com_business_category',opener.document).val(com_business_category);

	if(com_div == 'R'){
		$('#com_div_R',opener.document).attr('checked',true);
	}else if(com_div == 'P'){
		$('#com_div_P',opener.document).attr('checked',true);
	}else if(com_div == 'S'){
		$('#com_div_S',opener.document).attr('checked',true);
	}else if(com_div == 'E'){
		$('#com_div_E',opener.document).attr('checked',true);
	}

	$('#com_phone_1',opener.document).val(com_phone_1);
	$('#com_phone_2',opener.document).val(com_phone_2);
	$('#com_phone_3',opener.document).val(com_phone_3);

	$('#com_mobile_1',opener.document).val(com_mobile_1);
	$('#com_mobile_2',opener.document).val(com_mobile_2);
	$('#com_mobile_3',opener.document).val(com_mobile_3);

	$('#com_email',opener.document).val(com_email);
	$('#com_homepage',opener.document).val(com_homepage);
	
	$('#com_zip',opener.document).val(com_zip);
	$('#com_addr1',opener.document).val(com_addr1);
	$('#com_addr2',opener.document).val(com_addr2);

	$('#company_name',opener.document).html(company_name);
	$('#com_person',opener.document).html(com_person);

	$('#loan_price',opener.document).val(loan_price);
	$('#deposit_price',opener.document).val(deposit_price);

	$('#seller_message',opener.document).val(seller_message);

	if(seller_auth == 'N'){
		$('#seller_auth_N',opener.document).attr('checked',true);
	}else if(seller_auth == 'Y'){
		$('#seller_auth_Y',opener.document).attr('checked',true);
	}else if(seller_auth == 'X'){
		$('#seller_auth_X',opener.document).attr('checked',true);
	}

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
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 거래처 검색
							</td>
							<td width='90%' align='right' valign='top' >
								&nbsp;
							</td>
						</tr>
					</table>
				</td>
			</tr-->
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("거래처 검색", "거래처 검색", false)."</td>
			</tr>
			<tr height=30><td class='p11 ls1' style='padding:0 0 0 5px;'  align='left' > - 찾으실 회사명 또는 상점명을 입력하세요.</td></tr>
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
										<td align='center'  colspan=2 class='input_box_title'><b>거래처 검색</b>
											<select name='search_type'>
					
												<option value='ccd.com_ceo'> 이름</option>
												<option value='ccd.com_name' > 거래처명 </option>
												<option value='ccd.com_phone'> 전화번호 </option>
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
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> ".($search_text == "" ? "회원 전체 목록입니다.":"'".$search_text ."' 로 검색된 결과 입니다.")."</td>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> </td>
	</tr>
	<tr><form name='send_mail' method='post' action='sms.act.php' onsubmit='return CheckSMS(this);' ><input type=hidden name='act' value='send_mail' >
		<td colspan=2 style='padding: 0 5px 0 5px' width=100% valign=top>
		<table width=100% class='list_table_box'>
		<tr height='28' bgcolor='#ffffff'>
			<td width='20%' align='center' class='m_td'><font color='#000000'><b>거래처명</b></font></td>
			<td width='*' align='center' class=m_td><font color='#000000'><b>대표자명</b></font></td>
			<td width='24%' align='center' class=m_td><font color='#000000'><b>사업자번호</b></font></td>
			<td width='20%' align='center' class=m_td><font color='#000000'><b>전화번호</b></font></td>
		  </tr>";


if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);
		
		if(strpos($db->dt[com_number],"-")){
			$com_number = explode("-",$db->dt[com_number]);
			$com_number_1 = $com_number[0];
			$com_number_2 = $com_number[1];
			$com_number_3 = $com_number[2];
		}else{
			$com_number_1 = substr($db->dt[com_number],0,3);
			$com_number_2 = substr($db->dt[com_number],3,2);
			$com_number_3 = substr($db->dt[com_number],5,5);
		}
		
		if($db->dt[corporate_number]){
			if(strpos($db->dt[corporate_number],"-")){
				$corporate_number = explode("-",$db->dt[corporate_number]);
				$corporate_number_1 = $corporate_number[0];
				$corporate_number_2 = $corporate_number[1];
				$corporate_number_3 = $corporate_number[2];
			}else{
				$corporate_number_1 = substr($db->dt[corporate_number],0,3);
				$corporate_number_2 = substr($db->dt[corporate_number],3,2);
				$corporate_number_3 = substr($db->dt[corporate_number],5,5);
			}
		}

		$com_phone = explode("-",$db->dt[com_phone]);
		$com_phone_1 = $com_phone[0];
		$com_phone_2 = $com_phone[1]; 
		$com_phone_3 = $com_phone[2];

		$com_mobile = explode("-",$db->dt[com_mobile]);
		$com_mobile_1 = $com_mobile[0];
		$com_mobile_2 = $com_mobile[1];
		$com_mobile_3 = $com_mobile[2];
		
		if($db->dt[com_number]){
			if(strpos($db->dt[com_number],"-")){
				$com_number = $db->dt[com_number];
			}else{
				$com_number =  substr($db->dt[com_number],0,3)."-".substr($db->dt[com_number],3,2)."-".substr($db->dt[com_number],5,5);
			}
		}else{
			$com_number = "-";
		}
		
		$sql = "select
					relation_code
				from
					".TBL_COMMON_COMPANY_RELATION."
				where
					company_id = '".$db->dt[company_id]."'";
		$mdb->query($sql);
		$mdb->fetch();
		$relation_code = $mdb->dt[relation_code];

		$relation_length = strlen($relation_code);

		if($relation_length == '5'){
			$relation_code	= $relation_code;
		}else{
			$relation_code = substr($relation_code,0,$relation_length - 4 );
		}
		$sql = "SELECT 
					c.com_name
			FROM 
				".TBL_COMMON_COMPANY_DETAIL." as c 
				inner join ".TBL_COMMON_COMPANY_RELATION." as cr on (c.company_id = cr.company_id)
			where
				cr.relation_code = '".$relation_code."'
				order by cr.relation_code ASC
				";
		$mdb->query($sql);
		$mdb->fetch();
		$company_name = $mdb->dt[com_name];

		$sql= "select
					AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name
				from
					".TBL_COMMON_MEMBER_DETAIL."
				where
					code = '".$db->dt[person]."'
				";
		$mdb->query($sql);
		$mdb->fetch();
		$com_person = $mdb->dt[name];

		//company_id,거래처코드,거래시작일,거래처유형,거래처구분,국내외구분,거래처등급,물류창고사용여부,상호명,대표자명,사업자번호1,사업자번호2,사업자번호3,법인번호1,법인번호2,법인번호3,업태,업종,사업자유형,전화번호1,
		//전화번호2,전화번호3,핸드폰1,핸드폰2,핸드폰3,이메일,홈페이지,우편코드,상세주소1,상세주소2,본사담당사업장,구매담당자,여신한도,보증금,기타사항,셀러업체승인
		$Contents .= "<tr height=25 style='text-align:center;cursor:pointer;' >
					<td class='list_box_td list_bg_gray'>".$db->dt[com_name]."</td>
					<td class='list_box_td point' onclick=\"SearchCharger('".$db->dt[company_id]."','".$db->dt[company_code]."','".$db->dt[seller_date]."','".$db->dt[seller_type]."','".$db->dt[seller_division]."','".$db->dt[nationality]."','".$db->dt[seller_level]."','".$db->dt[is_wharehouse]."','".$db->dt[com_name]."','".$db->dt[com_ceo]."','".$com_number_1."','".$com_number_2."','".$com_number_3."','".$corporate_number_1."','".$corporate_number_2."','".$corporate_number_3."','".$db->dt[com_business_status]."','".$db->dt[com_business_category]."','".$db->dt[com_div]."','".$com_phone_1."','".$com_phone_2."','".$com_phone_3."','".$com_mobile_1."','".$com_mobile_2."','".$com_mobile_3."','".$db->dt[com_email]."','".$db->dt[com_homepage]."','".$db->dt[com_zip]."','".$db->dt[com_addr1]."','".$db->dt[com_addr2]."','".$company_name."','".$db->dt[com_person]."','".$db->dt[loan_price]."','".$db->dt[deposit_price]."','".$db->dt[seller_message]."','".$db->dt[seller_auth]."');\">".$db->dt[com_ceo]."</td>
					<td class='list_box_td '>".$com_number."</td>
					<td class='list_box_td list_bg_gray'>".$db->dt[com_mobile]."</td>
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
$P->Navigation = "거래처 검색";
$P->NaviTitle = "거래처 검색";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>