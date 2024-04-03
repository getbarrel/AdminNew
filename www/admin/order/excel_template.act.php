<?
include("../class/layout.class");

$db = new Database();

if($act=="get_template_ajax"){
	$sql="select * from shop_order_excel_template where oet_ix = '".$_POST["oet_ix"]."' ";
	$db->query($sql);
	$db->fetch();

	$return["oet_type"]=$db->dt["oet_type"];
	$return["oet_name"]=$db->dt["oet_name"];
	$return["oet_line"]=$db->dt["oet_line"];
	

	if($db->dt["charger_ix"]!=""){
		$return["charger_check"]="Y";
	}else{
		$return["charger_check"]="";
	}
	
	$tmp=unserialize(urldecode($db->dt["oet_array"]));
	$tmp2="";

	foreach($tmp as $info){
		$tmp2[]=$info["code"]."|".$info["text"];
	}

	$return["select_colums_info"]=$tmp2;

	echo json_encode($return);
	exit;
}

if($act=="template_update"){

	if($_POST["charger_check"]=="Y"){
		$charger_ix=$_SESSION["admininfo"]["charger_ix"];
	}else{
		$charger_ix="";
	}

	$oet_array=array();
	$col="A";
	foreach($_POST["select_colums_info"] as $val){
		list($code,$text) = explode("|",$val);
		$oet_array[$col]["code"]=$code;
		$oet_array[$col]["text"]=$text;
		
		$col++;
	}

	$oet_array=urlencode(serialize($oet_array));

	if($_POST["oet_ix"]!=""){
		$sql="update shop_order_excel_template set
		oet_type='".$_POST["oet_type"]."',
		oet_name='".$_POST["oet_name"]."',
		oet_array='".$oet_array."',
		oet_line='".$_POST["oet_line"]."',
		charger_ix='".$charger_ix."'
		where oet_ix='".$_POST["oet_ix"]."'";
	}else{
		$sql="insert into shop_order_excel_template (oet_ix,oet_type,oet_name,oet_array,oet_line,company_id,charger_ix) values('','".$_POST["oet_type"]."','".$_POST["oet_name"]."','".$oet_array."','".$oet_line."','".$_SESSION["admininfo"]["company_id"]."','".$charger_ix."')";
	}

	$db->query($sql);

	echo("<script>alert('정상적으로  처리 되었습니다.');parent.document.location.reload();</script>");
	exit;
}


if($act=="template_delete"){

	$sql="delete from shop_order_excel_template where oet_ix='".$oet_ix."'";
	$db->query($sql);
	echo("<script>alert('정상적으로 삭제 처리 되었습니다.');parent.document.location.reload();</script>");
	exit;
}

?>