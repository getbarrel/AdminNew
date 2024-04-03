<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");


if($act == "insert"){
	$db = new Database;
	if($option_use == ""){
		$option_use = "0";	
	}
	
	$db->query("INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS." (opn_ix, pid, option_name, option_kind, option_use, regdate) VALUES ('','$pid','$option_name','$option_kind','$option_use',NOW())");		

	echo "<html>
		  <meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
			<body>
			<div id='option_name_view' style='display:none'>";
			echo PrintOptionName($pid, $select_opn_ix);
	echo "	</div>
			</body>
		</html>\n
		";
	echo "<Script Language='JavaScript'>		
		opener.document.getElementById('addOptionNameArea').innerHTML=document.getElementById('option_name_view').innerHTML;
		opener.document.forms['optionform'].reset();
		</Script>";
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 추가 되었습니다.');document.location.href='option.pop.php?pid=$pid'</script>";	
}


if($act == "update"){
	$db = new Database;
	
	if($option_use == ""){
		$option_use = "0";	
	}
	
	$sql = "update  ".TBL_SHOP_PRODUCT_OPTIONS." set 
		option_name='$option_name', option_kind='$option_kind', option_use='$option_use'
		where pid = '$pid' and opn_ix = '$opn_ix' ";
	//echo $sql;
	//exit;	
	$db->query($sql);		

	echo "<html>
		  <meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
			<body>
			<div id='option_name_view' style='display:none'>";
			echo PrintOptionName($pid, $opn_ix);
	echo "	</div>
			</body>
		</html>\n
		";
	echo "<Script Language='JavaScript'>		
		opener.document.getElementById('addOptionNameArea').innerHTML=document.getElementById('option_name_view').innerHTML;
		opener.document.forms['optionform'].reset();
		</Script>";
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정 되었습니다.');document.location.href='option.pop.php?pid=$pid'</script>";	
}


if($act == "delete"){
	$db = new Database;
	
	$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS." where opn_ix ='$opn_ix'  and pid='$pid'");
	$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix ='$opn_ix'  and pid='$pid' ");
	
	echo "<html>
		  <meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
			<body>
			<div id='option_name_view' style='display:none'>";
			echo PrintOptionName($pid, $select_opn_ix);
	echo "	</div>
			</body>
		</html>\n
		";
	echo "<Script Language='JavaScript'>				
		opener.document.getElementById('addOptionNameArea').innerHTML=document.getElementById('option_name_view').innerHTML;
		opener.document.forms['optionform'].reset();
		</Script>";
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 삭제 되었습니다.');document.location.href='option.pop.php?pid=$pid'</script>";	
}



function PrintOptionName($pid, $select_opn_ix)
{
	$mdb = new Database;
	$mdb->query("SELECT * FROM ".TBL_SHOP_PRODUCT_OPTIONS." where pid ='$pid'");
	
	$SelectString = "<Select name='opn_ix' onchange=\"ChangeOptionName('$pid', this);\">";
	
	if ($mdb->total){
			$SelectString = $SelectString."<option value=''>옵션이름 선택</option>";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			$SelectString = $SelectString."<option value='".$mdb->dt[opn_ix]."' option_kind='".$mdb->dt[option_kind]."'>".$mdb->dt[option_name]."</option>";
		}
	}else{
	$SelectString = $SelectString."<option value=''> 옵션이 없습니다.</option>";
	}
	
	$SelectString = $SelectString."</Select>";
	
	return $SelectString;
}


?>