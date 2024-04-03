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
$db2 = new Database;
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


$sql = "select
			count(*) as total
		from
			".TBL_COMMON_USER." cu,
			".TBL_COMMON_MEMBER_DETAIL." cmd,
			".TBL_COMMON_SELLER_DETAIL." csd,
			".TBL_COMMON_COMPANY_DETAIL." ccd
		where
			cu.mem_div in ('A','S')
			and cu.company_id = ccd.company_id
			and cu.code = cmd.code
			and csd.company_id = ccd.company_id
			and ccd.seller_auth = 'Y' $search_str $code_str";
//echo nl2br($sql);
$db->query($sql);
$db->fetch();
echo "<!-- " . $sql . " -->";
$total = $db->dt[total]+1;


$sql = "select
			ccd.company_id,
			ccd.com_name,
			ccd.com_phone,
			ccd.com_ceo,
			ccd.com_zip,
			ccd.com_addr1,
			ccd.com_addr2,
			ccd.com_number,
			csd.shop_name
		from
			".TBL_COMMON_COMPANY_DETAIL." ccd
			inner join ".TBL_COMMON_SELLER_DETAIL." csd on (ccd.company_id = csd.company_id)
		where
			ccd.com_type in ('S','A')
			and ccd.seller_auth = 'Y'
			$search_str
			order by com_name asc
			limit $start, $max ";
$db->query($sql);
echo "<!-- " . $sql . " -->";
$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&buying_status=$buying_status&sdate=$sdate&edate=$edate&input_id=$input_id&input_name=$input_name&type=$type$search_type=$search_type&search_text=$search_text","");

$Script = "
<script language='JavaScript' >

if(window.dialogArguments){
	opener = window.dialogArguments;
}else{
	//alert(window.dialogArguments);
	//opener = window.opener;
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

function SelectMember(type, company_id, com_name,com_ceo, com_number, com_zip, com_addr1, com_addr2){
	//alert($('#company_id',opener.document).parent().html());
	$('#".$input_id."',opener.document).val(company_id);		//한페이지서 동시 사용시 구분을위하여 외부에서 input_id, input_name 값을 받아옴 2014-04-17 이학봉
	$('#".$input_name."',opener.document).val(com_name);


	if($('#delivery_type_1',opener.document).is(':checked')){
		company_id ='".$HEAD_OFFICE_CODE."';
	}

	if(company_id && type =='input'){
		$.ajax({
	    url : './seller_search.act.php',
	    type : 'POST',
	    data : {company_id:company_id,
				act:'select_delivery_template',
				mode:'select_company'
				},
	    dataType: 'json',
	    error: function(data,error){// 실패시 실행함수
	        alert(error);},
	    success: function(args){

				if(args != null){
					//기본배송정책 뿌려주는 부분 시작
					$('#basic_template_delivery',opener.document).html();
					$.each(args.input, function(is_wholesale, entry){
						$.each(entry,function (delivery_div,detail){
							$.each(detail,function (dt_ix,delivery_name){
								$('#basic_template_delivery',opener.document).html(delivery_name);
								$('#template_basic_dt_check',opener.document).attr('checked',true);
								$('#template_basic_dt_r',opener.document).val(dt_ix);
							});
						});
					});

					//개별배송정책
					$('select[id^=dt_ix]',opener.document).empty();
					$.each(args.select, function(is_wholesale, entry){
						$.each(entry,function (delivery_div,detail){
							$('#dt_ix_'+is_wholesale+'_'+delivery_div,opener.document).append('<option value=>배송정책 선택</option>');
							$.each(detail,function (dt_ix,delivery_name){
								$('#dt_ix_'+is_wholesale+'_'+delivery_div,opener.document).append('<option value='+dt_ix+' selected>'+delivery_name+'</option>');
							});
						});
					});

					$('input[name=delivery_type][value=2]', opener.document).prop('checked', true);
					$('input[name=delivery_type][value=1]', opener.document).prop('checked', false);
					
					if($('#delivery_setting_zone', opener.document).is(':visible') == false){
						$('#delivery_setting_zone', opener.document).show();
						$('#delivery_setting_result_zone', opener.document).hide();
					}
					self.close();
				}else{
					alert('해당 셀러업체 배송정책이 존재하지 않습니다. 상품관리 > 셀러관리 > 배송정책에서 기본 배송정책을 설정해주세요.');
					$('select[id^=dt_ix]',opener.document).empty();

					$('select[name^=dt_ix]',opener.document).each(function (){
						$(this).append('<option value=>해당 배송정책이 없습니다.</option>');

					});
					$('#basic_template_delivery',opener.document).html('해당 배송정책이 없습니다.');
					self.close();
				}
	        }
	    });
	}else if(type == 'company_info'){
		if(com_zip != ''){
			var _com_zip = com_zip.split('-');
			$('input[name=com_zip1]',opener.document).val(_com_zip[0]);
			$('input[name=com_zip2]',opener.document).val(_com_zip[1]);
		}else{
			$('input[name=com_zip1]',opener.document).val('');
			$('input[name=com_zip2]',opener.document).val('');
		}
		$('span[id=com_ceo_text]',opener.document).html(com_ceo);
		$('input[name=com_ceo]',opener.document).val(com_ceo);
		$('input[name=com_addr1]',opener.document).val(com_addr1);
		$('input[name=com_addr2]',opener.document).val(com_addr2);
		if(com_number != ''){
			var _com_number = com_number.split('-');
			$('input[name=com_number1]',opener.document).val(_com_number[0]);
			$('input[name=com_number2]',opener.document).val(_com_number[1]);
			$('input[name=com_number3]',opener.document).val(_com_number[2]);
		}else{
			$('input[name=com_number1]',opener.document).val('');
			$('input[name=com_number2]',opener.document).val('');
			$('input[name=com_number3]',opener.document).val('');
		}
	}else if(type == 'contractor_info'){
		if(com_zip != ''){
			var _contractor_zip = com_zip.split('-');
			$('input[name=contractor_zip1]',opener.document).val(_contractor_zip[0]);
			$('input[name=contractor_zip2]',opener.document).val(_contractor_zip[1]);
		}else{
			$('input[name=contractor_zip1]',opener.document).val('');
			$('input[name=contractor_zip2]',opener.document).val('');
		}
		$('span[id=contractor_ceo_text]',opener.document).html(com_ceo);
		$('input[name=contractor_ceo]',opener.document).val(com_ceo);
		$('input[name=contractor_addr1]',opener.document).val(com_addr1);
		$('input[name=contractor_addr2]',opener.document).val(com_addr2);

		if(com_number != ''){
			var _contractor_number = com_number.split('-');
			$('input[name=contractor_reg_no1]',opener.document).val(_contractor_number[0]);
			$('input[name=contractor_reg_no2]',opener.document).val(_contractor_number[1]);
			$('input[name=contractor_reg_no3]',opener.document).val(_contractor_number[2]);
		}else{
			$('input[name=contractor_reg_no1]',opener.document).val('');
			$('input[name=contractor_reg_no2]',opener.document).val('');
			$('input[name=contractor_reg_no3]',opener.document).val('');
		}
	}else{
		self.close();
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
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 셀러검색
							</td>
							<td width='90%' align='right' valign='top' >
								&nbsp;
							</td>
						</tr>
					</table>
				</td>
			</tr-->
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("셀러검색", "셀러검색", false)."</td>
			</tr>
			<tr height=30><td class='p11 ls1' style='padding:0 0 0 5px;'  align='left' > - 찾으실 회사명 또는 상점명을 입력하세요.</td></tr>
			<tr>
				<td align=center style='padding: 0 5px 0 5px'>
				<form name='z' method='get'  action=''  onSubmit='return CheckSearch(this)'>
				<input type='hidden' name='act' value='search'>
				<input type='hidden' name='input_id' value='$input_id'>
				<input type='hidden' name='input_name' value='$input_name'>
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
										<td align='center'  colspan=2 class='input_box_title'><b>셀러검색</b>
											<select name='search_type'>
												<option value='com_name' ".($search_type == 'com_name' || $search_type == ''?'selected':'')."> 상호명</option>
												<option value='shop_name' ".($search_type == 'shop_name'?'selected':'')."> 상점명 </option>
												<option value='com_ceo' ".($search_type == 'com_ceo'?'selected':'')."> 대표자 </option>
												<option value='com_phone' ".($search_type == 'com_phone'?'selected':'')."> 전화번호 </option>
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
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> ".($search_text == "" ? "셀러 전체 목록입니다.":"'".$search_text ."' 로 검색된 결과 입니다.")."</td>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> </td>
	</tr>
	<tr><form name='send_mail' method='post' action='sms.act.php' onsubmit='return CheckSMS(this);' ><input type=hidden name='act' value='send_mail' >
		<td colspan=2 style='padding: 0 5px 0 5px' width=100% valign=top>
		<table width=100% class='list_table_box'>
		<tr height='28' bgcolor='#ffffff'>
			<td width='20%' align='center' class='m_td'><font color='#000000'><b>회사명</b></font></td>
			<!--<td width='*' align='center' class=m_td><font color='#000000'><b>상점명</b></font></td>-->
			<td width='10%' align='center' class=m_td><font color='#000000'><b>대표자</b></font></td>
			<td width='10%' align='center' class=m_td><font color='#000000'><b>셀러명</b></font></td>
			<td width='15%' align='center' class=m_td><font color='#000000'><b>셀러ID</b></font></td>
			<td width='17%' align='center' class=m_td><font color='#000000'><b>전화번호</b></font></td>
		</tr>";

if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);

		$sql = "select
						cu.id,
						AES_DECRYPT(UNHEX(cmd.name),'".$db2->ase_encrypt_key."') as name
					from
						".TBL_COMMON_SELLER_DETAIL." as csd
						inner join ".TBL_COMMON_USER." as cu on (csd.charge_code = cu.code)
						inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)
					where
						csd.company_id = '".$db->dt[company_id]."'";

		$db2->query($sql);
		$db2->fetch();

		$Contents .= "<tr height=25 style='text-align:center;cursor:pointer;' onclick=\"SelectMember('".$type."','".$db->dt[company_id]."','".$db->dt[com_name]."','".$db->dt[com_ceo]."','".$db->dt[com_number]."','".$db->dt[com_zip]."','".$db->dt[com_addr1]."','".$db->dt[com_addr2]."');\">
								<td class='list_box_td list_bg_gray'>".$db->dt[com_name]."</td>
								<!--<td class='list_box_td point' >".$db->dt[shop_name]."</td>-->
								<td class='list_box_td '>".$db->dt[com_ceo]."</td>
								<td class='list_box_td '>".$db2->dt[name]."</td>
								<td class='list_box_td '>".$db2->dt[id]."</td>
								<td class='list_box_td list_bg_gray'>".$db->dt[com_phone]."</td>
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
$P->Navigation = "셀러검색";
$P->NaviTitle = "셀러검색";
$P->strContents = $Contents;
echo $P->PrintLayOut();
?>





