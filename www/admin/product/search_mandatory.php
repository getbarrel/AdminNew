<?
include("../class/layout.class");

if($_COOKIE[mandatory_max_limit]){
	$max = $_COOKIE[mandatory_max_limit]; //페이지당 갯수
}else{
	$max = 10;
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

$db = new Database;
$db2 = new Database;

if($search_text != "" && $search_type){
	$where = " and mandatory_name like '%".$search_text."%'";
}

$sql = "SELECT
			count(mi_ix) as total  
		FROM 
			shop_mandatory_info
		where
			1
			and is_use = '1'
			$where
			";
$db->query($sql);
$db->fetch();
$total = $db->dt[total];

$sql = "SELECT 
			*
		FROM 
			shop_mandatory_info
		where
			1
			and is_use = '1'
			$where

			order by regdate desc  
			limit $start,$max";
$db->query($sql);


$pagestring = page_bar($total, $page, $max, "&cid2=$cid2&depth=$depth&orderby=$orderby&is_use=$is_use&search_type=$search_type&search_text=$search_text&bd_ix2=$bd_ix2","");

$Script = "
<script language='JavaScript' >
if(window.dialogArguments){
	var opener = window.dialogArguments;
}else{
	var opener = window.opener;
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
					$('select[id^=dt_ix]',opener.document).empty();
					//$('#dt_ix_whole',opener.document).empty();

					$.each(args, function(is_wholesale, entry){
						$.each(entry,function (delivery_div,detail){
							$('#dt_ix_'+is_wholesale+'_'+delivery_div,opener.document).append('<option value=>배송정책 선택</option>');
							$.each(detail,function (dt_ix,delivery_name){
								$('#dt_ix_'+is_wholesale+'_'+delivery_div,opener.document).append('<option value='+dt_ix+' selected>'+delivery_name+'</option>');
							});
						});
					}); 
					self.close();
				}else{
					alert('해당 셀러업체 배송정책이 존재하지 않습니다. 상품관리 > 셀러관리 > 배송정책에서 기본 배송정책을 설정해주세요.');
					$('select[id^=dt_ix]',opener.document).empty();

					$('select[name^=dt_ix]',opener.document).each(function (){
						$(this).append('<option value=>해당 배송정책이 없습니다.</option>');
					
					});
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
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 상품고시정보 검색
							</td>
							<td width='90%' align='right' valign='top' >
								&nbsp;
							</td>
						</tr>
					</table>
				</td>
			</tr-->
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("상품고시정보 검색", "상품고시정보 검색", false)."</td>
			</tr>
			<tr height=30><td class='p11 ls1' style='padding:0 0 0 5px;'  align='left' > - 찾으실 상품고시코드 또는 고시명을 입력하세요.</td></tr>
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
										<td align='center'  colspan=2 class='input_box_title'><b>고시정보검색</b>
											<select name='search_type'>
												<option value='mandatory_name' ".($search_type == 'mandatory_name' || $search_type == ''?'selected':'')."> 상품고시명 </option>
												<option value='mi_code' ".($search_type == 'mi_code'?'selected':'')."> 고시정보코드</option>
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
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> ".($search_text == "" ? "상품고시정보 전체 목록입니다.":"'".$search_text ."' 로 검색된 결과 입니다.")."</td>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> </td>
	</tr>
	<tr><form name='send_mail' method='post' action='sms.act.php' onsubmit='return CheckSMS(this);' ><input type=hidden name='act' value='send_mail' >
		<td colspan=2 style='padding: 0 5px 0 5px' width=100% valign=top>
		<table width=100% class='list_table_box'>
		<tr height='28' bgcolor='#ffffff'>
			<td width='10%' align='center' class='m_td'><font color='#000000'><b>코드</b></font></td>
			<td width='65%' align='center' class=m_td><font color='#000000'><b>상품고시명</b></font></td>
			<td width='25%' align='center' class=m_td><font color='#000000'><b>등록일</b></font></td>
		</tr>";

if($db->total){
	for($i=0;$i<$db->total; $i++){
		$db->fetch($i);
		
		$Contents .= "
		<tr height=25 style='text-align:center;cursor:pointer;' onclick=\"SelectMember('".$type."','".$db->dt[mi_code]."','".$db->dt[mandatory_name]."','".$db->dt[mi_code]."');\">
			<td class='list_box_td list_bg_gray'>".$db->dt[mi_code]."</td>
			<td class='list_box_td list_bg_gray'>".$db->dt[mandatory_name]."</td>
			<td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
		</tr>";
	}
}else{
	$Contents .= "
		<tr height=25 style='text-align:center;cursor:pointer;'>
			<td class='list_box_td' colspan='3'>상품고시 정보가 없습니다.</td>
		</tr>";
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
			".$pagestring."
		</td>
	</tr></form>
</TABLE>
";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "상품고시정보 검색";
$P->NaviTitle = "상품고시정보 검색";
$P->strContents = $Contents;
echo $P->PrintLayOut();
?>





