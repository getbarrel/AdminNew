<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head>
<title>출고내역자세히보기</title>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<LINK REL='stylesheet' HREF='/admin/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>
<script language='JavaScript' src='./webedit/webedit.js'></script>
<script language='JavaScript' src='../js/admin.js'></Script>
<script language='JavaScript' src='input_pop.js'></Script>
<style>

input {border:1px solid #c6c6c6}
</style>
</head>
<body topmargin=0 leftmargin=0 ><!--onload="Init(document.send_mail);"-->
<?
$db = new Database;
$db2 = new Database;
$sdb = new Database;
$mdb = new Database;
$sql = "select h.regdate as regdate , p.regdate as p_regdate,h.output_msg,output_totalsize,ouput_status,h.charger_ix,p.pcode,p.sellprice,output_type,oid,
			(select place_name from inventory_place_info where pi_ix = p.inventory_info) as place_name,
			(select company_name from inventory_company_info where c_ix = output_saler) as company_name 
			from inventory_output_history h,shop_product p 
			where h.ioh_ix = '".$idx."' and p.id = '".$pid."'";
//echo $sql;

$db->query($sql);
$db->fetch();

?>
<TABLE cellSpacing=0 cellPadding=0 width="100%" align=center border=0 >
	<TR>
		<td align=center colspan=2>
		<table border="0" width="100%" cellpadding="0" cellspacing="1" align="center">
			<tr><td  align=left class='top_orange'  ></td></tr>
			<tr height=35 bgcolor=#efefef>
				<td  style='padding:0 0 0 0;'>
					<table width='100%' border='0' cellspacing='0' cellpadding='0' >
						<tr>
							<td width='10%' height='31' valign='middle' style='font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding-left:10px;' nowrap><!--border-bottom:2px solid #ff9b00;-->
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 출고내역
							</td>
							<td width='90%' align='right' valign='top' ><!--style='border-bottom:2px solid #efefef;'-->
								&nbsp;
							</td>
						</tr>
						<!--tr height=10><td colspan=2></td></tr-->
					</table>
				</td>
			</tr>
			<tr height=25>
				<td style="padding:10px 0px 0px 15px" align="left"><img src='../images/dot_org.gif' align=absmiddle> <b>출고 상품정보</b></td>
			</tr>
			<tr>
				<td style="padding:10px 0px 0px 15px">
					<table class='box_shadow' style='width:98%;height:20px' cellpadding="0" cellspacing="0" border="0"><!---mbox04-->
						<tr>
							<th class='box_01'></th>
							<td class='box_02'></td>
							<th class='box_03'></th>
						</tr>
						<tr>
							<th class='box_04'></th>
							<td class='box_05 align=center'  width='98%'>
								<table border=0 cellpadding=0 cellspacing=0 width='98%'>
									<tr>
										<?
										if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$pid.".gif")){
											$img_str = $admin_config[mall_data_root]."/images/product/s_".$pid.".gif";
										}else{
											$img_str = "../image/b_no_image.gif";

										}
										?>
										<td style='width:200px;padding:10px' align=center><img src="<?=$img_str?>" width=90 height=90></td>
										<td width='*' valign=top>
											<table border=0 cellpadding=0 cellspacing=0 width='100%'>
												<tr>
													<td width='70px' height="22"><b>상품코드</b></td>
													<td width='10'>:</td>
													<td width='*' align="left"><?=$db->dt[pcode]?></td>
												</tr>
												<tr>
													<td colspan=3 class='dot-x'></td>
												</tr>
												<tr>
													<td height="22"><b>제품명</b></td>
													<td>:</td>
													<td align="left"><?=$db->dt[pname]?></td>
												</tr>
												<tr>
													<td colspan=3 class='dot-x'></td>
												</tr>
												<tr>
													<td height="22"><b>공급가</b></td>
													<td>:</td>
													<td align="left"><?=number_format($db->dt[coprice])?> 원</td>
												</tr>
												<tr>
													<td colspan=3 class='dot-x'></td>
												</tr>
												<tr>
													<td height="22"><b>판매가</b></td>
													<td>:</td>
													<td align="left"><?=number_format($db->dt[sellprice])?> 원</td>
												</tr>
												<tr>
													<td colspan=3 class='dot-x'></td>
												</tr>
												<tr>
													<td height="22"><b>등록일</b></td>
													<td>:</td>
													<td align="left"><?=substr($db->dt[regdate],0,10)?></td>
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
					</table>
				</td>
			</tr>

		</table><br>
		<table border=0 cellpadding=0 cellspacing=0 align=left width=100%>
			<tr height=25>
				<td style="padding:10px 0px 0px 15px" align="left"><img src='../images/dot_org.gif' align=absmiddle> <b>출고내용</b></td>
			</tr>
			<tr>
				<td style="padding:10px 0px 0px 15px">
					 <table border='0' cellspacing='1' cellpadding='5' width='98%'>
						<tr>
						  <td bgcolor='#F8F9FA'>

							<table border='0' width='100%' cellspacing='1' cellpadding='0'>
								<tr>
									<td bgcolor='#c0c0c0'>
										<table border='0' width='100%' cellspacing='1' cellpadding='0'>
											<tr id='default' style="padding-left:10px;" height=25>
												<td bgcolor='#CCCCCC' align='left' style='padding-left:10px;'  class=leftmenu nowrap width='15%'><img src='../image/title_head.gif'> 옵션</td>
												<td bgcolor='#ffffff' style="padding-left:5px;" width='35%' align="left">
													<?
														if($db->dt[option_text] == ""){
													?>
													등록된 옵션이 없습니다.
													<?
														}else{
													?>
													<?=$db->dt[option_text]?>
													<?
														}
													?>
												</td>
												<td width='15%' bgcolor='#CCCCCC' align='left' style='padding-left:10px;' align='left' style='padding-left:10px;' class=leftmenu nowrap><img src='../image/title_head.gif'> 출고창고</td>
												<td bgcolor='#ffffff' style="padding-left:5px;" width='35%' align="left"><?=$db->dt[pi_ix]?>
												</td>
											</tr>

											<tr height=25>
												<td bgcolor='#CCCCCC' align='left' style='padding-left:10px;'  class=leftmenu nowrap><img src='../image/title_head.gif'> 출고수량</td>
												<td bgcolor='#ffffff' style="padding-left:5px;" align="left">
													<?=$db->dt[output_totalsize]?> 개
												</td>
												<td bgcolor='#CCCCCC' align='left' style='padding-left:10px;'  class=leftmenu nowrap><img src='../image/title_head.gif'> 판매처</td>
												<td bgcolor='#ffffff' style="padding-left:5px;" align="left">
													<?=$db->dt[company_name]?>
												</td>
											</tr>

											<tr height=25>
												<td bgcolor='#CCCCCC' align='left' style='padding-left:10px;'  class=leftmenu nowrap><img src='../image/title_head.gif'> 출고Type</td>
												<td bgcolor='#ffffff' style="padding-left:5px;" align="left">
													<?
													if($db->dt[output_type] == 1){
													?>
													일반출고
													<? }elseif($db->Dt[output_type] == 2){
													?>
													직원판매
													<? }else{ ?>
													손/방실
													<? }?>
												</td>
												<td bgcolor='#CCCCCC' align='left' style='padding-left:10px;'  class=leftmenu nowrap><img src='../image/title_head.gif'> 출고자</td>
												<td bgcolor='#ffffff' style="padding-left:5px;" align="left"><?=$db->dt[charger_ix]?></td>
											</tr>
											<?
												if($db->dt[output_type] == 1){
											?>
											<tr height=25>
												<td bgcolor='#CCCCCC' align='left' style='padding-left:10px;'  class=leftmenu nowrap><img src='../image/title_head.gif'> 주문번호</td>
												<td bgcolor='#ffffff' style="padding-left:5px;" align="left"><?=$db->dt[oid]?></td>
												<td bgcolor='#CCCCCC' align='left' style='padding-left:10px;'  class=leftmenu nowrap><img src='../image/title_head.gif'> 출고일</td>
												<td bgcolor='#ffffff' style="padding-left:5px;" align="left"><?=$db->dt[regdate]?></td>
											</tr>
											<?
												}else{
											?>
											<tr height=25>
												<td bgcolor='#CCCCCC' align='left' style='padding-left:10px;'  class=leftmenu nowrap><img src='../image/title_head.gif'> 출고일</td>
												<td bgcolor='#ffffff' style="padding-left:5px;" colspan=3 align="left"><?=$db->dt[regdate]?></td>
											</tr>
											<?
												}
											?>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>

			<tr height=25>
				<td style="padding:10px 0px 0px 15px" align="left"><img src='../images/dot_org.gif' align=absmiddle> <b>재고현황</b></td>
			</tr>
			<tr>
				<td style='padding:10px 0px 0px 15px'>
					<table border=0 cellpadding=0 cellspacing=0 width='98%'>
						<tr>
							<td class='s_td' width='40%' align=center height="25"> 옵션이름</td>
							<td class='m_td' width='20%' align=center> 재고</td>
							<td class='e_td' width='20%' align=center> 안전재고</td>
						</tr>

<?
			$sql = "select  id,stock, safestock from ".TBL_SHOP_PRODUCT." where id = '$pid'";
			$sdb->query($sql);
			$sdb->fetch();

?>
						<tr>
							<td colspan=3><?=PrintStockByOption($sdb);?></td>
						</tr>
					</table>
				</td>
			</tr>

			<tr height=25>
				<td style="padding:10px 0px 0px 15px" align="left"><img src='../images/dot_org.gif' align=absmiddle> <b>출고내역</b></td>
			</tr>
			<tr>
				<td style="padding:10px 0px 0px 15px" width='98%'>
					<table border=0 cellpadding=0 cellspacing=0 width='98%'>
						<tr align=center>
							<td width='5%' class=s_td height=25>번호</td>
							<td width='*' class=m_td>상품명</td>
							<td width='15%' class='m_td'>옵션 및 수량</td>
							<td width='10%' class=m_td>출고창고</td>
							<td width='8%' class=m_td>출고내용</td>
							<td width='8%' class=m_td>작성자</td>
							<td width='16%' class=e_td>날짜</td>
						</tr>
<?
			if($max == ""){
				$max = 10; //페이지당 갯수
			}

			if ($page == ''){
				$start = 0;
				$page  = 1;
			}else{
				$start = ($page - 1) * $max;
			}

			$sql = "select * from inventory_output_history where pid = '".$pid."' ";
			$mdb->query($sql);
			$total = $mdb->total;
			$str_page_bar = page_bar($total, $page,$max, "&max=$max&pid=$pid&idx=$idx&company_code=$company_code","");
			$sql = "select h.*,(select place_name from inventory_place_info where pi_ix = h.pi_ix) as place_name from inventory_output_history h where pid = '".$pid."' order by regdate desc LIMIT $start,$max";
			$mdb->query($sql);

			for($i=0;$i<$mdb->total;$i++){
				$mdb->fetch($i);

				$no = $total - ($page - 1) * $max - $i;

				if($mdb->dt[option_text] == ""){
					$option_text = "FREE";
				}else{
					$option_text = $mdb->dt[option_text];
				}
				if($mdb->dt[output_type] == "1"){
					$input_text = "일반판매";
				}else if($mdb->dt[output_type] == "2"){
					$input_text = "직원판매";
				}else if($mdb->dt[output_type] == "3"){
					$input_text = "손/방실";
				}
				if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$mdb->dt[pid].".gif")){
					$img_str = $admin_config[mall_data_root]."/images/product/c_".$mdb->dt[pid].".gif";
				}else{
					$img_str = "../image/no_img.gif";
				}
?>
					<tr bgcolor='#ffffff'>
						<td  align=center bgcolor='#efefef'><?=$no?></td>
						<td  align=left style='padding-left:10px'>
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<tr>
									<td width="40" style="padding:2px 0px;" align="left"><a href="javascript:PoPWindow3('stock_output_detail.php?idx=<?=$mdb->dt[o_ix]?>&pid=<?=$mdb->dt[pid]?>&company_code=<?=$mdb->dt[pi_ix]?>',820,700,'input_detail_pop')"><img src='<?=$img_str?>' style='border:1px solid #eaeaea' align=absmiddle></a></td>
									<td width="*"><a href="javascript:PoPWindow3('stock_output_detail.php?idx=<?=$mdb->dt[o_ix]?>&pid=<?=$mdb->dt[pid]?>&company_code=<?=$mdb->dt[pi_ix]?>',820,700,'input_detail_pop')"><b><?=$mdb->dt[pname]?></b></a></td>
								</tr>
							</table>
						</td>
						<td  align=center bgcolor='#efefef'><?=$option_text?> <br> <?=$mdb->dt[output_totalsize]?> 개</td>
						<td  align=center><?=$mdb->dt[place_name]?></td>
						<td  align=center bgcolor='#efefef'><?=$input_text?></td>
						<td  align=center bgcolor='#ffffff'><?=$mdb->dt[charger_ix]?></td>
						<td  align=center bgcolor='#efefef'><?=$mdb->dt[date]?></td>

				<tr height=1><td colspan=8 class='dot-x'></td></tr>
<?
			}
?>
				</table>
			</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style='padding-top:10px' align=center><?=$str_page_bar?></td>
	</tr>

</TABLE>
</form>
<IFRAME id=act name=act src="" frameBorder=0 width=0 height=0 scrolling=no ></IFRAME>
</body>
</html>
<?
function PrintStockByOption($sdb){

	$mdb = new Database;

	$sql = "select id, option_div,option_price, option_m_price, option_d_price, option_a_price, option_useprice, option_stock, option_safestock,option_etc1 from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." a, ".TBL_SHOP_PRODUCT_OPTIONS." b  where b.option_kind = 'b' and b.pid = '".$sdb->dt[id]."' and a.opn_ix = b.opn_ix order by id asc";

	$mdb->query($sql);

	$mString = "<table cellpadding=4 cellspacing=0 width=100% height=100% style='table-layout : fixed' bgcolor=silver border=0>";



	//$mString = $mString."<tr align=center bgcolor=#efefef height=25><td>비회원가</td><td>회원가</td><td>딜러가</td><td>대리점가</td><td >재고</td><td >안전재고</td></tr>";
	if ($mdb->total == 0){
		$mString .= "<tr height=30>";
		$mString .= "<td width='40%' bgcolor='#efefef'  align=center>옵션등록이 되어 있지 않습니다.</td>
			<td width='20%' bgcolor='#ffffff' align=center>".$sdb->dt[stock]."</td>
			<td width='20%' bgcolor='#efefef' align=center>".$sdb->dt[safestock]."</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$mdb->total;$i++){
			$mdb->fetch($i);
			$mString = $mString."<tr height=30 bgcolor=#ffffff>
			<td width='40%' bgcolor='#efefef' align=center>".$mdb->dt[option_div]."</td>

			<td width='20%' align=center bgcolor='#ffffff' >".$mdb->dt[option_stock]."</td>
			<td width='20%' align=center bgcolor='#efefef' >".$mdb->dt[option_safestock]."</td>
			</tr><tr height=1><td colspan=3 class='dot-x'></td></tr>
			";
		}

		$mString .= "<td width='40%' bgcolor='#efefef' align=center height=30>총계</td>
			<td width='20%' bgcolor='#ffffff' align=center>".$sdb->dt[stock]."</td>
			<td width='20%' bgcolor='#efefef' align=center>".$sdb->dt[safestock]."</td>";
	}
	$mString .= "<tr height=1><td colspan=3 class='dot-x'></td></tr>";
	$mString = $mString."</table>";

	return $mString;
}
?>
