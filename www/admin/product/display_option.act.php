<?
include("../web.config");
include("../../class/database.class");

$db = new Database;

if ($act == 'insert')
{
	
	$sql = "insert into ".TBL_SHOP_PRODUCT_DISPLAYINFO." (dp_ix,pid,dp_title,dp_desc,regdate) values('$dp_ix','$pid','$dp_title','$dp_desc',NOW()) ";
	
	$db->query($sql);

	
$mstring = "
<html>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<body>
".PrintDisplayOption($pid)."
</body>
</html>";
	echo $mstring;		
	echo "<script language='javascript' src='../js/message.js.php'></script><Script Language='JavaScript'>
	show_alert('정상적으로 입력되었습니다');
	parent.document.getElementById('addDisplayOptionArea').innerHTML=document.body.innerHTML;
	parent.document.forms['dispoptionform'].reset();
	
	</Script>";	
	//header("Location:../product_input.php");
}

if ($act == 'update')
{
	
	$sql = "update ".TBL_SHOP_PRODUCT_DISPLAYINFO." set pid='$pid',dp_title='$dp_title',dp_desc='$dp_desc',regdate='$regdate' where dp_ix='$dp_ix' ";
	
	//echo $sql;
	$db->query($sql);
		
	echo "<html>
		  <meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
			<body>
			<div id='option_view'>";
			echo PrintDisplayOption($pid);
	echo "	</div>
			</body>
		</html>\n
		";
	echo "<script language='javascript' src='../js/message.js.php'></script><Script Language='JavaScript'>
		show_alert('정상적으로 입력되었습니다');
		parent.document.getElementById('addDisplayOptionArea').innerHTML=document.getElementById('option_view').innerHTML;
		parent.document.forms['dispoptionform'].reset();
		//parent.document.forms['product_input'].stock.value = '".$option_stock."';
		//parent.document.forms['product_input'].safestock.value = '".$option_safestock."';
		</Script>";	
	//header("Location:../product_input.php");
}

if ($act == "delete")
{
	$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_DISPLAYINFO." WHERE dp_ix='$dp_ix' and pid ='$pid'");

$mstring = "
<html>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<body>
".PrintDisplayOption($pid)."
</body>
</html>";
	echo $mstring;
	echo "<script language='javascript' src='../js/message.js.php'></script><Script Language='JavaScript'>show_alert('정상적으로 삭제되었습니다');parent.document.getElementById('addDisplayOptionArea').innerHTML=document.body.innerHTML;</Script>";	
	//header("Location:../product_input.php");
}


function PrintDisplayOption($pid){
	global $db;
	
	$sql = "select * from ".TBL_SHOP_PRODUCT_DISPLAYINFO." a where pid = '$pid' ";
	$db->query($sql);
	
	$mString = "<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver>";	
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td class=s_td>번호</td><td class=m_td>옵션제목</td><td class=m_td>옵션설명</td><td class=e_td>관리</td></tr>";	
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=9 align=center>입력된 디스플레이 옵션이 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$mString = $mString."<tr height=25 bgcolor=#ffffff align=center>
			<td >".($i+1)."</td>
			<td><a href=\"JavaScript:UpdateDisplayOption('".$db->dt[dp_ix]."','".$db->dt[dp_title]."','".$db->dt[dp_desc]."')\" ><u>".$db->dt[dp_title]."</u></a></td>
			<td>".$db->dt[dp_desc]."</td>
			<td align=center>				
				<a href=\"JavaScript:deleteDisplayOption('delete','".$db->dt[dp_ix]."','$pid')\"><img  src='../image/si_remove.gif' border=0></a>
			</td>
			</tr>
			<tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>
			";
		}
	}
	$mString = $mString."</table>";
	
	return $mString;
}


function _PrintOption($pid){
	global $db;
	
	$sql = "select id,option_name, option_div,option_price, option_useprice, option_stock from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." a where pid = '$pid' ";
	$db->query($sql);
	
	$mString = "<table cellpadding=2 cellspacing=1 width=600 bgcolor=silver>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td>번호</td><td>옵션이름</td><td>옵션구분</td><td>옵션가격</td><td>옵션재고</td><td>옵션표시</td><td>관리</td></tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff><td colspan=4 align=center>입력된 옵션이 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$mString = $mString."<tr height=25 bgcolor=#ffffff>
			<td  align=center>".($i+1)."</td><td>".$db->dt[option_name]."</td><td>".$db->dt[option_div]."</td><td>".$db->dt[option_price]."</td><td>".$db->dt[option_stock]."</td>
			<td>".PrintSelect($db->dt[option_name],$db->dt[option_div],$db->dt[option_price],$db->dt[option_useprice])."</td>
			<td align=center>
				<a href=\"JavaScript:UpdateOption('".$db->dt[id]."','".$db->dt[option_name]."','".$db->dt[option_div]."','".$db->dt[option_price]."','".$db->dt[option_stock]."')\">○</a>
				<a href=\"JavaScript:deleteOption('delete','".$db->dt[id]."','$pid')\">×</a>
			</td>
			</tr>";
		}
	}
	$mString = $mString."</table>";
	
	return $mString;
}


function PrintSelect($op_name,$op_div,$op_price,$op_useprice)
{
	$aryOp_div = explode("|",$op_div);
	$aryOp_price = explode("|",$op_price);
	$size = count($aryOp_div);
	
	$SelectString = "<Select>";
	
	if ($size == 0){
		$SelectString = $SelectString."<option>옵션이 없습니다.</option>";
	}else{
		if($op_useprice ==1){
			for($i=0; $i < $size; $i++){
				$SelectString = $SelectString."<option value='".$aryOp_div[$i]."'>".$aryOp_div[$i]."</option>";
			}
		}else{
			for($i=0; $i < $size; $i++){
				$SelectString = $SelectString."<option value='".$aryOp_price[$i]."'>".$aryOp_div[$i]."</option>";
			}
		}
	}
	
	$SelectString = $SelectString."</Select>";
	
	return $SelectString;
}
?>
