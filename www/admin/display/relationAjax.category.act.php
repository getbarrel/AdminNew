<?
include($_SERVER["DOCUMENT_ROOT"]."/shop/common/util.php");
include("../class/layout.class");
include('../../include/xmlWriter.php');
header("Pragma: no-cache");
//header('Content-type: text/xml;');
//header("Content-type: charset=ks_c_5601-1987");
header('Content-Type: text/xml; charset=utf-8');

//header("Content-Type: text/html; charset=KS_C_5601-1987");

function PrintProductListXML($cid, $depth){
	global $start,$page, $orderby, $admin_config;
	global $max, $page, $start, $mode;
	global $company_id, $search_type, $search_text;
	global $admininfo;

	if($max == ""){
		$max = 5; //?L쨋쨈걔뮤?}else{
		$max = $max;
	}

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$db = new Database;

	if($admininfo[admin_level] < 9){
		$product_company_id_str = " and p.admin = '".$admininfo[company_id]."' ";
	}

	if($mode == "list"){

		$db->query("SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,p.reserve, p.brand_name  FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and r.cid LIKE '".substr($cid,0,($depth+1)*3)."%' and p.disp = 1 $product_company_id_str order by vieworder LIMIT $start, $max ");
	}else{
		if($search_type && $search_text){
			//$search_text = iconv("utf-8","CP949", $search_text);
			$search_str = " and ".$search_type." LIKE '%".$search_text."%'";
		}
		if($company_id){
			$company_id_str = " and p.admin = '".$company_id."'";
		}



		$db->query("SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, p.brand_name  FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and p.disp = 1 $search_str $company_id_str $product_company_id_str order by vieworder LIMIT $start, $max ");
	}
	$relation_goods = $db->fetchall();

	if($db->total){
		$xml = new XmlWriter_();
		$xml->push('relationProducts');

		foreach ($relation_goods as $relation_good) {

			$xml->push('products', array('pid' => $relation_good[0]));
			$xml->element('pid', $relation_good[0]);
			$xml->element('tb_pid', "tb_".$relation_good[0]);
			//$xml->element('pname', iconv('EUC-KR','UTF-8',strip_tags(htmlspecialchars($relation_good[3]))));    //
			$xml->element('pname', strip_tags(htmlspecialchars($relation_good[3])));    //
			//$xml->element('sellprice', iconv('EUC-KR','UTF-8',number_format($relation_good[4]))." ");
			$xml->element('sellprice', number_format($relation_good[4])." ");
			//$xml->element('pname', $relation_good[3]);    //iconv('EUC-KR','UTF-8',strip_tags($relation_good[3]))
			$xml->element('img_src', $admin_config[mall_data_root]."/images/product/c_".$relation_good[0].".gif");
			$xml->element('brand_name', strip_tags(htmlspecialchars($relation_good[10])));
			$xml->pop();

		}

		$xml->pop();
		return $xml->getXml();
	}

}
if($mode == "list_pageing"){
	$db = new Database;
	if($admininfo[admin_level] < 9){
		$product_company_id_str = " and p.admin = '".$admininfo[company_id]."' ";
	}
	//echo ("SELECT count(*) as total FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and r.cid LIKE '".substr($cid,0,($depth+1)*3)."%' and p.disp = 1;");
	$db->query("SELECT count(distinct p.id) as total FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid  and r.cid LIKE '".substr($cid,0,($depth+1)*3)."%' and p.disp = 1 $product_company_id_str ;");
	$db->fetch();

	echo $db->dt[total];
}

if($mode == "search_list_pageing"){
	$db = new Database;

	if($search_type && $search_text){
		//$search_text = "째징쨔?
		//$search_text = iconv("utf-8","CP949", $search_text);
		$search_str = " and ".$search_type." LIKE '%".$search_text."%'";
	}
	if($company_id){
		$company_id_str = " and p.admin = '".$company_id."'";
	}

	if($admininfo[admin_level] < 9){
		$product_company_id_str = " and p.admin = '".$admininfo[company_id]."' ";
	}

	//echo ("SELECT count(*) as total FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid  and p.disp = 1 $search_str $company_id_str ");
	$db->query("SELECT count(distinct p.id) as total FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid  and p.disp = 1 $search_str $company_id_str $product_company_id_str ");

	$db->fetch();

	echo $db->dt[total];
}

if($mode == "list" || $mode == "search_list"){
echo PrintProductListXML($cid,$depth);
}

if($mode == "insert"){

	$db = new Database;


	$db->query("select max(vieworder)+1 as vieworder from ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp  where event_ix = '$event_ix' ");
	$db->fetch();
	$vieworder = $db->dt[vieworder];

	$db->query("select event_ix from ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp  where pid = '$pid' and event_ix = '$event_ix' ");

	if($db->total){
		echo "<script>alert('L쨔?關?關?????');</script>
		<script type='text/javascript'>
		parent.Sortable.create('sortlist',
		{

			onUpdate: function()
			{
				//alert(parent.Sortable.serialize('sortlist'));
				new parent.Ajax.Request('/admin/marketting/event.act.php',
				{
					method: 'POST',
					parameters: parent.Sortable.serialize('sortlist')+'&act=vieworder_update&event_ix=$event_ix',
					onComplete: function(transport){
					//alert(transport.responseText);
					}
				});
			}
		});
		</script>";
	}else{

		$sql = "insert into ".TBL_SHOP_EVENT_PRODUCT_RELATION." (erp_ix,pid,event_ix,vieworder, regdate) values('$erp_ix','$pid','$event_ix','$vieworder',NOW())";

		$db->query($sql);


		echo "
		<html>
		<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
		<body>
		<div id='relation_product'>
		".relationProductList($event_ix)."
		</div>
		</body>
		</html>\n
		<Script Language='JavaScript'>
				parent.document.getElementById('relation_product').innerHTML=document.getElementById('relation_product').innerHTML;
		</script>
<script type='text/javascript'>
parent.Sortable.create('sortlist',
{

	onUpdate: function()
	{
		//alert(parent.Sortable.serialize('sortlist'));
		new parent.Ajax.Request('/admin/marketting/event.act.php',
		{
			method: 'POST',
			parameters: parent.Sortable.serialize('sortlist')+'&act=vieworder_update&event_ix=$event_ix',
			onComplete: function(transport){
			//alert(transport.responseText);
			}
		});
	}
});
</script>";
	}

}

if($mode == "delete"){

		$db = new Database;



		$db->query("delete from ".TBL_SHOP_EVENT_PRODUCT_RELATION." where erp_ix = '$erp_ix' ");


		echo "
		<html>
		<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
		<body>
		<div id='relation_product'>
		".relationProductList($event_ix)."
		</div>
		</body>
		</html>\n
		<Script Language='JavaScript'>
				parent.document.getElementById('relation_product').innerHTML=document.getElementById('relation_product').innerHTML;
		</script>
<script type='text/javascript'>
parent.Sortable.create('sortlist',
{

	onUpdate: function()
	{
		//alert(parent.Sortable.serialize('sortlist'));
		new parent.Ajax.Request('/admin/marketting/event.act.php',
		{
			method: 'POST',
			parameters: parent.Sortable.serialize('sortlist')+'&act=vieworder_update&event_ix=$event_ix',
			onComplete: function(transport){
			//alert(transport.responseText);
			}
		});
	}
});
</script>
";

}


function PrintProductList($cid, $depth){
	global $start,$page, $orderby, $admin_config;

	$max = 105;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$db = new Database;

	$db->query("SELECT distinct p.id,p.pname, p.sellprice,  p.reserve FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and r.cid LIKE '".substr($cid,0,($depth+1)*3)."%' and p.disp = 1   ");
	$total = $db->total;


	$db->query("SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve  FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and r.cid LIKE '".substr($cid,0,($depth+1)*3)."%' and p.disp = 1 order by vieworder limit $start,$max");


	$mString = "<table cellpadding=0 cellspacing=0 width=100% bgcolor=silver style='font-size:10px;'>";

/*	$mString .= "<tr align=center bgcolor=#efefef height=25>
			<td class=s_td><!--input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.estimatefrm)'--></td>
			<td class=m_td width=20% >???td>
			<td class=m_td width=60%>?類d>
			<td class=m_td width=20%>째징째?/td>
			<!--td width=70 class=e_td></td-->
			</tr>";
*/
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=5 align=center>關?關??d쨘쨍째징 쩐?쨈?</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$mString .= "<tr height=27 bgcolor=#ffffff>
						<td class=table_td_white align=left style='padding:5px;'>
							<div id='".$db->dt[id]."' style='cursor:hand;'><!--dragable='true' ondragstart='return false'  -->
								<table bgcolor=#ffffff>
									<tr>
										<td><img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif' align=absmiddle></td>
										<td> ".cut_str($db->dt[pname],70)."</td>
									</tr>
								</table>
							</div>
						</td>
						</tr>";
			$mString .= "<tr height=1><td background='../image/dot.gif'></td></tr>";

			$mScript .= "new parent.Draggable('parent.".$db->dt[id]."', {revert: true});\n";
		}
	}
	//$str_page_bar = product_page_bar($total, $page,$max, "&view=innerview&max=$max&cid=$cid");

	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";
	$mString .= "<tr align=right bgcolor=#ffffff height=30><td colspan=5 align=left >$str_page_bar</td></tr>";
	$mString = $mString."</table>

	";

	$mScript = "<script>\n$mScript</script>";

	return $mString.$mScript;

}


function relationProductList($event_ix){

	global $start,$page, $orderby, $admin_config, $erpid;

	$max = 105;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$db = new Database;

	$sql = "SELECT distinct p.id,p.pname, p.sellprice,  p.reserve, event_ix
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix' and p.disp = 1   ";
	$db->query($sql);
	$total = $db->total;

	$sql = "SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, event_ix, erp_ix, erp.vieworder
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix'  and p.disp = 1 order by erp.vieworder asc limit $start,$max";
	$db->query($sql);




	if ($db->total == 0){
		$mString = "<table cellpadding=0 cellspacing=0 width=100% height=100%  bgcolor=silver style='font-size:10px;'>";
		$mString = $mString."<tr bgcolor=#ffffff height=100%><td colspan=5 align=center class='small'>關?關?L쨘占/짹餓 ?d쨘쨍째징 쩐?쨈?. <br> ?캡 ?L째??關藥≤?첩r>L쨘占/짹餓 ?쨍쨌?關?關??? </td></tr>";
	}else{
		$mString = "<ul id='sortlist'>";

		$i=0;



		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);

			$mString .= "<li id='image_".$db->dt[id]."' >
						<table width=99% border=0 >
						<col width='60'>
						<col width='*'>
						<col width='60'>
						<tr height=27 bgcolor=#ffffff >
						<td class=table_td_white align=center style='padding:5px;'>
							<img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif'>
						</td>
						<td class=table_td_white>".cut_str($db->dt[pname],30)."</td>
						<td><a href='relation.category.act.php?mode=delete&event_ix=".$event_ix."&erp_ix=".$db->dt[erp_ix]."'  target=act><img src='../image/btc_del.gif'></a></td>
						</tr><tr height=1><td colspan=5 background='../image/dot.gif'></td></tr>
						</table></li>";
		}
	}

	$mString = $mString."</ul>";

	return $mString;

}



function relationProductList2($event_ix){

	global $start,$page, $orderby, $admin_config;

	$max = 105;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$db = new Database;

	$sql = "SELECT distinct p.id,p.pname, p.sellprice, p.reserve, event_ix
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix' and p.disp = 1   ";
	$db->query($sql);
	$total = $db->total;

	$sql = "SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice, p.reserve, event_ix, erp_ix
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix'  and p.disp = 1 order by vieworder limit $start,$max";
	$db->query($sql);




	if ($db->total == 0){
		$mString = "<table cellpadding=0 cellspacing=0 width=100% height=100%  bgcolor=silver style='font-size:10px;'>";
		$mString = $mString."<tr bgcolor=#ffffff height=100%><td colspan=5 align=center class='small'>關?關?L쨘占/짹餓 ?d쨘쨍째징 쩐?쨈?. <br> ?캡 ?L째??關藥≤?첩r>L쨘占/짹餓 ?쨍쨌?關?關??? </td></tr>";
	}else{
		$mString = "<table cellpadding=0 cellspacing=0 width=100% bgcolor=silver style='font-size:10px;'>";

		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$mString .= "<tr height=27 bgcolor=#ffffff >
						<td class=table_td_white align=center style='padding:5px;'>
							<img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif'>
						</td>
						<td class=table_td_white>".cut_str($db->dt[pname],30)."</td>
						<td><a href='relation.category.act.php?mode=delete&event_ix=".$event_ix."&erp_ix=".$db->dt[erp_ix]."'  target=act><img src='../image/btc_del.gif'></a></td>
						</tr>";
			$mString .= "<tr height=1><td colspan=5 background='../image/dot.gif'></td></tr>";
		}
	}
	//$str_page_bar = product_page_bar($total, $page,$max, "&view=innerview&max=$max&cid=$cid");

	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";
	$mString .= "<tr align=right bgcolor=#ffffff height=30><td colspan=5 align=left >$str_page_bar</td></tr>";
	$mString = $mString."</table>

	";

	return $mString;

}


?>