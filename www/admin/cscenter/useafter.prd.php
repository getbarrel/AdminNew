<?
include("../class/layout.class");

$db = new database;

$max = 10; 

if ( $page == '' ) {
	$start = 0;
	$page  = 1;
} else {
	$start = ($page - 1) * $max;
}

$where =" po.option_use='1' and p.reg_category = 'Y'";

if($search_type != "" && $search_text != ""){
	$where .= " and ".$search_type." LIKE '%".trim($search_text)."%' ";
}

if($admininfo[admin_level] == 9){	//시스템 관리자일경우
	$where .= "";
}else{								//셀러업체일경우
	$where .= "and admin ='".$admininfo[company_id]."' and p.product_type != 12 ";
}

$where .= " and p.is_delete = 0 ";

/*상품 리스트 기본 조건 끝 2014-04-11 이학봉*/

$sql = "SELECT 
			 count(p.id) as total
		FROM
			".TBL_SHOP_PRODUCT." p
			left join shop_product_options po on (p.id=po.pid)
			left join shop_product_options_detail pod on (po.opn_ix=pod.opn_ix)
			left join ".TBL_SHOP_PRODUCT_RELATION." r on (p.id = r.pid and r.basic = 1)
			left join ".TBL_SHOP_CATEGORY_INFO." c on r.cid = c.cid
			inner join ".TBL_COMMON_COMPANY_DETAIL." ccd on (p.admin = ccd.company_id)
			left join ".TBL_COMMON_SELLER_DETAIL." csd on (ccd.company_id = csd.company_id)
			
		where
			$where
		";
$db->query($sql);
$result = $db->fetch();
$total = $result['total'];
$ptotal = $total;

$sql = "SELECT 
			 p.*, pod.option_div
		FROM
			".TBL_SHOP_PRODUCT." p
			left join shop_product_options po on (p.id=po.pid)
			left join shop_product_options_detail pod on (po.opn_ix=pod.opn_ix)
			left join ".TBL_SHOP_PRODUCT_RELATION." r on (p.id = r.pid and r.basic = 1)
			left join ".TBL_SHOP_CATEGORY_INFO." c on r.cid = c.cid
			inner join ".TBL_COMMON_COMPANY_DETAIL." ccd on (p.admin = ccd.company_id)
			left join ".TBL_COMMON_SELLER_DETAIL." csd on (ccd.company_id = csd.company_id)
			
		where
			$where
			LIMIT $start, $max 
		";
$db->query($sql);
$goods_datas = $db->fetchall("object");

if($QUERY_STRING == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}

$str_page_bar = page_bar($total, $page, $max,$query_string,"");


$Script = "
<script language='JavaScript' >
	function select(id, pname, option_name){
		$('#fake_name', opener.document).val(pname);
		$('input[name=pid]', opener.document).val(id);
		$('input[name=pname]', opener.document).val(pname);
		$('input[name=option_name]', opener.document).val(option_name);
		self.close();
	}
</Script>";

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("상품정보 검색", "상품정보 검색", false)."</td>
			</tr>
			<tr height=30><td class='p11 ls1' style='padding:0 0 0 5px;'  align='left' > -  등록하고자 하는 상품을 검색해주세요</td></tr>
			<tr>
				<td align=center style='padding: 0 5px 0 5px'>
					<form name='z' method='get' onSubmit='return CheckSearch(this)' >
					<input type='hidden' name='company_id' value='".$company_id."'>
					<input type='hidden' name='surtax_yorn' value='".$surtax_yorn."'>
					<input type='hidden' name='goods_search_type' value='".$goods_search_type."'>
					
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
									<tr>
										<td class='search_box_title'>  검색어 </td>
										<td class='search_box_item'>
											<table cellpadding=0 cellspacing=0 border='0'>
											<col width='120'>
											<col width='*'>
											<tr>
												<td valign='top'>
													<div style='padding-top:5px;'>
													<select name='search_type' id='search_type'  style=\"font-size:12px;\">
														<option value='p.pname' ".CompareReturnValue("p.pname",$search_type).">상품명</option>
														<option value='p.pcode' ".CompareReturnValue("p.pcode",$search_type).">상품코드</option>
														<option value='p.id' ".CompareReturnValue("p.id",$search_type).">상품시스템코드</option>
													</select>
													</div>
												</td>
												<td style='padding:5px;'>
													<div id='search_text_input_div'>
														<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='WIDTH: 210px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
													</div>
												</td>
											</tr>
											</table>
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
						<tr >
							<td colspan=3 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
						</tr>
					</table>
					</form>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr height=30>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> ".($search_text == "" ? "":"'".$search_text ."' 로 검색된 결과 입니다.")."</td>
		<td class='p11 ls1' style='padding:0 0 0 5px;text-align:right;' nowrap>
		</td>
	</tr>
	<tr>
		<td colspan=2 style='padding: 0 5px 0 5px' width=100% valign=top>
		<table width=100% class='list_table_box'>
		<col width='10%'>
		<col width='*'>
		<col width='20%'>
		<col width='20%'>
		<tr height='28' bgcolor='#ffffff'>
			<td align='center' class=m_td ><font color='#000000'><b>상품구분</b></font></td>
			<td align='center' class='m_td' ><font color='#000000'><b>상품명</b></font></td>
			<td align='center' class=m_td ><font color='#000000'><b>상품상태</b></font></td>
			<td align='center' class=m_td ><font color='#000000'><b>상품가격</b></font></td>
		</tr>";


if(count($goods_datas) > 0){

	for ($i = 0; $i < count($goods_datas); $i++){

		//$db->fetch($i);

		switch($goods_datas[$i][state]){
			case '1':
				$state_text = "판매중";
			break;
			case '0':
				$state_text = "임시품절";
			break;
			case '2':
				$state_text = "판매중지";
			break;
			case '6':
				$state_text = "승인대기";
			break;
			case '8':
				$state_text = "승인거부";
			break;
			case '9':
				$state_text = "판매거부";
			break;
			case '7':
				$state_text = "본사대기상품";
			break;

		}
		

		$sql = "select dt.delivery_package from shop_product_delivery pd left join shop_delivery_template dt on (pd.dt_ix=dt.dt_ix) where pd.pid = '".$goods_datas[$i][id]."' and pd.is_wholesale = 'R' order by pd.delivery_div limit 0,1";
		$slave_mdb->query($sql);
		$slave_mdb->fetch();
		$delivery_package = $slave_mdb->dt[delivery_package];

		if($delivery_package == 'N'){
			$use_bundle_text = '묶음배송';
		}else{
			$use_bundle_text = '개별배송';
		}
		
		if($goods_search_type == "bbs"){
			$option_select = "<input type='button' value='상품선택' class='product_select' pid='".$goods_datas[$i][id]."' pcode='".$goods_datas[$i][pcode]."' pname='".$goods_datas[$i][pname]."' com_name='".$goods_datas[$i][com_name]."' delivery_package='".$delivery_package."' />";
		}else{
			$sql = "select * from shop_product_options where pid = '".$goods_datas[$i][id]."' and option_use='1' ";
			$slave_mdb->query($sql);
			if($slave_mdb->total){
				$option_select = "<input type='button' value='옵션선택' onclick=\"ShowModalWindow('./goods_option_select.php?pid=".$goods_datas[$i][id]."&delivery_package=".$delivery_package."',450,700,'goods_option_select',true);\" />";
			}else{
				$option_select = "<input type='button' value='상품선택' class='product_select' pid='".$goods_datas[$i][id]."' pcode='".$goods_datas[$i][pcode]."' pname='".$goods_datas[$i][pname]."' com_name='".$goods_datas[$i][com_name]."' delivery_package='".$delivery_package."' />";
			}
		}

		$Contents .= "
		<tr height=45 onclick=\"select('".$goods_datas[$i][id]."', '".$goods_datas[$i][pname]."', '".$goods_datas[$i][option_div]."');\" style='cursor:pointer;'>
			<td class='list_box_td list_bg_gray'>".getProductType($goods_datas[$i][product_type])."</td>
			<td class='list_box_td' style='text-align:left;padding:0px 5px;line-height:150%;'>".$goods_datas[$i][pname]." - <span class='red'>".$goods_datas[$i][option_div]."</span></td>
			<td class='list_box_td '>".$state_text."</td>
			<td class='list_box_td point'>".number_format($goods_datas[$i][sellprice])."원</td>
		</tr>";

	}

}else{
	$Contents .= "<tr align=center height=30>
				<td colspan=7 align=center> 상품정보가 존재 하지 않습니다.</td>	
			</tr>";
}



$Contents .= "
		</table>
		</td>
	</tr>
	<tr>
		<td align=center style='padding:0 10px 0 0px' colspan=2>
		</td>
	</tr>

	<tr>
		<td align=left style='padding:10px 10px 0 5px' >
			<!--img src='./images/".$admininfo["language"]."/btn_goods_intoon.gif' border='0' align='absmiddle' onclick='GoodsSelectAll()' style='cursor:pointer;'-->
		</td> 
		<td align=right style='padding:10px 0 0 0' >
			".$str_page_bar."
		</td>
	</tr>
	<tr>
		<td align=left style='padding:10px 10px' colspan=2>";

$help_text = "
<table cellpadding=2 cellspacing=0 class='small' style='line-height:120%' >
	<col width=8>
	<col width=*>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td>원하는 삼품을 검색후 선택하실수 있습니다.</td></tr>
</table>
";



$Contents .= HelpBox("상품선택", $help_text,"50");
$Contents .= "
		</td>
	</tr>
</TABLE>
";




$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "상품선택";
$P->NaviTitle = "상품선택";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>