<?
include("../class/layout.class");

$max = 5; //페이지당 갯수

if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}


$db = new Database;
$mdb = new Database;

$db->query("SELECT cmd.code, AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name, (SELECT id FROM ".TBL_COMMON_USER." cu2 WHERE cu2.code=rfd.rsl_code) AS rsl_id FROM reseller_flowin_detail rfd , ".TBL_COMMON_MEMBER_DETAIL." cmd  where rfd.flowin_code = '$code' and rfd.flowin_code = cmd.code ORDER BY cmd.date DESC");
$db->fetch();

$rsl_id=$db->dt[rsl_id];

//$Script = "<script language='JavaScript' ></Script>";


$Contents = "

<TABLE cellSpacing=0 cellPadding=0 width='931' align=center border=0>
	<TR>
		<td align=center>
			<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>
				<tr >
					<td align='left' colspan=2> ".GetTitleNavigation("회원정보 보기 및 상담하기", "회원관리 > 회원정보 보기 및 상담하기", false)."</td>
				</tr>
				<tr height=20><td class='p11 ls1' style='padding:3px 0 3px 0px;text-align:left;'  colspan=2> </td></tr>
				<tr>
					<td align=left style='' width='*' valign='top'>
						<div class='tab' style='width:99%;'>
							<table class='s_org_tab' width=100%>
								<tr>
									<td class='tab'>
										<table id='tab_01' class='on'>
											<tr>
												<th class='box_01'></th>
												<td class='box_02'>유입회원정보</td>
												<th class='box_03'></th>
											</tr>
										</table>
									</td>
									<td align=right style='vertical-align:bottom;padding-bottom:10px;'><b>".$db->dt[name]."</b> 님의 회원정보 입니다.</td>
								</tr>
							</table>
						</div>
						<div class='mallstory t_no' style='width:97%;'>
							<div id='member_info_view' style='width:100%;'>

									<table border='0' width='100%' cellspacing='0' cellpadding='0'>
										<tr>
											<td bgcolor='#ffffff'>
												<table border='0' width='100%' cellpadding='0' cellspacing='0'>
													<tr>
														<td>
															<table width='100%' border='0' cellpadding='0' cellspacing='0'>
																<tr height='25' bgcolor='#efefef' align=center>
																	<td width='8%' class='s_td'><b>번호</b></td>
																	<td width='15%' class='m_td'><b>인센티브형테</b><td/>
																	<td width='*' colspan=2 class='m_td'><b>제품명</b></td>
																	<td width='5%' class='m_td'><b>수량</b></td>
																	<td width='20%' class='m_td'><b>합계</b></td>
																	<td width='10%' class='m_td'><b>인센티브</b></td>
																</tr>";


							$mdb->query("SELECT count(*) as total FROM reseller_incentive ri LEFT JOIN ".TBL_SHOP_ORDER_DETAIL." od on (ri.od_ix = od.od_ix) WHERE ri.flowin_code = '".$code."' and ri.ac_ix != '' ");
							$mdb->fetch();
							$total = $mdb->dt[total];

							$mdb->query("SELECT sum(case when ri.incentive_type='1' then incentive else '0' end) as sum_incen_com,
							sum(case when ri.incentive_type='2' then incentive else '0' end) as sum_incen_ord FROM reseller_incentive ri WHERE ri.flowin_code = '".$code."' and ac_ix != '' ");
							$mdb->fetch();
							$sum_incen_com = $mdb->dt[sum_incen_com];
							$sum_incen_ord = $mdb->dt[sum_incen_ord];
							$sum_total = $sum_incen_ord + $sum_incen_com;

							$sql = "SELECT od.pid, od.pname, pcnt, psprice, ri.incentive, ri.incentive_type, ri.regdate
								FROM  reseller_incentive ri LEFT JOIN ".TBL_SHOP_ORDER_DETAIL." od on (ri.od_ix = od.od_ix)
								WHERE ri.flowin_code = '".$code."' and ri.ac_ix != '' ORDER BY regdate DESC LIMIT $start, $max ";
							$str_page_bar = page_bar($total, $page,$max, "&code=$code","view");


							//echo $sql;

							$mdb->query($sql);

							
							//$sum = 0;

							for($j = 0; $j < $mdb->total; $j++)
							{
								$mdb->fetch($j);

								$num = $total - ($page - 1) * $max - $j;
								
								$pname = $mdb->dt[pname];
								$count = $mdb->dt[pcnt];
								$price = $mdb->dt[psprice];
								$incentive = $mdb->dt[incentive];
								$incentive_type = $mdb->dt[incentive_type];
								
								
								if($incentive_type == 1){
									$incentive_type = '가입';
								}else{
									$incentive_type = ' 매출';
								}
								
					
								$ptotal = $price * $count;
								//$sum += $ptotal;

								if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $mdb->dt[pid], "c"))){
									$img_str = "<img src=\"".PrintImage($admin_config[mall_data_root]."/images/product", $mdb->dt[pid], "c")."\" style='margin:3px 0px;'>";
								}elseif($incentive_type == 1){
									$img_str = "";
								}else{
									$img_str = "<img src=\"../image/no_img.gif\" style='margin:3px 0px;'>";
								}

						$Contents .= "
																<tr align='center' >
																	<td >".$num."</td>
																	<td align=center>".$incentive_type."</td>
																	<td colspan=2 >".$img_str."</td>
																	<td ><div align='left' style='padding:5px 0 5px 0'><a href=\"/shop/goods_view.php?id=".$mdb->dt[pid]."\" target=_blank>".$pname."</a></div></td>
																	<td >".number_format($count)." 개</td>
																	<td >".number_format($ptotal)."</td>
																	<td align=left style='padding-left:5px;'>".$incentive."</td>
																</tr>
																<tr height=1><td colspan=8 background='../image/dot.gif'></td></tr>";

								$num++;
							
							}

							if (!$mdb->total){

							$Contents .= "
															  <tr align='center' height='50' >
																<td colspan='8' align='center'>구입한 상품 정보가 없습니다.</td>
															 </tr>
																<tr height=1><td colspan=8 background='../image/dot.gif'></td></tr>";
								}


						$Contents .= "
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
									
									";
			
	$Contents .= "</div>
						</div>
						<div>".$str_page_bar."<div>
					</td>
					<td width=30% valign=top style='padding:0px 3px'>
						<table width=100% border=0 cellpadding=0 cellspacing=0>
							<tr>
								<td valign=top>
									<table width=350 border=0 cellpadding=0 cellspacing=0>
										<tr height=25>
											<td align=right style='vertical-align:bottom;padding-bottom:5px;'> 리셀러 ID <b>".$rsl_id."</b> 님의 인센티브정보 입니다.</td>
										</tr>
										<tr>
											<td align='left' colspan=2 height=150 valign=top style='padding-top:3px;'>
												<table width='100%' border=0 cellpadding=0 cellspacing=0>
													<tr height=25 align=right>
														<td class=s_td width=30% nowrap>가입 </td>
														<td class=m_td width=30% nowrap>매출 </td>
														<td class=e_td width=30% nowrap>총합계</td>
													</tr>
													<tr height=27 align=right>
														<td >".number_format($sum_incen_com)."<span class='small'>원</span></td>
														<td align=right style='padding:8px 0 8px 10px'>".number_format($sum_incen_ord)."<span class='small'>원</span><br>
														</td>
														<td bgcolor=#ffffff align=right><span style='color:red'>".number_format($sum_total)."</span><span class='small'>원</span></td>
													</tr>
													<tr height=1><td colspan=4 background='/img/dot.gif'></td></tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</TABLE>
";


$P = new ManagePopLayOut();
//$P->addScript = $Script;
$P->Navigation = "유입가입자 상세정보보기 > 유입가입자구입상세보기";
$P->NaviTitle = "유입가입자 구입상세보기";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>



