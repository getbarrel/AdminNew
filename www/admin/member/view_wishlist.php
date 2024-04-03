<?
include("../class/layout.class");


$db = new MySQL;

$max = 15; //페이지당 갯수

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

$Contents = "
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation("찜상품 보기", "회원관리 > 찜상품 보기 ")."</td>
	</tr>
</table>
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td style='width:75%;' colspan=2 valign=top>
			<form name='search_frm' method='get' action=''>
			<input type=hidden name='mmode' value='".$mmode."'>
			<input type=hidden name='mem_ix' value='".$mem_ix."'>
			<table width=100%  border=0>
				<tr>
					<td align='left' colspan=2   width='100%' valign=top>
						<table cellpadding=0 cellspacing=0 width='100%' border='0' class='search_table_box'>
						<col width=15%>
						<col width=35%>
						<col width=15%>
						<col width=35%>

							<tr height=30>
								<th class='search_box_title' >검색항목 : </th>
								<td class='search_box_item' colspan=3>
									<table cellpadding='0' cellspacing='0' border='0' >
									<tr>
										<td >
										<select name='search_type' style='font-size:12px;'>
											<!--<option value='name' ".CompareReturnValue('cmd.name',$search_type,' selected').">주문자이름</option>-->
											<!--<option value='cu.id' ".CompareReturnValue('cu.id',$search_type,' selected').">주문자 아이디</option>-->
											<option value='pname' ".CompareReturnValue('pname',$search_type,' selected').">상품이름</option>
										</select>
										</td>
										<td width='*'><input type='text' class=textbox name='search_text' size='30' value='$search_text' style=''></td>
										</tr>
										</table>
									</td>
							</tr>
							<tr height=33>
								<th class='search_box_title' >
								찜상품 일자
								</th>
								<td class='search_box_item'  colspan=3>
									".search_date('startDate','endDate',$startDate,$endDate)."
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=2 align=center style='padding:10px 0px;'>
			<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
		</td>
	</tr>
</table>
</form>
<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
 <tr>";

if ($vFromYY == ""){
	$api_data["startDate"]=$startDate;
	$api_data["endDate"]=$endDate;
	$api_data["start"]=$start;
	$api_data["max"]=$max;
}else{
	foreach($_GET as $key => $val){
		$api_data[$key]=$val;
	}
}

if($mmode == "personalization"){
	$where .= " and w.mid = '".$mem_ix."' ";
}

if($search_type && $search_text){
	if($search_type == "name" || $search_type == "mail" || $search_type == "pcs" || $search_type == "tel" ){
		$where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
	}else{
		$where .= " and ".$search_type." LIKE '%".$search_text."%' ";
	}
}

if($startDate && $endDate){
	$where .= " and  date_format(w.regdate, '%Y%m%d') between '".str_replace('-','',$startDate)."' and '".str_replace('-','',$endDate)."' ";
}

$sql="select count(*) as total
		from shop_wishlist w
		right join shop_product p on w.pid = p.id
		right join common_member_detail cmd on w.mid = cmd.code
		left join common_user cu on w.mid = cu.code
		where w.pid = p.id ".$where." ";
//echo nl2br($sql);
$db->query($sql);
$db->fetch();
$total=$db->dt[total];

$Contents .= "<td colspan=3 align=left><b>전체 주문수 : $total 건</b></td><td colspan=10 align=right>";
$Contents .= "
	</td>
  </tr>
  </table>
  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box' style='table-layout:fixed;display:table;'>
	<tr height='25' >  ";
	if($mmode != "personalization"){
$Contents .= "
		<td width='7%' align='center' class='m_td' nowrap><font color='#000000' class=small><b>회원명</b></font></td>";
	}
$Contents .= "
		<td width='*' align='center' class='m_td' nowrap><font color='#000000' class=small><b>제품명</b></font></td>

		<td width='9%' align='center' class='m_td' nowrap><font color='#000000' class=small><b>상품금액</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap><font color='#000000' class=small><b>재고</b></font></td>
		<!-- td width='9%' align='center' class='m_td' nowrap><font color='#000000' class=small><b>주문금액</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap><font color='#000000' class=small><b>예상<br>적립금</b></font></td -->
		<td width='10%' align='center' class='m_td' nowrap><font color='#000000' class=small><b>등록일자</b></font></td>
		<!-- td width='10%' align='center' class='e_td' nowrap><font color='#000000' class=small><b>관리</b></font></td -->
	</tr>

  ";

if($total){



	$sql="select w.*,  p.pname, p.sellprice, p.stock, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
	from shop_wishlist w right join shop_product p on w.pid = p.id
	right join common_member_detail cmd on w.mid = cmd.code
	left join common_user cu on cmd.code = cu.code
	where w.pid = p.id  ".$where." order by regdate desc 
	limit $start, $max ";//order_from='".$site_code."' and co_od_ix in ('".implode("','",$od_ixs)."')
	$db->query($sql);
	$carts = $db->fetchall();

	for($j=0;$j < count($carts);$j++){//$ddb->total
		//$ddb->fetch($j);
	$Contents .= "
	<tr height='25' >  ";
	if($mmode != "personalization"){
$Contents .= "
		<td  align='center'  >".($carts[$j][name] ? wel_masking_seLen($carts[$j][name],1 ,1):"비회원")."</td>";
	}
	$Contents .= "
		<td  align='left'  >
			<table cellpadding=3>
			<tr>
				<td>
				<img src='".PrintImage($admin_config[mall_data_root]."/images/product", $carts[$j][pid], "c")."' width=50 align=left>
				</td>
				<td>
				<b>".$carts[$j][pname]."</b><br>".$carts[$j][options_text]."
				</td>
			</tr>
			</table>
		</td>
		<td align='center'  >".number_format($carts[$j][sellprice])."</td>
		<td align='center'  >".$carts[$j][stock]."</td>
		<!-- td align='center'  >".number_format($carts[$j][totalprice])."</td>
		<td align='center'  >".number_format($carts[$j][reserve])."</td -->
		<td align='center'  >".$carts[$j][regdate]."</td>
		<!-- td align='center' ></td -->
	</tr>  ";

	}
	$script_time[loop_end] = time();
}else{
	if($mem_ix != ""){
		$Contents .= "<tr height=50><td colspan=4 align=center>찜상품 목록이 존재하지 않습니다.</td></tr>";
	}else{
		$Contents .= "<tr height=50><td colspan=4 align=center>찜상품 목록이 존재하지 않습니다.</td></tr>";
	}
}

if($QUERY_STRING == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}

$Contents = str_replace("{total_sum}",$total_sum,$Contents) ;

$Contents .= "
	</tabel>
  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
	<col width='30%' />
	<col width='*' />
  <tr height=40>
    <td align='center' colspan='2'>&nbsp;".page_bar($total, $page, $max,$query_string,"")."&nbsp;</td>
  </tr>
</table> ";


$Contents .= HelpBox("찜상품 보기", $help_text);

if($mmode == "personalization"){
	$P = new ManagePopLayOut();
	$P->addScript = "".$Script;
	$P->OnloadFunction = "";
	$P->strLeftMenu = member_menu();
	$P->Navigation = "회원관리 > 찜상품 보기";
	$P->title = "찜상품 보기";
	$P->NaviTitle =  "찜상품 보기";
	$P->strContents =  $Contents;
	$P->layout_display = false;
	$P->view_type = "personalization";
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->strLeftMenu = member_menu();
	$P->OnloadFunction = "";//MenuHidden(false);
	$P->addScript = "".$Script;
	$P->Navigation = "회원관리 > 찜상품 보기";
	$P->title = "찜상품 보기";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

//	웰숲 우클릭 방지
include_once("../order/wel_drag.php");
?>