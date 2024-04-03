<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");


$db = new Database;

$sql = "SELECT p1.sellprice, p1.coprice, p1.noninterest, p1.reserve,p1.regdate FROM ".TBL_SHOP_PRICEINFO." p1 where p1.pid = '$id' order by regdate";

$db->query($sql);

if($db->total == 0){
	echo "";	
}else{
	$mstring .= "<table cellpadding=5 cellspacing=0 border=0 width='100%' height='100%'>";
	$mstring .= "<tr height='40'><td colspan=5><b>$pname 가격 정보</b>  </td></tr>";
	$mstring .= "<tr align=center><td class=s_td>등록일자</td><td class=m_td>판매가격</td><td class=m_td>공급가격</td><td class=m_td>무이자</td><td class=e_td>적립금</td></tr>";
	$mstring .= "<tr height=2><td bgcolor=#ffffff colspan=5></td></tr>";
	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);
		$mstring .= "<tr align=center bgcolor='#ffffff'><td bgcolor=#efefef>".$db->dt[regdate]."</td><td>".number_format($db->dt[sellprice],0)." 원</td><td bgcolor=#efefef>".number_format($db->dt[coprice],0)." 원</td><td>".$db->dt[noninterest]."</td><td bgcolor=#efefef>".number_format($db->dt[reserve],0)." 원</td></tr>";
		$mstring .= "<tr height=1><td background='/img/dot.gif' colspan=5></td></tr>";
		
	}
	$mstring .= "<tr height='100%'><td colspan=6 height=100%></td></tr>";
	$mstring .= "</table>";
	
	
}

?>
<html>
<head><title></title></head>
<meta http-equiv="content-type" content="text/html; charset=euc-kr">
<meta http-equiv="cache-control" content="no-cache">
<LINK REL="stylesheet" HREF="../include/admin.css" TYPE="text/css">
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>
<body topmargin=0 leftmargin=0>
<table cellpadding=10 width='100%' height='100%'>
<tr>
<td class='controlpanel_pop' width='100%' height='100%' >
	<table cellpadding=10 width='100%' height='100%'>
	<tr>
	<td bgcolor=#ffffff width='100%' height='100%' >
	<?echo $mstring?>
	</td>
	</tr>
	</table>
</td>
</tr>
</table>
</body>
</html>