<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');

$db = new Database;


if ($act == "update"){
	$coupon_text = $content;
	$coupon_use_sdate = $FromYY.$FromMM.$FromDD;
	$coupon_use_edate = $ToYY.$ToMM.$ToDD;

//	print_r($_POST);
//	exit;
	$sql = "update ".TBL_SHOP_SP_COUPON." set
			cid='$cid',pid='$pid',coupon_title='$coupon_title',coupon_text='$coupon_text',coupon_use_sdate='$coupon_use_sdate',coupon_use_edate='$coupon_use_edate',disp='$disp',full='$full'
			where coupon_ix='$coupon_ix'";
	$db->query($sql);

	echo("<script>top.location.href = 'sp_coupon.write.php?coupon_ix=$coupon_ix';</script>");
}

if ($act == "insert"){


	$coupon_text = $content;
	$coupon_use_sdate = $FromYY.$FromMM.$FromDD;
	$coupon_use_edate = $ToYY.$ToMM.$ToDD;
	$db->query("insert into ".TBL_SHOP_SP_COUPON."(coupon_ix,cid,pid, coupon_title,coupon_text,coupon_use_sdate,coupon_use_edate,disp,full, regdate) values('$coupon_ix','$cid','$pid','$coupon_title','$coupon_text','$coupon_use_sdate','$coupon_use_edate','$disp','$full',NOW())");

	echo("<script>top.location.href = 'sp_coupon.list.php';</script>");
}

if ($act == "delete")
{

	$db->query("DELETE FROM ".TBL_SHOP_SP_COUPON." WHERE coupon_ix='$coupon_ix'");
	echo("<script>top.location.href = 'sp_coupon.list.php';</script>");
	exit;
}



function ClearText($str){
	return str_replace(">","",$str);
}

?>
