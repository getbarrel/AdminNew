<?
include("../class/layout.class");

$db = new Database;

if ($act == "add" )
{

	if ($ESTIMATE_INTRA[$id])
	{
		unset($ESTIMATE_INTRA[$id]);
		$db->query("SELECT pname, sellprice, id,reserve,  coprice, pcode FROM ".TBL_SHOP_PRODUCT." WHERE id='$id'");

		if ($db->total){
			$db->fetch();
			$ESTIMATE_INTRA[$id] = array("pname"=>$db->dt[pname], "pcount"=>$pcount, "sellprice"=>$sellprice ,"id"=>$id, "cid"=>$cid, "pcode"=>$db->dt[pcode],"totalprice"=>$sellprice*$pcount);
		}



	}else{
		$db->query("SELECT pname, sellprice, id,reserve,coprice,  pcode FROM ".TBL_SHOP_PRODUCT." WHERE id='$id'");

		if ($db->total){
			$db->fetch();
			$ESTIMATE_INTRA[$id] = array("pname"=>$db->dt[pname], "pcount"=>$pcount, "sellprice"=>$sellprice ,"id"=>$id, "cid"=>$cid, "pcode"=>$db->dt[pcode],"totalprice"=>$sellprice*$pcount);
		}
	}



	session_register("ESTIMATE_INTRA");

}

if ($act == "del")
{
	unset($ESTIMATE_INTRA[$id]);

	session_register("ESTIMATE_INTRA");
}



$Script = "
<Script Language='JavaScript'>
function setCategory(cname,cid,depth,id){
	//document.location.href='estimate.intra.php?view=innerview&cid='+cid+'&depth='+depth;
	window.frames['act'].location.href='estimate.intra.php?view=innerview&cid='+cid+'&depth='+depth;
}

function deleteEstimate(act, est_ix){
	window.frames['act'].location.href='estimate.act.php?act='+act+'&est_ix='+est_ix;
}

function num_apply(frm, pid, select_option_id) {
	frm.quantity.value = parseInt(frm.quantity.value) ;
	window.frames['act'].location.href='estimate_countadd.php?PID='+pid+'&select_option_id='+select_option_id+'&act=mod&count='+frm.quantity.value;
}

function num_p(frm, pid) {
	frm.quantity.value = parseInt(frm.quantity.value) + 1;
	window.frames['act'].location.href='estimate_countadd.php?PID='+pid+'&act=mod&count='+frm.quantity.value;
}

function num_m(frm, pid) {

	if(frm.quantity.value > 1) {
		frm.quantity.value = parseInt(frm.quantity.value) -1;
		window.frames['act'].location.href='estimate_countadd.php?PID='+pid+'&act=mod&count='+frm.quantity.value;
	}else {
		frm.quantity.value = 1;
		alert('1개 이상 선택하셔야 합니다    ');
		return;
	}
}

function CheckValue(frm){

	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}

	if(frm.est_pass.value != frm.est_again.value){
		alert('비밀번호가 올바르지 않습니다. 다시 입력해주세요 ');
		frm.est_pass.value = '';
		frm.est_again.value = '';
		frm.est_pass.focus();
		return false;
	}

	return true;

}
</Script>";


$Contents ="
<table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%' valign=top>
<tr >
	<td align='left' colspan=6 style='padding-bottom:5px;'> ".GetTitleNavigation("내부견적서", "견적센터 > 내부견적서 ")."</td>
</tr>
<!--tr height=20>
	<td colspan=3 align=right style='padding-bottom:10px;'>
	".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 내부견적서</b></div>")."
	</td>
</tr-->
<tr>
	<td width='10%' valign=top>
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
	<td colspan=2 width='100%' style='padding-left:20px;'  valign=top id='estimate_product_list'>".PrintProductList($cid, $depth)."</td>
</tr>
<tr height=30 >
	<td style='padding-left:0px;' bgc colspan=3>
	<table width=100% border=0>
	<tr height=25><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>견적 제품정보</b></td>
	</table>
	</td>
</tr>
<tr >
	<td width='100%' colspan='3' valign=top style='padding-top:3px;'>
	<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center class='list_table_box'>
				<col width='*'>
				<col width='10%'>
				<col width='10%'>
				<col width='10%'>
				<col width='10%'>
				<tr align=center height=27>
					<td class=s_td>제 품 명</td>
					<td class=m_td>수량</td>
					<td class=m_td>판 매 가</td>
					<td class=m_td>합 계</td>
					<td class=e_td>취소</td>
				</tr>";
if($ESTIMATE_INTRA){

	for ( reset($ESTIMATE_INTRA); $key = key($ESTIMATE_INTRA); next($ESTIMATE_INTRA) ){

			$value = pos($ESTIMATE_INTRA);
			$pid = $value[id];
			$pname = $value[pname];
			$pname_str .= $value[pname];
			$pcount    = $value[pcount];
			$sellprice = $value[sellprice];
			$totalprice = $value[totalprice];
			$estimate_totalprice = $estimate_totalprice + $totalprice;

			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$pid.".gif")){
				$img_str = "".$admin_config[mall_data_root]."/images/product/s_".$pid.".gif";
			}else{
				$img_str = "../image/no_img.gif";
			}

			if(file_exists(PrintImage($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product", $pid, "s"))){
				$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $pid, "s");
			}else{
				$img_str = "../image/no_img.gif";
			}

$Contents .="
				<tr height=55>
					<form name='estimate_form_$pid' method='post' action='estimate.php?act=mod&id=$pid' onsubmit='return isNum(this.count)'>
					<td height='55' align='left' width=10% valign='middle' style='padding:5px;'>
						<table cellpadding=0 cellspacing=0>
							<tr>
								<td><a href='goods_view.php?id=$pid'><img src='$img_str' border=0 width=50 align=left></a></td>
								<td style='padding-left:10px;'><a href='goods_view.php?id=$pid'>$pname </a></td>
							</tr>
						</table>
					</td>
					<td nowrap>
						<table cellpadding=2 cellspacing=0 align='center'>
						<tr>
							<td ><input type=text name=quantity class=textbox value='$pcount' size=5 class=input2 style='text-align:right;padding:0 5px 0 0' ></td>
							<td> 개 </td>
							<td>
							<A href=\"javascript:num_apply(document.estimate_form_$pid,'$pid','');\"><img src='../images/".$admininfo["language"]."/bts_modify.gif'  border=0 align='absmiddle'></a>							
							</td>
						</tr>
						</table>
					</td>
					<td height='55' align=center valign='middle'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($sellprice)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td height='55' align=center valign='middle'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($totalprice)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td height='55' align=center valign='middle'><a href='estimate.intra.php?act=del&id=$pid'><img src='../image/icon_x.gif' border='0'></a></td>
					</form>
				</tr>
				";
	}
}else{
$Contents .="
				<tr height=50><td colspan=5 align=center>견적상품 내역이  존재 하지 않습니다.</td></tr>
				<!--tr>
					<td colspan=7 bgcolor='#D8D8D8'></td>
				</tr-->";

}


$Contents .="
				<tr>
					<td colspan='5'>
						<div style='width:100%;'>
							<table cellpadding='0' cellspacing='0' border='0' width='100%'>
								<tr>
									<td width='75%' height='27' align='right' class=s_td><b><font color='#333333'>총합계</font></b></td>
									<td align=right width='5%' colspan='3' class=m_td>-</td>
									<td align=center class=m_td><b> <font color='FF4E00'>".number_format($estimate_totalprice)." </font></b><font color='FF4E00'> 원</font></td>
									<td align=left width='5%' class=e_td>-</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>

			</table><br><br>
	</td>

</tr>
<tr height=30 >
	<td style='padding-left:0px;' bgc colspan=3>
	<table width=100% border=0>
	<tr height=25><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>견적의뢰 고객정보</b></td>
	</table>
	</td>
</tr>
<tr><form name='estimate_form' method='post' action='estimate.act.php' onsubmit='return CheckValue(this)'>
	<input type=hidden name='est_type' value='i'>
	<input type=hidden name='act' value='intra_insert'>
	<td style='padding-left:0px;'  colspan=3>
		<table width='100%' class='input_table_box' cellpadding=0 cellspacing='0'>
			<tr>
				<td class='input_box_title' nowrap><font color='#000000'> <b>상호/기관명</b></font></td>
				<td class='input_box_item text_box'><input type='text' name='est_company' size='20' maxlength='20' class='input textbox' value='' title='상호/기관명' validation='true'></td>
			</tr>
			<tr>
				<td class='input_box_title' nowrap><font color='#000000'>  <b>담당자</b></font></td>
				<td class='input_box_item'><input type='text' name='est_charger' size='20' maxlength='20' class='input textbox' value='' title='담당자' validation='true'></td>
			</tr>
			<tr>
				<td class='input_box_title'><font color='#000000'>  <b>이메일</b></font></td>
				<td class='input_box_item'><input type='text' name='est_email' size='50' maxlength='100' class='input textbox' value='' title='이메일' validation='true'></td>
			</tr>
			<tr>
				<td class='input_box_title'><font color='#000000'>  <b>휴대전화</b></font></td>
				<td class='input_box_item'>
					<input type='text' name='est_mobile1' size='3' maxlength='3' class='input textbox' value='' title='휴대전화' validation='true'> -
					<input type='text' name='est_mobile2' size='4' maxlength='4' class='input textbox' value='' title='휴대전화' validation='true'> -
					<input type='text' name='est_mobile3' size='4' maxlength='4' class='input textbox' value='' title='휴대전화' validation='true'></td>
			</tr>
			<tr>
				<td class='input_box_title'><font color='#000000'>  <b>납품예정일</b></font></td>
				<td class='input_box_item'><input type='text' name='est_plan_date' size='50' maxlength='100' class='input textbox' value='' title='납품예정일' validation='true'></td>
			</tr>
			<tr>
				<td class='input_box_title' ><font color='#000000'>  제품입고지</font></td>
				<td class='input_box_item'><input type='text' name='est_delivery_postion' size='50' maxlength='100' class='input textbox' value='' title='제품입고지' validation='false'></td>
			</tr>
			<tr>
				<td class='input_box_title'><font color='#000000'> 결제방법</font></td>
				<td class='input_box_item'> <input type='radio' name='est_order_method' class=nonborder value='1' checked>현금결제 <input type='radio' name='est_order_method' class=nonborder value='0' >카드결제 </td>
			</tr>


			<tr>
				<td class='input_box_title' nowrap><font color='#000000'>  <b>견적서 수령 방법</b></font></td>
				<td class='input_box_item'> <input type='radio' name='est_receive_method' class=nonborder value='1' checked>이메일 <input type='radio' name='est_receive_method' class=nonborder value='2' >FAX <input type='radio' name='est_receive_method' class=nonborder value='3' >방문상담/기타</td>
			</tr>
			<tr>
				<td class='input_box_title'><font color='#000000'>  <b>비밀번호</b></font></td>
				<td class='input_box_item'> <input type='password' name='est_pass' size='12' maxlength='30' class='input textbox' title='비밀번호' validation='true'> (비밀번호 숫자는 4자리만 가능합니다.) </td>
			</tr>
			<tr>
				<td class='input_box_title'><font color='#000000'>  <b>비밀번호확인</b></font></td>
				<td class='input_box_item'> <input type='password' name='est_again' size='12' maxlength='30' class='input textbox' title='비밀번호확인' validation='true'></td>
			</tr>
			<tr>
				<td class='input_box_title'>    <font color='#000000'>기타정보</font></td>
				<td bgcolor='#ffffff' style='padding:5px; 0px 5px 5px;'> <textarea name='est_etc' size='20' rows=5  cols=70 class='input textbox' style='width:97%;' ></textarea></td>
			</tr>
		</table>
		<br>

	</td>
</tr>
<tr height=50>
	<td align=center colspan=3>
	<input type=image src='../images/".$admininfo["language"]."/btn_s_ok.gif' aligb=absmiddle border=0>
	<br><br><br>
	</td>
</tr></form>
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

	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>".PrintProductList($cid,$depth)."</body></html>";

	echo "
	<Script>
	parent.document.getElementById('estimate_product_list').innerHTML = document.body.innerHTML;
	</Script>";
}else{
	$P = new LayOut;
	$P->addScript = $Script;
	$P->strLeftMenu = estimate_menu();
	$P->Navigation = "견적센타 > 내부견적서";
	$P->title = "내부견적서";
	$P->strContents = $Contents;
	$P->PrintLayOut();
}


function PrintProductList($cid, $depth){
	global $start,$page, $orderby, $admin_config, $DOCUMENT_ROOT, $currency_display, $admin_config;

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$db = new Database;

	$db->query("SELECT distinct p.id,p.pname, p.sellprice, p.reserve FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and r.cid LIKE '".substr($cid,0,($depth+1)*3)."%' and p.disp = 1   ");
	$total = $db->total;


	$db->query("SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve  FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and r.cid LIKE '".substr($cid,0,($depth+1)*3)."%' and p.disp = 1 order by vieworder limit $start,$max");


	$mString = "<table cellpadding=0 cellspacing=0 width=100% border=0  style='font-size:10px;' class='list_table_box'>";
	$mString .= "<col width='*'>
			<col width='15%'>
			<col width='15%'>
			<col width='15%'>
			<col width='15%'>
			<tr align=center bgcolor=#efefef height=25>
			<td class=m_td >제 품 명</td>
			<td class=m_td >가격</td>
			<td class=m_td  nowrap>회원 판매가</td>
			<td class=m_td >딜러가</td>
			<td class=e_td >대리점가</td>
			</tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=7 align=center>등록된 상품 정보가 없습니다.</td></tr>";
		$mString = $mString."";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			/*if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif")){
				$img_str = $admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif";
			}else{
				$img_str = "../image/no_img.gif";
			}*/
			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[id], "c"))){
				$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[id], "c");
			}else{
				$img_str = "../image/no_img.gif";
			}
			$mString .= "<tr height=55 bgcolor=#ffffff>
						<td class='list_box_td ' align='center' style='padding:10px;'>
							<table>
							<tr>
							<td><img src='$img_str' align=absmiddle style='margin-right:10px;'></td>
							<td align=left>".$db->dt[pcode]."<br><b>".cut_str($db->dt[pname],60)."</b></td>
							</tr>
							</table>
						</td>
						<td class='list_box_td '><a href='estimate.intra.php?act=add&id=".$db->dt[id]."&pcount=1&sellprice=".$db->dt[sellprice]."'> ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[sellprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</a> </td>
						<td class='list_box_td list_bg_gray'><a href='estimate.intra.php?act=add&id=".$db->dt[id]."&pcount=1&sellprice=".$db->dt[prd_member_price]."'> ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[prd_member_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</a></td>
						<td class='list_box_td '><a href='estimate.intra.php?act=add&id=".$db->dt[id]."&pcount=1&sellprice=".$db->dt[prd_dealer_price]."'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[prd_dealer_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</a></td>
						<td class='list_box_td list_bg_gray'><a href='estimate.intra.php?act=add&id=".$db->dt[id]."&pcount=1&sellprice=".$db->dt[prd_agent_price]."'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[prd_agent_price])."".$currency_display[$admin_config["currency_unit"]]["back"]."</a></td>
						<!--td class=table_td_white align=center><a href=\"JavaScript:deleteCategory('delete','".$db->dt[erid]."','$pid')\"><img src='../funny/image/icon_x.gif' border=0></a></td-->
						</tr>";
			$mString .= "";
		}
	}

	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max");
	$mString .= "</table>
				<table cellpadding=0 cellspacing=0 width=100%>";
	$mString .= "<tr align=center  height=50><td colspan=7 align=center><b>* 해당되는 가격을 클릭하시면 견적서에 추가 됩니다.</b></td></tr>";
	$mString .= "<tr align=right bgcolor=#ffffff height=30><td colspan=7 align=center>$str_page_bar</td></tr>";
	$mString = $mString."</table>

	";

	return $mString;

}



function Category($templet_src="..")
{
$mdb = new Database;

	global $id;
$source_path = "/manage";
$m_string = "
<script language='JavaScript' src='$templet_src/include/manager.js'></script>
<script language='JavaScript' src='$templet_src/include/Tree.js'></script>
<script>

/*	 Create Tree		*/
	var tree = new Tree();
	tree.color = 'black';
	tree.bgColor = 'white';
	tree.borderWidth = 0;


/*	Create Root node	*/
	var rootnode = new TreeNode('상품카테고리', '$templet_src/resources/ServerMag_Etc_Root.gif','$templet_src/resources/ServerMag_Etc_Root.gif');
	rootnode.action = \"setCategory('product category','000000000000000',-1,'".$id."')\";
	rootnode.expanded = true;";

$mdb->query("SELECT * FROM ".TBL_SHOP_CATEGORY_INFO." order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");

$total = $mdb->total;
for ($i = 0; $i < $mdb->total; $i++)
{

	$mdb->fetch($i);

	if ($mdb->dt["depth"] == 0){
		$m_string = $m_string.PrintNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$templet_src);
	}else if($mdb->dt["depth"] == 1){
		$m_string = $m_string.PrintGroupNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$templet_src);
	}else if($mdb->dt["depth"] == 2){
		$m_string = $m_string.PrintGroupNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$templet_src);
	}else if($mdb->dt["depth"] == 3){
		$m_string = $m_string.PrintGroupNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$templet_src);
	}else if($mdb->dt["depth"] == 4){
		$m_string = $m_string.PrintGroupNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$templet_src);
	}
}

	$m_string = $m_string."tree.addNode(rootnode);";

$m_string = $m_string."
</script>
<form>
<div id=TREE_BAR style='margin:5;'>
<script>
tree.draw();

tree.nodes[0].select();
</script>
</div>
</form>";

return $m_string;
}




function PrintRootNode($cname,$templet_src){
	$vPrintRootNode = "var rootnode = new TreeNode('$cname', '$templet_src/resources/ServerMag_Etc_Root.gif','$templet_src/resources/ServerMag_Etc_Root.gif');/n
			rootnode.expanded = true;\n\n";

	return $vPrintRootNode;
}

function PrintNode($cname,$cid,$depth,$templet_src)
{
	global $id;
	$cid1 = substr($cid,0,3);
	$cid2 = substr($cid,3,3);
	$cid3 = substr($cid,6,3);
	$cid4 = substr($cid,9,3);
	$cid5 = substr($cid,12,3);

	return "	var node$cid = new TreeNode('$cname', '$templet_src/resources/Common_TreeNode_CodeManage.gif', '$templet_src/resources/Common_TreeNode_CodeManage.gif');
	node$cid.expanded = true;
	node$cid.action = \"setCategory('$cname','$cid',$depth,'$id')\";
	rootnode.addNode(node$cid);\n\n";
}

function PrintGroupNode($cname,$mcid,$depth,$templet_src)
{
	global $id,$cid;
	$cid1 = substr($mcid,0,3);
	$cid2 = substr($mcid,3,3);
	$cid3 = substr($mcid,6,3);
	$cid4 = substr($mcid,9,3);
	$cid5 = substr($mcid,12,3);

	$Parentdepth = $depth - 1;

	if ($depth+1 == 1){
		$cid1 = "000";
	}else if($depth+1 == 2){
		$cid2 = "000";
	}else if($depth+1 == 3){
		$cid3 = "000";
	}else if($depth+1 == 4){
		$cid4 = "000";
	}else if($depth+1 == 5){
		$cid5 = "000";
	}

	$parent_cid = "$cid1$cid2$cid3$cid4$cid5";

	if ($depth ==1){
		$ParentNodeCode = "node$parent_cid";
	}else if($depth ==2){
		$ParentNodeCode = "groupnode$parent_cid";
	}else if($depth ==3){
		$ParentNodeCode = "groupnode$parent_cid";
	}else if($depth ==4){
		$ParentNodeCode = "groupnode$parent_cid";
	}else if($depth ==5){
		$ParentNodeCode = "groupnode$parent_cid";
	}

	if ($cid == $mcid){
		$expandstring = "true";
	}else{
		$expandstring = "false";
	}

	return "		var groupnode$mcid = new TreeNode('$cname',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);
		groupnode$mcid.tooltip = '$cname';
		groupnode$mcid.id ='nodeid$mcid';
		groupnode$mcid.expanded = $expandstring;
		groupnode$mcid.action = \"setCategory('$cname','$mcid',$depth,'$id')\";
		$ParentNodeCode.addNode(groupnode$mcid);\n\n";
}
?>
