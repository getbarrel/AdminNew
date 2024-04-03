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
$mdb = new Database;

if($search_type && $search_text){
	$where = " and $search_type LIKE '%$search_text%' ";
}


	
if($h_div == '1'){		// 1:입고, 2:출고
	$select_title = "매입처";
	$where .= " and ( ccd.seller_type like '%2%' or ccd.seller_type like '%4%') order by ccr.relation_code asc";
}else if($h_div == '2'){
	$select_title = "매출처";
	$where .= " and (ccd.seller_type like '%1%' or ccd.seller_type like '%3%')  order by ccr.relation_code asc";
}else{
	$select_title = "거래처";
}
$sql = "select relation_code from common_company_relation where company_id = '".$company_id."'";

$db->query($sql);
$db->fetch();
$relaiotn_code = $db->dt[relation_code];

if($type == "estimate_order"){
$sql = "select
		distinct ccd.company_id,
		ccd.company_id as company_id,
		ccd.com_name as com_name,
		ccd.regdate,
		ccd.loan_price,
		ccd.noaccept_price,
		ccd.com_number,
		ccd.com_phone,
		ccd.com_mobile,
		ccd.com_zip,
		ccd.com_addr1,
		ccd.com_addr2,
		ccd.com_email,

		cmd.voucher_div,
		cmd.voucher_num_div,
		cmd.certificate_yn,

		cmd.voucher_phone,
		cmd.voucher_card,
		cmd.expense_num,
		cmd.phone_voucher_name,
		cmd.card_voucher_name,

		cu.id,
		AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
		AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
		AES_DECRYPT(UNHEX(cmd.zip),'".$db->ase_encrypt_key."') as zip,
		AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."') as addr1,
		AES_DECRYPT(UNHEX(cmd.addr2),'".$db->ase_encrypt_key."') as addr2,
		AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."') as tel,
		AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs

		from
			common_company_detail as ccd
			inner join common_company_relation as ccr on (ccd.company_id = ccr.company_id)
			left join common_user as cu on (cu.company_id = ccd.company_id)
			left join common_member_detail as cmd on (cu.code = cmd.code)
		where
			ccr.relation_code like '".$relaiotn_code."%'
			and ccd.seller_auth = 'Y'
			$where
		

";	

}else{
/*
$sql = "select
		distinct ccd.company_id,
		ccd.company_id as company_id,
		ccd.com_name as com_name,
		ccd.regdate,
		ccd.loan_price,
		ccd.noaccept_price,
		ccd.com_number,
		ccd.com_phone,
		ccd.com_mobile,
		ccd.com_zip,
		ccd.com_addr1,
		ccd.com_addr2,
		ccd.com_email,

		cmd.voucher_div,
		cmd.voucher_num_div,
		cmd.certificate_yn,

		cmd.voucher_phone,
		cmd.voucher_card,
		cmd.expense_num,
		cmd.phone_voucher_name,
		cmd.card_voucher_name,

		cu.id,
		AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
		AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
		AES_DECRYPT(UNHEX(cmd.zip),'".$db->ase_encrypt_key."') as zip,
		AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."') as addr1,
		AES_DECRYPT(UNHEX(cmd.addr2),'".$db->ase_encrypt_key."') as addr2,
		AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."') as tel,
		AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs
		from
			common_company_detail as ccd
			inner join common_company_relation as ccr on (ccd.company_id = ccr.company_id)
			left join common_user as cu on (cu.company_id = ccd.company_id)
			left join common_member_detail as cmd on (cu.code = cmd.code)
		where
			ccr.relation_code like '".$relaiotn_code."%'
			and ccd.seller_auth = 'Y'
			$where
		
	
";
*/

/*
	left join common_user as cu on (cu.company_id = ccd.company_id)
	left join common_member_detail as cmd on (cu.code = cmd.code)
*/

$sql = "select
		distinct ccd.company_id,
		ccd.company_id as company_id,
		ccd.com_name as com_name,
		ccd.regdate
		from
			common_company_detail as ccd
			inner join common_company_relation as ccr on (ccd.company_id = ccr.company_id)
			
		where
			ccr.relation_code like '".$relaiotn_code."%'
			and ccd.seller_auth = 'Y'
			$where";
}

$db->query($sql);
$db->fetch();
$total = $db->total;

$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&buying_status=$buying_status&sdate=$sdate&edate=$edate&type=$type&company_id=$company_id&h_div=$h_div&search_type=$search_type&search_text=$search_text ","");


if($type == "estimate_order"){
$sql = "select
		distinct ccd.company_id,
		ccd.company_id as company_id,
		ccd.com_name as com_name,
		ccd.regdate,
		ccd.loan_price,
		ccd.noaccept_price,
		ccd.com_number,
		ccd.com_phone,
		ccd.com_mobile,
		ccd.com_zip,
		ccd.com_addr1,
		ccd.com_addr2,
		ccd.com_email,

		cmd.voucher_div,
		cmd.voucher_num_div,
		cmd.certificate_yn,

		cmd.voucher_phone,
		cmd.voucher_card,
		cmd.expense_num,
		cmd.phone_voucher_name,
		cmd.card_voucher_name,

		cu.id,
		cu.code,
		cu.mem_type,
		g.use_discount_type,
		g.whole_wms_discount_type,
		g.retail_wms_discount_type,
		AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
		AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
		AES_DECRYPT(UNHEX(cmd.zip),'".$db->ase_encrypt_key."') as zip,
		AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."') as addr1,
		AES_DECRYPT(UNHEX(cmd.addr2),'".$db->ase_encrypt_key."') as addr2,
		AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."') as tel,
		AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs

		from
			common_company_detail as ccd
			inner join common_company_relation as ccr on (ccd.company_id = ccr.company_id)
			left join common_user as cu on (cu.company_id = ccd.company_id)
			left join common_member_detail as cmd on (cu.code = cmd.code)
			left join shop_groupinfo g on (g.gp_ix = cmd.gp_ix)
		where
			ccr.relation_code like '".$relaiotn_code."%'
			and ccd.seller_auth = 'Y'
			$where
			
			limit $start,$max
";	

}else{

/*
$sql = "select
		
		distinct ccd.company_id,
		ccd.company_id as company_id,
		ccd.com_name as com_name,
		ccd.regdate,
		ccd.loan_price,
		ccd.noaccept_price,
		ccd.com_number,
		ccd.com_phone,
		ccd.com_mobile,
		ccd.com_zip,
		ccd.com_addr1,
		ccd.com_addr2,
		ccd.com_email,

		cmd.voucher_div,
		cmd.voucher_num_div,
		cmd.certificate_yn,

		cmd.voucher_phone,
		cmd.voucher_card,
		cmd.expense_num,
		cmd.phone_voucher_name,
		cmd.card_voucher_name,

		cu.id,
		cu.code,
		AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
		AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
		AES_DECRYPT(UNHEX(cmd.zip),'".$db->ase_encrypt_key."') as zip,
		AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."') as addr1,
		AES_DECRYPT(UNHEX(cmd.addr2),'".$db->ase_encrypt_key."') as addr2,
		AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."') as tel,
		AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs

		from
			common_company_detail as ccd
			inner join common_company_relation as ccr on (ccd.company_id = ccr.company_id)
			left join common_user as cu on (cu.company_id = ccd.company_id)
			left join common_member_detail as cmd on (cu.code = cmd.code)
		where
			ccr.relation_code like '".$relaiotn_code."%'
			and ccd.seller_auth = 'Y'
			$where
			
			limit $start,$max
";

*/

/*
	left join common_user as cu on (cu.company_id = ccd.company_id)
	left join common_member_detail as cmd on (cu.code = cmd.code)
*/

$sql = "select
		
		distinct ccd.company_id,
		ccd.company_id as company_id,
		ccd.com_name as com_name,
		ccd.com_ceo,
		(select csd.shop_name from common_seller_detail csd where csd.company_id = ccd.company_id) as shop_name,
		ccd.regdate
		from
			common_company_detail as ccd
			inner join common_company_relation as ccr on (ccd.company_id = ccr.company_id)
		where
			ccr.relation_code like '".$relaiotn_code."%'
			and ccd.seller_auth = 'Y'
			$where
			
			limit $start,$max";

}

$db->query($sql);
$db->fetch();


$Script = "
<script language='JavaScript' >

function CheckSearch(frm){
	if(frm.search_text.value.length < 1){
		alert('검색어를 입력해주세요');
		return false;
	}
}



function SelectCompany(code,company_id, company_name,com_number,loan_price,noaccept_price,com_phone,com_mobile,com_zip,com_addr1,com_addr2,voucher_div,voucher_num_div,voucher_phone,phone_voucher_name,voucher_card,card_voucher_name,expense_num,certificate_yn,id,name,tel,pcs,zip,addr1,addr2,mail,com_mail,use_discount_type,wms_discount_type,wms_discount_value)
{

	//alert($('#company_id',opener.document).parent().html());
	$('#ci_ix',opener.document).val(company_id);
	$('#ci_name',opener.document).val(company_name);
	//$('#com_name',opener.document).val(company_name);

	/////////////수동수주주서 작성시 매출처 불러오기 필요합니다. 2013-07-02 이학봉////////////////
		var voucher_value = voucher_div;
		var voucher_num_value = voucher_num_div;
		var certificate_yn_value = certificate_yn;

		var com_number_array = com_number.split('-');	//사업자번호
		var com_phone_array = com_phone.split('-');		//회사전화번호
		var com_mobile_array = com_mobile.split('-');	//회사 핸드폰번호
		var voucher_phone_array = voucher_phone.split('-');	//휴대폰 현금영수증
		var voucher_card_array = voucher_card.split('-');	//현금영수증카드번호
		var expense_num_array = expense_num.split('-');	//지출증빙번호
		var tel = tel.split('-');	//주문자 전화번호
		var pcs = pcs.split('-');	//주문자 휴대폰전화번호
		var zip = com_zip.split('-');	//주문자 배송지 우편코드
		var remain_loan_price;

		if($('#code',opener.document)){ //20130909 Hong 추가
			$('#code',opener.document).val(code);
		}

		$('#com_number1',opener.document).val(com_number_array[0]);
		$('#com_number2',opener.document).val(com_number_array[1]);
		$('#com_number3',opener.document).val(com_number_array[2]);
		
		/*
		$('#com_phone1',opener.document).val(com_phone_array[0]);
		$('#com_phone2',opener.document).val(com_phone_array[1]);
		$('#com_phone3',opener.document).val(com_phone_array[2]);

		$('#com_mobile1',opener.document).val(com_mobile_array[0]);
		$('#com_mobile2',opener.document).val(com_mobile_array[1]);
		$('#com_mobile3',opener.document).val(com_mobile_array[2]);
		*/

		$('#com_zip',opener.document).val(com_zip);
		$('#com_addr1',opener.document).val(com_addr1);
		$('#com_addr2',opener.document).val(com_addr2);

		$('#member_id',opener.document).val(id);	//주문자 아이디
		$('#rname',opener.document).val(company_name);		//주문자명
		//$('#member_idy',opener.document).html(id);

		$('#tel1',opener.document).val(com_phone_array[0]);
		$('#tel2',opener.document).val(com_phone_array[1]);
		$('#tel3',opener.document).val(com_phone_array[2]);

		$('#pcs1',opener.document).val(com_mobile_array[0]);
		$('#pcs2',opener.document).val(com_mobile_array[1]);
		$('#pcs3',opener.document).val(com_mobile_array[2]);
		
		$('#r_mail',opener.document).val(com_mail);
		$('#zip1',opener.document).val(zip[0]);
		$('#zip2',opener.document).val(zip[1]);
		$('#addr1',opener.document).val(com_addr1);
		$('#addr2',opener.document).val(com_addr2);
		
		$('#voucher_div_'+voucher_value,opener.document).attr('checked',true);

		if(voucher_value == '1'){
			$('#voucher_num_div_'+voucher_num_value,opener.document).attr('checked',true);
		}

		if(voucher_value == '3'){
			$('#certificate_yn_'+certificate_yn_value,opener.document).attr('checked',true);
		}

		if(voucher_value == ''){
			$(\"input[type=radio][name='voucher_div']\",opener.document).attr('checked',false);
			$(\"input[type=radio][name='voucher_num_div']\",opener.document).attr('checked',false);
			$(\"input[type=radio][name='certificate_yn']\",opener.document).attr('checked',false);
		}

		$('#voucher_phone1',opener.document).val(voucher_phone_array[0]);
		$('#voucher_phone2',opener.document).val(voucher_phone_array[1]);
		$('#voucher_phone3',opener.document).val(voucher_phone_array[2]);
		$('#phone_voucher_name',opener.document).val(phone_voucher_name);

		$('#voucher_card1',opener.document).val(voucher_card_array[0]);
		$('#voucher_card2',opener.document).val(voucher_card_array[1]);
		$('#voucher_card3',opener.document).val(voucher_card_array[2]);
		$('#voucher_card4',opener.document).val(voucher_card_array[3]);
		$('#card_voucher_name',opener.document).val(card_voucher_name);
		
		$('#expense_num1',opener.document).val(expense_num_array[0]);
		$('#expense_num2',opener.document).val(expense_num_array[1]);
		$('#expense_num3',opener.document).val(expense_num_array[2]);
		
		 //20130909 Hong 추가
		$('#loan_price_text',opener.document).text(FormatNumber(loan_price));
		if(loan_price==0 || loan_price==''){
			remain_loan_price='무제한';
		}else{
			remain_loan_price=FormatNumber(loan_price-noaccept_price);
		}
		$('[id^=remain_loan_price_text]',opener.document).text(remain_loan_price);
		$('#new_info_2',opener.document).trigger('click');
		
		//품목별 다중가격 관련!
		if(use_discount_type=='w'){
			$('#wms_discount_type',opener.document).val(wms_discount_type);
			if(wms_discount_type=='whole'){
				$('#whole_wms_discount_type',opener.document).val(wms_discount_value);
				$('#whole_wms_discount_type',opener.document).show();
				$('#retail_wms_discount_type',opener.document).hide();
				$('#retail_wms_discount_type',opener.document).val('')
			}else{
				$('#retail_wms_discount_type',opener.document).val(wms_discount_value);
				$('#retail_wms_discount_type',opener.document).show();
				$('#whole_wms_discount_type',opener.document).hide();
				$('#whole_wms_discount_type',opener.document).val('')
			}
		}else{
			$('#wms_discount_type',opener.document).val('retail');
			$('#wms_discount_value',opener.document).val('');
		}

	/////////////수동수주주서 작성시 필요합니다. 2013-07-02 이학봉////////////////
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
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> ".$select_title." 검색
							</td>
							<td width='90%' align='right' valign='top' >
								&nbsp;
							</td>
						</tr>
					</table>
				</td>
			</tr-->
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation($select_title."검색", $select_title."검색", false)."</td>
			</tr>
			<tr height=30><td class='p11 ls1' style='padding:0 0 0 5px;'  align='left' > - 찾으실 ".$select_title."명 또는 ".$select_title."코드을 입력하세요.</td></tr>
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
									<col width='220'>
									<col width='*'>
									<tr height='40' valign='middle'>
										<td align='center'  colspan=2 class='input_box_title'><b>".$select_title."검색</b>
											<select name='search_type'>
												<option value='ccd.com_name'> ".$select_title."명</option>
												<option value='ccd.company_id'> ".$select_title."코드(key)</option>
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
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> ".($search_text == "" ? "".$select_title." 전체 목록입니다.":"'".$search_text ."' 로 검색된 결과 입니다.")."</td>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> </td>
	</tr>
	<tr><form name='send_mail' method='post' action='sms.act.php' onsubmit='return CheckSMS(this);' ><input type=hidden name='act' value='send_mail' >
		<td colspan=2 style='padding: 0 5px 0 5px' width=100% valign=top>
		<table width=100% class='list_table_box'>
		<tr height='28' bgcolor='#ffffff'>
			<td width='20%' align='center' class='m_td'><font color='#000000'><b>상점명</b></font></td>
			<td width='*' align='center' class=m_td><font color='#000000'><b>".$select_title." 명</b></font></td>";

if($type == "estimate_order"){
		$Contents .= "<td width='20%' align='center' class=m_td><font color='#000000'><b>회원명(아이디)</b></font></td>";
}else{
		$Contents .= "<td width='20%' align='center' class=m_td><font color='#000000'><b>대표자명</b></font></td>";
}
$Contents .= "
			<td width='20%' align='center' class=m_td><font color='#000000'><b>등록일</b></font></td>
		  </tr>";


if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);
		
		if(strpos($db->dt[com_number],'-')){
			$com_number = $db->dt[com_number];	
		}else{
			$com_number_array[0] = substr($db->dt[com_number],0,3);		//사업자 번호 ERP에서 받는건 - 구분이 없으므로 없을경우 앞3자리 가운데 2자리 나머지로 처리해줘야함
			$com_number_array[1] = substr($db->dt[com_number],3,2);
			$com_number_array[2] = substr($db->dt[com_number],5,5);
			
			$com_number = implode("-",$com_number_array);
		}

		if(strpos($db->dt[tel],'-')){
			$tel = $db->dt[tel];	
		}else{
			if(strlen($db->dt[tel]) == '11'){
				$tel_array[0] = substr($db->dt[tel],0,3);		//주문자 정보 뿌려줄때 2013-07-04 이학봉
				$tel_array[1] = substr($db->dt[tel],3,4);
				$tel_array[2] = substr($db->dt[tel],8,4);
				$tel = implode("-",$tel_array);
			}else{
				$tel_array[0] = substr($db->dt[tel],0,2);		//주문자 정보 뿌려줄때 2013-07-04 이학봉
				$tel_array[1] = substr($db->dt[tel],2,4);
				$tel_array[2] = substr($db->dt[tel],7,4);
				$tel = implode("-",$tel_array);
			}
		}

		if(strpos($db->dt[pcs],'-')){
			$pcs = $db->dt[pcs];	
		}else{
			if(strlen($db->dt[pcs]) == '11'){
			$pcs_array[0] = substr($db->dt[pcs],0,3);		//주문자 정보 뿌려줄때 2013-07-04 이학봉
			$pcs_array[1] = substr($db->dt[pcs],3,4);
			$pcs_array[2] = substr($db->dt[pcs],9,4);
			$pcs = implode("-",$pcs_array);
			}
		}
		 
		if($db->dt[use_discount_type]=="w"){
			if($db->dt[mem_type]=="C" || $db->dt[mem_type]=="A"){
				$wms_discount_type="whole";
				$wms_discount_value= $db->dt[whole_wms_discount_type];
			}else{
				$wms_discount_type="retail";
				$wms_discount_value= $db->dt[retail_wms_discount_type];
			}
		}else{
			$wms_discount_type="retail";
			$wms_discount_value="";
		}

		$Contents .= "<tr height=25 style='text-align:center;cursor:pointer;' onclick=\"if($(this).children().css('background-color').replace(/\s/g,'')=='rgb(249,222,209)'){SelectCompany('".$db->dt[code]."','".$db->dt[company_id]."','".$db->dt[com_name]."','".$com_number."','".$db->dt[loan_price]."','".$db->dt[noaccept_price]."','".$db->dt[com_phone]."','".$db->dt[com_mobile]."','".$db->dt[com_zip]."','".$db->dt[com_addr1]."','".$db->dt[com_addr2]."','".$db->dt[voucher_div]."','".$db->dt[voucher_num_div]."','".$db->dt[voucher_phone]."','".$db->dt[phone_voucher_name]."','".$db->dt[voucher_card]."','".$db->dt[card_voucher_name]."','".$db->dt[expense_num]."','".$db->dt[certificate_yn]."','".$db->dt[id]."','".$db->dt[name]."','".$tel."','".$pcs."','".$db->dt[zip]."','".$db->dt[addr1]."','".$db->dt[addr2]."','".$db->dt['mail']."','".$db->dt['com_email']."','".$db->dt['use_discount_type']."','".$wms_discount_type."','".$wms_discount_value."');}else{changeBgColor(this)}\" >
								<td class='list_box_td '>".$db->dt[shop_name]."</td>
								<td class='list_box_td point' >".$db->dt[com_name]."</td>";
				if($type == "estimate_order"){
					$Contents .="<td class='list_box_td list_bg_gray' >".($db->dt[name] ? $db->dt[name]."<br>( ".$db->dt[id]." )":" 미가입자")."</td>";
				}else{
					$Contents .="<td class='list_box_td list_bg_gray' >".$db->dt[com_ceo]."</td>";
				}
		$Contents .="
					<td class='list_box_td list_bg_gray' >".str_replace(" ","<br/>",$db->dt[regdate])."</td>
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
$P->Navigation = "".$select_title."검색";
$P->NaviTitle = "".$select_title."검색";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>





