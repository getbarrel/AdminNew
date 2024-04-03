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

//브랜드
if($search_type == "brand"){
	$title_text = "브랜드";
	$type_text = "브랜드명";

//상품
}else if($search_type == "product"){
	$title_text = "상품";
	$type_text = "상품명";

//업체(협력사?)
}else if($search_type == "company_v"){
	$title_text = "업체";
	$type_text = "업체명";
	$biz_div = "V";
//업체(협력사?)
}else if($search_type == "trade_company"){
	$title_text = "매입업체";
	$type_text = "매입업체";
	$biz_div = "T";

//프로모션카드
}else if($search_type == "promotion_card"){
	$title_text = "프로모션";
	$type_text = "프로모션(카드)";
//프로모션쿠폰
}else if($search_type == "promotion_cupon"){
	$title_text = "프로모션";
	$type_text = "프로모션(쿠폰)";
//제휴사
}else if($search_type == "company_j"){
	$title_text = "제휴사";
	$type_text = "제휴사명";
	$biz_div = "J";

}else{
	$title_text = "검색";
	$type_text = "검색어";
}


$db = new Database;
//$db->debug =true;
$search_str = "";
$total = 0;
$result = null;
if($search_type){
	if($search_type == "brand"){
		if(!empty($search_text)){
			$search_str = " and brand_name LIKE '%$search_text%' ";
		}
		$db->query("select count(*) as total from shop_brand where disp='1' $search_str ");
		if($db->total){
			$db->fetch();
			$total = $db->dt[total];
		}

		$sql = "select brand_name as name, b_ix as code from shop_brand where disp='1' $search_str  limit $start, $max";
		$db->query($sql);
		
		if($db->total){
			$result = $db->fetchall();
		}

	}else if($search_type == "product"){
		if(!empty($search_text)){
			$search_str = " WHERE pname LIKE '%$search_text%' ";
		}

		$db->query("select count(*) as total from shop_product $search_str ");
		if($db->total){
			$db->fetch();
			$total = $db->dt[total];
		}

		$sql = "select pname as name, id as code from shop_product $search_str limit $start, $max";
		$db->query($sql);
		if($db->total){
			$result = $db->fetchall();
		}
		
	
	
	}else if($search_type == "company_v"){
		if(!empty($search_text)){
			$search_str = " and AES_DECRYPT(com_name,'".$db->ase_encrypt_key."') LIKE '%$search_text%'";
		}

		$db->query("select count(*) as total from ".TBL_COMMON_COMPANY_DETAIL." ccd where com_type = 'S'  $search_str ");//AND biz_div = '".$biz_div."'
		$db->fetch();

		$total = $db->dt[total];

		$sql = "select ccd.company_id as code, ccd.com_name as name, ccd.com_phone, ccd.com_ceo, csd.commission,csd.account_method
			from  ".TBL_COMMON_COMPANY_DETAIL." ccd left join ".TBL_COMMON_SELLER_DELIVERY." csd on (csd.company_id = ccd.company_id)
			where com_type = 'S'  $search_str
			limit $start, $max"; //AND biz_div = '".$biz_div."'
		
		$db->query($sql);
		if($db->total){
			$result = $db->fetchall();
		}
	}else if($search_type == "company_j"){
		if(!empty($search_text)){
			$search_str = " and AES_DECRYPT(com_name,'".$db->ase_encrypt_key."') LIKE '%$search_text%'  ";
		}

		$db->query("select count(*) as total from ".TBL_COMMON_COMPANY_DETAIL." ccd where com_type = 'S'   $search_str "); //AND biz_div = '".$biz_div."'
		$db->fetch();

		$total = $db->dt[total];

		$sql = "select ccd.company_id as code, ccd.com_name as name, ccd.com_phone, ccd.com_ceo, csd.commission,csd.account_method
			from  ".TBL_COMMON_COMPANY_DETAIL." ccd left join ".TBL_COMMON_SELLER_DELIVERY." csd on (csd.company_id = ccd.company_id)
			where com_type = 'S' $search_str 
			limit $start, $max"; //AND biz_div = '".$biz_div."' 
		
		$db->query($sql);
		if($db->total){
			$result = $db->fetchall();
		}
	}else if($search_type == "trade_company"){
		if(!empty($search_text)){
			$search_str = " and AES_DECRYPT(com_name,'".$db->ase_encrypt_key."') LIKE '%$search_text%'  ";
		}

		$db->query("select count(*) as total from ".TBL_COMMON_COMPANY_DETAIL." ccd where com_type = 'G' and seller_type = '2'   $search_str "); //AND biz_div = '".$biz_div."'
		$db->fetch();

		$total = $db->dt[total];

		$sql = "select ccd.company_id as code, ccd.com_name as name, ccd.com_phone, ccd.com_ceo, csd.commission,csd.account_method
			from  ".TBL_COMMON_COMPANY_DETAIL." ccd left join ".TBL_COMMON_SELLER_DELIVERY." csd on (csd.company_id = ccd.company_id)
			where com_type = 'G' and seller_type = '2'   $search_str 
			limit $start, $max"; //AND biz_div = '".$biz_div."' 
		
		$db->query($sql);
		if($db->total){
			$result = $db->fetchall();
		}

	//프로모션 카드 무이자
	}else if($search_type == "promotion_card"){
			if(!empty($search_text)){
				$search_str = " WHERE title LIKE '%".$search_text."%'";
			}
			$db->query("select count(*) as total from SHOP_CARD_PROMOTION ".$search_str." ");
			$db->fetch();

			$total = $db->dt[total];

			$sql = "select title as name, cp_ix as code
				from  SHOP_CARD_PROMOTION
				".$search_str."
				limit $start, $max";
			
			$db->query($sql);
			if($db->total){
				$result = $db->fetchall();
			}
		
	}else if($search_type == "promotion_cupon"){
		if(!empty($search_text)){
			$search_str = " WHERE CUPON_KIND LIKE '%".$search_text."%'";
		}
		$db->query("select count(*) as total from ".TBL_SHOP_CUPON_PUBLISH." scp LEFT JOIN	".TBL_SHOP_CUPON." sc ON scp.CUPON_IX = sc.CUPON_IX $search_str ");
		$db->fetch();

		$total = $db->dt[total];

		$sql = "select sc.CUPON_KIND as name, scp.PUBLISH_IX as code
			from  
				".TBL_SHOP_CUPON_PUBLISH." scp
			LEFT JOIN
				".TBL_SHOP_CUPON." sc
			ON
				scp.CUPON_IX = sc.CUPON_IX
			$search_str
			limit $start, $max";
		$db->query($sql);
		if($db->total){
			$result = $db->fetchall();
		}
	}
	
}

$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&search_type=$search_type&search_text=".urlencode($search_text),"");

$Script = "
<script language='JavaScript' >
	
	function isNodeList(nodes) {
		var result = Object.prototype.toString.call(nodes);
		// modern browser such as IE9 / firefox / chrome etc.
		if (result === '[object HTMLCollection]' || result === '[object NodeList]') {
			return true;
		}
		//ie 6,7,8
		if (typeof(nodes) != 'object') {
			return false;
		}
		// detect length and item 
		if (!('length' in nodes) || !('item' in nodes)) {
			return false;
		}
		// use the trick NodeList(index),all browsers support
		try {
			if (nodes(0) === null || (nodes(0) && nodes(0).tagName)) return true;
		}
		catch (e) {
			return false;
		}
		return false;

    }
 
	function CheckSearch(frm){
		if(frm.search_text.value.length < 1){
			alert('검색어를 입력해주세요');
			return false;
		}
	}

	function check(frm,index){
		if(isNodeList(frm.selected_code)){
			if(frm.selected_code[index].checked){
				frm.selected_code[index].checked = false;
			}else{
				frm.selected_code[index].checked = true;
			}
		}else{
			if(frm.selected_code.checked){
				frm.selected_code.checked = false;
			}else{
				frm.selected_code.checked = true;
			}
		}
	}
	function clearAll(frm){
		if(isNodeList(frm.selected_code)){
			for(i=0;i < frm.selected_code.length;i++){
					frm.selected_code[i].checked = false;
			}
		}else{
			frm.selected_code.checked = false;
		}
	}
	function checkAll(frm){
		if(isNodeList(frm.selected_code)){
			for(i=0;i < frm.selected_code.length;i++){
					frm.selected_code[i].checked = true;
			}
		}else{
			frm.selected_code.checked = true;
		}
	}
	function fixAll(frm){
		if (!frm.all_fix.checked){
			clearAll(frm);
			frm.all_fix.checked = false;
				
		}else{
			checkAll(frm);
			frm.all_fix.checked = true;
		}
	}
	function add_search_text(frm){
		if(isNodeList(frm.selected_code)){
			for(i=0;i < frm.selected_code.length;i++){
				if(frm.selected_code[i].checked == true){
					//output = opener.document.getElementById('".$search_type."_code').value;
					output = $(opener.document).find('#".$search_type."_code').val();
					if(output != ''){
						output = output + ',' + frm.selected_code[i].value;
					}else{
						output = frm.selected_code[i].value;	
					}
					//opener.document.getElementById('".$search_type."_code').value = output;
					$(opener.document).find('#".$search_type."_code').val(output);
					output = '';
				}
			}
		}else{
			if(frm.selected_code.checked == true){
				//output = opener.document.getElementById('".$search_type."_code').value;
				output = $(opener.document).find('#".$search_type."_code').val();
				if(output != ''){
					output = output + ',' + frm.selected_code.value;
				}else{
					output = frm.selected_code.value;	
				}
				//opener.document.getElementById('".$search_type."_code').value = output;
				$(opener.document).find('#".$search_type."_code').val(output);
				output = '';
			}
		}
	}

</Script>";

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			
			
			<tr>
				<td align=center style='padding: 0 5px 0 5px'>
				<form name='z' method='get'  action=''  onSubmit='return CheckSearch(this)'>
				<input type='hidden' name='act' value='search'>
				<input type='hidden' name='search_type' value = '".$search_type."'>
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
										<td align='center'  colspan=2 class='input_box_title'><b>".$type_text."</b>
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
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> ".($search_text == "" ? "전체 목록입니다.":"'".$search_text ."' 로 검색된 결과 입니다.")."</td>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> </td>
	</tr>
	<tr>
		<form name='listform'>
		<td colspan=2 style='padding: 0 5px 0 5px' width=100% valign=top>
		<table width=100% class='list_table_box'>
		<tr height='28' bgcolor='#ffffff'>
			<td width='10%' align='center' class='m_td'><input type='checkbox' class='nonborder' name='all_fix' onclick='fixAll(document.listform)'></td>
			<td width='*' align='center' class=m_td><font color='#000000'><b>".($search_type == "product" ? "상품명":$type_text)."</b></font></td>
			<td width='30%' align='center' class='m_td'><font color='#000000'><b>코드</b></font></td>
		  </tr>";

$index = 0;
if(!empty($result)){
	foreach($result as $rt):

		$Contents .= "<tr height=25 style='text-align:center;' >
								<td class='list_box_td'><input type='checkbox' name='selected_code[]' id='selected_code' value='".$rt["code"]."'></td>
								<td class='list_box_td point' style='cursor:pointer;'onclick='check(document.listform,\"".$index."\");'>".$rt["name"]."</td>
								<td class='list_box_td'>".$rt["code"]."</td>
								</tr>";
		$index++;
	endforeach;
}else{
	$Contents .= "<tr height=25 style='text-align:center;' >
								<td class='list_box_td' colspan=3>검색 결과가 없습니다.</td>
								</tr>";
}


$Contents .= "
		</table>
		</td>
	</tr>
	<tr>
		<td><img src='./images/korea/btn_add2.gif' onclick='add_search_text(document.listform)' style='cursor:pointer;padding:10px 0px 0px 10px;' align='absmiddle'></td>
		<td align=center style='padding:0 10px 0 10px' colspan=2>
		</td>
	</tr>
	<tr>
		<td align=center style='' colspan=2>
			".$str_page_bar."
		</td>
	</tr>
	</form>
</TABLE>
";




$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = $title_text ." 검색";
$P->NaviTitle = $title_text ." 검색";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>





