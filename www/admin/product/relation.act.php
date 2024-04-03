<?
include("../../class/database.class");

$db = new Database;
echo "cid:".$cid."<br>";
echo "pid:".$pid."<br>";
echo checkrelation($mcid, $mpid)."<br>";

if ($mode == "insert"){	
	if (checkrelation($cid, $pid)){
		$sql = "insert into ".TBL_SHOP_PRODUCT_RELATION." (rid, cid, pid ,disp ,regdate) values ('','$cid', '$pid','1',NOW());";
		$db->query($sql);
		$sql = "select * from ".TBL_SHOP_PRODUCT_RELATION." where pid = '$pid'";
		$db->query($sql);
		if($db->total == 1){
			$sql = "update shop_product set reg_category = 'Y' where id = '$pid'";
			$db->query($sql);
		}

		echo "<script language='javascript' src='../js/message.js.php'></script><Script Language='JavaScript'>show_alert('정상적으로 입력되었습니다');parent.document.getElementById('divarea').innerHTML=\"".PrintRelation($pid)."\"</Script>";	
		
		
	}else{
		echo "<script language='javascript' src='../_language/language.php'></script><Script Language='JavaScript'>alert(language_data['region.act.php']['A'][language]);</Script>";	
		//'해당 카테고리에 이미 등록되어 있는 상품입니다.'
	}
//	Header("Location: category.php");	
}

if ($mode == "delete")
{
	$sql = "delete from ".TBL_SHOP_PRODUCT_RELATION." where rid ='$rid';";
	$db->query($sql);
	$sql = "select * from ".TBL_SHOP_PRODUCT_RELATION." where pid = '$pid'";
	$db->query($sql);
	if($db->total == 0){
		$sql = "update shop_product set reg_category = 'N' where id = '$pid'";
		$db->query($sql);
	}
	echo "<script language='javascript' src='../js/message.js.php'></script><Script Language='JavaScript'>show_alert('정상적으로 삭제되었습니다');parent.document.getElementById('divarea').innerHTML=\"".PrintRelation($pid)."\"</Script>";	
	
}


function checkrelation($mcid, $mpid)
{
	global $db;
	$sql = "select * from ".TBL_SHOP_PRODUCT_RELATION." where cid ='$mcid' and pid = '$mpid'";
	$db->query($sql);
	
	if ($db->total == 0){
		return true;
	}else{
		return false;
	}
	
	
}

function PrintRelationBackup($pid){
	global $db;
	
	$sql = "select c.cid,c.cname  from ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_SHOP_CATEGORY_INFO." c where pid = '$pid' and c.cid = r.cid ";
	$db->query($sql);
	
	$mString = "<table cellpadding=5 cellspacing=1 width=480 bgcolor=silver>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td>카테고리 ID</td><td>카테고리</td><td>삭제</td></tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff><td colspan=5>선택된 카테고리 정보가 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$mString = $mString."<tr height=25 bgcolor=#ffffff><td>".$db->dt[cid]."</td><td>".$db->dt[cname]."</td><td>X</td></tr>";
		}
	}
	$mString = $mString."</table>";
	
	return $mString;
}


function PrintRelation($pid){
	global $db;
	
	$sql = "select c.cid,c.cname,c.depth, r.rid, r.regdate  from ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_SHOP_CATEGORY_INFO." c where pid = '$pid' and c.cid = r.cid ";
	//echo $sql;	
	$db->query($sql);
	
	$mString = "<table cellpadding=0 cellspacing=0 width=100% bgcolor=silver style='font-size:10px;'>";
	$mString .= "<tr align=center bgcolor=#efefef height=25><td class='s_td small'>번호</td>	<td class='m_td small'>카테고리 ID</td><td class='m_td small'>카테고리</td><td class='m_td small'>등록날짜</td><td class='e_td small'>삭제</td></tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=42><td colspan=5 align=center class=table_td_white>선택된 카테고리 정보가 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$parent_cname = GetParentCategory($db->dt[cid],$db->dt[depth]);
			$mString .= "<tr height=25 bgcolor=#ffffff align=center><td class=table_td_white align=center>".($i+1)."</td><td class=table_td_white>".$db->dt[cid]."</td><td class=table_td_white>".($parent_cname != "" ? $parent_cname.">":"").$db->dt[cname]."</td><td class='table_td_white small'>".$db->dt[regdate]."</td><td class=table_td_white align=center><a href=JavaScript:deleteCategory('delete','".$db->dt[rid]."','$pid')><img src='../image/btc_del.gif' border=0></a></td></tr>";
			$mString .= "<tr hegiht=1><td colspan=5 background='../image/dot.gif'></td></tr>";
		}
	}
	$mString = $mString."</table>";
	
	return $mString;
}

/*
function PrintRelation($pid){
	global $db;
	
	$sql = "select c.cid,c.cname,c.depth, r.rid, r.regdate  from ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_SHOP_CATEGORY_INFO." c where pid = '$pid' and c.cid = r.cid ";
	
	
	$db->query($sql);
	
	$mString = "<table cellpadding=0 cellspacing=0 width=100% bgcolor=silver style='font-size:10px;'>";	
	$mString .= "<tr align=center bgcolor=#efefef height=25>
				<td class='s_td small'>번호</td>
				<td class='m_td small'>카테고리 ID</td>
				<td class='m_td small'>카테고리</td>
				<td class='m_td small'>등록날짜</td>
				<td class='e_td small'>삭제</td>
			</tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=45><td colspan=5 align=center>선택된 카테고리 정보가 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$parent_cname = GetParentCategory($db->dt[cid],$db->dt[depth]);
			$mString .= "<tr height=30 bgcolor=#ffffff align=center>
				<td class='table_td_white small' align=center>".($i+1)."</td>
				<td class='table_td_white small'>".$db->dt[cid]."</td>
				<td class='table_td_white small'>".($parent_cname != "" ? $parent_cname.">":"").$db->dt[cname]."</td>
				<td class='table_td_white small'>".$db->dt[regdate]."</td>
				<td class=table_td_white align=center><a href=\"JavaScript:deleteCategory('delete','".$db->dt[rid]."','$pid')\"><img src='../image/btc_del.gif' border=0></a></td>
				</tr>";
			$mString .= "<tr height=1><td colspan=5 background='../image/dot.gif'></td></tr>";
		}
	}
	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";
	$mString = $mString."</table>";
	
	return $mString;
}

*/

function GetParentCategory($subcid,$subdepth)
{
	$mdb = new Database;
	
	$sql = "select c.cid,c.cname from ".TBL_SHOP_CATEGORY_INFO." c where cid LIKE '".substr($subcid,0,$subdepth*3)."%' and depth = ".($subdepth-1)."  ";
	$mdb->query($sql);
	
	$mdb->fetch(0);
	
	return $mdb->dt[cname];
	
	
}
/*
create table ".TBL_SHOP_PRODUCT_RELATION." (
rid int(10) unsigned zerofill not null auto_increment,
cid varchar(15)not null,
pid varchar(6) null default null,
disp char(1) null default '1',
regdate datetime not null,
primary key(rid));
*/
?>?