<?
header("Content-type: text/html; charset=utf-8");
include "../class/database.class";		

$pid    = $_REQUEST["pid"];
$branch = $_REQUEST["branch"];
$option_ix = $_REQUEST["option_ix"];
//$admin = $_REQUEST["admin"];
$limit = $_REQUEST["limit"];

$db = new Database;

switch($branch)
{
	case 'count' :
$sql = " select count(*) as total from  shop_product
                 where product_type in ('0','')
			order by regdate
       ";
			break;
	case 'product' :
$sql = " select * from  shop_product
                 where product_type in ('0','')
			order by regdate $limit
       ";
			break;
	case 'category' :
$sql = "select pid, cid, basic from shop_product_relation
                       where pid = '$pid'";
			break;
	case 'priceinfo' :
$sql = "select * from shop_priceinfo a
                     ,(select max(id) as id from shop_priceinfo where pid = '$pid') b
                where a.id = b.id";
			break;
	case 'options' :
$sql = "select * from shop_product_options where pid='$pid'";
			break;
	case 'option' :
$sql = "select * from shop_product_options_detail where pid='$pid' and opn_ix = '$option_ix'";
			break;
}
$db->query($sql);
$data = $db->fetchall();

echo(base64_encode(serialize($data)));
?>
