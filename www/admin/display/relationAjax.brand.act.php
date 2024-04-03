<?
include("$DOCUMENT_ROOT/shop/common/util.php");
include("../class/layout.class");
include('../../include/xmlWriter.php');
header("Pragma: no-cache");
header('Content-Type: text/xml; charset=utf-8');

function PrintProductListXML(){
	global $start,$page, $orderby, $admin_config;
	global $max, $page, $start, $mode;
	global $brand_name, $search_type, $search_text;
	global $admininfo;

	if($max == ""){
		$max = 5;
		$max = $max;
	}

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$db = new MySQL;

	if($mode == "list"){
		$sql = "SELECT COUNT(DISTINCT(ccd.company_id)) 
					FROM common_company_detail ccd, common_seller_detail csd 
					where ccd.company_id=csd.company_id and ccd.com_type ='S' 
					and ccd.seller_auth = 'Y' and csd.minishop_yn='Y' ";

		$sql = "SELECT count(*) as total 
					FROM shop_brand mb 					
					LEFT JOIN shop_brand_relation br ON mb.b_ix=br.b_ix AND br.basic='1'  
					where mb.b_ix IS NOT NULL 
					 ";

		$db->query($sql);
		$db->fetch();
		$total = $db->dt[0];

		$sql = "SELECT DISTINCT mb.* , count(*) as goods_cnt, br.cid 
					FROM shop_brand mb 					
					LEFT JOIN shop_brand_relation br ON mb.b_ix=br.b_ix AND br.basic='1'  
					where mb.b_ix IS NOT NULL 
					group by mb.b_ix 
					order by mb.regdate desc
					LIMIT $start, $max ";

		$db->query($sql);
	}else{
		if($search_type && $search_text){
			//$search_text = iconv("utf-8","CP949", $search_text);
			$search_str = " and ".$search_type." LIKE '%".$search_text."%'";
		}
		
		$sql = "SELECT count(*) as total
					FROM shop_brand mb 					
					LEFT JOIN shop_brand_relation br ON mb.b_ix=br.b_ix AND br.basic='1'  
					where mb.b_ix IS NOT NULL 
					".$search_str." ";

		$db->query($sql);
		$db->fetch();
		$total = $db->dt[0];

		$sql = "SELECT DISTINCT mb.* , count(*) as goods_cnt, br.cid 
					FROM shop_brand mb 					
					LEFT JOIN shop_brand_relation br ON mb.b_ix=br.b_ix AND br.basic='1'  
					where mb.b_ix IS NOT NULL 
					".$search_str."
					order by mb.regdate desc
					LIMIT $start, $max ";

		$db->query($sql);
	}
	$relation_goods = $db->fetchall();
	
	if($db->total){
		
		$xml = new XmlWriter_();
		$xml->push('relationBrands', array('total'=>$total));

		foreach ($relation_goods as $relation_good) {

			$xml->push('brands', array('b_ix' => $relation_good[b_ix]));
			$xml->element('b_ix', $relation_good[b_ix]);
			//$xml->element('brand_name', "tb_".$relation_good[brand_name]);
			$xml->element('brand_name', strip_tags(htmlspecialchars($relation_good[brand_name])));    //
			//$xml->element('sellprice', strip_tags(htmlspecialchars($relation_good[2])));
			$xml->element('img_src',  $admin_config['mall_data_root'].'/images/brand/'.$relation_good[b_ix].'/brand_'.$relation_good[b_ix].'.gif' );
			$xml->pop();

		}

		$xml->pop();
		return $xml->getXml();
	}

}


if($mode == "list_pageing"){
	$db = new MySQL;
	//if($admininfo[admin_level] < 9){
	//	$product_company_id_str = " and p.admin = '".$admininfo[company_id]."' ";
	//}
	//echo ("SELECT count(*) as total FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and r.cid LIKE '".substr($cid,0,($depth+1)*3)."%' and p.disp = 1;");

	if($cid){
		$search_str = " and r.cid LIKE '".substr($cid,0,($depth+1)*3)."%' ";
	}

	$sql = "SELECT count(*) as total
					FROM shop_brand mb 					
					LEFT JOIN shop_brand_relation br ON mb.b_ix=br.b_ix AND br.basic='1'  
					where mb.b_ix IS NOT NULL 
					".$search_str." ";

	$db->query($sql);
	$db->fetch();

	echo $db->dt[total];
}

if($mode == "search_list_pageing"){
	$db = new MySQL;

	if($search_type && $search_text){
		//$search_text = "째징쨔??
		$search_text = iconv("utf-8","CP949", $search_text);
		$search_str = " and ".$search_type." LIKE '%".$search_text."%'";
	}
	if($company_id){
		$company_id_str = " and p.admin = '".$company_id."'";
	}

	if($admininfo[admin_level] < 9){
		$product_company_id_str = " and p.admin = '".$admininfo[company_id]."' ";
	}

	$sql = "SELECT count(*) as total
					FROM shop_brand mb 					
					LEFT JOIN shop_brand_relation br ON mb.b_ix=br.b_ix AND br.basic='1'  
					where mb.b_ix IS NOT NULL 
					".$search_str." ";

	//echo ("SELECT count(*) as total FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid  and p.disp = 1 $search_str $company_id_str ");
	$db->query($sql);

	$db->fetch();

	echo $db->dt[total];
}

if($mode == "list" || $mode == "search_list"){
echo PrintProductListXML();
}

if($mode == "insert"){

	$db = new MySQL;


	$db->query("select max(vieworder)+1 as vieworder from ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp  where event_ix = '$event_ix' ");
	$db->fetch();
	$vieworder = $db->dt[vieworder];

	$db->query("select event_ix from ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp  where pid = '$pid' and event_ix = '$event_ix' ");

	if($db->total){
		echo "<script>alert('L쨔?쨉低쨉쨩都求褸');</script>
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

		$db = new MySQL;



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

	$db = new MySQL;

	$db->query("SELECT distinct p.id,p.pname, p.sellprice,  p.reserve FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and r.cid LIKE '".substr($cid,0,($depth+1)*3)."%' and p.disp = 1   ");
	$total = $db->total;


	$db->query("SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve  FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and r.cid LIKE '".substr($cid,0,($depth+1)*3)."%' and p.disp = 1 order by vieworder desc limit $start,$max");


	$mString = "<table cellpadding=0 cellspacing=0 width=100% bgcolor=silver style='font-size:10px;'>";

/*	$mString .= "<tr align=center bgcolor=#efefef height=25>
			<td class=s_td><!--input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.estimatefrm)'--></td>
			<td class=m_td width=20% >쨩?湄屎d>
			<td class=m_td width=60%>쨩?낯類d>
			<td class=m_td width=20%>째징째蒡/td>
			<!--td width=70 class=e_td>쨩??/td-->
			</tr>";
*/
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=5 align=center>쨉低쨉쨩d쨘쨍째징 쩐占승닝?/td></tr>";
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

	$db = new MySQL;

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
		$mString = $mString."<tr bgcolor=#ffffff height=100%><td colspan=5 align=center class='small'>쨉低쨉L쨘짜트/짹?? 쨩?d쨘쨍째징 쩐占승닝? <br> 캡 쨩??쨩 L째占싱쨉藥≤絿첩?r>L쨘짜트/짹?? 쨩??쨍쨌?쨉低쨉絳求</td></tr>";
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

	$db = new MySQL;

	$sql = "SELECT distinct p.id,p.pname, p.sellprice,  p.reserve, event_ix
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix' and p.disp = 1   ";
	$db->query($sql);
	$total = $db->total;

	$sql = "SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, event_ix, erp_ix
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix'  and p.disp = 1 order by erp.vieworder  limit $start,$max";
	$db->query($sql);




	if ($db->total == 0){
		$mString = "<table cellpadding=0 cellspacing=0 width=100% height=100%  bgcolor=silver style='font-size:10px;'>";
		$mString = $mString."<tr bgcolor=#ffffff height=100%><td colspan=5 align=center class='small'>쨉低쨉L쨘짜트/짹?? 쨩?d쨘쨍째징 쩐占승닝? <br> 캡 쨩??쨩 L째占싱쨉藥≤絿첩?r>L쨘짜트/짹?? 쨩??쨍쨌?쨉低쨉絳求</td></tr>";
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