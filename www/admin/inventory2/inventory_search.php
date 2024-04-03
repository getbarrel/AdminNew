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


if($admininfo[admin_level] == 9){
	if($company_id){
		$where = "where p.id Is NOT NULL and p.id = r.pid and p.stock_use_yn = 'Y' and p.admin ='".$company_id."' 
						and p.id = po.pid and po.option_kind = 'b'
						and po.opn_ix = pod.opn_ix
						and p.inventory_info = pi.pi_ix ";
	}else{
		$where = "where p.id Is NOT NULL and p.id = r.pid and p.stock_use_yn = 'Y' 
						and p.id = po.pid and po.option_kind = 'b'
						and po.opn_ix = pod.opn_ix
						and p.inventory_info = pi.pi_ix ";
	}
}else{
	$where = "where p.id Is NOT NULL and p.id = r.pid  and p.stock_use_yn = 'Y' and p.admin ='".$admininfo[company_id]."' 
					and p.id = po.pid and po.option_kind = 'b'
					and po.opn_ix = pod.opn_ix
					and p.inventory_info = pi.pi_ix";
}

	if($pid != ""){
		$where = $where."and p.id = $pid ";
	}

	if($search_text != ""){
		$where = $where."and p.".$search_type." LIKE '%".$search_text."%' ";
	}

	if($disp != ""){
		$where .= " and p.disp = ".$disp;
	}

//echo $state;
	if($state2 != ""){
		//session_register("state");
		$where = $where." and p.state = ".$state2." ";
	}
	if($brand2 != ""){
		//session_register("brand");
		$where .= " and brand = ".$brand2."";
	}

	/*if($brand_name != ""){
		$where .= " and brand_name LIKE '%".$brand_name."%' ";
	}*/

	if($brand != ""){
		//session_register("brand");
		$where .= " and brand = ".$brand."";
	}

	if($cid2 != ""){
		//session_register("cid");
		//session_register("depth");
		$where .= " and r.cid LIKE '".substr($cid2,0,$cut_num)."%'";
	}else{
		$where .= "";
	}


if($stock_status == "soldout"){
	$stock_where = "and (stock = 0 or option_stock_yn = 'N') ";
}else if($stock_status == "shortage"){
	$stock_where = "and (stock < safestock or option_stock_yn = 'R') ";
}else if($stock_status == "surplus"){
	$stock_where = "and (stock > safestock or option_stock_yn = 'Y')";
}

switch ($depth){
	case 0:
		$cut_num = 3;
		break;
	case 1:
		$cut_num = 6;
		break;
	case 2:
		$cut_num = 9;
		break;
	case 3:
		$cut_num = 12;
		break;
	case 4:
		$cut_num = 15;
		break;
}

if ($cid2){
	$where .= " and r.cid LIKE '".substr($cid2,0,$cut_num)."%' ";
}
		
$sql = "select count(*) as total
			from  ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_SHOP_PRODUCT." p  
			, ".TBL_SHOP_PRODUCT_OPTIONS." po 
			,	".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." pod 
			, inventory_place_info pi 
			
			$where $stock_where ";

//echo $sql;
$db->query($sql);
$db->fetch();
$total = $db->dt[total];
//	echo $db->total;
	//exit;


if($orderby == "date"){
	$orderbyString = "order by p.regdate desc, vieworder2 asc,  id desc";
}else{
	$orderbyString = "order by vieworder2 asc, p.regdate desc, id desc";
}

if($mode == "excel"){
	$sql = "select p.id,  r.cid, p.pcode, p.pname, p.sellprice, p.coprice, p.regdate,p.vieworder,p.disp, p.surtax_yorn, po.opn_ix, stock, safestock, pi.place_name, pod.id as opndt_ix, option_name, option_div,option_code,option_price, option_stock, p.sell_ing_cnt, option_sell_ing_cnt, option_safestock,
			case when vieworder = 0 then 100000 else vieworder end as vieworder2
			from  ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_SHOP_PRODUCT." p  
			, ".TBL_SHOP_PRODUCT_OPTIONS." po 
			,	".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." pod 
			, inventory_place_info pi 
			
			$where $stock_where $orderbyString  ";
}else{
	$sql = "select p.id,  r.cid, p.pcode, p.pname, p.sellprice, p.coprice, p.regdate,p.vieworder,p.disp, p.surtax_yorn, po.opn_ix, stock, safestock, pi.place_name, pod.id as  opndt_ix,option_name, option_div,option_code,option_price, option_stock, p.sell_ing_cnt, option_sell_ing_cnt, option_safestock,
			case when vieworder = 0 then 100000 else vieworder end as vieworder2
			from  ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_SHOP_PRODUCT." p  
			, ".TBL_SHOP_PRODUCT_OPTIONS." po 
			, ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." pod 
			, inventory_place_info pi 		
			$where $stock_where $orderbyString 
			LIMIT $start, $max";
}

//echo nl2br($sql);
//exit;
$db->query($sql);

$goods_infos = $db->fetchall();

$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&buying_status=$buying_status&sdate=$sdate&edate=$edate","");

$Script = "
<script language='JavaScript' >
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

function SelectMember(company_id, com_name){
	//alert($('#company_id',opener.document).parent().html());
	$('#company_id',opener.document).val(company_id);
	$('#com_name',opener.document).val(com_name);
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
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 재고정보검색
							</td>
							<td width='90%' align='right' valign='top' >
								&nbsp;
							</td>
						</tr>
					</table>
				</td>
			</tr-->
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("재고정보검색", "재고정보검색", false)."</td>
			</tr>
			<tr height=30><td class='p11 ls1' style='padding:0 0 0 5px;'  align='left' > - 찾으실 회사명 또는 상점명을 입력하세요.</td></tr>	
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
										<td align='center'  colspan=2 class='input_box_title'><b>재고정보검색</b>
											<select name='search_type'>
												<option value=''> 재고정보검색</option>
												<option value='com_name'> 상품명</option>
												<option value='shop_name' > 상품명 </option>
												<option value='com_ceo'> 상품코드 </option>
												<option value='com_phone'> 단품코드 </option>
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
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> ".($search_text == "" ? "셀러 전체 목록입니다.":"'".$search_text ."' 로 검색된 결과 입니다.")."</td>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> </td>
	</tr>	
	<tr><form name='send_mail' method='post' action='sms.act.php' onsubmit='return CheckSMS(this);' ><input type=hidden name='act' value='send_mail' >		
		<td colspan=2 style='padding: 0 5px 0 5px' width=100% valign=top>
		<table width=100% class='list_table_box'>
		<tr height='28' bgcolor='#ffffff'>		
			<td width='14%' align='center' class=m_td><font color='#000000'><b>단품코드</b></font></td>
			<td width='35%' align='center' class='m_td'><font color='#000000'><b>상품명</b></font></td>
			<td width='*' align='center' class=m_td><font color='#000000'><b>규격</b></font></td>
			
			<td width='20%' align='center' class=m_td><font color='#000000'><b>상품코드</b></font></td>
		  </tr>";


if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);
		
		$Contents .= "<tr height=25 style='text-align:center;cursor:pointer;' >
								<td class='list_box_td list_bg_gray'>".$db->dt[opndt_ix]."</td>
								<td class='list_box_td list_bg_gray'>".$db->dt[pname]."</td>
								<td class='list_box_td point' >".$db->dt[option_div]."</td>
								<td class='list_box_td '>".$db->dt[pcode]."</td>
								
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
$P->Navigation = "재고정보검색";
$P->NaviTitle = "재고정보검색";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>





