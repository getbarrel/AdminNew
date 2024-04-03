<?
include($_SERVER["DOCUMENT_ROOT"]."/shop/common/util.php");
include("../class/layout.class");

session_start();


if(!$relation_view_type){
	$relation_view_type = $_relation_view_type;
}

if($relation_view_type == "small"){
	$ajax_add_string = "tag:'img',overlap:'horizontal',constraint:false, ";
	$_relation_view_type = $relation_view_type;
	session_register("_relation_view_type");	
}else{
	$_relation_view_type = $relation_view_type;
	session_register("_relation_view_type");	
}



if($mode == "list"){

echo "
<html>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<body>
<div id='reg_product'>
".PrintProductList($cid,$depth)."
</div>

</body>
</html>\n	
<Script Language='JavaScript'>		
		parent.document.getElementById('reg_product').innerHTML=document.getElementById('reg_product').innerHTML;
</script>";

}

if($mode == "view"){
	
		$mstirng = "
		<html>
		<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
		<body>
		<div id='relation_product'>
		".relationProductList($pid, $relation_view_type)."
		</div>
		
		</body>
		</html>\n	
		<Script Language='JavaScript'>		
				parent.document.getElementById('relation_product').innerHTML=document.getElementById('relation_product').innerHTML;";
				
				if($relation_view_type == "small"){
	$mstirng .= "parent.document.getElementById('relation_view_button_area').innerHTML= \"<a href='relation.category.act.php?mode=view&relation_view_type=large&pid=$pid' target=act>큰아이콘보기</a>\";";	
				}else{
	$mstirng .= "parent.document.getElementById('relation_view_button_area').innerHTML= \"<a href='relation.category.act.php?mode=view&relation_view_type=small&pid=$pid' target=act>작은아이콘 보기</a>\";";						
				}
		$mstirng .= "		
		</script>
		<script type='text/javascript'>
		parent.Sortable.create('sortlist' ,
		{
			$ajax_add_string
			onUpdate: function()
			{
				//alert(parent.Sortable.serialize('sortlist'));
				new parent.Ajax.Request('/admin/product/product_input.act.php',
				{
					method: 'POST',
					parameters: parent.Sortable.serialize('sortlist')+'&act=vieworder_update&pid=$pid',
					onComplete: function(transport){
					//alert(transport.responseText);
					}
				});
			}
		});
		</script>";
	
		echo $mstirng;
}

if($mode == "insert"){
	
	$db = new Database;
	
	
	$db->query("select rp_ix from ".TBL_SHOP_RELATION_PRODUCT." where pid = '$pid' and rp_pid = '$rp_pid' ");
	
	if($db->total){
		echo "<script language='javascript' src='../_language/language.php'></script><script>alert(language_data['relation.category.act.php']['A'][language]);</script>";	//'이미 등록된 상품입니다.'
	}else{
		$db->query("select max(vieworder) as vieworder from ".TBL_SHOP_RELATION_PRODUCT." where pid = '$pid'  ");
		$db->fetch();
		$vieworder = $db->dt[vieworder];
		
		$db->query("insert into ".TBL_SHOP_RELATION_PRODUCT."(rp_ix,pid,rp_pid,vieworder, regdate) values('','$pid','$rp_pid','$vieworder',NOW())");
	
	
		echo "
		<html>
		<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
		<body>
		<div id='relation_product'>
		".relationProductList($pid, $relation_view_type)."
		</div>
		
		</body>
		</html>\n	
		<Script Language='JavaScript'>		
				parent.document.getElementById('relation_product').innerHTML=document.getElementById('relation_product').innerHTML;
		</script>
		<script type='text/javascript'>
		parent.Sortable.create('sortlist' ,
		{
			$ajax_add_string
			onUpdate: function()
			{
				//alert(parent.Sortable.serialize('sortlist'));
				new parent.Ajax.Request('/admin/product/product_input.act.php',
				{
					method: 'POST',
					parameters: parent.Sortable.serialize('sortlist')+'&act=vieworder_update&pid=$pid',
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
	
	
	
		$db->query("delete from ".TBL_SHOP_RELATION_PRODUCT." where rp_ix = '$rp_ix' ");
	
	
		echo "
		<html>
		<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
		<body>
		<div id='relation_product'>
		".relationProductList($pid, $relation_view_type)."
		</div>
		
		</body>
		</html>\n	
		<Script Language='JavaScript'>		
				parent.document.getElementById('relation_product').innerHTML=document.getElementById('relation_product').innerHTML;
		</script>
		<script type='text/javascript'>
		parent.Sortable.create('sortlist' ,
		{
			$ajax_add_string
			onUpdate: function()
			{
				//alert(parent.Sortable.serialize('sortlist'));
				new parent.Ajax.Request('/admin/product/product_input.act.php',
				{
					method: 'POST',
					parameters: parent.Sortable.serialize('sortlist')+'&act=vieworder_update&pid=$pid',
					onComplete: function(transport){
					//alert(transport.responseText);
					}
				});
			}
		});
		</script>";
	
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
	
	
	$db->query("SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve  FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r where p.id = r.pid and r.cid LIKE '".substr($cid,0,($depth+1)*3)."%' and p.disp = 1 order by p.vieworder desc limit $start,$max");		
	
	
	$mString = "<table cellpadding=0 cellspacing=0 width=100% bgcolor=silver style='font-size:10px;'>";	

/*	$mString .= "<tr align=center bgcolor=#efefef height=25>
			<td class=s_td><!--input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.estimatefrm)'--></td>
			<td class=m_td width=20% >상품코드</td>
			<td class=m_td width=60%>상풍명</td>
			<td class=m_td width=20%>가격</td>
			<!--td width=70 class=e_td>삭제</td-->
			</tr>";
*/			
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=5 align=center>등록된 상품 정보가 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);			
			$mString .= "<tr height=27 bgcolor=#ffffff>
						<td class=table_td_white align=left style='padding:5px;'>
							<div dragable='true' id='".$db->dt[id]."' ondragstart='return false' style='cursor:move;'>
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
		}
	}
	//$str_page_bar = product_page_bar($total, $page,$max, "&view=innerview&max=$max&cid=$cid");
	
	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";
	$mString .= "<tr align=right bgcolor=#ffffff height=30><td colspan=5 align=left >$str_page_bar</td></tr>";
	$mString = $mString."</table>
	
	";
	
	return $mString;
	
}

function relationProductList($pid, $r_view_type=""){

	global $start,$page, $orderby, $admin_config ;
	
	$max = 105;
	
	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}
		
	$db = new Database;
	
	$db->query("SELECT distinct p.id,p.pname, p.sellprice,  p.reserve, rp_ix FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_RELATION_PRODUCT." rp where p.id = rp.rp_pid and rp.pid = '$pid' and p.disp = 1   ");
	$total = $db->total;
	
	
	$db->query("SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, rp_ix  FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_RELATION_PRODUCT." rp where p.id = rp.rp_pid and rp.pid = '$pid'  and p.disp = 1 order by rp.vieworder limit $start,$max");		
	
	
	

	if ($db->total == 0){
		$mString = "<table cellpadding=0 cellspacing=0 width=100% height=100%  bgcolor=silver style='font-size:10px;'>";	
		$mString .= "<tr bgcolor=#ffffff height=100%><td colspan=5 align=center class='small'>등록된 관련 상품 정보가 없습니다 . <br> 좌측 상품을 이곳으로 드래그하시면 <br>관련상품이 등록됩니다. </td></tr>";
		$mString .= "</table>";	
	}else{
		
		if($r_view_type == "small"){		
			$mString = "<div id='sortlist'>";				
			$i=0;
			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);			
				$mString .= "<img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif' id='image_".$db->dt[id]."' title='".cut_str($db->dt[pname],30)."' ondblclick=\"document.frames['act'].location.href='relation.category.act.php?mode=delete&relation_view_type=$r_view_type&pid=".$pid."&rp_ix=".$db->dt[rp_ix]."'\" width=50 height=50 style='border:1px solid silver' vspace=2 hspace=2>";
			}
			$mString = $mString."</div>";
		}else{
			$mString = "<ul id='sortlist'>";				
			$i=0;
			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);			
				$mString .= "<li id='image_".$db->dt[id]."' >
							<table width=96% border=0 >
							<col width='60'>
							<col width='*'>
							<col width='60'>
							<tr height=27 bgcolor=#ffffff >
							<td class=table_td_white align=center style='padding:5px;'>
								<img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif' ondblclick=\"document.frames['act'].location.href='relation.category.act.php?mode=delete&relation_view_type=$r_view_type&pid=".$pid."&rp_ix=".$db->dt[rp_ix]."'\" style='cursor:hand;'>
							</td>						
							<td class=table_td_white>".cut_str($db->dt[pname],30)."</td>
							<td><a href='relation.category.act.php?mode=delete&pid=".$pid."&rp_ix=".$db->dt[rp_ix]."'  target=act><img src='../image/btc_del.gif'></a></td>
							</tr>
							<tr height=1><td colspan=5 background='../image/dot.gif'></td></tr>
						</table></li>";
			}
			$mString = $mString."</ul>";
		}
	}
	
	
	
	return $mString;
	
}




?>