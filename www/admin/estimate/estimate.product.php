<?
include("../class/layout.class");
include("./category.estimate.lib.php");


$Script = "
<Script Language='JavaScript'>
function setCategory(cname,cid,depth,id){
	//document.location.href='estimate.product.php?view=innerview&cid='+cid+'&depth='+depth;
	window.frames['act'].location.href='estimate.product.php?view=innerview&cid='+cid+'&depth='+depth;
}

function deleteEstimateRelation(act, erid, cid, depth){
	window.frames['act'].location.href='estimate.product.act.php?act='+act+'&erid='+erid+'&cid='+cid+'&depth='+depth;
}
</Script>";


$Contents ="
<table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%' valign=top>
<tr>
	<td align='left' colspan=6 style='padding-bottom:5px;'> ".GetTitleNavigation("견적상품 등록", "견적센터 > 견적상품 등록 ")."</td>
</tr>

<tr>
	<td width='270' valign=top>
	<table cellpadding=0 cellspacing=0 border=0 bgcolor=#e2e2e2 style='border:3px solid #d8d8d8'>
		<tr>
			<td width=200 height=400 valign=top style='overflow:auto;padding:10px;'>
			<div id=TREE_BAR style=\"width:200px;height:420px;padding:5px;overflow:auto;margin:1;background-color:#ffffff\" >
			".Category()."
			</div>
			</td>
		</tr>
	</table>
	</td>
	<td width='90%'  valign=top id='estimate_product_list' style='padding-left:20px;'>".EstimateRelationList($cid, $depth)."</td>
</tr>
<tr>
	<td width='100%' colspan='2' valign=top>


	</td>

</tr>
<tr>
	<td bgcolor='D0D0D0' height='1' colspan='4'></td>
</tr>
</table>
<form action='./estimate.product.act.php'>
<input type=hidden name='ecid' value=''>
<input type=hidden name='pid' value=''>
</form>
";


//if(false){
if($view == "innerview"){

	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>".EstimateRelationList($cid,$depth)."</body></html>";

	echo "
	<Script>
	parent.document.getElementById('estimate_product_list').innerHTML = document.body.innerHTML;
	</Script>";
}else{
	$P = new LayOut;
	$P->addScript = $Script;
	$P->strLeftMenu = estimate_menu();
	$P->Navigation = "견적센타 > 견적상품 등록";
	$P->title = "견적상품 등록";
	$P->strContents = $Contents;
	$P->PrintLayOut();
}


function EstimateRelationList($ecid, $depth){
global $admininfo, $currency_display, $admin_config;

	$db = new Database;

	$sql = "select p. pname, p.pcode, p.sellprice, r.erid, p.id  from ".TBL_SHOP_ESTIMATE_RELATION."  r, ".TBL_SHOP_PRODUCT."  p where r.pid = p.id and r.ecid = '$ecid' ";


	$db->query($sql);

	$mString = "<table cellpadding=0 cellspacing=0 width=100% style='font-size:10px;' class='list_table_box'>";
	$mString .= "<tr align=center bgcolor=#efefef height=25><td class=s_td>상품코드</td><td class=m_td>상풍명</td><td class=m_td>가격</td><td class=e_td>삭제</td></tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=4 align=center>등록된 상품 정보가 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$mString .= "<tr height=25 bgcolor=#ffffff>
				<td class=table_td_white align=center>".$db->dt[pcode]."</td>
				<td class=table_td_white align=center>".cut_str($db->dt[pname],50)."</td>
				<td class=table_td_white align=center>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[sellprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
				<td class=table_td_white align=center><a href=\"JavaScript:deleteEstimateRelation('delete','".$db->dt[erid]."','$ecid','$depth')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a></td></tr>";

		}
	}
	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";
	$mString .="</table>
				<table cellpadding=0 cellspacing=0 width=100% style='margin-top:5px;'>";
	$mString .= "<tr align=right bgcolor=#ffffff height=30><td colspan=5 style='padding-right:20px;'><a href=\"javascript:PoPWindow('./estimate.pop.php?ecid=$ecid',920,700,'estimate')\"><img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif'></a></td></tr>";
	$mString = $mString."</table>

	";

	return $mString;
}
?>