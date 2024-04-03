<?
include("../../class/database.class");



$db = new Database;

if ($act == "insert")
{


	$sql = "insert into shop_promotion_div (div_ix, agent_type, div_name,div_code, tab_view,leftmenu_view, disp,regdate) values('','$agent_type','$div_name','$div_code','$tab_view','$leftmenu_view','$disp',NOW())";
	$db->sequences = "SHOP_PROMOTION_DIV_SEQ";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('프로모션 전시 분류가 정상적으로 등록되었습니다.');</script>");
	if($agent_type == "M"){
		echo("<script>top.document.location.href='../mShop/promotion_category.php?mmode=$mmode';</script>");
	}else{
		echo("<script>document.location.href='promotion_category.php?mmode=$mmode';</script>");
	}
}


if ($act == "update"){

	$sql = "update shop_promotion_div set div_name='$div_name',div_code='$div_code',tab_view='$tab_view',leftmenu_view='$leftmenu_view',disp='$disp' where div_ix='$div_ix' ";

	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('프로모션 전시 분류가 정상적으로 수정되었습니다.');</script>");
	if($agent_type == "M"){
		echo("<script>top.document.location.href='../mShop/promotion_category.php?mmode=$mmode';</script>");
	}else{
		echo("<script>location.href = 'promotion_category.php?mmode=$mmode';</script>");
	}
}

if ($act == "delete"){

	$sql = "delete from shop_promotion_div where div_ix='$div_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('프로모션 전시 분류가 정상적으로 삭제되었습니다.');</script>");
	if($agent_type == "M"){
		echo("<script>top.document.location.href='../mShop/promotion_category.php?mmode=$mmode';</script>");
	}else{
		echo("<script>document.location.href='promotion_category.php?mmode=$mmode';</script>");
	}
}


/*
CREATE TABLE IF NOT EXISTS `shop_promotion_div` (
  `div_ix` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `div_name` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '구분명',
  `disp` char(1) CHARACTER SET utf8 DEFAULT '1' COMMENT '노출여부',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY (`div_ix`),
  KEY `disp` (`disp`,`div_ix`)
) ENGINE=MyISAM  COMMENT='프로모션 상품분류'  


*/
?>
