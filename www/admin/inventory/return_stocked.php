<?



include("../class/layout.class");

$title_str = "반품입고";
$sub_title = "입고";

$Contents="<div style='width:100%;text-align:center;margin-top:200px;'>이메뉴는 아직 사용하실수 없습니다.</div>";

$P = new LayOut;
$P->addScript = $Script;
$P->OnloadFunction = "";//MenuHidden(false);
$P->prototype_use = false;
$P->jquery_use = true;
$P->strLeftMenu = inventory_menu();
$P->strContents = $Contents;
$P->Navigation = "재고관리 > ".$sub_title."(사입)요청관리 > $title_str 작성";
$P->title = "$title_str 작성";
$P->PrintLayOut();

exit;
/////////////////////////////////////////////////반품 입고는 차후 처리할 예정입니다. 처리되면 이 주석 위내용을 삭제 하면 됩니다./////////////////////////////////////////////////


//include_once("../class/layout.class");
$page_type = 'stocked';
/*
$fix_type = array('MI');
for($i=0;$i < count($fix_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$fix_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$fix_type[$i];
	}
}
*/
$title_str = "반품입고";
$sub_title = "입고";
$sub_price_title = "입고가";
$sub_amount_title = "입고수량";
$h_div = "1"; // 1:입고, 2:출고
$type = "I";
$type_div = "3"; // 반품입고
include("register.php");
//include("orders.goods_list.php");



?>