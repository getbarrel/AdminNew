<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include("./inventory.lib.php");


$db = new Database;
$db2 = new Database;
$mdb = new Database;
$sdb = new Database;
$sql = "select p.regdate as regdate , p.regdate as p_regdate,p.pcode,p.pname, p.sellprice,p.coprice , p.stock,p.sell_ing_cnt
			from  shop_product p 
			where p.id = '".$pid."'";
//echo $sql;

$db->query($sql);
$db->fetch();

$sql = "select company_name from inventory_company_info where c_ix = '".$db->dt[input_company]."'";
$db2->query($sql);
$db2->fetch();

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>
			
			<tr height=25>
				<td style='padding:10px 0px 0px 0px' align='left'><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>입고 상품정보</b></td>
			</tr>
			<tr>
				<td style='padding:0px 0px 0px 0px'>
					
					<table border=0 cellpadding=0 cellspacing=0 width='100%'>
						<tr>";

						if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $pid, "s"))){
							$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $pid, "s");
						}else{
							$img_str = "../image/b_no_image.gif";
						}
						
$Contents .= "
							<td style='width:150px;padding:10px' align=center><img src='".$img_str."' width=50></td>
							<td width='*' valign=top>
								<table border=0 cellpadding=0 cellspacing=0 width='100%' class='input_table_box'>
									<col width='20%' >
									<col width='30%' >
									<col width='20%' >
									<col width='30%' >
									<tr align='left' height='25' >
										<td class='input_box_title'> <b>상품코드</b></td>
										<td class='input_box_item'>&nbsp;".$db->dt[pcode]."</td>									
										<td class='input_box_title'><b>제품명</b></td>
										<td class='input_box_item'>&nbsp;".$db->dt[pname]."</td>
									</tr>
									<tr align='left' height='25'>
										<td class='input_box_title'><b>공급가</b></td>
										<td class='input_box_item'>&nbsp;".number_format($db->dt[coprice])." 원</td>
										<td class='input_box_title'><b>판매가</b></td>
										<td class='input_box_item'>&nbsp;".number_format($db->dt[sellprice])." 원</td>
									</tr>
									<tr align='left' height='25'>
										<td class='input_box_title'><b>상품 재고</b></td>
										<td class='input_box_item point'>&nbsp;".number_format($db->dt[stock])." 개</td>
										<td class='input_box_title'><b>판매진행중 재고</b></td>
										<td class='input_box_item point'>&nbsp;".number_format($db->dt[sell_ing_cnt])." 개</td>
									</tr>
									<tr align='left' height='25'>
										<td class='input_box_title'><b>등록일</b></td>
										<td class='input_box_item' colspan=3>&nbsp;".substr($db->dt[regdate],0,10)."</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>

		</table><br>
		<table border=0 cellpadding=0 cellspacing=0 align=left width=100%>
			<tr height=25>
				<td style='padding:10px 0px 0px 0px' align='left'><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>재고현황</b></td>
			</tr>
			<tr>
				<td style='padding:10px 0px 0px 0px'>
					<table border=0 cellpadding=0 cellspacing=0 width='100%' class='list_table_box'>
						<tr height='27'>
							<td class='s_td' width='20%' align=center > 옵션이름</td>
							<td class='s_td' width='20%' align=center > 옵션코드</td>
							<td class='s_td' width='20%' align=center > 보관장소</td>
							<td class='m_td' width='20%' align=center> 재고</td>
							<td class='e_td' width='20%' align=center> 안전재고</td>
						</tr>";


			$sql = "select  id,stock, safestock from ".TBL_SHOP_PRODUCT." where id = '$pid'";
			$sdb->query($sql);
			$sdb->fetch();

$Contents .= "
						<tr>
							<td colspan=5>".PrintStockByOption($sdb->dt)."</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr height=25>
				<td style='padding:10px 0px 0px 0px' align='left'><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>입고내역</b></td>
			</tr>
			<tr>
				<td style='padding:10px 0px 0px 0px'>
					<table border=0 cellpadding=0 cellspacing=0 width='100%' class='list_table_box'>
						<tr height=30 align=center>
							<td width='7%' class=s_td>번호</td>
							<td width='*' class=m_td>상품명</td>
							<td width='15%' class=m_td>보관장소</td>
							<td width='10%' class=m_td>입고내용</td>
							<td width='10%' class=m_td>입고처</td>
							<td width='8%' class=m_td>작성자</td>
							<td width='15%' class=e_td><a href='product_list.php?orderby=date&company_id=$company_id'>날짜</a></td>
						</tr>";

			if($max == ""){
				$max = 10; //페이지당 갯수
			}

			if ($page == ''){
				$start = 0;
				$page  = 1;
			}else{
				$start = ($page - 1) * $max;
			}

			$sql = "select count(*) as total 
						from inventory_input_history iih 
						left join inventory_customer_info ici on ici.ci_ix = iih.input_company,
						inventory_input_history_detail iihd left join inventory_place_info ipi on iihd.input_inventory = ipi.pi_ix 
						where iih.h_ix = iihd.hix and iihd.pid = '".$pid."' ";

			$mdb->query($sql);
			$mdb->fetch();
			$total = $mdb->dt[total];

			//$str_page_bar = page_bar($total, $page,$max, "&max=$max&pid=$pid&idx=$idx&company_code=$company_code","");
			$sql = "select *
					from inventory_input_history_detail where pid = '".$pid."' 
					order by regdate desc LIMIT $start,$max";

			$sql = "select iihd.*, iih.* ,  ipi.place_name , ici.customer_name
						from inventory_input_history iih
						left join inventory_customer_info ici on ici.ci_ix = iih.input_company,
						inventory_input_history_detail iihd 
						left join inventory_place_info ipi on iihd.input_inventory = ipi.pi_ix 
						where iih.h_ix = iihd.hix and iihd.pid = '".$pid."' 
						LIMIT $start,$max ";

			//echo nl2br($sql);
			$mdb->query($sql);

			for($i=0;$i<$mdb->total;$i++){
				$mdb->fetch($i);

				$no = $total - ($page - 1) * $max - $i;

				if($mdb->dt[input_type] == "DI"){
					$input_text = "일반입고";
				}else if($mdb->dt[input_type] == "DM"){
					$input_text = "재고이동";
				}else{
					$input_text = "반품처리";
				}
				//if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$mdb->dt[pid].".gif")){
				if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $mdb->dt[pid], "s"))){
					$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $mdb->dt[pid], "c");
				}else{
					$img_str = "../image/no_img.gif";
				}
$Contents .= "
					<tr bgcolor='#ffffff' height=50>
						<td  align=center>".$no."</td>
						<td  align=left style='padding:5px;'>
							<table cellpadding='0' cellspacing='0' border='0' width='100%'>
								<tr>
									<td width='40' style='padding:0px 3px;' align='left'>
										<a href='?idx=".$mdb->dt[h_ix]."&pid=".$mdb->dt[pid]."&input_inventory=".$mdb->dt[input_inventory]."'><img src='".$img_str."' width=50 height=50 style='border:1px solid #eaeaea' align=absmiddle></a>
									</td>
									<td width='*' style='padding-left:6px;'><a href='?idx=".$mdb->dt[h_ix]."&pid=".$mdb->dt[pid]."&input_inventory=".$mdb->dt[input_inventory]."'><b>".$mdb->dt[pname]."</b></a></td>
								</tr>
							</table>
						</td>
						<td align=center>".$mdb->dt[place_name]."</td>
						<td  align=center>".$input_text."</td>
						<td  align=center style='padding:5px 5px;'>".$mdb->dt[customer_name]."</td>
						<td  align=center>".$mdb->dt[input_owner]."</td>
						<td  align=center>".$mdb->dt[regdate]."</td>
					</tr>";

			}

$Contents .= "
				</table>
			</td>
			</tr>
			
			
		</table>
		</td>
	</tr>
	<tr>
		<td style='padding-top:10px' align=center>".$str_page_bar."</td>
	</tr>

</TABLE>
</form>";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "재고관리 > 입고내역";
$P->NaviTitle = "입고내역";
$P->title = "입고내역";
$P->strContents = $Contents;
echo $P->PrintLayOut();

/*
function PrintStockByOption($sdb){

	$mdb = new Database;

	$sql = "select id, option_div,option_code,option_price, option_m_price, option_d_price, option_a_price, option_useprice, option_stock, option_safestock,option_etc1 from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." a, ".TBL_SHOP_PRODUCT_OPTIONS." b  where b.option_kind = 'b' and b.pid = '".$sdb->dt[id]."' and a.opn_ix = b.opn_ix order by id asc";

	$mdb->query($sql);

	$mString = "<table cellpadding=4 cellspacing=0 width=100% height=100% style='table-layout : fixed' bgcolor=silver border=0>";



	//$mString = $mString."<tr align=center bgcolor=#efefef height=25><td>비회원가</td><td>회원가</td><td>딜러가</td><td>대리점가</td><td >재고</td><td >안전재고</td></tr>";
	if ($mdb->total == 0){
		$mString .= "<tr height=30>";
		$mString .= "<td width='60%' bgcolor='#efefef'  colspan=2 align=center>재고합계</td>
			<td width='20%' bgcolor='#ffffff' align=center>".$sdb->dt[stock]."</td>
			<td width='20%' bgcolor='#efefef' align=center>".$sdb->dt[safestock]."</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$mdb->total;$i++){
			$mdb->fetch($i);
			$mString = $mString."<tr height=27 bgcolor=#ffffff>
			<td width='30%' bgcolor='#efefef' align=center style='border-bottom:1px solid silver'>".$mdb->dt[option_div]." </td>
			<td width='30%' align=center bgcolor='#efefef' style='border-bottom:1px solid silver'>".$mdb->dt[option_code]."</td>
			<td width='20%' align=center bgcolor='#ffffff' style='border-bottom:1px solid silver'>".$mdb->dt[option_stock]."</td>
			<td width='20%' align=center bgcolor='#efefef' style='border-bottom:1px solid silver'>".$mdb->dt[option_safestock]."</td>
			</tr>
			";
		}

		$mString .= "<td width='60%' colspan=2 bgcolor='#efefef' align=center height=27>총계</td>
			<td width='20%' bgcolor='#ffffff' align=center>".$sdb->dt[stock]."</td>
			<td width='20%' bgcolor='#efefef' align=center>".$sdb->dt[safestock]."</td>";
	}
	$mString = $mString."</table>";

	return $mString;
}
*/
?>
