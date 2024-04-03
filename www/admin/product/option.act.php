<?
include("../web.config");
include("../../class/database.class");

$db = new Database;

if ($act == 'insert')
{

	//$sql = $sql."INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price,option_m_price, option_d_price, option_a_price, option_stock, option_safestock, option_etc1) ";
	$sql = $sql."INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price, option_stock, option_safestock, option_etc1) ";
	//$sql = $sql." values('','$pid','$opn_ix','$option_div','$option_price','$option_m_price','$option_d_price','$option_a_price','$option_stock','$option_safestock','$option_etc1') ";
	$sql = $sql." values('','$pid','$opn_ix','$option_div','$option_price','$option_stock','$option_safestock','$option_etc1') ";

	$db->query($sql);
//	$db->query("SELECT id FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE id=LAST_INSERT_ID()");
//	$db->fetch();

	$mstring = "
		<html>
		<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
		<body>
		".PrintOption($pid,$opn_ix)."
		</body>
		</html>";
		echo $mstring;
		echo "<Script Language='JavaScript'>
		parent.document.getElementById('addOptionArea').innerHTML=document.body.innerHTML;
		</Script>";

	if($option_kind == "b"){
		$db->query("SELECT sum(option_stock) as option_stock, sum(option_safestock) as option_safestock  FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE pid='$pid' group by pid");
		$db->fetch();
		$option_stock = $db->dt[option_stock];
		$option_safestock = $db->dt[option_safestock];
		$db->query("update ".TBL_SHOP_PRODUCT." set stock = '$option_stock', safestock = '$option_safestock'  WHERE id='$pid'");


			echo "<Script Language='JavaScript'>
			parent.document.forms['product_input'].stock.value = '".$option_stock."';
			parent.document.forms['product_input'].safestock.value = '".$option_safestock."';
			parent.document.forms['optionform'].option_div.focus();
			</Script>";

	}

	echo "<script language='javascript' src='../js/message.js.php'></script><Script Language='JavaScript'>show_alert('正常に入力されてい');</script>"; //정상적으로 입력되었습니다

	//header("Location:../product_input.php");
}

if ($act == 'update')
{

	$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set ";
	//$sql .= " opn_ix = '$opn_ix', option_div='$option_div',option_price='$option_price',option_m_price='$option_m_price',option_d_price='$option_d_price',option_a_price='$option_a_price',option_useprice='$option_useprice', option_stock='$option_stock', option_safestock='$option_safestock' , option_etc1='$option_etc1' ";
	$sql .= " opn_ix = '$opn_ix', option_div='$option_div',option_price='$option_price', option_stock='$option_stock', option_safestock='$option_safestock' , option_etc1='$option_etc1' ";
	$sql .= " where id ='$option_id'";

	//echo $sql;


	$db->query($sql);
	if($option_kind == "b"){
		$sql = "select option_stock,option_safestock from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where id = '$option_id' ";
		$db->query($sql);
		$db->fetch();
		$one_option_stock = $db->dt[option_stock];
		$one_option_safestock = $db->dt[option_safestock];
		if($one_option_stock > $one_option_safestock){
			$stock_update = " , option_stock_yn = 'N'";
		}else if($one_option_stock == 0){
			$stock_update = " , option_stock_yn = 'Y'";
		}else if($one_option_stock < $one_option_safestock){
			$stock_update = " , option_stock_yn = 'S'";
		}
	}
	echo "<html>
		  <meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
			<body>
			<div id='option_view'>";
			echo PrintOption($pid,$opn_ix);
	echo "	</div>
			</body>
		</html>\n
		";
	echo "<Script Language='JavaScript'>
		parent.document.getElementById('addOptionArea').innerHTML=document.getElementById('option_view').innerHTML;
		</Script>";

	if($option_kind == "b"){
		$db->query("SELECT sum(option_stock) as option_stock, sum(option_safestock) as option_safestock  FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE pid='$pid' group by pid");
		$db->fetch();
		$option_stock = $db->dt[option_stock];
		$option_safestock = $db->dt[option_safestock];


		$db->query("update ".TBL_SHOP_PRODUCT." set stock = '$option_stock', safestock = '$option_safestock' $stock_update WHERE id='$pid'");


	echo "<Script Language='JavaScript'>

		//parent.document.forms['optionform'].reset();

		parent.document.forms['optionform'].act.value = 'insert';
		parent.document.forms['optionform'].option_div.value = '';
		parent.document.forms['optionform'].option_price.value = '';
		//parent.document.forms['optionform'].option_m_price.value = '';
		//parent.document.forms['optionform'].option_d_price.value = '';
		//parent.document.forms['optionform'].option_a_price.value = '';
		parent.document.forms['optionform'].option_stock.value = '';
		parent.document.forms['optionform'].option_safestock.value = '';

		parent.document.forms['product_input'].stock.value = '".$option_stock."';
		parent.document.forms['product_input'].safestock.value = '".$option_safestock."';
		parent.document.forms['optionform'].option_div.focus();
		</Script>";

	}

	echo "<script language='javascript' src='../js/message.js.php'></script><Script Language='JavaScript'>show_alert('正常に修正されました');</script>";//정상적으로 수정되었습니다
	//header("Location:../product_input.php");
}

if ($act == 'view'){
	echo "<html>
		  <meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
			<body>
			<div id='option_view'>";
			echo PrintOption($pid,$opn_ix);
	echo "	</div>
			</body>
		</html>\n
		";
	echo "<Script Language='JavaScript'>
		parent.document.getElementById('addOptionArea').innerHTML=document.getElementById('option_view').innerHTML;
		</Script>";
	//header("Location:../product_input.php");
}

if ($act == "delete")
{
	/*
	$db->query("SELECT sum(option_stock) as option_stock, sum(option_safestock) as option_safestock  FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE pid='$pid' and id = '$id' ");
	$db->fetch();
	$option_stock = $db->dt[option_stock];
	$option_safestock = $db->dt[option_safestock];
	*/


	$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE id='$id'");


$mstring = "
<html>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<body>
".PrintOption($pid,$opn_ix)."
</body>
</html>";
	echo $mstring;

	echo "<Script Language='JavaScript'>
	parent.document.getElementById('addOptionArea').innerHTML=document.body.innerHTML;
	</Script>";

if($option_kind == "b"){
	$db->query("SELECT sum(option_stock) as option_stock, sum(option_safestock) as option_safestock  FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE pid='$pid' group by pid");
	$db->fetch();
	$option_stock = $db->dt[option_stock];
	$option_safestock = $db->dt[option_safestock];
	$db->query("update ".TBL_SHOP_PRODUCT." set stock = '$option_stock', safestock = '$option_safestock'  WHERE id='$pid'");

	echo "<Script Language='JavaScript'>
	parent.document.forms['product_input'].stock.value ='".$option_stock."';
	parent.document.forms['product_input'].safestock.value = '".$option_safestock."';
	</Script>";
}

echo "<script language='javascript' src='../js/message.js.php'></script><Script Language='JavaScript'>show_alert('正常に削除されました');</script>"; //정상적으로 삭제되었습니다
	//header("Location:../product_input.php");
}



function PrintOption($pid, $opn_ix){
	global $db;

	//$sql = "select id, option_div,option_price, option_m_price, option_d_price, option_a_price, option_useprice, option_stock, option_safestock,option_etc1 from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." a where pid = '$pid' and opn_ix ='$opn_ix' order by id asc";
	$sql = "select id, option_div,option_price, option_stock, option_safestock,option_etc1 from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." a where pid = '$pid' and opn_ix ='$opn_ix' order by id asc";
	$db->query($sql);

	$mString = "<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver>";
	$mString .=  "<tr height=1><td colspan=9 ></td></tr>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td rowspan=3>번호</td><td rowspan=3>옵션구분</td><td colspan=4>옵션가격</td><td colspan=2>옵션재고</td><td rowspan=3>기타(색상)</td><td rowspan=3>관리</td></tr>";
	$mString .=  "<tr height=1><td colspan=6 background='../image/dot.gif'></td></tr>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td>비회원가</td><td>회원가</td><td>딜러가</td><td>대리점가</td><td >재고</td><td >안전재고</td></tr>";
	$mString .=  "<tr height=1><td colspan=9 ></td></tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=10 align=center>入力されたオプションの詳細はありません。</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			/*
			$mString = $mString."<tr height=25 bgcolor=#ffffff>
			<td  align=center>".($i+1)."</td>
			<td><a href=\"JavaScript:UpdateOption('".$db->dt[id]."','".str_replace("\"","&quot;",$db->dt[option_div])."','".$db->dt[option_price]."','".$db->dt[option_m_price]."','".$db->dt[option_d_price]."','".$db->dt[option_a_price]."','".$db->dt[option_stock]."','".$db->dt[option_safestock]."','".str_replace("\"","&quot;",$db->dt[option_etc1])."')\" ><u>".$db->dt[option_div]."</u></a></td>
			<td>".$db->dt[option_price]."</td>
			<td>".$db->dt[option_m_price]."</td>
			<td>".$db->dt[option_d_price]."</td>
			<td>".$db->dt[option_a_price]."</td>
			<td>".$db->dt[option_stock]."</td>
			<td>".$db->dt[option_safestock]."</td>
			<td align=center>".$db->dt[option_etc1]."</td>
			<td align=center>
				<a href=\"JavaScript:deleteOption('delete','".$db->dt[id]."','$pid','$opn_ix')\"><img  src='../image/btc_del.gif' border=0></a>
			</td>
			</tr>
			<tr height=1><td colspan=9 background='../image/dot.gif'></td></tr>
			";
			*/
			$mString = $mString."<tr height=25 bgcolor=#ffffff>
			<td  align=center>".($i+1)."</td>
			<td><a href=\"JavaScript:UpdateOption('".$db->dt[id]."','".str_replace("\"","&quot;",$db->dt[option_div])."','".$db->dt[option_price]."','".$db->dt[option_stock]."','".$db->dt[option_safestock]."','".str_replace("\"","&quot;",$db->dt[option_etc1])."')\" ><u>".$db->dt[option_div]."</u></a></td>
			<td>".$db->dt[option_price]."</td>
			<td>".$db->dt[option_stock]."</td>
			<td>".$db->dt[option_safestock]."</td>
			<td align=center>".$db->dt[option_etc1]."</td>
			<td align=center>
				<a href=\"JavaScript:deleteOption('delete','".$db->dt[id]."','$pid','$opn_ix')\"><img  src='../image/btc_del.gif' border=0></a>
			</td>
			</tr>
			<tr height=1><td colspan=9 background='../image/dot.gif'></td></tr>
			";
		}
	}
	$mString = $mString."</table>";

	return $mString;
}

function _PrintOption($pid){
	global $db;

	$sql = "select id, option_div,option_price, option_useprice, option_stock from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." a where pid = '$pid' ";
	$db->query($sql);

	$mString = "<table cellpadding=2 cellspacing=1 width=600 bgcolor=silver>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td>번호</td><td>옵션이름</td><td>옵션구분</td><td>옵션가격</td><td>옵션재고</td><td>옵션표시</td><td>관리</td></tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff><td colspan=4 align=center>入力されたオプションがありません。</td></tr>";
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
		$SelectString = $SelectString."<option>選択オプションはありません</option>";
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
