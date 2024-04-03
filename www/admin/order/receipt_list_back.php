<?
include("../class/layout.class");

$db = new Database;
$mdb = new Database;
/*
$help_text = "	-  현금영수증신청 목록입니다. 발행 취소를 했을 경우 재발행이 불가능 합니다. <br>

		";*/
		
		$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$mstring ="

		<table cellpadding=5 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("현금영수증 발행내역", "주문관리 > 현금영수증 발행내역")."</td>
		</tr>
		<tr>
			<td>
			".PrintEventList()."
			</td>
		</tr>";
$mstring .= "<tr><td style='padding-bottom:10px;' colspan=7>".HelpBox("현금영수증 관리", $help_text)."</td></tr>";
$mstring .="</table>";

$Contents = $mstring;


$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "HOME > 주문관리 > 현금영수증 발행내역";
$P->strLeftMenu = order_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

function PrintEventList(){
	global $db, $mdb,$admininfo,$page,$nset,$sns_product_type;

	$sql = "select b.bname,a.oid,a.m_rcash_noappl,a.m_pgAuthDate,a.m_ruseopt from receipt_result as a left join ".TBL_SHOP_ORDER." as b on a.oid = b.oid LEFT JOIN ".TBL_SHOP_ORDER_DETAIL." od ON b.oid=od.oid WHERE od.product_type NOT IN (".implode(',',$sns_product_type).")";
	$mdb->query($sql);
	$total = $mdb->total;

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}


	$mString = "<table cellpadding=4 cellspacing=0 border=0 width=100% bgcolor=silver>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td class=s_td width='15%'>주문자</td><td class=m_td width='30%'>주문번호</td><td class=m_td width='10%'>승인번호</td><td class=m_td width='15%'>승인날자</td><td class=m_td width='20%'>사용용도</td><td class=e_td width='10%'>승인취소</td></tr>";
	if ($mdb->total == 0){
		$mString .= "<tr bgcolor=#ffffff height=50><td colspan=6 align=center><!--현금영수증 내역이 존재 하지 않습니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</td></tr>";

	}else{

		$db->query("select b.bname,a.oid,a.m_rcash_noappl,a.regdate,a.m_ruseopt,a.m_tid,a.module_div from receipt_result as a left join shop_order as b on a.oid = b.oid   order by  regdate desc limit $start , $max");

		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);

			//$no = $no + 1;
			if($db->dt[m_ruseopt] == 0){
				$m_ruseopt = "소득공제용";
			}else{
				$m_ruseopt = "지출증빙용";
			}
			$mString = $mString."<tr height=30 bgcolor=#ffffff align=center>
			<td bgcolor='#efefef'><a href=\"javascript:PoPWindow('/admin/order/receipt_view.php?oid=".$db->dt[oid]."','650','370','')\">".$db->dt[bname]."</a></td>
			<td align=left style='padding-left:20px;'>".$db->dt[oid]."</td>
			<td bgcolor='#efefef'>".$db->dt[m_rcash_noappl]."</td>
			<td>".substr($db->dt[regdate],0,4)."/".substr($db->dt[regdate],5,2)."/".substr($db->dt[regdate],8,2)."</td>
			<td bgcolor='#efefef' align=center>".$m_ruseopt."</td>
			<td>";

			if($db->dt[module_div] == "1"){
			$mString = $mString."<a href=\"javascript:PoPWindow('/shop/inicis/sample/INIcancel_write.php?tid=".$db->dt[m_tid]."','520','180','')\"><img src='/admin/image/btn_cancle.gif'></a>";
			}else if($db->dt[module_div] == "0"){
			$mString = $mString."<a href=\"javascript:PoPWindow('/cash/AGSCash.php?oid=".$db->dt[oid]."&Adm_no=".$db->dt[m_rcash_noappl]."','560','440','')\"><img src='/admin/image/btn_cancle.gif'></a>";
			}else{
			$mString = $mString."<a href=\"javascript:PoPWindow('/shop/lgdacom/cashreceipt_write.php?oid=".$db->dt[oid]."&Adm_no=".$db->dt[m_rcash_noappl]."','560','440','')\"><img src='/admin/image/btn_cancle.gif'></a>";
			}

			$mString = $mString."</td>
			</tr>
			<tr height=1><td colspan=6 class='dot-x'></td></tr>
			";
		}

		$mString .= "<tr height=50 bgcolor=#ffffff>
					<td colspan=4 align=left>".page_bar($total, $page, $max,  "&max=$max","")."</td>
					<td colspan=2 align=right></td>
				</tr>";
	}


	$mString .= "</table>";

	return $mString;
}


?>
