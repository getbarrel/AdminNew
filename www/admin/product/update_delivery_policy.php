<?
if($act == "showDeliveryFromAjax"){
	include_once("../class/layout.class");
	include_once("./goods_input.lib.php");
	echo select_delivery_template($company_id,'R',$type,$id,'checked');
	exit;
}

if($act == "showDeliveryText"){
	include_once("../class/layout.class");
	include_once("./goods_input.lib.php");

	$sql = "select * from shop_delivery_template where company_id = '".$company_id."' and is_basic_template = '1' and product_sell_type = 'R'";
	$db->query($sql);
	$template_array = $db->fetchall("object");

	echo "<span id='basic_template_delivery'>".get_delivery_policy_text($template_array,'0')."</span>";
	echo "<input type='checkbox' name='dt_ix[".$template_array[0][product_sell_type]."][".$template_array[0][delivery_div]."][template_check]' id='template_basic_dt_check' value='1' checked>
	<input type='hidden' name='dt_ix[".$template_array[0][product_sell_type]."][".$template_array[0][delivery_div]."][dt_ix]' id='template_basic_dt_r' value='".$template_array[0][dt_ix]."'>";
	exit;
}

$update_kind_type = 'update_delivery_policy';
$page_type = 'update_delivery_policy';
$menu_title = '상품일괄수정';
$menu_name = "배송정책";
$help_width ='300';

include ('./update_category.php');

?>